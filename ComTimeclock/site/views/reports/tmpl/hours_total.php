<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.5 component
 * Copyright (C) 2008-2009, 2011 Hunt Utilities Group, LLC
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
 * @copyright  2008-2009, 2011 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die('Restricted access');
?>
        <tr class="row<?php print $this->k; ?>">
            <th align="right style="<?php print $totalStyle; ?>""><?php print JText::_(COM_TIMECLOCK_TOTAL); ?></th>
            <?php foreach ($this->totals["cat"] as $cat => $hours) : ?>
            <?php $hours = empty($hours) ? $this->cell_fill : $hours; ?>
            <?php $perc = round(($hours/$this->total)*100, 1); ?>
            <td style="<?php print $this->cellStyle; ?>"><?php print $hours; ?></td>
            <td style="<?php print $this->cellStyle; ?>"><?php print $perc; ?>%</td>
            <?php endforeach; ?>
            <td style="<?php print $totalStyle; ?>"><?php print $this->total; ?></td>
            <td style="<?php print $totalStyle; ?>">100%</td>
        </tr>
