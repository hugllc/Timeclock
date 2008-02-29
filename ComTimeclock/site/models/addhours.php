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

/** Include the project stuff */
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'projects.php';

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
class TimeclockModelAddHours extends JModel
{
    /**
     * Constructor that retrieves the ID from the request
     *
     * @return    void
     */
    function __construct()
    {
        parent::__construct();

        $others = TableTimeclockPrefs::getPref("admin_otherTimesheets");
        if ($others) $cid = JRequest::getVar('cid', 0, '', 'array');
        if (empty($cid)) {
            $u =& JFactory::getUser();
            $cid = $u->get("id");
        }
        $this->setId($cid);

        $date = JRequest::getVar('date', 0, '', 'string');
        $this->setDate($date);

        $project = JRequest::getVar('projid', 0, '', 'string');
        $this->setProject($project);

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
        if (is_array($id)) {
            $this->_id = (int)$id[0];
        } else {
            $this->_id = (int)$id;
        }
    }
    /**
     * Method to set the id
     *
     * @param int $date The date to set
     *
     * @return    void
     */
    function setDate($date)
    {
        if (empty($date)) {
            $this->_date = date("Y-m-d");
        } else {
            $this->_date = self::_fixDate($date);
        }
    }

    /**
     * Method to set the id
     *
     * @return    void
     */
    function getDate()
    {
        return $this->_date;
    }

    /**
     * Method to set the id
     *
     * @param int $project The project to set
     *
     * @return    void
     */
    function setProject($project)
    {
        if (empty($project)) {
            $this->_project = null;
        } else {
            $this->_project = self::_fixDate($date);
        }
    }

    /**
     * Method to display the view
     *
     * @return string
     */
    function getData()
    {
   
        $query = "SELECT t.*
                  FROM #__timeclock_timesheet as t
                  WHERE t.worked ='".$this->_date."'
                     AND t.created_by = '".$this->_id."'
                  ";
        $ret = $this->_getList($query);
        if (!is_array($ret)) return array();
        $data = array();
        foreach ($ret as $d) {
            $data[$d->project_id] = $d;
        }
        return $data;
    }


    /**
     * Where statement for the reporting period dates
     *
     * @param int $m The month
     * @param int $d The day
     * @param int $y The year
     *
     * @return array
     */ 
    private function _dateUnix($m, $d, $y)
    {
        return mktime(6,0,0, $m, $d, $y);
    }


    /**
     * Where statement for the reporting period dates
     *
     * @param string $date Date to use in MySQL format ("Y-m-d H:i:s")
     *
     * @return array
     */ 
    function _fixDate($date) {
        preg_match("/[1-9][0-9]{3}-[0-1][0-9]-[0-3][0-9]/", $date, $ret);
        return $ret[0];
    }



}

?>
