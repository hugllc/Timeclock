<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.6 component
 * Copyright (C) 2008-2009, 2011 Hunt Utilities Group, LLC
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009, 2011 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');

$headerColSpan    = ($this->weeks * 4) + 3;

$cellStyle  = "text-align:center; padding: 1px;";
$totalStyle = $cellStyle." font-weight: bold;";
$document        =& JFactory::getDocument();
$dateFormat      = JText::_("DATE_FORMAT_LC1");
$shortDateFormat = JText::_("DATE_FORMAT_LC3");
//$document->setTitle("Payroll Summary for ".JHTML::_('date', $this->period['unix']["start"], $shortDateFormat)." to ".JHTML::_('date', $this->period['unix']["end"], $shortDateFormat));
$document->setTitle($this->params->get('page_title')." (".JHTML::_('date', $this->period['unix']["start"], $shortDateFormat)." ".JText::_(COM_TIMECLOCK_TO)." ".JHTML::_('date', $this->period['unix']["end"], $shortDateFormat).")");

?>
<div id="timeclock">
<form action="<?php JROUTE::_("index.php"); ?>" method="post" id="adminForm" autocomplete="off">
    <?php if ($this->params->get('show_page_title')) : ?>
    <div class="componentheading<?php echo $this->params->get('pageclass_sfx');?>">
            <?php echo $this->escape($this->params->get('page_title')); ?>
    </div>
    <?php endif; ?>
    <div>
        <?php nextPrev($this); ?>

        <?php print $this->loadTemplate("export"); ?>

        <div id="dateheader" style="clear:both;">
            <strong>
                <?php print JText::sprintf(
                    COM_TIMECLOCK_DATE_TO_DATE,
                    JHTML::_('date', $this->period['unix']["start"], $dateFormat),
                    JHTML::_('date', $this->period['unix']["end"], $dateFormat)
                ); ?>
            </strong>
        </div>
    </div>
    <table id="timeclockTable" style="padding-bottom: 3em;">
        <tr>
            <th rowspan="2" style="<?php print $cellStyle; ?>"><?php print JHTML::_('grid.sort', JText::_(COM_TIMECLOCK_EMPLOYEE), 'u.name', @$this->lists['order_Dir'], @$this->lists['order']); ?><?php //print JText::_("Employee"); ?></td>
<?php
for ($w = 0; $w < $this->weeks; $w++) {
    ?>
            <th colspan="4" align="center" style="<?php print $cellStyle; ?>"><?php print JText::_(COM_TIMECLOCK_WEEK)." ".($w+1); ?> </td>
    <?php
}
?>
            <th rowspan="2" style="<?php print $cellStyle; ?>"><?php print JHTML::_('grid.sort', JText::_(COM_TIMECLOCK_EMPLOYEE), 'u.name', @$this->lists['order_Dir'], @$this->lists['order']); ?><?php //print JText::_("Employee"); ?></td>
            <th rowspan="2" style="<?php print $cellStyle; ?>"><?php print JText::_(COM_TIMECLOCK_TOTAL); ?></td>
        </tr>
        <tr>
<?php
for ($w = 0; $w < $this->weeks; $w++) {
    ?>
            <th style="<?php print $cellStyle; ?>"><?php print JText::_(COM_TIMECLOCK_WORKED); ?> </td>
            <th style="<?php print $cellStyle; ?>"><?php print JText::_(COM_TIMECLOCK_PTO); ?> </td>
            <th style="<?php print $cellStyle; ?>"><?php print JText::_(COM_TIMECLOCK_HOLIDAY); ?> </td>
            <th style="<?php print $cellStyle; ?>"><?php print JText::_(COM_TIMECLOCK_TOTAL); ?> </td>
    <?php
}
?>
        </tr>
<?php
$k = 0;
$totals = array();
foreach ($this->report as $id => $time) {
    ?>
        <tr class="row<?php print $k;?>">
            <td align="right" style="<?php print $cellStyle; ?>"><?php print $time["name"]; ?></td>
    <?php
    for ($w = 0; $w < $this->weeks; $w++) {
        foreach (array("PROJECT", "PTO", "HOLIDAY") as $type) {
            $hours = (empty($time[$w][$type]["hours"])) ? $this->cell_fill : $time[$w][$type]["hours"];
            ?>
                <td align="center" style="<?php print $cellStyle; ?>"><?php print $hours; ?></td>
            <?php
        }
        $hours = (empty($time[$w]["TOTAL"]["hours"])) ? $this->cell_fill : $time[$w]["TOTAL"]["hours"];
        ?>
                <td style="<?php print $totalStyle; ?>"><?php print $hours; ?></td>
        <?php
    }
    $hours = (empty($this->totals["user"][$id])) ? 0 : $this->totals["user"][$id];
    ?>
            <td align="right" style="<?php print $cellStyle; ?>"><?php print $time["name"]; ?></td>
            <td style="<?php print $totalStyle; ?>"><?php print $hours; ?></td>
        </tr>
    <?php
    $k = 1 - $k;
}
?>
        <tr class="row<?php print $k;?>">
            <th align="right" style="<?php print $totalStyle; ?>"><?php print JText::_(COM_TIMECLOCK_TOTAL); ?></td>
    <?php
