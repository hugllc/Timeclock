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
@include_once($mainframe->getPath('class', 'com_dfprojectwcomp'));
@include_once($mainframe->getPath('class', 'com_dfprojectbilling'));
require_once('HTML/QuickForm.php');

define("_PROJ_BASEPATH", dirname($mainframe->getPath('class', 'com_dfprefs')));
define("_PROJ_IMGPATH", sefRelToAbs("components/com_dfproject/images/"));

require_once(_PROJ_BASEPATH."/include/tables.inc.php");
require_once(_PROJ_BASEPATH."/include/extra.inc.php");

class HTML_DragonflyProject {
    
    var $_listHeader = array(
        'default' => array(
            'action' => '',
            'projectNum' => '#',
            'name' => 'Name',
            'date' => 'Created',
            'type' => 'Type',
            'status' => 'Status',
            'parent' => 'Parent',
      ),
        'fulllist' => array(
            'action' => '',
            'projectNum' => '#',
            'name' => 'Name',
            'description' => 'Description',
            'research' => 'Research',
            'date' => 'Created',
            'type' => 'Type',
            'status' => 'Status',
            'parent' => 'Parent',
            'wcCode' => 'Workers Comp Code',
       ),
   );        

    var $_listAttrib = array(
        'action' => array('style' => 'text-align: center; white-space: nowrap;'),
        'projectNum' => array('style' => 'text-align: center;'),
        'parent' => array('style' => 'text-align: center;'),
        'description' => array('style' => 'vertical-align: top;'),
  );

    var $_view_attrib = array(
            'description' => array('style' => 'vertical-align: top;'),
  );


    var $_viewCols = array(
        'default' => array(
            'projectNum' => 'Number',
            'name' => 'Name',
            'description' => 'Description',
            '_users_name' => 'Owner',
            '_groups_group_define_name' => "Group",
            'date' => 'Created',
            'research' => 'Research',
            'status' => 'Status',
            'type' => 'Type',
            'projectLog' => 'Project Log',
            'parentname' => 'Parent Project',
            'wcCode' => 'Worker\'s Comp',
            'customer' => 'Customer',
            'action' => 'action',
            'users' => 'Users',
            'adduser' => 'Add User',
      ),
  );
   
    function HTML_DragonflyProject(&$db) {
        $this->_db = &$db;
        $this->_id = (int)$_REQUEST['id'];
        $servers = null;
        $this->_proj = new project($db);
        
        $this->_form = new HTML_QuickForm('editProject', 'post', getMyURL());
        $this->_table = new dfTable();

    }

    function caption($image, $text, $title) {
        $img = '<img src="'.$image.'" alt=" ['.$title.'] " title="'.$title.'" style="border: none;" />';
        $img = '<div style="text-align: center;">'.$img.'<br>'.$text.'</span>';
        return $img;
    }

    

    function edit()
{

        $this->_project = array();

//        $this->_project['proj'] = $this->_proj->get($this->_id);
        $query = "SELECT * FROM ".$this->_proj->_tbl." WHERE id=".$this->_id;
        $this->_db->setQuery($query);
        $project = $this->_db->loadAssocList();

        $this->_project['proj'] = $project[0];
         $this->_project['proj']['projectNum'] = str_pad($this->_project['proj']["id"], 4, "0", STR_PAD_LEFT);
        foreach ($this->_project['proj'] as $k => $v) if (is_string($v)) $this->_project['proj'][$k] = htmlentities($v);

        $this->_form->addElement('hidden', 'proj[id]', $this->_id);
        $this->add('edit');
    }

