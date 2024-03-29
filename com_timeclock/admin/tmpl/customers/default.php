<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Layout\FileLayout;

HTMLHelper::_("jquery.framework");

$row = new FileLayout('row', __DIR__.'/layouts');

$listOrder  = $this->state->get('list.ordering');
$listDirn   = strtoupper($this->state->get('list.direction')) or "ASC";
?>
<form action="<?php echo Route::_('index.php?option=com_timeclock&view=customers'); ?>" method="post" name="adminForm" id="adminForm">
    <div id="j-main-container" class="j-main-container">
        <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
        <table id="adminTable" cellpadding="0" cellspacing="0" width="100%" class="table table-striped">
            <thead>
                <tr>
                    <th width="1%" class="">
                        <?php echo HTMLHelper::_('grid.checkall'); ?>
                    </th>
                    <th width="1%" style="min-width:55px" class="nowrap text-center">
                        <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'c.published', $listDirn, $listOrder); ?>
                    </th>
                    <th>
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_TIMECLOCK_COMPANY', 'c.company', $listDirn, $listOrder); ?>
                    </th>
                    <th class="text-center hidden-phone">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_TIMECLOCK_CONTACT_NAME', 'c.name', $listDirn, $listOrder); ?>
                    </th>
                    <th class="text-center hidden-phone">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_TIMECLOCK_BILL_PTO', 'c.bill_pto', $listDirn, $listOrder); ?>
                    </th>
                    <th class="text-center hidden-phone">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_TIMECLOCK_NOTES', 'c.notes', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%" class="nowrap center hidden-phone">
                        <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'c.customer_id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
            <tbody id="customer-list">
                <?php for($i=0, $n = count($this->items);$i<$n;$i++) {
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
