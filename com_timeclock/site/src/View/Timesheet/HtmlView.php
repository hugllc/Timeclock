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
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use HUGLLC\Component\Timeclock\Administrator\Helper\TimeclockHelper;
use Joomla\CMS\Language\Text;

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
        $app = Factory::getApplication();
        $layout = $this->getLayout();

        $this->params    = ComponentHelper::getParams('com_timeclock');
        $this->payperiod = $this->getModel()->getPayperiod();
        $this->payperiod->view = "timesheet";
        $this->actions   = TimeclockHelper::getActions();
        $this->user      = $this->getModel()->getUser();
        $this->data      = $this->getModel()->listItems();
        $this->projects  = $this->getModel()->listProjects();
        $this->counts    = $this->getModel()->counts();

        if (!$this->user->me) {
            $this->payperiod->user_id = $this->user->id;
        }

        if (!$this->user->me && !$this->actions->get('timeclock.timesheet.others')) {
            throw new \Exception(Text::_('JGLOBAL_AUTH_ACCESS_DENIED'));
        }

        HTMLHelper::stylesheet(
            Uri::base().'components/com_timeclock/css/timeclock.css', 
            array()
        );

        if (($layout == "addhours") || ($layout == "modal")) {
            $this->addhours($tpl);
        }
        return parent::display();
    }
    /**
    * Renders this view
    *
    * @return unknown
    */
    function addhours($tpl)
    {
        if (!$this->user->me) {
            throw new \Exception(Text::_('JGLOBAL_AUTH_ACCESS_DENIED'));
        }

        $this->date      = $this->getModel()->getState('date');
        
    }
}
?>