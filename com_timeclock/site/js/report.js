// Only define the Joomla namespace if not defined.
var Report = {
    zero: true,
    empty: true,
    setup: function ()
    {
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
    formData: function ()
    {
        // Collect the base information from the form
        var base = {};
        jQuery("form.timeclock_report").find(":input").each(function(ind,elem) {
            var name = jQuery(elem).attr('name');
            var value = jQuery(elem).val();
            base[name] = value;
        });
        base.date = this.filter.start;
        return base;
    }
}