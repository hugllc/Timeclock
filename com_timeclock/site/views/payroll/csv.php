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
 * @version    SVN: $Id: e1fc5c887a1edad708ebadc65fbd04a50869766b $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
/** Check to make sure we are under Joomla */
defined('_JEXEC') or die('Restricted access');

/** Import the views */
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
class TimeclockViewsPayrollCsv extends JViewHtml
{
    /**
    * Renders this view
    *
    * @return unknown
    */
    function render()
    {
        $app = JFactory::getApplication();
        $layout = $this->getLayout();
        
        $useReport = $app->input->get("report", 0, "int");
        $this->params    = JComponentHelper::getParams('com_timeclock');
        $this->payperiod = $this->model->getState('payperiod');

        if ($useReport) {
            $report = $this->model->getReport();
            $data   = $report->timesheets;
            $users  = $report->users;
            $file   = "payroll-saved-";
        } else {
            $data  = $this->model->listItems();
            $users = $this->model->listUsers();
            $file   = "payroll-live-";
        }
        $file .= $this->payperiod->start;
        header('Content-Type: text/csv; charset=utf-8', true);
        header('Content-Disposition: attachment;filename="'.$file.'.csv"', true);
        
        echo $this->_export($users, $data);
        $app->close();
    }
    /**
    * This prints out a row in the file
    *
    * @param array $data The data to use
    *
    * @return string The row created
    */
    private function _export($users, $data)
    {
        $return = $this->_header().PHP_EOL;
        foreach ($users as $user_id => $user) {
            $user = (object)$user;
            $user->data = isset($data[$user_id]) ? $data[$user_id] : array();
            $return .= $this->_row($user).PHP_EOL;
        }
        $return .= $this->_totals($data["totals"]);
        return utf8_encode($return);
    }
    /**
    * This prints out a row in the file
    *
    * @param array $data The data for this row
    *
    * @return string The row created
    */
    private function _row($data)
    {
        $return = $this->_quote(empty($data->name) ? "User ".$data->user_id : $data->name);
        $total  = 0;
        for ($w = 0; $w < $this->payperiod->subtotals; $w++) {
            $worked   = 0;
            $pto      = 0;
            $holiday  = 0;
            $subtotal = 0;
            if (isset($data->data[$w])) {
                $d        = (object)$data->data[$w];
                $worked   = $d->worked;
                $pto      = $d->pto;
                $holiday  = $d->holiday;
                $subtotal = $d->subtotal;
            }
            $total   += $subtotal;
            $return .= ",".$worked;
            $return .= ",".$pto;
            $return .= ",".$holiday;
            $return .= ",".$subtotal;
        }
        $return .= ",".$total;
        return $return;
    }
    /**
    * This prints out a row in the file
    *
    * @param array $data The data for this row
    *
    * @return string The row created
    */
    private function _totals($data)
    {
        $return = $this->_quote(JText::_("COM_TIMECLOCK_TOTAL"));
        $total  = 0;
        for ($w = 0; $w < $this->payperiod->subtotals; $w++) {
            $worked   = 0;
            $pto      = 0;
            $holiday  = 0;
            $subtotal = 0;
            if (isset($data[$w])) {
                $d        = (object)$data[$w];
                $worked   = $d->worked;
                $pto      = $d->pto;
                $holiday  = $d->holiday;
                $subtotal = $d->subtotal;
            }
            $total   += $subtotal;
            $return .= ",".$worked;
            $return .= ",".$pto;
            $return .= ",".$holiday;
            $return .= ",".$subtotal;
        }
        $return .= ",".$total;
        return $return;
    }
    /**
    * This prints out a header row in the file
    *
    * @return string The header row created
    */
    private function _header()
    {
        $return = $this->_quote(JText::_("COM_TIMECLOCK_EMPLOYEE"));
        for ($w = 1; $w <= $this->payperiod->subtotals; $w++) {
            $return .= ",".$this->_quote(JText::_("COM_TIMECLOCK_WEEK")." $w ".JText::_("COM_TIMECLOCK_WORKED"));
            $return .= ",".$this->_quote(JText::_("COM_TIMECLOCK_WEEK")." $w ".JText::_("COM_TIMECLOCK_PTO"));
            $return .= ",".$this->_quote(JText::_("COM_TIMECLOCK_WEEK")." $w ".JText::_("COM_TIMECLOCK_HOLIDAY"));
            $return .= ",".$this->_quote(JText::_("COM_TIMECLOCK_WEEK")." $w ".JText::_("COM_TIMECLOCK_SUBTOTAL"));
        }
        $return .= ",".$this->_quote(JText::_("COM_TIMECLOCK_TOTAL"));
        return $return;
    }
    /**
    * This prints out a row in the file
    *
    * @param array $data
    *
    * @return string The row created
    */
    private function _quote($data)
    {
        return '"'.$data.'"';
    }
}
?>