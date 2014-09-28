<div class="toolbar" style="clear: both;">
    <button class="pull-right hasreport" type="button" onclick="Payroll.save();"><?php print JText::_("COM_TIMECLOCK_SAVE_REPORT"); ?></button>
    <button class="pull-right notlocked" type="button" onclick="Payroll.lock();"><?php print JText::_("COM_TIMECLOCK_LOCK_PAYPERIOD"); ?></button>
    <button class="pull-right unlock locked" type="button" onclick="Payroll.unlock();"><?php print JText::_("COM_TIMECLOCK_UNLOCK_PAYPERIOD"); ?></button>
    <button class="pull-left livedata noreport" type="button" onclick="Payroll.setReport(false);"><?php print JText::_("COM_TIMECLOCK_SAVED_DATA"); ?></button>
    <button class="pull-left reportdata noreport" type="button" onclick="Payroll.setReport(true);"><?php print JText::_("COM_TIMECLOCK_LIVE_DATA"); ?></button>
</div>