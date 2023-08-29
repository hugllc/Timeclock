<?php
/**
 * This component is for tracking tim
 *
 * PHP Version 5
 *
 * <pre>
 * com_timeclock is a Joomla! 3.1 component
 * Copyright (C) 2023 Hunt Utilities Group, LLC
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
 * @copyright  2023 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: $Id: 91f88619a0067d378d0ae6dec8304ac31c63fb2c $
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
 * @copyright  2023 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockControllersProject extends TimeclockControllersDefault
{
    /** This is our basic link */
    protected $baselink = "index.php?option=com_timeclock&controller=project";
    /** This is our controller name */
    protected $controller = "project";
    /** This is our model name */
    protected $model = "project";
    /** These are our system messages */
    protected $msgs = array(
        "saved" => "COM_TIMECLOCK_PROJECT_SAVED",
        "saveFailed" => "COM_TIMECLOCK_PROJECT_SAVE_FAILED",
    );
    /**
    * This function saves our stuff.
    * 
    * @return null
    */
    protected function taskSave()
    {
        $this->applyUsers();
        return parent::taskSave();
    }
    /**
    * This function saves our stuff and returns a json response
    * 
    * @param bool $output Not used in this function
    *
    * @return null
    */
    protected function taskApply($output = true)
    {
        $ret = parent::taskApply(false);
        $this->applyUsers();
        $this->echoJSON($ret);
        return $ret;
    }
    /**
    * This function saves our stuff and returns a json response
    * 
    * @return null
    */
    protected function applyUsers()
    {
        $app   = $this->getApplication();
        $model = $this->getModel();
        $users_in = $app->input->get("users_in", array(), "array");
        $model->addUsers($users_in, $model->insert_id);
        $users_out = $app->input->get("users_out", array(), "array");
        $model->removeUsers($users_out, $model->insert_id);
    }
}