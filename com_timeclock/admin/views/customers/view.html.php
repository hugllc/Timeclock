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
/** Check to make sure we are under Joomla */
defined('_JEXEC') or die('Restricted access');

/** Import the views */
jimport('joomla.application.component.view');

/**
 * HTML View class for the ComTimeclockWorld Component
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockAdminViewCustomers extends JViewLegacy
{
    /**
    * The display function
    *
    * @param string $tpl The template to use
    *
    * @return none
    */
    public function display($tpl = null)
    {
        $layout = $this->getLayout();
        if (method_exists($this, $layout)) {
            $this->$layout($tpl);
        } else {
            $this->showList($tpl);
        }
    }


    /**
    * The display function
    *
    * @param string $tpl The template to use
    *
    * @return none
    */
    public function showList($tpl = null)
    {
        $mainframe = JFactory::getApplication();
        $option = JRequest::getCmd('option');
        $model = $this->getModel("Customers");

        $db = JFactory::getDBO();
        $filter_order = $mainframe->getUserStateFromRequest(
            "$option.customers.filter_order",
            'filter_order',
            'c.company',
            'cmd'
        );
        $filter_order_Dir = $mainframe->getUserStateFromRequest(
            "$option.customers.filter_order_Dir",
            'filter_order_Dir',
            'asc',
            'word'
        );
        $filter_state = $mainframe->getUserStateFromRequest(
            "$option.customers.filter_state",
            'filter_state',
            '',
            'word'
        );
        $search = $mainframe->getUserStateFromRequest(
            "$option.customers.search",
            'search',
            '',
            'string'
        );
        $search        = JString::strtolower($search);
        $search_filter = $mainframe->getUserStateFromRequest(
            "$option.customers.search_filter",
            'search_filter',
            'notes',
            'string'
        );

        $limit      = $mainframe->getUserStateFromRequest(
            'global.list.limit',
            'limit',
            $mainframe->getCfg('list_limit'),
            'int'
        );
        $limitstart = $mainframe->getUserStateFromRequest(
            $option.'.customers.limitstart',
            'limitstart',
            0,
            'int'
        );
        if (trim(strtolower($filter_order_Dir)) == "asc") {
            $filter_order_Dir = "ASC";
        } else {
            $filter_order_Dir = "DESC";
        }

        $where = array();

        if ($filter_state) {
            if ($filter_state == 'P') {
                $where[] = 't.published = 1';
            } else if ($filter_state == 'U') {
                $where[] = 't.published = 0';
            }
        }
        if ($search) {
            $where[] = 'LOWER('.TimeclockAdminSql::dotNameQuote($search_filter).')
                    LIKE '.$db->Quote('%'.$db->getEscaped($search, true).'%', false);
        }

        $where   = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
        $orderby = ' ORDER BY '. TimeclockAdminSql::dotNameQuote($filter_order)
                .' '.$filter_order_Dir;

        $rows = $model->getCustomers($where, $limitstart, $limit, $orderby);
        $total = $model->countCustomers($where);

        jimport('joomla.html.pagination');
        $pagination = new JPagination($total, $limitstart, $limit);

        // state filter
        $lists['state'] = JHTML::_(
            'grid.state',
            $filter_state,
            "COM_TIMECLOCK_ACTIVE",
            "COM_TIMECLOCK_INACTIVE"
        );

        // table ordering
        $lists['order_Dir']      = $filter_order_Dir;
        $lists['order']          = $filter_order;

        // search filter
        $lists['search']         = $search;

        $user = JFactory::getUser();
        $this->assignRef("lists", $lists);
        $this->assignRef("user", $user);
        $this->assignRef("rows", $rows);
        $this->assignRef("pagination", $pagination);

        TimeclockHelper::title(JText::_("COM_TIMECLOCK_TIMECLOCK_CUSTOMERS"));
        JToolBarHelper::publishList("customers.publish", "COM_TIMECLOCK_ACTIVATE");
        JToolBarHelper::unpublishList("customers.unpublish", "COM_TIMECLOCK_DEACTIVATE");
        JToolBarHelper::editList("customers.edit");
        JToolBarHelper::addNew("customers.add");
        JToolBarHelper::preferences('com_timeclock');

        parent::display($tpl);
    }
    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return none
     */
    public function form($tpl = null)
    {
        $model = JModelLegacy::getInstance("Customers", "TimeclockAdminModel");
        // Set this as the default model
        $this->setModel($model, true);
        $row = $this->get("Data");

        $user = JFactory::getUser();

        $cid = JRequest::getVar('cid', 0, '', 'array');
        // fail if checked out not by 'me'
        if ($row->isCheckedOut($user->get('id'))) {
                $msg = JText::sprintf(
                    'DESCBEINGEDITTED',
                    JText::_("COM_TIMECLOCK_THE_POLL"),
                    $poll->title
                );
                $this->setRedirect(
                    'index.php?option=com_timeclock&task=customers.display',
                    $msg
                );
        }
        $model->checkout($user->get("id"), $cid[0]);

        $add = empty($row->id);

        $this->assignRef("lists", $lists);
        $this->assignRef("add", $add);
        $this->assignRef("row", $row);

        $title = ($add) ? JText::_("COM_TIMECLOCK_ADD") : JText::_("COM_TIMECLOCK_EDIT");

        TimeclockHelper::title(JText::sprintf("COM_TIMECLOCK_CUSTOMER_EDIT_TITLE", $title));
        JToolBarHelper::apply("customers.apply");
        JToolBarHelper::save("customers.save");
        JToolBarHelper::cancel("customers.cancel");

        parent::display($tpl);
    }
}

?>
