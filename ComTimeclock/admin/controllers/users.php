<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.6 component
 * Copyright (C) 2008-2009, 2011 Hunt Utilities Group, LLC
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009, 2011 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/** Load the prefs table */
require_once dirname(__FILE__)."/../tables/timeclockprefs.php";
/** Load the users table */
require_once dirname(__FILE__)."/../tables/timeclockusers.php";
/** Load the projets controller */
require_once dirname(__FILE__)."/projects.php";

/**
 * ComTimeclock World Component Controller
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009, 2011 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockAdminControllerUsers extends JController
{
    /**
     * Custom Constructor
     *
     * @param array $default The configuration array.
     */
    function __construct($default = array())
    {
        parent::__construct($default);

        $this->registerTask('add', 'edit');

    }

    /**
     * Method to display the view
     *
     * @access public
     * @return null
     */
    function display()
    {
        require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/timeclock.php';
        // Load the submenu.
        TimeclockHelper::addSubmenu(
            JRequest::getCmd('view', 'timeclock'),
            'users'
        );

        JRequest::setVar('view', 'users');
        parent::display();
    }
    /**
     * Method to display the view
     *
     * @access public
     * @return null
     */
    function edit()
    {
        JRequest::setVar('model', 'users');
        JRequest::setVar('view', 'users');
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);
        parent::display();
    }

    /**
     * redirects to a default url
     *
     * @param string $msg The message to have when redirected
     *
     * @return void
     */
    function reset($msg=null)
    {
        $link = 'index.php?option=com_timeclock&task=users.display';
        $this->setRedirect($link, $msg);

    }

    /**
     * Publishes an item
     *
     * @return void
     */
    function adduserproject()
    {
        if (!JRequest::checkToken()) {
            $this->setRedirect(
                JRoute::_("index.php"),
                JText::_(COM_TIMECLOCK_BAD_FORM_TOKEN),
                "error"
            );
            return;
        }
        $model = $this->getModel("Users");

        $id = JRequest::getVar('user_id', 0, 'post', 'int');
        $projects = $model->getUserProjectIds($id);
        $user_id = JRequest::getVar('id', 0, 'post', 'int');
        $link = TimeclockAdminController::referer();

        if (empty($projects)) {
            $msg = JText::_(COM_TIMECLOCK_PROJECT_NO_PROJECTS);
            $type = "error";
        } else if ($model->addproject($projects, $user_id)) {
            $msg = JText::_(COM_TIMECLOCK_PROJECTS_ADD_SAVED);
        } else {
            $msg = JText::_(COM_TIMECLOCK_PROJECTS_ADD_FAILED);
            $type = "error";
        }
        $this->setRedirect($link, $msg, $type);
    }

    /**
     * Publishes an item
     *
     * @return void
     */
    function addproject()
    {
        if (!JRequest::checkToken()) {
            $this->setRedirect(
                JRoute::_("index.php"),
                JText::_(COM_TIMECLOCK_BAD_FORM_TOKEN),
                "error"
            );
            return;
        }
        $model = $this->getModel("Users");
        $projid = JRequest::getVar('projid', array(0), 'post', 'array');
        $user_id = JRequest::getVar('id', 0, 'post', 'int');
        if ($model->addproject($projid, $user_id)) {
            $msg = JText::_(COM_TIMECLOCK_PROJECT_ADD_SAVED);
        } else {
            $msg = JText::_(COM_TIMECLOCK_PROJECT_ADD_FAILED);
            $type = "error";
        }
        $link = TimeclockAdminController::referer();
        $this->setRedirect($link, $msg, $type);
    }

    /**
     * Publishes an item
     *
     * @return void
     */
    function removeproject()
    {
        if (!JRequest::checkToken()) {
            $this->setRedirect(
                JRoute::_("index.php"),
                JText::_(COM_TIMECLOCK_BAD_FORM_TOKEN),
                "error"
            );
            return;
        }
        $projid = JRequest::getVar('remove_projid', array(0), '', 'array');

        $user_id = JRequest::getVar('id', 0, '', 'int');


        $model = $this->getModel("Users");
        if ($model->removeproject($projid, $user_id)) {
            $msg = JText::_(COM_TIMECLOCK_PROJECT_REMOVE_SAVED);
        } else {
            $msg = JText::_(COM_TIMECLOCK_PROJECT_REMOVE_FAILED);
            $type = "error";
        }
        $link = TimeclockAdminController::referer();
        $this->setRedirect($link, $msg, $type);
    }


    /**
     * Publishes an item
     *
     * @return void
     */
    function publish()
    {
        if (!JRequest::checkToken()) {
            $this->setRedirect(
                JRoute::_("index.php"),
                JText::_(COM_TIMECLOCK_BAD_FORM_TOKEN),
                "error"
            );
            return;
        }
        $model = $this->getModel("Users");
        $model->publish(1);
        $this->reset();
    }

    /**
     * unpublishes an item
     *
     * @return void
     */
    function unpublish()
    {
        if (!JRequest::checkToken()) {
            $this->setRedirect(
                JRoute::_("index.php"),
                JText::_(COM_TIMECLOCK_BAD_FORM_TOKEN),
                "error"
            );
            return;
        }
        $model = $this->getModel("Users");

        $model->publish(0);
        $this->reset();
    }

    /**
     * save a record (and redirect to main page)
     *
     * @return void
     */
    function apply()
    {
        if (!JRequest::checkToken()) {
            $this->setRedirect(
                JRoute::_("index.php"),
                JText::_(COM_TIMECLOCK_BAD_FORM_TOKEN),
                "error"
            );
            return;
        }
        $model = $this->getModel("Users");

        if ($model->store()) {
            $msg = JText::_(COM_TIMECLOCK_USER_SETTINGS_SAVED);
        } else {
            $msg = JText::_(COM_TIMECLOCK_USER_SETTINGS_FAILED);
        }
        $id    = JRequest::getVar('id', 0, '', 'int');
        $link  = 'index.php?option=com_timeclock&task=users.edit';
        $link .= '&cid[]='.$id;
        $this->setRedirect($link, $msg);

    }

    /**
     * save a record (and redirect to main page)
     *
     * @return void
     */
    function save()
    {
        if (!JRequest::checkToken()) {
            $this->setRedirect(
                JRoute::_("index.php"),
                JText::_(COM_TIMECLOCK_BAD_FORM_TOKEN),
                "error"
            );
            return;
        }
        $model = $this->getModel("Users");

        if ($model->store()) {
            $msg = JText::_(COM_TIMECLOCK_USER_SETTINGS_SAVED);
        } else {
            $msg = JText::_(COM_TIMECLOCK_USER_SETTINGS_FAILED);
        }
        $id = JRequest::getVar('id', 0, '', 'int');
        $model->checkin($id);
        $this->reset($msg);

    }

    /**
     * save a record (and redirect to main page)
     *
     * @return void
     */
    function cancel()
    {
        $model = $this->getModel("Users");
        $cid = JRequest::getVar('cid', 0, '', 'array');
        $model->checkin($cid[0]);
        $this->reset();

    }

}

?>
