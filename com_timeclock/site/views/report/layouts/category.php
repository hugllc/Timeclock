<?php 
defined('_JEXEC') or die('Restricted access'); 
if ($displayData["id"] > 0) :
?>
    <tr class="category">
        <th colspan="<?php print $displayData->cols; ?>" class="hasTooltip" title="<?php print JText::_($displayData["description"]); ?>">
            <?php print JText::_($displayData->name); ?>
        </th>
    </tr>
<?php
endif;