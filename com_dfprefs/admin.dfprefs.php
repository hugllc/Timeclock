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
require_once($mainframe->getPath('admin_html'));

$id = mosGetParam($_REQUEST, 'id', null) ;
if (is_null($id)) {
    $cid = mosGetParam($_REQUEST, 'cid', array());
    $id = $cid[0];
}

$area = mosGetParam($_REQUEST, "area", $option);

$HTML = new HTML_dfprefs();

$save = false;
switch ($task) {
case 'config':
    $df_config = dfprefs::getSystem();
    HTML_dfprefs::showConfig($option, $df_config);
    break;
case 'configsave':
    $new = mosGetParam($_POST, 'df_config', array());

    if (dfprefs::setSystemArray($new)) {
        $msg = "Configuration Saved";
    } else {
        $msg = "Save Failed";
    }

    mosRedirect("index2.php?option=$option&task=config&area=$area", $msg);
    break;
case 'new':
    editPrefDefine(null, $option);
    break;
case 'editpref':
    editPrefDefine($id, $option);
    break;
case 'edituser':
    editUser($id, $option);
    break;
case 'save':
    $save = true;
case 'apply':
    $savetype = mosGetParam($_POST, "savetype", "user");
    switch($savetype) {
        case "pref_define":
            if (savePrefDefine($id, $option)) {
                if ($save) mosRedirect("index2.php?option=$option&task=prefs&area=$area");
            }
            break;
        case "user":
        default:
            saveUser($id, $option, $area);
            if ($save) {
                mosRedirect("index2.php?option=$option&area=$area");
            } else {
                mosRedirect("index2.php?option=$option&area=$area&id=$id&task=edituser&hidemainmenu=1");
            }
            break;
    }
    break;
case 'install':
    @require_once("install.dfprefs.php");
    com_install();

case 'about':
    HTML_dfprefs::showAbout();
    break;
case 'prefs':
    showPrefsDefine($option, $task);
    break;   

case 'users':
default:
    showUsers($option, $task);
    break;   
}

