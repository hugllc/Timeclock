<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

    defined('_JEXEC') or die(); 
?>
            <tr class="<?php print strtolower($displayData->type);?><?php print ($displayData->mine == 0) ? " alert alert-info" : ""; ?>">
                <td class="project hasTooltip" title="<?php print Text::_($displayData->description); ?>">
                    <?php print Text::_($displayData->name); ?>
                </td>
<?php
    $d = 1;
    $split = empty($displayData->payperiod->splitdays) ? 31 : $displayData->payperiod->splitdays;
    foreach ($displayData->payperiod->dates as $date => $employed):
        $hours = isset($displayData->data[$date]) ? $displayData->data[$date]->hours : 0;
        $timeentry = $employed && $displayData->mine && (!$displayData->nohours || (!$displayData->nonewhours && ($hours > 0)));
        
        if ($timeentry) {
            $tipTitle           = Text::_("COM_TIMECLOCK_ADD_HOURS");
            $tip                = "for ".$displayData->name." on ".JHTML::_('date', $date, Text::_("DATE_FORMAT_LC1"));
            $link = Route::_('index.php?option=com_timeclock&controller=timesheet&task=addhours&layout=modal&tmpl=component&project_id='.$displayData->project_id.'&date='.$date);
        } else {
            $tipTitle = Text::_("COM_TIMECLOCK_NO_HOURS");
            if ($employed) {
                $tip = Text::_("COM_TIMECLOCK_PROJECT_NO_AUTH");
            } else {
                $tip = Text::_("COM_TIMECLOCK_NO_HOURS_LOCKED");
            }
        }
        if ($hours > 0) {
            $tipTitle = Text::_("COM_TIMECLOCK_NOTES");
            $tip = $displayData->data[$date]->notes;
        }
        $tooltip = "<strong>".$tipTitle."</strong><br />".$tip;
        $sub = (int)(($d - 1) / $split) + 1;
        $class = "date-$date proj-".$sub."-".$displayData->project_id." proj-".$displayData->project_id;
        $modalId = 'modal-'.str_replace(" ", "-", $date."-".$displayData->project_id."-".$sub);
        $modalParams = array();
        $modalParams['title']      = sprintf(Text::_("COM_TIMECLOCK_ADD_HOURS_TITLE"), $displayData->user->name, HTMLHelper::_("date", $this->date));
        $modalParams['url']        = $link;
        $modalParams['height']     = '100%';
        $modalParams['width']      = '100%';
        $modalParams['bodyHeight'] = 80;
        $modalParams['modalWidth'] = 60;
        $modalParams['closeButton'] = false;
        $modalParams['footer']      = '<button type="button" name="apply" onClick="Addhours.submitform(\'apply\');">Save</button>
        <button type="button" name="save" data-dismiss="modal" onClick="Addhours.submitform(\'save\');">Save &amp; Close</button>
        <button type="button" name="close" onClick="window.document.getElementById("'.$modalId.'").close();">Close</button>
';
?>
                <td class="hours">
                    <?php if ($timeentry) echo HTMLHelper::_('bootstrap.renderModal', $modalId, $modalParams); ?>
                    <div class="hasTooltip" title="<?php print $tooltip; ?>">
                    <?php if ($timeentry) : ?>
                    <button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#<?php echo $modalId; ?>">
                    <?php endif; ?>
                    <span id="hours-<?php print $displayData->project_id."-".$date; ?>" class="<?php print $class;?> ">-</span>
                    <?php if ($timeentry) : ?>
                    </button>
                    <?php endif; ?>
                    </div>
                </td>
    <?php 
        if (($displayData->payperiod->splitdays != 0) && (($d++ % $displayData->payperiod->splitdays) == 0)) : 
            $class = "subtotal-proj-".$sub." subtotal-date-".$date;
            $tooltip = sprintf(Text::_("COM_TIMECLOCK_SUBTOTAL_FOR"), $sub, $displayData->name);
    ?>
                <td class="subtotal">
                    <span class="subtotal-proj-<?php print $sub."-".$displayData->project_id; ?> <?php print $class; ?> hasTooltip" title="<?php print $tooltip; ?>">-</span>
                </td>
    <?php endif; ?>
<?php endforeach;?>
                <td class="subtotal">
                    <span class="total-proj-<?php print $displayData->project_id; ?> total-proj hasTooltip" title="<?php printf(Text::_("COM_TIMECLOCK_TOTAL_FOR"), $displayData->name); ?>">-</span>
                </td>
            </tr>
            