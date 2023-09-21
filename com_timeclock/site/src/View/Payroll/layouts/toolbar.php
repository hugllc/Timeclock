<?php 
use Joomla\CMS\Language\Text; 
$url = 'index.php?option=com_timeclock&controller=payroll&date='.$displayData->payperiod->start;
?>

<div class="toolbar" style="clear: both;">
    <?php if ($displayData->actions->get('timeclock.payroll.lock')): ?>
        <?php if ($displayData->payperiod->locked): ?>
            <button class="pull-right unlock locked" type="button" onclick="window.location.href='<?php echo $url."&task=unlock" ?>';">
                <?php print Text::_("COM_TIMECLOCK_UNLOCK_PAYPERIOD"); ?>
            </button>
        <?php else: ?>
            <button class="pull-right notlocked" type="button" onclick="window.location.href='<?php echo $url."&task=lock" ?>';">
                <?php print Text::_("COM_TIMECLOCK_LOCK_PAYPERIOD"); ?>
            </button>
        <?php endif; ?>
    <?php endif; ?>
</div>