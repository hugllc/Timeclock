<?php
/**
    $Id: install.dfprojectbilling.php 422 2006-12-31 18:24:37Z prices $
    @file install.dfproject.php
    
    @verbatim
    Copyright 2005 Hunt Utilities Group, LLC (www.hugllc.com)
    
    install.dfproject.php is part of com_dfproject.

    com_dfproject is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    com_dfproject is distributed in the hope that it will be useful,
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
			'name' => 'userRead',
			'type' => 'YESNO',
			'preftype' => 'ADMINUSER',
			'default' => 0,
			'help' => 'Does this person have read access to customers?',
			'parameters' => array(
				'title' => "Read Access",
			),
		),
		array(
			'name' => 'userWrite',
			'type' => 'YESNO',
			'preftype' => 'ADMINUSER',
			'default' => 0,
			'help' => 'Does this person have write access to customers?',
			'parameters' => array(
				'title' => "Write Access",
			),
		),
		array(
			'name' => 'userReports',
			'type' => 'YESNO',
			'preftype' => 'ADMINUSER',
			'default' => 0,
			'help' => 'Does this person have access to view reports?',
			'parameters' => array(
				'title' => "Report Access",
			),
		),
		array(
			'name' => 'userDebug',
			'type' => 'YESNO',
			'preftype' => 'ADMINUSER',
			'default' => 0,
			'help' => 'Does this person get debug messages.',
			'parameters' => array(
				'title' => "Debug Access",
			),
		),
		array(
			'name' => 'groupRead',
			'type' => 'HIDDEN',
			'preftype' => 'SYSTEM',
			'default' => -1,
			'help' => '',
			'parameters' => array(
				'visible' => FALSE,
			),
		),
		array(
			'name' => 'groupWrite',
			'type' => 'HIDDEN',
			'preftype' => 'SYSTEM',
			'default' => -1,
			'help' => '',
			'parameters' => array(
				'visible' => FALSE,
			),
		),
		array(
			'name' => 'groupReports',
			'type' => 'HIDDEN',
			'preftype' => 'SYSTEM',
			'default' => -1,
			'help' => '',
			'parameters' => array(
				'visible' => FALSE,
			),
		),
		array(
			'name' => 'groupDebug',
			'type' => 'HIDDEN',
			'preftype' => 'SYSTEM',
			'default' => -1,
			'help' => '',
			'parameters' => array(
				'visible' => FALSE,
			),
		),
		array(
			'name' => 'debug',
			'type' => 'HIDDEN',
			'preftype' => 'SYSTEM',
			'default' => 0,
			'help' => '',
			'parameters' => array(
				'visible' => FALSE,
			),
		),
		array(
			'name' => 'com_label',
			'type' => 'HIDDEN',
			'preftype' => 'SYSTEM',
			'default' => "Billing",
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
			dfProject Billing Installation
			</th>
		</tr>
		</table>
	<h2>Required Components</h2>
<?php
	$found = array();
	foreach(array("dfprefs", "dfproject") as $com) {
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

	if ($found['dfprefs']) {
		print "<h2>Required Pref Definitions</h2>";
		foreach ($prefs as $p) {
    		print '<div style="font-weight:bold;">Setting '.$p['name'].'... ';
			$gPref = dfPrefs_define::get($p['name'], NULL, 'com_dfprojectbilling');			
			$id = $gPref[0]->id;
			$ret = @dfPrefs_define::set($id, $p['name'], $p['default'], $p['type'], $p['preftype'], 'com_dfprojectbilling', $p['help'], $p['parameters']);
        	if ($ret) {
        		print '<span style="color:green;">Succeeded</span>';
        	} else {
        		print '<span style="color:red;">Failed</span>';
        	}
        	print "</div>\n";
        }
			
	}
?>
	</div>
<?php
}
?>