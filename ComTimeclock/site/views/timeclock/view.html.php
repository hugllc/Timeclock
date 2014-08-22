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

jimport('joomla.application.component.view');

/**
 * HTML View class for the ComTimeclockWorld Component
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

class TimeclockViewTimeclock extends JViewLegacy
{
    /** @var This is where our parameters are stored */
    private $_params;
    /** @var This is the order we sort the database records into */
    private $_orderby;
    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return null
     */
    public function display($tpl = null)
    {
        $mainframe = JFactory::getApplication();

        $this->_getCookies();

        $this->filter();
        $layout          = JRequest::getVar('layout');
        $model           = $this->getModel();
        $user            = JFactory::getUser();
        $user_id         = $user->get("id");
        $employmentDates = $model->getEmploymentDatesUnix();
        $date            = $model->get("date");
        $this->_params   = $mainframe->getParams('com_timeclock');
        $this->_getProjects($user_id, null, null, $this->_orderby);
        $decimalPlaces = TimeclockHelper::getParam("decimalPlaces");

        $this->assignRef("decimalPlaces", $decimalPlaces);
        $this->assignRef("employmentDates", $employmentDates);
        $this->assignRef("user", $user);
        $this->assignRef("date", $date);


        if (method_exists($this, $layout)) {
            $this->$layout($tpl);
        } else {
            $this->timesheet($tpl);
        }

    }


    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return null
     */
    public function timesheet($tpl = null)
    {
        $mainframe = JFactory::getApplication();

        $model   = $this->getModel();
        $period  = $model->getPeriodDates();
        $this->_timesheetData();

        if (!is_object($this->_params)) {
            $this->_params = $mainframe->getParams('com_timeclock');
        }
        $today_color      = $this->_params->get("today_color");
        $today_background = $this->_params->get("today_background");
        $holiday_color      = $this->_params->get("holiday_color");
        $holiday_background = $this->_params->get("holiday_background");
        $decimalPlaces = TimeclockHelper::getParam("decimalPlaces");

        $this->assignRef("decimalPlaces", $decimalPlaces);
        $this->assignRef("today_color", $today_color);
        $this->assignRef("today_background", $today_background);
        $this->assignRef("holiday_color", $holiday_color);
        $this->assignRef("holiday_background", $holiday_background);
        $this->assignRef("period", $period);

        parent::display($tpl);
    }

    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return null
     */
    public function addhours($tpl = null)
    {
        // get the Form
        $this->form = $this->get('Form');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

        $model    = $this->getModel();
        $data     = $model->getData();
        $referer  = JRequest::getVar(
            'referer',
            $_SERVER["HTTP_REFERER"],
            '',
            'string'
        );
        $projid   = JRequest::getVar('projid', null, '', 'string');

        $maxHours = TimeclockHelper::getParam("maxDailyHours");
        $minNoteChars = TimeclockHelper::getParam("minNoteChars");

        $this->assignRef("projid", $projid);
        $this->assignRef("referer", $referer);
        $this->assignRef("data", $data);
        $this->assignRef("maxHours", $maxHours);
        $this->assignRef("minNoteChars", $minNoteChars);

        JHTML::_('behavior.tooltip');
        JHTML::_('behavior.formvalidation');
        $this->wCompCodes = TimeclockHelper::getWCompCodes();
        $this->wCompEnable = TimeclockHelper::getParam("wCompEnable");

        $this->form =


        parent::display($tpl);
    }

    /**
     * Checks employment dates and says if the user can enter hours on that date
     *
     * @param int $date The unix date to check
     *
     * @return bool
     */
    public function checkDate($date)
    {
        return TimeclockModelTimeclock::checkEmploymentDates(
            $this->employmentDates["start"],
            $this->employmentDates["end"],
            $date
        );
    }

    /**
     * Get timesheet data
     *
     * @param int $user_id The ID of the user to get projects for
     *
     * @return null
     */
    private function _getProjects($user_id)
    {
        $projModel = JModelLegacy::getInstance("Projects", "TimeclockAdminModel");
        $projects  = $projModel->getUserProjects($user_id, null, null, $this->_orderby);
        $cats      = TimeclockHelper::getUserParam("Timeclock_Category");
        /*
        foreach ($projects as $k => $p) {
            if (isset($cats[$p->id])) {
                $projects[$k]->show = false;
            } else {
                $projects[$k]->show = true;
            }
        }
        */
        $this->assignRef("projects", $projects);
    }
    /**
     * The display function
     *
     * @return null
     */
    private function _getCookies()
    {
        $set = JRequest::getVar('Timeclock_Set', null, '', 'string', "COOKIE");
        if (!is_array($_COOKIE) || is_null($set)) {
            return;
        }
        $cookie = array();
        foreach ($_COOKIE as $name => $value) {
            if (strtolower(substr(trim($name), 0, 18)) == "timeclock_category") {
                if (trim(strtolower($value)) == "closed") {
                    $key = (int)substr(trim($name), 18);
                    $cookie[$key] = "closed";
                }
            }
        }
        TimeclockHelper::setUserParam("Timeclock_Category", $cookie);
    }
    /**
     * Get timesheet data
     *
     * @return null
     */
    private function _timesheetData()
    {
        $model = $this->getModel();
        $data  = $model->getTimesheetData();
        $hours = array();
        $totals = array("proj" => array(), "worked" => array(), "total" => 0.0);
        foreach ($data as $k => $d) {
            if (!isset($hours[$d->project_id])) {
                $hours[$d->project_id] = array();
                $totals["proj"][$d->project_id] = 0.0;
            }
            if (!isset($hours[$d->project_id][$d->worked])) {
                $hours[$d->project_id][$d->worked] = array(
                    'hours' => 0, 
                    'notes' => ''
                );
            }
            if (!isset($totals["worked"][$d->worked])) {
                $totals["worked"][$d->worked] = 0.0;
            }
            $hours[$d->project_id][$d->worked]['hours'] += $d->hours;
            $hours[$d->project_id][$d->worked]['notes'] .= $d->notes;
            $totals["proj"][$d->project_id]             += $d->hours;
            $totals["worked"][$d->worked]               += $d->hours;
            $totals["total"]                            += $d->hours;
        }
        $this->assignRef("hours", $hours);
        $this->assignRef("totals", $totals);
    }
    /**
     * filter, search and pagination
     *
     * @return null
     */
    public function filter()
    {
        $mainframe = JFactory::getApplication();
        $option = JRequest::getCmd('option');
        $layout = $this->getLayout();
        $db     = JFactory::getDBO();

        if (!is_object($this->_params)) {
            $this->_params = $mainframe->getParams('com_timeclock');
        }
        $filter_order = $mainframe->getUserStateFromRequest(
            "$option.reports.$layout.filter_order",
            'filter_order',
            $this->_params->get("filter_order"),
            'cmd'
        );
        $filter_order_Dir = $mainframe->getUserStateFromRequest(
            "$option.reports.$layout.filter_order_Dir",
            'filter_order_Dir',
            $this->_params->get("filter_order_dir"),
            'word'
        );
        $filter2_order = $mainframe->getUserStateFromRequest(
            "$option.reports.$layout.filter2_order",
            'filter2_order',
            $this->_params->get("filter2_order"),
            'cmd'
        );
        $filter2_order_Dir = $mainframe->getUserStateFromRequest(
            "$option.reports.$layout.filter2_order_Dir",
            'filter2_order_Dir',
            $this->_params->get("filter2_order_dir"),
            'word'
        );

        $filter_order_dir =
            (trim(strtolower($filter_order_Dir)) == "asc") ? "ASC" : "DESC";
        $filter_order_dir2 =
            (trim(strtolower($filter2_order_Dir)) == "asc") ? "ASC" : "DESC";

        if (empty($filter_order)) {
            $filter_order = TimeclockHelper::getUserParam("user_timesheetSort");
            $filter_order_Dir = TimeclockHelper::getUserParam("user_timesheetSortDir");
        }
        if (!empty($filter_order)) {
            $this->_orderby = ' ORDER BY '
                                .TimeclockAdminSql::dotNameQuote($filter_order)
                                .' '.$filter_order_Dir;
            if (!empty($filter2_order)) {
                $this->_orderby .= ", "
                                .TimeclockAdminSql::dotNameQuote($filter2_order)
                                .' '.$filter2_order_Dir;
            }
        }
        // table ordering
        $this->_lists['order_Dir']  = $filter_order_Dir;
        $this->_lists['order']      = $filter_order;
        $this->_lists['order_Dir2'] = $filter2_order_Dir;
        $this->_lists['order2']     = $filter2_order;

        $this->assignRef("lists", $this->_lists);

    }


}

?>