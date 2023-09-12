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
?>
                <tr class="row<?php echo $displayData["index"] % 2; ?>" sortable-group-id="<?php echo $displayData["data"]->customer_id?>">
                    <td class="center">
                        <?php echo HTMLHelper::_('grid.id', $displayData["index"], $displayData["data"]->customer_id, $displayData["data"]->checked_out, "id"); ?>
                    </td>
                    <td class="center">
                        <div class="btn-group">
                            <?php echo HTMLHelper::_('jgrid.published', $displayData["data"]->published, $displayData["index"], 'customer.', $canChange, 'cb'); ?>
                        </div>
                    </td>
                    <td class="nowrap has-context">
                        <div class="pull-left hasTooltip" title="<?php print $displayData["data"]->notes; ?>">
                            <?php if ($displayData["data"]->checked_out) : ?>
                                <?php echo HTMLHelper::_('jgrid.checkedout', $displayData["index"], $displayData["data"]->checked_out, $displayData["data"]->checked_out_time, 'customer.', $canCheckin); ?>
                            <?php endif; ?>
                            <?php if (($canEdit || $canEditOwn) && !($displayData["data"]->checked_out)): ?>
                                <a href="<?php echo Route::_('index.php?option=com_timeclock&controller=customer&id='.(int) $displayData["data"]->customer_id); ?>">
                                <?php echo $displayData["data"]->company; ?></a>
                            <?php else : ?>
                                <?php echo $displayData["data"]->company; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="center hidden-phone">
                        <?php echo $displayData["data"]->name; ?>
                    </td>
                    <td class="center hidden-phone">
                        <?php echo $displayData["data"]->contact; ?>
                    </td>
                    <td class="center hidden-phone">
                        <?php echo ($displayData["data"]->bill_pto) ? "Yes" : "No"; ?>
                    </td>
                    <td class="center hidden-phone">
                        <?php echo $displayData["data"]->notes; ?>
                    </td>
                    <td class="center hidden-phone">
                        <?php echo $displayData["data"]->customer_id; ?>
                    </td>
                </tr>
