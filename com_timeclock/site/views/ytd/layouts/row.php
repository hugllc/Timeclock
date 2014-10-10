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
    foreach (array_keys($displayData->cols) as $col) :
        if (isset($displayData->data[$col])) :
            $hours = (float)$displayData->data[$col];
        else :
            $hours = $zero;
        endif;
        if ($col == "total") :
            $class = "total";
        else :
            $class = "hours";
        endif;
?>
                <td class="<?php print $class; ?>"><?php print $hours; ?></td>
<?php endforeach; ?>
            </tr>
<?php endif; ?>
