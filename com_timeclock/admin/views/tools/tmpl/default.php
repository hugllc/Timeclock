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
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: $Id: 7946a752adea5e6c66038cc8c30ce7efe84854dc $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$baseUrl = "index.php?option=com_timeclock&controller=tools";
?>
<form id="adminForm">
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
    <p>
    <?php print Text::_("COM_TIMECLOCK_TIMECLOCK_TOOLS_DESC"); ?>
    </p>
    <ol>
        <li>
            <a href="<?php print Route::_($baseUrl."&task=setup"); ?>">
                <?php print Text::_("COM_TIMECLOCK_TIMECLOCK_TOOLS_SETUP"); ?>
            </a>
        </li>
        <li>
            <a href="<?php print Route::_($baseUrl."&task=dbcheck"); ?>">
                <?php print Text::_("COM_TIMECLOCK_TIMECLOCK_TOOLS_CHECK_DB"); ?>
            </a>
        </li>
    </ol>

    </div>
</form>