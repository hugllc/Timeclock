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
            </tr>
