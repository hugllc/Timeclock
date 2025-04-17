var Timesheet = {
    decimals: 2,
    setup: function ()
    {
        this.decimals = (Timeclock.params.decimalPlaces) ? Timeclock.params.decimalPlaces : 2;
        this.update();
        this.setComplete(this.payperiod.done);
        this.setLocked(this.payperiod.locked);
    },
    round: function (value)
    {
        var val = parseFloat(value);
        if (isNaN(val)) {
            return value;
        }
        return parseFloat(val.toFixed(this.decimals));
    },
    setLocked: function (locked)
    {
        if (locked) {
            jQuery("#timeclock .locked").show();
            jQuery("#timeclock .notlocked").hide();
        } else {
            jQuery("#timeclock .locked").hide();
            jQuery("#timeclock .notlocked").show();
        }
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
    _subtotalDates: function (key, sel)
    {
        var total = 0;
        jQuery("table."+sel+" .hours .date-"+key).each(function(ind,elem){
            var hours = parseFloat(jQuery(elem).text());
            if (!isNaN(hours)) {
                total += hours;
            }
        });
        jQuery("table."+sel+" .subtotal-"+key).text(this.round(total));
    },
    _subtotalProj: function (key, parts, sel)
    {
        var total = 0;
        for (var i = 1; i <= parts; i++) {
            var subtotal = 0;
            jQuery("table."+sel+" .hours .proj-"+i+"-"+key).each(function(ind,elem){
                var hours = parseFloat(jQuery(elem).text());
                if (!isNaN(hours)) {
                    subtotal += hours;
                }
            });
            jQuery("table."+sel+" .subtotal-proj-"+i+"-"+key).text(this.round(subtotal));
            total += subtotal;
        }
        jQuery("table."+sel+" .total-proj-"+key).text(this.round(total));
    },
    _psubtotal: function (parts, sel)
    {
        var total = 0;
        for (var i = 1; i <= parts; i++) {
            var subtotal = 0;
            jQuery("table."+sel+" .subtotal .subtotal-proj-"+i).each(function(ind,elem){
                var hours = parseFloat(jQuery(elem).text());
                if (!isNaN(hours)) {
                    subtotal += hours;
                }
            });
            jQuery("table."+sel+" .psubtotal-proj-"+i).text(this.round(subtotal));
            total += subtotal;
        }
        jQuery("table."+sel+" .grandtotal").text(this.round(total));
    },
    total: function (sel)
    {
        var self = this;
        jQuery.each(self.dates, function(ind,key) {
            self._subtotalDates(key, sel);
        });
        jQuery.each(self.allprojs, function(ind,key) {
            self._subtotalProj(key, self.subtotalcols, sel);
        });
        self._psubtotal(self.subtotalcols, sel);
        self.hilightholiday(sel);
    },
    hilightholiday: function (sel)
    {
        jQuery("table."+sel+" .holiday td.hours").each(function(ind,elem){
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
            url: 'index.php/timeclock?task=timesheeet.complete&format=json',
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
    findProjs: function ()
    {
        var self = this;
        var found = null;
        self.projs = {};
        jQuery.each(self.projects, function(ind,p) {
            self.projs = {...self.progs, ...p.proj};
        });
        return found;
    },
    update: function ()
    {
        var p;
        var hours;
        var date;
        var hours_ytd = 0;
        var thisyear = null;
        var self = this;
        self.findProjs();
        jQuery.each(self.allprojs, function(ind,p) {
            const proj = self.projs[p];
            jQuery.each(self.dates, function(ind2,date) {
                hours = (self.data[p] && self.data[p][date]) ? parseFloat(self.data[p][date].hours) : 0;
                let mod = true;
                const d = new Date(date);
                if (thisyear === null) {
                    thisyear = d.getUTCFullYear();
                }
                if (proj && proj.max_yearly_hours > 0) {
                    if (thisyear === d.getUTCFullYear()) {
                        mod = (proj.max_yearly_hours > proj.hours_ytd) || (hours > 0);
                    } else {
                        // This covers only the case where the payperiod starts in one year and goes into the next
                        hours_ytd += self.round(hours);
                        mod = proj.max_yearly_hours > hours_ytd;
                    }
                }
                // Set both places where the hours are stored.  One fixed, one that can be modified.
                jQuery("table.timesheet #hours-"+p+"-"+date+"-fixed").text(self.round(hours));
                jQuery("table.timesheet #hours-"+p+"-"+date+"-mod").text(self.round(hours));
                // Set one to diplay and one to not display
                jQuery("table.timesheet #hoursdiv-"+p+"-"+date+"-fixed").css("display", mod ? "none" : "block");
                jQuery("table.timesheet #hoursdiv-"+p+"-"+date+"-mod").css("display", mod ? "block" : "none");
            });
        });
        var d = new Date();

        var month = d.getMonth()+1;
        var day = d.getDate();
        var date = d.getFullYear()+'-'+(month<10 ? '0' : '')+month+'-'+(day<10 ? '0' : '')+day;

        jQuery("th.timeclockheader:not(th.timeclockheader-"+date+")").removeClass("today");
        jQuery("th.timeclockheader-"+date).addClass("today");
        this.total("paid");
        this.total("volunteer");
    },
    refresh: function ()
    {
        var self = this;
        var url = 'index.php/timeclock?view=timesheet&format=json&date='+self.payperiod.start;
        jQuery.ajax({
            url,
            type: 'GET',
            data: {},
            dataType: 'JSON',
            success: function(ret)
            {
                if ( ret.success ){
                    // This puts the new values into the form.  This is required
                    // for new records, as the primary key must be set or another
                    // record will be entered into the database when save is pressed
                    // again.
                    self.data = ret.data.data;
                    self.projects = ret.data.projects;
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