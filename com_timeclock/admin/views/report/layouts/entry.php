<?php
$user       = JFactory::getUser();
$canCreate  = $user->authorise('core.create',     'com_timeclock');
$canEdit    = $user->authorise('core.edit',       'com_timeclock');
$canCheckin = $user->authorise('core.manage',     'com_checkin') || $displayData["data"]->checked_out == $userId || $displayData["data"]->checked_out == 0;
$canEditOwn = $user->authorise('core.edit.own',   'com_timeclock') && $displayData["data"]->created_by == $user->id;
$canChange  = $user->authorise('core.edit.state', 'com_timeclock') && $canCheckin;
$selectname = "export".$displayData["index"];
?>
                <tr class="row<?php echo $displayData["index"] % 2; ?>" sortable-group-id="<?php echo $displayData["data"]->report_id?>">
                    <td class="center">
                        <?php echo JHtml::_('grid.id', $displayData["index"], $displayData["data"]->report_id, $displayData["data"]->checked_out, "id"); ?>
                    </td>
                    <td class="center">
                        <div class="btn-group">
                            <?php echo JHtml::_('jgrid.published', $displayData["data"]->published, $displayData["index"], 'report.', $canChange, 'cb'); ?>
                        </div>
                    </td>
                    <td class="nowrap has-context">
                        <div class="pull-left hasTooltip" title="<?php print $displayData["data"]->description; ?>">
                            <?php if ($displayData["data"]->checked_out) : ?>
                                <?php echo JHtml::_('jgrid.checkedout', $displayData["index"], $displayData["data"]->checked_out, $displayData["data"]->checked_out_time, 'report.', $canCheckin); ?>
                            <?php endif; ?>
                            <?php if (($canEdit || $canEditOwn) && !($displayData["data"]->checked_out)): ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_timeclock&controller=report&task=edit&id='.(int) $displayData["data"]->report_id); ?>">
                                <?php echo $displayData["data"]->name; ?></a>
                            <?php else : ?>
                                <?php echo $displayData["data"]->name; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="center">
                        <select name="<?php print $selectname; ?>" class="narrow" onChange="exportReport(this, <?php print $displayData["data"]->report_id; ?>, this.value, '<?php print $displayData["data"]->type; ?>');">
                            <option value=""><?php print JText::_("COM_TIMECLOCK_SELECT_FORMAT"); ?></option>
                            <option value="csv">CSV</option>
                            <option value="ehtml">HTML</option>
                            <option value="xlsx">Excel 2007</option>
                        </select>
                    </td>
                    <td class="center hidden-phone">
                        <?php echo $displayData["data"]->type; ?>
                    </td>
                    <td class="center hidden-phone">
                        <?php echo $displayData["data"]->author; ?>
                    </td>
                    <td class="center hidden-phone">
                        <?php echo $displayData["data"]->startDate; ?>
                    </td>
                    <td class="center hidden-phone">
                        <?php echo $displayData["data"]->endDate; ?>
                    </td>
                    <td class="center hidden-phone">
                        <?php echo $displayData["data"]->report_id; ?>
                    </td>
                </tr>
