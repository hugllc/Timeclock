<div id="notes-<?php print $displayData->user_id; ?>" class="notes">
    <h2 class="name"><?php print $displayData->name; ?></h2>
<?php if (empty($displayData->data)) : ?>
        <div class="nonotes">
            <?php print JText::_("COM_TIMECLOCK_NO_NOTES_FOUND"); ?>
        </div>
<?php else : ?>
<?php foreach ($displayData->data as $proj_id => $proj) : ?>
    <h3><?php print $proj["project_name"]; ?></h3>
    <?php foreach ($proj["worked"] as $date => $row) : ?>
        <div class="noteheader"><?php print JHtml::_("date", $date); ?> (<?php print $row->hours; ?> <?php print JText::_("COM_TIMECLOCK_HOURS"); ?>)</div>
        <div class="note"><?php print $row->notes; ?></div>
    <?php endforeach; ?>
<?php endforeach; ?>
<?php endif; ?>
</div>
