<?php

include_once 'DBPediaUpdater.php';

header ("content-type: text/xml");

global $ARCCONFIG;
$localARCConfig = $ARCCONFIG;
$localARCConfig['store_name'] = 'loader';

$store = ARC2::getStore($localARCConfig);

if (!$store->isSetUp()) {
	$store->setUp();
}

$action = $_REQUEST['action'];

switch ($action) {
	case "initialize":
		$result = setUpDBPediaUpdaterDBTable();
		
		if ($result) {
			print '<answer>Initialized!</answer>';
		} else {
			print '<answer>There was a problem, the update table was not initialized! (' . $result . ')</answer>';
		}
		
		break;
	
	case "filesToUpdate":
		$processStartTime = time();
		
		$lang = $_REQUEST['lang'];

		$localStatus = getListOfFilesAndModificationTimesOnLocalDB($lang);
		$remoteStatus = getListOfFilesAndModificationTimesOnDBPediaServer($lang);
		$updateStatus = compareAndReturnUpdateTask($remoteStatus, $localStatus);

		print "<answer>\n";

		// Files to remove
		print "<remove>\n";
		foreach ($updateStatus['remove'] as $filename) {
			print "\t<file filename=\"$filename\" />\n";
		}
		print "</remove>\n";
		
		// Files to upload
		print "<upload>\n";
		foreach ($updateStatus['upload'] as $filename) {
			print "\t<file filename=\"$filename\" size=\"" . $remoteStatus[$filename]['size'] . "\" />\n";
		}
		print "</upload>\n";
		
		// Files up to date
		print "<todate>\n";
		foreach ($updateStatus['todate'] as $filename) {
			print "\t<file filename=\"$filename\" size=\"" . $remoteStatus[$filename]['size'] . "\" />\n";
		}
		print "</todate>\n";
		
		// Files in tmp folder
		print "<intemp>\n";
		$filesInTempFolder = getListOfFilesAndModificationTimesOnTmpFolder($lang);
		
		foreach ($filesInTempFolder as $filename => $props) {
			print "\t<file filename=\"$filename\" size=\"" . $props['size'] . "\" />\n";
		}
		print "</intemp>\n";
		
		$totalTime = time() - $processStartTime;
		print "\t<time sec=\"$totalTime\" />\n";
		
		print "</answer>\n";
		
		break;

	case "uploadFile":
		$processStartTime = time();
		
		$filename = $_REQUEST['file'];
		$lang = $_REQUEST['lang'];
		
		print "<answer>\n";
		print "\t<file filename=\"$filename\" />\n";
		
		$unzipped = downloadAndUncompressToTmpFolder($filename, $lang);
		if ($unzipped) {
			print "\t<unzipped status=\"ok\" filename=\"$unzipped\" />\n";
		}
		
		$result = uploadSingleFileToLocalARCStore($store, $unzipped, $lang);
		if ($result) {
			print "\t<triplets count=\"$result\" />\n";
		}
		
		$totalTime = time() - $processStartTime;
		print "\t<time sec=\"$totalTime\" />\n";
		
		recordFileUploadInLocalDB($filename, $result, $totalTime, $lang);
		
		print "</answer>\n";
		
		break;
	
	case "uploadTempFile":
		$processStartTime = time();
		
		$filename = $_REQUEST['file'];
		$lang = $_REQUEST['lang'];
		
		print "<answer>\n";
		print "\t<file filename=\"$filename\" />\n";

		$result = uploadSingleFileToLocalARCStore($store, $filename, $lang);
		
		if ($result) {
			print "\t<triplets count=\"$result\" />\n";
			recordFileUploadInLocalDB($filename.'.bz2', $lang);
		} else {
			print "\t<triplets count=\"-1\" />\n";
		}
		
		$totalTime = time() - $processStartTime;
		print "\t<time sec=\"$totalTime\" />\n";
		
		print "</answer>\n";
		
		break;
		
	case "removeFile":
		$processStartTime = time();
		
		$filename = $_REQUEST['file'];
		$unzipped = TEMPFOLDER . '/' . str_replace('.bz2', '', $filename);
		$lang = $_REQUEST['lang'];
		
		print "<answer>\n";
		print "\t<file filename=\"$filename\" />\n";
		
		$result = removeSingleFileFromLocalARCStore($store, $unzipped, $lang);
		if ($result) {
			print "\t<triplets count=\"$result\" />\n";
			recordFileRemovalInLocalDB($filename, $lang);
		} else {
			print "\t<triplets count=\"-1\" />\n";
		}
		
		$totalTime = time() - $processStartTime;
		print "\t<time sec=\"$totalTime\" />\n";
		
		print "</answer>\n";
		
		break;
		
	default:
		break;
}
?>