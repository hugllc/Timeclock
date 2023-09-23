<?php 
    defined('_JEXEC') or die(); 
    $proj_id = $displayData->project_id;

    $total = isset($displayData->data["total"]) ? $displayData->data["total"] : "0";
?>
            <tr class="project<?php print ($total == 0) ? " empty" : ""; ?>">
                <td>
                    <?php print $displayData->name ?>
                </td>
<?php foreach ($displayData->users as $user) : ?>
    <?php
        $hide    = property_exists($user, "hide") ? $user->hide : false;
        if ($hide) continue; ?>
                <td class="hours">
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
                    <span id="total-<?php print $proj_id?>">
                        <?php
                            if ($displayData->money) {
                                print $displayData->currency.number_format($total, 2);
                            } else {
                                print $total; 
                            }
                        ?>
                    </span>
                </td>
            </tr>
            