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
        <tr>
            <td width="100" align="right" class="key">
                <label for="decimalPlaces">
                    <?php echo JText::_('Minimum Note'); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" size="10" maxlength="50" name="prefs[minNoteChars]" id="prefs_minNoteChars" value="<?php echo $this->prefs["minNoteChars"];?>" /> Characters
            </td>
            <td>
                The minimum number of characters to accept as a note.  Set to 0 to disable.
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key" style="vertical-align: top;">
                <label for="userTypes">
                    <?php echo JText::_('User Types'); ?>:
                </label>
            </td>
            <td>
                <textarea class="text_area" type="text" name="prefs[userTypes]" id="prefs_userTypes" cols="50" rows="5"><?php echo $this->prefs["userTypes"];?></textarea>
            </td>
            <td>
                One type per line.  It can either be: shortname:longname or just
                longname.  The order they show up here is the order they will be
                in the pulldown dialog.
            </td>
        </tr>
    </table>
<?php
    echo $pane->endPanel();
    echo $pane->startPanel(JText::_("Timesheet"), "payperiod-pane");

    $firstViewPeriodStart = $this->prefs["firstViewPeriodStart"];
    if (empty($firstViewPeriodStart)) {
        $firstViewPeriodStart = $this->prefs["firstPayPeriodStart"];
    }
?>
    <table class="admintable">
        <tr>
            <td width="100" align="right" class="key">
                <label for="firstPayPeriodStart">
                    <?php echo JText::_('First Pay Period Start'); ?>:
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
                <label for="payPeriodType">
                    <?php echo JText::_('Pay Period Type'); ?>:
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
        <tr>
            <td width="100" align="right" class="key">
                <label for="TimesheetViewStyle">
                    <?php echo JText::_('Timesheet View Period'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.genericList", $this->timesheetViewOptions, "prefs[timesheetView]", "", 'value', 'text', $this->prefs["timesheetView"]); ?>
            </td>
            <td>
                How the user views their timesheet.  "payperiod" sets this to the same as the payperiods.
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="viewPeriodLength">
                    <?php echo JText::_('View Period Length'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.integerList", 1, 31, 1, "prefs[viewPeriodLengthFixed]", "", $this->prefs["viewPeriodLengthFixed"]); ?>
            </td>
            <td>
                The length of the view period in days for a fixed length view period
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="firstViewPeriodStart">
                    <?php echo JText::_('First View Period Start'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("calendar", $firstViewPeriodStart, "prefs[firstViewPeriodStart]", "prefsfirstViewPeriodStart", "%Y-%m-%d", "");?>
            </td>
            <td>
                This is for calculating 1-Week and 2-Week views.  The day this starts on will be shown as the first day of the week.
                This is ignored for 'payperiod' view.
            </td>
        </tr>
    </table>
<?php
    echo $pane->endPanel();
    echo $pane->startPanel(JText::_("Paid Time Off"), "pto-pane");
?>
    <table class="admintable">
        <tr>
            <td width="100" align="right" class="key">
                <label for="firstPayPeriodStart">
                    <?php echo JText::_('Accrue PTO'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.booleanList", "prefs[ptoEnable]", "", $this->prefs["ptoEnable"]); ?>
            </td>
            <td>
                Select whether timeclock should keep track of PTO accrual.
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key" style="vertical-align: top;">
                <label for="userTypes">
                    <?php echo JText::_('Accrual Rates'); ?>:
                </label>
            </td>
            <td>
                <textarea class="text_area" type="text" name="prefs[ptoAccrualRates]" id="prefs_ptoAccrualRates" cols="50" rows="10"><?php echo $this->prefs["ptoAccrualRates"];?></textarea>
            </td>
            <td>
                The first line is a colon separated list of user types.  Every line
                after that each line is a colon separated list:
                <pre>
[years of service]:[accrual rate for 1st type]:[accrual rate for 2nd type]...
                </pre>
                <strong>The service is in years.  The accrual rates are in days per year.</strong>
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="ptoAccrualWait">
                    <?php echo JText::_('Days Before Accrual Begins'); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" size="10" maxlength="100" name="prefs[ptoAccrualWait]" id="prefs_ptoAccrualWait" value="<?php echo $this->prefs["ptoAccrualWait"];?>" />
            </td>
            <td>
                This is the number of days an employee has to work before PTO shows up.
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="ptoHoursPerDay">
                    <?php echo JText::_('PTO Hours / Day'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.integerList", 1, 24, 1, "prefs[ptoHoursPerDay]", "", $this->prefs["ptoHoursPerDay"]); ?>
            </td>
            <td>
                How many PTO hours does the user get per day listed above.
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="ptoAccrualPeriod">
                    <?php echo JText::_('Accrual Period'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.genericList", $this->ptoAccrualPeriodOptions, "prefs[ptoAccrualPeriod]", "", 'value', 'text', $this->prefs["ptoAccrualPeriod"]); ?>
            </td>
            <td>
                The user gets PTO hours periodically on this time frame
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="ptoAccrualTime">
                    <?php echo JText::_('Accrual Time'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.genericList", $this->ptoAccrualTimeOptions, "prefs[ptoAccrualTime]", "", 'value', 'text', $this->prefs["ptoAccrualTime"]); ?>
            </td>
            <td>
                When during the period is the time accrued
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="ptoCarryOverDefExpire">
                    <?php echo JText::_('Default PTO Carryover Expiration'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("calendar", $this->prefs["ptoCarryOverDefExpire"], "prefs[ptoCarryOverDefExpire]", "ptoCarryOverDefExpire", "%m-%d", "");?>
            </td>
            <td>
                The default time that PTO carry over expires.  This is in the form Month-Day.  The
                year will be automatically added on for each year PTO carry over happens.
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="ptoNegative">
                    <?php echo JText::_('Acceptable Negative PTO'); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" size="10" maxlength="100" name="prefs[ptoNegative]" id="prefs_ptoNegative" value="<?php echo $this->prefs["ptoNegative"];?>" />
            </td>
            <td>
                This is the amount a user can go negative and still be within acceptable limits
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
    echo $pane->startPanel(JText::_("Extras"), "extras-pane");
?>
    <table class="admintable">
        <tr>
            <td width="100" align="right" class="key">
                <label for="wCompEnable">
                    <?php echo JText::_('JPGraph Path'); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" size="50" maxlength="100" name="prefs[JPGraphPath]" id="prefs_JPGraphPath" value="<?php echo $this->prefs["JPGraphPath"];?>" />
            </td>
            <td>
                Enter the path to jpgraph.php.
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="timeclockDisable">
                    <?php echo JText::_('Disable Timeclock'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.booleanList", "prefs[timeclockDisable]", "", $this->prefs["timeclockDisable"]); ?>
            </td>
            <td>
                This allows the timeclock system to be taken down for maintenance without taking the rest of your
                website down.
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key" style="vertical-align: top;">
                <label for="prefs_timeclockDisableMessage">
                    <?php echo JText::_('Disable Message'); ?>:
                </label>
            </td>
            <td>
                <textarea class="text_area" type="text" name="prefs[timeclockDisableMessage]" id="prefs_timeclockDisableMessage" cols="50" rows="5"><?php echo $this->prefs["timeclockDisableMessage"];?></textarea>
            </td>
            <td>
                This is the message that timeclock users will receive if the timeclock is down for maintenance.
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
