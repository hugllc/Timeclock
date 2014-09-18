<?php 
    defined('_JEXEC') or die('Restricted access'); 
    $name  = empty($displayData->name) ? "User ".$displayData->user_id : $displayData->name;
    $total = 0;
?>
            <tr class="employee">
                <th>
                    <?php print $name ?>
                </th>
<?php
for ($w = 0; $w < $displayData->payperiod->subtotals; $w++) {
    $class    = "user-".$w."-".$displayData->user_id;
    $worked   = 0;
    $pto      = 0;
    $holiday  = 0;
    $subtotal = 0;
    if (isset($displayData->data[$w])) {
        $worked   = $displayData->data[$w]->worked;
        $pto      = $displayData->data[$w]->pto;
        $holiday  = $displayData->data[$w]->holiday;
        $subtotal = $displayData->data[$w]->subtotal;
    }
    $total   += $subtotal;
    ?>
            <td class="hours">
                <span id="hours-<?php print $displayData->user_id."-".$w; ?>" class="worked-<?php print $w; ?> <?php print $class;?>"><?php print $worked; ?></span>
            </td>
            <td class="hours">
                <span id="pto-<?php print $displayData->user_id."-".$w; ?>" class="pto-<?php print $w; ?> <?php print $class;?>"><?php print $pto; ?></span>
            </td>
            <td class="hours">
                <span id="holiday-<?php print $displayData->user_id."-".$w; ?>" class="holiday-<?php print $w; ?> <?php print $class;?>"><?php print $holiday; ?></span>
            </td>
            <td class="subtotal">
                <span id="subtotal-<?php print $displayData->user_id."-".$w; ?>" class="subtotal-<?php print $w."-".$displayData->user_id; ?>"><?php print $subtotal; ?></span>
            </td>
    <?php
}
?>
                <th>
                    <?php print $name ?>
                </th>
                <td class="total">
                    <span id="total-<?php print $displayData->user_id?>"><?php print $total; ?></span>
                </td>
            </tr>
            