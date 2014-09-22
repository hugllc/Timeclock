<?php
/**
 * This component is for tracking tim
 *
 * PHP Version 5
 *
 * <pre>
 * com_timeclock is a Joomla! 3.1 component
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: $Id: 1d23523e3892a5809ebfd024ca10359070d0803a $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once __DIR__."/report.php";

/**
 * Description Here
 *
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockModelsPayroll extends TimeclockModelsReport
{    
    /** This is our percentage of holiday pay */
    private $_holiday_perc = 1;
    /** This is where we store our totals */
    private $_totals = array(
        "total" => 0,
    );
    /** This is our percentage of holiday pay */
    private $_myusers = null;
    /**
    * The constructor
    */
    public function __construct()
    {
        $app = JFactory::getApplication();
        $this->_user = JFactory::getUser();
        parent::__construct(); 
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @param int $user_id The user to get the projects for
    * 
    * @return array An array of results.
    */
    public function listUsers()
    {
        if (is_null($this->_myusers)) {
            $this->_myusers = parent::listUsers();
            foreach ($this->_myusers as &$user) {
                $start = $this->getState("payperiod.start");
                $timesheetDone = isset($user->timeclock["timesheetDone"]) ? $user->timeclock["timesheetDone"] : 0;
                $user->done = TimeclockHelpersDate::compareDates($timesheetDone, $start) >= 0;
            }
        }
        return $this->_myusers;
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @return array An array of results.
    */
    public function lock()
    {
        $next = $this->getState("payperiod.next");
        return $this->setParam("payperiodCutoff", $next);
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @return array An array of results.
    */
    public function unlock()
    {
        $start = $this->getState("payperiod.start");
        return $this->setParam("payperiodCutoff", $start);
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @return array An array of results.
    */
    public function listItems()
    {
        $query = $this->_buildQuery();
        $query = $this->_buildWhere($query);
        $list = $this->_getList($query);
        $this->listUsers();
        $worked = array();
        $notes  = array();
        foreach ($list as $row) {
            $worked[$row->worked][] = $row;
        }
        $dates = $this->getState("payperiod.dates");
        $split = $this->getState("payperiod.splitdays");
        $period = -1;
        $days   = 0;
        $return = array(
            "totals" => array("total" => 0),
            "notes"  => $notes
        );
        foreach (array_keys($dates) as $date) {
            if (($days++ % $split) == 0) {
                $period++;
            }
            if (!isset($return["totals"][$period])) {
                $return["totals"][$period] = (object)array(
                    "worked"   => 0,
                    "pto"      => 0,
                    "holiday"  => 0,
                    "subtotal" => 0,
                );
            }
            if (!isset($worked[$date])) {
                continue;
            }
            foreach ($worked[$date] as $row) {
                $user_id = !is_null($row->user_id) ? (int)$row->user_id : (int)$row->worked_by;
                $row->user_id = $user_id;
                $return[$user_id] = isset($return[$user_id]) ? $return[$user_id] : array();
                if (!isset($return[$user_id][$period])) {
                    $return[$user_id][$period] = (object)array(
                        "worked"   => 0,
                        "pto"      => 0,
                        "holiday"  => 0,
                        "subtotal" => 0,
                    );
                }
                $this->checkTimesheet($row);
                $this->checkTimesheetUser($row);
                switch ($row->project_type) {
                case "HOLIDAY":
                    $return[$user_id][$period]->holiday += $row->hours;
                    $return["totals"][$period]->holiday    += $row->hours;
                    break;
                case "PTO":
                    $return[$user_id][$period]->pto += $row->hours;
                    $return["totals"][$period]->pto    += $row->hours;
                    break;
                default:
                    $return[$user_id][$period]->worked += $row->hours;
                    $return["totals"][$period]->worked    += $row->hours;
                    break;
                }
                $return[$user_id][$period]->subtotal += $row->hours;
                $return["totals"][$period]->subtotal    += $row->hours;
                $return["totals"]["total"]              += $row->hours;
                
                // Get the notes
                $notes[$user_id] = isset($notes[$user_id]) ? $notes[$user_id] : array();
                $notes[$user_id][$row->project_id] = isset($notes[$user_id][$row->project_id]) ? $notes[$user_id][$row->project_id] : array(
                    "project_id" => $row->project_id,
                    "project_name" => $row->project,
                    "worked" => array(),
                );
                $notes[$user_id][$row->project_id]["worked"][$row->worked] = $row;
            }
        }
        $return["notes"] = $notes;
        return $return;
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @param int $user_id The user to get the projects for
    * 
    * @return array An array of results.
    */
    protected function checkTimesheet(&$entry)
    {
        if ($entry->project_type == "HOLIDAY") {
            $entry->hours = $entry->hours * $this->_holiday_perc;
        }
        if (TimeclockHelpersDate::beforeStartDate($entry->worked, $entry->user_id)) {
            $entry->hours = 0;
        }
        if (TimeclockHelpersDate::afterEndDate($entry->worked, $entry->user_id)) {
            $entry->hours = 0;
        }
        // Round the hours
        $params = $this->getState("params");
        $decimals = empty($params->decimalPlaces) ? 2 : $params->decimalPlaces;
        $entry->hours = round($entry->hours, $decimals);
    }
    /**
    * Checks to make sure this project exists
    *
    * @param object &$row The row to check
    * 
    * @return array An array of results.
    */
    protected function checkTimesheetUser(&$row)
    {
        $user_id = !is_null($row->user_id) ? (int)$row->user_id : (int)$row->worked_by;
        $users = &$this->_users;
        // This adds in projects and categories that the user has time in,
        // but isn't currently a member.
        if (($row->hours > 0) && !isset($users[$user_id])) {
            $users[$user_id] = (object)array(
                "user_id" => $user_id,
                "name" => empty($row->user) ? "User $user_id" : $row->user,
            );
        }
    }
    /**
    * Method to auto-populate the model state.
    *
    * This method should only be called once per instantiation and is designed
    * to be called on the first call to the getState() method unless the model
    * configuration flag to ignore the request is set.
    * 
    * @param object $registry Ignored in subclasses
    * 
    * @return  void
    *
    * @note    Calling getState in this method will result in recursion.
    * @since   12.2
    */
    protected function populateState($registry = null)
    {
        $context = is_null($this->context) ? $this->table : $this->context;

        $app = JFactory::getApplication();
        $registry = $this->loadState();

        $user = JFactory::getUser();

        $date = TimeclockHelpersDate::fixDate(
            $app->input->get('date', date("Y-m-d"), "raw")
        );
        $date = empty($date) ?  date("Y-m-d") : $date;
        $registry->set('date', $date);
        
        $type = 'payroll';
        $registry->set('type', $type);
        
        // Get the pay period Dates
        $startTime = TimeclockHelpersTimeclock::getParam("firstViewPeriodStart");
        $len = TimeclockHelpersTimeclock::getParam("viewPeriodLength");
        $start = TimeclockHelpersDate::fixedPayPeriodStart($startTime, $date, $len);
        $registry->set("payperiod.days", $len);
        $registry->set("payperiod.start", $start);
        $registry->set("start", $start);
        $s = TimeclockHelpersDate::explodeDate($start);
        $end = TimeclockHelpersDate::dateUnix(
            $s["m"], $s["d"]+$len-1, $s["y"]
        );
        $registry->set("payperiod.end", date("Y-m-d", $end));
        $registry->set("end", date("Y-m-d", $end));
        
        $next = TimeclockHelpersDate::dateUnix(
            $s["m"], $s["d"]+$len, $s["y"]
        );
        $registry->set("payperiod.next", date("Y-m-d", $next));
        $prev = TimeclockHelpersDate::dateUnix(
            $s["m"], $s["d"]-$len, $s["y"]
        );
        $registry->set("payperiod.prev", date("Y-m-d", $prev));
        
        $cutoff = TimeclockHelpersTimeclock::getParam("payperiodCutoff");
        $registry->set("payperiod.cutoff", $cutoff);

        $locked = TimeclockHelpersDate::compareDates($cutoff, $next) >= 0;
        $registry->set("payperiod.locked", $locked);

        $unlock = TimeclockHelpersDate::compareDates($cutoff, $next) == 0;
        $registry->set("payperiod.unlock", $unlock);

        $dates = array_flip(TimeclockHelpersDate::payPeriodDates($start, $end));
        foreach ($dates as $date => &$value) {
            $value = true;
        }
        $registry->set("payperiod.dates", $dates);
        
        $this->_holiday_perc = ((int)TimeclockHelpersTimeclock::getUserParam("holidayperc", $user->id, $date)) / 100;
        $registry->set("holiday.perc", $this->_holiday_perc);

        $split = (int)TimeclockHelpersTimeclock::getParam("payPeriodSplitDays");
        $split = empty($split) ? 7 : $split;
        $registry->set("payperiod.splitdays", $split);

        $subtotals = (int)($len / $split);
        $registry->set("payperiod.subtotals", $subtotals);


        //$this->setState($registry);
        parent::populateState($registry);
    }
    /**
    * This function gets the dates of the period, and says wheter or not time can 
    * be added to it
    *
    * @param string $start  The first day of employment
    * @param string $end    The last day of employment
    * @param string $cutoff The last day locked down so time can't be entered
    *
    * @return array, keys are the dates, values are true if time can be added
    */
    private function _getPayPeriodDates($start, $end, $cutoff)
    {
    }
    /**
    * Builds the query to be used by the model
    *
    * @return object Query object
    */
    protected function _buildQuery()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(TRUE);
        $query->select('DISTINCT t.timesheet_id, t.user_id as worked_by,
            (t.hours1 + t.hours2 + t.hours3 + t.hours4 + t.hours5 + t.hours6)
            as hours, t.worked, t.project_id, t.notes, z.user_id as user_id');
        $query->from('#__timeclock_timesheet as t');
        $query->select('p.name as project, p.type as project_type, 
            p.description as project_description, p.parent_id as cat_id');
        $query->leftjoin('#__timeclock_projects as p on t.project_id = p.project_id');
        $query->select('q.name as cat_name, q.description as cat_description');
        $query->leftjoin('#__timeclock_projects as q on p.parent_id = q.project_id');
        $query->select('u.name as user');
        $query->leftjoin('#__timeclock_users as z on 
            ((z.user_id = t.user_id OR p.type = "HOLIDAY") AND t.project_id = z.project_id)');
        $query->leftjoin('#__users as u on z.user_id = u.id');
        return $query;
    }
    /**
    * Builds the filter for the query
    * 
    * @param object $query Query object
    * @param int    $id    The id of the object to get
    * 
    * @return object Query object
    *
    */
    protected function _buildWhere(&$query)
    { 
        $db = JFactory::getDBO();
        $start = $this->getState("payperiod.start");
        $end   = $this->getState("payperiod.end");
        $query->where($db->quoteName("t.worked").">=".$db->quote($start));
        $query->where($db->quoteName("t.worked")."<=".$db->quote($end));
        $query->where($db->quoteName("p.type")." <> 'UNPAID'");
        $query->order("t.worked asc");

        return $query;
    }
    /**
    * Builds the query to be used by the model
    *
    * @return object Query object
    */
    protected function _buildProjQuery()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(TRUE);
        $query->select('q.project_id as project_id, q.user_id as user_id');
        $query->from('#__timeclock_users as q');
        $query->select('p.project_id as project_id, 1 as mine, 
            p.name as name, p.parent_id as parent_id, p.description as description,
            p.type as type');
        $query->leftjoin('#__timeclock_projects as p on q.project_id = p.project_id');
        $query->select('r.name as parent_name, r.description as parent_description');
        $query->leftjoin('#__timeclock_projects as r on p.parent_id = r.project_id');
        $query->where('q.user_id = '.$db->quote($this->_user->id));
        $query->where('q.project_id > 0');
        $query->order("p.name asc");
        return $query;
    }
}