<?php

use Joomla\CMS\Language\Text;
 defined('_JEXEC') or die('Restricted access'); ?>
            <tr class="subtotal">
                <th>
                    <?php print Text::_("COM_TIMECLOCK_SUBTOTALS"); ?>
                </th>
<?php
    $d = 0;
    foreach ($displayData->dates as $date => $timeentry): 
        $tooltip = Text::_("COM_TIMECLOCK_SUBTOTAL")." for ".JHTML::_('date', $date, Text::_("DATE_FORMAT_LC1"));
?>
                <td class="subtotal">
                    <span class="subtotal-<?php print $date; ?> hasTooltip" title="<?php print $tooltip; ?>">-</span>
                </td>
    <?php if (($displayData->splitdays != 0) && ((++$d % $displayData->splitdays) == 0)) : ?>
                <th>
                    &nbsp;
                </th>
    <?php endif; ?>
<?php endforeach;?>
                <th>
                    &nbsp;
                </th>
            </tr>
            