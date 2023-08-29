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
 * @version    GIT: $Id: f336dd073cf7f3e6baae683159405d4271cb0884 $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Language\Text;
 
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
class TimeclockControllersPto extends TimeclockControllersDefault
{
    /** This is our basic link */
    protected $baselink = "index.php?option=com_timeclock&controller=pto";
    /** This is our controller name */
    protected $controller = "pto";
    /** This is our model name */
    protected $model = "pto";
    /** These are our system messages */
    protected $msgs = array(
        "saved" => "COM_TIMECLOCK_PTO_SAVED",
        "saveFailed" => "COM_TIMECLOCK_PTO_SAVE_FAILED",
    );
    /**
    * This function saves our stuff.
    * 
    * @return null
    */
    protected function taskDelete()
    {
        JRequest::checkToken('request') or jexit("JINVALID_TOKEN");
        
        // Get the application
        $app   = $this->getApplication();
        $model = $this->getModel();
        if ($index = $model->delete()) {
            $app->enqueueMessage(
                Text::_("COM_TIMECLOCK_PTO_DELETED"), 'message'
            );
        } else {
            $app->enqueueMessage(
                Text::_("COM_TIMECLOCK_PTO_DELETE_FAILED"), 'warning'
            );
        }
        $app->redirect($this->baselink);
        return true;
    }

}