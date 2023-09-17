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
 * @version    GIT: $Id: a70fad7ecea96c148fd07befe386dd1bba7cfe4f $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
namespace HUGLLC\Component\Timeclock\Administrator\Model;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Form\FormFactoryInterface;


defined( '_JEXEC' ) or die();

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
class ToolsModel extends AdminModel
{
    /**
     * The prefix to use with controller messages.
     *
     * @var    string
     * @since  1.6
     */
    protected $text_prefix = 'COM_TIMECLOCK';
    /**
     * The type alias for this content type.
     *
     * @var    string
     * @since  3.2
     */
    public $typeAlias = 'com_timeclock.customer';

    /**
     * Constructor.
     *
     * @param   array                 $config       An array of configuration options (name, state, dbo, table_path, ignore_request).
     * @param   MVCFactoryInterface   $factory      The factory.
     * @param   FormFactoryInterface  $formFactory  The form factory.
     *
     * @since   1.6
     * @throws  \Exception
     */
    public function __construct($config = [], MVCFactoryInterface $factory = null, FormFactoryInterface $formFactory = null)
    {
        parent::__construct($config, $factory, $formFactory);

        // $this->register()
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
    * This returns the table for this model
    * 
    * @return  Table object
    */
    public function getForm($name = '', $prefix = '', $options = [])
    {
        // return Form::getInstance($this->table, 'Form');
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
