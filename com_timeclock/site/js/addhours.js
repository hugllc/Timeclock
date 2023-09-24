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
        //jQuery("form.addhours input.hours").on("blur", this.validateHours);
    },
    /**
     * This submits the form
     * 
     * @param task The task to perform
     * 
     * @return null
     */
    reset: function (proj_id, date)
    {
        this.proj_id = proj_id;
        this.date = date;
        var total = 0;
        var sel = ".hours .date-"+date+":not(.proj-"+proj_id+")";
        jQuery(sel).each(function(ind,elem){
            var hours = parseFloat(jQuery(elem).text());
            if (!isNaN(hours)) {
                total += hours;
            }
        });
        this.hoursoffset = total;
        this.calculateHourTotal();
        this.validateFieldset(jQuery("fieldset#addhours-"+proj_id));
    },
    /**
     * This submits the form
     * 
     * @param task The task to perform
     * 
     * @return null
     */
    submitform: function (task, modal)
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
                self.message(elem.project_id, "Check the field values", "danger");
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
        var url = jQuery("form.addhours").attr('action')+'&format=json&task=timesheet.apply'
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
                                // Full page
                                window.location.href = "index.php?option=com_timeclock&view=timesheet&date="+self.payperiod.start;
                            } else {
                                // Modal
                                parent.jQuery('.modal').modal('hide');
                            }
                        }
                    }
                } else {
                    self.message(elem.project_id, data.message, "danger");
                }
            },
            error: function(data)
            {
                self.message(elem.project_id, "Save Failed", "danger");
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
        div.removeClass('alert-danger');
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
        var total = this.getHours();
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
        var max = Timeclock.params.maxDailyHours;
        var total = this.getHours();

        if (max < this.getHours()) {
            ret = false;
        }
        fieldset.find("input.hours").each(function(ind, elem) {
            var res = self._validateHours(elem);
            ret = ret && res;
        });
        fieldset.find('[name="notes"]').each(function(ind, elem) {
            var res = self._validateNotes(elem);
            ret = ret && res;
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
    validateHours: function(self)
    {
        if (typeof self === "undefined") {
            var self = this;
        }
        var fieldset = jQuery(self).closest("fieldset");
        
        Addhours.validateFieldset(fieldset);
    },
    /**
     * Prints out a message for max number of hours
     * 
     * @param fieldset The fieldset to print message for
     * 
     * @return null
     */
    maxHoursMessage: function(fieldset, max)
    {
        if (max == undefined) {
            max = Timeclock.params.maxDailyHours;
        }
        this.message(
            fieldset.find('[name="project_id"]').val(), 
            'Must have at most '+max+' hours',
            "danger"
        );
    },
    /**
     * Prints out a message for max number of hours
     * 
     * @param fieldset The fieldset to print message for
     * 
     * @return null
     */
    maxYearlyHoursMessage: function(fieldset, hours, max)
    {
        this.message(
            fieldset.find('[name="project_id"]').val(), 
                    hours + 'is too many hours.  Must have at most '+max+' hours for the year.',
                    "danger"
        );
    },
    /**
     * Prints out a message for max number of hours
     * 
     * @param fieldset The fieldset to print message for
     * 
     * @return null
     */
    minHoursMessage: function(fieldset, min)
    {
        if (min != undefined) {
            this.message(
                fieldset.find('[name="project_id"]').val(), 
                        'Must have at least '+min+' hours',
                        "danger"
            );
        }
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
        var parent   = jQuery(obj).closest(".control-group");

        var proj = parseInt(fieldset.find('[name="project_id"]').val());

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
        var max = parseInt(Timeclock.params.maxDailyHours);
        if (max < 0) {
            max = 0;
        }
        total = this.getHours();

        // Check the max
        var div = jQuery('#hoursTotal')
        if (total > max) {
            this.setValid(false, parent);
            this.maxHoursMessage(fieldset, max);
            div.addClass('invalid');
            ret = false;
        } else {
            this.setValid(true, parent);
            fieldset.find("div.alert").hide();
            div.removeClass('invalid');
        }
        // Set the min and max
        var pmin     = parseInt(fieldset.find('[name="pmin"]').val());
        var pmax     = parseInt(fieldset.find('[name="pmax"]').val());
        var ytd      = parseInt(fieldset.find('[name="hours_ytd"]').val());
        var pmax_ytd = parseInt(fieldset.find('[name="pmax_ytd"]').val());
        // Round the hours
        hours = Addhours.round(hours);
        // Show/hide the star
        if (ret != false) {
            if ((pmax != 0) && (hours > pmax)) {
                this.setValid(false, parent);
                this.maxHoursMessage(fieldset, pmax);
                div.addClass('invalid');
                ret = false;
            } else if ((hours != 0) && (hours < pmin)) {
                this.setValid(false, parent);
                this.minHoursMessage(fieldset, pmin);
                div.addClass('invalid');
                ret = false;
            } else if ((hours != 0) && (pmax_ytd != 0) && ((ytd + hours ) > pmax_ytd)) {
                this.setValid(false, parent);
                this.maxYearlyHoursMessage(fieldset, ytd + hours, pmax_ytd);
                div.addClass('invalid');
                ret = false;
            } else {
                this.setValid(true, parent);
                fieldset.find("div.alert").hide();
                div.removeClass('invalid');
            }
        }
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
     * Sets the classes to show if a field is valid or not
     * 
     * @param valid True or false
     * @param group jQuery object for the field in question
     * 
     * @return null
     */
    setValid: function (valid, group)
    {
        if (valid) {
            group.find("label").removeClass("invalid");
            group.find(":input").removeClass("invalid");
        } else {
            group.find("label").addClass("invalid");
            group.find(":input").addClass("invalid");
        }
    },
    /**
     * Validates the notes
     * 
     * This should be called from a event trigger
     * 
     * @return null
     */
    validateNotes: function(self)
    {
        if (typeof self === "undefined") {
            var self = this;
        }
        Addhours._validateNotes(self);
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
            this.setValid(false, parent);
            ret = false;
        } else {
            this.setValid(true, parent);
            parent.find("span.minchars").removeClass("invalid");
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
        if (typeof group === "undefined") {
            // Get everything if nothing is given.
            var group = jQuery("form.addhours");
        }
        var hours = 0;
        group.find("input.hours,select.hours").each(function(ind, elem) {
            var hrs = parseFloat(jQuery(elem).val());
            if (!isNaN(hrs)) {
                hours += hrs;
            }
        });
        hours += this.hoursoffset;
        return hours;
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
