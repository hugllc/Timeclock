<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.6 component
 * Copyright (C) 2023 Hunt Utilities Group, LLC
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
 * @copyright  2023 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: $Id: e1fc5c887a1edad708ebadc65fbd04a50869766b $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
/** Check to make sure we are under Joomla */
namespace HUGLLC\Component\Timeclock\Site\View\Payroll;

use HUGLLC\Component\Timeclock\Site\View\Payroll\BaseView;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

defined('_JEXEC') or die();

/**
 * HTML View class for the ComTimeclockWorld Component
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2023 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class CsvView extends BaseView
{
    /** This is our mime type */
    protected $mimetype = "text/csv";
    /** This is our file extension */
    protected $fileext  = "csv";
    /**
    * This prints out a row in the file
    *
    * @return null
    */
    protected function finalize()
    {
        $objWriter = new Csv($this->phpexcel);
        $objWriter->setDelimiter(',');
        $objWriter->setEnclosure('"');
        $objWriter->setLineEnding("\r\n");
        $objWriter->setSheetIndex(0);
        $objWriter->setPreCalculateFormulas(true);
        $objWriter->save('php://output');
    }
}
?>