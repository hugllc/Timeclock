<?php

use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

$user       = Factory::getUser();
$canCreate  = $user->authorise('core.create',     'com_timeclock');
$canEdit    = $user->authorise('core.edit',       'com_timeclock');
$canCheckin = $user->authorise('core.manage',     'com_checkin') || $displayData["data"]->checked_out == $userId || $displayData["data"]->checked_out == 0;
$canEditOwn = $user->authorise('core.edit.own',   'com_timeclock') && $displayData["data"]->created_by == $user->id;
$canChange  = $user->authorise('core.edit.state', 'com_timeclock') && $canCheckin;
$checkedOut = $displayData["data"]->checked_out && ($displayData["data"]->checked_out != $user->id);

?>
                <tr class="row<?php echo $displayData["index"] % 2; ?>" sortable-group-id="<?php echo $displayData["data"]->department_id?>">
                    <td class="center">
                        <?php echo HTMLHelper::_('grid.id', $displayData["index"], $displayData["data"]->department_id, false, "department_id", 'cb', $displayData["data"]->name); ?>
                    </td>
                    <td class="center">
                        <?php echo HTMLHelper::_('jgrid.published', $displayData["data"]->published, $displayData["index"], 'departments.', $canChange, 'cb'); ?>
                    </td>
                    <td class="nowrap has-context">
                        <div class="pull-left hasTooltip" title="<?php print $displayData["data"]->description; ?>">
                            <?php if ($displayData["data"]->checked_out) : ?>
                                <?php echo HTMLHelper::_('jgrid.checkedout', $displayData["index"], $displayData["data"]->checked_out, $displayData["data"]->checked_out_time, 'department.', $canCheckin); ?>
                            <?php endif; ?>
                            <?php if (($canEdit || $canEditOwn) && !$checkedOut): ?>
                                <a href="<?php echo Route::_('index.php?option=com_timeclock&controller=department&task=edit&department_id='.(int) $displayData["data"]->department_id); ?>">
                                <?php echo $displayData["data"]->name; ?></a>
                            <?php else : ?>
                                <?php echo $displayData["data"]->name; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="center hidden-phone">
                        <?php echo $displayData["data"]->manager; ?>
                    </td>
                    <td class="center hidden-phone">
                        <?php echo $displayData["data"]->department_id; ?>
                    </td>
                </tr>
