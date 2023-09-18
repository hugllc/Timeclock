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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Factory;
use HUGLLC\Component\Timeclock\Site\Controller\DisplayController;
use HUGLLC\Component\Timeclock\Administrator\Helper\TimeclockHelper;
use HUGLLC\Component\Timeclock\Site\Model\TimesheetModel;
use HUGLLC\Component\Timeclock\Site\Model\AddhoursModel;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

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
class TimesheetController extends DisplayController
{
    /** This is where we store our task */
    private $_task = null;
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
        // These don't work for some reason
        // $this->registerTask('addhours', 'addhours');
        // $this->registerTask('apply', 'apply');
        // $this->registerTask('complete', 'complete');
    }

    /**
    * This is the main function that executes everything.
    *
    * @return bool
    */
    public function execute($task = NULL)
    {
        // This is only needed because the 'registerTask' function calls in the constructor don't seem to do anything.
        $this->checkAuth();
        if ($task == "apply") {
            return $this->apply();
        } else if ($task == "complete") {
            return $this->complete();
        } else if ($task == "addhours") {
            return $this->addhours();
        }
        return true;
    }
    /**
    * This function saves our stuff and returns a json response
    * 
    * @return null
    */
    protected function complete()
    {
        // Get the application
        $app   = Factory::getApplication();
        $app->getInput() or die("Invalid Token");

        $model = new TimesheetModel();
        $this->checkMe($model);

        if ($index = $model->complete()) {
            $json = new JsonResponse(
                $index, 
                Text::_("COM_TIMECLOCK_PAYPERIOD_COMPLETE"),
                false,  // Error
                false    // Ignore Message Queue
            );
        } else {
            $json = new JsonResponse(
                array(), 
                Text::_("COM_TIMECLOCK_PAYPERIOD_COMPLETE_FAILED"),
                true,    // Error
                false     // Ignore Message Queue
            );
        }
        Factory::getDocument()->setMimeEncoding( 'application/json' );
        $app->setHeader('Content-Disposition','inline;filename="apply.json"');
        echo $json;
        $app->close();
        return true;
        return true;
    }
    /**
    * This displays our forms
    * 
    * @return bool
    */
    public function addhours()
    {
        // Get the application
        $app = Factory::getApplication();
        $params = ComponentHelper::getParams('com_timeclock');
        // Get the document object.
        $document = Factory::getDocument();
        $viewName = "timesheet";
        $layoutName = $app->input->getWord('layout', 'addhours');
        if (($layoutName !== "addhours") && ($layoutName !== "modal")) {
            $layoutName = "addhours";
        }
        $model = new AddhoursModel();
        $this->checkMe($model);
        $view = $this->getView("Timesheet", 'html');
        $view->setLayout($layoutName);
        $view->setModel($model, true);
        // Render our view.
        $view->display();
        return true;
    }
    /**
    * This function saves our stuff and returns a json response
    * 
    * @return null
    */
    protected function apply()
    {
        // Get the application
        $app   = Factory::getApplication();
        $app->getInput() or die("Invalid Token");
        $model = new AddhoursModel();
        $this->checkMe($model);

        if ($index = $model->store()) {
            $json = new JsonResponse(
                get_object_vars($index), 
                Text::_("COM_TIMECLOCK_HOURS_SAVED"),
                false,  // Error
                false    // Ignore Message Queue
            );
        } else {
            $json = new JsonResponse(
                array(), 
                Text::_("COM_TIMECLOCK_HOURS_FAILED"),
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

    protected function checkMe($model)
    {
        if (!$model->getUser()->me) {
            Factory::getApplication()->redirect('index.php',Text::_('JGLOBAL_AUTH_ACCESS_DENIED'));
        }

    }
    /**
    * This is the main function that executes everything.
    *
    * @return bool
    */
    public function authorize()
    {
        $user = Factory::getUser();
        if ($user->get('guest')) {
            return false;
        }
        $active = TimeclockHelper::getUserParam("active", $user->id);
        if ($active) {
            $app = Factory::getApplication();
            if (is_null($app->input->get("id", null, "int"))) {
                return true;
            }
            $reports = TimeclockHelper::getUserParam("reports", $user->id);
            return (bool)$reports;
        }
        return false;
    }
}