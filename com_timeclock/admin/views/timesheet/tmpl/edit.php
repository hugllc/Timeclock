<?php

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('formbehavior.chosen', 'select:not(.plain)');
JHTML::script(Juri::base()."components/com_timeclock/js/edit.js");
?>

<script type="text/javascript">
    Joomla.submitbutton = function (task)
    {
        Joomla.Timeclock.submitbutton(task);
    }
</script>
<form action="index.php?option=com_timeclock&controller=timesheet" method="post" id="adminForm" name="adminForm">
    <div class="row-fluid">
        <div class="span9">
            <fieldset class="form-horizontal">
<?php 
    $field = $this->form->getField("worked");
    $field->setValue($this->data->worked);
    print TimeclockHelpersView::getFormField($field);
    if ($this->params->get("wCompEnable")) {
        $codes = TimeclockHelpersTimeclock::getWCompCodes();
        for ($i = 1; $i <= 6; $i++) {
            $field = new stdClass();
            $code = isset($this->data->{"wcCode".$i}) ? (int)$this->data->{"wcCode".$i} : 0;
            if ($code == 0) {
                $field->label = Text::_("COM_TIMECLOCK_CODE_NOT_ENABLED");
            } else if (!isset($codes[$code]) || empty($codes[$code])) {
                $field->label = Text::_("COM_TIMECLOCK_CODE_NOT_DEFINED");
            } else {
                $field->label = $codes[$code];
            }
            $field->label = "$i. ".$field->label;
            $field->input  = '<input type="text" size="6" maxsize="6" class="span2" ';
            $field->input .= 'name="hours'.$i.'" value="'.$this->data->{"hours".$i}.'" />';
            print TimeclockHelpersView::getFormField($field);
        }
    } else {
        print TimeclockHelpersView::getFormSetH("hours", $this->form, $this->data);
    }
    
    $field = $this->form->getField("notes");
    $field->setValue($this->data->notes);
    print TimeclockHelpersView::getFormField($field);
?>
            </fieldset>
        </div>
        <div class="span3">
            <fieldset class="form-vertical">
<?php  
print TimeclockHelpersView::getFormSetV("sidebar", $this->form, $this->data);
?>
            </fieldset>
        </div>
    </div>
    <input type="hidden" name="timesheet_id" value="<?php print $this->data->timesheet_id; ?>" />
    <input type="hidden" name="id" value="<?php print $this->data->timesheet_id; ?>" />
    <input type="hidden" name="created" value="<?php print $this->data->created; ?>" />
    <input type="hidden" name="created_by" value="<?php print $this->data->created_by; ?>" />
    <input type="hidden" name="task" value="" />
    <?php print JHTML::_("form.token"); ?>
</form>
