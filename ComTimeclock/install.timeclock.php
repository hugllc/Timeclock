<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_HUGnet is a Joomla! 1.5 component
 * Copyright (C) 2008-2009 Hunt Utilities Group, LLC
 * Copyright 2009 Scott Price
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


/**
 * This installs everthing
 *
 * @return null
 */
function Com_install()
{
    $database =& JFactory::getDBO();
    $adminDir = dirname(__FILE__);

    foreach (array("mod_timeclockinfo") as $file) {
        $from = $adminDir.DS."modules".DS.$file;
        $to   = JPATH_ROOT.DS."modules".DS.$file;
        rename($from, $to);
    }
    $database->setQuery(
        "INSERT INTO `jos_modules` (`title`, `content`, `ordering`,
        `position`, `checked_out`, `checked_out_time`, `published`, `module`,
        `numnews`, `access`, `showtitle`, `params`,
        `iscore`, `client_id`, `control`)
        VALUES ('Timeclock Information', '', 101,
        'left', 0, '0000-00-00 00:00:00', 1, 'mod_timeclockinfo',
        0, 0, 1, '',
        1, 0, '');"
    );
    $result = $database->query();

    $sql_files = array(
        "timeclock_customers",
        "timeclock_prefs",
        "timeclock_projects",
        "timeclock_timesheet",
        "timeclock_users",
    );
    // If debug is on under certain conditions this will crash because a query
    // failed.  This prevents that from happening.
    $debug = $database->get("_debug");
    $database->debug(0);
    foreach ($sql_files as $file) {
        $sql = file_get_contents($adminDir.DS."install".DS.$file.".sql");
        $q = explode(";", $sql);
        foreach ($q as $query) {
            if (!empty($query)) {
                $database->setQuery($query);
                $result = $database->query();
            }
        }
    }
    // Set debug back to what it was.
    $database->debug($debug);

}

?>