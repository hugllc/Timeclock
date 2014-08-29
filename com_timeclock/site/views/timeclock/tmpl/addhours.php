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

JHTML::_('behavior.tooltip');
JHTML::_('bootstrap.framework');
JHTML::_('jquery.framework');

$totalhours = 0;
if (empty($this->days)) $this->days = 7;

$headerColSpan    = 3;
$shortDateFormat  = JText::_("DATE_FORMAT_LC3");
$document         = JFactory::getDocument();
$document->setTitle(
    JText::sprintf(
        "COM_TIMECLOCK_ADD_HOURS_TITLE",
        $this->user->get("name"),
        JHTML::_('date', $this->date." 06:00:00", $shortDateFormat)
    )
);

JHTML::script(Juri::base()."components/com_timeclock/views/timeclock/tmpl/category.js");


?>
<div id="timeclock">
<form action="<?php print JRoute::_("index.php"); ?>" method="post" name="addhoursform" autocomplete="off" class="form-validate">
    <h1><?php print JText::_("COM_TIMECLOCK_ADD_HOURS"); ?></h1>
    <table id="timeclockTable">
        <tr>
            <th align="right" style="width: 30%;">
                <label id="date_label" for="date" class="required">
                    <?php print JText::_("JDATE"); ?><span class="star"> *</span>
                </label>
            </th>
            <td style="width: 40%;">
                <input type="text" id="date" name="date" class="inputbox" onblur="validateDate(this);" value="<?php print $this->date; ?>" />
                <?php// print JHTML::_("calendar", $this->date, "date", "date", "%Y-%m-%d", array('class' => "inputbox", 'onBlur' => 'validateDate(this);'));?>
            </td>
            <td>
                YYYY-MM-DD
            </td>
        </tr>
