<?php 
    defined('_JEXEC') or die('Restricted access'); 
    $rowclass = "employee";

?>
            <tr class="employee">
                <th>
                    <?php print $displayData->name ?>
                </th>
<?php
for ($w = 0; $w < $displayData->payperiod->subtotals; $w++) {
    $class = "user-".$w."-".$displayData->user_id;
    ?>
            <td>
                <span id="hours-<?php print $displayData->user_id."-".$w; ?>" class="worked-<?php print $w; ?> <?php print $class;?> ">-</span>
            </td>
            <td>
                <span id="pto-<?php print $displayData->user_id."-".$w; ?>" class="pto-<?php print $w; ?> <?php print $class;?> ">-</span>
            </td>
            <td>
                <span id="holiday-<?php print $displayData->user_id."-".$w; ?>" class="holiday-<?php print $w; ?> <?php print $class;?> ">-</span>
            </td>
            <td>
                <span id="subtotal-<?php print $displayData->user_id."-".$w; ?>" class="subtotal-<?php print $w."-".$displayData->user_id; ?>">-</span>
            </td>
    <?php
}
?>
                <th>
                    <?php print $displayData->name ?>
                </th>
                <td class="total">
                    <span id="total-<?php print $displayData->user_id?>">-</span>
                </td>
            </tr>
            