<?php
/**
 * This component is for tracking tim
 *
 * PHP Version 5
 *
 * <pre>
 * com_timeclock is a Joomla! 3.1 component
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
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2023 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: $Id: 1704fec720b1e135e464969c032dd8cf90adeb1d $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
defined( '_JEXEC' ) or die();

 
/**
 * Helpers for viewing stuff
 *
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2023 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockHelpersContrib
{
    /**
     * Checks for PHPExcel, and includes what is needed.
     *
     * @access public
     * @return bool True if it exists, false otherwise
     */
    static public function phpspreadsheet()
    {
        if (file_exists(JPATH_COMPONENT.'/contrib/vendor/phpoffice/phpspreadsheet/README.md')) {
            include_once JPATH_COMPONENT.'/contrib/vendor/autoload.php';
            return true;
        }
        return false;
    }
    /**
     * Checks for PHPGraphLib, and includes what is needed.
     *
     * @access public
     * @return bool True if it exists, false otherwise
     */
    static public function phpgraph()
    {
        if (file_exists(JPATH_COMPONENT.'/contrib/phpgraph/phpgraphlib.php')) {
            include_once JPATH_COMPONENT.'/contrib/phpgraph/phpgraphlib.php';
            include_once JPATH_COMPONENT.'/contrib/phpgraph/phpgraphlib_pie.php';
            return true;
        }
        return false;
    }

}
