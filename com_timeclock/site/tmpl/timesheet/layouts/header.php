<?php
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;

$headerDateFormat = 'D <b\r/>M<b\r>d';
?>
    <tr class="header">
        <th>Project</th>
<?php
$today = date("Y-m-d");
$d = 0;
foreach ($displayData->dates as $date => $timeentry) :
    $class = "";
    if ($timeentry) {
        $url = Route::_('index.php/timeclock?task=timesheet.addhours&date='.urlencode($date));
        $tipTitle = Text::_("COM_TIMECLOCK_ADD_HOURS");
        $tip = "on ".JHTML::_('date', $date, Text::_("DATE_FORMAT_LC1"));
    } else {
        $url = "";
        $tipTitle = Text::_("COM_TIMECLOCK_NO_HOURS");
        $tip = Text::_("COM_TIMECLOCK_NO_HOURS_BEFORE_START");
        $class .= " nohours ";
    };
    $jdate = Factory::getDate($date);
    ?>
        <th class="timeclockheader timeclockheader-<?php print $date; ?> <?php print $class; ?>">
            <?php 
                print '<span class="hasTooltip" title="';
                print '<strong>'.$tipTitle.'</strong><br />'.$tip.'">';
                if ($url != "") {
                    print '<a href="'.$url.'"> '.$jdate->format($headerDateFormat).' </a>';
                } else {
                    print $jdate->format($headerDateFormat);
                }
                print '</span>';
                //print JHTML::_('tooltip', $tip, $tipTitle, '', $date->format($headerDateFormat), $url); 
            ?>
        </th>
    <?php if (($displayData->splitdays != 0) && ((++$d % $displayData->splitdays) == 0)) : ?>
        <th class="vertical">
            <span class="vertical-text nowrap"><?php print Text::_("COM_TIMECLOCK_SUBTOTAL"); ?></span>
        </th>
    <?php endif; ?>
<?php endforeach; ?>
        <th class="vertical">
            <span class="vertical-text nowrap"><?php print Text::_("COM_TIMECLOCK_TOTAL"); ?></span>
        </th>
    </tr>
