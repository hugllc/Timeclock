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

JHTML::_('behavior.tooltip');

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<table>
    <tr>
        <td align="left" width="100%">
            <?php echo JText::_(COM_TIMECLOCK_FILTER); ?>:
            <input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
            <?php echo JText::_(COM_TIMECLOCK_BY); ?>:
            <?php echo JHTML::_('select.genericlist', $this->lists['search_options'], 'search_filter', '', 'value', 'text', $this->lists['search_filter'], 'search_filter'); ?>
            <button onclick="this.form.submit();"><?php echo JText::_(COM_TIMECLOCK_GO); ?></button>
            <button onclick="document.getElementById('search').value='';document.getElementById('search_filter').value='<?php print $this->lists['search_options_default'];?>';this.form.submit();"><?php echo JText::_(COM_TIMECLOCK_RESET); ?></button>
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
                <?php echo JHTML::_('grid.sort', COM_TIMECLOCK_ID, 'u.id', @$this->lists['order_Dir'], @$this->lists['order'], "users.display"); ?>
            </th>
            <th width="20">
                <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" />
            </th>
            <th  class="title">
                <?php echo JHTML::_('grid.sort', COM_TIMECLOCK_NAME, 'u.name', @$this->lists['order_Dir'], @$this->lists['order'], "users.display"); ?>
            </th>
            <th width="1%" align="center">
                <?php echo JText::_(COM_TIMECLOCK_ACTIVE); ?>
            </th>
            <th width="1%" align="center">
                <?php echo JText::_(COM_TIMECLOCK_REPORTS); ?>
            </th>
            <th width="10%" align="center">
                <?php echo JHTML::_('grid.sort', COM_TIMECLOCK_LAST_VISIT, 'u.lastvisitDate', @$this->lists['order_Dir'], @$this->lists['order'], "users.display"); ?>
            </th>
            <th width="10%" align="center">
                <?php echo JText::_(COM_TIMECLOCK_START_DATE); ?>
            </th>
            <th width="10%" align="center">
                <?php echo JText::_(COM_TIMECLOCK_END_DATE); ?>
            </th>
            <th width="5%" align="center">
                <?php echo JText::_(COM_TIMECLOCK_STATUS); ?>
            </th>
            <th width="5%" align="center">
                <?php echo JText::_(COM_TIMECLOCK_PTO); ?>
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

    $link           = JRoute::_('index.php?option=com_timeclock&task=users.edit&cid[]='. $row->id);

    $checked        = JHTML::_('grid.checkedout', $row, $i);
    $published      = JHTML::_('grid.published', $row, $i, 'tick.png', 'publish_x.png', "users.");

    if ($row->pto > $row->ptoYTD) {
        if (abs($row->pto - $row->ptoYTD) <= $this->ptoNegative) {
            $ptoStyle = "background: #FFFF00; color: black;";
        } else {
            $ptoStyle = "background: #FF0000; color: black;";
        }
    } else {
        $ptoStyle = "";
    }

    $author         = empty($row->created_by_name) ? $row->created_by : $row->created_by_name;
    ?>
        <tr class="<?php echo "row$k"; ?>">
            <td>
                <?php echo $row->id; ?>
            </td>
            <td>
                <?php echo $checked; ?>
            </td>
            <td>
                <span class="editlinktip hasTip" title="<?php echo JText::_(COM_TIMECLOCK_EDIT_USER);?>::<?php echo $row->name; ?>">
                <a href="<?php echo $link  ?>">
                <?php echo $row->name; ?></a></span>
            </td>
            <td align="center">
                <?php echo $published;?>
            </td>
            <td align="center">
                <?php echo $row->prefs["reports"] ? "YES" : "NO";?>
            </td>
            <td align="center">
                <?php echo $row->lastvisitDate; ?>
            </td>
            <td align="center">
                <?php echo $row->startDate; ?>
            </td>
            <td align="center">
                <?php echo ($row->endDate != "0000-00-00") ? $row->endDate : "---"; ?>
            </td>
            <td align="center">
                <?php echo $row->prefs["status"]; ?>
            </td>
            <td align="center" style="<?php print $ptoStyle; ?>">
                <?php echo $row->pto." / ".$row->ptoYTD; ?>
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
<input type="hidden" name="task" id="task" value="users.display" />
<input type="hidden" name="controller" id="controller" value="users" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
<?php echo JHTML::_('form.token'); ?>
</form>
