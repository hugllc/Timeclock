<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.5 component
 * Copyright (C) 2008-2009, 2011 Hunt Utilities Group, LLC
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
 * @copyright  2008-2009, 2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die('Restricted access');

if (empty($this->jpgraph_path)) {
    return;
}
require $this->jpgraph_path.DS."jpgraph.php";
require $this->jpgraph_path.DS."jpgraph_pie.php";
require $this->jpgraph_path.DS."jpgraph_pie3d.php";

if (empty($this->graphwidth)) $this->graphwidth = 500;
if (empty($this->graphheight)) $this->graphheight = ($this->graphwidth*3)/5;
if ($margin["bottom"] > ($this->graphheight / 2)) $margin["bottom"] = $this->graphheight / 2;


// Create the graph
$graph = new PieGraph($this->graphwidth, $this->graphheight);
$graph->SetShadow();
// Set the graph title
$graph->title->Set($this->user);
$graph->title->SetFont(FF_FONT2,FS_BOLD);

$p1 = new PiePlot($this->data);
$p1->SetTheme("earth");
//$p1->SetLegends($this->cats);
//$p1->SetLabelType(PIE_VALUE_PER);
$p1->SetLabels($this->cats);
$p1->SetLabelPos(1.05);
if (count($this->data) > 10) {
    $p1->value->SetFont(FF_FONT0);
} else {
    $p1->value->SetFont(FF_FONT1);
}
$p1->value->SetColor('black');
$p1->SetGuideLines(true,true,true);
//$p1->SetGuideLinesAdjust(1.5);
$p1->SetSize(0.25);
$p1->SetLabelType(PIE_VALUE_PER);
$p1->value->Show();
$p1->value->SetFormat('%2.1f%%');
$p1->value->Show();

//$p1->SetCenter(0.3);
$graph->Add($p1);
$graph->img->SetMargin($margin["left"], $margin["right"], $margin["top"], $margin["bottom"]);

ob_end_clean();
$graph->Stroke();

?>