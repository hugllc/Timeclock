<?php use HUGLLC\Component\Timeclock\Administrator\Helper\TimeclockView; ?>

        <fieldset class="form-horizontal">
<?php  
print TimeclockView::getFormSet($displayData["name"], $displayData["form"], $displayData["data"]);
?>
            </fieldset>
