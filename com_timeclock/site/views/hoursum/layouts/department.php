<?php
defined('_JEXEC') or die('Restricted access');
?>
        <div class="row-fluid">
            <div class="span6">
                <table class="report table table-striped table-bordered table-hover table-condensed">
                    <thead>
                        <tr class="header">
                            <th><?php print JText::_("COM_TIMECLOCK_DEPARTMENT"); ?></th>
                            <th><?php print JText::_("COM_TIMECLOCK_HOURS"); ?></th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="header">
                            <th><?php print JText::_("COM_TIMECLOCK_TOTAL"); ?></th>
                            <th></th>
                            <th>&nbsp;</th>
                        </tr>
                    </tfoot>
                    <tbody>
<?php /*
    foreach ($this->users as $user) {
        $user_id = (int)$user->id;
        $user->data      = isset($this->data[$user_id]) ? $this->data[$user_id] : array();
        $user->genparams = $this->params;
        $user->projects  = $this->projects;
        print $this->_row->render($user);
    }*/
?>
                    </tbody>
                </table>
            </div>
        </div>
