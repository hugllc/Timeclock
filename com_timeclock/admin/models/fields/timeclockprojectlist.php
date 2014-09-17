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
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.field');
require_once JPATH_ROOT.'/administrator/components/com_timeclock/helpers/timeclock.php';

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

class JFormFieldTimeclockProjectList extends JFormField
{
    protected $type = 'TimeclockProjectList';

    public function getLabel()
    {
        if ($this->element["label"] == 'true') {
            return parent::getLabel();
        } else {
            return "";
        }
    }
    public function getInput()
    {
        $lang = JFactory::getLanguage();
        $lang->load("com_timeclock");
        $idName = empty($this->elements["userid"])?"id":$this->elements["userid"];
        $id = JRequest::getInt($idName);
        if (empty($id)) {
            return "";
        }
        $ret = array(
            '<h2>Check box to remove the project</h2>',
            '<div class="'.$this->class.'" style="clear:both;">'
        );
        $model = TimeclockHelpersTimeclock::getModel("project");
        $projects = array(); //$model->getUserProjectsBare($id, null, null);
        foreach ((array)$projects as $proj) {
            if (($proj->id > 0)) {
                $ret[] = '<div style="white-space: nowrap;">';
                $id = $this->id.'_'.$proj->id;
                $name = $this->name.'['.$proj->id.']';
                $labeltext = sprintf("%04d", $proj->id).": ".JText::_($proj->name);
                $labeltitle = htmlspecialchars(trim($labeltext, ":")."::".JText::_($proj->description), ENT_COMPAT, 'UTF-8');
                $ret[] = '<label class="hasTip" title="'.$labeltitle.'" for="'.$id.'">';
                if ($this->element["remove"] == 'true') {
                    $ret[] = '<input type="checkbox" name="'.$name.'" id="'.$id.'" value="'.(int)$proj->id.'" />';
                }
                $ret[] = $labeltext."</label>";
                $ret[] = "</div>";
            }
        }
        if (count($ret) === 1) {
            // No projects assigned to this user
            $ret[] = JText::_($this->element['emptyMessage']);
        }
        $ret[] = "</div>";
        return implode("\n", $ret);
    }
}
