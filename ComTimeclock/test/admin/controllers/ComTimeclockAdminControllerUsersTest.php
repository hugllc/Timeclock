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
 * @copyright  2008-2009 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock:JoomlaUI
 */

/** Require the JoomlaMock stuff */
require_once dirname(__FILE__).'/../../JoomlaMock/joomla.php';
require_once dirname(__FILE__).'/../../JoomlaMock/testCases/JControllerTest.php';
/** Require the module under test */
require_once dirname(__FILE__).'/../../../admin/controllers/users.php';

/**
 * Test class for driver.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-30 at 08:44:25.
 *
 * @category   Test
 * @package    ComTimeclockTest
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock:JoomlaUI
 */
class ComTimeclockAdminControllerUsersTest extends JControllerTest
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
        $this->o = new TimeclockAdminControllerUsers();
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
    public static function dataDisplay()
    {
        return array(
            array("display", array("view" => "users")),
            array(
                "edit",
                array(
                    "model" => "users",
                    "view" => "users",
                    "layout" => "form",
                    "hidemainmenu" => 1
                )
            ),
        );
    }
    /**
     * Data provider
     *
     * @return array
     */
    public static function dataRegisterTask()
    {
        return array(
            array(array(array('add', 'edit'))),
        );
    }
    /**
     * Data provider
     *
     * @return array
     */
    public static function dataStoreTasks()
    {
        return array(
            array(
                "save",
                true,
                array(
                    "link" => "index.php?option=com_timeclock&controller=users",
                    "msg" => "User Settings Saved!"
                )
            ),
            array(
                "save",
                false,
                array(
                    "link" => "index.php?option=com_timeclock&controller=users",
                    "msg" => "Error Saving User Settings"
                )
            ),
            array(
                "apply",
                true,
                array(
                    "link" => "index.php?option=com_timeclock&controller=users"
                        ."&task=edit&cid[]=0",
                    "msg" => "User Settings Saved!"
                )
            ),
            array(
                "apply",
                false,
                array(
                    "link" => "index.php?option=com_timeclock&controller=users"
                        ."&task=edit&cid[]=0",
                    "msg" => "Error Saving User Settings"
                )
            ),
            array(
                "reset",
                true,
                array(
                    "link" => "index.php?option=com_timeclock&controller=users",
                    "msg" => null
                )
            ),
            array(
                "cancel",
                true,
                array(
                    "link" => "index.php?option=com_timeclock&controller=users",
                    "msg" => null
                )
            ),
            array(
                "publish",
                true,
                array(
                    "link" => "index.php?option=com_timeclock&controller=users",
                    "msg" => null
                )
            ),
            array(
                "unpublish",
                true,
                array(
                    "link" => "index.php?option=com_timeclock&controller=users",
                    "msg" => null
                )
            ),
            array(
                "addproject",
                true,
                array(
                    "link" => "index.php",
                    "msg" => "Project add failed."
                )
            ),
            array(
                "addproject",
                false,
                array(
                    "link" => "index.php",
                    "msg" => "Project add failed."
                )
            ),
            array(
                "removeproject",
                true,
                array(
                    "link" => "index.php",
                    "msg" => "Project remove failed."
                )
            ),
            array(
                "removeproject",
                false,
                array(
                    "link" => "index.php",
                    "msg" => "Project remove failed."
                )
            ),
            array(
                "adduserproject",
                true,
                array(
                    "link" => "index.php",
                    "msg" => "No projects to add."
                )
            ),
            array(
                "adduserproject",
                false,
                array(
                    "link" => "index.php",
                    "msg" => "No projects to add."
                )
            ),
        );
    }

}

?>
