<?php
/**
 * This component is for tracking tim
 *
 * PHP Version 5
 *
 * <pre>
 * com_timeclock is a Joomla! 3.1 component
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
 * @version    GIT: $Id: c750c693ed2edc131222e22bb45920555ca1a94f $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;

require_once __DIR__."/default.php";

/**
 * Description Here
 *
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockModelsReports extends TimeclockModelsDefault
{
    /** Where Fields */
    protected $_report_id = null;
    protected $_defaultSort   = "r.report_id";
    protected $table          = "TimeclockReports";

    /**
    * This is the constructor
    */
    public function __construct()
    {
        parent::__construct(); 
        $this->_report_id = !empty($this->id) ? (int) reset($this->id) : null;
    }
    /**
    * Stores the data given, or request data.
    *
    * @param array $data The data to store.  If not given, get the post data
    *
    * @return Table instance with data in it.
    */
    public function store($data=null)
    {
        $data = $data ? $data : JRequest::get('post');
        $row = $this->getTable();

        if (!is_object($row)) {
            return false;
        }
        $date = date("Y-m-d H:i:s");

        // Bind the form fields to the table
        if (!$row->load($this->_report_id)) {
            return false;
        }
        if (!empty($data["name"])) {
            $row->name = $data["name"];
        }
        if (!empty($data["description"])) {
            $row->description = $data["description"];
        }
        if (!empty($data["published"])) {
            $row->published = $data["published"];
        }
        $row->modified = $date;

        // Make sure the record is valid
        if (!$row->check()) {
            return false;
        }
    
        if (!$row->store()) {
            return false;
        }
        // Set our id for things after this.
        if (empty($this->id) || (isset($this->id[0]) && empty($this->id[0]))) {
            $key = $row->getKeyName();
            $this->id = array($row->$key);
        }
        $this->insert_id = $this->id[0];
        
        return $row;

    }
    /**
    * Stores the data given, or request data.
    *
    * @param array $data The data to store.  If not given, get the post data
    *
    * @return Table instance with data in it.
    */
    public function delete(&$pks)
    {
        /*
        $data = $data ? $data : JRequest::get('post');
        $row = $this->getTable();

        if (!is_object($row)) {
            return false;
        }

        $ids = $this->getState("id");
        foreach ($ids as $id) {
            // Load the row
            if (!$row->load($id)) {
                continue;
            }
            $row->delete();
        }
        */
        return true;
    }
    /**
    * Builds the query to be used by the model
    *
    * @return object Query object
    */
    protected function _buildQuery()
    {
        $db = Factory::getDBO();
        $query = $db->getQuery(TRUE);
        $query->select('r.report_id, r.name, r.type, r.description, r.published, 
                        r.created_by, r.created, r.modified, r.startDate, r.endDate');
        $query->from('#__timeclock_reports as r');
        $query->select('v.name as author');
        $query->leftjoin('#__users as v on r.created_by = v.id');
        return $query;
    }
    /**
    * Builds the query to be used to count the number of rows
    *
    * @return object Query object
    */
    protected function _buildCountQuery()
    {
        $db = Factory::getDBO();
        $query = $db->getQuery(TRUE);
        $query->select('COUNT(r.report_id) as count');
        $query->from('#__timeclock_reports as r');
        $query->leftjoin('#__users as v on r.created_by = v.id');
        return $query;
    }
    /**
    * Builds the filter for the query
    * 
    * @param object $query Query object
    * @param int    $id    The id of the object to get
    * 
    * @return object Query object
    *
    */
    protected function _buildWhere(&$query, $id = null)
    { 
        $db = Factory::getDBO();
        $id = is_numeric($id) ? $id : $this->_report_id;
        
        if(is_numeric($id)) {
            $query->where('r.report_id = ' . (int) $id);
        }

        $filter = $this->getState("filter");
        if(!empty($filter->search) && is_string($filter->search)) {
            $query->where($db->quoteName("r.name")." LIKE ".$db->quote("%".$filter->search."%"));
        }
        
        if (is_numeric($filter->published)) {
            $query->where($db->quoteName('r.published').' = ' . $db->quote((int) $filter->published));
        }
        
        if (is_numeric($filter->user_id)) {
            $query->where($db->quoteName("r.created_by")." = " . $db->quote((int) $filter->user_id));
        }

        if (!empty($filter->type)) {
            $query->where($db->quoteName("r.type")." = ".$db->quote($filter->type));
        }
        return $query;
    }
    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     *
     * @since   3.0
     */
    protected function getSortFields()
    {
        return array(
            'r.name',
            'r.created',
            'r.created_by',
            'r.type',
            'r.modified',
            'r.startDate',
            'r.endDate',
        );
    }

}