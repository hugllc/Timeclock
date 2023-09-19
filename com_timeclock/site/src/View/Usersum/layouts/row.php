<?php 
defined('_JEXEC') or die();
if (!$displayData->hide) :   
    $user_id = $displayData->user_id;
    $total   = isset($displayData->data["total"]) ? $displayData->data["total"] : "0";
    $places  = $displayData->genparams->get("decimalPlaces");
    $zero    = '<span class="zero">0</span>';
?>
            <tr class="project<?php print ($total == 0) ? " empty" : ""; ?>">
                <td>
                    <?php print $displayData->name ?>
                </td>
<?php 
    foreach ($displayData->projects as $projects) :
        foreach ($projects["proj"] as $proj) :
            $proj_id = (int)$proj->project_id; 
            if (isset($displayData->data[$proj_id])) :
                $hours = (float)$displayData->data[$proj_id];
                if ($total != 0) {
                    $perc = round(($hours / $total) * 100, $places)."%";
                } else {
                    $prec = $zero;
                }
            else :
                $hours = $zero;
                $perc  = $zero;
            endif;
?>
                <td class="hours"><?php print $hours; ?></td>
                <td class="percent"><?php print $perc; ?></td>
                </td>
    <?php endforeach; ?>
<?php endforeach; ?>
                <td class="total">
                    <span id="total-<?php print $user_id?>">
                        <?php print $total; ?>
                    </span>
                </td>
            </tr>
<?php endif; ?>