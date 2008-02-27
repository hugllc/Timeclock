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

JToolBarHelper::title(JText::_("User Configuration: <small><small>[ ".$this->user->name." ]</small></small>"));
JToolBarHelper::apply();
JToolBarHelper::save();
JToolBarHelper::cancel();

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div>
    <table class="admintable">
        <tr>
            <td width="100" align="right" class="key">
                <label for="startDate">
                    <?php echo JText::_('Start Date'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("calendar", $this->row->startDate, "startDate", "startDate", "%Y-%m-%d", "");?>
            </td>
            <td>
                When this user starts
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="endDate">
                    <?php echo JText::_('End Date'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("calendar", $this->row->endDate, "endDate", "endDate", "%Y-%m-%d", "");?>
            </td>
            <td>
                When this user leaves.  Leave blank if the user is still employed.
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="active">
                    <?php echo JText::_('Active'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.booleanList", "published", "", $this->row->published); ?>
            </td>
            <td>
                Is this user active in the timeclock.  'No' means they will not be able to access any
                sort of timeclock.
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="reports">
                    <?php echo JText::_('Reports'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.booleanList", "admin_reports", "", $this->row->prefs["admin_reports"]); ?>
            </td>
            <td>
                Can this user view reports.
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="type">
                    <?php echo JText::_('User Type'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.genericList", $this->lists["status"], "admin_status", "", 'value', 'text', $this->row->prefs["admin_status"]); ?>
            </td>
            <td>
                The status of the user
            </td>
        </tr>
        <tr>
            <td class="key">
                <label for="Projects">
                    <?php echo JText::_('Add Project'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.genericList", $this->lists["projects"], "projid", 'onChange="this.form.task.value=\'addproject\';this.form.submit();"', 'value', 'text', 0); ?>
            </td>
        </tr>
        <tr>
            <td class="key">
                <label for="Projects">
                    <?php echo JText::_('Projects'); ?>:
                </label>
            </td>
            <td>
            <?php 
foreach ($this->lists["userProjects"] as $proj) { ?>
                    <button onClick="this.form.task.value='removeproject';this.form.projid.value='<?php print $proj->id;?>';this.form.submit();">Remove</button>
                    <?php print sprintf("%04d", $proj->id).": ".$proj->name; ?><br />
    <?php
} 
?>
            </td>
        </tr>
    </table>

</div>

<div class="clr"></div>
<input type="hidden" name="created" value="<?php print $this->row->created; ?>" />
<input type="hidden" name="created_by" value="<?php print $this->row->created_by; ?>" />
<input type="hidden" name="checked_out" value="<?php print $this->row->checked_out; ?>" />
<input type="hidden" name="checked_out_time" value="<?php print $this->row->checked_out_time; ?>" />

<input type="hidden" name="option" value="com_timeclock" />
<input type="hidden" name="id" value="<?php print $this->row->id; ?>" />
<input type="hidden" name="task" id="task" value="" />
<input type="hidden" name="controller" value="users" />
</form>
