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
 * @version    GIT: $Id: 1496d899d0a055a9bf71907f1ce58d681ba0617f $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Factory;

 
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
class TimeclockModelsDefault extends AdminModel
{
    protected $__state_set     = null;
    protected $_total          = null;
    protected $_pagination     = null;
    protected $_db             = null;
    protected $_view           = "list";
    protected $_layout         = "list";
    protected $table           = "";
    protected $id              = array();
    protected $context         = null;
    protected $_defaultSortDir = "asc";
    public $insert_id          = null;
    protected $namefield       = "name";
    private   $_users          = array();

    /**
    * The constructor
    */
    public function __construct()
    {
        $app = Factory::getApplication();
        $this->id = $app->input->get('id', array(), "array");
        parent::__construct(); 
    }

    /**
    * Stores the data given, or request data.
    *
    * @param array $data The data to store.  If not given, get the post data
    *
    * @return Table instance with data in it.
    */
    public function store($data=null)
    {
        $data = $data ? $data : JRequest::get('post');
        $row = $this->getTable();

        if (!is_object($row)) {
            return false;
        }
        $date = date("Y-m-d H:i:s");

        // Bind the form fields to the table
        if (!$row->bind($data)) {
            return false;
        }

        $row->modified = $date;
        if (!$row->created) {
            $row->created_by = Factory::getUser()->id;
            $row->created    = $date;
        }

        // Make sure the record is valid
        if (!$row->check()) {
            return false;
        }
    
        if (!$row->store()) {
            return false;
        }
        // Set our id for things after this.
        if (empty($this->id) || (isset($this->id[0]) && empty($this->id[0]))) {
            $key = $row->getKeyName();
            $this->id = array($row->$key);
        }
        $this->insert_id = $this->id[0];
        
        return $row;

    }
    
    /**
    * Modifies a property of the object, creating it if it does not already exist.
    *
    * @param   string  $property  The name of the property.
    * @param   mixed   $value     The value of the property to set.
    *
    * @return  mixed  Previous value of the property.
    */
    public function set($property, $value = null)
    {
        $previous = isset($this->$property) ? $this->$property : null;
        $this->$property = $value;
    
        return $previous;
    }

    /**
    * Retrieves a property of the object, returning $default if it doesnt' exist.
    *
    * @param string $property The name of the property.
    * @param mixed  $default  The value of the property to set.
    *
    * @return mixed Previous value of the property.
    */
    public function get($property, $default = null) 
    {
        return isset($this->$property) ? $this->$property : $default;
    }

