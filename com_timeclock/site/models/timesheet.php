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
class TimeclockModelsTimesheet extends TimeclockModelsSiteDefault
{    
    /** This is where we cache our projects */
    private $_projects = null;
    /** This is our percentage of holiday pay */
    private $_holiday_perc = 1;
    
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
        $query = $this->_buildQuery();
        $query = $this->_buildWhere($query);
        $list = $this->_getList($query);
        $this->listProjects();
        $return = array();
        foreach ($list as $row) {
            $proj_id = (int)$row->project_id;
            $cat_id  = (int)$row->cat_id;
            $return[$proj_id] = isset($return[$proj_id]) ? $return[$proj_id] : array();
            $this->checkTimesheet($row);
            $this->checkTimesheetProject($row);
            $return[$proj_id][$row->worked] = $row;
        }
        return $return;
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
            $entry->hours = $entry->hours * $this->_holiday_perc;
        }
        $entry->cat_name = JText::_($entry->cat_name);
        $entry->cat_description = JText::_($entry->cat_description);
    }
    /**
    * Checks to make sure this project exists
    *
    * @param object &$row The row to check
    * 
    * @return array An array of results.
    */
    protected function checkTimesheetProject(&$row)
    {
        $proj_id = (int)$row->project_id;
        $cat_id  = (int)$row->cat_id;
        $projs = &$this->_projects;
        // This adds in projects and categories that the user has time in,
        // but isn't currently a member.
        if (!isset($projs[$cat_id])) {
            $projs[$cat_id] = array(
                "project_id" => $cat_id,
                "name" => $row->cat_name,
                "description" => $row->cat_description,
                "proj" => array()
            );
        }
        if (!isset($projs[$cat_id]["proj"][$proj_id])) {
            $projs[$cat_id]["proj"][$proj_id] = (object)array(
                "project_id" => $proj_id,
                "name" => $row->project,
                "description" => $row->project_description,
                "type" => $row->project_type,
                "nohours" => 1,
                "mine" => 0,
            );
        }
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

        $estart = TimeclockHelpersTimeclock::getUserParam("startDate");
        $estart = empty($estart) ? 0 : TimeclockHelpersDate::fixDate($estart);
        $registry->set('employment.start', $estart);
        $eend = TimeclockHelpersTimeclock::getUserParam("endDate");
        $eend = empty($eend) ? 0 : TimeclockHelpersDate::fixDate($eend);
        $registry->set('employment.end', $eend);
        $date = TimeclockHelpersDate::fixDate(
            $app->input->get('date', date("Y-m-d"), "raw")
        );
        $date = empty($date) ?  date("Y-m-d") : $date;
        $registry->set('date', $date);
        
        // Get the pay period Dates
        $startTime = TimeclockHelpersTimeclock::getParam("firstViewPeriodStart");
        $len = TimeclockHelpersTimeclock::getParam("viewPeriodLength");
        $start = TimeclockHelpersDate::fixedPayPeriodStart($startTime, $date, $len);
        $registry->set("payperiod.days", $len);
        $registry->set("payperiod.start", $start);
        $s = TimeclockHelpersDate::explodeDate($start);
        $end = TimeclockHelpersDate::dateUnix(
            $s["m"], $s["d"]+$len-1, $s["y"]
        );
        $registry->set("payperiod.end", date("Y-m-d", $end));
        $next = TimeclockHelpersDate::dateUnix(
            $s["m"], $s["d"]+$len, $s["y"]
        );
        $registry->set("payperiod.next", date("Y-m-d", $next));
        $prev = TimeclockHelpersDate::dateUnix(
            $s["m"], $s["d"]-$len, $s["y"]
        );
        $registry->set("payperiod.prev", date("Y-m-d", $prev));
        
        $cutoff = TimeclockHelpersTimeclock::getParam("payperiodCutoff");
        $registry->set("payperiod.cutoff", $cutoff);

        $dates = array_flip(TimeclockHelpersDate::payPeriodDates($start, $end));
        foreach ($dates as $date => &$value) {
            $here = TimeclockHelpersDate::checkEmploymentDates($estart, $eend, $date);
            $valid = (TimeclockHelpersDate::compareDates($date, $cutoff)  >= 0);
            $value = $here && $valid;
        }
        $registry->set("payperiod.dates", $dates);
        
        $this->_holiday_perc = ((int)TimeclockHelpersTimeclock::getUserParam("holidayperc", $user->id, $date)) / 100;
        $registry->set("holiday.perc", $this->_holiday_perc);

        $split = (int)TimeclockHelpersTimeclock::getParam("payPeriodSplitDays");
        $split = empty($split) ? 7 : $split;
        $registry->set("payperiod.splitdays", $split);

        $subtotals = (int)($len / $split);
        $registry->set("payperiod.subtotals", $subtotals);


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