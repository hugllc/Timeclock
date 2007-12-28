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
 * @subpackage Com_DfProjectWcomp
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2005-2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id: sensor.php 545 2007-12-11 21:50:55Z prices $    
 * @link       https://dev.hugllc.com/index.php/Project:Timeclock
 */
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

require_once($mainframe->getPath('class'));
require_once($mainframe->getPath('front_html', 'com_dfproject'));
require_once('HTML/QuickForm.php');

define("_PROJ_BASEPATH", dirname($mainframe->getPath('class', 'com_dfprefs')));
define("_PROJ_IMGPATH", sefRelToAbs("components/com_dfproject/images/"));

require_once(_PROJ_BASEPATH."/include/tables.inc.php");
require_once(_PROJ_BASEPATH."/include/extra.inc.php");



class HTML_DragonflyProject_wcCode {

    var $_listHeader = array(
        'default' => array(
            'action' => '',
            'id' => 'Code',
            'title' => 'Name',
            'price' => 'Price',
      ),
        'fulllist' => array(
            'action' => '',
            'id' => 'Code',
            'title' => 'Name',
            'description' => "Description",
            'price' => 'Price',
      ),
  );
    var $_listAttrib = array(
        'default' => array(
            'action' => array('style' => 'text-align: center; white-space: nowrap;'),
            'projectNum' => array('style' => 'text-align: center;'),
            'parent' => array('style' => 'text-align: center;'),
      ),
  );

    var $_viewCols = array(
        'default' => array(
            'id' => 'Code',
            'title' => 'Name',
            'description' => 'Description',
            'price' => 'Price',
      ),
  );

    function HTML_DragonflyProject_wcCode(&$db) {
        $this->_db = &$db;
        $this->_wcCode = new wcCode($db);
        $this->_proj = new project($db);
        $this->_form = new HTML_QuickForm('editWorkersComp', 'post', getMyURL());
        $this->_table = new dfTable();
    }

    function edit($id) {
        $this->_code = array();
        $this->_code['info'] = $this->_wcCode->loadArray($id);
        $this->add("edit");
    }
    function add($mode="add") {

        $this->_form->addElement('hidden', 'mode', $mode);
        $this->_form->addElement('text', 'info[id]', 'Code', array('size' => 10, 'maxlength' => 10));
        $this->_form->addElement('text', 'info[title]', "Name",array('maxlength' => 64));
        $this->_form->addElement('textarea', 'info[description]', "Description",array('rows' => 10, 'style' => 'width: 400px;'));
        $this->_form->addElement('text', 'info[price]', 'Price', array('size' => 10, 'maxlength' => 10));

        $this->_form->addElement('submit', 'postwcCode', 'Save');
        $this->_form->addRule('info[id]', 'Code can not be blank', 'required', null, 'client');
        $this->_form->addRule('info[title]', 'Name can not be blank', 'required', null, 'client');
        $this->_form->addRule('info[description]', 'Description can not be blank', 'required', null, 'client');
        $this->_form->addRule('info[price]', 'Price must be numeric', 'numeric', null, 'client');
    
        $this->_form->setDefaults($this->_code);

        if (isset($_REQUEST['postwcCode']) && $this->_form->validate()) {
            $info = mosGetParam($_REQUEST, 'info', array());
//            $this->_workersComp->makeWritable();
            $return = $this->_wcCode->save($info);
            if ($return) {
                mosRedirect(getMyURL(array('task','id')).'task=view&id='.$this->_wcCode->id, "Successfully Saved");
            } else {
                $this->_form->addElement('static', null, '<span class="error">Error:</span>', '<span class="error">'.$this->_workersComp->Error.'</span>');
            }
        }
        if ($mode == 'edit') {
             $title = $this->format_code($this->_code['info']['id'])." ".$this->_code['info']['title'];        
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
                
        $code = $this->_wcCode->loadArray($id);

        foreach ($code as $k => $v) if (is_string($v)) $code[$k] = htmlentities($v);

        $code['id'] = $this->format_code($code["id"]);

        $this->_table->createInfo($viewCols, $code);
        $this->_table->finishInfo($attrib);

        $title = $code['id']." ".$code['title'];
        
        if (dfprefs::checkAccess('Write')) {
           $edit = '<td><a href="'.getMyURL(array('task', 'mosmsg')).'task=edit">'.HTML_DragonflyProject::caption(sefRelToAbs('images/edit_f2.png'), 'Edit', 'Edit').'</a></td>';
        }
        $this->output($title, $edit);        
    }
    
    function format_code($code) {
        return str_pad($code, 4, "0", STR_PAD_LEFT);
    }
    function show($uHeader='default') {
        global $my, $dfconfig;

        if (is_array($this->_listHeader[$uHeader])) {
            $header = &$this->_listHeader[$uHeader];
        } else {
            $header = &$this->_listHeader['default'];
        }

        if (!isset($parentKey)) $parentKey = 0;
         $query = "SELECT * FROM ".$this->_proj->_wc_tbl." ORDER BY id ASC";
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
                    $val['action'] .= '<a href="'.$link.'task=edit"><img src="'.sefRelToAbs('images/edit_f2.png').'" alt=" [edit] " title="edit" width="16" height="16" style="border: none;" /></a>';
                }
                $val['action'] .= '<a href="'.$link.'task=view"><img src="'.sefRelToAbs('images/preview_f2.png').'" alt=" [view] " title="view" width="16" height="16" style="border: none;" /></a>';
                $val['wcCode'] = str_pad($val["id"], 4, "0", STR_PAD_LEFT);
                $this->_table->addListRow($val);
    
            }
            $this->_table->finishList($attrib);
        }

        $this->output();
    }
    function output($name = "", $extratools="") {
        global $my, $dfconfig, $task;
        if (empty($name)) $name = "Worker's Comp Codes";
        $link = getMyURL(array('task','id'));
        echo '<div class="componentheading">'.$name.'</div>';
        echo '<table style="float: right;"><tr>';
        echo $extratools;
        if (dfprefs::checkAccess('Write')) {
            echo '<td><a href="'.$link.'task=new_wcCodes">'.HTML_DragonflyProject::caption(sefRelToAbs('images/new_f2.png'), 'New', 'New').'</a></td>';
        }
        if ($task != 'wcCodes') {
            echo '<td><a href="'.$link.'task=wcCodes">'.HTML_DragonflyProject::caption(_PROJ_IMGPATH.'text-x-generic.png', 'List', 'List').'</a></td>';
        }
        echo '<td>'.HTML_DragonflyProject::helpImageLink('wcCodes_help').'</td>';       
        echo '</tr></table>';

        if (is_object($this->_form)) print $this->_form->toHTML();
        if (is_object($this->_table)) print $this->_table->toHTML();
    }
    
     function help($task) {

        $this->output("Help", "", false);
        switch ($task) {
        default:
            HTML_DragonflyProject_wcCode::help_about();  
            break;      
        }    
    }
    
    function help_about()
{
?>
<h1>About Dragonfly Project Worker's Compensation</h1>
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
        echo '<div>com_dfprojectwcomp &copy; 2005-2006 <a href="http://www.hugllc.com">Hunt Utilities Group, LLC</a></div>';
    }

}

?>

