<?php
/**
 * This component is for tracking tim
 *
 * PHP Version 5
 *
 * <pre>
 * com_timeclock is a Joomla! 3.1 component
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
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2023 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT: $Id: a70fad7ecea96c148fd07befe386dd1bba7cfe4f $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
namespace HUGLLC\Component\Timeclock\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Pagination\Pagination;

defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Description Here
 *
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2023 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class CustomersModel extends ListModel
{
    /** Where Fields */
    protected $_customer_id = null;
    protected $_published   = 1;
    protected $_total       = null;
    protected $_pagination  = null;
    protected $_defaultSort = "c.customer_id";
    protected $namefield    = "company";

    /**
    * This is the constructor
    */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'c.published',
                'c.name',
                'c.company',
                'contact',
                'c.customer_id',
                'c.notes',
                'c.bill_pto',
                'c.published'
            ];
        }
        parent::__construct($config); 
    }

    public function getListQuery()
    {
        $query = $this->_buildQuery();
        if (empty($where) || !is_array($where)) {
            $query = $this->_buildWhere($query);
        } else {
            foreach ($where as $clause) {
                $query->where($clause);
            }
        }
        if (is_null($sort) || empty($sort)) {
            $this->_setSort($query);
        } else {
            $query->order($sort);
        }
        return $query;
    }
     /**
    * This returns the values given for the sort fields are viable
    * 
    * @param mixed $values Either an array with keys = sort field, or a string
    *                      that is the sort field.
    * 
    * @return  void
    */
    public function checkSortFields($values)
    {
        $fields = $this->getSortFields();
        if (is_string($values)) {
            if (in_array($values, $fields)) {
                // This value is in the array, so it is good
                return $values;
            }
        } else if (is_array($values)) {
            // This returns the key/value pairs in the array that are in $fields
            $ret = array_intersect_key($values, array_flip($fields));
            if (!empty($ret)) {
                return $ret;
            } else {
                return array($this->_defaultSort => Text::_('JDEFAULT'));
            }
        }
        return $this->_defaultSort;
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
        $query->select('c.customer_id, c.company, c.name, c.address1, c.address2,
                        c.city, c.state, c.zip, c.country, c.notes, c.checked_out,
                        c.checked_out_time, c.published, c.created_by, c.created,
                        c.modified, c.bill_pto');
        $query->from('#__timeclock_customers as c');
        $query->select('u.name as author');
        $query->leftjoin('#__users as u on c.created_by = u.id');
        $query->select('v.name as contact');
        $query->leftjoin('#__users as v on c.contact_id = v.id');
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
        $query->select('COUNT(c.customer_id) as count');
        $query->from('#__timeclock_customers as c');
        $query->leftjoin('#__users as u on c.created_by = u.id');
        $query->leftjoin('#__users as v on c.contact_id = v.id');
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
        $id = is_numeric($id) ? $id : $this->_customer_id;
        
        if(is_numeric($id)) {
            $query->where('c.customer_id = ' . (int) $id);
        }
        $search = $this->getState("filter.search");
        if(!empty($search) && is_string($search)) {
            $query->where($db->quoteName("c.company")." LIKE ".$db->quote("%".$search."%"));
        }
        $published = $this->getState("filter.published");
        if (is_numeric($published)) {
            $query->where($db->quoteName('c.published').' = ' . $db->quote((int) $published));
        }
        $user_id = $this->getState("filter.user_id");
        if (is_numeric($user_id)) {
            $query->where($db->quoteName("c.contact_id")." = " . $db->quote((int) $user_id));
        }
        return $query;
    }
    /**
    * Builds the filter for the query
    * @param object Query object
    * @return object Query object
    *
    */
    protected function _setSort(&$query)
    {
        $order = $this->checkSortFields($this->getState("list.ordering"));
        $query->order(
            $order.' '.$this->getState("list.direction")
        );
    }
    /**
    * Method to auto-populate the model state.
    *
    * This method should only be called once per instantiation and is designed
    * to be called on the first call to the getState() method unless the model
    * configuration flag to ignore the request is set.
    * 
    * @return  void
    *
    * @note    Calling getState in this method will result in recursion.
    * @since   12.2
    */
    protected function populateState($ordering = "c.customer_id", $direction = "asc")
    {
        $this->popstate = true;
        $context = is_null($this->context) ? $this->table : $this->context;

        // Load the parameters.
        $params = ComponentHelper::getParams('com_timeclock');
        $this->setState('params', $params);

        // List state information.
        parent::populateState($ordering, $direction);

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
            'c.published',
            'c.name',
            'c.company',
            'contact',
            'c.customer_id',
            'c.notes',
            'c.bill_pto',
            'c.published',
        );
    }

}