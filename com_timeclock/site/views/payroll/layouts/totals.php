<?php defined('_JEXEC') or die('Restricted access'); ?>
            <tr class="subtotal">
                <th>
                    <?php print JText::_("COM_TIMECLOCK_TOTAL"); ?>
                </th>
<?php
for ($w = 0; $w < $displayData->payperiod->subtotals; $w++) {
    ?>
            <td id="subtotal-worked-<?php print $w; ?>" class="subtotal-worked subtotal"><?php print $displayData->totals[$w]->worked; ?></td>
            <td id="subtotal-pto-<?php print $w; ?>" class="subtotal-pto subtotal"><?php print $displayData->totals[$w]->pto; ?></td>
            <td id="subtotal-holiday-<?php print $w; ?>" class="subtotal-holiday subtotal"><?php print $displayData->totals[$w]->holiday; ?></td>
            <td id="subtotal-total-<?php print $w; ?>" class="subtotal-total subtotal"><?php print $displayData->totals[$w]->subtotal; ?></td>
    <?php
}
?>
                <th>
                    <?php print JText::_("COM_TIMECLOCK_TOTAL"); ?>
                </th>
                <td class="total">
                    <span id="total">-</span>
                </td>
            </tr>
