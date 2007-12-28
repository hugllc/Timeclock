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
 * @subpackage Com_DfProjectTimeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2005-2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id: sensor.php 545 2007-12-11 21:50:55Z prices $    
 * @link       https://dev.hugllc.com/index.php/Project:Timeclock
 */
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

if (!@include_once $mainframe->getPath('class', 'com_dfproject')) {
    die('com_dfproject is required for com_dfprojecttimeclock');
}
@include_once $mainframe->getPath('class', 'com_dfprojectwcomp');
require_once($mosConfig_absolute_path."/includes/sef.php");

define("_HAVE_DFPROJECT_TIMECLOCK", true);

define("PLUGIN_TIMESHEET_TABLE", '#__dfproject_timesheet');
define("PLUGIN_TIMECLOCK_DEF_PERIOD", 14);
define("DAY_SECONDS", 86400);
define('SHORTDATEFORMAT', " Y-m-d ");
define('TIMEFORMAT', " h:i a ");

define("_PROJ_BASEPATH", dirname($mainframe->getPath('class', 'com_dfprefs')));
define("_PROJ_TIMECLOCK_IMGPATH", sefRelToAbs("components/com_dfprojecttimeclock/images/"));

class timesheet extends mosDBTable{
    var $_tbl = '#__dfproject_timesheet';
    var $_tbl_key = 'id';

    var $_dateField = 'Date';

    var $_periodStart = "2000-12-11";
    var $_periodType = "FIXED";
    var $_periodLength = 14;
    var $_maxDailyHours = 19;
    var $_decimalPlaces = 2;

    // These are the field variables
    var $id;
    var $project_id;
    var $user_id;
    var $hours;
    var $Date;
    var $insertDate;
    var $Notes;

    function timesheet($config = null) {
        global $database;
        $this->_db =& $database;
        if (is_null($config)) $config = dfprefs::getSystem();

        $this->_config = $config;

        $this->_maxDailyHours = $config['maxhours'];
        $this->_periodStart = $config['periodstart'];
        $this->_periodType = $config['periodtype'];
        $this->_periodLength = $config['periodlength'];
        $this->_decimalPlaces = $config['decimalPlaces'];
        
    }

    function getUserStartDate($id = null) {
        global $my;
        if (is_null($id)) $id = $my->id;    

        $startdate = dfprefs::getUser("startDate", null, $id);
        $startdate = strtotime($startdate);
        return $startdate;
    }

    function checkUserStartDate($date, $id = null) {
        global $my;
        if (is_string($date)) $date = strtotime($date);
        if (is_null($id)) $id = $my->id;
        
        $startdate = $this->getUserStartDate($id);
        return ($date >= $startdate);
    }

    function getHours($user_id, $Date) {
        $query = "SELECT * ";
        $query .= " from #__dfproject_timesheet ";
        $query .= " WHERE ";
        $query .= " Date='".$Date."'";
        $query .= " AND ";
        $query .= ' #__dfproject_timesheet.user_id='.$user_id;

        $this->_db->setQuery($query);
        $res = $this->_db->loadAssocList();

        return $res;   
    }

    function getPeriod($Start, $End) {
        foreach (array('Start', 'End') as $Date) {
            if (empty($$Date)) {
                $$Date = time();
            } else if (is_string($$Date)) {
                $$Date = strtotime($$Date);
            }
            // This makes sure daylight savings time doesn't effect us.
            $$Date = strtotime(date('Y-m-d 06:00:00', $$Date));
        }
        $periodLength = round(abs($End - $Start)/86400)+1;

        $startDay = date('d', $Start);
        $prevDay = $startDay - $periodLength;
        $prevDayEnd = $prevDay + $periodLength-1;
        $nextDay = $startDay + $periodLength;
        $nextDayEnd = $nextDay + $periodLength-1;
        $endDay = $nextDay - 1;
        // Get the start and end
        $return['start'] = mktime(6,0,0, date("m", $Start), $startDay, date('Y', $Start));
        $return['end'] = mktime(6,0,0, date("m", $Start), $endDay, date('Y', $Start));
        $return['prev'] = mktime(6,0,0, date("m", $Start), $prevDay, date('Y', $Start));
        $return['prevend'] = mktime(6,0,0, date("m", $Start), $prevDayEnd, date('Y', $Start));
        $return['next'] = mktime(6,0,0, date("m", $Start), $nextDay, date('Y', $Start));
        $return['nextend'] = mktime(6,0,0, date("m", $Start), $nextDayEnd, date('Y', $Start));
        $return['periodlength'] = $periodLength;

        $this->period = $return;
        return($return);
    }

