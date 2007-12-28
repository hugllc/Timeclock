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
 * @subpackage Com_DfPrefs
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2005-2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id: sensor.php 545 2007-12-11 21:50:55Z prices $    
 * @link       https://dev.hugllc.com/index.php/Project:Timeclock
 */
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

    
require_once($mainframe->getPath('class'));
require_once($mainframe->getPath('class', 'com_dfproject'));
require_once($mainframe->getPath('front_html', 'com_dfproject'));

require_once(_PROJ_BASEPATH."/include/tables.inc.php");
require_once(_PROJ_BASEPATH."/include/extra.inc.php");



class HTML_DragonflyProject_Timeclock {

    var $linesPerHeader = 50;
    var $dateFormat = 'D <b\r/>m/d';
        
        

   
    function HTML_DragonflyProject_Timeclock()
{
        global $my, $database;
        $this->_db =& $database;
        $this->_timesheet = new timesheet();
        $this->_proj = new project();
        $this->config = dfprefs::getSystem();

           $this->_users = mosGetParam($_REQUEST, 'users', null);
        if ($this->_users == null) {
            $this->_users = $my->id;
        }
        if (!is_array($this->_users)) $this->_users = array($this->_users);

        $this->_table = new dfTable("Timesheet".date('Ymd', $this->period['start']), array('style' => 'width: 100%;'));

    }
    function setDate()
{
        unset($this->period);
        
        // Get the default period and the start dates.
        $Date = mosGetParam($_REQUEST, 'Date', null);
        $this->Date = getMySQLDate($Date);
    }

    function setDates($defStart=null, $defEnd=null) {
        unset($this->period);
        
        // Get the default period and the start dates.
        $this->StartDate = mosGetParam($_REQUEST, 'StartDate', $defStart);
        $this->EndDate = mosGetParam($_REQUEST, 'EndDate', $defEnd);
        $this->Date = mosGetParam($_REQUEST, 'Date', null);

        if (!empty($this->StartDate) && !empty($this->EndDate)) {
//            $this->StartDate = getMySQLDate($StartDate);
//            $this->EndDate = getMySQLDate($EndDate);
               $this->period = $this->_timesheet->getPeriod($this->StartDate, $this->EndDate);
        } else {
            $this->period = $this->_timesheet->getPayPeriod($this->Date);
        $this->StartDate = date("Y-m-d", $this->period['start']);
        $this->EndDate = date("Y-m-d", $this->period['end']);
        }

    }
    
    function setPayPeriod()
{
        $date = mosGetParam($_REQUEST, 'StartDate', null);
        if (is_array($date)) {
            $date['Y']."-".$date['M']."-".$date['d'];        
        }
          $this->period = $this->_timesheet->getPayPeriod($date);    
    }
    
    function prevnext()
{
    $url =getMyURL(array('Date', 'StartDate', 'EndDate', 'dateSubmit'));

        $pnTable = new HTML_Table(array('style' => 'width: 100%'));
        $nexticon = '<img src="'._PROJ_TIMECLOCK_IMGPATH.'go-next.png" alt="->" title="Next" style="border: none; float: right;" />';
        $previcon = '<img src="'._PROJ_TIMECLOCK_IMGPATH.'go-previous.png" alt="<-" title="Prev" style="border: none; float: left;" />';
        $nextDates = htmlentities('StartDate='.date("Y-m-d", $this->period['next'])."&EndDate=".date("Y-m-d", $this->period["nextend"]));
        $prevDates = htmlentities('StartDate='.date("Y-m-d", $this->period['prev'])."&EndDate=".date("Y-m-d", $this->period["prevend"]));

        $prev = '<a href="'.$url.$prevDates.'">'.$previcon.' &nbsp; '._CMN_PREV.'</a>';
        $now = '<a href="'.$url.'">Today</a>';
        $next = '<a href="'.$url.$nextDates.'">'.$nexticon.' '._CMN_NEXT.' &nbsp;</a>';

        
        // Set up the next/prev dialog.
        $pnTable->setCellContents(0, 0, $prev);
        $pnTable->setCellAttributes(0, 0, array('style' => 'text-align: left; vertical-align: middle; width: 33%;'));
        $pnTable->setCellContents(0, 1, $now);
        $pnTable->setCellAttributes(0, 1, array('style' => 'text-align: center; vertical-align: middle; width: 33%;'));
        $pnTable->setCellContents(0, 2, $next);
        $pnTable->setCellAttributes(0, 2, array('style' => 'text-align: right; vertical-align: middle; width: 33%;'));

        return $pnTable->toHTML();

    }
    
