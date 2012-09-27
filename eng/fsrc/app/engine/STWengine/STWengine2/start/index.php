<?php 

$CLASSNAME = basename(dirname(dirname($_SERVER['PHP_SELF']))); // 
$METHODNAME = 'web'.basename(dirname($_SERVER['PHP_SELF'])); // 


$filename="$CLASSNAME.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) { include_once("$updir$filename");break;}


$filename="SRCengineGateway.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) { include_once("$updir$filename");break;}




?>