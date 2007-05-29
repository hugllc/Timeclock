<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
    $Id: dfprefs.class.php 459 2007-01-05 23:16:03Z prices $
    @file dfprefs.class.php
    
    @verbatim
    Copyright 2005 Hunt Utilities Group, LLC (www.hugllc.com)
    
    dfprefs.class.php is part of com_dfprefs

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

// This requires that the session be started.
@session_start();

$myInclude = dirname(__FILE__).DIRECTORY_SEPARATOR."include";
$path = ini_get("include_path");
if (stristr($path, $myInclude) === FALSE) {
    ini_set("include_path", $path.PATH_SEPARATOR.$myInclude);
}


class dfPrefs extends mosDBTable {
    /** @var mosDatabase Database connector */
    var $_db = null;

	var $id = null;
    var $name = NULL;
    var $value = NULL;
    var $_area = NULL;
    var $type = NULL;
    var $visible = NULL;

	var $_defaults = array(
		'useredit' => 1,
	);
    

    function dfPrefs( &$db , $cache = TRUE) {

        $this->mosDBTable('#__dfprefs', 'id', $db );
        $this->_db =& $db;
        $this->_cache = $cache;
        $this->_area = mosGetParam( $_REQUEST, 'option', 'unknown' );
    }

    function getArea() {
        return mosGetParam( $_REQUEST, 'option', 'unknown' );
    }

	function cache() {
		global $database;
		if (!isset($_SESSION['dfprefs']['cache'])) {
            $query = "SELECT * FROM #__dfprefs "
               . " WHERE "
               . " area = 'com_dfprefs'"
               . " AND type = 'SYSTEM'"
               . " AND name = 'cache'";
            $database->setQuery( $query );

            $ret = $database->loadObjectList();
			$_SESSION['dfprefs']['cache'] = (bool)unserialize($ret[0]->value);
		}	
		return $_SESSION['dfprefs']['cache'];
	}


    function set($id, $name, $value, $type='USER', $visible=0, $area = NULL ) {
        global $database;
        
		if (is_null($area)) $area = dfprefs::getArea();

        if (dfprefs::cache() && ($_SESSION['dfprefs'][$type][$id][$area][$name] == $value)) {
            return TRUE;
        } else {
        
            $query = "REPLACE into #__dfprefs "
               . " SET "
               . " id = ".(int)$id
               . ", name = '".(string)$name."'"
               . ", value = '".(string)serialize($value)."'"
               . ", area = '".(string)$area."'"
               . ", type = '".(string)$type."'"
               . ", visible = ".(int)$visible;

            $database->setQuery( $query );
            $ret = $database->query();
            // This adds it to the cache
            $_SESSION['dfprefs'][$type][$id][$area][$name] = $value;
            return $ret;
        }            
    }

    function get($id, $name=NULL, $type = NULL, $area=NULL, $visible=NULL) {
    	global $database;
    	
    	if (!dfprefs::cache()) unset($_SESSION['dfprefs'][$type]);
        if (!isset($_SESSION['dfprefs'][$type][$id])) {
            $query = "SELECT * FROM #__dfprefs "
               . " WHERE 1 "
               . " AND id = ".(int)$id;
            if (!is_null($area)) $query .=  " AND area = '".(string)$area."'";
            if (!is_null($type)) $query .= " AND type = '".(string)$type."'";
            if (!is_null($visible)) $query .= " AND visible = ".(int)(bool)$visible;
            $database->setQuery( $query );

            $ret = $database->loadObjectList();

            if (is_null($_SESSION['dfprefs'][$type][$id])) {                    
            	$_SESSION['dfprefs'][$type][$id] = dfprefs_define::getDefaults();
            }

            if (is_array($ret)) {
                foreach($ret as $val) {
                    $_SESSION['dfprefs'][$type][$id][$val->area][$val->name] = unserialize($val->value);
                }        
            }
        }

        if ($area == NULL) return $_SESSION['dfprefs'][$type][$id];
        if ($name == NULL) return $_SESSION['dfprefs'][$type][$id][$area];
        return $_SESSION['dfprefs'][$type][$id][$area][$name];
    }
    
    
    function setUser($name, $value, $visible = 0, $id = NULL, $area = NULL) {
        global $my;
        if (is_null($id)) $id = $my->id;        
  		if (is_null($area)) $area = dfPrefs::getArea();
       
        if ($my->id > 0) {
            dfprefs::set( $id, $name, $value, 'USER', $visible , $area);
        } else {
            $_SESSION['dfprefs']['USER'][$id][$area][$name] = $value;
        }
    
    }


    function getUser($name, $area = NULL, $id=NULL) {
        global $my;
        if (is_null($id)) $id = $my->id;
 		if (is_null($area)) $area = dfPrefs::getArea();
       
        if ($id > 0) {
            $pref = dfprefs::get($id, $name, NULL, $area);
        } else {
            $pref = $_SESSION['dfprefs']['USER'][$id][$area][$name];        
        }

        return $pref;
    }

    function getSystem($name = NULL, $area = NULL) {
        global $my;
 		if (is_null($area)) $area = dfPrefs::getArea();
       
        return dfprefs::get(0, $name, 'SYSTEM', $area);

    }

