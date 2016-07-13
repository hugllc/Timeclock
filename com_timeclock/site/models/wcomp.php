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
class TimeclockModelsWcomp extends TimeclockModelsReport
{    
    /** This is the type of report */
    protected $type = "wcomp";

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
            "totals" => array("total" => 0),
        );
        foreach ($list as $row) {
            $this->checkTimesheet($row);
            $proj_id = (int)$row->project_id;
            $user_id = !is_null($row->user_id) ? (int)$row->user_id : (int)$row->worked_by;
            $this->checkUserRow($users[$user_id], $row);
            if ($users[$user_id]->hide) {
                continue;
            }
            $return[$user_id]            = isset($return[$user_id]) ? $return[$user_id] : array("total" => 0);
            for ($i = 1; $i <= 6; $i++) {
                $hours = $row->{"hours".$i};
                $code  = $row->{"wcCode".$i};
                if ($hours != 0) {
                    $return[$user_id][$code]    = isset($return[$user_id][$code]) ? $return[$user_id][$code] : 0;
                    $return[$user_id][$code]   += $hours;
                    $return[$user_id]["total"] += $hours;
                    $return["totals"][$code]    = isset($return["totals"][$code]) ? $return["totals"][$code] : 0;
                    $return["totals"][$code]   += $hours;
                    $return["totals"]["total"] += $hours;
                    $return["codes"][$code]     = $code;
                }
            }
        }
        return $return;
    }
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @param int $user_id The user to get the projects for
    * 
    * @return array An array of results.
    */
    protected function checkTimesheet(&$entry)
    {
        parent::checkTimesheet($entry);
        if ($entry->hours == 0) {
            for ($i = 1; $i <= 6; $i++) {
                $entry->{"hours".$i} = 0;
            }
        } else if ($entry->project_type == "HOLIDAY") {
            $perc = $this->getHolidayPerc($entry->user_id, $entry->worked);
            for ($i = 1; $i <= 6; $i++) {
                $entry->{"hours".$i} = $entry->{"hours".$i} * $perc;
            }
        }
    }
}
