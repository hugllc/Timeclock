<?php
/**

*/

	function getMyURL($skipArgs = array(), $addSep = TRUE) {
		$sep = "?";
		$url = $_SERVER['PHP_SELF'];
		$skipArgs[] = 'mosmsg';
		foreach($_GET as $key => $val) {
			if (array_search($key, $skipArgs) === FALSE) {
				if (!is_array($val)) {
					$url .= $sep.$key.'='.urlencode($val);
					$sep = "&";
				} else {
					foreach($val as $k => $v) {
						$url .= $sep.$key.'['.$k.']='.urlencode($v);
						$sep = "&";
					}
				}
			}
		}
		if ($addSep) $url .= $sep;
		return $url;
	}


    function getImageLink($link, $image, $alt) {
        return '<a href="'.$link.'"><img src="'.$image.'" title="'.$alt.'" alt="'.$alt
            .'" style="border: none;"/></a>';
    }
    
	function getReturnTo() 
	{
		$returnTo = (isset($_REQUEST['returnTo'])) ? $_REQUEST['returnTo'] : $_SERVER['HTTP_REFERER'];
		if (trim(strtolower($returnTo)) == trim(strtolower($_SERVER['REQUEST_URI']))
			|| empty($returnTo))
		{
			$returnTo = $_SERVER['PHP_SELF'];
		} 
		return($returnTo);
	}

    function getUser($id) {
        global $database;
        
        if ($id == NULL) return FALSE;
        
        $query = "SELECT * "
        . "\n FROM #__users"
        . "\n WHERE id = '".$id."'"
        . "\n ORDER BY name"
        ;
        $database->setQuery( $query );
        $user = $database->loadObjectList();
        if (is_array($user)) list(,$user) = each($user);

        return $user;
    }

    function getUsers($block = 0) {
        global $database;
        
        $query = "SELECT * "
        . "\n FROM #__users"
        . "\n WHERE block = '".$block."'"
        . "\n ORDER BY name"
        ;
        $database->setQuery( $query );
        $users = $database->loadObjectList();

        return $users;
    }
	/**
 *      Returns seconds as years days hours minutes seconds
 */



?>
