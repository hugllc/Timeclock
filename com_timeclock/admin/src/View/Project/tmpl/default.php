<?php

defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;


?>
<form action="<?php echo Route::_("index.php?option=com_timeclock&controller=department"); ?>" method="post" id="adminForm" name="adminForm">
    <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'details')); ?>
    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('JDETAILS')); ?>
    <div class="row">
        <div class="col-lg-9">
            
            <?php echo $this->form->renderFieldset('main'); ?>
        </div>
        <div class="col-lg-3">
            <?php echo $this->form->renderFieldset('sidebar'); ?>
        </div>
    </div>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>
    <?php if ($this->params->get("wCompEnable")): ?>
        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'wcomp', Text::_('COM_TIMECLOCK_WORKERS_COMP')); ?>
        <div><?php echo Text::_("COM_TIMECLOCK_WORKERS_COMP_CODES"); ?></div>
        <div class="col-lg-10">
            <?php echo $this->form->renderFieldset('wcomp'); ?>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>
    <?php else: ?>
        <?php echo $this->form->renderFieldset('wcomphidden'); ?>
    <?php endif ?>
    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'users', Text::_('COM_TIMECLOCK_USERS')); ?>
    <?php echo $this->form->renderFieldset('users'); ?>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>
    <?php echo HTMLHelper::_('uitab.endTabSet'); ?>

    <?php echo $this->form->renderFieldset('hidden'); ?>
    <input type="hidden" name="task" value="" />
    <?php echo HTMLHelper::_("form.token"); ?>
</form>
