<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_timeclock is a Joomla! 1.6 component
 * Copyright (C) 2008-2009, 2011 Hunt Utilities Group, LLC
 * Copyright 2009 Scott Price
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
 * @package    Comtimeclock
 * @subpackage Com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009, 2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:Comtimeclock
 */

jimport('joomla.plugin.helper');
jimport('joomla.application.module.helper');

/**
 * ComTimeclock World Component Controller
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009, 2011 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class com_timeclockInstallerScript
{

    /**
    * This installs everthing
    *
    * @param object $parent This is the parent class
    *
    * @return null
    */
    public function install($parent)
    {
        $this->installModule("mod_timeclockinfo");
        $this->installPlugin("user", "plg_user_timeclock");
        $parent->getParent()->setRedirectURL('index.php?option=com_timeclock&view=about');
    }
    /**
    * This removes everthing
    *
    * @param object $parent This is the parent class
    *
    * @return null
    */
    public function uninstall($parent)
    {
        $this->uninstallModule("mod_timeclockinfo");
        $this->uninstallPlugin("user", "plg_user_timeclock");
    }
    /**
    * This updates everthing
    *
    * @param object $parent This is the parent class
    *
    * @return null
    */
    public function update($parent)
    {
        $this->installModule("mod_timeclockinfo");
        $this->installPlugin("user", "plg_user_timeclock");
    }
    /**
    * This runs before install/update/uninstall
    *
    * @param string $type   The type of change (install, update, discover_install)
    * @param object $parent This is the parent class
    *
    * @return null
    */
    public function preflight($type, $parent)
    {
    }
    /**
    * This runs after install/update/uninstall
    *
    * @param string $type   The type of change (install, update, discover_install)
    * @param object $parent This is the parent class
    *
    * @return null
    */
    public function postflight($type, $parent)
    {
        //if ($type === "install") {
            $this->saveDefConfig();
        //}
    }

    /**
    * This saves the default config
    *
    * @return null
    */
    public function saveDefConfig()
    {
        $component = &JComponentHelper::getComponent('com_timeclock');
        $xml = simplexml_load_file(dirname(__FILE__).'/admin/config.xml');
        $defaults = array();
        foreach($xml as $element) {
            foreach ($element->field as $field) {
                $att = $field->attributes();
                $name = trim((string)$att['name']);
                $value = trim((string)$att['default']);
                if ((strlen($name) > 0)) {
                    $defaults[$name] = $value;
                }
            }
        }
        $row = JTable::getInstance('extension');
        if ($row->load($component->id)) {
            $params = $row->get("params");
            if (empty($params) || (trim($params) === "{}")) {
                $row->set('params', json_encode($defaults));
                $row->store();
            }
        }

    }

    /**
    * This runs after install/update/uninstall
    *
    * @param string $name The name of the module to install
    *
    * @return null
    */
    public function installModule($name)
    {
        $basedir = dirname(__FILE__).DS."admin".DS."modules";
        $inst = new JInstaller();
        $this->uninstallModule($name);
        $ret = $inst->install($basedir.DS.$name);
        if ($ret) {
            $mod = $this->getExtensionId($name, "module");
            $ret = $this->protectExtension($mod, 1);
        }
        return $ret;
    }

    /**
    * This runs after install/update/uninstall
    *
    * @param string $name The name of the module to install
    *
    * @return null
    */
    public function uninstallModule($name)
    {
        $mod = $this->getExtensionId($name, "module");
        if ($this->protectExtension($mod, 0)) {
            $inst = new JInstaller();
            return $inst->uninstall('module', $mod);
        }
        return false;

    }

    /**
    * This runs after install/update/uninstall
    *
    * @param string $type The type of plugin to use
    * @param string $name The name of the module to install
    *
    * @return null
    */
    public function installPlugin($type, $name)
    {
        $basedir = dirname(__FILE__).DS."admin".DS."plugins";
        $this->uninstallPlugin($name, $type);
        $inst = new JInstaller();
        $ret = $inst->install($basedir.DS.$name);
        if ($ret) {
            $plug = $this->getExtensionId($name, "plugin", $type);
            $ret = $this->protectExtension($plug, 1);
        }
        return $ret;
    }
    /**
    * This runs after install/update/uninstall
    *
    * @param string $type The type of plugin to use
    * @param string $name The name of the module to install
    *
    * @return null
    */
    public function uninstallPlugin($type, $name)
    {
        $plug = $this->getExtensionId($name, "plugin", $type);
        if ($this->protectExtension($plug, 0)) {
            $inst = new JInstaller();
            return $inst->uninstall('plugin', $plug);
        }
        return true;
    }

    /**
    * This runs after install/update/uninstall
    *
    * @param string $name   The name of the module to install
    * @param string $type   The type of plugin to use
    * @param string $folder The folder to find the extension in
    *
    * @return null
    */
    public function getExtensionId($name, $type = null, $folder=null)
    {
        $db = &JFactory::getDBO ();
        $query = "SELECT extension_id FROM #__extensions WHERE name='$name' AND type='$type'";
        if (!is_null($folder)) {
            $query .= " AND folder='$folder'";
        }
        $db->setQuery($query);
        $plug = $db->loadObject();
        return $plug->extension_id;

    }
    /**
    * This runs after install/update/uninstall
    *
    * @param int $id The extension_id to protect
    *
    * @return null
    */
    public function protectExtension($id, $value=1)
    {
        $row = JTable::getInstance('extension');
        if ($ret = $row->load($id)) {
            if (!empty($row->extension_id)) {
                $row->enabled = (int)$value;
                $row->protected = (int)$value;
                return $row->store();
            }
        }
        return false;
    }

}

?>