<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.5 component
 * Copyright (C) 2008-2009 Hunt Utilities Group, LLC
 * Copyright 2009 Scott Price
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
 * @package    ComHUGnet
 * @subpackage Com_HUGnet
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die('Restricted access');

if ($this->jpgraph_path === false) return;
require $this->jpgraph_path.DS."jpgraph.php";
require $this->jpgraph_path.DS."jpgraph_line.php";
require $this->jpgraph_path.DS."jpgraph_bar.php";
require $this->jpgraph_path.DS."jpgraph_date.php";

$margin = array(
    "top"    => empty($this->margintop)    ? 20  : $this->margintop,
    "bottom" => empty($this->marginbottom) ? 180 : $this->marginbottom,
    "left"   => empty($this->marginleft)   ? 70  : $this->marginleft,
    "right"  => empty($this->marginright)  ? 70  : $this->marginright,
);
if (empty($this->graphwidth)) $this->graphwidth = 600;
if (empty($this->graphheight)) $this->graphheight = ($this->graphwidth * 5) / 6;
if ($margin["bottom"] > ($this->graphheight / 2)) $margin["bottom"] = $this->graphheight / 2;
if (is_numeric($this->doLegend)) $this->doLegend = (bool) $this->doLegend;
if (!is_bool($this->doLegend)) $this->doLegend = true;

// These are the graph colors that will be used, in order
$colors = array("aqua", "azure4", "blue", "blueviolet", "brown3", "chartreuse", "coral", "cornflowerblue", "darkcyan",
"darkorange", "darkorchid", "darkseagreen", "deeppink", "deepskyblue", "gold", "firebrick1", "magenta", "midnightblue",
"olivedrab", "yellow");

$unitConversion = new unitConversion();
// Create the lines
for ($i = 0; $i < $this->devInfo["TotalSensors"]; $i++) {
    if (($i >= $this->devInfo["ActiveSensors"]) && ($i < $this->devInfo["NumSensors"])) continue;
    if (!$unitConversion->graphable($this->devInfo["Units"][$i])) continue;

    // Skip if we are to ignore this one anyway
    if ($this->devInfo["dType"][$i] == "ignore") continue;

    // Set the units to a small variable because we use them so often here
    $u =& $this->devInfo["Units"][$i];

    $aUnits[$u][$i] = $i;
    if (!isset($aUTypes[$u])) $aUTypes[$u] = $this->devInfo["unitType"][$i];
}
$units = array();
$lines = array();
if (count($aUnits) == 0) {
    return;
} else if (count($aUnits) == 1) {
    list($units[1], $lines[1]) = each($aUnits);
} else {
    $gUnits =& $this->devInfo["params"]["graphUnits"];
    for($i = 0; $i < 2; $i++) {
        // Try the expected units first.
        if (!empty($aUnits[$gUnits[$i]])) {
            $lines[$i+1] = $aUnits[$gUnits[$i]];
            $units[$i+1] = $gUnits[$i];
            unset($aUnits[$gUnits[$i]]);
        }
    }
    // Now just get the first ones if the above failed
    for($i = 1; $i < 3; $i++) {
        if (empty($lines[$i])) list($units[$i], $lines[$i]) = each($aUnits);
    }

}

// Create the columns we need for the graph
$history = array();
$dates   = array();
$index = 0;
$count = count($this->history) / 20;
foreach (array_reverse($this->history) as $data) {
    $data["Date"] = date("Y-m-d H:i:s", HUGnetHelper::fixDST($data["Date"]));
    $dates[$index] = JHTML::_("date", $data["Date"], "%s");
    for ($y = 1; $y < 3; $y++) {
        if (!is_array($lines[$y])) continue;
        foreach($lines[$y] as $i) {
            $history[$y][$i][$index] = (float) $data["Data".$i];
        }
    }
    $index++;
}
// Create the graph
$graph = new Graph($this->graphwidth, $this->graphheight, "auto");
$graph->SetScale("datlin");
// Set the graph title
$title = JText::_($this->devInfo["DeviceName"]);
if (!empty($this->devInfo["DeviceLocation"])) $title .= " (".JText::_($this->devInfo["DeviceLocation"]).")";
$graph->title->Set($title);
// The classes
$class = array(
    1 => "LinePlot",
    2 => "LinePlot",
);

$labels = array();

// Create the actual graph lines.
foreach ($history as $index => $hist) {
    foreach ($hist as $i => $h) {
        // Create the line
        $c = $class[$index];

        $plot[$i] = new $c($h, $dates);

        // Figure out what the legend is going to be
        $leg = empty($this->devInfo["params"]["Loc"][$i]) ? $this->devInfo["Labels"][$i]." ".($i+1) : $this->devInfo["params"]["Loc"][$i];

        if ($this->doLegend) $plot[$i]->SetLegend($leg);
        // Set the color
        $plot[$i]->setColor($colors[$i]);
        // Attach it to one axis or the other.
        if ($index == 1) $graph->Add($plot[$i]);
        else $graph->AddY2($plot[$i]);
    }
}

// Set up the second axis if we have one.
if (!empty($units[2])) $graph->SetY2Scale("lin");

// Set up the axis
$graph->xgrid->Show(true);
$graph->xaxis->SetLabelAngle(90);
$graph->yaxis->title->Set(JText::_($aUTypes[$units[1]])." (".html_entity_decode($units[1]).")");
if (!empty($units[2])) $graph->y2axis->title->Set(JText::_($aUTypes[$units[2]])." (".html_entity_decode($units[2]).")");
// Setup the legend
if ($this->doLegend) {
    $graph->legend->SetLayout(LEGEND_HOR);
    $graph->legend->Pos(0.5, .99, "center", "bottom");
    $graph->legend->SetColumns(3);
}
// Set the margins
//$graph->img->SetMargin(70, 70, 20, 180);
$graph->img->SetMargin($margin["left"], $margin["right"], $margin["top"], $margin["bottom"]);

// Set fonts.
$graph->title->SetFont(FF_FONT1, FS_BOLD);
if (!empty($units[2])) $graph->y2axis->title->SetFont(FF_FONT1, FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1, FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD);
$graph->xaxis->SetPos("min");

ob_end_clean();
$graph->Stroke();

?>