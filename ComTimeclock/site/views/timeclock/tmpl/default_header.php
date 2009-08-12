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

$headerDateFormat = 'D <b\r/>M<b\r>d';
?>
    <tr>
        <td class="sectiontableheader">Project</td>
<?php
$today = date("Y-m-d");
$d = 0;
foreach ($this->period["dates"] as $key => $uDate) :
    if ($key == $today) {
        $style = "background: ".$this->today_background."; color: ".$this->today_color.";";
    } else {
        $style = "";
    }
    if ($this->checkDate($uDate)) {
        $url = JRoute::_('index.php?&option=com_timeclock&task=addhours&date='.urlencode($key).'&id='.(int)$this->user->get("id"));
        $tipTitle = "Add Hours";
        $tip = "on ".JHTML::_('date', $uDate, JText::_("DATE_FORMAT_LC1"));
    } else {
        $url = "";
        $tipTitle = "No Hours";
        $tip = "Hours can not be entered before your employment start date or after your end date";
    };
    ?>
        <td class="sectiontableheader" style="<?php print $this->cellStyle.$style; ?>">
            <?php print JHTML::_('tooltip', $tip, $tipTitle, '', date($headerDateFormat, $uDate), $url); ?>
        </td>
    <?php if ((++$d % $this->days) == 0) : ?>
        <td class="sectiontableheader">
            Wk<?php print (int) ($d / $this->days); ?>
        </td>
        <?php $dtotal = 0; ?>

    <?php endif; ?>
<?php endforeach; ?>
        <td class="sectiontableheader"><?php print JText::_("Total"); ?></td>
    </tr>
