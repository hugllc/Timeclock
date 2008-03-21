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


$this->totals     = array();
if (empty($this->days)) $this->days = 7;

$headerColSpan    = ($this->period["length"]+2+($this->period["length"]/$this->days));

$this->cellStyle  = "text-align:center; padding: 1px;";
$this->totalStyle = $this->cellStyle." font-weight: bold;";
$document        =& JFactory::getDocument();
$dateFormat      = JText::_("DATE_FORMAT_LC1");
$shortDateFormat = JText::_("DATE_FORMAT_LC3");
$document->setTitle(JText::_("Timeclock Reports"));
JHTML::_('behavior.tooltip');
JHTML::_('behavior.formvalidation');

?>
<script type="text/javascript">
        Window.onDomReady(function(){
            document.formvalidator.setHandler('dateverify', 
                function (value) {
                    regex=/[1-9][0-9]{3}-[0-1]{0,1}[0-9]-[0-3]{0,1}[0-9]/;
                    return regex.test(value); 
                }     
            );
        });
</script>

<form action="<?php JRoute::_("index.php"); ?>" method="post" name="userform" autocomplete="off" class="form-validate">
    <input type="hidden" name="option" value="com_timeclock" />
    <input type="hidden" name="task" value="reports" />
    <input type="hidden" name="view" value="reports" />
    <div class="componentheading"><?php print JText::_("Timeclock Reports");?></div>
    <table>
        <tr>
            <td width="100" align="right" class="sectiontableheader">
                <label for="startDate">
                    <?php echo JText::_('Start Date'); ?>:
                </label>
            </td>
            <td style="white-space:nowrap;">
                <?php print JHTML::_("calendar", $this->period["start"], "startDate", "startDate", "%Y-%m-%d", 'class="inputbox validate-dateverify required date_label"');?>
            </td>
            <td>
                The date to start the report
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="sectiontableheader">
                <label for="endDate">
                    <?php echo JText::_('End Date'); ?>:
                </label>
            </td>
            <td style="white-space:nowrap;">
                <?php print JHTML::_("calendar", $this->period["end"], "endDate", "endDate", "%Y-%m-%d", 'class="inputbox validate-dateverify required date_label"');?>
            </td>
            <td>
                The date to end the report
            </td>
        </tr>
        <tr>
            <th style="vertical-align: top;">
                 &nbsp;
            </th>
            <td>
                <button type="submit" class="button validate"><?php print JText::_("Apply"); ?></button>
            </td>
        </tr>    
    </table>
</form>
<?php include "report.php"; ?>