<?php
/**
 * This is part of the joomla mock for testing
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
 * @package    TimeclockTest
 * @subpackage Test
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2007 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$    
 * @link       https://dev.hugllc.com/index.php/Project:Timeclock
 */

/**
 * Converts an absolute URL to SEF format
 *
 * This is the original file header from the file this function was in:
 *
 * <pre>
 * @version $Id$
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * </pre>
 *
 * @param string The URL
 *
 * @return string
 */
function sefRelToAbs($string)
{
    global $mosConfig_live_site, $mosConfig_sef, $mosConfig_multilingual_support;
    global $iso_client_lang;

    //multilingual code url support
    if($mosConfig_sef && $mosConfig_multilingual_support && $string!='index.php' && !eregi("^(([^:/?#]+):)",$string) && !strcasecmp(substr($string,0,9),'index.php') && !eregi('lang=', $string)) {
        $string .= '&amp;lang='. $iso_client_lang;
    }

    // SEF URL Handling
    if ($mosConfig_sef && !eregi("^(([^:/?#]+):)",$string) && !strcasecmp(substr($string,0,9),'index.php')) {
        // Replace all &amp; with &
        $string = str_replace('&amp;', '&', $string);

        // Home index.php
        if ($string == 'index.php') {
            $string = '';
        }

        // break link into url component parts
        $url = parse_url($string);

        // check if link contained fragment identifiers (ex. #foo)
        $fragment = '';
        if (isset($url['fragment'])) {
            // ensure fragment identifiers are compatible with HTML4
            if (preg_match('@^[A-Za-z][A-Za-z0-9:_.-]*$@', $url['fragment'])) {
                $fragment = '#'. $url['fragment'];
            }
        }

        // check if link contained a query component
        if (isset($url['query'])) {
            // special handling for javascript
            $url['query'] = stripslashes(str_replace('+', '%2b', $url['query']));
            // clean possible xss attacks
            $url['query'] = preg_replace("'%3Cscript[^%3E]*%3E.*?%3C/script%3E'si", '', $url['query']);

            // break url into component parts
            parse_str($url['query'], $parts);

            // special handling for javascript
            foreach($parts as $key => $value) {
                if (strpos($value, '+') !== false) {
                    $parts[$key] = stripslashes(str_replace('%2b', '+', $value));
                }
            }
            //var_dump($parts);
            $sefstring = '';

            // Component com_content urls
            if (((isset($parts['option']) && ($parts['option'] == 'com_content' || $parts['option'] == 'content'))) && ($parts['task'] != 'new') && ($parts['task'] != 'edit')) {
            // index.php?option=com_content [&task=$task] [&sectionid=$sectionid] [&id=$id] [&Itemid=$Itemid] [&limit=$limit] [&limitstart=$limitstart] [&year=$year] [&month=$month] [&module=$module]
                $sefstring .= 'content/';

                // task
                if (isset($parts['task'])) {
                    $sefstring .= $parts['task'].'/';
                }
                // sectionid
                if (isset($parts['sectionid'])) {
                    $sefstring .= $parts['sectionid'].'/';
                }
                // id
                if (isset($parts['id'])) {
                    $sefstring .= $parts['id'].'/';
                }
                // Itemid
                if (isset($parts['Itemid'])) {
                    //only add Itemid value if it does not correspond with the 'unassigned' Itemid value
                    if ($parts['Itemid'] != 99999999 && $parts['Itemid'] != 0) {
                        $sefstring .= $parts['Itemid'].'/';
                    }
                }
                // order
                if (isset($parts['order'])) {
                    $sefstring .= 'order,'. $parts['order'].'/';
                }
                // filter
                if (isset($parts['filter'])) {
                    $sefstring .= 'filter,'. $parts['filter'].'/';
                }
                // limit
                if (isset($parts['limit'])) {
                    $sefstring .= $parts['limit'].'/';
                }
                // limitstart
                if (isset($parts['limitstart'])) {
                    $sefstring .= $parts['limitstart'].'/';
                }
                // year
                if (isset($parts['year'])) {
                    $sefstring .= $parts['year'].'/';
                }
                // month
                if (isset($parts['month'])) {
                    $sefstring .= $parts['month'].'/';
                }
                // module
                if (isset($parts['module'])) {
                    $sefstring .= $parts['module'].'/';
                }
                // lang
                if (isset($parts['lang'])) {
                    $sefstring .= 'lang,'. $parts['lang'].'/';
                }

                $string = $sefstring;

            // all other components
            // index.php?option=com_xxxx &...
            } else if (isset($parts['option']) && (strpos($parts['option'], 'com_') !== false)) {
                // do not SEF where com_content - `edit` or `new` task link
                if (!(($parts['option'] == 'com_content') && ((isset($parts['task']) == 'new') || (isset($parts['task']) == 'edit')))) {
                    $sefstring     = 'component/';

                    foreach($parts as $key => $value) {
                        // remove slashes automatically added by parse_str
                        $value        = stripslashes($value);
                        $sefstring .= $key .','. $value.'/';
                    }

                    $string = str_replace('=', ',', $sefstring);
                }
            }
        // no query given. Empty $string to get only the fragment
        // index.php#anchor or index.php?#anchor
        } else {
            $string = '';
        }

        // allows SEF without mod_rewrite
        // comment line below if you dont have mod_rewrite
        return $mosConfig_live_site .'/'. $string . $fragment;

        // allows SEF without mod_rewrite
        // uncomment Line 512 and comment out Line 514

        // uncomment line below if you dont have mod_rewrite
        // return $mosConfig_live_site .'/index.php/'. $string . $fragment;
        // If the above doesnt work - try uncommenting this line instead
        // return $mosConfig_live_site .'/index.php?/'. $string . $fragment;
    } else {
    // Handling for when SEF is not activated
        // Relative link handling
        if ((strpos($string, $mosConfig_live_site) !== 0)) {
            // if URI starts with a "/", means URL is at the root of the host...
            if (strncmp($string, '/', 1) == 0) {
                // splits http(s)://xx.xx/yy/zz..." into [1]="http(s)://xx.xx" and [2]="/yy/zz...":
                $live_site_parts = array();
                eregi("^(https?:[\/]+[^\/]+)(.*$)", $mosConfig_live_site, $live_site_parts);

                $string = $live_site_parts[1] . $string;
            } else {
                $check = 1;

                // array list of non http/https    URL schemes
                $url_schemes     = explode(', ', _URL_SCHEMES);
                $url_schemes[]     = 'http:';
                $url_schemes[]     = 'https:';

                foreach ($url_schemes as $url) {
                    if (strpos($string, $url) === 0) {
                        $check = 0;
                    }
                }

                if ($check) {
                    $string = $mosConfig_live_site .'/'. $string;
                }
            }
        }

        return $string;
    }
}
?>