    function dateStr()
{
        return strftime(_DATE_FORMAT_LC, $this->period['start']).' to '.strftime(_DATE_FORMAT_LC, $this->period['end']);
    }
        
    function timesheet()
{
        global $my, $dfconfig;
        
        // This forces the timesheet to only show this users time
        // if they don't have access to other peoples time sheets
        if (!dfprefs::checkAccess('TOthers')) {
            $this->_users = array($my->id);
        }

        $this->dateFormat = 'D <b\r/>M<b\r>d';

        $header = array(
            'name' => "Project",
      );
        
        $format = array(
            'name' => array(),
      );
        $this->_week = array();
        $week = 1;
        $days = 1;
        $weekSub = array("name" => "Week Totals");
        for ($d = $this->period['start']; $d <= $this->period['end']; $d += 86400) {
            $mDate = date($this->dateFormat, $d);
            $header[date('Y-m-d', $d)] = $mDate;
            $this->_subtotal[date('Y-m-d', $d)] = true;
            $this->_week['week'.$week][date('Y-m-d', $d)] = true;
            $format[date('Y-m-d', $d)] = array('style' => 'text-align: center;');
            if (date('Y-m-d') == date('Y-m-d', $d)) {
                //$format[date('Y-m-d', $d)]['class'] = 'today';
                $header[date('Y-m-d', $d)] = '<div class="today">'.$header[date('Y-m-d', $d)].'</div>';
            }
            if (($days % 7) == 0) {
                $header['week'.$week] = 'Wk '.$week;            
                $format['week'.$week] = array('style' => 'text-align: center;');
                $weekSub['week'.$week] = true;
                $week++;
            }
            $days++;
        }
        $header['subTotal'] = 'Subtotal';
        $format['subTotal'] = array('style' => 'text-align: center;');
            
            
        $this->_table->createList($header, null, 0, false);
        foreach ($this->_week as $name => $w) {
            $this->_table->addListSubTotalCol($name, $w);
        }
        $this->_table->addListSubTotalCol('subTotal', $this->_subtotal);
        $this->_table->addListDividerRow('<div style="font-weight: bold;">'.$this->dateStr().'</div>', array('class' => ''), false);
        
        
        
        // Loop through all the users.
        foreach ($this->_users as $user_id) {
            $uInfo = getUser($user_id);
            $this->_table->addListDividerRow($uInfo->name, array('class' => 'sectiontableheader'));
        
               $nheader = $header;
                $baseURL = getMyURL(array('task','user_id', 'project_id', 'Date'))."task=add&";
               foreach (array_keys($this->_subtotal) as $key) {
                   $url = htmlentities('addhours.php?user_id='.$user_id.'&Date='.$key);
//                 $nheader[$key] = '<a href="'.$url.'">'.$header[$key].'</a>';
                $nheader[$key] = '<a href="'.htmlentities($baseURL.'user_id='.$user_id.'&Date='.$key).'">'.$header[$key].'</a>';

               }
               $this->_table->updateListHeader($nheader);
            $this->_table->addListHeaderRow();

            $sheet = $this->_timesheet->prepare_timesheet($user_id);

            $rowCount = 0;        
            foreach ($sheet as $key => $p) {
                if ($rowCount > 10) {
                    $this->_table->addListHeaderRow();
                    $rowCount = 0;
                }
                $this->_table->addListDividerRow($p['name'], array('class' => 'sectiontableheader'), false);
                if ($p['type'] !== 'UMBRELLA'){
                    $this->timeclockAddHoursLink($user_id, $p);
                    $rowtype = ($p['type'] === 'UNPAID') ? 'unpaid' : 'data';
                    $this->_table->addListRow($p, null, $rowtype.$user_id);
                }
                if (is_array($p['subProjects'])) {
                    foreach ($p['subProjects'] as $sp) {
                        $this->timeclockAddHoursLink($user_id, $sp);
                        $rowtype = ($p['type'] === 'UNPAID') ? 'unpaid' : 'data';
                        $this->_table->addListRow($sp, null, $rowtype.$user_id);                
                        $rowCount++;
                    }
                }
                $rowCount++;
            }
            $st = $this->_subtotal;
            $st['name'] = 'Subtotal';
            $this->_table->addListSubTotalRow($st, 'data'.$user_id);
        //    $this->_table->addListSubTotalRow(array('name' => 'Unpaid Total', 'subTotal' => true), 'unpaid'.$user_id);
            $this->_table->addListSubTotalRow($weekSub, 'data'.$user_id);
            $this->_table->addListSubTotalRow(array('name' => 'Paid Total', 'subTotal' => true), 'data'.$user_id);
        
        }
        $this->_table->finishList($format);
        mosCommonHTML::loadOverlib();
                
        $this->output();
        
    }



