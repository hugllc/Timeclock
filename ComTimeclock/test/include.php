<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_timeclock is a Joomla! 1.6 component
 * Copyright (C) 2008-2009, 2011 Hunt Utilities Group, LLC
 * Copyright 2009 Scott Price
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
 * @category   Test
 * @package    ComTimeclock
 * @subpackage Com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009, 2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:Comtimeclock
 */

$_SESSION["JoomlaMockBaseDir"] = realpath(dirname(__FILE__)."/..");

require_once(dirname(__FILE__)."/JoomlaMock/joomla.php");
require_once(dirname(__FILE__)."/JoomlaMock/mocks/JTable.php");


if (!defined('JPATH_COMPONENT_SITE')) {
    define('JPATH_COMPONENT_SITE', realpath(dirname(__FILE__)."/../site"));
}
if (!defined('JPATH_COMPONENT')) {
    define('JPATH_COMPONENT', realpath(dirname(__FILE__)."/../site"));
}
if (!defined('JPATH_COMPONENT_ADMINISTRATOR')) {
    define('JPATH_COMPONENT_ADMINISTRATOR', realpath(dirname(__FILE__)."/../admin"));
}
?>