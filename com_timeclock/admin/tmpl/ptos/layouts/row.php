<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

$user       = Factory::getUser();
$accrual    = ($displayData["data"]->type == "ACCRUAL");
$canCreate  = $user->authorise('core.create',     'com_timeclock');
$canEdit    = $user->authorise('core.edit',       'com_timeclock');
$canCheckin = $user->authorise('core.manage',     'com_checkin');
$canEditOwn = $user->authorise('core.edit.own',   'com_timeclock') && ($displayData["data"]->created_by == $user->id);
$canChange  = $user->authorise('core.edit.state', 'com_timeclock') && $canCheckin;
$desc       = $displayData["data"]->notes;
$desc       = ($accrual) ? $desc."<br /><strong>".Text::_("COM_TIMECLOCK_AUTOMATIC_ACCRUAL_NO_EDIT")."</strong>" : $desc;
?>
                <tr class="row<?php echo $displayData["index"] % 2; ?>" sortable-group-id="<?php echo $displayData["data"]->pto_id?>">
                    <td class="text-center">
                        <?php if ($canEdit || $canEditOwn): ?>
                            <?php echo HTMLHelper::_('grid.id', $displayData["index"], $displayData["data"]->pto_id, false, "pto_id", 'cb'); ?>
                        <?php endif; ?>
                    </td>
                    <td class="nowrap has-context">
                        <div class="pull-left" title="<?php print $desc; ?>">
                            <?php if ($canEdit || $canEditOwn): ?>
                                <a href="<?php echo Route::_('index.php?option=com_timeclock&task=pto.edit&pto_id='.(int) $displayData["data"]->pto_id); ?>">
                                <?php echo $displayData["data"]->name; ?></a>
                            <?php else : ?>
                                <?php echo $displayData["data"]->name; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="text-center hidden-phone">
                        <?php echo $displayData["data"]->hours; ?>
                    </td>
                    <td class="text-center hidden-phone">
                        <?php echo $displayData["data"]->type; ?>
                    </td>
                    <td class="text-center hidden-phone">
                        <?php echo $displayData["data"]->author; ?>
                    </td>
                    <td class="text-center hidden-phone">
                        <?php echo $displayData["data"]->valid_from; ?>
                    </td>
                    <td class="text-center hidden-phone">
                        <?php echo $displayData["data"]->valid_to; ?>
                    </td>
                    <td class="text-center hidden-phone">
                        <?php echo $displayData["data"]->pto_id; ?>
                    </td>
                </tr>
