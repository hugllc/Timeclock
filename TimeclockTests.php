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
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id: TimeclockTests.php 680 2007-12-28 22:55:57Z prices $    
 * @link       https://dev.hugllc.com/index.php/Project:Timeclock
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'TimeclockTests::main');
}

/** Test framework */
require_once 'PHPUnit/Framework.php';
/** This is for running tests */
require_once 'PHPUnit/TextUI/TestRunner.php';


// Joomla stuff
require_once dirname(__FILE__)."/test/JoomlaMock/joomla.php";
require_once dirname(__FILE__)."/test/JoomlaMock/test/JoomlaMockTests.php";


require_once dirname(__FILE__)."/com_dfprefs/test/DfPrefsTests.php";
require_once dirname(__FILE__)."/com_dfproject/test/DfProjectTests.php";
require_once dirname(__FILE__)."/com_dfprojecttimeclock/test/DfProjectTimeclockTests.php";
require_once dirname(__FILE__)."/com_dfprojectbilling/test/DfProjectBillingTests.php";
require_once dirname(__FILE__)."/com_dfprojectwcomp/test/DfProjectWcompTests.php";

/**
 *  This class runs all of the tests.  This must be done with no errors
 * before the software is ever released.
 *
 * @category   Test
 * @package    TimeclockTest
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:Timeclock
 */
class TimeclockTests
{
    /**
     * main function
     *
     * @return void
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }
 
    /**
     * test suite
     *
     * @return void
     */
    public static function suite()
    {
        PHPUnit_Util_Filter::addDirectoryToFilter(dirname(__FILE__)."/test", '.php');
        PHPUnit_Util_Filter::addFileToFilter(__FILE__);
        $suite = new PHPUnit_Framework_TestSuite('Timeclock');

//        $suite->addTestSuite('otherTest');
        // JoomlaMock tests.  These should be first.
        $suite->addTest(JoomlaMockTests::suite());

        // dfprefs tests.  These should be second.
        $suite->addTest(DfPrefsTests::suite());

        // dfproject tests.  These should be third.
        $suite->addTest(DfProjectTests::suite());

        // DfTimeclock tests.
        $suite->addTest(DfProjectTimeclockTests::suite());
        $suite->addTest(DfProjectBillingTests::suite());
        $suite->addTest(DfProjectWcompTests::suite());
 
        return $suite;
    }
}
 
if (PHPUnit_MAIN_METHOD == 'TimeclockTests::main') {
    TimeclockTests::main();
}
?>
