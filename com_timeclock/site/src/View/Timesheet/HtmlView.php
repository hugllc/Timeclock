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
 * @version    GIT: $Id: e1fc5c887a1edad708ebadc65fbd04a50869766b $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
namespace HUGLLC\Component\Timeclock\Site\View\Timesheet;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

/** Check to make sure we are under Joomla */
defined('_JEXEC') or die();

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
    function display($tpl = NULL)
    {
        $this->addTemplatePath(__DIR__ . '/tmpl', 'normal');
        $app = Factory::getApplication();
        $layout = $this->getLayout();

        $this->params    = ComponentHelper::getParams('com_timeclock');
        $this->payperiod = $this->getModel()->getState('payperiod');
        $this->payperiod->view = "timesheet";

        HTMLHelper::stylesheet(
            Uri::base().'components/com_timeclock/css/timeclock.css', 
            array()
        );

        if (($layout == "addhours") || ($layout == "modal")) {
            return $this->renderAddhours($tpl);
        }
        return $this->renderTimesheet($tpl);
    }
    /**
    * Renders this view
    *
    * @return unknown
    */
    function renderTimesheet($tpl)
    {

        $this->_header     = new FileLayout('header', __DIR__.'/layouts');
        $this->_row        = new FileLayout('row', __DIR__.'/layouts');
        $this->_totals     = new FileLayout('totals', __DIR__.'/layouts');
        $this->_nextprev   = new FileLayout('payperiodnextprev', dirname(__DIR__).'/layouts');
        $this->_category   = new FileLayout('category', __DIR__.'/layouts');
        $this->_subtotals  = new FileLayout('subtotals', __DIR__.'/layouts');
        $this->_psubtotals = new FileLayout('psubtotals', __DIR__.'/layouts');
        $this->_toolbar    = new FileLayout('toolbar', __DIR__.'/layouts');
        $this->_uname      = new FileLayout('name', __DIR__.'/layouts');
        $this->_notes      = new FileLayout('notes', dirname(__DIR__).'/layouts');

        $this->data      = $this->getModel()->listItems();
        $this->projects  = $this->getModel()->listProjects();
        $this->user      = $this->getModel()->getUser();

        return parent::display($tpl);
    }
    /**
    * Renders this view
    *
    * @return unknown
    */
    function renderAddhours($tpl)
    {
        $this->_entry    = new FileLayout('entry', __DIR__.'/layouts');

        $this->data      = $this->getModel()->listItems();
        $this->projects  = $this->getModel()->listProjects();
        $this->date      = $this->getModel()->getState('date');
        
        return parent::display();
    }
}
?>