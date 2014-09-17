<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.6 component
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
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id: d11869e256275b9b94fc0e0207b9f8626af242e2 $
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockViewsAboutHtml extends JViewHtml
{
    /**
    * Renders this view
    *
    * @return unknown
    */
    function render()
    {
        JHTML::stylesheet(
            JURI::base().'components/com_timeclock/css/timeclock.css', 
            array(), 
            true
        );
        $app = JFactory::getApplication();
        $this->addToolbar();
        TimeclockHelpersView::addSubmenu("about");
        
        $this->sidebar = JHtmlSidebar::render();

        //display
        return parent::render();
    }
    /**
    * Adds the toolbar for this view.
    *
    * @return unknown
    */
    protected function addToolbar()
    {
        $actions = TimeclockHelpersTimeclock::getActions();
        // Get the toolbar object instance
        $bar = JToolBar::getInstance('toolbar');
        JToolbarHelper::title(
            JText::_("COM_TIMECLOCK_ABOUT_TIMECLOCK"), "clock"
        );
        if ($actions->get('core.admin'))
        {
            JToolbarHelper::preferences('com_timeclock');
        }
    }
}
?>