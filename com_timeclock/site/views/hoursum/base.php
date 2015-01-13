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
class TimeclockViewsHoursumBase extends JViewBase
{
    private $_projType = array(
        "PROJECT"  => "COM_TIMECLOCK_PROJECT",
        "CATEGORY" => "JCATEGORY",
        "PTO"      => "COM_TIMECLOCK_PTO",
        "HOLIDAY"  => "COM_TIMECLOCK_HOLIDAY",
        "UNPAID"   => "COM_TIMECLOCK_VOLUNTEER",
    );
    /** This is our mime type */
    protected $mimetype = "text/html";
    /** This is our file extension */
    protected $fileext  = "html";
    /** This is our PHPExcel object */
    protected $phpexcel;
    /** This is the line we are currently on */
    protected $line = 1;
    /** This is maximum column */
    protected $maxCol = "A";
    /** This is maximum column */
    protected $colBase = "";
    /**
    * Renders this view
    *
    * @return unknown
    */
    function render()
    {
        if (!TimeclockHelpersContrib::phpexcel()) {
            return false;
        }
        $app = JFactory::getApplication();
        
        $doReport = $app->input->get("report", 1, "int");
        $report_id = $this->model->getState("report.id");
        $this->params    = JComponentHelper::getParams('com_timeclock');
        $this->start  = $this->model->getState('start');
        $this->end    = $this->model->getState('end');


        if (!empty($report_id) && $doReport) {
            $report            = $this->model->getReport();
            $this->data        = $report->timesheets;
            $this->projects    = $report->projects;
            $this->users       = $report->users;
            $this->customers   = $this->report->customers;
            $this->departments = $this->report->departments;
            $file   = str_replace(" ", "_", $report->name);
        } else {
            $this->data        = $this->model->listItems();
            $this->users       = $this->model->listUsers();
            $this->projects    = $this->model->listProjects();
            $this->customers   = $this->model->listCustomers();
            $this->departments = $this->model->listDepartments();
            $file              = "report-live-";
        }
        $file .= $this->start."to".$this->end;
        $this->setup($file);
        $this->export();
        $this->finalize();
        $app->close();
    }
    /**
    * This prints out a row in the file
    *
    * @return string The row created
    */
    protected function export()
    {
        $allproj = array();
        foreach ($this->projects as $cat) {
            foreach ($cat["proj"] as $proj) {
                $allproj[$proj->project_id] = array(
                    "name" => $proj->name,
                    "description" => $proj->description,
                );
            }
        }

        /******************** HOURS BY PROJECT MANAGER ************************/
        $data  = array();
        foreach ($this->data["proj_manager"] as $user_id => $hours) {
            $name = isset($this->users[$user_id]) ? $this->users[$user_id]->name : "";
            if (empty($name)) {
                $name = "User $user_id";
            }
            $data[$name] = $hours;
        }
        $this->dataset(
            JText::_("COM_TIMECLOCK_HOURS_BY_PROJ_MANAGER"),
            JText::_("COM_TIMECLOCK_PROJECT_MANAGER"),
            $data
        );
        /******************** HOURS BY USER MANAGER ************************/
        $data  = array();
        foreach ($this->data["user_manager"] as $user_id => $hours) {
            $name = isset($this->users[$user_id]) ? $this->users[$user_id]->name : "";
            if (empty($name)) {
                $name = "User $user_id";
            }
            $data[$name] = $hours;
        }
        $this->dataset(
            JText::_("COM_TIMECLOCK_HOURS_BY_USER_MANAGER"),
            JText::_("COM_TIMECLOCK_USER_MANAGER"),
            $data
        );
        /******************** HOURS BY PROJECT TYPE ************************/
        $data  = array();
        foreach ($this->data["type"] as $type => $hours) {
            $name = $this->getProjType($type);
            $data[$name] = $hours;
        }
        $this->dataset(
            JText::_("COM_TIMECLOCK_HOURS_BY_PROJECT_TYPE"),
            JText::_("COM_TIMECLOCK_PROJECT_TYPE"),
            $data
        );
        /******************** HOURS BY CATEGORY ************************/
        $data  = array();
        foreach ($this->data["category"] as $cat_id => $hours) {
            $name = isset($this->projects[$cat_id]) ? $this->projects[$cat_id]["name"] : "";
            if (empty($name)) {
                $name = "Category $cat_id";
            }
            $data[$name] = $hours;
        }
        $this->dataset(
            JText::_("COM_TIMECLOCK_HOURS_BY_CATEGORY"),
            JText::_("COM_TIMECLOCK_CATEGORY"),
            $data
        );
        /******************** HOURS BY CUSTOMER ************************/
        $data  = array();
        foreach ($this->data["customer"] as $cust_id => $hours) {
            $name = isset($this->customers[$cust_id]) ? $this->customers[$cust_id]->company : "";
            if (empty($name)) {
                $name = "Customer $cust_id";
            }
            $data[$name] = $hours;
        }
        $this->dataset(
            JText::_("COM_TIMECLOCK_HOURS_BY_CUSTOMER"),
            JText::_("COM_TIMECLOCK_CUSTOMER"),
            $data
        );
        /******************** HOURS BY DEPARTMENT ************************/
        $data  = array();
        foreach ($this->data["department"] as $dept_id => $hours) {
            $name = isset($this->departments[$dept_id]) ? $this->departments[$dept_id]->name : "";
            if (empty($name)) {
                $name = "Department $dept_id";
            }
            $data[$name] = $hours;
        }
        $this->dataset(
            JText::_("COM_TIMECLOCK_HOURS_BY_DEPARTMENT"),
            JText::_("COM_TIMECLOCK_DEPARTMENT"),
            $data
        );
        /******************** HOURS BY WCOMP CODE ************************/
        $data  = array();
        foreach ($this->data["wcomp"] as $code => $hours) {
            $name = sprintf("%04d", $code);
            $data[$name] = $hours;
        }
        $this->dataset(
            JText::_("COM_TIMECLOCK_HOURS_BY_WCOMP_CODE"),
            JText::_("COM_TIMECLOCK_WCOMP_CODE"),
            $data
        );
        
        /******************** HOURS BY PROJECT ************************/
        $data  = array();
        foreach ($this->data["project"] as $proj_id => $hours) {
            $name = isset($allproj[$proj_id]) ? $allproj[$proj_id]["name"] : "";
            if (empty($name)) {
                $name = "Project $proj_id";
            }
            $data[$name] = $hours;
        }
        $this->dataset(
            JText::_("COM_TIMECLOCK_HOURS_BY_PROJECT"),
            JText::_("COM_TIMECLOCK_PROJECT"),
            $data
        );
        /******************** HOURS BY USER ************************/
        $data  = array();
        foreach ($this->data["user"] as $user_id => $hours) {
            $name = isset($this->users[$user_id]) ? $this->users[$user_id]->name : "";
            if (empty($name)) {
                $name = "User $user_id";
            }
            $data[$name] = $hours;
        }
        $this->dataset(
            JText::_("COM_TIMECLOCK_HOURS_BY_USER"),
            JText::_("COM_TIMECLOCK_USER"),
            $data
        );

    }
    /**
    * This prints out a row in the file
    *
    * @param string $file The filename to use
    *
    * @return null
    */
    protected function setup($file)
    {
        $user = JFactory::getUser();
        // Create new PHPExcel object
        $this->phpexcel = new PHPExcel();
        // Set document properties
        $report = JText::sprintf("COM_TIMECLOCK_USERSUM_REPORT_TITLE", $this->start, $this->end);
        $this->phpexcel->getProperties()->setCreator($user->name)
            ->setLastModifiedBy($user->name)
            ->setTitle($report)
            ->setSubject($report)
            ->setKeywords(JText::_("COM_TIMECLOCK_HOURSUM_REPORT"));
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: '.$this->mimetype);
        header('Content-Disposition: attachment;filename="'.$file.'.'.$this->fileext.'"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        // Rename worksheet
        $this->phpexcel->getActiveSheet()->setTitle(JText::_("COM_TIMECLOCK_HOURSUM_REPORT"));
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $this->phpexcel->setActiveSheetIndex(0);

    }
    /**
    * This prints out a row in the file
    *
    * @return null
    */
    protected function finalize()
    {
        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $objWriter->setPreCalculateFormulas(true);
        $objWriter->save('php://output');
    }
    /**
    * This prints out a row in the file
    *
    * @param array $data The data for this row
    *
    * @return string The row created
    */
    protected function dataset($header, $group, $data)
    {
        $places = $this->params->get("decimalPlaces");
        $total  = array();
        $start  = $this->line;
        $sheet  = $this->phpexcel->getActiveSheet();
        $sheet->mergeCells('A'.$this->line.':C'.$this->line);
        $sheet->getStyle("A".$this->line.":C".$this->line)->getFont()->setBold(true);
        $sheet->setCellValue("A".$this->line, $header);
        $this->line++;
        $sheet->setCellValue("A".$this->line, $group);
        $sheet->setCellValue("B".$this->line, JText::_("COM_TIMECLOCK_HOURS"));
        $sheet->setCellValue("C".$this->line, "%");
        $sheet->getStyle("A".$this->line.":C".$this->line)->getFont()->setBold(true);
        $this->line++;
        $tot    = "B".($this->line + count($data));
        // Do the the label and data
        foreach ($data as $name => $hours) {
            $sheet->setCellValue("A".$this->line, $name);
            $total[] = "B".$this->line;
            $sheet->setCellValue("B".$this->line, $hours);
            $perc = "=IF($tot>0,(B".$this->line."/$tot),0)";
            $sheet->setCellValue("C".$this->line, $perc);
            $sheet->getStyle("C".$this->line)->getNumberFormat()->applyFromArray( 
                array( 
                    'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
                )
            );  
            $this->line++;
        }
        $sheet->setCellValue("A".$this->line, JText::_("COM_TIMECLOCK_TOTAL"));
        $sheet->setCellValue("B".$this->line, "=SUM(".implode(",", $total).")");
        $sheet->getStyle("A".$this->line.":A".$this->line)->getFont()->setBold(true);

        while (($this->line - $start) < 15) {
            $this->line++;
        }
        $this->dataGraph($header, $start, count($data));
    }
    /**
    * Creates a graph
    *
    * @param string $title The title for the graph
    * @param int    $row   The row to start on.
    * @param int    $count The number of rows to use
    *
    * @return string The row created
    */
    protected function datagraph($title, $start, $count)
    {
        $row = $start;
        $dataSeriesLabels1 = array(
            new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$'.$row, null, 1),
        );
        $row += 2;
        $end = $count + $row - 1;

        $xAxisTickValues1 = array(
            new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$'.$row.':$A$'.$end, NULL, $count),
        );
        $dataSeriesValues1 = array(
            new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$'.$row.':$B$'.$end, NULL, $count),
        );
        // Build the dataseries
        $series1 = new PHPExcel_Chart_DataSeries(
            PHPExcel_Chart_DataSeries::TYPE_PIECHART,   // plotType
            NULL,   // plotGrouping
            range(0, count($dataSeriesValues1)-1),  // plotOrder
            $dataSeriesLabels1, // plotLabel
            $xAxisTickValues1,  // plotCategory
            $dataSeriesValues1  // plotValues
            );
        // Set up a layout object for the Pie chart
        $layout1 = new PHPExcel_Chart_Layout();
        $layout1->setShowVal(false);
        $layout1->setShowPercent(true);
        // Set the series in the plot area
        $plotArea1 = new PHPExcel_Chart_PlotArea($layout1, array($series1));
        // Set the chart legend
        $legend1 = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, null, false);
        $title1 = new PHPExcel_Chart_Title($title);
        // Create the chart
        $chart1 = new PHPExcel_Chart(
            'chart1',   // name
            $title1,    // title
            $legend1,   // legend
            $plotArea1, // plotArea
            true,   // plotVisibleOnly
            0,  // displayBlanksAs
            null,   // xAxisLabel
            null    // yAxisLabel - Pie charts don't have a Y-Axis
        );
        // Set the position where the chart should appear in the worksheet
        $chart1->setTopLeftPosition('D'.$start);
        $chart1->setBottomRightPosition('J'.($start+ 10));
        
        // Add the chart to the worksheet
        $this->phpexcel->getActiveSheet()->addChart($chart1);
    }
    /**
    * This gets the next column.  This works up to column ZZ.
    *
    * @param array $col The current column
    *
    * @return string The next column
    */
    protected function nextCol($col)
    {
        if ($col == "A") {
            // This is a new row
            $this->colBase = "";
        }
        $col = substr($col, -1);
        if ($col == "Z") {
            // This starts over with "A"
            $col = chr(ord("A") - 1);
        }
        $next = $this->colBase.chr(ord($col) + 1);
        if (substr($next, -1) == "Z") {
            if ($this->colBase == "") {
                $this->colBase = "A";
            } else {
                $this->colBase = chr(ord($this->colBase) + 1);
            }
        }
        return $next;
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