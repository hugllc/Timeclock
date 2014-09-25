<?php 
    defined('_JEXEC') or die('Restricted access'); 
    $proj_id = $displayData->project_id;
?>
            <tr class="project <?php print $displayData->rowClass; ?>">
                <td>
                    <?php print $displayData->name ?>
                </td>
<?php foreach ($displayData->users as $user) : ?>
                <td class="hours">
                    <?php print isset($displayData->data[$user->id]) ? $displayData->data[$user->id] : '<span class="zero">0</span>'; ?>
                </td>
<?php endforeach; ?>
                <td class="total">
                    <span id="total-<?php print $proj_id?>">
                        <?php print isset($displayData->data["total"]) ? $displayData->data["total"] : "0"; ?>
                    </span>
                </td>
            </tr>
            