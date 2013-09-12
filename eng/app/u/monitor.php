	<html>
<head>
<title>NCE mon</title>
	<STYLE TYPE="text/css">
	<!--
		span.desc { font-size: 7pt; color:blue; }
	-->
	</STYLE>

</head>
<body>


<?php 


$filename="app/root.php";
#############################################
$found=false;$max=10;
for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
{ ;
	if (file_exists("$updir$filename")) 
	{ include_once("$updir$filename"); $sitefound=true; break;}
}
if (!$sitefound) print "<br>NO $filename loaded from ".getcwd()."... SYSTEM FAILURE";
#############################################



$filename="u/FRIdbUtilities.php";
#############################################
$found=false;$max=10;
for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
{ ;
	if (file_exists("$updir$filename")) 
	{ include_once("$updir$filename"); $sitefound=true; break;}
}
if (!$sitefound) print "<br>NO $filename loaded from ".getcwd()."... SYSTEM FAILURE";
#############################################

$filename="css/skin.php";
#############################################
$found=false;$max=10;
for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
{ ;
	if (file_exists("$updir$filename")) 
	{ include_once("$updir$filename"); $found=true; break;}
}
if (!$found) print "<br>NO $filename loaded from ".getcwd()."... SYSTEM FAILURE";
#############################################
//
//
//$filename="gen/u/arc/ARC2.php";
//#############################################
//$found=false;$max=10;
//for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
//{ ;
//	if (file_exists("$updir$filename")) 
//	{ include_once("$updir$filename"); $sitefound=true; 
//		#print "<br> included: $updir$filename from ".getcwd();
//		break;}
//}
//if (!$sitefound) print "<br>NO $filename loaded from ".getcwd()."... SYSTEM FAILURE";



$filename="u/nce_functions.php";
#############################################
$found=false;$max=10;
for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
{ 
	if (file_exists("$updir$filename")) 
	{ 
		include_once("$updir$filename"); 
		$sitefound=true; 
		#print "<br> included: $updir$filename from ".getcwd();
		break;
	}
}
if (!$sitefound) print "<br>NO $filename loaded from ".getcwd()."... SYSTEM FAILURE";




$filename="scr/pte.php";
#############################################
$found=false;$max=10;
for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
{
	;
	if (file_exists("$updir$filename"))
	{
		include_once("$updir$filename"); $sitefound=true;
		#print "<br> included: $updir$filename from ".getcwd();
		break;
	}
}
if (!$sitefound) print "<br>NO $filename loaded from ".getcwd()."... SYSTEM FAILURE";









#############################################################
#############################################################
#############################################################

//CALL LIKE: /app/scr/monitor.php?what=match
$what=$_GET['what'];  // what to do
$showuser=$_GET['user']; 
$when=$_GET['when']; 
$want_locations=$_GET['want_locations'];
$IPLOCATOR="http://ipinfodb.com/ip_locator.php?ip=";
$u=$_GET['u'];  // restrict to user
$reload=$_GET['reload'];  // restrict to user

$REFRESH=($reload<>'')?$reload:5;

$PAGE_BEGIN=<<<EOP
<html>
<head>
<link rel="stylesheet" href="../css/nce.css.php" type="text/css" />
<meta http-equiv="refresh" content="$REFRESH";>
</head>
<body>
EOP;

$PAGE_END=<<<EOP
</body>
<html>
EOP;

print $PAGE_BEGIN;


