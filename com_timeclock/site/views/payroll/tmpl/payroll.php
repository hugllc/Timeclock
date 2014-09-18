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
<form action="<?php JROUTE::_("index.php"); ?>" method="post" name="userform" autocomplete="off">
    <div class="page-header">
        <h2 itemprop="name">
            <a id="timeclocktop"><?php print JText::sprintf("COM_TIMECLOCK_PAYROLL", $user->name);?></a>
        </h2>
    </div>
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
    <div class="">
        <table class="timesheet">
            <thead>
<?php print $this->_header->render($this->payperiod); ?>
            </thead>
            <tfoot>
<?php print $this->_totals->render($totals); ?>
            </tfoot>
            <tbody>
<?php 
    $allproj = array();
    foreach ($this->users as $user_id => $user) {
        $user->payperiod = $this->payperiod;
        $user->data = $this->data[$user_id];
        print $this->_row->render($user);
    }
?>
            </tbody>
        </table>
    </div>
</form>
<script type="text/JavaScript">
    jQuery( document ).ready(function() {
        //Payroll.setup();
    });
    Payroll.subtotalcols = <?php print $this->payperiod->subtotals; ?>;
    Payroll.dates        = <?php print json_encode(array_keys($this->payperiod->dates)); ?>;
    Payroll.allprojs     = <?php print json_encode($allproj); ?>;
    Payroll.projects     = <?php print json_encode($this->projects); ?>;
    Payroll.payperiod    = <?php print json_encode($this->payperiod); ?>;
    Payroll.data         = <?php print json_encode($this->data); ?>;
    Timeclock.params     = <?php print json_encode($this->params); ?>

</script>
</div>