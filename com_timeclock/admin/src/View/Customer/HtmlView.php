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
 * @version    GIT: $Id: 2ca951f3d4bf855d0f6f272fc59dcb457433906e $
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
namespace HUGLLC\Component\Timeclock\Administrator\View\Customer;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

\defined( '_JEXEC' ) or die();


/**
 * HTML view class for customers
 *
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2023 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class HtmlView extends BaseHtmlView
{
    /** This is our item */
    protected $item;
    /** This is the state */
    protected $state;
    /** This is the form we might have */
    protected $form;
    /**
    * Renders this view
    *
    * @return unknown
    */
    function display($tpl = null)
    {
        $this->addTemplatePath(__DIR__ . '/tmpl', 'normal');
        $model = $this->getModel();

        $this->params = ComponentHelper::getParams('com_timeclock');
        $this->state  = $model->getState();
        $this->item = $model->getItem();
        $this->form = $model->getForm();
        $this->addToolbar();

        //display
        return parent::display();
    } 
    /**
    * Adds the toolbar for this view.
    *
    * @return unknown
    */
    protected function addToolbar()
    {
        Factory::getApplication()->getInput()->set('hidemainmenu', true);
        $add = empty($this->item->customer_id);
        $title = ($add) ? Text::_("COM_TIMECLOCK_ADD") : Text::_("COM_TIMECLOCK_EDIT");

        $toolbar    = Toolbar::getInstance();

        ToolbarHelper::title(
            Text::sprintf("COM_TIMECLOCK_CUSTOMER_EDIT_TITLE", $title), "clock"
        );
        $toolbar->apply('customer.apply');
        $toolbar->save('customer.save');
        $toolbar->cancel('customer.cancel', 'JTOOLBAR_CANCEL');
    }
    /**
     * Returns an Form object
     *
     * @return  object Form object for this form
     *
     * @since   3.0
     */
    public function getForm()
    {
        if (!is_object($this->form)) {
            $this->form = Form::getInstance(
                'customer', 
                JPATH_COMPONENT_ADMINISTRATOR."/forms/customer.xml"
            );
        }
        return $this->form;
    }
    
}