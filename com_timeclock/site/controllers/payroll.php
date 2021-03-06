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
require_once __DIR__."/report.php";
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
class TimeclockControllersPayroll extends TimeclockControllersReport
{
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
        $this->model = TimeclockHelpersTimeclock::getModel("payroll");

        $task = $this->getTask();
        $fct  = "task".ucfirst($task);
        if (method_exists($this, $fct)) {
            return $this->$fct();
        }
        return $this->display();
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
        $viewName = "payroll";
        $viewFormat = $document->getType();
        $layoutName = 'payroll';
        
        $app->input->set('view', $viewName);
        // Register the layout paths for the view
        $paths = new SplPriorityQueue;
        $paths->insert(JPATH_COMPONENT . '/views/' . $viewName . '/tmpl', 'normal');
        $viewClass = 'TimeclockViews' . ucfirst($viewName) . ucfirst($viewFormat);
        $modelClass = 'TimeclockModelsPayroll';
        $view = new $viewClass(new $modelClass, $paths);
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
    protected function taskLock()
    {
        JRequest::checkToken('request') or jexit("JINVALID_TOKEN");
        // Get the application
        $app   = $this->getApplication();

        if ($index = $this->model->lock()) {
            $json = new JResponseJson(
                $index, 
                JText::_("COM_TIMECLOCK_PAYPERIOD_LOCKED"),
                false,  // Error
                false    // Ignore Message Queue
            );
        } else {
            $json = new JResponseJson(
                array(), 
                JText::_("COM_TIMECLOCK_PAYPERIOD_LOCK_FAILED"),
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
    * This function saves our stuff and returns a json response
    * 
    * @return null
    */
    protected function taskUnlock()
    {
        JRequest::checkToken('request') or jexit("JINVALID_TOKEN");
        // Get the application
        $app   = $this->getApplication();

        if ($index = $this->model->unlock()) {
            $json = new JResponseJson(
                $index, 
                JText::_("COM_TIMECLOCK_PAYPERIOD_UNLOCKED"),
                false,  // Error
                false    // Ignore Message Queue
            );
        } else {
            $json = new JResponseJson(
                array(), 
                JText::_("COM_TIMECLOCK_PAYPERIOD_UNLOCK_FAILED"),
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
    * This function returns the message to show when a controller is saved
    *
    * @param mixed $data The data to output
    *
    * @return null
    */
    protected function echoJSON($data)
    {
    }

}