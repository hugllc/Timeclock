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
 * @version    SVN: $Id: c3ba8006178caa5bdf6f33e494e1b6d4220036f4 $
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

class JFormFieldTimeclockUserProjects extends JFormField
{
    protected $type = 'TimeclockUserProjects';

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
        $model = TimeclockHelpersTimeclock::getModel("project");
        $projects = $model->listItems(array("p.published=1", "p.type <> 'CATEGORY'"));
        foreach ($projects as $row) {
            $displayData["outOptions"][$row->project_id] = JHTML::_(
                'select.option', 
                (int)$row->project_id, 
                JText::_($row->name)
            );
        }
        $id = JFactory::getApplication()->input->get("id", null, "int");
        $uprojects = $model->listUserProjects($id);
        foreach ($uprojects as $row) {
            if (isset($displayData["outOptions"][$row->project_id])) {
                $displayData["inOptions"][$row->project_id] = $displayData["outOptions"][$row->project_id];
                unset($displayData["outOptions"][$row->project_id]);
            }
        }
        $displayData["label_in"]  = "COM_TIMECLOCK_IN";
        $displayData["label_out"] = "COM_TIMECLOCK_OUT";
        $layout = new JLayoutFile('dualselect', JPATH_ROOT.'/administrator/components/com_timeclock/layouts');
        return $layout->render($displayData);
    }
}
