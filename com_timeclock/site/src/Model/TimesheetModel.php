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
namespace HUGLLC\Component\Timeclock\Site\Model;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use HUGLLC\Component\Timeclock\Site\Model\DefaultModel;
use HUGLLC\Component\Timeclock\Administrator\Helper\TimeclockHelper;
use HUGLLC\Component\Timeclock\Site\Helper\DateHelper;
use HUGLLC\Component\Timeclock\Site\Trait\PayperiodTrait;
use Joomla\CMS\Router\Route;

\defined( '_JEXEC' ) or die();


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
class TimesheetModel extends DefaultModel
{    
    use PayperiodTrait;

    /** This is where we cache our projects */
    private $_projects = null;
    /** This is our percentage of holiday pay */
    private $_user_id = null;
    /** This counts paid and unpaid projects */
    private $_counts = array(
        "paid" => 0,
        "unpaid" => 0
    );
    
    /**
    * The constructor
    */
    public function __construct()
    {
        // Set the user
        $app      = Factory::getApplication();
        $pk = $app->input->get('id', null, "int");
        $this->_user_id = empty($pk) ? $this->getUser()->id : $pk;
        parent::__construct(); 
    }

    /**
    * Gets the user and returns the timeclock params
    *
    * @param int $id The id of the user to get
    * 
    * @return array An array of results.
    */
    public function &getUser($id = null)
    {
        $id = is_null($id) ? $this->_user_id : $id;
        return parent::getUser($id);
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @return array An array of results.
    */
    public function complete()
    {
        $start = $this->getState("payperiod.start");
        TimeclockHelper::setUserParam("timesheetDone", $start, $this->_user_id);
        $set = TimeclockHelper::getUserParam("timesheetDone", $this->_user_id);
        if ($start == $set) {
            $this->logComplete();
            return $set;
        }
        $this->logComplete(false, "Save Failed");
        return false;
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @return array An array of results.
    */
    public function approve()
    {
        $start = $this->getState("payperiod.start");
        TimeclockHelper::setUserParam("timesheetApproved", $start, $this->_user_id);
        $set = TimeclockHelper::getUserParam("timesheetApproved", $this->_user_id);
        if ($start == $set) {
            $this->logApprove();
            return $set;
        }
        $this->logApprove(false, "Save Failed");
        return false;
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @return array An array of results.
    */
    public function disapprove()
    {
        $prev = $this->getState("payperiod.prev");
        TimeclockHelper::setUserParam("timesheetApproved", $prev, $this->_user_id);
        $set = TimeclockHelper::getUserParam("timesheetApproved", $this->_user_id);
        if ($prev == $set) {
            $this->logDisapprove();
            return $set;
        }
        $this->logDisapprove(false, "Save Failed");
        return false;
    }
    /**
     * Logs the complete time sheet
     * 
     * @param bool   $success True if successful, false otherwise
     * @param string $msg     A message to attach
     * 
     * @return Void
     */
    public function logComplete($success = true, $msg = "")
    {
        $message = $this->logMessage('complete', $success, $msg);
        TimeclockHelper::addActionLog([$message], 'COM_TIMECLOCK_ACTION_LOG_COMPLETE', 'complete');

    }

    /**
     * Logs the complete time sheet
     * 
     * @param bool   $success True if successful, false otherwise
     * @param string $msg     A message to attach
     * 
     * @return Void
     */
    public function logApprove($success = true, $msg = "")
    {
        $message = $this->logMessage('approve', $success, $msg);
        TimeclockHelper::addActionLog([$message], 'COM_TIMECLOCK_ACTION_LOG_APPROVE', 'approve');

    }
    /**
     * Logs the complete time sheet
     * 
     * @param bool   $success True if successful, false otherwise
     * @param string $msg     A message to attach
     * 
     * @return Void
     */
    public function logDisapprove($success = true, $msg = "")
    {
        $message = $this->logMessage('disapprove', $success, $msg);
        TimeclockHelper::addActionLog([$message], 'COM_TIMECLOCK_ACTION_LOG_DISAPPROVE', 'disapprove');

    }
    /**
     * Logs the complete time sheet
     * 
     * @param bool   $success True if successful, false otherwise
     * @param string $msg     A message to attach
     * 
     * @return Void
     */
    public function logAccess()
    {
        $message = $this->logMessage('access');
        TimeclockHelper::addActionLog([$message], 'COM_TIMECLOCK_ACTION_LOG_ACCESS', 'access');

    }
    /**
     * Logs the complete time sheet
     * 
     * @param string $action  The action we are taking
     * @param bool   $success True if successful, false otherwise
     * @param string $msg     A message to attach
     * 
     * @return Void
     */
    protected function logMessage($action, $success = true, $msg = "")
    {
        $user_id = Factory::getApplication()->getInput()->getInt("id", NULL);
        $by = Factory::getUser();
        $for = Factory::getUser($user_id);
        $start = $this->getState('payperiod.start');
        $message = [
            'action'      => $action,
            'status'      => $success ? 'success' : 'failure',
            'message'     => $msg,
            'forid'       => $for->id,
            'forname'     => $for->username,
            'forlink'     => 'index.php?option=com_users&task=user.edit&id=' . $for->id,
            'userid'      => $by->id,
            'username'    => $by->username,
            'userlink' => 'index.php?option=com_users&task=user.edit&id=' . $by->id,
            'timesheetlink' => Route::link('site', 'index.php?option=com_timeclock&view=timesheet&date='.$start.'&id=' . $for->id),
            'payrolllink' => Route::link('site', 'index.php?option=com_timeclock&view=payroll&date='.$start),
            'payperiodstart' => $start,
        ];
        return $message;
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
        if (($entry->project_type == "HOLIDAY") || ($entry->project_type == "FLOATING_HOLIDAY")) {
            $user = $this->getUser();
            $holiday_perc = TimeclockHelper::getHolidayPerc($user->id, $entry->worked);
            $entry->hours = $entry->hours * $holiday_perc;
        }
        $entry->cat_name = Text::_($entry->cat_name);
        $entry->cat_description = Text::_($entry->cat_description);
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
                if ($entry->type == "UNPAID") {
                    $this->_counts['unpaid']++;
                } else {
                    $this->_counts['paid']++;
                }

            }
        }
        return $this->_projects;
    }
    /**
    * Returns the counts
    *
    * @return array An array of counts.
    */
    public function counts()
    {
        return $this->_counts;
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
        $entry->nonewhours = 1;
        $max       = (int)$entry->max_yearly_hours;
        $hours_ytd = (int)$entry->hours_ytd;
        if (($max != 0) && ($max <= $hours_ytd)) {
            $entry->nonewhours = 0;
        } else {
            if (($entry->type == "PTO") || ($entry->type == "PROJECT") || ($entry->type == "UNPAID") || ($entry->type == "FLOATING_HOLIDAY") || ($entry->type == "VOLUNTEER")) {
                $entry->nohours = 0;
                $entry->nonewhours = 0;
            }
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

        $app = Factory::getApplication();

        // Load state from the request.
        $pk = $app->input->get('id', null, "int");
        $this->setState('id', $pk);

        
        // Load state from the request.
        $project_id = $app->input->get('project_id', null, "int");
        $this->setState('project_id', $project_id);

        // Load the parameters.
        $params = ComponentHelper::getParams('com_timeclock');
        $this->setState('params', $params);
        $user = $this->getUser();
        
        if ((!$user->authorise('core.edit.state', 'com_timeclock')) 
            &&  (!$user->authorise('core.edit', 'com_timeclock'))
        ) {
                $this->setState('filter.published', 1);
                $this->setState('filter.archived', 2);
        }

        $estart = isset($user->timeclock["startDate"]) ? $user->timeclock["startDate"] : 0;
        $estart = empty($estart) ? 0 : DateHelper::fixDate($estart);
        $this->setState('employment.start', $estart);
        $eend = isset($user->timeclock["endDate"]) ? $user->timeclock["endDate"] : 0;
        $eend = empty($eend) ? 0 : DateHelper::fixDate($eend);
        $this->setState('employment.end', $eend);
        $date = DateHelper::fixDate(
            $app->input->get('date', date("Y-m-d"), "raw")
        );
        $date = empty($date) ?  date("Y-m-d") : $date;
        $this->setState('date', $date);
        
        $this->populatePayperiodState($date, $estart, $eend);
    }
    /**
     * Returns true if this is marked complete
     * 
     * @return True if complete, false otherwise
     */
    public function isComplete()
    {
        return $this->getState("payperiod.done");
    }
    /**
     * Returns true if this is marked complete
     * 
     * @return True if complete, false otherwise
     */
    public function isApproved()
    {
        return $this->getState("payperiod.approved");
    }
    /**
    * This function gets the dates of the period, and says wheter or not time can 
    * be added to it
    *
    * @param int    $id    The user id of the timesheet to get
    * @param string $start The first day of employment
    * @param string $end   The last day of employment
    * @param bool   $paid  Only paid records?
    *
    * @return The total hours for this time period
    */
    public function periodTotal(
        $id = null, $start = null, $end = null, $paid = true
    ) {
        $id    = empty($id) ? $this->_user_id : $id;
        $start = empty($start) ? $this->getState('start') : $start;
        $end   = empty($end) ? $this->getState('end') : $end;
        $query = $this->_buildQuery($id);
        $this->_periodWhere($query, $start, $end);
        $this->_userWhere($query, $id);

        $list = $this->_getList($query);
        $this->listProjects();
        $return = 0.0;
        foreach ($list as $row) {
            if ($paid && ($row->project_type == "UNPAID")) {
                continue;
            }
            $this->checkTimesheet($row);
            $return += $row->hours;
        }
        return (float)$return;
    }
    /**
    * This function gets the dates of the period, and says wheter or not time can 
    * be added to it
    *
    * @param int    $id    The user id of the timesheet to get
    * @param string $start The first day of employment
    * @param string $end   The last day of employment
    *
    * @return The total hours for this time period
    */
    public function ptoTotal(
        $id = null, $start = null, $end = null
    ) {
        $id    = empty($id) ? $this->_user_id : $id;
        $start = empty($start) ? $this->getState('start') : $start;
        $end   = empty($end) ? $this->getState('end') : $end;
        $query = $this->_buildQuery($id);
        $this->_periodWhere($query, $start, $end);
        $this->_userWhere($query, $id);
        $db = Factory::getDBO();
        $query->where($db->quoteName("p.type")." = ".$db->quote("PTO"));
        
        $list = $this->_getList($query);
        $this->listProjects();
        $return = 0.0;
        foreach ($list as $row) {
            $this->checkTimesheet($row);
            $return += $row->hours;
        }
        return (float)$return;
    }
    /**
    * Builds the query to be used by the model
    *
    * @param int $id The user id of the timesheet to get
    *
    * @return object Query object
    */
    protected function _buildQuery($id = null)
    {
        $id = empty($id) ? $this->_user_id : $id;
        $db = Factory::getDBO();
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
            (z.user_id = '.$db->quote((int)$id).' AND t.project_id = z.project_id)');
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

        $this->_periodWhere($query);
        $this->_userWhere($query);
        
        return $query;
    }
    /**
    * This function gets the dates of the period, and says wheter or not time can 
    * be added to it
    *
    * @param object $query The query to build on
    * @param string $start The first day
    * @param string $end   The last day
    *
    * @return The total hours for this time period
    */
    private function _periodWhere($query, $start = null, $end = null)
    {
        $db = Factory::getDBO();
        $start = empty($start) ? $this->getState("payperiod.start") : $start;
        $end   = empty($end)   ? $this->getState("payperiod.end")   : $end;

        $query->where($db->quoteName("t.worked").">=".$db->quote($start));
        $query->where($db->quoteName("t.worked")."<=".$db->quote($end));

    }
    /**
    * This function gets the dates of the period, and says wheter or not time can 
    * be added to it
    *
    * @param object $query The query to build on
    * @param int    $id    The ID to use
    *
    * @return The total hours for this time period
    */
    private function _userWhere($query, $id = null)
    {
        $db = Factory::getDBO();
        $user = $this->getUser($id);
        $query->where(
            "((".$db->quoteName("t.user_id")."=".$db->quote($user->id)." AND "
            .$db->quoteName("p.type")."<>'HOLIDAY') OR ("
            .$db->quoteName("z.user_id")."=".$db->quote($user->id)." AND "
            .$db->quoteName("p.type")."='HOLIDAY'))"
        );
        $start = isset($user->timeclock["startDate"]) ? $user->timeclock["startDate"] : 0;
        $query->where($db->quoteName("t.worked").">=".$db->quote($start));
        $end = isset($user->timeclock["endDate"]) ? $user->timeclock["endDate"] : 0;
        if (!empty($end) && ($end != "0000-00-00 00:00:00")) {
            $query->where($db->quoteName("t.worked")."<=".$db->quote($end));
        }

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

        $y     = (int)explode("-", $this->getState("date"))[0];
        $year  = "(".$db->quoteName("worked")." >= ".$db->quote($y."-01-01")." AND ".$db->quoteName("worked")." < ".$db->quote(($y + 1)."-01-01").")";

        $query->select('q.project_id as project_id, q.user_id as user_id');
        $query->select('(SELECT SUM(hours1 + hours2 + hours3 + hours4 + hours5 + hours6) from #__timeclock_timesheet where project_id = q.project_id and user_id='.$db->quote($this->_user_id).' AND '.$year.') as hours_ytd');
        $query->from('#__timeclock_users as q');
        $query->select('p.project_id as project_id, 1 as mine, 
            p.name as name, p.parent_id as parent_id, p.description as description,
            p.type as type, p.max_yearly_hours as max_yearly_hours');
        $query->leftjoin('#__timeclock_projects as p on q.project_id = p.project_id');
        $query->select('r.name as parent_name, r.description as parent_description');
        $query->leftjoin('#__timeclock_projects as r on p.parent_id = r.project_id');
        $query->where('q.user_id = '.$db->quote($this->_user_id));
        $query->where('q.project_id > 0');
        $query->where('p.published = 1');
        $query->order("p.name asc");
        return $query;
    }
}
