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
 * Copyright (C) 2008-2009 Hunt Utilities Group, LLC
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
 * @package    ComHUGnet
 * @subpackage Com_HUGnet
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComHUGnet
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
$path = JPATH_ROOT.DS."components".DS."com_timeclock";

require_once $path.DS."models".DS."timeclock.php";



class modTimeclockInfoHelper
{
    public function getDisplay()
    {
        $list = array();

        $decimalPlaces = TableTimeclockPrefs::getPref("decimalPlaces", "system");

        $timeclockModel =& JModel::getInstance("Timeclock", "TimeclockModel");
        $ytdWhere = " `t`.`worked` >= '".date("Y")."-1-1'";
        $list["YTD Hours"] = round($timeclockModel->getTotal($ytdWhere), $decimalPlaces);
        $days = $timeclockModel->daysSinceStart();
        if ($days > date("z")) {
            $days = date("z");
        }
        $week = $days/7;
        $list["Hours/Week"] = round($list["YTD Hours"] / $week, $decimalPlaces);

        // Do stuff here
        return $list;

    }


}


?>
