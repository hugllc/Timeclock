<?php

use Joomla\CMS\Language\Text;
 
    defined('_JEXEC') or die(); 
    $worked    = 0;
    $pto       = 0;
    $holiday   = 0;
    $subtotal  = 0;
    $overtime  = 0;
    $volunteer = 0;
    for ($w = 0; $w < $displayData->payperiod->subtotals; $w++) {
        if (!isset($displayData->totals[$w])) {
            continue;
        }
        $data = (object)$displayData->totals[$w];
        $worked    += $data->worked;
        $pto       += $data->pto;
        $holiday   += $data->holiday;
        $subtotal  += $data->subtotal;
        $overtime  += $data->overtime;
        $volunteer += $data->volunteer;
    }
    $total = $subtotal + $overtime;
?>
            <tr class="header">
                <th colspan="3">
                    <?php print Text::_("COM_TIMECLOCK_TOTAL"); ?>
                </th>
                <td id="subtotal-worked" class="subtotal-worked subtotal"><?php print $worked; ?></td>
                <td id="subtotal-pto" class="subtotal-pto subtotal"><?php print $pto; ?></td>
                <td id="subtotal-volunteer" class="subtotal-volunteer subtotal"><?php print $volunteer; ?></td>
                <td id="subtotal-holiday" class="subtotal-holiday subtotal"><?php print $holiday; ?></td>
                <td id="subtotal-total" class="subtotal-total subtotal"><?php print $subtotal; ?></td>
                <td id="subtotal-overtime" class="subtotal-overtime subtotal"><?php print $overtime; ?></td>
                <td class="total">
                    <span id="total"><?php print $total; ?></span>
                </td>
            </tr>
