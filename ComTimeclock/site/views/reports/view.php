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

defined('_JEXEC') or die('Restricted access');

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

class TimeclockViewReportsBase extends JView
{
    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return null
     */
    function pdisplay($tpl = null)
    {
        global $mainframe;

        $layout        = $this->getLayout();
        $model         =& $this->getModel();
        $this->_params =& $mainframe->getParams('com_timeclock');

        $this->assignRef("params", $this->_params);

        $this->_where = array();
        $this->cellFill();

        if (method_exists($this, $layout)) {
            $this->$layout($tpl);
        } else {
            $this->report($tpl);
        }
    }

    /**
     * filter, search and pagination
     *
     * @return null
     */
    function where()
    {
        $cat_id = JRequest::getVar('cat_id', "0", '', 'int');
        if (!empty($cat_id)) {
            $this->_where[] = "pc.id = ".(int)$cat_id;
        }
        $this->assignRef("cat_id", $cat_id);
        $cust_id = JRequest::getVar('cust_id', "0", '', 'int');
        if (!empty($cust_id)) {
            $this->_where[] = "c.id = ".(int)$cust_id;
        }
        $this->assignRef("cust_id", $cust_id);
        $proj_id = JRequest::getVar('proj_id', "0", '', 'int');
        if (!empty($proj_id)) {
            $this->_where[] = "p.id = ".(int)$proj_id;
        }
        $this->assignRef("proj_id", $proj_id);
        $projManager = JRequest::getVar('projManager', "0", '', 'int');
        if (!empty($projManager)) {
            $this->_where[] = "p.manager = ".(int)$projManager;
        }
        $this->assignRef("projManager", $projManager);
        $userManager = JRequest::getVar('userManager', "0", '', 'int');
        if (!empty($userManager)) {
            $this->_where[] = "tp.manager = ".(int)$userManager;
        }
        $this->assignRef("userManager", $userManager);
    }

    /**
     * filter, search and pagination
     *
     * @return null
     */
    function catBy()
    {
        if (!is_object($this->_params)) {
            $this->_params =& $mainframe->getParams('com_timeclock');
        }
        $catBy = JRequest::getVar(
            'cat_by',
            $this->_params->get("cat_by"),
            '',
            'word'
        );
        $catBy = trim(strtolower($catBy));
        $this->assignRef("cat_by", $catBy);
        if ($catBy == "project") {
            return "project_name";
        } else if ($catBy == "customer") {
            return "company_name";
        } else {
            return "category_name";
        }
    }

