<?php use HUGLLC\Component\Timeclock\Administrator\Helper\ViewHelper; ?>

        <fieldset class="form-horizontal">
<?php  
print ViewHelper::getFormSet($displayData["name"], $displayData["form"], $displayData["data"]);
?>
            </fieldset>
