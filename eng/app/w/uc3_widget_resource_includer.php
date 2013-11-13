<?php

//Resource includer for every uc3 widget
//Should use UC3 resources instead of RODIN RESOURCES

//Supposes $RODINSEGMENT IS SET!

//Change dir to currend
$olddir = getcwd();
$r=chdir($newdir="../../../../rodinuc3/$RODINSEGMENT/app/u");

//print "changing dir to $newdir";
include_once("../root.php"); //This changes RODINROOT to UC3
include_once("./RodinWidgetBase.php");
include_once("./RodinResult/RodinResultManager.php");

?>