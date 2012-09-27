<?php

	# SRC ENGINE GATEWAY
	#
	# März 2010
	# Fabio.fr.Ricci@hesge.ch  
	# HEG 
	#
	$user		=$_REQUEST['user'];
	$sid		=$_REQUEST['sid'];
	$q			=$_REQUEST['q']; //base64encoded
	$v			=$_REQUEST['v']; //base64encoded
	$w			=$_REQUEST['w'];
	$lang		=$_REQUEST['l'];
	$maxdur	=$_REQUEST['maxdur'];
	$c			=$_REQUEST['c'];
	$cid		=$_REQUEST['cid'];
	$action	=$_REQUEST['action']; 
	$SRCDEBUG = (param_named('SRCDEBUG',$_REQUEST));
	$VERBOSE = (param_named('VERBOSE',$_REQUEST));
	
	// When this script is included, $METHODNAME AND $CLASSNAME
	// should have already been computed.
	
	$SRC = new $CLASSNAME();
	
	switch($METHODNAME)
	{
		case('webstart'): print $SRC->webStart($user); break;
		case('webrefine'): print $SRC->webRefine($sid,$q,$v,$w,$lang,$maxdur,$c,$cid,$action); break;
	}
	
?>