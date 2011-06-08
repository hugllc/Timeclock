<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_HUGnet is a Joomla! 1.5 component
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
 * @package    ComHUGnet
 * @subpackage Com_HUGnet
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009, 2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComHUGnet
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
        $database =& JFactory::getDBO();
        $adminDir = dirname(__FILE__);

        foreach (array("mod_timeclockinfo") as $file) {
            $from = $adminDir.DS."modules".DS.$file;
            $to   = JPATH_ROOT.DS."modules".DS.$file;
            rename($from, $to);
        }
        $database->setQuery(
            "INSERT INTO `jos_modules` (`title`, `content`, `ordering`,
            `position`, `checked_out`, `checked_out_time`, `published`, `module`,
            `numnews`, `access`, `showtitle`, `params`,
            `iscore`, `client_id`, `control`)
            VALUES ('Timeclock Information', '', 101,
            'left', 0, '0000-00-00 00:00:00', 1, 'mod_timeclockinfo',
            0, 0, 1, '',
            1, 0, '');"
        );
        $result = $database->query();
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
        $database =& JFactory::getDBO();
        $adminDir = dirname(__FILE__);

        // Move the modules back to the component so they get deleted with everything
        foreach (array("mod_timeclockinfo") as $file) {
            $to   = $adminDir.DS."modules".DS.$file;
            $from = JPATH_ROOT.DS."modules".DS.$file;
            rename($from, $to);
            $database->setQuery(
                "DELETE FROM `#__modules` WHERE `module`='".$file."';"
            );
            $database->query();
        }
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

}

?>