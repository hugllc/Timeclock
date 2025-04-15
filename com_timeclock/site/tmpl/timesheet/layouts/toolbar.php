<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use HUGLLC\Component\Timeclock\Administrator\Helper\TimeclockHelper;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die(); 
$url = Route::_("index.php?option=com_timeclock&controller=timesheet&id=".$displayData->user->id."&date=".$displayData->payperiod->start);
$payroll = Route::_("index.php?option=com_timeclock&view=payroll&date=".$displayData->payperiod->start);
$approval = $displayData->me->authorise("timeclock.timesheet.approve.all", "com_timeclock") 
  || ($displayData->me->authorise("timeclock.timesheet.approve", "com_timeclock") && ((int)$displayData->user->timeclock["manager"] == (int)$displayData->me->id));
?>
<div class="toolbar" style="clear: both;">
<?php if ($displayData->user->me && !$displayData->payperiod->approved): ?>
    <button class="pull-right notcomplete" type="button" onclick="Timesheet.complete();" style="display: none;"><?php print Text::_("COM_TIMECLOCK_TIMESHEET_COMPLETE"); ?></button>
<?php elseif (!$displayData->user->me && $approval && !$displayData->payperiod->locked): ?>
    <?php if ($displayData->payperiod->approved): ?>
    <button class="pull-right approved" type="button" onclick="window.location.href='<?php print $url; ?>&task=disapprove';"><?php print Text::_("COM_TIMECLOCK_TIMESHEET_DISAPPROVE"); ?></button>
    <?php else: ?>
    <button class="pull-right notapproved" type="button" onclick="window.location.href='<?php print $url; ?>&task=approve';"><?php print Text::_("COM_TIMECLOCK_TIMESHEET_APPROVE"); ?></button>
    <?php endif; ?>
<?php endif; ?>
<?php if (!$displayData->user->me && TimeclockHelper::getActions()->get("timeclock.payroll")): ?>
    <button class="pull-right" type="button" onclick="window.location.href='<?php print $payroll; ?>';"><?php print Text::_("COM_TIMECLOCK_PAYROLL_REPORT"); ?></button>
<?php endif; ?>
</div>
