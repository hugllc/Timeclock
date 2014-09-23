<?php 
    defined('_JEXEC') or die('Restricted access'); 
    $user_id = $displayData->id;
    $name  = empty($displayData->name) ? "User ".$displayData->user_id : $displayData->name;
    $class    = "user-".$user_id;
    $worked   = 0;
    $pto      = 0;
    $holiday  = 0;
    $subtotal = 0;
    $overtime = 0;
    for ($w = 0; $w < $displayData->payperiod->subtotals; $w++) {
        if (isset($displayData->data[$w])) {
            $data = (object)$displayData->data[$w];
            $worked   += $data->worked;
            $pto      += $data->pto;
            $holiday  += $data->holiday;
            $subtotal += $data->subtotal;
            $overtime += $data->overtime;
        }
    }
    $total = $subtotal + $overtime;
    $timesheeturl = JRoute::_('index.php?&option=com_timeclock&controller=timesheet&id='.$user_id);
?>
            <tr class="employee <?php print $displayData->rowClass; ?>">
                <td>
                    <a class="modal" href="#notes-<?php print $user_id; ?>" rel="{onOpen : function(){ jQuery('#sbox-content div').show(); }}"><?php print $name ?></a>
                </td>
                <td class="complete <?php print $displayData->done ? "yes" : "no"; ?>">
                    <a href="<?php print $timesheeturl; ?>">
                    <?php print $displayData->done ? JText::_("JYES") : Jtext::_("JNO"); ?>
                    </a>
                </td>
                <td class="hours">
                    <span id="hours-<?php print $user_id; ?>" class="worked <?php print $class;?>"><?php print $worked; ?></span>
                </td>
                <td class="hours">
                    <span id="pto-<?php print $user_id; ?>" class="pto <?php print $class;?>"><?php print $pto; ?></span>
                </td>
                <td class="hours">
                    <span id="holiday-<?php print $user_id; ?>" class="holiday <?php print $class;?>"><?php print $holiday; ?></span>
                </td>
                <td class="subtotal">
                    <span id="subtotal-<?php print $user_id; ?>" class="subtotal-<?php print $user_id; ?>"><?php print $subtotal; ?></span>
                </td>
                <td class="overtime">
                    <span id="overtime-<?php print $user_id; ?>" class="overtime overtime-<?php print $user_id; ?>"><?php print $overtime; ?></span>
                </td>
                <td class="total">
                    <span id="total-<?php print $user_id?>"><?php print $total; ?></span>
                </td>
            </tr>
            