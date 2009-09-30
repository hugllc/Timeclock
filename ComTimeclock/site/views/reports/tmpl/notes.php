<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.5 component
 * Copyright (C) 2008-2009 Hunt Utilities Group, LLC
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
 * @copyright  2008-2009 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');

$document        =& JFactory::getDocument();
$dateFormat      = JText::_("DATE_FORMAT_LC1");
$shortDateFormat = JText::_("DATE_FORMAT_LC3");
$document->setTitle($this->params->get('page_title'));

?>
<?php if ($this->params->get('show_page_title')) : ?>
<div class="componentheading<?php echo $this->params->get('pageclass_sfx');?>">
        <?php echo $this->escape($this->params->get('page_title')); ?>
</div>
<?php endif; ?>
<?php if (is_array($this->controls)) : ?>
    <?php print $this->loadTemplate("controls"); ?>
<?php endif; ?>
<form action="<?php print JROUTE::_("index.php"); ?>" method="post" name="userform">
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
        </div>
        <div><?php print $note->notes; ?></div>
        <?php if ($this->params->get('show_entered_date')) : ?>
        <div class="modifydate"><?php echo JText::_("Entered")." ".JHTML::_('date', $note->created, JText::_('DATE_FORMAT_LC2')); ?></div>
        <?php endif; ?>
    </div>
    <span class="article_separator">&nbsp;</span>
    <?php
}
if (count($this->notes) == 0) print "No notes found";
?>
    <div style="text-align: center; padding: 10px;">
        <?php if ($this->params->get('show_filter')) : ?>
        <div style="padding: 3px;">
            <?php echo JText::_('Filter'); ?>:
            <input class="inputbox" type="text" id="report_search" name="report_search" size="30" maxlength="255" value="<?php echo $this->lists["search"];?>" />
            <?php echo JText::_('in'); ?>:
            <?php echo JHTML::_('select.genericlist', $this->lists['search_options'], 'report_search_filter', '', 'value', 'text', $this->lists['search_filter'], 'search_filter'); ?>
            <button onclick="this.form.submit();"><?php echo JText::_('Go'); ?></button>
            <button onclick="document.getElementById('report_search').value='';document.getElementById('report_search_filter').value='<?php print $this->lists['search_options_default'];?>';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_('Reset'); ?></button>
        </div>
        <?php endif; ?>
        <?php if ($this->params->get('show_pagination')) : ?>
        <div style="text-align: center;"><?php echo JText::_("Display Num").": ".$this->pagination->getLimitBox(); ?></div>
        <div style="text-align: center;"><?php echo $this->pagination->getPagesLinks(); ?></div>
        <div style="text-align: center;"><?php echo $this->pagination->getPagesCounter(); ?></div>
        <?php endif; ?>
    </div>
</div>
<input type="hidden" name="option" value="com_timeclock" />
<input type="hidden" name="layout" value="notes" />
<input type="hidden" name="view" id="view" value="reports" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
