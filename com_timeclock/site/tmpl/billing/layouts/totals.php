<?php

use Joomla\CMS\Language\Text;
 
    defined('_JEXEC') or die(); 
    $places = $displayData->params->get("decimalPlaces");
    $cost = isset($displayData->data["cost"]) ? $displayData->data["cost"] : "0";
?>
            <tr class="header">
                <th>
                    <?php print Text::_("COM_TIMECLOCK_TOTAL"); ?>
                </th>
                <td class="total">
                    <span id="total">
                        <?php print isset($displayData->data["total"]) ? $displayData->data["total"] : "0"; ?>
                    </span>
                </td>
                <th>&nbsp;</th>
                <td class="total cost"><?php print $displayData->view->currency($cost); ?></td>
            </tr>
