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
class TimeclockViewsPayrollBase extends JViewBase
{
    /** This is our output */
    protected $output = "";
    
    /**
    * Renders this view
    *
    * @return unknown
    */
    function render()
    {
        $app = JFactory::getApplication();
        
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
        $this->setup($file);
        $this->export($users, $data);
        $this->finalize();
        echo $this->output;
        $app->close();
    }
    /**
    * This prints out a row in the file
    *
    * @param array $users The user list to use
    * @param array $data  The data to use
    *
    * @return string The row created
    */
    protected function export($users, $data)
    {
        $this->header();
        foreach ($users as $user_id => $user) {
            $user = (object)$user;
            $user->data = isset($data[$user_id]) ? $data[$user_id] : array();
            $this->row($user);
        }
        $this->totals($data["totals"]);
    }
    /**
    * This sets up our format
    *
    * @return null
    */
    protected function setup($file)
    {
    }
    /**
    * This prints out a row in the file
    *
    * @param array $data The data for this row
    *
    * @return string The row created
    */
    protected function row($data)
    {
        return "";
    }
    /**
    * This prints out a row in the file
    *
    * @param array $data The data for this row
    *
    * @return string The row created
    */
    protected function totals($data)
    {
        return "";
    }
    /**
    * This prints out a header row in the file
    *
    * @return string The header row created
    */
    protected function header()
    {
        return "";
    }
    /**
    * This prints out a row in the file
    *
    * @param array $data
    *
    * @return string The row created
    */
    protected function quote($data)
    {
        return '"'.$data.'"';
    }
}
?>