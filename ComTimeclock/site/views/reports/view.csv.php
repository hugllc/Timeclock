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

require_once "view.php";

/**
 * HTML View class for the ComTimeclockWorld Component
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

class TimeclockViewReports extends TimeclockViewReportsBase
{
    /** This is the character(s) that separate the variables */
    public $separator = ",";
    /** This is the character(s) that separate lines */
    public $lineSep = "\r\n";
    
    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return null
     */
    function display($tpl = null)
    {
        parent::pdisplay($tpl);
        $layout        = $this->getLayout();

        $function = $layout."CSV";
        $filename = $layout."Report.csv";

//        header("Content-Type: text/plain");
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=".$filename);
        header("Pragma: no-cache");
        header("Cache-Control: no-cache");
        
        if (method_exists($this, $function)) {
            $this->$function();
        } else {
            $this->reportCSV();
        }
        die();
    }
    /**
     * The display function
     *
     * @return null
     */
    function reportCSV()
    {
        $this->dateCSV();
        $this->reportCSV_header();

        $totals = array();
        foreach ($this->report as $cat => $projArray) {
            print $this->quoteCSV("Category: ".$cat);
            print $this->lineSep;
            foreach ($projArray as $proj => $userArray) {
                print $this->quoteCSV($proj);
                print $this->separator;
                foreach (array_keys($this->totals["user"]) as $user) {
                    $hours = empty($userArray[$user]) ? $this->cell_fill : $userArray[$user];
                    print $this->quoteCSV($hours);
                    print $this->separator;
                }
                print $this->quoteCSV($this->totals["proj"][$proj]);
                print $this->lineSep;
            }
        }
        print $this->quoteCSV("Total");
        print $this->separator;
        foreach ($this->totals["user"] as $user => $hours) {
            print $this->quoteCSV($hours);
            print $this->separator;
        }
        print $this->quoteCSV($this->total);
        print $this->lineSep;
    }

    /**
     * The display function
     *
     * @return null
     */
    function payrollCSV()
    {
        $this->dateCSV();
        $this->payrollCSV_header();

        foreach ($this->report as $id => $time) {
            print $this->quoteCSV($time["name"]);
            print $this->separator;
            for ($w = 0; $w < $this->weeks; $w++) {
                foreach (array("PROJECT", "PTO", "HOLIDAY") as $type) {
                    $hours = (empty($time[$w][$type]["hours"])) ? $this->cell_fill : $time[$w][$type]["hours"];
                    print $this->quoteCSV($hours);
                    print $this->separator;
                }
                $hours = (empty($time[$w]["TOTAL"]["hours"])) ? $this->cell_fill : $time[$w]["TOTAL"]["hours"];
                print $this->quoteCSV($hours);
                print $this->separator;
            }
            $hours = (empty($this->totals["user"][$id])) ? 0 : $this->totals["user"][$id];
            print $this->quoteCSV($hours);
            print $this->lineSep;
        }
        print $this->quoteCSV("Total");
        print $this->separator;
        for ($w = 0; $w < $this->weeks; $w++) {
            foreach (array("PROJECT", "PTO", "HOLIDAY") as $type) {
                $hours = (empty($this->totals["type"][$w][$type])) ? 0 : $this->totals["type"][$w][$type];
                print $this->quoteCSV($hours);
                print $this->separator;
            }
            $hours = (empty($this->totals["type"][$w]["TOTAL"])) ? 0 : $this->totals["type"][$w]["TOTAL"];
            print $this->quoteCSV($hours);
            print $this->separator;
        }
        $hours = (empty($this->totals["total"])) ? 0 : $this->totals["total"];
        print $this->quoteCSV($hours);
        print $this->lineSep;

    }

    /**
     * The display function
     *
     * @return null
     */
    function payrollCSV_header()
    {
        print $this->quoteCSV("Project");
        print $this->separator;
        for ($w = 0; $w < $this->weeks; $w++) {
            $week = $w + 1;
            print $this->quoteCSV("Week ".$week." Worked");
            print $this->separator;
            print $this->quoteCSV("Week ".$week." PTO");
            print $this->separator;
            print $this->quoteCSV("Week ".$week." Holiday");
            print $this->separator;
            print $this->quoteCSV("Week ".$week." Total");
            print $this->separator;
        }
        print $this->quoteCSV("Total");
        print $this->lineSep;
    }
    /**
     * The display function
     *
     * @return null
     */
    function hoursCSV()
    {
        $this->dateCSV();
        $this->hoursCSV_header();
        foreach ($this->report as $user => $catArray) {
            print $this->quoteCSV($user);
            print $this->separator;
            $total = $this->totals["user"][$user];
            foreach (array_keys($this->totals["cat"]) as $cat) {
                $hours = empty($catArray[$cat]) ? $this->cell_fill : $catArray[$cat];
                $perc = round(($hours/$total)*100);
                print $this->quoteCSV($hours);
                print $this->separator;
                print $this->quoteCSV($perc."%");
                print $this->separator;
            }
            print $this->quoteCSV($this->total);
            print $this->separator;
            print $this->quoteCSV("100%");
            print $this->lineSep;
        
        }
        print $this->quoteCSV("Total");
        print $this->separator;
        foreach ($this->totals["cat"] as $cat => $hours) {
            $hours = empty($hours) ? $this->cell_fill : $hours;
            $perc = round(($hours/$total)*100);
            print $this->quoteCSV($hours);
            print $this->separator;
            print $this->quoteCSV($perc."%");
            print $this->separator;
        }
        print $this->quoteCSV($this->total);
        print $this->separator;
        print $this->quoteCSV("100%");
        print $this->lineSep;

    }

    /**
     * The display function
     *
     * @return null
     */
    function hoursCSV_header()
    {
        print $this->quoteCSV("User");
        print $this->separator;
        foreach (array_keys($this->totals["cat"]) as $cat) {
            print $this->quoteCSV($cat." Hours"); 
            print $this->separator;
            print $this->quoteCSV($cat." %"); 
            print $this->separator;
        }
        print $this->quoteCSV("Total Hours");
        print $this->separator;
        print $this->quoteCSV("Total %");
        print $this->lineSep;
    }

    /**
     * The display function
     *
     * @return null
     */
    function dateCSV()
    {
        print $this->quoteCSV($this->period["start"]);
        print $this->separator;
        print $this->quoteCSV("to");
        print $this->separator;
        print $this->quoteCSV($this->period["end"]);
        print $this->lineSep;
    }        

    /**
     * The display function
     *
     * @return null
     */
    function reportCSV_header()
    {
        print $this->quoteCSV("Project");
        print $this->separator;
        foreach ($this->users as $user) {
            print $this->quoteCSV($user);
            print $this->separator;
        }
        print $this->quoteCSV("Total");
        print $this->lineSep;
    }
    
    /**
     * Quotes things that need it
     *
     * @param string $str The string to use
     *
     * @return string
     */
    function quoteCSV($str)
    {
        if (is_string($str)) return '"'.JText::_($str).'"';
        return $str;
    }
    
    
    
}

?>