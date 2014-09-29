var Addhours = {
    decimals: 2,      // The number of decimal places
    count: 0,         // The number of things posted
    total: 0,         // The total number of thing to post
    hoursoffset: 0,   // The offset for the daily hours calculation
    setup: function ()
    {
        var self = this;
        this.decimals = (Timeclock.params.decimalPlaces) ? Timeclock.params.decimalPlaces : 2;
        this.calculateHourTotal();
        // Immedately validate everything.
        jQuery("form.addhours fieldset.addhours").each(function(ind,elem) {
            self.validateFieldset(jQuery(elem));
        });
        jQuery("form.addhours input.hours").on("blur", this.validateHours);
    },
    /**
     * This submits the form
     * 
     * @param task The task to perform
     * 
     * @return null
     */
    submitform: function (task)
    {
        // Hide all of the alerts
        var div = jQuery("form.addhours div.alert").hide();
        // Do the rest of the stuff
        var self = this;
        var worked = jQuery('form.addhours [name="worked"]').val();
        var post = {};
        // Collect the base information from the form
        var base = {};
        jQuery("form.addhours fieldset#extra").find(":input").each(function(ind,elem) {
            var name = jQuery(elem).attr('name');
            var value = jQuery(elem).val();
            base[name] = value;
        });
        // Collect the timesheet data from each fieldset in the form
        jQuery("form.addhours fieldset.addhours").each(function(ind,elem) {
            var data = jQuery.extend({}, base);
            jQuery(elem).find(":input").each(function(ind,elem) {
                var name = jQuery(elem).attr('name');
                var value = jQuery(elem).val();
                if (value != "") {
                    data[name] = value;
                }
            });
            data.worked = worked;
            var id = parseInt(data.timesheet_id, 10);
            data.timesheet_id = (isNaN(id)) ? 0 : id;
            var total = self.getHours(jQuery(elem));
            // Only post this if it already has a timesheet_id or it has hours
            if ((total > 0) || (data.timesheet_id != 0)) {
                post[data.project_id] = data;
            }
        });
        // Post everything
        this.postall(post, task);
    },
    /**
     * This posts all of the given sets
     * 
     * @param data The data object to use
     * @param task The task to perform
     * 
     * @return null
     */
    postall: function (data, task)
    {
        var self = this;
        this.total = Object.keys(data).length;
        this.count = 0;
        jQuery.each(data, function (ind, elem) {
            var fieldset = jQuery('fieldset#addhours-'+elem.project_id);
            if (!self.validateFieldset(fieldset)) {
                self.message(elem.project_id, "Check the field values", "error");
                fieldset.find('[name="notes"]').on("keypress", self.validateNotes);
            } else {
                self.post(elem, task);
            }
        });
    },
    /**
     * This posts all of the given sets
     * 
     * @param elem The data object to use
     * @param task The task to perform
     * 
     * @return null
     */
    post: function (elem, task) 
    {
        var url = jQuery("form.addhours").attr('action')+'&format=json&task=apply'
        var method = jQuery("form.addhours").attr('method');
        var self = this;
        jQuery.ajax({
            url: url,
            type: method,
            data: elem,
            dataType: 'JSON',
            success: function(data)
            {
                if ( data.success ){
                    // This puts the new values into the form.  This is required
                    // for new records, as the primary key must be set or another
                    // record will be entered into the database when save is pressed
                    // again.
                    var fieldset = jQuery('fieldset#addhours-'+elem.project_id);
                    var key;
                    for (key in data.data) {
                        fieldset.find('[name="'+key+'"]').val(data.data[key]);
                    }
                    // Set the complete flag to 0
                    self.message(elem.project_id, data.message, "success");
                    fieldset.find('[name="notes"]').off("keypress", self.validateNotes);
                    self.count++;
                    if (self.count >= self.total) {
                        if (typeof Timesheet !== 'undefined') {
                            Timesheet.refresh();
                            Timesheet.setComplete(0);
                        }
                        if (task == 'save') {
                            if (typeof Timesheet === 'undefined') {
                                window.location.href = "index.php?option=com_timeclock&controller=timesheet&date="+self.payperiod.start;
                            } else {
                                SqueezeBox.close();
                            }
                        }
                    }
                } else {
                    self.message(elem.project_id, data.message, "error");
                }
            },
            error: function(data)
            {
                self.message(elem.project_id, "Save Failed", "error");
            }
        });
    },
    /**
     * This posts a message on the individual project form.
     * 
     * @param project_id The project id to send the message to.
     * @param msg        The message to post
     * @param type       The type of message (success, error, info)
     * 
     * @return null
     */
    message: function (project_id, msg, type)
    {
        var div = jQuery("fieldset#addhours-"+project_id+" div.alert");
        div.text(msg);
        div.removeClass('alert-success');
        div.removeClass('alert-error');
        div.removeClass('alert-info');
        div.addClass('alert-'+type);
        div.show();
    },
    /**
     * This posts the total hours for the day
     * 
     * @return null
     */
    calculateHourTotal: function() {
        var total = this.getHours(jQuery("form.addhours"));
        total += this.hoursoffset;
        jQuery('#hoursTotal').text(this.round(total));
    },
    /**
     * Validates one fieldset
     * 
     * @param fieldset The fieldset to validate.  (jQuery object)
     * 
     * @return boolean
     */
    validateFieldset: function (fieldset)
    {
        var ret = true;
        var self = this;
        fieldset.find("input.hours").each(function(ind, elem) {
            ret = ret && self._validateHours(elem);
        });
        fieldset.find('[name="notes"]').each(function(ind, elem) {
            ret = ret && self._validateNotes(elem);
        });
        return ret;
    },
    /**
     * Validates the number of hours
     * 
     * This should be called from a event trigger
     * 
     * @return null
     */
    validateHours: function()
    {
        Addhours._validateHours(this);
    },
    /**
     * Validates the number of hours
     * 
     * @param obj The object to validate.  (DOM Object)
     * 
     * @return null
     */
    _validateHours: function(obj)
    {
        var ret = true;
        var fieldset = jQuery(obj).closest("fieldset");
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
        var max = Timeclock.params.maxDailyHours;
        if (max < 0) {
            max = 0;
        }

        // Round the hours
        hours = Addhours.round(hours);

        // Check the max
        if (hours > max) {
            hours = max;
            Joomla.renderMessages({'error': ['Only '+Timeclock.params.maxDailyHours+' are allowed']});
        }
        // Show/hide the star
        if (hours > 0) {
            var star = fieldset.find("#notes-lbl span.star");
            if (star[0] == undefined) {
                fieldset.find("#notes-lbl").append('<span class="star">&#160;*</span>');
                star = fieldset.find("#notes-lbl span.star").show();
            }
            star.show();
            jQuery(obj).addClass("required");
        } else {
            fieldset.find("#notes-lbl span.star").hide();
            jQuery(obj).removeClass("required");
        }
        
        // Set the old value
        jQuery(obj).attr("oldvalue", hours);

        // Set the current value
        obj.value = hours;

        // calculate the total
        this.calculateHourTotal();
        return ret;
    },
    /**
     * Validates the notes
     * 
     * This should be called from a event trigger
     * 
     * @return null
     */
    validateNotes: function()
    {
        Addhours._validateNotes(this);
    },
    /**
     * Validates the notes
     * 
     * @param obj The object to validate.  (DOM Object)
     * 
     * @return null
     */
    _validateNotes: function(obj)
    {
        var fieldset = jQuery(obj).closest("fieldset");
        var parent   = jQuery(obj).closest(".control-group");
        var ret      = true;
        
        var hours = this.getHours(fieldset);
        if ((hours > 0)
            && (jQuery(obj).val().length < Timeclock.params.minNoteChars)) {
            parent.find("span.minchars").addClass("invalid");
            jQuery(obj).addClass("invalid");
            parent.find("label").addClass("invalid");
            ret = false;
        } else {
            parent.find("span.minchars").removeClass("invalid");
            jQuery(obj).removeClass("invalid");
            parent.find("label").removeClass("invalid");
        }
        return ret
    },
    /**
     * Gets the total number of hours for the group specified
     * 
     * @param group jQuery object
     * 
     * @return float The number of hours found
     */
    getHours: function(group)
    {
        var hours = 0;
        group.find("input.hours").each(function(ind, elem) {
            var hrs = parseFloat(jQuery(elem).val());
            if (!isNaN(hrs)) {
                hours += hrs;
            }
        });
        return hours;
    },
    /**
     * Validates the number of date
     * 
     * This should be called from a event trigger
     * 
     * @return null
     */
    validateDate: function ()
    {
        Addhours._validateDate(this);
    },
    /**
     * Validates the date
     * 
     * @param obj The object to validate.  (DOM Object)
     * 
     * @return null
     */
    _validateDate: function(obj)
    {
        regex=/^[1-9][0-9]{3}-[0-1]{0,1}[0-9]{1,1}-[0-3]{0,1}[0-9]$/;
        if (regex.test(obj.value)) {
            jQuery(obj).removeClass("invalid");
            jQuery("#date_label").removeClass("invalid");
        } else {
            jQuery(obj).addClass("invalid");
            jQuery("#date_label").addClass("invalid");
        }
    },
    /**
     * Rounds the value to a certain number of decimal places
     * 
     * @param value The value to round
     * 
     * @return float The rounded value
     */
    round: function (value)
    {
        var val = parseFloat(value);
        if (isNaN(val)) {
            return value;
        }
        return parseFloat(val.toFixed(this.decimals));
    },
       
}
