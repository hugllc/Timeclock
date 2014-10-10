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
 * @version    GIT: $Id: 6b8d5a6331c8adfcb151cb0c9b474d783d23a465 $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once __DIR__."/default.php";

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
class TimeclockModelsPto extends TimeclockModelsDefault
{
    /** Where Fields */
    protected $_pto_id   = null;
    protected $_published      = 1;
    protected $_total          = null;
    protected $_pagination     = null;
    protected $_defaultSort    = "o.pto_id";
    protected $_defaultSortDir = "desc";
    protected $table           = "TimeclockPto";

    /**
    * This is the constructor
    */
    public function __construct()
    {
        parent::__construct(); 
        $this->_pto_id = !empty($this->id) ? (int) reset($this->id) : null;
    }
    /**
    * Stores the data given, or request data.
    *
    * @param array $data The data to store.  If not given, get the post data
    *
    * @return JTable instance with data in it.
    */
    public function store($data=null)
    {
        $data = $data ? $data : JRequest::get('post');
        $data["valid_from"] = TimeclockHelpersDate::fixDate($data["valid_from"]);
        $to                 = TimeclockHelpersDate::explodeDate($data["valid_from"]);
        if ($data["type"] == "CARRYOVER") {
            $data["valid_from"] = $to["y"]."-01-01";
            if (!empty($data["valid_to"])) {
                $valid_to = TimeclockHelpersDate::fixDate($data["valid_to"]);
                $valid_to = TimeclockHelpersDate::explodeDate($valid_to);
                $data["valid_to"] = $to["y"]."-".$valid_to["m"]."-".$valid_to["d"];
            } else {
                $cutoff           = TimeclockHelpersTimeclock::getParam("ptoCarryOverDefExpire");
                $data["valid_to"] = $to["y"]."-".$cutoff;
            }
        } else {
            $data["valid_to"] = $to["y"]."-12-31";
        }
        return parent::store($data);

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
        $query->select('*');
        $query->from('#__timeclock_pto as o');
        $query->select('u.name as name');
        $query->leftjoin('#__users as u on o.user_id = u.id');
        $query->select('v.name as author');
        $query->leftjoin('#__users as v on o.created_by = v.id');
        return $query;
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @param string $start The date to start
    * @param string $end   The date to end
    * @param int    $id    The id of the user to accrue for
    * 
    * @return array An array of results.
    */
    public function getAccrual($start, $end, $id = null)
    {
        $id    = empty($id) ? $this->getUser()->id : (int)$id;
        return $this->_getPTO($start, $end, $id, "ACCRUAL", false);
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @param string $start The date to start
    * @param string $end   The date to end
    * @param int    $id    The id of the user to accrue for
    * 
    * @return array An array of results.
    */
    public function getManual($start, $end, $id = null)
    {
        $id    = empty($id) ? $this->getUser()->id : (int)$id;
        return $this->_getPTO($start, $end, $id, "MANUAL", false);
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @param string $start The date to start
    * @param string $end   The date to end
    * @param int    $id    The id of the user to accrue for
    * 
    * @return array An array of results.
    */
    public function getDonation($start, $end, $id = null)
    {
        $id    = empty($id) ? $this->getUser()->id : (int)$id;
        return $this->_getPTO($start, $end, $id, "DONATION", false);
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @param string $start The date to start
    * @param string $end   The date to end
    * @param int    $id    The id of the user to accrue for
    * 
    * @return array An array of results.
    */
    public function getCarryover($start, $end, $id = null)
    {
        $id    = empty($id) ? $this->getUser()->id : (int)$id;
        return $this->_getPTO($start, $end, $id, "CARRYOVER", false);
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @param int $year The year to get the PTO balance for
    * @param int $id   The id of the user to accrue for
    * 
    * @return array An array of results.
    */
    public function getPTO($year, $id = null)
    {
        $year      = (int)$year;
        $start     = "$year-01-01";
        $end       = "$year-12-31";
        $id        = empty($id) ? $this->getUser()->id : (int)$id;
        $decimals  = (int)TimeclockHelpersTimeclock::getParam("decimalPlaces");
        $regular   = $this->_getPTO($start, $end, $id, "CARRYOVER", true);
        $timesheet = TimeclockHelpersTimeclock::getModel("Timesheet");
        $worked    = (float)$timesheet->ptoTotal($id, $start, $end, true);
        $hours     = $regular - $worked + $this->_getCarryoverOffset($year, $id);
        $hours     = sprintf("%4.".$decimals."f", $hours);
        return (float)$hours;
    }
    /**
    * Gets the offset based on carryover
    *
    * @param int $year The year to get the PTO balance for
    * @param int $id   The id of the user to accrue for
    * 
    * @return array An array of results.
    */
    private function _getCarryoverOffset($year, $id)
    {
        $start = "$year-01-01";
        $pto_id = $this->find($id, $start, "CARRYOVER");
        if (is_null($pto_id)) {
            // No carryover
            return 0;
        }
        $carry = $this->getItem($pto_id);
        $end   = $carry->valid_to;
        $timesheet = TimeclockHelpersTimeclock::getModel("Timesheet");
        $taken = (float)$timesheet->ptoTotal($id, $start, $end, true);
        $hours = $taken - $carry->hours;
        if ($hours >= 0) {
            return $carry->hours;
        } else {
            return $taken;
        }
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @param string $start   The date to start
    * @param string $end     The date to end
    * @param int    $id      The id of the user to accrue for
    * @param string $type    The type to get
    * @param bool   $nottype Invert the type to get everything but that
    * 
    * @return array An array of results.
    */
    private function _getPTO($start, $end, $id, $type, $nottype = false)
    {
        $decimals  = (int)TimeclockHelpersTimeclock::getParam("decimalPlaces");
        $db    = JFactory::getDBO();
        $query = $db->getQuery(TRUE);
        $query->select('SUM(hours) as hours');
        $query->from('#__timeclock_pto');
        $query->where($db->quoteName("user_id")." = ".$db->quote($id));
        $query->where($db->quoteName("valid_from")." >= ".$db->quote($start));
        $query->where($db->quoteName("valid_from")." <= ".$db->quote($end));
        if ($nottype) {
            $query->where($db->quoteName("type")." <> ".$db->quote($type));
        } else {
            $query->where($db->quoteName("type")." = ".$db->quote($type));
        }
        $db->setQuery($query);

        $item = $db->loadObject();
        $hours     = sprintf("%4.".$decimals."f", $item->hours);
        return (float)$hours;
    }
    /**
    * Builds the query to be used by the model
    *
    * @param int    $user_id    The user ID to check
    * @param string $valid_from The date the PTO was logged
    * @param string $type       The type to look for
    * 
    * @return object Query object
    */
    protected function find($user_id, $valid_from, $type = null)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(TRUE);
        $query->select('pto_id');
        $query->from('#__timeclock_pto');
        $query->where($db->quoteName('user_id')." = ".$db->quote($user_id));
        $query->where($db->quoteName('valid_from')." = ".$db->quote($valid_from));
        if (!empty($type)) {
            $query->where($db->quoteName('type')." = ".$db->quote($type));
        }
        $db->setQuery($query);
        $res = $db->loadResult();
        return $res;
    }
    /**
    * Builds the query to be used to count the number of rows
    *
    * @return object Query object
    */
    protected function _buildCountQuery()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(TRUE);
        $query->select('COUNT(o.pto_id) as count');
        $query->from('#__timeclock_pto as o');
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
    protected function _buildWhere(&$query, $id = null)
    { 
        $db = JFactory::getDBO();
        $id = is_numeric($id) ? $id : $this->_pto_id;
        
        if(is_numeric($id)) {
            $query->where($db->quoteName('o.pto_id').' = ' . $db->quote((int) $id));
        }
        $filter = $this->getState("filter");
        if(!empty($filter->search) && is_string($filter->search)) {
            $query->where($db->quoteName("o.notes")." LIKE ".$db->quote("%".$filter->search."%"));
        }
        
        if (is_numeric($filter->year)) {
            $query->where($db->quoteName("o.valid_from")." >= " . $db->quote((int) $filter->year."-01-01"));
            $query->where($db->quoteName("o.valid_from")." <= " . $db->quote((int) $filter->year."-12-31"));
        }
        
        if (is_numeric($filter->user_id)) {
            $query->where($db->quoteName("o.user_id")." = " . $db->quote((int) $filter->user_id));
        }

        return $query;
    }
    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     *
     * @since   3.0
     */
    protected function getSortFields()
    {
        return array(
            'user',
            'author',
            'o.valid_from',
            'o.valid_to',
            'o.created',
            'o.modified',
            'o.pto_id'
        );
    }
    /**
    * Checks out this record
    * 
    * @param int $id The id of the item to check in
    * 
    * @return  boolean
    */
    public function publish($id = null)
    {
        return true;
    }
    /**
    * Checks out this record
    * 
    * @param int $id The id of the item to check in
    * 
    * @return  boolean
    */
    public function unpublish($id = null)
    {
        return true;
    }
    /**
    * Sets an accrual record for the
    * 
    * @param string $start The date to start
    * @param string $end   The date to end
    * @param int    $id    The id of the user to accrue for
    * 
    * @return  boolean
    */
    public function setAccrual($start, $end, $id = null)
    {
        $id     = empty($id) ? $this->getUser()->id : (int)$id;
        $period = trim(TimeclockHelpersTimeclock::getParam("ptoAccrualPeriod"));
        $days   = TimeclockHelpersDate::days($start, $end);
        $ret    = true;
        for ($d = 0; $d < $days;) {
            if (strtolower($period) == "month") {
                $len = $days;
            } else if (strtolower($period) == "year") {
                $len = $days;
            } else if (strtolower($period) == "week") {
                $ret = $ret && $this->_setAccrualWeek($start, $id);
                $len = 7;
            } else {
                $len = TimeclockHelpersTimeclock::getParam("payPeriodLengthFixed");
                $ret = $ret && $this->_setAccrualPayperiod($start, $id);
            }
            // The 8th day is the first day of the next week.
            $start = TimeclockHelpersDate::end($start, $len + 1);
            // Increment the days by $len.
            $d += $len;
            if (($days < $d + $len) || ($len <= 0)) {
                break;
            }
        }
        return $ret;
    }
    /**
    * Sets an accrual record for the
    * 
    * @param string $start The date to start
    * @param string $end   The date to end
    * @param int    $id    The id of the user to accrue for
    * 
    * @return  boolean
    */
    private function _setAccrualWeek($start, $id)
    {
        $end = TimeclockHelpersDate::end($start, 7);
        $hours  = $this->calcAccrual($start, $end, $id);
        $return = $this->_storeAccrual($end, $hours, $id);
        return $return;
    }
    /**
    * Sets an accrual record for the
    * 
    * @param string $start The date to start
    * @param string $end   The date to end
    * @param int    $id    The id of the user to accrue for
    * 
    * @return  boolean
    */
    private function _setAccrualPayperiod($start, $id)
    {
        $startTime = TimeclockHelpersTimeclock::getParam("firstPayPeriodStart");
        $len = TimeclockHelpersTimeclock::getParam("payPeriodLengthFixed");
        $period = TimeclockHelpersDate::fixedPayPeriod($startTime, $start, $len);
        $hours  = $this->calcAccrual($period["start"], $period["end"], $id);
        $return = $this->_storeAccrual($period["next"], $hours, $id);
        return $return;
    }
    /**
    * Sets an accrual record for the
    * 
    * @param string $start The date to start
    * @param string $end   The date to end
    * @param int    $id    The id of the user to accrue for
    * 
    * @return  boolean
    */
    private function _storeAccrual($date, $hours, $id)
    {
        if ($hours == 0) {
            // Don'e need to store this one.
            return true;
        }
        
        $row = new stdClass();
        $d = TimeclockHelpersDate::explodeDate($date);
        if (!is_object($row)) {
            return false;
        }
        $row->pto_id     = $this->find($id, $date);
        $row->type       = "ACCRUAL";
        $row->user_id    = $id;
        $row->hours      = $hours;
        $row->valid_from = $date;
        $row->valid_to   = $d["y"]."-12-31";
        $row->created_by = $this->getUser()->id;
        $row->created    = date("Y-m-d H:i:s");
        $row->modified   = $row->created;
        $row->notes      = "Automatic Accrual";
        
        return parent::store($row);
    }
    /**
    * Sets an accrual record for the
    * 
    * @param string $start The date to start
    * @param string $end   The date to end
    * @param int    $id    The id of the user to accrue for
    * 
    * @return  boolean
    */
    public function calcAccrual($start, $end, $id = null)
    {
        $user   = $this->getUser($id);
        $rate   = TimeclockHelpersTimeclock::getPtoAccrualRate($user->id, $end);
        $hours  = 0;
        if ($rate > 0) {
            $rate  *= (float)TimeclockHelpersTimeclock::getParam("ptoHoursPerDay");
            $d = TimeclockHelpersDate::explodeDate($end);
            $year   = (int)$d["y"];
            $ytd    = $this->getAccrual("$year-01-01", "$year-12-31", $id);
            $status = TimeclockHelpersTimeclock::getUserParam('status', $id);
            $hpy    = ($status == "FULLTIME") ? 2080 : 1040;
            $decimals  = (int)TimeclockHelpersTimeclock::getParam("decimalPlaces");
            $timesheet = TimeclockHelpersTimeclock::getModel("Timesheet");
            $worked    = (float)$timesheet->periodTotal($user->id, $start, $end, true);
            $hours     = ($rate / $hpy) * $worked;
            if (($hours + $ytd) > $rate) {
                $hours = $rate - $ytd;
                $hours = ($hours < 0) ? 0 : $hours;
            }
            $hours     = sprintf("%4.".$decimals."f", $hours);
        }
        return (float)$hours;
    }
}