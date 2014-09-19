<?php 
    defined('_JEXEC') or die('Restricted access'); 
    $user_id = $displayData->id;
    $name  = empty($displayData->name) ? "User ".$displayData->user_id : $displayData->name;
    $total = 0;
?>
            <tr class="employee <?php print $displayData->rowClass; ?>">
                <th>
                    <a class="modal" href="#notes-<?php print $user_id; ?>" rel="{onOpen : function(){ jQuery('#sbox-content div').show(); }}"><?php print $name ?></a>
                </th>
<?php
for ($w = 0; $w < $displayData->payperiod->subtotals; $w++) {
    $class    = "user-".$w."-".$user_id;
    $worked   = 0;
    $pto      = 0;
    $holiday  = 0;
    $subtotal = 0;
    if (isset($displayData->data[$w])) {
        $data = (object)$displayData->data[$w];
        $worked   = $data->worked;
        $pto      = $data->pto;
        $holiday  = $data->holiday;
        $subtotal = $data->subtotal;
    }
    $total   += $subtotal;
    ?>
            <td class="hours">
                <span id="hours-<?php print $user_id."-".$w; ?>" class="worked-<?php print $w; ?> <?php print $class;?>"><?php print $worked; ?></span>
            </td>
            <td class="hours">
                <span id="pto-<?php print $user_id."-".$w; ?>" class="pto-<?php print $w; ?> <?php print $class;?>"><?php print $pto; ?></span>
            </td>
            <td class="hours">
                <span id="holiday-<?php print $user_id."-".$w; ?>" class="holiday-<?php print $w; ?> <?php print $class;?>"><?php print $holiday; ?></span>
            </td>
            <td class="subtotal">
                <span id="subtotal-<?php print $user_id."-".$w; ?>" class="subtotal-<?php print $w."-".$user_id; ?>"><?php print $subtotal; ?></span>
            </td>
    <?php
}
?>
                <th>
                    <?php print $name ?>
                </th>
                <td class="total">
                    <span id="total-<?php print $user_id?>"><?php print $total; ?></span>
                </td>
            </tr>
            