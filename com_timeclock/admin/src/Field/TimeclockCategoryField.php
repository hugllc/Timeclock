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
 * @version    GIT: $Id: 36cb7bc19bfa03f05e21e3ddda3749dbdbf00660 $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
namespace HUGLLC\Component\Timeclock\Administrator\Field;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;
use HUGLLC\Component\Timeclock\Administrator\Model\ProjectsModel;
use Joomla\CMS\HTML\HTMLHelper;

\defined('_JEXEC') or die();

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

class TimeclockCategoryField extends FormField
{
    protected $type = 'TimeclockCategory';

    /**
    * Method to get the field options.
    *
    * @return      array   The field option objects.
    */
    public function getInput()
    {
        $model = new ProjectsModel;
        $model->getState("filter.published");  // This populates the state
        $model->setState("filter.type", "CATEGORY");
        $model->setState("filter.published", 1);
        $model->setState("list.ordering", "p.name");
        $model->setState("list.direction", "ASC");
        $list = $model->getItems();
        $options = array(
            HTMLHelper::_('select.option', 0, Text::_("JNone"))
        );
        foreach ($list as $value) {
            $options[] = HTMLHelper::_(
                'select.option', 
                $value->project_id, 
                Text::_($value->name)
            );
        }
        return HTMLHelper::_(
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
