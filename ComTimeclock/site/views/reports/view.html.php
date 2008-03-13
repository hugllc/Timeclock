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
        global $mainframe, $option;
        $layout = JRequest::getVar('layout');
        $model   =& $this->getModel();

        $db =& JFactory::getDBO();
        $filter_order     = $mainframe->getUserStateFromRequest("$option.reports.$layout.filter_order", 'filter_order', 'u.name', 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest("$option.reports.$layout.filter_order_Dir", 'filter_order_Dir', 'asc', 'word');
        $filter_state     = $mainframe->getUserStateFromRequest("$option.reports.$layout.filter_state", 'filter_state', '', 'word');
        $search           = $mainframe->getUserStateFromRequest("$option.reports.$layout.search", 'search', '', 'string');
        $search           = JString::strtolower($search);
        $search_filter    = $mainframe->getUserStateFromRequest("$option.reports.$layout.search_filter", 'search_filter', 'notes', 'string');

        $this->_orderby        = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
                
        $this->where = array();

        if ($search) {
            $this->_where[] = 'LOWER(t.'.$search_filter.') LIKE '.$db->Quote('%'.$db->getEscaped($search, true).'%', false);
        }

        if (method_exists($this, $layout)) {
            $this->$layout($tpl);
        } else {
            $this->_where          = (count($this->_where) ? ' WHERE ' . implode(' AND ', $this->_where) : '');
            $data = $model->getTimesheetData($this->_where, $orderby);
            $dates["start"] = $model->getStartDate();
            $dates["end"] = $model->getEndDate();

            $this->assignRef("data", $data);        
            $this->assignRef("dates", $dates);        
            parent::display($tpl);
        }        
        
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

        $this->_where          = (count($this->_where) ? ' WHERE ' . implode(' AND ', $this->_where) : '');
        $data = $model->getTimesheetData($this->_where, $orderby);

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
                    if (empty($report[$user_id]["name"])) $report[$user_id]["name"] = $dates[$key]["rec"]->user_name;
        
                    $projname = $dates[$key]["rec"]->project_name;
                    $username = $dates[$key]["rec"]->user_name;
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

        $this->_where          = (count($this->_where) ? ' WHERE ' . implode(' AND ', $this->_where) : '');
        $data = $model->getTimesheetData($this->_where, $orderby);

        $notes = array();
        // Make the data into something usefull for this particular report
        foreach ($data as $user_id => $projdata) {
            foreach ($projdata as $proj_id => $dates) {
                foreach ($period["dates"] as $key => $uDate) {
                    $projname = $dates[$key]["rec"]->project_name;
                    $username = $dates[$key]["rec"]->user_name;
                    $notes[$username][$projname][$key]["hours"] += $dates[$key]["hours"];
                    $notes[$username][$projname][$key]["notes"] .= $dates[$key]["notes"];
                }
            }
        }

        $this->assignRef("notes", $notes);        

        parent::display($tpl);

    }


}

?>