<?php
/**
 * This component is for tracking tim
 *
 * PHP Version 5
 *
 * <pre>
 * com_timeclock is a Joomla! 3.1 component
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
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: $Id: 10047929d21a2fbd9c667b0a61cbd703279bcbc8 $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;

 
/**
 * Description Here
 *
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockModelsTools extends JModelBase
{
    /**
    * Constructor that retrieves the ID from the request
    *
    * @return    void
    */
    public function __construct()
    {
        parent::__construct();
        $this->_customers = Table::getInstance("TimeclockCustomers", "Table");
        $this->_prefs = Table::getInstance("TimeclockDepartments", "Table");
        $this->_projects = Table::getInstance("TimeclockProjects", "Table");
        $this->_users = Table::getInstance("TimeclockUsers", "Table");
        $this->_timesheet = Table::getInstance("TimeclockTimesheet", "Table");
        $this->_db = Factory::getDBO();

    }

    /**
    * This function goes through and checks all of the databases
    *
    * @return array The problem array
    */
    public function setup()
    {
        $app = Factory::getApplication();
        $package = $app->input->get("package", null);
        return $this->_setupDownload($package);
    }
    /**
    * This function goes through and checks all of the databases
    *
    * @param string $package The name of the package to download
    *
    * @return array The problem array
    */
    private function _setupDownload($package)
    {
        $url = "http://downloads.hugllc.com/Joomla/Timeclock/contrib/";
        switch($package) {
        case "phpgraph":
            $url .= "phpgraph.zip";
            break;
        case "phpexcel":
            $url .= "phpexcel.zip";
            break;
        default:
            return null;
        }
        if (file_exists(JPATH_ROOT."/compoents/com_timeclock/contrib/".$package)) {
            // Don't install something twice.
            return null;
        }
        $data = file_get_contents($url);
        $file = tempnam(sys_get_temp_dir(), "timeclock");
        $fd = fopen($file, "w");
        $ret = false;
        if ($fd) {
            fwrite($fd, $data);
            fclose($fd);
            $zip = new ZipArchive();
            if ($zip->open($file)) {
                $path = JPATH_ROOT."/components/com_timeclock/contrib/";
                if ($zip->extractTo($path)) {
                    $ret = true;
                }
                $zip->close();
            }
            unlink($file);
        }
        return $ret;
    }
    /**
    * This function goes through and checks all of the databases
    *
    * @return array The problem array
    */
    public function dbCheck()
    {
        $ret = array(
            "Departments" => $this->_dbCheckDepartments(),
            "Customers" => $this->_dbCheckCustomers(),
            "Projects" => $this->_dbCheckProjects(),
            "Timesheets" => $this->_dbCheckTimesheets(),
            "Users" => $this->_dbCheckUsers(),
        );
        return $ret;
    }



    /**
    * This function goes through and checks the prefs
    *
    * @return array The problem array
    */
    private function _dbCheckDepartments()
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
        $ret[] = $this->_dbCheckProjectsBadType();
        $ret[] = $this->_dbCheckProjectsBadManager();
        return $ret;
    }
    /**
    * This function goes through and checks the prefs
    *
    * @return array The problem array
    */
    private function _dbCheckProjectsBadType()
    {
        $test = array(
            "name" => "Checking for Invalid Parent Types",
            "result" => true,
            "description" => "Fix: Please go into the project editor and select a "
                            ." valid parent project.",
            "log" => "",
        );
        $ret = $this->_dbCheckProjectsGetProjects();
        $valid_array = array("CATEGORY");
        foreach ((array)$ret as $row) {
            if (is_null($row["parent_type"])) {
                continue;
            }
            if (array_search($row["parent_type"], $valid_array) === false) {
                $test["result"] = false;
                $test["log"] .= "Project ".$row["name"]." has invalid parent "
                                .$row["parent_name"]."\n";
            }
        }
        return $test;
    }
    /**
    * This function goes through and checks the prefs
    *
    * @return array The problem array
    */
    private function _dbCheckProjectsBadManager()
    {
        $test = array(
            "name" => "Checking for Missing Managers",
            "result" => true,
            "description" => "Fix: Please go into the project editor and select a "
                            ." valid project manager.",
            "log" => "",
        );
        $ret = $this->_dbCheckProjectsGetProjects();

        foreach ((array)$ret as $row) {
            if (($row["manager"] != 0) && is_null($row["manager_name"])) {
                $test["result"] = false;
                $test["log"] .= "Project ".$row["name"]." has invalid parent "
                                .$row["parent_name"]."\n";
            }
        }
        return $test;
    }
    /**
    * This checks for users in categories.
    *
    * @return array The problem array
    */
    private function _dbCheckProjectsGetProjects()
    {
        static $data;
        if (!is_array($data)) {
            $sql = "SELECT p.*, u.name as manager_name, pp.type as parent_type,
                    pp.name as parent_name
                    FROM #__timeclock_projects as p
                    LEFT JOIN #__users as u
                    ON p.manager_id = u.id
                    LEFT JOIN #__timeclock_projects as pp
                    ON p.parent_id = pp.project_id";
            $this->_db->setQuery($sql);
            $data = $this->_db->loadAssocList();
        }
        return $data;
    }

    /**
    * This function goes through and checks the prefs
    *
    * @return array The problem array
    */
    private function _dbCheckTimesheets()
    {
        $ret = array();
        $ret[] = $this->_dbCheckTimesheetsNoDate();
        $ret[] = $this->_dbCheckTimesheetsBadProject();
        $ret[] = $this->_dbCheckTimesheetsBadUser();
        return $ret;
    }
    /**
    * This function goes through and checks the prefs
    *
    * @return array The problem array
    */
    private function _dbCheckTimesheetsBadProject()
    {
        $test = array(
            "name" => "Checking for Timesheets attached to non-existant project",
            "result" => true,
            "description" => "These should be fixed in the timesheet entry in the "
                ." administrator panel.  These will show up on reports and no where "
                ." else.",
            "log" => "",
        );
        $ret = $this->_dbCheckTimesheetsGetTimesheet(
            " p.name IS NULL "
        );
        foreach ((array)$ret as $row) {
            $test["result"] = false;
            $test["log"] .= "Record #".$row["timesheet_id"];
            $test["log"] .= " (".$row["user_name"]." on ".$row["project_name"].") ";
            $test["log"] .= " has no attached project\n";
        }
        return $test;
    }
    /**
    * This function goes through and checks the prefs
    *
    * @return array The problem array
    */
    private function _dbCheckTimesheetsBadUser()
    {
        $test = array(
            "name" => "Checking for Timesheets attached to non-existant user",
            "result" => true,
            "description" => "These should be fixed in the timesheet entry in the "
                ." administrator panel.  These will show up on reports and no where "
                ." else.",
            "log" => "",
        );
        $ret = $this->_dbCheckTimesheetsGetTimesheet(
            " u.name IS NULL "
        );
        foreach ((array)$ret as $row) {
            $test["result"] = false;
            $test["log"] .= "Record #".$row["timesheet_id"];
            $test["log"] .= " (".$row["user_name"]." on ".$row["project_name"].") ";
            $test["log"] .= " has no attached user\n";
        }
        return $test;
    }
    /**
    * This function goes through and checks the prefs
    *
    * @return array The problem array
    */
    private function _dbCheckTimesheetsNoDate()
    {
        $test = array(
            "name" => "Checking for Timesheets with no date",
            "result" => true,
            "description" => "These should be fixed in the timesheet entry in the "
                ." administrator panel.  These will show up on reports and no where "
                ." else.",
            "log" => "",
        );
        $ret = $this->_dbCheckTimesheetsGetTimesheet(
            " worked = '0000-00-00' "
        );
        foreach ((array)$ret as $row) {
            $test["result"] = false;
            $test["log"] .= "Record #".$row["timesheet_id"];
            $test["log"] .= " (".$row["user_name"]." on ".$row["project_name"].") ";
            $test["log"] .= " has no date\n";
        }
        return $test;
    }
    /**
    * This checks for users in categories.
    *
    * @param string $where The where clause to use
    *
    * @return array The problem array
    */
    private function _dbCheckTimesheetsGetTimesheet($where=null)
    {
        static $data;
        if (!isset($data[$where]) || !is_array($data[$where])) {
            $sql = "select t.*, u.name as user_name, p.name as project_name
                    from #__timeclock_timesheet as t
                    LEFT JOIN #__timeclock_projects as p
                    ON t.project_id = p.project_id
                    LEFT JOIN #__users as u
                    ON u.id = t.created_by ";
            if (!empty($where)) {
                $sql .= " WHERE ".$where;
            }
            $this->_db->setQuery($sql);
            $data[$where] = $this->_db->loadAssocList();
        }
        return $data[$where];
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
            $sql = "select u.*, p.*, u.project_id as proj_id, ju.name as user_name
                    from #__timeclock_users as u
                    LEFT JOIN #__timeclock_projects as p
                    ON u.project_id = p.project_id
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
            "log" => "",
        );
        $ret = $this->_dbCheckUsersGetUsers();
        foreach ((array)$ret as $row) {
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
            "log" => "",
        );
        $ret = $this->_dbCheckUsersGetUsers();
        foreach ((array)$ret as $row) {
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
            "log" => "",
        );
        $ret = $this->_dbCheckUsersGetUsers();
        foreach ((array)$ret as $row) {
            if (is_null($row["project_id"])) {
                $test["result"] = false;
                $test["log"]   .= "Project #".$row["proj_id"]." does not exist.\n";
            }
        }
        return $test;
    }
}