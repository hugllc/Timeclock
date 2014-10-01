<?php
defined('_JEXEC') or die('Restricted access');
$png = "";
$total = $displayData->data["total"];
$decimals = $displayData->params->get("decimalPlaces");
if (TimeclockHelpersContrib::phpgraph() && !empty($total)):
    $graph = new PHPGraphLibPie(400, 200);
    $data  = array();
    foreach ($displayData->data["user_manager"] as $user_id => $hours) {
        $name = isset($displayData->users[$user_id]) ? $displayData->users[$user_id]->name : "User $user_id";
        $data[$name] = $hours;
    }
    $graph->addData($data);
    $graph->setTitle(JText::_("COM_TIMECLOCK_HOURSUM_USER_MANAGER_PLOT_TITLE"));
    $graph->setLabelTextColor('black');
    $graph->setLegendTextColor('black');

    ob_start();
    $graph->createGraph();
    $png = ob_get_contents();
    ob_end_clean();
endif;
?>
        <h3><?php print JText::_("COM_TIMECLOCK_HOURS_BY_USER_MANAGER"); ?></h3>
        <hr />
        <div class="row-fluid">
            <div class="span6">
                <table class="report table table-striped table-bordered table-hover table-condensed">
                    <thead>
                        <tr class="header">
                            <th><?php print JText::_("COM_TIMECLOCK_USER_MANAGER"); ?></th>
                            <th><?php print JText::_("COM_TIMECLOCK_HOURS"); ?></th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="header">
                            <th><?php print JText::_("COM_TIMECLOCK_TOTAL"); ?></th>
                            <td class="total"><?php print $total; ?></td>
                            <th>&nbsp;</th>
                        </tr>
                    </tfoot>
                    <tbody>
<?php foreach ($data as $name => $hours) : ?>
                        <tr class="user">
                            <td class="user"><?php print $name; ?></td>
                            <td class="hours"><?php print $hours; ?></td>
                            <td class="percent"><?php print empty($total) ? 0 : round(($hours/$total)*100, $decimals); ?>%</td>
                        </tr>
<?php endforeach; ?>
                    </tbody>
                </table>
            </div>
<?php if (!empty($png)) :  ?>
            <img class="span6" alt="graph" src="data:image/png;base64,<?php print base64_encode($png); ?>" />
<?php endif; ?>
        </div>
