<?php



$filenamex="app/root.php";
###########################################################
$max=10; for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
{ if (file_exists("$updir$filenamex")) 
	{	require_once("$updir$filenamex"); break;}	}

###########################################################
###########################################################
$DEBUG = $_REQUEST['DEBUG']; if(!$DEBUG) $DEBUG=0;
$ELIB_WIDGET_S_M 		= getA('ELIB_WIDGET_S_M'); 	//Limit number of search results each widgets to ...
$ELIB_THESAURI_S_M 	= getA('ELIB_THESAURI_S_M'); //Limit number of search results each thesaurus to ...
$ELIB_LOD_S_M 			= getA('ELIB_LOD_S_M'); //Limit number of search results each thesaurus to ...
$ELIB_AUTOC_M				= getA('ELIB_AUTOC_M'); 
$ELIB_USERID				= getA('ELIB_USERID');

$ELIB_WIDGETS_TO_USE = getA('ELIB_WIDGETS_TO_USE'); 	//alex,elib,swissbib
if(!$ELIB_WIDGETS_TO_USE) $ELIB_WIDGETS_TO_USE='elibCH';
$ELIB_THESAURI_TO_USE = getA('ELIB_THESAURI_TO_USE'); 	//stw,thesoz,locsh
$ELIB_LODSOURCES_TO_USE = getA('ELIB_LODSOURCES_TO_USE'); 	//europeana,

$TTPNEWLINE = '&#013;';

if (0)
{
	print "<hr>elibroot:";
	print "<br>ELIB_WIDGET_S_M: $ELIB_WIDGET_S_M";
	print "<br>ELIB_THESAURI_S_M: $ELIB_THESAURI_S_M";
	print "<br>ELIB_LOD_S_M: $ELIB_LOD_S_M";
	print "<br>ELIB_AUTOC_M: $ELIB_AUTOC_M";
	print "<br>ELIB_USERID: $ELIB_USERID";
	print "<br>ELIB_WIDGET_TO_USE: $ELIB_WIDGET_TO_USE";
	print "<br>ELIB_THESAURI_TO_USE: $ELIB_THESAURI_TO_USE";
	print "<br>ELIB_LODSOURCES_TO_USE: $ELIB_LODSOURCES_TO_USE";
	
	print "<br>AUTOCOMPLETERESPONDER: $AUTOCOMPLETERESPONDER";
}

$AUTOCOMPLETERESPONDER 	= "$WEBROOT$RODINROOT/$RODINSEGMENT/elib/app/u/AutoCompleteResponder.php";
$languages   						= array('en','fr','de','it','es');

?>