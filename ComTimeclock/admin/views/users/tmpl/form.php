<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.5 component
 * Copyright (C) 2008-2009 Hunt Utilities Group, LLC
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
 * @copyright  2008-2009 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die('Restricted access');
jimport("joomla.html.pane");

TimeclockAdminController::title(JText::_("User Configuration: <small><small>[ ".$this->user->name." ]</small></small>"));
JToolBarHelper::apply();
JToolBarHelper::save();
JToolBarHelper::cancel();

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div style="float: right; width: 30%;">
<?php
$pane = JPane::getInstance("sliders");
echo $pane->startPane("project-pane");
echo $pane->startPanel(JText::_("Add Projects"), "addproject-page");
?>
<div style="padding: 5px;">
<?php
array_shift($this->lists["projects"]);
if (count($this->lists["projects"])) {
    print JHTML::_("select.genericList", $this->lists["projects"], "projid[]", 'multiple="multiple"', 'value', 'text', 0);
    ?><br />
        <button onClick="this.form.task.value='addproject';this.form.submit();">Add Projects</button>
    <?php
} else {
    print JText::_("No Projects to add");
}
?>
</div>
<?php
echo $pane->endPanel();
echo $pane->startPanel(JText::_("Add Projects from User"), "adduserproject-page");
?>
<div style="padding: 5px;">
    <?php print JHTML::_("select.genericList", $this->lists["users"], "user_id", 'onChange="this.form.task.value=\'adduserproject\';this.form.submit();"', 'value', 'text', 0); ?>
</div>
<?php
echo $pane->endPanel();
echo $pane->startPanel(JText::_("Remove Projects"), "project-page");
?>
<div style="padding: 5px;">
<?php
$options = array();
foreach ($this->lists["userProjects"] as $proj) {
     $options[] = JHTML::_("select.option", $proj->id, $proj->name);
}
print JHTML::_("select.genericList", $options, "remove_projid[]", 'multiple="multiple"', 'value', 'text', 0);
    ?><br />
        <button onClick="this.form.task.value='removeproject';this.form.submit();">Remove Projects</button>
</div>
<?php
echo $pane->endPanel();
echo $pane->endPane();
?>
</div>
<div>
    <table class="admintable">
        <tr>
            <td width="100" align="right" class="key">
                <label for="startDate">
                    <?php echo JText::_('Start Date'); ?>:
                </label>
            </td>
            <td style="white-space:nowrap;">
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
            <td style="white-space:nowrap;">
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
                <label for="manager">
                    <?php echo JText::_('Manager'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.genericList", $this->lists["users"], "admin_manager", '', 'value', 'text', $this->row->prefs["admin_manager"]); ?>
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
<?php
if ($this->row->prefs["admin_status"] == "PARTTIME") {
    ?>
        <tr>
            <td width="100" align="right" class="key">
                <label for="holidayperc">
                    <?php echo JText::_('Holiday Pay'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.integerList", 0, 100, 10, "admin_holidayperc", "", $this->row->prefs["admin_holidayperc"]); ?>%
            </td>
            <td>
                The percentage of holiday pay this user gets
            </td>
        </tr>
    <?php
}
?>
        <tr>
            <td width="100" align="right" class="key" style="vertical-align:top;">
                <label for="projects">
                    <?php echo JText::_('Projects'); ?>:
                </label>
            </td>
            <td style="white-space: nowrap;">
<?php
foreach ($this->lists["userProjects"] as $proj) { ?>
                    <?php print sprintf("%04d", $proj->id).": ".$proj->name; ?><br />
    <?php
}
?>
            </td>
            <td>
                This is just a list of projects.
            </td>
        </tr>
        <tr>
            <td class="key" style="vertical-align:top;">
                <?php print JText::_("Change History"); ?>:<br />
                (<?php print JText::_("Old Values"); ?>)
            </td>
            <td colspan="2">
<?php
if (!is_array($this->row->history["timestamps"])) $this->row->history["timestamps"] = array();
krsort($this->row->history["timestamps"]);
foreach ($this->row->history["timestamps"] as $date => $user) { ?>
                <p ><div><strong><?php print $user." <br /> ".$date; ?>:</strong></div>
    <?php
    foreach ($this->row->history as $key => $value) {
        if (!array_key_exists($date, $value)) continue;
        if ($key == "timestamps") continue;
        print $key." = ".$value[$date]."<br />";
    }
    ?>
    </p>
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
