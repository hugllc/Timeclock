<?php
    JHTML::script(Juri::base()."components/com_timeclock/js/report.js");
    JHTML::script(Juri::base()."components/com_timeclock/js/timeclock.js");
    JHTML::_('behavior.modal'); 
    JHTML::_('behavior.calendar');
    JHtml::_('formbehavior.chosen', 'select:not(.plain)');
    $cols = count($this->users) + 2;

    JFactory::getDocument()->setTitle(
        JText::sprintf(
            "COM_TIMECLOCK_BILLING_REPORT_TITLE",
            JHTML::_('date', $this->start, JText::_("DATE_FORMAT_LC3")),
            JHTML::_('date', $this->end, JText::_("DATE_FORMAT_LC3"))
        )
    );
    $doreports = ($this->report_id != 0);
?>
<div id="timeclock" class="container-fluid">
<form action="<?php JROUTE::_("index.php?option=com_timeclock&controller=billing"); ?>" method="post" name="userform" class="report">
    <div class="page-header row">
        <h2 itemprop="name">
            <a id="timeclocktop"></a>
            <?php print JText::_("COM_TIMECLOCK_BILLING_REPORT"); ?>
            <?php print ($doreports) ? " - ".JText::_("COM_TIMECLOCK_SAVED_DATA").":  ".$this->report->name : ""; ?>
        </h2>
    </div>
    <?php print $this->_control->render($this->filter); ?>
    <div class="dateheader">
        <strong>
            <?php print JText::sprintf(
                "COM_TIMECLOCK_DATE_TO_DATE",
                JHTML::_('date', $this->start),
                JHTML::_('date', $this->end)
                ); ?>
        </strong>
    </div>
    <?php 
        print $this->_export->render(
            (object)array(
                "url" => JRoute::_('&option=com_timeclock&controller=billing'),
                "export" => $this->export,
            )
        ); 
    ?>
    <div class="table-responsive">
        <table class="report table table-striped table-bordered table-hover table-condensed">
            <thead>
<?php print $this->_header->render($this->data); ?>
            </thead>
            <tfoot>
<?php 
    print $this->_totals->render(
        (object)array(
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
        print $this->_row->render($user);
    }
?>
            </tbody>
        </table>
    </div>
    <input type="hidden" name="controller" value="billing" />
    <?php print JHTML::_("form.token"); ?>
</form>
<script type="text/JavaScript">
    jQuery( document ).ready(function() {
        Report.setup();
    });
    Report.filter    = <?php print json_encode($this->filter); ?>;
    Report.projects  = <?php print json_encode($this->projects); ?>;
    Report.data      = <?php print json_encode($this->data); ?>;
    Report.doreports = <?php print (int)$doreports; ?>;
    Timeclock.params = <?php print json_encode($this->params); ?>;
    Timeclock.report = 0;
    

</script>
</div>