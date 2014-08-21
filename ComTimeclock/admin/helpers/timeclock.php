<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.6 component
 * Copyright (C) 2008-2009, 2011 Hunt Utilities Group, LLC
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
 * @copyright  2008-2009, 2011 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.parameter');

if (file_exists(JPATH_ROOT."/plugins/user/timeclock/timeclock.php")) {
    include_once JPATH_ROOT."/plugins/user/timeclock/timeclock.php";
} else {
    // This is purely for test purposes
    include_once dirname(__FILE__)."/../plugins/plg_user_timeclock/timeclock.php";
}
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
class TimeclockHelper
{
    /**
    * Configure the links below the header
    *
    * @param string $vName The name of the active view.
    * @param string $cName The name of the active controller.
    *
    * @return null
    */
    public static function addSubmenu($vName, $cName)
    {
        JSubMenuHelper::addEntry(
            JText::_("COM_TIMECLOCK_CUSTOMERS"),
            'index.php?option=com_timeclock&task=customers.display',
            $cName == 'customers'
        );
        JSubMenuHelper::addEntry(
            JText::_("COM_TIMECLOCK_PROJECTS"),
            'index.php?option=com_timeclock&task=projects.display',
            $cName == 'projects'
        );
        JSubMenuHelper::addEntry(
            JText::_("COM_TIMECLOCK_HOLIDAYS"),
            'index.php?option=com_timeclock&task=holidays.display',
            $cName == 'holidays'
        );
        JSubMenuHelper::addEntry(
            JText::_("COM_TIMECLOCK_TIMESHEETS"),
            'index.php?option=com_timeclock&task=timesheets.display',
            $cName == 'timesheets'
        );
        JSubMenuHelper::addEntry(
            JText::_("COM_TIMECLOCK_MISC_TOOLS"),
            'index.php?option=com_timeclock&task=tools.display',
            $cName == 'tools'
        );
        JSubMenuHelper::addEntry(
            JText::_("COM_TIMECLOCK_ABOUT"),
            'index.php?option=com_timeclock&view=about',
            $vName == 'about'
        );
    }
    /**
    * Title cell
    * For the title and toolbar to be rendered correctly,
    * this title fucntion must be called before the starttable function and
    * the toolbars icons this is due to the nature of how the css has been used
    * to postion the title in respect to the toolbar
    *
    * @param string $title The title
    *
    * @return none
    */
    static public function title($title)
    {
        $mainframe = JFactory::getApplication();

        $html  = "<div class=\"pagetitle\" style=\"background-image: url("
                ."components/com_timeclock/images/"
                ."clock-48.png); background-repeat: no-repeat;\">\n";
        $html .= "<h2>$title</h2>";
        $html .= "</div>\n";

        $mainframe->set('JComponentTitle', $html);
    }
    /**
    * Get the actions
    */
    public static function getActions($messageId = 0)
    {
        $user   = JFactory::getUser();
        $result = new JObject;

        if (empty($messageId)) {
            $assetName = 'com_timeclock';
        }
        else {
            $assetName = 'com_timeclock.message.'.(int) $messageId;
        }

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }
    /**
     * Get Referrer
     *
     * @return string
     */
    static public function referer()
    {
        $referer = JRequest::getString('referer', "", 'post');
        if (!empty($referer)) {
            return $referer;
        }
        $referer = $_SERVER["HTTP_REFERER"];
        if (!empty($referer)) {
            return $referer;
        }
        return "index.php";

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
    /**
    * gets a model
    *
    * @param string $model The model to get
    *
    * @return array
    */
    static public function getModel($model)
    {
        $file = dirname(__FILE__)."/../models/$model.php";
        if (file_exists($file)) {
            include_once($file);
            $class = "TimeclockAdminModel".ucfirst($model);
            return new $class();
        }
        return false;
    }

}
?>
