<?php

###########################################
###########################################
###########################################
		error_reporting(0); # disable error reporting for albator

	  $SOLR_URL = "http://localhost:8885/solr";

	  $term=$_GET['term'];
	  $freq=$_GET['freq'];
	  $facet=$_GET['facet'];
	  
    $QS=$_SERVER['QUERY_STRING'];
    
    $implodedQS=  implode('&', $QS);
    
    
	  $X = ($freq)?$freq:$facet;
	  
	  $URL="$SOLR_URL/$freq?q=$term";
	  
		$solr_out = file_get_contents($URL);

		//header ("content-type: text/xml");
		print $solr_out;
		
###########################################
###########################################
###########################################

?>