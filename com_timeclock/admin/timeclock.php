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
 * @version    GIT: $Id: cf4c4c441ff422f81fc1068eaf088254353faea5 $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Table\Table;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Factory;

// load table paths
JLoader::register('TimeclockHelpersDate', JPATH_COMPONENT_SITE.'/helpers/date.php');
Table::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_timeclock/tables');
Form::addFieldPath(JPATH_ADMINISTRATOR.'/components/com_timeclock/models/fields');
//load classes
JLoader::registerPrefix('Timeclock', JPATH_COMPONENT_ADMINISTRATOR);
//Load plugins
PluginHelper::importPlugin('timeclock');
//application
$app = Factory::getApplication();
// Require specific controller if requested

$classname = "TimeclockControllers".ucfirst($app->input->get('controller', "About"));

if (!class_exists($classname)) {
    $classname = "TimeclockControllersAbout";
}
$controller = new $classname();
// Perform the Request task
$controller->execute();
