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

JHTML::_('behavior.tooltip');

?>
<form action="index.php" method="post" id="adminForm" name="adminForm">
<table>
    <tr>
        <td align="left" width="100%">
            <?php echo JText::_("COM_TIMECLOCK_FILTER"); ?>:
            <input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
            <button onclick="this.form.submit();"><?php echo JText::_("COM_TIMECLOCK_GO"); ?></button>
            <button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_("COM_TIMECLOCK_RESET"); ?></button>
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
                <?php echo JHTML::_('grid.sort', "COM_TIMECLOCK_ID", 'c.id', @$this->lists['order_Dir'], @$this->lists['order'], "customers.display"); ?>
            </th>
            <th width="20">
                <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" />
            </th>
            <th  class="title">
                <?php echo JHTML::_('grid.sort', "COM_TIMECLOCK_COMPANY", 'c.company', @$this->lists['order_Dir'], @$this->lists['order'], "customers.display"); ?>
            </th>
            <th width="10%" nowrap="nowrap">
                <?php echo JHTML::_('grid.sort', "COM_TIMECLOCK_CONTACT_NAME", 'c.name', @$this->lists['order_Dir'], @$this->lists['order'], "customers.display"); ?>
            </th>
            <th width="1%" nowrap="nowrap">
                <?php echo JHTML::_('grid.sort', "COM_TIMECLOCK_ACTIVE", 'c.published', @$this->lists['order_Dir'], @$this->lists['order'], "customers.display"); ?>
            </th>
            <th width="1%" nowrap="nowrap">
                <?php echo JHTML::_('grid.sort', "COM_TIMECLOCK_BILL_PTO", 'c.bill_pto', @$this->lists['order_Dir'], @$this->lists['order'], "customers.display"); ?>
            </th>
            <th nowrap="nowrap">
                <?php echo JHTML::_('grid.sort', "COM_TIMECLOCK_NOTES", 'c.notes', @$this->lists['order_Dir'], @$this->lists['order'], "customers.display"); ?>
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

    $link           = JRoute::_('index.php?option=com_timeclock&task=customers.edit&cid[]='. $row->id);

    $checked        = JHTML::_('grid.checkedout', $row, $i);
    $published      = JHTML::_('grid.published', $row, $i, 'tick.png', 'publish_x.png', "customers.");
    $bill_pto       = ($row->bill_pto == 0) ? JText::_("JNO") : JText::_("JYES");
    $notes          = substr($row->notes, 0, 60);
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
        echo $row->company;
    } else {
        ?>
                <span class="editlinktip hasTip" title="<?php echo JText::_("COM_TIMECLOCK_EDIT_CUSTOMER");?>::<?php echo $row->company; ?>">
                <a href="<?php echo $link  ?>">
                <?php echo $row->company; ?></a></span>
        <?php
    }
            ?>
            </td>
            <td align="center">
                    <?php echo $row->name; ?>
            </td>
            <td align="center">
                    <?php echo $published; ?>
            </td>
            <td align="center">
                    <?php echo $bill_pto; ?>
            </td>
            <td>
                    <?php echo $row->notes; ?>
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
<input type="hidden" name="task" id="task" value="customers.display" />
<input type="hidden" name="controller" value="customers" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
<?php print JHTML::_("form.token"); ?>
</form>
