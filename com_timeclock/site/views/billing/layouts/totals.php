<?php 
    defined('_JEXEC') or die('Restricted access'); 
    $places = $displayData->params->get("decimalPlaces");
    $cost = isset($displayData->data["cost"]) ? $displayData->data["cost"] : "0";
?>
            <tr class="header">
                <th>
                    <?php print JText::_("COM_TIMECLOCK_TOTAL"); ?>
                </th>
                <td class="total">
                    <span id="total">
                        <?php print isset($displayData->data["total"]) ? $displayData->data["total"] : "0"; ?>
                    </span>
                </td>
                <th>&nbsp;</th>
                <td class="total cost"><?php print number_format($cost, 2); ?></td>
            </tr>
