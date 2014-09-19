// Only define the Joomla namespace if not defined.
var Timesheet = {
    decimals: 2,
    setup: function ()
    {
        this.decimals = Timeclock.params.decimalPlaces;
        this.update();
        this.setComplete(this.payperiod.done);
    },
    round: function (value)
    {
        var val = parseFloat(value);
        if (isNaN(val)) {
            return value;
        }
        return parseFloat(val.toFixed(this.decimals));
    },
    setComplete: function (complete)
    {
        if (complete) {
            jQuery("#timeclock .complete").show();
            jQuery("#timeclock .notcomplete").hide();
        } else {
            jQuery("#timeclock .complete").hide();
            jQuery("#timeclock .notcomplete").show();
        }
    },
    _subtotalDates: function (key, name)
    {
        var total = 0;
        jQuery("table.timesheet .hours .date-"+key).each(function(ind,elem){
            var hours = parseFloat(jQuery(elem).text());
            if (!isNaN(hours)) {
                total += hours;
            }
        });
        jQuery("table.timesheet #subtotal-"+key).text(this.round(total));
    },
    _subtotalProj: function (key, parts)
    {
        var total = 0;
        for (var i = 1; i <= parts; i++) {
            var subtotal = 0;
            jQuery("table.timesheet .hours .proj-"+i+"-"+key).each(function(ind,elem){
                var hours = parseFloat(jQuery(elem).text());
                if (!isNaN(hours)) {
                    subtotal += hours;
                }
            });
            jQuery("table.timesheet #subtotal-proj-"+i+"-"+key).text(this.round(subtotal));
            total += subtotal;
        }
        jQuery("#total-proj-"+key).text(this.round(total));
    },
    _psubtotal: function (parts)
    {
        var total = 0;
        for (var i = 1; i <= parts; i++) {
            var subtotal = 0;
            jQuery("table.timesheet .subtotal .subtotal-proj-"+i).each(function(ind,elem){
                var hours = parseFloat(jQuery(elem).text());
                if (!isNaN(hours)) {
                    subtotal += hours;
                }
            });
            jQuery("table.timesheet #psubtotal-proj-"+i).text(this.round(subtotal));
            total += subtotal;
        }
        jQuery("table.timesheet #total").text(this.round(total));
    },
    total: function ()
    {
        var self = this;
        jQuery.each(self.dates, function(ind,key) {
            self._subtotalDates(key);
        });
        jQuery.each(self.allprojs, function(ind,key) {
            self._subtotalProj(key, self.subtotalcols);
        });
        self._psubtotal(self.subtotalcols);
        self.hilightholiday();
    },
    hilightholiday: function ()
    {
        jQuery("table.timesheet .holiday td.hours").each(function(ind,elem){
            var hours = parseFloat(jQuery(elem).text());
            if (isNaN(hours) || (hours == 0)) {
                jQuery(elem).removeClass("holiday");
            } else {
                jQuery(elem).addClass("holiday");
            }
        });
    },
    complete: function ()
    {
        var self = this;
        jQuery.ajax({
            url: 'index.php?option=com_timeclock&controller=timesheet&task=complete&format=json',
            type: 'GET',
            data: self._formData(),
            dataType: 'JSON',
            success: function(ret)
            {
                if ( ret.success ){
                    //Joomla.renderMessages({'success': [ret.message]});
                    self.setComplete(true);
                } else {
                    Joomla.renderMessages({'error': [ret.message]});
                }
            },
            error: function(ret)
            {
                Joomla.renderMessages({'error': ['Setting as complete failed']});
            }
        });
        
    },
    update: function ()
    {
        var proj;
        var hours;
        var date;
        var self = this;
        jQuery.each(self.allprojs, function(ind,proj) {
            jQuery.each(self.dates, function(ind2,date) {
                hours = (self.data[proj] && self.data[proj][date]) ? parseFloat(self.data[proj][date].hours) : 0;
                
                jQuery('table.timesheet #hours-'+proj+"-"+date).text(self.round(hours));
            });
        });
        this.total();
    },
    refresh: function ()
    {
        var self = this;
        jQuery.ajax({
            url: 'index.php?option=com_timeclock&controller=timesheet&format=json&date='+self.payperiod.start,
            type: 'POST',
            data: {},
            dataType: 'JSON',
            success: function(ret)
            {
                if ( ret.success ){
                    // This puts the new values into the form.  This is required
                    // for new records, as the primary key must be set or another
                    // record will be entered into the database when save is pressed
                    // again.
                    self.data = ret.data;
                    self.update();
                }
            },
            error: function(ret)
            {
            }
        });
        
    },
    _formData: function ()
    {
        // Collect the base information from the form
        var base = {};
        jQuery("form.timesheet").find(":input").each(function(ind,elem) {
            var name = jQuery(elem).attr('name');
            var value = jQuery(elem).val();
            base[name] = value;
        });
        base.date = this.payperiod.start;
        return base;
    }
}