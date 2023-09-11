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
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

 
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
class TimeclockViewsCustomerHtml extends HtmlView
{
    /** Whether we are adding or editing */
    protected $add = false;
    /** This is the form we might have */
    protected $form = null;
    /**
    * Renders this view
    *
    * @return unknown
    */
    function display($tpl = null)
    {
        $this->addTemplatePath(__DIR__ . '/tmpl', 'normal');
        $layout = $this->getLayout();
        if ($layout == "add") {
            $this->add = true;
            $layout = "edit";
            $this->setLayout($layout);
        }
        $this->model = $this->getModel();

        $this->params = ComponentHelper::getParams('com_timeclock');
        $this->state  = $this->model->getState();
        if ($layout == 'edit') {
            if ($this->add) {
                $this->data = $this->model->getNew();
            } else {
                $this->data = $this->model->getItem();
            }
            $this->getForm();
            $this->editToolbar();
        } else {
            //retrieve task list from model
            $this->data = $this->model->listItems();
            $this->_customerListView = new FileLayout('entry', __DIR__.'/layouts');
            $this->sortFields = $this->model->checkSortFields($this->getSortFields());
            $this->listToolbar();
            $this->sidebar = JHtmlSidebar::render();

        }
        $this->pagination = $this->model->getPagination();
        //display
        return parent::display($tpl);
    } 
    /**
    * Adds the toolbar for this view.
    *
    * @return unknown
    */
    protected function listToolbar()
    {
        $actions = TimeclockHelpersTimeclock::getActions();
        // Get the toolbar object instance
        $bar = JToolBar::getInstance('toolbar');
        JToolbarHelper::title(
            Text::_("COM_TIMECLOCK_TIMECLOCK_CUSTOMERS"), "clock"
        );
        if ($actions->get('core.admin'))
        {
            JToolbarHelper::preferences('com_timeclock');
        }
        if ($actions->get('core.create')) {
            JToolbarHelper::addNew('customer.add');
        }

        if (($actions->get('core.edit')) || ($actions->get('core.edit.own')))
        {
            JToolbarHelper::editList('customer.edit');
        }
        if ($actions->get('core.edit.state'))
        {
            JToolbarHelper::publish('customer.publish', 'JTOOLBAR_PUBLISH', true);
            JToolbarHelper::unpublish('customer.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            JToolbarHelper::checkin('customer.checkin');
        }
        JHtmlSidebar::setAction('index.php?option=com_timeclock&controller=customer');

        JHtmlSidebar::addFilter(
            Text::_('JOPTION_SELECT_PUBLISHED'),
            'filter_published',
            HTMLHelper::_('select.options', array(0 => Text::_("JUNPUBLISHED"), 1 => Text::_("JPUBLISHED")), 'value', 'text', $this->state->get('filter.published'), true)
        );
        $options = TimeclockHelpersView::getUsersOptions();
        JHtmlSidebar::addFilter(
            Text::_('COM_TIMECLOCK_SELECT_CONTACT'),
            'filter_user_id',
            HTMLHelper::_('select.options', $options, 'value', 'text', $this->state->get('filter.user_id'), true)
        );
    }
    /**
    * Adds the toolbar for this view.
    *
    * @return unknown
    */
    protected function editToolbar()
    {
        // Factory::getApplication()->getInput()->set('hidemainmenu', true);
        $add = empty($this->data->customer_id);
        $title = ($add) ? Text::_("COM_TIMECLOCK_ADD") : Text::_("COM_TIMECLOCK_EDIT");

        JToolbarHelper::title(
            Text::sprintf("COM_TIMECLOCK_CUSTOMER_EDIT_TITLE", $title), "clock"
        );
        JToolBarHelper::apply("apply");
        JToolBarHelper::save("save");
        JToolBarHelper::cancel("cancel");
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
            'c.company' => Text::_('COM_TIMECLOCK_COMPANY'),
            'c.name' => Text::_('COM_TIMECLOCK_NAME'),
            'contact' => Text::_('COM_TIMECLOCK_CONTACT_NAME'),
            'c.customer_id' => Text::_('JGRID_HEADING_ID'),
            'c.published' => Text::_('JSTATUS'),
        );
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
    /**
     * Returns an Form object
     *
     * @return  object Form object for this form
     *
     * @since   3.0
     */
    public function get($property, $default = null)
    {
        var_dump(func_get_args());
    }
    
}