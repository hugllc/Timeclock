<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
    $Id: admin.dfprojectwcomp.php 404 2006-12-29 21:15:35Z prices $
    @file admin.dfproject.php
    
    @verbatim
    Copyright 2005 Hunt Utilities Group, LLC (www.hugllc.com)
    
    admin.dfproject.php is part of com_dfproject.

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
require_once( $mainframe->getPath( 'admin_html' ) );

$cid = mosGetParam( $_POST, 'cid', array( mosGetParam( $_POST, 'id' ) ) );


switch ($task) {
case 'config':
    $df_config = dfprefs::getSystem();
    HTML_dfprojectwcomp::showConfig($option, $df_config);
    break;
case 'configsave':
    $new = mosGetParam( $_POST, 'df_config', array( ) );

    if (dfprefs::setSystemArray($new)) {
        $msg = "Configuration Saved";
    } else {
        $msg = "Save Failed";
    }
    mosRedirect( "index2.php?option=$option&task=config" , $msg);
    break;
case 'userpref':
	mosRedirect( "index2.php?option=com_dfprefs&task=userpref&area=$option" );
	break;
case 'install':
	require_once("install.dfprojectwcomp.php");
	com_install();
default:
    HTML_dfprojectwcomp::showAbout();
    break;   
}




?>