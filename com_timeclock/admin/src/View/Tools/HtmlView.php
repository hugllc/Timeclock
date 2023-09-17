<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.6 component
 * Copyright (C) 2023 Hunt Utilities Group, LLC
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
 * @copyright  2023 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: $Id: d11869e256275b9b94fc0e0207b9f8626af242e2 $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
namespace HUGLLC\Component\Timeclock\Administrator\View\Tools;

use HUGLLC\Component\Timeclock\Administrator\Helper\TimeclockHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Router\Route;

/** Check to make sure we are under Joomla */
\defined('_JEXEC') or die();


/**
 * HTML View class for the ComTimeclockWorld Component
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2023 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class HtmlView extends BaseHtmlView
{
    /**
    * Renders this view
    *
    * @return unknown
    */
    function display($tpl = null)
    {
        HTMLHelper::stylesheet(
            Uri::base().'components/com_timeclock/css/timeclock.css', 
            array()
        );
        $this->addTemplatePath(__DIR__ . '/tmpl', 'normal');

        $action = Factory::getApplication()->getInput()->get("action");
        if ($action == "dbcheck") {
            $this->addToolToolbar();
            $this->results = $this->getModel()->dbCheck();
            $this->setLayout("dbcheck");
        } else {
            $this->addToolbar();
        }

        //display
        return parent::display($tpl);
    }
    /**
    * Adds the toolbar for this view.
    *
    * @return unknown
    */
    protected function addToolbar()
    {
        // Get the toolbar object instance
        $toolbar = ToolBar::getInstance();
        ToolbarHelper::title(
            Text::_("COM_TIMECLOCK_TIMECLOCK_TOOLS"), "clock"
        );
        $toolbar->link(
            Text::_("COM_TIMECLOCK_TIMECLOCK_TOOLS_CHECK_DB"),
            Route::_("index.php?option=com_timeclock&view=tools&action=dbcheck")
        );
        // $toolbar->customButton('checkdb', Text::_("COM_TIMECLOCK_TIMECLOCK_TOOLS_CHECK_DB"), 'tools.dbcheck');
    }
    /**
    * Adds the toolbar for this view.
    *
    * @return unknown
    */
    protected function addToolToolbar()
    {
        // Get the toolbar object instance
        $toolbar = ToolBar::getInstance();
        ToolbarHelper::title(
            Text::_("COM_TIMECLOCK_CHECK_DATABASE"), "clock"
        );
        $toolbar->link(
            Text::_("JTOOLBAR_BACK"),
            Route::_("index.php?option=com_timeclock&view=tools")
        );
        // $toolbar->customButton('checkdb', Text::_("COM_TIMECLOCK_TIMECLOCK_TOOLS_CHECK_DB"), 'tools.dbcheck');
    }
}
?>