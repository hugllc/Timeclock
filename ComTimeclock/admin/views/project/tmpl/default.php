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

$title = ($this->add) ? "Add Project" : "Edit Project: <small><small>[ ".$this->row->name." ]</small></small>";

JToolBarHelper::title(JText::_($title));
JToolBarHelper::apply();
JToolBarHelper::save();
JToolBarHelper::cancel();
JRequest::setVar( 'hidemainmenu', 1 );

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
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
                <label for="status">
                    <?php echo JText::_('Status'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.genericList", $this->statusOptions, "status", "", 'value', 'text', $this->row->status); ?>
            </td>
            <td>
                The status of the project
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="parent_id">
                    <?php echo JText::_('Parent Project'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.genericList", $this->parentOptions, "parent_id", "", 'value', 'text', $this->row->status); ?>
            </td>
            <td>
                The parent project
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="parent_id">
                    <?php echo JText::_('Parent Project'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.genericList", $this->wCompCodeOptions, "wcCode", "", 'value', 'text', (int)$this->row->wcCode); ?>
            </td>
            <td>
                The worker's comp code
            </td>
        </tr>
    </table>
</div>

<div class="clr"></div>

<input type="hidden" name="option" value="com_timeclock" />
<input type="hidden" name="id" value="<?php print $this->row->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="project" />
</form>
