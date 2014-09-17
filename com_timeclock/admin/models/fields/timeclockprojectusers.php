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
 * @version    SVN: $Id: 0e0a42e141361ddf6a524ed6c01e281d912246ea $
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

class JFormFieldTimeclockProjectUsers extends JFormField
{
    protected $type = 'TimeclockProjectUsers';

    /**
    * Method to get the field options.
    *
    * @return      array   The field option objects.
    */
    public function getInput()
    {
        $displayData = get_object_vars($this);
        $displayData["outOptions"] = array();
        $displayData["inOptions"] = array();
        
        $users = TimeclockHelpersTimeclock::getUsers();
        foreach ($users as $row) {
            $displayData["outOptions"][$row->id] = JHTML::_(
                'select.option', 
                (int)$row->id, 
                JText::_($row->name)
            );
        }
        $model = TimeclockHelpersTimeclock::getModel("project");
        $projects = $model->listProjectUsers();
        foreach ($projects as $row) {
            if (isset($displayData["outOptions"][$row->id])) {
                $displayData["inOptions"][$row->id] = $displayData["outOptions"][$row->id];
                unset($displayData["outOptions"][$row->id]);
            }
        }
        $displayData["label_in"]  = "COM_TIMECLOCK_IN";
        $displayData["label_out"] = "COM_TIMECLOCK_OUT";
        $layout = new JLayoutFile('dualselect', JPATH_ROOT.'/components/com_timeclock/layouts');

        return $layout->render($displayData);
    }
}
