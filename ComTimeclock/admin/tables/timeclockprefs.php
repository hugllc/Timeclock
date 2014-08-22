<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_Preferences is a Joomla! 1.6 component
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
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
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
 * @copyright  2014 Hunt Utilities Group, LLC
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
     * @var string
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
     * @var string
     */
    public $startDate = null;
    /**
     * Variable
     *
     * @var string
     */
    public $endDate = null;
    /**
     * Variable
     *
     * @var int
     */
    public $manager = 0;
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
        "holidayperc" => 100,
        "status" => "FULLTIME",

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
        $params = new JParameter();
        $params->bind($value);
        return (string)$params;
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
        } else {
            if (is_string($value) && (substr(trim($value), -1) !== "}")) {
                return (array) unserialize(base64_decode($value));
            }
        }
        if (!is_string($value)) {
            return array();
        }
        $params = new JParameter($value);
        return (array)$params->toArray();
    }

    /**
     * Load a row and bind it to the object
     *
     * @param int  $oid   Optional Id argument
     * @param bool $reset The reset
     *
     * @return true
     */
    public function load($oid = -1, $reset = true)
    {
        $ret = parent::load($oid);
        $prefs = self::decode($this->prefs);
        $this->prefs = array_merge(self::$_defaults, $prefs);
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
    public function getDefaults($pref)
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
    public function create($oid)
    {
        $this->id = (int) $oid;
        $this->id = $oid;
        $this->prefs = self::$_defaults;
        // Default the start date to today if it is empty.
        if (($this->startDate == "0000-00-00") || empty($this->startDate)) {
            $this->startDate = date("Y-m-d");
        }
        $query = 'SELECT *'
                .' FROM '.$this->_tbl
                .' WHERE '.$this->_db->NameQuote($this->_tbl_key)
                    .' = '.$this->_db->Quote($oid);
        $this->_db->setQuery($query);

        if ($this->_db->loadAssocList()) {
            // This already exists
            $ret = true;
        } else {
            $ret = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);
        }
        return $ret;
    }
    /**
     * Load a row and bind it to the object
     *
     * @param int $pks    The id of the user to activate
     * @param int $state  0 = deactivate, 1 = activate
     * @param int $userId The id of the user making the change
     *
     * @return true
     */
    public function publish($pks = NULL, $state = 1, $userId = 0)
    {
        foreach ($pks as $oid) {
            self::load($oid);
        }
        return parent::publish($pks, $state, $userId);
    }

    /**
     * Save a row that is bound to this object
     *
     * @param bool $updateNulls Update the nulls
     *
     * @return true
     */
    public function store($updateNulls = false)
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
    public function __construct(&$db)
    {
        parent::__construct('#__timeclock_prefs', "id", $db);
    }

    /**
     * Gets preferences
     *
     * @param string $name   The name of the pref to get
     * @param int    $oid    Optional Id argument
     * @param bool   $reload Force the reloading of the prefs
     *
     * @return mixed The value of the parameter.
     */
    public function getPref($name, $oid = null, $reload=false)
    {
        static $instance;
        if (empty($oid)) {
            $u = JFactory::getUser();
            $oid = $u->get("id");
        }
        // Unauthenticated user.  We don't care
        if (empty($oid)) {
            return null;
        }
        $type = "user";

        $inst = $instance[$oid];
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
        if (isset(self::$_defaults[$name])) {
            return self::getDefaultPref($name);
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
    public function getDefaultPref($name)
    {
        return self::filterPref($name, self::$_defaults[$name]);
    }

    /**
     * Filter a pref
     *
     * @param string $name  The name of the pref to filter
     * @param mixed  $value The value to filter
     *
     * @return mixed Filtered value
     */
    public function filterPref($name, $value)
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
    public function setPref($name, $value, $oid=null)
    {
        if (empty($oid)) {
            $u = JFactory::getUser();
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
            self::getPref($name, $oid, true);
        }
        return $ret;
    }

}