    function add($mode="new") {
        global $my;

        $project =& $this->_project;

        $this->_form->addElement('hidden', 'mode');
        $this->_form->addElement('text', 'proj[name]', "Name",array('maxlength' => 32));
        $this->_form->addElement('textarea', 'proj[description]', "Description",array('rows' => 10, 'style' => 'width: 400px;'));
        $this->_form->addElement('select', 'proj[research]', "Research", array('NO' => 'No', 'YES' => 'Yes'));

        $StatusArray = array('ACTIVE' => "Active", 'DONE' => "Done", 'HOLD' => "Hold", 'SPECIAL' => "Special");
        $this->_form->addElement('select', 'proj[status]', "Status", $StatusArray);
        

        $TypeArray = array('PROJECT' => 'Project', 'UMBRELLA' => 'Umbrella', 'VACATION' => "Vacation", 'SICK' => "Sick Time", 'HOLIDAY' => "Paid Holiday", "UNPAID" => "Unpaid Hours");
        $this->_form->addElement('select', 'proj[type]', "Type", $TypeArray);

/*        $this->_proj->reset();    
        $this->_proj->setOrder("id");
        $this->_proj->addWhere("parent_id=0");
        $this->_proj->addWhere("Status='ACTIVE'");
*/
        $query = "SELECT * FROM ".$this->_proj->_tbl
                ." WHERE "
                ." parent_id=0 "
                ." AND "
                ." Status='ACTIVE'"
                ." ORDER BY id ASC ";
        $this->_db->setQuery($query);
        $parentProject = $this->_db->loadAssocList();
//        $parentProject = $this->_db->getArray($query);

//        $parentProject = $this->_proj->getAll();
        $parentArray = array('0' => 'None');
        foreach ($parentProject as $key => $val) {
            $parentArray[$val['id']] = htmlentities(str_pad($val["id"], 4, "0", STR_PAD_LEFT).' '.$val['name']);
        }
        $this->_form->addElement('select', 'proj[parent_id]', "Parent", $parentArray);

        if (defined("_HAVE_DFPROJECT_WCOMP")) {
            $query = "SELECT * FROM ".$this->_proj->_wc_tbl
                    ." ORDER BY id ASC ";
            $this->_db->setQuery($query);
            $wc = $this->_db->loadAssocList();
    //        $wc = $this->_db->getArray($query);
            $options = array();
            if (is_array($wc)) {
                foreach ($wc as $val) {
                    $code = str_pad($val["id"], 4, "0", STR_PAD_LEFT);
                    $options[$val['id']] = $code." ".$val['title'];
                }
                $this->_form->addElement('select', 'proj[wcCode]', 'Worker\'s Comp', $options);
            }
        }

        if (defined("_HAVE_DFPROJECT_BILLING")) {

            $query = "SELECT * FROM ".PLUGIN_BILLING_TABLE
                    ." ORDER BY id ASC ";
            $this->_db->setQuery($query);
            $wc = $this->_db->loadAssocList();
    //        $wc = $this->_db->getArray($query);
            $options = array("0" => "None");
            if (is_array($wc)) {
                foreach ($wc as $val) {
                    $options[$val['id']] = $val['name']." - ".$val['company'];
                }
                $this->_form->addElement('select', 'proj[customer]', 'Customer', $options);
            }
        }

        $this->_form->addElement('submit', 'postProject', 'Save');
        $this->_form->addRule('proj[name]', 'Name', 'required', null, 'client');
        $this->_form->addRule('proj[description]', 'Description', 'required', null, 'client');

        $this->_form->setDefaults($project);

        if (isset($_REQUEST['postProject']) && $this->_form->validate()) {
            $info = mosGetParam($_REQUEST, 'proj', array());
            $info["user_id"] = $my->id;
            if ($mode == 'new') {
                $info["date"] = date("Y-m-d H:i:s");
            }
            $this->_id = $info['id'];

            $return = $this->_proj->save($info);
            if ($return) {
                mosRedirect(getMyURL(array('task','id')).'task=view&id='.$this->_proj->id, "Successfully Saved");
                exit();
            } else {
                print $this->_db->stderr(true);
                $this->_form->addElement('static', null, '<span class="error">Error: </span>', '<span class="error">Save Failed</span>');
            }
        } else {
            $this->_id = $_GET["id"];
        }

        if ($mode == 'edit') {
             $title = $project['proj']['projectNum']." ".$project['proj']['name'];        
            $view = '<td><a href="'.getMyURL(array('task')).'task=view">'.HTML_DragonflyProject::caption(sefRelToAbs('images/edit_f2.png'), 'View', 'View').'</a></td>';
        } else {
            $title = _E_ADD;        
        }
        $this->output($title, $view);

    }
    
