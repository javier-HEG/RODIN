<?php

/*
 * Survista main configuration file
 *
 * Copyright 2011 HTW Chur.
 */



#######################################
#
# RODIN
#
$filename="$rodinsegment/app/root.php";
#######################################
$max=10; 
for ($x=1,$updir='';$x<=$max;$x++,$updir.="../") {
	if (file_exists("$updir$filename")) {
//		print "<br>[stw/config.inc] - - $updir$filename exists!";
//		print "<br>[stw/config.inc] Trying to include $filename in $updir";
		$CWD=getcwd();		
		$U = $updir."$rodinsegment/app/u";
		$F = "../../fsrc/app/sroot.php";
		chdir($U); //print "<br>chdir($U)<br>include($F) in ".getcwd();
		include_once($F); #print "<br>included: $F";
		chdir($CWD);
		break;
	} else {
		#print "<br>[config.inc] - - $updir$filename doesn't exist!";
	}
}
#######################################

//print "<br>[stw/config.inc] Included sroot.php";

// ARC config
$config = array();
$config['max_errors'] = 100;
$config['db_name'] = $ARCDB_DBNAME;
$config['db_user'] = $ARCDB_USERNAME;
$config['db_pwd'] = $ARCDB_USERPASS;
#######################################


// be whiny
error_reporting(E_ALL & ~E_NOTICE); // Used to be E_ALL

define('SURVISTA_MAIN_PATH', '../'); // contains survista/, vendor/ etc
define('SURVISTA_PATH', SURVISTA_MAIN_PATH . 'survista/');
define('SURVISTA_ARC_PATH', SURVISTA_MAIN_PATH . 'vendor/semsol-arc2-495d10b/');
// TODO: allow individual cache setting per store
define('SURVISTA_CACHES', false); // stores graph/json data in file cache if true
define('SURVISTA_CACHE_PATH', './cache/'); // relative

// include helpers
include_once(SURVISTA_PATH . 'functions.inc.php');

// include Semsol ARC2
include_once(SURVISTA_ARC_PATH . 'ARC2.php');

$lang = "de";
if (@ $_GET['l'] == "en" || @ $_POST['l'] == "en") {
    $lang = "en";
}
?>