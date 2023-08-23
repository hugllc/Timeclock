<?php
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$baseurl = '&option=com_timeclock&controller='.$displayData->controller;
$url = Route::_($baseurl.'&date=now');
$today = '<a href="'.$url.'">'.Text::_("COM_TIMECLOCK_TODAY").'</a>';

$tip = Text::_("COM_TIMECLOCK_GO_TO_NEXT_PAYPERIOD");
$img = "components/com_timeclock/images/1rightarrow.png";
$text = '<img src="'.$img.'" alt="&gt;" style="border: none;" />';
$url = Route::_($baseurl.'&date='.$displayData->next);
$nextImg = '<a href="'.$url.'">'.$text.'</a>';
$next = '<a href="'.$url.'">'.Text::_("JNEXT").'</a>';

$tip = Text::_("COM_TIMECLOCK_GO_TO_PREV_PAYPERIOD");
$img = "components/com_timeclock/images/1leftarrow.png";
$text = '<img src="'.$img.'" alt="&lt;" style="border: none;" />';
$url = Route::_($baseurl.'&date='.$displayData->prev);
$prevImg = '<a href="'.$url.'">'.$text.'</a>';
$prev = '<a href="'.$url.'">'.Text::_("JPREVIOUS").'</a>';

?>
<div class="row-fluid" style="clear: both;">
    <div align="left" class="pull-left nextprev span3">
        <?php print $prevImg; ?><span><?php print $prev; ?></span>
    </div>
    <div class="pull-center nextprev span6 center"><?php print $today; ?></div>
    <div align="right" class="pull-right nextprev span3">
        <span><?php print $next; ?></span><?php print $nextImg; ?>
    </div>
</div>