    function view($id, $uCols = 'default') {
        global $my, $dfconfig;
                
        if (is_array($this->_viewCols[$uCols])) {
            $viewCols = &$this->_viewCols[$uCols];
        } else {
            $viewCols = &$this->_viewCols['default'];
        }

        $attrib = $this->_view_attrib;
                
//            $this->_proj->reset();
//            $this->_proj->addLeftJoin(LU_PERM_TABLE, $prefs['tablePrefix'].'projects.owner_user_id='.LU_PERM_TABLE.'.perm_user_id');
//            $this->_proj->addLeftJoin(LU_USER_TABLE, LU_PERM_TABLE.'.perm_user_id='.LU_USER_TABLE.'.auth_user_id');
//            $this->_proj->addLeftJoin($prefs['tablePrefix'].'groups', $prefs['tablePrefix'].'projects.owner_group_id='.$prefs['tablePrefix'].'groups.group_id');
//            $project = $this->_proj->get($_REQUEST["id"]);
        $query = "SELECT * FROM ".$this->_proj->_tbl." WHERE id=".$id;
        $this->_db->setQuery($query);
        $project = $this->_db->loadAssocList();
        $project = $project[0];
        if (is_array($project)) {
            foreach ($project as $k => $v) if (is_string($v)) $val[$k] = htmlentities($v);

            $project = array_reverse($project);
            $project['projectNum'] = str_pad($project["id"], 4, "0", STR_PAD_LEFT);
            $project = array_reverse($project);

            $query = "SELECT "
                    . $this->_proj->_users_tbl.".* "
                    . ", ".$my->_tbl.".name as name "
                    ." FROM ".$this->_proj->_users_tbl;
            $query .= " LEFT JOIN ".$my->_tbl." ON ".$my->_tbl.".id = ".$this->_proj->_users_tbl.".user_id ";
            $query .= " WHERE ".$this->_proj->_users_tbl.".id=".$id;
//                $users = $this->_db->getArray($query);
            $this->_db->setQuery($query);
            $users = $this->_db->loadAssocList();

            $project['users'] = '';
            foreach ($users as $key => $val) {
                $project['users'] .= '<div  style="height: 18px;">';
                if (dfprefs::checkAccess('Write')) {
                    $project['users'] .= '<a href="'.getMyURL(array('task')).'task=removeuser&user_id='.$val["user_id"].'"><img src="'.sefRelToAbs('images/cancel_f2.png').'" alt=" [remove] " title="remove" width=16 height=16 style="border: none; float: left;" /></a> &nbsp;';
                }
//                    $theUser = $this->_proj->user->getUser($val['user_id']);
                $project['users'] .= $val['name'];
                $project['users'] .= "</div>\n";
            }
            if (empty($project['users'])) $project['users'] = "None";
            if (dfprefs::checkAccess('Write')) {
                $project['adduser'] = '<form action="'.getMyURL(array(task)).'task=adduser" method="post">';
                $project['adduser'] .= mosAdminMenus::UserSelect('user_id', '', 1, 'onChange="submit();"', 'name', 0);
                $project['adduser'] .= '</form>';
            }
            if ($project['parent_id'] != 0) {
                $parent_id = $project['parent_id'];
                if ($project['parent_id'] != 0) {
                    $this->_db->setQuery("SELECT * FROM ".$this->_proj->_tbl." WHERE id=".$project['parent_id']);
                    $parent = $this->_db->loadAssocList();

//                    $parent = $this->_db->getArray("SELECT * FROM ".$this->_proj->_tbl." WHERE id=".$project['parent_id']);                        
//                        $parent = $this->_proj->get($project['parent_id']);
                    if (is_array($parent[0])) {
                        foreach ($parent[0] as $key => $val) {
                            $project['parent'.$key] = htmlentities($val);
                        }
                    }
                } else {
                    $project['parentname'] = 'None';
                }
            }
            $project['description'] = nl2br($project['description']);
            if (defined("_HAVE_DFPROJECT_BILLING")) {
    
                $query = "SELECT * FROM ".PLUGIN_BILLING_TABLE
                        ." WHERE id=".$project['customer']
                        ." ORDER BY id ASC ";
                $this->_db->setQuery($query);
                $wc = $this->_db->loadAssocList();

                if (is_array($wc[0])) {
                    $wc = $wc[0];
                    $project['customer'] = $wc['name']." - ".$wc['company'];
                } else {
                    unset($project['customer']);
                }    
            } else {
                unset($project['customer']);
            }
            $this->_table->createInfo($viewCols, $project);
            $this->_table->finishInfo($attrib);
        }
        $title = $project['projectNum']." ".$project['name'];
        
        if (dfprefs::checkAccess('Write')) {
           $edit = '<td><a href="'.getMyURL(array('task')).'task=edit">'.HTML_DragonflyProject::caption(sefRelToAbs('images/edit_f2.png'), 'Edit', 'Edit').'</a></td>';
        }
        $this->output($title, $edit);        

    }
    function listProj($uHeader = 'default') {
        global $my, $dfconfig;

        if (is_array($this->_listHeader[$uHeader])) {
            $header = &$this->_listHeader[$uHeader];
        } else {
            $header = &$this->_listHeader['default'];
        }

        if (!isset($parent_id)) $parent_id = 0;
//        $this->_proj->setWhere("parent_id=".$parent_id);
//        $this->_proj->setOrder("id");

//        $this->_proj->setIndex('id');
        $attrib = array(
            'action' => 'style="white-space: nowrap;"',
      );
//        $projects = $this->_db->getArray("SELECT * FROM ".$this->_proj->_tbl." ORDER BY id ASC");
        $query = "SELECT * FROM ".$this->_proj->_tbl." ORDER BY id ASC";
        $this->_db->setQuery($query);
        $projects = $this->_db->loadAssocList();
        $this->_table->setFilter(true, true);
        $this->_table->createList($header);        

        if (is_array($projects)) {
            foreach ($projects as $key => $val) {
                foreach ($val as $k => $v) if (is_string($v)) $val[$k] = htmlentities($v);
                $link = getMyURL(array('task','id')).'id='.$val['id'].'&';
                $val['action'] = "";
                if (dfprefs::checkAccess('Write')) {
                    $val['action'] .= '<a href="'.$link.'task=edit"><img src="'.sefRelToAbs('images/edit_f2.png').'" alt=" [edit] " title="edit" width="16" height="16" style="border: none;" /></a>';
                }
                $val['action'] .= '<a href="'.$link.'task=view"><img src="'.sefRelToAbs('images/preview_f2.png').'" alt=" [view] " title="view" width="16" height="16" style="border: none;" /></a>';
        
                $val['projectNum'] = str_pad($val["id"], 4, "0", STR_PAD_LEFT);
                if ($val['parent_id'] != 0) {
                    $val['parent'] = str_pad($val["parent_id"], 4, "0", STR_PAD_LEFT);
                } else {
                    $val['parent'] = 'None';
                }
                
                
                $this->_table->addListRow($val);
    
            }
        }
        $this->_table->finishList($attrib);

        $this->output();
    }


