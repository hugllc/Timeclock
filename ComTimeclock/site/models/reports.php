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
     * @return string
     */
    function getTimesheetData($where, $orderby="")
    {
        $key = base64_encode($where.$orderby);
        if (empty($this->data[$key])) {
            $query = "SELECT SUM(t.hours) as hours, t.worked, t.project_id, t.notes,
                      u.name as user_name, p.name as project_name, t.created_by as user_id
                      FROM #__timeclock_timesheet as t
                      LEFT JOIN #__timeclock_projects as p on t.project_id = p.id
                      LEFT JOIN #__users as u on t.created_by = u.id
                      ".$where."
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
        }
        return $this->data[$key];
    }
    /**
     * Method to display the view
     *
     * @param array $data Data to merge with
     * @param int   $id   The id of the employee
     *
     * @return string
     */
    function getHolidayHours($where, $orderby)
    {
        $key = base64_encode($where.$orderby);
        

    }

}

?>
