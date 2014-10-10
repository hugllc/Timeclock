<?php
defined('_JEXEC') or die('Restricted access');
$cnt = 0;
?>
        <tr class="header">
            <th><?php print JText::_("COM_TIMECLOCK_USER"); ?></th>
<?php foreach ($displayData as $col) : ?>
            <th><?php print JText::_($col); ?></th>
<?php endforeach; ?>
        </tr>
