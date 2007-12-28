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
require_once( $mainframe->getPath( 'class' ) );
require_once( $mainframe->getPath( 'front_html' ) );
require_once( $mainframe->getPath( 'class' , 'com_dfprefs') );


check_ssl();


$HTML = new HTML_DragonflyProject($database);
$project = new project($database);

$prefs = new dfPrefs($database);

if (dfprefs::requireAccess('Read')) {
    switch ($task) {
    case 'adduser':
        if (dfprefs::requireAccess('Write')) {
            $user_id   = mosGetParam( $_REQUEST, 'user_id', null );
            $id   = mosGetParam( $_REQUEST, 'id', null );
            if (($id !== null) && ($user_id !== null)) {
                $project->addUser($user_id, $id);
            }
            header("Location:".getReturnTo());
        }
        break;
    case 'removeuser':
        if (dfprefs::requireAccess('Write')) {
            $user_id   = mosGetParam( $_REQUEST, 'user_id', null );
            $id   = mosGetParam( $_REQUEST, 'id', null );
            if (($id !== null) && ($user_id !== null)) {
                $project->removeUser($user_id, $id);
            }
            header("Location:".getReturnTo());
        }
        break;
    case 'new':
        if (dfprefs::requireAccess('Write')) {
            if ($my->id > 0) {
                $HTML->add();
                break;
            } // else fall through to view
        }
    case 'edit':
        if (dfprefs::requireAccess('Write')) {
            if ($my->id > 0) {
                $HTML->edit();
                break;
            } // else fall through to view
        }
    case 'view':
        $id = mosGetParam($_REQUEST, 'id', null);        
        if (empty($id)) mosRedirect(getMyURL(array('task','id')));
        $HTML->view($id);
        break;
    case 'help':
        $HTML->help($helptask);
        break;

    default:
        $listheader = mosGetParam($_REQUEST, 'listheader', 'default');
        $HTML->listProj($listheader);
        break;   
    }


}
$HTML->copyright();

?>
