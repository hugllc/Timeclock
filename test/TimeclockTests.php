<?php
/**
 * This runs all of the tests associated with Timeclock
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
 * @copyright  2008 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:Timeclock
 */

/** Test framework */
require_once 'PHPUnit/Framework.php';
/** This is for running tests */
require_once 'PHPUnit/TextUI/TestRunner.php';
/** JoomlaMock stuff */
require_once "JoomlaMock/joomla.php";
require_once "JoomlaMock/mocks/JTable.php";

$dir = dirname(__FILE__);
$dir = substr($dir, 0, strlen($dir) - 5);
$_SESSION["JoomlaMockBaseDir"] = $dir;

/** Test suites */
require_once dirname(__FILE__)."/site/TimeclockSiteTests.php";
require_once dirname(__FILE__)."/admin/TimeclockAdminTests.php";

/** THis includes everything so we get a real idea on the code coverage */
$dirs = array(
    "admin/models", 
    "admin/controllers", 
    "admin/tables/", 
    "admin/views",
    "site/models", 
    "site/controllers", 
    "site/tables/", 
    "site/views",
);
foreach ($dirs as $d) {
    includeRecursive($dir."/".$d, 1);
}
/**
 *  This class runs all of the tests.  This must be done with no errors
 * before the software is ever released.
 *
 * @category   Test
 * @package    TimeclockTest
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:Timeclock
 */
class TimeclockTests
{
    static $_tests = array(
        "TimeclockXmlTest",
    );

    /**
     * main function
     *
     * @return null
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }
 
    /**
     * test suite
     *
     * @return null
     */
    public static function suite()
    {
        PHPUnit_Util_Filter::addDirectoryToFilter(dirname(__FILE__), '.php');
        $suite = new PHPUnit_Framework_TestSuite('Timeclock');
        
        foreach (self::$_tests as $test) {
            include_once dirname(__FILE__).'/'.$test.'.php';
            $suite->addTestSuite($test);
        }
        $suite->addTest(TimeclockAdminTests::suite());
        $suite->addTest(TimeclockSiteTests::suite());
        // Base class tests
        return $suite;
    }
}

/**
 * Recursively include files
 *
 * @param string $dir       The directory to start with
 * @param int    $recursive How many levels to recurse
 * @param int    $level     Internal use only
 *
 * @return null
 */
function includeRecursive($dir, $recursive=0, $level=0)
{
    if ($level > $recursive) return false;
    if(!is_dir($dir)) return false;
    $dh = opendir($dir);
    while (false !== ($file = readdir($dh))) {
        if (substr($file, 0, 1) == ".") continue;
        if (is_file("$dir/$file") && (substr($file, -4) == ".php")) {
            include_once "$dir/$file";
        } else if (is_dir("$dir/$file")) {
            includeRecursive("$dir/$file", $recursive, $level+1);
        }
    }
    closedir($dh);
}

?>
