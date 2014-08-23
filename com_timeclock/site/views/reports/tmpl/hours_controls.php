<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.6 component
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');


JHTML::_('behavior.formvalidation');

?>
<script type="text/javascript">
        Window.onDomReady(function(){
            document.formvalidator.setHandler('dateverify',
                function (value) {
                    regex=/[1-9][0-9]{3}-[0-1]{0,1}[0-9]-[0-3]{0,1}[0-9]/;
                    return regex.test(value);
                }
            );
        });
</script>

<form action="<?php JRoute::_("index.php"); ?>" method="post" name="userform" autocomplete="off" class="form-validate">
    <input type="hidden" name="option" value="com_timeclock" />
    <input type="hidden" name="view" value="reports" />
    <div style="white-space: nowrap;">
        <strong><?php echo ucfirst(JText::_("COM_TIMECLOCK_FROM")); ?></strong>
        <?php print JHTML::_("calendar", $this->period["start"], "startDate", "startDate", "%Y-%m-%d", array('class' => "inputbox validate-dateverify required date_label"));?>
        <strong><?php echo JText::_("COM_TIMECLOCK_TO"); ?></strong>
        <?php print JHTML::_("calendar", $this->period["end"], "endDate", "endDate", "%Y-%m-%d", array('class' => "inputbox validate-dateverify required date_label")); ?>
    </div>
    <div>
        <?php print JHTML::_("select.genericList", $this->controls["category"], "cat_id", "", 'value', 'text', $this->cat_id); ?>
        <strong><?php echo JText::_("COM_TIMECLOCK_OR"); ?></strong>
        <?php print JHTML::_("select.genericList", $this->controls["project"], "proj_id", "", 'value', 'text', $this->proj_id); ?>
    </div>
    <div>
        <strong><?php print JText::_("COM_TIMECLOCK_CUSTOMER"); ?>: </strong> <?php print JHTML::_("select.genericList", $this->controls["customer"], "cust_id", "", 'value', 'text', $this->cust_id); ?>
    </div>
    <div>
        <strong><?php print JText::_("COM_TIMECLOCK_PROJECT_MANAGER"); ?>: </strong> <?php print JHTML::_("select.genericList", $this->controls["projManager"], "projManager", "", 'value', 'text', $this->projManager); ?>
    </div>
    <div>
        <strong><?php print JText::_("COM_TIMECLOCK_USER_MANAGER"); ?>: </strong> <?php print JHTML::_("select.genericList", $this->controls["userManager"], "userManager", "", 'value', 'text', $this->userManager); ?>
    </div>
    <div>
        <strong><?php print JText::_("COM_TIMECLOCK_GROUP_BY"); ?>:</strong>
        <?php print JHTML::_("select.genericList", $this->controls["cat_by"], "cat_by", "", 'value', 'text', $this->cat_by); ?>
    </div>
    <div>
        <strong><?php print JText::_("COM_HUGNET_REPORT_TYPE"); ?>:</strong>
        <?php print JHTML::_("select.genericList", $this->controls["report_type"], "report_type", "", 'value', 'text', $this->report_type); ?>
    </div>
    <div style="white-space: nowrap;">
        <button type="submit" class="button validate"><?php print JText::_("COM_TIMECLOCK_APPLY"); ?></button>
    </div>
</form>
