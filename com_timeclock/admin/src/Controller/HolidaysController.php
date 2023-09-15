<?php
/**
 * This component is for tracking time
 * 
 * <pre>
 * com_timeclock is a Joomla! 4 component
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
 * @version    GIT: $Id: 16751c233707692a830d8a351fd78574c8402659 $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
namespace HUGLLC\Component\Timeclock\Administrator\Controller;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use HUGLLC\Component\Timeclock\Administrator\Controller\DisplayController;
use Joomla\CMS\Factory;

\defined( '_JEXEC' ) or die;

/**
 * 
 *
 */
class HolidaysController extends DisplayController
{
    /**
     * Constructor.
     *
     * @param   array                $config   An optional associative array of configuration settings.
     * Recognized key values include 'name', 'default_task', 'model_path', and
     * 'view_path' (this list is not meant to be comprehensive).
     * @param   MVCFactoryInterface  $factory  The factory.
     * @param   CMSApplication       $app      The Application for the dispatcher
     * @param   Input                $input    Input
     *
     * @since   3.0
     */
    public function __construct($config = [], MVCFactoryInterface $factory = null, $app = null, $input = null)
    {
        parent::__construct($config, $factory, $app, $input);

        // $this->registerTask('unpublish', 'publish');
    }

    /**
     * Publish entries
     */
    public function publish() {
        $this->checkToken();
        $input = Factory::getApplication()->getInput();
        $cid = $input->get("holiday_id");
        $task = $input->get("task");
        $this->getModel("holiday")->publish($cid, ($task == "publish") ? 1 : 0);
        $this->setRedirect("index.php?option=com_timeclock&view=holidays");
    }
    /**
     * Checkin entries
     */
    public function checkin() {
        $this->checkToken();
        $input = Factory::getApplication()->getInput();
        $cid = $input->get("holiday_id");
        $model = $this->getModel("holiday");
        foreach ($cid as $id) {
            $model->checkin($id);
        }
        $this->setRedirect("index.php?option=com_timeclock&view=holidays");
    }
}

?>