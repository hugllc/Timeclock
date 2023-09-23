<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\FileLayout;

$dataset = new FileLayout('dataset', __DIR__.'/layouts');
$export  = new FileLayout('export', dirname(__DIR__).'/layouts');
$control = new FileLayout('reportcontrol', dirname(__DIR__).'/layouts');

HTMLHelper::_("jquery.framework");
HTMLHelper::script(Uri::base()."components/com_timeclock/js/report.js");
HTMLHelper::script(Uri::base()."components/com_timeclock/js/timeclock.js");

$cols = count($this->users) + 2;

Factory::getDocument()->setTitle(
    Text::sprintf(
        "COM_TIMECLOCK_HOURSUM_REPORT_TITLE",
        HTMLHelper::_('date', $this->start, Text::_("DATE_FORMAT_LC3")),
        HTMLHelper::_('date', $this->end, Text::_("DATE_FORMAT_LC3"))
    )
);
$allproj = array();
foreach ($this->projects as $cat) {
    foreach ($cat["proj"] as $proj) {
        $allproj[$proj->project_id] = array(
            "name" => $proj->name,
            "description" => $proj->description,
        );
    }
}
?>
<div id="timeclock" class="container-fluid">
<form action="<?php Route::_("index.php?option=com_timeclock&controller=hoursum"); ?>" method="post" name="userform" class="report">
    <div class="page-header row">
        <h2 itemprop="name">
            <a id="timeclocktop"></a>
            <?php print Text::_("COM_TIMECLOCK_HOURSUM_REPORT"); ?>
        </h2>
    </div>
    <?php print $control->render($this->filter); ?>
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
        print $export->render(
            (object)array(
                "url" => Route::_('&option=com_timeclock&controller=hoursum'),
                "export" => $this->export,
            )
        );
    ?>
    <div class="container-fluid">
    <?php
        /******************** HOURS BY PROJECT MANAGER ************************/
        $data  = array();
        foreach ($this->data["proj_manager"] as $user_id => $hours) {
            $name = isset($this->users[$user_id]) ? $this->users[$user_id]->name : "";
            if (empty($name)) {
                $name = "User $user_id";
            }
            $data[$name] = $hours;
        }
        print $dataset->render(
            (object)array(
                "data"     => $data,
                "total"    => $this->data["total"],
                "decimals" => $this->params->get("decimalPlaces"),
                "title"    => Text::_("COM_TIMECLOCK_HOURS_BY_PROJ_MANAGER"),
                "group"    => Text::_("COM_TIMECLOCK_PROJECT_MANAGER"),
                "png"      => $this->pie(
                    Text::_("COM_TIMECLOCK_HOURSUM_PROJ_MANAGER_PLOT_TITLE"), $data
                ),
            )
        );
        /******************** HOURS BY USER MANAGER ************************/
        $data  = array();
        foreach ($this->data["user_manager"] as $user_id => $hours) {
            $name = isset($this->users[$user_id]) ? $this->users[$user_id]->name : "";
            if (empty($name)) {
                $name = "User $user_id";
            }
            $data[$name] = $hours;
        }
        print $dataset->render(
            (object)array(
                "data"     => $data,
                "total"    => $this->data["total"],
                "decimals" => $this->params->get("decimalPlaces"),
                "title"    => Text::_("COM_TIMECLOCK_HOURS_BY_USER_MANAGER"),
                "group"    => Text::_("COM_TIMECLOCK_USER_MANAGER"),
                "png"      => $this->pie(
                    Text::_("COM_TIMECLOCK_HOURSUM_USER_MANAGER_PLOT_TITLE"), $data
                ),
            )
        );
        /******************** HOURS BY PROJECT TYPE ************************/
        $data  = array();
        foreach ($this->data["type"] as $type => $hours) {
            $name = $this->getProjType($type);
            $data[$name] = $hours;
        }
        print $dataset->render(
            (object)array(
                "data"     => $data,
                "total"    => $this->data["total"],
                "decimals" => $this->params->get("decimalPlaces"),
                "title"    => Text::_("COM_TIMECLOCK_HOURS_BY_PROJECT_TYPE"),
                "group"    => Text::_("COM_TIMECLOCK_PROJECT_TYPE"),
                "png"      => $this->pie(
                    Text::_("COM_TIMECLOCK_HOURSUM_PROJECT_TYPE_PLOT_TITLE"), $data
                ),
            )
        );
        /******************** HOURS BY CATEGORY ************************/
        $data  = array();
        foreach ($this->data["category"] as $cat_id => $hours) {
            $name = isset($this->projects[$cat_id]) ? $this->projects[$cat_id]["name"] : "";
            if (empty($name)) {
                $name = "Category $cat_id";
            }
            $data[$name] = $hours;
        }
        print $dataset->render(
            (object)array(
                "data"     => $data,
                "total"    => $this->data["total"],
                "decimals" => $this->params->get("decimalPlaces"),
                "title"    => Text::_("COM_TIMECLOCK_HOURS_BY_CATEGORY"),
                "group"    => Text::_("COM_TIMECLOCK_CATEGORY"),
                "png"      => $this->pie(
                    Text::_("COM_TIMECLOCK_HOURSUM_CATEGORY_PLOT_TITLE"), $data
                ),
            )
        );
        /******************** HOURS BY CUSTOMER ************************/
        $data  = array();
        foreach ($this->data["customer"] as $cust_id => $hours) {
            $name = isset($this->customers[$cust_id]) ? $this->customers[$cust_id]->company : "";
            if (empty($name)) {
                $name = "Customer $cust_id";
            }
            $data[$name] = $hours;
        }
        print $dataset->render(
            (object)array(
                "data"     => $data,
                "total"    => $this->data["total"],
                "decimals" => $this->params->get("decimalPlaces"),
                "title"    => Text::_("COM_TIMECLOCK_HOURS_BY_CUSTOMER"),
                "group"    => Text::_("COM_TIMECLOCK_CUSTOMER"),
                "png"      => $this->pie(
                    Text::_("COM_TIMECLOCK_HOURSUM_CUSTOMER_PLOT_TITLE"), $data
                ),
            )
        );
        /******************** HOURS BY DEPARTMENT ************************/
        $data  = array();
        foreach ($this->data["department"] as $dept_id => $hours) {
            $name = isset($this->departments[$dept_id]) ? $this->departments[$dept_id]->name : "";
            if (empty($name)) {
                $name = "Department $dept_id";
            }
            $data[$name] = $hours;
        }
        print $dataset->render(
            (object)array(
                "data"     => $data,
                "total"    => $this->data["total"],
                "decimals" => $this->params->get("decimalPlaces"),
                "title"    => Text::_("COM_TIMECLOCK_HOURS_BY_DEPARTMENT"),
                "group"    => Text::_("COM_TIMECLOCK_DEPARTMENT"),
                "png"      => $this->pie(
                    Text::_("COM_TIMECLOCK_HOURSUM_DEPARTMENT_PLOT_TITLE"), $data
                ),
            )
        );
        /******************** HOURS BY WCOMP CODE ************************/
        $data  = array();
        foreach ($this->data["wcomp"] as $code => $hours) {
            $name = sprintf("%04d", $code);
            $data[$name] = $hours;
        }
        print $dataset->render(
            (object)array(
                "data"     => $data,
                "total"    => $this->data["total"],
                "decimals" => $this->params->get("decimalPlaces"),
                "title"    => Text::_("COM_TIMECLOCK_HOURS_BY_WCOMP_CODE"),
                "group"    => Text::_("COM_TIMECLOCK_WCOMP_CODE"),
                "png"      => $this->pie(
                    Text::_("COM_TIMECLOCK_HOURSUM_WCOMP_CODE_PLOT_TITLE"), $data
                ),
            )
        );
        /******************** HOURS BY PROJECT ************************/
        $data  = array();
        foreach ($this->data["project"] as $proj_id => $hours) {
            $name = isset($allproj[$proj_id]) ? $allproj[$proj_id]["name"] : "";
            if (empty($name)) {
                $name = "Project $proj_id";
            }
            $data[$name] = $hours;
        }
        print $dataset->render(
            (object)array(
                "data"     => $data,
                "total"    => $this->data["total"],
                "decimals" => $this->params->get("decimalPlaces"),
                "title"    => Text::_("COM_TIMECLOCK_HOURS_BY_PROJECT"),
                "group"    => Text::_("COM_TIMECLOCK_PROJECT"),
                "png"      => $this->pie(
                    Text::_("COM_TIMECLOCK_HOURSUM_PROJECT_PLOT_TITLE"), $data
                ),
            )
        );
        /******************** HOURS BY USER ************************/
        $data  = array();
        foreach ($this->data["user"] as $user_id => $hours) {
            $name = isset($this->users[$user_id]) ? $this->users[$user_id]->name : "";
            if (empty($name)) {
                $name = "User $user_id";
            }
            $data[$name] = $hours;
        }
        print $dataset->render(
            (object)array(
                "data"     => $data,
                "total"    => $this->data["total"],
                "decimals" => $this->params->get("decimalPlaces"),
                "title"    => Text::_("COM_TIMECLOCK_HOURS_BY_USER"),
                "group"    => Text::_("COM_TIMECLOCK_USER"),
                "png"      => $this->pie(
                    Text::_("COM_TIMECLOCK_HOURSUM_USER_PLOT_TITLE"), $data
                ),
            )
        );
    ?>
    </div>
    <input type="hidden" name="controller" value="hoursum" />
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