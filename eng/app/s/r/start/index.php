<?php

	#SRC START (SIMULATION OF THE SRC SERVICE)

	$user		=$_REQUEST['user'];
	
	$ANSWER= "<src_started>"
					."<user>"
					.$user
					."</user>"
					."</src_started>"
					;


sleep(5);
header ("content-type: text/xml");
print $ANSWER;	
?>
