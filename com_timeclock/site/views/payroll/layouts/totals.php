<?php defined('_JEXEC') or die('Restricted access'); ?>
            <tr class="subtotal">
                <th>
                    <?php print JText::_("COM_TIMECLOCK_TOTAL"); ?>
                </th>
<?php
for ($w = 0; $w < $displayData->subtotals; $w++) {
    ?>
            <td id="subtotal-worked-<?php print $w; ?>" class="subtotal-worked">-</td>
            <td id="subtotal-pto-<?php print $w; ?>" class="subtotal-pto">-</td>
            <td id="subtotal-holiday-<?php print $w; ?>" class="subtotal-holiday">-</td>
            <td id="subtotal-total-<?php print $w; ?>" class="subtotal-total">-</td>
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
