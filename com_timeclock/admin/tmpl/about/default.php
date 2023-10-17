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
 * @version    GIT: $Id: 7802c37cdec819f01202b94efe19229a378d74fa $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die();
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_("jquery.framework");

?>
<form id="adminForm">
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
    <div style="width: 500px;">
        <p>
        Timeclock is a component designed to track the time employees spend on projects.
        It is designed to be used to facilitate payroll for employees that range across multiple sites.
        More information on Timeclock can be found on the <a href="https://github.com/hugllc/Timeclock"
        >https://github.com/hugllc/Timeclock</a>
        </p>
        <p>
        There are some add on packages that can be downloaded for timeclock to add
        extra functionality.  If you haven't yet, please go to the setup page under
        tools to add them.  <a href="index.php?option=com_timeclock&controller=tools&task=setup">Click here to go there</a>.
        </p>
        <p>
        Timeclock was written by Scott Price (<a href="mailto:prices@hugllc.com">prices@hugllc.com</a>) an employee of
        <a href="http://www.hugllc.com">Hunt Utilities Group, LLC.</a>.
        </p>
        <h2 style="clear:both;">Contributions and Thanks</h2>
        <p>
        <ul>
        <li>The next and previos icons were created by and © <a href="http://www.notmart.org/">Marco Martin</a> and posted on
        <a href="http://www.iconfinder.net">iconfinder.net</a>.  They are licensed
        under the GNU GPL.</li>
        <li>This component is based on the <a href="http://lendr.websparkinc.com/">the Lendr component</a>.
        This is an excellent Joomla3 tutorial.</li>
        </ul>
        </p>
        <h2 style="clear:both;">License</h2>
        <a href="../components/com_timeclock/LICENSE.TXT">License File</a><br />
        <a href="https://github.com/hugllc/Timeclock">Timeclock</a> is a Joomla! 4 component<br />
        Copyright © 2023 <a href="http://www.hugllc.com">Hunt Utilities Group, LLC</a><br />
        <p>
        This program is free software; you can redistribute it and/or
        modify it under the terms of the GNU General Public License
        as published by the Free Software Foundation; either version 3
        of the License, or (at your option) any later version.
        </p>
        <p>
        This program is distributed in the hope that it will be useful,
        but WITHOUT ANY WARRANTY; without even the implied warranty of
        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
        GNU General Public License for more details.
        </p>
        <p>
        You should have received a copy of the GNU General Public License
        along with this program; if not, write to the Free Software
        Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
        MA  02110-1301, USA.
        </p>
    </div>
</div>
</form>