    function setSystem($name, $value, $area = NULL) {
 		if (is_null($area)) $area = dfPrefs::getArea();
       
        return dfprefs::set( 0 , $name, $value, 'SYSTEM', 0 , $area);

    }

	function setSystemArray($array, $area=NULL){
		$success = TRUE;
		if (!is_array($array)) return FALSE;
    	foreach($array as $pref => $value) {
    		$ret = dfprefs::setSystem($pref, $value, $area);
    		if ($success) $success = $ret; 
    	}
		return $success;
	}

    
	function checkAccess($name, $area=NULL, $id=NULL){
		global $my;
		if (is_null($id)) $id = $my->id;
        if (is_null($area)) $area = dfprefs::getArea();
		//get($id, $name=NULL, $type = NULL, $area=NULL, $visible=NULL)
		$gid = dfPrefs::get(0, "group".$name, NULL, $area);
		if (($gid >= 0) && (is_numeric($gid))) {
    		if ($gid <= $my->gid) return TRUE;
    	}
		if (dfPrefs::get($id, "user".$name, NULL, $area) == 1) return TRUE;
		return FALSE; 
	}    

    function requireAccess($name, $area = NULL, $id=NULL) {

        $access = dfprefs::checkAccess($name, $area, $id);
        if (!$access) {
            mosNotAuth();
        }
        return $access;
    }
    
    function flushCache($type=NULL) {
    	if (is_string($type)) {
     		$_SESSION['dfprefs'][$type] = array();
    	} else {
	    	$_SESSION['dfprefs'] = array();
	  	}
    }
}

class dfPrefs_define extends mosDBTable {
    /** @var mosDatabase Database connector */
    var $_db = null;

	var $id = null;
    var $name = NULL;
    var $type = NULL;
    var $area = NULL;
    var $parameters = NULL;
    

    function dfPrefs_define( ) {
		global $database;

        $this->mosDBTable('#__dfprefs_define', 'id', $database );
        $this->_db =& $db;
        $this->_area = dfPrefs::getArea();
    }

	function getDefaults($area=NULL, $type=NULL) {
		global $database;

        //$area = dfPrefs::getArea();

        $query = "SELECT "
           . " `name`, `default`, `area`, `type` "
           . " FROM #__dfprefs_define "
           . " WHERE 1 ";
        if (!is_null($area)) $query .= " `area` = '".(string)$area."'";
        if (!is_null($type)) $query .= " AND `preftype` = '".(string)$type."'";

        $database->setQuery( $query );

        $ret = $database->loadObjectList();

		$return = array();
		if (is_array($ret)) {
			foreach($ret as $entry) {
				$return[$entry->area][$entry->name] = unserialize($entry->default);
			}
	    }
	    if (is_null($area)) return $return;
	   	return $return[$area];
		
	}

    function set($id, $name, $default, $type, $preftype='USER', $area = NULL, $help='', $parameters=array() ) {
		global $database;        

		if (is_null($area)) $area = dfPrefs::getArea();        
        if (empty($id)) unset($id);        

        $basequery = " name = '".(string)$name."'"
           . ", area = '".(string)$area."'"
           . ", type = '".(string)$type."'"
           . ", preftype = '".(string)$preftype."'"
           . ", help = '".(string)$help."'"
           . ", `default` = '".(string)serialize($default)."'"
           . ", parameters = '".(string)serialize($parameters)."'";


		if (is_null($id)) {
 		    $query = "INSERT INTO #__dfprefs_define "
                . " SET "
                . " id = '".(int)$id."' ,"
                . $basequery;
        } else {
 		    $query = "UPDATE #__dfprefs_define "
                . " SET "
                . $basequery
                . " WHERE id = '".(int)$id."'";
        }

        $database->setQuery($query);
        $ret = $database->query();
    	if ($database->getErrorNum()) {
    		return false;
			if (is_object($this)) {
	    		if ($this->debug) print $database->stderr();
	    	}
    	}

        return $ret;
    }

    function getById( $id ) {
        global $database;
        $query = "SELECT * FROM #__dfprefs_define "
           . " WHERE id = ".$id;
        $database->setQuery( $query );

        $ret = $database->loadObjectList();
    	if ($database->getErrorNum()) {
    		echo $database->stderr();
    		return false;
    	}
    	if (is_array($ret)) {
            $ret = $ret[0];
			$ret->default = unserialize($ret->default);
			$ret->parameters = unserialize($ret->parameters);
    	}

		return $ret;
    
    }


    function get( $name = NULL, $preftype = NULL, $area = NULL, $type = NULL ) {
		global $database;

//		if (is_null($area)) $area = dfPrefs::getArea();        

        $query = "SELECT * FROM #__dfprefs_define "
           . " WHERE 1 ";
		if (!is_null($area)) $query .= " AND area = '".(string)$area."'";
		if (!is_null($name)) $query .= " AND name = '".(string)$name."'";
		if (!is_null($type)) $query .= " AND type = '".(string)$type."'";
		if (!is_null($preftype)) $query .= " AND preftype = '".(string)$preftype."'";

        $database->setQuery( $query );

        $ret = $database->loadObjectList();
    	if ($database->getErrorNum()) {
    		echo $database->stderr();
    		return false;
    	}
    	if (is_array($ret)) {
			foreach($ret as $key => $val) {
		
    			$ret[$key]->default = unserialize($ret[$key]->default);
    			$ret[$key]->parameters = unserialize($ret[$key]->parameters);
			}
//    		if (count($ret) == 1) $ret = $ret[0];
    	}

		return $ret;
    }
}


