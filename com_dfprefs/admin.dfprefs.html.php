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

require_once $mainframe->getPath('class');

/**
 * Main output class for dfprefs
 *
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage Com_DfPrefs
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2005-2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:Timeclock
 */
class HTML_DfPrefs
{

    /**
     * Displays the user list
     *
     * @param array  &$rows   array of result objects
     * @param object $pageNav Page navigation object
     * @param string $search  Search text
     * @param string $option  The option chosen
     * @param array  $lists   list filters
     * @param string $area    The area
     * @param mixed  $task    The task chosen
     *
     * @return none
     */
    function showUsers(&$rows, $pageNav, $search, $option, $lists, $area, $task)
    {
        ?>
        <form action="index2.php" method="post" name="adminForm">

        <table class="adminheading">
        <tr>
            <th class="user">
            User Preferences Manager
            </th>
            <td>
            Filter:
            </td>
            <td>
            <input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" />
            </td>
        </tr>
        </table>

        <table class="adminlist">
        <tr>
            <th width="2%" class="title">
            #
            </th>
            <th width="3%" class="title">
            <input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows); ?>);" />
            </th>
            <th class="title">
            Name
            </th>
            <th width="15%" class="title" >
            Username
            </th>
            <th width="5%" class="title" nowrap="nowrap">
            Logged In
            </th>
            <th width="15%" class="title">
            Group
            </th>
            <th width="15%" class="title">
            E-Mail
            </th>
            <th width="10%" class="title">
            Last Visit
            </th>
            <th width="1%" class="title">
            ID
            </th>            
        </tr>
        <?php
        $k = 0;
        for ($i=0, $n=count($rows); $i < $n; $i++) {
            $row     =& $rows[$i];

            $img     = $row->block ? 'publish_x.png' : 'tick.png';
            $alt     = $row->block ? 'Enabled' : 'Blocked';
            $link     = 'index2.php?option=com_dfprefs&amp;task=edituser&amp;id='. $row->id. '&amp;hidemainmenu=1&amp;area='.$area;
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                <?php echo $i+1+$pageNav->limitstart;?>
                </td>
                <td>
                <?php echo mosHTML::idBox($i, $row->id); ?>
                </td>
                <td>
                <a href="<?php echo $link; ?>">
                <?php echo $row->name; ?>
                </a>
                <td>
                <?php echo $row->username; ?>
                </td>
                </td>
                <td align="center">
                <?php echo $row->loggedin ? '<img src="images/tick.png" width="12" height="12" border="0" alt="" />': ''; ?>
                </td>
                <td>
                <?php echo $row->groupname; ?>
                </td>
                <td>
                <a href="mailto:<?php echo $row->email; ?>">
                <?php echo $row->email; ?>
                </a>
                </td>
                <td nowrap="nowrap">
                <?php echo mosFormatDate($row->lastvisitDate, _CURRENT_SERVER_TIME_FORMAT); ?>
                </td>
                <td>
                <?php echo $row->id; ?>
                </td>
            </tr>
            <?php
            $k = 1 - $k;
        }
        ?>
        </table>
        <?php echo $pageNav->getListFooter(); ?>

        <input type="hidden" name="option" value="<?php echo $option;?>" />
        <input type="hidden" name="task" value="<?php echo $task;?>" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="hidemainmenu" value="0" />
        </form>
        <?php
    }
    /**
     * Displays the user list
     *
     * @param array  &$rows   array of result objects
     * @param object $pageNav Page navigation object
     * @param string $search  Search text
     * @param string $option  The option chosen
     * @param array  $lists   list filters
     * @param mixed  $task    The task chosen
     *
     * @return none
     */
    function showPrefsDefine(&$rows, $pageNav, $search, $option, $lists, $task) 
    {
        ?>
        <form action="index2.php" method="post" name="adminForm">

        <table class="adminheading">
        <tr>
            <th>
            Preferences Manager
            </th>
            <td>
            Filter:
            </td>
            <td>
            <input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" />
            </td>
            <td width="right">
            <?php echo $lists['type'];?>
            </td>
            <td width="right">
            <?php echo $lists['logged'];?>
            </td>
        </tr>
        </table>

        <table class="adminlist">
        <tr>
            <th width="2%" class="title">
            #
            </th>
            <th width="3%" class="title">
            <input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows); ?>);" />
            </th>
            <th class="title">
            Name
            </th>
            <th width="15%" class="title" nowrap="nowrap" style="text-align: center;">
            Preference Type
            </th>
            <th width="15%" class="title" nowrap="nowrap" style="text-align: center;">
            Variable Type
            </th>
            <th width="15%" class="title" nowrap="nowrap">
            Category
            </th>
            <th width="5%" class="title" nowrap="nowrap">
            Editable
            </th>
            <th width="5%" class="title" nowrap="nowrap">
            Id
            </th>
        </tr>
        <?php
        $k = 0;
        for ($i=0, $n=count($rows); $i < $n; $i++) {
            $row     =& $rows[$i];

            $row->parameters = unserialize($row->parameters);
            $editable = ($row->parameters['editable'] !== false);
            $img     = ($editable) ? 'tick.png' : 'publish_x.png';
            $alt     = $row->block ? 'Enabled' : 'Blocked';
            $link     = 'index2.php?option=com_dfprefs&amp;task=editpref&amp;id='. $row->id. '&amp;hidemainmenu=1';
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                <?php echo $i+1+$pageNav->limitstart;?>
                </td>
                <td>
                <?php if ($editable) echo mosHTML::idBox($i, $row->id); ?>
                </td>
                <td>
                <?php if ($editable) echo '<a href="<?php echo $link; ?>">'; ?>
                <?php echo $row->name; ?>
                <?php if ($editable) echo '</a>'; ?>
                <td align="center">
                <?php echo $row->preftype; ?>
                </td>
                <td align="center">
                <?php echo $row->type; ?>
                </td>
                <td>
                <?php echo $row->area; ?>
                </td>
                <td align="center">
                <img src="images/<?php echo $img; ?>" width="12" height="12" border="0" alt="" />
                </td>
                <td>
                <?php echo $row->id; ?>
                </td>
            </tr>
            <?php
            $k = 1 - $k;
        }
        ?>
        </table>
        <?php echo $pageNav->getListFooter(); ?>

        <input type="hidden" name="option" value="<?php echo $option;?>" />
        <input type="hidden" name="task" value="<?php echo $task;?>" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="hidemainmenu" value="0" />
        </form>
        <?php
    }
    
    /**
     * Displays the user list
     *
     * @param string $option  The option chosen
     * @param array  &$row    database result object
     * @param array  &$prefs  Preferences
     * @param array  &$values Values
     * @param string $area    The area
     *
     * @return none
     */
    function editUser($option, &$row, &$prefs, &$values, $area)
    {

        $yesno[] = mosHTML::makeOption('0', 'No');
        $yesno[] = mosHTML::makeOption('1', 'Yes');

        // This builds the preferences
        $userprefs = array();
        if (is_array($prefs)) {

            foreach ($prefs as $p) {
                $up = array(
                    'name' => $p->name,
                    'help' => $p->help,
                );
                if (!empty($p->parameters['title'])) {
                    $up['title'] = $p->parameters['title'];
                } else {
                    $up['title'] = $up['name'];
                }

                if ($p->preftype == USER) {
                    $varname = 'dfprefs';
                    $up['help'] .= ' <span style="color: red;">User Changable</span> ';
                } else {
                    $varname = 'admin_dfprefs';
                }
                $value = $values[$p->area][$p->name];
                switch($p->type) {
                // Text Inputs
                case 'TEXT':
                    $up['input'] = '<input type="text" ';
                    $up['input'] .= ' name="'.$varname.'['.$p->area.']['.$p->name.']" value="'.$value.'" ';
                    if ($p->parameters['size'] > 0) {
                        $up['input'] .= ' size="'.$p->parameters['size'].'" ';
                    }
                    if ($p->parameters['maxlength'] > 0) {
                        $up['input'] .= ' maxlength="'.$p->parameters['maxlength'].'" ';
                    }
                    $up['input'] .= ' />';
                    break;
                case 'YESNO':
                    $up['input'] = mosHTML::selectList($yesno, $varname.'['.$p->area.']['.$p->name.']', 'class="inputbox"', 'value', 'text', $value);
                    break;
                case 'SELECT':
                    $options = array();
                    foreach ($p->parameters['options'] as $key => $value) {
                        $options[] = mosHTML::makeOption($key, $value);
                    }
                    $up['input'] = mosHTML::selectList($options, $varname.'['.$p->area.']['.$p->name.']', 'class="inputbox"', 'value', 'text', $value);
                    break;
                case 'DATE':
                    mosCommonHTML::loadCalendar();
                    $size = ($p->parameters['size'] > 0) ? $p->parameters['size'] : 13;
                    $maxlength = ($p->parameters['maxlength'] > 0) ? $p->parameters['maxlength'] : 12;
                    
                    $up['input'] = '<input class="inputbox" type="text" name="'.$varname.'['.$p->area.']['.$p->name.']" id="'.$p->name.'" size="'.$size.'" maxlength="'.$maxlength.'" value="'.$value.'" />';
                    $up['input'] .= '<input type="reset" class="button" value="..." onClick="return showCalendar(\''.$p->name.'\', \'y-mm-dd\');">';
                    break;
                    
                }
                if ($p->type != 'HIDDEN') $userprefs[$p->area][] = $up;
            }
        }

        $tab = new mosTabs(true);

        ?>
        <script language="javascript" type="text/javascript">
    
        function submitbutton(pressbutton) {
            var form = document.adminForm;
    
            <?php getEditorContents('editor1', 'answer');?>
          
            if (pressbutton == 'cancel') {
                submitform(pressbutton);
                return;
            }
            submitform(pressbutton);
    
        }
        </script>
    
        <form action="index2.php" method="post" name="adminForm" id="adminForm">
    
    
        <table class="adminheading">
        <tr>
            <th class="user">
            Preferences for <?php echo $row->name?>
            </th>
        </tr>
        </table>
        <?php
        $tab->startPane("dfprefs-config-pane");
     
        foreach ($userprefs as $theArea => $parea) { 
            $areaName = dfprefs::getSystem("com_label", $theArea);
            if (empty($areaName)) $areaName = $theArea;
            $tab->startTab($areaName, $theArea);
    
            ?>
            <table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform" id="prefstable">
            <?php 
            foreach ($parea as $pref) { ?>
                <tr>
                    <td valign="top" align="right"><?php echo $pref['title']?>:</td>
                    <td valign="top" align="right">
                      <?php echo $pref['input']?>
            
                    </td>
                    <td valign="top" align="right">
                        <?php echo $pref['help']?>
            
                    </td>
                </tr>
            <?php 
            } ?>
            </table>
        
            <?php 
            $tab->endTab();
        }    
        $tab->endPane();
    
        ?>
    
    
        <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
        <input type="hidden" name="option" value="<?php echo $option;?>" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="area" id="area" value="<?php echo $area; ?>" />
        </form>
    
    <?php
    }

    /**
     * Displays the user list
     *
     * @param string $option The option chosen
     * @param object &$row   database result object
     *
     * @return none
     */
    function editPrefDefine($option, &$row)
    {

        $yesno[] = mosHTML::makeOption('0', 'No');
        $yesno[] = mosHTML::makeOption('1', 'Yes');

        $preftype[] = mosHTML::makeOption('USER', 'User');
        $preftype[] = mosHTML::makeOption('ADMINUSER', 'Admin');


        $preftypelist = mosHTML::selectList($preftype, 'dfprefs_define[preftype]', 'class="inputbox"', 'value', 'text', $row->preftype);

        $type[] = mosHTML::makeOption('TEXT', 'Text');
        $type[] = mosHTML::makeOption('YESNO', 'Yes/No');

        $typelist = mosHTML::selectList($type, 'dfprefs_define[type]', 'class="inputbox"', 'value', 'text', $row->type);


        ?>
        <script language="javascript" type="text/javascript">
        function submitbutton(pressbutton) {
            var form = document.adminForm;
            <?php getEditorContents('editor1', 'answer');?>
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
        <table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">
          <tr>
            <th colspan="3" class="title" >
              Define a Preference Variable
            </th>
          </tr>
    
          <tr>
            <td style="vertical-align: top; text-align: right;">Name:</td>
            <td>
              <input type="text" name="dfprefs_define[name]" value="<?php echo $row->name?>">
            </td>
            <td>
                The name of the variable
            </td>
          </tr>
          <tr>
            <td style="vertical-align: top; text-align: right;">Title:</td>
            <td>
              <input type="text" name="dfprefs_define[parameters][title]" value="<?php echo $row->parameter['title']?>"/>
            </td>
            <td>
                The title that will show up in the edit preferences dialog.
            </td>
          </tr>
          <tr>
            <td style="vertical-align: top; text-align: right;">Preference Type:</td>
            <td>
                <?php echo $preftypelist?>
            </td>
            <td>
                The type of preference
            </td>
          </tr>
          <tr>
            <td style="vertical-align: top; text-align: right;">Variable Type:</td>
            <td>
                <?php echo $typelist?>
            </td>
            <td>
                The type of variable
            </td>
          </tr>
          <tr>
            <td style="vertical-align: top; text-align: right;">Help Message:</td>
            <td>
              <textarea type="text" name="dfprefs_define[help]" cols="40" rows="5"><?php echo $row->help?></textarea>
            </td>
            <td>
                The help text for this input
            </td>
          </tr>
    
    
        </table>
        <div id="TEXT" name="TEXT" style="display=block">
        <table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform" />
          <tr>
            <th colspan="3" class="title" >
              Text Variable Preferences
            </th>
          </tr>
    
          <tr>
            <td style="vertical-align: top; text-align: right;">Size:</td>
            <td>
              <input type="text" name="dfprefs_define_text[size]" value="<?php echo $row->parameters['size']?>" size="5" maxlength="5">
            </td>
            <td>
                The size of the text field
            </td>
          </tr>
          <tr>
            <td style="vertical-align: top; text-align: right;">Max Length:</td>
            <td>
              <input type="text" name="dfprefs_define_text[maxlength]" value="<?php echo $row->parameters['maxlength']?>" size="5" maxlength="5">
            </td>
            <td>
                The maximum length of the text field
            </td>
          </tr>
          <tr>
            <td style="vertical-align: top; text-align: right;">Default:</td>
            <td>
              <input type="text" name="dfprefs_define[default]" value="<?php echo $row->default?>" size="50">
            </td>
            <td>
                The default value
            </td>
          </tr>
    
    
        </table>
        
        </div>
    
    
        <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
        <input type="hidden" name="option" value="<?php echo $option;?>" />  
        <input type="hidden" name="dfprefs_define[area]" value="Local Preferences" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="savetype" value="pref_define" />
        </form>
    <?php
    }


    /**
     * Displays the user list
     *
     * @param string $option The option chosen
     * @param array  $config The configuration to display
     *
     * @return none
     */
    function showConfig($option, $config)
    {

        $yesno[] = mosHTML::makeOption('0', 'No');
        $yesno[] = mosHTML::makeOption('1', 'Yes');
   
        $debugsel = mosHTML::selectList($yesno, 'df_config[debug]', 'class="inputbox"', 'value', 'text', $config['debug']);
        $cachesel = mosHTML::selectList($yesno, 'df_config[cache]', 'class="inputbox"', 'value', 'text', $config['cache']);
        ?>
        <script language="javascript" type="text/javascript">
        function submitbutton(pressbutton) {
          var form = document.adminForm;
          <?php getEditorContents('editor1', 'answer');?>
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
                dfPrefs Configuration
                </th>
            </tr>
            </table>
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
          <tr>
            <td valign="middle" align="right">Cache Preferences:</td>
            <td>
              <?php echo $cachesel; ?>
            </td>
            <td>
                This enables caching for preferences.  This speeds up the prefs greatly
                and reduces database queries by a large factor.
            </td>
          </tr>
     
        </table>
    
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>" />
        <input type="hidden" name="option" value="<?php echo $option;?>" />
        <input type="hidden" name="task" value="configsave" />
        </form>
        <?php
           
    }


    /**
     * Displays the user list
     *
     * @return none
     */
    function showAbout()
    {
        ?>
        <div style="text-align: left;">
            <table class="adminheading">
            <tr>
                <th class="about">
                About dfPrefs
                </th>
            </tr>
            </table>
            <p>
            dfPrefs allows preferences to be set for users by the users themselves,
            by administrators, and by components and modules.
            </p>    
            <div>com_dfprefs by Scott Price (prices@hugllc.com)</div>
            <div><a href="http://www.hugllc.com/">www.hugllc.com</a></div>
            <div>
            </div>
        </div>

        <?php    
    }

}

?>