<?php

/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.6 component
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
 * @version    GIT: $Id: a9f8da33aebe613777d9e1b213741dd5d69c73ab $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
use Joomla\CMS\Table\Table;
use Joomla\CMS\Component\ComponentHelper;
use HUGLLC\Component\Timeclock\Administrator\Helper\TimeclockHelper;
/**
 * Timeclock World Component Controller
 *
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2023 Hunt Utilities Group, LLC
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
        $defaults = TimeclockHelper::getParamDefaults();

        $db = Factory::getDbo();
        
        $query = $db->getQuery(true);
		$query->select('params');
		$query->from('#__extensions');
		$query->where('element = "com_timeclock"');
		$db->setQuery($query);
		$params_json = $db->loadResult();
        $params = json_decode($params_json, true) ?? array();
        
        if (count($params) < count($defaults))
        {
            $newParams = json_encode(array_merge($defaults, $params));
            
            $query = $db->getQuery(true);
            $query->update($db->quoteName('#__extensions'));
            $query->set($db->quoteName('params') . ' = ' . $db->quote($newParams));
            $query->where('element = "com_timeclock"'); 
            $db->setQuery($query);
            $result = $db->execute();
        }


    }
}