class dfFileConfig {

    var $file;
    var $html_class;
    var $default_config;
    
    
    function dfFileConfig($default=array(), $file=NULL, $path = NULL) {
        global $mainframe;
        $this->file = $file;
        if (empty($this->file)) $this->file = "config.inc.php";
        $this->default_config = $default;
        if ($path === NULL) {
            $this->path = dirname( $mainframe->getPath( 'class' ) );
        } else {
            $this->path = $path;
        }
    }

    function getConfig() {

        @include($this->path.DIRECTORY_SEPARATOR.$this->file);
        $df_config = $this->mergeConfig($this->default_config, $df_config);
        return $df_config;    
    }

    function mergeConfig($old, $new) {
        if (!is_array($old)) return $new;
        if (!is_array($new)) return $old;
        
        if (count($new) > 0) {
            foreach($new as $key => $val) {
                if (is_array($val) && is_array($old[$key])) {
                    $old[$key] = $this->mergeConfig($old[$key], $val);
                } else {
                    $old[$key] = $val;
                }
            }
        } else {
            $old = $new;        
        }
        return $old;
    }

    function saveConfig($new) {
        if (!is_array($new)) return FALSE;
        $df_config = $this->getConfig();
//        if ($new === NULL) $new = mosGetParam( $_REQUEST, 'df_config', array( ) );
        $df_config = $this->mergeConfig($df_config, $new);
        return $this->writeFile($df_config);
    }

    function writeFile($df_config) {

        $cf = fopen($this->path.DIRECTORY_SEPARATOR.$this->file, "w");
        if ($cf == FALSE) return FALSE;
        fwrite($cf, "<?php\n");
        fwrite($cf, "defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );\n\n");

        $this->writeRaw($cf, $df_config);

        fwrite($cf, "?>\n");

        fclose($cf);    
        return TRUE;   
    }
    
    function writeRaw ($cf, $df_config, $name='$df_config') {
        if (!is_array($df_config)) return;
        foreach($df_config as $key => $val) {
            if (is_array($val)) {
                $this->writeRaw($cf, $val, $name."[".trim($key)."]");
            } else {
                $val = str_replace("'", "\\'", $val);
                fwrite($cf, $name.'['.$key.']=\''.$val."';\n");
            }
        }
        
    }

    function checkGroupAccess($type = "read", $user_id = NULL) {

        $user = &$this->getUser($user_id);

        $config = $this->getConfig();
        if (!isset($config['groupAccess'][$type])) return FALSE;
        if ($config['groupAccess'][$type] < 0) return FALSE;
        return ($user->gid >= $config['groupAccess'][$type]);
    }

    function requireGroupAccess($type = "read", $user_id = NULL) {
        $access = $this->checkAccess($type, $user_id);
        if (!$access) {
            mosNotAuth();
        }
        return $access;
    }
    
    
    function checkUserAccess($type='read', $user_id = NULL) {
        $user = &$this->getUser($user_id);

        $config = $this->getConfig();
        if (is_array($config['userAccess'][$type])) {
            return (bool) in_array($user->id, $config['userAccess'][$type]);
        } else {
            return FALSE;
        }
    }

    function requireUserAccess($type='read', $user_id = NULL) {
        $access = $this->checkUserAccess($type, $user_id);
        if (!$access) {
            mosNotAuth();
        }
        return $access;
    }

    function checkAccess($type = 'read', $user_id = NULL) {
        return ($this->checkGroupAccess($type, $user_id) || $this->checkUserAccess($type, $user_id));
    }

    function requireAccess($type='read', $user_id = NULL) {

        $access = $this->checkAccess($type, $user_id);
        if (!$access) {
            mosNotAuth();
        }
        return $access;
    }

    function getUser($user_id = NULL) {
        global $my, $database;
        
        if ( $user_id === NULL ) {
            return $my;
        } else {
            if (!is_object($this->_users[$user_id])) {
                $query = "SELECT id, gid, name, usertype "
                . "\n FROM #__users"
                . "\n WHERE id = ".$user_id
                ;
                $database->setQuery($query);
                $database->loadObject($this->_users[$user_id]);
            }
            return $this->_users[$user_id];
        }
    }

}



function check_ssl() {
	global $mosConfig_live_site;
    if (trim(strtolower($_SERVER["HTTPS"])) != 'on') {
		$option = mosGetParam($_GET, "option", "");
		if (strlen($option)) $option = "?option=".$option;
        $site = str_replace("http://", "https://", $mosConfig_live_site);
        mosRedirect($site."/index.php".$option);
        exit();
    }
}


?>