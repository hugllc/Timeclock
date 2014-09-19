<?php
    JHTML::script(Juri::base()."components/com_timeclock/views/payroll/tmpl/payroll.js");
    JHTML::script(Juri::base()."components/com_timeclock/js/timeclock.js");
    JHTML::_('behavior.modal'); 
    JHTML::_('behavior.calendar');
    $cols = ($this->payperiod->subtotals * 4) + 3;
    $this->payperiod->cols = $cols;

    JFactory::getDocument()->setTitle(
        JText::sprintf(
            "COM_TIMECLOCK_PAYROLL_TITLE",
            JHTML::_('date', $this->payperiod->start, JText::_("DATE_FORMAT_LC3")),
            JHTML::_('date', $this->payperiod->end, JText::_("DATE_FORMAT_LC3"))
        )
    );
    $totals = (object)array("payperiod" => $this->payperiod, "totals" => $this->totals);
?>
<div id="timeclock">
    <div class="page-header">
        <h2 itemprop="name">
            <a id="timeclocktop"></a>
            <?php print JText::_("COM_TIMECLOCK_PAYROLL"); ?>
             <span class="locked">(<?php print JText::_("COM_TIMECLOCK_PAYPERIOD_LOCKED"); ?>)</span>
        </h2>
    </div>
    <?php print $this->_toolbar->render($this->payperiod); ?>
    <?php print $this->_nextprev->render($this->payperiod); ?>
    <div class="dateheader">
        <strong>
            <?php print JText::sprintf(
                "COM_TIMECLOCK_DATE_TO_DATE",
                JHTML::_('date', $this->payperiod->start),
                JHTML::_('date', $this->payperiod->end)
                ); ?>
        </strong>
    </div>
    <div class="table-responsive">
        <table class="report table table-striped table-bordered table-hover table-condensed">
            <thead>
<?php print $this->_header->render($this->payperiod); ?>
            </thead>
            <tfoot>
<?php 
    print $this->_totals->render($totals); 
?>
            </tfoot>
            <tbody>
<?php 
    foreach ($this->users as $user_id => $user) {
        $user->payperiod = $this->payperiod;
        $user->data = isset($this->data[$user_id]) ? $this->data[$user_id] : array();
        $user->rowClass = "livedata";
        print $this->_row->render($user);
    }
    foreach ((array)$this->report->users as $user_id => $user) {
        $user = (object)$user;
        $user->payperiod = $this->payperiod;
        $user->data = isset($this->report->timesheets[$user_id]) ? $this->report->timesheets[$user_id] : array();
        $user->rowClass = "reportdata";
        print $this->_row->render($user);
    }
?>
            </tbody>
        </table>
    </div>
<form action="<?php JROUTE::_("index.php"); ?>" method="post" name="userform" class="payroll">
    <?php print JHTML::_("form.token"); ?>
</form>
<script type="text/JavaScript">
    jQuery( document ).ready(function() {
        Payroll.setup();
    });
    Payroll.subtotalcols = <?php print $this->payperiod->subtotals; ?>;
    Payroll.dates        = <?php print json_encode(array_keys($this->payperiod->dates)); ?>;
    Payroll.allprojs     = <?php print json_encode($allproj); ?>;
    Payroll.projects     = <?php print json_encode($this->projects); ?>;
    Payroll.payperiod    = <?php print json_encode($this->payperiod); ?>;
    Payroll.data         = <?php print json_encode($this->data); ?>;
    Payroll.report       = {
        "projects": <?php print json_encode($this->report->projects); ?>,
        "users": <?php print json_encode($this->report->users); ?>,
        "data": <?php print json_encode($this->report->timesheets); ?>,
    };
    Payroll.doreports = <?php print (int)($this->report->report_id != 0); ?>;
    Timeclock.params     = <?php print json_encode($this->params); ?>;
    

</script>
</div>