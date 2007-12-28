<?php
/**
 * Tests the driver class
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
 * @category   Test
 * @package    TimeclockTest
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:Timeclock
 */
 
/**
 * Mock mainframe class
 *
 * @category   Test
 * @package    TimeclockTest
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:Timeclock
 */
class JoomlaMainFrame
{
    /**
     * Returns the path to the specified item
     *
     * @param string $item   The item to find
     * @param string $option The option to use
     *
     * @return string
     */
    function getPath($item, $option)
    {
        // Get the base directory
        $basedir = explode("/", dirname(__FILE__));
        unset($basedir[count($basedir)-1]);
        $basedir = implode("/", $basedir);
        // This is the file base
        $filebase = substr($option, 4);
        // If this is a class file return this address
        if ($item == "class") return $basedir."/".$option."/".$filebase.".class.php";
        // Don't know what this is.  Return nothing
        return "";
    }    
}


/**
 * Mock table class
 *
 * @category   Test
 * @package    TimeclockTest
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:Timeclock
 */
class MosDBTable
{

}

/**
 * Mock database class
 *
 * Parts of this class fall under the following:
 * <pre>
 *  @version $Id$
 *  @package Joomla
 *  @subpackage Database
 *  @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 *  @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *  Joomla! is free software. This version may have been modified pursuant
 *  to the GNU General Public License, and as distributed it includes or
 *  is derivative of works licensed under the GNU General Public License or
 *  other free or open source software licenses.
 *  See COPYRIGHT.php for copyright notices and details.
 * </pre>
 *
 * @category   Test
 * @package    TimeclockTest
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:Timeclock
 */
class MosDatabase
{
    /**
     * Sets the SQL query string for later execution.
     *
     * This function replaces a string identifier <var>$prefix</var> with the
     * string held is the <var>_table_prefix</var> class variable.
     *
     * @param string $sql    The SQL query
     * @param string $offset The offset to start selection
     * @param string $limit  The number of results to return
     * @param string $prefix The common table prefix
     *
     * @return bool
     */
    function setQuery($sql, $offset = 0, $limit = 0, $prefix='#__') 
    {
    }
    /**
     * Load a list of database objects
     *
     * If <var>key</var> is not empty then the returned array is indexed by the value
     * the database key.  Returns <var>null</var> if the query fails.
     *
     * @param string $key The field name of a primary key
     *
     * @return array If <var>key</var> is empty as sequential list of returned records.
     */
    function loadObjectList($key='') 
    {
    }
}

/**
 * Function to get parameters
 *
 * @param string $var     The global variable to use
 * @param string $name    The name of the index of that global variable
 * @param mixed  $default The default value to send.
 *
 * @return mixed
 */
function mosGetParam($var, $name, $default=null)
{
    return $default;
}

/**************************************************************
 *******        Here starts the important stuff      **********
 **************************************************************/
global $database;
global $mainframe;
global $mosConfig_absolute_path;
$database                = new MosDatabase();
$mainframe               = new JoomlaMainFrame();
$mosConfig_absolute_path = dirname(__FILE__);
?>