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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined( '_JEXEC' ) or die();

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
trait DefaultDBTrait
{
    /**
    * Build query and where for protected _getList function and return a list
    *
    * @param array  $where The where clauses to use (defaults to stuff from input)
    * @param string $sort  The sort clause to uses (defaults to stuff from input)
    * @param bool   $limit Limit the list for paging
    * 
    * @return array An array of results.
    */
    public function getItemsWhere($where = array(), $sort = null, $limit = true)
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
        $db = Factory::getDBO();
        if ($limit) {
            $db->setQuery(
                $query, 
                $this->getState("limitstart"), 
                $this->getState("limit")
            );
        } else {
            $db->setQuery($query);
        }
        return $db->loadObjectList();
    }
    /**
    * Gets an array of options
    * 
    * @param array  $where The where clauses to use (defaults to stuff from input)
    * @param string $sort  The sort clause to uses (defaults to stuff from input)
    * 
    * @return  array
    */
    public function getOptions($where = null, $sort = null)
    {
        $table = $this->getTable();
        $id    = $table->getKeyName();
        $list  = $this->getItemsWhere($where, $sort, false);
        $options = array();
        foreach ($list as $value) {
            $options[] = HTMLHelper::_(
                'select.option', 
                $value->$id, 
                $this->name($value)
            );
        }
        return $options;
    }
    /**
    * Returns the name associated with this record
    * 
    * @param object $row The row to get the name of
    * 
    * @return string
    */
    public function name($row)
    {
        $name  = property_exists($this, "namefield") ? $this->namefield : "name";
        return Text::_($row->$name);
    }
}