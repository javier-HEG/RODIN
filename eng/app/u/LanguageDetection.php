<?php

/**
 * Library in order to use the detecton from PHP
 */

	#Automatically load root
	$filename="app/u/root.php"; $max=10;
	#######################################
	for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
		if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}


	function detectLanguage($text)
	{
		global $DOCROOT;
		$script = dirname($DOCROOT) . '/cgi-bin/cld.exe "' . $text . '"';
		exec($script, $resp);
		
		$lang = $resp[0];
		
		return $lang;
	}


?>