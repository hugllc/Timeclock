<?php

use Joomla\CMS\Language\Text;
 defined('_JEXEC') or die(); ?>
            <tr class="subtotal">
                <th colspan="<?php print ($displayData->cols - 1); ?>">
                    <?php print Text::_("COM_TIMECLOCK_TOTAL"); ?>
                </th>
                <td class="total">
                    <span class="grandtotal">-</span>
                </td>
            </tr>
