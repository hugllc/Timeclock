<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

    HTMLHelper::_('behavior.multiselect');
    $name     = $displayData["name"];
    $nameIsArray = (strpos($name, "][]") !== false);

    $btnInID  = 'btn_'.$displayData["id"].'_in';
    $btnOutID = 'btn_'.$displayData["id"].'_out';
    $selInID  = 'sel_'.$displayData["id"].'_in';
    $selOutID = 'sel_'.$displayData["id"].'_out';
    $divID    = 'div_'.$displayData["id"];
    $hidID    = 'hid_'.$displayData["id"];
    $inID     = $displayData["id"].'_in';
    $outID    = $displayData["id"].'_out';
    $inName   = ($nameIsArray) ? str_replace("[]", "[in]", $name) : $name."_in";
    $outName  = ($nameIsArray) ? str_replace("[]", "[out]", $name) : $name."_out";
?>
            <script type="text/javascript">
                jQuery(document).ready(function() {
                    jQuery("#<?php print $btnOutID; ?>").click(function(e) {
                        var selectedOpts = jQuery("#<?php print $selInID; ?> option:selected");
                        if (selectedOpts.length > 0) {
                            jQuery("#<?php print $selOutID; ?>").append(jQuery(selectedOpts).clone());
                            selectedOpts.each(function(ind, elem) {
                                jQuery("#<?php print $hidID; ?>_"+parseInt(elem.value, 10)).prop("name", "<?php print $outName; ?>[]");
                            });
                            jQuery(selectedOpts).remove();
                        }
                        e.preventDefault();
                    });
        
                    jQuery("#<?php print $btnInID; ?>").click(function(e) {
                        var selectedOpts = jQuery("#<?php print $selOutID; ?> option:selected");
                        if (selectedOpts.length > 0) {
                            jQuery("#<?php print $selInID; ?>").append(jQuery(selectedOpts).clone());
                            selectedOpts.each(function(ind, elem) {
                                jQuery("#<?php print $hidID; ?>_"+parseInt(elem.value, 10)).prop("name", "<?php print $inName; ?>[]");
                            });
                            jQuery(selectedOpts).remove();
                        }
                        e.preventDefault();
                    });
                });
            </script>
            <div class="row">
                <div class="col-lg-3 center">
                    <div class="center"><?php print Text::_($displayData["label_in"]); ?></div>
<?php print HTMLHelper::_(
            'select.genericlist', 
            $displayData["inOptions"], 
            $selInID, 
            array("multiple" => "multiple", "size" => 15, "style" => "width: 95%;", "class" => "dualselect plain"), 
            'value', 
            'text', 
            null,
            $selInID
        ); ?>
                </div>
                <div class="col-lg-2 btn-group center" style="vertical-align: middle;">
                    <input type="button" id="<?php print $btnInID; ?>" value="&lt;&lt;" />
                    <input type="button" id="<?php print $btnOutID; ?>" value="&gt;&gt;" />
                </div>
                <div class="col-lg-3 center">
                    <div class="center"><?php print Text::_($displayData["label_out"]); ?></div>
<?php print HTMLHelper::_(
            'select.genericlist', 
            $displayData["outOptions"], 
            $selOutID, 
            array("multiple" => "multiple", "size" => 15, "style" => "width: 95%;", "class" => "dualselect plain"), 
            'value', 
            'text', 
            null,
            $selOutID
        ); ?>
                </div>
            </div>
            <div id="<?php print $divID; ?>">
<?php
    // This creates a lot of hidden elements that get updated as things change
    // in the select boxes
    foreach (array($inName => "inOptions", $outName => "outOptions") as $id => $data) {
        foreach ($displayData[$data] as $key => $obj) {
            print '<input type="hidden" id="'.$hidID.'_'.(int)$key.'" ';
            print 'name="'.$id.'[]" ';
            print 'value="'.$obj->value.'" />';
            print "\n";
        }
    }
?>
            </div>