    function timeclockAddHoursLink($user_id, &$proj) {
    
        $baseURL = getMyURL(array('task','user_id', 'project_id', 'Date'))."task=add&";
        foreach (array_keys($this->_subtotal) as $key) {
            $proj[$key] = (empty($proj[$key])) ? '0' : $proj[$key];

            if (!empty($proj[$key."_Notes"])) {
                $proj[$key] = timesheet::notePopup($proj[$key], $proj[$key."_Notes"]);
            }
            if ($proj['setTime'] === true) {
                // This checks to see if the date is before the user started
                if  ($this->_timesheet->checkUserStartDate($key, $user_id)) {
                       $proj[$key] = '<a href="'.htmlentities($baseURL.'user_id='.$user_id.'&project_id='.$proj['id'].'&Date='.$key).'">'.$proj[$key].'</a>';
                } else {
                    $proj[$key] = 0;
                }
            }

        }
    }
    
    function wcSummary()
{

        if (defined('_HAVE_DFPROJECT_WCOMP')) {
            $header = array(
                'projectName' => "Project",
          );
            
            $this->_format = array(
                'projectName' => array(),
          );
            
            for ($d = $this->period['start']; $d <= $this->period['end']; $d += 86400) {
                $mDate = date($this->dateFormat, $d);
                $header[date('Y-m-d', $d)] = $mDate;
                $subtotal[date('Y-m-d', $d)] = true;
                $this->_format[date('Y-m-d', $d)] = array('style' => 'text-align: center;');
            }
            $header['subTotal'] = 'Subtotal';
            $this->_format['subTotal'] = array('style' => 'text-align: center;');
    
            $res = $this->_timesheet->prepare_wcsummary();
            
           // If we don't get any data, don't build the table.
            $summary = array();
            $header = array(
                'user_name' => "Employee",
          );
            foreach ($res['codes'] as $key => $name) {
                $header[$key] = wordwrap($name, 10, "<br/>"); //$name;
                $this->_format[$key] = array('style' => 'text-align: center;');
                $subtotal[$key] = true;
            }
            $header['subTotal'] = 'Subtotal';
    
            $summary = $res['sheet'];
    
            $this->_table->createList($header, null, 0, false);
            $this->_table->addListSubTotalCol('subTotal', $subtotal);
            $this->_table->addListDividerRow('<div style="font-weight: bold;">'.$this->dateStr().'</div>', array('class' => ''), false);
            $this->_table->addListDividerRow("Worker's Comp Codes", array('class' => 'sectiontableheader'));
            $this->_table->addListHeaderRow();
        
            $st = $subtotal;
            foreach ($summary as $key => $sum) {
                if (empty($sum['user_name'])) $sum['user_name'] = "#".$key;
                $this->_table->addListRow($sum, null, 'data');    
            }
              $st['project_name'] = 'User Totals';
            $this->_table->addListSubTotalRow($st, 'data');
            $this->_table->addListSubTotalRow(array('project_name' => 'Total', 'subTotal' => true), 'data');
        
            $this->_table->finishList($this->_format);
    
            echo "<h2>Worker's Comp Summary</h2>";
        } else {
            echo "Worker's Comp Module not installed";
        }
    }

