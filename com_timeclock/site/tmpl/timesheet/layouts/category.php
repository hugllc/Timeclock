<?php 
defined('_JEXEC') or die(); 

use Joomla\CMS\Language\Text;
if ($displayData["id"] > 0) :
?>
    <tr class="category">
        <th colspan="<?php print $displayData["cols"]; ?>" class="hasTooltip" title="<?php print Text::_($displayData["description"]); ?>">
            <?php print Text::_($displayData["name"]); ?>
        </th>
    </tr>
<?php
endif;