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
    <input type="hidden" name="controller" value="payroll" />
    <input type="hidden" name="view" value="payroll" />
    <?php print HTMLHelper::_("form.token"); ?>
</form>
<?php 
    /*
    foreach ($this->users as $user_id => $user) {
        $user->payperiod = $this->payperiod;
        $user->data = isset($this->data["notes"][$user_id]) ? $this->data["notes"][$user_id] : array();
        print $notes->render($user);
    }
    */
?>
</div>