    /**
     * filter, search and pagination
     *
     * @return null
     */
    function cellFill()
    {
        $cell_fill = " ";
        if (is_object($this->_params)) {
            $cell_fill = $this->_params->get("cell_fill");
        }
        if ($cell_fill == " ") {
            $cell_fill = "&nbsp;";
        }
        $this->assignRef("cell_fill", $cell_fill);
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
        $db     =& JFactory::getDBO();

        if (!is_object($this->_params)) {
            $this->_params =& $mainframe->getParams('com_timeclock');
        }
        $filter_order = $mainframe->getUserStateFromRequest(
            "$option.reports.$layout.filter_order",
            'filter_order',
            $this->_params->get("filter_order"),
            'cmd'
        );
        $filter_order_Dir = $mainframe->getUserStateFromRequest(
            "$option.reports.$layout.filter_order_Dir",
            'filter_order_Dir',
            $this->_params->get("filter_order_dir"),
            'word'
        );
        $filter2_order = $mainframe->getUserStateFromRequest(
            "$option.reports.$layout.filter2_order",
            'filter2_order',
            $this->_params->get("filter2_order"),
            'cmd'
        );
        $filter2_order_Dir = $mainframe->getUserStateFromRequest(
            "$option.reports.$layout.filter2_order_Dir",
            'filter2_order_Dir',
            $this->_params->get("filter2_order_dir"),
            'word'
        );
        $filter3_order = $mainframe->getUserStateFromRequest(
            "$option.reports.$layout.filter3_order",
            'filter3_order',
            $this->_params->get("filter3_order"),
            'cmd'
        );
        $filter3_order_Dir = $mainframe->getUserStateFromRequest(
            "$option.reports.$layout.filter3_order_Dir",
            'filter3_order_Dir',
            $this->_params->get("filter3_order_dir"),
            'word'
        );
        $filter_state = $mainframe->getUserStateFromRequest(
            "$option.reports.$layout.filter_state",
            'filter_state',
            '',
            'word'
        );
        $search = $mainframe->getUserStateFromRequest(
            "$option.reports.$layout.search",
            'report_search',
            '',
            'string'
        );
        $search        = JString::strtolower($search);
        $search_filter = $mainframe->getUserStateFromRequest(
            "$option.reports.$layout.search_filter",
            'report_search_filter',
            '',
            'string'
        );

        if (!empty($filter_order)) {
            $this->_orderby = ' ORDER BY '.$filter_order
                             .' '.$filter_order_Dir;
            if (!empty($filter2_order)) {
                $this->_orderby .= ", ".$filter2_order
                                  .' '.$filter2_order_Dir;
            }
            if (!empty($filter3_order)) {
                $this->_orderby .= ", ".$filter3_order
                                  .' '. $filter3_order_Dir;
            }
        }

        if ($search) {
            $this->_where[] = 'LOWER('.$db->NameQuote($search_filter).') LIKE '
                        .$db->Quote('%'.$db->getEscaped($search, true).'%', false);
        }

        // state filter
        $this->_lists['state'] = JHTML::_(
            'grid.state',
            $filter_state,
            "Active",
            "Inactive"
        );

        // table ordering
        $this->_lists['order_Dir']     = $filter_order_Dir;
        $this->_lists['order']         = $filter_order;

        // search filter
        $this->_lists['search']        = $search;
        $this->_lists['search_filter'] = $search_filter;

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

        $this->_limit = (int)$mainframe->getUserStateFromRequest(
            'global.list.limit',
            'limit',
            $mainframe->getCfg('list_limit'),
            'int'
        );
        $this->_limitstart = (int)$mainframe->getUserStateFromRequest(
            $option.'.projects.limitstart',
            'limitstart',
            0,
            'int'
        );
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

        $model =& $this->getModel();

        $this->_where[] = $model->periodWhere("t.worked");

        $where = (count($this->_where) ? implode(' AND ', $this->_where) : '');
        $ret   = $model->getTimesheetData($where, null, null, $this->_orderby);
        $data  = array();

        foreach ($ret as $d) {
            $data[$d->user_id][$d->project_id][$d->worked]['hours'] += $d->hours;
            $data[$d->user_id][$d->project_id][$d->worked]['notes'] .= $d->notes;
            $data[$d->user_id][$d->project_id][$d->worked]['rec']    = $d;
        }

        $period = $model->getPeriodDates();
        $days   = 7;

        $report = array();
        $notes  = array();
        $totals = array();
        $weeks  = round($period["length"] / $days);
        // Make the data into something usefull for this particular report
        foreach ($data as $user_id => $projdata) {
            foreach ($projdata as $proj_id => $dates) {
                $d = 0;
                foreach ($period["dates"] as $key => $uDate) {
                    $week = (int)($d++ / $days);
                    if (!array_key_exists($key, $dates)) {
                        continue;
                    }
                    $hours = $dates[$key]["hours"];
                    $type  = $dates[$key]["rec"]->type;

                    $report[$user_id][$week][$type]["hours"]   += $hours;
                    $report[$user_id][$week]["TOTAL"]["hours"] += $hours;
                    if (empty($report[$user_id]["name"])) {
                        $report[$user_id]["name"] = $dates[$key]["rec"]->author;
                    }
                    $projname = $dates[$key]["rec"]->project_name;
                    $username = $dates[$key]["rec"]->author;

                    $notes[$username][$projname][$key]["hours"]
                        += $dates[$key]["hours"];
                    $notes[$username][$projname][$key]["notes"]
                        .= $dates[$key]["notes"];

                    $totals["type"][$week][$type]   += $hours;
                    $totals["type"][$week]["TOTAL"] += $hours;
                    $totals["user"][$user_id]       += $hours;
                    $totals["total"]                += $hours;
                }
            }
        }

        $this->assignRef("weeks", $weeks);
        $this->assignRef("days", $days);
        $this->assignRef("report", $report);
        $this->assignRef("totals", $totals);
        $this->assignRef("notes", $notes);
        $this->assignRef("period", $period);

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
        $model =& $this->getModel();
        $this->filter();
        $this->where();

        $this->_where[] = "(p.type <> 'HOLIDAY')";

        $where = implode(' AND ', $this->_where);

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
        $notes = $model->getTimesheetData(
            $where,
            $this->_limitstart,
            $this->_limit,
            $this->_orderby
        );
        $this->assignRef("notes", $notes);

    }
    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return null
     */
    function report($tpl = null)
    {
        $model   =& $this->getModel();
        $this->_reportGetPeriod();

        $this->filter();
        $this->where();

        $this->_lists["search_options"] = array(
            JHTML::_('select.option', 't.notes', 'Notes'),
            JHTML::_('select.option', 't.worked', 'Date Worked'),
            JHTML::_('select.option', 'p.name', 'Project Name'),
            JHTML::_('select.option', 'u.name', "User Name"),
            JHTML::_('select.option', 'pc.name', "Category Name"),
            JHTML::_('select.option', 'c.company', "Company Name"),
            JHTML::_('select.option', 'c.name', "Company Contact"),
        );

        $this->_reportGetData();

        $control = $this->_params->get("show_controls");
        if ($control) {
            $this->_reportControls();
        }
    }
    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return null
     */

