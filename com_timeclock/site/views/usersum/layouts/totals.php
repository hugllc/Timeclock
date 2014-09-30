<?php 
    defined('_JEXEC') or die('Restricted access'); 
?>
            <tr class="header">
                <th>
                    <?php print JText::_("COM_TIMECLOCK_TOTAL"); ?>
                </th>
<?php foreach ($displayData->projects as $projects) : ?>
    <?php foreach ($projects["proj"] as $proj) : ?>
        <?php $proj_id = (int)$proj->project_id; ?>
                <td class="total">
                    <?php print isset($displayData->data[$proj_id]) ? $displayData->data[$proj_id] : "0"; ?>
                </td>
                <th>&nbsp;</th>
    <?php endforeach; ?>
<?php endforeach; ?>
                <td class="total">
                    <span id="total">
                        <?php print isset($displayData->data["total"]) ? $displayData->data["total"] : "0"; ?>
                    </span>
                </td>
            </tr>
