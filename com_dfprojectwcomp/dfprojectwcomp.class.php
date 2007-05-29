<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
    $Id: dfprojectwcomp.class.php 417 2006-12-31 16:06:31Z prices $
    @file dfproject.class.php
    
    @verbatim
    Copyright 2005 Hunt Utilities Group, LLC (www.hugllc.com)
    
    dfproject.class.php is part of com_dfproject.

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
// This requires that the session be started.
@session_start();

if(!@include_once $mainframe->getPath( 'class', 'com_dfproject' )) {
    die('com_dfproject is required for com_dfprojecttimeclock');
}

define("_HAVE_DFPROJECT_WCOMP", TRUE);

define("DFPROJECT_CONFIG_FILE", "config.inc.php");
require_once( $mainframe->getPath( 'class', 'com_dfprefs' ) );


define("PLUGIN_WORKERSCOMP_TABLE", '#__dfproject_workers_comp');
define("PROJECT_AREA_NAME", 'dfProject');

class wcCode  extends mosDBTable{
	var $_tbl = '#__dfproject_workers_comp';
    var $_tbl_key = "id";

    var $id;
    var $title;
    var $description;
    var $price;

    function wcCode() {
        global $database;
        $this->_db =& $database;
    }
    
    function loadArray($id) {
        $this->load($id);
        foreach (get_object_vars( $this ) as $key => $val) {
            if (substr( $key, 0, 1 ) != '_') {
                $cache[$key] = $val;
            }
        }
        return ($cache);
    }

    function save( $source, $order_filter='' ) {
        $k = $this->_tbl_key;
        $this->load($source[$this->_tbl_key]);

        
        if (is_null($this->$k)) {
            $update = FALSE;
        } else {
            $update = TRUE;
        }
        if (!$this->bind( $source )) {
            return false;
        }
        if (!$this->check()) {
            return false;
        }                


        if ($update) {
            $ret = $this->_db->updateObject( $this->_tbl, $this, $this->_tbl_key, $updateNulls );
        } else {
            $ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
        }
        if( !$ret ) {
            $this->_error = strtolower(get_class( $this ))."::store failed <br />" . $this->_db->getErrorMsg();
            return false;
        }

        return true;
    }
}
?>
