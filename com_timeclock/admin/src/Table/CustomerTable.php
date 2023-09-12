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
 * @copyright  2023 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: $Id: 1c44074ad153f94ca235220ed62f23129b8b335f $    
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock:JoomlaUI
 */
namespace HUGLLC\Component\Timeclock\Administrator\Table;

use Joomla\CMS\Table\Table;

defined('_JEXEC') or die();

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
class CustomerTable extends Table
{
    public $customer_id      = null;
    public $company          = '';
    public $name             = '';
    public $address1         = '';
    public $address2         = '';
    public $city             = '';
    public $state            = '';
    public $zip              = '';
    public $country          = 'US';
    public $notes            = '';
    public $published        = 1;
    public $contact_id       = 0;
    public $created_by       = 0;
    public $created          = '';
    public $modified         = '';
    public $bill_pto         = 0;
    public $checked_out      = 0;
    public $checked_out_time = '';

    /**
     * Constructor
     *
     * @param object &$db Database connector object
     */
    public function __construct(&$db)
    {
        parent::__construct('#__timeclock_customers', "customer_id", $db);
    }
    
}
