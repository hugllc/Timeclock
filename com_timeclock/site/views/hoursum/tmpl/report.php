<?php
    JHTML::script(Juri::base()."components/com_timeclock/js/report.js");
    JHTML::script(Juri::base()."components/com_timeclock/js/timeclock.js");
    JHTML::_('behavior.modal'); 
    JHTML::_('behavior.calendar');
    JHtml::_('formbehavior.chosen', 'select:not(.plain)');
    $cols = count($this->users) + 2;

    JFactory::getDocument()->setTitle(
        JText::sprintf(
            "COM_TIMECLOCK_USERSUM_REPORT_TITLE",
            JHTML::_('date', $this->start, JText::_("DATE_FORMAT_LC3")),
            JHTML::_('date', $this->end, JText::_("DATE_FORMAT_LC3"))
        )
    );
    $doreports = ($this->report_id != 0);
?>
<div id="timeclock" class="container-fluid">
<form action="<?php JROUTE::_("index.php?option=com_timeclock&controller=hoursum"); ?>" method="post" name="userform" class="report">
    <div class="page-header row">
        <h2 itemprop="name">
            <a id="timeclocktop"></a>
            <?php print JText::_("COM_TIMECLOCK_HOURSUM_REPORT"); ?>
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
    <?php /*
        print $this->_export->render(
            (object)array(
                "url" => JRoute::_('&option=com_timeclock&controller=hoursum'),
                "export" => $this->export,
            )
        ); */
    ?>
    <div class="container-fluid">
    <?php
        print $this->_proj_manager->render(
            (object)array(
                "users"  => $this->users,
                "data"   => $this->data,
                "params" => $this->params,
            )
        );
    ?>
    </div>
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