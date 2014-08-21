<?php
/**
 * Returns a category drop down menu
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.6 component
 * Copyright (C) 2008-2009, 2011 Hunt Utilities Group, LLC
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
 * @copyright  2008-2009, 2011 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die();
jimport( 'joomla.html.parameter.element' );
/** get the model we need */
require_once realpath(
    dirname(__FILE__).'/../models/customers.php'
);


/**
 * Returns a category drop down menu
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009, 2011 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class JElementCustomer extends JElement
{
    /**
     * Element name
     *
     * @var string
     */
    var    $_name = 'Customer';

    /**
     * get the element
     *
     * @param string $name         The name of the element
     * @param mixed  $value        The current value
     * @param object &$node        No idea what this is for
     * @param string $control_name The name of the overall variable
     *
     * @return string
     */

    function fetchElement($name, $value, &$node, $control_name)
    {
        $model =& JModelLegacy::getInstance("Customers", "TimeclockAdminModel");
        $options = $model->getOptions("", "All");
        return JHTML::_(
            "select.genericList",
            $options,
            $control_name.'['.$name.']',
            "",
            'value',
            'text',
            $value
        );
    }
}
