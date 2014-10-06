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
 * @version    GIT: $Id: 1d23523e3892a5809ebfd024ca10359070d0803a $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once __DIR__."/report.php";

/**
 * Description Here
 *
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockModelsYtd extends TimeclockModelsReport
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
        $pto   = TimeclockHelpersTimeclock::getModel("pto");
        $this->listProjects();
        $return = array(
            "totals" => array("total" => 0),
        );
        foreach ($list as $row) {
            $this->checkTimesheet($row);
            $proj_id                     = (int)$row->project_id;
            $user_id = !is_null($row->user_id) ? (int)$row->user_id : (int)$row->worked_by;
            if ($users[$user_id]->hide) {
                continue;
            }
            $type = $row->project_type;
            $return[$user_id]            = isset($return[$user_id]) ? $return[$user_id] : array("total" => 0);
            $return[$user_id][$type]    = isset($return[$user_id][$type]) ? $return[$user_id][$type] : 0;
            $return[$user_id][$type]   += $row->hours;
            $return[$user_id]["total"] += $row->hours;
            $return["totals"][$type]    = isset($return["totals"][$type]) ? $return["totals"][$type] : 0;
            $return["totals"][$type]   += $row->hours;
            $return["totals"]["total"] += $row->hours;
            if (!isset($return[$user_id]["PTO_ACCRUAL"])) {
                $return[$user_id]["PTO_ACCRUAL"] = (float)$pto->getAccrual($start, $end, $user_id);
                $return["totals"]["PTO_ACCRUAL"] += $return[$user_id]["PTO_ACCRUAL"];
            }
        }
        $return["cols"] = array(
            "PROJECT" => "COM_TIMECLOCK_WORKED", 
            "HOLIDAY" => "COM_TIMECLOCK_HOLIDAY", 
            "UNPAID" => "COM_TIMECLOCK_VOLUNTEER", 
            "PTO" => "COM_TIMECLOCK_PTO_TAKEN",
            "PTO_ACCRUAL" => "COM_TIMECLOCK_PTO_ACCRUED"
        );
        return $return;
    }
}