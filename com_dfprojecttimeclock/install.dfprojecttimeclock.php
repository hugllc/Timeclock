<?php
/**
    $Id: install.dfprojecttimeclock.php 422 2006-12-31 18:24:37Z prices $
    @file install.dfprojecttimeclock.php
    
    @verbatim
    Copyright 2005 Hunt Utilities Group, LLC (www.hugllc.com)
    
    install.dfprojecttimeclock.php is part of com_dfprojecttimeclock.

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

function com_install() {
	global $mainframe;

	$prefs = array(
		array(
			'name' => 'startDate',
			'type' => 'DATE',
			'preftype' => 'ADMINUSER',
			'default' => '',
			'help' => 'Format:  YYYY-MM-DD <br />The date this person started',
			'parameters' => array(
				'title' => "Start Date",
			),
		),
		array(
			'name' => 'vacationHours',
			'type' => 'TEXT',
			'preftype' => 'ADMINUSER',
			'default' => '80',
			'help' => 'This is the number of hours the person gets for paid vacation time. Enter 0 for no paid vacation time.',
			'parameters' => array(
				'size' => 5,
				'maxlength' => 4,
				'title' => "Vacation Hours",
			),
		),
		array(
			'name' => 'sickHours',
			'type' => 'TEXT',
			'preftype' => 'ADMINUSER',
			'default' => '40',
			'help' => 'This is the number of hours the person gets for paid sick time. Enter 0 for no paid sick time.',
			'parameters' => array(
				'size' => 5,
				'maxlength' => 4,
				'title' => "Sick Hours",
			),
		),
		array(
			'name' => 'userHolidayHours',
			'type' => 'YESNO',
			'preftype' => 'ADMINUSER',
			'default' => 0,
			'help' => 'Does this person get holiday hours.',
			'parameters' => array(
				'title' => "Holiday Hours",
			),
		),
		array(
			'name' => 'userTimeclock',
			'type' => 'YESNO',
			'preftype' => 'ADMINUSER',
			'default' => 0,
			'help' => 'Does this person have access to the timeclock',
			'parameters' => array(
				'title' => "Timeclock Access",
			),
		),
		array(
			'name' => 'userTSummary',
			'type' => 'YESNO',
			'preftype' => 'ADMINUSER',
			'default' => 0,
			'help' => 'Does this person have access to the timeclock summaries.',
			'parameters' => array(
				'title' => "Summary Access",
			),
		),
		array(
			'name' => 'userTDebug',
			'type' => 'YESNO',
			'preftype' => 'ADMINUSER',
			'default' => 0,
			'help' => 'Does this person get debug messages about timeclock.',
			'parameters' => array(
				'title' => "Debug Access",
			),
		),
		array(
			'name' => 'userTOthers',
			'type' => 'YESNO',
			'preftype' => 'ADMINUSER',
			'default' => 0,
			'help' => 'Does this person get access to other peoples timecards?',
			'parameters' => array(
				'title' => "Acess to Others Timecards",
			),
		),
		array(
			'name' => 'decimalPlaces',
			'type' => 'HIDDEN',
			'preftype' => 'SYSTEM',
			'default' => 2,
			'help' => '',
			'parameters' => array(
				'visible' => FALSE,
			),
		),
		array(
			'name' => 'maxhours',
			'type' => 'HIDDEN',
			'preftype' => 'SYSTEM',
			'default' => 24,
			'help' => '',
			'parameters' => array(
				'visible' => FALSE,
			),
		),
		array(
			'name' => 'periodlength',
			'type' => 'HIDDEN',
			'preftype' => 'SYSTEM',
			'default' => 14,
			'help' => '',
			'parameters' => array(
				'visible' => FALSE,
			),
		),
		array(
			'name' => 'periodstart',
			'type' => 'HIDDEN',
			'preftype' => 'SYSTEM',
			'default' => '2000-12-11',
			'help' => '',
			'parameters' => array(
				'visible' => FALSE,
			),
		),
		array(
			'name' => 'groupTimeclock',
			'type' => 'HIDDEN',
			'preftype' => 'SYSTEM',
			'default' => -1,
			'help' => '',
			'parameters' => array(
				'visible' => FALSE,
			),
		),
		array(
			'name' => 'groupTSummary',
			'type' => 'HIDDEN',
			'preftype' => 'SYSTEM',
			'default' => -1,
			'help' => '',
			'parameters' => array(
				'visible' => FALSE,
			),
		),
		array(
			'name' => 'groupTOthers',
			'type' => 'HIDDEN',
			'preftype' => 'SYSTEM',
			'default' => -1,
			'help' => '',
			'parameters' => array(
				'visible' => FALSE,
			),
		),
		array(
			'name' => 'groupHolidayHours',
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
			'default' => "Timeclock",
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
			dfProject Timeclock Installation
			</th>
		</tr>
		</table>
	<h2>Required Components</h2>
<?php
	$found = array();
	foreach(array("dfproject", "dfprefs") as $com) {
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
			$gPref = dfPrefs_define::get($p['name'], NULL, 'com_dfprojecttimeclock');			
			$id = $gPref[0]->id;
			$ret = @dfPrefs_define::set($id, $p['name'], $p['default'], $p['type'], $p['preftype'], 'com_dfprojecttimeclock', $p['help'], $p['parameters']);
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
