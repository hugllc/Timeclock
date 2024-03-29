<?php
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
?>
<div id="notes-<?php print $displayData->user_id; ?>" class="notes">
<?php if (empty($displayData->data)) : ?>
        <div class="nonotes">
            <?php print Text::_("COM_TIMECLOCK_NO_NOTES_FOUND"); ?>
        </div>
<?php else : ?>
<?php foreach ($displayData->data as $proj_id => $proj) : ?>
    <h3><?php print $proj["project_name"]; ?></h3>
    <?php foreach ($proj["worked"] as $date => $row) : ?>
        <div class="noteheader"><?php print HTMLHelper::_("date", $date); ?> (<?php print round($row->hours, $displayData->decimals); ?> <?php print Text::_("COM_TIMECLOCK_HOURS"); ?>)</div>
        <div class="note"><?php print $row->notes; ?></div>
    <?php endforeach; ?>
<?php endforeach; ?>
<?php endif; ?>
</div>
