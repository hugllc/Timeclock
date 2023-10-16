<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\FileLayout;

$header     = new FileLayout('header', __DIR__.'/layouts');
$row        = new FileLayout('row', __DIR__.'/layouts');
$totals     = new FileLayout('totals', __DIR__.'/layouts');
$nextprev   = new FileLayout('payperiodnextprev', dirname(__DIR__).'/layouts');
$category   = new FileLayout('category', __DIR__.'/layouts');
$subtotals  = new FileLayout('subtotals', __DIR__.'/layouts');
$psubtotals = new FileLayout('psubtotals', __DIR__.'/layouts');
$toolbar    = new FileLayout('toolbar', __DIR__.'/layouts');
$uname      = new FileLayout('name', __DIR__.'/layouts');
$notes      = new FileLayout('notes', dirname(__DIR__).'/layouts');

HTMLHelper::_("jquery.framework");
HTMLHelper::script(Uri::base()."components/com_timeclock/js/timesheet.js");
HTMLHelper::script(Uri::base()."components/com_timeclock/js/timeclock.js");
HTMLHelper::_("bootstrap.tooltip", ".hasTooltip", ["trigger" => "hover"]);

$cols = $this->payperiod->days + 2 + $this->payperiod->subtotals;
$this->payperiod->cols = $cols;

Factory::getDocument()->setTitle(
    Text::sprintf(
        "COM_TIMECLOCK_TIMESHEET_TITLE",
        $this->user->name,
        HTMLHelper::_('date', $this->payperiod->start, Text::_("DATE_FORMAT_LC3")),
        HTMLHelper::_('date', $this->payperiod->end, Text::_("DATE_FORMAT_LC3"))
    )
);
    
?>
<div id="timeclock">
    <div class="page-header">
        <h2 itemprop="name">
            <a id="timeclocktop"></a>
            <?php print Text::sprintf("COM_TIMECLOCK_TIMESHEET_FOR", $this->user->name);?>
            <span class="locked hasTooltip" title="<?php print Text::_("COM_TIMECLOCK_PAYPERIOD_LOCKED"); ?>" style="display: none;"><?php print HTMLHelper::_('image', 'system/checked_out.png', null, null, true); ?></span>
            <span class="complete smaller" style="display: none;">(<?php print Text::_("COM_TIMECLOCK_COMPLETE"); ?>)</span>
            <?php if ($this->payperiod->approved): ?>
            <span class="approved smaller">(<?php print Text::_("COM_TIMECLOCK_APPROVED"); ?>)</span>
            <?php endif; ?>
        </h2>
    </div>
    <?php print $toolbar->render((object)array("user" => $this->user, "payperiod" => $this->payperiod)); ?>
    <?php print $nextprev->render($this->payperiod); ?>
    <div class="dateheader">
        <strong>
            <?php print Text::sprintf(
                "COM_TIMECLOCK_DATE_TO_DATE",
                HTMLHelper::_('date', $this->payperiod->start),
                HTMLHelper::_('date', $this->payperiod->end)
                ); ?>
        </strong>
    </div>
    <?php if ($this->counts["paid"] > 0): ?>
    <div class="paid">
        <table class="paid timesheet table table-striped table-bordered table-hover table-condensed">
            <thead>
<?php 
    print $uname->render(
        (object)array(
            "cols" => $this->payperiod->cols,
            "name" => "COM_TIMECLOCK_PAID_TIME",
        )
    );
    print $header->render($this->payperiod); 
?>
            </thead>
            <tfoot>
<?php print $header->render($this->payperiod);  ?>
<?php print $subtotals->render($this->payperiod); ?>
<?php print $psubtotals->render($this->payperiod); ?>
<?php print $totals->render($this->payperiod); ?>
            </tfoot>
            <tbody>
<?php 
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
            $render .= $row->render($proj);
        }
        if ($cnt > 0) {
            print $category->render(
                array(
                    "cols" => $cols,
                    "id" => $cat,
                    "name" => $projects["name"],
                    "description" => $projects["description"],
                )
            );
            print $render;
        }
    }
?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    <?php if ($this->counts["unpaid"] > 0): ?>
    <div class="volunteer">
        <table class="volunteer timesheet">
            <thead>
<?php 
    print $uname->render(
        (object)array(
            "cols" => $this->payperiod->cols,
            "name" => "COM_TIMECLOCK_VOLUNTEER_TIME",
        )
    );
    print $header->render($this->payperiod); 
?>
            </thead>
            <tfoot>
<?php print $header->render($this->payperiod);  ?>
<?php print $subtotals->render($this->payperiod); ?>
<?php print $psubtotals->render($this->payperiod); ?>
<?php print $totals->render($this->payperiod); ?>
            </tfoot>
            <tbody>
<?php 
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
            $render .= $row->render($proj);
        }
        if ($cnt > 0) {
            print $category->render(
                array(
                    "cols" => $cols,
                    "id" => $cat,
                    "name" => $projects["name"],
                    "description" => $projects["description"],
                )
            );
            print $render;
        }
    }
?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    <form action="<?php JROUTE::_("index.php"); ?>" method="post" class="timesheet">
        <?php print HTMLHelper::_("form.token"); ?>
    </form>
<?php 
    if (!$this->user->me) {
        $user = $this->user;
        $user->user_id = $user->id;
        $user->payperiod = $this->payperiod;
        $user->data = array();
        $user->decimals = $this->params->get("decimalPlaces");
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
        print "<h2>Notes:</h2>";
        print $notes->render($user);
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
        Timeclock.params       = <?php print json_encode($this->params->toArray()); ?>;
        Timeclock.me           = <?php print (int)$this->user->me; ?>;
        Timeclock.approve      = <?php print (int)(!$this->user->me && Factory::getUser()->authorise("timeclock.timesheet.approve", "com_timeclock")) ?>;

    </script>
</div>
