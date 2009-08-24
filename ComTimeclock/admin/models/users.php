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

$base      = dirname(JApplicationHelper::getPath("front", "com_timeclock"));
$adminbase = dirname(JApplicationHelper::getPath("admin", "com_timeclock"));

require_once $base.DS.'models'.DS.'timeclock.php';

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
class TimeclockAdminModelUsers extends JModel
{
    /** The ID to load */
    private $_id = -1;
    /** Query to get all records */
    private $_allQuery = "SELECT p.*, u.*
                      FROM #__users AS u
                      LEFT JOIN #__timeclock_prefs as p ON u.id = p.id ";

    /**
     * Constructor that retrieves the ID from the request
     *
     * @return    void
     */
    function __construct()
    {
        parent::__construct();

        $array = JRequest::getVar('cid', 0, '', 'array');
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
        $row = $this->getTable("TimeclockPrefs");
        $id = is_int($this->_id) ? $this->_id : $this->_id[0];
        $row->load($id);
        return $row;
    }

    /**
     * Method to display the view
     *
     * @param string $where      The where clause to use (must include "WHERE")
     * @param int    $limitstart The record to start on
     * @param int    $limit      The max number of records to retrieve
     * @param string $orderby    The orderby clause to use (must include "ORDER BY")
     *
     * @return string
     */
    function getUsers($where = "", $limitstart=null, $limit=null, $orderby = "")
    {
        $key = (string)$limitstart.$limit;
        $query = $this->_allQuery." "
                 .$where." "
                 .$orderby;
        $ret = $this->_getList($query, $limitstart, $limit);
        if (!is_array($ret)) {
            return $ret;
        }
        foreach ($ret as $key => $val) {
            $ret[$key]->prefs = TableTimeclockPrefs::decode($val->prefs);
        }
        return $ret;
    }

    /**
     * Method to display the view
     *
     * @param string $where The where clause to use.  Must include 'WHERE'
     *
     * @return string
     */
    function countUsers($where="")
    {
        $query = $this->_allQuery." ".$where;
        return $this->_getListCount($query);
    }

    /**
     * Publishes or unpublishes an item
     *
     * @param int $publish 1 to publish, 0 to unpublish
     *
     * @return bool
     */
    function publish($publish)
    {
        $user =& JFactory::getUser();
        $user_id = $user->get("id");
        $table = $this->getTable("TimeclockPrefs");
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
        $table = $this->getTable("TimeclockPrefs");
        return $table->checkin($oid);
    }

    /**
     * Checks in an item
     *
     * @param array $id      Projects to add
     * @param int   $user_id The user id to add the projects to
     *
     * @return bool
     */
    function addproject($id = array(), $user_id = 0)
    {
        $row = $this->getTable("TimeclockUsers");

        $this->store();

        if (!is_array($id)) {
            $id = array($id);
        }
        $ret = true;
        foreach ($id as $p) {
            $data = array(
                "id" =>(int) $p,
                "user_id" => (int) $user_id,
            );

            if (!$row->bind($data)) {
                $this->setError($this->_db->getErrorMsg());
                $ret = false;
                continue;
            }
            // Make sure the record is valid
            if (!$row->check()) {
                $this->setError($this->_db->getErrorMsg());
                $ret = false;
                continue;
            }
            // Store the web link table to the database
            if (!$row->store()) {
                $this->setError($this->_db->getErrorMsg());
                $ret = false;
                continue;
            }
        }
        return $ret;
    }

    /**
     * Checks in an item
     *
     * @param array $projid  Projects to add
     * @param int   $user_id The user id to add the projects to
     *
     * @return bool
     */
    function removeproject($projid, $user_id)
    {
        $this->store();

        $row = $this->getTable("TimeclockUsers");

        $ret = true;
        foreach ($projid as $p) {
            $data = array(
                "id" => (int)$p,
                "user_id" => (int)$user_id,
            );
            // Bind the form fields to the hello table
            if (!$row->bind($data)) {
                $this->setError($this->_db->getErrorMsg());
                $ret = false;
            }
            // Store the web link table to the database
            if (!$row->delete()) {
                $this->setError($this->_db->getErrorMsg());
                $ret = false;
            }
        }
        return $ret;
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
        $table = $this->getTable("TimeclockPrefs");
        return $table->checkout($who, $oid);
    }


