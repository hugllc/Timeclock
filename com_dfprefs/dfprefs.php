<?php
/**
 * Short Description
 *
 * PHP Version 5
 *
 * <pre>
 * Timeclock is a Joomla application to keep track of employee time
 * Copyright (C) 2007 Hunt Utilities Group, LLC
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage Com_DfPrefs
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2005-2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id: sensor.php 545 2007-12-11 21:50:55Z prices $    
 * @link       https://dev.hugllc.com/index.php/Project:Timeclock
 */
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
require_once($mainframe->getPath('class'));
require_once($mainframe->getPath('front_html'));

$option = mosGetParam($_REQUEST, 'option') ;

switch ($task) {

    case 'save':
        saveUser();
        mosRedirect("index.php?option=".$option);
    default:
        if (dfprefs::getUser("useredit", "com_dfprefs")) {
            editUser($option);
        } else {
            mosNotAuth();
        }
        break;
}

function editUser($option) {
    global $my;

    $area = mosGetParam($_REQUEST, 'area', null);
    $prefs = dfPrefs_define::get(null, "USER");

    // check to ensure only super admins can edit super admin info
    if (($my->gid < 25) && ($row->gid == 25)) {
        mosRedirect('index2.php?option=com_users', _NOT_AUTH);
    }
    
    dfprefs::flushCache();
    $values = dfprefs::get($my->id);

    HTML_dfprefs::editUser($option, $my, $prefs, $values, $area);

}

function saveUser()
{
    global $my;
    $newprefs = mosGetParam($_POST, 'dfprefs', array());
    
    foreach ($newprefs as $area => $prefs) {
        foreach ($prefs as $name => $value) {
            $ret = dfprefs::set($my->id, $name, $value, 'USER', 1, $area);
            var_dump($ret);
        }
    }    
}


HTML_dfprefs::copyright();

?>
