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
 * @version    GIT: $Id: 01b4d7b19d0e3bd4323909fb925c4af842973a39 $
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
 * HTML view class for holidays
 *
 * @category   Timeclock
 * @package    Timeclock
 * @subpackage com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2023 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockViewsHolidayHtml extends HtmlView
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
            $this->_holidayListView = new FileLayout('entry', __DIR__.'/layouts');
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
            Text::_("COM_TIMECLOCK_TIMECLOCK_TIMESHEETS"), "clock"
        );
        if ($actions->get('core.admin'))
        {
            JToolbarHelper::preferences('com_timeclock');
        }
        if ($actions->get('core.create')) {
            JToolbarHelper::addNew('holiday.add');
        }

        if (($actions->get('core.edit')) || ($actions->get('core.edit.own')))
        {
            JToolbarHelper::editList('holiday.edit');
        }
        if ($actions->get('core.edit.state'))
        {
            JToolbarHelper::checkin('holiday.checkin');
        }
        JHtmlSidebar::setAction('index.php?option=com_timeclock');

        $options = TimeclockHelpersView::getYearOptions();
        JHtmlSidebar::addFilter(
            Text::_('COM_TIMECLOCK_SELECT_YEAR'),
            'filter_year',
            HTMLHelper::_('select.options', $options, 'value', 'text', $this->state->get('filter.year'), true)
        );
    }
    /**
    * Adds the toolbar for this view.
    *
    * @return unknown
    */
    protected function editToolbar()
    {
        $add = empty($this->data->timesheet_id);
        $title = ($add) ? Text::_("COM_TIMECLOCK_ADD") : Text::_("COM_TIMECLOCK_EDIT");

        JToolbarHelper::title(
            Text::sprintf("COM_TIMECLOCK_HOLIDAY_EDIT_TITLE", $title), "clock"
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
            'worked' => Text::_('COM_TIMECLOCK_WORKED'),
            'project_name' => Text::_('COM_TIMECLOCK_PROJECT'),
            'name' => Text::_('COM_TIMECLOCK_NAME'),
            'author' => Text::_('JAUTHOR'),
            't.timesheet_id' => Text::_('JGRID_HEADING_ID'),
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
                'holiday', 
                JPATH_COMPONENT_ADMINISTRATOR."/forms/holiday.xml"
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