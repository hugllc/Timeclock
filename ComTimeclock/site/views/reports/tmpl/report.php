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

$headerColSpan    = 3 + count($this->totals);

$cellStyle  = "text-align:center; padding: 1px;";
$projStyle  = "text-align:right; padding: 1px;";
$totalStyle = $cellStyle." font-weight: bold;";
$document        =& JFactory::getDocument();
$dateFormat      = JText::_("DATE_FORMAT_LC1");
$shortDateFormat = JText::_("DATE_FORMAT_LC3");
$document->setTitle("Payroll Summary for ".JHTML::_('date', $this->period['unix']["start"], $shortDateFormat)." to ".JHTML::_('date', $this->period['unix']["end"], $shortDateFormat));

?>

<form action="<?php JROUTE::_("index.php"); ?>" method="post" name="userform" autocomplete="off">
    <?php if ($this->params->get('show_page_title')) : ?>
    <div class="componentheading<?php echo $this->params->get('pageclass_sfx');?>">
            <?php echo $this->escape($this->params->get('page_title')); ?>
    </div>
    <?php endif; ?>
    <table style="padding-bottom: 3em;" class="contentpaneopen<?php echo $this->params->get('pageclass_sfx');?>">
        <tr>
            <td colspan="<?php print $headerColSpan; ?>">
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
            <td class="sectiontableheader" width="50px" style="<?php print $cellStyle; ?>"><?php print JText::_("Project"); ?></td>
            <?php foreach (array_keys($this->totals["user"]) as $user) : ?>
            <?php $user = implode("<br />", str_split($user, 1)); ?>
            <td class="sectiontableheader" width="5px" style="<?php print $cellStyle; ?> vertical-align: bottom;"><?php print $user; ?></td>
            <?php endforeach; ?>            
            <?php $total = implode("<br />", str_split(JText::_("Totals"), 1)); ?>
            <td class="sectiontableheader" width="5px" style="<?php print $cellStyle; ?> vertical-align: bottom;"><?php print $total; ?></td>
        </tr>
<?php
$k = 0;
$totals = array();
foreach ($this->report as $cat => $projArray) {
    if (!empty($cat)) {
        ?>
        <tr>
            <td class="sectiontableheader" colspan="<?php print $headerColSpan; ?>" align="right" style="<?php print $cellStyle; ?>"><?php print JText::_($cat); ?></td>
        </tr>
        <?php
    }
    $k = 0;
    foreach ($projArray as $proj => $userArray) {
        ?>
        <tr>
            <td class="sectiontablerow<?php print $k; ?>" style="<?php print $projStyle; ?>"><?php print JText::_($proj); ?></td>
        <?php
        foreach (array_keys($this->totals["user"]) as $user) {
            $hours = empty($userArray[$user]) ? $this->cell_fill : $userArray[$user];
            ?>
            <td class="sectiontablerow<?php print $k; ?>" style="<?php print $cellStyle; ?>"><?php print $hours; ?></td>
            <?php           
        }
        ?>
            <td class="sectiontablerow<?php print $k; ?>" style="<?php print $totalStyle; ?>"><?php print $this->totals["proj"][$proj]; ?></td>
        </tr>
        <?php
        $k = 1-$k;
    }

}
?>
        <tr>
            <td class="sectiontableheader" align="right style="<?php print $totalStyle; ?>""><?php print JText::_("Total"); ?></td>
            <?php foreach ($this->totals["user"] as $user => $hours) : ?>
            <td class="sectiontablerow<?php print $k; ?>" style="<?php print $totalStyle; ?>"><?php print $hours; ?></td>
            <?php endforeach; ?>            
            <td class="sectiontableheader" style="<?php print $totalStyle; ?>"><?php print $this->total; ?></td>
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
    $url = JROUTE::_("index.php?option=com_timeclock&view=reports&layout=report&date=".$obj->period["next"]);
    $nextImg = '<a href="'.$url.'">'.$text.'</a>';
    $next = '<a href="'.$url.'">'.JText::_("Next").'</a>';

    $tip = "Go to the previous pay period";
    $img = "components".DS."com_timeclock".DS."images".DS."1leftarrow.png";
    $text = '<img src="'.$img.'" alt="&lt;" style="border: none;" />';
    $url = JROUTE::_("index.php?option=com_timeclock&view=reports&layout=report&date=".$obj->period["prev"]);
    $prevImg = '<a href="'.$url.'">'.$text.'</a>';
    $prev = '<a href="'.$url.'">'.JText::_("Previous").'</a>';

    $text = JText::_('Today');
    $url = JROUTE::_("index.php?option=com_timeclock&view=reports&layout=report");
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
