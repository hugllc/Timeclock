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
/**

*/
function getMyURL($skipArgs = array(), $addSep = true) 
{
    $sep = "?";
    $url = $_SERVER['PHP_SELF'];
    $skipArgs[] = 'mosmsg';
    foreach ($_GET as $key => $val) {
        if (array_search($key, $skipArgs) === false) {
            if (!is_array($val)) {
                $url .= $sep.$key.'='.urlencode($val);
                $sep = "&";
            } else {
                foreach ($val as $k => $v) {
                    $url .= $sep.$key.'['.$k.']='.urlencode($v);
                    $sep = "&";
                }
            }
        }
    }
    if ($addSep) $url .= $sep;
    return $url;
}


function getImageLink($link, $image, $alt) 
{
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

function getUser($id) 
{
    global $database;
    
    if ($id == null) return false;
    
    $query = "SELECT * "
    . "\n FROM #__users"
    . "\n WHERE id = '".$id."'"
    . "\n ORDER BY name"
    ;
    $database->setQuery($query);
    $user = $database->loadObjectList();
    if (is_array($user)) list(,$user) = each($user);

    return $user;
}

function getUsers($block = 0) 
{
    global $database;
    
    $query = "SELECT * "
    . "\n FROM #__users"
    . "\n WHERE block = '".$block."'"
    . "\n ORDER BY name"
    ;
    $database->setQuery($query);
    $users = $database->loadObjectList();

    return $users;
}



?>
