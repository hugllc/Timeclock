<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * This module was copied and modified from mod_stats.  That is the reason for
 * the OSM Copyright.
 *
 * <pre>
 * mod_timeclockinfo is a Joomla! 1.5 module
 * Copyright (C) 2023 Hunt Utilities Group, LLC
 * Copyright (C) 2009 Scott Price
 * Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
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
 * @package    Comtimeclock
 * @subpackage Com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2023 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @copyright  2005-2008 Open Source Matters. All rights reserved.
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: $Id: c5e307ee2e94fb79a5e6e881912cf0354d57334c $
 * @link       https://dev.hugllc.com/index.php/Project:Comtimeclock
 */

use HUGLLC\Module\TimeclockInfo\Site\Helper\TimeclockInfoHelper;
use Joomla\CMS\Helper\ModuleHelper;

// no direct access
defined('_JEXEC') or die('Restricted access');


$params->def('showYTDHours', 1);
$params->def('showHoursPerWeek', 1);
$params->def('showNextHoliday', 1);


$stuff = TimeclockInfoHelper::getDisplay($params);

require ModuleHelper::getLayoutPath('mod_timeclockinfo');

?>