<?php
foreach ($this->projects as $cat) {
    if (($cat->mine == false) || !$cat->published) continue;
    if (!is_null($this->projid) && !array_key_exists($this->projid, $cat->subprojects)) continue;
    $safeName = JText::_("JCATEGORY").$cat->id;
    ?>
        <tr>
            <td class="sectiontableheader" colspan="<?php print $headerColSpan; ?>">
                <h2>
                    <?php print JText::_("JCATEGORY").": ".JText::_($cat->name); ?>
                </h2>
            </td>
        </tr>
        <tbody id="<?php print $safeName; ?>_cat" class="pane">
    <?php
    foreach ($cat->subprojects as $pKey => $proj) {
        if ($proj->mine == false) continue;
        if (!$proj->published) continue;
        if ($proj->noHours) continue;
        if (!is_null($this->projid) && !($this->projid == $proj->id)) continue;
        ?>
        <tr>
            <th class="sectiontableheader" colspan="<?php print $headerColSpan; ?>">
                <h3><?php print JText::_("COM_TIMECLOCK_PROJECT").": ".TimeclockModelTimeclock::formatProjId($proj->id)." ".JText::_($proj->name); ?></h3>
            </th>
        </tr>
        <?php
        // Check for any codes
        $code = false;
        for ($i = 1; $i < 7; $i++) {
            $var = "hours".$i;
            $wcVar = "wcCode".$i;
            if (isset($proj->$var)) {
                $code |= ($proj->$var > 0);
            }
            if (isset($proj->$wcVar)) {
                $code |= ($proj->$wcVar > 0);
            }
        }
        $projhours = 0;
        // Now do something about the codes
        for ($i = 1; $i < 7; $i++):
            $wcNote = "";
            if (($this->wCompEnable) && ($code)) {
                $var = "hours".$i;
                $wcVar = "wcCode".$i;
                $hours = (isset($this->data[$proj->id]) && $this->data[$proj->id]->$var) ? $this->data[$proj->id]->$var : 0;
                if (($proj->$wcVar <= 0) && ($hours == 0)) {
                    continue;
                }
                $wcName = empty($this->wCompCodes[abs($proj->$wcVar)]) ? JText::_("COM_TIMECLOCK_UNKNOWN")."[".$i."]" : $this->wCompCodes[abs($proj->$wcVar)];
                if ($proj->$wcVar < 0) {
                    $wcNote = JText::_("COM_TIMECLOCK_NO_NEW_HOURS");
                }
            } else {
                if ($i > 1) break;
                $var = "hours1";
                $wcName = JText::_("COM_TIMECLOCK_HOURS");
                $hours = ($this->data[$proj->id]->hours) ? $this->data[$proj->id]->hours : 0;
            }
            $hoursId = "timesheet_".$proj->id."_hours_".$i;
            $totalhours += $hours;
            $projhours  += $hours;
            ?>
        <tr>
            <th align="right">
                <div class="control-label">
                <label id="hours_<?php print $i; ?>_<?php print $proj->id;?>_label" for="<?php print $hoursId; ?>">
                    <?php print JText::_($wcName);?>
                </label>
                </div>
            </th>
            <td>
                <input class="inputbox hoursinput" type="text" id="<?php print $hoursId; ?>" name="timesheet[<?php print $proj->id;?>][<?php print $var; ?>]" size="10" maxlength="10" value="<?php echo $hours;?>" oldvalue="<?php print $hours; ?>" onblur="validateHours(this);"/>
                <span><?php print $wcNote; ?></span>
            </td>
            <td>
                <?php print JText::_("COM_TIMECLOCK_HOURS_WORKED_HELP"); ?>
            </td>
        </tr>
        <?php endfor; ?>
        <tr>
            <th style="vertical-align: top;"  align="right" id="notes_<?php print $proj->id;?>_label">
                <label id="timesheet_<?php print $proj->id;?>_notes_label" for="timesheet_<?php print $proj->id;?>_notes">
                    <?php echo JText::_("COM_TIMECLOCK_NOTES"); ?><span class="star" style="<?php print ($projhours > 0) ? "" : "display: none;"; ?>" id="timesheet_<?php print $proj->id;?>_notes_star"> *</span>
                </label>
            </th>
            <td>
                <textarea class="inputbox" id="timesheet_<?php print $proj->id;?>_notes" name="timesheet[<?php print $proj->id;?>][notes]" cols="50" rows="5" onBlur="validateNotes(this);" onkeyup="validateNotes(this);"><?php echo (isset($this->data[$proj->id])) ? $this->data[$proj->id]->notes : "";?></textarea>
                <?php if (isset($this->data[$proj->id])) { ?>
                <input type="hidden" id="timesheet_<?php print $proj->id;?>_id" name="timesheet[<?php print $proj->id;?>][id]" value="<?php echo $this->data[$proj->id]->id;?>" />
                <input type="hidden" id="timesheet_<?php print $proj->id;?>_created" name="timesheet[<?php print $proj->id;?>][created]" value="<?php echo $this->data[$proj->id]->created;?>" />
                <?php } ?>
                <input type="hidden" id="timesheet_<?php print $proj->id;?>_project_id" name="timesheet[<?php print $proj->id;?>][project_id]" value="<?php echo $proj->id;?>" />
            </td>
            <td>
                <?php print JText::_("COM_TIMECLOCK_WORK_NOTES_HELP"); ?>
                <div id="timesheet_<?php print $proj->id;?>_notes_error" style="padding: 3px;">
                <?php if ($this->minNoteChars > 0): ?>
                    <strong><?php print JText::sprintf("COM_TIMECLOCK_WORK_NOTES_MIN_CHARS", $this->minNoteChars); ?></strong>
                <?php endif; ?>
                </div>
            </td>
        </tr>
        <tr>
            <th style="vertical-align: top;">
                 &nbsp;
            </th>
            <td>
<!--                <button type="submit" onMouseDown="document.getElementById('theTask').value='timeclock.applyhours';" class="button validate"><?php print JText::_("COM_TIMECLOCK_APPLY"); ?></button>-->
                <button type="submit" onMouseDown="document.getElementById('theTask').value='timeclock.savehours';" class="button validate submit"><?php print JText::_("COM_TIMECLOCK_SAVE"); ?></button>
            </td>
        </tr>

        <?php
    }
    ?>
    </tbody>
    <?php
}
?>
    </table>
    <input type="hidden" name="controller" value="timeclock" />
    <input type="hidden" name="referer" value="<?php print $this->referer; ?>" />
    <input type="hidden" name="option" value="com_timeclock" />
    <input type="hidden" name="task" id="theTask" value="" />
    <?php print JHTML::_("form.token"); ?>
</form>
<div>
    <a name="required_field" />
<span class="star">*</span> <?php print JText::_("COM_TIMECLOCK_REQUIRED_FIELD"); ?>
</div>
<script type="JavaScript">
<?php print $this->loadTemplate("js"); ?>
</script>
<div id="addHoursTotal">
    <?php print JText::_("COM_TIMECLOCK_TOTAL_HOURS"); ?>: <span id="hoursTotal"><?php print $totalhours; ?></span><span id="hoursTotalError" class="error"></span>
</div>
</div>
