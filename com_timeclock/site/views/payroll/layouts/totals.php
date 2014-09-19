<?php defined('_JEXEC') or die('Restricted access'); ?>
            <tr class="header <?php print $displayData->rowClass; ?>">
                <th>
                    <?php print JText::_("COM_TIMECLOCK_TOTAL"); ?>
                </th>
<?php
for ($w = 0; $w < $displayData->payperiod->subtotals; $w++) {
    $data = (object)$displayData->totals[$w];
    ?>
            <td id="subtotal-worked-<?php print $w; ?>" class="subtotal-worked subtotal"><?php print $data->worked; ?></td>
            <td id="subtotal-pto-<?php print $w; ?>" class="subtotal-pto subtotal"><?php print $data->pto; ?></td>
            <td id="subtotal-holiday-<?php print $w; ?>" class="subtotal-holiday subtotal"><?php print $data->holiday; ?></td>
            <td id="subtotal-total-<?php print $w; ?>" class="subtotal-total subtotal"><?php print $data->subtotal; ?></td>
    <?php
}
?>
                <th>
                    <?php print JText::_("COM_TIMECLOCK_TOTAL"); ?>
                </th>
                <td class="total">
                    <span id="total"><?php print $displayData->totals["total"]; ?></span>
                </td>
            </tr>
