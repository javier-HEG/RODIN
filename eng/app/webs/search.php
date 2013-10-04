<?php

	/**
	 * Search service
	 * 
	 * Input:
	 * 
	 * A query string like ?query=Digital%20Economy&widgets=econbiz,swissbib,arxiv&userid=2&m=2
	 * Note - widgets: A list of the data sources in which to launch the search (at least one data source)
	 */
	
	$DEBUG=0;
	
	$userid=trim($_REQUEST['userid']);
	$query=trim($_REQUEST['query']);
	$k = $_REQUEST['k']; // start outputting results from k
	$rm = $_REQUEST['m']; // return first $rm results - tell me how many results there are at all
	$query=trim($_REQUEST['query']);
	$widgets=$_REQUEST['widgets']; // List of widgets names separated by comma
		
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

	

	// Validation:
	
	//Userid
	$userid_ok = $userid<>'';
	
	//Query
	$query_ok = $query<>'';
	
	//Widgets
	$widgets_records = get_matching_widget_records($widgets);
	
	//Are the input parameters satisfying the minimial exec conditions?
	$search_could_start = 
				$query_ok 
		&&	$userid_ok
		&&	count($widgets_records)
		;
				
	if (!$search_could_start)
	{
		$errornotification = "Syntax: ?query=Information+Economy"
											."&widgets=econbiz,swissbib,alexandria"
											."&userid=6"
											."{&k=0}"
											."{&m=3}";
											
		$allResultsJson = json_encode(array('sid' => 0, 'count' => 0, 'from' => $k, 'upto' => ($k+$m-1), 'all' => $all, 'results' => null,
																	'error'=>$errornotification));									
											
	}									
	else // $search_could_start
	{
		if ($DEBUG) 
		{
			print "<br><br>OK $query_ok '$query' "
						."<br>widgets: ";
						
			foreach($widgets_records as $widget_record)
				print "<br>".$widget_record['name'].': '.$widget_record['url'];
		}
		
		
		
		//compute $sid
		$sid = compute_sid($userid);
		//search in each widget -> results in sid
		//$wm = $DEFAULT_M; // in db for segment
		search_in_each_widget($widgets_records,$userid,$sid,$query,$wm=10);
		
		//Get all results and send them as json
		$allResultsJson = RodinResultManager::get_json_searchresults4webservice($sid, $k, $rm, true, true);
	} // $search_could_start
	
	//Output results in JSON:
	if (!$DEBUG) header('Content-type: application/json; charset=utf-8');
	if ($DEBUG) var_dump($allResultsJson);
	echo $allResultsJson;

	
	/**
	 * Searches in every widgets
	 * Stores results in SOLR/DB under $sid
	 * Returns NOTHING 
	 */
	function search_in_each_widget($widgets_records,$userid,$sid,$query,$wm)
	{
		$DEBUG=0;
		global $DOCROOT,$RODINUTILITIES_GEN_URL;
		global $RDW_REQUEST;
		global $WEBSERVICE;
		global $USER; $USER=$userid;
		if ($wm==0) $wm=10; // default

		if (!$userid) print "<br>ERROR: NO userid provided!";
		
		foreach($widgets_records as $widget_record)
		{
			$include_path_widget=widget_include_path('http://localhost'.$widget_record['url']);
			$widget_classname=widget_get_class_name($widget_record['url']);
			
			if ($DEBUG)
				print "<br>requiring widget $include_path_widget class ($widget_classname)";
			
			$NOSESSION=true;
			$WEBSERVICE=true;
					
			if ($DEBUG) print "A";
			require_once($include_path_widget);
			if ($DEBUG) print "B";
			
			if ($DEBUG) print "<bt>WIDGET classname: $widget_classname";
			$WIDGET= new $widget_classname();
			
			$widget_clean_url = substr($u=widget_url_cleanup($widget_record['url']),0,strlen($u) - strpos($u,"." ));
			$saved_userprefs_for_this_widget_application= get_prefs($userid,$REQ_APP_ID= -1,$widget_clean_url,$widget_record['id']);
			if($saved_userprefs_for_this_widget_application)
				$PREFS="&$saved_userprefs_for_this_widget_application";
		
			if ($DEBUG) print "<br>saved_userprefs_for_this_widget_application: $saved_userprefs_for_this_widget_application";
			// Load user preferences as PHP variables and in the _REQUEST variables
			$userprefstatement = explode("&",$saved_userprefs_for_this_widget_application);
			foreach ($userprefstatement as $x) {
				if ($DEBUG) print "<br>CONSIDER ($x)";
				list($name,$value) = explode('=',$x);
				if ($value) {
					eval( "\${$name} = $value;" );
					$_REQUEST[$name] = $value;
					$RDW_REQUEST{$name} = $value;
				}
			}
			
			$RDW_REQUEST['q']=$query;
			$RDW_REQUEST['m']=$wm;
			$RDW_REQUEST['sid']=$sid;
			
			//Execute widget:
			$rescount = $WIDGET::DEFINITION_RDW_COLLECTRESULTS();
			if ($DEBUG) {
				print "<br> $rescount results got from ".$widget_record['name']
						." on '$query' with sid=$sid";
			}
		} // foreach $widgets_records
	} // search_in_each_widget
	

	
	
	
	
?>