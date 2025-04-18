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
        jQuery("table."+sel+" .hours .date-"+key).each((ind,elem) => {
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
            jQuery("table."+sel+" .hours .proj-"+i+"-"+key).each((ind,elem) => {
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
            jQuery("table."+sel+" .subtotal .subtotal-proj-"+i).each((ind,elem) => {
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
        jQuery.each(this.dates, (ind,key) => {
            this._subtotalDates(key, sel);
        });
        jQuery.each(this.allprojs, (ind,key) => {
            this._subtotalProj(key, this.subtotalcols, sel);
        });
        this._psubtotal(this.subtotalcols, sel);
        this.hilightholiday(sel);
    },
    hilightholiday: function (sel)
    {
        jQuery("table."+sel+" .holiday td.hours").each((ind,elem) => {
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
        jQuery.ajax({
            url: 'index.php/timeclock?task=timesheeet.complete&format=json',
            type: 'GET',
            data: this._formData(),
            dataType: 'JSON',
            success: (ret) =>
            {
                if ( ret.success ){
                    //Joomla.renderMessages({'success': [ret.message]});
                    this.setComplete(true);
                } else {
                    Joomla.renderMessages({'error': [ret.message]});
                }
            },
            error: (ret) =>
            {
                Joomla.renderMessages({'error': ['Setting as complete failed']});
            }
        });
        
    },
    findProjs: function ()
    {
        var found = null;
        this.projs = {};
        jQuery.each(this.projects, (ind,p) => {
            this.projs = {...this.progs, ...p.proj};
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
        this.findProjs();
        jQuery.each(this.allprojs, (ind,p) => {
            const proj = this.projs[p];
            jQuery.each(this.dates, (ind2,date) => {
                hours = (this.data[p] && this.data[p][date]) ? parseFloat(this.data[p][date].hours) : 0;
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
                        hours_ytd += this.round(hours);
                        mod = proj.max_yearly_hours > hours_ytd;
                    }
                }
                // Set both places where the hours are stored.  One fixed, one that can be modified.
                jQuery("table.timesheet #hours-"+p+"-"+date+"-fixed").text(this.round(hours));
                jQuery("table.timesheet #hours-"+p+"-"+date+"-mod").text(this.round(hours));
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
        var url = 'index.php/timeclock?view=timesheet&format=json&date='+this.payperiod.start;
        jQuery.ajax({
            url,
            type: 'GET',
            data: {},
            dataType: 'JSON',
            success: (ret) =>
            {
                if ( ret.success ){
                    // This puts the new values into the form.  This is required
                    // for new records, as the primary key must be set or another
                    // record will be entered into the database when save is pressed
                    // again.
                    this.data = ret.data.data;
                    this.projects = ret.data.projects;
                    this.update();
                }
            },
            error: (ret) =>
            {
            }
        });
        
    },
    _formData: function ()
    {
        // Collect the base information from the form
        var base = {};
        jQuery("form.timesheet").find(":input").each((ind,elem) => {
            var name = jQuery(elem).attr('name');
            var value = jQuery(elem).val();
            base[name] = value;
        });
        base.date = this.payperiod.start;
        return base;
    }
}