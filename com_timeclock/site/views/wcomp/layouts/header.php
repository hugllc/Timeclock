<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
$cnt = 0;
?>
        <tr class="header">
            <th><?php print Text::_("COM_TIMECLOCK_USER"); ?></th>
<?php foreach ($displayData as $code) : ?>
        <?php if (!is_numeric($code)) continue; ?>
            <th><?php print $code; ?></th>
<?php endforeach; ?>
            <th><?php print Text::_("COM_TIMECLOCK_TOTAL"); ?></th>
        </tr>
