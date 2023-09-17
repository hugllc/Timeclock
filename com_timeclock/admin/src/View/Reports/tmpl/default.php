<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');

$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$sortFields = $this->sortFields;
?>
<div id="timeclock">
<form action="<?php echo Route::_('index.php?option=com_timeclock&view=reports'); ?>" method="post" name="adminForm" id="adminForm">
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
        <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
        <table id="adminTable" cellpadding="0" cellspacing="0" width="100%" class="table table-striped">
            <thead>
                <tr>
                    <th width="1%" class="">
                        <?php echo HTMLHelper::_('grid.checkall'); ?>
                    </th>
                    <th width="1%" style="min-width:55px" class="nowrap text-center">
                        <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'r.published', $listDirn, $listOrder); ?>
                    </th>
                    <th>
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_TIMECLOCK_NAME', 'r.name', $listDirn, $listOrder); ?>
                    </th>
                    <th class="text-center">
                        <?php echo Text::_("COM_TIMECLOCK_EXPORT"); ?>
                    </th>
                    <th class="text-center hidden-phone">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_TIMECLOCK_TYPE', 'r.type', $listDirn, $listOrder); ?>
                    </th>
                    <th class="text-center hidden-phone">
                        <?php echo HTMLHelper::_('searchtools.sort', 'JAUTHOR', 'author', $listDirn, $listOrder); ?>
                    </th>
                    <th class="text-center hidden-phone">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_TIMECLOCK_START_DATE', 'r.startDate', $listDirn, $listOrder); ?>
                    </th>
                    <th class="text-center hidden-phone">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_TIMECLOCK_END_DATE', 'r.endDate', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%" class="nowrap text-center hidden-phone">
                        <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'r.report_id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
            <tbody id="report-list">
                <?php for($i=0, $n = count($this->items);$i<$n;$i++) {
                    echo $this->_row->render(
                        array(
                            "data" => $this->items[$i],
                            "index" => $i,
                        )
                    );
                } ?>
            </tbody>
            <tfoot>
            </tfoot>
        </table>
        <?php echo $this->pagination->getListFooter(); ?>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo HTMLHelper::_('form.token'); ?>
    </div>
</form>
<iframe class="element-invisible" id="exportframe"></iframe>
</div>
