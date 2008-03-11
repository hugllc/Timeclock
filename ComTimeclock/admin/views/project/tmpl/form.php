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

TimeclockAdminController::title(JText::_("Project: <small><small>[ ".$title." ]</small></small>"));
JToolBarHelper::apply();
JToolBarHelper::save();
JToolBarHelper::cancel();

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<?php
if (!$this->add) {
    ?>
    <div style="float: right; width: 30%;">
    <?php
    $pane = JPane::getInstance("sliders");
    echo $pane->startPane("user-pane");  
    echo $pane->startPanel(JText::_("Users"), "user-page");
    ?>
    <div style="padding: 5px;">
    <?php 
    foreach ($this->lists["projectUsers"] as $user) { ?>
                    <button onClick="this.form.task.value='removeuser';this.form.user_id.value='<?php print $user->id;?>';this.form.submit();">Remove</button>
                    <?php print empty($user->name) ? $user->id : $user->name; ?><br />
        <?php 
    } 
    ?>
    </div>
    <?php
    echo $pane->endPanel();
    echo $pane->startPanel(JText::_("Add Users"), "adduser-page");
    ?>
    <div style="padding: 5px;">
    <?php 
        array_shift($this->lists["users"]);
        print JHTML::_("select.genericList", $this->lists["users"], "user_id[]", 'multiple="multiple"', 'value', 'text', 0); 
    ?><br />
        <button onClick="this.form.task.value='adduser';this.form.submit();">Add Users</button>
    </div>
    <?php
    echo $pane->endPanel();
    echo $pane->endPane(); 
    ?>
    </div>
    <?php
}
?>
<div>
    <table class="admintable">
        <tr>
            <td width="100" align="right" class="key">
                <label for="name">
                    <?php echo JText::_('Name'); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="name" id="name" size="32" maxlength="64" value="<?php echo $this->row->name;?>" />
            </td>
            <td>
                The name of the project
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key" style="vertical-align: top;">
                <label for="description">
                    <?php echo JText::_('Description'); ?>:
                </label>
            </td>
            <td>
                <textarea class="text_area" type="text" name="description" id="description" cols="30" rows="5"><?php echo $this->row->description;?></textarea>
            </td>
            <td>
                A description of the project
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="Research">
                    <?php echo JText::_('Research'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.booleanList", "research", "", $this->row->research); ?>
            </td>
            <td>
                Is this project research?
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="published">
                    <?php echo JText::_('Active'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.booleanList", "published", "", $this->row->published); ?>
            </td>
            <td>
                Is this project active?
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="type">
                    <?php echo JText::_('Type'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.genericList", $this->typeOptions, "type", "", 'value', 'text', $this->row->type); ?>
            </td>
            <td>
                The type of project.
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="category">
                    <?php echo JText::_('Category'); ?>:
                </label>
            </td>
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
                The category the project is in.
            </td>
        </tr>
<?php 
if ($this->lists["wCompEnable"] != 0) { 
    ?>
        <tr>
            <td width="100" align="right" class="key">
                <label for="parent_id">
                    <?php echo JText::_('Workers Comp Code'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.genericList", $this->wCompCodeOptions, "wcCode", "", 'value', 'text', (int)$this->row->wcCode); ?>
            </td>
            <td>
                The worker's comp code
            </td>
        </tr>
    <?php
}
?>
        <tr>
            <td width="100" align="right" class="key">
                <label for="customer">
                    <?php echo JText::_('Customer'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.genericList", $this->lists["customers"], "customer", "", 'value', 'text', (int)$this->row->customer); ?>
            </td>
            <td>
                The customer this project should be billed to
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
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="projects" />
</form>
