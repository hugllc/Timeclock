<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_Preferences is a Joomla! 1.6 component
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
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2023 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: $Id: 49c7d569947893ae484e0ae46c7707b65d7186d9 $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock:JoomlaUI
 */

defined('_JEXEC') or die();

use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;

/**
 * Preferences table
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Tables
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2023 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock:JoomlaUI
 */
class TableTimeclockUsers extends Table
{
    public $project_id      = null;
    public $user_id         = null;
    /**
     * Constructor
     *
     * @param object &$db Database connector object
     */
    public function __construct(&$db)
    {
        parent::__construct('#__timeclock_users', "project_id", $db);
    }

    /**
     * Save a row that is bound to this object
     *
     * @param bool $updateNulls Update the nulls
     *
     * @return true
     */
    public function store($updateNulls = false)
    {
        $this->delete($this->project_id, $this->user_id);
        return $db->insertObject("#__timeclock_users", $this, $this->_tbl_key);
    }

    /**
     * Deletes data
     *
     * @param mixed $id The id to delete
     *
     * @return bool
     */
    public function delete($id = NULL)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(TRUE);
        
        $query->delete($db->quoteName('#__timeclock_users'));
        $query->where(
            array(
                $db->quoteName("project_id")." = ".$db->Quote($this->project_id),
                $db->quoteName("user_id")." = ".$db->Quote($this->user_id)
            )
        );
        $db->setQuery($query);
        return (bool) $db->query();
    }
    /**
     * Checks the row
     *
     * @return array
     */
    public function check()
    {

        if (($this->project_id == 0) || ($this->user_id == 0)) {
            return false;
        }
        return true;
    }


}
