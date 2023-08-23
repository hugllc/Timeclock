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
class TimeclockViewsPayrollBase extends JViewBase
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
        $app = Factory::getApplication();
        
        $doReport = $app->input->get("report", 1, "int");
        $report_id = $this->model->getState("report.id");
        $this->params    = ComponentHelper::getParams('com_timeclock');
        $this->payperiod = $this->model->getState('payperiod');

        if (!empty($report_id) && $doReport) {
            $report = $this->model->getReport();
            $data   = $report->timesheets;
            $users  = $report->users;
            $file   = str_replace(" ", "_", $report->name);
        } else {
            $data  = $this->model->listItems();
            $users = $this->model->listUsers();
            $file   = "payroll-live-".$this->payperiod->start;
        }
        $this->setup($file);
        $this->export($users, $data);
        $this->finalize();
        $app->close();
    }
    /**
    * This prints out a row in the file
    *
    * @param array $users The user list to use
    * @param array $data  The data to use
    *
    * @return string The row created
    */
    protected function export($users, $data)
    {
        $this->header();
        foreach ($users as $user_id => $user) {
            $user = (object)$user;
            $user->data = isset($data[$user_id]) ? $data[$user_id] : array();
            $this->row($user);
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
        $user = Factory::getUser();
        // Create new PHPExcel object
        $this->phpexcel = new PHPExcel();
        // Set document properties
        $payroll = Text::sprintf("COM_TIMECLOCK_PAYROLL_TITLE", $this->payperiod->start, $this->payperiod->end);
        $this->phpexcel->getProperties()->setCreator($user->name)
            ->setLastModifiedBy($user->name)
            ->setTitle($payroll)
            ->setSubject($payroll)
            ->setKeywords(Text::_("COM_TIMECLOCK_PAYROLL"));
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
        $this->phpexcel->getActiveSheet()->setTitle($this->payperiod->start);
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
        $total = array();
        $worked   = 0;
        $pto      = 0;
        $holiday  = 0;
        $subtotal = 0;
        $overtime = 0;
        for ($w = 0; $w < $this->payperiod->subtotals; $w++) {
            if (isset($data->data[$w])) {
                $d        = (object)$data->data[$w];
                $worked   += $d->worked;
                $pto      += $d->pto;
                $holiday  += $d->holiday;
                $subtotal += $d->subtotal;
                $overtime += $d->overtime;
            }
        }
        $col = $this->nextCol($col);
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, $worked);
        $col = $this->nextCol($col);
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, $pto);
        $col = $this->nextCol($col);
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, $holiday);
        $col = $this->nextCol($col);
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, $subtotal);
        $this->phpexcel->getActiveSheet()->getStyle($col.$this->line.":".$col.$this->line)->getFont()->setBold(true);
        $total[] = $col.$this->line;
        $col = $this->nextCol($col);
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, $overtime);
        $this->phpexcel->getActiveSheet()->getStyle($col.$this->line.":".$col.$this->line)->getFont()->setBold(true);
        $total[] = $col.$this->line;
        $col = $this->nextCol($col);
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, "=SUM(".implode(",", $total).")");
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
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, Text::_("COM_TIMECLOCK_TOTAL"));
        $end = $this->line - 1;
        while ($col != $this->maxCol) {
            $col = $this->nextCol($col);
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
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, Text::_("COM_TIMECLOCK_EMPLOYEE"));
        
        $col = $this->nextCol($col);
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, Text::_("COM_TIMECLOCK_WORKED"));
        $col = $this->nextCol($col);
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, Text::_("COM_TIMECLOCK_PTO"));
        $col = $this->nextCol($col);
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, Text::_("COM_TIMECLOCK_HOLIDAY"));
        $col = $this->nextCol($col);
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, Text::_("COM_TIMECLOCK_SUBTOTAL"));
        $col = $this->nextCol($col);
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, Text::_("COM_TIMECLOCK_OVERTIME"));
        $col = $this->nextCol($col);
        $this->phpexcel->getActiveSheet()->setCellValue($col.$this->line, Text::_("COM_TIMECLOCK_TOTAL"));
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
    * This gets the next column
    *
    * @param array $col The current column
    *
    * @return string The next column
    */
    protected function nextCol($col)
    {
        $next = chr(ord($col) + 1);
        return $next;
    }
}
?>