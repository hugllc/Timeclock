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

?>
    <tr class="sectiontableentry<?php print $k; ?>">
        <td>
            <?php print JHTML::_('tooltip', $this->proj->description, 'Project', '', $this->proj->name); ?>
        </td>
<?php
$rowtotal = 0;
$dtotal = 0;
$d = 0;
foreach ($this->period["dates"] as $key => $uDate) {
    $hours               = ($this->hours[$this->proj->id][$key]) ? $this->hours[$this->proj->id][$key]['hours'] : 0;
    $rowtotal           += $hours;
    $dtotal             += $hours;
    if ($this->proj->noHours || !$this->proj->published || !$this->proj->mine || !$this->cat->published) {
        $tip                = $this->hours[$this->proj->id][$key]['notes'];
        $link = ($hours == 0) ? $hours : JHTML::_('tooltip', $tip, "Notes", '', " $hours ", $url);
    } else {
        if ($this->checkDate($uDate)) {     
            $tipTitle           = ($hours == 0) ? "Add Hours" : "Notes";
            $tip                = ($hours == 0) ? "for ".$this->proj->name." on ".JHTML::_('date', $uDate, JText::_("DATE_FORMAT_LC1")) : $this->hours[$this->proj->id][$key]['notes'];
            $url                = 'index.php?&option=com_timeclock&task=addhours&date='.urlencode($key).'&projid='.(int)$this->proj->id.'&id='.(int)$this->user->get("id");
        } else {
            $url = $hours;
            $tipTitle = "No Hours";
            $tip = "Hours can not be entered before your employment start date or after your end date";
        };
        $link = JHTML::_('tooltip', $tip, $tipTitle, '', " $hours ", $url);
    }
    ?>
        <td style="<?php print $this->cellStyle;?>">
            <?php print $link; ?>
        </td>
    <?php
    if ((++$d % $this->days) == 0) {
        ?>
        <td style="<?php print $this->totalStyle; ?>">
            <?php print $dtotal; ?>
        </td>
        <?php
        $this->totals[$d] += $dtotal;
        $dtotal = 0;
        
    }
}
?>
        <td style="<?php print $this->totalStyle; ?>">
            <?php print $this->totals["proj"][$this->proj->id]; ?>
        </td>
    </tr>
<?php $k = 1-$k; ?>