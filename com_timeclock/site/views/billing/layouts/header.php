<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
$cnt = 0;
?>
        <tr class="header">
            <th><?php print Text::_("COM_TIMECLOCK_USER"); ?></th>
            <th><?php print Text::_("COM_TIMECLOCK_HOURS"); ?></th>
            <th><?php print Text::_("COM_TIMECLOCK_BILLABLE_RATE"); ?></th>
            <th><?php print Text::_("COM_TIMECLOCK_TOTAL_COST"); ?></th>
        </tr>
