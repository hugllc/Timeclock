<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_Preferences is a Joomla! 1.5 component
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
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock:JoomlaUI
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Preferences table
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock:JoomlaUI
 */
class TableTimeclockPrefs extends JTable
{
    /**
     * Primary Key
     *
     * @var int
     */
    public $id = null;

    /**
     * Variable
     *
     * @var int
     */
    public $prefs = array();
    /**
     * Variable
     *
     * @var int
     */
    public $published = 0;
    /**
     * Variable
     *
     * @var int
     */
    public $startDate = null;
    /**
     * Variable
     *
     * @var int
     */
    public $endDate = null;
    /**
     * Variable
     *
     * @var int
     */
    public $history = array();

    /**
     * @var array The defaults for everything
     */
    private static $_defaults = array(
        "system" => array(
            "decimalPlaces" => 2,
            "maxDailyHours" => 24,
            "firstPayPeriodStart" => "2000-12-11",
            "payPeriodType" => "FIXED",
            "payPeriodLengthFixed" => 14,
            "wCompEnable" => 0,
            "wCompCodes" => '',
            "timeclockDisable" => 0,
            "timeclockDisableMessage" =>
                "The timeclock system is currently down for maintenance.  Please try again later.",
            "userTypes" => "FULLTIME:Full Time\nPARTTIME:Part Time\nCONTRACTOR:Contractor\nTEMPORARY:Temporary\nTERMINATED:Terminated\nRETIRED:Retired\nUNPAID:Unpaid Leave",
        ),
        "user" => array(
            "admin_holidayperc" => 100,
            "admin_status" => "FULLTIME",
        ),

    );

    /**
     * Encode the parameters
     *
     * @param mixed $value The value to encode
     *
     * @return array
     */
    public function encode($value)
    {
        return base64_encode(serialize($value));
    }
    /**
     * Encode the parameters
     *
     * @param string $value The value to decode
     *
     * @return string
     */
    public function decode($value)
    {
        if (is_array($value)) {
            return $value;
        }
        if (!is_string($value)) {
            return array();
        }
        return unserialize(base64_decode($value));
    }

    /**
     * Load a row and bind it to the object
     *
     * @param int $oid Optional Id argument
     *
     * @return true
     */
    function load($oid = -1)
    {
        $ret = parent::load($oid);
        $prefs = self::decode($this->prefs);
        $this->prefs = array_merge(self::$_defaults["user"], $prefs);
        $this->history = self::decode($this->history);
        // If we don't find it create one
        if (!$ret) {
            return $this->create($oid);
        }
        return $ret;
    }

    /**
     * Load a row and bind it to the object
     *
     * @param string $pref The type of pref to get defaults for (user, system)
     *
     * @return true
     */
    function getDefaults($pref)
    {
        return self::$_defaults[$pref];
    }

    /**
     * Load a row and bind it to the object
     *
     * @param int $oid Optional Id argument
     *
     * @return true
     */
    function create($oid = -1)
    {
        $this->id = (int) $oid;
        if ($oid > 0) {
            $pref = "user";
        }
        if ($oid <= 0) {
            $pref = "system";
        }
        $this->id = $oid;
        $this->prefs = self::$_defaults[$pref];
        // Default the start date to today if it is empty.
        if (($this->startDate == "0000-00-00") || empty($this->startDate)) {
            $this->startDate = date("Y-m-d");
        }
        $ret = $this->store();
        return $ret;
    }
    /**
     * Load a row and bind it to the object
     *
     * @param int $cid     The id of the user to activate
     * @param int $publish 0 = deactivate, 1 = activate
     * @param int $user_id The id of the user making the change
     *
     * @return true
     */
    function publish($cid, $publish, $user_id)
    {
        foreach ($cid as $oid) {
            self::load($oid);
        }
        return parent::publish($cid, $publish, $user_id);
    }

    /**
     * Save a row that is bound to this object
     *
     * @return true
     */
    function store()
    {
        $this->_prefs = $this->prefs;
        $this->prefs = self::encode($this->prefs);
        $this->_history = $this->history;
        $this->history = self::encode($this->history);
        $ret = parent::store();
        $this->prefs = $this->_prefs;
        unset($this->_prefs);
        $this->history = $this->_history;
        unset($this->_history);
        return $ret;
    }

