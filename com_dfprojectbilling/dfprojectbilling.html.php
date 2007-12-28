<?php
/**
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
 * @subpackage com_dfprefs
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2005-2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id: sensor.php 545 2007-12-11 21:50:55Z prices $    
 * @link       https://dev.hugllc.com/index.php/Project:Timeclock
 */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

require_once( $mainframe->getPath( 'class' ) );
require_once( $mainframe->getPath( 'class' , 'com_dfprojecttimeclock') );
require_once( $mainframe->getPath( 'front_html' , 'com_dfproject' ) );
require_once( 'HTML/QuickForm.php' );

define("_PROJ_BASEPATH", dirname( $mainframe->getPath( 'class', 'com_dfprefs' ) ));
define("_PROJ_IMGPATH", sefRelToAbs("components/com_dfproject/images/"));

require_once( _PROJ_BASEPATH."/include/tables.inc.php" );
require_once( _PROJ_BASEPATH."/include/extra.inc.php" );



class HTML_DragonflyProject_Billing {

    var $_listHeader = array(
        'default' => array(
            'action' => '',
            'id' => 'Id',
            'name' => 'Name',
            'company' => 'Company',
        ),
        'fulllist' => array(
            'action' => '',
            'id' => 'Id',
            'name' => 'Name',
            'company' => 'Company',
        ),
    );
    var $_listAttrib = array(
        'default' => array(
            'action' => array('style' => 'text-align: center; white-space: nowrap;'),
            'id' => array('style' => 'text-align: center;'),
        ),
    );

    var $_viewCols = array(
        'default' => array(
            'id' => 'Id',
            'name' => 'Name',
            'company' => 'Company',
            'address1' => 'Address1',
            'address2' => 'Address2',
            'city' => 'City',
            'state' => 'State',
            'zip' => 'Zip',
            'country' => 'Country',
            'notes' => 'Notes',
        ),
    );

    function HTML_DragonflyProject_Billing(&$db) {
        $this->_db = &$db;
        $this->_billing = new billing($db);
        $this->_proj = new project($db);
        $this->_form = new HTML_QuickForm('editBilling', 'post', getMyURL());
        $this->_table = new dfTable();
    }

    function edit($id) {
        $this->_customer = array();
        $this->_customer['info'] = $this->_billing->getRecord($id);
        $this->add("edit");
    }
    function add($mode="add") {    

        $this->_form->addElement('hidden', 'mode', $mode);
        $this->_form->addElement('text', 'info[name]', 'Name', array('size' => 50, 'maxlength' => 64));
        $this->_form->addElement('text', 'info[company]', "Company",array('size' => 50, 'maxlength' => 64));
        $this->_form->addElement('text', 'info[address1]', "Address1",array('size' => 50, 'maxlength' => 64));
        $this->_form->addElement('text', 'info[address2]', 'Address2', array('size' => 50, 'maxlength' => 64));
        $this->_form->addElement('text', 'info[city]', 'City', array('size' => 30, 'maxlength' => 64));
        $this->_form->addElement('text', 'info[state]', 'State', array('size' => 20, 'maxlength' => 64));
        $this->_form->addElement('text', 'info[zip]', 'Zipcode', array('size' => 10, 'maxlength' => 10));
        $this->_form->addElement('text', 'info[country]', 'Country', array('size' => 10, 'maxlength' => 64));
        $this->_form->addElement('textarea', 'info[notes]', 'Notes', array('rows' => 10, 'cols' => 50));

        $this->_form->addElement('submit', 'postbilling', 'Save');
        $this->_form->addRule('info[name]', 'Name can not be blank', 'required', null, 'client');
        $this->_form->addRule('info[address1]', 'Address1 can not be blank', 'required', null, 'client');
        $this->_form->addRule('info[city]', 'City can not be blank', 'required', null, 'client');
        $this->_form->addRule('info[state]', 'State can not be blank', 'required', null, 'client');
        $this->_form->addRule('info[zip]', 'Zipcode must be a number', 'numeric', null, 'client');
        $this->_form->addRule('info[zip]', 'Zipcode can not be blank', 'required', null, 'client');
    
        $this->_form->setDefaults($this->_customer);

        if (isset($_REQUEST['postbilling']) && $this->_form->validate()) {
            $info = mosGetParam($_REQUEST, 'info', array( ) );

            $return = $this->_billing->save($info);
            if ($return) {
                mosRedirect(getMyURL(array('task','id')).'task=view&id='.$this->_billing->id, "Successfully Saved");
            } else {
                $this->_form->addElement('static', null, '<span class="error">Error:</span>', '<span class="error">'.$this->_workersComp->Error.'</span>');
            }
        }
        if ($mode == 'edit') {
             $title = $this->format_customer($this->_customer['info']['id'])." ".$this->_customer['info']['title'];        
        } else {
            $title = _E_ADD;        
        }
        $view = '<td><a href="'.getMyURL(array('task')).'task=view">'.HTML_DragonflyProject::caption(sefRelToAbs('images/preview_f2.png'), 'View', 'View').'</a></td>';
        $this->output($title, $view);

    }
    function view($id, $viewCols = 'default') {
        global $my, $dfconfig;

        if (is_array($this->_viewCols[$uCols])) {
            $viewCols = &$this->_viewCols[$uCols];
        } else {
            $viewCols = &$this->_viewCols['default'];
        }

        $attrib = array();
                
        $code = $this->_billing->getRecord($id);

        foreach ($code as $k => $v) if (is_string($v)) $code[$k] = htmlentities($v);

        $code['id'] = $this->format_customer($code["id"]);

        $this->_table->createInfo($viewCols, $code);
        $this->_table->finishInfo($attrib);

        $title = $code['company']." (".$code['name'].") ";
        
        if (dfprefs::checkAccess('Write')) {
           $edit = '<td><a href="'.getMyURL(array('task', 'mosmsg')).'task=edit">'.HTML_DragonflyProject::caption(sefRelToAbs('images/edit_f2.png'), 'Edit', 'Edit').'</a></td>';
        }
        $this->output($title, $edit);        
    }
    
