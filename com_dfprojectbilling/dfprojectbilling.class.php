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
// This requires that the session be started.
@session_start();

if (!@include_once $mainframe->getPath('class', 'com_dfproject')) {
    die('com_dfproject is required for com_dfprojecttimeclock');
}

define("_HAVE_DFPROJECT_BILLING", true);

define("DFPROJECT_CONFIG_FILE", "config.inc.php");
require_once($mainframe->getPath('class', 'com_dfprefs'));


define("PLUGIN_BILLING_TABLE", '#__dfproject_billing');
define("PROJECT_AREA_NAME", 'dfProject');

class billing  extends mosDBTable {
    var $_tbl = '#__dfproject_billing';
    var $_tbl_key = "id";

    var $_period = null;

    var $id;
    var $company;
    var $name;
    var $address1;
    var $address2;
    var $city;
    var $state;
    var $zip;
    var $country;
    var $notes;

    function billing(&$db, $config = null) {
        $this->_db =& $db;
        if (!is_null($config)) $this->_config = $config;
    }


    function getRecord($id) {

        $this->load($id);
        foreach (get_object_vars($this) as $key => $val) {
            if (substr($key, 0, 1) != '_') {
                $cache[$key] = $val;
            }
        }
        
        return $cache;
        
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

    function prevnext()
{

        if (!empty($this->_period['prev']) && !empty($this->_period['next'])) {
            $pnTable = new HTML_Table(array('style' => 'width: 100%'));
            $nexticon = '<img src="'._PROJ_TIMECLOCK_IMGPATH.'go-next.png" alt="->" title="Next" style="border: none; float: right;" />';
            $previcon = '<img src="'._PROJ_TIMECLOCK_IMGPATH.'go-previous.png" alt="<-" title="Prev" style="border: none; float: left;" />';
            $prev = '<a href="'.getMyURL(array('Date')).htmlentities('Date='.date("Y-m-d", $this->_period['prev'])).'">'.$previcon.' &nbsp; '._CMN_PREV.'</a>';
            $now = '<a href="'.getMyURL(array('Date')).htmlentities('Date='.date("Y-m-d")).'">Today</a>';
            $next = '<a href="'.getMyURL(array('Date')).htmlentities('Date='.date("Y-m-d", $this->_period['next'])).'">'.$nexticon.' '._CMN_NEXT.' &nbsp;</a>';
    
            
            // Set up the next/prev dialog.
            $pnTable->setCellContents(0, 0, $prev);
            $pnTable->setCellAttributes(0, 0, array('style' => 'text-align: left; vertical-align: middle; width: 33%;'));
            $pnTable->setCellContents(0, 1, $now);
            $pnTable->setCellAttributes(0, 1, array('style' => 'text-align: center; vertical-align: middle; width: 33%;'));
            $pnTable->setCellContents(0, 2, $next);
            $pnTable->setCellAttributes(0, 2, array('style' => 'text-align: right; vertical-align: middle; width: 33%;'));
    
            return $pnTable->toHTML();
        } else {
            return "";
        }

    }

    function setPeriod($Date=null) {
        global $prefs;

        if (is_null($Date)) $Date = mosGetParam($_REQUEST, "Date", null);

        if (is_null($Date)) {
            $Date = time();
        } else if (is_string($Date)) {
            $Date = strtotime($Date);
        }

        $StartDate = mosGetParam($_REQUEST, "StartDate", null);
        $EndDate = mosGetParam($_REQUEST, "EndDate", null);

        if (!is_null($StartDate) && !is_null($EndDate)) $this->_periodType = "CUSTOM";


        // This makes sure daylight savings time doesn't effect us.
        $this->_Date = strtotime(date('Y-m-d', $Date));
        $Date = strtotime(date('Y-m-d 06:00:00', $Date));
        $return = array();

        $d['Y'] = date('Y', $Date);
        $d['m'] = date('m', $Date);
        $d['d'] = date('d', $Date);
        $d['t'] = date('t', $Date);
        
        switch($this->_periodType) {
            case 'CUSTOM':
                $return['start'] = strtotime($StartDate);
                $return['end'] = strtotime($EndDate);
                $return['prev'] = false;
                $return['next'] = false;
                break;
            case 'MONTHLY':
            default:
                $return['start'] = mktime(6,0,0, $d['m'], 1, $d['Y']);
                $return['end'] = mktime(6,0,0, $d['m'], $d['t'], $d['Y']);
                $return['prev'] = mktime(6,0,0, ($d['m'] - 1), 15, $d['Y']);
                $return['next'] = mktime(6,0,0, ($d['m'] + 1), 15, $d['Y']);

                break;
        }
        $this->_period = $return;
        return($return);
        
    }

    function getPeriod()
{
        if (is_null($this->_period)) $this->setPeriod();
        return($this->_period);
    }

    function setup_billing_report($customer) {
        global $dfconfig;

        if (is_null($this->_period)) $this->setPeriod($customer);    

        $query = "SELECT ";
        $query .= " #__dfproject_timesheet.id as id ";
        $query .= ", #__dfproject.id as project_id ";
        $query .= ", #__dfproject.name as project_name ";
        $query .= ", #__dfproject.type as project_type ";
        $query .= ", #__dfproject_timesheet.user_id as user_id ";
        $query .= ", #__users.name as user_name";
        $query .= ", SUM(#__dfproject_timesheet.hours) as totalHours ";
        $query .= " from #__dfproject_timesheet ";
        $query .= " LEFT JOIN #__users on #__users.id=#__dfproject_timesheet.user_id ";
        $query .= " LEFT JOIN #__dfproject on #__dfproject.id=#__dfproject_timesheet.project_id ";
        $query .= " WHERE ";
        $query .= " (#__dfproject_timesheet.Date>='".date("Y-m-d", $this->_period['start'])."' ";
        $query .= " AND ";
        $query .= " #__dfproject_timesheet.Date<='".date("Y-m-d", $this->_period['end'])."') ";
        if (isset($_REQUEST['research'])) {
        // Mark this as research only
            $query .= " AND ";
            $query .= " #__dfproject.research='YES'";
        }
        $query .= " AND #__dfproject.customer = '".$customer."' ";
        $query .= " GROUP BY #__dfproject.id, #__dfproject_timesheet.user_id ";
        $query .= " ORDER BY ";
        if (defined('_HAVE_DFPROJECT_WCOMP')) $query .= " #__dfproject.wcCode asc, ";
        $query .= " #__dfproject.id asc ";

        $this->_db->setQuery($query);
        $res = $this->_db->loadObjectList();

        $sheet = array();
        $users = array();
        $holiday = array();
        if (!is_array($res)) $res = array();
        foreach ($res as $ts) {
            if (!isset($sheet[$ts->project_id])) {
                $sheet[$ts->project_id]['project_name'] = $ts->project_name;
            }
            if (!isset($users[$ts->user_id])) $users[$ts->user_id] = $ts->user_name;
            if ($ts->project_type == "HOLIDAY") {
                $holiday[] = $ts;
            } else {
                $sheet[$ts->project_id][$ts->user_id] += $ts->totalHours;
            }
        }
        
        // Holidays have to be done last so we have all the users
        foreach ($holiday as $ts) {
            foreach ($users as $user_id => $name) {
                if (dfprefs::checkAccess('HolidayHours', null, $user_id)) {
                    $sheet[$ts->project_id][$user_id] += $ts->totalHours;
                }
            }
        }

        ksort($sheet);
        ksort($users);

        return array('users' => $users, 'sheet' => $sheet);

    }


}

?>
