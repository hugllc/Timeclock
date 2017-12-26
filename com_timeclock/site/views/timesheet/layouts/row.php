<?php 
    defined('_JEXEC') or die('Restricted access'); 
?>
            <tr class="<?php print strtolower($displayData->type);?><?php print ($displayData->mine == 0) ? " alert alert-info" : ""; ?>">
                <td class="project hasTooltip" title="<?php print JText::_($displayData->description); ?>">
                    <?php print JText::_($displayData->name); ?>
                </td>
<?php
    $d = 1;
    $split = empty($displayData->payperiod->splitdays) ? 31 : $displayData->payperiod->splitdays;
    foreach ($displayData->payperiod->dates as $date => $employed):
        $hours = isset($displayData->data[$date]) ? $displayData->data[$date]->hours : 0;
        $timeentry = $employed && $displayData->mine && (!$displayData->nohours || (!$displayData->nonewhours && ($hours > 0)));
        
        if ($timeentry) {
            $tipTitle           = JText::_("COM_TIMECLOCK_ADD_HOURS");
            $tip                = "for ".$displayData->name." on ".JHTML::_('date', $date, JText::_("DATE_FORMAT_LC1"));
            $link = JRoute::_('index.php?option=com_timeclock&controller=timesheet&task=addhours&layout=modal&tmpl=component&project_id='.$displayData->project_id.'&date='.$date);
        } else {
            $tipTitle = JText::_("COM_TIMECLOCK_NO_HOURS");
            if ($employed) {
                $tip = JText::_("COM_TIMECLOCK_PROJECT_NO_AUTH");
            } else {
                $tip = JText::_("COM_TIMECLOCK_NO_HOURS_LOCKED");
            }
        }
        if ($hours > 0) {
            $tipTitle = JText::_("COM_TIMECLOCK_NOTES");
            $tip = $displayData->data[$date]->notes;
        }
        $tooltip = "<strong>".$tipTitle."</strong><br />".$tip;
        $sub = (int)(($d - 1) / $split) + 1;
        $class = "date-$date proj-".$sub."-".$displayData->project_id." proj-".$displayData->project_id;
?>
                <td class="hours hasTooltip" title="<?php print $tooltip; ?>">
                    <?php if ($timeentry) : ?>
                    <a href="<?php print $link; ?>" class="modal" rel="{onOpen : function(){ Addhours.reset(<?php print $displayData->project_id; ?>, '<?php print $date; ?>'); }}">
                    <?php endif; ?>
                    <span id="hours-<?php print $displayData->project_id."-".$date; ?>" class="<?php print $class;?> ">-</span>
                    <?php if ($timeentry) : ?>
                    </a>
                    <?php endif; ?>
                </td>
    <?php 
        if (($displayData->payperiod->splitdays != 0) && (($d++ % $displayData->payperiod->splitdays) == 0)) : 
            $class = "subtotal-proj-".$sub." subtotal-date-".$date;
            $tooltip = sprintf(JText::_("COM_TIMECLOCK_SUBTOTAL_FOR"), $sub, $displayData->name);
    ?>
                <td class="subtotal">
                    <span class="subtotal-proj-<?php print $sub."-".$displayData->project_id; ?> <?php print $class; ?> hasTooltip" title="<?php print $tooltip; ?>">-</span>
                </td>
    <?php endif; ?>
<?php endforeach;?>
                <td class="subtotal">
                    <span class="total-proj-<?php print $displayData->project_id; ?> total-proj hasTooltip" title="<?php printf(JText::_("COM_TIMECLOCK_TOTAL_FOR"), $displayData->name); ?>">-</span>
                </td>
            </tr>
            