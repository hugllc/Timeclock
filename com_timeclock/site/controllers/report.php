<?php
/**
 * This component is for tracking tim
 *
 * PHP Version 5
 *
 * <pre>
 * com_timeclock is a Joomla! 3.1 component
 * Copyright (C) 2014 Hunt Utilities Group, LLC
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: $Id: 592d3a54f46f5b31b8b65f6a0f0b2a1f26cafe40 $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
defined( '_JEXEC' ) or die( 'Restricted access' ); 
require_once __DIR__."/default.php";
/**
 * Description Here
 *
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockControllersReport extends TimeclockControllersDefault
{
    /** This is our model */
    protected $model = null;
    /**
    * This function performs everything for this controller.  It is the goto 
    * function.
    *
    * @access public
    * @return boolean
    */
    public function execute()
    {
        $this->checkAuth();
        $this->model = TimeclockHelpersTimeclock::getModel("report");
        $task = $this->getTask();
        $fct  = "task".ucfirst($task);
        $this->checkReportID();
        
        if (method_exists($this, $fct)) {
            return $this->$fct();
        }
        return $this->display();
    }
    /**
    * This function checks to see if we are loading a specific report.  If we are,
    * it checks to see what controller is involved, and redirects accordingly.
    * function.
    *
    * @access public
    * @return boolean
    */
    protected function checkReportID()
    {
        $viewFormat = strtolower(JFactory::getDocument()->getType());
        if ($viewFormat == "html") {
            return;
        }
        $app = $this->getApplication();
        $report_id = $app->input->get("report_id", null, "int");
        if (empty($report_id)) {
            return;
        }
        $report = JTable::getInstance('TimeclockReports', 'Table');
        $report->load($report_id);
        if (!empty($report->type) && ($report->type != "report")) {
            $inline = $app->input->get("inline", 0, int);
            $app->redirect(
                "index.php?option=com_timeclock&controller=".$report->type."&task=display&report_id=".$report_id."&format=".$viewFormat."&inline=".$inline
            );
        }
    }
    /**
    * This function performs everything for this controller.  It is the goto 
    * function.
    *
    * @access public
    * @return boolean
    */
    protected function display()
    {
        // Get the application
        $app = $this->getApplication();
        $params = JComponentHelper::getParams('com_timeclock');
        // Get the document object.
        $document = JFactory::getDocument();
        $viewName = "report";
        $viewFormat = $document->getType();
        $layoutName = $app->input->get("layout", "report");
        if ($layoutName != "modalsave") {
            $layoutName = 'report';
        }
        $app->input->set('view', $viewName);
        // Register the layout paths for the view
        $paths = new SplPriorityQueue;
        $paths->insert(JPATH_COMPONENT . '/views/' . $viewName . '/tmpl', 'normal');
        $viewClass = 'TimeclockViews' . ucfirst($viewName) . ucfirst($viewFormat);
        $view = new $viewClass($this->model, $paths);

        if (method_exists($view, "setLayout")) {
            $view->setLayout($layoutName);
        }
        // Render our view.
        echo $view->render();
        return true;
    }
    /**
    * This function saves our stuff and returns a json response
    * 
    * @return null
    */
    protected function taskSave()
    {
        JRequest::checkToken('request') or jexit("JINVALID_TOKEN");
        // Get the application
        $app   = $this->getApplication();

        if ($index = $this->model->store()) {
            $json = new JResponseJson(
                get_object_vars($index), 
                JText::_("COM_TIMECLOCK_REPORT_SAVED"),
                false,  // Error
                false    // Ignore Message Queue
            );
        } else {
            $json = new JResponseJson(
                array(), 
                JText::_("COM_TIMECLOCK_REPORT_SAVE_FAILED"),
                true,    // Error
                false     // Ignore Message Queue
            );
        }
        JFactory::getDocument()->setMimeEncoding( 'application/json' );
        JResponse::setHeader('Content-Disposition','inline;filename="apply.json"');
        echo $json;
        $app->close();
        return true;
    }
    /**
    * This is the main function that executes everything.
    *
    * @return bool
    */
    public function authorize()
    {
        $user = JFactory::getUser();
        if ($user->get('guest')) {
            return false;
        }
        $active = TimeclockHelpersTimeclock::getUserParam("active", $user->id);
        if ($active) {
            $reports = TimeclockHelpersTimeclock::getUserParam("reports", $user->id);
            return (bool)$reports;
        }
        return false;
    }

}