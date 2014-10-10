<?php
defined('_JEXEC') or die('Restricted access');
$cnt = 0;
?>
        <tr class="header">
            <th><?php print JText::_("COM_TIMECLOCK_USER"); ?></th>
<?php foreach ($displayData as $col) : ?>
            <th><?php print JText::_($col); ?></th>
<?php endforeach; ?>
            <th class="hasTooltip" title="<?php print JText::_("COM_TIMECLOCK_PTO_CURRENT_DESC"); ?>">
                <?php print JText::_("COM_TIMECLOCK_PTO_CURRENT"); ?>
            </th>
        </tr>
