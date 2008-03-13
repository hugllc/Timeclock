<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.5 component
 * Copyright (C) 2008 Hunt Utilities Group, LLC
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, 
 * MA  02110-1301, USA.
 * </pre>
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die('Restricted access'); 

JHTML::_('behavior.tooltip');

$document        =& JFactory::getDocument();
$dateFormat      = JText::_("DATE_FORMAT_LC1");
$shortDateFormat = JText::_("DATE_FORMAT_LC3");
$document->setTitle(JText::_("Timeclock Notes"));

?>
<div class="componentheading"><?php print JText::_("Notes"); ?></div>
<div>
<?php
foreach ($this->notes as $key => $notes) {
    foreach ($notes as $note) {
        ?>
    <div class="contentpaneopen">        
        <div>
            <div>
                <span class="contentheading"><?php print $note->project_name; ?></span>
                <span class="small"> <?php print JText::_('by')." ".$note->author; ?></span>
            </div>
            <div class="createdate">
                <?php echo JText::_("Worked")." ".JHTML::_('date', $note->worked, JText::_('DATE_FORMAT_LC1')); ?>
                (<?php echo JText::_("Entered")." ".JHTML::_('date', $note->created, JText::_('DATE_FORMAT_LC2')); ?>)
            </div>
        </div>
        <div><?php print $note->notes; ?></div>
    </div>
    <span class="article_separator">&nbsp;</span>
    <?php
    }
}
?>
</div>
