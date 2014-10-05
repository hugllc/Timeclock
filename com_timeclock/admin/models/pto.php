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
        $query->select('u.name as user');
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
            $query->where($db->quoteName("t.notes")." LIKE ".$db->quote("%".$filter->search."%"));
        }
        
        if (is_numeric($filter->year)) {
            $query->where($db->quoteName("t.worked")." >= " . $db->quote((int) $filter->year."-01-01"));
            $query->where($db->quoteName("t.worked")." <= " . $db->quote((int) $filter->year."-12-31"));
        }
        
        if (is_numeric($filter->user_id)) {
            $query->where($db->quoteName("t.user_id")." = " . $db->quote((int) $filter->user_id));
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
        $start = TimeclockHelpersDate::fixDate($start);
        $end   = TimeclockHelpersDate::fixDate($end);
        $s     = TimeclockHelpersDate::explodeDate($start);
        $e     = TimeclockHelpersDate::explodeDate($end);
        if ($s["y"] == $e["y"]) {
            $hours = $this->calcAccrual($start, $end, $id);
            $return = $this->_setAccrual($end, $hours, $id);
        } else {
            $end1   = $s["y"]."-12-31";
            $hours  = $this->calcAccrual($start, $end1, $id);
            $return = $this->_setAccrual($end1, $hours, $id);
            if ($return) {
                $hours  = $this->calcAccrual($e["y"]."-01-01", $end, $id);
                $return = $this->_setAccrual($end, $hours, $id);
            }
        }
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
    public function setAccrualWeek($start, $id = null)
    {
        $end = TimeclockHelpersDate::end($start, 7);
        return $this->setAccrual($start, $end, $id);
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
    private function _setAccrual($date, $hours, $id = null)
    {
        $id  = empty($id) ? $this->getUser()->id : (int)$id;
        
        $row = new stdClass();
        $d = TimeclockHelpersDate::explodeDate($date);
        if (!is_object($row)) {
            return false;
        }
        $row->type       = "ACCRUAL";
        $row->user_id    = $id;
        $row->hours      = $hours;
        $row->valid_from = $date;
        $row->valid_to   = $d["y"]."-12-31";
        $row->created_by = $this->getUser()->id;
        $row->created    = date("Y-m-d H:i:s");
        $row->modified   = $row->created;
        $row->notes      = "Automatic Accrual";
        
        $this->store($row);
        
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
    public function calcAccrual($start, $end, $id = null)
    {
        $user  = $this->getUser($id);
        $rate  = TimeclockHelpersTimeclock::getPtoAccrualRate($user->id, $end);
        $hours = 0;
        if ($rate > 0) {
            $decimals  = (int)TimeclockHelpersTimeclock::getParam("decimalPlaces");
            $timesheet = TimeclockHelpersTimeclock::getModel("Timesheet");
            $worked    = (float)$timesheet->periodTotal($user->id, $start, $end, true);
            $hpd       = (float)TimeclockHelpersTimeclock::getParam("ptoHoursPerDay");
            $hours     = sprintf("%4.".$decimals."f", ($rate / 2080) * $worked * $hpd);
        }
        return (float)$hours;
    }

}