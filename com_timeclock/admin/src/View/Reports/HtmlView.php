<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 *
 * @copyright   (C) 2008 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace HUGLLC\Component\Timeclock\Administrator\View\Reports;

use HUGLLC\Component\Timeclock\Administrator\Helper\TimeclockHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\HTML\HTMLHelper;


\defined('_JEXEC') or die;

/**
 * View to edit a contact.
 *
 * @since  1.6
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The search tools form
     *
     * @var    Form
     * @since  1.6
     */
    public $filterForm;

    /**
     * The active search filters
     *
     * @var    array
     * @since  1.6
     */
    public $activeFilters = [];

    /**
     * Category data
     *
     * @var    array
     */
    protected $categories = [];

    /**
     * An array of items
     *
     * @var    array
     */
    protected $items = [];

    /**
     * The pagination object
     *
     * @var    Pagination
     */
    protected $pagination;

    /**
     * The model state
     *
     * @var    Registry
     */
    protected $state;

    /**
     * Is this view an Empty State
     *
     * @var  boolean
     */
    private $isEmptyState = false;

    /**
     * Display the view.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */
    public function display($tpl = null)
    {
        $this->addTemplatePath(__DIR__ . '/tmpl', 'normal');
        $model               = $this->getModel("reports");
        $this->items         = $model->getItems();
        $this->pagination    = $model->getPagination();
        $this->state         = $model->getState();
        $this->filterForm    = $model->getFilterForm();
        $this->activeFilters = $model->getActiveFilters();
        $this->pagination    = $model->getPagination();

        if (!\count($this->items) && $this->isEmptyState = $this->get('IsEmptyState')) {
            $this->setLayout('emptystate');
        }

        // Check for errors.
        if (\count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->_row = new FileLayout('row', __DIR__.'/tmpl');

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        $actions = TimeclockHelper::getActions();
        // Get the toolbar object instance
        $bar = ToolBar::getInstance();
        ToolbarHelper::title(
            Text::_("COM_TIMECLOCK_TIMECLOCK_REPORTS"), "clock"
        );
        if ($actions->get('core.create')) {
            ToolbarHelper::addNew('report.add');
        }
        if ($actions->get('core.edit.state'))
        {
            ToolbarHelper::publish('reports.publish', 'JTOOLBAR_PUBLISH', true);
            ToolbarHelper::unpublish('reports.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            ToolbarHelper::checkin('reports.checkin');
        }
        if ($actions->get('core.admin'))
        {
            ToolbarHelper::preferences('com_timeclock');
        }
    }
}
