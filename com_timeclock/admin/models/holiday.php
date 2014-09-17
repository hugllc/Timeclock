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
 * @version    GIT: $Id: d58448d21a212d6eb41cfdf4a9729d5165ef01a1 $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once __DIR__."/timesheets.php";

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
class TimeclockModelsHoliday extends TimeclockModelsTimesheets
{
    /**
    * Builds the query to be used by the model
    *
    * @return object Query object
    */
    protected function _buildQuery()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(TRUE);
        $query->select('DISTINCT t.timesheet_id,
            (t.hours1 + t.hours2 + t.hours3 + t.hours4 + t.hours5 + t.hours6)
            as hours,
            t.worked, t.project_id, t.notes,
            t.hours1 as hours1, t.hours2 as hours2, t.hours3 as hours3,
            t.hours4 as hours4, t.hours5 as hours5, t.hours6 as hours6,
            t.user_id as user_id, t.created_by as created_by');
        $query->from('#__timeclock_timesheet as t');
        $query->select('p.name as project, p.type as project_type, 
            p.wcCode1, p.wcCode2, p.wcCode3, p.wcCode4, p.wcCode5, p.wcCode6');
        $query->leftjoin('#__timeclock_projects as p on t.project_id = p.project_id');
        $query->select('u.name as user');
        $query->leftjoin('#__users as u on t.user_id = u.id');
        $query->select('v.name as author');
        $query->leftjoin('#__users as v on t.created_by = v.id');
        // We only want holiday timesheets
        $query->where("p.type = 'HOLIDAY'");
        return $query;
    }
    /*
            p.wcCode1 as wcCode1, p.wcCode2 as wcCode2, p.wcCode3 as wcCode3,
            p.wcCode4 as wcCode4, p.wcCode5 as wcCode5, p.wcCode6 as wcCode6,
            t.user_id as user_id, p.name as project_name, p.type as type,
            u.name as author, pc.name as category_name, c.company as company_name,
            c.name as contact_name, t.project_id as project_id, 
            u.id as user_id, p.parent_id as category_id');
    */
    /**
    * Builds the query to be used to count the number of rows
    *
    * @return object Query object
    */
    protected function _buildCountQuery()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(TRUE);
        $query->select('COUNT(t.timesheet_id) as count');
        $query->from('#__timeclock_timesheet as t');
        $query->select('p.name as project, p.type as project_type');
        $query->leftjoin('#__timeclock_projects as p on t.project_id = p.project_id');
        $query->where("p.type = 'HOLIDAY'");
        return $query;
    }

}