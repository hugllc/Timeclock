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
class TimeclockAdminModelProjects extends JModel
{
    /** The ID to load */
    private $_id = -1;
    var $_allQuery = "SELECT t.*, p.name as parentname, u.name as created_by_name, p.checked_out as parent_checked_out
                      FROM #__timeclock_projects AS t 
                      LEFT JOIN #__timeclock_projects as p ON t.parent_id = p.id
                      LEFT JOIN #__users as u ON t.created_by = u.id ";

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
        $row = $this->getTable("TimeclockProjects");
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
    function getProjects($where = "", $limitstart=null, $limit=null, $orderby = "")
    {
        $key = (string)$limitstart.$limit;
        $query = $this->_allQuery." "
                 .$where." "
                 .$orderby;
        return $this->_getList($query, $limitstart, $limit);
    }

    /**
     * Method to display the view
     *
     * @return string
     */
    function countProjects($where="")
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
        $table = $this->getTable("TimeclockProjects");
        $id = is_array($this->_id) ? $this->_id : array($this->_id);
        return $table->publish($id, $publish, $user_id);
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
        $table = $this->getTable("TimeclockProjects");
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
        $table = $this->getTable("TimeclockProjects");
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
        $row =& $this->getTable("TimeclockProjects"); 
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
    
        return true;
    }


    /**
     * Gets select options for parent projects
     *
     * @param int $id       The Id of the item to get the parent for
     * @param int $selected The Id of the item to be selected
     *
     * @return array
     */
    function getParentOptions($id, $selected=0)
    {
        $parents = array(JHTML::_("select.option", 0, "None"));
        if ($this->countParents($id) > 0) return $parents;
        if (empty($this->_parents[$id])) {
            $query = "SELECT id, name FROM #__timeclock_projects WHERE parent_id=0 AND published=1 AND (type='PROJECT' OR type='UMBRELLA') ORDER BY id asc";
            $parentList = $this->_getList($query);
            if (!is_array($parentList)) return $parents;
            foreach ($parentList as $val) {
                $parents[] = JHTML::_("select.option", $val->id, sprintf("%04d", $val->id).": ".$val->name);
            }
            $this->_parents[$id] = $parents;
        }
        return $this->_parents[$id];
    }

    /**
     * Gets select options for parent projects
     *
     * @param int $id       The Id of the item to get the parent for
     * @param int $selected The Id of the item to be selected
     *
     * @return array
     */
    function getOptions($where, $selected=0, $selectedText = "None")
    {
        $ret = array(JHTML::_("select.option", 0, $selectedText));
        $query = "SELECT id, name FROM #__timeclock_projects ".$where." ORDER BY id asc";
        $list = self::_getList($query);
        if (!is_array($list)) return $ret;
        foreach ($list as $val) {
            $ret[] = JHTML::_("select.option", $val->id, sprintf("%04d", $val->id).": ".$val->name);
        }
        return $ret;
    }

    /**
     * Method to display the view
     *
     * @param int $id The Id of the item to get the parent for
     *
     * @return string
     */
    function countParents($id)
    {
        if (empty($this->_parentCount[$id])) {
            $query = "select * from #__timeclock_projects where parent_id=".(int)$id;
            $this->_projectCount[$id] = $this->_getListCount($query);
        }
        return $this->_projectCount[$id];
    }
    /**
     * Get projects for a user
     *
     * @param int $oid Project id
     * @param int $limitstart The record to start on
     * @param int $limit      The max number of records to retrieve 
     *
     * @return array
     */
    function getProjectUsers($oid, $limitstart = null, $limit = null)
    {
        $query = "select u.id as proj_id, p.*, u.user_id as id from #__timeclock_users as u
                  LEFT JOIN #__users as p on u.user_id = p.id 
                  WHERE u.id = ".(int)$oid."
                  ORDER BY p.id asc
                  ";
        $ret = $this->_getList($query, $limitstart, $limit);
        if (!is_array($ret)) return array();
        return $ret;
    }

}

?>