    function dateForm()
{
        mosCommonHTML::loadCalendar();
    $option=mosGetParam($_REQUEST, "option");
    $Itemid=mosGetParam($_REQUEST, "Itemid");
    $user_id=mosGetParam($_REQUEST, "user_id");
    $task=mosGetParam($_REQUEST, "task", "report");
    $reporttask = mosGetParam($_REQUEST, "reporttask");
    $research = (bool) mosGetParam($_REQUEST, "research");

?>
    <form method="get" action="<?=$_SERVER['REQUEST_URI']?>">
    <input type="hidden" name="option" value="<?=$option?>" />
    <input type="hidden" name="Itemid" value="<?=$Itemid?>" />
    <input type="hidden" name="user_id" value="<?=$user_id?>" />
    <input type="hidden" name="task" value="<?=$task?>" />
    <input type="hidden" name="reporttask" value="<?=$reporttask?>" />
        Dates: <input class="inputbox" type="text" name="StartDate" id="StartDate" size="20" maxlength="19" value="<?=$this->StartDate?>" />
               <input type="reset" class="button" value="..." onClick="return showCalendar('StartDate', 'y-mm-dd');">
                to
        <input class="inputbox" type="text" name="EndDate" id="EndDate" size="20" maxlength="19" value="<?=$this->EndDate?>" />
               <input type="reset" class="button" value="..." onClick="return showCalendar('EndDate', 'y-mm-dd');">
                Date Format: YYYY-MM-DD
                <br />
        <b>Research Only:</b><input name="research" type="checkbox" value="1" <?php if ($research) print " checked "; ?>/>        
        <input type="submit" name="dateS2ubmit" value="Go">        
        <br />

    </form>


<?php


/*
        $form = new HTML_QuickForm('timesheet', 'post', getMyURL());

        // Date form
        $options = array(
            'language' => _LANGUAGE,
            'format' => 'd M Y',
            'minYear' => 2000,
            'maxYear' => date("Y"),                echo '<td><a href="'.$link.'task=paysum">'.HTML_DragonflyProject::caption(_PROJ_TIMECLOCK_IMGPATH."paysummary.png", "Payperiod", "Payperiod").'</a></td>';

      );

        $fDates = array();
        $fDates[] = $form->createElement('date', 'StartDate', 'Start:', $options);
        $fDates[] = $form->createElement('date', 'EndDate', 'End:', $options);
        $form->addGroup($fDates, null, 'Dates:', ' to ');
        $box = array();
        $box[] =& $form->createElement('checkbox', 'research', 'Research Only:');
        $box[] =& $form->createElement('submit', 'dateSubmit', 'Go');
        $form->addGroup($box, null, 'Research Only:');
        $def = array(
            'StartDate' => $this->period['start'],
            'EndDate' => $this->period['end'],
      );
        $form->setDefaults($def);    

        print $form->toHTML();
*/
    }

