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
    /**
     * Constructor that retrieves the ID from the request
     *
     * @return    void
     */
    function __construct()
    {
        parent::__construct();
    
        $array = JRequest::getVar('cid',  0, '', 'array');
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
    function getProjects($limitstart=null, $limit=null)
    {
        $key = (string)$limitstart.$limit;
        if (empty($this->_projects[$key])) {
            $query = "select #__timeclock_projects.*, parents.name as parentname from #__timeclock_projects left join #__timeclock_projects as parents on #__timeclock_projects.parent_id = parents.id order by id asc";
            $this->_projects[$key] = $this->_getList($query, $limitstart, $limit);
        }
        return $this->_projects[$key];
    }

    /**
     * Method to display the view
     *
     * @return string
     */
    function countProjects()
    {
        if (empty($this->_projectCount)) {
            $query = "select * from #__timeclock_projects order by id asc";
            $this->_projectCount = $this->_getListCount($query);
        }
        return $this->_projectCount;
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

}

?>
