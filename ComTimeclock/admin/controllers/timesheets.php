<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.6 component
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @copyright  2014 Hunt Utilities Group, LLC
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockAdminControllerTimesheets extends JControllerLegacy
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
        //require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/timeclock.php';
        // Load the submenu.
        TimeclockHelper::addSubmenu(
            JRequest::getCmd('view', 'timeclock'),
            "timesheets"
        );

        JRequest::setVar('view', 'timesheets');
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
        JRequest::setVar('model', 'timesheets');
        JRequest::setVar('view', 'timesheets');
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
        $link = 'index.php?option=com_timeclock&task=timesheets.display';
        $this->setRedirect($link, $msg);

    }

    /**
     * save a record (and redirect to main page)
     *
     * @param bool $apply Set to true if we are applying and going back to editing
     *
     * @return void
     */
    function save($apply=false)
    {
        if (!JRequest::checkToken()) {
            $this->setRedirect(
                JRoute::_("index.php"),
                JText::_("COM_TIMECLOCK_BAD_FORM_TOKEN"),
                "error"
            );
            return;
        }
        $model = $this->getModel("Timesheets");

        $link  = 'index.php?option=com_timeclock';
        if ($id = $model->store()) {
            $msg   = JText::_("COM_TIMECLOCK_TIMESHEET_SAVED");
            $link .= '&task=timesheets.edit&cid[]='.(int)$id;
            $type = "message";
            if (!$apply) {
                $this->reset($msg);
                return;
            }
        } else {
            $msg  = JText::_("COM_TIMECLOCK_TIMESHEET_FAILED");
            if (is_string($model->lastError)) {
                $msg .= " (".JText::_($model->lastError).")";
            }
            $type = "error";
            if (empty($model->lastStoreId)) {
                $link .= "&task=timesheets.add";
            } else {
                $link .= "&task=timesheets.edit&cid[]=".(int)$model->lastStoreId;
            }
        }
        $this->setRedirect($link, $msg, $type);

    }

    /**
     * save a record (and redirect to main page)
     *
     * @return void
     */
    function apply()
    {
        $this->save(true);
    }

    /**
     * save a record (and redirect to main page)
     *
     * @return void
     */
    function cancel()
    {
        $model = $this->getModel("Timesheets");
        $id = JRequest::getVar('id', 0, '', 'int');
        $model->checkin($id);
        $this->reset();

    }

}

?>
