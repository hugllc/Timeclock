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
 * @version    GIT: $Id: 1ad196872e606d31e7932a3f659df5e825b74f82 $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;

 
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
class TimeclockModelsSiteDefault extends BaseDatabaseModel
{
    protected $__state_set     = null;
    protected $_total          = null;
    protected $_pagination     = null;
    protected $_db             = null;
    protected $_view           = "report";
    protected $_layout         = "report";
    protected $table           = "";
    protected $id              = array();
    protected $context         = null;
    protected $_defaultSortDir = "asc";
    private   $_user           = null;
    private   $_users          = array();
    /**
    * The constructor
    */
    public function __construct()
    {
        parent::__construct(); 
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
    * Method to get the filter object
    *
    * @return object The property where specified, the state object where omitted
    */
    public function getFilter()
    {
        $filter = new stdClass();
        foreach ($this->getState() as $key => $value) {
            if (str_contains($key, "filter.")) {
                $k = explode(".", $key)[1];
                $filter->$k = $value;
            }
        }

        return $filter;
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
        
        // Load state from the request.
        $pk = $app->input->get('id', array(), "array");
        $this->setState('id', $pk);

        // Load the parameters.
        $params = ComponentHelper::getParams('com_timeclock');
        $this->setState('params', $params);

    }
    /**
    * This returns the table for this model
    * 
    * @return  Table object
    */
    public function getTable($name = '', $prefix = 'Table', $options = [])
    {
        return Table::getInstance($this->table, $prefix);
    }
    /**
    * This sets a parameter of com_timeclock.
    *
    * @param string $name  The name of the param to set
    * @param mixed  $value The value to set it to
    * 
    * @return The value set if successful, false otherwise
    */
    public function setParam($name, $value)
    {
        // Get the params and set the new values
        $params = ComponentHelper::getParams('com_timeclock');
        $params->set($name, $value);
    die("HERE");
        // Get a new database query instance
        $db = Factory::getDBO();
        $query = $db->getQuery(true);

        // Build the query
        $query->update('#__extensions AS a');
        $query->set('a.params = ' . $db->quote((string)$params));
        $query->where('a.element = "com_timeclock"');

        // Execute the query
        $db->setQuery($query);
        $db->query();
        
        $set = TimeclockHelpersTimeclock::getParam($name);
        return $set == $value ? $set : false;
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
            $this->_user[$id]->me = is_null($id) || ($id == Factory::getUser()->id);
        }
        return $this->_user[$id];
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @param int $blocked null gets all users, 1 gets blocked users, 0 gets unblocked
    *                     users
    * 
    * @return array An array of results.
    */
    public function listUsers($blocked = 0)
    {
        if (empty($this->_users)) {
            $this->_users = TimeclockHelpersTimeclock::getUsers($blocked);
            $me = Factory::getUser()->id;
            foreach ($this->_users as &$user) {
                $user->timeclock = TimeclockHelpersTimeclock::getUserParams($user->id);
                $user->error     = "";
                $user->me = ($user->id == $me);
            }
        }
        return $this->_users;
    }

}