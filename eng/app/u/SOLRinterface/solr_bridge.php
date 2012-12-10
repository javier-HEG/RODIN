<?php

###########################################
###########################################
###########################################
		error_reporting(E_ALL); # disable error reporting for rodin server

	  $SOLR_URL = "http://localhost:8885/solr";

	  $method=$_GET['method']; //everything before (not solr)?
	  $coll=$_GET['coll']; //everything before (not solr)?
	  $QUERYSTRING=explode('&',$_SERVER['QUERY_STRING']);
    $qs='';
    $format='xml'; // default
    
    foreach($QUERYSTRING as $pair)
    {
       list($k,$v) = explode('=',$pair);
       if ($k<>'coll' && $k<>'method')
       {
//         print "<br>$k=>$v";
         $qs.=$qs<>''?'&':'?';
         $qs.="$k=$v";
         
         if ($k=='wt')
           $format=$v;
       }    
    }

    $URL="$SOLR_URL/$coll/$method$qs";

//    print "<br>coll: $coll";
//    print "<br>method: $method";
//    print "<br>qs: $qs";
//    print "<br>INNER LOCAL USED URL: $URL";
    $contenttype='text/xml'; // default
    switch ($format)
    {
      case 'xml': $contenttype='text/xml'; break;
      case 'json': $contenttype='text/x-json'; break;
      case 'csv': $contenttype='application/CSV';break;
    }
    
    
		$solr_out = file_get_contents($URL);
 		//print htmlentities($solr_out);
		header ("content-type: $contenttype");
		print ($solr_out);
		
###########################################
###########################################
###########################################

?>