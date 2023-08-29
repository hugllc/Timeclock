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
 * Copyright (C) 2023 Hunt Utilities Group, LLC
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
 * @copyright  2023 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: $Id: 63be8a20801842e16c4bbf0f8d5747063f81b4df $
 * @link       https://dev.hugllc.com/index.php/Project:Comtimeclock
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;

/**
* This class is the 'model' for the module.
*
* @category   UI
* @package    Comtimeclock
* @subpackage Com_timeclock
* @author     Scott Price <prices@hugllc.com>
* @copyright  2023 Hunt Utilities Group, LLC
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
    static public function getDisplay($params)
    {
        $list = array();

        $decimals  = TimeclockHelpersTimeclock::getParam("decimalPlaces");
        $timesheet = TimeclockHelpersTimeclock::getModel("timesheet");
        $pto       = TimeclockHelpersTimeclock::getModel("pto");
        $year      = date("Y");
        $start     = "$year-01-01";
        $end       = date("Y-m-d");
        $user      = $timesheet->getUser();
        $ytd       = $timesheet->periodTotal($user->id, $start, $end, false);

        if ($params->get("showYTDHours") == 1) {
            $list["MOD_TIMECLOCKINFO_YTD"] = round($ytd, $decimals);
        }
        $days = date("z");
        $week = $days/7;
        if ($params->get("showHoursPerWeek") == 1) {
            $list["MOD_TIMECLOCKINFO_HOURS_PER_WEEK"] = round($ytd / $week, $decimals);
        }
        $pto = round($pto->getPTO($year), $decimals);
        if ($params->get("showPTO") == 1) {
            $list["MOD_TIMECLOCKINFO_PTO_BALANCE"] = $pto." ".Text::_("MOD_TIMECLOCKINFO_HOURS");
        }

        // Do stuff here
        return $list;

    }



}


?>
