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
JHTML::_('behavior.formvalidation');

$this->totals     = array();
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

$hoursSum = array();
$initPanes = array();

?>
<script type="text/javascript">
        window.addEvent('domready', function(){
        console.log(document);
            document.formvalidator.setHandler('dateverify',
                function (value) {
                    regex=/[1-9][0-9]{3}-[0-1]{0,1}[0-9]-[0-3]{0,1}[0-9]/;
                    return regex.test(value);
                }
            );
            calculateHourTotal();
        });
</script>
<div id="timeclock" style="padding-top: 1.5em;">
<div id="addHoursTotal">
    <?php print JText::_("COM_TIMECLOCK_TOTAL_HOURS"); ?>: <span id="hoursTotal"> - </span><span id="hoursTotalError" class="error"></span>
</div>
<form action="<?php print JRoute::_("index.php"); ?>" method="post" name="userform" autocomplete="off" class="form-validate">
    <h1><?php print JText::_("COM_TIMECLOCK_ADD_HOURS"); ?></h1>
    <table id="timeclockTable">
        <tr>
            <th align="right" style="width: 30%;">
                <label id="date_label" for="date" class="required">
                    <?php print JText::_("JDATE"); ?><span class="star"> *</span>
                </label>
            </th>
            <td style="width: 40%;">
                <?php print JHTML::_("calendar", $this->date, "date", "date", "%Y-%m-%d", array('class' => "inputbox validate-dateverify required date_label"));?>
            </td>
        </tr>
