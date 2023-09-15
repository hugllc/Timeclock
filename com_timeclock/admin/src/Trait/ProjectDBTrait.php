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
trait ProjectDBTrait
{
    /** Fields filter/sort works on */
    protected $filterFields = array(
        'p.published',
        'p.name',
        'manager',
        'p.manager_id',
        'p.project_id',
        'p.description',
        'p.type'
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
    protected function _buildWhere(&$query)
    { 
        $db = Factory::getDBO();

        $search = $this->getState("filter.search");
        if(!empty($search) && is_string($search)) {
            $query->where($db->quoteName("p.name")." LIKE ".$db->quote("%".$search."%"));
        }

        $published = $this->getState("filter.published");
        if (is_numeric($published)) {
            $query->where($db->quoteName('p.published').' = ' . $db->quote((int) $published));
        }

        $category = $this->getState("filter.category");
        if (is_numeric($category)) {
            $query->where($db->quoteName('p.parent_id').' = ' . $db->quote((int) $category));
        }
        
        $department = $this->getState("filter.department");
        if (is_numeric($department)) {
            $query->where($db->quoteName('p.department_id').' = ' . $db->quote((int) $department));
        }
        
        $customer = $this->getState("filter.customer");
        if (is_numeric($customer)) {
            $query->where($db->quoteName('p.customer_id').' = ' . $db->quote((int) $customer));
        }
        $user_id = $this->getState("filter.user_id");
        if (is_numeric($user_id)) {
            $query->where($db->quoteName("p.manager_id")." = " . $db->quote((int) $user_id));
        }
        $type = $this->getState("filter.type");
        if (!empty($type)) {
            $query->where($db->quoteName("p.type")." = " . $db->quote($type));
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
        $order = $this->getState('list.ordering', 'd.department_id');
        $dir = $this->getState('list.direction', 'ASC');
        $query->order($order.' '.$dir);
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
        $project_id = $project_id ? $project_id : Factory::getApplication()->getInput()->get("project_id");
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
        $user_id = $user_id or Factory::getUser()->id;
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
}