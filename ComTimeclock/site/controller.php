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

jimport('joomla.application.component.controller');

/**
 * ComTimeclock World Component Controller
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockController extends JController
{
    /**
     * Method to display the view
     *
     * @access public
     * @return null
     */
    function display()
    {
        JRequest::setVar('view', 'timeclock');
        parent::display();
    }

    /**
     * Method to display the view
     *
     * @access public
     * @return null
     */
    function addhours()
    {
        JRequest::setVar('view', 'addhours');
        JRequest::setVar('hidemainmenu', 1);
        parent::display();
    }

    /**
     * Method to display the view
     *
     * @access public
     * @return null
     */
    function savehours()
    {
        if (!JRequest::checkToken()) {
            $this->setRedirect("index.php", "Bad form token.  Please try again.", "error");
            return;
        }

        $model = $this->getModel("AddHours");
    
        if ($model->store()) {
            $msg = JText::_('Hours Saved!');
        } else {
            $msg = JText::_('Error Saving Hours');
        }

        $url = JRequest::getVar('referer', $_SERVER["HTTP_REFERER"], '', 'string');
        $this->setRedirect($url, $msg);
    }

    /**
     * Format the project id
     *
     * @param int $id The project ID
     *
     * @return string
     */
    function formatProjId($id)
    {
        return sprintf("%04d", (int)$id);
    }
    
    /**
     * Where statement for the reporting period dates
     *
     * @param string $date Date to use in MySQL format ("Y-m-d H:i:s")
     *
     * @return array
     */ 
    function fixDate($date) {
        preg_match("/[1-9][0-9]{3}-[0-1][0-9]-[0-3][0-9]/", $date, $ret);
        return $ret[0];
    }

}

?>