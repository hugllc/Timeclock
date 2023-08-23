<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
$cnt = 0;
?>
        <tr class="header">
            <th rowspan="2"><?php print Text::_("COM_TIMECLOCK_USER"); ?></th>
<?php foreach ($displayData as $projects) : ?>
    <?php foreach ($projects["proj"] as $proj) : ?>
    <?php $name  = empty($proj->name) ? "Project ".$proj->project_id : $proj->name;?>
    <?php $cnt++ ?>
            <th colspan="2"><?php print $name; ?></th>
    <?php endforeach; ?>
<?php endforeach; ?>
            <th rowspan="2"><?php print Text::_("COM_TIMECLOCK_TOTAL"); ?></th>
        </tr>
        <tr class="subheader">
<?php for ($i = 0; $i < $cnt; $i++) : ?>
            <th><?php print Text::_("COM_TIMECLOCK_HOURS"); ?></th>
            <th>%</th>
<?php endfor; ?>
        </tr>
