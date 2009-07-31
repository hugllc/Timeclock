<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_Preferences is a Joomla! 1.5 component
 * Copyright (C) 2008-2009 Hunt Utilities Group, LLC
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
 * @copyright  2008-2009 Hunt Utilities Group, LLC
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
 * @copyright  2008-2009 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock:JoomlaUI
 */
class TableTimeclockTimesheet extends JTable
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
     * @var int
     */
    public $project_id = 0;
    /**
     * Variable
     *
     * @var int
     */
    public $created_by = 0;
    /**
     * Variable
     *
     * @var float
     */
    public $hours1 = 0;
    /**
     * Variable
     *
     * @var float
     */
    public $hours2 = 0;
    /**
     * Variable
     *
     * @var float
     */
    public $hours3 = 0;
    /**
     * Variable
     *
     * @var float
     */
    public $hours4 = 0;
    /**
     * Variable
     *
     * @var float
     */
    public $hours5 = 0;
    /**
     * Variable
     *
     * @var float
     */
    public $hours6 = 0;
    /**
     * Variable
     *
     * @var string
     */
    public $worked = '';
    /**
     * Variable
     *
     * @var string
     */
    public $created= '';
    /**
     * Variable
     *
     * @var string
     */
    public $notes = '';

    /**
     * Constructor
     *
     * @param object &$db Database connector object
     */
    function __construct(&$db)
    {
        parent::__construct('#__timeclock_timesheet', "id", $db);
    }

}
