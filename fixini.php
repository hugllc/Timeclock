#!/usr/bin/env php
<?php
/**
 * Tests the driver class
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
 * @category   Test
 * @package    JoomlaMock
 * @subpackage TestCase
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: $Id: 95da6790de129908943f3c30a5a83ac6bf9c84ed $
 * @link       https://dev.hugllc.com/index.php/Project:JoomlaMock
 */

$basedirs = array(
    realpath(__DIR__."/com_timeclock/site/languages/"),
    realpath(__DIR__."/com_timeclock/admin/languages/"),
    realpath(__DIR__."/mod_timeclockinfo/language/"),
    realpath(__DIR__."/plg_user_timeclock/language/"),
);
$baselang = "en-GB";
$langs    = array("fr-FR");

foreach ($basedirs as $dir) {
    foreach (array(".com_timeclock.ini", ".com_timeclock.sys.ini") as $filename) {
        $basefile = $dir."/".$baselang."/".$baselang.$filename;
        if (!file_exists($basefile)) {
            continue;
        }
        $base = parse_ini_file($basefile);
        foreach ($langs as $lang) {
            $file   = $dir."/".$lang."/".$lang.$filename;
            $ini    = parse_ini_file($file);
            $string = "";
            foreach ($base as $key => $value) {
                if (!isset($ini[$key])) {
                    $string .= "$key=\"$value\"\n";
                }
            }
            if (!empty($string)) {
                print "Adding :\n$string\nTo: $file\n\n";
                $fd = fopen($file, "a");  // Append stuff to this file
                fwrite($fd, "\n".$string);
                fclose($fd);
            }
        }
    }
}
?>
