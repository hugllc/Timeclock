<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
    $Id: dfprojecttimeclock.php 701 2007-05-17 18:57:31Z prices $
    @file dfprojecttimeclock.php
    
    @verbatim
    Copyright 2005 Hunt Utilities Group, LLC (www.hugllc.com)
    
    dfprojecttimeclock.php is part of com_dfprojecttimeclock.

    com_dfprojecttimeclock is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    com_dfprojecttimeclock is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Foobar; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
    @endverbatim
*/

// This module only works for registered users.
if ($my->id > 1) {
//    mosRedirect("", _NOT_AUTH);
    
    require_once( $mainframe->getPath( 'class' ) );
    require_once( $mainframe->getPath( 'front_html' ) );
    require_once( $mainframe->getPath( 'class' , 'com_dfprefs') );
    
    check_ssl();
    
    
    $HTML = new HTML_DragonflyProject_Timeclock($database, dfprefs::getSystem());
    
    $mainframe->addCustomHeadTag('<link href="'.sefRelToAbs("components/com_dfprojecttimeclock/include/timeclock.css").'" rel="stylesheet" type="text/css" />');
    
    
    switch ($task) {
    case 'add':
        if (dfprefs::requireAccess('Timeclock')) {
            $HTML->setDate();
            $HTML->add();
            $HTML->output("Add Hours","", FALSE);   
        }
        break;   
    case 'paysum':
        $HTML->setPayPeriod();
        $HTML->output_header("Payperiod Summary");
        $HTML->summary(FALSE);
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
