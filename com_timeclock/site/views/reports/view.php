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

class TimeclockViewReportsBase extends JViewLegacy
{
    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return null
     */
    public function pdisplay($tpl = null)
    {
        $mainframe = JFactory::getApplication();

        $layout        = $this->getLayout();
        $model         = $this->getModel();
        $this->_params = $mainframe->getParams('com_timeclock');

        $this->assignRef("params", $this->_params);

        $this->_where = array();
        $this->cellFill();

        $sqlDateFormat = "%Y-%m-%d %H:%M:%S";
        $this->assignRef("sqlDateFormat", $sqlDateFormat);

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
    public function where()
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
    public function catBy()
    {
        if (!is_object($this->_params)) {
            $this->_params = $mainframe->getParams('com_timeclock');
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
    public function cellFill()
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
    public function filter()
    {
        $mainframe = JFactory::getApplication();
        $option = JRequest::getCmd('option');
        $layout = $this->getLayout();
        $db     = JFactory::getDBO();

        if (!is_object($this->_params)) {
            $this->_params = $mainframe->getParams('com_timeclock');
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

        $filters = array(
            "filter_order_Dir", "filter2_order_Dir", "filter3_order_Dir"
        );
        foreach ($filters as $filter) {
            if (!isset($$filter) || trim(strtolower($$filter)) == "asc")
            {
                $$filter = "ASC";
            } else {
                $$filter = "DESC";
            }
        }
        if (!empty($filter_order)) {
            $this->_orderby = ' ORDER BY '
                                .TimeclockAdminSql::dotNameQuote($filter_order)
                                .' '.$filter_order_Dir;
            if (!empty($filter2_order)) {
                $this->_orderby .= ", "
                                .TimeclockAdminSql::dotNameQuote($filter2_order)
                                .' '.$filter2_order_Dir;
            }
            if (!empty($filter3_order)) {
                $this->_orderby .= ", "
                                .TimeclockAdminSql::dotNameQuote($filter3_order)
                                .' '. $filter3_order_Dir;
            }
        } else {
            $this->_orderby = "";
        }

        if ($search) {
            $this->_where[] = 'LOWER('
                        .TimeclockAdminSql::dotNameQuote($search_filter).') LIKE '
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
    public function pagination($total)
    {
        $mainframe = JFactory::getApplication();
        $option = JRequest::getCmd('option');
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
    public function payroll($tpl = null)
    {

        $model = $this->getModel();
        $model->setPeriodType("payperiod");

        $this->filter();

        $this->_where[] = $model->periodWhere("t.worked");

        $where = (count($this->_where) ? implode(' AND ', $this->_where) : '');
        $ret   = $model->getTimesheetData($where, null, null, $this->_orderby);
        $data  = array();

        foreach ($ret as $d) {
            if (!isset($data[$d->user_id])) {
                $data[$d->user_id] = array();
            }
            if (!isset($data[$d->user_id][$d->project_id])) {
                $data[$d->user_id][$d->project_id] = array();
            }
            if (!isset($data[$d->user_id][$d->project_id][$d->worked])) {
                $data[$d->user_id][$d->project_id][$d->worked] = array(
                    "hours" => 0.0,
                    "notes" => "",
                );
            }
            $data[$d->user_id][$d->project_id][$d->worked]['hours'] += $d->hours;
            $data[$d->user_id][$d->project_id][$d->worked]['notes'] .= $d->notes;
            $data[$d->user_id][$d->project_id][$d->worked]['rec']    = $d;
        }

        $period = $model->getPeriodDates();
        $periodType     = $model->get("type");
        $this->assignRef("period", $period);
        $this->assignRef("periodType", $periodType);

        $days   = 7;

        $report = array();
        $notes  = array();
        $totals = array("type" => array(), "user" => array(), "total" => 0.0);
        $weeks  = round($period["length"] / $days);
        if (($period["length"] % $days) > 0) {
            $weeks++;  // Get that extra bit in.
        }
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

                    if (!isset($report[$user_id])) {
                        $report[$user_id] = array();
                    }
                    if (!isset($report[$user_id][$week])) {
                        $report[$user_id][$week] = array();
                    }
                    if (!isset($report[$user_id][$week][$type])) {
                        $report[$user_id][$week][$type] = array("hours" => 0.0);
                    }
                    if (!isset($report[$user_id][$week]["TOTAL"])) {
                        $report[$user_id][$week]["TOTAL"] = array("hours" => 0.0);
                    }


                    $report[$user_id][$week][$type]["hours"]   += $hours;
                    $report[$user_id][$week]["TOTAL"]["hours"] += $hours;
                    $username = $dates[$key]["rec"]->author;
                    if (empty($username)) {
                        $username = $user_id;
                    }
                    $projname = $dates[$key]["rec"]->project_name;
                    if (empty($projname)) {
                        $projname = $proj_id;
                    }
                    if (empty($report[$user_id]["name"])) {
                        $report[$user_id]["name"] = $username;
                    }

                    if (!isset($notes[$username])) {
                        $notes[$username] = array();
                    }
                    if (!isset($notes[$username][$projname])) {
                        $notes[$username][$projname] = array();
                    }
                    if (!isset($notes[$username][$projname][$key])) {
                        $notes[$username][$projname][$key] = array(
                            "notes" => "",
                            "hours" => 0.0,
                        );
                    }
                    $notes[$username][$projname][$key]["hours"]
                        += $dates[$key]["hours"];
                    $notes[$username][$projname][$key]["notes"]
                        .= $dates[$key]["notes"];

                    if (!isset($totals["type"][$week])) {
                        $totals["type"][$week] = array();
                    }
                    if (!isset($totals["type"][$week][$type])) {
                        $totals["type"][$week][$type] = 0.0;
                    }
                    if (!isset($totals["type"][$week]["TOTAL"])) {
                        $totals["type"][$week]["TOTAL"] = 0.0;
                    }
                    if (!isset($totals["user"][$user_id])) {
                        $totals["user"][$user_id] = 0.0;
                    }
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
    public function notes($tpl = null)
    {
        $model = $this->getModel();
        $this->_reportGetPeriod();
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
        $this->_lists["search_options_default"] = "";
        $total = $model->getTimesheetDataCount($where);
        $this->pagination($total);
        $notes = $model->getTimesheetData(
            $where,
            $this->_limitstart,
            $this->_limit,
            $this->_orderby
        );
        $this->assignRef("notes", $notes);

        $control = $this->_params->get("show_controls");
        if ($control) {
            $this->_reportControls();
            $cat_name = $this->catBy();
        }

    }
    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return null
     */
    public function report($tpl = null)
    {
        $model   = $this->getModel();
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

    public function hours($tpl = null)
    {
        $model   = $this->getModel();
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

    public function hoursgraph($tpl = null)
    {

        $model   = $this->getModel();
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
        $jpgraph_path = TimeclockHelper::getParam("JPGraphPath");
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
        $document = &JFactory::getDocument();
        $document->setMimeEncoding("image/png");
    }
    /**
     * The display function
     *
     * @return null
     */
    private function _hoursgraphGetData()
    {
        $model    = $this->getModel();
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
            $cat   = (empty($d->$cat_name)) ? JText::_("COM_TIMECLOCK_GENERAL") : $d->$cat_name;
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
    public function wcomp($tpl = null)
    {
        $this->enable = (bool)TimeclockHelper::getParam("wCompEnable");
        $model = $this->getModel();
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
    private function _wcompGetData()
    {
        $model    = $this->getModel();

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
                $cat = (empty($d->$cat_name)) ? JText::_("COM_TIMECLOCK_GENERAL") : $d->$cat_name;
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
    private function _hoursGetData()
    {
        $model    = $this->getModel();

        $where    = (count($this->_where) ? implode(' AND ', $this->_where) : '');

        $ret      = $model->getTimesheetData($where, null, null, $this->_orderby);
        $report   = array();
        $totals   = array("user" => array(), "cat" => array());
        $cat_name = $this->catBy();
        $total    = 0.0;
        $users    = array();
        foreach ($ret as $d) {
            if ($d->category_id < -1) {
                continue;
            }
            $hours = (float)$d->hours;
            $user  = $d->created_by;
            $cat   = (empty($d->$cat_name)) ? JText::_("COM_TIMECLOCK_GENERAL") : $d->$cat_name;
            if (empty($users[$user])) {
                $users[$user] = !empty($d->author) ? $d->author : $d->user_id;
            }
            if (!isset($report[$user])) {
                $report[$user] = array();
            }
            if (!isset($report[$user][$cat])) {
                $report[$user][$cat] = 0.0;
            }
            if (!isset($totals["user"][$user])) {
                $totals["user"][$user] = 0.0;
            }
            if (!isset($totals["cat"][$cat])) {
                $totals["cat"][$cat] = 0.0;
            }
            $report[$user][$cat]   += $hours;
            $totals["user"][$user] += $hours;
            $totals["cat"][$cat]   += $hours;
            $total                 += $hours;
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
    private function _reportGetData()
    {
        $model    = $this->getModel();
        $where    = (count($this->_where) ? implode(' AND ', $this->_where) : '');
        $ret      = $model->getTimesheetData($where, null, null, $this->_orderby);
        $report   = array();
        $totals   = array("user" => array(), "proj" => array());
        $cat_name = $this->catBy();
        $total    = 0.0;
        foreach ($ret as $d) {
            $hours = (float)$d->hours;
            $user  = !empty($d->author) ? $d->author : $d->user_id;
            $proj  = !empty($d->project_name) ? $d->project_name : $d->proj_id;
            $cat   = (empty($d->$cat_name)) ? JText::_("COM_TIMECLOCK_GENERAL") : $d->$cat_name;
            if (!isset($report[$cat])) {
                $report[$cat] = array();
            }
            if (!isset($report[$cat][$proj])) {
                $report[$cat][$proj] = array();
            }
            if (!isset($report[$cat][$proj][$user])) {
                $report[$cat][$proj][$user] = 0.0;
            }
            if (!isset($totals["proj"][$proj])) {
                $totals["proj"][$proj] = 0.0;
            }
            if (!isset($totals["user"][$user])) {
                $totals["user"][$user] = 0.0;
            }
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
    private function _reportGetPeriod()
    {
        $model          = $this->getModel();
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
    private function _reportControls()
    {
        $userModel     = JModelLegacy::getInstance("Users", "TimeclockAdminModel");
        $projectModel  = JModelLegacy::getInstance("Projects", "TimeclockAdminModel");
        $customerModel = JModelLegacy::getInstance("Customers", "TimeclockAdminModel");
        $layout        = $this->getLayout();

        $controls["category"] = $projectModel->getParentOptions(
            0,
            array(),
            "Select Category"
        );
        $controls["project"]  = $projectModel->getOptions(
            "WHERE type <> 'CATEGORY'",
            "Select Project",
            array(),
            0
        );
        $controls["projManager"]  = $userModel->getOptions(
            "WHERE u.block = 0",
            "Select Project Manager",
            array()
        );
        $controls["userManager"]  = $userModel->getOptions(
            "WHERE u.block = 0",
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