    /**
    * Build a query, where clause and return an object
    *
    * @param int $id The id of the record to get.
    */
    public function getItem($id = null)
    {
        $db = Factory::getDBO();

        $query = $this->_buildQuery();
        $this->_buildWhere($query, $id);
        $db->setQuery($query);

        $item = $db->loadObject();

        return $item;
    }
    /**
    * Build a query, where clause and return an object
    *
    */
    public function getNew()
    {
        $row = $this->getTable();

        if (is_object($row)) {
            return $row;
        }
        return new stdClass();
    }
    /**
    * Builds the filter for the query
    * @param object Query object
    * @return object Query object
    *
    */
    protected function _setSort(&$query)
    {
        $order = $this->checkSortFields($this->getState("list.ordering"));
        $query->order(
            $order.' '.$this->getState("list.direction")
        );
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @param array  $where The where clauses to use (defaults to stuff from input)
    * @param string $sort  The sort clause to uses (defaults to stuff from input)
    * @param bool   $limit Limit the list for paging
    * 
    * @return array An array of results.
    */
    public function listItems($where = array(), $sort = null, $limit = true)
    {
        $query = $this->_buildQuery();
        if (empty($where) || !is_array($where)) {
            $query = $this->_buildWhere($query);
        } else {
            foreach ($where as $clause) {
                $query->where($clause);
            }
        }
        if (is_null($sort) || empty($sort)) {
            $this->_setSort($query);
        } else {
            $query->order($sort);
        }
        if ($limit) {
            $list = $this->_getList(
                $query, 
                $this->getState("limitstart"), 
                $this->getState("limit")
            );
        } else {
            $list = $this->_getList($query);
        }
        return $list;
    }
    
    /**
    * Gets an array of objects from the results of database query.
    *
    * @param string  $query      The query.
    * @param integer $limitstart Offset.
    * @param integer $limit      The number of records.
    *
    * @return array An array of results.
    */
    protected function _getList($query, $limitstart = 0, $limit = 0)
    {
        $db = Factory::getDBO();
        $db->setQuery($query, $limitstart, $limit);
        $result = $db->loadObjectList();
    
        return $result;
    }
    
    /**
    * Returns a record count for the query
    *
    * @param string $query The query.
    *
    * @return integer Number of rows for query
    */
    protected function _getListCount($query)
    {
        $db = Factory::getDBO();
        $db->setQuery($query);
        // Take the first one and go
        return (int)$db->loadResult();
    }
    
    /** 
    * Method to get model state variables
    *
    * @param string $property Optional parameter name
    * @param mixed  $default  Optional default value
    *
    * @return object The property where specified, the state object where omitted
    */
    public function getState($property = null, $default = null)
    {
        if (!$this->__state_set) {
            // Protected method to auto-populate the model state.
            $this->populateState();
            // Set the model state set flag to true.
            $this->__state_set = true;
        }

        return $property === null ? $this->state : $this->state->get($property, $default);
    }
    /**
    * Get total number of rows for pagination
    * 
    * @return int The total number of rows
    */
    public function getTotal() 
    {
        if (empty($this->_total)) {
            $query = $this->_buildCountQuery();
            $query = $this->_buildWhere($query);
            $this->_total = $this->_getListCount($query);
        }
        return $this->_total;
    }
    
    /**
    * Generate pagination
    *
    * @return object Pagination object
    */
    public function getPagination() 
    {
        $app = Factory::getApplication();
        // Lets load the content if it doesn't already exist
        if (empty($this->_pagination)) {
            $this->_pagination = new Pagination(
                $this->getTotal(), 
                $this->getState("limitstart"),
                $this->getState("limit"),
                null,
                $app
            );
        }
        return $this->_pagination;
    }
    /**
    * Method to auto-populate the model state.
    *
    * This method should only be called once per instantiation and is designed
    * to be called on the first call to the getState() method unless the model
    * configuration flag to ignore the request is set.
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
        // $registry = $this->loadState();
        
        // Load state from the request.
        $pk = $app->input->get('id', array(), "array");
        $this->setState('id', $pk);

        // Load the parameters.
        $params = ComponentHelper::getParams('com_timeclock');
        $this->setState('params', $params);

        $user = Factory::getUser();

        if ((!$user->authorise('core.edit.state', 'com_timeclock')) 
            &&  (!$user->authorise('core.edit', 'com_timeclock'))
        ) {
                $this->setState('filter.published', 1);
                $this->setState('filter.archived', 2);
        }

        $limitstart = $app->getUserStateFromRequest(
            $context.'_limitstart',
            "limitstart",
            0,
            "int"
        );
        $this->setState("limitstart", $limitstart);
        $limit = $app->getUserStateFromRequest(
            $context.'_limit',
            "limit",
            10,
            "int"
        );
        $this->setState("limit", $limit);
        $ordering = $app->getUserStateFromRequest(
            $context.'_list.ordering',
            "filter_order",
            $this->_defaultSort
        );
        $ordering = $this->checkSortFields($ordering);
        $this->setState("list.ordering", $ordering);
        $direction = $app->getUserStateFromRequest(
            $context.'_list.direction',
            "filter_order_Dir",
            $this->_defaultSortDir
        );
        $this->setState(
            "list.direction", 
            (trim(strtolower($direction)) == "desc") ? "DESC" : "ASC"
        );
        $search = $app->getUserStateFromRequest($context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        
        $published = $app->getUserStateFromRequest($context.'.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        $category = $app->getUserStateFromRequest($context.'.filter.category', 'filter_category', '');
        $this->setState('filter.category', $category);

        $department = $app->getUserStateFromRequest($context.'.filter.department', 'filter_department', '');
        $this->setState('filter.department', $department);

        $customer = $app->getUserStateFromRequest($context.'.filter.customer', 'filter_customer', '');
        $this->setState('filter.customer', $customer);

        $year = $app->getUserStateFromRequest($context.'.filter.year', 'filter_year', '');
        $this->setState('filter.year', $year);

        $user_id = $app->getUserStateFromRequest($context.'.filter.user_id', 'filter_user_id', '');
        $this->setState('filter.user_id', $user_id);

        $type = $app->getUserStateFromRequest($context.'.filter.type', 'filter_type', '');
        $this->setState('filter.type', $type);

        $this->setState($registry);
    }
    /**
    * This returns the values given for the sort fields are viable
    * 
    * @param mixed $values Either an array with keys = sort field, or a string
    *                      that is the sort field.
    * 
    * @return  void
    */
    public function checkSortFields($values)
    {
        $fields = $this->getSortFields();
        if (is_string($values)) {
            if (in_array($values, $fields)) {
                // This value is in the array, so it is good
                return $values;
            }
        } else if (is_array($values)) {
            // This returns the key/value pairs in the array that are in $fields
            $ret = array_intersect_key($values, array_flip($fields));
            if (!empty($ret)) {
                return $ret;
            } else {
                return array($this->_defaultSort => Text::_('JDEFAULT'));
            }
        }
        return $this->_defaultSort;
    }
    /**
    * This returns the table for this model
    * 
    * @return  Table object
    */
    public function getForm($name = '', $prefix = '', $options = [])
    {
        // return Form::getInstance($this->table, 'Form');
    }

    /**
    * This returns the table for this model
    * 
    * @return  Table object
    */
    public function getTable($name = '', $prefix = '', $options = [])
    {
        return Table::getInstance($this->table, 'Table');
    }
    /**
    * Checks out this record
    * 
    * @param int $id      The id of the item to check in
    * @param int $user_id The user id to check out as
    * 
    * @return  boolean
    */
    public function checkout($user_id = null, $id = null)
    {
        $table = $this->getTable();
        $id    = is_null($id) ? (int) reset($this->id) : (int)$id;
        return $table->checkout($user_id, $id);
    }
    /**
    * Checks out this record
    * 
    * @param int $id The id of the item to check in
    * 
    * @return  boolean
    */
    public function checkin($id = null)
    {
        $table = $this->getTable();
        $id    = is_null($id) ? (int) reset($this->id) : (int)$id;
        return $table->checkin($id);
    }
    /**
    * Checks out this record
    * 
    * @param int $id The id of the item to check in
    * 
    * @return  boolean
    */
    public function publish(&$pks, $value = 1)
    {
        $table = $this->getTable();
        return $table->publish($pks, 1);
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
        $table = $this->getTable();
        $id = is_null($id) ? $this->id : $id;
        return $table->publish($id, 0);
    }
    /**
    * Gets an array of options
    * 
    * @param array  $where The where clauses to use (defaults to stuff from input)
    * @param string $sort  The sort clause to uses (defaults to stuff from input)
    * 
    * @return  array
    */
    public function getOptions($where = null, $sort = null)
    {
        $table = $this->getTable();
        $id    = $table->getKeyName();
        $list  = $this->listItems($where, $sort, false);
        $options = array();
        foreach ($list as $value) {
            $options[] = JHTML::_(
                'select.option', 
                $value->$id, 
                $this->name($value)
            );
        }
        return $options;
    }
    /**
    * Returns the name associated with this record
    * 
    * @param object $row The row to get the name of
    * 
    * @return string
    */
    public function name($row)
    {
        $name  = $this->namefield;
        return Text::_($row->$name);
    }
    /**
    * Gets the user and returns the timeclock params
    *
    * @param int $id The id of the user to get
    * 
    * @return array An array of results.
    */
    public function &getUser($id = null)
    {
        $id = empty($id) ? null : (int)$id;
        if (!isset($this->_user[$id])) {
            $this->_user[$id] = Factory::getUser($id);
            $this->_user[$id]->timeclock = TimeclockHelpersTimeclock::getUserParams($this->_user[$id]->id);
        }
        return $this->_user[$id];
    }
}