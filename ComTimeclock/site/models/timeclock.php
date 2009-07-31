<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.5 component
 * Copyright (C) 2008-2009 Hunt Utilities Group, LLC
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
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/** Include the project stuff */
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'users.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'projects.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'customers.php';
require_once JPATH_COMPONENT_SITE.DS.'tables'.DS.'timeclocktimesheet.php';

/**
 * ComTimeclock model
 *
 * Dates are set by either the parameter "date" and a period type (i.e. "month")
 * or by two date parameters ("startDate" and "endDate").  This should be sent
 * on the URL.
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockModelTimeclock extends JModel
{

    /** @var string The type of period */
    protected $periods = array(
        "month" => array(
            "start" => "Y-m-01",
            "end" => "Y-m-t",
        ),
        "year" => array(
            "start" => "Y-01-01",
            "end" => "Y-12-31",
        ),
        "day" => array(
            "start" => "Y-m-d",
            "end" => "Y-m-d",
        ),
        "default" => array(
            "start" => "Y-m-01",
            "end" => "Y-m-t",
        ),
    );
    /** @var string The start date in MySQL format */
    protected $period = array(
        "type" => "payperiod",
    );
    /** @var int The project we are dealing with */
    private $_project = 0;

    /**
     * Constructor that retrieves the ID from the request
     *
     * @return    void
     */
    function __construct()
    {
        parent::__construct();

        $others = TableTimeclockPrefs::getPref("admin_otherTimesheets");
        if ($others) {
            $cid = JRequest::getVar('cid', 0, '', 'array');
        }
        if (empty($cid)) {
            $u =& JFactory::getUser();
            $cid = $u->get("id");
        }
        $this->setId($cid);

        $date = JRequest::getString('date', date("Y-m-d"));
        $this->setDate(TimeclockController::fixDate($date), "date", true);
        $startDate = JRequest::getString('startDate');
        $this->setPeriodDate($startDate, "start");
        $endDate = JRequest::getString('endDate');
        $this->setPeriodDate($endDate, "end");



        $project = JRequest::getVar('projid', 0, '', 'string');
        $this->setProject($project);
    }
    /**
     * Method to set the id
     *
     * @param int $id The ID of the Project to get
     *
     * @return    void
     */
    function setId($id)
    {
        if (is_array($id)) {
            $this->_id = (int)$id[0];
        } else {
            $this->_id = (int)$id;
        }
    }

    /**
     * Get the type of period
     *
     * @param string $data  to store
     * @param string $field the field to store it into
     *
     * @return string
     */
    function set($data, $field)
    {
        return $this->period[$field] = $data;
    }
    /**
     * Get the type of period
     *
     * @param string $data  to store
     * @param string $field the field to store it into
     *
     * @return string
     */
    function setUnix($data, $field)
    {
        return $this->period["unix"][$field] = $data;
    }
    /**
     * Where statement for the reporting period dates
     *
     * @param string $field The field to set
     *
     * @return array
     */
    function get($field)
    {
        return $this->period[$field];
    }
    /**
     * Where statement for the reporting period dates
     *
     * @param string $field The field to set
     *
     * @return array
     */
    function getUnix($field)
    {
        return $this->period["unix"][$field];
    }
    /**
     * Where statement for the reporting period dates
     *
     * @param string $date  Date to use if it is set in MySQL format ("Y-m-d")
     * @param string $field The field to save the date in
     * @param bool   $force Make it return a valid date no matter what
     *
     * @return null
     */
    function setDate($date, $field, $force=false)
    {
        $date = TimeclockController::fixDate($date);
        if (empty($date) && $force) {
            $date = date("Y-m-d");
        }
        $this->setUnix(TimeclockController::dateUnixSql($date), $field);
        return $this->set($date, $field);
    }

    /**
     * Where statement for the reporting period dates
     *
     * @param string $date  Date to use if it is set in MySQL format ("Y-m-d")
     * @param string $field The field to save the date in
     *
     * @return null
     */
    function setPeriodDate($date, $field)
    {
        $date = TimeclockController::fixDate($date);
        $this->setDate($date, $field);
        if ($this->get($field)) {
            return;
        }
        $date = $this->get("date");
        $type = $this->get("type");
        $method = "get".$type.$field;
        $unixDate = TimeclockController::dateUnixSql($date);
        if (method_exists($this, $method)) {
            $dateFormat = $this->$method($date);
        } else {
            $dateFormat = $this->periods[$type][$field];
        }
        if (empty($dateFormat)) {
            $dateFormat = $this->periods["default"][$field];
        }
        $date = date($dateFormat, $unixDate);

        return self::setDate($date, $field);
    }

    /**
     * Method to set the id
     *
     * @param int $project The project to set
     *
     * @return    void
     */
    function setProject($project)
    {
        $project = (int) $project;
        if (empty($project)) {
            $this->_project = null;
        } else {
            $this->_project = $project;
        }
    }
    /**
     * Gets this hugely complex SQL query
     *
     * @param string $where1 The where clause to add. Must NOT include "WHERE"
     * @param string $where2 The where clause to add. Must NOT include "WHERE"
     *
     * @return string
     */
    protected function sqlQuery($where1, $where2=null)
    {
        if (empty($where2)) {
            $where2 = $where1;
        }
        return "SELECT DISTINCT t.id as id,
            (t.hours1 + t.hours2 + t.hours3 + t.hours4 + t.hours5 + t.hours6)
            as hours,
            t.worked, t.project_id, t.notes,
            t.hours1 as hours1, t.hours2 as hours2, t.hours3 as hours3,
            t.hours4 as hours4, t.hours5 as hours5, t.hours6 as hours6,
            p.wcCode1 as wcCode1, p.wcCode2 as wcCode2, p.wcCode3 as wcCode3,
            p.wcCode4 as wcCode4, p.wcCode5 as wcCode5, p.wcCode6 as wcCode6,
            t.created_by as created_by, p.name as project_name, p.type as type,
            u.name as author, pc.name as category_name, c.company as company_name,
            c.name as contact_name, p.id as project_id, u.id as user_id
            FROM      #__timeclock_timesheet as t
            LEFT JOIN #__timeclock_projects as p on t.project_id = p.id
            LEFT JOIN #__timeclock_users as j on (j.id = p.id OR p.type != 'HOLIDAY')
            LEFT JOIN #__users as u on j.user_id = u.id
            LEFT JOIN #__timeclock_prefs as tp on tp.id = u.id
            LEFT JOIN #__timeclock_projects as pc on p.parent_id = pc.id
            LEFT JOIN #__timeclock_customers as c on p.customer = c.id
            WHERE
            (
                ".$where1." AND (p.type = 'PROJECT' OR p.type = 'PTO')
                AND (j.user_id = t.created_by OR j.user_id IS NULL)
            )
            OR
            (
                ".$where2." AND p.type = 'HOLIDAY'
                AND (
                    (t.worked >= tp.startDate)
                    AND ((t.worked <= tp.endDate) OR (tp.endDate = '0000-00-00'))
                )
            )
            ";
    }


    /**
     * Method to display the view
     *
     * @return string
     */
    function getTimesheetData()
    {
        if (empty($this->data)) {
            $where = array(
                "t.created_by = ".$this->_id,
                $this->employmentDateWhere("t.worked"),
                $this->periodWhere("t.worked"),
            );
            $holidaywhere = array(
                "j.user_id = ".$this->_id,
                $this->employmentDateWhere("t.worked"),
                $this->periodWhere("t.worked"),
            );
            $where = implode(" AND ", $where);
            $holidaywhere = implode(" AND ", $holidaywhere);
            $query = $this->sqlQuery($where, $holidaywhere);
            $this->data = $this->_getList($query);
            if (!is_array($this->data)) {
                return array();
            }
            foreach ($this->data as $k => $d) {
                if ($d->type != "HOLIDAY") {
                    continue;
                }
                $hperc = $this->getHolidayPerc($d->user_id, $d->worked);
                $this->data[$k]->hours =  $d->hours * $hperc;
            }
        }
        return $this->data;
    }

    /**
     * Gets the perc of holiday pay this user should get
     *
     * @param int    $id   The user id to check
     * @param string $date The date to check
     *
     * @return int
     */
    function getHolidayPerc($id, $date)
    {
        static $perc;
        $key = $id.$date;
        if (!isset($perc[$key])) {
            $hist = TableTimeclockPrefs::getPref("history", "user", $id);
            if (is_array($hist["admin_holidayperc"])) {
                ksort($hist["admin_holidayperc"]);
                foreach ($hist["admin_holidayperc"] as $d => $h) {
                    if (TimeclockController::compareDates($date, $d) < 0) {
                        $perc[$key] = $h/100;
                        break;
                    }
                }
            }
            if (!isset($perc[$key])) {
                $hperc = TableTimeclockPrefs::getPref(
                    "admin_holidayperc",
                    "user",
                    $id
                );
                $perc[$key] = $hperc / 100;
            }
        }
        return $perc[$key];
    }

    /**
     * Where statement for employment dates
     *
     * @param string $field The field to use
     *
     * @return string
     */
    function employmentDateWhere($field)
    {
        $dates = self::getEmploymentDates();
        return self::dateWhere($field, $dates["start"], $dates["end"]);
    }

    /**
     * Where statement for dates
     *
     * @param string $field The field to use
     * @param string $start The start date
     * @param string $end   The end date
     *
     * @return string
     */
    function dateWhere($field, $start, $end="")
    {
        $ret = "($field >= '$start'";

        if (($end != '0000-00-00') && !empty($end)) {
            $ret .= " AND $field <= '$end'";
        }
        $ret .= ")";
        return $ret;
    }

    /**
     * Where statement for employment dates
     *
     * @return array
     */
    function getEmploymentDates()
    {
        static $eDates;
        if (empty($eDates)) {
            $eDates = array(
                "start" => TimeclockController::fixDate(
                    TableTimeclockPrefs::getPref("startDate")
                ),
                "end"   => TimeclockController::fixDate(
                    TableTimeclockPrefs::getPref("endDate")
                ),
            );
        }
        return $eDates;
    }

    /**
     * Where statement for employment dates
     *
     * @return array
     */
    function getEmploymentDatesUnix()
    {
        static $eDatesUnix;
        if (empty($eDatesUnix)) {
            $eDatesUnix = self::getEmploymentDates();
            foreach ($eDatesUnix as $key => $val) {
                $eDatesUnix[$key] = TimeclockController::dateUnixSql($val);
            }
        }
        return $eDatesUnix;
    }

    /**
     * Where statement for the reporting period dates
     *
     * @param string $field The field to use
     *
     * @return string
     */
    function periodWhere($field)
    {
        $period = $this->getPeriodDates();
        return self::dateWhere($field, $period["start"], $period["end"]);
    }
    /**
     * Where statement for the reporting period dates
     *
     * @param string $date Date to use in MySQL format ("Y-m-d H:i:s").  If left
     *                      blank the date read from the request variables is used.
     *
     * @return array
     */
    function getPayPeriodStart($date)
    {
        return self::_getPayPeriodFixedStart($date);
    }
    /**
     * Where statement for the reporting period dates
     *
     * @param string $date Date to use in MySQL format ("Y-m-d H:i:s").  If left
     *                      blank the date read from the request variables is used.
     *
     * @return array
     */
    function getPayPeriodEnd($date)
    {
        return self::_getPayPeriodFixedEnd($date);
    }
    /**
     * Where statement for the reporting period dates
     *
     * @param string $date Date to use in MySQL format ("Y-m-d H:i:s").  If left
     *                      blank the date read from the request variables is used.
     *
     * @return array
     */
    function getQuarterStart($date)
    {
        $date = TimeclockController::explodeDate($date);
        if ($date["m"] < 4) {
            return date("Y-01-01");
        }
        if ($date["m"] < 7) {
            return date("Y-04-01");
        }
        if ($date["m"] < 10) {
            return date("Y-07-01");
        }
        return date("Y-10-01");
    }
    /**
     * Where statement for the reporting period dates
     *
     * @param string $date Date to use in MySQL format ("Y-m-d H:i:s").  If left
     *                      blank the date read from the request variables is used.
     *
     * @return array
     */
    function getQuarterEnd($date)
    {
        $date = TimeclockController::explodeDate($date);
        if ($date["m"] < 4) {
            return date("Y-03-31");
        }
        if ($date["m"] < 7) {
            return date("Y-06-30");
        }
        if ($date["m"] < 10) {
            return date("Y-09-30");
        }
        return date("Y-12-31");
    }

    /**
     * Where statement for the reporting period dates
     *
     * @param int $date The date in Mysql ("Y-m-d") format.
     *
     * @return array
     */
    private function _getPayPeriodFixedEnd($date)
    {

        $s = self::_getPayPeriodFixedStart($date);
        $s = TimeclockController::explodeDate($s);
        $length = TableTimeclockPrefs::getPref("payPeriodLengthFixed", "system");
        $this->set($length, "length");
        $end = self::_date($s["m"], $s["d"]+$length-1, $s["y"]);
        return $end;
    }

    /**
     * Where statement for the reporting period dates
     *
     * @return array
     */
    function getLength()
    {
        $startUnix = TimeclockController::dateUnixSql($this->get("start"));
        $endUnix = TimeclockController::dateUnixSql($this->get("end"));
        $length = (int)round(($endUnix - $startUnix) / 86400) + 1;
        return $this->set($length, "length");
    }

    /**
     * Where statement for the reporting period dates
     *
     * @return array
     */
    function getPeriodDates()
    {
        if (!$this->get("_done")) {
            $startDate =& $this->get("start");
            $endDate =& $this->get("end");
            $s = TimeclockController::explodeDate($startDate);
            $e = TimeclockController::explodeDate($endDate);

            $length = $this->getLength();
            // These are all of the dates in the pay period
            for ($i = 0; $i < $length; $i++) {
                $this->period['dates'][self::_date($s["m"], $s["d"]+$i, $s["y"])]
                    = TimeclockController::dateUnix($s["m"], $s["d"]+$i, $s["y"]);
            }

            // Get the start and end
            $this->setUnix(
                TimeclockController::dateUnix($s["m"], $s["d"]-$length, $s["y"]),
                "prev"
            );
            $this->setUnix(
                TimeclockController::dateUnix($s["m"], $s["d"]-1, $s["y"]),
                "prevend"
            );
            $this->setUnix(
                TimeclockController::dateUnix($e["m"], $e["d"]+1, $e["y"]),
                "next"
            );
            $this->setUnix(
                TimeclockController::dateUnix($e["m"], $e["d"]+$length, $e["y"]),
                "nextend"
            );
            $this->set(self::_date($this->getUnix('prev')), "prev");
            $this->set(self::_date($this->getUnix('prevend')), "prevend");
            $this->set(self::_date($this->getUnix('next')), "next");
            $this->set(self::_date($this->getUnix('nextend')), "nextend");
            $this->set(true, "_done");
        }
        return $this->period;
    }
    /**
     * Where statement for the reporting period dates
     *
     * @param int $date The date in Mysql ("Y-m-d") format.
     *
     * @return array
     */
    private function _getPayPeriodFixedStart($date)
    {
        // Get this date
        $uDate = TimeclockController::dateUnixSql($date);
        $d = TimeclockController::explodeDate($date);

        // Get the pay period start
        $startTime = TableTimeclockPrefs::getPref("firstPayPeriodStart", "system");
        $start = TimeclockController::dateUnixSql($startTime);

        // Get the length in days
        $len = TableTimeclockPrefs::getPref("payPeriodLengthFixed", "system");

        // Get the time difference in days
        $timeDiff = round(($uDate - $start) / 86400);
        $days = $timeDiff % $len;

        return self::_date($d["m"], ($d["d"] - $days), $d["y"]);
    }

    /**
     * Where statement for the reporting period dates
     *
     * @param int $m The month or the unix date if $d and $y are null
     * @param int $d The day
     * @param int $y The year
     *
     * @return array
     */
    private function _date($m, $d=null, $y=null)
    {
        if (!(is_null($d) && is_null($y))) {
            $m = TimeclockController::dateUnix($m, $d, $y);
        }
        return date("Y-m-d", $m);
    }


    /**
     * Method to display the view
     *
     * @return string
     */
    function getData()
    {
        $query = "SELECT t.*,
                  (t.hours1 + t.hours2 + t.hours3 + t.hours4 + t.hours5 + t.hours6)
                  as hours
                  FROM #__timeclock_timesheet as t
                  WHERE t.worked ='".$this->get("date")."'
                     AND t.created_by = '".$this->_id."'
                  ";
        $ret = $this->_getList($query);
        if (!is_array($ret)) {
            return array();
        }
        $data = array();
        foreach ($ret as $d) {
            $data[$d->project_id] = $d;
        }
        return $data;
    }


    /**
     * Checks in an item
     *
     * @return bool
     */
    function store()
    {
        $row = $this->getTable("TimeclockTimesheet");
        $timesheet = JRequest::getVar('timesheet', array(), '', 'array');
        $date = JRequest::getVar('date', '', '', 'string');
        $user =& JFactory::getUser();
        if (empty($date)) {
            return false;
        }
        $ret = true;
        foreach ($timesheet as $data) {
            $htotal = 0;
            for ($i = 1; $i < 7; $i++) {
                $data["hours".$i] = (float) $data["hours".$i];
                $htotal += $data["hours".$i];
            }
            // If there are no hours don't create a record.
            // If there is already a record allow 0 hours.
            if (empty($htotal) && empty($data["id"])) {
                continue;
            }
            // Remove white space from the notes
            $data["notes"] = trim($data["notes"]);
            $data["id"] = (int) $data["id"];
            $data["created_by"] = $user->get("id");
            $data["worked"] = $date;
            if (empty($data["created"])) {
                $data["created"] = date("Y-m-d H:i:s");
            }
            if (!$row->bind($data)) {
                $this->setError($this->_db->getErrorMsg());
                $ret = false;
                continue;
            }
            // Make sure the record is valid
            if (!$row->check()) {
                $this->setError($this->_db->getErrorMsg());
                $ret = false;
                continue;
            }

            // Store the web link table to the database
            if (!$row->store()) {
                $this->setError($this->_db->getErrorMsg());
                $ret = false;
                continue;
            }
        }
        return $ret;
    }

}

?>
