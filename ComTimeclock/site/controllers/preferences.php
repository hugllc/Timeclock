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
$base      = JPATH_SITE."/components/com_timeclock";

require_once $base.'/controller.php';

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
class TimeclockControllerPreferences extends TimeclockController
{
    /**
     * Method to display the view
     *
     * @param bool  $cachable Whether to cache or not
     * @param array $params   The parameters to use for the URL
     *
     * @access public
     * @return null
     */
    public function display($cachable = false, $urlparams = array())
    {
        JRequest::setVar('view', 'preferences');
        parent::display();
    }
    /**
     * save a record (and redirect to main page)
     *
     * @return void
     */
    public function save()
    {
        if (!JRequest::checkToken()) {
            $this->setRedirect(
                JRoute::_("index.php"),
                JText::_("COM_TIMECLOCK_BAD_FORM_TOKEN"),
                "error"
            );
            return;
        }
        $model = $this->getModel("Preferences");

        if ($model->store()) {
            $msg = JText::_("COM_TIMECLOCK_PREFS_SAVED");
        } else {
            $msg = JText::_("COM_TIMECLOCK_PREFS_FAILED");
        }
        $link = 'index.php?option=com_timeclock&task=preferences.display';
        $this->setRedirect($link, $msg);

    }
}

?>
