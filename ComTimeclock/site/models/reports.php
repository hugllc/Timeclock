<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.6 component
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
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockModelReports extends TimeclockModelTimeclock
{
    /** @var string The start date in MySQL format */
    protected $period = array(
        "type" => "month",
    );

    /**
     * Constructor that retrieves the ID from the request
     *
     * @return    void
     */
    function __construct()
    {
        $this->set(
            JRequest::getVar('period', $this->get("type"), '', 'word'),
            "type"
        );
        parent::__construct();
    }


    /**
     * Method to display the view
     *
     * @param string $where      The where clause to add. Must NOT include "WHERE"
     * @param int    $limitstart The record to start on
     * @param int    $limit      The max number of records to retrieve
     * @param string $orderby    The orderby clause.  Must include "ORDER BY"
     *
     * @return string
     */
    function getTimesheetData(
        $where = "1", $limitstart=null, $limit=null, $orderby=""
    ) {
        $db = TimeclockHelper::getParam("decimalPlaces");
        if (empty($where)) {
            $where = " 1 ";
        }
        $key = base64_encode($where.$orderby);
        if (empty($this->data[$key])) {
            $query = $this->sqlQuery($where).$orderby;

            $this->data[$key] = $this->_getList($query, $limitstart, $limit);

            if (!is_array($this->data[$key])) {
                return array();
            }
            foreach ($this->data[$key] as $k => $d) {
                $endDate = TimeclockHelper::getUserParam("endDate", $d->user_id);
                if (empty($endDate)
                    || (self::compareDates($endDate, $d->worked) > 0)
                ) {
                    if ($d->type == "HOLIDAY") {
                        $hperc = $this->getHolidayPerc($d->user_id, $d->worked);
                        $this->data[$key][$k]->hours = $d->hours * $hperc;
                    }
                    $this->data[$key][$k]->hours = round($this->data[$key][$k]->hours, $db);
                } else {
                    unset($this->data[$key][$k]);
                }

            }

        }
        return $this->data[$key];
    }
    /**
     * Method to display the view
     *
     * @param string $where The where clause to add. Must NOT include "WHERE"
     *
     * @return string
     */
    function getTimesheetDataCount($where)
    {
        if (empty($where)) {
            $where = " 1 ";
        }
        $key = base64_encode($where);
        if (empty($this->countData[$key])) {
            $query = $this->sqlQuery($where);
            $this->countData[$key] = $this->_getListCount($query);
        }
        return $this->countData[$key];
    }
}

?>
