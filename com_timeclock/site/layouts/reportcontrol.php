<?php
    defined( '_JEXEC' ) or die( 'Restricted access' );
    $form = JForm::getInstance(
        'reportcontrol', 
        JPATH_ROOT."/components/com_timeclock/forms/reportcontrol.xml"
    );
    $from = $form->getField("start");
    $from->setValue($displayData->start);
    $to = $form->getField("end");
    $to->setValue($displayData->end);
?>
<div class="reportcontrol row">
    <div class="row-fluid">
        <div class="span4"><?php print $from->input; ?></div>
        <div class="span1"><?php print JText::_("COM_TIMECLOCK_TO"); ?></div>
        <div class="span4"><?php print $to->input; ?></div>
    </div>
    <button type="submit">Submit</button>
</div>
