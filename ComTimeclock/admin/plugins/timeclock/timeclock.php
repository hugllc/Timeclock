<?php
/**
/**
 * This component is the user interface for the endpoints
 *
 * PHP Version 5
 *
 * This module was copied and modified from mod_stats.  That is the reason for
 * the OSM Copyright.
 *
 * <pre>
 * mod_timeclockinfo is a Joomla! 1.5 module
 * Copyright (C) 2008-2009, 2011 Hunt Utilities Group, LLC
 * Copyright (C) 2009 Scott Price
 * Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
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
 *
 * @category   UI
 * @package    Comtimeclock
 * @subpackage Com_timeclock
 * @author     Scott Price <prices@hugllc.com>
 * @copyright  2008-2009, 2011 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @copyright  2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id$
 * @link       https://dev.hugllc.com/index.php/Project:Comtimeclock
 *
 */

defined('JPATH_BASE') or die;
jimport('joomla.utilities.date');

/**
* This is a plugin to display timeclock user information in the user screen
*
* @category   UI
* @package    Comtimeclock
* @subpackage Com_timeclock
* @author     Scott Price <prices@hugllc.com>
* @copyright  2008-2009, 2011 Hunt Utilities Group, LLC
* @copyright  2009 Scott Price
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @link       https://dev.hugllc.com/index.php/Project:Comtimeclock
*/
class plgUserTimeclock extends JPlugin
{
    /**
    * This happens when we are preparing the form
    *
    * @param string $context The context for the data
    * @param int    $data    The user id
    *
    * @return boolean
    */
    function onContentPrepareData($context, $data)
    {
        // Check we are manipulating a valid form.
        if (!in_array($context, array('com_users.timeclock','com_users.user', 'com_users.registration', 'com_admin.timeclock'))) {
            return true;
        }

        if (is_object($data))
        {
            $userId = isset($data->id) ? $data->id : 0;

            // Load the timeclock data from the database.
            $db = JFactory::getDbo();
            $db->setQuery(
                'SELECT profile_key, profile_value FROM #__user_profiles' .
                ' WHERE user_id = '.(int) $userId." AND profile_key LIKE 'timeclock.%'" .
                ' ORDER BY ordering'
            );
            $results = $db->loadRowList();

            // Check for a database error.
            if ($db->getErrorNum())
            {
                $this->_subject->setError($db->getErrorMsg());
                return false;
            }

            // Merge the timeclock data.
            $data->timeclock = array();

            foreach ($results as $v)
            {
                $k = str_replace('timeclock.', '', $v[0]);
                $data->timeclock[$k] = $v[1];
            }
            /*
            if (!JHtml::isRegistered('users.url')) {
                JHtml::register('users.url', array(__CLASS__, 'url'));
            }
            if (!JHtml::isRegistered('users.calendar')) {
                JHtml::register('users.calendar', array(__CLASS__, 'calendar'));
            }
            if (!JHtml::isRegistered('users.tos')) {
                JHtml::register('users.tos', array(__CLASS__, 'tos'));
            }
            */
        }

        return true;
    }
/*
    public static function url($value)
    {
        if (empty($value))
        {
            return JHtml::_('users.value', $value);
        }
        else
        {
            $value = htmlspecialchars($value);
            if(substr ($value, 0, 4) == "http") {
                return '<a href="'.$value.'">'.$value.'</a>';
            }
            else {
                return '<a href="http://'.$value.'">'.$value.'</a>';
            }
        }
    }

    public static function calendar($value)
    {
        if (empty($value)) {
            return JHtml::_('users.value', $value);
        } else {
            return JHtml::_('date', $value);
        }
    }

    public static function tos($value)
    {
        if ($value) {
            return JText::_('JYES');
        }
        else {
            return JText::_('JNO');
        }
    }
*/

