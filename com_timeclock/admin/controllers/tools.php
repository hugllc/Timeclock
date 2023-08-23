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
 * @version    GIT: $Id: 7e641fa474f49c089e5636e4aba78940f3bf6ea8 $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
 
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
class TimeclockControllersTools extends TimeclockControllersDefault
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
        $task = $task ? $task : $this->getTask();
        $fct  = "tools".ucfirst($task);
        if (method_exists($this, $fct)) {
            return $this->$fct();
        }
        return $this->display();
    }
    /**
    * This function performs everything for this controller.  It is the goto 
    * function.
    *
    * @access public
    * @return boolean
    */
    public function display($cachable = false, $urlparams = [])
    {
        // Get the application
        $app = Factory::getApplication();
        // Get the document object.
        $document = Factory::getDocument();
        $viewName = $app->input->getWord('view', 'tools');
        $viewFormat = $document->getType();
        $layoutName = $app->input->getWord('layout', 'default');
        $app->input->set('view', $viewName);
        // Register the layout paths for the view
        $paths = new SplPriorityQueue;
        $paths->insert(JPATH_COMPONENT . '/views/' . $viewName . '/tmpl', 'normal');
        $viewClass = 'TimeclockViews' . ucfirst($viewName) . ucfirst($viewFormat);
        $modelClass = 'TimeclockModelsTools';
        $view = new $viewClass(); // new $modelClass, $paths);
        $view->setLayout($layoutName);
        // Render our view.
        echo $view->display();
        return true;
    }
    /**
    * This function performs everything for this controller.  It is the goto 
    * function.
    *
    * @access public
    * @return boolean
    */
    protected function toolsDbcheck()
    {
        // Get the application
        $app = Factory::getApplication();
        // Get the document object.
        $document = Factory::getDocument();
        $viewName = 'tools';
        $viewFormat = $document->getType();
        $layoutName = 'check';
        $app->input->set('view', $viewName);
        // Register the layout paths for the view
        $paths = new SplPriorityQueue;
        $paths->insert(JPATH_COMPONENT . '/views/' . $viewName . '/tmpl', 'normal');
        $viewClass = 'TimeclockViews' . ucfirst($viewName) . ucfirst($viewFormat);
        $modelClass = 'TimeclockModelsTools';
        $model = new $modelClass;
        $view = new $viewClass();
        $view->setLayout($layoutName);
        $view->setModel($model, true);
        // Render our view.
        echo $view->display();
        return true;
    }
    /**
    * This function performs everything for this controller.  It is the goto 
    * function.
    *
    * @access public
    * @return boolean
    */
    protected function toolsSetup()
    {

        // Get the application
        $app = Factory::getApplication();
        // Get the document object.
        $document = Factory::getDocument();
        $viewName = 'tools';
        $viewFormat = $document->getType();
        $layoutName = 'setup';
        $app->input->set('view', $viewName);
        // Register the layout paths for the view
        $paths = new SplPriorityQueue;
        $paths->insert(JPATH_COMPONENT . '/views/' . $viewName . '/tmpl', 'normal');
        $viewClass = 'TimeclockViews' . ucfirst($viewName) . ucfirst($viewFormat);
        $modelClass = 'TimeclockModelsTools';
        $model = new $modelClass;
        $view = new $viewClass();
        $view->setLayout($layoutName);
        $view->setModel($model);
        $ret = $model->setup();
        if ($ret === false) {
            $app->enqueueMessage(
                "Install Failed", 'warning'
            );
        }

        // Render our view.
        echo $view->display();
        return true;
    }
}