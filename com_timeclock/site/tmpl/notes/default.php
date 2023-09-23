<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_("jquery.framework");
HTMLHelper::script(Uri::base()."components/com_timeclock/js/report.js");
HTMLHelper::script(Uri::base()."components/com_timeclock/js/timeclock.js");

$cols = count($this->users) + 2;

Factory::getDocument()->setTitle(
    Text::sprintf(
        "COM_TIMECLOCK_NOTES_REPORT_TITLE",
        HTMLHelper::_('date', $this->start, Text::_("DATE_FORMAT_LC3")),
        HTMLHelper::_('date', $this->end, Text::_("DATE_FORMAT_LC3"))
    )
);
?>
<div id="timeclock" class="container-fluid">
<form action="<?php Route::_("index.php?option=com_timeclock&controller=notes"); ?>" method="post" name="userform" class="report">
    <div class="page-header row">
        <h2 itemprop="name">
            <a id="timeclocktop"></a>
            <?php print Text::_("COM_TIMECLOCK_NOTES_REPORT"); ?>
        </h2>
    </div>
    <?php print $this->_control->render($this->filter); ?>
    <div class="dateheader">
        <strong>
            <?php print Text::sprintf(
                "COM_TIMECLOCK_DATE_TO_DATE",
                HTMLHelper::_('date', $this->start),
                HTMLHelper::_('date', $this->end)
                ); ?>
        </strong>
    </div>
    <?php 
        print $this->_export->render(
            (object)array(
                "url" => Route::_('&option=com_timeclock&controller=notes'),
                "export" => $this->export,
            )
        ); 
    ?>
    <div>
        <?php 
            foreach ($this->users as $user_id => $user) {
                if (($user_id > 0) && isset($this->data["notes"][$user_id])) {
                    $user->payperiod = $this->payperiod;
                    $user->data = $this->data["notes"][$user_id];
                    print $this->_notes->render($user);
                }
            }
        ?>
    </div>
    <input type="hidden" name="controller" value="notes" />
    <?php print HTMLHelper::_("form.token"); ?>
</form>
<script type="text/JavaScript">
    jQuery( document ).ready(function() {
        Report.setup();
    });
    Report.filter    = <?php print json_encode($this->filter); ?>;
    Report.projects  = <?php print json_encode($this->projects); ?>;
    Report.data      = <?php print json_encode($this->data); ?>;
    Timeclock.params = <?php print json_encode($this->params); ?>;
    Timeclock.report = 0;
    

</script>
</div>