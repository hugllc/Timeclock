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
defined('_VALID_MOS') or die('Restricted access');
//die("GOTHERE");

require_once($mainframe->getPath('class', 'com_dfproject'));
require_once($mainframe->getPath('class', 'com_dfprojecttimeclock'));

//$return = mosGetParam($_SERVER, 'REQUEST_URI', null);
// converts & to &amp; for xtml compliance
//$return = str_replace('&', '&amp;', $return);
/*
$registration_enabled     = $mainframe->getCfg('allowUserRegistration');
$message_login             = $params->def('login_message', 0);
$message_logout         = $params->def('logout_message', 0);
$pretext                 = $params->get('pretext');
$posttext                 = $params->get('posttext');
$login                     = $params->def('login', $return);
$logout                 = $params->def('logout', $return);
$name                     = $params->def('name', 1);
$greeting                 = $params->def('greeting', 1);

$form_submit_loc = str_replace("http://", "https://", $mosConfig_live_site)."/";
$form_submit_loc .= sefRelToAbs('index.php');
*/

if (dfprefs::checkAccess('Timeclock', "com_dfprojecttimeclock") && ($my->id > 0)) {
    $timesheet = new timesheet();
    $startdate = dfprefs::getUser("startDate", "com_dfprojecttimeclock");
    $types = array("Hours" => null, "Paid Time Off" => "VACATION", "Sick Time" => "SICK");
    $typesMax = array("Hours" => null, "Paid Time Off" => "vacationHours", "Sick Time" => "sickHours");
    foreach ($types as $label => $type) {
        $query = " FROM #__dfproject_timesheet "
                . " LEFT JOIN #__dfproject on #__dfproject.id = #__dfproject_timesheet.project_id "
                . " WHERE "
                . " #__dfproject_timesheet.Date >= '".date("Y-01-01")."' "
                . " AND #__dfproject_timesheet.Date >= '".$startdate."' "
                . " AND (#__dfproject_timesheet.user_id='".$my->id."' ";

        if (!is_null($type)) {
            $query .= " AND #__dfproject.Type='".$type."' ";
            $query .= " AND (#__dfproject.status = 'SPECIAL' ";
            $query .= " OR #__dfproject.status = 'ACTIVE')";
        } else {
            if (dfprefs::checkAccess('HolidayHours', "com_dfprojecttimeclock")) {
                $query .= " OR #__dfproject.Type = 'HOLIDAY'";
            } else {
                $query .= " AND #__dfproject.Type <> 'HOLIDAY'";            
            }
        }
        $query .= ")";
        $database->setQuery("SELECT SUM(Hours) as Hours ".$query);
        $res = $database->loadAssocList();

        $hours = ($res[0]['Hours'] > 0) ? $res[0]['Hours'] : 0;
        if (is_null($type)) {
            print "<div style=\"text-align: left;\"><b>".$label.":</b> ".$hours." h</div>\n";
            print "<div style=\"text-align: left;\"><b>".$label."/week:</b> ".round($hours / (date("z")/7), 2)." h</div>\n";
        } else {
            $database->setQuery("SELECT #__dfproject.id as id ".$query);
            $idres = $database->loadAssocList();
            if (is_array($idres)) {
                foreach ($idres as $id) {
                    if (project::userOnProject($my->id, $id['id'])) {
/*
                        if (!is_null($typesMax[$label])) {
                            $max = dfprefs::getUser($typesMax[$label], "com_dfprojecttimeclock");
                            if (empty($max)) continue;
                            $hours .= " / ".$max;
                        }
*/
                        print "<div style=\"text-align: left;\"><b>".$label.":</b> ";
                        if ($type == "VACATION") {
                            $vh = $timesheet->getVacation();
                            if ($vh > 0) {
                                if ($hours < $vh) {
                                    print $hours."/".$vh;
                                } else {
                                    print '<span style="color:black; background: red;">'.$hours."/".$vh."</span>";
                                }
                            } else {
                                print $hours;
                            }
                        } else {
                            print $hours;                        
                        }
                        print " h</div>\n";
                        break;
                    }
                }
            }
        }
    }
    $service = $timesheet->getServiceLength(time(), $my->id, "y");
    print "<div style=\"text-align: left;\"><b>Service Length:</b> ".round($service, 2)." y</div>\n";
}
?>
