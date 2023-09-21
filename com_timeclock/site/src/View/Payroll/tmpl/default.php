<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

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
    <?php print $this->_toolbar->render((object)array(
        "payperiod" => $this->payperiod,
        "actions" => $this->actions,

    )); 
    ?>
    <?php print $this->_nextprev->render($this->payperiod); ?>
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
        print $this->_export->render(
            (object)array(
                "url" => 'index.php?option=com_timeclock',
                "export" => $this->export,
            )
        ); 
    ?>
    <div class="table-responsive">
        <table class="report table table-striped table-bordered table-hover table-condensed">
            <thead>
<?php print $this->_header->render($this->payperiod); ?>
            </thead>
            <tfoot>
<?php 
    print $this->_totals->render(
        (object)array(
            "payperiod" => $this->payperiod, 
            "totals" => $this->data["totals"], 
            "rowClass" => "livedata"
        )
    ); 
?>
            </tfoot>
            <tbody>
<?php 
    foreach ($this->users as $user_id => $user) {
        $user->payperiod = $this->payperiod;
        $user->data = isset($this->data[$user_id]) ? $this->data[$user_id] : array();
        $user->rowClass = "livedata";
        $user->notes = isset($this->data["notes"][$user_id]) ? $this->data["notes"][$user_id] : array();
        print $this->_row->render($user);
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
        print $this->_notes->render($user);
    }
    */
?>
</div>