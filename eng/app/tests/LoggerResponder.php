<?php
/**
 * AJAX responder for the Logger class. Used to log
 * actions from Javascript.
 * FRI - December 2012 - deactivated - seems to block/slow down response!!!
 *  */
/*
include_once 'Logger.php';

// Load the session used by posh
session_name('myhomepage');
session_start();

$action = $_REQUEST['action'];

switch ($action) {
	case -17:
		Logger::emptyLogFile();
		break;
	case -32:
		Logger::backupWithTimestampAndEmpty();
		break;
	default:
		if (Logger::LOGGER_ACTIVATED) {
			$info = array();
			
			foreach ($_REQUEST as $key => $value) {
				if (in_array($key, array('id', 'name', 'tab', 'msg', 'query', 'from'))) {
					$info[$key] = $value;
				}
			}
			
			Logger::logAction($action, $info);
		}
		break;
}
*/
?>