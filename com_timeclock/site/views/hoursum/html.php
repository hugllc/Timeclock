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
class TimeclockViewsHoursumHtml extends JViewHtml
{
    private $_projType = array(
        "PROJECT"  => "COM_TIMECLOCK_PROJECT",
        "CATEGORY" => "JCATEGORY",
        "PTO"      => "COM_TIMECLOCK_PTO",
        "HOLIDAY"  => "COM_TIMECLOCK_HOLIDAY",
        "UNPAID"   => "COM_TIMECLOCK_VOLUNTEER",
    );
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

        $this->_dataset = new JLayoutFile('dataset', __DIR__.'/layouts');
        $this->_export  = new JLayoutFile('export', dirname(__DIR__).'/layouts');
        $this->_control = new JLayoutFile('reportcontrol', dirname(__DIR__).'/layouts');

        if (empty($this->report_id)) {
            $this->data              = $this->model->listItems();
            $this->users             = $this->model->listUsers();
            $this->projects          = $this->model->listProjects();
            $this->customers         = $this->model->listCustomers();
            $this->departments       = $this->model->listDepartments();
            $this->filter            = $this->model->getState("filter");
            $this->filter->start     = $this->start;
            $this->filter->end       = $this->end;
            $this->filter->report_id = $this->report_id;
        } else {
            $this->report      = $this->model->getReport();
            $this->filter      = $this->report->filter;
            $this->users       = $this->report->users;
            $this->data        = $this->report->timesheets;
            $this->projects    = $this->report->projects;
            $this->customers   = $this->report->customers;
            $this->departments = $this->report->departments;
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
    * This creates a pie graph for us
    *
    * @param string $title The header for the graph
    * @param array  $data  The data to use for the graph
    *
    * @return binary string that is the image
    */
    protected function pie($title, $data)
    {
        $total = 0;
        foreach ($data as $val) {
            $total += $val;
        }
        $png = "";
        if (TimeclockHelpersContrib::phpgraph() && !empty($total)) {
            $graph = new PHPGraphLibPie(400, 200);
            $graph->addData($data);
            $graph->setTitle($title);
            $graph->setLabelTextColor('black');
            $graph->setLegendTextColor('black');

            ob_start();
            $graph->createGraph();
            $png = ob_get_contents();
            ob_end_clean();
        }
        return $png;
    }
    /**
    * This creates a pie graph for us
    *
    * @param string $type The type of project
    *
    * @return binary string that is the image
    */
    protected function getProjType($type)
    {
        if (isset($this->_projType[$type])) {
            return JText::_($this->_projType[$type]);
        }
        return JText::_("COM_TIMECLOCK_UNKNOWN");
    }
}
?>