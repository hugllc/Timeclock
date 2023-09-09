<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

    JHTML::script(Juri::base()."components/com_timeclock/views/timesheet/tmpl/timesheet.js");
    JHTML::script(Juri::base()."components/com_timeclock/js/timeclock.js");
    JHtmlBehavior::core();
    JHTML::_("bootstrap.tooltip", ".hasTooltip", []);

    $cols = $this->payperiod->days + 2 + $this->payperiod->subtotals;
    $this->payperiod->cols = $cols;

    Factory::getDocument()->setTitle(
        Text::sprintf(
            "COM_TIMECLOCK_TIMESHEET_TITLE",
            $this->user->name,
            JHTML::_('date', $this->payperiod->start, Text::_("DATE_FORMAT_LC3")),
            JHTML::_('date', $this->payperiod->end, Text::_("DATE_FORMAT_LC3"))
        )
    );
    
?>
<div id="timeclock">
    <div class="page-header">
        <h2 itemprop="name">
            <a id="timeclocktop"></a>
            <?php print Text::sprintf("COM_TIMECLOCK_TIMESHEET_FOR", $this->user->name);?>
            <span class="locked hasTooltip" title="<?php print Text::_("COM_TIMECLOCK_PAYPERIOD_LOCKED"); ?>"><?php print HTMLHelper::_('image', 'system/checked_out.png', null, null, true); ?></span>
            <span class="complete">(<?php print Text::_("COM_TIMECLOCK_COMPLETE"); ?>)</span>
        </h2>
    </div>
    <?php print $this->_toolbar->render($this->user); ?>
    <?php print $this->_nextprev->render($this->payperiod); ?>
    <div class="dateheader">
        <strong>
            <?php print Text::sprintf(
                "COM_TIMECLOCK_DATE_TO_DATE",
                JHTML::_('date', $this->payperiod->start),
                JHTML::_('date', $this->payperiod->end)
                ); ?>
        </strong>
    </div>
    <div class="paid">
        <table class="paid timesheet table table-striped table-bordered table-hover table-condensed">
            <thead>
<?php 
    print $this->_uname->render(
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
    $paid    = 0;
    $allproj = array();
    $proj    = array();
    foreach ($this->projects as $cat => $projects) {
        $render = "";
        $cnt    = 0;
        foreach ($projects["proj"] as $proj) {
            if ($proj->type == "UNPAID") {
                continue;
            }
            $cnt++;
            $allproj[$proj->project_id] = $proj;
            $proj->payperiod = $this->payperiod;
            $proj->data      = isset($this->data[$proj->project_id]) ? $this->data[$proj->project_id] : array();
            $proj->user      = $this->user;
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
    print $this->_uname->render(
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
            $allproj[$proj->project_id] = $proj;
            $proj->payperiod = $this->payperiod;
            $proj->data      = isset($this->data[$proj->project_id]) ? $this->data[$proj->project_id] : array();
            $proj->user      = $this->user;
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
<?php 
    if (!$this->user->me) {
        $user = $this->user;
        $user->user_id = $user->id;
        $user->payperiod = $this->payperiod;
        $user->data = array();
        foreach ($allproj as $proj_id => $proj) {
            if (!isset($this->data[$proj_id])) {
                continue;
            }
            $user->data[$proj_id] = array(
                "project_id" => $proj->project_id,
                "project_name" => $proj->name,
                "worked" => $this->data[$proj_id],
            );
        }
        print $this->_notes->render($user);
    }
?>
    <script type="text/JavaScript">
        jQuery( document ).ready(function() {
            Timesheet.setup();
        });
        Timesheet.subtotalcols = <?php print $this->payperiod->subtotals; ?>;
        Timesheet.dates        = <?php print json_encode(array_keys($this->payperiod->dates)); ?>;
        Timesheet.allprojs     = <?php print json_encode(array_keys($allproj)); ?>;
        Timesheet.projects     = <?php print json_encode($this->projects); ?>;
        Timesheet.payperiod    = <?php print json_encode($this->payperiod); ?>;
        Timesheet.data         = <?php print json_encode($this->data); ?>;
        Timesheet.paid         = <?php print $paid; ?>;
        Timesheet.volunteer    = <?php print $unpaid; ?>;
        Timeclock.params       = <?php print json_encode($this->params->toArray()); ?>;
        Timeclock.me           = <?php print (int)$this->user->me; ?>;

    </script>
</div>
