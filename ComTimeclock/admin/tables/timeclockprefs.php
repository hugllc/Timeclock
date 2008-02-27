<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_Preferences is a Joomla! 1.5 component
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
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008 Hunt Utilities Group, LLC
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
 * @copyright  2008 Hunt Utilities Group, LLC
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
     * @var array The defaults for everything
     */
    private static $_defaults = array(
        "system" => array(
            "decimalPlaces" => 2,
            "maxDailyHours" => 24,
            "firstPayPeriodStart" => "2000-12-11",
            "payPeriodType" => "FIXED",
            "payPeriodLength" => 14,
            "wCompEnable" => 0,
            "wCompCodes" => '',
        ),
        "user" => array(
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
        if (is_array($value)) return $value;
        if (!is_string($value)) return array();
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
        $this->prefs = self::decode($this->prefs);
        // If we don't find it create one
        if (!$ret) return $this->create($oid);
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
        $this->id = $oid;
        if ($oid > 0) $pref = "user";
        if ($oid < 0) $pref = "system";
        $this->prefs = self::encode(self::$_defaults[$pref]);
        $ret = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);
        $this->prefs = self::$_defaults[$pref];
        return $ret;
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
        $ret = parent::store();
        $this->prefs = $this->_prefs;
        unset($this->_prefs);
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
     * @param string $name The name of the pref to get
     * @param string $type The type of param to get
     *
     * @return mixed The value of the parameter.
     */
    function getPref($name, $type="user")
    {
        static $instance;
        
        if ($type == "system") {
            $oid = -1;
        } else {
            $u =& JFactory::getUser();
            $oid = $u->id;
            if (empty($oid)) return self::_prefCache($name);
            $type = "user";
        }        
        
        if (empty($instance[$type])) {
            $instance[$type] = JTable::getInstance("TimeclockPrefs", "Table");
            $instance[$type]->load($oid);
        }
        if (isset($instance[$type]->prefs[$name])) return self::filterPref($name, $instance[$type]->prefs[$name]);
        if (isset(self::$_defaults[$type][$name])) return self::filterPref($name, self::$_defaults[$type][$name]);
        if ($type != "system") return self::getPref($name, "system");
        return self::filterPref($name, null);
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
        if (empty($name)) return $value;
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
        $ret = array();
        $v = explode("\n", $value);
        foreach ($v as $line) {
            $line = trim($line);
            $key = substr($line, 0, 4);
            $val = substr($line, 4);
            $ret[$key] = $val;
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
     * @param int    $oid   The user ID to set the prefs for.  null sets them for the current user.
     *
     * @return mixed The value of the parameter.
     */
    function setPref($name, $value, $oid=null)
    {
        if (empty($oid)) {
            $u =& JFactory::getUser();
            $oid = $u->id;
        }
        if (empty($oid)) return self::_prefCache($name, $value);
        
        $p = JTable::getInstance("TimeclockPrefs", "Table");
        $p->load($oid);
        $p->prefs[$name] = $value;
        return $p->store();
    }

    /**
     * Sets preferences cache.
     *
     * This is for unauthenticated users.  It doesn't make sense to permanently store
     * things for them, so we store it in their session.  That way changes that they
     * make stick at least until they leave the page.
     *
     * Preferences set this way are ALWAYS user prefs.  System prefs are set through
     * the administrator panel.
     *
     * @param string $name  The name of the pref to get/set
     * @param string $value The value of the pref to set.  If this is null the preferences
     *                      is gotten instead of set.
     *
     * @return mixed The value of the parameter if get, true if set.
     */
    function _prefCache($name, $value = null)
    {
        $prefs =& $_SESSION["TimeclockPrefs"];
        if (is_null($value)) {
            if (isset($prefs[$name])) return $prefs[$name];
            if (isset(self::$_defaults["user"][$name])) return self::$_defaults["user"][$name];
            return self::getPref($name, "system");
        } else {
            $prefs[$name] = $value;
            return true;
        }
    }
}
