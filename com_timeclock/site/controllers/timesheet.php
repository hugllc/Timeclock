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
class TimeclockControllersTimesheet extends TimeclockControllersDefault
{
    /** This is where we store our task */
    private $_task = null;
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
        $viewName = "timesheet";
        $viewFormat = $document->getType();
        $layoutName = 'timesheet';
        
        $app->input->set('view', $viewName);
        // Register the layout paths for the view
        $paths = new SplPriorityQueue;
        $paths->insert(JPATH_COMPONENT . '/views/' . $viewName . '/tmpl', 'normal');
        $viewClass = 'TimeclockViews' . ucfirst($viewName) . ucfirst($viewFormat);
        $modelClass = 'TimeclockModelsTimesheet';
        $view = new $viewClass(new $modelClass, $paths);
        $view->setLayout($layoutName);
        // Render our view.
        echo $view->render();
        return true;
    }
    /**
    * This function saves our stuff and returns a json response
    * 
    * @return null
    */
    protected function taskComplete()
    {
        JRequest::checkToken('request') or jexit("JINVALID_TOKEN");
        // Get the application
        $app   = $this->getApplication();

        $model = TimeclockHelpersTimeclock::getModel("timesheet");

        if ($index = $model->complete()) {
            $json = new JResponseJson(
                $index, 
                JText::_("COM_TIMECLOCK_PAYPERIOD_COMPLETE"),
                false,  // Error
                false    // Ignore Message Queue
            );
        } else {
            $json = new JResponseJson(
                array(), 
                JText::_("COM_TIMECLOCK_PAYPERIOD_COMPLETE_FAILED"),
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
    * This displays our forms
    * 
    * @return bool
    */
    protected function taskAddhours()
    {
        // Get the application
        $app = $this->getApplication();
        $params = JComponentHelper::getParams('com_timeclock');
        // Get the document object.
        $document = JFactory::getDocument();
        $viewName = "timesheet";
        $viewFormat = $document->getType();
        $layoutName = $app->input->getWord('layout', 'addhours');
        if (($layoutName !== "addhours") && ($layoutName !== "modal")) {
            $layoutName = "addhours";
        }
        $app->input->set('view', $viewName);
        // Register the layout paths for the view
        $paths = new SplPriorityQueue;
        $paths->insert(JPATH_COMPONENT . '/views/' . $viewName . '/tmpl', 'normal');
        $viewClass = 'TimeclockViews' . ucfirst($viewName) . ucfirst($viewFormat);
        $modelClass = 'TimeclockModelsAddhours';
        $view = new $viewClass(new $modelClass, $paths);
        $view->setLayout($layoutName);
        // Render our view.
        echo $view->render();
        return true;
    }
    /**
    * This function saves our stuff and returns a json response
    * 
    * @return null
    */
    protected function taskApply()
    {
        JRequest::checkToken('request') or jexit("JINVALID_TOKEN");
        // Get the application
        $app   = $this->getApplication();
        $model = TimeclockHelpersTimeclock::getModel("addhours");

        if ($index = $model->store()) {
            $json = new JResponseJson(
                get_object_vars($index), 
                JText::_("COM_TIMECLOCK_HOURS_SAVED"),
                false,  // Error
                false    // Ignore Message Queue
            );
        } else {
            $json = new JResponseJson(
                array(), 
                JText::_("COM_TIMECLOCK_HOURS_FAILED"),
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
    * This function get the task for us
    * 
    * @return null
    */
    protected function getTask()
    {
        if (is_null($this->_task)) {
            // Get the application
            $app = $this->getApplication();
            // Get the document object.
            $document = JFactory::getDocument();
            $task = $app->input->get('task', 'list');
            $task = empty($task) ? 'list' : $task;
            $task = ($task == "display") ? 'list' : $task;
            if (strpos($task, ".")) {
                list($controller, $task) = explode(".", $task);
            }
            $this->_task = $task;
        }
        return $this->_task;
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