    /**
     * Method to store a record
     *
     * @access    public
     * @return    boolean    True on success
     */
    function store()
    {
        $row =& $this->getTable("TimeclockPrefs");
        $data = JRequest::get('post');
        if (empty($data["id"])) {
            return false;
        }
        // Load the old data
        $row->load($data["id"]);
        $prefs = array(
            "prefs" => $row->prefs,
            "id" => $data["id"],
            "startDate" => $row->startDate,
            "endDate" => $row->endDate,
            "manager" => $row->manager,
            "published" => $row->published,
            "history" => $row->history,
        );
        $this->_fixPrefs($prefs, $data);
        $this->_loadData($prefs, $data);

        // Bind the form fields to the hello table
        if (!$row->bind($prefs)) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        // Make sure the record is valid
        if (!$row->check()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // Store the web link table to the database
        if (!$row->store()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        return true;
    }
    /**
     * Fixes any inconsistancies in the prefs
     *
     * @param array &$prefs The prefs to check/fix
     * @param array &$data  The new data
     *
     * @return null
     */
    private function _fixPrefs(&$prefs, &$data)
    {
        if ($data["admin_status"] != "PARTTIME") {
            $data["admin_holidayperc"] = TableTimeclockPrefs::getDefaultPref(
                "admin_holidayperc",
                "user"
            );
        }
    }


    /**
     * Loads incoming data into the prefs array
     *
     * @param array &$prefs The prefs to check/fix
     * @param array &$data  The new data
     *
     * @return null
     */
    private function _loadData(&$prefs, &$data)
    {
        // Load the new data
        $timestamp = date("Y-m-d H:i:s");
        $user =& JFactory::getUser();
        $id = $user->get("name");
        foreach ($data as $f => $v) {
            if (substr($f, 0, 6) == "admin_") {
                if ($v != $prefs["prefs"][$f]) {
                    $prefs["history"][$f][$timestamp] = $prefs["prefs"][$f];
                    $prefs["history"]["timestamps"][$timestamp] = $id;
                }
                $prefs["prefs"][$f] = $v;
            }
        }
        foreach (array("published", "startDate", "endDate", "manager") as $key) {
            if ($data[$key] != $prefs[$key]) {
                $prefs["history"][$key][$timestamp] = $prefs[$key];
                $prefs["history"]["timestamps"][$timestamp] = $id;
            }
            $prefs[$key] = $data[$key];
        }
    }


    /**
     * Get projects for a user
     *
     * @param int $oid        User id
     * @param int $limitstart The record to start on
     * @param int $limit      The max number of records to retrieve
     *
     * @return array
     */
    function getUserProjects($oid, $limitstart = null, $limit = null)
    {
        $query = "select * from #__timeclock_users as u
                  LEFT JOIN #__timeclock_projects as p on u.id = p.id
                  WHERE u.user_id = ".(int)$oid."
                     AND p.published = 1
                     AND p.Type <> 'CATEGORY'
                  ORDER BY p.id asc
                  ";
        $ret = $this->_getList($query, $limitstart, $limit);
        if (!is_array($ret)) {
            return array();
        }
        return $ret;
    }


    /**
     * Get projects for a user
     *
     * @param int $oid        User id
     * @param int $limitstart The record to start on
     * @param int $limit      The max number of records to retrieve
     *
     * @return array
     */
    function getUserProjectIds($oid, $limitstart = null, $limit = null)
    {
        $projects = $this->getUserProjects($oid, $limitstart, $limit);
        foreach ($projects as $p) {
            $proj[] = $p->id;
        }
        return $proj;
    }
    /**
     * Gets select options for parent projects
     *
     * @param string $where  The where clause to use.  Must include 'WHERE'
     * @param string $text   The text of the first entry
     * @param array  $ignore Array of user id's to not show
     *
     * @return array
     */
    function getOptions($where, $text = "None", $ignore = array())
    {
        if (!is_null($text)) {
            $ret = array(JHTML::_("select.option", 0, $text));
        } else {
            $ret = array();
        }
        $query = "SELECT u.id, u.name FROM #__users AS u
                  LEFT JOIN #__timeclock_prefs AS p ON u.id = p.id "
                  .$where." ORDER BY u.id asc";
        $list = self::_getList($query);
        if (!is_array($list)) {
            return $ret;
        }
        foreach ($list as $val) {
            if (array_search($val->id, $ignore) !== false) {
                continue;
            }
            $ret[] = JHTML::_("select.option", $val->id, $val->name);
        }
        return $ret;
    }

    /**
     * Gets select options for parent projects
     *
     * @param int    $oid  The user to get the PTO for.
     * @param string $date The date to check
     *
     * @return array
     */
    function getPTO($oid, $date=null)
    {
        return $this->_getPTO($oid, $date) + $this->_getPTOCarryOver($oid, $date);
    }
    /**
     * Gets select options for parent projects
     *
     * @param int    $oid  The user to get the PTO for.
     * @param string $date The date to check
     *
     * @return array
     */
    function _getPTO($oid, $date=null)
    {
        if (empty($date)) {
            $date = date("Y-m-d");
        }
        $period = TableTimeclockPrefs::getPref("ptoAccrualPeriod", "system");
        $wait = TableTimeclockPrefs::getPref("ptoAccrualWait", "system");
        $dailyHours = (int)TableTimeclockPrefs::getPref("ptoHoursPerDay", "system");
        $ret = 0;
        $service = self::getServiceLength($oid, $date);
        if (($wait/365.25) > $service) {
            return 0;
        }
        switch($period) {
        case "week":
            $ret = self::_getPTOWeek($oid, $date);
            break;
        case "month":
            $ret = self::_getPTOMonth($oid, $date);
            break;
        case "year":
            $ret = self::_getPTOYear($oid, $date);
            break;
        }
        var_dump($ret * $dailyHours);
        return $ret * $dailyHours;
    }

    /**
     * Gets select options for parent projects
     *
     * @param int    $oid  The user to get the PTO for.
     * @param string $date The date to check
     *
     * @return array
     */
    function _getPTOCarryOver($oid, $date=null)
    {
        if (empty($date)) {
            $date = date("Y-m-d");
        }
        $co = TableTimeclockPrefs::getPref("admin_ptoCarryOver", "user", $oid);
        $coe = TableTimeclockPrefs::getPref("admin_ptoCarryOverExpire", "user", $oid);
        $year = date("Y", strtotime($date." 06:00:00"));
        if (!isset($co[$year])) {
            return 0;
        }
        if (TimeclockModelTimeclock::compareDates($coe[$year], $date) > 0) {
            return (int) $co[$year];
        }
        $pto = (int)TimeclockModelTimeclock::getTotal(
            " `p`.`type` = 'PTO' AND `t`.`worked` >= '".date("Y-01-01")."'
            AND `t`.`worked` <= '".$coe[$year]."'",
            $oid
        );
        if (($pto - $co[$year]) < 0) {
            return $pto;
        } else {
            return $co[$year];
        }
    }


    /**
    * Gets select options for parent projects
    *
    * @param int    $oid  The user to get the PTO for.
    * @param string $date The date to check
    *
    * @return array
    */
    function _getPTOWeek($oid, $date=null)
    {
        $accTime = (int)TableTimeclockPrefs::getPref("ptoAccrualTime", "system");
        $date = strtotime($date);
        $weeks = round(date("z", $date)/7, 0);
        $weeks += $accTime;
        $hours = 0;
        for ($i = 0; $i < $weeks; $i++) {
            $time = mktime(6, 0, 0, 1, 1+($i * 7), date("Y", $date));
            $hours += self::getPTOAccrualRate($oid, $time) / 52;
        }
        return $hours;
    }
    /**
    * Gets select options for parent projects
    *
    * @param int    $oid  The user to get the PTO for.
    * @param string $date The date to check
    *
    * @return array
    */
    function _getPTOMonth($oid, $date=null)
    {
        $accTime = (int)TableTimeclockPrefs::getPref("ptoAccrualTime", "system");
        $date = strtotime($date);
        $months = date("m", $date);
        $months += $accTime;
        $hours = 0;
        for ($i = 1; $i <= $months; $i++) {
            $time = mktime(6, 0, 0, $i, 1, date("Y", $date));
            $hours += self::getPTOAccrualRate($oid, $time) / 12;
        }
        return $hours;
    }

    /**
    * Gets select options for parent projects
    *
    * @param int    $oid  The user to get the PTO for.
    * @param string $date The date to check
    *
    * @return array
    */
    function _getPTOYear($oid, $date=null)
    {
        $date = strtotime($date);
        $time = mktime(6, 0, 0, 1, 1, date("Y", $date));
        $hours += self::getPTOAccrualRate($oid, $time);

        return $hours;
    }

    /**
     * Gets the perc of holiday pay this user should get
     *
     * @param int    $oid  The user id to check
     * @param string $date The date to check
     *
     * @return int
     */
    function getPTOAccrualRate($oid, $date)
    {
        static $rate;
        $key = $oid.$date;
        $rates = TableTimeclockPrefs::getPref("ptoAccrualRates", "system");
        $service = self::getServiceLength($oid, $date);
        $status = self::getStatus($oid, $date);

        if ($service == 0) {
            return 0;
        }
        if (!is_array($rates[$status])) {
            return 0;
        }
        foreach ($rates[$status] as $s => $r) {
            if ($service < $s) {
                return $r;
            }
        }
        return $r;
    }

    /**
     * Gets the perc of holiday pay this user should get
     *
     * @param int    $id   The user id to check
     * @param string $date The date to check
     *
     * @return int
     */
    function getStatus($id, $date)
    {
        static $status;
        $key = $id.$date;
        if (!isset($status[$key])) {
            $hist = TableTimeclockPrefs::getPref("history", "user", $id);
            if (is_array($hist["admin_status"])) {
                ksort($hist["admin_status"]);
                foreach ($hist["admin_status"] as $d => $r) {
                    if (TimeclockModelTimeclock::compareDates(date("Y-m-d", $date), $d) < 0) {
                        $status[$key] = $r;
                        break;
                    }
                }
            }
            if (!isset($status[$key])) {
                $status[$key] = TableTimeclockPrefs::getPref(
                    "admin_status",
                    "user",
                    $id
                );
            }
        }
        return $status[$key];
    }

    /**
     * Gets select options for parent projects
     *
     * @param int    $oid  The user to get the PTO for.
     * @param string $date The date to check
     *
     * @return array
     */
    function getServiceLength($oid, $date=null)
    {
        if (empty($date)) {
            $date = time();
        } else if (!is_numeric($date)) {
            $date = strtotime($date);
        }
        $start = TableTimeclockPrefs::getPref("startDate", "user", $oid);
        $start = strtotime($start);
        if ($date < $start) {
            return 0;
        }
        $length = $date - $start;
        return $length / (60*60*24*365.25);
    }

}

?>
