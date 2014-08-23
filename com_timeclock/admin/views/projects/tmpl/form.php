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
jimport("joomla.html.html.sliders");

?>
<form action="index.php" method="post" id="adminForm" name="adminForm">
<div>
    <table class="admintable">
        <tr>
            <th>
                <label for="name">
                    <?php echo JText::_("COM_TIMECLOCK_NAME"); ?>:
                </label>
            </th>
            <td>
                <input class="text_area" type="text" name="name" id="name" size="32" maxlength="64" value="<?php echo $this->row->name;?>" />
            </td>
            <td>
                <?php print JText::_("COM_TIMECLOCK_NAME_PROJECT_DESC"); ?>
            </td>
        </tr>
        <tr>
            <th style="vertical-align: top;">
                <label for="description">
                    <?php echo JText::_("COM_TIMECLOCK_DESCRIPTION"); ?>:
                </label>
            </th>
            <td>
                <textarea class="text_area" type="text" name="description" id="description" cols="30" rows="5"><?php echo $this->row->description;?></textarea>
            </td>
            <td>
                <?php print JText::_("COM_TIMECLOCK_DESCRIPTION_PROJECT_DESC"); ?>
            </td>
        </tr>
        <tr>
            <th>
                <label for="Research">
                    <?php echo JText::_("COM_TIMECLOCK_MANAGER"); ?>:
                </label>
            </th>
            <td>
                <?php print JHTML::_("select.genericList", $this->lists["allUsers"], "manager", '', 'value', 'text', $this->row->manager);  ?>
            </td>
            <td>
                <?php print JText::_("COM_TIMECLOCK_MANAGER_PROJECT_DESC"); ?>
            </td>
        </tr>
        <tr>
            <th>
                <label for="Research">
                    <?php echo JText::_("COM_TIMECLOCK_RESEARCH"); ?>:
                </label>
            </th>
            <td>
                <?php print JHTML::_("select.booleanList", "research", "", $this->row->research); ?>
            </td>
            <td>
                <?php print JText::_("COM_TIMECLOCK_RESEARCH_PROJECT_DESC"); ?>
            </td>
        </tr>
        <tr>
            <th>
                <label for="published">
                    <?php echo JText::_("COM_TIMECLOCK_ACTIVE"); ?>:
                </label>
            </th>
            <td>
                <?php print JHTML::_("select.booleanList", "published", "", $this->row->published); ?>
            </td>
            <td>
                <?php print JText::_("COM_TIMECLOCK_ACTIVE_PROJECT_DESC"); ?>
            </td>
        </tr>
        <tr>
            <th>
                <label for="type">
                    <?php echo JText::_("COM_TIMECLOCK_TYPE"); ?>:
                </label>
            </th>
            <td>
                <?php print JHTML::_("select.genericList", $this->typeOptions, "type", "", 'value', 'text', $this->row->type); ?>
            </td>
            <td>
                <?php print JText::_("COM_TIMECLOCK_TYPE_PROJECT_DESC"); ?>
            </td>
        </tr>
        <tr>
            <th>
                <label for="category">
                    <?php echo JText::_("JCATEGORY"); ?>:
                </label>
            </th>
            <td>
<?php
if ($this->row->parent_id < -1) {
    print JText::_($this->cat->name);
} else {
    print JHTML::_("select.genericList", $this->parentOptions, "parent_id", "", 'value', 'text', $this->row->parent_id);
}
?>
            </td>
            <td>
                <?php print JText::_("COM_TIMECLOCK_CATEGORY_PROJECT_DESC"); ?>
            </td>
        </tr>
<?php
if ($this->lists["wCompEnable"] != 0) {
    ?>
        <tr>
            <th>
                <label for="parent_id">
                    <?php echo JText::_("COM_TIMECLOCK_WORKERS_COMP_CODES"); ?>:
                </label>
            </th>
            <td>
                <?php for ($i = 1; $i < 7; $i++): ?>
                <?php $var = "wcCode".$i; ?>
                <?php $enable = (int)($this->row->$var >= 0); ?>
                <div style="white-space: nowrap;">
                    <strong><?php print $i; ?>:</strong>
                    <?php print JHTML::_("select.genericList", $this->wCompCodeOptions, $var, "", 'value', 'text', (int)abs($this->row->$var)); ?>
                    <?php print JHTML::_("select.genericList", $this->wCompCodeEnOptions, $var."En", "", 'value', 'text', $enable); ?>
                </div>
                <?php endfor; ?>
            </td>
            <td>
                <?php print JText::_("COM_TIMECLOCK_WORKERS_COMP_CODES_PROJECT_DESC"); ?>
            </td>
        </tr>
    <?php
}
?>
        <tr>
            <th>
                <label for="customer">
                    <?php echo JText::_("COM_TIMECLOCK_CUSTOMER"); ?>:
                </label>
            </th>
            <td>
                <?php print JHTML::_("select.genericList", $this->lists["customers"], "customer", "", 'value', 'text', (int)$this->row->customer); ?>
            </td>
            <td>
                <?php print JText::_("COM_TIMECLOCK_CUSTOMER_PROJECT_DESC"); ?>
            </td>
        </tr>
        <tr>
            <th style="vertical-align:top;">
                <label for="users">
                    <?php echo JText::_("COM_TIMECLOCK_USERS"); ?>:
                </label>
            </th>
            <td>
<?php
$sep = "";
foreach ($this->lists["projectUsers"] as $user) {
    print empty($user->name) ? $sep.$user->id : $sep.$user->name;
    $sep = ", ";
}
?>
            </td>
            <td>
                <?php print JText::_("COM_TIMECLOCK_USERS_PROJECT_DESC"); ?>
            </td>
        </tr>
<?php
if (!$this->add && ($this->row->type !== "CATEGORY")) {
    ?>
        <tr>
            <th>
                <?php echo JText::_("COM_TIMECLOCK_ADD_USERS"); ?>
                <button onClick="this.form.task.value='projects.adduser';this.form.submit();"><?php print JText::_("COM_TIMECLOCK_ADD_USERS"); ?></button>
            </th>
            <td style="padding: 5px;">
                <?php
                    array_shift($this->lists["users"]);
                    print JHTML::_("select.genericList", $this->lists["users"], "user_id[]", 'multiple="multiple" size="10"', 'value', 'text', 0);
                ?>
            </td>
        </tr>
        <tr>
            <th>
                <?php echo JText::_("COM_TIMECLOCK_REMOVE_USERS"); ?>
                <button onClick="this.form.task.value='projects.removeuser';this.form.submit();"><?php print JText::_("COM_TIMECLOCK_REMOVE_USERS"); ?></button>
            </th>
            <td style="padding: 5px;">
                <?php
                $options = array();
                foreach ($this->lists["projectUsers"] as $user) {
                    $options[] = JHTML::_("select.option", $user->id, $user->name);
                }
                print JHTML::_("select.genericList", $options, "remove_user_id[]", 'multiple="multiple" size="10"', 'value', 'text', 0);
                ?>
            </td>
        </tr>
    <?php
}
?>
    </table>
</div>

<div class="clr"></div>
<input type="hidden" name="created" value="<?php print $this->row->created; ?>" />
<input type="hidden" name="created_by" value="<?php print $this->row->created_by; ?>" />
<input type="hidden" name="checked_out" value="<?php print $this->row->checked_out; ?>" />
<input type="hidden" name="checked_out_time" value="<?php print $this->row->checked_out_time; ?>" />


<input type="hidden" name="option" value="com_timeclock" />
<input type="hidden" name="id" value="<?php print $this->row->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="projects" />
<?php print JHTML::_("form.token"); ?>
</form>
