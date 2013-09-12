<?php

/**
 * checkonto_temp.php
 * USE from AJAX
 */

	$check = $_GET[c]=='false'?0:1;
	$src_id = $_GET[i];
	$VERBOSE = $_GET['VERBOSE'];
 
	$filenamex="app/root.php";
	#######################################
	$max=10;
	//print "<br>FRIutilities: try to require $filenamex at cwd=".getcwd()."<br>";
	for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
	{ if (file_exists("$updir$filenamex")) 
		{require_once("$updir$filenamex"); break;}}
	 
	if ($VERBOSE)
	{
		print "<br>i:$src_id"; 
		print "<br>c:$check"; 
	}
 
  global $RODINDB_DBNAME, $RODINDB_USERNAME, $RODINDB_PASSWD, $RODINDB_HOST;
  //print "DB: $RODINDB_DBNAME, $RODINDB_USERNAME, $RODINDB_PASSWD, $RODINDB_HOST";
  $DBconn = mysqli_connect($RODINDB_HOST,$RODINDB_USERNAME,$RODINDB_PASSWD,$RODINDB_DBNAME) or $errors = $errors . "Could not connect to database.\n";
  
  $Q=<<<EOQ
    UPDATE $RODINDB_DBNAME.`src_interface` 
    SET temporarily_used = $check
    WHERE ID=$src_id
EOQ;


	if ($VERBOSE)
	{
		print "<br><br>$Q<br><br>";
	}
  
  $resultset = mysqli_query($DBconn,$Q);
	$numofrows=mysqli_affected_rows($DBconn);
  mysqli_close($DBconn);
  
	//Return XML value for ajax rc
	
	if (!$VERBOSE) header("content-type: text/xml");
	print "<affectedrows>$numofrows</affectedrows>";  
   

?>