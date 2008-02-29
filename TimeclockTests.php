<?php
/**
 * This runs all of the tests associated with HUGnetLib.
 *
 * PHP Version 5
 *
 * <pre>
 * HUGnetLib is a library of HUGnet code
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
 * @package    Timeclock
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 *
 */
/** This sets the main function to run */
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'TimeclockTests::main');
}

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

define('JPATH_COMPONENT_SITE', dirname(__FILE__)."/ComTimeclock/site");
define('JPATH_COMPONENT_ADMINISTRATOR', dirname(__FILE__)."/ComTimeclock/admin");
require_once 'ComTimeclock/test/ComTimeclockTests.php';

/**
 *  This class runs all of the tests.  This must be done with no errors
 * before the software is ever released.
 *
 * @category   Test
 * @package    Timeclock
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:HUGnetLib
 */
class TimeclockTests
{
    /**
     * Main function
     *
     * @return null
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }
 
    /**
     * Suite function
     *
     * @return null
     */
    public static function suite()
    {

        PHPUnit_Util_Filter::addFileToFilter(__FILE__);
        PHPUnit_Util_Filter::addDirectoryToFilter(dirname(__FILE__)."/../JoomlaMock/", '.php');
    
        $suite = new PHPUnit_Framework_TestSuite('AllTimeclockTests');

        $suite->addTest(ComTimeclockTests::suite());
 
        return $suite;
    }
}
 
if (PHPUnit_MAIN_METHOD == 'TimeclockTests::main') {
    HUGnetTests::main();
}
?>
