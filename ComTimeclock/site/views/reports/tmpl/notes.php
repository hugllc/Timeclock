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
<form action="<?php JROUTE::_("index.php"); ?>" method="post" name="adminForm">
<div>
<?php
foreach ($this->notes as $key => $note) {
    $title = $note->project_name;
    $title = (empty($note->category_name)) ? $title : $note->category_name." : ".$title; 
    $title = (empty($note->company_name)) ? $title : $note->company_name." : ".$title; 
    ?>
    <div class="contentpaneopen">        
        <div>
            <div class="contentheading"><?php print $title; ?></div>
            <div class="small"> <?php print JText::_('by')." ".$note->author; ?> <span>(<?php print $note->hours." ".JText::_("hours");?>)</span></div>
            <div class="createdate"><?php echo JText::_("Worked")." ".JHTML::_('date', $note->worked, JText::_('DATE_FORMAT_LC1')); ?></div>
<!--            <div class="createdate"><?php echo JText::_("Entered")." ".JHTML::_('date', $note->created, JText::_('DATE_FORMAT_LC2')); ?></div>-->
        </div>
        <div><?php print $note->notes; ?></div>
    </div>
    <span class="article_separator">&nbsp;</span>
    <?php
}
?>
    <div align="center">
        <div style="text-align: center;"><?php echo JText::_("Display Num").$this->pagination->getLimitBox(); ?></div>
        <div style="text-align: center;"><?php echo $this->pagination->getPagesLinks(); ?></div>
        <div style="text-align: center;"><?php echo $this->pagination->getPagesCounter(); ?></div>
    </div>
</div>
<input type="hidden" name="option" value="com_timeclock" />
<input type="hidden" name="layout" value="notes" />
<input type="hidden" name="view" id="view" value="reports" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
