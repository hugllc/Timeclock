// Only define the Joomla namespace if not defined.
var Report = {
    setup: function ()
    {
        this.setReport(true);
    },
    setReport: function (live)
    {
        if (this.doreports) {
            if (live) {
                jQuery("#timeclock .livedata").show();
                jQuery("#timeclock .reportdata").hide();
                Timeclock.report = 0;
            } else {
                jQuery("#timeclock .livedata").hide();
                jQuery("#timeclock .reportdata").show();
                Timeclock.report = 1;
            }
        } else {
            jQuery("#timeclock .noreport").hide();
            jQuery("#timeclock .reportdata").hide();
            Timeclock.report = 0;
        }
    },
    lock: function ()
    {
        var self = this;
        jQuery.ajax({
            url: 'index.php?option=com_timeclock&controller=payroll&task=lock&format=json',
            type: 'GET',
            data: self._formData(),
            dataType: 'JSON',
            success: function(ret)
            {
                if ( ret.success ){
                    //Joomla.renderMessages({'success': [ret.message]});
                    self.setLocked(true);
                } else {
                    Joomla.renderMessages({'error': [ret.message]});
                }
            },
            error: function(ret)
            {
                Joomla.renderMessages({'error': ['Locking failed']});
            }
        });
        
    },
    unlock: function ()
    {
        var self = this;
        jQuery.ajax({
            url: 'index.php?option=com_timeclock&controller=payroll&task=unlock&format=json',
            type: 'GET',
            data: self._formData(),
            dataType: 'JSON',
            success: function(ret)
            {
                if ( ret.success ){
                    //Joomla.renderMessages({'success': [ret.message]});
                    self.setLocked(false);
                } else {
                    Joomla.renderMessages({'error': [ret.message]});
                }
            },
            error: function(ret)
            {
                Joomla.renderMessages({'error': ['Locking failed']});
            }
        });
        
    },
    save: function ()
    {
        var self = this;
        jQuery.ajax({
            url: 'index.php?option=com_timeclock&controller=payroll&task=save&format=json',
            type: 'GET',
            data: self._formData(),
            dataType: 'JSON',
            success: function(ret)
            {
                if ( ret.success ){
                    Joomla.renderMessages({'success': [ret.message]});
                    window.location.href = window.location.href;
                } else {
                    Joomla.renderMessages({'error': [ret.message]});
                }
            },
            error: function(ret)
            {
                Joomla.renderMessages({'error': ['Save failed']});
            }
        });
        
    },
    _formData: function ()
    {
        // Collect the base information from the form
        var base = {};
        jQuery("form.payroll").find(":input").each(function(ind,elem) {
            var name = jQuery(elem).attr('name');
            var value = jQuery(elem).val();
            base[name] = value;
        });
        base.date = this.payperiod.start;
        return base;
    }
}