    function format_customer($code) {
        return $code;
    }
    
    function reports($id, $report="default") {
        mosCommonHTML::loadCalendar();
         switch($this->_periodType) {
            default:
                $HTML = $this->summary($id);
        }
        $this->output("Reports", "", $HTML, true);

    }

    function summary($customer, $printheader = true) {


        $cs = $this->_billing->getRecord($customer);

        $header = array(
            'projectName' => "Project",
        );
        
        $this->_format = array(
            'projectName' => array(),
        );
        
        $this->period = $this->_billing->getPeriod();        

        for ($d = $this->period['start']; $d <= $this->period['end']; $d += 86400) {
            $mDate = date($this->dateFormat, $d);
            $header[date('Y-m-d', $d)] = $mDate;
            $subtotal[date('Y-m-d', $d)] = true;
            $this->_format[date('Y-m-d', $d)] = array('style' => 'text-align: center;');
        }
        $header['subTotal'] = 'Subtotal';
        $this->_format['subTotal'] = array('style' => 'text-align: center;');

        $res = $this->_billing->setup_billing_report($customer);

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
        $dateStr = date("Y-m-d", $this->period['start'])." to ".date("Y-m-d", $this->period['end']);
        $this->_table->addListDividerRow("".$cs['name']." (".$cs['company'].")", array('class' => 'sectiontableheader'));
        $this->_table->addListDividerRow('<div style="font-weight: bold;">'.$dateStr.'</div>', array('class' => 'sectiontableheader'), "Partial");
        $this->_table->addListHeaderRow();
    
        $st = $subtotal;
        foreach ($summary as $sum) {
            $this->_table->addListRow($sum, null, 'data');    
        }
          $st['project_name'] = 'User Totals';
        $this->_table->addListSubTotalRow($st, 'data');
        $this->_table->addListSubTotalRow(array('project_name' => 'Total', 'subTotal' => true), 'data');
    
        $this->_table->finishList($this->_format);

        $return = "";
        if ($printheader) $return .= "<h2>Time Summary for ".$cs['name']." - ".$cs['company']."</h2>";
        $return .= $this->_table->toHTML();

        unset($this->_table);
        return $return;
    }

    function setPeriod($Date = null) {
        return $this->_billing->setPeriod($Date);
    }

