<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\FileLayout;

$header   = new FileLayout('header', __DIR__.'/layouts');
$row      = new FileLayout('row', __DIR__.'/layouts');
$totals   = new FileLayout('totals', __DIR__.'/layouts');
$nextprev = new FileLayout('payperiodnextprev', dirname(__DIR__).'/layouts');
$toolbar  = new FileLayout('toolbar', __DIR__.'/layouts');
$export   = new FileLayout('export', dirname(__DIR__).'/layouts');
$notes    = new FileLayout('notes', dirname(__DIR__).'/layouts');

HTMLHelper::_("jquery.framework");
JHTML::script(Juri::base()."components/com_timeclock/js/report.js");
JHTML::script(Juri::base()."components/com_timeclock/js/timeclock.js");

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
            <?php if ($this->payperiod->locked): ?>
                <span class="locked hasTooltip" title="<?php print Text::_("COM_TIMECLOCK_PAYPERIOD_LOCKED"); ?>"><?php print HTMLHelper::_('image', 'system/checked_out.png', null, null, true); ?></span>
            <?php endif; ?>
        </h2>
    </div>
    <?php print $toolbar->render((object)array(
        "payperiod" => $this->payperiod,
        "actions" => $this->actions,

    )); 
    ?>
    <?php print $nextprev->render($this->payperiod); ?>
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
        print $export->render(
            (object)array(
                "url" => 'index.php?option=com_timeclock',
                "export" => $this->export,
            )
        ); 
    ?>
    <div class="table-responsive">
        <table class="report table table-striped table-bordered table-hover table-condensed">
            <thead>
<?php print $header->render($this->payperiod); ?>
            </thead>
            <tfoot>
<?php 
    print $totals->render(
        (object)array(
            "payperiod" => $this->payperiod, 
            "totals" => $this->data["totals"], 
        )
    ); 
?>
            </tfoot>
            <tbody>
<?php 
    foreach ($this->users as $user_id => $user) {
        $user->payperiod = $this->payperiod;
        $user->data = isset($this->data[$user_id]) ? $this->data[$user_id] : array();
        $user->notes = isset($this->data["notes"][$user_id]) ? $this->data["notes"][$user_id] : array();
        print $row->render($user);
    }
?>
            </tbody>
        </table>
    </div>
<form action="<?php Route::_("index.php"); ?>" method="post" name="userform" class="timeclock_report">
    <input type="hidden" name="view" value="payroll" />
    <?php print HTMLHelper::_("form.token"); ?>
</form>
<h3><?php print Text::_("COM_TIMECLOCK_USER_MANAGERS_APPROVAL"); ?></h3>
<?php
    $done = 0;
    $notdone = 0;
?>
<table class="report table table-striped table-bordered table-hover table-condensed" style="width: auto;">
<thead>
        <tr>
            <th><?php print Text::_("COM_TIMECLOCK_MANAGER") ?></th>
            <th class="text-center"><?php print Text::_("COM_TIMECLOCK_DONE") ?></th>
            <th class="text-center"><?php print Text::_("COM_TIMECLOCK_NOT_DONE") ?></th>
            <th class="text-center">%</th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($this->managers as $user_id => $user): ?>
        <?php if (($user->done + $user->notdone) == 0) continue; ?>
        <?php $done += $user->done; ?>
        <?php $notdone += $user->notdone; ?>
        <tr>
            <td class="hasToolTip" title="<?php print implode("\r\n", $user->users); ?>"><?php print $user->name; ?></td>
            <td class="text-center"><?php print $user->done; ?></td>
            <td class="text-center"><?php print $user->notdone; ?></td>
            <td class="text-center"><?php print round(($user->done / ($user->notdone + $user->done)) * 100); ?></td>
        </tr>
<?php endforeach; ?>
    </tbody>
    <tfoot>
    <tr>
            <th><?php print Text::_("COM_TIMECLOCK_TOTAL") ?></th>
            <td class="text-center"><?php print $done; ?></td>
            <td class="text-center"><?php print $notdone; ?></td>
            <th>&nbsp;</th>
        </tr>
    </tfoot>
</table>

<?php 
    /*
    foreach ($this->users as $user_id => $user) {
        $user->payperiod = $this->payperiod;
        $user->data = isset($this->data["notes"][$user_id]) ? $this->data["notes"][$user_id] : array();
        print $notes->render($user);
    }
    */
?>
<script type="text/JavaScript">
    Report.filter    = { start: "<?php print $this->payperiod->start; ?>" };
    Report.projects  = { };
    Report.data      = { };
    Timeclock.params     = <?php print json_encode($this->params); ?>;
    Timeclock.report     = 0;
    

</script>

</div>