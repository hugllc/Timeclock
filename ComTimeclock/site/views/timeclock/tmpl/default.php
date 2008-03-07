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
$document->setTitle("Timesheet for ".$this->user->get("name")." - ".JHTML::_('date', $this->period["start"], $shortDateFormat)." to ".JHTML::_('date', $this->period["end"], $shortDateFormat));

?>

<form action="index.php" method="post" name="userform" autocomplete="off">
    <div class="componentheading"><?php print JText::_("Timesheet for ").$this->user->get("name");?></div>
    <table cellpadding="5" cellspacing="0" border="0" width="100%">
        <theader>
            <strong>
                <?php print JHTML::_('date', $this->period["start"], $dateFormat); ?>
                <?php print JText::_("to"); ?>
                <?php print JHTML::_('date', $this->period["end"], $dateFormat); ?>
            </strong>
        </theader>
<?php
tableHeader($this);
$rows = 0;
foreach ($this->projects as $cat) {
    if (($cat->mine == false) || !$cat->published){
        $array = array_intersect_key($cat->subprojects, $this->hours);
        if (empty($array)) continue;
    }
    ?>
        <tr>
            <td class="sectiontableheader" colspan="<?php print $headerColSpan; ?>">
                <?php print JHTML::_('tooltip', $cat->description, 'Category', '', $cat->name); ?>
            </td>
        </tr>    
    <?php
    if (array_key_exists($cat->id, $this->hours)) projectRow($this, $cat);
    foreach ($cat->subprojects as $pKey => $proj) {
        if (($proj->mine == false) || !$proj->published) {
            if (!array_key_exists($proj->id, $this->hours)) continue;
        }
        projectRow($this, $proj, $cat);
    }
}
tableHeader($this);
?>
        <tr class="sectiontablerow<?php echo $k?>">
            <td class="sectiontableheader" style="text-align:right; padding: 1px;">
                Subtotals
            </td>
<?php
$d = 0;
foreach ($this->period["dates"] as $key => $uDate) {
    $hours = ($this->totals[$key]) ? $this->totals[$key] : 0;
    print '            <td style="'.$this->totalStyle.'">';
    print '                '.$hours."\n";
    print "            </td>\n";
    if ((++$d % $this->days) == 0) {
        print '            <td class="sectiontableheader">';
        print '                &nbsp;'."\n";
        print "            </td>\n";
        $dtotal = 0;
    }
}

$k = 1-$k;
?>
            <td class="sectiontableheader">
                &nbsp;
            </td>
        </tr>
        <tr class="sectiontablerow<?php echo $k?>">
            <td class="sectiontableheader" style="text-align:right; padding: 1px;">
                Periodic Subtotals
            </td>
<?php
for ($i = $this->days; $i <= $headerColSpan; $i+=$this->days) {
    ?>
            <td class="sectiontableheader" style="<?php print $this->cellStyle; ?>" colspan="<?php print $this->days; ?>">
                &nbsp
            </td>
            <td style="<?php print $this->totalStyle; ?>">
                <?php print $this->totals[$i]; ?>
            </td>
    <?php
}
$k = 1-$k;

?>
            <td class="sectiontableheader">
                &nbsp;
            </td>
        </tr>
        <tr>
            <td class="sectiontableheader" style="text-align:right; padding: 1px;" colspan="<?php echo $headerColSpan-1; ?>">
                <?php print JText::_("Total"); ?>
            </td>
            <td style="<?php print $this->totalStyle; ?>">
                <?php print $this->totals["total"]; ?>
            </td>
        </tr>
    </table>
</form>

<?php
/**
 * Prints out the header
 *
 * @param object &$obj Pass it $this
 *
 * @return null
 */ 
