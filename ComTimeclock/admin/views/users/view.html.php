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
 * @copyright  2008-2009 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockAdminViewUsers extends JView
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
    function showList($tpl = null)
    {
        global $mainframe, $option;
        $model = $this->getModel("Users");
        $timeclockModel =& JModel::getInstance("Timeclock", "TimeclockModel");

        $db =& JFactory::getDBO();
        $filter_order = $mainframe->getUserStateFromRequest(
            "$option.users.filter_order",
            'filter_order',
            'u.name',
            'cmd'
        );
        $filter_order_Dir = $mainframe->getUserStateFromRequest(
            "$option.users.filter_order_Dir",
            'filter_order_Dir',
            '',
            'word'
        );
        $filter_state = $mainframe->getUserStateFromRequest(
            "$option.users.filter_state",
            'filter_state',
            '',
            'word'
        );
        $search = $mainframe->getUserStateFromRequest(
            "$option.users.search",
            'search',
            '',
            'string'
        );
        $search        = JString::strtolower($search);
        $search_filter = $mainframe->getUserStateFromRequest(
            "$option.users.search_filter",
            'search_filter',
            'name',
            'string'
        );
        $limit = $mainframe->getUserStateFromRequest(
            'global.list.limit',
            'limit',
            $mainframe->getCfg('list_limit'),
            'int'
        );
        $limitstart = $mainframe->getUserStateFromRequest(
            $option.'.users.limitstart',
            'limitstart',
            0,
            'int'
        );

        $where = array();

        if ($filter_state) {
            if ($filter_state == 'P') {
                $where[] = 'p.published = 1';
            } else if ($filter_state == 'U') {
                $where[] = 'p.published = 0';
            }
        }
        if ($search) {
            $where[] = 'LOWER('.$search_filter.') LIKE '
                      .$db->Quote('%'.$db->getEscaped($search, true).'%', false);
        }

        $where   = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
        $orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;

        $rows = $model->getUsers($where, $limitstart, $limit, $orderby);
        $total = $model->countUsers($where);

        foreach ($rows as $k => $row) {
            $rows[$k]->pto = round(
                $timeclockModel->getTotal(" `p`.`type` = 'PTO'", $row->id),
                $decimalPlaces
            );
            $rows[$k]->ptoYTD = round($model->getPTO($row->id), $decimalPlaces);
        }

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
            JHTML::_('select.option', 'u.name', 'Name'),
            JHTML::_('select.option', 'u.email', 'Email'),
            JHTML::_('select.option', 'u.username', "Username"),
        );
        $lists['search_options_default'] = 'name';
        $lists["wCompCodes"] = TableTimeclockPrefs::getPref("wCompCodes");

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
        $model =& JModel::getInstance("Users", "TimeclockAdminModel");
        $projectModel =& JModel::getInstance("Projects", "TimeclockAdminModel");
        $userModel =& JModel::getInstance("Users", "TimeclockAdminModel");

        // Set this as the default model
        $this->setModel($model, true);
        $row = $this->get("Data");

        $user =& JFactory::getUser();

        $cid = JRequest::getVar('cid', 0, '', 'array');
        if ($cid[0] < 1) {
            $this->setRedirect(
                'index.php?option=com_timeclock&controller=users',
                JText::_("No User given!")
            );
        };
        $user =& JFactory::getUser($cid[0]);

        $status = TableTimeclockPrefs::getPref("userTypes");
        if (is_array($status)) {
            foreach ($status as $key => $value) {
                $lists["status"][] = JHTML::_("select.option", $key, $value);
            }
        }
        /*
        $lists["status"] = array(
            JHTML::_("select.option", "FULLTIME", "Full Time"),
            JHTML::_("select.option", "PARTTIME", "Part Time"),
            JHTML::_("select.option", "CONTRACTOR", "Contractor"),
            JHTML::_("select.option", "TEMPORARY", "Temporary"),
            JHTML::_("select.option", "TERMINATED", "Terminated"),
            JHTML::_("select.option", "RETIRED", "Retired"),
            JHTML::_("select.option", "UNPAID", "Unpaid Leave"),
        );
        */
        $lists["userProjects"] = $model->getUserProjects($cid[0]);
        $uProj = array();
        foreach ($lists["userProjects"] as $p) {
            $uProj[] = $p->id;
        }
        $lists["projects"] = $projectModel->getOptions(
            "WHERE p.published=1 AND p.type <> 'CATEGORY'",
            "Add Project",
            $uProj
        );
        $lists["users"] = $userModel->getOptions(
            $userWhere,
            "Select User",
            $cid
        );

        $this->assignRef("user", $user);
        $this->assignRef("lists", $lists);
        $this->assignRef("row", $row);
        parent::display($tpl);
    }

}

?>
