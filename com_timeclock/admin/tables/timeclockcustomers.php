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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
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
class TableTimeclockCustomers extends JTable
{
    /**
     * Primary Key
     *
     * @var int
     */
    public $id = null;

    /**
     * Variable
     *
     * @var string
     */
    public $company = null;
    /**
     * Variable
     *
     * @var string
     */
    public $name = '';
    /**
     * Variable
     *
     * @var string
     */
    public $address1 = '';
    /**
     * Variable
     *
     * @var string
     */
    public $address2 = '';
    /**
     * Variable
     *
     * @var string
     */
    public $city = '';
    /**
     * Variable
     *
     * @var string
     */
    public $state = '';
    /**
     * Variable
     *
     * @var string
     */
    public $zip = '';
    /**
     * Variable
     *
     * @var string
     */
    public $country = 'USA';
    /**
     * Variable
     *
     * @var string
     */
    public $notes = '';
    /**
     * Variable
     *
     * @var string
     */
    public $published = '';
    /**
     * Variable
     *
     * @var int
     */
    public $checked_out = 0;

    /**
     * Variable
     *
     * @var string
     */
    public $checked_out_time = '';
    /**
     * Variable
     *
     * @var int
     */
    public $created_by = null;
    /**
     * Variable
     *
     * @var string
     */
    public $created = '';
    /**
     * Variable
     *
     * @var int
     */
    public $bill_pto = null;

    
    /**
     * Constructor
     *
     * @param object &$db Database connector object
     */
    public function __construct(&$db)
    {
        parent::__construct('#__timeclock_customers', "id", $db);
    }
    
}
