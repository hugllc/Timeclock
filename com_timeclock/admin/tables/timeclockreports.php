<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_Preferences is a Joomla! 1.6 component
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
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2023 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: $Id: 008825a1a092a2ab5410f0cd663815540d2a905b $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock:JoomlaUI
 */

defined('_JEXEC') or die();

use Joomla\CMS\Table\Table;

/**
 * Preferences table
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2023 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock:JoomlaUI
 */
class TableTimeclockReports extends Table
{
    public $report_id      = null;
    public $name             = '';
    public $description      = '';
    public $startDate        = '';
    public $endDate          = '';
    public $created_by       = 0;
    public $created          = '';
    public $modified         = '';
    public $type             = 'generic';
    public $customers        = '';
    public $filter           = '';
    public $departments      = '';
    public $projects         = '';
    public $timesheets       = '';
    public $users            = '';

    /**
     * Constructor
     *
     * @param object &$db Database connector object
     */
    public function __construct(&$db)
    {
        parent::__construct('#__timeclock_reports', "report_id", $db);
    }
    /**
     * Checks the row
     *
     * @return array
     */
    public function check()
    {
        return true;
    }


}
