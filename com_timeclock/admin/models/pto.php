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
    * @param int $id The id of the item to check in
    * 
    * @return  boolean
    */
    public function accrue($date, $id = null)
    {
        $timesheet = TimeclockHelpersTimeclock::getModel("Timesheet");
        
        return true;
    }

}