    /**
     * Constructor
     *
     * @param object &$db Database connector object
     */
    function __construct(&$db)
    {
        parent::__construct('#__timeclock_prefs', "id", $db);
    }

    /**
     * Gets preferences
     *
     * @param string $name   The name of the pref to get
     * @param string $type   The type of param to get
     * @param int    $oid    Optional Id argument
     * @param bool   $reload Force the reloading of the prefs
     *
     * @return mixed The value of the parameter.
     */
    function getPref($name, $type="user", $oid = null, $reload=false)
    {
        static $instance;
        if ($type == "system") {
            $oid = -1;
        } else {
            if (empty($oid)) {
                $u =& JFactory::getUser();
                $oid = $u->get("id");
            }
            // Unauthenticated user.  We don't care
            if (empty($oid)) {
                return null;
            }
            $type = "user";
        }

        $inst =& $instance[$type][$oid];
        if (empty($inst)) {
            $inst = JTable::getInstance("TimeclockPrefs", "Table");
            $reload = true;
        }
        if ($reload) {
            $inst->load($oid);
        }
        if (isset($inst->$name)) {
            return self::filterPref($name, $inst->$name);
        }
        if (isset($inst->prefs[$name])) {
            return self::filterPref($name, $inst->prefs[$name]);
        }
        if (isset(self::$_defaults[$type][$name])) {
            return self::getDefaultPref($name, $type);
        }
        if ($type != "system") {
            return self::getPref($name, "system", $oid);
        }
        return self::filterPref($name, null);
    }
    /**
     * Gets preferences
     *
     * @param string $name The name of the pref to get
     * @param string $type The type of param to get
     *
     * @return mixed The value of the parameter.
     */
    function getDefaultPref($name, $type="user")
    {
        return self::filterPref($name, self::$_defaults[$type][$name]);
    }

    /**
     * Filter a pref
     *
     * @param string $name  The name of the pref to filter
     * @param mixed  $value The value to filter
     *
     * @return mixed Filtered value
     */
    function filterPref($name, $value)
    {
        // Protect it from calling itself.
        if (empty($name)) {
            return $value;
        }
        $function = "filterPref".ucfirst($name);

        $methods = get_class_methods("TableTimeclockPrefs");
        if (array_search($function, $methods) !== false) {
            return self::$function($value);
        }
        return $value;
    }

    /**
     * Filter
     *
     * @param string $value The string to parse
     *
     * @return array
     */
    function filterPrefWCompCodes($value)
    {
        $enabled = self::getPref("wCompEnable", "system");
        if (!$enabled) {
            return array(0 => "Hours");
        }
        $ret = array();
        $v = explode("\n", $value);
        foreach ($v as $line) {
            $line = trim($line);
            $key = substr($line, 0, 4);
            $val = substr($line, 4);
            $ret[(int)$key] = $val;
        }
        return $ret;
    }
    /**
     * Filter
     *
     * @param string $value The string to parse
     *
     * @return array
     */
    function filterPrefUserTypes($value)
    {
        $ret = array();
        if (empty($value)) {
            $value = self::$_defaults["system"]["userTypes"];
        }

        $v = explode("\n", $value);
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
        return $ret;
    }

    /**
     * Sets preferences
     *
     * Preferences set this way are ALWAYS user prefs.  System prefs are set through
     * the administrator panel.
     *
     * @param string $name  The name of the pref to get
     * @param string $value The value of the pref to set
     * @param int    $oid   The user ID to set the prefs for.  null sets them for
     *                      the current user.
     *
     * @return mixed The value of the parameter.
     */
    function setPref($name, $value, $oid=null)
    {
        if (empty($oid)) {
            $u =& JFactory::getUser();
            $oid = $u->id;
        }
        if (empty($oid)) {
            return false;
        }
        $p = JTable::getInstance("TimeclockPrefs", "Table");
        $p->load($oid);
        $p->prefs[$name] = $value;
        $ret = $p->store();
        if ($ret) {
            self::getPref($name, "user", $oid, true);
        }
        return $ret;
    }

}
