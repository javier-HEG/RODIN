<?php

	# SRC ENGINE GATEWAY
	#
	# 2012
	# fabio.ricci at ggaweb.ch 
	# HEG 
	#
	$user		=$_REQUEST['user'];
	$sid		=$_REQUEST['sid'];
	$qb64		=$_REQUEST['q']; //base64encoded
	$vb64		=$_REQUEST['v']; //base64encoded
	$w			=$_REQUEST['w'];
	$m			=$_REQUEST['m'];
	$sortrank=$_REQUEST['sortrank'];
	$lang		=$_REQUEST['l'];
	$maxdur	=$_REQUEST['maxdur'];
	$c			=$_REQUEST['c'];
	$cid		=$_REQUEST['cid'];
	$action	=$_REQUEST['action']; 
	$SRCDEBUG = (param_named('SRCDEBUG',$_REQUEST));
	$VERBOSE = (param_named('VERBOSE',$_REQUEST));
	if ($_REQUEST{'directloading'}) $MODE='direct';  else $MODE='web';

	// When this script is included, $METHODNAME AND $CLASSNAME
	// should have already been computed.
  
	$SRC = new $CLASSNAME();
	
  switch($METHODNAME)
  {
    case('webstart'): $OUTPUT= $SRC->webStart($user); break;
    case('webrefine'): $OUTPUT= $SRC->webRefine($sid,$qb64,$vb64,$w,$lang,$m,$sortrank,$maxdur,$c,$cid,$action,$CLASSNAME); break;
  }
      
  if ($MODE=='web') // we were called as a web service on http
  {
    print $OUTPUT;
  }
   
?>