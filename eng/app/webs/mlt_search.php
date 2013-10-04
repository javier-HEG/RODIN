<?php

	/**
	 * MLT Search service
	 * 
	 * Use Case 1 - MLT from widget result
	 * Use Case 2 - MLT from facet term
	 * @return - JSON results
	 * 
	 * Parameters
	 * userid - always
	 * 
	 * Use Case 1
	 * 	rid result id (solr-id of a widget result)
	 *  sid (optional)
	 * 
	 * Use Case 2
	 *   TBD
	 */
	
	$DEBUG=$_REQUEST['DEBUG'];
	
	$userid=trim($_REQUEST['userid']);
	$rid=$_REQUEST['rid']; // $solr-id of widget result
	$sid=$_REQUEST['sid'];
	$k = $_REQUEST['k']; // start outputting results from k
	$rm = $_REQUEST['m']; // return first $rm results - tell me how many results there are at all

	
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

	$filename="$RODINSEGMENT/app/u/RodinResult/RodinResultManager.php"; $maxretries=10;
	#######################################
	for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	{
		if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}
	}



	

	// Validation Use Case 1

	$usecase1 = ($userid<>'' 
							&& $rid <>''
							);	
	
	//Are the input parameters satisfying the minimial exec conditions?
	$search_could_start = 
						$usecase1 || $usecase2
											;
				
	if (!$search_could_start)
	{
		$errornotification = "Syntax: ?rid=(solr-id-of-widgetresult)"
											."{&sid=...}"
											."&userid=6"
											."{&k=0}"
											."{&m=3}";
											
		$allResultsJson = json_encode(array('sid' => 0, 'count' => 0, 'from' => $k, 'upto' => ($k+$m-1), 'all' => $all, 'results' => null,
																	'error'=>$errornotification));									
											
	}									
	else // $search_could_start
	{
		//Get all results and send them as json
		if ($usecase1) 
		{
			if ($DEBUG)
 				print "<br>USE CASE 1<br>";			
 			$allResultsJson = RodinResultManager::get_json_mltresults4webservice($rid, $sid, $k, $rm, true, true);
			if ($DEBUG)
 				print "<br>USE CASE 1 end<br>";			
 			
		}
	} // $search_could_start
	
	//Output results in JSON:
	if (!$DEBUG) header('Content-type: application/json; charset=utf-8');
	if ($DEBUG) var_dump($allResultsJson);
	echo $allResultsJson;
?>