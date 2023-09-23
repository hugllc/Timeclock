<?php

defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;
use HUGLLC\Component\Timeclock\Administrator\Helper\TimeclockHelper;
use HUGLLC\Component\Timeclock\Administrator\Helper\ViewHelper;
?>
<form action="<?php echo Route::_("index.php?option=com_timeclock&controller=timesheet"); ?>" method="post" id="adminForm" name="adminForm">
    <div class="row">
        <div class="col-lg-9">
            <?php echo $this->form->renderFieldset('main'); ?>
            <?php if ($this->params->get("wCompEnable")): ?>
                <?php
                $codes = TimeclockHelper::getWCompCodes();
                for ($i = 1; $i <= 6; $i++) {
                    $field = new stdClass();
                    $code = isset($this->item->{"wcCode".$i}) ? (int)$this->item->{"wcCode".$i} : 0;
                    if ($code == 0) {
                        $field->label = Text::_("COM_TIMECLOCK_CODE_NOT_ENABLED");
                    } else if (!isset($codes[$code]) || empty($codes[$code])) {
                        $field->label = Text::_("COM_TIMECLOCK_CODE_NOT_DEFINED");
                    } else {
                        $field->label = $codes[$code];
                    }
                    $id = '"jform_hours'.$i.'"';
                    $field->label = '<label id="'.$id.'-lbl" for="'.$id.'">'.$i.". ".$field->label.'</label>';
                    $field->input  = '<input id="'.$id.'" type="text" size="6" maxsize="6" class="" ';
                    $field->input .= 'name="jform[hours'.$i.']" value="'.(int)$this->item->{"hours".$i}.'" />';
                    $field->description = '<div id="'.$id.'-desc" ><small class="form-text">'.Text::_("COM_TIMECLOCK_HOURS_TIMESHEET_DESC").'</small></div>';
                    print ViewHelper::getFormField($field);
                }
                ?>
            <?php else: ?>
                <?php echo $this->form->renderFieldset('hours'); ?>
            <?php endif ?>
        </div>
        <div class="col-lg-3">
            <?php echo $this->form->renderFieldset('sidebar'); ?>
        </div>
    </div>
    <?php echo $this->form->renderFieldset('hidden'); ?>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="timesheet_id" value="<?php echo $this->item->timesheet_id ?>" />
    <?php print HTMLHelper::_("form.token"); ?>
</form>
