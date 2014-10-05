<?php
require __DIR__."/joomla/bootstrap.php";

define('SRC_PATH',realpath(dirname(__DIR__)));
define('TEST_PATH',realpath(__DIR__));

require_once __DIR__."/core/TestCaseDatabase.php";
require_once __DIR__."/core/TestCase.php";

// load table paths
JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_timeclock/tables');
JLoader::register('TimeclockHelpersTimeclock', JPATH_ADMINISTRATOR.'/components/com_timeclock/helpers/timeclock.php');
JLoader::register('TimeclockHelpersDate', JPATH_SITE.'/components/com_timeclock/helpers/date.php');
