<?php

use Joomla\CMS\Language\Text;
 
    defined('_JEXEC') or die('Restricted access');
    if ($displayData->splitdays != 0) : 
        $colspan = array();
        $days = count($displayData->dates);
        for ($d = 1; $d <= $displayData->subtotals; $d++) {
            if (($d * $displayData->splitdays) > $days) {
                // The last one may be short
                $colspan[$d] = $days % $displayData->splitdays;
            } else {
                // Most of them are just going to be the number of split days
                $colspan[$d] = $displayData->splitdays;
            }
        }
        // This accounts for the projects column
        $colspan[1]++;
?>
            <tr class="subtotal">
<?php
    $d = 0;
    foreach ($colspan as $key => $span): 
?>
                <th colspan="<?php print $span; ?>">
                    <?php print ($key == 1) ? Text::_("COM_TIMECLOCK_PERIODIC_SUBTOTALS") : "&nbsp;" ?>
                </th>
                <td class="subtotal">
                    <span class="psubtotal-proj-<?php print $key; ?>">-</span>
                </td>
<?php endforeach;?>
                <th>
                    &nbsp;
                </th>
            </tr>
<?php endif; ?>
