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
 * @version    SVN: $Id: 395f76552bdc30c83e33b68c23ab76e0020d684f $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */

defined('_JEXEC') or die('Restricted access');

$baseUrl = "index.php?option=com_timeclock&controller=tools&task=setup";
?>
<form id="adminForm">
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
    <h2><?php print JText::_("COM_TIMECLOCK_TIMECLOCK_TOOLS_SETUP"); ?></h2>
    <h3><?php print JText::_("COM_TIMECLOCK_EXTRA_PACKAGES"); ?></h2>
    <p><?php print JText::_("COM_TIMECLOCK_EXTRA_PACKAGES_DESC"); ?></h2>
<dl>
    <dt>PHPExcel</dt>
    <dd>
        Github link: <a href="http://github.com/PHPOffice/PHPExcel">http://github.com/PHPOffice/PHPExcel</a> 
        <p>Export reports to Excel format</p>
        <p>Status: <?php print $this->phpexcel ? "Installed" : '<a href="'.$baseUrl.'&package=phpexcel">Not Installed (click to install)</a>'; ?></p>
    </dd>
    <dt>PHPGraphLib</dt>
    <dd>
        Github link: <a href="http://github.com/elliottb/phpgraphlib">http://github.com/elliottb/phpgraphlib</a> 
        <p>Creates graphs for reports</p>
        <p>Status: <?php print $this->phpgraph ? "Installed" : '<a href="'.$baseUrl.'&package=phpgraph">Not Installed (click to install)</a>'; ?></p>
    </dd>
</dl>
</div>
</form>