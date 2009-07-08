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
    /** The fixed categories and their IDs */
    static $cat = array(
        "general" => -1,
        "special" => -2,
        "unpaid" =>  -3,
    );
    /** The ID to load */
    private $_id = -1;
    var $_allQuery = "SELECT t.*, p.name as parentname, u.name as created_by_name,
                      m.name as manager_name,
                      p.checked_out as parent_checked_out,
                      c.company as customer_name, c.name as customer_contact,
                      c.checked_out as customer_checked_out
                      FROM #__timeclock_projects AS t
                      LEFT JOIN #__timeclock_projects as p ON t.parent_id = p.id
                      LEFT JOIN #__users as u ON t.created_by = u.id
                      LEFT JOIN #__users as m ON t.manager = m.id
                      LEFT JOIN #__timeclock_customers as c ON c.id = t.customer
                      ";
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
        $this->_id = $id;
    }

    /**
     * Method to display the view
     *
     * @param int $id The id of the project to retrieve.
     *
     * @return string
     */
    function &getData($id = null)
    {
        $row = $this->getTable("TimeclockProjects");
        if (is_null($id)) {
            $id = is_int($this->_id) ? $this->_id : $this->_id[0];
        }
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
     * @param string $where The where clause to use.  Must include 'WHERE'
     *
     * @return string
     */
    function countProjects($where="")
    {
        $query = $this->_allQuery." ".$where;
        return $this->_getListCount($query);
    }
    /**
     * Checks in an item
     *
     * @return bool
     */
    function adduser()
    {
        $row = $this->getTable("TimeclockUsers");

        $this->store();

        $id = JRequest::getVar('id', 0, '', 'int');
        $user_id = JRequest::getVar('user_id', array(0), '', 'array');
        if (!is_array($user_id)) {
            $user_id = array($user_id);
        }
        $ret = true;
        foreach ($user_id as $u) {
            $data = array(
                "id" => $id,
                "user_id" => $u,
            );

            if (!$row->bind($data)) {
                $this->setError($this->_db->getErrorMsg());
                $ret = false;
                continue;
            }
            // Make sure the record is valid
            if (!$row->check()) {
                $this->setError($this->_db->getErrorMsg());
                $ret = false;
                continue;
            }

            // Store the web link table to the database
            if (!$row->store()) {
                $this->setError($this->_db->getErrorMsg());
                $ret = false;
                continue;
            }
        }
        return $ret;
    }

    /**
     * Checks in an item
     *
     * @return bool
     */
    function removeuser()
    {
        $this->store();

        $row = $this->getTable("TimeclockUsers");

        $id = JRequest::getVar('id', 0, '', 'int');
        $user_id = JRequest::getVar('remove_user_id', array(0), '', 'array');
        if (!is_array($user_id)) {
            $user_id = array($user_id);
        }
        $ret = true;
        foreach ($user_id as $u) {
            $data = array(
                "id" => $id,
                "user_id" => $u,
            );
            // Bind the form fields to the hello table
            if (!$row->bind($data)) {
                $this->setError($this->_db->getErrorMsg());
                $ret = false;
            }
            // Store the web link table to the database
            if (!$row->delete()) {
                $this->setError($this->_db->getErrorMsg());
                $ret = false;
            }
        }
        return $ret;
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

        return $row->id;
    }

    /**
     * Gets select options for parent projects
     *
     * @param int    $id       The Id of the item to get the parent for
     * @param int    $selected The Id of the item to be selected
     * @param string $text     The text to put in the default entry
     *
     * @return array
     */
    function getParentOptions($id=0, $selected=0, $text = "None")
    {
        $parents = array(JHTML::_("select.option", 0, $text));
        $query = "SELECT id, name FROM #__timeclock_projects WHERE parent_id=0
                  AND id > 0 AND published=1 AND type='CATEGORY' ORDER BY id asc";
        $parentList = $this->_getList($query);
        if (!is_array($parentList)) {
            return $parents;
        }
        foreach ($parentList as $val) {
            $parents[] = JHTML::_(
                "select.option",
                $val->id,
                sprintf("%04d", $val->id).": ".$val->name
            );
        }
        return $parents;
    }

    /**
     * Gets select options for parent projects
     *
     * @param string $where     The where clause to use.  Must include 'WHERE'
     * @param string $text      The text of the first entry
     * @param array  $exclude   Projects to exclude
     * @param int    $textValue The value to go with the text
     *
     * @return array
     */
    function getOptions($where, $text = "None", $exclude=array(), $textValue = -1)
    {
        $ret = array(JHTML::_("select.option", $textValue, $text));
        $query = "SELECT id, name FROM #__timeclock_projects "
                 .$where." ORDER BY id asc";
        $list = self::_getList($query);
        if (!is_array($list)) {
            return $ret;
        }
        foreach ($list as $val) {
            if (array_search($val->id, $exclude) !== false) {
                continue;
            }
            $ret[] = JHTML::_(
                "select.option",
                $val->id,
                sprintf("%04d", $val->id).": ".$val->name
            );
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
     * @param int $oid        Project id
     * @param int $limitstart The record to start on
     * @param int $limit      The max number of records to retrieve
     *
     * @return array
     */
    function getProjectUsers($oid, $limitstart = null, $limit = null)
    {
        $query = "select u.id as proj_id, p.*,
                  u.user_id as id from #__timeclock_users as u
                  LEFT JOIN #__users as p on u.user_id = p.id
                  WHERE u.id = ".(int)$oid."
                  ORDER BY p.id asc
                  ";
        $ret = $this->_getList($query, $limitstart, $limit);
        if (!is_array($ret)) {
            return array();
        }
        return $ret;
    }

    /**
     * Get projects for a user
     *
     * @param int $oid        User id
     * @param int $limitstart The record to start on
     * @param int $limit      The max number of records to retrieve
     *
     * @return array
     */
    function getUserProjects($oid, $limitstart = null, $limit = null)
    {
        $query = "select * from #__timeclock_users as u
                  WHERE u.user_id = ".(int)$oid."";
        $ret = $this->_getList($query, $limitstart, $limit);
        if (!is_array($ret)) {
            return array();
        }
        $uProj = array();
        foreach ($ret as $p) {
            $uProj[$p->id] = $p->user_id;
        }
        $proj = $this->getProjects("", null, null, "ORDER BY id asc");
        if (!is_array($proj)) {
            return array();
        }

        foreach ($proj as $p) {
            if ($p->type == "CATEGORY") {
                $p->subprojects = array();
                $projects[$p->id] = $p;
            }
        }

        foreach ($proj as $p) {
            if ($p->type != "CATEGORY") {
                $p->mine = array_key_exists($p->id, $uProj);
                $cat = $this->_getProjectCategory($p, $projects);
                if ($p->mine) {
                    $projects[$cat]->mine = true;
                }
                if ($p->type == 'HOLIDAY') {
                    $p->noHours = true;
                }
                $projects[$cat]->subprojects[$p->id] = $p;
            }
        }
        return $projects;
    }

    /**
     * Get projects for a user
     *
     * @param object &$proj The project to get the category for
     * @param array  &$cats The categories to choose from
     *
     * @return array
     */
    private function _getProjectCategory(&$proj, &$cats)
    {
        if ($proj->type == "UNPAID") {
            return self::$cat["unpaid"];
        }
        if ($proj->type == "HOLIDAY") {
            return self::$cat["special"];
        }
        if ($proj->type == "PTO") {
            return self::$cat["special"];
        }
        if (is_object($cats[$proj->parent_id])) {
            return $proj->parent_id;
        }
        return self::$cat["general"];
    }
    /**
     * Get projects for a user
     *
     * @param int $oid User id
     *
     * @return array
     */
    function getUserProjectsCount($oid)
    {
        $query = "select * from #__timeclock_users as u
                  WHERE u.user_id = ".(int)$oid."";
        return $this->_getListCount($query);
    }

    /**
     * Get projects for a user
     *
     * @param int $oid    User id
     * @param int $projid Project id
     *
     * @return array
     */
    function userInProject($oid, $projid)
    {
        $query = "select * from #__timeclock_users as u
                  WHERE u.user_id = ".(int)$oid."
                      AND u.id = ".(int)$projid."";
        return $this->_getListCount($query);
    }


}

?>
