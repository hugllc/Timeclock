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
                    <?php
                        if ($displayData->money) {
                            print isset($displayData->data[$user->id]) ? $displayData->view->currency($displayData->data[$user->id]) : '<span class="zero">'.$displayData->view->currency(0).'</span>';
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
                                print isset($displayData->data["total"]) ? $displayData->view->currency($displayData->data["total"]) : '<span class="zero">'.$displayData->view->currency(0).'</span>';
                            } else {
                                print isset($displayData->data["total"]) ? $displayData->data["total"] : '<span class="zero">0</span>'; 
                            }
                        ?>
                    </span>
                </td>
            </tr>
