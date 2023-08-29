<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.6 component
 * Copyright (C) 2023 Hunt Utilities Group, LLC
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
 * @copyright  2023 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: $Id: 395f76552bdc30c83e33b68c23ab76e0020d684f $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

$baseUrl = "index.php?option=com_timeclock&controller=tools";
?>
<form id="adminForm">
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
    <h2><?php print Text::_("COM_TIMECLOCK_CHECKING_DATABASE"); ?>...</h2>
    <?php foreach ((array)$this->results as $cat => $results): ?>
        <h3><?php print Text::_($cat); ?>...</h3>
        <?php if (empty($results)): ?>
        <p><?php print $this->noResults; ?></p>
        <?php endif; ?>
        <?php foreach ((array)$results as $test): ?>
            <div style="margin: 3px; border: 1px solid grey; padding: 3px;">
                <div>
                    <?php print $test["name"]; ?>
                    <?php if ($test["result"] === true) : ?>
                        <span style="float: right; <?php print $style;?> background: green; color: white; "><?php print Text::_("COM_TIMECLOCK_PASS"); ?></span>
                    <?php elseif (is_null($test["result"])) : ?>
                        <span style="float: right; <?php print $style;?> background: yellow; color: black; "><?php print Text::_("COM_TIMECLOCK_WARNING"); ?></span>
                    <?php elseif ($test["result"] === false) : ?>
                        <span style="float: right; <?php print $style;?> background: red; color: white; "><?php print Text::_("COM_TIMECLOCK_FAIL"); ?></span>
                    <?php else: ?>
                        <span style="float: right; <?php print $style;?>"><?php print Text::_("COM_TIMECLOCK_NO_RESULT"); ?></span>
                    <?php endif; ?>
                </div>
                <p>
                    <?php print $test["description"]; ?>
                </p>
                <p style="padding-left: 2em;">
                    <?php print (isset($test["log"])) ? nl2br($test["log"]) : "None"; ?>
                </p>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
    <h2><?php print Text::_("COM_TIMECLOCK_KEY"); ?></h2>
    <div>
        <span style="float: left; margin-right: 1em; <?php print $style;?> background: green; color: white; "><?php print Text::_("COM_TIMECLOCK_PASS"); ?></span>
        <?php print Text::_("COM_TIMECLOCK_PASS_MSG"); ?>
    </div>
    <div>
        <span style="float: left; margin-right: 1em;  <?php print $style;?> background: yellow; color: black; "><?php print Text::_("COM_TIMECLOCK_WARNING"); ?></span>
        <?php print Text::_("COM_TIMECLOCK_WARNING_MSG"); ?>
    </div>
    <div>
        <span style="float: left; margin-right: 1em;  <?php print $style;?> background: red; color: white; "><?php print Text::_("COM_TIMECLOCK_FAIL"); ?></span>
        <?php print Text::_("COM_TIMECLOCK_FAIL_MSG"); ?>
    </div>

    </pre>
</div>
</form>