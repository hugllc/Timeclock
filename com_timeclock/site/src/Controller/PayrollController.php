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
 * @version    GIT: $Id: 592d3a54f46f5b31b8b65f6a0f0b2a1f26cafe40 $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
namespace HUGLLC\Component\Timeclock\Site\Controller;

use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Factory;
use HUGLLC\Component\Timeclock\Site\Controller\ReportController;
use HUGLLC\Component\Timeclock\Administrator\Helper\TimeclockHelper;
use HUGLLC\Component\Timeclock\Site\Model\PayrollModel;

\defined( '_JEXEC' ) or die( 'Restricted access' ); 

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
class PayrollController extends ReportController
{
    /**
    * This function performs everything for this controller.  It is the goto 
    * function.
    *
    * @access public
    * @return boolean
    */
    public function execute($task = null)
    {
        $this->checkAuth();

        if ($task == "lock") {
            return $this->taskLock();
        } else if ($task == "unlock") {
            return $this->taskUnlock();
        }
        return parent::execute($task);
    }
    /**
    * This function saves our stuff and returns a json response
    * 
    * @return null
    */
    protected function taskLock()
    {
        // Get the application
        $app   = Factory::getApplication();
        $app->getInput() or die("Invalid Token");

        $model = new PayrollModel();

        if ($index = $model->lock()) {
            $json = new JsonResponse(
                $index, 
                Text::_("COM_TIMECLOCK_PAYPERIOD_LOCKED"),
                false,  // Error
                false    // Ignore Message Queue
            );
        } else {
            $json = new JsonResponse(
                array(), 
                Text::_("COM_TIMECLOCK_PAYPERIOD_LOCK_FAILED"),
                true,    // Error
                false     // Ignore Message Queue
            );
        }
        Factory::getDocument()->setMimeEncoding( 'application/json' );
        $app->setHeader('Content-Disposition','inline;filename="apply.json"');
        echo $json;
        $app->close();
        $this->model->setAccrual();
        return true;
    }
    /**
    * This function saves our stuff and returns a json response
    * 
    * @return null
    */
    protected function taskUnlock()
    {
        // Get the application
        $app   = Factory::getApplication();
        $app->getInput() or die("Invalid Token");

        $model = new PayrollModel();

        if ($index = $model->unlock()) {
            $json = new JsonResponse(
                $index, 
                Text::_("COM_TIMECLOCK_PAYPERIOD_UNLOCKED"),
                false,  // Error
                false    // Ignore Message Queue
            );
        } else {
            $json = new JsonResponse(
                array(), 
                Text::_("COM_TIMECLOCK_PAYPERIOD_UNLOCK_FAILED"),
                true,    // Error
                false     // Ignore Message Queue
            );
        }
        Factory::getDocument()->setMimeEncoding( 'application/json' );
        $app->setHeader('Content-Disposition','inline;filename="apply.json"');
        echo $json;
        $app->close();
        return true;
    }
    /**
    * This function returns the message to show when a controller is saved
    *
    * @param mixed $data The data to output
    *
    * @return null
    */
    protected function echoJSON($data)
    {
    }

}