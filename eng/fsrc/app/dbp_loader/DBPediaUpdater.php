<?php

include_once '../sroot.php';
include_once '../../../gen/u/arc/ARC2.php';
include_once '../../../../gen/u/simplehtmldom/simple_html_dom.php';
include_once '../u/FRIutilities.php';
include_once '../u/FRIdbUtilities.php';

define('TEMPFOLDER', '../../gen/u/tmp');

/**
 *
 * This function scraps filenames and modification times from the download server apache
 * answer to the directory corresponding to the language given in parameter.
 * @param string $lang the language for which the list needs to be built.
 * @return An associative table where keys are filenames and values are modification times.
 */
function getListOfFilesAndModificationTimesOnDBPediaServer($lang = '') {
	$downloadURL = "http://downloads.dbpedia.org/current/$lang/";
	$fileNameRE = "/.+\.nt\.bz2/";

	if ($lang == '') {
		$downloadURL = "http://downloads.dbpedia.org/current/$lang/";
		$fileNameRE = "/.+\.owl\.bz2/";
	}

	$fileContent = get_file_content($downloadURL, false);

	$html = str_get_html($fileContent);
	
	$filesTable = $html->find('table', 0);
	$filesTRs = $filesTable->find('tr');

	$filesCount = count($filesTRs)-4;

	$tableToReturn = array();
	// Nor the 3 first or the last TRs represent real files
	for ($i=3; $i<$filesCount+3; $i++) {
		$singleFileTR = $filesTRs[$i];
		
		$fileName = $singleFileTR->find('td',1)->find('a',0)->getAttribute("href");

		// We are only interested in the OWL file, there should only be one
		if (preg_match($fileNameRE, $fileName)) {
			$modificationTimeString = $singleFileTR->find('td',2)->innertext;
			$modificationDateTime = new DateTime($modificationTimeString);

			$fileSize = $singleFileTR->find('td',3)->innertext;
				
			$tableToReturn[$fileName] = array('modif' => $modificationDateTime, 'size' => trim($fileSize));
		}
	}
	
	return $tableToReturn;
}

function getListOfFilesAndModificationTimesOnLocalDB($lang = '') {
	$query = "SELECT * FROM lastupdates WHERE  language = '$lang'";

	if ($lang == "") {
		$query = "SELECT * FROM lastupdates WHERE  language IS NULL";
	}

	$dbObject = new SRC_DB();
	$connexion = $dbObject->DBconn;
	$resultset = mysql_query($query, $connexion);
	
	$tableToReturn = array();
	while ($row = mysql_fetch_assoc($resultset)) {
		$filename = $row['filename'];
		$modif = (string) $row['last'];
		$modifDT = new DateTime($modif);
		
		$tableToReturn[$filename] = $modifDT;
	}

	$dbObject->close();

	return $tableToReturn;
}

function getListOfFilesAndModificationTimesOnTmpFolder($lang = '') {
	$tempDir = opendir(TEMPFOLDER);
	
	$filenameUnzippedNTRE = "/.+_$lang\.nt/";
	$filenameUnzippedOwlRE = "/.+\.owl/";
	$filenameBZip2RE = "/.+\.bz2/";
	
	$tableToReturn = array();

	if ($tempDir) {
		while (false !== ($filename = readdir($tempDir))) {
			if ((strpos($filename, ".") > 0)
				&& (!preg_match($filenameBZip2RE, $filename))
				&& (preg_match($filenameUnzippedNTRE, $filename) || preg_match($filenameUnzippedOwlRE, $filename))) {
				
				$fileSize = 0;
				if (file_exists(TEMPFOLDER . '/' . $filename)) {
					$fileSize = filesize(TEMPFOLDER . '/' . $filename);
				}
					
				$tableToReturn[$filename] = array('size' => $fileSize);
			}
		}

		closedir($tempDir);
	}

	return $tableToReturn;
}

/**
 *
 * This function compares two lists of files and modification times. The goal is to build two lists:
 *  - One of the files to be removed from the ARC store, either because they are no longer present in the
 *    download server or because they will be updated.
 *  - Another one with the files to be uploaded to the ARC store, either because they are new or have been
 *    modified since our last update.
 * @param array $filesInDBPediaDS an associative array with the filenames recovered from the
 * 		  DBPedia Download server and their current modification times.
 * @param array $filesInLocalDB an associative array with the filenames and modification times
 * 		  from the local DB.
 * @return A table containing 2 lists, the first is a list of the files that we need to remove
 * 		   from the ARC DB and the second is a list of the files we need to load.
 */
