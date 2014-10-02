<?php 
defined('_JEXEC') or die('Restricted access');
if (!$displayData->hide) :   
    $user_id = $displayData->user_id;
    $total   = isset($displayData->data["total"]) ? $displayData->data["total"] : "0";
    $places  = $displayData->genparams->get("decimalPlaces");
    $zero    = '<span class="zero">0</span>';
?>
            <tr class="project<?php print ($total == 0) ? " empty" : ""; ?>">
                <td>
                    <?php print $displayData->name ?>
                </td>
<?php 
    foreach ($displayData->codes as $code) :
        if (isset($displayData->data[$code])) :
            $hours = (float)$displayData->data[$code];
        else :
            $hours = $zero;
        endif;
?>
                <td class="hours"><?php print $hours; ?></td>
                </td>
<?php endforeach; ?>
                <td class="total">
                    <span id="total-<?php print $user_id?>">
                        <?php print $total; ?>
                    </span>
                </td>
            </tr>
<?php endif; ?>