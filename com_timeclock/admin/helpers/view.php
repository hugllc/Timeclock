<?php
/**
 * This component is for tracking tim
 *
 * PHP Version 5
 *
 * <pre>
 * com_timeclock is a Joomla! 3.1 component
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
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: $Id: e479483dc62cc088c8b1471db292e17111970fa1 $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

 
/**
 * Helpers for viewing stuff
 *
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockHelpersView
{
    /**
    * Gets the HTML for a layout
    *
    * @param object $form JForm The form to print out
    * @param object $data stdClass The data for the object we are doing
    * 
    * @return string The HTML rendering of that view
    */
    static function getForm($form, $data)
    {
        $layout = new JLayoutFile('detailsedit', dirname(__DIR__).'/layouts');

        return $layout->render(
            array("data" => $data, "form" => $form)
        );
    }
    /**
    * Gets the HTML for a layout
    *
    * @param string $name The name of the fieldset to display
    * @param object $form JForm The form to print out
    * @param object $data stdClass The data for the object we are doing
    * 
    * @return string The HTML rendering of that view
    */
    static function getFormSet($name, $form, $data)
    {
        $layout = new JLayoutFile('fieldset', dirname(__DIR__).'/layouts');

        return $layout->render(
            array("name" => $name, "data" => $data, "form" => $form)
        );
    }
    /**
    * Gets the HTML for a layout
    *
    * @param string $name The name of the fieldset to display
    * @param object $form JForm The form to print out
    * @param object $data stdClass The data for the object we are doing
    * 
    * @return string The HTML rendering of that view
    */
    static function getFormSetH($name, $form, $data)
    {
        $layout = new JLayoutFile('hfieldset', dirname(__DIR__).'/layouts');

        return $layout->render(
            array("name" => $name, "data" => $data, "form" => $form)
        );
    }
    /**
    * Gets the HTML for a layout
    *
    * @param string $name The name of the fieldset to display
    * @param object $form JForm The form to print out
    * @param object $data stdClass The data for the object we are doing
    * 
    * @return string The HTML rendering of that view
    */
    static function getFormSetV($name, $form, $data)
    {
        $layout = new JLayoutFile('vfieldset', dirname(__DIR__).'/layouts');

        return $layout->render(
            array("name" => $name, "data" => $data, "form" => $form)
        );
    }
    /**
    * Gets the HTML for a layout
    *
    * @param string $name The name of the fieldset to display
    * @param object $form JForm The form to print out
    * @param object $data stdClass The data for the object we are doing
    * 
    * @return string The HTML rendering of that view
    */
    static function getFormField($data)
    {
        $layout = new JLayoutFile('field', dirname(__DIR__).'/layouts');

        return $layout->render($data);
    }
    /**
    * Configure the links below the header
    *
    * @param string $cName The name of the active controller.
    *
    * @return null
    */
    public static function addSubmenu($cName)
    {
        JHtmlSidebar::addEntry(
            JText::_("COM_TIMECLOCK_CUSTOMERS"),
            'index.php?option=com_timeclock&controller=customer',
            $cName == 'customer'
        );
        JHtmlSidebar::addEntry(
            JText::_("COM_TIMECLOCK_DEPARTMENTS"),
            'index.php?option=com_timeclock&controller=department',
            $cName == 'department'
        );
        JHtmlSidebar::addEntry(
            JText::_("COM_TIMECLOCK_PROJECTS"),
            'index.php?option=com_timeclock&controller=project',
            $cName == 'project'
        );
        JHtmlSidebar::addEntry(
            JText::_("COM_TIMECLOCK_HOLIDAYS"),
            'index.php?option=com_timeclock&controller=holiday',
            $cName == 'holiday'
        );
        JHtmlSidebar::addEntry(
            JText::_("COM_TIMECLOCK_TIMESHEETS"),
            'index.php?option=com_timeclock&controller=timesheet',
            $cName == 'timesheet'
        );
        JHtmlSidebar::addEntry(
            JText::_("COM_TIMECLOCK_MISC_TOOLS"),
            'index.php?option=com_timeclock&controller=tools',
            $cName == 'tools'
        );
        JHtmlSidebar::addEntry(
            JText::_("COM_TIMECLOCK_ABOUT"),
            'index.php?option=com_timeclock&controller=about',
            $cName == 'about'
        );
    }
}
