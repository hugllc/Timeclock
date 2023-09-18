<?php
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
?>
        <tr class="header">
            <th><?php print Text::_("COM_TIMECLOCK_PROJECT"); ?></th>
<?php foreach ($displayData as $user) : ?>
    <?php if ($user->hide) continue; ?>
    <?php $name  = empty($user->name) ? "User ".$user->user_id : $user->name;?>
            <th><?php print $name; ?></th>
<?php endforeach; ?>
            <th><?php print Text::_("COM_TIMECLOCK_TOTAL"); ?></th>
        </tr>
