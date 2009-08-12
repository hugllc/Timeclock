<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_timeclock is a Joomla! 1.5 component
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
function Com_uninstall()
{
    $database =& JFactory::getDBO();
    $adminDir = dirname(__FILE__);

    // Move the modules back to the component so they get deleted with everything
    foreach (array("mod_timeclockmenu", "mod_timeclockinfo") as $file) {
        $to   = $adminDir.DS."modules".DS.$file;
        $from = JPATH_ROOT.DS."modules".DS.$file;
        rename($from, $to);
        $database->setQuery(
            "DELETE FROM `#__modules` WHERE `module`='".$file."';"
        );
        $database->query();
    }

}
?>