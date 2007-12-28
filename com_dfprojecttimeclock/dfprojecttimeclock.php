<?php
/**
 * Short Description
 *
 * PHP Version 5
 *
 * <pre>
 * Timeclock is a Joomla application to keep track of employee time
 * Copyright (C) 2007 Hunt Utilities Group, LLC
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage Com_DfProjectTimeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2005-2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id: sensor.php 545 2007-12-11 21:50:55Z prices $    
 * @link       https://dev.hugllc.com/index.php/Project:Timeclock
 */
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// This module only works for registered users.
if ($my->id > 1) {
//    mosRedirect("", _NOT_AUTH);
    
    require_once($mainframe->getPath('class'));
    require_once($mainframe->getPath('front_html'));
    require_once($mainframe->getPath('class', 'com_dfprefs'));
    
    check_ssl();
    
    
    $HTML = new HTML_DragonflyProject_Timeclock($database, dfprefs::getSystem());
    
    $mainframe->addCustomHeadTag('<link href="'.sefRelToAbs("components/com_dfprojecttimeclock/include/timeclock.css").'" rel="stylesheet" type="text/css" />');
    
    
    switch ($task) {
    case 'add':
        if (dfprefs::requireAccess('Timeclock')) {
            $HTML->setDate();
            $HTML->add();
            $HTML->output("Add Hours","", false);   
        }
        break;   
    case 'paysum':
        $HTML->setPayPeriod();
        $HTML->output_header("Payperiod Summary");
        $HTML->summary(false);
        $HTML->week_totals();
        $HTML->notes();
        $HTML->output_body();
    
        break;
    case 'report':
        if (dfprefs::requireAccess('TSummary')) {
            $HTML->setDates(date("Y-1-1"), date("Y-12-31"));    
            $HTML->output_header("Timeclock Reports");
            $HTML->dateForm();
            $HTML->report_top();
            $reporttask = mosGetParam($_REQUEST, 'reporttask', 'summary');
            switch($reporttask) {
            case 'wcSummary':
                $HTML->wcSummary();
                break;  
            case 'summary':            
            default:
                $HTML->summary();
                break;
            }
            $HTML->output_body();
        }
        break;   
    case 'help':
        $HTML->help($helptask);
        break;
         
    case 'timesheet':
    default:
        $task = 'timesheet';
        if (dfprefs::checkAccess('Timeclock') || dfprefs::requireAccess('TOther')) {
            $HTML->setPayPeriod();
            $HTML->timesheet();
        } else if (dfprefs::checkAccess('TSummary')) {
            mosRedirect(getMyURL(array('task'))."task=paysum");
        } else {
            mosNotAuth();
        }
        break;   
    }
    $HTML->copyright();
} else {
    mosNotAuth();
}



?>
