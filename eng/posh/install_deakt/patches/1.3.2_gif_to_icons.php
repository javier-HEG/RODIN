<?php
	$handle=opendir('../modules/pictures/');
	while ($file = readdir($handle)) {
		if (substr($file,0,4)=='box0' && substr($file,-4)=='.gif'){
			$newfile=substr($file,0,strlen($file)-4);
			copy("../modules/pictures/".$file,"../modules/pictures/".$newfile);
		}
	}
	closedir($handle);
?>