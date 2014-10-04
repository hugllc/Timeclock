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
 * @version    GIT: $Id: 1d35dec121960365c28c1d00a51fc8c424131138 $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once JPATH_ROOT."/plugins/user/timeclock/timeclock.php";

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
class TimeclockHelpersTimeclock
{
    public static $extension = 'com_timeclock';
    /**
    * This returns the actions that we could take.
    * 
    * @return JObject
    */
    public static function getActions()
    {
        $user   = JFactory::getUser();
        $result = new JObject;
        $assetName = 'com_timeclock';
        $level = 'component';
        $actions = JAccess::getActions('com_timeclock', $level);
        foreach ($actions as $action) {
            $result->set($action->name, $user->authorise($action->name, $assetName));
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
        $file     = strtolower($model);
        $fullfile = dirname(__DIR__)."/models/".$file.".php";
        if (file_exists($fullfile)) {
            include_once($fullfile);
        }
        $fullfile = JPATH_ROOT."/components/com_timeclock/models/".$file.".php";
        if (file_exists($fullfile)) {
            include_once $fullfile;
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
    public static function getUsers($blocked = null)
    {
        $ret   = array();
        $db    = JFactory::getDBO();
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
        $ret = array();
        $v = explode("\n", self::getParam("wCompCodes"));
        foreach ($v as $line) {
            $line = trim($line);
            $line = explode(" ", $line);
            $key = abs($line[0]);
            unset($line[0]);
            $val = implode(" ", $line);
            $ret[(int)$key] = $val;
        }
        if (empty($ret)) {
            $ret = array(1 => "Hours");
        }
        return (array)$ret;
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
            if (!isset($keys)) {
                $keys = explode(":", $line);
            } else {
                $line = explode(":", $line);
                foreach ($keys as $k => $name) {
                    $ret[$name][$line[0]] = $line[$k+1];
                }
            }
        }
        return (array)$ret;
    }
    /**
     * get an array of PTO accrual rates
     *
     * @param object &$user The user to get accrual rates for
     * 
     * @return array
     */
    static public function getPtoAccrualRate(&$user)
    {
        $rates = self::getPtoAccrualRates();
        var_dump($rates);
        return (array)$ret;
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
        static $component;
        if (!is_object($component)) {
            $component = JComponentHelper::getComponent('com_timeclock');
        }
        $ret = $component->params->get($param);
        return $ret;
    }
    /**
    * gets a component parameter
    *
    * @param string $param The parameter to get
    * @param int    $id    The user id to get values about
    * @param string $date  The date to get the param for
    *
    * @return array
    */
    static public function getUserParam($param, $id=null, $date=null)
    {
        return plgUserTimeclock::getParamValue($param, $id, $date);
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
        return plgUserTimeclock::getParams($id);
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
        return plgUserTimeclock::setParamValue($param, $value, $id);
    }

}
