<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.5 component
 * Copyright (C) 2008 Hunt Utilities Group, LLC
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
 * @copyright  2008 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die('Restricted access'); 

JHTML::_('behavior.tooltip');

$this->totals     = array();
if (empty($this->days)) $this->days = 7;

$headerColSpan    = ($this->period["length"]+2+($this->period["length"]/$this->days));

$this->cellStyle  = "text-align:center; padding: 1px;";
$this->totalStyle = $this->cellStyle." font-weight: bold;";
$document        =& JFactory::getDocument();
$dateFormat      = JText::_("DATE_FORMAT_LC1");
$shortDateFormat = JText::_("DATE_FORMAT_LC3");
$document->setTitle(JText::_("Timeclock Reports"));

$report = array();
$weeks = round($this->period["length"] / $this->days);
// Make the data into something usefull for this particular report
foreach ($this->data as $user_id => $projdata) {
    foreach ($projdata as $proj_id => $dates) {
        $d = 0;
        foreach ($this->period["dates"] as $key => $uDate) {
            $week = (int)($d++ / $this->days);
            if (!array_key_exists($key, $dates)) continue;
            $type = $dates[$key]["rec"]->type;
            $report[$user_id][$week][$type]["hours"] += $dates[$key]["hours"];
            $report[$user_id][$week]["TOTAL"]["hours"] += $dates[$key]["hours"];
            $report[$user_id]["TOTAL"] += $dates[$key]["hours"];
            if (empty($report[$user_id]["name"])) $report[$user_id]["name"] = $dates[$key]["rec"]->user_name;
        }
    }
}

?>

<form action="<?php JROUTE::_("index.php"); ?>" method="post" name="userform" autocomplete="off">
    <div class="componentheading"><?php print JText::_("Timeclock Payroll Report");?></div>
    <table style="padding-bottom: 3em;">
        <tr>
            <td colspan="<?php print (($weeks *4) + 3); ?>">
                <?php nextPrev($this); ?>
                <div id="dateheader" style="clear:both;">
                   <strong>
                       <?php print JHTML::_('date', $this->period['unix']["start"], $dateFormat); ?>
                       <?php print JText::_("to"); ?>
                       <?php print JHTML::_('date', $this->period['unix']["end"], $dateFormat); ?>
                   </strong>
               </div>
           </td>        
        </tr>
        <tr>
            <td class="sectiontableheader" rowspan="2"><?php print JText::_("Employee"); ?></td>
<?php
for ($w = 0; $w < $weeks; $w++) {
    ?>
            <td class="sectiontableheader" colspan="4" align="center"><?php print JText::_("Week")." ".($w+1); ?> </td>            
    <?php
}
?>
            <td class="sectiontableheader" rowspan="2"><?php print JText::_("Employee"); ?></td>
            <td class="sectiontableheader" rowspan="2"><?php print JText::_("Total"); ?></td>
        </tr>
        <tr>
<?php
for ($w = 0; $w < $weeks; $w++) {
    ?>
            <td class="sectiontableheader"><?php print JText::_("Worked"); ?> </td>            
            <td class="sectiontableheader"><?php print JText::_("PTO"); ?> </td>            
            <td class="sectiontableheader"><?php print JText::_("Holiday"); ?> </td>            
            <td class="sectiontableheader"><?php print JText::_("Total"); ?> </td>            
    <?php
}
?>
        </tr>
<?php
$k = 0;
$totals = array();
foreach ($report as $id => $time) {
    ?>
        <tr>
            <td class="sectiontablerow<?php print $k;?>" align="right"><?php print $time["name"]; ?></td>
    <?php
    for ($w = 0; $w < $weeks; $w++) {
        foreach (array("PROJECT", "PTO", "HOLIDAY") as $type) {
            $hours = (empty($time[$w][$type]["hours"])) ? 0 : $time[$w][$type]["hours"];
            $totals[$w][$type] += $hours;
            ?>
                <td class="sectiontablerow<?php print $k;?>" align="center"><?php print $hours; ?></td>
            <?php
        }
        $hours = (empty($time[$w]["TOTAL"]["hours"])) ? 0 : $time[$w]["TOTAL"]["hours"];
        $totals[$w]["TOTAL"] += $hours;
        ?>
                <td class="sectiontablerow<?php print $k;?>" style="font-weight: bold; text-align: center;"><?php print $hours; ?></td>
        <?php
    }
    $k = 1 - $k;
    $hours = (empty($time["TOTAL"])) ? 0 : $time["TOTAL"];
    $totals["TOTAL"] += $hours;
    ?>
            <td class="sectiontablerow<?php print $k;?>" align="right"><?php print $time["name"]; ?></td>
            <td class="sectiontablerow<?php print $k;?>" style="font-weight: bold; text-align: center;"><?php print $hours; ?></td>
        </tr>
    <?php
}
?>
        <tr>
            <td class="sectiontableheader" align="right"><?php print JText::_("Total"); ?></td>
    <?php
    for ($w = 0; $w < $weeks; $w++) {
        foreach (array("PROJECT", "PTO", "HOLIDAY") as $type) {
            $hours = (empty($totals[$w][$type])) ? 0 : $totals[$w][$type];
            ?>
                <td class="sectiontablerow<?php print $k;?>" align="center"><?php print $hours; ?></td>
            <?php
        }
        $hours = (empty($totals[$w]["TOTAL"])) ? 0 : $totals[$w]["TOTAL"];
        ?>
                <td class="sectiontablerow<?php print $k;?>" style="font-weight: bold; text-align: center;"><?php print $hours; ?></td>
        <?php
    }
    $k = 1 - $k;
    $hours = (empty($totals["TOTAL"])) ? 0 : $totals["TOTAL"];
    ?>
            <td class="sectiontableheader" align="right"><?php print JText::_("Total"); ?></td>
            <td class="sectiontablerow<?php print $k;?>" style="font-weight: bold; text-align: center;"><?php print $hours; ?></td>
        </tr>
    </table>
</form>
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
    $tip = "Go to the next pay period";
    $img = "components".DS."com_timeclock".DS."images".DS."1rightarrow.png";
    $text = '<img src="'.$img.'" alt="&gt;" style="border: none;" />';
    $url = JROUTE::_("index.php?option=com_timeclock&view=reports&layout=payroll&date=".$obj->period["next"]);
    $nextImg = '<a href="'.$url.'">'.$text.'</a>';
    $next = '<a href="'.$url.'">'.JText::_("Next").'</a>';

    $tip = "Go to the previous pay period";
    $img = "components".DS."com_timeclock".DS."images".DS."1leftarrow.png";
    $text = '<img src="'.$img.'" alt="&lt;" style="border: none;" />';
    $url = JROUTE::_("index.php?option=com_timeclock&view=reports&layout=payroll&date=".$obj->period["prev"]);
    $prevImg = '<a href="'.$url.'">'.$text.'</a>';
    $prev = '<a href="'.$url.'">'.JText::_("Previous").'</a>';

    $text = JText::_('Today');
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
