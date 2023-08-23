<?php

use Joomla\CMS\Language\Text;
 
    defined('_JEXEC') or die('Restricted access'); 
    $places = $displayData->params->get("decimalPlaces");
?>
            <tr class="header">
                <th>
                    <?php print Text::_("COM_TIMECLOCK_TOTAL"); ?>
                </th>
<?php foreach ($displayData->projects as $projects) : ?>
    <?php foreach ($projects["proj"] as $proj) : ?>
        <?php $proj_id = (int)$proj->project_id; ?>
        <?php $hours   = isset($displayData->data[$proj_id]) ? $displayData->data[$proj_id] : "0"; ?>
        <?php $perc    = !empty($displayData->data["total"]) ? round(($hours / $displayData->data["total"]) * 100, $places) : "0"; ?>
                <td class="total">
                    <?php print $hours; ?>
                </td>
                <td class="total"><?php print $perc; ?>%</td>
    <?php endforeach; ?>
<?php endforeach; ?>
                <td class="total">
                    <span id="total">
                        <?php print isset($displayData->data["total"]) ? $displayData->data["total"] : "0"; ?>
                    </span>
                </td>
            </tr>
