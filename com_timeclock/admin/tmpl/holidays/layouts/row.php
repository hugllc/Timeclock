<?php

use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

$user       = Factory::getUser();
$canCreate  = $user->authorise('core.create',     'com_timeclock');
$canEdit    = $user->authorise('core.edit',       'com_timeclock');
$canEditOwn = $user->authorise('core.edit.own',   'com_timeclock') && $displayData["data"]->created_by == $user->id;
$canChange  = $user->authorise('core.edit.state', 'com_timeclock');
$name       = substr($displayData["data"]->notes, 0, 60);
?>
                <tr class="row<?php echo $displayData["index"] % 2; ?>" sortable-group-id="<?php echo $displayData["data"]->timesheet_id?>">
                    <td class="text-center">
                        <?php echo HTMLHelper::_('grid.id', $displayData["index"], $displayData["data"]->timesheet_id, false, "timesheet_id", 'cb'); ?>
                    </td>
                    <td class="nowrap has-context">
                        <div class="pull-left hasTooltip" title="<?php print $displayData["data"]->notes; ?>">
                            <?php if ($canEdit || $canEditOwn): ?>
                                <a href="<?php echo Route::_('index.php?option=com_timeclock&task=holiday.edit&timesheet_id='.(int) $displayData["data"]->timesheet_id); ?>">
                                <?php echo $name; ?></a>
                            <?php else : ?>
                                <?php echo $name; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="text-center">
                        <?php echo $displayData["data"]->worked; ?>
                    </td>
                    <td class="text-center hidden-phone">
                        <?php echo $displayData["data"]->hours; ?>
                    </td>
                    <td class="text-center hidden-phone">
                        <?php echo $displayData["data"]->project; ?>
                    </td>
                    <td class="nowrap text-center hidden-phone">
                        <?php echo $displayData["data"]->author; ?>
                    </td>
                    <td class="text-center hidden-phone">
                        <?php echo $displayData["data"]->timesheet_id; ?>
                    </td>
                </tr>
