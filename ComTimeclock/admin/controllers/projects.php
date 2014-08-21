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

/** Load the projects table */
require_once dirname(__FILE__)."/../tables/timeclockprojects.php";

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
class TimeclockAdminControllerProjects extends JControllerLegacy
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
     * @param bool  $cachable Whether to cache or not
     * @param array $params   The parameters to use for the URL
     *
     * @access public
     * @return null
     */
    function display($cachable = false, $urlparams = array())
    {
        require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/timeclock.php';
        // Load the submenu.
        TimeclockHelper::addSubmenu(
            JRequest::getCmd('view', 'timeclock'),
            "projects"
        );

        JRequest::setVar('view', 'projects');
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
        JRequest::setVar('model', 'projects');
        JRequest::setVar('view', 'projects');
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);
        parent::display();
    }
    /**
     * Publishes an item
     *
     * @return void
     */
    function adduser()
    {
        if (!JRequest::checkToken()) {
            $this->setRedirect(
                JRoute::_("index.php"),
                JText::_("COM_TIMECLOCK_BAD_FORM_TOKEN"),
                "error"
            );
            return;
        }
        $model = $this->getModel("Projects");
        $model->adduser();
        $link = TimeclockHelper::referer();
        $this->setRedirect($link, $msg);
    }

    /**
     * Publishes an item
     *
     * @return void
     */
    function removeuser()
    {
        if (!JRequest::checkToken()) {
            $this->setRedirect(
                JRoute::_("index.php"),
                JText::_("COM_TIMECLOCK_BAD_FORM_TOKEN"),
                "error"
            );
            return;
        }
        $model = $this->getModel("Projects");
        $model->removeuser();
        $link = TimeclockHelper::referer();
        $this->setRedirect($link, $msg);
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
        $link = 'index.php?option=com_timeclock&task=projects.display';
        $this->setRedirect($link, $msg);

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
                JText::_("COM_TIMECLOCK_BAD_FORM_TOKEN"),
                "error"
            );
            return;
        }
        $model = $this->getModel("Projects");
        $user  = JFactory::getUser();

        $model->publish(1, $user->get("id"));
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
                JText::_("COM_TIMECLOCK_BAD_FORM_TOKEN"),
                "error"
            );
            return;
        }
        $model = $this->getModel("Projects");
        $user  = JFactory::getUser();

        $model->publish(0, $user->get("id"));
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
                JText::_("COM_TIMECLOCK_BAD_FORM_TOKEN"),
                "error"
            );
            return;
        }
        $model = $this->getModel("Projects");

        if ($id = $model->store()) {
            $msg   = JText::_("COM_TIMECLOCK_PROJECT_SAVED");
            $link  = 'index.php?option=com_timeclock&task=projects.edit';
            $link .= '&cid[]='.$id;
        } else {
            $msg = JText::_("COM_TIMECLOCK_PROJECT_FAILED");
            $link = $_SERVER["HTTP_REFERER"];
        }
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
                JText::_("COM_TIMECLOCK_BAD_FORM_TOKEN"),
                "error"
            );
            return;
        }
        $model = $this->getModel("Projects");

        if ($model->store()) {
            $msg = JText::_("COM_TIMECLOCK_PROJECT_SAVED");
        } else {
            $msg = JText::_("COM_TIMECLOCK_PROJECT_FAILED");
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
        $model = $this->getModel("Projects");
        $cid = JRequest::getVar('cid', 0, '', 'array');
        $model->checkin($cid[0]);
        $this->reset();

    }

}

?>