    function summary($printheader = true) {

        $header = array(
            'projectName' => "Project",
      );
        
        $this->_format = array(
            'projectName' => array(),
      );
        
        for ($d = $this->period['start']; $d <= $this->period['end']; $d += 86400) {
            $mDate = date($this->dateFormat, $d);
            $header[date('Y-m-d', $d)] = $mDate;
            $subtotal[date('Y-m-d', $d)] = true;
            $this->_format[date('Y-m-d', $d)] = array('style' => 'text-align: center;');
        }
        $header['subTotal'] = 'Subtotal';
        $this->_format['subTotal'] = array('style' => 'text-align: center;');

        $res = $this->_timesheet->prepare_summary();
        
       // If we don't get any data, don't build the table.
        $summary = array();
        $header = array(
            'project_name' => "Project",
      );
        foreach ($res['users'] as $key => $name) {
            if (empty($name)) $name = "#".$key;

            $header[$key] = wordwrap($name, 10, "<br/>"); //$name;
            $this->_format[$key] = array('style' => 'text-align: center;');
            $subtotal[$key] = true;
        }
        $header['subTotal'] = 'Subtotal';

        $summary = $res['sheet'];

        $this->_table->createList($header, null, 0, false);
        $this->_table->addListSubTotalCol('subTotal', $subtotal);
        $this->_table->addListDividerRow('<div style="font-weight: bold;">'.$this->dateStr().'</div>', array('class' => ''), false);
        $this->_table->addListDividerRow("Employees", array('class' => 'sectiontableheader'));
        $this->_table->addListHeaderRow();
    
        $st = $subtotal;
        foreach ($summary as $sum) {
            $this->_table->addListRow($sum, null, 'data');    
        }
        $this->_table->addListHeaderRow();
          $st['project_name'] = 'User Totals';
        $this->_table->addListSubTotalRow($st, 'data');
        $this->_table->addListSubTotalRow(array('project_name' => 'Total', 'subTotal' => true), 'data');
    
        $this->_table->finishList($this->_format);

        if ($printheader) echo "<h2>Time Summary</h2>";
    }

    function week_totals()
{
        $header = array(
            'user_name' => "User",
      );
        
        $format = array(
            'user_name' => array(),
      );
        $week = 1;
        $days = 1;
        for ($d = $this->period['start']; $d <= $this->period['end']; $d += 86400) {
            if (($days % 7) == 0) {
                $header['week'.$week] = 'Week '.$week;            
                $format['week'.$week] = array('style' => 'text-align: center;');
                $weekSub['week'.$week] = true;
                $subtotal['week'.$week] = true;
                $week++;
            }
            $days++;
        }
        $header['subTotal'] = 'Subtotal';
        $format['subTotal'] = array('style' => 'text-align: center;');

        $table = new dfTable("WeekTotal".date('Ymd', $this->period['start']), array('style' => ''));
        $table->createList($header, null, 0, false);
        $table->addListSubTotalCol('subTotal', $subtotal);
        $table->addListHeaderRow();
    
        $summary = $this->_timesheet->prepare_week_totals();
        foreach ($summary as $sum) {
            $table->addListRow($sum, null, 'data');    
        }
        $table->addListHeaderRow();
        $weekSub['user_name'] = "SubTotal";
        $table->addListSubTotalRow($weekSub, 'data');
        $table->addListSubTotalRow(array("user_name" => "Total", 'subTotal'=>true), 'data');
    
        $table->finishList($format);

        $this->_tableHTML .= "<h2>Weekly Totals</h2>\n";
        $this->_tableHTML .= $table->toHTML();
    }

