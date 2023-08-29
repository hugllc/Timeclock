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
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Language\Text;

require_once __DIR__."/report.php";

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
class TimeclockModelsNotes extends TimeclockModelsReport
{    
    /** This is the type of report */
    protected $type = "notes";

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
        $this->listProjects();
        $return = array(
            "totals" => array("total" => 0, "cost" => 0),
            "notes" => array()
        );
        foreach ($list as $row) {
            $this->checkTimesheet($row);
            $proj_id                     = (int)$row->project_id;
            $user_id = !is_null($row->user_id) ? (int)$row->user_id : (int)$row->worked_by;
            $this->checkUserRow($users[$user_id], $row);
            if ($users[$user_id]->hide) {
                continue;
            }
            $rate = isset($users[$user_id]->timeclock["billableRate"]) ? (float)$users[$user_id]->timeclock["billableRate"] : 0.0;
            $return[$user_id]  = isset($return[$user_id]) ? $return[$user_id] : array(
                "rate" => $rate,
                "hours" => 0,
                "cost"  => 0,
                "error" => ($rate == 0) ? Text::_("COM_TIMECLOCK_BILLABLE_RATE_ZERO") : "",
            );
            $cost = $row->hours * $rate;
            $return[$user_id]["hours"] += $row->hours;
            $return[$user_id]["cost"]  += $cost;
            $return["totals"]["total"] += $row->hours;
            $return["totals"]["cost"]  += $cost;

            // Get the notes
            $return["notes"][$user_id] = isset($return["notes"][$user_id]) ? $return["notes"][$user_id] : array();
            $return["notes"][$user_id][$row->project_id] = isset($return["notes"][$user_id][$row->project_id]) ? $return["notes"][$user_id][$row->project_id] : array(
                "project_id" => $row->project_id,
                "project_name" => $row->project,
                "worked" => array(),
            );
            $return["notes"][$user_id][$row->project_id]["worked"][$row->worked] = $row;
        }
        return $return;
    }
}