<?php
    defined( '_JEXEC' ) or die( 'Restricted access' );
    $sep = "";
    if (TimeclockHelpersContrib::phpexcel()):
?>
<div class="export">
    <iframe src="about:blank" style="display: none;"></iframe>
    <script type="text/javascript">
        function doReportExport (format)
        {
            var url = '<?php print $displayData->url; ?>';
            url = url+'&format='+format;
            url = url+'&report='+Timeclock.report;
            jQuery("#timeclock .export iframe").attr("src", url);
        }
    </script>
    <?php print JText::_("COM_TIMECLOCK_EXPORT_TO"); ?>:
    <?php foreach ($displayData->export as $name => $format) : ?>
        <span>
            <?php print $sep; $sep = " | "; ?>
            <a href="javascript:void(0)" onClick="doReportExport('<?php print $format; ?>');"><?php print $name; ?></a>
        </span>
    <?php endforeach; ?>
</div>
<?php endif; ?>