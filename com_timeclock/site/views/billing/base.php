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
defined('_JEXEC') or die();

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;

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
class TimeclockViewsBillingBase extends HtmlView
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
    function display($tpl = NULL)
    {
        if (!TimeclockHelpersContrib::phpspreadsheet()) {
            return false;
        }
        $app = Factory::getApplication();
        
        $doReport = $app->input->get("report", 1, "int");
        $report_id = $this->getModel()->getState("report.id");
        $this->params    = ComponentHelper::getParams('com_timeclock');
        $this->start  = $this->getModel()->getState('start');
        $this->end    = $this->getModel()->getState('end');


        if (!empty($report_id) && $doReport) {
            $report         = $this->getModel()->getReport();
            $this->data     = $report->timesheets;
            $this->projects = $report->projects;
            $this->users    = $report->users;
            $file           = str_replace(" ", "_", $report->name)."-billing-";
        } else {
            $this->data     = $this->getModel()->listItems();
            $this->users    = $this->getModel()->listUsers();
            $this->projects = $this->getModel()->listProjects();
            $file           = "billing-report-live-";
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
        $this->header();
        foreach ($this->users as $user_id => $user) {
            if ($user_id == 0) {
                continue;
            }
            $user = (object)$user;
            $user->data   = isset($this->data[$user_id]) ? $this->data[$user_id] : array();
            $this->row($user);
        }
        $this->totals($this->data["totals"]);
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
        $user = Factory::getUser();
        // Create new PHPExcel object
        $this->phpexcel = new Spreadsheet();
        // Set document properties
        $report = Text::sprintf("COM_TIMECLOCK_BILLING_REPORT_TITLE", $this->start, $this->end);
        $this->phpexcel->getProperties()->setCreator($user->name)
            ->setLastModifiedBy($user->name)
            ->setTitle($report)
            ->setSubject($report)
            ->setKeywords(Text::_("COM_TIMECLOCK_BILLING_REPORT"));
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
        $this->phpexcel->getActiveSheet()->setTitle(Text::_("COM_TIMECLOCK_BILLING_REPORT"));
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
        $objWriter = new Xlsx($this->phpexcel);
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
        $places = $this->params->get("decimalPlaces");
        $total  = array();
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, empty($data->name) ? "User ".$data->user_id : $data->name);
        $col   = $this->nextCol($col);
        $hours = $col.$this->line;
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, $data->data["hours"]);
        $col  = $this->nextCol($col);
        $rate = $col.$this->line;
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, $data->data["rate"]);
        $this->phpexcel->getActiveSheet()->getStyle($rate)->getNumberFormat()->applyFromArray( 
            array( 
                'code' => PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE
            )
        );  
        $col = $this->nextCol($col);
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, "=$hours*$rate");
        $this->phpexcel->getActiveSheet()->getStyle($col.$this->line.":".$col.$this->line)->getFont()->setBold(true);
        $this->phpexcel->getActiveSheet()->getStyle($col.$this->line)->getNumberFormat()->applyFromArray( 
            array( 
                'code' => PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE
            )
        );  
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
        $places = $this->params->get("decimalPlaces");
        $this->phpexcel->getActiveSheet()->setCellValue("A".$this->line, Text::_("COM_TIMECLOCK_TOTAL"));
        $this->phpexcel->getActiveSheet()->getStyle("A".$this->line.":".$this->maxCol.$this->line)->getFont()->setBold(true);
        $end = $this->line - 1;
        $this->phpexcel->getActiveSheet()->setCellValue("B".$this->line, "=SUM(B2:B".$end.")");
        $this->phpexcel->getActiveSheet()->setCellValue($this->maxCol.$this->line, "=SUM(".$this->maxCol."2:".$this->maxCol.$end.")");
        $this->phpexcel->getActiveSheet()->getStyle($this->maxCol.$this->line)->getNumberFormat()->applyFromArray( 
            array( 
                'code' => PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE
            )
        );  
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
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, Text::_("COM_TIMECLOCK_USER"));
        $col = $this->nextCol($col);
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, Text::_("COM_TIMECLOCK_HOURS"));
        $col = $this->nextCol($col);
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, Text::_("COM_TIMECLOCK_BILLABLE_RATE"));
        $col = $this->nextCol($col);
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, Text::_("COM_TIMECLOCK_TOTAL_COST"));
        $columnID = "A";
        $this->phpexcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        while ($col != $columnID) {
            $columnID = $this->nextCol($columnID);
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
}
?>