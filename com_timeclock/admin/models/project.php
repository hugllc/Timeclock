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
 * @version    GIT: $Id: aa5d9aba45ff334937cf8ca5439dbe0c0d50ba24 $
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
class TimeclockModelsProject extends TimeclockModelsDefault
{
    /** Where Fields */
    protected $_project_id = null;
    protected $_defaultSort   = "p.project_id";
    protected $table          = "TimeclockProjects";

    /**
    * This is the constructor
    */
    public function __construct()
    {
        parent::__construct(); 
        $this->_project_id = !empty($this->id) ? (int) reset($this->id) : null;
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
        $query->select('p.*');
        $query->from('#__timeclock_projects as p');
        $query->select('u.name as manager');
        $query->leftjoin('#__users as u on p.manager_id = u.id');
        $query->select('v.name as author');
        $query->leftjoin('#__users as v on p.created_by = v.id');
        $query->select('q.name as category');
        $query->leftjoin('#__timeclock_projects as q on q.project_id = p.parent_id');
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
        $query->select('COUNT(p.project_id) as count');
        $query->from('#__timeclock_projects as p');
        $query->leftjoin('#__users as u on p.created_by = u.id');
        $query->leftjoin('#__users as v on p.manager_id = v.id');
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
        $id = is_numeric($id) ? $id : $this->_project_id;
        
        if(is_numeric($id)) {
            $query->where('p.project_id = ' . (int) $id);
        }

        $filter = $this->getState("filter");

        if(!empty($filter->search) && is_string($filter->search)) {
            $query->where($db->quoteName("p.name")." LIKE ".$db->quote("%".$filter->search."%"));
        }
        
        if (is_numeric($filter->published)) {
            $query->where($db->quoteName('p.published').' = ' . $db->quote((int) $filter->published));
        }

        if (is_numeric($filter->category)) {
            $query->where($db->quoteName('p.parent_id').' = ' . $db->quote((int) $filter->category));
        }
        
        if (is_numeric($filter->department)) {
            $query->where($db->quoteName('p.department_id').' = ' . $db->quote((int) $filter->department));
        }
        
        if (is_numeric($filter->customer)) {
            $query->where($db->quoteName('p.customer_id').' = ' . $db->quote((int) $filter->customer));
        }
        if (is_numeric($filter->user_id)) {
            $query->where($db->quoteName("p.manager_id")." = " . $db->quote((int) $filter->user_id));
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
            'p.published',
            'p.name',
            'manager',
            'p.manager_id',
            'p.project_id',
            'p.description',
        );
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @param int $project_id The project to get the users for
    * 
    * @return array An array of results.
    */
    public function listProjectUsers($project_id = null)
    {
        $project_id = is_null($project_id) ? $this->_project_id : $project_id;
        if (empty($project_id)) {
            return array();
        }
        $db = Factory::getDBO();
        $query = $db->getQuery(TRUE);
        $query->select('p.project_id as project_id, p.user_id as user_id');
        $query->from('#__timeclock_users as p');
        $query->select('u.*');
        $query->leftjoin('#__users as u on p.user_id = u.id');

        $query->where('p.project_id = ' . $db->quote((int) $project_id));

        $list = $this->_getList($query, 0, 0);
        $ret  = array();
        foreach ($list as $entry) {
            $ret[$entry->id] = $entry;
        }
        return $ret;
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @param int $user_id The user to get the projects for
    * 
    * @return array An array of results.
    */
    public function listUserProjects($user_id = null)
    {
        $user_id = is_null($user_id) ? Factory::getUser()->id : $user_id;
        if (empty($user_id)) {
            return array();
        }

        $db = Factory::getDBO();
        $query = $db->getQuery(TRUE);
        $query->select('q.project_id as project_id, q.user_id as user_id');
        $query->from('#__timeclock_users as q');
        $query->select('p.*');
        $query->leftjoin('#__timeclock_projects as p on q.project_id = p.project_id');

        $query->where('q.user_id = '.$db->quote((int)$user_id));

        $list = $this->_getList($query, 0, 0);
        $ret  = array();
        foreach ($list as $entry) {
            $ret[$entry->project_id] = $entry;
        }
        return $ret;
    }
    /**
    * Adds users to this project
    *
    * @param mixed $users      The users to add
    * @param int   $project_id The project ID to use.
    * 
    * @return bool
    */
    public function addUsers($users, $project_id = null)
    {
        $db = Factory::getDBO();
        $project_id = is_null($project_id) ? $this->_project_id : $project_id;
        if(!is_numeric($project_id)) {
            return false;
        }
        if (!is_array($users)) {
            $users = array($users);
        }
        $all = $this->listProjectUsers($project_id);
        $values = array();
        foreach ($users as $user) {
            if (!isset($all[$user])) {
                $values[] = $db->quote((int)$project_id).", ".$db->quote((int)$user);
            }
        }
        return $this->_addUser($values);
    }
    /**
    * Adds users to this project
    *
    * @param array $values array of strings of data
    * 
    * @return bool
    */
    private function _addUser($values)
    {
        if (count($values) < 1) {
            return true;
        }
        $db = Factory::getDBO();
        $query = $db->getQuery(TRUE);
        $query->insert($db->quoteName('#__timeclock_users'));
        $query->columns($db->quoteName(array("project_id", "user_id")));
        foreach ($values as $val) {
            $query->values($val);
        }
        $db->setQuery($query);
        return $db->execute();
    }
    /**
    * Removes users from this project
    *
    * @param mixed $users      The users to remove
    * @param int   $project_id The project ID to use.
    * 
    * @return bool
    */
    public function removeUsers($users, $project_id = null)
    {
        $project_id = is_null($project_id) ? $this->_project_id : $project_id;
        if(!is_numeric($project_id)) {
            return false;
        }
        if (!is_array($users)) {
            $users = array($users);
        }
        $ret = false;
        foreach ($users as $user) {
            $ret = $ret || $this->_removeUser($user, $project_id);
        }
        return $ret;
    }
    /**
    * Removes users from this project
    *
    * @param int $user_id    The user ID to remove
    * @param int $project_id The project ID to use.
    * 
    * @return bool
    */
    private function _removeUser($user_id, $project_id)
    {
        $db = Factory::getDBO();
        $query = $db->getQuery(TRUE);
        $query->delete($db->quoteName('#__timeclock_users'));
        $query->where(
            array(
                $db->quoteName("project_id")." = ".$db->quote((int)$project_id), 
                $db->quoteName("user_id")." = ".$db->quote((int)$user_id)
            )
        );
        $db->setQuery($query);
        return $db->execute();
    }
}