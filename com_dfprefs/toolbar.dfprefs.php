<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
    $Id: toolbar.dfprefs.php 426 2007-01-01 02:26:11Z prices $
    @file toolbar.dfprefs.php
    
    @verbatim
    Copyright 2005 Hunt Utilities Group, LLC (www.hugllc.com)
    
    toolbar.dfprefs.php is part of com_dfprefs

    com_dfprefs is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    com_dfprefs is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Foobar; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
    @endverbatim
*/

require_once( $mainframe->getPath( 'toolbar_html' ) );


$task = mosGetParam( $_REQUEST, 'task', '' );

switch($task) {
case 'config':  
    dfprefsToolBar::CONFIG_MENU();
    break;
case 'install':
case 'about':
    dfprefsToolBar::ABOUT_MENU();
    break;
case 'apply':
case 'new':
case 'editpref':
case 'edituser':
    dfprefsToolBar::EDIT_MENU();
    break;
case 'prefs': 
    dfprefsToolBar::PREFS_MENU();
    break;
default:
    dfprefsToolBar::USERS_MENU();
    break;
}

?>