<?php

use Joomla\CMS\Language\Text;
 
    defined('_JEXEC') or die(); 
?>
            <tr class="header">
                <th>
                    <?php print Text::_("COM_TIMECLOCK_TOTAL"); ?>
                </th>
<?php foreach ($displayData->users as $user) : ?>
    <?php if ($user->hide) continue; ?>
                <td class="total">
                    <?php
                        if ($displayData->money) {
                            print isset($displayData->data[$user->id]) ? $displayData->currency.number_format($displayData->data[$user->id], 2) : '<span class="zero">'.$displayData->currency.'0.00</span>';
                        } else {
                            print isset($displayData->data[$user->id]) ? $displayData->data[$user->id] : '<span class="zero">0</span>'; 
                        }
                    ?>
                </td>
<?php endforeach; ?>
                <td class="total">
                    <span id="total">
                        <?php
                            if ($displayData->money) {
                                print isset($displayData->data["total"]) ? $displayData->currency.number_format($displayData->data["total"], 2) : '<span class="zero">'.$displayData->currency.'0.00</span>';
                            } else {
                                print isset($displayData->data["total"]) ? $displayData->data["total"] : '<span class="zero">0</span>'; 
                            }
                        ?>
                    </span>
                </td>
            </tr>
