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
 * @version    GIT: $Id: ee479480f4213143e8e00cc988c591c5203daa2e $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
defined( '_JEXEC' ) or die( 'Restricted access' ); 

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
class TimeclockControllersDefault extends JControllerBase
{
    /** This is where we store our task */
    private $_task = null;
    /**
    * This is the main function that executes everything.
    *
    * @return bool
    */
    public function execute()
    {
        // Get the application
        $app = $this->getApplication();
        // Redirect to the correct controller
        $app->redirect('index.php?option=com_timeclock&controller=timeclock');
        // Return
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
        return (bool)$active;
    }
    /**
    * This is the main function that executes everything.
    *
    * @return null
    */
    public function checkAuth()
    {
        $app = $this->getApplication();
        if (!$this->authorize()) {
            $app->redirect('index.php',JText::_('JGLOBAL_AUTH_ACCESS_DENIED'));
        }
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

}