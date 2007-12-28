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
 * @subpackage Com_DfProject
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2005-2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id: sensor.php 545 2007-12-11 21:50:55Z prices $    
 * @link       https://dev.hugllc.com/index.php/Project:Timeclock
 */
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
// This requires that the session be started.
@session_start();

define("DFPROJECT_CONFIG_FILE", "config.inc.php");
require_once($mainframe->getPath('class', 'com_dfprefs'));


define("PLUGIN_PROJECTS_TABLE", '#__dfproject');
define("PLUGIN_WORKERSCOMP_TABLE", '#__dfproject_workers_comp');
define("PLUGIN_PROJECT_USERS_TABLE", '#__dfproject_users');
//define("PLUGIN_TIMESHEET_TABLE", $mosConfig_dbprefix.'timesheet');
//define("PLUGIN_TIMECLOCK_DEF_PERIOD", 14);
define("DAY_SECONDS", 86400);

define("PROJECT_AREA_NAME", 'dfProject');


class project  extends mosDBTable{
    var $_tbl = '#__dfproject';
    var $_tbl_key = "id";
    var $_users_tbl = '#__dfproject_users';
    var $_wc_tbl = '#__dfproject_workers_comp';

    var $id;
    var $name;
    var $description;
    var $user_id;
    var $date;
    var $research;
    var $status;
    var $type;
    var $parent_id;
    var $wcCode;
    var $customer;

    function project()
    {
        global $database;
        $this->_db = &$database;
        
        
    }
    function userOnProject($user_id, $id) {
        global $database;
        $query = "SELECT * FROM ".PLUGIN_PROJECT_USERS_TABLE
                ." WHERE "
                ." user_id = '".(int)$user_id."' "
                ." AND id = '".(int)$id."' ";
        $database->setQuery($query);                                        
        $res = $database->loadAssocList($query);
         return (bool) count($res);
    
    }

    function adduser($user_id, $id) {
        global $database;
        $query = "INSERT INTO ".$this->_users_tbl
                ." SET "
                ." user_id = '".$user_id."' "
                .", id = '".$id."' ";
        $database->setQuery($query);                                
         return $database->query($query);
    }

    function removeuser($user_id, $id) {
        global $database;
        $query = "DELETE FROM ".$this->_users_tbl
                ." WHERE "
                ." user_id = '".$user_id."' "
                ." AND "
                ." id = '".$id."' ";
        $database->setQuery($query);                                
         return $database->query($query);
    }
    
    function save($source, $order_filter='') {
        if (!$this->bind($source)) {
            return false;
        }
        if (!$this->check()) {
            return false;
        }
        if (!$this->store()) {
            return false;
        }

        if ($order_filter) {
            $filter_value = $this->$order_filter;
            $this->updateOrder($order_filter ? "`$order_filter` = '$filter_value'" : '');
        }
        $this->_error = '';
        return true;
    }

}

if (!defined(getMySQLDate)) {
function getMySQLDate($dateArray) {
        if (is_array($dateArray)) {
            $date = "";
            $sep = "";
            if (isset($dateArray['Y'])) {
                    $date .= $dateArray['Y'];
                    $sep = '-';
            } else if (isset($dateArray['y'])) {
                    $date .= $dateArray['y'];
                    $sep = '-';
            }

            if (isset($dateArray['m'])) {
                    $date .= $sep.$dateArray['m'];
                    $sep = '-';
            } else if (isset($dateArray['M'])) {
                    $date .= $sep.$dateArray['M'];
                    $sep = '-';
            }

            if (isset($dateArray['d'])) {
                    $date .= $sep.$dateArray['d'];
            }
        } else if (is_numeric($dateArray)) {
            $date = date('Y-m-d', $dateArray);
        } else if (is_string($dateArray)) {
            $date = date('Y-m-d', strtotime($dateArray));
        } else {
            $date = date("Y-m-d");
        }
        if (strtotime($date) == 0) $date = date("Y-m-d");
        
        return $date;
}
}
?>
