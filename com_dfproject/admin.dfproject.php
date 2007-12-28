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

$cid = mosGetParam($_POST, 'cid', array(mosGetParam($_POST, 'id')));


switch ($task) {
case 'config':
    $df_config = dfprefs::getSystem();
    HTML_dfproject::showConfig($option, $df_config);
    break;
case 'configsave':
    $new = mosGetParam($_POST, 'df_config', array());

    if (dfprefs::setSystemArray($new)) {
        $msg = "Configuration Saved";
    } else {
        $msg = "Save Failed";
    }
    mosRedirect("index2.php?option=$option&task=config", $msg);
    break;
case 'userpref':
    mosRedirect("index2.php?option=com_dfprefs&task=userpref&area=$option");
    break;
case 'install':
    @require_once("install.dfproject.php");
    com_install();
default:
    HTML_dfproject::showAbout();
    break;   
}




?>