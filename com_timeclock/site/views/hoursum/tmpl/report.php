<?php
    JHTML::script(Juri::base()."components/com_timeclock/js/report.js");
    JHTML::script(Juri::base()."components/com_timeclock/js/timeclock.js");
    JHTML::_('behavior.modal'); 
    JHTML::_('behavior.calendar');
    JHtml::_('formbehavior.chosen', 'select:not(.plain)');
    $cols = count($this->users) + 2;

    JFactory::getDocument()->setTitle(
        JText::sprintf(
            "COM_TIMECLOCK_HOURSUM_REPORT_TITLE",
            JHTML::_('date', $this->start, JText::_("DATE_FORMAT_LC3")),
            JHTML::_('date', $this->end, JText::_("DATE_FORMAT_LC3"))
        )
    );
    $doreports = ($this->report_id != 0);
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
<form action="<?php JROUTE::_("index.php?option=com_timeclock&controller=hoursum"); ?>" method="post" name="userform" class="report">
    <div class="page-header row">
        <h2 itemprop="name">
            <a id="timeclocktop"></a>
            <?php print JText::_("COM_TIMECLOCK_HOURSUM_REPORT"); ?>
            <?php print ($doreports) ? " - ".JText::_("COM_TIMECLOCK_SAVED_DATA").":  ".$this->report->name : ""; ?>
        </h2>
    </div>
    <?php print $this->_control->render($this->filter); ?>
    <div class="dateheader">
        <strong>
            <?php print JText::sprintf(
                "COM_TIMECLOCK_DATE_TO_DATE",
                JHTML::_('date', $this->start),
                JHTML::_('date', $this->end)
                ); ?>
        </strong>
    </div>
    <?php
        print $this->_export->render(
            (object)array(
                "url" => JRoute::_('&option=com_timeclock&controller=hoursum'),
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
        print $this->_dataset->render(
            (object)array(
                "data"     => $data,
                "total"    => $this->data["total"],
                "decimals" => $this->params->get("decimalPlaces"),
                "title"    => JText::_("COM_TIMECLOCK_HOURS_BY_PROJ_MANAGER"),
                "group"    => JText::_("COM_TIMECLOCK_PROJECT_MANAGER"),
                "png"      => $this->pie(
                    JText::_("COM_TIMECLOCK_HOURSUM_PROJ_MANAGER_PLOT_TITLE"), $data
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
        print $this->_dataset->render(
            (object)array(
                "data"     => $data,
                "total"    => $this->data["total"],
                "decimals" => $this->params->get("decimalPlaces"),
                "title"    => JText::_("COM_TIMECLOCK_HOURS_BY_USER_MANAGER"),
                "group"    => JText::_("COM_TIMECLOCK_USER_MANAGER"),
                "png"      => $this->pie(
                    JText::_("COM_TIMECLOCK_HOURSUM_USER_MANAGER_PLOT_TITLE"), $data
                ),
            )
        );
        /******************** HOURS BY PROJECT TYPE ************************/
        $data  = array();
        foreach ($this->data["type"] as $type => $hours) {
            $name = $this->getProjType($type);
            $data[$name] = $hours;
        }
        print $this->_dataset->render(
            (object)array(
                "data"     => $data,
                "total"    => $this->data["total"],
                "decimals" => $this->params->get("decimalPlaces"),
                "title"    => JText::_("COM_TIMECLOCK_HOURS_BY_PROJECT_TYPE"),
                "group"    => JText::_("COM_TIMECLOCK_PROJECT_TYPE"),
                "png"      => $this->pie(
                    JText::_("COM_TIMECLOCK_HOURSUM_PROJECT_TYPE_PLOT_TITLE"), $data
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
        print $this->_dataset->render(
            (object)array(
                "data"     => $data,
                "total"    => $this->data["total"],
                "decimals" => $this->params->get("decimalPlaces"),
                "title"    => JText::_("COM_TIMECLOCK_HOURS_BY_CATEGORY"),
                "group"    => JText::_("COM_TIMECLOCK_CATEGORY"),
                "png"      => $this->pie(
                    JText::_("COM_TIMECLOCK_HOURSUM_CATEGORY_PLOT_TITLE"), $data
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
        print $this->_dataset->render(
            (object)array(
                "data"     => $data,
                "total"    => $this->data["total"],
                "decimals" => $this->params->get("decimalPlaces"),
                "title"    => JText::_("COM_TIMECLOCK_HOURS_BY_CUSTOMER"),
                "group"    => JText::_("COM_TIMECLOCK_CUSTOMER"),
                "png"      => $this->pie(
                    JText::_("COM_TIMECLOCK_HOURSUM_CUSTOMER_PLOT_TITLE"), $data
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
        print $this->_dataset->render(
            (object)array(
                "data"     => $data,
                "total"    => $this->data["total"],
                "decimals" => $this->params->get("decimalPlaces"),
                "title"    => JText::_("COM_TIMECLOCK_HOURS_BY_DEPARTMENT"),
                "group"    => JText::_("COM_TIMECLOCK_DEPARTMENT"),
                "png"      => $this->pie(
                    JText::_("COM_TIMECLOCK_HOURSUM_DEPARTMENT_PLOT_TITLE"), $data
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
        print $this->_dataset->render(
            (object)array(
                "data"     => $data,
                "total"    => $this->data["total"],
                "decimals" => $this->params->get("decimalPlaces"),
                "title"    => JText::_("COM_TIMECLOCK_HOURS_BY_PROJECT"),
                "group"    => JText::_("COM_TIMECLOCK_PROJECT"),
                "png"      => $this->pie(
                    JText::_("COM_TIMECLOCK_HOURSUM_PROJECT_PLOT_TITLE"), $data
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
        print $this->_dataset->render(
            (object)array(
                "data"     => $data,
                "total"    => $this->data["total"],
                "decimals" => $this->params->get("decimalPlaces"),
                "title"    => JText::_("COM_TIMECLOCK_HOURS_BY_USER"),
                "group"    => JText::_("COM_TIMECLOCK_USER"),
                "png"      => $this->pie(
                    JText::_("COM_TIMECLOCK_HOURSUM_USER_PLOT_TITLE"), $data
                ),
            )
        );
    ?>
    </div>
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