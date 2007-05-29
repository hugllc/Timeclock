<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
    $Id: install.dfprefs.php 448 2007-01-05 22:33:07Z prices $
    @file install.dfprefs.php
    
    @verbatim
    Copyright 2005 Hunt Utilities Group, LLC (www.hugllc.com)
    
    install.dfprefs.php is part of com_dfprefs

    com_dfprefs is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    com_dfprefs is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Foobar; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
    @endverbatim
*/
function com_install() {
	global $mainframe;

	$prefs = array(
		array(
			'name' => 'useredit',
			'type' => 'YESNO',
			'preftype' => 'ADMINUSER',
			'default' => 1,
			'help' => 'Can this user edit their own preferences? User editable preferences are marked in <span style="color: red;">red</span> in their help text.',
			'parameters' => array(
				'title' => "Allow User Edit",
			),
		),
		array(
			'name' => 'debug',
			'type' => 'HIDDEN',
			'preftype' => 'SYSTEM',
			'default' => -1,
			'help' => '',
			'parameters' => array(
				'visible' => FALSE,
			),
		),
		array(
			'name' => 'cache',
			'type' => 'HIDDEN',
			'preftype' => 'SYSTEM',
			'default' => -1,
			'help' => '',
			'parameters' => array(
				'visible' => FALSE,
			),
		),
		array(
			'name' => 'com_label',
			'type' => 'HIDDEN',
			'preftype' => 'SYSTEM',
			'default' => "dfPrefs",
			'help' => '',
			'parameters' => array(
				'visible' => FALSE,
				'static' => TRUE,
			),
		),

	);			
?>	
	<div style="text-align: left; margin-bottom: 50px;">

		<table class="adminheading">
		<tr>
			<th class="install">
			dfPrefs Installation
			</th>
		</tr>
		</table>
	<h2>Required Components</h2>
<?php
	$found = array();
	foreach(array() as $com) {
    	print '<div style="font-weight:bold;">Checking for '.$com.'... ';
    	if (@include_once($mainframe->getPath( 'class', 'com_'.$com ))) {
    		$found[$com] = TRUE;
    		print '<span style="color:green;">Found</span>';
    	} else {
    		$found[$com] = FALSE;
    		print '<span style="color:red;">Not Found</span>';
    	}
    	print "</div>\n";
	}
	if ($found == array()) print "None";
    
    require_once( $mainframe->getPath( 'class' , 'com_dfprefs' ) );

	print "<h2>Required Pref Definitions</h2>";
	foreach ($prefs as $p) {
		print '<div style="font-weight:bold;">Setting '.$p['name'].'... ';
		$gPref = dfPrefs_define::get($p['name'], NULL, 'com_dfprefs');			
		$id = $gPref[0]->id;
		$ret = @dfPrefs_define::set($id, $p['name'], $p['default'], $p['type'], $p['preftype'], 'com_dfprefs', $p['help'], $p['parameters']);
    	if ($ret) {
    		print '<span style="color:green;">Succeeded</span>';
    	} else {
    		print '<span style="color:red;">Failed</span>';
    	}
    	print "</div>\n";
    }
			
?>
	</div>
<?php
}
?>