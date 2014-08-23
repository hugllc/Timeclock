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
jimport("joomla.html.pane");
?>
<form action="<?php JROUTE::_("index.php"); ?>" method="post" name="userForm" id="userForm" autocomplete="off">
    <div class="componentheading"><?php print JText::_("COM_TIMECLOCK_TIMECLOCK_PREFS_FOR").$this->user->get("name");?></div>
    <table class="usertable">
        <tr>
            <td width="100" align="right" class="key">
                <label for="timesheetSort">
                    <?php echo JText::_("COM_TIMECLOCK_TIMESHEET_SORT"); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.genericList", $this->timesheetSortOptions, "prefs[user_timesheetSort]", "", 'value', 'text', $this->prefs["user_timesheetSort"]); ?>
                <?php print JHTML::_("select.genericList", $this->timesheetSortOptionsDir, "prefs[user_timesheetSortDir]", "", 'value', 'text', $this->prefs["user_timesheetSortDir"]); ?>
            </td>
            <td>
                <?php print JText::_("COM_TIMECLOCK_TIMESHEET_SORT_HELP"); ?>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <button type="submit" onMouseDown="document.getElementById('theTask').value='preferences.save';" class="button validate"><?php print JText::_("COM_TIMECLOCK_SAVE"); ?></button>
            </td>
        </tr>
    </table>

    <div class="clr"></div>

    <input type="hidden" name="option" value="com_timeclock" />
    <input type="hidden" name="id" value="-1" />
    <input type="hidden" name="task" id="theTask" value="" />
    <input type="hidden" name="controller" value="preferences" />
    <?php print JHTML::_("form.token"); ?>
</form>
