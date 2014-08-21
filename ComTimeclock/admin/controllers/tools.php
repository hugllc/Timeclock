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
class TimeclockAdminControllerTools extends JControllerLegacy
{
    /**
     * Custom Constructor
     *
     * @param array $default The configuration array.
     */
    function __construct($default = array())
    {
        parent::__construct($default);

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
        // Load the submenu.
        TimeclockHelper::addSubmenu(
            JRequest::getCmd('view', 'timeclock'),
            'tools'
        );

        JRequest::setVar('view', 'tools');
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
        JRequest::setVar('model', 'tools');
        JRequest::setVar('view', 'tools');
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);
        parent::display();
    }
    /**
     * Method to display the view
     *
     * @access public
     * @return null
     */
    function dbcheck()
    {
        JRequest::setVar('model', 'tools');
        JRequest::setVar('view', 'tools');
        JRequest::setVar('layout', 'dbcheck');
        JRequest::setVar('tpl', 'check');
        self::display();
    }

    /**
     * Method to display the view
     *
     * @access public
     * @return null
     */
    function convertprefs()
    {
        JRequest::setVar('model', 'tools');
        JRequest::setVar('view', 'tools');
        JRequest::setVar('layout', 'convertprefs');
        self::display();
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
        $link = 'index.php?option=com_timeclock&task=tools.display';
        $this->setRedirect($link, $msg);

    }

    /**
     * save a record (and redirect to main page)
     *
     * @return void
     */
    function apply()
    {

    }

    /**
     * save a record (and redirect to main page)
     *
     * @return void
     */
    function save()
    {

    }

    /**
     * save a record (and redirect to main page)
     *
     * @return void
     */
    function cancel()
    {

    }

}

?>
