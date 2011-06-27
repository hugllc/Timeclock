<?php
/**
 * This component is the user interface for the endpoints
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

defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.field');
require_once JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_timeclock'.DS.'helpers'.DS.'timeclock.php';

/**
 * This creates a select box with the user types in it.
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009, 2011 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

class JFormFieldTimeclockPTOCarryOver extends JFormField
{
    protected $type = 'TimeclockPTOCarryOver';

    public function getLabel()
    {
        return "";
    }
    public function getInput()
    {
        $idName = empty($this->elements["userid"])?"id":$this->elements["userid"];
        $id = JRequest::getInt($idName);
        if (empty($id)) {
            return "";
        }
        $calAttrib = array(
            'style' => '',
            'class' => $this->element["class"],
            'title' => $this->element["title"],
        );
        $ret = array();
        $startDate = new JDate(TimeclockHelper::getUserParam("startDate", $id), "UTC");
        $end = (int)$startDate->year;
        if (empty($end)) {
            $end = (int)date("Y");
        }
        for ($year = (int)date("Y")+1; $year > $end; $year--) {
            $id = $this->id."_".$year;
            $name = $this->name.'['.$year.']';
            $value = &$this->value[$year];
            $labelclass = 'hasTip';
            $labelclass = $this->required == true ? $labelclass.' required' : $labelclass;
            $labeltext = JText::sprintf($this->element['label'], $year);
            $labeltitle = htmlspecialchars(trim($labeltext, ":")."::".JText::_($this->description), ENT_COMPAT, 'UTF-8');
            if (empty($value["expires"])) {
                $expire = TimeclockHelper::getParam("ptoCarryOverDefExpire");
                $value["expires"] = (empty($expire)) ? $year."-3-31" : $year."-".$expire;
            }

            $ret[] = '<fieldset style="border: thin solid grey; margin: 0px; padding: 3px;">';
            $ret[] = '<label class="'.$labelclass.'" title="'.$labeltitle.'" for="'.$id.'_amount">'.$labeltext.'<span class="star">&#160;*</span></label>';
            $ret[] = '<input type="text" class="'.$this->element["class"].'" name="';
            $ret[] = $name.'[amount]" size="7" maxlength="5" value="'.(int)$value["amount"].'" id="'.$id.'_amount" />';

            $labeltext = JText::sprintf($this->element['labelExpires'], $year);
            $labeltitle = htmlspecialchars(trim($labeltext, ":")."::".JText::_($this->element['descriptionExpires']), ENT_COMPAT, 'UTF-8');
            $ret[] = '<label class="'.$labelclass.'" title="'.$labeltitle.'" for="'.$id.'_expires">'.$labeltext.'<span class="star">&#160;*</span></label>';
            $ret[] = JHTML::_("calendar", $value["expires"], $name."[expires]", $id."_expires", "%Y-%m-%d", $calAttrib);
            $ret[] = "</fieldset>\n";
        }
        return implode($ret);
    }
}