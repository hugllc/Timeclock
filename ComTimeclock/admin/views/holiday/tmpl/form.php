<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.5 component
 * Copyright (C) 2008 Hunt Utilities Group, LLC
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
 * @copyright  2008 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die('Restricted access'); 
jimport("joomla.html.pane");

$title = ($this->add) ? "Add" : "Edit";

TimeclockAdminController::title(JText::_("Timeclock Holidays: <small><small>[ ".$title." ]</small></small>"));
JToolBarHelper::apply();
JToolBarHelper::save();
JToolBarHelper::cancel();

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div>
    <table class="admintable">
        <tr>
            <td width="100" align="right" class="key">
                <label for="Published">
                    <?php echo JText::_('Project'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.genericList", $this->lists["projects"], "project_id", "", 'value', 'text', $this->row->project_id); ?>
            </td>
            <td>
                The project
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="payPeriodLength">
                    <?php echo JText::_('Work Date'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("calendar", $this->row->worked, "worked", "worked", "%Y-%m-%d", "");?>
            </td>
            <td>
                When the first pay period starts
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="hours">
                    <?php echo JText::_('Hours'); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="hours" id="hours" size="10" maxlength="10" value="<?php echo $this->row->hours;?>" />
            </td>
            <td>
                The number of hours of holiday for this day
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key" style="vertical-align: top;">
                <label for="notes">
                    <?php echo JText::_('Notes'); ?>:
                </label>
            </td>
            <td>
                <textarea class="text_area" type="text" name="notes" id="notes" cols="30" rows="5"><?php echo $this->row->notes;?></textarea>
            </td>
            <td>
                A description of the holiday
            </td>
        </tr>
    </table>
</div>

<div class="clr"></div>

<input type="hidden" name="option" value="com_timeclock" />
<input type="hidden" name="id" value="<?php print $this->row->id; ?>" />
<input type="hidden" name="created" value="<?php print $this->row->created; ?>" />
<input type="hidden" name="created_by" value="<?php print $this->row->created_by; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="holidays" />
</form>
