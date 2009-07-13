<?php
/**
 * Tests the driver class
 *
 * PHP Version 5
 *
 * <pre>
 * ComTimeclock is a Joomla application to keep track of employee time
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
 * @package    ComTimeclockTest
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock:JoomlaUI
 */
/** Require the JoomlaMock stuff */
require_once dirname(__FILE__).'/../../JoomlaMock/joomla.php';
require_once dirname(__FILE__).'/../../JoomlaMock/testCases/JModelTest.php';
require_once dirname(__FILE__).'/../../../admin/models/projects.php';

/**
 * Test class for driver.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:25.
 *
 * @category   Test
 * @package    ComTimeclockTest
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock:JoomlaUI
 */
class ComTimeclockAdminModelProjectsTest extends JModelTest
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return null
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->sqlFile = array(
            dirname(__FILE__)."/../../../admin/install/timeclock_projects.sql",
            dirname(__FILE__)."/../../../admin/install/timeclock_users.sql",
            dirname(__FILE__)."/../../../admin/install/timeclock_prefs.sql",
            dirname(__FILE__)."/../../../admin/install/timeclock_timesheet.sql",
            dirname(__FILE__)."/../../../admin/install/timeclock_customers.sql",
            dirname(__FILE__)."/users.sql",
        );
        $this->o = new TimeclockAdminModelProjects();
        parent::setUp();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return null
     *
     * @access protected
     */
    protected function tearDown()
    {
        parent::tearDown();
        unset($this->o);
    }

    /**
     * Data provider
     *
     * @return array
     */
    public static function dataGetDataCache()
    {
        return array(
        );
    }
    /**
     * Data provider
     *
     * @return array
     */
    public static function dataStore()
    {
        return array(
            array(
                array(
                    "name" => "a",
                    "description" => "b",
                    "created_by" => "c",
                    "manager" => "z",
                    "research" => "d",
                    "type" => "e",
                    "parent_id" => "f",
                    "wcCode1" => "1",
                    "wcCode2" => "2",
                    "wcCode3" => "3",
                    "wcCode4" => "4",
                    "wcCode5" => "5",
                    "wcCode6" => "6",
                    "customer" => "h",
                    "checked_out" => "i",
                    "checked_out_time" => "j",
                    "published" => 1,
                ),
                "post",
                array(
                    "id" => null,
                    "name" => "a",
                    "description" => "b",
                    "created_by" => 62,
                    "manager" => "z",
                    "research" => "d",
                    "type" => "e",
                    "parent_id" => "f",
                    "wcCode1" => "1",
                    "wcCode2" => "2",
                    "wcCode3" => "3",
                    "wcCode4" => "4",
                    "wcCode5" => "5",
                    "wcCode6" => "6",
                    "customer" => "h",
                    "checked_out" => "i",
                    "checked_out_time" => "j",
                    "published" => 1,
                ),
                "store",
            ),
        );
    }
    /**
     * Data provider
     *
     * @return array
     */
    public static function dataStoreRet()
    {
        return array(
            array(
                null,
                "store",
                13,
                array(
                    "id" => 13
                )
            ),
            array(
                "bind",
                "store",
                false,
                array(
                    "id" => 13
                )
            ),
            array(
                "store",
                "store",
                false,
                array(
                    "id" => 13
                )
            ),

            array(
                null,
                "adduser",
                true,
                array(
                    "id" => 13,
                    "projid" => 10
                )
            ),
            array(
                "bind",
                "adduser",
                false,
                array(
                    "id" => 13,
                    "projid" => 10
                )
            ),
            array(
                "check",
                "adduser",
                false,
                array(
                    "id" => 13,
                    "projid" => 10
                )
            ),

            array(
                null,
                "removeuser",
                true,
                array(
                    "id" => 13,
                    "remove_user_id" => 10
                )
            ),
            array(
                "bind",
                "removeuser",
                false,
                array(
                    "id" => 13,
                    "remove_user_id" => 10
                )
            ),

        );
    }

    /**
     * Data provider for testFiles
     *
     * @return array()
     */
    static public function dataGetUserProjects()
    {
        $data1 = new SimpleXMLElement(dirname(__FILE__).DS."ProjectReturn01.xml", NULL, TRUE);

        return array(
            array(
                dirname(__FILE__).DS."ProjectDataSet01.xml",
                5,
                null,
                null,
                 joomlaMockXMLToArray($data1),
            ),
        );
    }

    /**
     * Tests the users name
     *
     * @param string $file The file name to check
     *
     * @return null
     *
     * @dataProvider dataGetUserProjects()
     */
    public function testGetUserProjects($preload, $oid, $limitstart, $limit, $expect)
    {
        $this->_db->joomlaMockPreloadXML($preload);
        $ret = $this->o->getUserProjects($oid, $limitstart, $limit);
        $this->assertEquals($expect, $ret);
    }

    /**
     * Data provider for testFiles
     *
     * @return array()
     */
    static public function dataGetProjectUsers()
    {
        $res[] = toStdClass(
            array(
                "proj_id"       => "1",
                "id"            => "5",
                "name"          => null,
                "username"      => null,
                "email"         => null,
                "password"      => null,
                "usertype"      => null,
                "block"         => null,
                "sendEmail"     => null,
                "gid"           => null,
                "registerDate"  => null,
                "lastvisitDate" => null,
                "activation"    => null,
                "params"        => null,
            )
        );
        return array(
            array(
                dirname(__FILE__).DS."ProjectDataSet01.xml",
                1,
                null,
                null,
                $res,
            ),
        );
    }

    /**
     * Tests the users name
     *
     * @param string $file The file name to check
     *
     * @return null
     *
     * @dataProvider dataGetProjectUsers()
     */
    public function testGetProjectUsers($preload, $oid, $limitstart, $limit, $expect)
    {
        $this->o->joomlaMockPreloadXML($preload);
        $ret = $this->o->getProjectUsers($oid, $limitstart, $limit);
        $this->assertEquals($expect, $ret);
    }


    /**
     * Data provider for testFiles
     *
     * @return array()
     */
    static public function dataCountParents()
    {
        return array(
            array(
                dirname(__FILE__).DS."ProjectDataSet01.xml",
                2,
                1,
            ),
            array(
                dirname(__FILE__).DS."ProjectDataSet01.xml",
                1,
                0,
            ),
        );
    }

    /**
     * Tests the users name
     *
     * @param string $file The file name to check
     *
     * @return null
     *
     * @dataProvider dataCountParents()
     */
    public function testCountParents($preload, $oid, $expect)
    {
        $this->_db->joomlaMockPreloadXML($preload);
        $ret = $this->o->countParents($oid);
        $this->assertSame($expect, $ret);
    }

    /**
     * Data provider for testFiles
     *
     * @return array()
     */
    static public function dataGetUserProjectsCount()
    {
        return array(
            array(
                dirname(__FILE__).DS."ProjectDataSet01.xml",
                5,
                1,
            ),
            array(
                dirname(__FILE__).DS."ProjectDataSet01.xml",
                1,
                0,
            ),
        );
    }

    /**
     * Tests the users name
     *
     * @param string $file The file name to check
     *
     * @return null
     *
     * @dataProvider dataGetUserProjectsCount()
     */
    public function testGetUserProjectsCount($preload, $oid, $expect)
    {
        $this->_db->joomlaMockPreloadXML($preload);
        $ret = $this->o->getUserProjectsCount($oid);
        $this->assertSame($expect, $ret);
    }


    /**
     * Data provider for testFiles
     *
     * @return array()
     */
    static public function dataUserInProject()
    {
        return array(
            array(
                dirname(__FILE__).DS."ProjectDataSet01.xml",
                5,
                1,
                1,
            ),
            array(
                dirname(__FILE__).DS."ProjectDataSet01.xml",
                1,
                1,
                0,
            ),
        );
    }

    /**
     * Tests the users name
     *
     * @param string $file The file name to check
     *
     * @return null
     *
     * @dataProvider dataUserInProject()
     */
    public function testUserInProject($preload, $oid, $projid, $expect)
    {
        $this->_db->joomlaMockPreloadXML($preload);
        $ret = $this->o->userInProject($oid, $projid);
        $this->assertSame($expect, $ret);
    }

}

?>
