<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

$user       = Factory::getUser();
$canCreate  = $user->authorise('core.create',     'com_timeclock');
$canEdit    = $user->authorise('core.edit',       'com_timeclock');
$canCheckin = $user->authorise('core.manage',     'com_checkin');
$canEditOwn = $user->authorise('core.edit.own',   'com_timeclock') && $displayData["data"]->created_by == $user->id;
$canChange  = $user->authorise('core.edit.state', 'com_timeclock') && $canCheckin;
$selectname = "export".$displayData["index"];
?>
                <tr class="row<?php echo $displayData["index"] % 2; ?>" sortable-group-id="<?php echo $displayData["data"]->report_id?>">
                    <td class="text-center">
                        <?php echo HTMLHelper::_('grid.id', $displayData["index"], $displayData["data"]->report_id, false, "report_id"); ?>
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            <?php echo HTMLHelper::_('jgrid.published', $displayData["data"]->published, $displayData["index"], 'reports.', $canChange, 'cb'); ?>
                        </div>
                    </td>
                    <td class="nowrap has-context">
                        <div class="pull-left hasTooltip" title="<?php print $displayData["data"]->description; ?>">
                            <?php if ($canEdit || $canEditOwn): ?>
                                <a href="<?php echo Route::_('index.php?option=com_timeclock&task=report.edit&report_id='.(int) $displayData["data"]->report_id); ?>">
                                <?php echo $displayData["data"]->name; ?></a>
                            <?php else : ?>
                                <?php echo $displayData["data"]->name; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="text-center">
                        <select name="<?php print $selectname; ?>" class="narrow" onChange="exportReport(this, <?php print $displayData["data"]->report_id; ?>, this.value);">
                            <option value=""><?php print Text::_("COM_TIMECLOCK_SELECT_FORMAT"); ?></option>
                            <option value="csv">CSV</option>
                            <option value="xlsx">Excel 2007</option>
                        </select>
                    </td>
                    <td class="text-center hidden-phone">
                        <?php echo $displayData["data"]->type; ?>
                    </td>
                    <td class="text-center hidden-phone">
                        <?php echo $displayData["data"]->author; ?>
                    </td>
                    <td class="text-center hidden-phone">
                        <?php echo $displayData["data"]->startDate; ?>
                    </td>
                    <td class="text-center hidden-phone">
                        <?php echo $displayData["data"]->endDate; ?>
                    </td>
                    <td class="text-center hidden-phone">
                        <?php echo $displayData["data"]->report_id; ?>
                    </td>
                </tr>
