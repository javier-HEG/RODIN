<?php

	# autoclassloader.php
	#
	# Purpose: Determine from the calling file THISCLASS ans BASECLASS 
	#          and load the BASECLASS
	#
	# defines: THISCLASS:BASECLASS to be used to create automatically a class
	#
	# Mai 2011
	# Fabio.fr.Ricci@hesge.ch  
	# HEG 

//$VERBOSE = (param_named('VERBOSE',$_REQUEST));

$THISCLASSNAME = basename(dirname($THISFILE)); // 
$BASECLASSNAME = basename(dirname(dirname($THISFILE))); // 
define(THISCLASS,$THISCLASS);
define(BASECLASS,$BASECLASSNAME);

if ($VERBOSE || 1)
{
chdir(dirname($THISFILE));
print "<br>Place: ".getcwd();
print "<br>FILENAME:".$THISFILE;
print "<br>BASECLASSNAME:".$BASECLASSNAME;
print "<br>THISCLASS:".$THISCLASSNAME;
print "<br>BASECLASS:".$BASECLASSNAME;
}

$filename="$BASECLASSNAME.php"; $max=10;
#######################################
for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {print(" YES $updir$filename ");include_once("$updir$filename");break;}

#
# From here on you can use sth like
#
# class THISCLASS extends BASECLASS {}


?>