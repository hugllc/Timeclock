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

jimport('joomla.application.component.view');

/**
 * HTML View class for the ComTimeclockWorld Component
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

class TimeclockViewTimeclock extends JView
{
    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return null
     */
    function display($tpl = null)
    {
        $model   =& $this->getModel();
        $projModel =& JModel::getInstance("Projects", "TimeclockAdminModel");

        $hours   = $model->getData();
        $period  = $model->getPeriod();
        $user    = JFactory::getUser();
        $user_id = $user->get("id");
        $projects = $projModel->getUserProjects($user_id);
        $employmentDates = $model->getEmploymentDatesUnix();
        $date    = $model->getDate();
        
        $this->assignRef("employmentDates", $employmentDates);        
        $this->assignRef("projects", $projects);
        $this->assignRef("user", $user);        
        $this->assignRef("hours", $hours);        
        $this->assignRef("date", $date);        
        $this->assignRef("period", $period);        
        parent::display($tpl);

    }
    
    /**
     * Checks employment dates and says if the user can enter hours on that date or not
     *
     * @param int $date The unix date to check
     *
     * @return bool
     */
    function checkDate($date)
    {
        return TimeclockController::checkEmploymentDates($this->employmentDates["start"], $this->employmentDates["end"], $date);
    }
}

?>