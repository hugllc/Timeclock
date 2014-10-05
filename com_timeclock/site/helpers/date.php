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
 * @version    GIT: $Id: 1704fec720b1e135e464969c032dd8cf90adeb1d $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

 
/**
 * Helpers for viewing stuff
 *
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockHelpersDate
{
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
    static public function checkEmploymentDates($start, $end, $date)
    {
        static $dates;
        $key = "$start $end $date";
        if (!isset($dates[$key])) {
            $dates[$key] = true;
            if (self::compareDates($date, $start) == -1) {
                $dates[$key] = false;
            } else if ((self::compareDates($date, $end) == 1) && !empty($end)) {
                $dates[$key] = false;
            }
        }
        return $dates[$key];
    }

    /**
     * Fixes the date format as best it can
     *
     * @param string $date Date to use in MySQL format ("Y-m-d H:i:s")
     *
     * @return array
     */
    static public function fixDate($date)
    {
        static $fixDate;
        if (!isset($fixDate[$date])) {
            $fixDate[$date] = null;
            if (is_int($date)) {
                $fixDate[$date] = date("Y-m-d", $date);
            } else if (is_string($date) && (trim(strtolower($date)) == "now")) {
                $fixDate[$date] = date("Y-m-d");
            } else if (is_string($date)) {
                preg_match(
                    "/[1-9][0-9]{3}-[0-1]{0,1}[0-9]-[0-3]{0,1}[0-9]/",
                    $date,
                    $ret
                );
                if (isset($ret[0])) {
                    $d = explode("-", $ret[0]);
                    $fixDate[$date] = date("Y-m-d", self::dateUnix($d[1], $d[2], $d[0]));
                }
            }
        }
        return $fixDate[$date];
    }
    /**
     * Returns a unix date for when that is necessary.
     *
     * @param int $m The month
     * @param int $d The day
     * @param int $y The year
     *
     * @return array
     */
    static public function dateUnix($m, $d, $y)
    {
        return mktime(6, 0, 0, (int)$m, (int)$d, (int)$y);
    }

    /**
     * Compares two dates.
     *
     * @param string $date1 The first date in Mysql ("Y-m-d") or unix format.
     * @param string $date2 The second date in Mysql ("Y-m-d") or unix format.
     *
     * @return -1 if date1 < date2, 1 if $date1 > $date2, 0 if they are equal
     */
    static public function compareDates($date1, $date2)
    {
        $date1 = is_int($date1) ? $date1 : self::dateUnixSql($date1);
        $date2 = is_int($date2) ? $date2 : self::dateUnixSql($date2);
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
    static public function dateUnixSql($sqlDate)
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
    static public function explodeDate($date)
    {

        $date = self::fixDate($date);
        $date = explode("-", $date);

        return array(
            "y" => isset($date[0]) ? $date[0] : null,
            "m" => isset($date[1]) ? $date[1] : null,
            "d" => isset($date[2]) ? $date[2] : null,
        );
    }
    /**
    * Calculates the start date of the pay period that $date is in, based on
    * the payperiod length, and the start of the first period.
    *
    * @param mixed  $firstPeriodStart The first pay period start date.
    * @param mixed  $date             The date in question
    * @param int    $len              The length of the period
    *
    * @return array
    */
    static public function fixedPayPeriodStart($firstPeriodStart, $date, $len)
    {
        // Get this date
        $unix = is_int($date) ? $date : self::dateUnixSql($date);
        $d = self::explodeDate(self::fixDate($date));

        $start = is_int($firstPeriodStart) ? $firstPeriodStart : self::dateUnixSql($firstPeriodStart);

        // Get the time difference in days
        $timeDiff = round(($unix - $start) / 86400);
        if ($len != 0) {
            $days = $timeDiff % $len;
        } else {
            $days = 0;
        }
        // When it goes negative, it find the beginning of the next period, rather
        // than the one the date is in.
        if ($days < 0) {
            // This is added here because we are subtracting it in the return call
            // so we want this number to be positive.
            $days += $len;
        }
        return date("Y-m-d", self::dateUnix($d["m"], ($d["d"] - $days), $d["y"]));

    }
    /**
    * Calculates the start date of the pay period that $date is in, based on
    * the payperiod length, and the start of the first period.
    *
    * @param mixed  $firstPeriodStart The first pay period start date.
    * @param mixed  $date             The date in question
    * @param int    $len              The length of the period
    *
    * @return array
    */
    static public function fixedPayPeriod($firstPeriodStart, $date, $len)
    {
        $return = array();
        $len = empty($len) ? 14 : $len;
        $start = TimeclockHelpersDate::fixedPayPeriodStart($firstPeriodStart, $date, $len);
        $return["days"] = $len;
        $return["start"] = $start;
        $s = TimeclockHelpersDate::explodeDate($start);
        $return["end"] = self::end($start, $len);
        
        $return["next"] = self::end($start, $len + 1);
        $return["prev"] = self::end($start, -1 * ($len + 1));
        $return["dates"] = TimeclockHelpersDate::payPeriodDates(
            $return["start"], $return["end"]
        );

        return $return;
    }
    /**
    * Creates an array where the values are the dates of the payperiod days, in order
    *
    * @param string $start The first day of the payperiod
    * @param string $end   The last day of the payperiod
    *
    * @return array
    */
    static public function payPeriodDates($start, $end)
    {
        $ret = array();
        $start = self::fixDate($start);
        $end   = self::fixDate($end);
        $first = self::explodeDate($start);
        $day   = $first["d"];
        $days  = self::days($start, $end);
        for ($i = 0; $i < $days; $i++) {
            $date = self::fixDate(self::dateUnix($first["m"], $day++, $first["y"]));
            $ret[] = $date;
            if ($date == $end) {
                break;
            }
        }
        return $ret;
    }
    /**
    * gets a component parameter
    *
    * @param string $date The date to check
    * @param int    $id   The user id to get values about
    *
    * @return array
    */
    static public function beforeStartDate($date, $id=null)
    {
        $start = TimeclockHelpersTimeclock::getUserParam("startDate", $id, $date);
        $ret   = self::compareDates($date, $start) < 0;
        return $ret;
    }
    /**
    * gets a component parameter
    *
    * @param string $date The date to check
    * @param int    $id   The user id to get values about
    *
    * @return array
    */
    static public function afterEndDate($date, $id=null)
    {
        $end = TimeclockHelpersTimeclock::getUserParam("endDate", $id, $date);
        if ($end == 0) {
            return false;
        }
        return self::compareDates($date, $end) > 0;
    }
    /**
    * Calculates the number of days between the two dates, including those two days
    *
    * @param string $start The first day
    * @param string $end   The last day
    *
    * @return array
    */
    static public function days($start, $end)
    {
        $ret = array();
        $start = self::dateUnixSql(self::fixDate($start));
        $end   = self::dateUnixSql(self::fixDate($end));
        return (int)(round(abs($end - $start) / 86400) + 1);
    }
    /**
    * Calculates the number of days between the two dates, including those two days
    *
    * @param string $start The first day
    * @param int    $days  The number of days
    *
    * @return array
    */
    static public function end($start, $days)
    {
        // $days must be 1 closer to 0.
        $days = ($days <= 0) ? (int)$days + 1 : (int)$days - 1;
        $s = TimeclockHelpersDate::explodeDate($start);
        $end = TimeclockHelpersDate::dateUnix($s["m"], $s["d"]+$days, $s["y"]);
        return date("Y-m-d", $end);
    }

}
