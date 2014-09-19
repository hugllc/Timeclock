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
    private $_holiday_perc = 1;
    /** This is our report ID */
    private $_report_id = null;
    
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
    public function listItems()
    {
        return array();
    }
    /**
    * Checks to see if there is a saved report and returns the ID
    * 
    * @param string $type  The type of report to look for
    * @param string $start The start date for the report
    * @param string $end   The end date for the report
    *
    * @return int The ID of the report
    */
    private function _getReportID($type = null, $start = null, $end = null)
    {
        if (is_null($this->_report_id)) {
            $db = JFactory::getDBO();
            $query = $db->getQuery(TRUE);
            $query->select('report_id');
            $query->from('#__timeclock_reports');
            $query->where($db->quoteName("type")." = ".$db->quote($type));
            $query->where($db->quoteName("startDate")." = ".$db->quote($start));
            $query->where($db->quoteName("endDate")." = ".$db->quote($end));
            $db->setQuery($query);
            $this->_report_id = (int)$db->loadResult();
        }
        return $this->_report_id;
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
            $query = $this->_buildProjQUery();
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
    public function listUsers()
    {
        if (is_null($this->_users)) {
            $this->_users = TimeclockHelpersTimeclock::getUsers(0);
        }
        return $this->_users;
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

        $row->name        = $app->input->get("name", "");
        $row->description = $app->input->get("description", "");
        $row->created_by  = JFactory::getUser()->id;
        $row->created     = $date;
        $row->startDate   = $this->getState("start");
        $row->endDate     = $this->getState("end");
        $row->type        = $this->getState("type");
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
        $row->report_id = $this->getState("report_id");

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
    * @param object $registry Ignored in subclasses
    * 
    * @return  void
    *
    * @note    Calling getState in this method will result in recursion.
    * @since   12.2
    */
    protected function populateState($registry = null)
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

        $report_id = $this->_getReportID(
            $registry->get("type"), 
            $registry->get("start"), 
            $registry->get("end")
        );
        $registry->set("report_id", $report_id);
        


        $this->setState($registry);
    }
    /**
    * This function gets the dates of the period, and says wheter or not time can 
    * be added to it
    *
    * @param string $start  The first day of employment
    * @param string $end    The last day of employment
    * @param string $cutoff The last day locked down so time can't be entered
    *
    * @return array, keys are the dates, values are true if time can be added
    */
    private function _getPayPeriodDates($start, $end, $cutoff)
    {
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
        $query->select('DISTINCT t.timesheet_id,
            (t.hours1 + t.hours2 + t.hours3 + t.hours4 + t.hours5 + t.hours6)
            as hours, t.worked, t.project_id, t.notes, t.user_id as user_id');
        $query->from('#__timeclock_timesheet as t');
        $query->select('p.name as project, p.type as project_type, 
            p.description as project_description, p.parent_id as cat_id');
        $query->leftjoin('#__timeclock_projects as p on t.project_id = p.project_id');
        $query->select('q.name as cat_name, q.description as cat_description');
        $query->leftjoin('#__timeclock_projects as q on p.parent_id = q.project_id');
        $query->select('u.name as user');
        $query->leftjoin('#__users as u on t.user_id = u.id');
        $query->select('v.name as author');
        $query->leftjoin('#__users as v on t.created_by = v.id');
        $query->leftjoin('#__timeclock_users as z on 
            (z.user_id = '.$db->quote($this->_user->id).' AND t.project_id = z.project_id)');
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
        
        $query->where(
            "((".$db->quoteName("t.user_id")."=".$db->quote($this->_user->id)." AND "
            .$db->quoteName("p.type")."<>'HOLIDAY') OR ("
            .$db->quoteName("z.user_id")."=".$db->quote($this->_user->id)." AND "
            .$db->quoteName("p.type")."='HOLIDAY'))"
        );
        $start = $this->getState("employment.start");
        $query->where($db->quoteName("t.worked").">=".$db->quote($start));
        $end = $this->getState("employment.end");
        if (!empty($end)) {
            $query->where($db->quoteName("t.worked")."<=".$db->quote($end));
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
        $query->select('q.project_id as project_id, q.user_id as user_id');
        $query->from('#__timeclock_users as q');
        $query->select('p.project_id as project_id, 1 as mine, 
            p.name as name, p.parent_id as parent_id, p.description as description,
            p.type as type');
        $query->leftjoin('#__timeclock_projects as p on q.project_id = p.project_id');
        $query->select('r.name as parent_name, r.description as parent_description');
        $query->leftjoin('#__timeclock_projects as r on p.parent_id = r.project_id');
        $query->where('q.user_id = '.$db->quote($this->_user->id));
        $query->where('q.project_id > 0');
        $query->order("p.name asc");
        return $query;
    }
}