    function show($uHeader='default') {
        global $my, $dfconfig;

        if (is_array($this->_listHeader[$uHeader])) {
            $header = &$this->_listHeader[$uHeader];
        } else {
            $header = &$this->_listHeader['default'];
        }

        if (!isset($parentKey)) $parentKey = 0;
         $query = "SELECT * FROM ".PLUGIN_BILLING_TABLE." ORDER BY id ASC";

        $this->_db->setQuery($query);
        $codes = $this->_db->loadAssocList();

        if (is_array($codes)) {
            $this->_table->setFilter(true, true);
            $this->_table->createList($header);        
            foreach ($codes as $key => $val) {

                foreach ($val as $k => $v) if (is_string($v)) $val[$k] = htmlentities($v);
                $link = getMyURL(array('task','id')).'id='.$val['id'].'&';
                $val['action'] = "";
                if (dfprefs::checkAccess('Write')) {
                    $val['action'] .= '<a href="'.$link.'task=edit"><img src="'.sefRelToAbs('images/edit_f2.png').'" alt=" [edit] " width="16" height="16" title="edit" style="border: none;" /></a>';
                }
                $val['action'] .= '<a href="'.$link.'task=view"><img src="'.sefRelToAbs('images/preview_f2.png').'" alt=" [view] " title="view" width="16" height="16" style="border: none;" /></a>';
                if (dfprefs::checkAccess('Reports')) {
                    $val['action'] .= '<a href="'.$link.'task=reports"><img src="'._PROJ_IMGPATH.'text-x-generic.png" alt=" [reports] " title="reports" width="16" height="16" style="border: none;" /></a>';
                }
                $val['billing'] = str_pad($val["id"], 4, "0", STR_PAD_LEFT);
                $this->_table->addListRow($val);
    
            }
            $this->_table->finishList($attrib);
        }

        $this->output();
    }
    function output($name = "", $extratools="", $extraHTML="", $dateform = false) {
        global $my, $dfconfig, $task;
        if (empty($name)) $name = "Customers";
        $link = getMyURL(array('task','id'));
        echo '<div class="componentheading">'.$name.'</div>';
        echo '<table style="float: right;"><tr>';
        echo $extratools;
        if (dfprefs::checkAccess('Write') && ($task != 'reports')) {
            echo '<td><a href="'.$link.'task=new_billings">'.HTML_DragonflyProject::caption(sefRelToAbs('images/new_f2.png'), 'New', 'New').'</a></td>';
        }
        echo '<td><a href="'.$link.'task=billings">'.HTML_DragonflyProject::caption(_PROJ_IMGPATH.'text-x-generic.png', 'List', 'List').'</a></td>';
        echo '<td>'.HTML_DragonflyProject::helpImageLink('billings_help').'</td>';       
        echo '</tr></table>';
        $option = mosGetParam($_REQUEST, 'option', 'com_dfprojectbilling');
        $task = mosGetParam($_REQUEST, 'task', 'reports');
        $report = mosGetParam($_REQUEST, 'report', 'default');
        $id = mosGetParam($_REQUEST, 'id');
//        echo '<div style="clear:both;"></div>';
        if ($dateform) {
?>
    <form method="get" action="<?=$_SERVER['SCRIPT_URL']?>">
        From: <input class="inputbox" type="text" name="StartDate" id="StartDate" size="25" maxlength="19" value="<?=date("Y-m-d", $this->period['start'])?>" />
               <input type="reset" class="button" value="..." onClick="return showCalendar('StartDate', 'y-mm-dd');">
                Date Format: YYYY-MM-DD
        <br />
        To: <input class="inputbox" type="text" name="EndDate" id="EndDate" size="25" maxlength="19" value="<?=date("Y-m-d", $this->period['end'])?>" />
               <input type="reset" class="button" value="..." onClick="return showCalendar('EndDate', 'y-mm-dd');">
                Date Format: YYYY-MM-DD
        <br />
        <input type="hidden" name="option" value="<?=$option?>">
        <input type="hidden" name="task" value="<?=$task?>">
        <input type="hidden" name="report" value="<?=$report?>">
        <input type="hidden" name="id" value="<?=$id?>">
        <input type="submit" value="Go">
    </form>
<?php
        }
        

        print $extraHTML;
        if (is_object($this->_form)) print $this->_form->toHTML();
        if (is_object($this->_table)) print $this->_table->toHTML();
    }
    
     function help($task) {

        $this->output("Help", "", false);
        switch ($task) {
        default:
            HTML_DragonflyProject_Billing::help_about();  
            break;      
        }    
    }
    
    function help_about()
{
?>
<h1>About Dragonfly Project Billing</h1>
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

    function copyright()
{
        echo '<div>com_dfprojectbilling &copy; 2005-2006 <a href="http://www.hugllc.com">Hunt Utilities Group, LLC</a></div>';
    }

}

?>

