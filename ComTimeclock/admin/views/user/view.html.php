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
/** Check to make sure we are under Joomla */
defined('_JEXEC') or die('Restricted access');

/** Import the views */
jimport('joomla.application.component.view');

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
class TimeclockAdminViewUser extends JView
{
    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return none
     */
    function display($tpl = null)
    {
        $model =& JModel::getInstance("Users", "TimeclockAdminModel");
        $projectModel =& JModel::getInstance("Projects", "TimeclockAdminModel");
        $userModel =& JModel::getInstance("Users", "TimeclockAdminModel");

        // Set this as the default model
        $this->setModel($model, true);
        $row = $this->get("Data");

        $user =& JFactory::getUser();
        
        $cid = JRequest::getVar('cid', 0, '', 'array');
        if ($cid[0] < 1) $this->setRedirect('index.php?option=com_timeclock&controller=holidayss', "No User given!");

        $user =& JFactory::getUser($cid[0]);
        
        $lists["status"] = array(
            JHTML::_("select.option", "FULLTIME", "Full Time"),
            JHTML::_("select.option", "PARTTIME", "Part Time"),
            JHTML::_("select.option", "CONTRACTOR", "Contractor"),
            JHTML::_("select.option", "TEMPORARY", "Temporary"),
            JHTML::_("select.option", "TERMINATED", "Terminated"),
        );

        $lists["userProjects"] = $model->getUserProjects($cid[0]);
        $uProj = array();
        foreach ($lists["userProjects"] as $p) {
            $uProj[] = $p->id;
        }
        $lists["projects"] = $projectModel->getOptions("WHERE published=1 AND Type <> 'CATEGORY'", "Add Project", $uProj);
        $lists["users"]       = $userModel->getOptions($userWhere, "Select User", $cid);

        $this->assignRef("user", $user);
        $this->assignRef("lists", $lists);
        $this->assignRef("row", $row);
        parent::display($tpl);
    }

}

?>
