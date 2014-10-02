<?php 
defined('_JEXEC') or die('Restricted access');
if (!$displayData->hide) :   
    $user_id = $displayData->user_id;
    $total   = isset($displayData->data["total"]) ? $displayData->data["total"] : "0";
    $places  = $displayData->genparams->get("decimalPlaces");
    $zero    = '<span class="zero">0</span>';
    $cols    = array("PROJECT", "HOLIDAY", "UNPAID", "PTO");
?>
            <tr class="project<?php print ($total == 0) ? " empty" : ""; ?>">
                <td>
                    <?php print $displayData->name ?>
                </td>
               
<?php 
    foreach ($cols as $col) :
        if (isset($displayData->data[$col]) && ($displayData->data[$col] > 0)) :
            $hours = (float)$displayData->data[$col];
        else :
            $hours = $zero;
        endif;
?>
                <td class="hours"><?php print $hours; ?></td>
<?php endforeach; ?>
                <td class="total">
                    <span id="total-<?php print $user_id?>">
                        <?php print $total; ?>
                    </span>
                </td>
            </tr>
<?php endif; ?>
