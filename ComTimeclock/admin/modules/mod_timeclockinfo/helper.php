<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * This module was copied and modified from mod_stats.  That is the reason for
 * the OSM Copyright.
 *
 * <pre>
 * mod_timeclockinfo is a Joomla! 1.5 module
 * Copyright (C) 2008-2009, 2011 Hunt Utilities Group, LLC
 * Copyright (C) 2009 Scott Price
 * Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
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
 * @package    Comtimeclock
 * @subpackage Com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009, 2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:Comtimeclock
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
$path = JPATH_ROOT.DS."components".DS."com_timeclock";

require_once $path.DS."models".DS."timeclock.php";

/**
* This class is the 'model' for the module.
*
* @category   UI
* @package    Comtimeclock
* @subpackage Com_timeclock
* @author     Scott Price <prices@hugllc.com>
* @copyright  2008-2009, 2011 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:Comtimeclock
*/
class ModTimeclockInfoHelper
{
    /**
    *  Sets the stuff to display for this module
    *
    * @param object $params The module parameters
    *
    * @return array A list to display
    */
    public function getDisplay($params)
    {
        $list = array();

        $decimalPlaces = TableTimeclockPrefs::getPref("decimalPlaces", "system");

        $user =& JFactory::getUser();
        $timeclockModel =& JModel::getInstance("Timeclock", "TimeclockModel");
        $userModel =& JModel::getInstance("Users", "TimeclockAdminModel");
        $ytdWhere = " `t`.`worked` >= '".date("Y")."-1-1'";
        $ytdhours = round($timeclockModel->getTotal($ytdWhere), $decimalPlaces);
        if ($params->get("showYTDHours") == 1) {
            $list[MOD_TIMECLOCKINFO_YTD] = $ytdhours." ".JText::_(MOD_TIMECLOCKINFO_HOURS;
        }
        $days = $timeclockModel->daysSinceStart();
        if ($days > date("z")) {
            $days = date("z");
        }
        $week = $days/7;
        if ($params->get("showHoursPerWeek") == 1) {
            $list[MOD_TIMECLOCKINFO_HOURS_PER_WEEK] = round($ytdhours / $week, $decimalPlaces);
        }
        $nextHoliday = $timeclockModel->getNextHoliday(
            "t.worked > '".date("Y-m-d")."'"
        );
        if ($nextHoliday == false) {
            $nextHoliday = JNONE;
        }
        if ($params->get("showNextHoliday") == 1) {
            $list[MOD_TIMECLOCKINFO_NEXT_HOLIDAY] = JHTML::_(
                'date', $nextHoliday,
                JText::_('DATE_FORMAT_LC')
            );
        }

        $ptoWhere = " `p`.`type` = 'PTO' AND `t`.`worked` >= '".date("Y-01-01")."'";
        $pto = round($timeclockModel->getTotal($ptoWhere), $decimalPlaces);
        $ptoYTD = round($userModel->getPTO($user->get("id")), $decimalPlaces);
        if ($params->get("showPTO") == 1) {
            $list[MOD_TIMECLOCKINFO_PTO] = $pto."/".$ptoYTD." ".JText::_(MOD_TIMECLOCKINFO_HOURS);
        }

        // Do stuff here
        return $list;

    }



}


?>
