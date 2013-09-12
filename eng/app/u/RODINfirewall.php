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



$filename="app/u/FRIdbUtilities.php";
#############################################
$found=false;$max=10;
for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
{ ;
	if (file_exists("$updir$filename")) 
	{ include_once("$updir$filename"); $sitefound=true; break;}
}
if (!$sitefound) print "<br>NO $filename loaded from ".getcwd()."... SYSTEM FAILURE";
#############################################



	/**
	 * 
	 */
	function check_registered_ip_access_or_die_here()
	{
		global $RODINSEGMENT;
		$REMOTE_IP=$_SERVER['REMOTE_ADDR'];	  //Used remote IP
		$REMOTE_PORT=$_SERVER['REMOTE_PORT']; //Used remote port
		$SERVER_ADDR=$_SERVER['SERVER_ADDR']; //10.0.1.2
		
		$EXTERNAL_SERVER_NAME=$_SERVER['SERVER_NAME']; //82.192.234.100
		$EXTERNAL_SERVER_PORT=$_SERVER['SERVER_PORT']; //33445
		$INTERNAL_SERVER_ADDR=$_SERVER['SERVER_ADDR']; //10.0.1.2
		
		$URL= $_SERVER['SCRIPT_FILENAME'];
		$BROWSER = $_SERVER['HTTP_USER_AGENT'];
		
		
		list($ALLOWED,$FORBIDDEN) = check_rodin_firewall_rules($REMOTE_IP, $URL);					
		
		if (!$ALLOWED && !$FORBIDDEN) // new and unknown
		{
			$BG_IMAGE= get_RODINIMAGE($RODINSEGMENT);
			$IMG = "<img width='250' height='300' src='$BG_IMAGE'>";
			print "<html><head><title>RODIN ACCESS WITH UNREGISTERED IP</title></head><body style='margin-left:20px'>" 
					."<br>HELLO! $IMG"
					."<br><br> Thank you very much for your interest in RODIN !"
					."<br>"
					."<br>UNFORTUNATELY the IP number (<b>$REMOTE_IP</b>)"
					."<br>you are using to access RODIN is UNREGISTERED"
					."<br>and you do not have (now) any access to RODIN"
					."<br><br>Please excuse the inconventient, "
					."<br>just <b>give us a call</b> communicating us the IP number"
					."<br> - We will register it ASAP !!"
					."<br><br> Your RODIN team"
					."</body></html>"
					;
					
				send_notification_per_email($REMOTE_IP,'New IP!');
				exit;
		}
		elseif (!$ALLOWED && $FORBIDDEN)
		{
			//NO message - DIE
			$ip_is_registered = false;
			send_notification_per_email($REMOTE_IP,'BLOCKED RODIN ACCESS FOR IP!');
			exit;
		}
		elseif ($ALLOWED && !$FORBIDDEN) {
			$ip_is_registered = true;
		}
		elseif ($ALLOWED && $FORBIDDEN)
		{
			$ip_is_registered = true;
				send_notification_per_email($REMOTE_IP,'CONFLICTING RULES CONCERNING IP!');
		}
		
		return $ip_is_registered;
	} // check_registered_ip_access


	/**
	 * returns a pair of records (ALLOWED_RECORD, FORBIDDEN_RECORD)
	 */
	function check_rodin_firewall_rules($IP, $URL)
	{
		$REC=direct_ip_match($ALLOWED=1,$IP);
		if (!$REC) $REC = partial_ip_match($ALLOWED=1,$IP);

		//Try negative
		$NEGREC=direct_ip_match($ALLOWED=0,$IP);
		if (!$NEGREC) $NEGREC = partial_ip_match($ALLOWED=0,$IP);

		return array($REC,$NEGREC);
	} // check_rodin_firewall_rules



	function send_notification_per_email($REMOTE_IP,$TXT='')
	{
		$DEBUG=0;
		$to='semweb@semweb.ch';
		$subject='RODIN IP';
		$message=$REMOTE_IP.' '.$TXT;
		
		// IF MACOSX 10.7 !!! :		
		$COMMAND=<<<EOC
echo "$message" | mail -s "$subject" $to
EOC;
		if ($DEBUG) print "$COMMAND";
		system($COMMAND);
	}


	/**
	 * returns direct ip matching record
	 */
	function direct_ip_match($ALLOWED,$IP)
	{
		$DEBUG=0;
		if ($DEBUG) print "<hr>";
		$QUERY =<<<EOQ
			SELECT *
			FROM `rodin_firewall_rules` 
			WHERE ALLOWED = $ALLOWED
			AND IP = '$IP'
EOQ;

		$REC=fetch_record($QUERY);
		if ($DEBUG) {
			print "<br>RECORD MATCHING $IP (ALLOWED=$ALLOWED) in query ($QUERY)";
			var_dump($REC);
		}
		
		return $REC;
	}	


	/**
	 * returns partial matching record
	 */
	function partial_ip_match($ALLOWED,$IP)
	{
		$DEBUG=0;
		$REC=null;
		if ($DEBUG) print "<hr>";
		if (!$ALLOWED) $ALLOWED=0;
		
		$QUERY =<<<EOQ
			SELECT *
			FROM `rodin_firewall_rules` 
			WHERE ALLOWED = $ALLOWED
EOQ;
			$RECS = fetch_records($QUERY);
			if ($RECS)
			{
				if ($DEBUG) {print "Trying IP partial match " ;var_dump($RECS);}
				foreach($RECS as $RECX)
				{
					$XIP=$RECX['IP'];
					$partial_match= (strstr($IP,$XIP) || strstr($XIP,$IP));
					if ($DEBUG) print "<br> IP: $XIP ".($partial_match?'YES':'NO');
					if ($partial_match)
					{
						$REC=$RECX;
						break;
					}
				}
			}  else {if ($DEBUG) print "EMPTY RESULT FOR QUERY: ($QUERY)";}
			
		return $REC;
	} // partial_match



function iphref($ip)
{
	global $IPLOCATOR;
	
	$HREF="<a href='{$IPLOCATOR}$ip' target='_blank' 'Click to see where'>$ip</a>";
	
	return $HREF;
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

?>