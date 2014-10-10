<?php 
    defined('_JEXEC') or die('Restricted access'); 
    $places = $displayData->params->get("decimalPlaces");
?>
            <tr class="header">
                <th>
                    <?php print JText::_("COM_TIMECLOCK_TOTAL"); ?>
                </th>
<?php foreach (array_keys($displayData->cols) as $col) : ?>
        <?php $hours   = isset($displayData->data[$col]) ? $displayData->data[$col] : "0"; ?>
                <td class="total">
                    <?php print $hours; ?>
                </td>
<?php endforeach; ?>
                <td class="total hasTooltip" title="<?php print JText::_("COM_TIMECLOCK_PTO_CURRENT_DESC"); ?>">
                    <span id="total">
                        <?php print isset($displayData->data["PTO_CURRENT"]) ? $displayData->data["PTO_CURRENT"] : "0"; ?>
                    </span>
                </td>
            </tr>
