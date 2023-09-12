<?php

defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;

?>
<form action="<?php echo Route::_("index.php?option=com_timeclock&controller=customer"); ?>" method="post" id="adminForm" name="adminForm">
    <div class="row">
        <div class="col-lg-9">
            <?php echo $this->form->renderFieldset('main'); ?>
        </div>
        <div class="col-lg-3">
            <?php echo $this->form->renderFieldset('sidebar'); ?>
        </div>
    </div>
    <?php echo $this->form->renderFieldset('hidden'); ?>
    <input type="hidden" name="task" value="" />
    <?php print HTMLHelper::_("form.token"); ?>
</form>
