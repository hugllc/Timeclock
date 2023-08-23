<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.6 component
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: $Id: cdb4770e02e52b5ccd9776bd0df9bb650838e5f6 $
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Component\Router\RouterBase;


/**
 * Timeclock Router
 *
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2016 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class TimeclockRouter extends RouterBase
{
    /**
    * This function transforms an array of URL parameters into an
    * array of segments that will form the SEF URL
    *
    * @see http://docs.joomla.org/Supporting_SEF_URLs_in_your_component
    *
    * @param array &$query The url parameters to convert
    *
    * @access public
    * @return array of segments
    */
    public function build(&$query)
    {
        $segments = array();
        return $segments;
    }

    /**
    * This function transforms an array of segments back into an array of URL parameters
    *
    * @see http://docs.joomla.org/Supporting_SEF_URLs_in_your_component
    *
    * @param array $segments The segments to transform
    *
    * @access public
    * @return array of segments
    */
    public function parse(&$segments)
    {
    }
}

/**
* This function transforms an array of URL parameters into an
* array of segments that will form the SEF URL
*
* @see http://docs.joomla.org/Supporting_SEF_URLs_in_your_component
*
* @param array &$query The url parameters to convert
*
* @access public
* @return array of segments
*/
function TimeclockBuildRoute(&$query)
{
    $router = new TimeclockRouter;
    return $router->build($query);

}
/**
* This function transforms an array of segments back into an array of URL parameters
*
* @see http://docs.joomla.org/Supporting_SEF_URLs_in_your_component
*
* @param array $segments The segments to transform
*
* @access public
* @return array of segments
*/
function TimeclockParseRoute($segments)
{
        $router = new TimeclockRouter;
        return $router->parse($segments);
}