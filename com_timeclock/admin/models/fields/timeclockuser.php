<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.6 component
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: $Id: 1ce2dfd95bfe6b01c9973cf1ca27a60121c30e18 $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');
/**
 * This creates a select box with the user types in it.
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

class JFormFieldTimeclockUser extends JFormField
{
    protected $type = 'TimeclockUser';

    /**
    * Method to get the field options.
    *
    * @return      array   The field option objects.
    */
    public function getInput()
    {
        $list = TimeclockHelpersTimeclock::getUsers(0);
        foreach ($list as $item) {
            $options[] = JHTML::_(
                'select.option', 
                (int)$item->id, 
                JText::_($item->name)
            );
        }
        $attrib = array();
        if (isset($this->class)) {
            $attrib['class'] = $this->class;
        }
        if (isset($this->onchange)) {
            $attrib['onchange'] = $this->onchange;
        }
        return JHTML::_(
            'select.genericlist',
            $options,
            $this->name,
            $attrib,
            'value',
            'text',
            $this->value,
            $this->id
        );
    }
}
