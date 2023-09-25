<?php

use Joomla\CMS\Language\Text;
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
                <tr class="row<?php echo $displayData["index"] % 2; ?>" sortable-group-id="<?php echo $displayData["data"]->project_id?>">
                    <td class="text-center">
                        <?php echo HTMLHelper::_('grid.id', $displayData["index"], $displayData["data"]->project_id, false, "project_id", 'cb', $displayData["data"]->name); ?>
                    </td>
                    <td class="text-center">
                        <?php echo HTMLHelper::_('jgrid.published', $displayData["data"]->published, $displayData["index"], 'projects.', $canChange, 'cb'); ?>
                    </td>
                    <td class="nowrap has-context">
                        <div class="pull-left hasTooltip" title="<?php print Text::_($displayData["data"]->description); ?>">
                            <?php if ($displayData["data"]->checked_out) : ?>
                                <?php echo HTMLHelper::_('jgrid.checkedout', $displayData["index"], $displayData["data"]->checked_out, $displayData["data"]->checked_out_time, 'project.', $canCheckin); ?>
                            <?php endif; ?>
                            <?php if (($canEdit || $canEditOwn) && !$checkedOut): ?>
                                <a href="<?php echo Route::_('index.php?option=com_timeclock&task=project.edit&project_id='.(int) $displayData["data"]->project_id); ?>">
                                <?php echo Text::_($displayData["data"]->name); ?></a>
                            <?php else : ?>
                                <?php echo Text::_($displayData["data"]->name); ?>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="hidden-phone">
                        <?php echo $displayData["data"]->manager; ?>
                    </td>
                    <td class="text-center hidden-phone">
                        <?php echo $displayData["data"]->type; ?>
                    </td>
                    <td class="text-center hidden-phone">
                        <?php echo ($displayData["data"]->type == "CATEGORY") ? '-' : Text::_($displayData["data"]->category); ?>
                    </td>
                    <td class="text-center hidden-phone">
                        <?php echo $displayData["data"]->project_id; ?>
                    </td>
                </tr>
