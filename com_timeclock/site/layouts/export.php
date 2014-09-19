<?php
    defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<div class="export">
    <?php print JText::_("COM_TIMECLOCK_EXPORT_TO"); ?>:
    <?php foreach ($displayData->export as $name => $format) : ?>
        <span>
            <a href="<?php print $displayData->url; ?>&format=<?php print $format; ?>"><?php print $name; ?></a>
        </span>
    <?php endforeach; ?>
</div>
