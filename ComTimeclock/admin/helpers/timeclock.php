<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.6 component
 * Copyright (C) 2008-2009, 2011 Hunt Utilities Group, LLC
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
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009, 2011 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die('Restricted access');

/**
 * ComTimeclock World Component Controller
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009, 2011 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockHelper
{
    /**
    * Configure the links below the header
    *
    * @param string $vName The name of the active view.
    * @param string $cName The name of the active controller.
    *
    * @return null
    */
    public static function addSubmenu($vName, $cName)
    {
        JSubMenuHelper::addEntry(
            JText::_(COM_TIMECLOCK_PREFERENCES),
            'index.php?option=com_timeclock&task=config.display',
            $cName == 'config'
        );
        JSubMenuHelper::addEntry(
            JText::_(COM_TIMECLOCK_USER_CONFIGS),
            'index.php?option=com_timeclock&task=users.display',
            $cName == 'users'
        );
        JSubMenuHelper::addEntry(
            JText::_(COM_TIMECLOCK_CUSTOMERS),
            'index.php?option=com_timeclock&task=customers.display',
            $cName == 'customers'
        );
        JSubMenuHelper::addEntry(
            JText::_(COM_TIMECLOCK_PROJECTS),
            'index.php?option=com_timeclock&task=projects.display',
            $cName == 'projects'
        );
        JSubMenuHelper::addEntry(
            JText::_(COM_TIMECLOCK_HOLIDAYS),
            'index.php?option=com_timeclock&task=holidays.display',
            $cName == 'holidays'
        );
        JSubMenuHelper::addEntry(
            JText::_(COM_TIMECLOCK_TIMESHEETS),
            'index.php?option=com_timeclock&task=timesheets.display',
            $cName == 'timesheets'
        );
        JSubMenuHelper::addEntry(
            JText::_(COM_TIMECLOCK_MISC_TOOLS),
            'index.php?option=com_timeclock&task=tools.display',
            $cName == 'tools'
        );
        JSubMenuHelper::addEntry(
            JText::_(COM_TIMECLOCK_ABOUT),
            'index.php?option=com_timeclock&view=about',
            $vName == 'about'
        );
    }
    /**
    * Title cell
    * For the title and toolbar to be rendered correctly,
    * this title fucntion must be called before the starttable function and
    * the toolbars icons this is due to the nature of how the css has been used
    * to postion the title in respect to the toolbar
    *
    * @param string $title The title
    *
    * @return none
    */
    function title($title)
    {
        $mainframe = JFactory::getApplication();

        $html  = "<div class=\"pagetitle\" style=\"background-image: url("
                ."components".DS."com_timeclock".DS."images".DS
                ."clock-48.png); background-repeat: no-repeat;\">\n";
        $html .= "<h2>$title</h2>";
        $html .= "</div>\n";

        $mainframe->set('JComponentTitle', $html);
    }
    /**
     * Get the actions
     */
    public static function getActions($messageId = 0)
    {
        $user   = JFactory::getUser();
        $result = new JObject;

        if (empty($messageId)) {
            $assetName = 'com_timeclock';
        }
        else {
            $assetName = 'com_timeclock.message.'.(int) $messageId;
        }

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action,   $user->authorise($action, $assetName));
        }

        return $result;
    }
    /**
     * Get Referrer
     *
     * @return string
     */
    function referer()
    {
        $referer = JRequest::getString('referer', "", 'post');
        if (!empty($referer)) {
            return $referer;
        }
        $referer = $_SERVER["HTTP_REFERER"];
        if (!empty($referer)) {
            return $referer;
        }
        return "index.php";

    }

}
?>
