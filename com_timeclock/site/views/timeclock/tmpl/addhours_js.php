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

    function calculateHourTotal() {
        var total = 0;
        jQuery(".hoursinput").each(function (ind, el) {
            var val = parseFloat(jQuery(el).val());
            if (!isNaN(val)) {
                total += val;
            }
        });
        var mod = Math.pow(10, <?php print $this->decimalPlaces; ?>);
        total = Math.round(total * mod) / mod;
        jQuery('#hoursTotal').text(total);
    }
    function validateHours(obj)
    {
        // clear any error
        jQuery('#hoursTotalError').html('&nbsp;');

        // Get the hours
        var hours = parseFloat(obj.value);
        if (isNaN(hours)) {
            hours = 0.0;
        }

        // Calculate the max hours available
        var total = parseFloat(jQuery('#hoursTotal').text());
        if (isNaN(total)) {
            total = 0.0;
        }
        var oldHours = parseFloat(jQuery(obj).attr("oldvalue"));
        if (isNaN(oldHours)) {
            oldHours = 0.0;
        }
        var max = <?php print $this->maxHours; ?> - total + oldHours;
        if (max < 0) {
            max = 0;
        }

        // Round the hours
        var mod = Math.pow(10, <?php print $this->decimalPlaces; ?>);
        hours = Math.round(hours * mod) / mod;

        // Check the max
        if (hours > max) {
            hours = max;
            jQuery('#hoursTotalError').html('Only <?php print $this->maxHours; ?> are allowed');
        }

        // Set the old value
        jQuery(obj).attr("oldvalue", hours);

        // Set the current value
        obj.value = hours;

        // calculate the total
        calculateHourTotal();
        
        // Revalidate the note field
        var id = jQuery(obj).attr("id");
        for (var i = 1; i <= 6; i++) {
            id = id.replace("_hours_"+i, "");
        }
        validateNotes(jQuery("#"+id+"_notes"));
    }
    function validateNotes(obj)
    {
        var id     = jQuery(obj).attr("id");
        var projid = id.replace("_notes", "");

        var hours = getHours(projid);
        if ((hours > 0)
            && (jQuery(obj).val().length < <?php print $this->minNoteChars; ?>)) {
            jQuery("#"+id+"_error").addClass("invalid");
            jQuery(obj).addClass("invalid");
            jQuery("#"+id+"_label").addClass("invalid");
            fixSubmit(false);
        } else {
            jQuery("#"+id+"_error").removeClass("invalid");
            jQuery(obj).removeClass("invalid");
            jQuery("#"+id+"_label").removeClass("invalid");
            fixSubmit(true);
        }
        // Show/hide the star
        if (hours > 0) {
            jQuery("#"+id+"_star").show();
            jQuery(obj).addClass("required");
        } else {
            jQuery("#"+id+"_star").hide();
            jQuery(obj).removeClass("required");
        }
    }
    function getHours(projid)
    {
        var hours = 0;
        for (var i = 1; i <= 6; i++) {
            var hrs = parseFloat(jQuery("#"+projid+"_hours_"+i).val());
            if (!isNaN(hrs)) {
                hours += hrs;
            }
        }
        return hours;
    }
    function validateDate(obj)
    {
        regex=/^[1-9][0-9]{3}-[0-1]{0,1}[0-9]{1,1}-[0-3]{0,1}[0-9]$/;
        if (regex.test(obj.value)) {
            jQuery(obj).removeClass("invalid");
            jQuery("#date_label").removeClass("invalid");
            fixSubmit(true);
        } else {
            jQuery(obj).addClass("invalid");
            jQuery("#date_label").addClass("invalid");
            fixSubmit(false);
        }
    }
    function fixSubmit(good)
    {
        if (good) {
            jQuery('button[type="submit"]').removeAttr('disabled');
        } else {
            jQuery('button[type="submit"]').attr('disabled','disabled');
        }
    }
