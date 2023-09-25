<?php 
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
if (!$displayData->hide) :
    $user_id = $displayData->id;
    $total   = isset($displayData->data["total"]) ? $displayData->data["total"] : "0";
    $places  = $displayData->genparams->get("decimalPlaces");
    $zero    = '<span class="zero">0</span>';
?>
            <tr class="project<?php print ($total == 0) ? " empty" : ""; ?>">
                <td>
                    <?php print $displayData->name ?>
                </td>
               
<?php 
    foreach (array_keys($displayData->cols) as $col) :
        if (isset($displayData->data[$col]) && ($displayData->data[$col] != 0)) :
            $hours = (float)$displayData->data[$col];
        else :
            $hours = $zero;
        endif;
        if ($col == "total") :
            $class = "total";
        else :
            $class = "hours";
        endif;
?>
                <td class="<?php print $class; ?>"><?php print $hours; ?></td>
<?php endforeach; ?>
                <td class="total hasTooltip" title="<?php print Text::_("COM_TIMECLOCK_PTO_CURRENT_DESC"); ?>">
                    <?php print isset($displayData->data["PTO_CURRENT"]) ? $displayData->data["PTO_CURRENT"] : "0"; ?>
                </td>
            </tr>
<?php endif; ?>
