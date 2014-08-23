<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.6 component
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

jimport('joomla.application.component.model');

/** Include the project stuff */
require_once "timeclock.php";

/**
 * ComTimeclock model
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009, 2011 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockModelPreferences extends JModel
{
    /** @var string The start date in MySQL format */
    protected $period = array(
        "type" => "month",
    );

    /**
     * Constructor that retrieves the ID from the request
     *
     * @return    void
     */
    function __construct()
    {
        $user =& JFactory::getUser();
        $this->_id = $user->get("id");
        parent::__construct();
    }
    /**
     * Method to display the view
     *
     * @access public
     * @return string
     */
    function getData()
    {
        $row = $this->getTable("TimeclockPrefs");
        $row->load($this->_id);

        return $row;
    }
    /**
     * Method to store a record
     *
     * @access    public
     * @return    boolean    True on success
     */
    function store()
    {
        $row =& $this->getTable("TimeclockPrefs");
        $user =& JFactory::getUser();
        $id = $user->get("id");
        $prefs = JRequest::getVar('prefs', array(), "post", "array");
        if (!is_array($prefs) || empty($id)) {
            return false;
        }
        // This cleans all the admin stuff off.  It shouldn't be set here.
        foreach ($prefs as $k => $v) {
            if (substr(trim(strtolower($k)), 0, 5) == "user_") {
                $uprefs[$k] = $v;
            }
        }
        $clean = array(
        );
        foreach ($clean as $c) {
            $uprefs[$c] = strip_tags(trim($uprefs[$c]));
        }

        // Merge in prefs that are hidden
        $row->load($id);
        $row->prefs = array_merge($row->prefs, $uprefs);

        // Make sure the hello record is valid
        if (!$row->check()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // Store the web link table to the database
        if (!$row->store()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        return true;
    }
}

?>
