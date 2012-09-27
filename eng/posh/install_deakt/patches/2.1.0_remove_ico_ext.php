<?php
	//remove .ico extension
	$handle=opendir('../modules/pictures/');
	while ($file = readdir($handle)) {
		if (substr($file,-4)=='.ico'){
			$newfile=substr($file,0,strlen($file)-4);
			copy("../modules/pictures/".$file,"../modules/pictures/".$newfile);
		}
	}
	closedir($handle);
	$handle=opendir('../modules/quarantine/');
	while ($file = readdir($handle)) {
		if (substr($file,-4)=='.ico'){
			$newfile=substr($file,0,strlen($file)-4);
			copy("../modules/quarantine/".$file,"../modules/quarantine/".$newfile);
		}
	}
	closedir($handle);

	// remove .ico reference in DB data
	$DB->getResults($install_replaceIcoExtInModule);
	$DB->getResults($install_replaceIcoExtInPages);
?>
