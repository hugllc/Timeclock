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

use Joomla\CMS\Factory;
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
class TimeclockModelsTimesheets extends TimeclockModelsDefault
{
    /** Where Fields */
    protected $_timesheet_id   = null;
    protected $_published      = 1;
    protected $_total          = null;
    protected $_pagination     = null;
    protected $_defaultSort    = "t.worked";
    protected $_defaultSortDir = "desc";
    protected $table           = "TimeclockTimesheet";

    /**
    * This is the constructor
    */
    public function __construct()
    {
        parent::__construct(); 
        $this->_timesheet_id = !empty($this->id) ? (int) reset($this->id) : null;
    }
    /**
    * Builds the query to be used by the model
    *
    * @return object Query object
    */
    protected function _buildQuery()
    {
        $db = Factory::getDBO();
        $query = $db->getQuery(TRUE);
        $query->select('DISTINCT t.timesheet_id,
            (t.hours1 + t.hours2 + t.hours3 + t.hours4 + t.hours5 + t.hours6)
            as hours,
            t.worked, t.project_id, t.notes,
            t.hours1 as hours1, t.hours2 as hours2, t.hours3 as hours3,
            t.hours4 as hours4, t.hours5 as hours5, t.hours6 as hours6,
            t.user_id as user_id, t.created_by as created_by');
        $query->from('#__timeclock_timesheet as t');
        $query->select('p.name as project, p.type as project_type, 
            p.wcCode1, p.wcCode2, p.wcCode3, p.wcCode4, p.wcCode5, p.wcCode6');
        $query->leftjoin('#__timeclock_projects as p on t.project_id = p.project_id');
        $query->select('u.name as user');
        $query->leftjoin('#__users as u on t.user_id = u.id');
        $query->select('v.name as author');
        $query->leftjoin('#__users as v on t.created_by = v.id');
        // We only want non-holiday timesheets
        $query->where("p.type <> 'HOLIDAY'");
        return $query;
    }
    /*
            p.wcCode1 as wcCode1, p.wcCode2 as wcCode2, p.wcCode3 as wcCode3,
            p.wcCode4 as wcCode4, p.wcCode5 as wcCode5, p.wcCode6 as wcCode6,
            t.user_id as user_id, p.name as project_name, p.type as type,
            u.name as author, pc.name as category_name, c.company as company_name,
            c.name as contact_name, t.project_id as project_id, 
            u.id as user_id, p.parent_id as category_id');
    */
    /**
    * Builds the query to be used to count the number of rows
    *
    * @return object Query object
    */
    protected function _buildCountQuery()
    {
        $db = Factory::getDBO();
        $query = $db->getQuery(TRUE);
        $query->select('COUNT(t.timesheet_id) as count');
        $query->from('#__timeclock_timesheet as t');
        $query->select('p.name as project, p.type as project_type');
        $query->leftjoin('#__timeclock_projects as p on t.project_id = p.project_id');
        $query->where("p.type <> 'HOLIDAY'");
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
        $db = Factory::getDBO();
        $id = is_numeric($id) ? $id : $this->_timesheet_id;
        
        if(is_numeric($id)) {
            $query->where($db->quoteName('t.timesheet_id').' = ' . $db->quote((int) $id));
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
            'project',
            't.worked',
            't.created',
            't.modified',
            't.timesheet_id'
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

}