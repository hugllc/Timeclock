<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Layout\FileLayout;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');

$row = new FileLayout('row', __DIR__.'/layouts');

$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));

?>
<form action="<?php echo Route::_('index.php?option=com_timeclock&view=projects'); ?>" method="post" name="adminForm" id="adminForm">
    <div id="j-main-container" class="span10">
        <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
        <table id="adminTable" cellpadding="0" cellspacing="0" width="100%" class="table table-striped">
            <thead>
                <tr>
                    <th width="1%" class="">
                        <?php echo HTMLHelper::_('grid.checkall'); ?>
                    </th>
                    <th width="1%" style="min-width:55px" class="nowrap text-center">
                        <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'p.published', $listDirn, $listOrder); ?>
                    </th>
                    <th>
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_TIMECLOCK_NAME', 'p.name', $listDirn, $listOrder); ?>
                    </th>
                    <th class="text-center hidden-phone">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_TIMECLOCK_MANAGER', 'manager', $listDirn, $listOrder); ?>
                    </th>
                    <th class="text-center hidden-phone">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_TIMECLOCK_TYPE', 'p.type', $listDirn, $listOrder); ?>
                    </th>
                    <th class="text-center hidden-phone">
                        <?php echo HTMLHelper::_('searchtools.sort', 'JCATEGORY', 'category', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%" class="nowrap text-center hidden-phone">
                        <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'p.project_id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
            <tbody id="project-list">
                <?php for($i=0, $n = count($this->items);$i<$n;$i++) {
                    if ($this->items[$i]->project_id <= 0) {
                        // Don't display the system categories
                        continue;
                    }
                    echo $row->render(
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
