<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\FileLayout;

$entry = new FileLayout('entry', __DIR__.'/layouts');

HTMLHelper::_("jquery.framework");
HTMLHelper::script(Uri::base()."components/com_timeclock/js/addhours.js");

$user = $this->getCurrentUser();
$subtotalcols = (int)($this->payperiod->days / $this->payperiod->splitdays);
$cols = $this->payperiod->days + 2 + $subtotalcols;
$this->payperiod->cols = $cols;
$this->payperiod->subtotalcols = $subtotalcols;
if (count($this->projects) > 1) {
    unset($this->projects[0]);
}
$allproj = array();
?>
<script type="text/JavaScript">
    var Timesheet = Timesheet || parent.Timesheet;
    var Timeclock = Timeclock || parent.Timeclock;
    parent.Addhours = Addhours;
    jQuery( document ).ready(function() {
        Addhours.setup();
    });

</script>
<div id="timeclock">
<form action="index.php?option=com_timeclock" method="post" name="userform" autocomplete="off" class="addhours">
    <div class="">
        <fieldset class="form-horizontal">
            <input type="hidden" name="worked" value="<?php print $this->date; ?>" />
            <div>
                <?php print Text::_("COM_TIMECLOCK_TOTAL_HOURS"); ?>: <span id="hoursTotal">-</span>
                (<?php print Text::_("COM_TIMECLOCK_MAX").":  ".$this->params->get("maxDailyHours"); ?>)
            </div>

<?php 
    $cat = reset($this->projects);
    $proj = reset($cat["proj"]);
    $allproj[$proj->project_id] = $proj->project_id;
    $proj->payperiod = &$this->payperiod;
    $proj->data      = isset($this->data[$proj->project_id]) ? $this->data[$proj->project_id] : array();
    $proj->form      = &$this->form;
    $proj->params    = &$this->params;
    $proj->noButtons = true;
    print $entry->render($proj);
?>
        </fieldset>
        <fieldset id="extra">
            <?php print HTMLHelper::_("form.token"); ?>
        </fieldset>
    </div>
</form>
<script type="text/JavaScript">
    jQuery( document ).ready(function() {
        Addhours.reset(<?php print $proj->project_id ?>, '<?php print $this->date; ?>', window.parent.document.body);
    });
</script>

</div>