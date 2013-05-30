<?php

/**
 * Fabio Ricci
 *
 * Library in order to use the detecton from PHP
 */

	#Automatically load root
	$here=getcwd();$filename="app/root.php"; $max=10;
	#######################################
	while($here<>'' && $x++<$max)
	{$toinclude="$here/$filename";
			if (file_exists($toinclude)) { require_once($toinclude);break;}
		$here=substr($here,0,strrpos($here,'/'));}
	

	function detectLanguage($text)
	{
		global $DOCROOT;
		if (!$DOCROOT) print "ERROR - empty DOCROOT - wrong language detection<br>";
		$script = dirname($DOCROOT) . '/cgi-bin/cld.exe "' . $text . '"';
		exec($script, $resp);
		$lang = $resp[0];
		return $lang;
	}


	function detectLanguageAndLog($text,$fromwhom,$sid)
	{
		global $DOCROOT;
		//print "<br>$sid $fromwhom ask ($text) ";
		Logger::logAction(27, array('from'=>'detectLanguageAndLog','msg'=>"ask($text,$fromwhom)"), $sid );
		
		$script = dirname($DOCROOT) . '/cgi-bin/cld.exe "' . $text . '"';
		exec($script, $resp);
		
		$lang = $resp[0];
		Logger::logAction(27, array('from'=>'detectLanguageAndLog','msg'=>"exit($lang) was($text,$fromwhom)"), $sid );
		
		//print "...$lang";
		
		return $lang;
	}


?>