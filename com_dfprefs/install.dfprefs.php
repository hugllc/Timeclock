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
function com_install()
{
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
                'visible' => false,
            ),
        ),
        array(
            'name' => 'cache',
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
            'default' => "dfPrefs",
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
            dfPrefs Installation
            </th>
        </tr>
        </table>
    <h2>Required Components</h2>
<?php
    $found = array();
    foreach (array() as $com) {
        print '<div style="font-weight:bold;">Checking for '.$com.'... ';
        if (@include_once($mainframe->getPath( 'class', 'com_'.$com ))) {
            $found[$com] = true;
            print '<span style="color:green;">Found</span>';
        } else {
            $found[$com] = false;
            print '<span style="color:red;">Not Found</span>';
        }
        print "</div>\n";
    }
    if ($found == array()) print "None";
    
    require_once( $mainframe->getPath( 'class' , 'com_dfprefs' ) );

    print "<h2>Required Pref Definitions</h2>";
    foreach ($prefs as $p) {
        print '<div style="font-weight:bold;">Setting '.$p['name'].'... ';
        $gPref = dfPrefs_define::get($p['name'], null, 'com_dfprefs');            
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