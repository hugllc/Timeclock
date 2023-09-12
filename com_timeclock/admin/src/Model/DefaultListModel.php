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
 * @version    GIT: $Id: 1496d899d0a055a9bf71907f1ce58d681ba0617f $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

namespace HUGLLC\Component\Timeclock\Administrator\Model;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;

\defined( '_JEXEC' ) or die();

 
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
class DefaultListModel extends ListModel
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

    public function getListQuery()
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
        return $query;
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
    protected function populateState($ordering = null, $direction = null)
    {
        $this->popstate = true;
        $context = is_null($this->context) ? $this->table : $this->context;

        $app = Factory::getApplication();
        
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
        // $this->setState("list.ordering", $ordering);

        $direction = $app->getUserStateFromRequest(
            $context.'_list.direction',
            "filter_order_Dir",
            $this->_defaultSortDir
        );
        /*
        $this->setState(
            "list.direction", 
            (trim(strtolower($direction)) == "desc") ? "DESC" : "ASC"
        );*/

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

        // List state information.
        parent::populateState($ordering, $direction);

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