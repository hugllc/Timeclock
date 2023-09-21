<?php
/**
 * This component is for tracking tim
 *
 * PHP Version 5
 *
 * <pre>
 * com_timeclock is a Joomla! 3.1 component
 * Copyright (C) 2023 Hunt Utilities Group, LLC
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
 * @copyright  2023 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: $Id: 1d23523e3892a5809ebfd024ca10359070d0803a $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
namespace HUGLLC\Component\Timeclock\Site\Model;

use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use HUGLLC\Component\Timeclock\Site\Helper\DateHelper;
use HUGLLC\Component\Timeclock\Administrator\Helper\TimeclockHelper;

defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Description Here
 *
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2023 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class PayrollModel extends ReportModel
{    
    /** This is where we store our totals */
    private $_totals = array(
        "total" => 0,
    );
    /**
    * The constructor
    */
    public function __construct()
    {
        $app = Factory::getApplication();
        $this->_user = Factory::getUser();
        parent::__construct(); 
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @return array An array of results.
    */
    public function listUsers($blocked = 0)
    {
        $users = parent::listUsers($blocked);
        unset($users[0]);
        return $users;
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
        $db = Factory::getDBO();
        $query = $db->getQuery(TRUE);
        $query->select('*');
        $query->from('#__timeclock_reports');
        $query->where($db->quoteName("type")." = ".$db->quote($type));
        $query->where($db->quoteName("name")." = ".$db->quote($name));
        $db->setQuery($query);
        $report = $db->loadObject();
        return is_object($report) ? (int)$report->report_id : 0;
    }
    /**
    * This creates data for the store.  It should be overwritten by child classes
    * if they want to store a different set.
    *
    * @return Table instance with data in it.
    */
    protected function buildStore()
    {
        $app = Factory::getApplication();
        $row = Table::getInstance('TimeclockReports', 'Table');
        
        if (!is_object($row)) {
            return false;
        }
        $date  = date("Y-m-d H:i:s");
        $type  = $this->getState("report.type");
        $start = $this->getState("start");
        $end   = $this->getState("end");

        $row->name        = $this->getState("report.name");
        $row->created_by  = Factory::getUser()->id;
        $row->created     = $date;
        $row->description = $this->getState("report.description");
        $row->modified    = $date;
        $row->startDate   = $start;
        $row->endDate     = $end;
        $row->type        = $type;
        $row->filter      = json_encode($this->getFilter());
        $row->users       = json_encode($this->listUsers());
        $row->projects    = json_encode($this->listProjects());
        $row->timesheets  = json_encode($this->listItems());
        return $row;
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @return array An array of results.
    */
    public function lock()
    {
        return $this->setParam("payperiodCutoff", $this->getState("payperiod.next"));
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @return array An array of results.
    */
    public function setAccrual()
    {
        $prev = $this->getState("payperiod.prev");
        $next = $this->getState("payperiod.next");
        $end  = $this->getState("payperiod.end");
        $pto       = TimeclockHelper::getModel("pto");
        $users     = $this->listUsers();
        foreach ($users as $user) {
            $pto->setAccrual($prev, $end, $user->id);
        }
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
        $users  = array();
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
                if ($row->hours == 0) {
                    continue;
                }
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
                // Hours could get modified by the above two calls, so this is here.
                if ($row->hours == 0) {
                    continue;
                }
                switch ($row->project_type) {
                case "HOLIDAY":
                case "FLOATING_HOLIDAY":
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
                if (!$users[$user_id]) {
                    $users[$user_id] = $this->getTimesheetUser($user_id);
                }
            }
        }
        $return["notes"] = $notes;
        $return["users"] = $this->_fixUsers($users);

        $this->_checkOvertime($return);
        return $return;
    }
    /**
     * Fixes the user array so the right number are there, and they are in name order
     * 
     * @param object &$users The user array to fix
     * 
     * @return void
     */
    private function _fixUsers(&$users) 
    {
        foreach ($this->listUsers() as $key => $user) {
            if (!isset($users[(int)$key])) {
                $users[(int)$key] = $this->getTimesheetUser($key);
            }
        }
        uasort(
            $users,
            function ($user1, $user2) {
                return strcmp($user1->name, $user2->name);
            }
        );
        return $users;
    }
    /**
    * Checks to make sure this project exists
    *
    * @param object &$row The row to check
    * 
    * @return array An array of results.
    */
    protected function getTimesheetUser($id = NULL)
    {
        $user = $this->getUser($id);
        $start = $this->getState("payperiod.start");
        $timesheetDone = isset($user->timeclock["timesheetDone"]) ? $user->timeclock["timesheetDone"] : 0;
        $user->done = DateHelper::compareDates($timesheetDone, $start) >= 0;
        $eend = !empty($user->timeclock["endDate"]) ? $user->timeclock["endDate"] : 0;

        $valid = (DateHelper::compareDates($eend, $this->getState('payperiod.start'))  >= 0);
        if (($eend != 0) && !$valid) {
            return false;
        }
        if (($user->block) && ($eend == 0)) {
            $user->error .= Text::_("COM_TIMECLOCK_ERROR_USER_DISABLED_NO_END");
        }
        return $user;
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
        foreach ($data["users"] as $user_id => &$user) {
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
                        $user->error .= Text::_("COM_TIMECLOCK_ERROR_PTO_AND_OVERTIME");
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
    protected function populateState()
    {
        $context = is_null($this->context) ? $this->table : $this->context;

        $app = Factory::getApplication();

        $user = Factory::getUser();

        $report_id = $app->input->get("report_id", 0, "int");
        if (empty($report_id)) {
            $date = DateHelper::fixDate(
                $app->input->get('date', date("Y-m-d"), "raw")
            );
            $date = empty($date) ?  date("Y-m-d") : $date;
            $this->setState('date', $date);
        } else {
            $this->setState('report.id', $report_id);
            $rep = $this->getReport($report_id);
            $date   = $rep->startDate;
            $this->setState('date', $date);
        }
        
        // Get the pay period Dates
        $startTime = TimeclockHelper::getParam("firstPayPeriodStart");
        $len = TimeclockHelper::getParam("payPeriodLengthFixed");
        $start = DateHelper::fixedPayPeriodStart($startTime, $date, $len);
        $this->setState('payperiod.days', $len);
        $this->setState('payperiod.start', $start);
        $this->setState("start", $start); 
        $s = DateHelper::explodeDate($start);
        $end = date(
            "Y-m-d", 
            DateHelper::dateUnix($s["m"], $s["d"]+$len-1, $s["y"])
        );
        $this->setState('payperiod.end', $end);
        $this->setState("end", $end);
        
        $next = DateHelper::dateUnix(
            $s["m"], $s["d"]+$len, $s["y"]
        );
        $this->setState('payperiod.next', date("Y-m-d", $next));
        $prev = DateHelper::dateUnix(
            $s["m"], $s["d"]-$len, $s["y"]
        );
        $this->setState('payperiod.prev', date("Y-m-d", $prev));
        
        $cutoff = TimeclockHelper::getParam("payperiodCutoff");
        $this->setState('payperiod.cutoff', $cutoff);

        $fulltimeHours = TimeclockHelper::getParam("fulltimeHours");
        $this->setState('payperiod.fulltimeHours', $fulltimeHours);

        $locked = DateHelper::compareDates($cutoff, $next) >= 0;
        $this->setState('payperiod.locked', $locked);

        $unlock = DateHelper::compareDates($cutoff, $next) == 0;
        $this->setState('payperiod.unlock', $unlock);

        $dates = array_flip(DateHelper::payPeriodDates($start, $end));
        foreach ($dates as $date => &$value) {
            $value = true;
        }
        $this->setState('payperiod.dates', $dates);

        if (empty($report_id)) {
            $type = "payroll";
            $name = "payroll $start $end";
            $this->setState('report.name', $name);
            $this->setState('report.type', $type);
            $this->setState('report.description', Text::sprintf("COM_TIMECLOCK_PAYROLL_TITLE", $start, $end));

            $report_id = $this->_getReportID(
                $type, 
                $name
            );
            $this->setState('report.id', $report_id);
        }

        $split = 7;
        $this->setState('payperiod.splitdays', $split);
        $split = 7;
        $this->setState('payperiod.splitdays', $split);

        $subtotals = (int)($len / $split);
        $this->setState('payperiod.subtotals', $subtotals);
    }
    /** 
    * Method to get the filter object
    *
    * @return object The property where specified, the state object where omitted
    */
    public function getPayperiod()
    {
        $payperiod = new \stdClass();
        foreach ($this->getState() as $key => $value) {
            if (str_contains($key, "payperiod.")) {
                $k = explode(".", $key)[1];
                $payperiod->$k = $value;
            }
        }

        return $payperiod;
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
        $db = Factory::getDBO();
        $start = $this->getState("payperiod.start");
        $end   = $this->getState("payperiod.end");
        $query->where($db->quoteName("t.worked").">=".$db->quote($start));
        $query->where($db->quoteName("t.worked")."<=".$db->quote($end));
        $query->where($db->quoteName("p.type")." <> 'UNPAID'");
        $query->order("t.worked asc");

        return $query;
    }
}
