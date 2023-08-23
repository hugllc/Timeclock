<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
$cnt = 0;
?>
        <tr class="header">
            <th><?php print Text::_("COM_TIMECLOCK_USER"); ?></th>
<?php foreach ($displayData as $col) : ?>
            <th><?php print Text::_($col); ?></th>
<?php endforeach; ?>
            <th class="hasTooltip" title="<?php print Text::_("COM_TIMECLOCK_PTO_CURRENT_DESC"); ?>">
                <?php print Text::_("COM_TIMECLOCK_PTO_CURRENT"); ?>
            </th>
        </tr>