    function add()
{
        global $my;
//        $this->Date = isset($_REQUEST['Date']) ? getMySQLDate($_REQUEST['Date']) : date('Y-m-d');

        $this->_form = new HTML_QuickForm('timesheet', 'post', getMyURL());
    
        $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : $my->id;
    
//        $this->_extraHTML .= "    <CENTER><h1>Add/Edit Hours</h1></CENTER>";
    //    $project->Assign->lookup(array("UserKey" => $user->UserKey, "Status" => "ACTIVE"), "UserKey, Status");
        if (!isset($this->Date)) $this->Date = date("Y-m-d");
        $thedate = strtotime($this->Date);
    
        $index = 0;
    
   
        $projects = $this->_timesheet->prepare_add($user_id);
    
        if (!is_array($projects) || (count($projects) == 0)) {
            mosRedirect($_SERVER['HTTP_REFERER'], '<span class="error">No projects found to add time to</span>');
        }

        $res = $this->_timesheet->getHours($user_id, $this->Date);

        $sheets = array();
        foreach ($res as $s) {
            $sheets[$s['project_id']] = $s;
        }

        $dayTotalHours = 0;
        foreach ($sheets as $sheet) {
            if (isset($_POST['hours'][$sheet['project_id']])) {
                $dayTotalHours += (float) $_POST['hours'][$sheet['project_id']];
            } else {
                $dayTotalHours += $sheet['hours'];        
            }
        }
    
        $this->_form->addElement('header', null, 'Enter Hours');
        $options = array(
            'language' => 'en',
            'format' => "d M Y",
            'minYear' => 2000,
            'maxYear' => date("Y")+1,
      );
        $this->_form->addElement('date', 'Date', 'Date Worked:', $options);
        $this->_form->setDefaults(array('Date' => $this->Date));
    
        $this->saveGrp = array(
            $this->_form->createElement('submit', 'PostHours', _CMN_SAVE),
            $this->_form->createElement('submit', 'PostHours', _CMN_SAVE.' & Close'),
      );
        $this->errors = '';
        $this->savedHours = false;
        foreach ($projects as $key => $val) {
            $this->_form->addElement('header', null, '<h2>Section: '.htmlentities($val['fid']." ".$val['name']).'</h2>');
            if ($val['type'] != "UMBRELLA") {
                $this->_build_add_form($key, $val, $user_id, $sheets);
            }
            if (is_array($val['subProjects'])) {
                foreach ($val['subProjects'] as $k => $v) {
                    $this->_build_add_form($k, $v, $user_id, $sheets);
                }
            }
    
        }
    
    
        $postHours = mosGetParam($_POST, 'PostHours', false);
        if (empty($this->errors)) {
            if (($postHours !== false) && ($this->savedHours)) {
                if ($postHours == 'Save & Close') {
                    mosRedirect(getMyURL(array('task','project_id', 'id')), 'Hours Saved');
                } else {
                    mosRedirect(getMyURL(), 'Hours Saved');            
                }
            }
        } else {
            $this->_extraHTML .= '<h2>Errors:</h2><ol>';
            $this->_extraHTML .= $this->errors;
            $this->_extraHTML .= '</ol>';
        }
    
        $this->_extraHTML .= '<div><span>';
        if ($this->_timesheet->checkHours($dayTotalHours)) {
            $this->_extraHTML .= '<span style="font-weight: bold;">Total hours:</span>';
        } else {
            $this->_extraHTML .= '<span style="font-weight: bold;" class="error">Total hours: (Maximum: '.$this->config['maxhours'].')</span> ';
        }
        $this->_extraHTML .= " ".$dayTotalHours.' ('.$this->config['maxhours'].' max)</span></div>';    
    }
    
