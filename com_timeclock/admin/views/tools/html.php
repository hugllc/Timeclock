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
 * @version    SVN: $Id: b350bcb39c520bf8e7934bf65c4652aed32f2021 $
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
class TimeclockViewsToolsHtml extends JViewHtml
{
    /**
    * Renders this view
    *
    * @return unknown
    */
    function render()
    {
        $app = JFactory::getApplication();
        $layout = $this->getLayout();

        JHTML::stylesheet(
            JURI::base().'components/com_timeclock/css/timeclock.css', 
            array(), 
            true
        );
        $app = JFactory::getApplication();

        $fct = "view".ucfirst($layout);
        if (method_exists($this, $fct)) {
            return $this->$fct();
        }
        $this->addToolbar("COM_TIMECLOCK_TIMECLOCK_TOOLS");
        TimeclockHelpersView::addSubmenu("tools");
        
        $this->sidebar = JHtmlSidebar::render();
        return parent::render();
    }
    /**
    * Adds the toolbar for this view.
    *
    * @param string $title The string to use as the title
    * 
    * @return unknown
    */
    protected function addToolbar($title)
    {
        $actions = TimeclockHelpersTimeclock::getActions();
        // Get the toolbar object instance
        $bar = JToolBar::getInstance('toolbar');
        JToolbarHelper::title(
            JText::_($title), "clock"
        );
        if ($actions->get('core.admin'))
        {
            JToolbarHelper::preferences('com_timeclock');
        }
    }
    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return none
     */
    public function viewCheck($tpl = null)
    {
        $this->noResults = JText::_("COM_TIMECLOCK_NO_TESTS");
        $this->pageheader = JText::_("COM_TIMECLOCK_CHECKING_DATABASE");
        $this->results = $this->model->dbCheck();
        $this->setLayout("check");
        
        $this->addToolbar("COM_TIMECLOCK_CHECK_DATABASE");
        TimeclockHelpersView::addSubmenu("tools");
        
        $this->sidebar = JHtmlSidebar::render();
        return parent::render();
    }
    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return none
     */
    public function viewSetup($tpl = null)
    {

        $this->setLayout("setup");
        
        $this->phpgraph = file_exists(JPATH_ROOT."/components/com_timeclock/contrib/phpgraph/phpgraphlib.php");
        $this->phpexcel = file_exists(JPATH_ROOT."/components/com_timeclock/contrib/phpexcel/PHPExcel.php");

        $this->addToolbar("COM_TIMECLOCK_CHECK_DATABASE");
        TimeclockHelpersView::addSubmenu("tools");
        
        $this->sidebar = JHtmlSidebar::render();
        return parent::render();
    }
}
?>