function tableHeader(&$obj)
{
    $headerDateFormat = 'D <b\r/>M<b\r>d';
    ?>
        <tr>
            <td class="sectiontableheader">Project</td>
    <?php
    $today = date("Y-m-d");
    $d = 0;
    foreach ($obj->period["dates"] as $key => $uDate) {
        $style = ($key == $today) ? "background: #00FF00; color: #000000;" : "";
        if (($uDate >= $obj->employmentDates["start"]) && ($uDate <= $obj->employmentDates["end"])) {     
            $url = JRoute::_('index.php?&option=com_timeclock&task=addhours&date='.urlencode($key).'&id='.(int)$obj->user->get("id"));
            $tipTitle = "Add Hours";
            $tip = "on ".JHTML::_('date', $uDate, JText::_("DATE_FORMAT_LC1"));
        } else {
            $url = "";
            $tipTitle = "No Hours";
            $tip = "Hours can not be entered before your employment start date or after your end date";
        };
        ?>
            <td class="sectiontableheader" style="<?php print $obj->cellStyle.$style; ?>">
                <?php print JHTML::_('tooltip', $tip, $tipTitle, '', date($headerDateFormat, $uDate), $url); ?>
            </td>
        <?php
        if ((++$d % $obj->days) == 0) {
            ?>
            <td class="sectiontableheader">
                Wk<?php print (int) ($d / $obj->days); ?>
            </td>
            <?php
            $dtotal = 0;
        }
    }
    ?>
            <td class="sectiontableheader"><?php print JText::_("Total"); ?></td>
        </tr>
    <?php
}
/**
 * Prints out the header
 *
 * @param object &$obj  Pass it $this
 * @param object &$proj Pass it the project to print
 * @param object &$cat Pass it the category of the project
 *
 * @return null
 */ 
function projectRow(&$obj, &$proj, &$cat)
{
    ?>
        <tr class=\"sectiontablerow$k\">
            <td>
                <?php print JHTML::_('tooltip', $proj->description, 'Project', '', $proj->name); ?>
            </td>
    <?php
    $rowtotal = 0;
    $dtotal = 0;
    $d = 0;
    foreach ($obj->period["dates"] as $key => $uDate) {
        $hours              = ($obj->hours[$proj->id][$key]) ? $obj->hours[$proj->id][$key]['hours'] : 0;
        $rowtotal          += $hours;
        $obj->totals[$key] += $hours;
        $dtotal            += $hours;
        if ($proj->noHours || !$proj->published || !$proj->mine || !$cat->published) {
            $link = $hours;
        } else {
            if (($uDate >= $obj->employmentDates["start"]) && ($uDate <= $obj->employmentDates["end"])) {     
                $tipTitle           = ($hours == 0) ? "Add Hours" : "Work Notes";
                $tip                = ($hours == 0) ? "for ".$proj->name." on ".JHTML::_('date', $uDate, JText::_("DATE_FORMAT_LC1")) : $obj->hours[$proj->id][$key]['notes'];
                $url                = 'index.php?&option=com_timeclock&task=addhours&date='.urlencode($key).'&projid='.(int)$proj->id.'&id='.(int)$obj->user->get("id");
            } else {
                $url = $hours;
                $tipTitle = "No Hours";
                $tip = "Hours can not be entered before your employment start date or after your end date";
            };
            $link = JHTML::_('tooltip', $tip, $tipTitle, '', " $hours ", $url);
        }
        ?>
            <td style="<?php print $obj->cellStyle;?>">
                <?php print $link; ?>
            </td>
        <?php
        if ((++$d % $obj->days) == 0) {
            ?>
            <td style="<?php print $obj->totalStyle; ?>">
                <?php print $dtotal; ?>
            </td>
            <?php
            $obj->totals[$d] += $dtotal;
            $dtotal = 0;
            
        }
    }
    $obj->totals["total"] += $rowtotal;
    ?>
            <td style="<?php print $obj->totalStyle; ?>">
                <?php print $rowtotal; ?>
            </td>
        </tr>
    <?php
    $k = 1-$k;
}
?>