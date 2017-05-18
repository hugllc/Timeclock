// Only define the Joomla namespace if not defined.
if (typeof(Joomla.Timeclock) === 'undefined') {
    Joomla.Timeclock = {};
}

Joomla.Timeclock.submitbutton = function (task, controller)
{
    if (task == "save") {
        jQuery('#adminForm [name="task"]').val("save");
        jQuery("#adminForm").submit();
    } else if (task == "cancel") {
        jQuery('#adminForm [name="task"]').val("cancel");
        jQuery("#adminForm").submit();
    } else if ((task == "apply") || (task == "update")) {
        var data = {};
        jQuery("#adminForm :input").each(function(ind,elem){
            data[jQuery(elem).attr('name')] = jQuery(elem).val();
        });
        data.task = "apply";
        jQuery.ajax({
            url: jQuery("#adminForm").attr('action')+'&format=json',
            type: 'POST',
            data: data,
            dataType: 'JSON',
            success: function(data)
            {
                //console.log(data);
                if ( data.success ){
                    // This puts the new values into the form.  This is required
                    // for new records, as the primary key must be set or another
                    // record will be entered into the database when save is pressed
                    // again.
                    var key;
                    for (key in data.data) {
                        jQuery('[name="'+key+'"]').val(data.data[key]);
                    }
                    Joomla.renderMessages({message: [data.message]});
                    if (task == "update") {
                        location.reload(false);
                    }
                } else {
                    Joomla.renderMessages({error: [data.message]});
                }
            },
            error: function(data)
            {
                Joomla.renderMessages({error: ["Save Failed"]});
            }
        });
    }

}
