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

$headerDateFormat = 'D <b\r/>M<b\r>d';
?>
    <tr>
        <th>Project</th>
<?php
$today = date("Y-m-d");
$d = 0;
foreach ($this->period["dates"] as $key => $uDate) :
    if ($key == $today) {
        $class = " today";
    } else {
        $class = "";
    }
    if ($this->checkDate($uDate)) {
        $url = JRoute::_('index.php?&option=com_timeclock&task=timeclock.addhours&date='.urlencode($key).'&id='.(int)$this->user->get("id"));
        $tipTitle = JText::_("COM_TIMECLOCK_ADD_HOURS");
        $tip = "on ".JHTML::_('date', $uDate, JText::_("DATE_FORMAT_LC1"));
    } else {
        $url = "";
        $tipTitle = JText::_("COM_TIMECLOCK_NO_HOURS");
        $tip = JText::_("COM_TIMECLOCK_NO_HOURS_BEFORE_START");
    };
    $date = JFactory::getDate($uDate);
    ?>
        <th class="<?php print $class; ?>" style="<?php print $this->cellStyle; ?>">
            <?php print JHTML::_('tooltip', $tip, $tipTitle, '', $date->format($headerDateFormat), $url); ?>
        </th>
    <?php if ((++$d % $this->days) == 0) : ?>
        <th>
            <span><?php print JText::_("COM_TIMECLOCK_WEEK_ABBREV").(int) ($d / $this->days); ?></span>
        </th>
        <?php $dtotal = 0; ?>

    <?php endif; ?>
<?php endforeach; ?>
        <th><?php print JText::_("COM_TIMECLOCK_TOTAL"); ?></th>
    </tr>