if ($what=='users' || $what=='match')
{
	$today = date("y-m-d");
	list($EVALSTARTDAT,$stats)= get_user_statistics($u,$RESIPISEGMENT);
	print "NCE {$REFRESH}sec use statistics (all segments) since $EVALSTARTDAT ...<hr>";
	print "<table cellpadding=1 cellspacing=1 border=0>";
	foreach($stats as $userARR)
	{
		
		$id=uniqid();
		list($start,$user,$segment,$ip,$activity,$UserActivityDetail) = $userARR;
		if ($when=='' || strstr($start,$when))
		{
			
			if ($want_locations)
			{
				if (!($location = $locations{ $ip }))
				{
					$location = $locations{ $ip } = get_ip_location($ip);
				}
				$LOCATION_DET="$location";
			}
			
			$EXPAND=<<<EOE
					onmouseover="this.style.cursor='crosshair'"
					onmouseout="this.style.cursor='default'"
					onclick="var d=document.getElementById('$id');
								if (d.style.display=='none') 
								{d.style.display='block'; d.style.visibility='visible';} 
								else {d.style.display='none'; d.style.visibility='hidden';}"
EOE;
		$DATECOLOR = strstr($start,$today)? "#a33":"gray";
		list($start_time,$comment,$computation_description,$selectedOntoId)=$UserActivityDetail;
		print "<tr $EXPAND><td width=10 align=left><b>$activity</b></td><td width='130' nowrap='nowrap' title='The latest work timestamp' style=\"color:$DATECOLOR\">$start</td><td width=2 >$segment:<b><span title=\"Click to open details\" "
					.">$user</span></b> </td><td width=10> ".iphref($ip)."</td><td width='300' align=left nowrap=nowrap>$LOCATION_DET</td></tr>";
		print "<tr><td colspan=4><div style=\"visibility:hidden; display:none\" id='$id'><table cellpadding=0 cellspacing=1>";
		
		for($i=0;$i<count($start_time);$i++)
		{
			if (!($ontoname = $ontonames{ $selectedOntoId[$i] }))
			{
				$ontology_record=get_ontology_info($selectedOntoId[$i]);
			  $ontonames{$selectedOntoId[$i]} = $ontoname = urldecode(trim($ontology_record['framename'].'_'.$ontology_record['versionname']));
			}
			
// 			if (preg_match("//",$computation_desctiption,$match))
// 			{
				
// 			}
// 			else 
			$computation_descriptionSHORT = $computation_descr = $computation_description[$i];
			
			
			print "<tr>";
			print "<td valign='top' nowrap=nowrap><b>{$start_time[$i]}</b></td><td valign='top'>$ontoname</td><td valign='top'>{$comment[$i]}</td><td valign='top' title='$computation_descr'><span class='desc'>$computation_descriptionSHORT</span></td>";
			print "</tr>";
		}
		
		print "</table></div></td></tr>";
		}
	}
	print "</table";
	
} // eval
else
if ($what=='onto')
{
	list($EVALSTARTDAT,$stats)= get_onto_match_statistics($RESIPISEGMENT);
	print "Ontologies used statistics NCE on $RESIPISEGMENT since $EVALSTARTDAT ...<hr>";
	print "<table>";
	foreach($stats as $activity=>$onto)
	{
		print "<tr><td><b>$onto</b>:</td><td align=right>$activity</td></tr>";
	}
	print "</table";

} // eval
else printsyntax();

print $PAGE_END;



#########################################################################################
#########################################################################################
#########################################################################################




function iphref($ip)
{
	global $IPLOCATOR;
	
	$HREF="<a href='{$IPLOCATOR}$ip' target='_blank' 'Click to see where'>$ip</a>";
	
	return $HREF;
}






function get_user_statistics($u,$segment)
####################################################
#
# returns a list($disambToken,$rankedDisamb)
# to $ontoselid,$entityname (if existing)
# else list(null,null) 
{
	global $SEPARATOR;
	
	$USERCONSTRAINT=$u<>''
								 ?" AND user='$u'"
								 :''
								 ;

	$QUERY=<<<EOQ
	SELECT min(start_time) as start
	FROM `execution_progress_indicator`
	WHERE segment='$segment' $USERCONSTRAINT
EOQ;
	
	$REC = get_record($QUERY);
	$EVALSTARTDAT=$REC['start'];
	  	
	  	
 	$QUERY=<<<EOQ
SELECT max(start_time) as start, user, segment, remote_ip,  count(*) AS ACTIVITY
FROM `execution_progress_indicator`
WHERE true $USERCONSTRAINT
GROUP BY user, segment, remote_ip
ORDER BY start_time DESC, segment ASC, ACTIVITY DESC
EOQ;
 
  	$STATS = get_records($QUERY);

	  foreach($STATS as $STAT)
	  {
	  	
	  	$start_time=$STAT['start'];
	  	$user=$STAT['user'];
	  	$remote_ip=$STAT['remote_ip'];
	  	$segment=$STAT['segment'];
	  	$activity=$STAT['ACTIVITY'];
	  	
	  	
	  	$UserActivityDetail=get_userActivityDetail($user,$segment,$remote_ip);
	  	
	  	$actiuser[]=array($start_time,$user,$segment,$remote_ip,$activity,$UserActivityDetail);
	  }
	  	
  	return array($EVALSTARTDAT,$actiuser);
} // get_user_statistics





