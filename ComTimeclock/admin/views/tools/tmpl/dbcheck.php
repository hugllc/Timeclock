<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.5 component
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

TimeclockAdminController::title(JText::_('Check Database'));

$style = "width: 10em; font-weight: bold; text-align: center;";

$baseUrl = "index.php?option=com_timeclock&controller=tools";
?>
<div style="width: 500px;">
<h2><?php print JText::_("Checking your Database"); ?>...</h2>
<?php foreach ($this->results as $cat => $results): ?>
    <h3><?php print JText::_($cat); ?>...</h3>
    <?php if (empty($results)): ?>
    <p><?php print JText::_("No Tests"); ?></p>
    <?php endif; ?>
    <?php foreach ($results as $test): ?>
        <div style="margin: 3px; border: 1px solid grey; padding: 3px;">
            <div>
                <?php print $test["name"]; ?>
                <?php if ($test["result"] === true) : ?>
                    <span style="float: right; <?php print $style;?> background: green; color: white; ">PASS</span>
                <?php elseif (is_null($test["result"])) : ?>
                    <span style="float: right; <?php print $style;?> background: yellow; color: black; ">WARNING</span>
                <?php elseif ($test["result"] === false) : ?>
                    <span style="float: right; <?php print $style;?> background: red; color: white; ">FAIL</span>
                <?php else: ?>
                    <span style="float: right; <?php print $style;?>">No Result</span>
                <?php endif; ?>
            </div>
            <p>
                <?php print $test["description"]; ?>
            </p>
            <p style="padding-left: 2em;">
                <?php print nl2br($test["log"]); ?>
            </p>
        </div>
    <?php endforeach; ?>
<?php endforeach; ?>
<h2>Key</h2>
<div>
    <span style="float: left; margin-right: 1em; <?php print $style;?> background: green; color: white; ">PASS</span>
    Everything looks good!
</div>
<div>
    <span style="float: left; margin-right: 1em;  <?php print $style;?> background: yellow; color: black; ">WARNING</span>
    Means that it is a problem, but not fatal
</div>
<div>
    <span style="float: left; margin-right: 1em;  <?php print $style;?> background: red; color: white; ">FAIL</span>
    This will cause major problems in the timeclock, and should be fixed immediately
</div>


<?php var_dump($this->results); ?>
</pre>
</div>