    function setPeriod($Start, $End) {
    
        $period = timesheet::getPeriod($Start, $End);
        // Add where fields
//        $this->addWhere($this->table.".".$this->_dateField.">='".date("Y-m-d", $period['start'])."'");
//        $this->addWhere($this->_dateField."<='".date("Y-m-d", $period['end'])."'");    
    }
    
    function getPayPeriodWhere($Date=null) {
        
        $period = timesheet::getPayPeriod($Date);
        // Add where fields
//        $this->addWhere($this->_dateField.">='".date("Y-m-d", $period['start'])."'");
//        $this->addWhere($this->_dateField."<='".date("Y-m-d", $period['end'])."'");    
        $where = " (".$this->_tbl.".".$this->_dateField.">='".date("Y-m-d", $period['start'])."' ";
        $where .= " AND ";
        $where .= $this->_tbl.".".$this->_dateField."<='".date("Y-m-d", $period['end'])."') ";
        return $where;
    }

    function getUserStartWhere()
{
        $startdate = dfprefs::getUser("startDate", null, $id);
        $startdate = strtotime($startdate);

        $where .= $this->_tbl.".".$this->_dateField.">='".date("Y-m-d", $startdate)."' ";
        return $where;
    }
    
    function getPayPeriod($Date=null) {
        global $prefs;

        if (empty($Date)) {
            $Date = time();
        } else if (is_string($Date)) {
            $Date = strtotime($Date);
        }
        // This makes sure daylight savings time doesn't effect us.
        $this->Date = strtotime(date('Y-m-d', $Date));
        $Date = strtotime(date('Y-m-d 06:00:00', $Date));
        $return = array();

        switch($this->_periodType) {
            case 'FIXED':
            default:
                // Get the pay period start
                $payPeriodStart = !empty($this->_periodStart) ? strtotime($this->_periodStart) : time();
                // Get the length
                $payPeriodLength = !empty($this->_periodLength) ? $this->_periodLength : PLUGIN_TIMECLOCK_DEF_PERIOD;
                $return['length'] = $payPeriodLength;
                // In Seconds
                $payPeriodLengthSec = $payPeriodLength * 86400;

                // Get the time difference in seconds
                $timeDiff = $Date - $payPeriodStart;
                // Get the offset to the end of the payperiod
                $timeDiff = ($timeDiff % $payPeriodLengthSec);

//                $startDay = date('d', $Date - $timeDiff);
//                $prevDay = $startDay-$payPeriodLength;
//                $nextDay = $startDay + $payPeriodLength;
//                $endDay = $nextDay - 1;
//                // Get the start and end
//                $return['start'] = mktime(6,0,0, date("m", $Date), $startDay, date('Y', $Date));
//                $return['end'] = mktime(6,0,0, date("m", $Date), $endDay, date('Y', $Date));
//                $return['prev'] = mktime(6,0,0, date("m", $Date), $prevDay, date('Y', $Date));
//                $return['next'] = mktime(6,0,0, date("m", $Date), $nextDay, date('Y', $Date));

                $start = strtotime(date("Y-m-d H:i:s", $Date - $timeDiff));
                $end = strtotime(date("Y-m-d H:i:s", $start+$payPeriodLengthSec-DAY_SECONDS));
                
/*
                $return['start'] = strtotime(date("Y-m-d H:i:s", $Date - $timeDiff));
                $return['next'] = strtotime(date("Y-m-d H:i:s", $return['start']+$payPeriodLengthSec));
                $return['prev'] = strtotime(date("Y-m-d H:i:s", $return['start']-$payPeriodLengthSec));
                $return['end'] = strtotime(date("Y-m-d H:i:s", $return['next']-DAY_SECONDS));
*/
                break;
        }
        $this->period = $this->getPeriod($start, $end);
        return($this->period);
    }
    