function get_userActivityDetail($user,$segment,$remote_ip)
{
	$QUERY=<<<EOQ
	SELECT comment, computation_description, selectedOntoId, start_time
	FROM `execution_progress_indicator`
	WHERE user='$user' 
	AND segment='$segment'
	AND remote_ip='$remote_ip'
	ORDER BY start_time DESC
EOQ;
	//print "<hr>$QUERY<hr>";
	$STATS = get_records($QUERY);
	foreach($STATS as $STAT)
	{
		//print "<br>get_userActivityDetail($user,$segment,$remote_ip): $start_time";
		$comment[]=$STAT['comment'];
		$computation_description[]=$STAT['computation_description'];
		$selectedOntoId[]=$STAT['selectedOntoId'];
		$start_time[]=$STAT['start_time'];
	}
	return array($start_time,$comment,$computation_description,$selectedOntoId);
}





function get_onto_match_statistics($segment)
####################################################
#
# returns a list($disambToken,$rankedDisamb)
# to $ontoselid,$entityname (if existing)
# else list(null,null)
{
	global $SEPARATOR;

	$QUERY=<<<EOQ
	SELECT min(start_time) as start
	FROM `execution_progress_indicator`
	WHERE segment='$segment'
EOQ;

	$REC = get_record($QUERY);
	$EVALSTARTDAT=$REC['start'];


	$QUERY=<<<EOQ
SELECT o.name,  count(*) AS ACTIVITY
FROM `execution_progress_indicator` i, `ontology` o
WHERE i.segment='$segment'
AND o.ID = i.selectedOntoId
GROUP BY o.name
ORDER BY ACTIVITY DESC
EOQ;

	//print "$QUERY<br><br>";
	$STATS = get_records($QUERY);

	foreach($STATS as $STAT)
	{
		$name=$STAT['name'];
		$activity=$STAT['ACTIVITY'];
		$actionto{$activity}=$name;
	}

	return array($EVALSTARTDAT,$actionto);
} // get_onto_match_statistics




function valid_ip($ip) {
	return preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])" .
            "(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/", $ip);
}



