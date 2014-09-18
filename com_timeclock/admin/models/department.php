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
 * @version    GIT: $Id: c750c693ed2edc131222e22bb45920555ca1a94f $
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
class TimeclockModelsDepartment extends TimeclockModelsDefault
{
    /** Where Fields */
    protected $_department_id = null;
    protected $_defaultSort   = "d.department_id";
    protected $table          = "TimeclockDepartments";

    /**
    * This is the constructor
    */
    public function __construct()
    {
        parent::__construct(); 
        $this->_department_id = !empty($this->id) ? (int) reset($this->id) : null;
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
        $query->select('d.department_id, d.name, d.checked_out, d.manager_id,
                        d.description, d.checked_out_time, d.published, 
                        d.created_by, d.created, d.modified');
        $query->from('#__timeclock_departments as d');
        $query->select('u.name as manager');
        $query->leftjoin('#__users as u on d.manager_id = u.id');
        $query->select('v.name as author');
        $query->leftjoin('#__users as v on d.created_by = v.id');
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
        $query->select('COUNT(d.department_id) as count');
        $query->from('#__timeclock_departments as d');
        $query->leftjoin('#__users as u on d.created_by = u.id');
        $query->leftjoin('#__users as v on d.manager_id = v.id');
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
        $id = is_numeric($id) ? $id : $this->_department_id;
        
        if(is_numeric($id)) {
            $query->where('d.department_id = ' . (int) $id);
        }
        $search = $this->getState("filter.search");
        if(!empty($search) && is_string($search)) {
            $query->where("d.name LIKE ".$db->quote("%".$search."%"));
        }
        
        $published = $this->getState("filter.published");
        if (is_numeric($published)) {
            $query->where('d.published = ' . (int) $published);
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
            'd.published',
            'd.name',
            'manager',
            'd.manager_id',
            'd.department_id',
            'd.description',
        );
    }

}