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

$url  = "index.php?option=com_timeclock&view=reports&layout=hoursgraph";
$url .= "&startDate=".JHTML::_("date", $this->startDate, $this->sqlDateFormat);
$url .= "&endDate=".JHTML::_("date", $this->endDate, $this->sqlDateFormat);
$url .= "&userid=".(int)$this->userid;
$styles = array(
                "graphwidth",
                "graphheight",
                "margintop",
                "marginbottom",
                "marginleft",
                "marginright"
               );
foreach ($styles as $key) {
    if (!empty($this->$key)) {
        $url .= "&$key=".(int)$this->$key;
    }
}
    $name =  empty($this->users[$this->userid]) ? $this->userid : $this->users[$this->userid];
    $total = $this->totals["user"][$this->userid];
    ?>
    <tr>
        <td class="sectiontableheader" align="right" style="<?php print $this->cellStyle; ?>"><?php print $name; ?></td>
        <td>
            <img src="<?php print JRoute::_($url); ?>" alt="Data Graph Failed." align="center"/>
        </td>
        <td class="sectiontableentry<?php print $this->k; ?>" style="<?php print $totalStyle; ?>"><?php print $total; ?></td>
        <td class="sectiontableentry<?php print $this->k; ?>" style="<?php print $totalStyle; ?>">100%</td>
    </tr>
    <?php  $this->k = 1-$this->k; ?>
