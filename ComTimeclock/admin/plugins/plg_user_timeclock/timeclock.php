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

 defined('_JEXEC') or die('Restricted access');
jimport('joomla.utilities.date');
jimport('joomla.form.form');
JForm::addFieldPath(JPATH_COMPONENT.'/../com_timeclock/models/fields');
if (!class_exists("TimeclockHelper")) {
    include_once JPATH_ROOT.'/administrator/components/com_timeclock/helpers/timeclock.php';
}
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

            $results = $this->getAllParams($userId, "admin");
            if ($results === false) {
                return false;
            }
            $data->timeclock = self::stripKeys($results);

            self::decodeArrays($data->timeclock);
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
    /**
    * This happens when we are preparing the form
    *
    * @param int    $userId The id of the user to get
    * @param string $type   The type of data
    *
    * @return boolean
    */
    protected function getAllParams($userId, $type)
    {
        // Load the timeclock data from the database.
        $db = JFactory::getDbo();
        $db->setQuery(
            'SELECT profile_key, profile_value FROM #__user_profiles' .
            ' WHERE user_id = '.(int) $userId." AND profile_key LIKE 'timeclock.$type%'" .
            ' ORDER BY ordering'
        );
        $results = $db->loadRowList();
       // Check for a database error.
        if ($db->getErrorNum())
        {
            $this->_subject->setError($db->getErrorMsg());
            return false;
        }
        return $results;

    }
    /**
    * This rebuilds arrays
    *
    * @param int    &$data    The user id
    *
    * @return boolean
    */
    static protected function decodeArrays(&$data)
    {
        foreach (array_keys($data) as $key) {
            if (substr($data[$key], 0, 5) === "array") {
                self::_decodeArray($key, $data);
            }
        }
    }

    /**
    * This rebuilds the SQL return array into a normal associative array
    *
    * @param array  $data  The SQL return
    * @param string $strip The string to strip off the keys
    *
    * @return boolean
    */
    static protected function stripKeys($data, $strip = 'timeclock.admin.')
    {
        $ret = array();
        foreach ($data as $key => $v)
        {
            if (is_array($v)) {
                $k = str_replace($strip, '', $v[0]);
                $ret[$k] = $v[1];
            } else {
                $k = str_replace($strip, '', $key);
                $ret[$k] = $v;
            }
        }
        return $ret;
    }

    /**
    * This rebuilds arrays
    *
    * @param int &$data The user id
    *
    * @return null
    */
    static protected function _decodeArray($name, &$data)
    {
        $data[$name] = array();

        foreach ($data as $key => $value) {
            if (substr($key, 0, strlen($name)) === $name) {
                $k = explode("_", $key);
                $levels = count($k);
                if ($levels == 1) {
                    continue;
                } else if ($levels == 2) {
                    $data[$name][$k[1]] = $value;
                } else if ($levels == 3) {
                    $data[$name][$k[1]][$k[2]] = $value;
                } else if ($levels == 4) {
                    $data[$name][$k[1]][$k[2]][$k[3]] = $value;
                } else if ($levels == 5) {
                    $data[$name][$k[1]][$k[2]][$k[3]][$k[4]] = $value;
                }
                unset($data[$key]);
            }
        }
    }
    /**
    * This rebuilds arrays
    *
    * @param int &$data The user id
    *
    * @return boolean
    */
    protected function encodeArrays(&$data)
    {
        foreach (array_keys($data) as $key) {
            if (is_array($data[$key])) {
                $count = count($data[$key]);
                $this->_encodeArray($key, $data, $data[$key]);
                $data[$key] = "array($count)";
            }
        }
    }

    /**
    * This rebuilds arrays
    *
    * @param int   $id    The id of the user to remove
    * @param array &$data The data to use
    *
    * @return boolean
    */
    protected function removeProjects($id, &$data)
    {
        $model = TimeclockHelper::getModel('projects');
        foreach ((array)$data['removeProject'] as $proj) {
            $model->removeOneUser((int)$proj, (int)$id);
        }
        unset($data['removeProject']);
    }
    /**
    * This rebuilds arrays
    *
    * @param int   $id    The id of the user to remove
    * @param array &$data The data to use
    *
    * @return boolean
    */
    protected function addProjects($id, &$data)
    {
        $model = TimeclockHelper::getModel('projects');
        foreach ((array)$data['addProject'] as $proj) {
            $model->addOneUser((int)$proj, (int)$id);
        }
        unset($data['addProject']);
    }
    /**
    * This rebuilds arrays
    *
    * @param int   $id    The id of the user to remove
    * @param array &$data The data to use
    *
    * @return boolean
    */
    protected function addUserProjects($id, &$data)
    {
        if (!empty($data['addProjFromUser'])) {
            $model = TimeclockHelper::getModel('projects');
            $projects = $model->getUserProjectIds($data['addProjFromUser']);
            foreach ((array)$projects as $proj) {
                $model->addOneUser((int)$proj, (int)$id);
            }

        }
        unset($data['addProjFromUser']);
    }

    /**
    * This rebuilds arrays
    *
    * @param int &$data The user id
    *
    * @return null
    */
    protected function _encodeArray($name, &$array, &$data)
    {
        if (strlen($name) >= 100) {
            return;
        }
        foreach (array_keys((array)$data) as $key) {
            $nName = $name."_".$key;
            if (is_array($data[$key])) {
                $this->_encodeArray($nName, $array, $data[$key]);
            } else {
                $array[$nName] = $data[$key];
            }
        }
    }

    /**
    * This prepares the form
    *
    * @param string $param  The profile parameter to get
    * @param int    $userId The userId to use.  Current user if left null
    * @param mixed  $date   String or integer date. The date to get the param for
    *
    * @return boolean
    */
    static public function getParamValue($param, $userId=null, $date=null)
    {
        if (empty($userId)) {
            $userId = JFactory::getUser()->id;
        }
        // Load the timeclock data from the database.
        $db = JFactory::getDbo();
        $db->setQuery(
            'SELECT profile_value, profile_key FROM #__user_profiles' .
            ' WHERE user_id = '.(int) $userId." AND (".
            "profile_key = 'timeclock.admin.$param'".
            "OR profile_key = 'timeclock.user.$param'".
            "OR profile_key = 'timeclock.set.$param')"
        );
        $results = $db->loadRowList();
        $res = isset($results[0][0]) ? $results[0][0] : null;
        if (substr($res, 0, 6) === "array(") {
            $key = str_replace("timeclock.", "", $results[0][1]);
            $value = self::stripKeys(self::getAllParams($userId, $key));
            self::decodeArrays($value);
            $res = $value;

        }
        if (is_null($date) || is_array($res)) {
            return $res;
        }
        $history = self::stripKeys(self::getAllParams($userId, "admin.history"));
        self::decodeArrays($history);
        $history = (array)$history['history'][$param];
        ksort($history);
        $date = new JDate($date);
        foreach ((array)$history as $change => $value) {
            $chDate = new JDate($change);
            if ($chDate->toUnix() > $date->toUnix()) {
                return $value;
            }
        }
        return $res;

    }
    /**
    * This prepares the form
    *
    * @param string $param  The profile parameter to get
    * @param mixed  $value  The value to set the param to
    * @param int    $userId The userId to use.  Current user if left null
    * @param string $prefix The prefix to use.  "set." is the default
    *
    * @return boolean
    */
    static public function setParamValue(
        $param, $value, $userId=null, $prefix="set."
    ) {
        if (empty($userId)) {
            $userId = JFactory::getUser()->id;
        }
        // Load the timeclock data from the database.
        $db = JFactory::getDbo();
        try
        {
            $db = JFactory::getDbo();
            $db->setQuery(
                'DELETE FROM #__user_profiles WHERE user_id = '.$userId .
                " AND profile_key = 'timeclock.$prefix$param'"
            );

            if (!$db->query()) {
                throw new Exception($db->getErrorMsg());
            }

            $db->setQuery(
                'INSERT INTO #__user_profiles VALUES '.
                '('.(int)$userId.', '.$db->quote('timeclock.'.$prefix.$param).', '.$db->quote($value).', -1)'
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
        return true;
    }
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

                //Sanitize the date
                foreach (array("startDate", "endDate") as $date) {
                    if (!empty($data['timeclock'][$date])) {
                        $jdate = new JDate($data['timeclock'][$date]);
                        $data['timeclock'][$date] = $jdate->format('Y-m-d');
                    }
                }
                // Do the stuff not related to this table
                $this->addProjects($userId, $data['timeclock']);
                $this->addUserProjects($userId, $data['timeclock']);
                $this->removeProjects($userId, $data['timeclock']);
                // get the history before it is encoded
                $history = $data['timeclock']['history'];
                unset($data['timeclock']['history']);
                // Encode the arrays
                $this->encodeArrays($data['timeclock']);
                // Now change the history
                $this->_setHistory($userId, $data['timeclock'], $history);
                // Now delete the old stuff
                $db = JFactory::getDbo();
                $db->setQuery(
                    'DELETE FROM #__user_profiles WHERE user_id = '.$userId .
                    " AND profile_key LIKE 'timeclock.admin.%'"
                );

                if (!$db->query()) {
                    throw new Exception($db->getErrorMsg());
                }

                // Now save the new stuff
                $tuples = array();
                $order    = 1;

                foreach ($data['timeclock'] as $k => $v)
                {
                    $tuples[] = '('.$userId.', '.$db->quote('timeclock.admin.'.$k).', '.$db->quote($v).', '.$order++.')';
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
    /**
    * Loads incoming data into the prefs array
    *
    * @param array $userId  The id of the user
    * @param array &$data   The new data
    * @param array $history Any history that was sent to us in the post
    *
    * @return null
    */
    private function _setHistory($userId, &$data, $history)
    {
        // Load the new data
        $id = JFactory::getUser()->get("name");
        $date = new JDate();
        $old = $this->getAllParams($userId, "admin");
        $changeDates = array();
        foreach((array)$history['effectiveDateSet'] as $d => $v) {
            if ((bool)$v && !empty($history['effectiveDate'][$d])) {
                $changeDates[$d] = $history['effectiveDate'][$d];
                $data["history_effectiveDateChange_".$date->toSql()] = $d;
                $data["history_timestamps_".$date->toSql()] = $id;
            }
        }
        foreach ($old as $row) {
            $key = substr($row[0], 16);
            $vals = explode("_", $key);
            if ($vals[0] === "history") {
                if (isset($changeDates[$vals[2]])) {
                    $vals[2] = $changeDates[$vals[2]];
                    $key = implode("_", $vals);
                }
                // Propigate the old history
                $data[$key] = $row[1];
                continue;
            } else if (substr($data[$vals[0]], 0, 5) == "array") {
                $pkey = str_replace("_", "*", $key);
            } else {
                $pkey = $key;
            }
            if (($data[$key] != $row[1]) && (substr($data[$key], 0, 5) !== "array")) {
                $data["history_".$pkey."_".$date->toSql()] = $row[1];
                $data["history_timestamps_".$date->toSql()] = $id;
                $data["history"] = "array()";
            }
        }
    }
}
