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

/** Load the customers table */
require_once dirname(__FILE__)."/../tables/timeclockcustomers.php";

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
class TimeclockAdminControllerCustomers extends JControllerLegacy
{
    /**
     * Custom Constructor
     *
     * @param array $default The configuration array.
     */
    public function __construct($default = array())
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
    public function display($cachable = false, $urlparams = array())
    {
        require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/timeclock.php';
        // Load the submenu.
        TimeclockHelper::addSubmenu(
            JRequest::getCmd('view', 'timeclock'),
            "customers"
        );

        JRequest::setVar('view', 'customers');
        parent::display();
    }
    /**
     * Method to display the view
     *
     * @access public
     * @return null
     */
    public function edit()
    {
        JRequest::setVar('model', 'customers');
        JRequest::setVar('view', 'customers');
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
    public function reset($msg=null)
    {
        $link = 'index.php?option=com_timeclock&task=customers.display';
        $this->setRedirect($link, $msg);

    }

    /**
     * save a record (and redirect to main page)
     *
     * @return void
     */
    public function apply()
    {
        if (!JRequest::checkToken()) {
            $this->setRedirect(
                JRoute::_("index.php"),
                JText::_("COM_TIMECLOCK_BAD_FORM_TOKEN"),
                "error"
            );
            return;
        }

        $model = $this->getModel("Customers");

        if ($id = $model->store()) {
            $msg   = JText::_("COM_TIMECLOCK_CUSTOMER_SAVED");
            $link  = 'index.php?option=com_timeclock&task=customers.edit';
            $link .= '&cid[]='.$id;
        } else {
            $msg  = JText::_("COM_TIMECLOCK_CUSTOMER_FAILED");
            $link = $_SERVER["HTTP_REFERER"];
        }
        $this->setRedirect($link, $msg);

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
        $model = $this->getModel("Customers");

        if ($model->store()) {
            $msg = JText::_("COM_TIMECLOCK_CUSTOMER_SAVED");
        } else {
            $msg = JText::_("COM_TIMECLOCK_CUSTOMER_FAILED");
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
    public function cancel()
    {
        $model = $this->getModel("Customers");
        $id = JRequest::getVar('id', 0, '', "int");
        $model->checkin($id);
        $this->reset();

    }
    /**
     * Publishes an item
     *
     * @return void
     */
    public function publish()
    {
        if (!JRequest::checkToken()) {
            $this->setRedirect(
                JRoute::_("index.php"),
                JText::_("COM_TIMECLOCK_BAD_FORM_TOKEN"),
                "error"
            );
            return;
        }
        $model = $this->getModel("Customers");
        $user = JFactory::getUser();

        $model->publish(1, $user->get("id"));
        $this->reset();
    }

    /**
     * unpublishes an item
     *
     * @return void
     */
    public function unpublish()
    {
        if (!JRequest::checkToken()) {
            $this->setRedirect(
                JRoute::_("index.php"),
                JText::_("COM_TIMECLOCK_BAD_FORM_TOKEN"),
                "error"
            );
            return;
        }
        $model = $this->getModel("Customers");
        $user = JFactory::getUser();

        $model->publish(0, $user->get("id"));
        $this->reset();
    }

}

?>