function compareAndReturnUpdateTask($filesInDBPediaDS, $filesInLocalDB) {
	// First let's find those files that are only present in one of the file lists
	$localDBFilenames = array_keys($filesInLocalDB);
	$remoteDSFilenames = array_keys($filesInDBPediaDS);
	$toRemoveList = array_diff($localDBFilenames, $remoteDSFilenames); // local only
	$toUploadList = array_diff($remoteDSFilenames, $localDBFilenames); // remote only
	$upToDateList = array();

	// We need to compare the modification times of the files that are on both lists
	$localAndRemote = array_intersect($localDBFilenames, $remoteDSFilenames);
	foreach ($localAndRemote as $filename) {
		if ($filesInDBPediaDS[$filename] > $filesInLocalDB[$filename]) {
			//if ($filesInDBPediaDS[$filename]['modif'] > $filesInLocalDB[$filename]) {
			$toRemoveList[] = $filename;
			$toUploadList[] = $filename;
		} else {
			$upToDateList[] = $filename;
		}
	}

	return array('remove' => $toRemoveList, 'upload' => $toUploadList, 'todate' => $upToDateList);
}

function removeSingleFileFromLocalARCStore($store, $filename, $lang = '') {
	$res = $store->query("DELETE FROM <$filename>");

	if ($errs = $store->getErrors()) {
		return false;
		/*foreach($errs as $err) {
		 print ("Error while deleting : $err<br />");
		 }*/
	}

	$duration = $res['query_time'];
	$removed_triples = $res['result']['t_count'];
	$delete_time = $res['result']['delete_time'];
	$index_update_time = $res['result']['index_update_time'];

	return $removed_triples;
}

function uploadSingleFileToLocalARCStore($store, $filename, $lang = '') {
	$res = $store->query('LOAD <' . TEMPFOLDER . '/' . $filename . '>');
	
	if ($errs = $store->getErrors()) {
		return false;

		foreach($errs as $err) {
			print ("Error while loading : $err");
		}
	}

	$duration = $res['query_time'];
	$added_triples = $res['result']['t_count'];
	$load_time = $res['result']['load_time'];
	$index_update_time = $res['result']['index_update_time'];

	return $added_triples;
}

function recordFileUploadInLocalDB($filename, $tripletsCount, $timeSpent, $lang = '') {
	$DB = new SRC_DB();
	$DBconn = $DB->DBconn;

	$now = new DateTime();
	$query = "INSERT INTO lastupdates (filename, triplets, time, language, last) VALUES ('$filename', '$tripletsCount', '$timeSpent', '$lang', '" . $now->format("Y-m-d H:i:s") . "')";
	if ($lang == "") {
		$query = "INSERT INTO lastupdates (filename, triplets, time, last) VALUES ('$filename', '$tripletsCount', '$timeSpent', '" . $now->format("Y-m-d H:i:s") . "')";
	}

	mysql_query($query, $DBconn);
	$DB->close();
}

function recordFileRemovalInLocalDB($filename, $lang = '') {
	$DB = new SRC_DB();
	$DBconn = $DB->DBconn;

	$query = "DELETE FROM lastupdates WHERE filename = '$filename' AND language = '$lang'";
	if ($lang == "") {
		$query = "DELETE FROM lastupdates WHERE filename = '$filename' AND language IS NULL";
	}

	mysql_query($query, $DBconn);

	$DB->close();
}

function downloadAndUncompressToTmpFolder($filename, $lang = '') {
	$url = "http://downloads.dbpedia.org/current/$lang/$filename";
	if ($lang == '') {
		$url = "http://downloads.dbpedia.org/current/$filename";
	}

	$fp = fopen(TEMPFOLDER . '/' . $filename, 'w');

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_FILE, $fp);

	$data = curl_exec($ch);

	curl_close($ch);
	fclose($fp);

	$unzippedFilename = uncompress($filename);

	if ($unzippedFilename) {
		return $unzippedFilename;
	} else {
		return false;
	}
}

function uncompress($srcName) {
	$script = 'python ' . $_SERVER['DOCUMENT_ROOT'] . '/../cgi-bin/jab-bzip2.py ' . $srcName;
	exec($script, $resp, $state);

	if ($resp[0] == 'Error') {
		return false;
	} else {
		return $resp[0];
	}
}

/**
 *
 * Creates the DB table where we will record the last updates made.
 */
function setUpDBPediaUpdaterDBTable() {
	$DB = new SRC_DB();
	$DBconn = $DB->DBconn;

	$query = "CREATE TABLE IF NOT EXISTS lastupdates (
        filename VARCHAR(128) NOT NULL,
        triplets INT(11) NOT NULL COMMENT 'Number of triplets loaded',
  		time INT(11) NOT NULL COMMENT 'Time it took to load in seconds',
        language VARCHAR(8) NULL,
        last DATETIME NOT NULL,
        PRIMARY KEY (filename))";

	$result = mysql_query($query, $DBconn);

	$DB->close();

	return $result;
}

?>