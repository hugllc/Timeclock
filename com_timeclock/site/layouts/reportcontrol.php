<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\HTML\Helpers\Sidebar;
use HUGLLC\Component\Timeclock\Administrator\Helper\TimeclockHelper;
use HUGLLC\Component\Timeclock\Administrator\Helper\ViewHelper;
use HUGLLC\Component\Timeclock\Administrator\Model\ProjectsModel;
use HUGLLC\Component\Timeclock\Administrator\Model\DepartmentsModel;
use HUGLLC\Component\Timeclock\Administrator\Model\CustomersModel;

    defined( '_JEXEC' ) or die( 'Restricted access' );
    $model = new ProjectsModel();
    $options = $model->getOptions(
        array("p.published=1", "p.type = 'CATEGORY'"),
        "p.name ASC"
    );
    Sidebar::addFilter(
        Text::_('COM_TIMECLOCK_SELECT_CATEGORY'),
        'filter_category',
        HTMLHelper::_('select.options', $options, 'value', 'text', $displayData->category, true)
    );
    $options = array(
        JHTML::_('select.option', "PROJECT", Text::_("COM_TIMECLOCK_PROJECT")),
        JHTML::_('select.option', "PTO", Text::_("COM_TIMECLOCK_PTO")),
        JHTML::_('select.option', "HOLIDAY", Text::_("COM_TIMECLOCK_HOLIDAY")),
        JHTML::_('select.option', "UNPAID", Text::_("COM_TIMECLOCK_VOLUNTEER")),
    );
    Sidebar::addFilter(
        Text::_('COM_TIMECLOCK_SELECT_PROJECT_TYPE'),
        'filter_project_type',
        HTMLHelper::_('select.options', $options, 'value', 'text', $displayData->project_type, true)
    );

    $model = new DepartmentsModel();
    $options = $model->getOptions(
        array("d.published=1"),
        "d.name ASC"
    );
    Sidebar::addFilter(
        Text::_('COM_TIMECLOCK_SELECT_DEPARTMENT'),
        'filter_department',
        HTMLHelper::_('select.options', $options, 'value', 'text', $displayData->department, true)
    );

    $model = new CustomersModel();
    $options = $model->getOptions(
        array("c.published=1"),
        "c.company ASC"
    );
    Sidebar::addFilter(
        Text::_('COM_TIMECLOCK_SELECT_CUSTOMER'),
        'filter_customer',
        HTMLHelper::_('select.options', $options, 'value', 'text', $displayData->customer, true)
    );
    $options = ViewHelper::getUsersOptions();
    Sidebar::addFilter(
        Text::_('COM_TIMECLOCK_SELECT_USER_MANAGER'),
        'filter_user_manager_id',
        HTMLHelper::_('select.options', $options, 'value', 'text', $displayData->user_manager_id, true)
    );
    Sidebar::addFilter(
        Text::_('COM_TIMECLOCK_SELECT_PROJECT_MANAGER'),
        'filter_proj_manager_id',
        HTMLHelper::_('select.options', $options, 'value', 'text', $displayData->proj_manager_id, true)
    );
    Sidebar::addFilter(
        Text::_('COM_TIMECLOCK_SELECT_USER'),
        'filter_user_id',
        HTMLHelper::_('select.options', $options, 'value', 'text', $displayData->user_id, true)
    );
    $typeoptions = array(
        JHTML::_('select.option', "hours", Text::_("COM_TIMECLOCK_HOURS")),
        JHTML::_('select.option', "money", Text::_("COM_TIMECLOCK_MONEY")),
    );
?>
<div class="reportcontrol row">
    <div class="row">
        <div class="col-md-5 dates">
            <h4 class="page-header"><?php echo Text::_('COM_TIMECLOCK_REPORT_DATES');?></h4>
            <h5><?php echo Text::_('COM_TIMECLOCK_FROM');?></h5>
            <?php print HTMLHelper::_("calendar", $displayData->start, "start", "startDate", '%Y-%m-%d', array("class" => "small")); ?>
            <h5><?php echo Text::_('COM_TIMECLOCK_to');?></h5>
            <?php print HTMLHelper::_("calendar", $displayData->end, "end", "endDate", '%Y-%m-%d', array("class" => "small")); ?>
            <?php if ($displayData->datatype) { ?>
            <hr class="hr-condensed" />
            <h5>Show:</h5><?php print HTMLHelper::_('select.genericlist', $typeoptions, 'datatype', 'class="inputbox form-select small"', 'value', 'text', $displayData->datatype); ?>
            <?php } ?>
            <hr class="hr-condensed d-md-none" />
            <button type="submit" class="d-md-none">Submit</button>
        </div>
        <div class="col-md-5 filters">
            <h4 class="page-header"><?php echo Text::_('JSEARCH_FILTER_LABEL');?></h4>
            <?php foreach (Sidebar::getFilters() as $filter) : ?>
                    <label for="<?php echo $filter['name']; ?>" class="element-invisible"><?php echo $filter['label']; ?></label>
                    <select name="<?php echo $filter['name']; ?>" id="<?php echo $filter['name']; ?>" class="small form-select" onchange="this.form.submit()">
                            <?php if (!$filter['noDefault']) : ?>
                                    <option value=""><?php echo $filter['label']; ?></option>
                            <?php endif; ?>
                            <?php echo $filter['options']; ?>
                    </select>
            <?php endforeach; ?>
            <hr class="hr-condensed d-md-none" />
        </div>
        <div class="col-md-2 controls">
            <h4 class="page-header"><?php echo Text::_('COM_TIMECLOCK_CONTROLS');?></h4>
            <button class="nonzero" type="button" onclick="Report.toggleZero();" style="display: none;"><?php print Text::_("COM_TIMECLOCK_SHOW_ZERO"); ?></button>
            <button class="zero" type="button" onclick="Report.toggleZero();" style="display: none;"><?php print Text::_("COM_TIMECLOCK_HIDE_ZERO"); ?></button>
            <button class="nonempty" type="button" onclick="Report.toggleEmpty();" style="display: none;"><?php print Text::_("COM_TIMECLOCK_SHOW_EMPTY"); ?></button>
            <button class="empty" type="button" onclick="Report.toggleEmpty();" style="display: none;"><?php print Text::_("COM_TIMECLOCK_HIDE_EMPTY"); ?></button>
            <hr class="hr-condensed d-md-none" />
        </div>
    </div>
    <div class="d-none d-lg-block">
        <button type="submit">Submit</button>
    </div>
</div>