    function _build_add_form($key, $val, $user_id, $sheets) {
        $val['fid'] = str_pad($val["id"], 4, "0", STR_PAD_LEFT);
        $this->_form->addElement('static', null, null, '<a name="'.htmlentities($val['id']).'"/>');
        $this->_form->addElement('header', null, 'Project: '.htmlentities($val['fid']." ".$val['name']));
        $this->_form->addElement('text', 'hours['.$key.']', 'Hours:', array('size' => 10, 'maxlength' => 5));
        $this->_form->addElement('textarea', 'notes['.$key.']', 'Notes:', array('cols' => '50', 'rows' => 10));
        $this->_form->addRule('hours['.$key.']', 'hours must be numeric', 'numeric', null, 'client');

 //           $hours = empty($val['hours']) ? 0 : $val['hours'];

        if (isset($sheets[$key])) {
            $def = array('hours['.$key.']' => $sheets[$key]['hours'], 'notes['.$key.']' => $sheets[$key]['Notes']);
        } else {
            $def = array('hours['.$key.']' => 0, 'notes['.$key.']' => '');        
        }

        $this->_form->setDefaults($def);
        $postHours = mosGetParam($_POST, 'PostHours', false);
        $hours = mosGetParam($_POST, 'hours', array());
        $notes = mosGetParam($_POST, 'notes', array());
        if (($postHours !== false) && $this->_form->validate()) {
            if (($hours[$key] >= 0) && (!empty($notes[$key]))) {

                $info = array(
                    'id' => $sheets[$key]['id'],
                    'project_id' => $key,
                    'user_id' => $user_id,
                    'hours' => round((float)$hours[$key], $this->config['decimalPlaces']),
                    'Date' => date("Y-m-d", strtotime($this->Date)),
                    'insertDate' => date("Y-m-d H:i:s"),
                    'Notes' => mosStripSlashes($notes[$key]),
              );

                $return = null;
                if (!$this->_timesheet->checkHours($info['hours'])) {
                    $this->errors .= '<li><a href="#'.urlencode(htmlentities($val['id'])).'" class="error">Too many hours.  Max '.$this->_timesheet->maxDailyHours.'</a></li>';
                    $this->_form->addElement('static', null, null, '<span class="error">Too many hours.  Max '.$this->_timesheet->maxDailyHours.'</span>');
                } else if (!$this->_timesheet->checkHours($dayTotalHours)) {
                    $this->errors .= '<li><a href="#'.urlencode(htmlentities($val['id'])).'" class="error">Not saved.  Too many hours for the day.</a></li>';
                    $this->_form->addElement('static', null, null, '<span class="error">Not saved.  Too many hours for the day.</span>');        
                   } else {
//                            $this->_timesheet->id = $info['id'];
                    $return = $this->_timesheet->save($info);
//var_dump($info);
                }
                if ($return === false) {
                    $this->errors .= '<li><a href="#'.urlencode(htmlentities($val['id'])).'" class="error">Database Error</a></li>';
                    $this->_form->addElement('static', null, null, '<span class="error">Error inserting into the database.</span>');
                } else {
                    $this->savedHours = true;
                    $this->_form->addElement('static', null, null, '<span class="success">Inserted correctly.</span>');            
                }
            } else if ($hours[$key] > 0) {
                if (empty($notes[$key])) {
                    $this->errors .= '<li><a href="#'.urlencode(htmlentities($val['id'])).'" class="error">Note field can not be blank</a><br/></li>';
                    $this->_form->addElement('static', null, null, '<span class="error">Note field can not be blank.</span>');
                   }
            }
        }
        $this->_form->addGroup($this->saveGrp);

    }    
    
    function notes()
{
        $res = $this->_timesheet->prepare_notes();
        $notes = array();
        foreach ($res as $note) {
            $notes[$note->user_name][$note->project_name][$note->date]['Note'] .= $note->note;
            $notes[$note->user_name][$note->project_name][$note->date]['Hours'] .= $note->hours;
        }
        
        $this->_tableHTML .= "<h2>Notes</h2>\n";
        $this->_tableHTML .= "<dl>\n";
        foreach ($notes as $user => $projects) {
            $this->_tableHTML .= "<dt><h3>".$user."</h3></dt>\n";
            $this->_tableHTML .= "<dd>\n<dl>\n";
            foreach ($projects as $project => $dates) {
                $this->_tableHTML .= "<dt style=\"font-weight: bold;\">".$project."</dt>\n";
                $this->_tableHTML .= "<dd>\n<dl>\n";
                foreach ($dates as $date => $note) {
                    $this->_tableHTML .= "<dt>".$date." (".$note['Hours']." h)</dt>\n";
                    $this->_tableHTML .= "<dd>".$note['Note']."</dd>\n";
                }
                $this->_tableHTML .= "</dl>\n<dd>\n";
            }
            $this->_tableHTML .= "</dl>\n<dd>\n";
        }
        $this->_tableHTML .= "</dl>\n";
    }
    
