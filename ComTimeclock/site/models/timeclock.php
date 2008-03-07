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
class TimeclockModelTimeclock extends JModel
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
     * Method to display the view
     *
     * @return string
     */
    function getData()
    {
    
            
    
        $query = "SELECT SUM(t.hours) as hours, t.worked, t.project_id, t.notes
                  FROM #__timeclock_timesheet as t
                  LEFT JOIN #__timeclock_projects as p on t.project_id = p.id
                  WHERE ".$this->employmentDateWhere("t.worked")
                  ." AND ".$this->periodWhere("t.worked")
                  ." AND t.created_by='".$this->_id."'
                     AND p.type <> 'HOLIDAY'
                  GROUP BY t.worked, t.project_id
                  ";
        $ret = $this->_getList($query);
        if (!is_array($ret)) return array();
        $data = array();
        foreach ($ret as $d) {
            $data[$d->project_id][$d->worked]['hours'] += $d->hours;
            $data[$d->project_id][$d->worked]['notes'] .= $d->notes;
        }
        $data = $this->getHolidayHours($data);
        return $data;
    }

    /**
     * Method to display the view
     *
     * @param array $data Data to merge with
     *
     * @return string
     */
    function getHolidayHours($data = array(), $id = null)
    {
        $id = empty($id) ? $this->_id : $id;
        $query = "SELECT SUM(t.hours) as hours, t.worked, t.project_id, t.notes
                  FROM #__timeclock_timesheet as t
                  LEFT JOIN #__timeclock_projects as p on t.project_id = p.id
                  JOIN #__timeclock_users as u on u.id = p.id
                  WHERE ".$this->employmentDateWhere("t.worked")
                  ." AND ".$this->periodWhere("t.worked")
                  ." AND p.type='HOLIDAY'
                     AND u.user_id='".$this->_id."'
                  GROUP BY t.worked, t.project_id
                  ";
        $ret = $this->_getList($query);
        $perc = TableTimeclockPrefs::getPref("admin_holidayperc", "user") / 100;
        if (!is_array($ret)) return array();
        if (!is_array($data)) $data = array();
        foreach ($ret as $d) {
            $hours = $d->hours * $perc;
            $data[$d->project_id][$d->worked]['hours'] += $hours;
            $data[$d->project_id][$d->worked]['notes'] .= $d->notes;
        }
        return $data;
    }



    /**
     * Where statement for employment dates
     *
     * @param string $field The field to use
     *
     * @return string
     */
    function employmentDateWhere($field)
    {
        $dates = self::getEmploymentDates();
        $ret = "($field >= '".$dates["start"]."'";
        
        if (($dates["end"] != '0000-00-00') && !empty($dates["end"])) $ret .= " AND $field <= '".$dates["end"]."'";

        $ret .= ")";
        return $ret;    
    }

    /**
     * Where statement for employment dates
     *
     * @return array
     */
    function getEmploymentDates()
    {
        $ret = array(
            "start" => self::_fixDate(TableTimeclockPrefs::getPref("startDate")),
            "end"   => self::_fixDate(TableTimeclockPrefs::getPref("endDate")),
        );
        return $ret;
    }

    /**
     * Where statement for employment dates
     *
     * @return array
     */
    function getEmploymentDatesUnix()
    {
        $ret = self::getEmploymentDates();
        foreach ($ret as $key => $val) {
            $ret[$key] = self::dateUnixSql($val);
        }
        return $ret;    
    }

    /**
     * Where statement for the reporting period dates
     *
     * @param string $field The field to use
     *
     * @return string
     */
    function periodWhere($field)
    {
        $this->getPeriod();
        $start = $this->_period["start"];
        $end = $this->_period["end"];
        $ret = "($field >= '$start' AND $field <= '$end')";

        return $ret;    
    }
    /**
     * Where statement for the reporting period dates
     *
     * @param string $date Date to use in MySQL format ("Y-m-d H:i:s").  If left blank
     *                      the date read from the request variables is used.
     *
     * @return array
     */ 
    function getPeriod($date=null) {
        // This should be the last one.
        return self::_getPeriodFixed($date);
    }
    /**
     * Where statement for the reporting period dates
     *
     * @return array
     */ 
    private function _getPeriodFixed($date) {
        static $periods;

        $date = self::_getDate($date);

        $start = self::_getPeriodFixedStart($date);        
        $return =& $periods[$start];
        if (!isset($return)) {
            $start = self::_explodeDate($start);
            
            $periodLength = TableTimeclockPrefs::getPref("payPeriodLengthFixed", "system");
    
            $y = $start["y"];
            $m = $start["m"];
            $d = $start["d"];

            // These are all of the dates in the pay period
            for ($i = 0; $i < $periodLength; $i++) {
                $return['dates'][self::_date($m, $d+$i, $y)] = self::dateUnix($m, $d+$i, $y);
            }

            // Get the start and end
            $return['start']        = self::_date($m, $d, $y);
            $return['end']          = self::_date($m, $d+$periodLength-1, $y);
            $return['prev']         = self::_date($m, $d-$periodLength, $y);
            $return['prevend']      = self::_date($m, $d-1, $y);
            $return['next']         = self::_date($m, $d+$periodLength, $y);
            $return['nextend']      = self::_date($m, $d+(2*$periodLength), $y);
            $return['length']       = $periodLength;
        }    
        if (is_object($this)) $this->_period = $return;
        return $return;
    }
    /**
     * Where statement for the reporting period dates
     *
     * @return array
     */ 
    private function _getPeriodFixedStart($date)
    {
        $uDate = strtotime($date." 06:00:00");
        // Get the pay period start
        $start = strtotime(TableTimeclockPrefs::getPref("firstPayPeriodStart", "system")." 06:00:00");
        // Get the length
        $len = TableTimeclockPrefs::getPref("payPeriodLengthFixed", "system");
        // In Seconds
        $lenS = $len * 86400;

        // Get the time difference in seconds
        $timeDiff = $uDate - $start;
        // Get the offset to the end of the payperiod
        $timeDiff = ($timeDiff % $lenS);


        return date("Y-m-d", $uDate - $timeDiff);
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
    public function dateUnix($m, $d, $y)
    {
        return mktime(6,0,0, $m, $d, $y);
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
    public function dateUnixSql($sqlDate)
    {
        $date = self::_explodeDate($sqlDate);

        return self::dateUnix($date["m"], $date["d"], $date["y"]);
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
    private function _explodeDate($date)
    {

        $date = self::_fixDate($date);
        $date = explode("-", $date);
        
        return array(
            "y" => $date[0],
            "m" => $date[1],
            "d" => $date[2],
        );
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
    private function _date($m, $d, $y)
    {
        return date("Y-m-d", self::dateUnix($m, $d, $y));
    }

    /**
     * Where statement for the reporting period dates
     *
     * @param string $date Date to use in MySQL format ("Y-m-d H:i:s")
     *
     * @return array
     */ 
    function _getDate($date=null) {
        $date = self::_fixDate($date);
        if (!empty($date)) return $date;        
        if (is_object($this)) return $this->_date;
        return date("Y-m-d");
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