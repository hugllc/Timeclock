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
jimport("joomla.html.pane");

$wCompCodes = TimeclockHelper::getWCompCodes();
?>
<form action="index.php" method="post" id="adminForm" name="adminForm">
<div>
    <table class="admintable">
        <tr>
            <?php if (empty($this->row->created_by)): ?>
            <td width="100" align="right" class="key">
                <label for="Published">
                    <?php echo JText::_("COM_TIMECLOCK_USER"); ?>:
                </label>
            </td>
            <td>
                    <?php print JHTML::_("select.genericList", $this->lists["users"], "created_by", 'onChange="document.getElementById(\'task\').value=\'timesheets.edit\';this.form.submit();"', 'value', 'text', $this->row->created_by); ?>
                    <input type="hidden" name="authOnly" value="1" />
            </td>
            <td>
                <?php print JText::_("COM_TIMECLOCK_SELECT_USER"); ?>
            </td>
        </tr>
        <?php else: ?>
        <tr>
            <td width="100" align="right" class="key">
                <label for="Published">
                    <?php echo JText::_("COM_TIMECLOCK_USER"); ?>:
                </label>
            </td>
            <td>
                <?php print $this->author; ?>
                <input type="hidden" name="created_by" value="<?php print $this->row->created_by; ?>" />
            </td>
            <td>
                <?php print JText::_("COM_TIMECLOCK_USER_HELP"); ?>
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="Published">
                    <?php echo JText::_("COM_TIMECLOCK_PROJECT"); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.genericList", $this->lists["projects"], "project_id", "", 'value', 'text', $this->row->project_id); ?>
            </td>
            <td>
                <?php print JText::_("COM_TIMECLOCK_PROJECT_TIMESHEET_DESC"); ?>
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="payPeriodLength">
                    <?php echo JText::_("COM_TIMECLOCK_WORK_DATE"); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("calendar", $this->row->worked, "worked", "worked", "%Y-%m-%d", array());?>
            </td>
            <td>
                <?php print JText::_("COM_TIMECLOCK_WORK_DATE_TIMESHEET_DESC"); ?>
            </td>
        </tr>
        <?php for ($i = 1; $i < 7; $i++): ?>
            <?php $var = "hours".$i; ?>
            <?php $wcVar = "wcCode".$i; ?>
            <?php if (($this->project->$wcVar == 0) && ($i > 1)) {continue;} ?>
            <?php $wcName = empty($wCompCodes[abs($this->project->$wcVar)]) ? JText::_("COM_TIMECLOCK_HOURS") : $wCompCodes[abs($this->project->$wcVar)] ; ?>
            <?php $wcNote =($this->project->$wcVar < 0) ? JText::_("COM_TIMECLOCK_CODE_DISABLED") : "" ; ?>
            <?php $hours = ($this->row->$var) ? $this->row->$var : 0; ?>
        <tr>
            <td width="100" align="right" class="key">
                <label for="<?php print $var; ?>">
                    <?php echo JText::_($wcName); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="<?php print $var; ?>" id="<?php print $var; ?>" size="10" maxlength="10" value="<?php print $hours; ?>" />
                <span><?php print $wcNote; ?></span>
            </td>
            <td>
                <?php print JText::_("COM_TIMECLOCK_HOURS_TIMESHEET_DESC"); ?>
            </td>
        </tr>
        <?php endfor; ?>
        <tr>
            <td width="100" align="right" class="key" style="vertical-align: top;">
                <label for="notes">
                    <?php echo JText::_("COM_TIMECLOCK_NOTES"); ?>:
                </label>
            </td>
            <td>
                <textarea class="text_area" type="text" name="notes" id="notes" cols="30" rows="5"><?php echo $this->row->notes;?></textarea>
            </td>
            <td>
                <?php print JText::_("COM_TIMECLOCK_NOTES_TIMESHEET_DESC"); ?>
            </td>
        </tr>
        <?php endif; ?>
    </table>
</div>

<div class="clr"></div>

<input type="hidden" name="option" value="com_timeclock" />
<input type="hidden" name="id" value="<?php print $this->row->id; ?>" />
<input type="hidden" name="created" value="<?php print $this->row->created; ?>" />
<input type="hidden" id="task" name="task" value="" />
<input type="hidden" name="controller" value="timesheets" />
<?php print JHTML::_("form.token"); ?>
</form>
