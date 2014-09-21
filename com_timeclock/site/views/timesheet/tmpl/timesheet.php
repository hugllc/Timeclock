<?php
    JHTML::script(Juri::base()."components/com_timeclock/views/timesheet/tmpl/timesheet.js");
    JHTML::script(Juri::base()."components/com_timeclock/views/timesheet/tmpl/addhours.js");
    JHTML::script(Juri::base()."components/com_timeclock/js/timeclock.js");
    JHTML::_('behavior.modal'); 
    JHTML::_('behavior.calendar');
    $cols = $this->payperiod->days + 2 + $this->payperiod->subtotals;
    $this->payperiod->cols = $cols;

    JFactory::getDocument()->setTitle(
        JText::sprintf(
            "COM_TIMECLOCK_TIMESHEET_TITLE",
            $this->user->name,
            JHTML::_('date', $this->payperiod->start, JText::_("DATE_FORMAT_LC3")),
            JHTML::_('date', $this->payperiod->end, JText::_("DATE_FORMAT_LC3"))
        )
    );
    
?>
<div id="timeclock">
    <div class="page-header">
        <h2 itemprop="name">
            <a id="timeclocktop"></a>
            <?php print JText::sprintf("COM_TIMECLOCK_TIMESHEET_FOR", $this->user->name);?>
            <span class="complete">(<?php print JText::_("COM_TIMECLOCK_COMPLETE"); ?>)</span>
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
    <div class="paid">
        <table class="paid timesheet">
            <thead>
<?php 
    print $this->_name->render(
        (object)array(
            "cols" => $this->payperiod->cols,
            "name" => "COM_TIMECLOCK_PAID_TIME",
        )
    );
    print $this->_header->render($this->payperiod); 
?>
            </thead>
            <tfoot>
<?php print $this->_header->render($this->payperiod);  ?>
<?php print $this->_subtotals->render($this->payperiod); ?>
<?php print $this->_psubtotals->render($this->payperiod); ?>
<?php print $this->_totals->render($this->payperiod); ?>
            </tfoot>
            <tbody>
<?php 
    $paid = 0;
    $allproj = array();
    foreach ($this->projects as $cat => $projects) {
        $render = "";
        $cnt    = 0;
        foreach ($projects["proj"] as $proj) {
            if ($proj->type == "UNPAID") {
                continue;
            }
            $cnt++;
            $allproj[$proj->project_id] = $proj->project_id;
            $proj->payperiod = $this->payperiod;
            $proj->data      = isset($this->data[$proj->project_id]) ? $this->data[$proj->project_id] : array();
            $render .= $this->_row->render($proj);
        }
        if ($cnt > 0) {
            print $this->_category->render(
                array(
                    "cols" => $cols,
                    "id" => $cat,
                    "name" => $projects["name"],
                    "description" => $projects["description"],
                )
            );
            print $render;
        }
        $paid += $cnt;
    }
?>
            </tbody>
        </table>
    </div>
    <div class="volunteer">
        <table class="volunteer timesheet">
            <thead>
<?php 
    print $this->_name->render(
        (object)array(
            "cols" => $this->payperiod->cols,
            "name" => "COM_TIMECLOCK_VOLUNTEER_TIME",
        )
    );
    print $this->_header->render($this->payperiod); 
?>
            </thead>
            <tfoot>
<?php print $this->_header->render($this->payperiod);  ?>
<?php print $this->_subtotals->render($this->payperiod); ?>
<?php print $this->_psubtotals->render($this->payperiod); ?>
<?php print $this->_totals->render($this->payperiod); ?>
            </tfoot>
            <tbody>
<?php 
    $unpaid = 0;
    foreach ($this->projects as $cat => $projects) {
        $cnt = 0;
        $render = "";
        foreach ($projects["proj"] as $proj) {
            if ($proj->type != "UNPAID") {
                continue;
            }
            $cnt++;
            $allproj[$proj->project_id] = $proj->project_id;
            $proj->payperiod = $this->payperiod;
            $proj->data      = isset($this->data[$proj->project_id]) ? $this->data[$proj->project_id] : array();
            $render .= $this->_row->render($proj);
        }
        if ($cnt > 0) {
            print $this->_category->render(
                array(
                    "cols" => $cols,
                    "id" => $cat,
                    "name" => $projects["name"],
                    "description" => $projects["description"],
                )
            );
            print $render;
        }
        $unpaid += $cnt;
    }
?>
            </tbody>
        </table>
    </div>
    <form action="<?php JROUTE::_("index.php"); ?>" method="post" class="timesheet">
        <?php print JHTML::_("form.token"); ?>
    </form>
    <script type="text/JavaScript">
        jQuery( document ).ready(function() {
            Timesheet.setup();
            Addhours.setup();
        });
        Timesheet.subtotalcols = <?php print $this->payperiod->subtotals; ?>;
        Timesheet.dates        = <?php print json_encode(array_keys($this->payperiod->dates)); ?>;
        Timesheet.allprojs     = <?php print json_encode($allproj); ?>;
        Timesheet.projects     = <?php print json_encode($this->projects); ?>;
        Timesheet.payperiod    = <?php print json_encode($this->payperiod); ?>;
        Timesheet.data         = <?php print json_encode($this->data); ?>;
        Timesheet.paid         = <?php print $paid; ?>;
        Timesheet.volunteer    = <?php print $unpaid; ?>;
        Timeclock.params       = <?php print json_encode($this->params); ?>;

    </script>
</div>
