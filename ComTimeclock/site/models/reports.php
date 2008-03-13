<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.5 component
 * Copyright (C) 2008 Hunt Utilities Group, LLC
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
 * @copyright  2008 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/** Include the project stuff */
require_once "timeclock.php";

/**
 * ComTimeclock model
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockModelReports extends TimeclockModelTimeclock
{
    /**
     * Constructor that retrieves the ID from the request
     *
     * @return    void
     */
    function __construct()
    {
        parent::__construct();

        $y = (int)date("Y");

        $startDate = JRequest::getVar('startDate', "$y-1-1", '', 'string');
        $this->setStartDate($startDate);
        $endDate = JRequest::getVar('endDate', "$y-12-31", '', 'string');
        $this->setEndDate($endDate);
        
    }

    /**
     * Where statement for the reporting period dates
     *
     * @param string $date Date to use in MySQL format ("Y-m-d")
     *
     * @return array
     */ 
    function getStartDate($date=null)
    {
        return parent::getDate($date, "_startDate");
    }

    /**
     * Where statement for the reporting period dates
     *
     * @param string $date Date to use in MySQL format ("Y-m-d")
     *
     * @return array
     */ 
    function getEndDate($date=null)
    {
        return parent::getDate($date, "_endDate");
    }

    /**
     * Where statement for the reporting period dates
     *
     * @param string $date Date to use in MySQL format ("Y-m-d")
     *
     * @return null
     */ 
    function setStartDate($date)
    {
        parent::setDate($date, "_startDate");
    }

    /**
     * Where statement for the reporting period dates
     *
     * @param string $date Date to use in MySQL format ("Y-m-d")
     *
     * @return null
     */ 
    function setEndDate($date)
    {
        parent::setDate($date, "_endDate");
    }

    /**
     * Method to display the view
     *
     * @param string $where   The where clause to add. Must include "WHERE"
     * @param string $orderby The orderby clause.  Must include "ORDER BY"
     *
     * @return string
     */
    function getTimesheetData($where, $orderby="")
    {
        if (empty($where)) $where = " WHERE 1 ";
        $key = base64_encode($where.$orderby);
        if (empty($this->data[$key])) {
            $query = "SELECT SUM(t.hours) as hours, t.worked, t.project_id, t.notes,
                      u.name as author, p.name as project_name, t.created_by as user_id,
                      p.type as type
                      FROM #__timeclock_timesheet as t
                      LEFT JOIN #__timeclock_projects as p on t.project_id = p.id
                      LEFT JOIN #__users as u on t.created_by = u.id
                      ".$where." AND (p.type = 'PROJECT' OR p.type = 'PTO')
                      GROUP BY t.created_by, t.worked, t.project_id
                      ".$orderby;
            $ret = $this->_getList($query);
            if (!is_array($ret)) return array();
            $this->data[$key] = array();
            foreach ($ret as $d) {
                $this->data[$key][$d->user_id][$d->project_id][$d->worked]['hours'] += $d->hours;
                $this->data[$key][$d->user_id][$d->project_id][$d->worked]['notes'] .= $d->notes;
                $this->data[$key][$d->user_id][$d->project_id][$d->worked]['rec'] = $d;
            }
            $this->getHolidayHours($where, $orderby, $key);
        }
        return $this->data[$key];
    }
    /**
     * Method to display the view
     *
     * @param string $where   The where clause to add. Must include "WHERE"
     * @param string $orderby The orderby clause.  Must include "ORDER BY"
     * @param string $key     The data key to use
     *
     * @return string
     */
    function getHolidayHours($where, $orderby, $key = null)
    {
        if (empty($key)) $key = base64_encode($where.$orderby);
        
        $query = "SELECT SUM(t.hours) as hours, t.worked, t.project_id, t.notes,
                  j.user_id as user_id, p.name as project_name, p.type as type, 
                  u.name as author
                  FROM #__timeclock_timesheet as t
                  LEFT JOIN #__timeclock_projects as p on t.project_id = p.id
                  JOIN #__timeclock_users as j on j.id = p.id
                  LEFT JOIN #__users as u on j.user_id = u.id
                  LEFT JOIN #__timeclock_prefs as tp on tp.id = u.id
                  ".$where." AND p.type = 'HOLIDAY'
                  AND ((t.worked >= tp.startDate) AND ((t.worked <= tp.endDate) OR (tp.endDate = '0000-00-00')))
                  GROUP BY j.user_id, t.worked, t.project_id
                  ".$orderby;

        $ret = $this->_getList($query);

        if (!is_array($ret)) return array();
        foreach ($ret as $d) {
            $hours = $d->hours * $this->getHolidayPerc($d->user_id, $d->worked);
            $this->data[$key][$d->user_id][$d->project_id][$d->worked]['hours'] += $hours;
            $this->data[$key][$d->user_id][$d->project_id][$d->worked]['notes'] .= $d->notes;
            $this->data[$key][$d->user_id][$d->project_id][$d->worked]['rec'] = $d;
        }

    }

}

?>
