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
 * @version    GIT: $Id: 1d23523e3892a5809ebfd024ca10359070d0803a $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once __DIR__."/default.php";

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
class TimeclockModelsReport extends TimeclockModelsSiteDefault
{    
    /** This is where we cache our users */
    private $_users = null;
    /** This is where we cache our projects */
    private $_projects = null;
    /** This is our percentage of holiday pay */
    private $_holiday_perc = array();
    /** This is our saved report */
    private $_report = array();
    /** This is our percentage of holiday pay */
    private $_myusers = null;

    /**
    * The constructor
    */
    public function __construct()
    {
        $app = JFactory::getApplication();
        $this->_user = JFactory::getUser();
        
        parent::__construct(); 
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @return array An array of results.
    */
    public function listUsers()
    {
        if (is_null($this->_myusers)) {
            $this->_myusers = parent::listUsers();
            foreach ($this->_myusers as $key => &$user) {
                $this->checkUser($user);
            }
        }
        return $this->_myusers;
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
        $manager_id = $this->getState("filter.user_manager_id");
        if (is_numeric($manager_id)) {
            if ($user->timeclock["manager"] != $manager_id) {
                $user->hide = true;
            }
        }
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
        foreach ($list as $row) {
            $this->checkTimesheet($row);
            $proj_id                     = (int)$row->project_id;
            $user_id = !is_null($row->user_id) ? (int)$row->user_id : (int)$row->worked_by;
            if ($users[$user_id]->hide) {
                continue;
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
    * Checks to see if there is a saved report and returns the record
    * 
    * @param int $id The ID of the report to get.
    * 
    * @return int The ID of the report
    */
    public function getReport($id = null)
    {
        if (is_null($id)) {
            $id = $this->getState("report.id");
        }
        if (!isset($this->_report[$id])) {
            $this->_report[$id] = JTable::getInstance('TimeclockReports', 'Table');
            $this->_report[$id]->load($id);
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
            $this->_projects = array();
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
        if ($entry->project_type == "HOLIDAY") {
            $entry->hours = $entry->hours * $this->getHolidayPerc($entry->user_id);
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
    * Build query and where for protected _getList function and return a list
    *
    * @param int $user_id The user to get the projects for
    * 
    * @return array An array of results.
    */
    protected function getHolidayPerc($id)
    {
        if (!isset($this->_holiday_perc[$id])) {
            $this->_holiday_perc[$id] = ((int)TimeclockHelpersTimeclock::getUserParam("holidayperc", $id)) / 100;
        }
        return $this->_holiday_perc[$id];
    }

    /**
    * This creates data for the store.  It should be overwritten by child classes
    * if they want to store a different set.
    *
    * @return JTable instance with data in it.
    */
    protected function buildStore()
    {
        $app = JFactory::getApplication();
        $row = JTable::getInstance('TimeclockReports', 'Table');
        
        if (!is_object($row)) {
            return false;
        }
        $date = date("Y-m-d H:i:s");

        $id = $this->getState("report.id");
        if (empty($id)) {
            $row->name        = $this->getState("report.name");
            $row->created_by  = JFactory::getUser()->id;
            $row->created     = $date;
        }
        $desc = $this->getState("report.description");
        if (!empty($desc)) {
            $row->description = $desc;
        }
        $row->modified    = $date;
        $row->startDate   = $this->getState("start");
        $row->endDate     = $this->getState("end");
        $row->type        = $this->getState("report.type");
        $row->filter      = json_encode($this->getState("filter"));
        $row->users       = json_encode($this->listUsers());
        $row->projects    = json_encode($this->listProjects());
        $row->timesheets  = json_encode($this->listItems());
        return $row;
    }
    /**
    * Stores the data given, or request data.
    *
    * @return JTable instance with data in it.
    */
    public function store()
    {
        $row = $this->buildStore();
        if (!empty($this->getState("report.id"))) {
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

        $app = JFactory::getApplication();
        $registry = $this->loadState();

        $start = TimeclockHelpersDate::fixDate(
            $app->input->get('start', "", "raw")
        );
        $start = empty($start) ?  date("Y-m-01") : $start;
        $registry->set('start', $start);

        $end = TimeclockHelpersDate::fixDate(
            $app->input->get('end', "", "raw")
        );
        $end = empty($end) ?  date("Y-m-d") : $end;
        $registry->set('end', $end);
        
        $user = JFactory::getUser();

        $date = TimeclockHelpersDate::fixDate(
            $app->input->get('date', date("Y-m-d"), "raw")
        );
        $date = empty($date) ?  date("Y-m-d") : $date;
        $registry->set('date', $date);
        
        $type = 'report';
        $registry->set('type', $type);
        
        $this->_populateFilter($registry);
        $this->_populateState($registry);
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
    protected function _populateFilter($registry = null)
    {
        $app = JFactory::getApplication();

        $category = $app->getUserStateFromRequest($context.'.filter.category', 'filter_category', '');
        $registry->set('filter.category', $category);

        $department = $app->getUserStateFromRequest($context.'.filter.department', 'filter_department', '');
        $registry->set('filter.department', $department);

        $customer = $app->getUserStateFromRequest($context.'.filter.customer', 'filter_customer', '');
        $registry->set('filter.customer', $customer);

        $user_manager_id = $app->getUserStateFromRequest($context.'.filter.user_manager_id', 'filter_user_manager_id', '');
        $registry->set('filter.user_manager_id', $user_manager_id);

        $proj_manager_id = $app->getUserStateFromRequest($context.'.filter.proj_manager_id', 'filter_proj_manager_id', '');
        $registry->set('filter.proj_manager_id', $proj_manager_id);

        $proj_type = $app->getUserStateFromRequest($context.'.filter.project_type', 'filter_project_type', '');
        $registry->set('filter.project_type', $proj_type);

        $report_id = $app->input->get("report_id", 0, int);
        $registry->set("report.id", $report_id);
        
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
    protected function _populateState($registry = null)
    {
        $context = is_null($this->context) ? $this->table : $this->context;

        $app = JFactory::getApplication();
        if (!is_object($registry)) {
            $registry = $this->loadState();
        }
        // Load state from the request.
        $pk = $app->input->get('id', array(), "array");
        $registry->set('id', $pk);

        // Load the parameters.
        $params = JComponentHelper::getParams('com_timeclock');
        $registry->set('params', $params);

        $user = JFactory::getUser();

        if ((!$user->authorise('core.edit.state', 'com_timeclock')) 
            &&  (!$user->authorise('core.edit', 'com_timeclock'))
        ) {
                $registry->set('filter.published', 1);
                $registry->set('filter.archived', 2);
        }

        $this->setState($registry);
    }
    /**
    * Builds the query to be used by the model
    *
    * @return object Query object
    */
    protected function _buildQuery()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(TRUE);
        $query->select('DISTINCT t.timesheet_id, t.user_id as worked_by,
            (t.hours1 + t.hours2 + t.hours3 + t.hours4 + t.hours5 + t.hours6)
            as hours, t.worked, t.project_id, t.notes, z.user_id as user_id');
        $query->from('#__timeclock_timesheet as t');
        $query->select('p.name as project, p.type as project_type, 
            p.description as project_description, p.parent_id as cat_id');
        $query->leftjoin('#__timeclock_projects as p on t.project_id = p.project_id');
        $query->select('q.name as cat_name, q.description as cat_description');
        $query->leftjoin('#__timeclock_projects as q on p.parent_id = q.project_id');
        $query->select('u.name as user');
        $query->leftjoin('#__timeclock_users as z on 
            ((z.user_id = t.user_id OR p.type = "HOLIDAY") AND t.project_id = z.project_id)');
        $query->leftjoin('#__users as u on z.user_id = u.id');
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
        $db = JFactory::getDBO();
        $start = $this->getState("start");
        $end   = $this->getState("end");
        $query->where($db->quoteName("t.worked").">=".$db->quote($start));
        $query->where($db->quoteName("t.worked")."<=".$db->quote($end));
        $query->order("t.worked asc");

        $filter = $this->getState("filter");
        if (!empty($filter->project_type)) {
            $query->where($db->quoteName("p.type")." = ".$db->quote($filter->project_type));
        }
        if (is_numeric($filter->category)) {
            $query->where($db->quoteName("p.parent_id")." = ".$db->quote((int)$filter->category));
        }
        if (is_numeric($filter->proj_manager_id)) {
            $query->where($db->quoteName("p.manager_id")." = ".$db->quote((int)$filter->proj_manager_id));
        }
        if (is_numeric($filter->customer)) {
            $query->where($db->quoteName("p.customer_id")." = ".$db->quote((int)$filter->customer));
        }
        if (is_numeric($filter->department)) {
            $query->where($db->quoteName("p.department_id")." = ".$db->quote((int)$filter->department));
        }
        if (is_numeric($filter->category)) {
            $query->where($db->quoteName("p.parent_id")." = ".$db->quote((int)$filter->category));
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
        $db = JFactory::getDBO();
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
}