    function hours($tpl = null)
    {
        $model   =& $this->getModel();
        $this->_reportGetPeriod();

        $this->filter();
        $this->where();

        $report_type = JRequest::getVar(
            'report_type',
            $this->_params->get("report_type"),
            '',
            'word'
        );
        $this->assignRef("report_type", $report_type);

        $this->_lists["search_options"] = array(
            JHTML::_('select.option', 't.notes', 'Notes'),
            JHTML::_('select.option', 't.worked', 'Date Worked'),
            JHTML::_('select.option', 'p.name', 'Project Name'),
            JHTML::_('select.option', 'u.name', "User Name"),
            JHTML::_('select.option', 'pc.name', "Category Name"),
            JHTML::_('select.option', 'c.company', "Company Name"),
            JHTML::_('select.option', 'c.name', "Company Contact"),
        );

        $this->_hoursGetData();
        unset($this->report["Special"]);

        $control = $this->_params->get("show_controls");
        if ($control) {
            $this->_reportControls();
        }
    }
    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return null
     */

    function hoursgraph($tpl = null)
    {

        $model   =& $this->getModel();
        $this->_reportGetPeriod();

        $this->filter();
        $this->where();

        $report_type = JRequest::getVar(
            'report_type',
            $this->_params->get("report_type"),
            '',
            'word'
        );
        $this->assignRef("report_type", $report_type);
        $jpgraph_path = TableTimeclockPrefs::getPref("JPGraphPath", "system");
        $this->assignRef("jpgraph_path", $jpgraph_path);
        $width  = (int)JRequest::getInt('graphwidth', 0);
        $height = (int)JRequest::getInt('graphheight', 0);
        $this->assignRef("graphwidth", $width);
        $this->assignRef("graphheight", $height);

        $this->_lists["search_options"] = array(
            JHTML::_('select.option', 't.notes', 'Notes'),
            JHTML::_('select.option', 't.worked', 'Date Worked'),
            JHTML::_('select.option', 'p.name', 'Project Name'),
            JHTML::_('select.option', 'u.name', "User Name"),
            JHTML::_('select.option', 'pc.name', "Category Name"),
            JHTML::_('select.option', 'c.company', "Company Name"),
            JHTML::_('select.option', 'c.name', "Company Contact"),
        );

        $this->_hoursgraphGetData();

        $control = $this->_params->get("show_controls");
        if ($control) {
            $this->_reportControls();
        }
    }
    /**
     * The display function
     *
     * @return null
     */
    function _hoursgraphGetData()
    {
        $model    =& $this->getModel();
        $this->assignRef("cat_id", $cat_id);
        $user_id = JRequest::getVar('userid', "0", '', 'int');
        if (!empty($user_id)) {
            $this->_where[] = "t.created_by = ".(int)$user_id;
        }

        $where    = (count($this->_where) ? implode(' AND ', $this->_where) : '');

        $ret      = $model->getTimesheetData($where, null, null, $this->_orderby);
        $report   = array();
        $cat_name = $this->catBy();
        $cats = array();
        $index = 0;

        foreach ($ret as $d) {
            if ($d->category_id < -1) {
                continue;
            }
            $hours = $d->hours;
            $cat   = (empty($d->$cat_name)) ? JText::_("General") : $d->$cat_name;
            if (!isset($cats[$cat])) {
                $cats[$cat] = $index++;
            }
            if (empty($user)) {
                $user = $d->author;
            }
            $data[$cats[$cat]] += $hours;
        }
        $cats = array_flip($cats);

        foreach ($cats as $key => $cat) {
            $cats[$key] = "%.1f%% ".$cat;
        }

        $this->assignRef("data", $data);
        $this->assignRef("cats", $cats);
        $this->assignRef("user", $user);

    }

    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return null
     */
    function wcomp($tpl = null)
    {
        $model =& $this->getModel();
        $this->_reportGetPeriod();

        $this->filter();
        $this->where();

        $this->_lists["search_options"] = array(
            JHTML::_('select.option', 't.notes', 'Notes'),
            JHTML::_('select.option', 't.worked', 'Date Worked'),
            JHTML::_('select.option', 'p.name', 'Project Name'),
            JHTML::_('select.option', 'u.name', "User Name"),
            JHTML::_('select.option', 'pc.name', "Category Name"),
            JHTML::_('select.option', 'c.company', "Company Name"),
            JHTML::_('select.option', 'c.name', "Company Contact"),
        );

        $this->_wcompGetData();

        $control = $this->_params->get("show_controls");
        if ($control) {
            $this->_reportControls();
        }
    }

