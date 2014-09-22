<?php 
    defined('_JEXEC') or die('Restricted access'); 
    $worked   = 0;
    $pto      = 0;
    $holiday  = 0;
    $subtotal = 0;
    $overtime = 0;
    for ($w = 0; $w < $displayData->payperiod->subtotals; $w++) {
        $data = (object)$displayData->totals[$w];
        $worked   += $data->worked;
        $pto      += $data->pto;
        $holiday  += $data->holiday;
        $subtotal += $data->subtotal;
        $overtime += $data->overtime;
    }
    $total = $subtotal + $overtime;
?>
            <tr class="header <?php print $displayData->rowClass; ?>">
                <th colspan="2">
                    <?php print JText::_("COM_TIMECLOCK_TOTAL"); ?>
                </th>
                <td id="subtotal-worked" class="subtotal-worked subtotal"><?php print $worked; ?></td>
                <td id="subtotal-pto" class="subtotal-pto subtotal"><?php print $pto; ?></td>
                <td id="subtotal-holiday" class="subtotal-holiday subtotal"><?php print $holiday; ?></td>
                <td id="subtotal-total" class="subtotal-total subtotal"><?php print $subtotal; ?></td>
                <td id="subtotal-overtime" class="subtotal-overtime subtotal"><?php print $overtime; ?></td>
                <td class="total">
                    <span id="total"><?php print $total; ?></span>
                </td>
            </tr>
