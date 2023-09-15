<?php use HUGLLC\Component\Timeclock\Administrator\Helper\ViewHelper; ?>

    <div class="row-fluid">
        <div class="span9">
<?php  
print ViewHelper::getFormSetH("main", $displayData["form"], $displayData["data"]);
?>
        </div>
        <div class="span3">
<?php  
print ViewHelper::getFormSetV("sidebar", $displayData["form"], $displayData["data"]);
?>
        </div>
    </div>