    function output($name="", $extratools="") {
        global $my, $dfconfig, $task;
        if (empty($name)) $name = "Projects";
        $link = getMyURL(array('task','id'));
        echo '<div class="componentheading">'.$name.'</div>';
        echo '<table style="float: right; width: auto;"><tr>';
        echo $extratools;
        if (dfprefs::checkAccess('Write')) {
           echo '<td><a href="'.$link.'task=new">'.HTML_DragonflyProject::caption(sefRelToAbs('images/new_f2.png'), 'New', 'New').'</a></td>';
//           echo '<td><a href="'.$link.'task=paysum"><img src="'._PROJ_IMGPATH.'document-new.png" width="24" height="24" border="0" title="New" alt="New" /></a></td>';
        }
        if ($task != 'list') {
            echo '<td><a href="'.$link.'task=list">'.HTML_DragonflyProject::caption(_PROJ_IMGPATH.'text-x-generic.png', 'List', 'List').'</a></td>';
//            echo '<td><a href="'.$link.'task=paysum"><img src="'._PROJ_IMGPATH.'text-x-generic.png" width="24" height="24" border="0" title="List" alt="List" /></a></td>';
        }
        echo '<td>'.$this->helpImageLink().'</td>';       
        echo '</tr></table>';
        echo '<div style="clear:both;"></div>';
        if (is_object($this->_form)) print $this->_form->toHTML();
        if (is_object($this->_table)) print $this->_table->toHTML();
    }

    function copyright()
{
        echo '<div>com_dfproject &copy; 2005-2006 <a href="http://www.hugllc.com">Hunt Utilities Group, LLC</a></div>';
    }

    function helpImageLink($helptask="help") {
        global $task;
        $url = getMyURL(array('task','id'));
        $link = '<a href="'.$url.'task='.$helptask.'&helptask='.$task.'">';
        $link .= HTML_DragonflyProject::caption(_PROJ_IMGPATH.'help.png', 'Help', 'Help');
//        $link .= '<img src="'._PROJ_IMGPATH.'system-help.png" width="24" height="24" border="0" title="Help" alt="Help" />';
        $link .= '</a>';
        return $link;
    }

    function help($task) {

        $this->output("Help", "", false);
        switch ($task) {
        default:
            $this->help_about();  
            break;      
        }    
    }
    
    function help_about()
{
?>
<h1>About Dragonfly Project</h1>
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

