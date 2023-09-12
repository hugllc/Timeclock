<?php use HUGLLC\Component\Timeclock\Administrator\Helper\TimeclockView; ?>

    <div class="row-fluid">
        <div class="span9">
<?php  
print TimeclockView::getFormSetH("main", $displayData["form"], $displayData["data"]);
?>
        </div>
        <div class="span3">
<?php  
print TimeclockView::getFormSetV("sidebar", $displayData["form"], $displayData["data"]);
?>
        </div>
    </div>