    function prepare_timesheet($user_id) {
        global $dfconfig;
        
        $query = "SELECT * ";
        $query .= ", #__dfproject_timesheet.id as id ";
        $query .= ", #__dfproject.id as project_id ";
        $query .= ", #__dfproject_timesheet.Date as Date ";
        $query .= " from #__dfproject_timesheet ";
        $query .= " LEFT JOIN #__dfproject on #__dfproject.id=#__dfproject_timesheet.project_id ";
        $query .= " WHERE ";
        $query .= $this->getPayPeriodWhere($this->Date);
        $query .= " AND ";
        $query .= $this->getUserStartWhere();
        $query .= " AND ";
        $query .= '(#__dfproject_timesheet.user_id='.$user_id;
        if (dfprefs::checkAccess('HolidayHours')) {
            $query .= " OR ";
            $query .= "#__dfproject.type='HOLIDAY'";        
        } else {
            $query .= " AND ";
            $query .= "#__dfproject.type<>'HOLIDAY'";                
        }
        $query .= ") AND ";
        $query .= " hours > 0";
        $query .= " ORDER BY #__dfproject_timesheet.id asc ";                        
        $this->_db->setQuery($query);
        $res = $this->_db->loadObjectList();

//        $res = $this->_db->getArray($query);
        if (!is_array($res)) $res = array();

        $sheet = array();
    
        $query = "SELECT *, #__dfproject_users.user_id as user_id, #__dfproject.user_id as owner_id";
        $query .= " from #__dfproject_users ";
        $query .= " JOIN #__dfproject on #__dfproject.id=#__dfproject_users.id ";
        $query .= " WHERE ";
        $query .= ' (#__dfproject_users.user_id='.$user_id;
        $query .= " AND ";
        $query .= " (#__dfproject.type='VACATION' "; 
        $query .= " OR ";
        $query .= "#__dfproject.type='SICK'";
        $query .= " OR ";
        $query .= "#__dfproject.type='HOLIDAY'";
        $query .= " OR ";
        $query .= "#__dfproject.status='ACTIVE'))";
        if (dfprefs::checkAccess('HolidayHours')) {
            $query .= " OR ";
            $query .= "#__dfproject.type='HOLIDAY'";
        
        }
//                $projRes = $this->_db->getArray($query);

        $this->_db->setQuery($query);
        $projRes = $this->_db->loadObjectList();

//                    arrayHtmlEntities($projRes);

        if (!is_array($projRes)) $projRes = array();

        // Add all the users projects to the sheet
        foreach ($projRes as $s) {
            if (empty($sheet[$s->id]['name'])) {
                
                $sheet[$s->id] = get_object_vars($s);
                $sheet[$s->id]['name'] = $this->projectName($s);
                if (($s->type == 'HOLIDAY') && ($user_id == $s->user_id)) {
                    $sheet[$s->id]['setTime'] = true;
                } else if ($s->type != 'HOLIDAY') {
 //                   $sheet[$s->id]['setTime'] = true;
                    $sheet[$s->id]['setTime'] = $this->setTime($s);
                }

            }
        }
    
        
        // Add the time to the sheet
        foreach ($res as $s) {
            if (!isset($sheet[$s->project_id]['name'])) {
                $sheet[$s->project_id] = get_object_vars($s);
                $sheet[$s->project_id]['name'] = $this->projectName($s);
            }
            $sheet[$s->project_id][$s->Date] += round($s->hours, $this->_decimalPlaces);
            $sheet[$s->project_id][$s->Date."_Notes"] .= $s->Notes;
        }

        // Make it a tree...
        foreach ($sheet as $k => $p) {
            if (($p['status'] == 'SPECIAL') && empty($p['parent_id'])) $p['parent_id'] = -2;

            if ($p['parent_id'] != 0) {
                
                if (!isset($sheet[$p['parent_id']])) {
                    // This get a parent that we don't have access to.  It
                    // forces the type to "UMBRELLA" so we can't add hours to
                    // it, but it is displayed correctly.
                    $query = "SELECT * ";
                    $query .= " from #__dfproject ";
                    $query .= " WHERE ";
                    $query .= ' #__dfproject.id='.$p['parent_id'];
                    $this->_db->setQuery($query);
                    $pproj = $this->_db->loadObjectList();
                    $sheet[$p['parent_id']] = get_object_vars($pproj[0]);
                    $sheet[$p['parent_id']]['type'] = "UMBRELLA";
                }
                $sheet[$p['parent_id']]['subProjects'][$k] = $p;
                unset($sheet[$k]);
            }
        }
        // Put everything without subprojects into one category...
    
        foreach ($sheet as $k => $p) {
            if ($p['parent_id'] == 0) {
                if (!isset($p['subProjects'])) {
                    $sheet[-1]['subProjects'][] = $p;
                    unset($sheet[$k]);
                }
            }
        }
        if (isset($sheet[-1])) {
            $sheet[-1]['name'] = 'Projects';    
            $sheet[-1]['type'] = 'UMBRELLA';        
        }
        if (isset($sheet[-2])) {
            $sheet[-2]['name'] = 'Special';    
            $sheet[-2]['type'] = 'UMBRELLA';        
        }
        ksort($sheet);    
        foreach (array_keys($sheet) as $k) {
            if (is_array($sheet[$k]['subProjects'])) {
                ksort($sheet[$k]['subProjects']);
            }
        }

        return $sheet;
    }


