<?php 

/*
 * If called/included directly by a local php program
 *    $path shows the include path, i.e. the path of this script
 *    $CLASSNAME has to be set in advance by the caller/includer
 */

if (!$CLASSNAME) $CLASSNAME = basename(dirname(dirname($_SERVER['PHP_SELF']))); // 
$METHODNAME = 'web'.basename(dirname($_SERVER['PHP_SELF'])); // 

//print "<br>CLASSNAME: $CLASSNAME";
//print "<br>METHODNAME: $METHODNAME";
//
//print "<br>RODINROOT: $RODINROOT";
//print "<br>RODINSEGMENT: $RODINSEGMENT";
//
//print "<br>IN FILE: $path"; 
//print "<br>IN DIR: ".getcwd();

//Change to $path if set!
if ($path) $chdir_res=chdir($path);


//Load the class meant
$filename="$CLASSNAME.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) { require_once("$updir$filename");break;}
  
  
//Exec the class meant
$filename="SRCengineGateway.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
  if (file_exists("$updir$filename")) { include_once("$updir$filename");break;}

?>