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
jimport("joomla.html.pane");

if (empty($this->days)) $this->days = 7;

$headerColSpan    = ($this->period["length"]+2+($this->period["length"]/$this->days));

$this->cellStyle  = "text-align:center; padding: 1px;";
$this->totalStyle = $this->cellStyle." font-weight: bold;";
$this->catStyle   = "font-weight: bold; padding: 1px;";
$document         =& JFactory::getDocument();
$dateFormat       = JText::_("DATE_FORMAT_LC1");
$shortDateFormat  = JText::_("DATE_FORMAT_LC3");
$document->setTitle("Timesheet for ".$this->user->get("name")." - ".JHTML::_('date', $this->period['unix']["start"], $shortDateFormat)." to ".JHTML::_('date', $this->period['unix']["end"], $shortDateFormat));

$pane = JPane::getInstance("sliders");
$initPanes = array();
JHTML::script("category.js", JURI::base()."components/com_timeclock/views/timeclock/tmpl/");

?>
<form action="<?php JROUTE::_("index.php"); ?>" method="post" name="userform" autocomplete="off">
    <div class="componentheading"><?php print JText::_("Timesheet for ").$this->user->get("name");?></div>
    <?php print $this->loadTemplate("nextprev"); ?>
    <div id="dateheader" style="clear:both;">
        <strong>
            <?php print JHTML::_('date', $this->period['unix']["start"], $dateFormat); ?>
            <?php print JText::_("to"); ?>
            <?php print JHTML::_('date', $this->period['unix']["end"], $dateFormat); ?>
        </strong>
    </div>
    <table cellpadding="5" cellspacing="0" border="0" width="100%">
    <?php print $this->loadTemplate("header"); ?>
<?php
$rows = 0;
foreach ($this->projects as $cat) {
    if (($cat->mine == false) || !$cat->published) {
        $array = array_intersect_key($cat->subprojects, $this->hours);
        if (empty($array)) continue;
    }
    $this->cat  =& $cat;
    $safeName = "Category".$cat->id;
    if ($cat->show === true) {
        $initFunction = "timeclockCatShow('".$safeName."');";
    } else if ($cat->show === false) {
        $initFunction = "timeclockCatHide('".$safeName."');";
    } else {
        $initFunction = "timeclockCatShowHide('".$safeName."', true);";
    }
    ?>
        <tr>
            <td class="sectiontableheader" style="<?php print $this->catStyle; ?>" colspan="<?php print $headerColSpan; ?>">
                <a href="JavaScript: timeclockCatShowHide('<?php print $safeName; ?>');">
                    <span id="<?php print $safeName; ?>_cat_span"> - </span>
                    <?php print JHTML::_('tooltip', $cat->description, 'Category', '', $cat->name); ?>
                </a>
            </td>
        </tr>
        <tbody id="<?php print $safeName; ?>_cat" class="pane">
    <?php
    if (array_key_exists($cat->id, $this->hours)) projectRow($this, $cat);
    foreach ($cat->subprojects as $pKey => $proj) {
        if (($proj->mine == false) || !$proj->published) {
            if (!array_key_exists($proj->id, $this->hours)) continue;
        }
        $this->proj =& $proj;
        print $this->loadTemplate("row");
    }
    ?>
    </tbody>
    <script type="text/javascript"><?php print $initFunction; ?></script>
    <?php
}
print $this->loadTemplate("header");
?>
        <tr class="sectiontableentry<?php echo $k?>">
            <td class="sectiontableheader" style="text-align:right; padding: 1px;">
                Subtotals
            </td>
<?php
$d = 0;
foreach ($this->period["dates"] as $key => $uDate) {
    $hours = ($this->totals["worked"][$key]) ? $this->totals["worked"][$key] : 0;
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
        <tr class="sectiontableentry<?php echo $k?>">
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
    <?php print $this->loadTemplate("nextprev"); ?>
<input type="hidden" name="option" value="com_timeclock" />
<input type="hidden" name="view" value="timeclock" />
</form>
