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
jimport("joomla.html.pane");

$title = ($this->add) ? "Add" : "Edit";

TimeclockAdminController::title(JText::_("Timeclock Customer: <small><small>[ ".$title." ]</small></small>"));
JToolBarHelper::apply();
JToolBarHelper::save();
JToolBarHelper::cancel();

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div>
    <table class="admintable">
        <tr>
            <td width="100" align="right" class="key">
                <label for="published">
                    <?php echo JText::_('Active'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.booleanList", "published", "", $this->row->published); ?>
            </td>
            <td>
                Is this customer active?
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="bill_pto">
                    <?php echo JText::_('Bill for Paid Time Off'); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.booleanList", "bill_pto", "", $this->row->bill_pto); ?>
            </td>
            <td>
                Should the billing report show a percentage of PTO based on how many hours the user worked
                on this project?
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="name">
                    <?php echo JText::_('Contact Name'); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="name" id="name" size="32" maxlength="64" value="<?php echo $this->row->name;?>" />
            </td>
            <td>
                The name of the contact person
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="company">
                    <?php echo JText::_('Company'); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="company" id="company" size="32" maxlength="64" value="<?php echo $this->row->company;?>" />
            </td>
            <td>
                The name of the company
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="address1">
                    <?php echo JText::_('Address1'); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="address1" id="address1" size="64" maxlength="64" value="<?php echo $this->row->address1;?>" />
            </td>
            <td>
                The name of the address of the company
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="Published">
                    <?php echo JText::_('Address2'); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="address2" id="address2" size="64" maxlength="64" value="<?php echo $this->row->address2;?>" />
            </td>
            <td>
                The name of the address of the company
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="Published">
                    <?php echo JText::_('City'); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="city" id="city" size="32" maxlength="64" value="<?php echo $this->row->city;?>" />
            </td>
            <td>
                The name of the city of the company
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="Published">
                    <?php echo JText::_('State'); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="state" id="state" size="32" maxlength="64" value="<?php echo $this->row->state;?>" />
            </td>
            <td>
                The name of the state of the company
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="Published">
                    <?php echo JText::_('Zip'); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="zip" id="zip" size="15" maxlength="10" value="<?php echo $this->row->zip;?>" />
            </td>
            <td>
                The name of the zip code of the company
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="Published">
                    <?php echo JText::_('Country'); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="country" id="country" size="32" maxlength="64" value="<?php echo $this->row->country;?>" />
            </td>
            <td>
                The name of the country of the company
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key" style="vertical-align: top;">
                <label for="notes">
                    <?php echo JText::_('Notes'); ?>:
                </label>
            </td>
            <td>
                <textarea class="text_area" type="text" name="notes" id="notes" cols="30" rows="5"><?php echo $this->row->notes;?></textarea>
            </td>
            <td>
                Any notes on this customer
            </td>
        </tr>
    </table>
</div>

<div class="clr"></div>

<input type="hidden" name="option" value="com_timeclock" />
<input type="hidden" name="id" value="<?php print $this->row->id; ?>" />
<input type="hidden" name="created" value="<?php print $this->row->created; ?>" />
<input type="hidden" name="created_by" value="<?php print $this->row->created_by; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="customers" />
</form>
