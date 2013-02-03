<?php
/**
 * SRC REFINE
 * Call: http://<Path to SRC>/refine?user=<num>
 */
require_once "../../u/FRIdbUtilities.php";
require_once "../../tests/Logger.php";

$user = $_REQUEST['user'];
$service_id	= $_REQUEST['service_id'];
$VERBOSEhere = $VERBOSE = (param_named('VERBOSE',$_REQUEST)) || (param_named('verbose',$_REQUEST));


//LOG SRC TIME HERE:
Logger::logAction(25, array('from'=>'s/refine/index.php','msg'=>'Started Service ID='.$service_id));


if ($VERBOSE) {
	header("content-type: text/html");
	print "<h1>SRC REFINE Verbose Mode</h1>";
	print "<h2>First batch of request variables</h2>";
	print "<ul><li>user: $user</li>";
	print "<li>service_id: $service_id</li></ul>";
}

$url_info_array = get_service_url('refine', $user, $service_id);
$SRC_REFINE_URL = $url_info_array[0];

if (!$user)	{
	print '<h2>FATAL error</h2>';
	print "<p>Missing user!</p>";
	exit;
}

if (!$service_id) {
	print '<h2>FATAL error</h2>';
	print "<p>Missing service!</p>";
	exit;
}

if ($SRC_REFINE_URL==':///') {
	print '<h2>FATAL error</h2>';
	print "<p>Some problems getting from DB informations on service_id = $service_id for user = $user</p>";
	exit;
}

$SRC_AUTH = $SRC_START_URL_INFO[1];
$SRC_PASS = $SRC_START_URL_INFO[2];

if ($VERBOSE) { 
	print "<h2>SRC Service values</h2>";
	print "<ul><li>SRC_CURL_TIMEOUT_SEC: $FSRC_CURL_TIMEOUT_SEC</li>";
	print "<li>SRC_START_URL: $SRC_REFINE_URL</li>";
	print "<li>SRC_AUTH: $SRC_AUTH</li>";
	print "<li>SRC_PASS: $SRC_PASS</li></ul>";
}	
	
$sid = $_REQUEST['sid'];
$newsid = $_REQUEST['newsid'];
$q = $_REQUEST['q'];
$v = $_REQUEST['v']; //base64encoded
$w = $_REQUEST['w'];
$l = $_REQUEST['l'];
$maxdur = $_REQUEST['maxdur'];
$c = $_REQUEST['c'];
$cid = $_REQUEST['cid'];
$rts = $_REQUEST['rts'];
$action = $_REQUEST['action'];
$timeout = $_REQUEST['timeout'];
	
if ($VERBOSE) {
	print "<h2>Second batch of request variables</h2>";
	print "<ul><li>sid: $sid</li>";
	print "<li>newsid: $newsid</li>";
	print "<li>maxdur: $maxdur</li>";
	print "<li>cid: $cid</li>";
	print "<li>action: $action</li>";
	print "<li>timeout: $timeout</li></ul>";
}
	
$discard = array('showmenu', 'currentpage', 'myhomepage',
	'laststate', 'PHPSESSID', 'MANTIS_STRING_COOKIE',
	'MANTIS_BUG_LIST_COOKIE', 'MANTIS_VIEW_ALL_COOKIE',
	'ZDEDebuggerPresent', 'VERBOSE');
	
if ($action<>'dummy' && $action<>'dummytimeout') {
	############################################################
	# Call the real SRC and ouput the answer
	############################################################
	foreach($_REQUEST as $name => $value) {
		if (!is_in_bag($name, $discard, true)) {
			$input .= ($input ? "&$name=$value" : "$name=$value");
		}
	}
	
	$SRCurl = "$SRC_REFINE_URL?$input";
			
	if ($VERBOSE) {
		print "<h2>Calling the real SRC</h2>";
		$SRCurl_x = "$SRCurl&VERBOSE=1&SRCDEBUG=1";
		print "<ul><li>Calling <a href='$SRCurl_x' target=\"_blank\">$SRCurl_x</a></li></ul>"; 
	}

	$options = array(
		CURLOPT_HTTPHEADER => array('Accept:text/xml'),
		CURLOPT_TIMEOUT => $FSRC_CURL_TIMEOUT_SEC
	);
  
	$output = parametrizable_curl($SRCurl, array(), $options);
	
	if (strstr($output, "Operation timed out") || strstr($output, "Couldn't resolve host") || $output=='') {
		if ($VERBOSE) {
			print "<h2>Operation timed out</h2>";
			print "<p>After $FSRC_CURL_TIMEOUT_SEC s</p>";
		}
		
		$output=<<<EOU
			<refine>
				<timeout>$FSRC_CURL_TIMEOUT_SEC</timeout>
				<cid>$cid</cid>
				<c>$c</c>
				<v> $v </v>
				<l>$l</l>
				<w>$w</w>
				<q>$q</q>
				<sid>$sid</sid>
				<srv>$srv</srv>
				<maxDur>$maxdur</maxDur>
				<rts>0</rts>
				<cdur>-1</cdur>
				<action>$action</action>
			</refine>
EOU;
	}
} else { // when action is 'dummy' or 'dummytimeout'
	if ($action=='dummytimeout')
		$TIMEOUTINFO="<timeout>$timeout</timeout>";
		
	$output=<<<EOO
		<refine>
			$TIMEOUTINFO
			<action>$action</action>
			<sid>$sid</sid>
			<cid>$cid</cid>
			<c>$c</c>
			<v></v>
			<w>$w</w>  
			<l>$l</l>
			<q>$q</q>
			<srv></srv>  
			<maxDur>$maxdur</maxDur>
			<rts>$rts</rts>
			<cdur>1</cdur>
		</refine>
EOO;
}


Logger::logAction(25, array('from'=>'s/refine/index.php','msg'=>'Delivered Service ID='.$service_id));

###################################
# Print out output
###################################
if ($VERBOSEhere) {
	print "<h2>Output:</h2>";
	//print '<div style="border: 1px solid gray;">' . html_printable_xml($output) . '</div>';
  print $output;
} else {
	header("content-type: text/xml");
	print $output;
}


?>
