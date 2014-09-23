<div class="toolbar" style="clear: both;">
    <button class="pull-right livedata" type="button" onclick="Report.save();"><?php print JText::_("COM_TIMECLOCK_SAVE_REPORT"); ?></button>
    <button class="pull-left livedata noreport" type="button" onclick="Report.setReport(false);"><?php print JText::_("COM_TIMECLOCK_SAVED_DATA"); ?></button>
    <button class="pull-left reportdata noreport" type="button" onclick="Report.setReport(true);"><?php print JText::_("COM_TIMECLOCK_LIVE_DATA"); ?></button>
</div>