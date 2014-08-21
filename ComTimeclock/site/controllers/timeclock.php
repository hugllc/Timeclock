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
$base = dirname(JApplicationHelper::getPath("front", "com_timeclock"));

require_once $base.'/models/timeclock.php';

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
class TimeclockControllerTimeclock extends TimeclockController
{
    /**
     * Custom Constructor
     *
     * @param array $default The configuration array.
     */
    function __construct($default = array())
    {
        parent::__construct($default);

        $this->registerTask('applyhours', 'savehours');

    }
    /**
     * Method to display the view
     *
     * @access public
     * @return null
     */
    function display()
    {
        include_once JPATH_COMPONENT_ADMINISTRATOR.'/models/projects.php';
        $projModel =& JModel::getInstance("Projects", "TimeclockAdminModel");
        $user    = JFactory::getUser();
        $user_id = $user->get("id");

        if ($projModel->getUserProjectsCount($user_id) == 0) {
            $this->setRedirect(
                "index.php",
                JText::_("COM_TIMECLOCK_NO_PROJECTS"),
                "error"
            );
            return;
        }

        JRequest::setVar('view', 'timeclock');
        parent::display();
        return true;
    }

    /**
    * Method to display the view
    *
    * @access public
    * @return null
    */
    function addhours()
    {
        include_once JPATH_COMPONENT_ADMINISTRATOR.'/models/projects.php';
        $projid   = JRequest::getVar('projid', null, '', 'string');
        if (!empty($projid)) {
            $projModel =& JModel::getInstance("Projects", "TimeclockAdminModel");
            $user      = JFactory::getUser();
            $user_id   = $user->get("id");
            if ($projModel->userInProject($user_id, $projid) == false) {
                $this->setRedirect(
                    JRoute::_("index.php?option=com_timeclock&view=timeclock"),
                    JText::_("COM_TIMECLOCK_PROJECT_NO_AUTH"),
                    "error"
                );
                return;
            }
        }
        $date = JRequest::getVar('date', null, '', 'string');
        if (!TimeclockModelTimeclock::checkDates($date)) {
            return;
        }
        JRequest::setVar('layout', 'addhours');
        JRequest::setVar('hidemainmenu', 1);
        parent::display();
    }

    /**
    * Method to save hours
    *
    * @access public
    * @return null
    */
    function savehours()
    {

        if (!JRequest::checkToken()) {
            $this->setRedirect(
                JRoute::_("index.php"),
                JText::_("COM_TIMECLOCK_BAD_FORM_TOKEN"),
                "error"
            );
            return;
        }

        $date = JRequest::getVar('date', null, '', 'string');
        if (!TimeclockModelTimeclock::checkDates($date)) {
            return;
        }
        $model = $this->getModel("Timeclock");

        if ($model->store()) {
            $msg = JText::_("COM_TIMECLOCK_HOURS_SAVED");
        } else {
            $msg = JText::_("COM_TIMECLOCK_HOURS_FAILED");
        }

        $referer = JRequest::getVar(
            'referer',
            $_SERVER["HTTP_REFERER"],
            '',
            'string'
        );

        $task = JRequest::getVar('task', '', '', 'word');

        if ($task == 'applyhours') {
            $url = $_SERVER["HTTP_REFERER"]."&referer=".urlencode($referer);
        } else {
            $url = $referer;
        }
        $this->setRedirect($url, $msg);
    }

}

?>
