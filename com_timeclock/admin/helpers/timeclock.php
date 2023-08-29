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
 * @version    GIT: $Id: 1d35dec121960365c28c1d00a51fc8c424131138 $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Helper\ContentHelper;
require_once JPATH_ROOT."/plugins/user/timeclock/timeclock.php";

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
class TimeclockHelpersTimeclock
{
    /** This is where we store the component parameters */
    public static $params = null;
    /** This is where we store the user parameters */
    public static $userparams = array();
    /** This is our extension name */
    public static $extension = 'com_timeclock';
    /**
    * This returns the actions that we could take.
    * 
    * @return CMSObject
    */
    public static function getActions()
    {
        $user   = Factory::getUser();
        $result = new CMSObject;
        $assetName = 'com_timeclock';
        $level = 'component';
        $actions = ContentHelper::getActions('com_timeclock', $level);
        foreach ($actions as $key => $action) {
            $result->set($key, $user->authorise($key, $assetName));
        }
        return $result;
    }
    /**
    * This returns the actions that we could take.
    * 
    * @param string $model The model class to get
    * 
    * @return JModel object
    */
    public static function getModel($model)
    {
        $modelClass = 'TimeclockModels'.ucfirst($model);
        if (!class_exists($modelClass)) {
            $file     = strtolower($model);
            $fullfile = dirname(__DIR__)."/models/".$file.".php";
            if (file_exists($fullfile)) {
                include_once($fullfile);
            }
            $fullfile = JPATH_ROOT."/components/com_timeclock/models/".$file.".php";
            if (file_exists($fullfile)) {
                include_once $fullfile;
            }
        }
        if (!class_exists($modelClass)) {
            include_once dirname(__DIR__)."/models/default.php";
            $modelClass = "TimeclockModelsDefault";
        }
        return new $modelClass();
    }
    /**
    * This returns all of the users that are active in timeclock
    * 
    * @param mixed $publish Whether to get published items or not.  Null for either.
    * 
    * @return array of user objects
    */
    public static function getUsers($blocked = 0)
    {
        $ret   = array();
        $db    = Factory::getDBO();
        $query = $db->getQuery(TRUE);
        $query->select('id, id as user_id, name, username, email, block, 
            registerDate, lastvisitDate, params');
        $query->from('#__users');
        if (!is_null($blocked)) {
            $query->where("block = ".(int)$blocked);
        }
        $query->order("name asc");
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        foreach ($rows as $row) {
            if ((bool)self::getUserParam("active", $row->id)) {
                $row->params = is_string($row->params) ? json_decode($row->params, true) : $row->params;
                $ret[$row->id] = $row;
            }
        }
        return $ret;
    }
    /**
     * Get an array of user types
     *
     * @return string
     */
    static public function getUserTypes()
    {
        $v = explode("\n", self::getParam("userTypes"));
        $ret = array();
        foreach ($v as $line) {
            $line = strip_tags(trim($line));
            if (empty($line)) {
                continue;
            }
            $line = explode(":", $line);
            if (count($line) > 1) {
                $key = strtoupper(str_replace(" ", "", trim($line[0])));
                $ret[$key] = trim($line[1]);
            } else {
                $line = trim($line[0]);
                $key = strtoupper(str_replace(" ", "", substr($line, 0, 16)));
                $ret[$key] = $line;
            }
        }
        if (empty($ret)) {
            $ret = array("EMPLOYEE" => "Employee");
        }
        return (array)$ret;
    }
    /**
     * Get an array of worker's comp codes
     *
     * @return array
     */
    static public function getWCompCodes()
    {
        $enabled = (bool)self::getParam("wCompEnable");
        if (!$enabled) {
            return array(0 => "Hours");
        }
        $codes = self::getParam("wCompCodes");
        if (is_string($codes)) {
            $v = explode("\n", $codes);
            $codes = array();
            foreach ($v as $line) {
                if (empty($line)) {
                    continue;
                }
                $line = trim($line);
                $line = explode(" ", $line);
                $key = abs($line[0]);
                unset($line[0]);
                $val = implode(" ", $line);
                if (empty($val)) {
                    $val = (string)$key;
                }
                $codes[(int)$key] = trim($val);
            }
            if (empty($codes)) {
                $codes = array(0 => "Hours");
            }
            // Cache this in the params
            self::$params["wCompCodes"] = $codes;
        }
        return $codes;
    }
    /**
     * get an array of PTO accrual rates
     *
     * @return array
     */
    static public function getPtoAccrualRates()
    {
        $enabled = (bool)self::getParam("ptoEnable");
        if (!$enabled) {
            return array();
        }
        $ret = array();

        $rates = self::getParam("ptoAccrualRates");
        foreach (explode("\n", $rates) as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            if (!isset($keys)) {
                $keys = explode(":", $line);
            } else {
                $line = explode(":", $line);
                foreach ($keys as $k => $name) {
                    if (isset($line[$k+1])) {
                        $ret[$name][(float)$line[0]] = (float)$line[$k+1];
                    }
                }
            }
        }
        return (array)$ret;
    }
    /**
     * get an array of PTO accrual rates
     *
     * @param int    $user_id The user id to get the accrual rate for
     * @param string $date    The date to check
     * 
     * @return array
     */
    static public function getPtoAccrualRate($user_id, $date)
    {
        $rates = self::getPtoAccrualRates();
        if (empty($rates)) {
            return 0;
        }
        $service = self::getServiceLength($user_id, $date);
        $status = self::getUserParam('status', $user_id);
        $end    = self::getUserParam("endDate", $user_id);
        $start  = self::getUserParam("startDate", $user_id);
        if ($service == 0) {
            return 0;
        }
        if (!TimeclockHelpersDate::checkEmploymentDates($start, $end, $date)) {
            return 0;
        }
        if (!isset($rates[$status]) || !is_array($rates[$status])) {
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
    * get the holiday percentage for a particular day
    *
    * @param int    $user_id The user id to get the holiday percentage for
    * @param string $date    The date to check
    * 
    * @return float The percentage of holiday pay as a decimal between 0.0 and 1.0
    */
    static public function getHolidayPerc($user_id, $date)
    {
        return ((int)plgUserTimeclock::getParamValue("holidayperc", $user_id, $date))/100;
    }
   /**
    * gets a component parameter
    *
    * @param string $param The parameter to get
    *
    * @return array
    */
    static public function getParam($param)
    {
        if (is_null(self::$params)) {
            $component = ComponentHelper::getComponent('com_timeclock');
            self::$params = $component->params;
        }
        return self::$params->get($param);
    }
    /**
    * gets a component parameter
    *
    * @param string $param The parameter to get
    * @param int    $id    The user id to get values about
    *
    * @return array
    */
    static public function getUserParam($param, $id=null)
    {
        $params = self::getUserParams($id);
        if (isset($params[$param])) {
            return $params[$param];
        }
        return null;
    }
    /**
    * gets a component parameter
    *
    * @param int $id The user id to get values about
    *
    * @return array
    */
    static public function getUserParams($id=null)
    {
        if (!isset(self::$userparams[$id])) {
            self::$userparams[$id] = plgUserTimeclock::getParams($id);
        }
        return (array)self::$userparams[$id];
        
    }
    /**
    * gets a component parameter
    *
    * @param string $param The parameter to get
    * @param mixed  $value The value of the parameter
    * @param int    $id    The user id to get values about
    *
    * @return array
    */
    static public function setUserParam($param, $value, $id=null)
    {
        $ret = plgUserTimeclock::setParamValue($param, $value, $id);
        // Update the cached params
        self::$userparams[$id] = plgUserTimeclock::getParams($id);
        return $ret;
    }
    /**
     * Gets select options for parent projects
     *
     * @param int    $oid  The user to get the PTO for.
     * @param string $date The date to check
     *
     * @return float value in years
     */
    function getServiceLength($oid, $date=null)
    {
        if (empty($date)) {
            $date = time();
        } else if (!is_numeric($date)) {
            $date = strtotime($date);
        }
        $start = self::getUserParam("startDate", $oid);
        $start = strtotime($start);
        if ($date < $start) {
            return 0;
        }
        $length = $date - $start;
        return $length / (60*60*24*365.25);
    }

}
