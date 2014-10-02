<?php
defined('_JEXEC') or die('Restricted access');
$cnt = 0;
?>
        <tr class="header">
            <th><?php print JText::_("COM_TIMECLOCK_USER"); ?></th>
<?php foreach ($displayData as $code) : ?>
        <?php if (!is_numeric($code)) continue; ?>
            <th><?php print $code; ?></th>
<?php endforeach; ?>
            <th><?php print JText::_("COM_TIMECLOCK_TOTAL"); ?></th>
        </tr>
