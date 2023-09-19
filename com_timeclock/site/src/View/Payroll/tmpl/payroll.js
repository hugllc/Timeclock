// Only define the Joomla namespace if not defined.
var Payroll = {
    setup: function ()
    {
        this.setLocked(this.payperiod.locked);
        if (!this.payperiod.unlock) {
            jQuery("#timeclock .unlock").hide();
        }
        //jQuery("#timeclock .notes").hide();
        this.otHighlight();
    },
    otHighlight: function ()
    {
        jQuery("table.report span.overtime").each(function(ind,elem) {
            var value = parseInt(jQuery(elem).text(), 10);
            if (!isNaN(value) && (value != 0)) {
                jQuery(elem).closest("td").addClass("highlight");
            }
        });
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
                console.log(ret);
                Joomla.renderMessages({'error': ['Locking failed']});
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