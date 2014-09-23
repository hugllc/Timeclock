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
<div class="row-fluid" style="clear: both;">
    <?php print $from->label; ?>
    <?php print $from->input; ?>
    <?php print $to->label; ?>
    <?php print $to->input; ?>
    <button type="submit">Submit</button>
</div>
