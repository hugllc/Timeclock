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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Factory;
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
    public function execute($task = NULL)
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
    public function display($cachable = false, $urlparams = [])
    {
        // Get the application
        $app = Factory::getApplication();
        $params = ComponentHelper::getParams('com_timeclock');
        // Get the document object.
        $document = Factory::getDocument();
        $viewName = "timesheet";
        $viewFormat = $document->getType();
        $layoutName = 'timesheet';
        
        $app->input->set('view', $viewName);
        $viewClass = 'TimeclockViews' . ucfirst($viewName) . ucfirst($viewFormat);
        $modelClass = 'TimeclockModelsTimesheet';
        $model = new $modelClass();
        $view = new $viewClass();
        $view->setLayout($layoutName);
        $view->setModel($model, true);
        // Render our view.
        $view->display();
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
        $app   = Factory::getApplication();

        $model = TimeclockHelpersTimeclock::getModel("timesheet");
        $this->checkMe($model);

        if ($index = $model->complete()) {
            $json = new JsonResponse(
                $index, 
                Text::_("COM_TIMECLOCK_PAYPERIOD_COMPLETE"),
                false,  // Error
                false    // Ignore Message Queue
            );
        } else {
            $json = new JsonResponse(
                array(), 
                Text::_("COM_TIMECLOCK_PAYPERIOD_COMPLETE_FAILED"),
                true,    // Error
                false     // Ignore Message Queue
            );
        }
        Factory::getDocument()->setMimeEncoding( 'application/json' );
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
        $app = Factory::getApplication();
        $params = ComponentHelper::getParams('com_timeclock');
        // Get the document object.
        $document = Factory::getDocument();
        $viewName = "timesheet";
        $viewFormat = $document->getType();
        $layoutName = $app->input->getWord('layout', 'addhours');
        if (($layoutName !== "addhours") && ($layoutName !== "modal")) {
            $layoutName = "addhours";
        }
        $app->input->set('view', $viewName);
        $viewClass = 'TimeclockViews' . ucfirst($viewName) . ucfirst($viewFormat);
        $model = TimeclockHelpersTimeclock::getModel("addhours");
        $this->checkMe($model);
        $view = new $viewClass();
        $view->setLayout($layoutName);
        $view->setModel($model, true);
        // Render our view.
        $view->display();
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
        $app   = Factory::getApplication();
        $model = TimeclockHelpersTimeclock::getModel("addhours");
        $this->checkMe($model);

        if ($index = $model->store()) {
            $json = new JsonResponse(
                get_object_vars($index), 
                Text::_("COM_TIMECLOCK_HOURS_SAVED"),
                false,  // Error
                false    // Ignore Message Queue
            );
        } else {
            $json = new JsonResponse(
                array(), 
                Text::_("COM_TIMECLOCK_HOURS_FAILED"),
                true,    // Error
                false     // Ignore Message Queue
            );
        }
        Factory::getDocument()->setMimeEncoding( 'application/json' );
        JResponse::setHeader('Content-Disposition','inline;filename="apply.json"');
        echo $json;
        $app->close();
        return true;
    }
    protected function checkMe($model)
    {
        if (!$model->getUser()->me) {
            Factory::getApplication()->redirect('index.php',Text::_('JGLOBAL_AUTH_ACCESS_DENIED'));
        }

    }
    /**
    * This function get the task for us
    * 
    * @return null
    */
    public function getTask()
    {
        if (is_null($this->_task)) {
            // Get the application
            $app = Factory::getApplication();
            // Get the document object.
            $document = Factory::getDocument();
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
    * This is the main function that executes everything.
    *
    * @return bool
    */
    public function authorize()
    {
        $user = Factory::getUser();
        if ($user->get('guest')) {
            return false;
        }
        $active = TimeclockHelpersTimeclock::getUserParam("active", $user->id);
        if ($active) {
            $app = Factory::getApplication();
            if (is_null($app->input->get("id", null, "int"))) {
                return true;
            }
            $reports = TimeclockHelpersTimeclock::getUserParam("reports", $user->id);
            return (bool)$reports;
        }
        return false;
    }
}