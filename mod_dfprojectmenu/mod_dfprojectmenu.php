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
defined('_VALID_MOS') or die('Restricted access');
//die("GOTHERE");

global $dfconfig, $cur_template;

require_once($mainframe->getPath('class', 'com_dfproject'));
@include_once($mainframe->getPath('class', 'com_dfprojecttimeclock'));
@include_once($mainframe->getPath('class', 'com_dfprojectwcomp'));
@include_once($mainframe->getPath('class', 'com_dfprojectbilling'));

$Itemid = mosGetParam($_GET, 'Itemid');
$option = mosGetParam($_REQUEST, 'option');
$task = mosGetParam($_REQUEST, 'task');

$indent = sefRelToAbs('/templates/'.$cur_template.'/images/indent1.png');

if (!defined('_DFPROJECTMENU_PROJECTS')) define('_DFPROJECTMENU_PROJECTS', 'Projects');
if (!defined('_DFPROJECTMENU_WORKERSCOMP')) define('_DFPROJECTMENU_WORKERSCOMP', 'Worker\'s Comp');
if (!defined('_DFPROJECTMENU_TIMECLOCK')) define('_DFPROJECTMENU_TIMECLOCK', 'Timeclock');
if (!defined('_DFPROJECTMENU_BILLING')) define('_DFPROJECTMENU_BILLING', 'Billing');

$linkextra = "";
if (stripos($option, "com_dfproject") === 0) {
    if (!is_null($Itemid)) $linkextra .= "Itemid=".$Itemid;
}

if (strlen($linkextra) > 0) {
    $linkextra = "&".$linkextra;
}
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
<?php
if (dfprefs::checkAccess('Read', 'com_dfproject')) {
    $id = "";
    if ($option == 'com_dfproject') $id = 'id="active_menu"';
    print '<tr align="left"><td>';
    print '<a href="'.sefRelToAbs("index.php?option=com_dfproject".$linkextra).'" class="mainlevel" '.$id.'>'._DFPROJECTMENU_PROJECTS.'</a>';
    print "</td></tr>\n";
}
if (defined('_HAVE_DFPROJECT_BILLING')) {
    if (dfprefs::checkAccess('Read', 'com_dfprojectbilling')) {
        $id = "";
        if ($option == 'com_dfprojectbilling') $id = 'id="active_menu"';
        print '<tr align="left"><td>';
        print '<a href="'.sefRelToAbs("index.php?option=com_dfprojectbilling".$linkextra).'" class="mainlevel" '.$id.'>'._DFPROJECTMENU_BILLING.'</a>';
        print "</td></tr>\n";
    }
}
if (defined('_HAVE_DFPROJECT_WCOMP')) {
    if (dfprefs::checkAccess('Read', 'com_dfprojectwcomp')) {
        $id = "";
        if ($option == 'com_dfprojectwcomp') $id = 'id="active_menu"';
        print '<tr align="left"><td>';
        print '<a href="'.sefRelToAbs("index.php?option=com_dfprojectwcomp".$linkextra).'" class="mainlevel" '.$id.'>'._DFPROJECTMENU_WORKERSCOMP.'</a>';
        print "</td></tr>\n";
    }
}
if (defined('_HAVE_DFPROJECT_TIMECLOCK')) {
    if (dfprefs::checkAccess('Timeclock', "com_dfprojecttimeclock")
        || dfprefs::checkAccess('TSummary', "com_dfprojecttimeclock")
      ) {
        $id = "";
        if ($option == 'com_dfprojecttimeclock') $id = 'id="active_menu"';
        print '<tr align="left"><td>';
        print '<a href="'.sefRelToAbs("index.php?option=com_dfprojecttimeclock".$linkextra).'" class="mainlevel" '.$id.'>'._DFPROJECTMENU_TIMECLOCK.'</a>';
        print "</td></tr>\n";
    }
}
?>
</table>