    /**
     * The display function
     *
     * @return null
     */
    function _wcompGetData()
    {
        $model    =& $this->getModel();

        $where    = (count($this->_where) ? implode(' AND ', $this->_where) : '');

        $ret      = $model->getTimesheetData($where, null, null, $this->_orderby);
        $report   = array();
        $totals   = array("user" => array(), "proj" => array());
        $cat_name = $this->catBy();
        $users = array();
        $codes = array();
        foreach ($ret as $d) {
            if ($d->category_id < -1) {
                continue;
            }
            for ($i = 1; $i < 7; $i++) {
                $var = "hours".$i;
                $wcVar = "wcCode".$i;
                $hours = $d->$var;
                $user  = $d->author;
                $code  = abs($d->$wcVar);
                if (empty($code)) {
                    continue;
                }
                $cat = (empty($d->$cat_name)) ? JText::_("General") : $d->$cat_name;
                if (empty($codes[$code])) {
                    $codes[$code] = $code;
                }
                $report[$user][$code]  += $hours;
                $totals["user"][$user] += $hours;
                $totals["code"][$code] += $hours;
                $total                 += $hours;
            }
        }
        $this->assignRef("report", $report);
        $this->assignRef("totals", $totals);
        $this->assignRef("total", $total);
        $this->assignRef("codes", $codes);

    }

