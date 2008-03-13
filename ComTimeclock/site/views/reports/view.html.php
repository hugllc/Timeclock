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
        $this->layout = JRequest::getVar('layout');
        $this->model   =& $this->getModel();

        $db =& JFactory::getDBO();
        $filter_order     = $mainframe->getUserStateFromRequest("$option.reports.$layout.filter_order", 'filter_order', 'u.name', 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest("$option.reports.$layout.filter_order_Dir", 'filter_order_Dir', 'asc', 'word');
        $filter_state     = $mainframe->getUserStateFromRequest("$option.reports.$layout.filter_state", 'filter_state', '', 'word');
        $search           = $mainframe->getUserStateFromRequest("$option.reports.$layout.search", 'search', '', 'string');
        $search           = JString::strtolower($search);
        $search_filter    = $mainframe->getUserStateFromRequest("$option.reports.$layout.search_filter", 'search_filter', 'notes', 'string');
                
        $this->where = array();

        if ($search) {
            $this->where[] = 'LOWER(t.'.$search_filter.') LIKE '.$db->Quote('%'.$db->getEscaped($search, true).'%', false);
        }



        $this->where          = (count($this->where) ? ' WHERE ' . implode(' AND ', $this->where) : '');
        $orderby        = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;

        $data = $this->model->getTimesheetData($where, $orderby);
        
        $dates["start"] = $this->model->getStartDate();
        $dates["end"] = $this->model->getEndDate();
        
        $this->assignRef("data", $data);        
        $this->assignRef("user", $user);        
        $this->assignRef("dates", $dates);        

        $this->payroll($layout);

        parent::display($tpl);

    }
    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return null
     */
    function payroll()
    {
        if ($this->layout !== "payroll") return;
        
        $this->where[] = $this->model->periodWhere("t.worked");
        
        $period  = $this->model->getPeriod();
        $this->assignRef("period", $period);        

    }
    /**
     * The display function
     *
     * @param string $layout The template to use
     *
     * @return null
     */
    function timesheet($layout)
    {
        if ($layout == "addhours") return;

        $model   =& $this->getModel();
        $hours   = $model->getTimesheetData();
        $period  = $model->getPeriod();

        $this->assignRef("hours", $hours);
        $this->assignRef("period", $period);        
    }

    /**
     * The display function
     *
     * @param string $layout The template to use
     *
     * @return null
     */
    function addhours($layout)
    {
        if ($layout != "addhours") return;

        $model   =& $this->getModel();
        $data     = $model->getData();

        $referer  = JRequest::getVar('referer', $_SERVER["HTTP_REFERER"], '', 'string');
        $projid   = JRequest::getVar('projid', null, '', 'string');

        $this->assignRef("projid", $projid);
        $this->assignRef("referer", $referer);
        $this->assignRef("data", $data);
    }
    
    /**
     * Checks employment dates and says if the user can enter hours on that date or not
     *
     * @param int $date The unix date to check
     *
     * @return bool
     */
    function checkDate($date)
    {
        return TimeclockController::checkEmploymentDates($this->employmentDates["start"], $this->employmentDates["end"], $date);
    }
}

?>