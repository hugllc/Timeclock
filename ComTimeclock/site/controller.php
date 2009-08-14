<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.5 component
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
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * ComTimeclock World Component Controller
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockController extends JController
{
    /**
     * Method to display the view
     *
     * @access public
     * @return null
     */
    function display()
    {
        if (TableTimeclockPrefs::getPref("timeclockDisable", "system")) {
            print '<div class="componentheading">';
            print JText::_("Timeclock Disabled")."</div>\n";
            print "<p><strong>";
            print JText::_(
                TableTimeclockPrefs::getPref("timeclockDisableMessage", "system")
            );
            print "</strong></p>";
            return;
        }
        parent::display();
    }

    /**
     * Method to display the view
     *
     * @param string $date The date to enter time
     *
     * @access public
     * @return null
     */
    function checkDates($date)
    {
        $model = $this->getModel("Timeclock");
        $date = self::dateUnixSql($date);
        $eDates = $model->getEmploymentDatesUnix();
        return self::checkEmploymentDates($eDates["start"], $eDates["end"], $date);
    }
    /**
     * Method to display the view
     *
     * @param string $start The date the employee started
     * @param string $end   The date the employee ended
     * @param string $date  The date to enter time
     *
     * @access public
     * @return null
     */
    function checkEmploymentDates($start, $end, $date)
    {
        if ($date < $start) {
            return false;
        }
        if (($date > $end) && !empty($end)) {
            return false;
        }
        return true;
    }


    /**
     * Format the project id
     *
     * @param int $id The project ID
     *
     * @return string
     */
    function formatProjId($id)
    {
        return sprintf("%04d", (int)$id);
    }

    /**
     * Where statement for the reporting period dates
     *
     * @param string $date Date to use in MySQL format ("Y-m-d H:i:s")
     *
     * @return array
     */
    function fixDate($date)
    {
        static $fixDate;
        if (empty($fixDate[$date])) {
            preg_match(
                "/[1-9][0-9]{3}-[0-1]{0,1}[0-9]-[0-3]{0,1}[0-9]/",
                $date,
                $ret
            );
            $fixDate[$date] = $ret[0];
        }
        return $fixDate[$date];
    }
    /**
     * Where statement for the reporting period dates
     *
     * @param int $m The month
     * @param int $d The day
     * @param int $y The year
     *
     * @return array
     */
    public function dateUnix($m, $d, $y)
    {
        return mktime(6, 0, 0, (int)$m, (int)$d, (int)$y);
    }

    /**
     * Where statement for the reporting period dates
     *
     * @param string $date1 The first date in Mysql ("Y-m-d") format.
     * @param string $date2 The second date in Mysql ("Y-m-d") format.
     *
     * @return array
     */
    public function compareDates($date1, $date2)
    {
        $date1 = self::dateUnixSql($date1);
        $date2 = self::dateUnixSql($date2);
        if ($date1 < $date2) {
            return -1;
        }
        if ($date1 > $date2) {
            return 1;
        }
        return 0;
    }

    /**
     * Where statement for the reporting period dates
     *
     * @param string $sqlDate The date in Mysql ("Y-m-d") format.
     *
     * @return array
     */
    public function dateUnixSql($sqlDate)
    {
        $date = self::explodeDate($sqlDate);
        if (empty($date["y"])) {
            return 0;
        }
        return self::dateUnix($date["m"], $date["d"], $date["y"]);
    }
    /**
     * Where statement for the reporting period dates
     *
     * @param int $date The date in Mysql ("Y-m-d") format.
     *
     * @return array
     */
    public function explodeDate($date)
    {

        $date = self::fixDate($date);
        $date = explode("-", $date);

        return array(
            "y" => $date[0],
            "m" => $date[1],
            "d" => $date[2],
        );
    }


    /**
     * Check to see if a user is authorized to view the timeclock
     *
     * @param string $task The task to authorize
     *
     * @return null
     */
    function authorize($task)
    {
        $user =& JFactory::getUser();
        if ($user->get("id") < 1) {
            return false;
        }
        $view = JRequest::getVar('view', "timesheet", '', 'word');
        if (($view == "reports") && !TableTimeClockPrefs::getPref("admin_reports")) {
            return false;
        }
        return TableTimeClockPrefs::getPref("published");
    }

}

?>