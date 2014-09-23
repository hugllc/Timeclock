<?php
    JHTML::script(Juri::base()."components/com_timeclock/views/report/tmpl/report.js");
    JHTML::script(Juri::base()."components/com_timeclock/js/timeclock.js");
    JHTML::_('behavior.modal'); 
    JHTML::_('behavior.calendar');
    $cols = count($this->users) + 2;

    JFactory::getDocument()->setTitle(
        JText::sprintf(
            "COM_TIMECLOCK_REPORT_TITLE",
            JHTML::_('date', $this->start, JText::_("DATE_FORMAT_LC3")),
            JHTML::_('date', $this->end, JText::_("DATE_FORMAT_LC3"))
        )
    );
    $doreports = ($this->report->report_id != 0);
?>
<div id="timeclock">
<form action="<?php JROUTE::_("index.php?option=com_timeclock&controller=report"); ?>" method="get" name="userform" class="report">
    <div class="page-header">
        <h2 itemprop="name">
            <a id="timeclocktop"></a>
            <?php print JText::_("COM_TIMECLOCK_REPORT"); ?>
            <span class="livedata noreport">(<?php print JText::_("COM_TIMECLOCK_LIVE_DATA"); ?>)</span>
            <span class="reportdata noreport">(<?php print JText::_("COM_TIMECLOCK_SAVED_DATA"); ?>)</span>
        </h2>
    </div>
    <?php print $this->_toolbar->render($this); ?>
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
                "url" => JRoute::_('&option=com_timeclock&controller=report'),
                "export" => $this->export,
            )
        ); 
    ?>
    <div class="table-responsive">
        <table class="report table table-striped table-bordered table-hover table-condensed">
            <thead>
<?php print $this->_header->render($this->users); ?>
            </thead>
            <tfoot>
<?php 
    print $this->_totals->render(
        (object)array(
            "users" => $this->users, 
            "data" => $this->data["totals"], 
            "rowClass" => "livedata"
        )
    ); 
    if ($doreports) {
        print $this->_totals->render(
            (object)array(
                "users" => $this->report->users, 
                "data" => $this->report->timesheets["totals"], 
                "rowClass" => "reportdata"
            )
        );
    }
?>
            </tfoot>
            <tbody>
<?php 
    $allproj = array();
    foreach ($this->projects as $cat => $projects) {
        $render = "";
        $cnt    = 0;
        foreach ($projects["proj"] as $proj) {
            $cnt++;
            $proj_id = (int)$proj->project_id;
            $allproj[$proj_id] = $proj;
            $proj->data     = isset($this->data[$proj_id]) ? $this->data[$proj_id] : array();
            $proj->rowClass = "livedata";
            $proj->users    = $this->users;
            $render .= $this->_row->render($proj);
        }
        if ($cnt > 0) {
            print $this->_category->render(
                (object)array(
                    "cols" => $cols,
                    "id" => $cat,
                    "name" => $projects["name"],
                    "description" => $projects["description"],
                    "rowClass" => "livedata",
                )
            );
            print $render;
        }
    }
    if ($doreports) {
        foreach ((array)$this->report->projects as $proj_id => $proj) {
            $proj = (object)$proj;
            $proj->data = isset($this->report->timesheets[$proj_id]) ? $this->report->timesheets[$proj_id] : array();
            $proj->rowClass = "reportdata noreport";
            print $this->_row->render($proj);
        }
    }
?>
            </tbody>
        </table>
    </div>
    <?php print JHTML::_("form.token"); ?>
</form>
<script type="text/JavaScript">
    jQuery( document ).ready(function() {
        Report.setup();
    });
    Report.projects     = <?php print json_encode($this->projects); ?>;
    Report.data         = <?php print json_encode($this->data); ?>;
    Report.report       = {
        "projects": <?php print json_encode($this->report->projects); ?>,
        "users": <?php print json_encode($this->report->users); ?>,
        "data": <?php print json_encode($this->report->timesheets); ?>,
    };
    Report.doreports = <?php print (int)$doreports; ?>;
    Timeclock.params     = <?php print json_encode($this->params); ?>;
    Timeclock.report     = 0;
    

</script>
</div>