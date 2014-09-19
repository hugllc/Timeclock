<?php
defined('_JEXEC') or die('Restricted access');
?>
        <tr class="header">
            <th rowspan="2"><?php print JHTML::_('grid.sort', "COM_TIMECLOCK_EMPLOYEE", 'u.name', @$this->lists['order_Dir'], @$this->lists['order']); ?><?php //print JText::_("Employee"); ?></th>
<?php
for ($w = 0; $w < $displayData->subtotals; $w++) {
    ?>
            <th colspan="4" align="center"><?php print JText::_("COM_TIMECLOCK_WEEK")." ".($w+1); ?> </th>
    <?php
}
?>
            <th rowspan="2"><?php print JHTML::_('grid.sort', "COM_TIMECLOCK_EMPLOYEE", 'u.name', @$this->lists['order_Dir'], @$this->lists['order']); ?><?php //print JText::_("Employee"); ?></th>
            <th rowspan="2"><?php print JText::_("COM_TIMECLOCK_TOTAL"); ?></th>
        </tr>
        <tr class="subheader">
<?php
for ($w = 0; $w < $displayData->subtotals; $w++) {
    ?>
            <th><?php print JText::_("COM_TIMECLOCK_WORKED"); ?> </th>
            <th><?php print JText::_("COM_TIMECLOCK_PTO"); ?> </th>
            <th><?php print JText::_("COM_TIMECLOCK_HOLIDAY"); ?> </th>
            <th><?php print JText::_("COM_TIMECLOCK_TOTAL"); ?> </th>
    <?php
}
?>
        </tr>
