<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
    $Id: dfproject.php 405 2006-12-29 21:15:51Z prices $
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

$option = mosGetParam( $_REQUEST, 'option' ) ;

switch ($task) {

    case 'save':
        saveUser();
        mosRedirect("index.php?option=".$option);
    default:
        if (dfprefs::getUser("useredit", "com_dfprefs")) {
            editUser($option);
        } else {
            mosNotAuth();
        }
        break;
}

function editUser($option) {
	global $my;

	$area = mosGetParam($_REQUEST, 'area', NULL);
	$prefs = dfPrefs_define::get(NULL, "USER");

	// check to ensure only super admins can edit super admin info
	if ( ( $my->gid < 25 ) && ( $row->gid == 25 ) ) {
		mosRedirect( 'index2.php?option=com_users', _NOT_AUTH );
	}
	
	dfprefs::flushCache();
	$values = dfprefs::get($my->id);

	HTML_dfprefs::editUser($option, $my, $prefs, $values, $area);

}

function saveUser() {
	global $my;
	$newprefs = mosGetParam($_POST, 'dfprefs', array());
	
	foreach($newprefs as $area => $prefs) {
    	foreach($prefs as $name => $value) {
    		$ret = dfprefs::set($my->id, $name, $value, 'USER', 1, $area );
    		var_dump($ret);
    	}
    }	
}


HTML_dfprefs::copyright();

?>