for ($w = 0; $w < $this->weeks; $w++) {
    foreach (array("PROJECT", "PTO", "HOLIDAY") as $type) {
        $hours = (empty($this->totals["type"][$w][$type])) ? 0 : $this->totals["type"][$w][$type];
        ?>
            <td align="center" style="<?php print $totalStyle; ?>"><?php print $hours; ?></td>
        <?php
    }
    $hours = (empty($this->totals["type"][$w]["TOTAL"])) ? 0 : $this->totals["type"][$w]["TOTAL"];
    ?>
            <td style="<?php print $totalStyle; ?>"><?php print $hours; ?></td>
    <?php
}
$hours = (empty($this->totals["total"])) ? 0 : $this->totals["total"];
?>
            <th align="right" style="<?php print $totalStyle; ?>"><?php print JText::_(COM_TIMECLOCK_TOTAL); ?></td>
            <td style="<?php print $totalStyle; ?>"><?php print $hours; ?></td>
        </tr>
    </table>
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
<?php print JHTML::_("form.token"); ?>
</form>
<h3><?php print JText::_(COM_TIMECLOCK_NOTES); ?></h3>
<?php
foreach ($this->notes as $user => $projects) {
?>

<dl>
    <dt><h4><?php print $user; ?></h4></dt>
    <dd>
        <dl>
    <?php
    foreach ($projects as $project => $dates) {
        ?>
            <dt style="font-weight: bold;"><?php print $project; ?></dt>
            <dd>
                <dl>

        <?php
        foreach ($dates as $date => $note) {
            ?>
                    <dt><?php print $date." (".$note['hours'];?> h)</dt>
                    <dd><?php print $note['notes']; ?></dd>
            <?php
        }
        ?>
                </dl>
            </dd>
        <?php
    }
    ?>
        </dl>
    </dd>
</dl>
    <?php
}
?>
</div>
<?php

/**
 * Prints out next previous header
 *
 * @param object &$obj Pass it $this
 *
 * @return null
 */
function nextprev(&$obj)
{
    $img = "components".DS."com_timeclock".DS."images".DS."1rightarrow.png";
    $text = '<img src="'.$img.'" alt="&gt;" style="border: none;" />';
    $url = JROUTE::_("index.php?option=com_timeclock&view=reports&layout=payroll&date=".$obj->period["next"]);
    $nextImg = '<a href="'.$url.'">'.$text.'</a>';
    $next = '<a href="'.$url.'">'.JText::_(JNEXT).'</a>';

    $img = "components".DS."com_timeclock".DS."images".DS."1leftarrow.png";
    $text = '<img src="'.$img.'" alt="&lt;" style="border: none;" />';
    $url = JROUTE::_("index.php?option=com_timeclock&view=reports&layout=payroll&date=".$obj->period["prev"]);
    $prevImg = '<a href="'.$url.'">'.$text.'</a>';
    $prev = '<a href="'.$url.'">'.JText::_(JPREVIOUS).'</a>';

    $text = JText::_(COM_TIMECLOCK_TODAY);
    $url = JROUTE::_("index.php?option=com_timeclock&view=reports&layout=payroll");
    $today = '<a href="'.$url.'">'.$text.'</a>';

    ?>
    <table width="100%" id="nextprev">
        <tr>
            <td width="5px" align="left"><?php print $prevImg; ?></td>
            <td width="20%" align="left" style="vertical-align: middle;"><?php print $prev; ?></td>

            <td align="center" style="white-space: nowrap;">
                <?php print $today; ?>
            </td>
            <td width="20%" align="right" style="vertical-align: middle;"><?php print $next; ?></td>
            <td width="5px;" align="right"><?php print $nextImg; ?></td>
        </tr>
    </table>
    <?php
}
?>
