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
    * @param $parent This is the parent class
    *
    * @return null
    */
    public function install($parent)
    {
        $this->installModule("mod_timeclockinfo");
        $parent->getParent()->setRedirectURL('index.php?option=com_timeclock&view=about');
    }
    /**
    * This removes everthing
    *
    * @param $parent This is the parent class
    *
    * @return null
    */
    public function uninstall($parent)
    {
        $this->uninstallModule("mod_timeclockinfo");
    }
    /**
    * This updates everthing
    *
    * @param $parent This is the parent class
    *
    * @return null
    */
    public function update($parent)
    {
    }
    /**
    * This runs before install/update/uninstall
    *
    * @param $parent This is the parent class
    *
    * @return null
    */
    public function preflight($parent)
    {
    }
    /**
    * This runs after install/update/uninstall
    *
    * @param $parent This is the parent class
    *
    * @return null
    */
    public function postflight($parent)
    {
    }

    /**
    * This runs after install/update/uninstall
    *
    * @param $name The name of the module to install
    *
    * @return null
    */
    public function installModule($name)
    {
        $basedir = dirname(__FILE__).DS."admin".DS."modules";
        $inst = new JInstaller();
        $this->uninstallModule($name);
        return $inst->install($basedir.DS.$name);
    }

    /**
    * This runs after install/update/uninstall
    *
    * @param $name The name of the module to install
    *
    * @return null
    */
    public function uninstallModule($name)
    {
        $db = &JFactory::getDBO ();

        $db->setQuery("SELECT * FROM #__extensions WHERE element='$name'");
        $mod = $db->loadObject();
        if (is_object($mod)) {
            $inst = new JInstaller();
            return $inst->uninstall('module', $mod->extension_id);
        }
        return true;
    }


}

?>