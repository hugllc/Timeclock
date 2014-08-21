<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.6 component
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
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/** Get the timesheet table */
require_once JPATH_COMPONENT_SITE.'/tables/timeclocktimesheet.php';
/** Get the projects model */
require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/projects.php';

/**
 * ComTimeclock model
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockAdminModelTimesheets extends JModelLegacy
{
    /** The ID to load */
    private $_id = -1;
    private $_allQuery = "SELECT t.*, u.name as created_by_name,
                      (t.hours1 + t.hours2 + t.hours3 + t.hours4 + t.hours5
                      + t.hours6) as hours,
                      p.name as project_name, u.name as created_by_name,
                      1 as published
                      FROM #__timeclock_timesheet AS t
                      LEFT JOIN #__timeclock_projects as p
                        ON (t.project_id = p.id OR p.id = 0)
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
        $this->_created_by = JRequest::getVar('created_by', 0, '', 'int');
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
        $row = $this->getTable("TimeclockTimesheet");
        $id = is_int($this->_id) ? $this->_id : $this->_id[0];
        $row->load($id);
        if (empty($row->id)) {
            $row->created_by = $this->_created_by;
        }
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
    function getTimesheets($where = "", $limitstart=null, $limit=null, $orderby = "")
    {
        if (empty($where)) {
            $where = " WHERE p.Type<>'HOLIDAY'";
        } else {
            $where .= " AND p.Type<>'HOLIDAY' ";
        }
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
    function countTimesheets($where="")
    {
        if (empty($where)) {
            $where = " WHERE Type<>'HOLIDAY' ";
        } else {
            $where .= " AND Type<>'HOLIDAY' ";
        }
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
        $row       = $this->getTable("TimeclockTimesheet");
        $projModel = JModelLegacy::getInstance("Projects", "TimeclockAdminModel");
        $data = JRequest::get('post');
        $this->lastError = null;
        $this->lastStoreId = $data['id'];

        // Can't have an empty project id.
        if (empty($data["project_id"])) {
            $this->lastError = "Project doesn't exist";
            return false;
        }
        if ($projModel->getUserProjectsCount($data["created_by"]) == 0) {
            $this->lastError = "User has no projects!";
            return false;
        }

        if (empty($data['id'])) {
            $data["created"] = date("Y-m-d H:i:s");
            if (empty($data["created_by"])) {
                $user = JFactory::getUser();
                $data["created_by"] = $user->get("id");
            }
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


}

?>
