<?php
/**
 * This component is for tracking tim
 *
 * PHP Version 5
 *
 * <pre>
 * com_timeclock is a Joomla! 3.1 component
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
 * @version    GIT: $Id: a70fad7ecea96c148fd07befe386dd1bba7cfe4f $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
namespace HUGLLC\Component\Timeclock\Site\Trait;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Pagination\Pagination;
use HUGLLC\Component\Timeclock\Administrator\Helper\TimeclockHelper;
use HUGLLC\Component\Timeclock\Site\Helper\DateHelper;

defined( '_JEXEC' ) or die();

/**
 * Description Here
 *
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2023 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
trait PayperiodTrait
{
    /** 
    * Method to get the filter object
    *
    * @return object The property where specified, the state object where omitted
    */
    public function getPayperiod()
    {
        $payperiod = new \stdClass();
        foreach ($this->getState() as $key => $value) {
            if (str_contains($key, "payperiod.")) {
                $k = explode(".", $key)[1];
                $payperiod->$k = $value;
            }
        }

        return $payperiod;
    }
    /**
     * Populates the payperiod state
     * 
     */
    protected function populatePayperiodState($date)
    {
        $user = $this->getUser();

        // Get the pay period Dates
        $startTime = TimeclockHelper::getParam("firstPayPeriodStart");
        $len = TimeclockHelper::getParam("payPeriodLengthFixed");
        $period = DateHelper::fixedPayPeriod($startTime, $date, $len);

        // $payperiod = new \stdClass();

        $this->setState("payperiod.days", $period["days"]);
        $this->setState("payperiod.start", $period["start"]);
        $this->setState("payperiod.end", $period["end"]);
        $this->setState("payperiod.next", $period["next"]);
        $this->setState("payperiod.prev", $period["prev"]);
        
        $cutoff = TimeclockHelper::getParam("payperiodCutoff");
        $this->setState("payperiod.cutoff", $cutoff);

        $locked = DateHelper::compareDates($cutoff, $period["next"]) >= 0;
        $this->setState("payperiod.locked", $locked);

        $unlock = DateHelper::compareDates($cutoff, $next) == 0;
        $this->setState('payperiod.unlock', $unlock);

        $fulltimeHours = TimeclockHelper::getParam("fulltimeHours");
        $this->setState("payperiod.fulltimeHours", $fulltimeHours);

        $usercutoff = isset($user->timeclock["noTimeBefore"]) ? $user->timeclock["noTimeBefore"] : 0;
        $this->setState("payperiod.usercutoff", $usercutoff);

        $cutoff = TimeclockHelper::getParam("payperiodCutoff");
        $this->setState('payperiod.cutoff', $cutoff);

        $timesheetDone = isset($user->timeclock["timesheetDone"]) ? $user->timeclock["timesheetDone"] : 0;
        $this->setState("payperiod.done", DateHelper::compareDates($timesheetDone, $period["start"]) >= 0);
        $timesheetApproved = isset($user->timeclock["timesheetApproved"]) ? $user->timeclock["timesheetApproved"] : 0;
        $this->setState("payperiod.approved", DateHelper::compareDates($timesheetApproved, $period["start"]) >= 0);

        $dates = array_flip($period["dates"]);
        foreach ($dates as $date => &$value) {
            if ($user->me) {
                $here = DateHelper::checkEmploymentDates($estart, $eend, $date);
                $valid = (DateHelper::compareDates($date, $cutoff)  >= 0);
                $uservalid = (DateHelper::compareDates($date, $usercutoff)  >= 0);
                $value = $here && $valid && $uservalid && !$payperiod->approved;
            } else {
                // Reading someone else's timesheet
                $value = false;
            }
        }
        $this->setState("payperiod.dates", $dates);
        
        $split = 7;
        $this->setState("payperiod.splitdays", $split);

        $subtotals = (int)($len / $split);
        $this->setState("payperiod.subtotals", $subtotals);
    }

}
