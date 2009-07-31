<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.5 component
 * Copyright (C) 2008-2009 Hunt Utilities Group, LLC
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
 * @copyright  2008-2009 Hunt Utilities Group, LLC
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
 * @copyright  2008-2009 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockAdminControllerProjects extends JController
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
        $model = $this->getModel("Projects");
        $model->adduser();
        $link = TimeclockAdminController::referer();
        $this->setRedirect($link, $msg);
    }

    /**
     * Publishes an item
     *
     * @return void
     */
    function removeuser()
    {
        $model = $this->getModel("Projects");
        $model->removeuser();
        $link = TimeclockAdminController::referer();
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
        $link = 'index.php?option=com_timeclock&controller=projects';
        $this->setRedirect($link, $msg);

    }

    /**
     * Publishes an item
     *
     * @return void
     */
    function publish()
    {
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
        $model = $this->getModel("Projects");

        if ($id = $model->store()) {
            $msg   = JText::_('Project Saved!');
            $link  = 'index.php?option=com_timeclock&controller=projects&task=edit';
            $link .= '&cid[]='.$id;
        } else {
            $msg = JText::_('Error Saving Project');
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
        $model = $this->getModel("Projects");

        if ($model->store()) {
            $msg = JText::_('Project Saved!');
        } else {
            $msg = JText::_('Error Saving Project');
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
