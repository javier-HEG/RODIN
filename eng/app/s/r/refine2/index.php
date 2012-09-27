<?php

	#SRC (SIMULATION OF THE SRC SERVICE)

	$sid		=$_REQUEST['sid'];
	$q			=$_REQUEST['q']; //base64encoded
	$v			=$_REQUEST['v']; //base64encoded
	$w			=$_REQUEST['w'];
	$maxdur	=$_REQUEST['maxdur'];
	$c			=$_REQUEST['c'];
	$cid		=$_REQUEST['cid'];


	$v_clean=base64_decode($v);
	
	$srv_clean="refined 2 $v_clean";
	
	$srv=base64_encode($srv_clean);


	$ANSWER= "<refine>"
					."<cid>$cid</cid>"
					."<c>$c</c>"
					."<v><![CDATA[ $v ]]></v>"  
					."<w>$w</w>" 
					."<q>$q</q>"
					."<sid>$sid</sid>"
					."<srv><![CDATA[  $srv   ]]></srv>"  
					."<maxDur>$maxdur</maxDur>"
					."<rts>1255287670</rts>"
					."<cdur>3030</cdur>"
					."</refine>"
					;


sleep(1);
header ("content-type: text/xml");
print $ANSWER;	
?>
