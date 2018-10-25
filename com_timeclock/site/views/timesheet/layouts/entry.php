<?php
defined('_JEXEC') or die;
$displayData->data = isset($displayData->data) ? (object)$displayData->data : new stdClass();
$max = (int)(isset($displayData->max_daily_hours) ? $displayData->max_daily_hours : 0);
$min = (int)(isset($displayData->min_daily_hours) ? $displayData->min_daily_hours : 0);
$ytdmax = (int)(isset($displayData->max_yearly_hours) ? $displayData->max_yearly_hours : 0);
$ytdhours = (int)(isset($displayData->hours_ytd) ? $displayData->hours_ytd : 0);
$left = ($ytdmax > 0) ? $ytdmax - $ytdhours : $max;
?>
    <h2><?php print $displayData->name; ?></h2>
    <fieldset id="addhours-<?php print $displayData->project_id; ?>" class="addhours">
    <div style="display: none;" class="alert"></div>
    <?php
        if ($displayData->type == "FLOATING_HOLIDAY") {
            print ' <div class="alert-info">'.JText::_("COM_TIMECLOCK_FLOATING_HOLIDAY_WARNING").'</div> ';
        }
    ?>
    <div class="controls"><?php
        if ($min != $max) {
            if ($min > 0) {
                print ' <span class="bold">'.JText::_("COM_TIMECLOCK_MIN").':</span> '.$max.' ';
            }
            if ($max > 0) {
                print ' <span class="bold">'.JText::_("COM_TIMECLOCK_MAX").':</span> '.$min.' ';
            }
        }
        if ($ytdmax > 0) {
            print ' <span class="bold">'.JText::_("COM_TIMECLOCK_YTD_LEFT").':</span> '.($ytdmax - $ytdhours).' ';
        }
        
    ?></div>
<?php
    $wcomp = (bool)$displayData->params->get("wCompEnable");
    if ($wcomp) {
        $codes = TimeclockHelpersTimeclock::getWCompCodes();
        $count = 6;
    } else {
        $codes = array(1 => "");
        $count = 1;
    }
    for ($i = 1; $i <= $count; $i++) {
        $name = "wcCode".$i;
        $code = isset($displayData->$name) ? (int)$displayData->$name : 0;
        if (($code == 0) && ($i > 1)) {
            continue;
        }
        $fname = "hours".$i;
        $hours = isset($displayData->data->$fname) ? $displayData->data->$fname : 0;

        $field = new stdClass();
        $name  = (empty($codes[$code])) ? JText::_("COM_TIMECLOCK_HOURS") : $codes[$code];
        $label = '<label id="'.$fname.'-lbl" for="'.$fname.'" class="required">';
        $star  = '<span class="star">&#160;*</span>';
        $field->label = $label.$name.$star."</label>";
        if (($min == $max) && ($min != 0)) {
            $field->input  = '<select class="span2 hours" ';
            $field->input .= 'name="hours'.$i.'" onchange="Addhours.validateHours(this);">';
            $field->input .= '<option value="0"'.(($hours == 0) ? ' selected="selected"' : '').'>0</option>';
            if ($left) {
                $field->input .= '<option value="'.$min.'"'.(($hours != 0) ? ' selected="selected"' : '').'>'.$min.'</option>';
            }
            $field->input .= '</select>';
        } else {
            $field->input  = '<input type="text" size="6" maxsize="6" class="span2 hours" ';
            $field->input .= 'name="hours'.$i.'" value="'.$hours.'" onblur="Addhours.validateHours(this);"/>';
        }
        print TimeclockHelpersView::getFormField($field);
    }
    $minimum = '<span class="minchars">'.sprintf(" ".JText::_('COM_TIMECLOCK_WORK_NOTES_MIN_CHARS'), $displayData->params->get("minNoteChars"))."</span>";
    $notes = array(
        "input" => '<textarea name="notes" id="notes" cols="80" rows="7" onblur="Addhours.validateNotes(this);">'.(isset($displayData->data->notes) ? $displayData->data->notes : "").'</textarea><br />'.$minimum,
        "label" => '<label id="notes-lbl" for="notes" class="hasTooltip" title="<strong>'.JText::_('COM_TIMECLOCK_NOTES').'</strong><br />'.JText::_('COM_TIMECLOCK_WORK_NOTES_HELP').'">'.JText::_('COM_TIMECLOCK_NOTES').'</label>'
    );
    print TimeclockHelpersView::getFormField((object)$notes);
?>
        <button type="button" name="apply" onClick="Addhours.submitform('apply');">Save</button>
        <button type="button" name="save" onClick="Addhours.submitform('save');">Save &amp; Close</button>
        <input type="hidden" name="created" value="<?php print isset($displayData->data->created) ? $displayData->data->created : 0; ?>" />
        <input type="hidden" name="created_by" value="<?php print isset($displayData->data->created_by) ? $displayData->data->created_by : -1; ?>" />
        <input type="hidden" name="project_id" value="<?php print $displayData->project_id; ?>" />
        <input type="hidden" name="pmin" value="<?php print $max; ?>" />
        <input type="hidden" name="pmax" value="<?php print $min; ?>" />
        <input type="hidden" name="ytdhours" value="<?php print $ytdhours; ?>" />
        <input type="hidden" name="ytdmax" value="<?php print $ytdmax; ?>" />
        <input type="hidden" name="timesheet_id" value="<?php print isset($displayData->data->timesheet_id) ? $displayData->data->timesheet_id : ""; ?>" />
    </fieldset>