    function notePopup($text, $note) {
        $tip = str_replace("\n", ' ', $note);
        $tip = str_replace("\r", ' ', $tip);
        // Two are required here because we need an actual \ in the string that is printed.
        $tip = addslashes(addslashes($tip));
        $tip = htmlentities($tip, ENT_QUOTES);

        $text = str_replace("\n", ' ', $text);
        $text = str_replace("\r", ' ', $text);
        $text = addslashes(addslashes($text));
        $text = htmlentities($text, ENT_QUOTES);
        
        $header = "Notes:";
        return mosToolTip($tip, $header, '', '', $text, '', false);
    
    }

    function projectName($proj) {
        $tip = str_replace("\n", ' ', $proj->description);
        $tip = str_replace("\r", ' ', $tip);
        // Two are required here because we need an actual \ in the string that is printed.
        $tip = addslashes(addslashes($tip));
        $tip = htmlentities($tip, ENT_QUOTES);

        $name = $proj->name;
        $name = str_replace("\n", ' ', $name);
        $name = str_replace("\r", ' ', $name);
        $name = addslashes(addslashes($name));
        $name = htmlentities($name, ENT_QUOTES);
        
        $header = $proj->id.'. '.$name;
        if (defined('_HAVE_DFPROJECT_WCOMP')) $header .= ' ('.$proj->wcCode.')';
        return mosToolTip($tip, $header, '', '', $name, '', false);

    }
    function prepare_summary()
{
        global $dfconfig;
        $query = "SELECT ";
        $query .= " #__dfproject_timesheet.id as id ";
        $query .= ", #__dfproject.id as project_id ";
        $query .= ", #__dfproject.name as project_name ";
        $query .= ", #__dfproject.type as project_type ";
        $query .= ", #__dfproject_timesheet.user_id as user_id ";
        $query .= ", #__dfproject_timesheet.Date as date ";
        $query .= ", #__users.name as user_name";
        $query .= ", #__dfproject_timesheet.hours as hours ";
        $query .= " from #__dfproject_timesheet ";
        $query .= " LEFT JOIN #__users on #__users.id=#__dfproject_timesheet.user_id ";
        $query .= " LEFT JOIN #__dfproject on #__dfproject.id=#__dfproject_timesheet.project_id ";
        $query .= " WHERE ";
        $query .= " (#__dfproject_timesheet.Date>='".date("Y-m-d", $this->period['start'])."' ";
        $query .= " AND ";
        $query .= " #__dfproject_timesheet.Date<='".date("Y-m-d", $this->period['end'])."') ";
        if (isset($_REQUEST['research'])) {
        // Mark this as research only
            $query .= " AND ";
            $query .= " #__dfproject.research='YES'";
        }
//        $query .= " GROUP BY #__dfproject.id, #__dfproject_timesheet.user_id ";
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
            if ($this->checkUserStartDate($ts->date, $ts->user_id)) {
                if (!isset($sheet[$ts->project_id])) {
                    $sheet[$ts->project_id]['project_name'] = $ts->project_name;
                }
                if (!isset($users[$ts->user_id])) $users[$ts->user_id] = $ts->user_name;
                if ($ts->project_type == "HOLIDAY") {
                    $holiday[] = $ts;
                } else {
                    $sheet[$ts->project_id][$ts->user_id] += round($ts->hours, $this->_decimalPlaces);
                }
            }
        }
        
