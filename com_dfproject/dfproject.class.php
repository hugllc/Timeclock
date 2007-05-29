<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
    $Id: dfproject.class.php 538 2007-02-02 20:03:14Z prices $
    @file dfproject.class.php
    
    @verbatim
    Copyright 2005 Hunt Utilities Group, LLC (www.hugllc.com)
    
    dfproject.class.php is part of com_dfproject.

    com_dfproject is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    com_dfproject is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Foobar; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
    @endverbatim
*/
// This requires that the session be started.
@session_start();

define("DFPROJECT_CONFIG_FILE", "config.inc.php");
require_once( $mainframe->getPath( 'class', 'com_dfprefs' ) );


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
		        ." , id = '".$id."' ";
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
    
    function save( $source, $order_filter='' ) {
        if (!$this->bind( $source )) {
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
            $this->updateOrder( $order_filter ? "`$order_filter` = '$filter_value'" : '' );
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
