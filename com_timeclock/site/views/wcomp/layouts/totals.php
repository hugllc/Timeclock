<?php

use Joomla\CMS\Language\Text;
 
    defined('_JEXEC') or die('Restricted access'); 
    $places = $displayData->params->get("decimalPlaces");
?>
            <tr class="header">
                <th>
                    <?php print Text::_("COM_TIMECLOCK_TOTAL"); ?>
                </th>
<?php foreach ($displayData->codes as $code) : ?>
        <?php $hours   = isset($displayData->data[$code]) ? $displayData->data[$code] : "0"; ?>
                <td class="total">
                    <?php print $hours; ?>
                </td>
<?php endforeach; ?>
                <td class="total">
                    <span id="total">
                        <?php print isset($displayData->data["total"]) ? $displayData->data["total"] : "0"; ?>
                    </span>
                </td>
            </tr>
