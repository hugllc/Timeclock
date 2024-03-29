<?php

use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

$user        = Factory::getUser();
$canCreate   = $user->authorise('core.create',     'com_timeclock');
$canEdit     = $user->authorise('core.edit',       'com_timeclock');
$canCheckin  = $user->authorise('core.manage',     'com_checkin') || $displayData["data"]->checked_out == $user->id || $displayData["data"]->checked_out == 0;
$canEditOwn  = $user->authorise('core.edit.own',   'com_timeclock') && $displayData["data"]->created_by == $user->id;
$canChange   = $user->authorise('core.edit.state', 'com_timeclock') && $canCheckin;
$checkedOut = $displayData["data"]->checked_out && ($displayData["data"]->checked_out != $user->id);
?>
                <tr class="row<?php echo $displayData["index"] % 2; ?>" sortable-group-id="<?php echo $displayData["data"]->customer_id?>">
                    <td class="text-center">
                        <?php echo HTMLHelper::_('grid.id', $displayData["index"], $displayData["data"]->customer_id, false, "customer_id", 'cb', $displayData["data"]->name); ?>
                    </td>
                    <td class="text-center">
                        <?php echo HTMLHelper::_('jgrid.published', $displayData["data"]->published, $displayData["index"], 'customers.', $canChange, 'cb'); ?>
                    </td>
                    <td class="nowrap has-context">
                        <div class="pull-left">
                            <?php if ($displayData["data"]->checked_out) : ?>
                                <?php echo HTMLHelper::_('jgrid.checkedout', $displayData["index"], $displayData["data"]->checked_out, $displayData["data"]->checked_out_time, 'customer.', $canCheckin); ?>
                            <?php endif; ?>
                            <?php if (($canEdit || $canEditOwn) && !$checkedOut): ?>
                                <a href="<?php echo Route::_('index.php?option=com_timeclock&task=customer.edit&customer_id='.(int) $displayData["data"]->customer_id); ?>">
                                <?php echo $displayData["data"]->company; ?></a>
                            <?php else : ?>
                                <?php echo $displayData["data"]->company; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="hidden-phone">
                        <?php echo $displayData["data"]->name; ?>
                    </td>
                    <td class="text-center hidden-phone">
                        <?php echo ($displayData["data"]->bill_pto) ? "Yes" : "No"; ?>
                    </td>
                    <td class="hidden-phone">
                        <?php echo $displayData["data"]->notes; ?>
                    </td>
                    <td class="text-center hidden-phone">
                        <?php echo $displayData["data"]->customer_id; ?>
                    </td>
                </tr>
