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
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2023 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: $Id: e18e940bbce50eadc20eca3bd29cea99da26026a $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Table\Table;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Factory;

//sessions
jimport( 'joomla.session.session' );
// require helper file
JLoader::register('TimeclockHelpersTimeclock', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/timeclock.php');
JLoader::register('TimeclockHelpersView', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/view.php');
JLoader::register('TimeclockHelpersDate', JPATH_COMPONENT_SITE.'/helpers/date.php');
//load tables
Table::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/tables');
//load classes
JLoader::registerPrefix('Timeclock', JPATH_COMPONENT);
//Load plugins
//PluginHelper::importPlugin('lendr');
//Load styles and javascripts
//LendrHelpersStyle::load();
//application
$app = Factory::getApplication();
// Require specific controller if requested
$controller = $app->input->get('controller', null);
if (is_null($controller)) {
    // See if the view is specified
    $controller = $app->input->get('view', null);
    if (is_null($controller)) {
        $controller = "timesheet";
    }
}
// Create the controller
$classname = 'TimeclockControllers'.ucwords($controller);
// Set a default if the one requested doesn't work.
$classname = class_exists($classname) ? $classname : "TimeclockControllersTimesheet";

$controller = new $classname();
// Perform the Request task
$controller->execute();