    function copyright()
{
        echo '<div>com_dfprojecttimeclock &copy; 2005-2006 <a href="http://www.hugllc.com">Hunt Utilities Group, LLC</a></div>';
    }

    function output_header($name = "", $extratools="", $prevnext=true) {
        global $my, $dfconfig, $task;
        if (empty($name)) $name = "Timesheet";
        $link = getMyURL(array('task'));
        echo '<div class="componentheading">'.$name.'</div>';
        echo '<table style="float: right; width: auto;"><tr>';
        echo $extratools;
        if (dfprefs::checkAccess('Timeclock')) {
            if ($task != "timesheet") {
                echo '<td><a href="'.$link.'">'.HTML_DragonflyProject::caption(_PROJ_TIMECLOCK_IMGPATH.'timesheet.png', 'Timesheet', 'timesheet').'</a></td>';
//                echo '<td><a href="'.$link.'"><img src="'._PROJ_TIMECLOCK_IMGPATH.'timesheet.png" width="24" height="24" border="0" title="Timesheet" alt="Timesheet" /></a></td>';
            }
        }
        if (dfprefs::checkAccess('TSummary')) {
            if ($task != "paysum") {
                echo '<td><a href="'.$link.'task=paysum">'.HTML_DragonflyProject::caption(_PROJ_TIMECLOCK_IMGPATH."paysummary.png", "Payperiod", "Payperiod").'</a></td>';
//                echo '<td><a href="'.$link.'task=paysum"><img src="'._PROJ_TIMECLOCK_IMGPATH.'paysummary.png" width="24" height="24" border="0" title="Pay Summary" alt="Pay Summary" /></a></td>';
            }
            if ($task != "report") {
//                echo '<td><a href="'.$link.'task=report"><img src="'._PROJ_TIMECLOCK_IMGPATH.'summary.png" width="24" height="24" border="0" title="Reports" alt="Reports" /></a></td>';
                echo '<td><a href="'.$link.'task=report">'.HTML_DragonflyProject::caption(_PROJ_TIMECLOCK_IMGPATH."summary.png", "Reports", "Reports").'</a></td>';
            }
        }
        echo '<td>'.HTML_DragonflyProject::helpImageLink().'</td>';
        echo '</tr></table>';
        echo '<div style="clear:both;"></div>';

        if ($prevnext) echo $this->prevnext();

        
    }
    
    function output_body()
{

        if (!empty($this->_extraHTML)) print $this->_extraHTML;
        if (is_object($this->_form)) print $this->_form->toHTML();
        if (is_object($this->_table)) print $this->_table->toHTML();
        if (!empty($this->_tableHTML)) print $this->_tableHTML;
    }

    function output ($name = "", $extratools="", $prevnext=true) {
        $this->output_header($name, $extratools, $prevnext);
        $this->output_body();        
    }
    
    function report_top()
{
        echo '<div style="text-align: center; padding-top: 20px; padding-bottom: 20px;">';
        echo '<a href="'.getMyURL(array('reporttask')).'reporttask=summary">[ Summary ]</a>';
        if (defined('_HAVE_DFPROJECT_WCOMP')) echo '<a href="'.getMyURL(array('reporttask')).'reporttask=wcSummary">[ Worker\'s Comp ]</h2></a>';
        echo '</div>';

        
    }
    
    function help($task) {

        $this->output_header("Help", "", false);
        switch ($task) {
        default:
            $this->help_about();  
            break;      
        }    
    }
    
    function help_about()
{
?>
<h1>About Dragonfly Project: Timeclock</h1>
<h2>Introduction</h2>
<p>
Put stuff here
</p>
<h2>Licensing</h2>
<p>
Most of the Icons were used from <a href="http://tango-project.org/">The Tango Project</a> and are released
under the <a href="http://creativecommons.org/licenses/by-sa/2.5/">Creative Commons Attribution Share-Alike license</a>.
</p>
<p>
Everything else is released under the GNU <a href="http://www.gnu.org/licenses/gpl.html">General Public License</a>
</p>
<?php
    }
}
?>
