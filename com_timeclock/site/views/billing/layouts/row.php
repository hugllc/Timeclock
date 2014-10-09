<?php 
defined('_JEXEC') or die('Restricted access');
if (!$displayData->hide) :   
    $user_id = $displayData->user_id;
    $cost    = isset($displayData->data["cost"]) ? $displayData->data["cost"] : "0";
    $places  = $displayData->genparams->get("decimalPlaces");
    $zero    = '<span class="zero">0</span>';
    if (!empty($displayData->data["error"])) {
        $errorClass   = " error hasTooltip";
        $errorTooltip = ' title="'.$displayData->data["error"].'"';
    } else {
        $errorClass = "";
        $errorTooltip = "";
    }
?>
            <tr class="user<?php print $errorClass; ?>"<?php print $errorTooltip; ?>>
                <td>
                    <?php print $displayData->name ?>
                </td>
                <td class="hours"><?php print $displayData->data["hours"]; ?></td>
                <td class="cost"><?php print $displayData->view->currency($displayData->data["rate"]); ?></td>
                <td class="total cost"><?php print $displayData->view->currency($cost); ?></span>
                </td>
            </tr>
<?php endif; ?>
