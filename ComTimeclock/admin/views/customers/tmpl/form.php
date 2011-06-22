<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.6 component
 * Copyright (C) 2008-2009, 2011 Hunt Utilities Group, LLC
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
 * @copyright  2008-2009, 2011 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die('Restricted access');
jimport("joomla.html.pane");

$title = ($this->add) ? "Add" : "Edit";

TimeclockHelper::title(JText::_("Timeclock Customer: <small><small>[ ".$title." ]</small></small>"));
JToolBarHelper::apply("customers.apply");
JToolBarHelper::save("customers.save");
JToolBarHelper::cancel("customers.cancel");

?>
<form action="index.php" method="post" id="adminForm" name="adminForm">
<div>
    <table class="admintable">
        <tr>
            <td width="100" align="right" class="key">
                <label for="published">
                    <?php echo JText::_(COM_TIMECLOCK_ACTIVE); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.booleanList", "published", "", $this->row->published); ?>
            </td>
            <td>
                <?php echo JText::_(COM_TIMECLOCK_ACTIVE_CUSTOMER_DESC); ?>
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="bill_pto">
                    <?php echo JText::_(COM_TIMECLOCK_BILL_FOR_PTO); ?>:
                </label>
            </td>
            <td>
                <?php print JHTML::_("select.booleanList", "bill_pto", "", $this->row->bill_pto); ?>
            </td>
            <td>
                <?php echo JText::_(COM_TIMECLOCK_BILL_FOR_PTO_CUSTOMER_DESC); ?>
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="name">
                    <?php echo JText::_(COM_TIMECLOCK_CONTACT_NAME); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="name" id="name" size="32" maxlength="64" value="<?php echo $this->row->name;?>" />
            </td>
            <td>
                <?php echo JText::_(COM_TIMECLOCK_CONTACT_NAME_CUSTOMER_DESC); ?>
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="company">
                    <?php echo JText::_(COM_TIMECLOCK_COMPANY); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="company" id="company" size="32" maxlength="64" value="<?php echo $this->row->company;?>" />
            </td>
            <td>
                <?php echo JText::_(COM_TIMECLOCK_COMPANY_CUSTOMER_DESC); ?>
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="address1">
                    <?php echo JText::_(COM_TIMECLOCK_ADDRESS1); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="address1" id="address1" size="64" maxlength="64" value="<?php echo $this->row->address1;?>" />
            </td>
            <td>
                <?php echo JText::_(COM_TIMECLOCK_ADDRESS1_CUSTOMER_DESC); ?>
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="Published">
                    <?php echo JText::_(COM_TIMECLOCK_ADDRESS2); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="address2" id="address2" size="64" maxlength="64" value="<?php echo $this->row->address2;?>" />
            </td>
            <td>
                <?php echo JText::_(COM_TIMECLOCK_ADDRESS2_CUSTOMER_DESC); ?>
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="Published">
                    <?php echo JText::_(COM_TIMECLOCK_CITY); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="city" id="city" size="32" maxlength="64" value="<?php echo $this->row->city;?>" />
            </td>
            <td>
                <?php echo JText::_(COM_TIMECLOCK_CITY_CUSTOMER_DESC); ?>
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="Published">
                    <?php echo JText::_(COM_TIMECLOCK_STATE); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="state" id="state" size="32" maxlength="64" value="<?php echo $this->row->state;?>" />
            </td>
            <td>
                <?php echo JText::_(COM_TIMECLOCK_STATE_CUSTOMER_DESC); ?>
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="Published">
                    <?php echo JText::_(COM_TIMECLOCK_ZIP); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="zip" id="zip" size="15" maxlength="10" value="<?php echo $this->row->zip;?>" />
            </td>
            <td>
                <?php echo JText::_(COM_TIMECLOCK_ZIP_CUSTOMER_DESC); ?>
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="Published">
                    <?php echo JText::_(COM_TIMECLOCK_COUNTRY); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="country" id="country" size="32" maxlength="64" value="<?php echo $this->row->country;?>" />
            </td>
            <td>
                <?php echo JText::_(COM_TIMECLOCK_COUNTRY_CUSTOMER_DESC); ?>
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key" style="vertical-align: top;">
                <label for="notes">
                    <?php echo JText::_(COM_TIMECLOCK_NOTES); ?>:
                </label>
            </td>
            <td>
                <textarea class="text_area" type="text" name="notes" id="notes" cols="30" rows="5"><?php echo $this->row->notes;?></textarea>
            </td>
            <td>
                <?php echo JText::_(COM_TIMECLOCK_NOTES_CUSTOMER_DESC); ?>
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
<?php print JHTML::_("form.token"); ?>
</form>
