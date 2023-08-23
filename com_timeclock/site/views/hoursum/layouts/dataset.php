<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
$total = $displayData->total;
?>
        <h3><?php print $displayData->title; ?></h3>
        <hr />
        <div class="row-fluid">
            <div class="span4">
                <table class="report table table-striped table-bordered table-hover table-condensed">
                    <thead>
                        <tr class="header">
                            <th><?php print $displayData->group; ?></th>
                            <th><?php print Text::_("COM_TIMECLOCK_HOURS"); ?></th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="header">
                            <th><?php print Text::_("COM_TIMECLOCK_TOTAL"); ?></th>
                            <td class="total"><?php print $total; ?></td>
                            <th>&nbsp;</th>
                        </tr>
                    </tfoot>
                    <tbody>
<?php foreach ($displayData->data as $name => $hours) : ?>
                        <tr class="user <?php print ($hours == 0) ? "empty" : ""; ?>">
                            <td class="user"><?php print $name; ?></td>
                            <td class="hours"><?php print $hours; ?></td>
                            <td class="percent"><?php print empty($total) ? 0 : round(($hours/$total)*100, $displayData->decimals); ?>%</td>
                        </tr>
<?php endforeach; ?>
                    </tbody>
                </table>
            </div>
<?php if (!empty($displayData->png)) : ?>
            <img class="span8" alt="graph" src="data:image/png;base64,<?php print base64_encode($displayData->png); ?>" />
<?php endif; ?>
        </div>
