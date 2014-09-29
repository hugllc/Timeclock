// Only define the Joomla namespace if not defined.
var Report = {
    zero: true,
    empty: true,
    setup: function ()
    {
        this.setReport(false);
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
    livedata: function ()
    {
        jQuery('form.report [name="report_id"]').val("");
        jQuery('form.report').submit();
    },
    save: function ()
    {
        //SqueezeBox.initialize();
        var url = "index.php?option=com_timeclock&controller=report&layout=modalsave&tmpl=component";
        SqueezeBox.open(url, {
            size: {x: 500, y: 300},
        });
        //SqueezeBox.fromElement('div#savereport');
    },
    submit: function ()
    {
        var self = this;
        var data = self._formData();
        data.report_name = jQuery('[name="report_name"]').val();
        data.report_description = jQuery('[name="report_description"]').val();
        console.log(data);
        jQuery.ajax({
            url: 'index.php?option=com_timeclock&controller=report&task=save&format=json',
            type: 'POST',
            data: data,
            dataType: 'JSON',
            success: function(ret)
            {
                if ( ret.success ){
                    var report = jQuery('[name="report_id"]');
                    report.append(jQuery("<option></option>").attr("value", ret.data.report_id).text(ret.data.name));
                    report.val(ret.data.report_id);
                    jQuery("form.report").submit();
                } else {
                    self.message(ret.message, "error");
                }
            },
            error: function(ret)
            {
                self.message("No response from server", "error");
            }
        });
        
    },
    /**
     * This posts a message on the individual project form.
     * 
     * @param msg        The message to post
     * @param type       The type of message (success, error, info)
     * 
     * @return null
     */
    message: function (msg, type)
    {
        var div = jQuery("#sbox-content div.alert");
        div.text(msg);
        div.removeClass('alert-success');
        div.removeClass('alert-error');
        div.removeClass('alert-info');
        div.removeClass('element-invisible');
        div.addClass('alert-'+type);
        div.show();
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