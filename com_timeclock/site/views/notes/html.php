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
 * @version    GIT: $Id: e1fc5c887a1edad708ebadc65fbd04a50869766b $
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
class TimeclockViewsNotesHtml extends JViewHtml
{
    /**
    * Renders this view
    *
    * @return unknown
    */
    function render()
    {
        $app = JFactory::getApplication();
        $layout = $this->getLayout();
        
        $this->params    = JComponentHelper::getParams('com_timeclock');
        $this->start     = $this->model->getState('start');
        $this->end       = $this->model->getState('end');
        $this->report_id = $this->model->getState("report.id");
        
        JHTML::stylesheet(
            JURI::base().'components/com_timeclock/css/timeclock.css', 
            array(), 
            true
        );

        $this->_header   = new JLayoutFile('header', __DIR__.'/layouts');
        $this->_row      = new JLayoutFile('row', __DIR__.'/layouts');
        $this->_totals   = new JLayoutFile('totals', __DIR__.'/layouts');
        $this->_category = new JLayoutFile('category', __DIR__.'/layouts');
        $this->_toolbar  = new JLayoutFile('toolbar', __DIR__.'/layouts');
        $this->_export   = new JLayoutFile('export', dirname(__DIR__).'/layouts');
        $this->_notes    = new JLayoutFile('notes', dirname(__DIR__).'/layouts');
        $this->_control  = new JLayoutFile('reportcontrol', dirname(__DIR__).'/layouts');

        if (empty($this->report_id)) {
            $this->data              = $this->model->listItems();
            $this->users             = $this->model->listUsers();
            $this->projects          = $this->model->listProjects();
            $this->filter            = $this->model->getState("filter");
            $this->filter->start     = $this->start;
            $this->filter->end       = $this->end;
            $this->filter->report_id = $this->report_id;
        } else {
            $this->report   = $this->model->getReport();
            $this->data     = $this->report->timesheets;
            $this->users    = $this->report->users;
            $this->projects = $this->report->projects;
            $this->filter   = $this->report->filter;
            
            $this->filter->report_id = $this->report_id;
        }
        $this->export   = array(
            "CSV" => "csv",
            "Excel 2007" => "xlsx",
        );
        JHTML::stylesheet(
            JURI::base().'components/com_timeclock/css/timeclock.css', 
            array(), 
            true
        );

        return parent::render();
    }
    /**
    * This routine formats currency
    *
    * @param float $amount The number to format as currency
    *
    * @return string The number, formatted as currency
    */
    public function currency($amount)
    {
        return "$".number_format($amount, 2);
    }
}
?>