function get_ip_location($remote_ip)
{
// 	$remote_ip='82.192.234.100';
// 	$remote_ip='109.153.189.197';

	//IP address : 188.154.151.84 Country : CH State/Province : BERN City : BERNE Zip or postal code : - Latitude : 46.947999 Longitude : 7.448148 Timezone : +01:00 Local time : January 31 17:07:09 Hostname : xdsl-188-154-151-84.adslplus.ch 
	global $IPLOCATOR;
	if (! valid_ip($remote_ip))
	return '?';	
	else
	{
		$html_content=get_file_content("$IPLOCATOR".$remote_ip);
		$XPATH_UL="/html/body/div/div/div[2]/div/div[2]/div/div/div/div/div/ul";
		$doc = new DOMDocument();
		@$doc->loadHTML($html_content);
		$xpath_processor = new DOMXpath($doc);
		$results = $xpath_processor->query($XPATH_UL);
		foreach($results as $result)
		{
			if ($result->nodeValue)
			{
				$res=str_replace("  ","",$result->nodeValue);
				$resARR=explode("\n",$res);
				
// 				print "(({$result->nodeValue}))";
//  				print "(($res))";
				
				foreach($resARR as $a)
				{
					if (trim($a))
					{
						if (preg_match("/City\W\:\W(.*)\W/",$a,$match))
							$location=$match[1];
						elseif (preg_match("/Hostname\W\:\W(.*)\W/",$a,$match))
							$hostname=$match[1];
						elseif (preg_match("/Country\W\:\W(.*)\W\W/",$a,$match))
							$country=$match[1];
					}
				}
			}
		}		

// 		print "<br>location=($location)";
// 		print "<br>hostname=($hostname)";
// 		print "<br>country=($country)";
		
		if (trim($location)<>'' && trim($location)<>'-')
			$LOC=trim($country)." - $location";
		else
			$LOC="$country - ";
		
		$RET="$LOC ($hostname)";
// 		print "<br>Returns: ".$RET;
// 		exit;
		return $RET;
	}
}




   
  	function listen_user_wish_terminate_computation($SID)
  ######################################################
  #
  # Return true iff there ex. computation($SID) with marked user temination
  #
  {
  global $SYSTEM_HTML;
  	
  $SELECT=<<<EOS
  SELECT `stopped_by_user`
  FROM `execution_progress_indicator`
  	  	  	WHERE `sid` = '$SID';
EOS;
    	$record = get_record($SELECT);
    	$user_wish_terminate_computation=false;
    	 
  //$SYSTEM_HTML.="<br>".$SELECT;
  
  if (is_array($record))
  {
  $user_wish_terminate_computation = $record['stopped_by_user'];
  //$SYSTEM_HTML.="<br>". "listen_user_wish_terminate_computation stopped_by_user: ($user_wish_terminate_computation)";
  }
  	
  return $user_wish_terminate_computation;
  }
  
  
  
  
  
  
  function register_progress($ID,$PROGRESS,$NOOFISSTEPS,$TIMELEFTSEC,$COMMENT)
  #########################################################################
  {
  	global $servicemode;
  	global $CRAWLERDB_DBNAME;
  	if (!$servicemode)
  	{
  	// Update one progress line into DB
  
  	$DB = new WP4_DB($CRAWLERDB_DBNAME);
  	$DBconn=$DB->DBconn;
  
  	$COMMENT_ESC = mysql_real_escape_string($COMMENT);
  
  		$UPDATE=<<<EOS
  	UPDATE `execution_progress_indicator`
  	SET
  	`progress` = $PROGRESS,
  	`comment` = '$COMMENT_ESC',
  	`no_of_is_steps` = $NOOFISSTEPS,
  					  			`time_left` = $TIMELEFTSEC
  			  	WHERE `sid` = '$ID';
EOS;
  		  	
  	  	$affected = update($UPDATE);
  	  	if ($affected<>1)
  	$SYSTEM_HTML.=htmlprint("System error: Could not update execution_progress_indicator using id=$ID ($affected affected rows)<br>$UPDATE",'red');
  	} // !servicemode
  	} // register_progress
  	
  	
  	
  	  
  	  
  	  
 	  function complete_progress($ID,$NOOFISSTEPS,$DURATION,$user_termination)
 	  #########################################################################
  	{
  	global $servicemode;
  	global $SYSTEM_HTML;
  	if (!$servicemode)
  	{
  
  	if ($user_termination)
  	$comment="Terminated by the user (closed by system)";
  		else
  		$comment="Terminated";
  
  		// Update one progress line into DB
  			$UPDATE=<<<EOS
  			UPDATE `execution_progress_indicator`
  			SET
  			`progress` = 1,
  			`comment` = '$comment',
  			`duration` = $DURATION,
  			`no_of_is_steps` = $NOOFISSTEPS,
  			`time_left` = 0
  			WHERE `sid` = '$ID';
EOS;
  	
  	
  	  		$affected = update($UPDATE);
  			if ($affected<>1)
  		  		$SYSTEM_HTML.=htmlprint("System error: Could not update execution_progress_indicator using id=$ID ($affected affected rows)<br>$UPDATE",'red');
  			 
  			 
  			 
  			 
  			if ($never) // we want to keep every computation...
  			{
  			// Create one progress line into DB
  			$DELETE=<<<EOS
  	  			  	  	DELETE FROM `execution_progress_indicator`
  			WHERE `sid` = '$ID';
EOS;
  			$affected = update($DELETE);
  			if ($affected<>1)
  			$SYSTEM_HTML.=htmlprint("System error: Could not delete execution_progress_indicator using id=$ID ($affected affected rows)<br>$DELETE",'red');
  			}
  
  			}
  	  } // delete_progress
  	   
  



function printsyntax()
######################
{
	global $IS_FRI_MAC_LAP, $IS_HEG_LNX_ALBATOR;
	if ($IS_FRI_MAC_LAP)	
		$ONTOID=186;
	else if($IS_HEG_LNX_ALBATOR)
		$ONTOID=89;
	
	fontprint("SYNTAX: /app/scr/monitor.php?what={users|onto}&when=yyyy-mm-dd",'red');
	
	exit;
}

?>
</body>
</html>
