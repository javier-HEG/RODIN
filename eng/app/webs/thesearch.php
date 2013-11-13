<?php

/**
 * FILE: thesearch.php
 * PURPOSE: interface to onto search
 * AUTHOR: fabio.ricci@semweb.ch (Tel. +41-76-5281961) for HEG (Geneve)
 * DATE: August 2013 
 * 
 * Input: 
 * 		A query string like: 
 * 				?userid=2&m=5&query=Information%20Economy&thesources=thesoz,locsh
 * 				?userid=2&m=5&query=Information%20Economy&thesources=*
 *  * Note - thesources: A list of the thesaurus sources in which to launch the search (at least one data source)
 * 				a thesource should be identified precisely with a name and a userid
 */
	
	
	$DEBUG = $_REQUEST['DEBUG'];
	
	$userid=trim($_REQUEST['userid']);
	$m = $_REQUEST['m'];
	$host = $_REQUEST['host']; if (!$host) $host='localhost';
	$query=trim($_REQUEST['query']);
	$thesources=$_REQUEST['thesources']; // List of thesources ids separated by comma
	$ontocontext=$_REQUEST['ontocontext']; // Compute also ontocontext each node (for mlt use)
	$sortfacets_lexicographically=$_REQUEST['sortfacets_lexicographically']; // 			
	$unifyedskosresults=$_REQUEST['unifyedskosresults'];
	$mode=$_REQUEST['mode']; //alfa, afla, rele
	if (!$mode) $mode = 'rele'; // sort by relevance
	
	//Load components	
	$filenamex="app/root.php";
	#######################################
	$max=10;
	//print "<br>FRIutilities: try to require $filenamex at cwd=".getcwd()."<br>";
	for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
	{ 
		//print "<br>xxl FRIutilities try to require $updir$filenamex";
		if (file_exists("$updir$filenamex")) 
		{
			//print "<br>REQUIRE $updir$filenamex";
			require_once("$updir$filenamex"); break;
		}
	}
	$filename="$RODINSEGMENT/app/u/FRIdbUtilities.php"; $maxretries=10;
	#######################################
	for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	{if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}}

	$filename="/u/RodinResult/RodinResultManager.php"; $maxretries=10;
	#######################################
	for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	{
		if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}
	}
	
	$filename="app/u/LanguageDetection.php"; $maxretries=10;
	#######################################
	for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
		if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}
	
	$filename="/u/RDFprocessor.php"; $maxretries=10;
	#######################################
	for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
		if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}
	

	// Validation:
	
	//Userid
	$userid_ok = $userid<>'';
	
	//Query
	$query_ok = $query<>'';
	
	//SRC
	//list($theok,$the_records,$errortxt) = get_matching_SRC_records($thesources,$userid,'UsedAsThesaurus');
	global $HOST;
	$HOST=$host;
	$SRCS = get_active_THESAURI_sources( $userid );
	
	if ($DEBUG){
		print "<br>get_active_THESAURI_sources( $userid ) on $RODINSEGMENT:<br> ";
		var_dump($SRCS);
	}
	
	$the_records = get_SRC_THESAURI_RECORDS($SRCS,$userid,$lang_notused,$thesources);
	
	if ($DEBUG) print "<br> THE records: ".count($the_records);
	
	//Are the input parameters satisfying the minimial exec conditions?
	$search_could_start = 
				$query_ok 
		&&	$userid_ok
		&&	count($the_records)
		;
				
	if (!$search_could_start)
	{
		$errornotification = "Syntax: ?query=Information+Economy"
												."&thesources=locsh,thesoz,dbpedia"
												."&userid=6"
												."{ontocontext=1}"
												."{sortfacets_lexicographically=1}"
												."{&m=3}"
												."{&unifyedskosresults=1}"
												."{&mode=alfa}"
												;
											
		$allResultsJson = json_encode(array('query' => $query,
																				'results' => null,
																				'error'=>$errornotification));									
	}									
	else // $search_could_start
	{
		if ($DEBUG) 
		{
			print "<br><br>OK $query_ok '$query' "
						;
			
			print "<br><br>thesources:"; 
			foreach($the_records as $the_record)
				print "<br>".$the_record[14].':'.$the_record[0].': '.$the_record[19];
		}
		//compute $sid
				
		//Get all results and send them as json
		$allResultsJson = RodinResultManager::get_json_thesearchresults4webservice(
												$query, $userid, $m, $ontocontext, $mode, $unifyedskosresults, $the_records);
	} // $search_could_start
	
	//Output results in JSON:
	if (!$DEBUG) header('Content-type: application/json; charset=utf-8');
	if ($DEBUG) var_dump($allResultsJson);
	echo $allResultsJson;
	
?>