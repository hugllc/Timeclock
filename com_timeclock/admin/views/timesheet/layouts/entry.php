<?php
$user       = JFactory::getUser();
$canCreate  = $user->authorise('core.create',     'com_timeclock');
$canEdit    = $user->authorise('core.edit',       'com_timeclock');
$canEditOwn = $user->authorise('core.edit.own',   'com_timeclock') && $displayData["data"]->created_by == $user->id;
$canChange  = $user->authorise('core.edit.state', 'com_timeclock');
$name       = empty($displayData["data"]->user) ? $displayData["data"]->user_id : $displayData["data"]->user;
?>
                <tr class="row<?php echo $displayData["index"] % 2; ?>" sortable-group-id="<?php echo $displayData["data"]->timesheet_id?>">
                    <td class="center">
                        <?php echo JHtml::_('grid.id', $displayData["index"], $displayData["data"]->timesheet_id, 0, "id"); ?>
                    </td>
                    <td class="nowrap has-context">
                        <div class="pull-left hasTooltip" title="<?php print $displayData["data"]->notes; ?>">
                            <?php if ($canEdit || $canEditOwn) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_timeclock&controller=timesheet&task=edit&id='.(int) $displayData["data"]->timesheet_id); ?>">
                                <?php echo $name; ?></a>
                            <?php else : ?>
                                <?php echo $name; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="center">
                        <?php echo $displayData["data"]->worked; ?>
                    </td>
                    <td class="center hidden-phone">
                        <?php echo $displayData["data"]->hours; ?>
                    </td>
                    <td class="center hidden-phone">
                        <?php echo $displayData["data"]->project; ?>
                    </td>
                    <td class="nowrap center hidden-phone">
                        <?php echo $displayData["data"]->author; ?>
                    </td>
                    <td class="center hidden-phone">
                        <?php echo $displayData["data"]->timesheet_id; ?>
                    </td>
                </tr>
