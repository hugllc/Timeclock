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
class TimeclockAdminModelUsers extends JModel
{
    /** The ID to load */
    private $_id = -1;
    /** Query to get all records */
    private $_allQuery = "SELECT u.*, p.prefs prefs
                      FROM #__users AS u 
                      LEFT JOIN #__timeclock_prefs as p ON u.id = p.id ";

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
        $row = $this->getTable("TimeclockPrefs");
        $id = is_int($this->_id) ? $this->_id : $this->_id[0];
        $row->load($id);
        return $row;
    }

    /**
     * Method to display the view
     *
     * @param int $limitstart The record to start on
     * @param int $limit      The max number of records to retrieve 
     *
     * @return string
     */
    function getUsers($where = "", $limitstart=null, $limit=null, $orderby = "")
    {
        $key = (string)$limitstart.$limit;
        $query = $this->_allQuery." "
                 .$where." "
                 .$orderby;
        $ret = $this->_getList($query, $limitstart, $limit);
        if (!is_array($ret)) return $ret;
        foreach ($ret as $key => $val) {
            $ret[$key]->prefs = TableTimeclockPrefs::decode($val->prefs);
            $ret[$key]->published = (bool) $val->prefs["admin_active"];
        }
        return $ret;
    }

    /**
     * Method to display the view
     *
     * @return string
     */
    function countUsers($where="")
    {
        $query = $this->_allQuery." ".$where;
        return $this->_getListCount($query);
    }

    /**
     * Publishes or unpublishes an item
     *
     * @param int $publish 1 to publish, 0 to unpublish
     *
     * @return bool
     */
    function publish($publish, $user_id)
    {
        TableTimeclockPrefs::setPref("admin_active", (bool)$publish, $user_id);
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
        $table = $this->getTable("TimeclockPrefs");
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
        $table = $this->getTable("TimeclockPrefs");
        return $table->checkout($who, $oid);
    }


    /**
     * Method to store a record
     *
     * @access    public
     * @return    boolean    True on success
     */
    function store()
    {
        $row =& $this->getTable("TimeclockPrefs"); 
        $data = JRequest::get('post');
        if (empty($data["id"])) return false;
        
        // Load the old data
        $row->load($data["id"]);
        $prefs = array(
            "prefs" => $row->prefs,
            "id" => $data["id"],
        );
        // Load the new data
        foreach ($data as $f => $v) {
            if (substr($f, 0, 6) == "admin_") $prefs["prefs"][$f] = $v;
        }

        // Bind the form fields to the hello table
        if (!$row->bind($prefs)) {
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
    
        return true;
    }



}

?>
