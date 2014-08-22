<?php
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * <pre>
 * com_ComTimeclock is a Joomla! 1.6 component
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
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
/** Check to make sure we are under Joomla */
defined('_JEXEC') or die('Restricted access');

/** Import the views */
jimport('joomla.application.component.view');

/**
 * HTML View class for the ComTimeclockWorld Component
 *
 * @category   UI
 * @package    ComTimeclock
 * @subpackage Com_Timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2014 Hunt Utilities Group, LLC
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       https://dev.hugllc.com/index.php/Project:ComTimeclock
 */
class TimeclockAdminViewTools extends JViewLegacy
{
    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return none
     */
    public function display($tpl = null)
    {
        JToolBarHelper::preferences('com_timeclock');
        $layout = $this->getLayout();
        if (method_exists($this, $layout)) {
            $this->$layout($tpl);
        } else {
            $this->defaultLayout($tpl);
        }
    }

    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return none
     */
    public function defaultLayout($tpl = null)
    {
        parent::display($tpl);
    }

    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return none
     */
    public function dbcheck($tpl = null)
    {
        TimeclockHelper::title(JText::_("COM_TIMECLOCK_CHECK_DATABASE"));
        $this->noResults = JText::_("COM_TIMECLOCK_NO_TESTS");
        $this->pageheader = JText::_("COM_TIMECLOCK_CHECKING_DATABASE");
        $model = $this->getModel();
        $this->results = $model->dbCheck();
        $this->setLayout("check");
        parent::display($tpl);
    }
    /**
     * The display function
     *
     * @param string $tpl The template to use
     *
     * @return none
     */
    public function convertprefs($tpl = null)
    {
        $model = $this->getModel();
        $this->results = $model->convertPrefs();
        $this->setLayout("check");
        parent::display($tpl);
    }

}

?>