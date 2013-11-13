<?php

	/**
	 * RDF/LOD/Expand service
	 * 
	 * Input:
	 * 
	 * A sid
	 * A list of thesauries
	 * A list of LODs sources 
	 * The userid
	 * The maximal amount to document to return (m) - needed ?
	 */
	
	$DEBUG=$_REQUEST['DEBUG'];
	$WEBSERVICE = true; //Prevent other modules to make output to stdin
	$userid=trim($_REQUEST['userid']);
	$sid=trim($_REQUEST['sid']);
	$lodsearch=trim($_REQUEST['lodsearch']);
	
	$k = $_REQUEST['k']; // start outputting results from k
	$rm = $_REQUEST['m']; // output rm results from k
	$thesauries=$_REQUEST['thesauries']; // List of THESAURI sources separated by comma
	$lodsources=$_REQUEST['lodsources']; // List of LOD sources separated by comma
		
	//Example: ?query=Information+Economy&widgets=econbiz+swissbib+googlebooks
	
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
	{
		if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}
	}


	$filename="/u/RodinResult/RodinResultManager.php"; $maxretries=10;
	#######################################
	for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	{
		if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}
	}

	$filename="/u/rdfize.php"; $maxretries=10;
	#######################################
	for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	{
		if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}
	}

	// Validation:
	
	//sid
	$userid_ok = $userid<>'';
	$sid_ok = $sid<>'';
	if ($DEBUG) {
		print "<br>sid_ok = $sid_ok, userid_ok=$userid_ok";
	}
		
	// THESAURI
	//list($theok,$the_records,$errortxt) = get_matching_SRC_records($thesauries,$userid,$the_as_service='UsedAsThesaurus');
	$the_records = get_SRC_THESAURI_RECORDS($SRCS=null,$userid,$lang_notused,$thesauries);
	
	if ($DEBUG) {
		print "<br>RESULT THESAURI: theok=$theok ".count($the_records).' records';
	}
	
	//LOD sources
	list($lodok,$lod_records,$errortxt)  = get_matching_SRC_records($lodsources,$userid,$lod_as_service='UsedForLODRdfExpansion');
	if ($DEBUG) {
		print "<br>RESULT LOD: lodok=$lodok ".count($lod_records).' records: <br>';
		var_dump($lod_records); print "<br>";
	}
	//Are the input parameters satisfying the minimial exec conditions?
	$search_could_start = 
				$sid_ok 
		&&	$userid_ok
		&&	((!$lodsearch) || ($lodok && count($lod_records)))
		;
				
	if (!$search_could_start)
	{
		if($DEBUG) print "<br>Could not start! <br>";
		$errornotification = "Syntax: ?sid=20130809.125101.498.eng.2"
												."&thesauries=thesoz,stw,rameau"
												."&lodsources=europeana"
												."&userid=6"
												."{&k=0}"
												."{&m=3}";
											
		$allResultsJson = json_encode(array(
																				'sid' => 0, 
																				'count' => 0, 
																				'from'=>$k, 
																				'upto' => ($k+$m-1), 
																				'all'=>$all, 
																				'results' => null, 
																				'error'=>$errornotification
																	) 		);									
	}									
	else // $search_could_start
	{
		global $USER; 		$USER		=$userid; // Needed from the called methods so
		global $USER_ID; 	$USER_ID=$userid; // Needed from the called methods so
		global $m; $m=$rm; // need it as global
		global $SEBSERVICE; $SEBSERVICE=1;
		global $COUNTTRIPLES; $COUNTTRIPLES=0;
				
		if ($DEBUG) 
		{
			print "<br>THESAURI: "; //var_dump($the_records); print "<br>";
			foreach($the_records as $the_record)
				print "<br>".$the_record[0].': '.$the_record[18];
			
			if ($lodok)
			{
				print "<br><br>LOD sources:"; 
				foreach($lod_records as $lod_record)
				print "<br>".$lod_record['Name'].': '.$lod_record['sparql_endpoint']; 
			}
		}
		
		global $rdfize; $rdfize=1; // needed by rdfize_and_expand():
		global $RDFLOG;
		rdfize_and_expand($sid, $lodsearch, $the_records, $lod_records);
		if ($DEBUG) {
			print "<br><br>RDFLOG:<br><br>".$RDFLOG;
		}
		//Get all ranked results and send them as json
		$allResultsJson = RodinResultManager::get_json_searchresults4webservice($sid, $k, $rm, true, true);
	} // $search_could_start
	
	//Output results in JSON:
	if (!$DEBUG) header('Content-type: application/json; charset=utf-8');
	if ($DEBUG && $allResultsJson) var_dump($allResultsJson);
	echo $allResultsJson;

	
?>