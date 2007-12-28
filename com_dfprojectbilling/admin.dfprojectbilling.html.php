<?php
/**
 * Short Description
 *
 * PHP Version 5
 *
 * <pre>
 * Timeclock is a Joomla application to keep track of employee time
 * Copyright (C) 2007 Hunt Utilities Group, LLC
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * </pre>
 *
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage Com_DfPrefs
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2005-2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id: sensor.php 545 2007-12-11 21:50:55Z prices $    
 * @link       https://dev.hugllc.com/index.php/Project:Timeclock
 */
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');
require_once($mainframe->getPath('class'));

class HTML_dfprojectbilling {
    function showConfig($option, $config) {
        global $database;

        $debugsel = mosHTML::yesnoRadioList('df_config[debug]', 'class="inputbox"', $config['debug']);

        $query = "SELECT id AS value, name AS text"
        . "\n FROM #__groups"
        . "\n ORDER BY id"
        ;
        $database->setQuery($query);

        $groups = array(mosHTML::makeOption(-1, 'None'));
        $groups = array_merge($groups, $database->loadObjectList());
        
        $readAccess = mosHTML::selectList($groups, 'df_config[groupRead]', 'class="inputbox"', 'value', 'text', $config['groupRead']);
        $writeAccess = mosHTML::selectList($groups, 'df_config[groupWrite]', 'class="inputbox"', 'value', 'text', $config['groupWrite']);
        $reportAccess = mosHTML::selectList($groups, 'df_config[groupReport]', 'class="inputbox"', 'value', 'text', $config['groupReport']);
        $debugAccess = mosHTML::selectList($groups, 'df_config[groupDebug]', 'class="inputbox"', 'value', 'text', $config['groupDebug']);


        
        $tab = new mosTabs(1);

    ?>
     <script language="javascript" type="text/javascript">
    function submitbutton(pressbutton) {
      var form = document.adminForm;
      <?php getEditorContents('editor1', 'answer') ;?>
      if (pressbutton == 'cancel') {
        submitform(pressbutton);
        return;
      }
      // do field validation
      if (form.dbHost.value == ""){
        alert("Hostname must be set.");
      if (form.dbUser.value == ""){
        alert("Username must be set.");
      if (form.dbPassword.value == ""){
        alert("Password must be set.");
      } else {
        submitform(pressbutton);
      }
    }
    </script>

    <form action="index2.php" method="post" name="adminForm" id="adminForm">
        <table class="adminheading">
        <tr>
            <th>
            dfProjectbilling Configuration
            </th>
        </tr>
        </table>
<?php $tab->startPane("config-pane"); ?>

<?php $tab->startTab("Group Access", "groupAccess"); ?>

    <table cellpadding="4" cellspacing="1" border="0" class="adminform">
      <tr>
        <td valign="middle" align="right">Read Access:</td>
        <td>
          <?php print $readAccess; ?>
        </td>
        <td>
            The group the user must be in to read the df data
        </td>
      </tr>
      <tr>
        <td valign="middle" align="right">Write Access:</td>
        <td>
          <?php print $writeAccess; ?>
        </td>
        <td>
            The group the user must be in to write the df data
        </td>
      </tr>
      <tr>
        <td valign="middle" align="right">Report Access:</td>
        <td>
          <?php print $reportAccess; ?>
        </td>
        <td>
            The group the user must be in to read reports
        </td>
      </tr>
      <tr>
        <td valign="middle" align="right">Debug Access:</td>
        <td>
          <?php print $debugAccess; ?>
        </td>
        <td>
            The group the user must be in to get df debugging Information
        </td>
      </tr>
   </table>
<?php $tab->endTab(); ?>
<?php $tab->startTab("General", "general"); ?>
    <table cellpadding="4" cellspacing="1" border="0" class="adminform">
      <tr>
        <td valign="middle" align="right">Debug Enabled:</td>
        <td>
          <?php echo $debugsel; ?>
        </td>
        <td>
          This enables debug output.  This is not for production servers!  It could
          give out information on your server setup.
        </td>
      </tr>
    </table>     
<?php $tab->endTab();?>

    <input type="hidden" name="id" value="<?php echo $row['id']; ?>" />
    <input type="hidden" name="option" value="<?php echo $option;?>" />
    <input type="hidden" name="task" value="" />
    </form>
<?php
        $tab->endPane();
           
    }

    function showAbout()
{
?>
<div style="text-align: left;">
    <table class="adminheading">
    <tr>
        <th class="about">
        About dfProject Billing
        </th>
    </tr>
    </table>
    dfProjectbilling allows projects to be billed to customers.

    <div>com_dfprojectbilling by Scott Price (prices@hugllc.com)</div>
    <div><a href="http://www.hugllc.com/">www.hugllc.com</a></div>
    <div>
    </div>
</div>

<?php    
    }

}

?>