<?php

use Joomla\CMS\Language\Text;
 defined('_JEXEC') or die('Restricted access'); ?>
<?php if ($displayData->me): ?>
<div class="toolbar" style="clear: both;">
    <button class="pull-right notcomplete" type="button" onclick="Timesheet.complete();"><?php print Text::_("COM_TIMECLOCK_TIMESHEET_DONE"); ?></button>
</div>
<?php endif; ?>