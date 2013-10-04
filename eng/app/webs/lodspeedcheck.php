<?php

	/**
	 * LODSPEEDCKECK
	 * 
	 * Input:
	 * 
	 * A userid
	 * $maxcallmsecduration
	 * 
	 * @return: a json obj: (response: array('Europeana'=>Array(ok=>1, warning=>...)), array('DBpedia'=>Array(ok=>1, warning=>...)), ...)
	 * 
	 */
	
	$DEBUG=$_REQUEST['DEBUG'];
	$WEBSERVICE = true; //Prevent other modules to make output to stdin
	
	$userid=$_REQUEST['userid'];
	$maxcallmsecduration=intval($_REQUEST['maxcallmsecduration']);

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

	$filename="$RODINSEGMENT/app/u/arcUtilities.php"; $maxretries=10;
	#######################################
	for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	{
		if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}
	}


	// Validation:
	
	//sid
	$userid_ok = $userid<>'';
	if ($DEBUG) {
		print "<br>userid_ok=$userid_ok";
	}
		
	// THESAURI ?
	
	$response = array();
	
	//LOD sources
	$LOD_SOURCES=get_active_LOD_expansion_sources($userid);
	$LOD_SOURCES_RECORDS= $LOD_SOURCES['records'];
	if (is_array($LOD_SOURCES_RECORDS) && count($LOD_SOURCES_RECORDS))
	{
		foreach($LOD_SOURCES_RECORDS as $LOD_SOURCES_RECORD)
		{
			$src_name=$LOD_SOURCES_RECORD['Name'];
			$src_id=$LOD_SOURCES_RECORD['ID'];
			$sds_sparql_endpoint=$LOD_SOURCES_RECORD['sparql_endpoint'];
			$sds_sparql_endpoint_params=$LOD_SOURCES_RECORD['sparql_endpoint_params'];
			$sds_parameters= $LOD_SOURCES_RECORD['src_parameters'];
			
			if ($DEBUG) {
				print "<br>src_name/src_id: $src_name/$src_id";
				print "<br>sds_sparql_endpoint: $sds_sparql_endpoint";
				print "<br>sds_sparql_endpoint: $sds_sparql_endpoint";
				print "<br>sds_sparql_endpoint_params: $sds_sparql_endpoint_params";
				print "<br>sds_parameters: $sds_parameters";
			}
			
			$warning_explanation="\nThis might happen due to mantainance works at the LOD source site or to other connectivity problems."
													."\nThis might therefore currently delay or compromise the RDF-ization of your next search!"
													."\n\nRODIN recommends you to temporarily deactivate the LOD SEARCH or to retry later";
			
			$date = new DateTime();
			$ts= $date->getTimestamp();
			list($used_url,$responsetime_LOD_source_msec, $response_ok) = checkresponsespeed_lod_source($src_name,$src_id,$sds_sparql_endpoint,$sds_sparql_endpoint_params);
			
			if ($response_ok && $responsetime_LOD_source_msec <= $maxcallmsecduration)
			{
				$ok = 1;
				$warning='';
			}
			else if ($responsetime_LOD_source_msec > $maxcallmsecduration) {
				$ok = 0;
				$warning = "The connected LOD source '$src_name' appears to have currently a larger time response"
									." ($responsetime_LOD_source_msec msec) than the expected one ($maxcallmsecduration msec) "
									.$warning_explanation;
			}
			else if (! $response_ok)
			{
				$ok = 0;
				$warning = "The connected LOD source '$src_name' appears not to respond correctly to a preliminary connectivity check."
									.$warning_explanation;

			} 

			$response{$src_name}=array( 'id'=>$src_id,
																	'ts'=>$ts,
																	'used_url'=>$used_url,
																	'limit_ms'=>$maxcallmsecduration,
																	'resp_ms'=>$responsetime_LOD_source_msec,
																	'ok'=>$ok,
																	'warning'=>$warning);
		} // foreach($LOD_SOURCES_RECORDS
	} // if


	
	//Output results in JSON:
	
	$speedckeckJson = json_encode($response);
	if (!$DEBUG) header('Content-type: application/json; charset=utf-8');
	if ($DEBUG && $speedckeckJson) {print "<hr>"; var_dump($speedckeckJson); print "<hr>";}
	echo $speedckeckJson;


?>