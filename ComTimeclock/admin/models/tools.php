<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.5 component
 * Copyright (C) 2008-2009 Hunt Utilities Group, LLC
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
 * @copyright  2008-2009 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
/** Include the project stuff */
$base      = dirname(JApplicationHelper::getPath("front", "com_timeclock"));
$adminbase = dirname(JApplicationHelper::getPath("admin", "com_timeclock"));

require_once $adminbase.DS.'tables'.DS.'timeclockcustomers.php';
require_once $adminbase.DS.'tables'.DS.'timeclockprefs.php';
require_once $adminbase.DS.'tables'.DS.'timeclockprojects.php';
require_once $adminbase.DS.'tables'.DS.'timeclockusers.php';
require_once $base.DS.'tables'.DS.'timeclocktimesheet.php';

/**
 * ComTimeclock model
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockAdminModelTools extends JModel
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
        $this->_customers =& JTable::getInstance("TimeclockCustomers", "Table");
        $this->_prefs =& JTable::getInstance("TimeclockPrefs", "Table");
        $this->_projects =& JTable::getInstance("TimeclockProjects", "Table");
        $this->_users =& JTable::getInstance("TimeclockUsers", "Table");
        $this->_timesheet =& JTable::getInstance("TimeclockTimesheet", "Table");
        $this->_db =& JFactory::getDBO();

    }

    /**
    * This function goes through and checks all of the databases
    *
    * @return array The problem array
    */
    function dbCheck()
    {
        $ret = array();
        $ret = array_merge($ret, $this->_dbCheckPrefs());
        $ret = array_merge($ret, $this->_dbCheckCustomers());
        $ret = array_merge($ret, $this->_dbCheckProjects());
        $ret = array_merge($ret, $this->_dbCheckTimesheets());
        $ret = array_merge($ret, $this->_dbCheckUsers());
        return $ret;
    }


    /**
    * This function goes through and checks the prefs
    *
    * @return array The problem array
    */
    private function _dbCheckPrefs()
    {
        $ret = array();
        return $ret;
    }

    /**
    * This function goes through and checks the prefs
    *
    * @return array The problem array
    */
    private function _dbCheckCustomers()
    {
        $ret = array();
        return $ret;
    }

    /**
    * This function goes through and checks the prefs
    *
    * @return array The problem array
    */
    private function _dbCheckProjects()
    {
        $ret = array();
        return $ret;
    }

    /**
    * This function goes through and checks the prefs
    *
    * @return array The problem array
    */
    private function _dbCheckTimesheets()
    {
        $ret = array();
        return $ret;
    }

    /**
    * This function goes through and checks the prefs
    *
    * @return array The problem array
    */
    private function _dbCheckUsers()
    {
        $ret = array();
        $ret[] = $this->_dbCheckUsersCategories();
        $ret[] = $this->_dbCheckUsersExist();
        $ret[] = $this->_dbCheckUsersProjExist();
        return $ret;
    }

    /**
    * This checks for users in categories.
    *
    * @return array The problem array
    */
    private function _dbCheckUsersGetUsers()
    {
        static $data;
        if (!is_array($data)) {
            $sql = "select u.*, p.*, u.id as proj_id, ju.name as user_name
                    from #__timeclock_users as u
                    LEFT JOIN #__timeclock_projects as p
                    ON u.id = p.id
                    LEFT JOIN #__users as ju
                    ON ju.id = u.user_id";
            $this->_db->setQuery($sql);
            $data = $this->_db->loadAssocList();
        }
        return $data;
    }
    /**
    * This checks for users in categories.
    *
    * @return array The problem array
    */
    private function _dbCheckUsersCategories()
    {
        $test = array(
            "name" => "Checking for users attached to categories",
            "result" => true,
            "description" => "If you edit the user in 'User Configurations' you can"
                ." remove them from the offending projects.",
        );
        $ret = $this->_dbCheckUsersGetUsers();
        foreach ($ret as $row) {
            if ($row["type"] == "CATEGORY") {
                $test["result"] = false;
                $test["log"]   .= "User ".$row["user_name"]
                    ." found in ".$row["name"]."\n";
            }
        }
        return $test;
    }
    /**
    * This checks for users in categories.
    *
    * @return array The problem array
    */
    private function _dbCheckUsersExist()
    {
        $test = array(
            "name" => "Checking that all users attached to projects exist",
            "result" => true,
            "description" => "If this fails data is lost.  The database entries for "
                ." this should be removed with your favorite database tool.",
        );
        $ret = $this->_dbCheckUsersGetUsers();
        foreach ($ret as $row) {
            if (is_null($row["user_name"])) {
                $test["result"] = false;
                $test["log"]   .= "User #".$row["user_id"]." does not exist.\n";
            }
        }
        return $test;
    }
    /**
    * This checks for users in categories.
    *
    * @return array The problem array
    */
    private function _dbCheckUsersProjExist()
    {
        $test = array(
            "name" => "Checking that all projects with users attached exist",
            "result" => true,
            "description" => "If this fails data is lost.  The database entries for "
                ." this should be removed with your favorite database tool.",
        );
        $ret = $this->_dbCheckUsersGetUsers();
        foreach ($ret as $row) {
            if (is_null($row["id"])) {
                $test["result"] = false;
                $test["log"]   .= "Project #".$row["proj_id"]." does not exist.\n";
            }
        }
        return $test;
    }
}

?>
