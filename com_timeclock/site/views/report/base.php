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

/** This is our excel writer */
require_once JPATH_COMPONENT.'/contrib/phpexcel/PHPExcel.php';


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
class TimeclockViewsReportBase extends JViewBase
{
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
        $app = JFactory::getApplication();
        
        $useReport = $app->input->get("report", 0, "int");
        $this->params    = JComponentHelper::getParams('com_timeclock');
        $this->start  = $this->model->getState('start');
        $this->end    = $this->model->getState('end');

        if ($useReport) {
            $report       = $this->model->getReport();
            $data         = $report->timesheets;
            $projects     = $report->projects;
            $this->users  = $report->users;
            $file         = "report-saved-";
        } else {
            $data        = $this->model->listItems();
            $this->users = $this->model->listUsers();
            $projects    = $this->model->listProjects();
            $file        = "report-live-";
        }
        $projs = array();
        foreach ($projects as $cat) {
            foreach ($cat["proj"] as $proj) {
                $projs[$proj->project_id] = $proj;
            }
        }
        $file .= $this->start;
        $this->setup($file);
        $this->export($projs, $data);
        $this->finalize();
        $app->close();
    }
    /**
    * This prints out a row in the file
    *
    * @param array $projects The user list to use
    * @param array $data     The data to use
    *
    * @return string The row created
    */
    protected function export($projects, $data)
    {
        $this->header();
        foreach ($projects as $proj_id => $proj) {
            $proj = (object)$proj;
            $proj->data = isset($data[$proj_id]) ? $data[$proj_id] : array();
            $this->row($proj);
        }
        $this->totals($data["totals"]);
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
        $report = JText::sprintf("COM_TIMECLOCK_REPORT_TITLE", $this->start, $this->end);
        $this->phpexcel->getProperties()->setCreator($user->name)
            ->setLastModifiedBy($user->name)
            ->setTitle($report)
            ->setSubject($report)
            ->setKeywords(JText::_("COM_TIMECLOCK_REPORT"));
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
        $this->phpexcel->getActiveSheet()->setTitle(JText::_("COM_TIMECLOCK_REPORT"));
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
    protected function row($data)
    {
        $col = "A";
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, empty($data->name) ? "User ".$data->user_id : $data->name);
        foreach ($this->users as $user) {
            $value = isset($data->data[$user->id]) ? $data->data[$user->id] : 0;
            $col = $this->nextCol($col);
            $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, $value);
        }
        $total = "B".$this->line.":".$col.$this->line;
        $col = $this->nextCol($col);
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, "=SUM(".$total.")");
        $this->phpexcel->getActiveSheet()->getStyle($col.$this->line.":".$col.$this->line)->getFont()->setBold(true);
        $this->line++;
    }
    /**
    * This prints out a row in the file
    *
    * @param array $data The data for this row
    *
    * @return string The row created
    */
    protected function totals($data)
    {
        $col = "A";
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, JText::_("COM_TIMECLOCK_TOTAL"));
        $end = $this->line - 1;
        foreach (range("B", $this->maxCol) as $col) {
            $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, "=SUM(".$col."2:".$col.$end.")");
        }
        $this->phpexcel->getActiveSheet()->getStyle("A".$this->line.":".$this->maxCol.$this->line)->getFont()->setBold(true);
        $this->line++;
    }
    /**
    * This prints out a header row in the file
    *
    * @return string The header row created
    */
    protected function header()
    {
        $col = "A";
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, JText::_("COM_TIMECLOCK_PROJECT"));
        foreach ($this->users as $user) {
            $name = isset($user->name) ? $user->name : "User ".$user->id;
            $col = $this->nextCol($col);
            $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, $name);
        }
        $col = $this->nextCol($col);
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, JText::_("COM_TIMECLOCK_TOTAL"));
        foreach(range('A',$col) as $columnID) {
            $this->phpexcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }
        $this->maxCol = $col;
        $this->phpexcel->getActiveSheet()->getStyle("A".$this->line.":".$this->maxCol.$this->line)->getFont()->setBold(true);
        $this->line++;
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
        $col = substr($col, -1);
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
}
?>