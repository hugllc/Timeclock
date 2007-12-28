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

function com_install()
{
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
            'name' => 'employeeType',
            'type' => 'SELECT',
            'preftype' => 'ADMINUSER',
            'default' => 'FULLTIME',
            'help' => 'Type of employee',
            'parameters' => array(
                'title' => "Employee Type",
                'options' => array(
                    'FULLTIME' => "Full Time",
                    'PARTTIME' => "Part Time",
                    'TEMPORARY' => "Temporary",
                    'CONTRACTOR' => "Contractor",
              ),
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
            'name' => 'vacationType',
            'type' => 'HIDDEN',
            'preftype' => 'SYSTEM',
            'default' => 'ACRUAL',
            'help' => '',
            'parameters' => array(
                'visible' => false,
          ),
      ),
        array(
            'name' => 'vacationAccrual',
            'type' => 'HIDDEN',
            'preftype' => 'SYSTEM',
            'default' => 'WEEKLY',
            'help' => 'How often vacation is accrued',
            'parameters' => array(
                'visible' => false,
          ),
      ),
        array(
            'name' => 'vacationAccrualTable',
            'type' => 'HIDDEN',
            'preftype' => 'SYSTEM',
            'default' => '',
            'help' => '',
            'parameters' => array(
                'visible' => false,
          ),
      ),
        array(
            'name' => 'decimalPlaces',
            'type' => 'HIDDEN',
            'preftype' => 'SYSTEM',
            'default' => 2,
            'help' => '',
            'parameters' => array(
                'visible' => false,
          ),
      ),
        array(
            'name' => 'maxhours',
            'type' => 'HIDDEN',
            'preftype' => 'SYSTEM',
            'default' => 24,
            'help' => '',
            'parameters' => array(
                'visible' => false,
          ),
      ),
        array(
            'name' => 'periodlength',
            'type' => 'HIDDEN',
            'preftype' => 'SYSTEM',
            'default' => 14,
            'help' => '',
            'parameters' => array(
                'visible' => false,
          ),
      ),
        array(
            'name' => 'periodstart',
            'type' => 'HIDDEN',
            'preftype' => 'SYSTEM',
            'default' => '2000-12-11',
            'help' => '',
            'parameters' => array(
                'visible' => false,
          ),
      ),
        array(
            'name' => 'groupTimeclock',
            'type' => 'HIDDEN',
            'preftype' => 'SYSTEM',
            'default' => -1,
            'help' => '',
            'parameters' => array(
                'visible' => false,
          ),
      ),
        array(
            'name' => 'groupTSummary',
            'type' => 'HIDDEN',
            'preftype' => 'SYSTEM',
            'default' => -1,
            'help' => '',
            'parameters' => array(
                'visible' => false,
          ),
      ),
        array(
            'name' => 'groupTOthers',
            'type' => 'HIDDEN',
            'preftype' => 'SYSTEM',
            'default' => -1,
            'help' => '',
            'parameters' => array(
                'visible' => false,
          ),
      ),
        array(
            'name' => 'groupHolidayHours',
            'type' => 'HIDDEN',
            'preftype' => 'SYSTEM',
            'default' => -1,
            'help' => '',
            'parameters' => array(
                'visible' => false,
          ),
      ),
        array(
            'name' => 'com_label',
            'type' => 'HIDDEN',
            'preftype' => 'SYSTEM',
            'default' => "Timeclock",
            'help' => '',
            'parameters' => array(
                'visible' => false,
                'static' => true,
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
    foreach (array("dfproject", "dfprefs") as $com) {
        print '<div style="font-weight:bold;">Checking for '.$com.'... ';
        if (@include_once($mainframe->getPath('class', 'com_'.$com))) {
            $found[$com] = true;
            print '<span style="color:green;">Found</span>';
        } else {
            $found[$com] = false;
            print '<span style="color:red;">Not Found</span>';
        }
        print "</div>\n";
    }

    if ($found['dfprefs']) {
        print "<h2>Required Pref Definitions</h2>";
        foreach ($prefs as $p) {
            print '<div style="font-weight:bold;">Setting '.$p['name'].'... ';
            $gPref = dfPrefs_define::get($p['name'], null, 'com_dfprojecttimeclock');            
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
