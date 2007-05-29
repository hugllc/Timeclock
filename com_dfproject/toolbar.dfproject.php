<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
    $Id: toolbar.dfproject.php 51 2006-05-14 20:49:33Z prices $
    @file toolbar.dfproject.php
    
    @verbatim
    Copyright 2005 Hunt Utilities Group, LLC (www.hugllc.com)
    
    toolbar.dfproject.php is part of com_dfproject.

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

require_once( $mainframe->getPath( 'toolbar_html' ) );


$task = mosGetParam( $_REQUEST, 'task', '' );

switch($task) {
case 'config':  
    dfprojectToolBar::CONFIG_MENU();
    break;
case 'about':
default:
    dfprojectToolBar::ABOUT_MENU();
    break;
}

?>