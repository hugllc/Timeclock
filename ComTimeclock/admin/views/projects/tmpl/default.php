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

JHTML::_('behavior.tooltip'); 

JToolBarHelper::title(JText::_('Timeclock Projects'));
JToolBarHelper::publishList();
JToolBarHelper::unpublishList();
JToolBarHelper::editListX();
JToolBarHelper::addNewX();

?>
<form action="index.php?option=com_timeclock&controller=projects" method="post" name="adminForm">
<table>
        <tr>
                <td align="left" width="100%">
                        <?php echo JText::_('Filter'); ?>:
                        <input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
                        <?php echo JText::_('by'); ?>:
                        <?php echo JHTML::_('select.genericlist', $this->lists['search_options'], 'search_filter', '', 'value', 'text', $this->lists['search_filter'], 'search_filter'); ?>
                        <button onclick="this.form.submit();"><?php echo JText::_('Go'); ?></button>
                        <button onclick="document.getElementById('search').value='';document.getElementById('search_filter').value='<?php print $this->lists['search_options_default'];?>';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_('Reset'); ?></button>
                </td>
                <td nowrap="nowrap">
                        <?php echo $this->lists['state']; ?>
                </td>
        </tr>
</table>

<div id="tablecell">
        <table class="adminlist">
        <thead>
                <tr>
                        <th width="5">
                                <?php echo JText::_('Id'); ?>
                        </th>
                        <th width="20">
                                <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" />
                        </th>
                        <th  class="title">
                            <?php echo JText::_("Name"); ?>
                        </th>
                        <th width="1%" align="center">
                            <?php echo JText::_("Active"); ?>
                        </th>
                        <?php if (TableTimeclockPrefs::getPref("wCompEnable") != 0) { ?>
                        <th width="5%" align="center">
                            <?php echo JText::_("Worker's Comp"); ?>
                        </th>
                        <?php } ?>
                        <th width="5%" align="center">
                            <?php echo JText::_("Type"); ?>
                        </th>
                        <th width="1%" align="center">
                            <?php echo JText::_("Research"); ?>
                        </th>
                        <th width="5%" nowrap="nowrap">
                            <?php echo JText::_("Created By"); ?>
                        </th>
                </tr>
        </thead>
        <tfoot>
                <tr>
                        <td colspan="9">
                                <?php echo $this->pagination->getListFooter(); ?>
                        </td>
                </tr>
        </tfoot>
        <tbody>
        <?php
        $k = 0;
        for ($i=0, $n=count($this->rows); $i < $n; $i++) {
                $row = &$this->rows[$i];

                $link           = JRoute::_('index.php?option=com_timeclock&controller=projects&task=edit&cid[]='. $row->id);

                $checked        = JHTML::_('grid.checkedout', $row, $i);
                $published      = JHTML::_('grid.published', $row, $i);
                $name           = ($row->parent_id == 0) ? $row->name : $row->parentname.": ".$row->name;
                $author         = empty($row->created_by_name) ? $row->created_by : $row->created_by_name;
        ?>
                <tr class="<?php echo "row$k"; ?>">
                        <td>
                                <?php printf("%04d", $row->id); ?>
                        </td>
                        <td>
                                <?php echo $checked; ?>
                        </td>
                        <td>
                        <?php if (JTable::isCheckedOut($this->user->get('id'), $row->checked_out)) {
                                echo $name;
                        } else {
                                ?>
                                <span class="editlinktip hasTip" title="<?php echo JText::_('Edit Project');?>::<?php echo $name; ?>">
                                <a href="<?php echo $link  ?>">
                                        <?php echo $name; ?></a></span>
                                <?php
                        }
                        ?>
                        </td>
                        <td align="center">
                                <?php echo $published;?>
                        </td>
                        <?php if (TableTimeclockPrefs::getPref("wCompEnable") != 0) { ?>
                        <td align="center">
                                <?php echo $row->wcCode;?>
                        </td>
                        <?php } ?>
                        <td align="center">
                                <?php echo $row->type; ?>
                        </td>
                        <td align="center">
                                <?php echo ($row->research == 0) ? "NO" : "YES"; ?>
                        </td>
                        <td align="center">
                                <?php echo $author; ?>
                        </td>
                </tr>
                <?php
                        $k = 1 - $k;
                }
                ?>
        </tbody>
        </table>
</div>


<div class="clr"></div>

<input type="hidden" name="option" value="com_timeclock" />
<input type="hidden" name="id" value="-1" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="task" id="task" value="" />
<input type="hidden" name="controller" value="projects" />
</form>
