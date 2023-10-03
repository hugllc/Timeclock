<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Factory;
defined('_JEXEC') or die(); 

HTMLHelper::_("bootstrap.tooltip", ".hasTooltip", ["trigger" => "hover"]);

$notes    = new FileLayout('notes', dirname(dirname(__DIR__)).'/layouts');

$user_id   = $displayData->id;
$name      = empty($displayData->name) ? "User ".$user_id : $displayData->name;
$class     = "user-".$user_id;
$worked    = 0;
$pto       = 0;
$holiday   = 0;
$subtotal  = 0;
$overtime  = 0;
$volunteer = 0;
for ($w = 0; $w < $displayData->payperiod->subtotals; $w++) {
    if (isset($displayData->data[$w])) {
        $data = (object)$displayData->data[$w];
        $worked    += $data->worked;
        $pto       += $data->pto;
        $holiday   += $data->holiday;
        $subtotal  += $data->subtotal;
        $overtime  += $data->overtime;
        $volunteer += $data->volunteer;
    }
}
$overtimeclass = ($overtime > 0) ? "highlight" : "";
$total = $subtotal + $overtime;
$timesheeturl = Route::_('index.php?&option=com_timeclock&view=timesheet&id='.$user_id."&date=".$displayData->payperiod->start);
if (!empty($displayData->error)) {
    $errorClass   = "error hasTooltip";
    $errorTooltip = ' title="'.$displayData->error.'"';
} else {
    $errorClass = "";
    $errorTooltip = "";
}

$body = $notes->render((object)array(
    "user_id" => $user_id."-modal",
    "name"    => $name,
    "data"    => $displayData->notes,
));

$modalId = 'modal-notes-'.$user_id;
$modalParams = array();
$modalParams['title']      = "Notes for ".$name;
$modalParams['height']     = '100%';
$modalParams['width']      = '100%';
$modalParams['modalWidth'] = 60;
$modalParams['closeButton'] = true;

?>
            <tr class="employee <?php print $errorClass; ?>"<?php print $errorTooltip; ?>>
                <td>
                    <?php echo HTMLHelper::_('bootstrap.renderModal', $modalId, $modalParams, $body); ?>
                    <div>
                        <button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#<?php echo $modalId; ?>"><?php print $name ?></button>
                    </div>
                </td>
                <td class="complete <?php print $displayData->done ? "yes" : "no"; ?>">
                    <a href="<?php print $timesheeturl; ?>" class="hasTooltip" title="<?php print Text::_("COM_TIMECLOCK_CLICK_TO_VIEW_TIMESHEET"); ?>">
                    <?php print $displayData->done ? Text::_("JYES") : Jtext::_("JNO"); ?>
                    </a>
                </td>
                <td class="approved <?php print $displayData->approved ? "yes" : "no"; ?> hasToolTip" title="<?php print $displayData->manager->name; ?>">
                    <?php print $displayData->approved ? Text::_("JYES") : Jtext::_("JNO"); ?>
                </td>
                <td class="hours">
                    <span id="hours-<?php print $user_id; ?>" class="worked <?php print $class;?>"><?php print $worked; ?></span>
                </td>
                <td class="hours">
                    <span id="pto-<?php print $user_id; ?>" class="pto <?php print $class;?>"><?php print $pto; ?></span>
                </td>
                <td class="hours">
                    <span id="volunteer-<?php print $user_id; ?>" class="volunteer <?php print $class;?>"><?php print $volunteer; ?></span>
                </td>
                <td class="hours">
                    <span id="holiday-<?php print $user_id; ?>" class="holiday <?php print $class;?>"><?php print $holiday; ?></span>
                </td>
                <td class="subtotal">
                    <span id="subtotal-<?php print $user_id; ?>" class="subtotal-<?php print $user_id; ?>"><?php print $subtotal; ?></span>
                </td>
                <td class="overtime <?php print $overtimeclass; ?>">
                    <span id="overtime-<?php print $user_id; ?>" class="overtime overtime-<?php print $user_id; ?>"><?php print $overtime; ?></span>
                </td>
                <td class="total">
                    <span id="total-<?php print $user_id?>"><?php print $total; ?></span>
                </td>
            </tr>
            