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
 * @version    GIT: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;
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

class FormFieldTimeclockHistory extends FormField
{
    protected $type = 'TimeclockHistory';

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
        $index = 0;
        $ret[] = "<div>";
        if (!isset($this->value) || !is_array($this->value)) {
            $this->value = array("timestamps" => array());
        }
        $ts = $this->value["timestamps"];
        krsort($ts);
        $this->value["timestamps"] = $ts;
        foreach ($this->value["timestamps"] as $date => $user) {
            if (empty($date)) {
                continue;
            }
            $index++;
            $id = $this->id."_effectiveDate_$index";
            $labeltext = $user;
            $labeltitle = htmlspecialchars(trim($user, ":"), ENT_COMPAT, 'UTF-8');
            $ret[] = '<label class="hasTip" title="'.$labeltitle.'" for="'.$id.'">'.$labeltext.'</label>'.$date;
            $ret[] = '<a href="#" onClick="document.getElementById(\''.$id.'\').style.display=\'\';document.getElementById(\'effectiveDate'.$index.'Set\').value=\'1\';">['.Text::_("COM_TIMECLOCK_EDIT").']</a>';
            $ret[] = '<div id="'.$id.'" style="display: none;">';
            $ret[] = JHTML::_("calendar", $date, $this->name."[effectiveDate][$date]", $id, "%Y-%m-%d %H:%M:%S", array());
            $ret[] = '<input type="hidden" name="'.$this->name.'[effectiveDateSet]['.$date.']" id="effectiveDate'.$index.'Set" value="0" />';
            $ret[] = '</div>';
            $ret[] = '<div style="clear:both; padding-bottom: 2em;">';
            foreach ((array)$this->value as $key => $value) {
                if (!$value[$date] || ($key == "timestamps")) {
                    continue;
                }
                $p = $value[$date];
                if (is_array($p)) {
                    $p = print_r($p, true);
                }
                $ret[] = $key." = ".$p."<br />";
            }
            $ret[] = '</div>';
        }
        if (count($ret) === 1) {
            // No projects assigned to this user
            $ret[] = Text::_($this->element['emptyMessage']);
        }
        $ret[] = "</div>";
        return implode("\n", $ret);
    }
}
