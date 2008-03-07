<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.5 component
 * Copyright (C) 2008 Hunt Utilities Group, LLC
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
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/**
 * ComTimeclock model
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockAdminModelCustomers extends JModel
{
    /** The ID to load */
    private $_id = -1;
    var $_allQuery = "SELECT c.*
                      FROM #__timeclock_customers AS c ";
    /**
     * Constructor that retrieves the ID from the request
     *
     * @return    void
     */
    function __construct()
    {
        parent::__construct();
    
        $array = JRequest::getVar('cid', 0, '', 'array');
        $this->setId($array);
    }
    /**
     * Method to set the id
     *
     * @param int $id The ID of the Project to get
     *
     * @return    void
     */
    function setId($id)
    {
        $this->_id      = $id;
    }

    /**
     * Method to display the view
     *
     * @return string
     */
    function &getData()
    {
        $row = $this->getTable("TimeclockCustomers");
        $id = is_int($this->_id) ? $this->_id : $this->_id[0];
        $row->load($id);
        return $row;
    }

    /**
     * Method to display the view
     *
     * @param string $where      The where clause to use.  Must include 'WHERE'
     * @param int    $limitstart The record to start on
     * @param int    $limit      The max number of records to retrieve 
     * @param string $orderby    The order by clause.  Must include 'ORDER BY'
     *
     * @return string
     */
    function getCustomers($where = "", $limitstart=null, $limit=null, $orderby = "")
    {
        $query = $this->_allQuery." "
                .$where." "
                .$orderby;
        return $this->_getList($query, $limitstart, $limit);
    }

    /**
     * Method to display the view
     *
     * @param string $where The where clause to use.  Must include 'WHERE'
     *
     * @return string
     */
    function countCustomers($where="")
    {
        $query = $this->_allQuery." ".$where;
        return $this->_getListCount($query);
    }

    /**
     * Checks in an item
     *
     * @param int $oid The id of the item to save
     *
     * @return bool
     */
    function checkin($oid)
    {
        $table = $this->getTable("TimeclockCustomers");
        return $table->checkin($oid);
    }

    /**
     * Publishes or unpublishes an item
     *
     * @param int $who The uid of the person doing the checkout
     * @param int $oid The id of the item to save
     *
     * @return bool
     */
    function checkout($who, $oid)
    {
        $table = $this->getTable("TimeclockCustomers");
        return $table->checkout($who, $oid);
    }

    /**
     * Get the project options for a select list
     *
     * @return array
     */
    function getOptions($where = "", $name = "None")
    {
        $ret = array(JHTML::_("select.option", 0, $name));
        $query = "SELECT id, name FROM #__timeclock_customers ".$where;
        $proj = $this->_getList($query, $limitstart, $limit);
        if (!is_array($proj)) return $ret;
        foreach ($proj as $p) {
            $ret[] = JHTML::_("select.option", $p->id, $p->name);
        }
        return $ret;
    }

    /**
     * Method to store a record
     *
     * @access    public
     * @return    boolean    True on success
     */
    function store()
    {
        $row =& $this->getTable("TimeclockCustomers"); 
        $data = JRequest::get('post');
    
        if (empty($data['id'])) {
            $data["created"] = date("Y-m-d H:i:s");
            $user =& JFactory::getUser();
            $data["created_by"] = $user->get("id");
        }
        
        // Bind the form fields to the hello table
        if (!$row->bind($data)) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        // Make sure the record is valid
        if (!$row->check()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
    
        // Store the web link table to the database
        if (!$row->store()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
    
        return $row->id;
    }
    /**
     * Publishes or unpublishes an item
     *
     * @param int $publish 1 to publish, 0 to unpublish
     * @param int $user_id The user ID to change
     *
     * @return bool
     */
    function publish($publish, $user_id)
    {
        $table = $this->getTable("TimeclockCustomers");
        $id = is_array($this->_id) ? $this->_id : array($this->_id);
        return $table->publish($id, $publish, $user_id);
    }


}

?>
