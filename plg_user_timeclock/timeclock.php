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
 * Copyright (C) 2014 Hunt Utilities Group, LLC
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
 * @copyright  2014 Hunt Utilities Group, LLC
 * @copyright  2009 Scott Price
 * @copyright  2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id: 8dbcb324639e0accf8ba00987a32318c13d5984a $
 * @link       https://dev.hugllc.com/index.php/Project:Comtimeclock
 *
 */
 defined('_JEXEC') or die('Restricted access');
jimport('joomla.utilities.date');
jimport('joomla.form.form');
require_once JPATH_ADMINISTRATOR.'/components/com_timeclock/helpers/timeclock.php';
JForm::addFieldPath(JPATH_ADMINISTRATOR.'/components/com_timeclock/models/fields');
/**
* This is a plugin to display timeclock user information in the user screen
*
* @category   UI
* @package    Comtimeclock
* @subpackage Com_timeclock
* @author     Scott Price <prices@hugllc.com>
* @copyright  2014 Hunt Utilities Group, LLC
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
    public function onContentPrepareData($context, $data)
    {
        // Check we are manipulating a valid form.
        if (!in_array($context, array('com_users.timeclock','com_users.user', 'com_users.registration', 'com_admin.timeclock'))) {
            return true;
        }

        if (is_object($data))
        {
            $userId = isset($data->id) ? $data->id : 0;

            $results = $this->getAllParams($userId);
            if ($results === false) {
                return false;
            }
            $data->timeclock = self::stripKeys($results);

            self::decodeArrays($data->timeclock);
        }

        return true;
    }
    /**
    * This happens when we are preparing the form
    *
    * @param int    $userId The id of the user to get
    *
    * @return boolean
    */
    static public function getParams($userId)
    {
        $ret = self::getAllParams($userId);
        $ret = self::stripKeys($ret);
        self::decodeArrays($ret);
        return $ret;
    }
    /**
    * This happens when we are preparing the form
    *
    * @param int    $userId The id of the user to get
    *
    * @return boolean
    */
    static protected function getAllParams($userId)
    {
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
            if (isset($data[$key]) && (substr($data[$key], 0, 5) === "array")) {
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
    static protected function stripKeys($data, $strip = 'timeclock.')
    {
        $ret = array();
        foreach ($data as $key => $v)
        {
            if (is_array($v)) {
                $k = str_replace($strip, '', $v[0]);
                $k = str_replace("admin.", "", $k);
                $k = str_replace("site.", "", $k);
                $k = str_replace("set.", "", $k);
                $ret[$k] = $v[1];
            } else {
                $k = str_replace($strip, '', $key);
                $k = str_replace("admin.", "", $k);
                $k = str_replace("site.", "", $k);
                $k = str_replace("set.", "", $k);
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
        $model = TimeclockHelpersTimeclock::getModel('project');
        if (isset($data['addProject']) && isset($data['addProject']['out'])) {
            foreach ((array)$data['addProject']['out'] as $proj) {
                $model->removeUsers((int)$id, (int)$proj);
            }
        }
        unset($data['addProject']['out']);
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
        $model = TimeclockHelpersTimeclock::getModel('project');
        if (isset($data['addProject']) && isset($data['addProject']['in'])) {
            foreach ((array)$data['addProject']['in'] as $proj) {
                $model->addUsers((int)$id, (int)$proj);
            }
        }
        unset($data['addProject']['in']);
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
        if (isset($data['addProjFromUser']) && !empty($data['addProjFromUser'])) {
            $model = TimeclockHelpersTimeclock::getModel('project');
            $projects = $model->listUserProjects($data['addProjFromUser']);
            foreach (array_keys((array)$projects) as $proj) {
                $model->addUsers((int)$id, (int)$proj);
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
            "profile_key = 'timeclock.$param' ".
            "OR profile_key = 'timeclock.admin.$param' ".
            "OR profile_key = 'timeclock.user.$param' ".
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
        $history = self::stripKeys(self::getAllParams($userId, "history"));
        self::decodeArrays($history);
        $history = isset($history['history']) ? (array)$history['history'][$param] : array();
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
    *
    * @return boolean
    */
    static public function setParamValue(
        $param, $value, $userId=null
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
                " AND profile_key = 'timeclock.$param'"
            );

            if (!$db->query()) {
                throw new Exception($db->getErrorMsg());
            }

            $db->setQuery(
                'INSERT INTO #__user_profiles VALUES '.
                '('.(int)$userId.', '.$db->quote('timeclock.'.$param).', '.$db->quote($value).', -1)'
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
    public function onContentPrepareForm($form, $data)
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

        return true;
    }

    public function onUserAfterSave($data, $isNew, $result, $error)
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
                if (isset($data['timeclock']['history'])) {
                    $history = $data['timeclock']['history'];
                    unset($data['timeclock']['history']);
                } else {
                    $history = array();
                }
                // Encode the arrays
                $this->encodeArrays($data['timeclock']);
                // Now change the history
                $this->_setHistory($userId, $data['timeclock'], $history);
                // Now delete the old stuff
                $db = JFactory::getDbo();
                $db->setQuery(
                    'DELETE FROM #__user_profiles WHERE user_id = '.$userId .
                    " AND profile_key LIKE 'timeclock.%'"
                );

                if (!$db->query()) {
                    throw new Exception($db->getErrorMsg());
                }

                // Now save the new stuff
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
    public function onUserAfterDelete($user, $success, $msg)
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
        $old = $this->getAllParams($userId);
        $changeDates = array();
        if (!isset($history['effectiveDateSet'])) {
            $history['effectiveDateSet'] = array();
        }
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
                if (isset($vals[2]) && isset($changeDates[$vals[2]])) {
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
