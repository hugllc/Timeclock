<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\FileLayout;

$header   = new FileLayout('header', __DIR__.'/layouts');
$row      = new FileLayout('row', __DIR__.'/layouts');
$totals   = new FileLayout('totals', __DIR__.'/layouts');
$category = new FileLayout('category', __DIR__.'/layouts');
$toolbar  = new FileLayout('toolbar', __DIR__.'/layouts');
$export   = new FileLayout('export', dirname(__DIR__).'/layouts');
$control  = new FileLayout('reportcontrol', dirname(__DIR__).'/layouts');

HTMLHelper::_("jquery.framework");
HTMLHelper::script(Uri::base()."components/com_timeclock/js/report.js");
HTMLHelper::script(Uri::base()."components/com_timeclock/js/timeclock.js");

$cols = count($this->users) + 2;

Factory::getDocument()->setTitle(
    Text::sprintf(
        "COM_TIMECLOCK_YTD_REPORT_TITLE",
        HTMLHelper::_('date', $this->start, Text::_("DATE_FORMAT_LC3")),
        HTMLHelper::_('date', $this->end, Text::_("DATE_FORMAT_LC3"))
    )
);

?>
<div id="timeclock" class="container-fluid">
<form action="<?php Route::_("index.php?controller=ytd"); ?>" method="post" name="userform" class="report">
    <div class="page-header row">
        <h2 itemprop="name">
            <a id="timeclocktop"></a>
            <?php print Text::_("COM_TIMECLOCK_YTD_REPORT"); ?>
        </h2>
    </div>
    <?php print $control->render($this->filter); ?>
    <div class="dateheader">
        <strong>
            <?php print Text::sprintf(
                "COM_TIMECLOCK_DATE_TO_DATE",
                HTMLHelper::_('date', $this->start),
                HTMLHelper::_('date', $this->end)
                ); ?>
        </strong>
    </div>
    <?php 
        print $export->render(
            (object)array(
                "url" => Route::_('&option=com_timeclock&controller=ytd'),
                "export" => $this->export,
            )
        ); 
    ?>
    <div class="table-responsive">
        <table class="report table table-striped table-bordered table-hover table-condensed">
            <thead>
<?php print $header->render($this->data["cols"]); ?>
            </thead>
            <tfoot>
<?php 
    print $totals->render(
        (object)array(
            "cols" => $this->data["cols"], 
            "data" => $this->data["totals"], 
            "params" => $this->params,
        )
    ); 
?>
            </tfoot>
            <tbody>
<?php 
    foreach ($this->users as $user) {
        $user_id = (int)$user->id;
        $user->data      = isset($this->data[$user_id]) ? $this->data[$user_id] : array();
        $user->genparams = $this->params;
        $user->cols = $this->data["cols"];
        print $row->render($user);
    }
?>
            </tbody>
        </table>
    </div>
    <input type="hidden" name="controller" value="ytd" />
    <?php print HTMLHelper::_("form.token"); ?>
</form>
<script type="text/JavaScript">
    jQuery( document ).ready(function() {
        Report.setup();
    });
    Report.filter    = <?php print json_encode($this->filter); ?>;
    Report.projects  = <?php print json_encode($this->projects); ?>;
    Report.data      = <?php print json_encode($this->data); ?>;
    Timeclock.params = <?php print json_encode($this->params); ?>;
    Timeclock.report = 0;
    

</script>
</div>