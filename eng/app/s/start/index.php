<?php
/**
 * SRC START/TEST
 * Call: http://<Path to SRC>/start?user=<num>
 */

require_once "../../u/FRIdbUtilities.php";

$VERBOSE = (param_named('VERBOSE',$_REQUEST));

$user = $_REQUEST['user_id'];
$service_id = $_REQUEST['service_id'];
$cid = $_REQUEST['cid'];

if (!$cid) 
	$cid = md5(uniqid(mt_rand(), true));

if ($VERBOSE) {
	header("content-type: text/html");
	print "<h1>SRC START Verbose Mode</h1>";
	print "<h2>Request variables</h2>";
	print "<ul><li>user: $user</li>";
	print "<li>service_id: $service_id</li>";
	print "<li>cid: $cid</li></ul>";
}

$url_info_array = get_service_url('start', $user, $service_id);
$src_url = $url_info_array[0];

$src_auth_user = $url_info_array[1];
$src_auth_pass = $url_info_array[2];

if ($VERBOSE) {
	print "<h2>Service URL and base authentification</h2>";
	print "<ul><li>Start URL: $src_url</li>";
	print "<li>Auth-username: $src_auth_user</li>";
	print "<li>Auth-password: $src_auth_pass</li></ul>";
}

$parameters = array("user" => $user);

if ($VERBOSE) {
	print "<h2>Paramters for call on Start URL</h2>";
	print '<p>' . var_export($parameters, true) . '</p>';
}

############################################################
# Call the real SRC and ouput the answer
############################################################
$options = array(
	CURLOPT_HTTPHEADER => array('Accept:text/xml'),
	CURLOPT_TIMEOUT => $FSRC_CURL_TIMEOUT_SEC
);

$output = parametrizable_curl($src_url, $parameters, $options);


if (strstr($output, "Operation timed out")) {
	if ($VERBOSE) {
		print "<h2>Operation timed out</h2>";
		print "<p>After $FSRC_CURL_TIMEOUT_SEC s</p>";	
	}
	
	$output= "<src_timeout>"
		."<timeout>$FSRC_CURL_TIMEOUT_SEC</timeout>"
		."<user>$user</user>"
		."</src_timeout>";
}

###################################
# Print out result
###################################
if ($VERBOSE) {
	print "<h2>Result:</h2>";
	print "<p style=\"border: 1px solid gray;\">$output</p>";
} else {
	header("content-type: text/xml");
	print $output;
}

?>
