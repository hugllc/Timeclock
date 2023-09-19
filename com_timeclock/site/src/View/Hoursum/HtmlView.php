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
namespace HUGLLC\Component\Timeclock\Site\View\Hoursum;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use HUGLLC\Component\Timeclock\Site\Helper\ContribHelper;

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
    private $_projType = array(
        "PROJECT"  => "COM_TIMECLOCK_PROJECT",
        "CATEGORY" => "JCATEGORY",
        "PTO"      => "COM_TIMECLOCK_PTO",
        "HOLIDAY"  => "COM_TIMECLOCK_HOLIDAY",
        "UNPAID"   => "COM_TIMECLOCK_VOLUNTEER",
    );
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
        $this->start     = $this->getModel()->getState('start');
        $this->end       = $this->getModel()->getState('end');
        
        HTMLHelper::stylesheet(
            Uri::base().'components/com_timeclock/css/timeclock.css', 
            array()
        );

        $this->_dataset = new FileLayout('dataset', __DIR__.'/layouts');
        $this->_export  = new FileLayout('export', dirname(__DIR__).'/layouts');
        $this->_control = new FileLayout('reportcontrol', dirname(__DIR__).'/layouts');

        $this->data              = $this->getModel()->listItems();
        $this->users             = $this->getModel()->listUsers();
        $this->projects          = $this->getModel()->listProjects();
        $this->customers         = $this->getModel()->listCustomers();
        $this->departments       = $this->getModel()->listDepartments();
        $this->filter            = $this->getModel()->getFilter();
        $this->filter->start     = $this->start;
        $this->filter->end       = $this->end;
        $this->export   = array(
            "CSV" => "csv",
            "Excel" => "xlsx",
        );

        return parent::display($tpl);
    }
    /**
    * This creates a pie graph for us
    *
    * @param string $title The header for the graph
    * @param array  $data  The data to use for the graph
    *
    * @return binary string that is the image
    */
    protected function pie($title, $data)
    {
        $total = 0;
        foreach ($data as $val) {
            $total += $val;
        }
        $png = "";
        if (ContribHelper::phpgraph() && !empty($total)) {
            $graph = new PHPGraphLibPie(400, 200);
            $graph->addData($data);
            $graph->setTitle($title);
            $graph->setLabelTextColor('black');
            $graph->setLegendTextColor('black');

            ob_start();
            $graph->createGraph();
            $png = ob_get_contents();
            ob_end_clean();
        }
        return $png;
    }
    /**
    * This creates a pie graph for us
    *
    * @param string $type The type of project
    *
    * @return binary string that is the image
    */
    protected function getProjType($type)
    {
        if (isset($this->_projType[$type])) {
            return Text::_($this->_projType[$type]);
        }
        return Text::_("COM_TIMECLOCK_UNKNOWN");
    }
}
?>