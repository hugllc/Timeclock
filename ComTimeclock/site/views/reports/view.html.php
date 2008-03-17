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

defined('_JEXEC') or die('Restricted access');

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

class TimeclockViewReports extends JView
{
    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return null
     */
    function display($tpl = null)
    {
        global $mainframe;
        $layout        = $this->getLayout();
        $model         =& $this->getModel();
        $this->_params =& $mainframe->getParams('com_timeclock');
        $this->assignRef("params", $this->_params);

        if (method_exists($this, $layout)) {
            $this->$layout($tpl);
        } else {
            $this->_where          = (count($this->_where) ? implode(' AND ', $this->_where) : '');
            $data = $model->getTimesheetData($this->_where, $orderby);
            $dates["start"] = $model->getStartDate();
            $dates["end"] = $model->getEndDate();

            $this->assignRef("data", $data);        
            $this->assignRef("dates", $dates);        
            parent::display($tpl);
        }        
    }
    /**
     * filter, search and pagination
     *
     * @return null
     */
    function filter()
    {
        global $mainframe, $option;
        $layout = $this->getLayout();
        $db =& JFactory::getDBO();

        $filter_order      = $this->_params->get("filter_order");
        $filter_order_Dir  = $this->_params->get("filter_order");

        $filter_order      = $mainframe->getUserStateFromRequest("$option.reports.$layout.filter_order", 'filter_order', $this->_params->get("filter_order"), 'cmd');
        $filter_order_Dir  = $mainframe->getUserStateFromRequest("$option.reports.$layout.filter_order_Dir", 'filter_order_Dir', $this->_params->get("filter_order_dir"), 'word');
        $filter2_order     = $mainframe->getUserStateFromRequest("$option.reports.$layout.filter2_order", 'filter2_order', $this->_params->get("filter2_order"), 'cmd');
        $filter2_order_Dir = $mainframe->getUserStateFromRequest("$option.reports.$layout.filter2_order_Dir", 'filter2_order_Dir', $this->_params->get("filter2_order_dir"), 'word');
        $filter3_order     = $mainframe->getUserStateFromRequest("$option.reports.$layout.filter3_order", 'filter3_order', $this->_params->get("filter3_order"), 'cmd');
        $filter3_order_Dir = $mainframe->getUserStateFromRequest("$option.reports.$layout.filter3_order_Dir", 'filter3_order_Dir', $this->_params->get("filter3_order_dir"), 'word');
        $filter_state      = $mainframe->getUserStateFromRequest("$option.reports.$layout.filter_state", 'filter_state', '', 'word');
        $search            = $mainframe->getUserStateFromRequest("$option.reports.$layout.search", 'report_search', '', 'string');
        $search            = JString::strtolower($search);
        $search_filter     = $mainframe->getUserStateFromRequest("$option.reports.$layout.search_filter", 'report_search_filter', '', 'string');

        $this->_orderby        = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
        if (!empty($filter2_order)) $this->_orderby .= ", ". $filter2_order .' '. $filter2_order_Dir;
        if (!empty($filter3_order)) $this->_orderby .= ", ". $filter3_order .' '. $filter3_order_Dir;

        $this->_where = array();

        if ($search) {
            $this->_where[] = 'LOWER('.$search_filter.') LIKE '.$db->Quote('%'.$db->getEscaped($search, true).'%', false);
        }

        // state filter
        $this->_lists['state'] = JHTML::_('grid.state', $filter_state, "Active", "Inactive");

        // table ordering
        $this->_lists['order_Dir']      = $filter_order_Dir;
        $this->_lists['order']          = $filter_order;

        // search filter
        $this->_lists['search']         = $search;
        $this->_lists['search_filter']  = $search_filter;

        $this->assignRef("lists", $this->_lists);
    
    }
    /**
     * pagination
     *
     * @param int $total The total number of items.
     *
     * @return null
     */
    function pagination($total)
    {
        global $mainframe, $option;
        jimport('joomla.html.pagination');

        $this->_limit            = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $this->_limitstart       = $mainframe->getUserStateFromRequest($option.'.projects.limitstart', 'limitstart', 0, 'int');

        $pagination = new JPagination($total, $this->_limitstart, $this->_limit);

        $this->assignRef("pagination", $pagination);
    }
    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return null
     */
    function payroll($tpl = null)
    {
        
        $model   =& $this->getModel();
        $this->_where[] = $model->periodWhere("t.worked");

        $where          = (count($this->_where) ? implode(' AND ', $this->_where) : '');
        $ret = $model->getTimesheetData($where, $this->_limitstart, $this->_limit, $this->_orderby);
        $data = array();
        foreach ($ret as $d) {
            $hours = ($d->type == "HOLIDAY") ? $d->hours * $model->getHolidayPerc($d->user_id, $d->worked) : $d->hours;
            $data[$d->user_id][$d->project_id][$d->worked]['hours'] += $hours;
            $data[$d->user_id][$d->project_id][$d->worked]['notes'] .= $d->notes;
            $data[$d->user_id][$d->project_id][$d->worked]['rec'] = $d;
        }

        $period  = $model->getPeriod();
        $days = 7;
        
        $report = array();
        $notes = array();
        $weeks = round($period["length"] / $days);
        // Make the data into something usefull for this particular report
        foreach ($data as $user_id => $projdata) {
            foreach ($projdata as $proj_id => $dates) {
                $d = 0;
                foreach ($period["dates"] as $key => $uDate) {
                    $week = (int)($d++ / $days);
                    if (!array_key_exists($key, $dates)) continue;
                    $type = $dates[$key]["rec"]->type;
                    $report[$user_id][$week][$type]["hours"] += $dates[$key]["hours"];
                    $report[$user_id][$week]["TOTAL"]["hours"] += $dates[$key]["hours"];
                    $report[$user_id]["TOTAL"] += $dates[$key]["hours"];
                    if (empty($report[$user_id]["name"])) $report[$user_id]["name"] = $dates[$key]["rec"]->author;
        
                    $projname = $dates[$key]["rec"]->project_name;
                    $username = $dates[$key]["rec"]->author;
                    $notes[$username][$projname][$key]["hours"] += $dates[$key]["hours"];
                    $notes[$username][$projname][$key]["notes"] .= $dates[$key]["notes"];
                }
            }
        }

        $this->assignRef("weeks", $weeks);        
        $this->assignRef("days", $days);        
        $this->assignRef("report", $report);        
        $this->assignRef("notes", $notes);        
        $this->assignRef("period", $period);

        parent::display($tpl);

    }

    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return null
     */
    function notes($tpl = null)
    {
        $model   =& $this->getModel();
        $this->filter();
        $cat_id = JRequest::getVar('cat_id', "0", '', 'int');
        if (!empty($cat_id)) $this->_where[] = "pc.id = ".(int)$cat_id;
        $cust_id = JRequest::getVar('cust_id', "0", '', 'int');
        if (!empty($cust_id)) $this->_where[] = "c.id = ".(int)$cust_id;
        $proj_id = JRequest::getVar('proj_id', "0", '', 'int');
        if (!empty($proj_id)) $this->_where[] = "p.id = ".(int)$proj_id;
        
        $where          = (count($this->_where) ? implode(' AND ', $this->_where) : '');

        $this->_lists["search_options"] = array(
            JHTML::_('select.option', 't.notes', 'Notes'),
            JHTML::_('select.option', 't.worked', 'Date Worked'),
            JHTML::_('select.option', 'p.name', 'Project Name'),
            JHTML::_('select.option', 'u.name', "User Name"),
            JHTML::_('select.option', 'pc.name', "Category Name"),
            JHTML::_('select.option', 'c.company', "Company Name"),
            JHTML::_('select.option', 'c.name', "Company Contact"),
        );
        $total = $model->getTimesheetDataCount($where);
        $this->pagination($total);
        $notes = $model->getTimesheetData($where, $this->_limitstart, $this->_limit, $this->_orderby);
        $this->assignRef("notes", $notes);        

        parent::display($tpl);

    }


}

?>