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
	
	$DEBUG=0;
	$WEBSERVICE = true; //Prevent other modules to make output to stdin
	$userid=trim($_REQUEST['userid']);
	$sid=trim($_REQUEST['sid']);
	$m = $_REQUEST['m'];
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
		print "<br>RESULT LOD: lodok=$lodok ".count($lod_records).' records';
	}
	//Are the input parameters satisfying the minimial exec conditions?
	$search_could_start = 
				$sid_ok 
		&&	$userid_ok
		&&  $lodok
		&&	count($lod_records)
		;
				
	if (!$search_could_start)
	{
		$errornotification = "Syntax: ?sid=20130809.125101.498.eng.2"
												."&thesauries=thesoz,stw,rameau"
												."&lodsources=europeana"
												."&userid=6"
												."{&m=3}";
											
		$allResultsJson = json_encode(array('sid' => 0, 'count' => 0, 'upto' => 0, 'results' => null, 'error'=>$errornotification));									
	}									
	else // $search_could_start
	{
		if ($DEBUG) 
		{
			print "<br>THESAURIES: ";
			foreach($the_records as $the_record)
				print "<br>".$the_record['Name'].': '.$the_record['Path_Refine'];
			print "<br><br>LOD sources:"; 
			foreach($lod_records as $lod_record)
				print "<br>".$lod_record['Name'].': '.$lod_record['sparql_endpoint']; 
		}
	
				
			if (count($lod_records)) 
			{
				global $USER; $USER=$userid;
				rdfize_and_expand($sid, $the_records, $lod_records);
			}
		
		//Get all results and send them as json
		$allResultsJson = RodinResultManager::get_json_searchresults4webservice($sid, true, true);
	} // $search_could_start
	
	//Output results in JSON:
	if (!$DEBUG) header('Content-type: application/json; charset=utf-8');
	if ($DEBUG && $allResultsJson) var_dump($allResultsJson);
	echo $allResultsJson;

	
?>