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
namespace HUGLLC\Component\Timeclock\Administrator\Trait;

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
trait CustomerDBTrait
{
    /** Fields filter/sort works on */
    protected $filterFields = array(
        'c.published',
        'c.name',
        'c.company',
        'manager',
        'c.customer_id',
        'c.notes',
        'c.bill_pto',
        'c.published',
    );

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
    protected function _buildWhere(&$query)
    { 
        $db = Factory::getDBO();
        $search = $this->getState("filter.search");
        if(!empty($search) && is_string($search)) {
            $query->where($db->quoteName("c.company")." LIKE ".$db->quote("%".$search."%"));
        }
        $published = $this->getState("filter.published");
        if (is_numeric($published)) {
            $query->where($db->quoteName('c.published').' = ' . $db->quote((int) $published));
        }
        $user_id = $this->getState("filter.contact_id");
        if (is_numeric($user_id)) {
            $query->where($db->quoteName("c.contact_id")." = " . $db->quote((int) $contact_id));
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
        $order = $this->getState('list.ordering', 'd.customer_id');
        $dir = $this->getState('list.direction', 'ASC');
        $query->order($order.' '.$dir);
    }


}