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
 * @version    GIT: $Id: a70fad7ecea96c148fd07befe386dd1bba7cfe4f $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
namespace HUGLLC\Component\Timeclock\Administrator\Trait;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Pagination\Pagination;
use HUGLLC\Component\Timeclock\Administrator\Helper\TimeclockHelper;
use HUGLLC\Component\Timeclock\Site\Helper\DateHelper;

defined( '_JEXEC' ) or die();

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
trait PtoDBTrait
{
    /** Fields filter/sort works on */
    protected $filterFields = array(
        'user',
        'author',
        'o.valid_from',
        'o.valid_to',
        'o.created',
        'o.modified',
        'o.pto_id'
);
    /**
    * Builds the query to be used by the model
    *
    * @return object Query object
    */
    protected function _buildQuery()
    {
        $db = Factory::getDBO();
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
    * Builds the query to be used to count the number of rows
    *
    * @return object Query object
    */
    protected function _buildCountQuery()
    {
        $db = Factory::getDBO();
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
    protected function _buildWhere(&$query)
    { 
        $db = Factory::getDBO();

        $search = $this->getState("filter.search");
        if(!empty($search) && is_string($search)) {
            $query->where($db->quoteName("u.name")." LIKE ".$db->quote("%".$search."%"));
        }
        
        $year = $this->getState("filter.year");
        if (is_numeric($year)) {
            $query->where($db->quoteName("o.valid_from")." >= " . $db->quote((int) $year."-01-01"));
            $query->where($db->quoteName("o.valid_from")." <= " . $db->quote((int) $year."-12-31"));
        }
        
        $user_id = $this->getState("filter.user_id");
        if (is_numeric($user_id)) {
            $query->where($db->quoteName("o.user_id")." = " . $db->quote((int) $user_id));
        }

        return $query;
    }
    /**
    * Builds the filter for the query
    * @param object Query object
    * @return object Query object
    *
    */
    protected function _setSort(&$query)
    {

        $order = $this->getState('list.ordering', 'o.pto_id');
        $dir = $this->getState('list.direction', 'DESC');
        $query->order($order.' '.$dir);
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
        $id    = empty($id) ? Factory::getUser()->id : (int)$id;
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
        $id    = empty($id) ? Factory::getUser()->id : (int)$id;
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
        $id    = empty($id) ? Factory::getUser()->id : (int)$id;
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
        $id    = empty($id) ? Factory::getUser()->id : (int)$id;
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
        $id        = empty($id) ? Factory::getUser()->id : (int)$id;
        $decimals  = (int)TimeclockHelper::getParam("decimalPlaces");
        $regular   = $this->_getPTO($start, $end, $id, "CARRYOVER", true);
        $timesheet = TimeclockHelper::getModel("Timesheet", false);
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
        $timesheet = TimeclockHelper::getModel("Timesheet", false);
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
        $decimals  = (int)TimeclockHelper::getParam("decimalPlaces");
        $db    = Factory::getDBO();
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
        $db = Factory::getDBO();
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
        $id     = empty($id) ? Factory::getUser()->id : (int)$id;
        $period = trim(TimeclockHelper::getParam("ptoAccrualPeriod"));
        $days   = DateHelper::days($start, $end);
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
                $len = TimeclockHelper::getParam("payPeriodLengthFixed");
                $ret = $ret && $this->_setAccrualPayperiod($start, $id);
            }
            // The 8th day is the first day of the next week.
            $start = DateHelper::end($start, $len + 1);
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
        $end = DateHelper::end($start, 7);
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
        $startTime = TimeclockHelper::getParam("firstPayPeriodStart");
        $len = TimeclockHelper::getParam("payPeriodLengthFixed");
        $period = DateHelper::fixedPayPeriod($startTime, $start, $len);
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
        
        $d = DateHelper::explodeDate($date);
        $now = Factory::getDate()->toSql();
        $row = array(
            "pto_id"     => $this->find($id, $date),
            "type"       => "ACCRUAL",
            "user_id"    => $id,
            "hours"      => $hours,
            "valid_from" => $date,
            "valid_to"   => $d["y"]."-12-31",
            "created_by" => Factory::getUser()->id,
            "created"    => $now,
            "notes"      => "Automatic Accrual",
        );

        return $this->save($row);
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
        $user   = Factory::getUser($id);
        $rate   = TimeclockHelper::getPtoAccrualRate($user->id, $end);
        $hours  = 0;
        if ($rate > 0) {
            $rate  *= (float)TimeclockHelper::getParam("ptoHoursPerDay");
            $d = DateHelper::explodeDate($end);
            $year   = (int)$d["y"];
            $ytd    = $this->getAccrual("$year-01-01", "$year-12-31", $id);
            $status = TimeclockHelper::getUserParam('status', $id);
            $hpy    = ($status == "FULLTIME") ? 2080 : 1040;
            $decimals  = (int)TimeclockHelper::getParam("decimalPlaces");
            $timesheet = TimeclockHelper::getModel("Timesheet", false);
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
