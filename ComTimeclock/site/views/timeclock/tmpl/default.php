<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.6 component
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @copyright  2014 Hunt Utilities Group, LLC
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
$this->catStyle   = "font-weight: bold; padding: 1px; text-align: left;";
$this->rowk       = 0;
$document         = JFactory::getDocument();
$dateFormat       = JText::_("DATE_FORMAT_LC1");
$shortDateFormat  = JText::_("DATE_FORMAT_LC3");
$document->setTitle(
    JText::sprintf(
        "COM_TIMECLOCK_TIMESHEET_TITLE",
        $this->user->get("name"),
        JHTML::_('date', $this->period['unix']["start"], $shortDateFormat),
        JHTML::_('date', $this->period['unix']["end"], $shortDateFormat)
    )
);

JHTML::_('tabs.start', "sliders");
$initPanes = array();
JHTML::script("category.js", JURI::base()."components/com_timeclock/views/timeclock/tmpl/");
//JHTML::_('behavior.mootools');
?>
<div id="timeclock">
<form action="<?php JROUTE::_("index.php"); ?>" method="post" name="userform" autocomplete="off">
    <div class="componentheading"><?php print JText::sprintf("COM_TIMECLOCK_TIMESHEET_FOR", $this->user->get("name"));?></div>
    <?php print $this->loadTemplate("nextprev"); ?>
    <div id="dateheader" style="clear:both;">
        <strong>
            <?php print JText::sprintf(
                "COM_TIMECLOCK_DATE_TO_DATE",
                JHTML::_('date', $this->period['unix']["start"], $dateFormat),
                JHTML::_('date', $this->period['unix']["end"], $dateFormat)
                ); ?>
        </strong>
    </div>

    <table id="timeclockTable">
    <?php print $this->loadTemplate("header"); ?>
<?php
$rows = 0;
//var_dump($this->projects);
foreach ($this->projects as $cat) {
    if (($cat->mine == false) || !$cat->published) {
        $array = array_intersect_key($cat->subprojects, $this->hours);
        if (empty($array)) continue;
    }
    $this->cat  =& $cat;
    $safeName = JText::_("JCATEGORY").$cat->id;
    if ($cat->show === true) {
        $initPanes[] = "timeclockCatShow('".$safeName."');";
    } else if ($cat->show === false) {
        $initPanes[] = "timeclockCatHide('".$safeName."');";
    } else {
        $initPanes[] = "timeclockCatShowHide('".$safeName."', true);";
    }
    ?>
        <tr>
            <th style="<?php print $this->catStyle; ?>" colspan="<?php print $headerColSpan; ?>">
                <a href="JavaScript: timeclockCatShowHide('<?php print $safeName; ?>');">
                    <span id="<?php print $safeName; ?>_cat_span"> - </span>
                    <?php print JHTML::_('tooltip', JText::_($cat->description), JText::_("JCATEGORY"), '', JText::_($cat->name)); ?>
                </a>
            </th>
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
    <?php
}

$document = JFactory::getDocument();
$js = 'window.addEvent(\'domready\', function() {'.implode(" ", $initPanes).'});';
$document->addScriptDeclaration($js);


print $this->loadTemplate("header");

?>

        <tr class="row<?php print (int)$this->rowk; ?>">
            <th style="text-align:right; padding: 1px;">
                <span><?php print JText::_("COM_TIMECLOCK_SUBTOTALS"); ?></span>
            </th>
<?php
$d = 0;
$k = 0;
foreach ($this->period["dates"] as $key => $uDate) {
    print '            <td style="'.$this->totalStyle.'">';
    print '                '.(isset($this->totals["worked"][$key]) ? $this->totals["worked"][$key] : 0)."\n";
    print "            </td>\n";
    if ((++$d % $this->days) == 0) {
        print '            <th>';
        print '                &nbsp;'."\n";
        print "            </th>\n";
        $dtotal = 0;
    }
}

$k = 1-$k;
?>
            <th>
                &nbsp;
            </th>
        </tr>

        <tr class="row<?php echo $k?>">
            <th style="text-align:right; padding: 1px;">
                <span><?php print JText::_("COM_TIMECLOCK_PERIODIC_SUBTOTALS"); ?></span>
            </th>
<?php
for ($i = $this->days; $i <= $headerColSpan; $i+=$this->days) {
    ?>
            <th style="<?php print $this->cellStyle; ?>" colspan="<?php print $this->days; ?>">
                &nbsp
            </th>
            <td style="<?php print $this->totalStyle; ?>">
                <?php print (float)$this->totals[$i]; ?>
            </td>
    <?php
}
$k = 1-$k;

?>
            <th>
                &nbsp;
            </th>
        </tr>
        <tr class="row<?php echo $k?>">
            <th style="text-align:right; padding: 1px;" colspan="<?php echo $headerColSpan-1; ?>">
                <?php print JText::_("COM_TIMECLOCK_TOTAL"); ?>
            </th>
            <td style="<?php print $this->totalStyle; ?>">
                <?php print isset($this->totals["total"]) ? $this->totals["total"] : 0; ?>
            </td>
        </tr>

    </table>

    <?php print $this->loadTemplate("nextprev"); ?>
<input type="hidden" name="option" value="com_timeclock" />
<input type="hidden" name="view" value="timeclock" />
<?php print JHTML::_("form.token"); ?>
</form>
</div>