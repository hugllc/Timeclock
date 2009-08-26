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

JHTML::_('behavior.tooltip');

TimeclockAdminController::title(JText::_('Timeclock Projects'));
JToolBarHelper::publishList('publish', 'Activate');
JToolBarHelper::unpublishList('unpublish', 'Deactivate');
JToolBarHelper::editListX();
JToolBarHelper::addNewX();

?>
<form action="index.php" method="post" name="adminForm">
<table>
    <tr>
        <td align="left" width="100%">
            <?php echo JText::_('Filter'); ?>:
            <input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
            <?php echo JText::_('by'); ?>:
            <?php echo JHTML::_('select.genericlist', $this->lists['search_options'], 'search_filter', '', 'value', 'text', $this->lists['search_filter'], 'search_filter'); ?>
            <button onclick="this.form.submit();"><?php echo JText::_('Go'); ?></button>
            <button onclick="document.getElementById('search').value='';document.getElementById('search_filter').value='<?php print $this->lists['search_options_default'];?>';this.form.submit();"><?php echo JText::_('Reset'); ?></button>
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
                        <?php echo JHTML::_('grid.sort', 'Id', 't.id', @$this->lists['order_Dir'], @$this->lists['order']); ?>
                    </th>
                    <th width="20">
                            <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" />
                    </th>
                    <th  class="title">
                        <?php echo JHTML::_('grid.sort', 'Name', 't.name', @$this->lists['order_Dir'], @$this->lists['order']); ?>
                    </th>
                    <th align="center">
                        <?php echo JHTML::_('grid.sort', 'Category', 'p.name', @$this->lists['order_Dir'], @$this->lists['order']); ?>
                    </th>
                    <th align="center">
                        <?php echo JHTML::_('grid.sort', 'Customer', 'c.company', @$this->lists['order_Dir'], @$this->lists['order']); ?>
                    </th>
                    <th width="1%" align="center">
                        <?php echo JHTML::_('grid.sort', 'Active', 't.published', @$this->lists['order_Dir'], @$this->lists['order']); ?>
                    </th>
<?php
if ($this->lists["wCompEnable"] != 0) { ?>
                    <th width="5%" align="center">
                        <?php echo JHTML::_('grid.sort', "Worker's Comp", 't.wcCode', @$this->lists['order_Dir'], @$this->lists['order']); ?>
                    </th>
    <?php
}
?>
                    <th width="5%" align="center">
                        <?php echo JHTML::_('grid.sort', 'Type', 't.Type', @$this->lists['order_Dir'], @$this->lists['order']); ?>
                    </th>
                    <th width="1%" align="center">
                        <?php echo JHTML::_('grid.sort', 'Research', 't.research', @$this->lists['order_Dir'], @$this->lists['order']); ?>
                    </th>
                    <th width="5%" nowrap="nowrap">
                        <?php echo JHTML::_('grid.sort', 'Manager', 't.manager', @$this->lists['order_Dir'], @$this->lists['order']); ?>
                    </th>
            </tr>
    </thead>
    <tfoot>
            <tr>
                    <td colspan="10">
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
    $parentlink     = JRoute::_('index.php?option=com_timeclock&controller=projects&task=edit&cid[]='. $row->parent_id);
    $customerlink   = JRoute::_('index.php?option=com_timeclock&controller=customers&task=edit&cid[]='. $row->customer);

    $checked        = JHTML::_('grid.checkedout', $row, $i);
    $published      = JHTML::_('grid.published', $row, $i);
    $author         = empty($row->manager_name) ? "None" : $row->manager_name;

    $wcCode = $sep  = "";
    for ($j = 1; $j < 7; $j++) {
        $var = "wcCode".$j;
        if ($row->$var == 0) continue;
        $wcCode .= $sep.$row->$var;
        $sep = ", ";
    }

    ?>
        <tr class="<?php echo "row$k"; ?>">
            <td>
                    <?php printf("%04d", $row->id); ?>
            </td>
            <td>
                    <?php echo $checked; ?>
            </td>
            <td>
            <?php
    if (JTable::isCheckedOut($this->user->get('id'), $row->checked_out)) {
        echo $row->name;
    } else {
        ?>
        <span class="editlinktip hasTip" title="<?php echo JText::_('Edit Project');?>::<?php echo $row->name; ?>">
        <a href="<?php echo $link  ?>">
        <?php echo $row->name; ?></a></span>
        <?php
    }
            ?>
            </td>
            <td>
            <?php
    if ($row->parent_id > 0) {
        if (JTable::isCheckedOut($this->user->get('id'), $row->parent_checked_out)) {
                echo $row->parentname;
        } else {
            ?>
            <span class="editlinktip hasTip" title="<?php echo JText::_('Edit Project');?>::<?php echo $row->parentname; ?>">
            <a href="<?php echo $parentlink  ?>">
            <?php echo $row->parentname; ?></a></span>
            <?php
        }
    } else {
        echo JText::_("None");
    }
            ?>
            </td>
            <td>
            <?php
    if ($row->customer > 0) {
        if (JTable::isCheckedOut($this->user->get('id'), $row->customer_checked_out)) {
                echo $row->customer_name;
        } else {
            ?>
            <span class="editlinktip hasTip" title="<?php echo JText::_('Edit Customer');?>::<?php echo $row->customer_name; ?>">
            <a href="<?php echo $customerlink  ?>">
            <?php echo $row->customer_name; ?></a></span>
            <?php
        }
    } else {
        echo JText::_("None");
    }
            ?>
            </td>
            <td align="center">
                <?php echo $published;?>
            </td>
    <?php
    if ($this->lists["wCompEnable"] != 0) { ?>
            <td align="center">
                <?php echo $wcCode;?>
            </td>
        <?php
    }
    ?>
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
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
<?php echo JHTML::_('form.token'); ?>
</form>
