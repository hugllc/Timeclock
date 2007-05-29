<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
    $Id: dfprojectbilling.php 403 2006-12-29 21:15:24Z prices $
    @file dfproject.php
    
    @verbatim
    Copyright 2005 Hunt Utilities Group, LLC (www.hugllc.com)
    
    dfproject.php is part of com_dfproject.

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

require_once( $mainframe->getPath( 'class' ) );
require_once( $mainframe->getPath( 'front_html' ) );
require_once( $mainframe->getPath( 'class' , 'com_dfprefs') );

check_ssl();

$HTML = new HTML_DragonflyProject_Billing($database);

$prefs = new dfPrefs($database);

if (dfprefs::requireAccess('Read')) {
    switch ($task) {
    case 'new':
    case 'new_billings':
        if (dfprefs::requireAccess('Write')) {
            if ($my->id > 0) {
                $HTML->add();
                break;
            } // else fall through to view
        }
    case 'edit':
    case 'edit_billings':
        if (dfprefs::requireAccess('Write')) {
            if ($my->id > 0) {
                $id   = mosGetParam( $_REQUEST, 'id', NULL );
                if (empty($id)) mosRedirect(getMyURL(array('task','id'))."task=customers");
                $HTML->edit($id);
                break;
            } // else fall through to view
        }
    case 'view':
    case 'view_billings':
        $id   = mosGetParam( $_REQUEST, 'id', NULL );
        if (empty($id)) mosRedirect(getMyURL(array('task','id'))."task=customers");
        $HTML->view($id);
        break;
    case 'reports':
        if (dfprefs::requireAccess('Reports')) {
            $id   = mosGetParam( $_REQUEST, 'id', NULL );
            $Date   = mosGetParam( $_REQUEST, 'Date', NULL );
            if (empty($id)) mosRedirect(getMyURL(array('task','id'))."task=customers");
            $report = mosGetParam( $_REQUEST, 'report', 'default' );
            $HTML->setPeriod($Date);
            $HTML->reports($id, $report);
        }
        break;
    case 'help':
    case 'billings_help':
        $HTML->help($helptask);
        break;

    default:
    case 'list':
    case 'customers':
        $listheader = mosGetParam($_REQUEST, 'listheader', 'default');
        $HTML->show($listheader);
        break;
    }


}
$HTML->copyright();

?>
