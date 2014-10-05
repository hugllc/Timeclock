<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_Preferences is a Joomla! 1.6 component
 * Copyright (C) 2014 Hunt Utilities Group, LLC
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful;
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: $Id: 51c1eda1b710154b4d5d5e001ecd4e395b81a19b $    
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock:JoomlaUI
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Preferences table
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock:JoomlaUI
 */
class TableTimeclockPto extends JTable
{
    public $pto_id           = null;
    public $type             = 'ACCRUAL';
    public $user_id          = 0;
    public $hours            = 0;
    public $link_id          = 0;
    public $valid_from       = 0;
    public $valid_to         = 0;
    public $created_by       = 0;
    public $created          = '';
    public $modified         = '';
    public $notes            = '';
    public $checked_out      = 0;
    public $checked_out_time = '';

    /**
     * Constructor
     *
     * @param object &$db Database connector object
     */
    public function __construct(&$db)
    {
        parent::__construct('#__timeclock_pto', "pto_id", $db);
    }
    /**
     * Checks the row
     *
     * @return array
     */
    public function check()
    {

        if (empty($this->user_id) || empty($this->valid_from) || empty($this->valid_to)) {
            return false;
        }
        if (empty($this->type)) {
            $this->type = "ACCRUAL";
        }
        $places = (int)TimeclockHelpersTimeclock::getParam("decimalPlaces");
        $this->hours = round($this->hours, $places);
        return true;
    }
    
}