    /**
    * This prepares the form
    *
    * @param JForm $form The form to be altered.
    * @param array $data The associated data for the form.
    *
    * @return boolean
    */
    function onContentPrepareForm($form, $data)
    {
        // Load user_timeclock plugin language
        $lang = JFactory::getLanguage();
        $lang->load('plg_user_timeclock', JPATH_ADMINISTRATOR);

        if (!($form instanceof JForm))
        {
            $this->_subject->setError('JERROR_NOT_A_FORM');
            return false;
        }

        // Check we are manipulating a valid form.
        if (!in_array($form->getName(), array('com_admin.timeclock','com_users.user', 'com_users.registration','com_users.timeclock'))) {
            return true;
        }

        // Add the registration fields to the form.
        JForm::addFormPath(dirname(__FILE__).'/profiles');
        $form->loadFile('timeclock', false);
/*
        // Toggle whether the address1 field is required.
        if ($this->params->get('register-require_address1', 1) > 0) {
            $form->setFieldAttribute('address1', 'required', $this->params->get('register-require_address1') == 2, 'timeclock');
        }
        else {
            $form->removeField('address1', 'timeclock');
        }

        // Toggle whether the address2 field is required.
        if ($this->params->get('register-require_address2', 1) > 0) {
            $form->setFieldAttribute('address2', 'required', $this->params->get('register-require_address2') == 2, 'timeclock');
        }
        else {
            $form->removeField('address2', 'timeclock');
        }

        // Toggle whether the city field is required.
        if ($this->params->get('register-require_city', 1) > 0) {
            $form->setFieldAttribute('city', 'required', $this->params->get('register-require_city') == 2, 'timeclock');
        }
        else {
            $form->removeField('city', 'timeclock');
        }

        // Toggle whether the region field is required.
        if ($this->params->get('register-require_region', 1) > 0) {
            $form->setFieldAttribute('region', 'required', $this->params->get('register-require_region') == 2, 'timeclock');
        }
        else {
            $form->removeField('region', 'timeclock');
        }

        // Toggle whether the country field is required.
        if ($this->params->get('register-require_country', 1) > 0) {
            $form->setFieldAttribute('country', 'required', $this->params->get('register-require_country') == 2, 'timeclock');
        }
        else {
            $form->removeField('country', 'timeclock');
        }

        // Toggle whether the postal code field is required.
        if ($this->params->get('register-require_postal_code', 1) > 0) {
            $form->setFieldAttribute('postal_code', 'required', $this->params->get('register-require_postal_code') == 2, 'timeclock');
        }
        else {
            $form->removeField('postal_code', 'timeclock');
        }

        // Toggle whether the phone field is required.
        if ($this->params->get('register-require_phone', 1) > 0) {
            $form->setFieldAttribute('phone', 'required', $this->params->get('register-require_phone') == 2, 'timeclock');
        }
        else {
            $form->removeField('phone', 'timeclock');
        }

        // Toggle whether the website field is required.
        if ($this->params->get('register-require_website', 1) > 0) {
            $form->setFieldAttribute('website', 'required', $this->params->get('register-require_website') == 2, 'timeclock');
        }
        else {
            $form->removeField('website', 'timeclock');
        }

        // Toggle whether the favoritebook field is required.
        if ($this->params->get('register-require_favoritebook', 1) > 0) {
            $form->setFieldAttribute('favoritebook', 'required', $this->params->get('register-require_favoritebook') == 2, 'timeclock');
        }
        else {
            $form->removeField('favoritebook', 'timeclock');
        }

        // Toggle whether the aboutme field is required.
        if ($this->params->get('register-require_aboutme', 1) > 0) {
            $form->setFieldAttribute('aboutme', 'required', $this->params->get('register-require_aboutme') == 2, 'timeclock');
        }
        else {
            $form->removeField('aboutme', 'timeclock');
        }

        // Toggle whether the tos field is required.
        if ($this->params->get('register-require_tos', 1) > 0) {
            $form->setFieldAttribute('tos', 'required', $this->params->get('register-require_tos') == 2, 'timeclock');
        }
        else {
            $form->removeField('tos', 'timeclock');
        }

        // Toggle whether the dob field is required.
        if ($this->params->get('register-require_dob', 1) > 0) {
            $form->setFieldAttribute('dob', 'required', $this->params->get('register-require_dob') == 2, 'timeclock');
        }
        else {
            $form->removeField('dob', 'timeclock');
        }
*/
        return true;
    }

    function onUserAfterSave($data, $isNew, $result, $error)
    {
        $userId    = JArrayHelper::getValue($data, 'id', 0, 'int');

        if ($userId && $result && isset($data['timeclock']) && (count($data['timeclock'])))
        {
            try
            {
                /*
                //Sanitize the date
                if (!empty($data['timeclock']['dob'])) {
                    $date = new JDate($data['timeclock']['dob']);
                    $data['timeclock']['dob'] = $date->toFormat('%Y-%m-%d');
                }
*/
                $db = JFactory::getDbo();
                $db->setQuery(
                    'DELETE FROM #__user_profiles WHERE user_id = '.$userId .
                    " AND profile_key LIKE 'timeclock.%'"
                );

                if (!$db->query()) {
                    throw new Exception($db->getErrorMsg());
                }

                $tuples = array();
                $order    = 1;

                foreach ($data['timeclock'] as $k => $v)
                {
                    $tuples[] = '('.$userId.', '.$db->quote('timeclock.'.$k).', '.$db->quote($v).', '.$order++.')';
                }

                $db->setQuery('INSERT INTO #__user_profiles VALUES '.implode(', ', $tuples));

                if (!$db->query()) {
                    throw new Exception($db->getErrorMsg());
                }

            }
            catch (JException $e)
            {
                $this->_subject->setError($e->getMessage());
                return false;
            }
        }

        return true;
    }

    /**
     * Remove all user timeclock information for the given user ID
     *
     * Method is called after user data is deleted from the database
     *
     * @param    array        $user        Holds the user data
     * @param    boolean        $success    True if user was succesfully stored in the database
     * @param    string        $msg        Message
     */
    function onUserAfterDelete($user, $success, $msg)
    {
        if (!$success) {
            return false;
        }

        $userId    = JArrayHelper::getValue($user, 'id', 0, 'int');

        if ($userId)
        {
            try
            {
                $db = JFactory::getDbo();
                $db->setQuery(
                    'DELETE FROM #__user_profiles WHERE user_id = '.$userId .
                    " AND profile_key LIKE 'timeclock.%'"
                );

                if (!$db->query()) {
                    throw new Exception($db->getErrorMsg());
                }
            }
            catch (JException $e)
            {
                $this->_subject->setError($e->getMessage());
                return false;
            }
        }

        return true;
    }
}
