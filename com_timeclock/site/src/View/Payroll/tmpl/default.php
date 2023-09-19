<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

    HtmlHelper::script(Uri::base()."components/com_timeclock/js/report.js");
    HtmlHelper::script(Uri::base()."components/com_timeclock/src/View/Payroll/tmpl/payroll.js");
    HtmlHelper::script(Uri::base()."components/com_timeclock/js/timeclock.js");
    $cols = ($this->payperiod->subtotals * 4) + 3;
    $this->payperiod->cols = $cols;

    Factory::getDocument()->setTitle(
        Text::sprintf(
            "COM_TIMECLOCK_PAYROLL_TITLE",
            HtmlHelper::_('date', $this->payperiod->start, Text::_("DATE_FORMAT_LC3")),
            HtmlHelper::_('date', $this->payperiod->end, Text::_("DATE_FORMAT_LC3"))
        )
    );
?>
<div id="timeclock">
    <div class="page-header">
        <h2 itemprop="name">
            <a id="timeclocktop"></a>
            <?php print Text::_("COM_TIMECLOCK_PAYROLL"); ?>
            <span class="locked hasTooltip" title="<?php print Text::_("COM_TIMECLOCK_PAYPERIOD_LOCKED"); ?>" style="display: none;"><?php print HTMLHelper::_('image', 'system/checked_out.png', null, null, true); ?></span>
            <span class="livedata noreport" style="display: none;">(<?php print Text::_("COM_TIMECLOCK_LIVE_DATA"); ?>)</span>
            <span class="reportdata noreport" style="display: none;">(<?php print Text::_("COM_TIMECLOCK_SAVED_DATA"); ?>)</span>
        </h2>
    </div>
    <?php print $this->_toolbar->render($this->payperiod); ?>
    <?php print $this->_nextprev->render($this->payperiod); ?>
    <div class="dateheader">
        <strong>
            <?php print Text::sprintf(
                "COM_TIMECLOCK_DATE_TO_DATE",
                HtmlHelper::_('date', $this->payperiod->start),
                HtmlHelper::_('date', $this->payperiod->end)
                ); ?>
        </strong>
    </div>
    <?php 
        print $this->_export->render(
            (object)array(
                "url" => 'index.php?option=com_timeclock',
                "export" => $this->export,
            )
        ); 
    ?>
    <div class="table-responsive">
        <table class="report table table-striped table-bordered table-hover table-condensed">
            <thead>
<?php print $this->_header->render($this->payperiod); ?>
            </thead>
            <tfoot>
<?php 
    print $this->_totals->render(
        (object)array(
            "payperiod" => $this->payperiod, 
            "totals" => $this->data["totals"], 
            "rowClass" => "livedata"
        )
    ); 
?>
            </tfoot>
            <tbody>
<?php 
    foreach ($this->users as $user_id => $user) {
        $user->payperiod = $this->payperiod;
        $user->data = isset($this->data[$user_id]) ? $this->data[$user_id] : array();
        $user->rowClass = "livedata";
        $user->notes = isset($this->data["notes"][$user_id]) ? $this->data["notes"][$user_id] : array();
        print $this->_row->render($user);
    }
?>
            </tbody>
        </table>
    </div>
<form action="<?php Route::_("index.php"); ?>" method="post" name="userform" class="timeclock_report">
    <input type="hidden" name="controller" value="payroll" />
    <input type="hidden" name="view" value="payroll" />
    <?php print HTMLHelper::_("form.token"); ?>
</form>
<script type="text/JavaScript">
    jQuery( document ).ready(function() {
        Payroll.setup();
    });
    Report.filter    = { start: "<?php print $this->payperiod->start; ?>" };
    Report.projects  = <?php print json_encode($this->projects); ?>;
    Report.data      = <?php print json_encode($this->data); ?>;
    Report.doreports = <?php print (int)$doreports; ?>;
    Payroll.subtotalcols = <?php print $this->payperiod->subtotals; ?>;
    Payroll.dates        = <?php print json_encode(array_keys($this->payperiod->dates)); ?>;
    Payroll.projects     = <?php print json_encode($this->projects); ?>;
    Payroll.payperiod    = <?php print json_encode($this->payperiod); ?>;
    Payroll.data         = <?php print json_encode($this->data); ?>;
    Payroll.doreports = <?php print (int)$doreports; ?>;
    Timeclock.params     = <?php print json_encode($this->params); ?>;
    Timeclock.report     = 0;
    

</script>
<?php 
    /*
    foreach ($this->users as $user_id => $user) {
        $user->payperiod = $this->payperiod;
        $user->data = isset($this->data["notes"][$user_id]) ? $this->data["notes"][$user_id] : array();
        print $this->_notes->render($user);
    }
    */
?>
</div>