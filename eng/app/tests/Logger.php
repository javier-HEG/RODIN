<?php
/**
 * Logger class, implemented so that user actions can be recorded
 * at least partially.
 */
class Logger {
	const LOGGER_ACTIVATED = true;
	const LOG_INTO_DB = true;

  const LOGIN_ACTION = 0;
	const LOGOUT_ACTION = 1;
	const ADD_WIDGET_ACTION = 2;
	const REMOVE_WIDGET_ACTION = 3;
	const OPEN_TAB_ACTION = 4;
	const REMOVE_TAB_ACTION = 5;
	const CREATE_TAB_ACTION = 6;
	const RENAME_TAB_ACTION = 7;
	const LAUNCH_METASEARCH_ACTION = 8;
	const LAUNCH_LOCALSEARCH_ACTION = 9;
	const LAUNCH_ONTOSEARCH_ACTION = 10;
	const TOGGLE_ONTOFACET_GROUP = 11;
	const TOGGLE_ONTOFACET_SEGMENT = 12;
	const CHANGE_WIDGET_POSITION = 13;
	const CHANGE_WIDGET_TAB = 14;
//	const OPEN_SURVISTA_ACTION = 11;
//	const MAXIMIZE_SURVISTA_ACTION = 10;
//	const CLOSE_SURVISTA_ACTION = 11;
//	const NAVIGATE_SURVISTA_ACTION = 12;
//	const ADD_TO_BREADCRUMB_ACTION = 13;
//	const REMOVE_FROM_BREADCRUMB_ACTION = 14;
//	const REMOVE_ALL_FROM_BREADCRUMB_ACTION = 15;
//	const LAUNCH_ZENFILTER_ACTION = 16;
//	const CLOSE_ZENFILTER_ACTION = 17;
//	const ADD_TO_BREADCRUMB_FROM_ZENFILTER_ACTION = 18;
//	const ADD_ALL_TO_BREADCRUMB_FROM_ZENFILTER_ACTION = 19;
//	const REFRESH_TAG_CLOUD_ACTION = 20;
//	const ERASE_TAG_CLOUD_ACTION = 21;
//	const LOAD_HISTORY_CLOUD_ACTION = 22;
//	const CHANGE_TEXT_ZOOM_ACTION = 23;
//	const ACCESS_USER_CONFIGURATION_ACTION = 24;
	const LOG_SRC_TIME = 25;
	const LOG_SRC_IMPORT = 26;
	const LOG_SRC_RDF = 27;
	
	public static function emptyLogFile() {
		$filename = $_SERVER['DOCUMENT_ROOT'] . "/rodin/usabilityLogFile.txt";
		$h = fopen($filename,"w");
		fclose($h);
	}
	
	public static function backupWithTimestampAndEmpty() {
		$oldFilename = $_SERVER['DOCUMENT_ROOT'] . '/rodin/usabilityLogFile.txt';
		
		$timeStampUsed = time();
		$newFilename = $_SERVER['DOCUMENT_ROOT'] . '/rodin/usabilityLogFile' . time() . '.txt';
		
		if (rename($oldFilename, $newFilename)) {
			print '<p>Success, file contents backed up to: usabilityLogFile' . time() . '.txt</p>';
		}
	}
	
  public static function logAction($action, $info = null) 
  {
	  	if (Logger::LOGGER_ACTIVATED) {
				$server_root = $_SERVER['DOCUMENT_ROOT'];
				$filename = $server_root . "/rodin/usabilityLogFile.txt";
				
				$segment = substr($_SESSION['RODINDB_DBNAME'], 6);
				$segment = $segment != '' ? $segment : '?';
				
				$userId = $_SESSION['user_id'];
				$userName = $_SESSION['username'];
				
				//date('d-m-Y H:i:s:u') 
				$timestamp =  date('H:i:s.') . str_pad(substr((float)microtime(), 2), 6, '0', STR_PAD_LEFT);
				$msg = Logger::makeActionString($action, $info);
				$logLine =  $timestamp . ';' . $segment . ';' . $userId . ';' . $userName . ';' . $msg;		
				
		    $h=fopen($filename, "a");
				fwrite($h, $logLine . "\n");
				fclose($h);
				
				if (Logger::LOG_INTO_DB)
				{ 
					if (!function_exists('dblogger'))
						include_once("../../../../../../app/u/FRIdbUtilities.php");
					dblogger($timestamp,$segment,$userId,$userName,$sid,$action,$info, $msg);
				} // 
				
	  	}
    }
    
    private static function makeActionString($action, $info = null, $delimiter = ';') {
    	$infoString = '(no info)';
    	
    	if ($info != null && count($info) > 0) {
    		$infoSingles = array();
    		foreach ($info as $key => $value) {
    			$infoSingles[] = $key . '=' . $value;
    		}
    		
	   		$infoString = implode(',', $infoSingles);
    	}
    	
    	$list = array($action, Logger::getActionName($action), $infoString);
    	
    	return implode($delimiter, $list);
    }
    
    private static function getActionName($action) {
    	switch ($action) {
    		case Logger::LOGIN_ACTION :
    			return 'logged in';
    			break;
    		case Logger::LOGOUT_ACTION :
    			return 'logged out';
    			break;
    		case Logger::ADD_WIDGET_ACTION :
    			return 'added a widget';
    			break;
    		case Logger::REMOVE_WIDGET_ACTION :
    			return 'removed a widget';
    			break;
    		case Logger::OPEN_TAB_ACTION :
    			return 'opened tab';
    			break;
    		case Logger::REMOVE_TAB_ACTION :
    			return 'removed tab';
    			break;
    		case Logger::CREATE_TAB_ACTION :
    			return 'created tab';
    			break;
    		case Logger::RENAME_TAB_ACTION :
    			return 'renamed tab';
    			break;
    		case Logger::LAUNCH_METASEARCH_ACTION :
    			return 'launched metasearch';
    			break;
    		case Logger::LAUNCH_LOCALSEARCH_ACTION :
    			return 'launched search in widget';
    			break;
    		case Logger::LAUNCH_ONTOSEARCH_ACTION :
    			return 'launched an ontological facet search';
    			break;
    		case Logger::TOGGLE_ONTOFACET_GROUP :
    			return 'open/close ontofacet service';
    			break;
    		case Logger::TOGGLE_ONTOFACET_SEGMENT :
    			return 'open/close onto-segment';
    			break;
    		case Logger::CHANGE_WIDGET_POSITION :
    			return 'widget moved';
    			break;
    		case Logger::CHANGE_WIDGET_TAB :
    			return 'widget move to tab (causes log of its removal)';
    			break;
        case Logger::LOG_SRC_TIME :
    			return 'SRC';
    			break;
        case Logger::LOG_SRC_IMPORT :
    			return 'SRC-import';
    			break;
        case Logger::LOG_SRC_RDF :
    			return 'RDF';
    			break;
    		default:
    			return 'unknown action';
    			break;
    	}
    }
}

?>