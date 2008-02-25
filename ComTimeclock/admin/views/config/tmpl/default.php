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
jimport("joomla.html.pane");
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div>
<?php
    $pane = JPane::getInstance("tabs");
    echo $pane->startPane("menu-pane");  
    echo $pane->startPanel(JText::_("Database"), "param-page");
?>
    <table class="admintable">
<?php
for ($i = 0; $i < 3; $i++) {
    ?>
        <tr>
            <td class="key">
                    <?php echo JText::_('Server')." $i"; ?>            
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="host">
                    <?php echo JText::_('Host'); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="prefs[servers][<?php print $i; ?>][host]" id="prefs_servers_<?php print $i; ?>_host" size="32" maxlength="250" value="<?php echo $this->prefs["servers"][$i]["host"];?>" />
            </td>
            <td>
                The ComTimeclock database server to use
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="user">
                    <?php echo JText::_('User'); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="prefs[servers][<?php print $i; ?>][user]" id="prefs_servers_<?php print $i; ?>_user" size="32" maxlength="250" value="<?php echo $this->prefs["servers"][$i]["user"];?>" />
            </td>
            <td>
                The ComTimeclock database user
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="password">
                    <?php echo JText::_('Password'); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="password" name="prefs[servers][<?php print $i; ?>][password]" id="prefs_servers_<?php print $i; ?>_password" size="32" maxlength="250" value="<?php echo $this->prefs["servers"][$i]["password"];?>" />
            </td>
            <td>
                The ComTimeclock database password
            </td>
        </tr>
    <?php
}
?>
    </table>
<?php
    echo $pane->endPanel();
    echo $pane->startPanel(JText::_("User Defaults"), "param-pane");
?>
    <table class="admintable">
        <tr>
            <td width="100" align="right" class="key">
                <label for="Gateways">
                    <?php echo JText::_('Gateways'); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="prefs[gateways]" id="prefs_gateways" size="32" maxlength="250" value="<?php echo $this->prefs["gateways"];?>" />
            </td>
            <td>
                Comma separated list of gateways to use.  Leave blank for all gateways.
            </td>
        </tr>
        <tr>
            <td width="100" align="right" class="key">
                <label for="Gateways">
                    <?php echo JText::_('Decimal Places'); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="prefs[decimalPlaces]" id="prefs_decimalPlaces" size="32" maxlength="250" value="<?php echo $this->prefs["decimalPlaces"];?>" />
            </td>
            <td>
                Default number of decimal places to show.
            </td>
        </tr>
    </table>

<?php
    echo $pane->endPanel();
    echo $pane->endPane();
?>
</div>

<div class="clr"></div>

<input type="hidden" name="option" value="com_ComTimeclock" />
<input type="hidden" name="id" value="-1" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="config" />
</form>
