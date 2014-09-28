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
    * Checks the user record, and adds anything extra needed
    *
    * @param object &$user The user object to check
    * 
    * @return array An array of results.
    */
    public function checkUser(&$user)
    {
        $start = $this->getState("payperiod.start");
        $timesheetDone = isset($user->timeclock["timesheetDone"]) ? $user->timeclock["timesheetDone"] : 0;
        $user->done = TimeclockHelpersDate::compareDates($timesheetDone, $start) >= 0;
        return true;
    }
    /**
    * Checks to see if there is a saved report and returns the ID
    * 
    * @param string $type The type of report to look for
    * @param string $name The name of the report
    *
    * @return int The ID of the report
    */
    private function _getReportID($type = null, $name = nulll)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(TRUE);
        $query->select('*');
        $query->from('#__timeclock_reports');
        $query->where($db->quoteName("type")." = ".$db->quote($type));
        $query->where($db->quoteName("name")." = ".$db->quote($name));
        $db->setQuery($query);
        $report = $db->loadObject();
        return (int)$report->report_id;
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
        $split = 7;
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
                        "overtime" => 0,
                    );
                }
                $this->checkTimesheet($row);
                $this->checkTimesheetUser($row);
                switch ($row->project_type) {
                case "HOLIDAY":
                    $return[$user_id][$period]->holiday += $row->hours;
                    break;
                case "PTO":
                    $return[$user_id][$period]->pto += $row->hours;
                    break;
                default:
                    $return[$user_id][$period]->worked += $row->hours;
                    break;
                }
                $return[$user_id][$period]->subtotal += $row->hours;
                
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
        $this->_checkOvertime($return);
        return $return;
    }
    /**
    * This checks for overtime and sets the overtime field if there is any
    *
    * @param array $data The data to check
    * 
    * @return array An array of results.
    */
    private function _checkOvertime(&$data)
    {
        $fulltime = $this->getState("payperiod.fulltimeHours");
        $users = $this->listUsers();
        foreach ($users as $user_id => &$user) {
            if (!isset($data[$user_id])) {
                continue;
            }
            foreach ($data[$user_id] as $period => &$wk) {
                if (!isset($data["totals"][$period])) {
                    $data["totals"][$period] = (object)array(
                        "worked"   => 0,
                        "pto"      => 0,
                        "holiday"  => 0,
                        "subtotal" => 0,
                        "overtime" => 0,
                    );
                }
                if ($wk->worked > $fulltime) {
                    $wk->overtime = $wk->worked - $fulltime;
                    $wk->worked = $fulltime;
                    $wk->subtotal = $wk->worked + $wk->holiday + $wk->pto;
                    if (($wk->pto > 0) && ($wk->overtime > 0)) {
                        $user->error .= JText::_("COM_TIMECLOCK_ERROR_PTO_AND_OVERTIME");
                    }
                } else {
                    $wk->overtime = 0;
                }
                $data["totals"][$period]->worked   += $wk->worked;
                $data["totals"][$period]->holiday  += $wk->holiday;
                $data["totals"][$period]->pto      += $wk->pto;
                $data["totals"][$period]->overtime += $wk->overtime;
                $data["totals"][$period]->subtotal += $wk->subtotal;
            }
            $data["totals"]["total"]           += $wk->subtotal;
        }
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
                "done" => false,
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

        
        $report_id = $app->input->get("report_id", 0, "int");
        if (empty($report_id)) {
            $date = TimeclockHelpersDate::fixDate(
                $app->input->get('date', date("Y-m-d"), "raw")
            );
            $date = empty($date) ?  date("Y-m-d") : $date;
            $registry->set('date', $date);
        } else {
            $registry->set("report.id", $report_id);
            $report = $this->getReport($report_id);
            $date   = $report->startDate;
            $registry->set('date', $date);
        }
        
        // Get the pay period Dates
        $startTime = TimeclockHelpersTimeclock::getParam("firstViewPeriodStart");
        $len = TimeclockHelpersTimeclock::getParam("viewPeriodLength");
        $start = TimeclockHelpersDate::fixedPayPeriodStart($startTime, $date, $len);
        $registry->set("payperiod.days", $len);
        $registry->set("payperiod.start", $start);
        $registry->set("start", $start);
        $s = TimeclockHelpersDate::explodeDate($start);
        $end = date(
            "Y-m-d", 
            TimeclockHelpersDate::dateUnix($s["m"], $s["d"]+$len-1, $s["y"])
        );
        $registry->set("payperiod.end", $end);
        $registry->set("end", $end);
        
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

        $fulltimeHours = TimeclockHelpersTimeclock::getParam("fulltimeHours");
        $registry->set("payperiod.fulltimeHours", $fulltimeHours);

        $locked = TimeclockHelpersDate::compareDates($cutoff, $next) >= 0;
        $registry->set("payperiod.locked", $locked);

        $unlock = TimeclockHelpersDate::compareDates($cutoff, $next) == 0;
        $registry->set("payperiod.unlock", $unlock);

        $dates = array_flip(TimeclockHelpersDate::payPeriodDates($start, $end));
        foreach ($dates as $date => &$value) {
            $value = true;
        }
        $registry->set("payperiod.dates", $dates);

        if (empty($report_id)) {
            $registry->set('report.name', "payroll $start $end");
            $registry->set('report.type', "payroll");
            $registry->set('report.description', JText::sprintf("COM_TIMECLOCK_PAYROLL_TITLE", $start, $end));

            $report_id = $this->_getReportID(
                $registry->get("report.type"), 
                $registry->get("report.name")
            );
            $registry->set("report.id", $report_id);
        }
        $this->_holiday_perc = ((int)TimeclockHelpersTimeclock::getUserParam("holidayperc", $user->id, $date)) / 100;
        $registry->set("holiday.perc", $this->_holiday_perc);

        $split = 7;
        $registry->set("payperiod.splitdays", $split);
        $split = 7;
        $registry->set("payperiod.splitdays", $split);

        $subtotals = (int)($len / $split);
        $registry->set("payperiod.subtotals", $subtotals);

        // This saves the payperiod information into the report
        $registry->set('filter', $registry->get('payperiod'));
        //$this->setState($registry);
        $this->_populateState($registry);
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
}