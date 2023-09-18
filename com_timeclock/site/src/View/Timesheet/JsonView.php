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
 * @version    GIT: $Id: af297190be388d3d384a4f69943e39bfe07e5d9f $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
namespace HUGLLC\Component\Timeclock\Site\View\Timesheet;

use Joomla\CMS\MVC\View\JsonView as BaseJsonView;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Factory;

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
class JsonView extends BaseJsonView
{
    /**
    * Renders this view
    *
    * @return unknown
    */
    function display($tpl = NULL)
    {
        $app = Factory::getApplication();

        $payperiod = $this->getModel()->getState('payperiod');
        $data      = $this->getModel()->listItems();
        $projects  = $this->getModel()->listProjects();
        
        echo new JsonResponse(
            $data,
            "",     // Message
            false,  // Error
            false    // Ignore Message Queue
        );
    }
}
?>