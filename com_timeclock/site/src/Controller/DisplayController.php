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
namespace HUGLLC\Component\Timeclock\Site\Controller;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use HUGLLC\Component\Timeclock\Administrator\Helper\TimeclockHelper;

\defined( '_JEXEC' ) or die;

/**
 * 
 *
 */
class DisplayController extends BaseController
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
     * Method to display a view.
     *
     * @param   boolean  $cachable   If true, the view output will be cached
     * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link \JFilterInput::clean()}.
     *
     * @return  BaseController|boolean  This object to support chaining.
     *
     * @since   1.5
     */
    public function display($cachable = false, $urlparams = [])
    {
        $this->checkAuth();
        $view = $this->input->get('view', NULL);
        $controller = $this->input->get('controller', NULL);
        if (is_null($view) && is_null($controller)) {
            $this->setRedirect(Route::_('index.php?option=com_timeclock&view=timesheet', false));
            return false;
        }
        return parent::display($cachable, $urlparams);
    }
    /**
    * This is the main function that executes everything.
    *
    * @return null
    */
    public function checkAuth($type = NULL)
    {
        if ($type) {
            $auth = (bool)TimeclockHelper::getActions()->get($type);
        } else {
            $auth = $this->authorize();
        } 
        if (!$auth) {
            throw new \Exception(Text::_('JGLOBAL_AUTH_ACCESS_DENIED'));
        }
        return true;
    }
    /**
    * This is the main function that executes everything.
    *
    * @return bool
    */
    public function authorize()
    {
        $actions = TimeclockHelper::getActions();
        if ($actions->get('timeclock')) {
            $view = $this->input->get('view', NULL);
            $act = "timeclock.".$view;
            $ret = $actions->get($act);
            if (is_null($ret)) {
                $ret = $actions->get('timeclock.reports');
            }
            return $ret;
        }
        return false;
    }

}

?>