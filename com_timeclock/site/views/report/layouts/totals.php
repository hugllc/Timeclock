<?php 
    defined('_JEXEC') or die('Restricted access'); 
?>
            <tr class="header">
                <th>
                    <?php print JText::_("COM_TIMECLOCK_TOTAL"); ?>
                </th>
<?php foreach ($displayData->users as $user) : ?>
    <?php if ($user->hide) continue; ?>
                <td class="total">
                    <?php print isset($displayData->data[$user->id]) ? $displayData->data[$user->id] : "0"; ?>
                </td>
<?php endforeach; ?>
                <td class="total">
                    <span id="total">
                        <?php print isset($displayData->data["total"]) ? $displayData->data["total"] : "0"; ?>
                    </span>
                </td>
            </tr>