    /**
     * The display function
     *
     * @return null
     */
    function _hoursGetData()
    {
        $model    =& $this->getModel();

        $where    = (count($this->_where) ? implode(' AND ', $this->_where) : '');

        $ret      = $model->getTimesheetData($where, null, null, $this->_orderby);
        $report   = array();
        $totals   = array("user" => array(), "proj" => array());
        $cat_name = $this->catBy();
        $users = array();
        foreach ($ret as $d) {
            if ($d->category_id < -1) {
                continue;
            }
            $hours = $d->hours;
            $user  = $d->created_by;
            $cat   = (empty($d->$cat_name)) ? JText::_("General") : $d->$cat_name;
            if (empty($users[$user])) {
                $users[$user] = $d->author;
            }
            $report[$user][$cat] += $hours;
            $totals["user"][$user]      += $hours;
            $totals["cat"][$cat]        += $hours;
            $total                      += $hours;
        }
        $this->assignRef("report", $report);
        $this->assignRef("totals", $totals);
        $this->assignRef("total", $total);
        $this->assignRef("users", $users);

    }


    /**
     * The display function
     *
     * @return null
     */
    function _reportGetData()
    {
        $model    =& $this->getModel();
        $where    = (count($this->_where) ? implode(' AND ', $this->_where) : '');
        $ret      = $model->getTimesheetData($where, null, null, $this->_orderby);
        $report   = array();
        $totals   = array("user" => array(), "proj" => array());
        $cat_name = $this->catBy();
        foreach ($ret as $d) {
            $hours = $d->hours;
            $user  = $d->author;
            $proj  = $d->project_name;
            $cat   = (empty($d->$cat_name)) ? JText::_("General") : $d->$cat_name;

            $report[$cat][$proj][$user] += $hours;
            $totals["proj"][$proj]      += $hours;
            $totals["user"][$user]      += $hours;
            $total                      += $hours;
        }
        $users = array_keys($totals["user"]);
        $this->assignRef("report", $report);
        $this->assignRef("totals", $totals);
        $this->assignRef("total", $total);
        $this->assignRef("users", $users);

    }
    /**
     * The display function
     *
     * @return null
     */
    function _reportGetPeriod()
    {
        $model          =& $this->getModel();
        $period         = $model->getPeriodDates();
        $periodType     = $model->get("type");
        $this->_where[] = $model->dateWhere(
            "t.worked",
            $period["start"],
            $period["end"]
        );
        $this->assignRef("period", $period);
        $this->assignRef("periodType", $periodType);

    }
    /**
     * The display function
     *
     * @return null
     */
    function _reportControls()
    {
        $userModel     =& JModel::getInstance("Users", "TimeclockAdminModel");
        $projectModel  =& JModel::getInstance("Projects", "TimeclockAdminModel");
        $customerModel =& JModel::getInstance("Customers", "TimeclockAdminModel");
        $layout        = $this->getLayout();

        $controls["category"] = $projectModel->getParentOptions(
            0,
            $value,
            "Select Category"
        );
        $controls["project"]  = $projectModel->getOptions(
            "WHERE type <> 'CATEGORY'",
            "Select Project",
            array(),
            0
        );
        $controls["projManager"]  = $userModel->getOptions(
            "WHERE p.published = 1",
            "Select Project Manager",
            array()
        );
        $controls["userManager"]  = $userModel->getOptions(
            "WHERE p.published = 1",
            "Select User Manager",
            array()
        );


        $controls["customer"] = $customerModel->getOptions(
            "WHERE published = 1",
            "Select Customer"
        );
        $controls["cat_by"]   = array(
            JHTML::_('select.option', 'category', 'Category'),
            JHTML::_('select.option', 'customer', 'Customer'),
            JHTML::_('select.option', 'project', 'Project'),
        );

        $controls["report_type"] = array(
            JHTML::_('select.option', 'table', 'Table'),
            JHTML::_('select.option', 'graph', 'Graph'),
        );

        $this->assignRef("controls", $controls);

    }
}

?>
