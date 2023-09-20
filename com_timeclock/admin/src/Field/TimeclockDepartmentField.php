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
 * @version    GIT: $Id: 05067b3e53acbb94ecf90fbfcbca199e94e4cf18 $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
namespace HUGLLC\Component\Timeclock\Administrator\Field;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;
use HUGLLC\Component\Timeclock\Administrator\Model\DepartmentsModel;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die();

/**
 * This creates a select box with the user types in it.
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2023 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

class TimeclockDepartmentField extends FormField
{
    protected $type = 'TimeclockDepartment';

    /**
    * Method to get the field options.
    *
    * @return      array   The field option objects.
    */
    public function getInput()
    {
        $model = new DepartmentsModel();
        $model->getState("filter.published");  // This populates the state
        $model->setState("filter.published", 1);
        $model->setState("list.ordering", "d.name");
        $model->setState("list.direction", "ASC");
        $depts = $model->getItems();
        $options = array(
            HTMLHelper::_(
                'select.option', 
                0, 
                Text::_("JNONE")
            )
        );
        foreach ($depts as $value) {
            $options[] = HtmlHelper::_(
                'select.option', 
                $value->department_id, 
                Text::_($value->name)
            );
        }
        return HtmlHelper::_(
            'select.genericlist', 
            $options, 
            $this->name, 
            array('class' => $this->class), 
            'value', 
            'text', 
            $this->value,
            $this->id
        );
    }
}
