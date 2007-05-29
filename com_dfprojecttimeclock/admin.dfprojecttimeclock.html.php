<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
    $Id: admin.dfprojecttimeclock.html.php 410 2006-12-30 17:32:39Z prices $
    @file admin.dfprojecttimeclock.html.php
    
    @verbatim
    Copyright 2005 Hunt Utilities Group, LLC (www.hugllc.com)
    
    admin.dfprojecttimeclock.html.php is part of com_dfprojecttimeclock.

    com_dfprojecttimeclock is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    com_dfprojecttimeclock is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Foobar; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
    @endverbatim
*/

require_once( $mainframe->getPath( 'class' ) );

class HTML_dfprojecttimeclock {
    function showConfig($option, $config) {
        global $database;

        mosCommonHTML::loadCalendar();

        $periodtypes = array(
            mosHTML::makeOption( 'FIXED', 'Fixed' ),
        );

        $debugsel = mosHTML::yesnoRadioList( 'df_config[debug]', 'class="inputbox"', $config['debug'] );

        $query = "SELECT id AS value, name AS text"
        . "\n FROM #__groups"
        . "\n ORDER BY id"
        ;
        $database->setQuery( $query );

        $groups = array(mosHTML::makeOption( -1, 'None' ));
        $groups = array_merge($groups, $database->loadObjectList());
        
        $query = "SELECT id AS value, name AS text"
        . "\n FROM #__users"
        . "\n ORDER BY name"
        ;
        $database->setQuery( $query );
        $users = $database->loadObjectList();

        $configUsers = array();
        $groupAccess = array();
        foreach(array('Timeclock', 'TSummary', 'TOthers', 'HolidayHours', 'SickHours', 'VacationHours') as $type) {
            $groupAccess[$type] = mosHTML::selectList( $groups, 'df_config[group'.$type.']', 'class="inputbox"', 'value', 'text', $config['group'.$type] );
        }
        
        $tab = new mosTabs(1);

    ?>
     <script language="javascript" type="text/javascript">
    function submitbutton(pressbutton) {
      var form = document.adminForm;
      <?php getEditorContents( 'editor1', 'answer' ) ;?>
      if (pressbutton == 'cancel') {
        submitform( pressbutton );
        return;
      }
      // do field validation
      if (form.dbHost.value == ""){
        alert( "Hostname must be set." );
      if (form.dbUser.value == ""){
        alert( "Username must be set." );
      if (form.dbPassword.value == ""){
        alert( "Password must be set." );
      } else {
        submitform( pressbutton );
      }
    }
    </script>

    <form action="index2.php" method="post" name="adminForm" id="adminForm">
		<table class="adminheading">
		<tr>
			<th>
			dfProject Timeclock Configuration
			</th>
		</tr>
		</table>
<?php $tab->startPane("config-pane"); ?>
<?php $tab->startTab("Group Access", "groupAccess"); ?>
    <table cellpadding="4" cellspacing="1" border="0" class="adminform">
      <tr>
        <td valign="middle" align="right">Timeclock:</td>
        <td>
          <?php print $groupAccess['Timeclock']; ?>
        </td>
        <td>
            The group the user must be in to read the df data
        </td>
      </tr>
      <tr>
        <td valign="middle" align="right">Timeclock Summarys:</td>
        <td>
          <?php print $groupAccess['TSummary']; ?>
        </td>
        <td>
            The group the user must be in to write the df data
        </td>
      </tr>
      <tr>
        <td valign="middle" align="right">Others' Timeclocks:</td>
        <td>
          <?php print $groupAccess['TOthers']; ?>
        </td>
        <td>
            The group the user must be in to view other peoples timesheets
        </td>
      </tr>
      <tr>
        <td valign="middle" align="right">Get Holiday Hours:</td>
        <td>
          <?php print $groupAccess['HolidayHours']; ?>
        </td>
        <td>
            The group the user must be in to get paid holiday hours
        </td>
      </tr>
   </table>
<?php $tab->endTab(); ?>
<?php $tab->startTab("User Settings", "userSettings"); ?>
    <table cellpadding="4" cellspacing="1" border="0" class="adminform">
      <tr>
        <td valign="middle" align="right">Max Daily Hours:</td>
        <td>
          <?php print mosHTML::integerSelectList( 1, 24, 1, 'df_config[maxhours]','class="inputbox"' , $config['maxhours']); ?>
        </td>
        <td>
            The maximum number of hours a person can log in one day.
        </td>
      </tr>
      <tr>
        <td valign="middle" align="right">Max Decimal Places:</td>
        <td>
          <?php print mosHTML::integerSelectList( 1, 6, 1, 'df_config[decimalPlaces]','class="inputbox"' , $config['decimalPlaces']); ?>
        </td>
        <td>
            The maximum number of hours a person can log in one day.
        </td>
      </tr>
 
    </table>
<?php $tab->endTab();?>
<?php $tab->startTab("Pay Period", "payPeriod"); ?>
    <table cellpadding="4" cellspacing="1" border="0" class="adminform">
      <tr>
        <td valign="middle" align="right">First Pay Period Start:</td>
        <td>
    		<input class="inputbox" type="text" name="df_config[periodstart]" id="periodstart" size="25" maxlength="19" value="<?=$config['periodstart']?>" />
			<input type="reset" class="button" value="..." onClick="return showCalendar('periodstart', 'y-mm-dd');">
        </td>
        <td>
            The date the first pay period starts on.
        </td>
      </tr>
      <tr>
        <td valign="middle" align="right">Type of Pay Period:</td>
        <td>
            <?php print mosHTML::selectList( $periodtypes, 'df_config[periodtype]', 'class="inputbox"', 'value', 'text', $config['periodtype'] ); ?>
        </td>
        <td>
            Type of Payperiod.  Fixed = Fixed length pay period.
        </td>
      </tr>
      <tr>
        <td valign="middle" align="right">Length of Pay Period:</td>
        <td>
          <?php print mosHTML::integerSelectList( 1, 31, 1, 'df_config[periodlength]','class="inputbox"' , $config['periodlength']); ?>
        </td>
        <td>
            The number of days in a fixed length pay period.
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

    function showAbout() {
?>
<div style="text-align: left;">
	<table class="adminheading">
	<tr>
		<th class="about">
		About dfProject Timeclock
		</th>
	</tr>
	</table>
	dfProject timeclock allows time to be entered by employees and tracked, as well
	as used for payroll.  

    <div>com_dfprojecttimeclock by Scott Price (prices@hugllc.com)</div>
    <div><a href="http://www.hugllc.com/">www.hugllc.com</a></div>
    <div>
    </div>
</div>

<?php    
    }

}


?>