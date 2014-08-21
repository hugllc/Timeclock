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
class TimeclockAdminController extends JControllerLegacy
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
    function display($cachable = false, $urlparams = array())
    {
        require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/timeclock.php';
        // Load the submenu.
        TimeclockHelper::addSubmenu(
            JRequest::getCmd('view', 'timeclock'),
            JRequest::getCmd('controller', 'timeclock')
        );

        JRequest::setVar('view', 'about');
        parent::display();
    }

    /**
    * Title cell
    * For the title and toolbar to be rendered correctly,
    * this title fucntion must be called before the starttable function and
    * the toolbars icons this is due to the nature of how the css has been used
    * to postion the title in respect to the toolbar
    *
    * @param string $title The title
    *
    * @return none
    */
    function title($title)
    {
        $mainframe = JFactory::getApplication();

        $html  = "<div class=\"pagetitle\" style=\"background-image: url("
                ."components/com_timeclock/images/"
                ."clock-48.png); background-repeat: no-repeat;\">\n";
        $html .= "<h2>$title</h2>";
        $html .= "</div>\n";

        $mainframe->set('JComponentTitle', $html);
    }

    /**
     * Get Referrer
     *
     * @return string
     */
    function referer()
    {
        $referer = JRequest::getString('referer', "", 'post');
        if (!empty($referer)) {
            return $referer;
        }
        $referer = $_SERVER["HTTP_REFERER"];
        if (!empty($referer)) {
            return $referer;
        }
        return "index.php";

    }


}
?>
