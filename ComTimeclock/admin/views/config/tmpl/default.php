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
jimport("joomla.html.pane");

TimeclockAdminController::title(JText::_('Timeclock Preferences'));

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div>
<?php
    $pane = JPane::getInstance("tabs");
    echo $pane->startPane("config-pane");  
    echo $pane->startPanel(JText::_("User Settings"), "user-page");
?>
    <table class="admintable">
        <tr>
            <td width="100" align="right" class="key">
                <label for="maxDailyHours">
                    <?php echo JText::_('Max Daily Hours'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.integerList", 1, 24, 1, "prefs[maxDailyHours]", "", $this->prefs["maxDailyHours"]); ?>
            </td>
            <td>
                The maximum number of hours an employee is allowed to post in a day.
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="decimalPlaces">
                    <?php echo JText::_('Decimal Places'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.integerList", 0, 5, 1, "prefs[decimalPlaces]", "", $this->prefs["decimalPlaces"]); ?>
            </td>
            <td>
                Default number of decimal places to show.
            </td>
        </tr>
    </table>
<?php
    echo $pane->endPanel();
    echo $pane->startPanel(JText::_("Pay Period"), "payperiod-pane");
?>
    <table class="admintable">
        <tr>
            <td width="100" align="right" class="key">
                <label for="payPeriodLength">
                    <?php echo JText::_('Pay Period Length'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("calendar", $this->prefs["firstPayPeriodStart"], "prefs[firstPayPeriodStart]", "prefsfirstPayPeriodStart", "%Y-%m-%d", "");?>
            </td>
            <td>
                When the first pay period starts
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="payPeriodLength">
                    <?php echo JText::_('Pay Period Length'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.genericList", $this->payPeriodTypeOptions, "prefs[payPeriodType]", "", 'value', 'text', $this->prefs["payPeriodLength"]); ?>
            </td>
            <td>
                The type of payperiod
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="payPeriodLength">
                    <?php echo JText::_('Pay Period Length'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.integerList", 1, 31, 1, "prefs[payPeriodLengthFixed]", "", $this->prefs["payPeriodLengthFixed"]); ?>
            </td>
            <td>
                The length of the pay period in days for a fixed length pay period
            </td>
        </tr>
    </table>
<?php
    echo $pane->endPanel();
    echo $pane->startPanel(JText::_("Worker's Compensation"), "wcomp-pane");
?>
    <table class="admintable">
        <tr>
            <td width="100" align="right" class="key">
                <label for="wCompEnable">
                    <?php echo JText::_('Enable'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.booleanList", "prefs[wCompEnable]", "", $this->prefs["wCompEnable"]); ?>
            </td>
            <td>
                Enable the Worker's Compensation extension
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key" style="vertical-align: top;">
                <label for="wCompCodes">
                    <?php echo JText::_('Codes'); ?>:
                </label>
            </td>
            <td>
                <textarea class="text_area" type="text" name="prefs[wCompCodes]" id="prefs_wCompCodes" cols="50" rows="5"><?php echo $this->prefs["wCompCodes"];?></textarea>
            </td>
            <td>
                Worker's Compensation codes.  Put one per line.  The first 4 characters of the line are the code,
                the rest is a description of the code.
            </td>
        </tr>
    </table>

<?php
    echo $pane->endPanel();
    echo $pane->endPane();
?>
</div>

<div class="clr"></div>

<input type="hidden" name="option" value="com_timeclock" />
<input type="hidden" name="id" value="-1" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="config" />
</form>
?>