        // Holidays have to be done last so we have all the users
        foreach ($holiday as $ts) {
            foreach ($users as $user_id => $name) {
                if (dfprefs::checkAccess('HolidayHours', null, $user_id)) {
                    if ($this->checkUserStartDate($ts->date, $user_id)) {
                        $sheet[$ts->project_id][$user_id] += round($ts->hours, $this->_decimalPlaces);
                    }
                }
            }
        }

        ksort($sheet);
        ksort($users);
        return array('users' => $users, 'sheet' => $sheet);

    }
    
    function prepare_wcsummary()
{
        if (defined('_HAVE_DFPROJECT_WCOMP')) {
            global $dfconfig;
            $query = "SELECT ";
            $query .= " #__dfproject_timesheet.id as id ";
            $query .= ", #__dfproject.wcCode as  wcCode";
            $query .= ", #__dfproject_timesheet.user_id as user_id ";
            $query .= ", #__users.name as user_name";
            $query .= ", #__dfproject_timesheet.hours as hours ";
            $query .= ", #__dfproject_timesheet.Date as date ";
            $query .= " from #__dfproject_timesheet ";
            $query .= " LEFT JOIN #__users on #__users.id=#__dfproject_timesheet.user_id ";
            $query .= " LEFT JOIN #__dfproject on #__dfproject.id=#__dfproject_timesheet.project_id ";
            $query .= " WHERE ";
            $query .= " (#__dfproject_timesheet.Date>='".date("Y-m-d", $this->period['start'])."' ";
            $query .= " AND ";
            $query .= " #__dfproject_timesheet.Date<='".date("Y-m-d", $this->period['end'])."') ";
            if (isset($_REQUEST['research'])) {
            // Mark this as research only
                $query .= " AND ";
                $query .= " #__dfproject.research='YES'";
            }
            $query .= " AND #__dfproject.type <> 'HOLIDAY' ";
//            $query .= " GROUP BY #__dfproject.wcCode, #__dfproject_timesheet.user_id ";
            $query .= " ORDER BY #__dfproject.wcCode asc ";
            $query .= ", #__dfproject.id asc ";
    //        $res = $this->_db->getArray($query);

            $this->_db->setQuery($query);
            $res = $this->_db->loadObjectList();

            $sheet = array();
            $codes = array();
            $users = array();
            if (!is_array($res)) $res = array();
            foreach ($res as $ts) {
                if ($this->checkUserStartDate($ts->date, $ts->user_id)) {
                    if (!isset($sheet[$ts->user_id])) {
                        $sheet[$ts->user_id]['user_name'] = $ts->user_name;
                    }
                    if (!isset($users[$ts->user_id])) $users[$ts->user_id] = $ts->user_name;            
                    if (!isset($codes[$ts->wcCode])) $codes[$ts->wcCode] = $ts->wcCode;            
                    $sheet[$ts->user_id][$ts->wcCode] += round($ts->hours, $this->_decimalPlaces);
                }
            }
    
            $query = str_replace("<> 'HOLIDAY'", "= 'HOLIDAY'", $query);
            $this->_db->setQuery($query);
            $res = $this->_db->loadObjectList();
    
            if (!is_array($res)) $res = array();
            foreach ($res as $ts) {
                if ($this->checkUserStartDate($ts->date, $ts->user_id)) {
                    if (!isset($sheet[$ts->user_id])) {
                        $sheet[$ts->user_id]['user_name'] = $ts->user_name;
                    }
                    if (!isset($users[$ts->user_id])) $users[$ts->user_id] = $ts->user_name;            
                    if (!isset($codes[$ts->wcCode])) $codes[$ts->wcCode] = $ts->wcCode;            
                    foreach ($users as $user_id => $name) {
                        if (dfprefs::checkAccess('HolidayHours', null, $user_id)) {
                            $sheet[$user_id][$ts->wcCode] += $ts->hours;
                        }
                    }
                }
            }
    
    
            ksort($sheet);
            ksort($codes);
            return array('codes' => $codes, 'sheet' => $sheet);
        } else {
            return array();
        }
    }

    function prepare_notes($user_id=null) {
        $query = "SELECT ";
        $query .= " #__dfproject.name as project_name ";
        $query .= ", #__dfproject.id as project_id ";
        $query .= ", #__dfproject_timesheet.user_id as user_id";
        $query .= ", #__dfproject_timesheet.Date as date";
        $query .= ", #__dfproject_timesheet.Notes as note";
        $query .= ", #__dfproject_timesheet.hours as hours";
        $query .= ", #__users.name as user_name";
        $query .= " from #__dfproject_timesheet";
        $query .= " LEFT JOIN #__dfproject on #__dfproject.id=#__dfproject_timesheet.project_id ";
        $query .= " LEFT JOIN #__users on #__dfproject_timesheet.user_id=#__users.id ";
        $query .= " WHERE ";
        $query .= " (#__dfproject_timesheet.Date>='".date("Y-m-d", $this->period['start'])."' ";
        $query .= " AND ";
        $query .= " #__dfproject_timesheet.Date<='".date("Y-m-d", $this->period['end'])."') ";
        if ($user_id !== null) {
            $query .= ' AND #__dfproject_users.user_id='.$user_id;
        }
        if ($proj_id !== null) {
               $query .= " AND #__dfproject.id=".$proj_id;
           }
           $query .= " AND #__dfproject_timesheet.Hours > 0 ";
           $query .= " AND #__dfproject.type<>'HOLIDAY' ";
        $query .= " ORDER BY ";
        $query .= " #__dfproject.id asc ";
        $query .= ", #__dfproject_timesheet.user_id asc ";
        $query .= ", #__dfproject_timesheet.Date asc ";

        $this->_db->setQuery($query);
        $res = $this->_db->loadObjectList();
        if (!is_array($res)) $res = array();

        foreach ($res as $key => $val) {
            $this->check_user($res[$key]); 
            if (!$this->checkUserStartDate($val->date, $ts->user_id)) {
                unset($res[$key]);
            }        
        }

        return $res;
   
    }

    function prepare_week_totals()
{
        $return = array();
        global $dfconfig;
        $query = "SELECT ";
        $query .= " #__dfproject_timesheet.id as id ";
        $query .= ", #__dfproject.id as project_id ";
        $query .= ", #__dfproject.name as project_name ";
        $query .= ", #__dfproject.type as project_type ";
        $query .= ", #__dfproject_timesheet.user_id as user_id ";
        $query .= ", #__dfproject_timesheet.Date as date ";
        $query .= ", #__users.name as user_name";
        $query .= ", SUM(#__dfproject_timesheet.hours) as hours ";
        $query .= " from #__dfproject_timesheet ";
        $query .= " LEFT JOIN #__users on #__users.id=#__dfproject_timesheet.user_id ";
        $query .= " LEFT JOIN #__dfproject on #__dfproject.id=#__dfproject_timesheet.project_id ";
        $query .= " WHERE ";
        $query .= " (#__dfproject_timesheet.Date>='".date("Y-m-d", $this->period['start'])."' ";
        $query .= " AND ";
        $query .= " #__dfproject_timesheet.Date<='".date("Y-m-d", $this->period['end'])."') ";
        if (isset($_REQUEST['research'])) {
        // Mark this as research only
            $query .= " AND ";
            $query .= " #__dfproject.research='YES'";
        }
        $query .= " GROUP BY #__dfproject_timesheet.user_id, #__dfproject_timesheet.Date ";
        $query .= " ORDER BY ";
        if (defined('_HAVE_DFPROJECT_WCOMP')) $query .= " #__dfproject.wcCode asc, ";
        $query .= " #__dfproject.id asc ";

        $this->_db->setQuery($query);
        $res = $this->_db->loadObjectList();
        
        $users = array();
        $temp = array();
        if (is_array($res)) {
            foreach ($res as $row) {
                $this->check_user($row);
                if (!isset($users[$row->user_id])) $users[$row->user_id] = $row->user_name;
                if ($row->project_type == "HOLIDAY") {
                    $holiday[] = $row;
                } else {
                    $temp[$row->user_name][$row->date] += round($row->hours, $this->_decimalPlaces);
                }
            }
        }
        
        // Holidays have to be done last so we have all the users
        if (is_array($holiday)) {
            foreach ($holiday as $row) {
                foreach ($users as $user_id => $name) {
                    if (dfprefs::checkAccess('HolidayHours', null, $user_id)) {
                        if ($this->checkUserStartDate($row->date, $user_id)) {
                            $temp[$name][$row->date] += round($row->hours, $this->_decimalPlaces);
                        }
                    }
                }
            }
        }

        $days = 1;
        $week = 1;

        for ($d = $this->period['start']; $d <= $this->period['end']; $d += 86400) {
            foreach (array_keys($temp) as $user) {
                if (!isset($return[$user]['user_name'])) $return[$user]['user_name'] = $user;
                $return[$user]['week'.$week] += $temp[$user][date('Y-m-d', $d)];
            }
            if (($days % 7) == 0) $week++;
            $days++;
        }
        return $return;
    }

    function check_user(&$row) {
        if (empty($row->user_name)) $row->user_name = "User #".$row->user_id; 
    }

    function prepare_add($user_id) {
        $projects = array();

        $proj_id = mosGetParam($_REQUEST, 'project_id', null);
        $query = "SELECT ";
        $query .= " #__dfproject.* ";
        $query .= ", #__dfproject_users.* ";
        $query .= " from #__dfproject_users";
        $query .= " LEFT JOIN #__dfproject on #__dfproject.id=#__dfproject_users.id ";
        $query .= " WHERE ";
        $query .= ' #__dfproject_users.user_id='.$user_id;

        if ($proj_id !== null) {
            $query .= " AND ";
               $query .= "#__dfproject.id=".$proj_id;
           }
        $query .= " AND ";
           $query .= " (#__dfproject.status='ACTIVE' OR #__dfproject.status='SPECIAL') ";
           
        $query .= " ORDER BY #__dfproject.id asc";

        $this->_db->setQuery($query);
        $projRes = $this->_db->loadAssocList();

        if (!is_array($projRes)) $projRes = array();
        foreach ($projRes as $s) {
            if ($this->setTime($s) === true) {
                if (empty($projects[$s['id']]['name'])) {
                    $projects[$s['id']] = $s;
                    $projects[$s['id']]['name'] = $s['name'];
                    if ($s['status'] == 'SPECIAL') {
                        if (empty($projects[$s['id']]['parent_id'])) {
                                $projects[$s['id']]['parent_id'] = -2;
                        }
                    }
                }
            }
        }
        // Make it a tree...
        foreach ($projects as $k => $p) {
            if (($p['status'] == 'SPECIAL') && empty($p['parent_id'])) $p['parent_id'] = -2;

            if ($p['parent_id'] != 0) {
                
                if (!isset($projects[$p['parent_id']])) {
                    // This get a parent that we don't have access to.  It
                    // forces the type to "UMBRELLA" so we can't add hours to
                    // it, but it is displayed correctly.
                    $query = "SELECT * ";
                    $query .= " from #__dfproject ";
                    $query .= " WHERE ";
                    $query .= ' #__dfproject.id='.$p['parent_id'];
                    $this->_db->setQuery($query);
                    $pproj = $this->_db->loadObjectList();
                    $projects[$p['parent_id']] = get_object_vars($pproj[0]);
                    $projects[$p['parent_id']]['type'] = "UMBRELLA";
                }
                $projects[$p['parent_id']]['subProjects'][$k] = $p;
                unset($projects[$k]);
            }
        }
        // Put everything without subprojects into one category...
        foreach ($projects as $k => $p) {
            if ($p['parent_id'] == 0) {
                if (!isset($p['subProjects'])) {
                    $projects[-1]['subProjects'][$p['id']] = $p;
                    unset($projects[$k]);
                }
            }
        }
        if (isset($projects[-1])) {
            $projects[-1]['name'] = 'Projects';    
            $projects[-1]['type'] = 'UMBRELLA';        
        }
        if (isset($projects[-2])) {
            $projects[-2]['name'] = 'Special';    
            $projects[-2]['type'] = 'UMBRELLA';        
        }
        ksort($projects);    
        foreach (array_keys($projects) as $k) {
            if (is_array($projects[$k]['subProjects'])) {
                ksort($projects[$k]['subProjects']);
            }
        }

        
        return $projects;
    }
    
    function setTime($proj) {
        if (is_array($proj)) {
            $type = $proj['type'];
        } else if (is_object($proj)) {
            $type = $proj->type;
        }
        if (($type != 'UMBRELLA')) {
            return true;
        }
        return false;                
    }
    
    
    
    function getSQLDate($dateArray) {
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
        }
        if (empty($date)) {
                $date = $dateArray;
        }
        return $date;
    }

    function checkHours($hours) {
        return $hours < $this->_maxDailyHours; 
    }

    function save($source, $order_filter='') {
            if (!$this->bind($source)) {
                    return false;
            }

            if ($source[$this->_tbl_key] === null) {
                $this->{$this->_tbl_key} = null;
            }
            
            if (!$this->check()) {
                    return false;
            }
            if (!$this->store()) {
                    return false;
            }
            $this->_error = '';
            return true;
    }

    function getVacation($id = null) {
        global $my;
        if (is_null($id)) $id = $my->id;    

        $accrual = $this->getVAccrual($id);
        switch (trim(strtolower($this->_config['vacationAccrual']))) {
            default:
            case "weekly":
                $vh = $accrual * (int) date('W');
                break;
        }
        return $vh;
    }
    
    function getVAccrual($id = null) {
        global $my;
        if (is_null($id)) $id = $my->id;    
        $table = $this->_config['vacationAccrualTable'];
        $temp = explode("\n", $table);
        $service = $this->getServiceLength(time(), $id, "y");
        $etype = dfprefs::getUser("employeeType", null, $id);
        foreach ($temp as $line) {
            $row = explode(",", $line);
            if ((int)$row[0] > (int)$service) {
                switch(trim(strtoupper($etype))) {
                    case "FULLTIME":
                        $accrual = $row[1];
                        break;
                    case "PARTTIME":
                        $accrual = $row[2];
                        break;
                    default:
                        $accrual = 0;
                        break;
                }
                break;
            }
        }
        return $accrual;
    }
    
    function getServiceLength($date, $id = null, $units="s") {
        global $my;
        if (is_string($date)) $date = strtotime($date);
        if (is_null($id)) $id = $my->id;
        $startdate = $this->getUserStartDate($id);
        $time = $date - $startdate;
        switch(trim(strtolower($units))) {
            case "y":
                $time /= 365.25;
            case "d":
                $time /= 24;
            case "h":
                $time /= 60;
            case "m":
                $time /= 60;
            case "s":
            default:
                break;
        }
        return $time;
    }
}



?>
