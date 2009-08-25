<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.5 component
 * Copyright (C) 2008-2009 Hunt Utilities Group, LLC
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
 * @copyright  2008-2009 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');

$headerColSpan    = 2 + count($this->totals["user"]);

$this->cellStyle  = "text-align:center; padding: 1px; vertical-align: center;";
$projStyle  = "text-align:right; padding: 1px;";
$totalStyle = $this->cellStyle." font-weight: bold;";
$document        =& JFactory::getDocument();
$dateFormat      = JText::_("DATE_FORMAT_LC1");
$shortDateFormat = JText::_("DATE_FORMAT_LC3");
$document->setTitle($this->params->get('page_title')." (".JHTML::_('date', $this->period['unix']["start"], $shortDateFormat)." ".JText::_("to")." ".JHTML::_('date', $this->period['unix']["end"], $shortDateFormat).")");


?>

<form action="<?php JROUTE::_("index.php"); ?>" method="post" name="adminForm" autocomplete="off">
    <?php if ($this->params->get('show_page_title')) : ?>
    <div class="componentheading<?php echo $this->params->get('pageclass_sfx');?>">
            <?php echo $this->escape($this->params->get('page_title')); ?>
    </div>
    <?php endif; ?>
    <table style="padding-bottom: 3em;" class="contentpaneopen<?php echo $this->params->get('pageclass_sfx');?>">
        <tr>
            <td colspan="<?php print $headerColSpan; ?>">
                <?php if (is_array($this->controls)) : ?>
                    <?php print $this->loadTemplate("controls"); ?>
                <?php else : ?>
                    <?php print $this->loadTemplate("nextprev"); ?>
                <?php endif; ?>

                <?php print $this->loadTemplate("export"); ?>

                <div id="dateheader" style="clear:both; white-space: nowrap;">
                   <strong>
                       <?php print JHTML::_('date', $this->period['unix']["start"], $dateFormat); ?>
                       <?php print JText::_("to"); ?>
                       <?php print JHTML::_('date', $this->period['unix']["end"], $dateFormat); ?>
                   </strong>
               </div>
           </td>
        </tr>
        <?php if (count($this->report) > 0) : ?>
        <?php print $this->loadTemplate("header"); ?>

<?php
$k = 0;
$totals = array();
foreach ($this->report as $cat => $projArray) {
    if (!empty($cat)) {
        if ($this->cat_by != "project") {
        ?>
        <tr>
            <td class="sectiontableheader" colspan="<?php print $headerColSpan; ?>" align="right" style="<?php print $this->cellStyle; ?>"><?php print JText::_($cat); ?></td>
        </tr>
        <?php
        }
    }
    $k = 0;
    foreach ($projArray as $proj => $userArray) {
        ?>
        <tr>
            <td class="sectiontableentry<?php print $k; ?>" style="<?php print $projStyle; ?>"><?php print JText::_($proj); ?></td>
        <?php
        foreach (array_keys($this->totals["user"]) as $user) {
            $hours = empty($userArray[$user]) ? $this->cell_fill : $userArray[$user];
            ?>
            <td class="sectiontableentry<?php print $k; ?>" style="<?php print $this->cellStyle; ?>"><?php print $hours; ?></td>
            <?php
        }
        ?>
            <td class="sectiontableentry<?php print $k; ?>" style="<?php print $totalStyle; ?>"><?php print $this->totals["proj"][$proj]; ?></td>
        </tr>
        <?php
        $k = 1-$k;
    }

}
?>
        <tr>
            <td class="sectiontableheader" align="right style="<?php print $totalStyle; ?>""><?php print JText::_("Total"); ?></td>
            <?php foreach ($this->totals["user"] as $user => $hours) : ?>
            <td class="sectiontableentry<?php print $k; ?>" style="<?php print $totalStyle; ?>"><?php print $hours; ?></td>
            <?php endforeach; ?>
            <td class="sectiontableheader" style="<?php print $totalStyle; ?>"><?php print $this->total; ?></td>
        </tr>
        <?php else : ?>
        <tr>
            <td class="sectiontableheader" colspan="<?php print $headerColSpan; ?>" align="right" style="<?php print $this->cellStyle; ?>"><?php print JText::_("No data found"); ?></td>
        </tr>
        <?php endif; ?>
    </table>
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