function showUsers($option, $task) {
    global $database, $mainframe, $my, $acl, $mosConfig_list_limit;

    $filter_type    = $mainframe->getUserStateFromRequest("filter_type{$option}", 'filter_type', 0);
    $filter_logged    = intval($mainframe->getUserStateFromRequest("filter_logged{$option}", 'filter_logged', 0));
    $limit             = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit));
    $limitstart     = intval($mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0));
    $search         = $mainframe->getUserStateFromRequest("search{$option}", 'search', '');
    $search         = $database->getEscaped(trim(strtolower($search)));
    $where             = array();
    $area            = mosGetParam($_REQUEST, "area", $option);

    if (isset($search) && $search!= "") {
        $where[] = "(a.username LIKE '%$search%' OR a.email LIKE '%$search%' OR a.name LIKE '%$search%')";
    }
    if ($filter_logged == 1) {
        $where[] = "s.userid = a.id";
    } else if ($filter_logged == 2) {
        $where[] = "s.userid IS null";
    }

    // exclude any child group id's for this user
    $pgids = $acl->get_group_children($my->gid, 'ARO', 'RECURSE');

    if (is_array($pgids) && count($pgids) > 0) {
        $where[] = "(a.gid NOT IN (" . implode(',', $pgids) . "))";
    }

    $query = "SELECT COUNT(a.id)"
    . "\n FROM #__users AS a";

    if ($filter_logged == 1 || $filter_logged == 2) {
        $query .= "\n INNER JOIN #__session AS s ON s.userid = a.id";
    }

    $query .= (count($where) ? "\n WHERE " . implode(' AND ', $where) : '')
    ;
    $database->setQuery($query);
    $total = $database->loadResult();

    require_once($GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php');
    $pageNav = new mosPageNav($total, $limitstart, $limit);

    $query = "SELECT a.*, g.name AS groupname"
    . "\n FROM #__users AS a"
    . "\n INNER JOIN #__core_acl_aro AS aro ON aro.value = a.id"    // map user to aro
    . "\n INNER JOIN #__core_acl_groups_aro_map AS gm ON gm.aro_id = aro.aro_id"    // map aro to group
    . "\n INNER JOIN #__core_acl_aro_groups AS g ON g.group_id = gm.group_id";

    if ($filter_logged == 1 || $filter_logged == 2) {
        $query .= "\n INNER JOIN #__session AS s ON s.userid = a.id";
    }

    $query .= (count($where) ? "\n WHERE " . implode(' AND ', $where) : "")
    . "\n GROUP BY a.id"
    ;
    $database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
    $rows = $database->loadObjectList();

    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }

    $template = 'SELECT COUNT(s.userid) FROM #__session AS s WHERE s.userid = %d';
    $n = count($rows);
    for ($i = 0; $i < $n; $i++) {
        $row = &$rows[$i];
        $query = sprintf($template, intval($row->id));
        $database->setQuery($query);
        $row->loggedin = $database->loadResult();
    }

    // get list of Groups for dropdown filter
    $query = "SELECT name AS value, name AS text"
    . "\n FROM #__core_acl_aro_groups"
    . "\n WHERE name != 'ROOT'"
    . "\n AND name != 'USERS'"
    ;
    $types[] = mosHTML::makeOption('0', '- Select Group -');
    $database->setQuery($query);
    $types = array_merge($types, $database->loadObjectList());
    $lists['type'] = mosHTML::selectList($types, 'filter_type', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', "$filter_type");

    // get list of Log Status for dropdown filter
    $logged[] = mosHTML::makeOption(0, '- Select Log Status - ');
    $logged[] = mosHTML::makeOption(1, 'Logged In');
    $lists['logged'] = mosHTML::selectList($logged, 'filter_logged', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', "$filter_logged");

    HTML_dfprefs::showUsers($rows, $pageNav, $search, $option, $lists, $area, $task);
}

function showPrefsDefine($option, $task) {
    global $database, $mainframe, $my, $acl, $mosConfig_list_limit;

    $filter_type    = $mainframe->getUserStateFromRequest("filter_type{$option}", 'filter_type', 0);
    $filter_logged    = intval($mainframe->getUserStateFromRequest("filter_logged{$option}", 'filter_logged', 0));
    $limit             = intval($mainframe->getUserStateFromRequest("viewlistlimit", 'limit', $mosConfig_list_limit));
    $limitstart     = intval($mainframe->getUserStateFromRequest("view{$option}limitstart", 'limitstart', 0));
    $search         = $mainframe->getUserStateFromRequest("search{$option}", 'search', '');
    $search         = $database->getEscaped(trim(strtolower($search)));
    $where             = array();


    // Exclude hidden variables
    $where[] = "(a.type <> 'HIDDEN')";
    $where[] = "(a.area = 'Local Preferences')";

    if (isset($search) && $search!= "") {
        $where[] = "(a.name LIKE '%$search%')";
    }

    $query = "SELECT COUNT(a.id)"
    . "\n FROM #__dfprefs_define AS a";

    $query .= (count($where) ? "\n WHERE " . implode(' AND ', $where) : '')
    ;
    $database->setQuery($query);
    $total = $database->loadResult();

    require_once($GLOBALS['mosConfig_absolute_path'] . '/administrator/includes/pageNavigation.php');
    $pageNav = new mosPageNav($total, $limitstart, $limit);

    $query = "SELECT a.* "
    . "\n FROM #__dfprefs_define AS a";


    $query .= (count($where) ? "\n WHERE " . implode(' AND ', $where) : "")
    . "\n"
    ;

    $database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
    $rows = $database->loadObjectList();

    if ($database->getErrorNum()) {
        echo $database->stderr();
        return false;
    }
    HTML_dfprefs::showPrefsDefine($rows, $pageNav, $search, $option, $lists, $task);
}


function editUser($uid='0', $option='com_dfprefs') {
    global $database, $my;

    $row = new mosUser($database);
    // load the row from the db table
    $row->load((int)$uid);

    if ($uid) {
        $query = "SELECT *"
        . "\n FROM #__contact_details"
        . "\n WHERE user_id = $row->id"
        ;
        $database->setQuery($query);
        $contact = $database->loadObjectList();
    } else {
        $contact     = null;
        $row->block = 0;
    }

    $area = mosGetParam($_REQUEST, 'area', null);
    $prefs = dfPrefs_define::get(null, "USER");
    $prefs += dfPrefs_define::get(null, "ADMINUSER");

    // check to ensure only super admins can edit super admin info
    if (($my->gid < 25) && ($row->gid == 25)) {
        mosRedirect('index2.php?option=com_users', _NOT_AUTH);
    }
    
    dfprefs::flushCache();        
    $values = dfprefs::get($uid);

    HTML_dfprefs::editUser($option, $row, $prefs, $values, $area);

}

function saveUser($uid, $option) {
    global $database, $dfprefs;
    
    $newprefs = mosGetParam($_POST, 'admin_dfprefs', array());

    foreach ($newprefs as $area => $prefs) {
        foreach ($prefs as $name => $value) {
            $ret = dfprefs::set($uid, $name, $value, 'ADMINUSER', 1, $area);
            var_dump($ret);
        }
    }

    $newprefs = mosGetParam($_POST, 'dfprefs', array());
    
    foreach ($newprefs as $area => $prefs) {
        foreach ($prefs as $name => $value) {
            $ret = dfprefs::set($uid, $name, $value, 'USER', 1, $area);
            var_dump($ret);
        }
    }    
}

function editPrefDefine($id, $option='com_dfprefs') {
    global $database, $my, $dfprefs, $option;

    if (!is_null($id)) {
        $row = dfprefs_define::getById($id);
    }
    HTML_dfprefs::editPrefDefine($option, $row);

}

function savePrefDefine($id, $option) {
    global $database, $dfprefs;
    
    if (empty($id)) unset($id);
    $newprefs = mosGetParam($_POST, 'dfprefs_define', array());
    
    if (!is_array($newprefs['parameters'])) $newprefs['parameters'] = array();

    switch($newprefs['type']) {
        case 'TEXT':
            $newprefs['parameters'] += mosGetParam($_POST, 'dfprefs_define_text', array());
            break;
        case 'YESNO':
            $newprefs['parameters'] += mosGetParam($_POST, 'dfprefs_define_yesno', array());
            break;
        case 'DATE':
            $newprefs['parameters'] += mosGetParam($_POST, 'dfprefs_define_date', array());
            break;
    }

    return dfprefs_define::set($id, $newprefs['name'], $newprefs['default'], $newprefs['type'], $newprefs['preftype'], $newprefs['area'], $newprefs['help'], $newprefs['parameters']);

    
}



?>