<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.5 component
 * Copyright (C) 2008 Hunt Utilities Group, LLC
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
 * @copyright  2008 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/** Include the project stuff */
require_once "timeclock.php";

/**
 * ComTimeclock model
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockModelReports extends TimeclockModelTimeclock
{
    /**
     * Constructor that retrieves the ID from the request
     *
     * @return    void
     */
    function __construct()
    {
        parent::__construct();

        $y = (int)date("Y");

        $startDate = JRequest::getVar('startDate', "$y-1-1", '', 'string');
        $this->setStartDate($startDate);
        $endDate = JRequest::getVar('endDate', "$y-12-31", '', 'string');
        $this->setEndDate($endDate);
        
    }

    /**
     * Where statement for the reporting period dates
     *
     * @param string $date Date to use in MySQL format ("Y-m-d")
     *
     * @return array
     */ 
    function getStartDate($date=null)
    {
        return parent::getDate($date, "_startDate");
    }

    /**
     * Where statement for the reporting period dates
     *
     * @param string $date Date to use in MySQL format ("Y-m-d")
     *
     * @return array
     */ 
    function getEndDate($date=null)
    {
        return parent::getDate($date, "_endDate");
    }

    /**
     * Where statement for the reporting period dates
     *
     * @param string $date Date to use in MySQL format ("Y-m-d")
     *
     * @return null
     */ 
    function setStartDate($date)
    {
        parent::setDate($date, "_startDate");
    }

    /**
     * Where statement for the reporting period dates
     *
     * @param string $date Date to use in MySQL format ("Y-m-d")
     *
     * @return null
     */ 
    function setEndDate($date)
    {
        parent::setDate($date, "_endDate");
    }

}

?>
