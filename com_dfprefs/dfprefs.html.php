<?php
/**
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
 * @subpackage com_dfprefs
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2005-2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id: sensor.php 545 2007-12-11 21:50:55Z prices $    
 * @link       https://dev.hugllc.com/index.php/Project:Timeclock
 */
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

require_once($mainframe->getPath('class'));

class HTML_dfprefs {
    
   
    function HTML_dfprefs()
{
    }

    function editUser($option, &$row, &$prefs, &$values, $area) {

        $yesno[] = mosHTML::makeOption('0', 'No');
        $yesno[] = mosHTML::makeOption('1', 'Yes');

        // This builds the preferences
        $userprefs = array();
        if (is_array($prefs)) {

            foreach ($prefs as $p) {
                $up = array(
                    'name' => $p->name,
                    'help' => $p->help
              );
                if (!empty($p->parameters['title'])) {
                    $up['title'] = $p->parameters['title'];
                } else {
                    $up['title'] = $up['name'];
                }
                
                $varname = 'dfprefs';
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
        $filter_area = mosGetParam($_REQUEST, "filter_area", $area);
        // Make the area drop down
        $fAreas = array(mosHTML::makeOption(0, '- Show All -'));
        $allAreas = array_keys($userprefs);
        $areaName = array();
        foreach ($allAreas as $a) {
            $areaName[$a] = dfprefs::getSystem("com_label", $a);
            if (empty($areaName[$a])) $areaName[$a] = $a;

            $fAreas[] = mosHTML::makeOption($a, $areaName[$a]);
        }
        $areaselect = mosHTML::selectList($fAreas, 'filter_area', 'class="inputbox" size="1" onchange="hideparts(this.value);"', 'value', 'text', "$filter_area");

?>
    <script language="javascript" type="text/javascript">
    function hideparts(area) {
        areas = [ "<?php print implode("\", \"", $allAreas); ?>" ];
        
        for (i = 0; i < areas.length; i++) {
            thisArea = areas[i];
            if ((area == 0) || (areas[i] == area)) {
                document.getElementById(thisArea).style.display = "table-row-group";
            } else {
                document.getElementById(thisArea).style.display = "none";
            }
        }
        document.getElementById('adminForm').area.value = area;
    }
    </script>

    <form action="index.php" method="post" name="adminForm" id="adminForm">

    <div class="componentheading">Preferences for <?=$row->name?></div>
    <table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform" id="prefstable">
    <tr>
        <th style="white-space: nowrap; text-align: right;">
        Area Filter: 
        </th>
        <td>
            <?=$areaselect?>
        </td>
        <td>This cuts down the display to only one area.  
        </td>
    </tr>

<?php foreach ($userprefs as $theArea => $parea) { 
//         if (!is_null($area) && ($theArea !== $area)) continue;
        if (($area == $theArea) || empty($area)) {
            $areaDisplay = "table-row-group";
        } else {
            $areaDisplay = "none";
        }
?>
      <tbody id="<?=$theArea?>" style="display:<?=$areaDisplay?>;">
      <tr>
        <td colspan="3" class="title" >
          <h2><?=$areaName[$theArea]?></h2>
        </td>
      </tr>

<?php   foreach ($parea as $pref) { ?>
      <tr>
        <th style="white-space: nowrap; text-align: right; vertical-align:top;"><?=$pref['title']?>:</th>
        <td style="white-space: nowrap; text-align: left; vertical-align:top;">
          <?=$pref['input']?>

        </td>
        <td style="width: 50%; text-align: left; vertical-align: top;">
            <?=$pref['help']?>

        </td>
      </tr>
<?php } ?>
      </tbody>

<?php    }    ?>

    </table>
    <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
    <input type="hidden" name="option" value="<?php echo $option;?>" />
    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="area" id="area" value="<?php echo $area; ?>" />
    <input type="submit" name="Save" value="Save" />
    </form>

<?php
  }


    function copyright()
{
        echo '<div>com_dfprefs &copy; 2005-2006 <a href="http://www.hugllc.com">Hunt Utilities Group, LLC</a></div>';
    }


}



?>

