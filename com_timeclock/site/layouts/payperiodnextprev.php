<?php
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

if (property_exists($displayData, "user_id")) {
    $id = "&id=".$displayData->user_id;
} else {
    $id = "";
}

$baseurl = 'index.php?view='.$displayData->view;
$url = Route::_($baseurl.'&date=now'.$id);
$today = '<a href="'.$url.'">'.Text::_("COM_TIMECLOCK_TODAY").'</a>';

$tip = Text::_("COM_TIMECLOCK_GO_TO_NEXT_PAYPERIOD");
$img = "components/com_timeclock/images/1rightarrow.png";
$text = '<img src="'.$img.'" alt="&gt;" style="border: none;" />';
$url = Route::_($baseurl.'&date='.$displayData->next.$id);
$nextImg = '<a href="'.$url.'">'.$text.'</a>';
$next = '<a href="'.$url.'">'.Text::_("JNEXT").'</a>';

$tip = Text::_("COM_TIMECLOCK_GO_TO_PREV_PAYPERIOD");
$img = "components/com_timeclock/images/1leftarrow.png";
$text = '<img src="'.$img.'" alt="&lt;" style="border: none;" />';
$url = Route::_($baseurl.'&date='.$displayData->prev.$id);
$prevImg = '<a href="'.$url.'">'.$text.'</a>';
$prev = '<a href="'.$url.'">'.Text::_("JPREVIOUS").'</a>';

?>
<div class="row" style="clear: both;">
    <div style="text-align: left;" class="pull-left nextprev col-lg-3">
        <?php print $prevImg; ?><span><?php print $prev; ?></span>
    </div>
    <div class="pull-center nextprev col-lg-6 text-center"><?php print $today; ?></div>
    <div style="text-align: right;" class="pull-right nextprev col-lg-3">
        <span><?php print $next; ?></span><?php print $nextImg; ?>
    </div>
</div>
