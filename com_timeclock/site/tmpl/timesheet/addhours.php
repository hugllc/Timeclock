<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Router\Route;

$entry = new FileLayout('entry', __DIR__.'/layouts');

HTMLHelper::_("jquery.framework");
HTMLHelper::script(Uri::base()."components/com_timeclock/js/addhours.js");
HTMLHelper::script(Uri::base()."components/com_timeclock/js/timeclock.js");

// $url = Route::_("index.php?option=com_timeclock&view=timesheet");
$url = Uri::getInstance();
$url->delVar('task');
$url->delVar('date');

$user = $this->getCurrentUser();
$subtotalcols = (int)($this->payperiod->days / $this->payperiod->splitdays);
$cols = $this->payperiod->days + 2 + $subtotalcols;
$this->payperiod->cols = $cols;
$this->payperiod->subtotalcols = $subtotalcols;
Factory::getDocument()->setTitle(
    Text::sprintf(
        "COM_TIMECLOCK_ADD_HOURS_TITLE",
        $user->name,
        HTMLHelper::_('date', $this->date, Text::_("DATE_FORMAT_LC3"))
    )
);
?>
<div id="timeclock">
<form action="<?php echo $url->toString() ?>" method="post" name="userform" autocomplete="off" class="addhours">
    <div class="page-header">
        <h3 itemprop="name">
            <?php printf(Text::_("COM_TIMECLOCK_ADD_HOURS_TITLE"), $user->name, HTMLHelper::_("date", $this->date)); ?>
        </h3>
    </div>
    <div class="">
        <fieldset class="form-horizontal">
            <input type="hidden" name="worked" value="<?php print $this->date; ?>" />
<?php 
    $allproj = array();
    $projlist = array();
    foreach ($this->projects as $cat => $projects) {
        print "<h2>".Text::_("JCATEGORY").": ".Text::_($projects["name"])."</h2>";
        foreach ($projects["proj"] as $proj) {
            $allproj[$proj->project_id] = $proj->project_id;
            $projlist[$proj->project_id] = $proj;
            $proj->payperiod = &$this->payperiod;
            $proj->data      = isset($this->data[$proj->project_id]) ? $this->data[$proj->project_id] : array();
            $proj->form      = &$this->form;
            $proj->params    = &$this->params;
            print $entry->render($proj);
        }
    }
    ?>
        </fieldset>
        <fieldset id="extra">
            <?php print HTMLHelper::_("form.token"); ?>
        </fieldset>
    </div>
</form>
<div id="addHoursTotal">
    <?php print Text::_("COM_TIMECLOCK_TOTAL_HOURS"); ?>: <span id="hoursTotal">-</span>
    (<?php print Text::_("COM_TIMECLOCK_MAX").":  ".$this->params->get("maxDailyHours"); ?>)
</div>
<script type="text/JavaScript">
    jQuery( document ).ready(function() {
        Addhours.setup();
    });
    Addhours.payperiod  = <?php print json_encode($this->payperiod); ?>;
    Timeclock.params    = <?php print json_encode($this->params->toArray()); ?>;
    Timeclock.projects  = <?php print json_encode($projlist); ?>;
</script>
</div>