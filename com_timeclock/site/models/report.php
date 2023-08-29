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
 * @version    GIT: $Id: 1d23523e3892a5809ebfd024ca10359070d0803a $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;

require_once __DIR__."/default.php";

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
class TimeclockModelsReport extends TimeclockModelsSiteDefault
{    
    /** This is where we cache our users */
    private $_users = null;
    /** This is where we cache our projects */
    private $_projects = null;
    /** This is where we cache our departments */
    private $_departments = null;
    /** This is where we cache our customers */
    private $_customers = null;
    /** This is our saved report */
    private $_report = array();
    /** This is our percentage of holiday pay */
    private $_myusers = null;
    /** This is the type of report */
    protected $type = "report";
    /** This is our context */
    protected $context = null;
    /** This is the default date to start this report on */
    protected $defaultStart = "Y-m-01";

    /**
    * The constructor
    */
    public function __construct()
    {
        $app = Factory::getApplication();
        $this->_user = Factory::getUser();
        $this->context = $this->type;
        parent::__construct(); 
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @return array An array of results.
    */
    public function listUsers($blocked = 0)
    {
        if (is_null($this->_myusers)) {
            $this->_myusers = parent::listUsers($blocked);
            foreach ($this->_myusers as $key => &$user) {
                if ($this->checkUser($user) === false) {
                    unset($this->_myusers[$key]);
                }
            }
            $this->_myusers[0] = (object)array(
                "name"    => Text::_("JNONE"),
                "user_id" => 0,
                "id"      => 0,
                "hide"    => true,
            );
        }
        return $this->_myusers;
    }
    /**
    * Adds another user to _users
    *
    * @param int $id The id of the user to add
    * 
    * @return none
    */
    public function extraUser($id)
    {
        $id = (int)$id;
        if (($id > 0) && !isset($this->_myusers[$id])) {
            $this->_myusers[$id] = $this->getUser($id);
            uasort(
                $this->_myusers,
                function ($user1, $user2) {
                    return strcmp($user1->name, $user2->name);
                }
            );
        }
    }
    /**
    * Checks the user record, and adds anything extra needed
    *
    * @param object &$user The user object to check
    * 
    * @return array An array of results.
    */
    public function checkUser(&$user)
    {
        $user->hide = false;
        $user->pruned = false;
        $user_manager_id = $this->getState("filter.user_manager_id");
        if (is_numeric($user_manager_id)) {
            if ($user->timeclock["manager"] != $user_manager_id) {
                $user->hide = true;
                $user->pruned = true;
            }
        }
        $user_id = $this->getState("filter.user_id");
        if (is_numeric($user_id)) {
            if ($user->id != $user_id) {
                $user->hide = true;
                $user->pruned = true;
            }
        }
        if ($user->block) {
            $user->hide = true;
        }
        return true;
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @return array An array of results.
    */
    public function listItems()
    {
        $query = $this->_buildQuery();
        $query = $this->_buildWhere($query);
        $list  = $this->_getList($query);
        $users = $this->listUsers();
        $this->listProjects();
        $return = array(
            "totals" => array("total" => 0),
        );
        $datatype = $this->getState("datatype");
        foreach ($list as $row) {
            $this->checkTimesheet($row);
            $proj_id                     = (int)$row->project_id;
            $user_id = !is_null($row->user_id) ? (int)$row->user_id : (int)$row->worked_by;
            if ($row->hours == 0) {
                continue;
            }
            $this->checkUserRow($users[$user_id], $row);
            if ($users[$user_id]->hide) {
                continue;
            }
            if ($datatype == "money") {
                $rate = isset($users[$user_id]->timeclock["billableRate"]) ? (float)$users[$user_id]->timeclock["billableRate"] : 0.0;
                $row->hours *= $rate;
            }
            $return[$proj_id]            = isset($return[$proj_id]) ? $return[$proj_id] : array("total" => 0);
            $return[$proj_id][$user_id]  = isset($return[$proj_id][$user_id]) ? $return[$proj_id][$user_id] : 0;
            $return[$proj_id][$user_id] += $row->hours;
            $return[$proj_id]["total"]  += $row->hours;
            $return["totals"][$user_id]  = isset($return["totals"][$user_id]) ? $return["totals"][$user_id] : 0;
            $return["totals"][$user_id] += $row->hours;
            $return["totals"]["total"]  += $row->hours;
        }
        return $return;
    }
    /**
    * Returns a list of compatible saved reports
    * 
    * @return array of objects
    */
    public function listReports()
    {
        $db = Factory::getDBO();
        $start = $this->getState("start");
        $end   = $this->getState("end");
        $type   = $this->getState("report")->type;
        $query = $db->getQuery(TRUE);
        $query->select('*');
        $query->from('#__timeclock_reports');
        $query->where($db->quoteName("type")." = ".$db->quote($type));
        $query->where($db->quoteName("startDate")." = ".$db->quote($start));
        $query->where($db->quoteName("endDate")." = ".$db->quote($end));
        $list = $this->_getList($query, 0, 0);
        foreach ($list as &$item) {
            $this->fixReport($item);
        }
        return $list;
    }
    /**
    * Checks to see if there is a saved report and returns the record
    * 
    * @param mixed $report Either array or object with report information in it
    * 
    * @return int The ID of the report
    */
    protected function fixReport(&$report)
    {
        if (is_array($report)) {
            $report = (object)$report;
        }
        if (!is_object($report)) {
            return;
        }
        $report->filter     = (object)json_decode($report->filter, true);
        $report->timesheets = json_decode($report->timesheets, true);
        foreach (array("users", "customers", "departments") as $field) {
            $report->$field = json_decode($report->$field, true);
            if (is_array($report->$field)) {
                foreach ($report->$field as &$item) {
                    $item = (object)$item;
                }
            }
        }
        $report->projects = (array)json_decode($report->projects, true);
        foreach ($report->projects as &$cat) {
            foreach ($cat["proj"] as &$proj) {
                $proj = (object)$proj;
            }
        }
        
    }
    /**
    * Returns a list of compatible saved reports
    * 
    * @return array of objects
    */
    public function getReportOptions()
    {
        $list = $this->listReports();
        $options = array();
        foreach ($list as $value) {
            $options[] = JHTML::_(
                'select.option', 
                $value->report_id, 
                $value->name
            );
        }
        return $options;
        
    }
    /**
    * Checks to see if there is a saved report and returns the record
    * 
    * @param int $id The ID of the report to get.
    * 
    * @return int The ID of the report
    */
    public function getReport($id = null)
    {
        if (is_null($id)) {
            $id = $this->getState("report")->id;
        }
        if (!isset($this->_report[$id])) {
            $this->_report[$id] = Table::getInstance('TimeclockReports', 'Table');
            $report = &$this->_report[$id];
            $report->load($id);
            $this->fixReport($report);
        }
        return $this->_report[$id];

    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @param int $user_id The user to get the projects for
    * 
    * @return array An array of results.
    */
    public function listProjects()
    {
        if (is_null($this->_projects)) {
            $query = $this->_buildProjQuery();
            $list = $this->_getList($query, 0, 0);
            $this->_projects = array(
                0 => array(
                    "id"          => 0,
                    "name"        => Text::_("JNONE"),
                    "description" => "",
                    "proj"        => array(),
                )
            );
            $ret = &$this->_projects;
            foreach ($list as $entry) {
                $cat  = (int)$entry->parent_id;
                $proj = (int)$entry->project_id;
                $ret[$cat] = isset($ret[$cat]) ? $ret[$cat] : array(
                    "id" => $cat, 
                    "name" => $entry->parent_name, 
                    "description" => $entry->parent_description,
                    "proj" => array()
                );
                $this->_checkProject($entry);
                $ret[$cat]["proj"][$proj] = $entry;
            }
        }
        return $this->_projects;
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @return array An array of results.
    */
    public function listDepartments()
    {
        if (is_null($this->_departments)) {
            $query = $this->_buildDeptQuery();
            $list = $this->_getList($query, 0, 0);
            $this->_departments = array(
                0 => (object)array(
                    "id"          => 0,
                    "name"        => Text::_("JNONE"),
                    "description" => "",
                )
            );
            $ret = &$this->_departments;
            foreach ($list as $entry) {
                $dept = (int)$entry->department_id;
                if (!isset($ret[$dept])) {
                    $ret[$dept] = $entry;
                }
            }
        }
        return $this->_departments;
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @return array An array of results.
    */
    public function listCustomers()
    {
        if (is_null($this->_customers)) {
            $query = $this->_buildCustQuery();
            $list = $this->_getList($query, 0, 0);
            $this->_customers = array(
                0 => (object)array(
                    "id"          => 0,
                    "name"        => Text::_("JNONE"),
                    "company"     => Text::_("JNONE"),
                    "description" => "",
                )
            );
            $ret = &$this->_customers;
            foreach ($list as $entry) {
                $cust = (int)$entry->customer_id;
                if (!isset($ret[$cust])) {
                    $ret[$cust] = $entry;
                }
            }
        }
        return $this->_customers;
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @param int $user_id The user to get the projects for
    * 
    * @return array An array of results.
    */
    private function _checkProject(&$entry)
    {
        $entry->nohours = 1;
        if (($entry->type == "PTO") || ($entry->type == "PROJECT") || ($entry->type == "UNPAID")) {
            $entry->nohours = 0;
        }
        $entry->name = Text::_($entry->name);
        $entry->description = Text::_($entry->description);
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @param int $user_id The user to get the projects for
    * 
    * @return array An array of results.
    */
    protected function checkTimesheet(&$entry)
    {
        if (($entry->project_type == "HOLIDAY") || ($entry->project_type == "FLOATING_HOLIDAY")) {
            $entry->hours = $entry->hours * $this->getHolidayPerc($entry->user_id, $entry->worked);
        }
        if (TimeclockHelpersDate::beforeStartDate($entry->worked, $entry->user_id)) {
            $entry->hours = 0;
        }
        if (TimeclockHelpersDate::afterEndDate($entry->worked, $entry->user_id)) {
            $entry->hours = 0;
        }
        // Round the hours
        $params = $this->getState("params");
        $decimals = empty($params->decimalPlaces) ? 2 : $params->decimalPlaces;
        $entry->hours = round($entry->hours, $decimals);
    }
    /**
    * Checks a user for this project
    *
    * @param object &$user The user array to use
    * @param object $row   The row to check on
    * 
    * @return array An array of results.
    */
    protected function checkUserRow(&$user, &$row)
    {
        if ($row->hours > 0) {
            if ($user && !$user->pruned && $user->hide) {
                $user->hide = false;
            }
            if ($row) {
                $this->extraUser($row->user_id);
                $user = $this->_myusers[$row->user_id];
            }
        }

    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @param int    $user_id The user to get the projects for
    * @param string $date    The date to get the holiday percentage for.
    * 
    * @return array An array of results.
    */
    protected function getHolidayPerc($id, $date)
    {
        return TimeclockHelpersTimeclock::getHolidayPerc($id, $date);

    }

    /**
    * This creates data for the store.  It should be overwritten by child classes
    * if they want to store a different set.
    *
    * @return Table instance with data in it.
    */
    protected function buildStore()
    {
        $app = Factory::getApplication();
        $row = Table::getInstance('TimeclockReports', 'Table');
        
        if (!is_object($row)) {
            return false;
        }
        $date     = date("Y-m-d H:i:s");
        $type     = $this->getState("report")->type;
        $start    = $this->getState("start");
        $end      = $this->getState("end");
        $datatype = $this->getState("datatype");

        $name = $app->input->get("report_name", "", "raw");
        if (empty($name)) {
            return false;
        }
        $row->name        = $name;
        $row->created_by  = Factory::getUser()->id;
        $row->created     = $date;
        $desc             = $app->input->get("report_description", "", "raw");
        if (!empty($desc)) {
            $row->description = $desc;
        }
        $row->modified    = $date;
        $row->startDate   = $start;
        $row->endDate     = $end;
        $row->type        = $type;
        $row->datatype        = $datatype;
        $row->filter      = json_encode($this->getState("filter"));
        $row->users       = json_encode($this->listUsers());
        $row->timesheets  = json_encode($this->listItems());
        $row->projects    = json_encode($this->listProjects());
        $row->customers   = json_encode($this->listCustomers());
        $row->departments = json_encode($this->listDepartments());
        return $row;
    }
    /**
    * Stores the data given, or request data.
    *
    * @return Table instance with data in it.
    */
    public function store()
    {
        $row = $this->buildStore();
        $report_id = $this->getState("report")->id;
        if (!empty($report_id) || !is_object($row)) {
            return false;
        }
        // Make sure the record is valid
        if (!$row->check()) {
            return false;
        }
    
        if (!$row->store()) {
            return false;
        }
        // Set our id for things after this.
        if (empty($this->id) || (isset($this->id[0]) && empty($this->id[0]))) {
            $key = $row->getKeyName();
            $this->id = array($row->$key);
        }
        return $row;

    }
    
    /**
    * Method to auto-populate the model state.
    *
    * This method should only be called once per instantiation and is designed
    * to be called on the first call to the getState() method unless the model
    * configuration flag to ignore the request is set.
    * 
    * @return  void
    *
    * @note    Calling getState in this method will result in recursion.
    * @since   12.2
    */
    protected function populateState()
    {
        $context = is_null($this->context) ? $this->table : $this->context;

        $app = Factory::getApplication();

        $start = TimeclockHelpersDate::fixDate(
            $app->getUserStateFromRequest($context.'.start', 'start', '')
        );
        // If this is the first day of the month, we want to see last month.
        $start = empty($start) ?  date($this->defaultStart, time() - 86400) : $start;
        $this->setState('start', $start);

        $end = TimeclockHelpersDate::fixDate(
            $app->getUserStateFromRequest($context.'.end', 'end', '')
        );
        $end = empty($end) ?  date("Y-m-d") : $end;
        $this->setState('end', $end);

        $datatype = $app->getUserStateFromRequest($context.'.datatype', 'datatype', 'hours');
        $this->setState("datatype", $datatype);
        
        $user = Factory::getUser();

        $date = TimeclockHelpersDate::fixDate(
            $app->input->get('date', date("Y-m-d"), "raw")
        );
        $date = empty($date) ?  date("Y-m-d") : $date;
        $this->setState('date', $date);
        
        $this->setState("report.type", $this->type);
        $report_id = $app->input->get("report_id", 0, "int");
        $this->setState("report.report_id", $report_id);
        
        $this->_populateFilter();
        $this->_populateState();
    }
    /**
    * This populates the filter used for narrowing down reports.
    *
    * This method should only be called once per instantiation and is designed
    * to be called on the first call to the getState() method unless the model
    * configuration flag to ignore the request is set.
    * 
    * @param object $registry Ignored in subclasses
    * 
    * @return  void
    */
    protected function _populateFilter()
    {
        $app = Factory::getApplication();
        $context = is_null($this->context) ? $this->table : $this->context;

        $category = $app->getUserStateFromRequest($context.'.filter.category', 'filter_category', '');
        $this->setState("filter.category", $category);

        $department = $app->getUserStateFromRequest($context.'.filter.department', 'filter_department', '');
        $this->setState("filter.department", $department);

        $customer = $app->getUserStateFromRequest($context.'.filter.customer', 'filter_customer', '');
        $this->setState("filter.customer", $customer);

        $user_manager_id = $app->getUserStateFromRequest($context.'.filter.user_manager_id', 'filter_user_manager_id', '');
        $this->setState("filter.user_manager_id", $user_manager_id);

        $user_id = $app->getUserStateFromRequest($context.'.filter.user_id', 'filter_user_id', '');
        $this->setState("filter.user_id", $user_id);

        $proj_manager_id = $app->getUserStateFromRequest($context.'.filter.proj_manager_id', 'filter_proj_manager_id', '');
        $this->setState("filter.proj_manager_id", $proj_manager_id);

        $proj_type = $app->getUserStateFromRequest($context.'.filter.project_type', 'filter_project_type', '');
        $this->setState("filter.proj_type", $proj_type);

        $user = Factory::getUser();
        if ((!$user->authorise('core.edit.state', 'com_timeclock')) 
            &&  (!$user->authorise('core.edit', 'com_timeclock'))
        ) {
                $this->setState("filter.published", 1);
                $this->setState("filter.archived", 2);
        }

        $this->setState("filter.type", $this->type);
    }
    /**
    * Method to auto-populate the model state.
    *
    * This method should only be called once per instantiation and is designed
    * to be called on the first call to the getState() method unless the model
    * configuration flag to ignore the request is set.
    * 
    * @param object $registry Ignored in subclasses
    * 
    * @return  void
    *
    * @note    Calling getState in this method will result in recursion.
    * @since   12.2
    */
    protected function _populateState()
    {
        $context = is_null($this->context) ? $this->table : $this->context;

        $app = Factory::getApplication();
        // Load state from the request.
        $pk = $app->input->get('id', array(), "array");
        $this->setState('id', $pk);

        // Load the parameters.
        $params = ComponentHelper::getParams('com_timeclock');
        $this->setState('params', $params);
    }
    /**
    * Builds the query to be used by the model
    *
    * @return object Query object
    */
    protected function _buildQuery()
    {
        $db = Factory::getDBO();
        $query = $db->getQuery(TRUE);
        $query->select('DISTINCT t.timesheet_id, t.user_id as worked_by,
            (t.hours1 + t.hours2 + t.hours3 + t.hours4 + t.hours5 + t.hours6)
            as hours, t.hours1 as hours1, t.hours2 as hours2, t.hours3 as hours3,
            t.hours4 as hours4, t.hours5 as hours5, t.hours6 as hours6,
            t.worked, t.project_id, t.notes, z.user_id as user_id');
        $query->from('#__timeclock_timesheet as t');
        $query->select('p.name as project, p.type as project_type, 
            p.description as project_description, p.parent_id as cat_id, 
            p.department_id as department_id, p.customer_id as customer_id,
            p.manager_id as proj_manager_id, p.wcCode1 as wcCode1, 
            p.wcCode2 as wcCode2, p.wcCode3 as wcCode3, p.wcCode4 as wcCode4,
            p.wcCode5 as wcCode5, p.wcCode6 as wcCode6');
        $query->leftjoin('#__timeclock_projects as p on t.project_id = p.project_id');
        $query->select('q.name as cat_name, q.description as cat_description');
        $query->leftjoin('#__timeclock_projects as q on p.parent_id = q.project_id');
        $query->select('u.name as user');
        $query->leftjoin('#__timeclock_users as z on 
            ((z.user_id = t.user_id OR p.type = "HOLIDAY") AND t.project_id = z.project_id)');
        $query->leftjoin('#__users as u on z.user_id = u.id');
        $query->where($db->quoteName("t.project_id").">=".$db->quote(0));
        return $query;
    }
    /**
    * Builds the filter for the query
    * 
    * @param object $query Query object
    * @param int    $id    The id of the object to get
    * 
    * @return object Query object
    *
    */
    protected function _buildWhere(&$query)
    { 
        $db = Factory::getDBO();
        $start = $this->getState("start");
        $end   = $this->getState("end");
        $query->where($db->quoteName("t.worked").">=".$db->quote($start));
        $query->where($db->quoteName("t.worked")."<=".$db->quote($end));
        $query->order("t.worked asc");

        $project_type = $this->getState("filter.project_type");
        if (!empty($project_type)) {
            $query->where($db->quoteName("p.type")." = ".$db->quote($project_type));
        }
        $category = $this->getState("filter.category");
        if (is_numeric($category)) {
            $query->where($db->quoteName("p.parent_id")." = ".$db->quote((int)$category));
        }
        $proj_manager_id = $this->getState("filter.proj_manager_id");
        if (is_numeric($proj_manager_id)) {
            $query->where($db->quoteName("p.manager_id")." = ".$db->quote((int)$proj_manager_id));
        }
        $customer = $this->getState("filter.customer");
        if (is_numeric($customer)) {
            $query->where($db->quoteName("p.customer_id")." = ".$db->quote((int)$customer));
        }
        $department = $this->getState("filter.department");
        if (is_numeric($department)) {
            $query->where($db->quoteName("p.department_id")." = ".$db->quote((int)$department));
        }
        $user_id = $this->getState("filter.user_id");
        if (is_numeric($user_id)) {
            $query->where($db->quoteName("z.user_id")." = ".$db->quote((int)$user_id));
        }
        $category = $this->getState("filter.category");
        if (is_numeric($category)) {
            $query->where($db->quoteName("p.parent_id")." = ".$db->quote((int)$category));
        }
        return $query;
    }
    /**
    * Builds the query to be used by the model
    *
    * @return object Query object
    */
    protected function _buildProjQuery()
    {
        $db = Factory::getDBO();
        $query = $db->getQuery(TRUE);
        $query->select('p.project_id as project_id, 
            p.name as name, p.parent_id as parent_id, p.description as description,
            p.type as type');
        $query->from('#__timeclock_projects as p');
        $query->select('r.name as parent_name, r.description as parent_description');
        $query->leftjoin('#__timeclock_projects as r on p.parent_id = r.project_id');
        $query->where("p.type <> 'CATEGORY'");
        $query->order("p.name asc");
        return $query;
    }
    /**
    * Builds the query to be used by the model
    *
    * @return object Query object
    */
    protected function _buildDeptQuery()
    {
        $db = Factory::getDBO();
        $query = $db->getQuery(TRUE);
        $query->select('*');
        $query->from('#__timeclock_departments as d');
        $query->order("d.name asc");
        return $query;
    }
    /**
    * Builds the query to be used by the model
    *
    * @return object Query object
    */
    protected function _buildCustQuery()
    {
        $db = Factory::getDBO();
        $query = $db->getQuery(TRUE);
        $query->select('*');
        $query->from('#__timeclock_customers as c');
        $query->order("c.company asc");
        return $query;
    }
}
