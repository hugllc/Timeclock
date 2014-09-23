<?php
    defined( '_JEXEC' ) or die( 'Restricted access' );
    $form = JForm::getInstance(
        'reportcontrol', 
        JPATH_COMPONENT."/forms/reportcontrol.xml"
    );
    $from = $displayData->form->getField("from");
    $from->setValue($displayData->startDate);
    $to = $displayData->form->getField("to");
    $to->setValue($displayData->startDate);
?>
<div class="row-fluid">
    <span class="span3"><?php print $from->label; ?></span>
    <span class="span3"><?php print $from->input; ?></span>
    <span class="span3"><?php print $to->label; ?></span>
    <span class="span3"><?php print $to->input; ?></span>
    
</div>
