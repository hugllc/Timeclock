<?php 
defined('_JEXEC') or die('Restricted access'); 
?>
<h2 itemprop="name"><?php print JText::_("COM_TIMECLOCK_SAVE_REPORT"); ?></h2>
<hr />
<div class="alert element-invisible"></div>
<div class="form-horizontal">
    <div class="control-group">
        <div class="control-label">
            <label for="report_name" class="hasTooltip" title="<?php print JText::_("COM_TIMECLOCK_REPORT_NAME_DESC"); ?>">
                <?php print JText::_("COM_TIMECLOCK_NAME"); ?>
            </label>
        </div>
        <div class="controls">
            <input type="text" name="report_name" value="" />
        </div>
    </div>
    <div class="control-group">
        <div class="control-label">
            <label for="report_description" class="hasTooltip" title="<?php print JText::_("COM_TIMECLOCK_REPORT_DESCRIPTION_DESC"); ?>">
                <?php print JText::_("COM_TIMECLOCK_DESCRIPTION"); ?>
            </label>
        </div>
        <div class="controls">
            <textarea name="report_description"></textarea>
        </div>
    </div>
    <div class="controls">
        <button class="submit" onClick="Report.submit();"><?php print JText::_("JSUBMIT"); ?></button>
    </div>
</div>
