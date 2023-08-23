<?php

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('formbehavior.chosen', 'select:not(.plain)');
JHTML::script(Juri::base()."components/com_timeclock/js/edit.js");
?>

<script type="text/javascript">
    Joomla.submitbutton = function (task)
    {
        Joomla.Timeclock.submitbutton(task);
    }
</script>
<form action="index.php?option=com_timeclock&controller=customer" method="post" id="adminForm" name="adminForm">
<?php print TimeclockHelpersView::getForm($this->form, $this->data); ?>
    <input type="hidden" name="customer_id" value="<?php print $this->data->customer_id; ?>" />
    <input type="hidden" name="id" value="<?php print $this->data->customer_id; ?>" />
    <input type="hidden" name="created" value="<?php print $this->data->created; ?>" />
    <input type="hidden" name="created_by" value="<?php print $this->data->created_by; ?>" />
    <input type="hidden" name="task" value="" />
    <?php print JHTML::_("form.token"); ?>
</form>
