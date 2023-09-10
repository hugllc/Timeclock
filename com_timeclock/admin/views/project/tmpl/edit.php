<?php

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('formbehavior.chosen', 'select:not(.plain)');
JHTML::script(Juri::base()."components/com_timeclock/js/edit.js");

?>

<script type="text/javascript">
    Joomla.submitbutton = function (task)
    {
        Joomla.Timeclock.submitbutton(task);
    }
</script>
<form action="index.php?option=com_timeclock&controller=project" method="post" id="adminForm" name="adminForm">
<div class="row-fluid">
<?php 
    print HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'details'));

    print HTMLHelper::_('bootstrap.addTab', 'myTab', 'details', Text::_('JDETAILS'));
    print TimeclockHelpersView::getForm($this->form, $this->data); 
    print HTMLHelper::_('bootstrap.endTab');
    if ($this->params->get("wCompEnable")) {
        print HTMLHelper::_('bootstrap.addTab', 'myTab', 'wcomp', Text::_('COM_TIMECLOCK_WORKERS_COMP'));
        print "<div>".Text::_("COM_TIMECLOCK_WORKERS_COMP_CODES")."</div>\n";
        print '<div class="span10">'."\n";
        print TimeclockHelpersView::getFormSetH("wcomp", $this->form, $this->data);
        print "</div>\n";
        print HTMLHelper::_('bootstrap.endTab');
    }
    print HTMLHelper::_('bootstrap.addTab', 'myTab', 'users', Text::_('COM_TIMECLOCK_USERS'));
    print '<div class="span10 users">'."\n";
    print TimeclockHelpersView::getFormSetV("users", $this->form, $this->data);
    print "</div>\n";
    print '<div class="span10 nousers">'."\n";
    print Text::_("COM_TIMECLOCK_CATEGORY_NO_USERS");
    print "</div>\n";
    print HTMLHelper::_('bootstrap.endTab');
    print HTMLHelper::_('bootstrap.endTabSet');
?>
    <input type="hidden" name="project_id" value="<?php print $this->data->project_id; ?>" />
    <input type="hidden" name="id" value="<?php print $this->data->project_id; ?>" />
    <input type="hidden" name="created" value="<?php print $this->data->created; ?>" />
    <input type="hidden" name="created_by" value="<?php print $this->data->created_by; ?>" />
    <input type="hidden" name="task" value="" />
    <?php print JHTML::_("form.token"); ?>
    <script type="text/JavaScript">
        jQuery( document ).ready(function() {
            jQuery("select#type").on("change", checkProjectType);
            checkProjectType();
        });
        function checkProjectType() {
            var sel = jQuery("select#type");
            if (sel.val() == "CATEGORY") {
                jQuery("div.users").hide();
                jQuery("div.nousers").show();
            } else {
                jQuery("div.users").show();
                jQuery("div.nousers").hide();
            }
        }
    </script>
</div>
</form>
