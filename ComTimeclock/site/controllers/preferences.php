<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.5 component
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

require_once $base.DS.'controller.php';

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
class TimeclockControllerPreferences extends TimeclockController
{
    /**
     * Method to display the view
     *
     * @access public
     * @return null
     */
    function display()
    {
        JRequest::setVar('view', 'preferences');
        parent::display();
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
                JText::_("Bad form token.  Please try again."),
                "error"
            );
            return;
        }
        $model = $this->getModel("Preferences");

        if ($model->store()) {
            $msg = JText::_('Preferences Saved!');
        } else {
            $msg = JText::_('Error Saving Preferences');
        }
        $link = 'index.php?option=com_timeclock&controller=preferences';
        $this->setRedirect($link, $msg);

    }
    /**
     * Check to see if a user is authorized to view the timeclock
     *
     * @param string $task The task to authorize
     *
     * @return null
     */
    function authorize($task)
    {
        $user =& JFactory::getUser();
        if ($user->get("id") < 1) {
            return false;
        }
        $view = JRequest::getVar('view', "timesheet", '', 'word');
        return TableTimeClockPrefs::getPref("published");
    }
}

?>
