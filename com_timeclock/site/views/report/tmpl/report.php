<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

    JHTML::script(Juri::base()."components/com_timeclock/js/report.js");
    JHTML::script(Juri::base()."components/com_timeclock/js/timeclock.js");
    HTMLHelper::_('formbehavior.chosen', 'select:not(.plain)');
    $cols = count($this->users) + 2;

    Factory::getDocument()->setTitle(
        Text::sprintf(
            "COM_TIMECLOCK_REPORT_TITLE",
            JHTML::_('date', $this->start, Text::_("DATE_FORMAT_LC3")),
            JHTML::_('date', $this->end, Text::_("DATE_FORMAT_LC3"))
        )
    );
    $doreports = ($this->report_id != 0);
?>
<div id="timeclock" class="container-fluid">
<form action="<?php JROUTE::_("index.php?option=com_timeclock&controller=report"); ?>" method="post" name="userform" class="report">
    <div class="page-header row">
        <h2 itemprop="name">
            <a id="timeclocktop"></a>
            <?php print Text::_("COM_TIMECLOCK_REPORT"); ?>
            <?php print ($doreports) ? " - ".Text::_("COM_TIMECLOCK_SAVED_DATA").":  ".$this->report->name : ""; ?>
        </h2>
    </div>
    <?php print $this->_control->render($this->filter); ?>
    <div class="dateheader">
        <strong>
            <?php print Text::sprintf(
                "COM_TIMECLOCK_DATE_TO_DATE",
                JHTML::_('date', $this->start),
                JHTML::_('date', $this->end)
                ); ?>
        </strong>
    </div>
    <?php 
        print $this->_export->render(
            (object)array(
                "url" => Route::_('&option=com_timeclock&controller=report'),
                "export" => $this->export,
            )
        ); 
    ?>
    <div class="table-responsive">
        <table class="report table table-striped table-bordered table-hover table-condensed">
            <thead>
<?php print $this->_header->render($this->users); ?>
            </thead>
            <tfoot>
<?php 
    print $this->_totals->render(
        (object)array(
            "users" => $this->users, 
            "data" => $this->data["totals"], 
            "rowClass" => "livedata",
            "money" => ($this->datatype == "money"),
            "currency" => $this->currency
        )
    ); 
?>
            </tfoot>
            <tbody>
<?php 
    $allproj = array();
    foreach ($this->projects as $cat => $projects) {
        $render = "";
        $cnt    = 0;
        foreach ($projects["proj"] as $proj) {
            $cnt++;
            $proj_id = (int)$proj->project_id;
            $allproj[$proj_id] = $proj;
            $proj->data     = isset($this->data[$proj_id]) ? $this->data[$proj_id] : array();
            $proj->users    = $this->users;
            $proj->money    = ($this->datatype == "money");
            $proj->currency = $this->currency;

            $render .= $this->_row->render($proj);
        }
        if ($cnt > 0) {
            print $this->_category->render(
                (object)array(
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
    <input type="hidden" name="controller" value="report" />
    <?php print JHTML::_("form.token"); ?>
</form>
<script type="text/JavaScript">
    jQuery( document ).ready(function() {
        Report.setup();
    });
    Report.filter    = <?php print json_encode($this->filter); ?>;
    Report.projects  = <?php print json_encode($this->projects); ?>;
    Report.data      = <?php print json_encode($this->data); ?>;
    Report.doreports = <?php print (int)$doreports; ?>;
    Timeclock.params = <?php print json_encode($this->params); ?>;
    Timeclock.report = 0;
    

</script>
</div>