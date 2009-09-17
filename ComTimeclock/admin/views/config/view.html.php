<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.5 component
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
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
/** Check to make sure we are under Joomla */
defined('_JEXEC') or die('Restricted access');

/** Import the views */
jimport('joomla.application.component.view');

/**
 * HTML View class for the ComTimeclockWorld Component
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockAdminViewConfig extends JView
{
    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return none
     */
    function display($tpl = null)
    {
        $row = $this->get("Data");
        // Merge in the defaults in case they have changed.
        $defaults = $row->getDefaults("system");
        $row->prefs = array_merge($defaults, $row->prefs);

        JToolBarHelper::save();

        $payPeriodTypeOptions = array(
            JHTML::_("select.option", "FIXED", "Fixed"),
        );
        $this->assignRef("payPeriodTypeOptions", $payPeriodTypeOptions);
        $timesheetViewOptions = array(
            JHTML::_("select.option", "payperiod", "Pay Period"),
            JHTML::_("select.option", "1week", "1 Week"),
        );
        $this->assignRef("timesheetViewOptions", $timesheetViewOptions);

        $firstWeekDayOptions = array(
            JHTML::_("select.option", "0", "Sunday"),
            JHTML::_("select.option", "1", "Monday"),
            JHTML::_("select.option", "2", "Tuesday"),
            JHTML::_("select.option", "3", "Wednesday"),
            JHTML::_("select.option", "4", "Thursday"),
            JHTML::_("select.option", "5", "Friday"),
            JHTML::_("select.option", "6", "Satday"),
        );
        $this->assignRef("firstWeekDayOptions", $firstWeekDayOptions);

        $ptoAccrualTime = array(
            JHTML::_("select.option", "end", "End of the Period"),
            JHTML::_("select.option", "begin", "Beginning of the Period"),
        );
        $this->assignRef("ptoAccrualTimeOptions", $ptoAccrualTime);

        $ptoAccrualPeriod = array(
            JHTML::_("select.option", "week", "Week"),
            JHTML::_("select.option", "month", "Month"),
            JHTML::_("select.option", "year", "Year"),
        );
        $this->assignRef("ptoAccrualPeriodOptions", $ptoAccrualPeriod);

        $this->assignRef("prefs", $row->prefs);
        parent::display($tpl);
    }
}

?>
