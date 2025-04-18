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
 * @version    GIT: $Id: 1d23523e3892a5809ebfd024ca10359070d0803a $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
namespace HUGLLC\Component\Timeclock\Site\Model;

use HUGLLC\Component\Timeclock\Site\Helper\DateHelper;
use HUGLLC\Component\Timeclock\Administrator\Helper\TimeclockHelper;
use Joomla\CMS\Factory;

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
class YtdModel extends ReportModel
{    
    /** This is the type of report */
    protected $type = "ytd";
    /** This is the default date to start this report on */
    protected $defaultStart = "Y-01-01";

    /**
    * Build query and where for protected _getList function and return a list
    *
    * @return array An array of results.
    */
    public function listItems()
    {
        $query = $this->_buildQuery();
        $query = $this->_buildWhere($query);
        $list  = $this->_getList($query);
        $users = $this->listUsers();
        $start = $this->getState("start");
        $end   = $this->getState("end");
        $pto   = TimeclockHelper::getModel("pto");
        $s     = DateHelper::explodeDate($start);
        $this->listProjects();
        $return = array(
            "totals" => array(
                "total" => 0,
                "PTO_ACCRUAL" => 0,
                "PTO_CARRYOVER" => 0,
                "PTO_MANUAL" => 0,
                "PTO_DONATION" => 0,
                "PTO_CURRENT" => 0,
            ),
        );
        foreach ($list as $row) {
            $this->checkTimesheet($row);
            $proj_id                     = (int)$row->project_id;
            $user_id = !is_null($row->user_id) ? (int)$row->user_id : (int)$row->worked_by;
            if (is_null($user_id)) {
                continue;
            }
            if (!isset($users[$user_id])) {
                $users[$user_id] = $this->getUser($user_id);
                $this->checkUser($users[$user_id]);
            }
            $this->checkUserRow($users[$user_id], $row);
            if ($users[$user_id]->hide) {
                continue;
            }
            $type = $row->project_type;
            $return[$user_id]           = isset($return[$user_id]) ? $return[$user_id] : array("total" => 0);
            $return[$user_id][$type]    = isset($return[$user_id][$type]) ? $return[$user_id][$type] : 0;
            $return[$user_id][$type]   += $row->hours;
            $return[$user_id]["total"] += $row->hours;
            $return["totals"][$type]    = isset($return["totals"][$type]) ? $return["totals"][$type] : 0;
            $return["totals"][$type]   += $row->hours;
            $return["totals"]["total"] += $row->hours;
            if (!isset($return[$user_id]["PTO_ACCRUAL"])) {
                $return[$user_id]["PTO_ACCRUAL"]    = (float)$pto->getAccrual($start, $end, $user_id);
                $return["totals"]["PTO_ACCRUAL"]   += $return[$user_id]["PTO_ACCRUAL"];
                $return[$user_id]["PTO_MANUAL"]     = (float)$pto->getManual($start, $end, $user_id);
                $return["totals"]["PTO_MANUAL"]   += $return[$user_id]["PTO_MANUAL"];
                $return[$user_id]["PTO_DONATION"]   = (float)$pto->getDonation($start, $end, $user_id);
                $return["totals"]["PTO_DONATION"]   += $return[$user_id]["PTO_DONATION"];
                $return[$user_id]["PTO_CURRENT"]    = (float)$pto->getPTO($s["y"], $user_id);
                $return["totals"]["PTO_CURRENT"]   += $return[$user_id]["PTO_CURRENT"];
                $return[$user_id]["PTO_CARRYOVER"]  = (float)$pto->getCarryover($start, $end, $user_id);
                $return["totals"]["PTO_CARRYOVER"]   += $return[$user_id]["PTO_CARRYOVER"];
            }
        }
        $return["cols"] = array(
            "PROJECT" => "COM_TIMECLOCK_WORKED", 
            "HOLIDAY" => "COM_TIMECLOCK_HOLIDAY", 
            "UNPAID" => "COM_TIMECLOCK_UNPAID", 
            "VOLUNTEER" => "COM_TIMECLOCK_VOLUNTEER", 
            "PTO" => "COM_TIMECLOCK_PTO_TAKEN",
            "total"  => "COM_TIMECLOCK_TOTAL_WORKED",
            "PTO_ACCRUAL" => "COM_TIMECLOCK_PTO_ACCRUED",
            "PTO_CARRYOVER" => "COM_TIMECLOCK_PTO_CARRYOVER",
            "PTO_MANUAL" => "COM_TIMECLOCK_PTO_MANUAL",
            "PTO_DONATION" => "COM_TIMECLOCK_PTO_DONATION",
        );
        return $return;
    }
}