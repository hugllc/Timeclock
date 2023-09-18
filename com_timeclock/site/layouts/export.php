<?php

use Joomla\CMS\Language\Text;
use HUGLLC\Component\Timeclock\Site\Helper\ContribHelper;

    defined( '_JEXEC' ) or die( 'Restricted access' );
    $sep = "";
    if (ContribHelper::phpspreadsheet()):
        $url = $displayData->url;
?>
<div class="export">
    <iframe src="about:blank" style="display: none;"></iframe>
    <script type="text/javascript">
        function doReportExport (format)
        {
            var url = '<?php print $displayData->url; ?>';
            // This gets all of our filter variables.
            var data = {
                format,
                report: Timeclock.report,
            };
            // Add in the report value
            url = url + "&" + jQuery.param(data);
            console.log(url);
            jQuery("#timeclock .export iframe").attr("src", url);
        }
    </script>
    <?php print Text::_("COM_TIMECLOCK_EXPORT_TO"); ?>:
    <?php foreach ($displayData->export as $name => $format) : ?>
        <span>
            <?php print $sep; $sep = " | "; ?>
            <a href="javascript:void(0)" onClick="doReportExport('<?php print $format; ?>');"><?php print $name; ?></a>
        </span>
    <?php endforeach; ?>
</div>
<?php endif; ?>