<?php
foreach ($this->projects as $cat) {
    if (($cat->mine == false) || !$cat->published) continue;
    if (!is_null($this->projid) && !array_key_exists($this->projid, $cat->subprojects)) continue;
    $safeName = JText::_("JCATEGORY").$cat->id;
    if (!empty($this->projid)) {
        // Do nothing here.
    } else if ($cat->show === true) {
        // We are told to show this
        $initPanes[] = "timeclockCatShow('".$safeName."');";
    } else if ($cat->show === false) {
        // We are told to hide this
        $initPanes[] = "timeclockCatHide('".$safeName."');";
    } else {
        // If $cat->show doesn't exist, go with the cookie
        $initPanes[] = "timeclockCatShowHide('".$safeName."', true);";
    }
    ?>
        <tr>
            <td class="sectiontableheader" colspan="<?php print $headerColSpan; ?>">
                <h2>
                    <?php if (empty($this->projid)):?>
                    <a href="JavaScript: timeclockCatShowHide('<?php print $safeName; ?>');">
                        <span id="<?php print $safeName; ?>_cat_span"> - </span>
                    <?php endif; ?>
                        <?php print JText::_("JCATEGORY").": ".JText::_($cat->name); ?>
                    <?php if (empty($this->projid)): ?>
                    </a>
                    <?php endif; ?>
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
        // Now do something about the codes
        $jsHoursTotal = array();
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
            $hoursSum[] = $hoursId;
            $jsHoursTotal[] = "$('$hoursId').value";
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
                <input class="inputbox required validate-hoursverify<?php print $hoursId;?>" type="text" id="<?php print $hoursId; ?>" name="timesheet[<?php print $proj->id;?>][<?php print $var; ?>]" size="10" maxlength="10" value="<?php echo $hours;?>" />
                <span><?php print $wcNote; ?></span>
                <span id="<?php print $hoursId; ?>_old" style="display: none;"><?php print $hours; ?></span>
            </td>
            <td>
                <?php print JText::_("COM_TIMECLOCK_HOURS_WORKED_HELP"); ?>
                <script lang="javascript">
                    window.addEvent('domready', function(){
                        document.formvalidator.setHandler('hoursverify<?php print $hoursId;?>',
                            function (value) {
                                // clear any error
                                document.getElementById('hoursTotalError').innerHTML = '&nbsp;';
                                // get our objects
                                var hours = jQuery('<?php print $hoursId; ?>');
                                var old = jQuery('<?php print $hoursId; ?>_old');
                                var notes = jQuery('timesheet_<?php print $proj->id;?>_notes');

                                // Calculate the max hours available
                                var total = parseFloat(document.getElementById('hoursTotal').innerHTML);
                                var oldHours = parseFloat(old.innerHTML);
                                var max = <?php print $this->maxHours; ?> - total + oldHours;
                                if (max < 0) {
                                    max = 0;
                                }

                                // Round the hours
                                var mod = Math.pow(10, <?php print $this->decimalPlaces; ?>);
                                hours.value = Math.round(hours.value * mod) / mod;

                                // Check the max
                                if (hours.value > max) {
                                    hours.value = max;
                                    document.getElementById('hoursTotalError').innerHTML = 'Only <?php print $this->maxHours; ?> are allowed';
                                }

                                // Set the old value
                                old.innerHTML = hours.value;

                                // calculate the total
                                calculateHourTotal();

                                // Return
                                return true;
                            }
                        );
                    });
                </script>

            </td>
        </tr>
        <?php endfor; ?>
        <tr>
            <th style="vertical-align: top;"  align="right" id="notes_<?php print $proj->id;?>_label">
                <label id="notes_<?php print $proj->id;?>_label" for="timesheet_<?php print $proj->id;?>_notes">
                    <?php echo JText::_("COM_TIMECLOCK_NOTES"); ?>:
                </label>
            </th>
            <td>
                <script lang="javascript">
                    window.addEvent('domready', function(){
                        document.formvalidator.setHandler('noteverify<?php print $proj->id;?>',
                            function (value) {
                            var errordisp = $('noteerror<?php print $proj->id;?>');
                                var hours = <?php print implode(" + ", (array)$jsHoursTotal); ?>;
                                if ((hours > 0)
                                    && (value.length < <?php print $this->minNoteChars; ?>)) {
                                    errordisp.style.background = 'red';
                                    errordisp.style.color = 'white';
                                    return false;
                                } else {
                                    errordisp.style.background = '';
                                    errordisp.style.color = '';
                                    return true;
                                }
                            }
                        );
                    });
                </script>
                <textarea class="inputbox validate-noteverify<?php print $proj->id;?>"  id="timesheet_<?php print $proj->id;?>_notes" name="timesheet[<?php print $proj->id;?>][notes]" cols="50" rows="5" onFocus="this.value=(this.value).trim();" onBlur="if ((this.value = this.value.trim()).length == 0) this.value+='  ';"> <?php echo (isset($this->data[$proj->id])) ? $this->data[$proj->id]->notes : "";?> </textarea>
                <?php if (isset($this->data[$proj->id])) { ?>
                <input type="hidden" id="timesheet_<?php print $proj->id;?>_id" name="timesheet[<?php print $proj->id;?>][id]" value="<?php echo $this->data[$proj->id]->id;?>" />
                <input type="hidden" id="timesheet_<?php print $proj->id;?>_created" name="timesheet[<?php print $proj->id;?>][created]" value="<?php echo $this->data[$proj->id]->created;?>" />
                <?php } ?>
                <input type="hidden" id="timesheet_<?php print $proj->id;?>_project_id" name="timesheet[<?php print $proj->id;?>][project_id]" value="<?php echo $proj->id;?>" />
            </td>
            <td>
                <?php print JText::_("COM_TIMECLOCK_WORK_NOTES_HELP"); ?>
                <div id="noteerror<?php print $proj->id;?>" style="padding: 3px;">
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
                <button type="submit" onMouseDown="document.getElementById('theTask').value='timeclock.savehours';" class="button validate"><?php print JText::_("COM_TIMECLOCK_SAVE"); ?></button>
            </td>
        </tr>

        <?php
    }
    ?>
    </tbody>
    <?php
}
$document = JFactory::getDocument();
$js = 'window.addEvent(\'domready\', function() {'.implode(" ", $initPanes).'});';
$document->addScriptDeclaration($js);
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
* <?php print JText::_("COM_TIMECLOCK_REQUIRED_FIELD"); ?>
</div>
</div>
<script type="text/javascript">
    function calculateHourTotal() {
        var total = 0;
        <?php foreach ($hoursSum as $hours): ?>
            value = parseFloat(document.getElementById('<?php print $hours; ?>').value);
            if (isNaN(value)) value = 0.0;
            total = total + value;
        <?php endforeach; ?>
        var mod = Math.pow(10, <?php print $this->decimalPlaces; ?>);
        total = Math.round(total * mod) / mod;
        document.getElementById('hoursTotal').innerHTML = total;
    }
</script>