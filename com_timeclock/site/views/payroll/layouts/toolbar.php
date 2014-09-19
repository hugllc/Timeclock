<div class="toolbar" style="clear: both;">
    <button class="pull-right" type="button" onclick="Payroll.save();">Save Report</button>
    <button class="pull-right notlocked" type="button" onclick="Payroll.lock();">Lock Payperiod</button>
    <button class="pull-right unlock locked" type="button" onclick="Payroll.unlock();">Unlock Payperiod</button>
    <button class="pull-right livedata noreport" type="button" onclick="Payroll.setReport(false);"><?php print JText::_("COM_TIMECLOCK_SAVED_DATA"); ?></button>
    <button class="pull-right reportdata noreport" type="button" onclick="Payroll.setReport(true);"><?php print JText::_("COM_TIMECLOCK_LIVE_DATA"); ?></button>
</div>