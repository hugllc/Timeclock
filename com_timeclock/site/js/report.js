// Only define the Joomla namespace if not defined.
var Report = {
    zero: true,
    empty: true,
    setup: function ()
    {
        this.setReport(true);
        this.toggleZero();
        this.toggleEmpty();
    },
    toggleZero: function ()
    {
        if (this.zero) {
            jQuery("#timeclock .zero").hide();
            jQuery("#timeclock .nonzero").show();
            this.zero = false;
        } else {
            jQuery("#timeclock .zero").show();
            jQuery("#timeclock .nonzero").hide();
            this.zero = true;
        }
    },
    toggleEmpty: function ()
    {
        if (this.empty) {
            jQuery("#timeclock .empty").hide();
            jQuery("#timeclock .nonempty").show();
            this.empty = false;
        } else {
            jQuery("#timeclock .empty").show();
            jQuery("#timeclock .nonempty").hide();
            this.empty = true;
        }
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
    save: function ()
    {
        var self = this;
        jQuery.ajax({
            url: 'index.php?option=com_timeclock&controller=report&task=save&format=json',
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
        jQuery("form.report").find(":input").each(function(ind,elem) {
            var name = jQuery(elem).attr('name');
            var value = jQuery(elem).val();
            base[name] = value;
        });
        base.date = this.filter.start;
        return base;
    }
}