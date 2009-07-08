<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.5 component
 * Copyright (C) 2008 Hunt Utilities Group, LLC
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
 * @copyright  2008 Hunt Utilities Group, LLC
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
 * @copyright  2008 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockAdminViewProjects extends JView
{
    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return none
     */
    function display($tpl = null)
    {
        $layout = $this->getLayout();
        if (method_exists($this, $layout)) {
            $this->$layout();
        } else {
            $this->showList();
        }
        parent::display($tpl);

    }


    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return none
     */
    function showList($tpl = null)
    {
        global $mainframe, $option;
        $model = $this->getModel("Projects");

        $db =& JFactory::getDBO();
        $filter_order = $mainframe->getUserStateFromRequest(
            "$option.projects.filter_order",
            'filter_order',
            't.id',
            'cmd'
        );
        $filter_order_Dir = $mainframe->getUserStateFromRequest(
            "$option.projects.filter_order_Dir",
            'filter_order_Dir',
            '',
            'word'
        );
        $filter_state = $mainframe->getUserStateFromRequest(
            "$option.projects.filter_state",
            'filter_state',
            '',
            'word'
        );
        $search = $mainframe->getUserStateFromRequest(
            "$option.projects.search",
            'search',
            '',
            'string'
        );
        $search        = JString::strtolower($search);
        $search_filter = $mainframe->getUserStateFromRequest(
            "$option.projects.search_filter",
            'search_filter',
            'name',
            'string'
        );

        $limit      = $mainframe->getUserStateFromRequest(
            'global.list.limit',
            'limit',
            $mainframe->getCfg('list_limit'),
            'int'
        );
        $limitstart = $mainframe->getUserStateFromRequest(
            $option.'.projects.limitstart',
            'limitstart',
            0,
            'int'
        );

        $where = array();

        if ($filter_state) {
            if ($filter_state == 'P') {
                $where[] = 't.published = 1';
            } else if ($filter_state == 'U') {
                $where[] = 't.published = 0';
            }
        }
        if ($search) {
            if (($search_filter == "m.name")
                && (trim(strtolower($search)) == "none")
            ) {
                $where[] = "t.manager = 0";
            } else {
                $where[] = 'LOWER('.$search_filter.') LIKE '
                    .$db->Quote('%'.$db->getEscaped($search, true).'%', false);
            }
        }
        $where[] = 't.id > 0';

        $where   = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
        $orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;

        $rows = $model->getProjects($where, $limitstart, $limit, $orderby);
        $total = $model->countProjects($where);

        jimport('joomla.html.pagination');
        $pagination = new JPagination($total, $limitstart, $limit);

        // state filter
        $lists['state'] = JHTML::_(
            'grid.state',
            $filter_state,
            "Active",
            "Inactive"
        );

        // table ordering
        $lists['order_Dir']      = $filter_order_Dir;
        $lists['order']          = $filter_order;

        // search filter
        $lists['search']         = $search;
        $lists['search_filter']  = $search_filter;
        $lists['search_options'] = array(
            JHTML::_('select.option', 't.name', 'Name'),
            JHTML::_('select.option', 't.type', 'Type'),
            JHTML::_('select.option', 'm.name', "Manager Name"),
            JHTML::_('select.option', 'c.company', "Customer Name"),
            JHTML::_('select.option', 'p.name', "Category"),
        );
        $lists['search_options_default'] = 'name';
        $lists["wCompCodes"] = TableTimeclockPrefs::getPref("wCompCodes");
        $lists["wCompEnable"] = TableTimeclockPrefs::getPref("wCompEnable");

        $this->assignRef("lists", $lists);
        $this->assignRef("user", JFactory::getUser());
        $this->assignRef("rows", $rows);
        $this->assignRef("pagination", $pagination);
        parent::display($tpl);
    }
    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return none
     */
    function form($tpl = null)
    {
        $model =& JModel::getInstance("Projects", "TimeclockAdminModel");
        $userModel =& JModel::getInstance("Users", "TimeclockAdminModel");
        $customerModel =& JModel::getInstance("Customers", "TimeclockAdminModel");

        // Set this as the default model
        $this->setModel($model, true);
        $row = $this->get("Data");
        if (!empty($row->parent_id)) {
            $cat = $model->getData($row->parent_id);
        }
        $user =& JFactory::getUser();

        $cid = JRequest::getVar('cid', 0, '', 'array');
        // fail if checked out not by 'me'
        if ($row->isCheckedOut($user->get('id'))) {
                $msg = JText::sprintf(
                    'DESCBEINGEDITTED',
                    JText::_('The poll'),
                    $poll->title
                );
                $this->setRedirect(
                    'index.php?option=com_timeclock&controller=projects',
                    $msg
                );
        }
        $model->checkout($user->get("id"), $cid[0]);

        $add = empty($row->id);

        $typeOptions = array(
            JHTML::_("select.option", "PROJECT", "Project"),
            JHTML::_("select.option", "CATEGORY", "Category"),
            JHTML::_("select.option", "PTO", "Paid Time Off"),
            JHTML::_("select.option", "HOLIDAY", "Holiday"),
            JHTML::_("select.option", "UNPAID", "Unpaid"),
        );
        $parentOptions = $model->getParentOptions($row->id, $row->parent_id);

        $wCompCodes = TableTimeclockPrefs::getPref("wCompCodes");
        $wCompCodeOptions = array(JHTML::_("select.option", 0, "None"));
        foreach ($wCompCodes as $code => $desc) {
            $wCompCodeOptions[] = JHTML::_(
                "select.option",
                $code,
                $code.": ".htmlspecialchars($desc)
            );
        }

        $lists["projectUsers"] = $model->getProjectUsers($cid[0]);
        $uUser = array();
        foreach ($lists["projectUsers"] as $u) {
            $uUser[] = $u->id;
        }

        $userWhere = "WHERE p.published=1
              AND (p.startDate <= '".date("Y-m-d")."' AND p.startDate > '0000-00-00')
              AND (p.endDate >= '".date("Y-m-d")."' OR p.endDate = '0000-00-00')";
        $lists["allUsers"] = $userModel->getOptions($userWhere, "None");
        $lists["users"]    = $userModel->getOptions($userWhere, "Add User", $uUser);

        $lists["customers"] = $customerModel->getOptions(
            "WHERE published=1",
            "None"
        );

        $lists["wCompEnable"] = TableTimeclockPrefs::getPref("wCompEnable");

        $this->assignRef("lists", $lists);

        $this->assignRef("lists", $lists);
        $this->assignRef("wCompCodeOptions", $wCompCodeOptions);
        $this->assignRef("parentOptions", $parentOptions);
        $this->assignRef("typeOptions", $typeOptions);
        $this->assignRef("add", $add);
        $this->assignRef("cat", $cat);
        $this->assignRef("row", $row);
        parent::display($tpl);
    }
}

?>
