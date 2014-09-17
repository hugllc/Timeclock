<?php
    JHTML::script(Juri::base()."components/com_timeclock/views/timesheet/tmpl/addhours.js");
    JHTML::script(Juri::base()."components/com_timeclock/js/timeclock.js");

    $user = JFactory::getUser();
    $subtotalcols = (int)($this->payperiod->days / $this->payperiod->splitdays);
    $cols = $this->payperiod->days + 2 + $subtotalcols;
    $this->payperiod->cols = $cols;
    $this->payperiod->subtotalcols = $subtotalcols;
    JFactory::getDocument()->setTitle(
        JText::sprintf(
            "COM_TIMECLOCK_ADD_HOURS_TITLE",
            $user->name,
            JHTML::_('date', $this->date, JText::_("DATE_FORMAT_LC3"))
        )
    );
?>
<div id="timeclock">
<form action="index.php?option=com_timeclock&controller=timesheet" method="post" name="userform" autocomplete="off" class="addhours">
    <div class="page-header">
        <h2 itemprop="name">
            <a id="timeclocktop"><?php print JText::_("COM_TIMECLOCK_ADD_HOURS");?></a>
        </h2>
    </div>
    <div class="">
        <fieldset class="form-horizontal">
<?php 
    $field = $this->form->getField("worked");
    $field->setValue($this->date);
    print TimeclockHelpersView::getFormField($field);
    $allproj = array();
    foreach ($this->projects as $cat => $projects) {
        print "<h2>".JText::_("JCATEGORY").": ".JText::_($projects["name"])."</h2>";
        foreach ($projects["proj"] as $proj) {
            $allproj[$proj->project_id] = $proj->project_id;
            $proj->payperiod = &$this->payperiod;
            $proj->data      = isset($this->data[$proj->project_id]) ? $this->data[$proj->project_id] : array();
            $proj->form      = &$this->form;
            $proj->params    = &$this->params;
            print $this->_entry->render($proj);
        }
    }
?>
        </fieldset>
        <fieldset id="extra">
            <?php print JHTML::_("form.token"); ?>
        </fieldset>
    </div>
</form>
<div id="addHoursTotal">
    <?php print JText::_("COM_TIMECLOCK_TOTAL_HOURS"); ?>: <span id="hoursTotal">-</span>
</div>
<script type="text/JavaScript">
    jQuery( document ).ready(function() {
        Addhours.setup();
    });
    Timeclock.params       = <?php print json_encode($this->params); ?>

</script>
</div>