<?php
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$sortFields = $this->sortFields;
?>
<script language="javascript" type="text/javascript">
Joomla.orderTable = function()
{
        var form = document.getElementById("adminForm");
        var order = document.getElementById("sortTable");
        var dir = document.getElementById("directionTable");
        
        form.filter_order.value = order.value;
        form.filter_order_Dir.value = dir.options[dir.selectedIndex].value;
        
        form.submit();
}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_timeclock'); ?>" method="post" name="adminForm" id="adminForm">
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
        <div id="filter-bar" class="btn-toolbar">
            <div class="filter-search btn-group pull-left">
                <label for="filter_search" class="element-invisible"><?php echo JText::_('COM_TIMECLOCK_FILTER_SEARCH_DESC');?></label>
                <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_TIMECLOCK_SEARCH_IN_NAME'); ?>" />
            </div>
            <div class="btn-group pull-left">
                <button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                <button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
            </div>
            <div class="btn-group pull-right hidden-phone">
                <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                <?php echo $this->pagination->getLimitBox(); ?>
            </div>
            <div class="btn-group pull-right hidden-phone">
                <label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
                <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
                    <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
                    <option value="asc" <?php if ($listDirn == 'ASC') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
                    <option value="desc" <?php if ($listDirn == 'DESC') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
                </select>
            </div>
            <div class="btn-group pull-right">
                <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
                <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
                    <option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
                    <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
                </select>
            </div>
        </div>
        <div class="clearfix"> </div>
        <table id="adminTable" cellpadding="0" cellspacing="0" width="100%" class="table table-striped">
            <thead>
                <tr>
                    <th width="1%" class="">
                        <?php echo JHtml::_('grid.checkall'); ?>
                    </th>
                    <th>
                        <?php echo JHtml::_('grid.sort', 'COM_TIMECLOCK_NOTES', 'notes', $listDirn, $listOrder); ?>
                    </th>
                    <th class="center">
                        <?php echo JHtml::_('grid.sort', 'COM_TIMECLOCK_WORK_DATE', 't.worked', $listDirn, $listOrder); ?>
                    </th>
                    <th class="center">
                        <?php echo JHtml::_('grid.sort', 'COM_TIMECLOCK_HOURS', 'hours', $listDirn, $listOrder); ?>
                    </th>
                    <th class="center hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'COM_TIMECLOCK_PROJECT', 'project', $listDirn, $listOrder); ?>
                    </th>
                    <th class="center hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'JAUTHOR', 'author', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%" class="nowrap center hidden-phone">
                        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 't.timesheet_id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
            <tbody id="holiday-list">
                <?php for($i=0, $n = count($this->data);$i<$n;$i++) {
                    echo $this->_holidayListView->render(
                        array(
                            "data" => $this->data[$i],
                            "index" => $i,
                        )
                    );
                } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8" align="center">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
        </table>
        <input type="hidden" name="controller" value="holiday" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
