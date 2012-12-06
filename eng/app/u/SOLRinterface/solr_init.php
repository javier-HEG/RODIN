<?php
/*
 * Fabio Ricci for HEG
 * fabio.ricci@ggaweb.ch Tel.: +41-76-5281961
 */

$filenamex="app/root.php";
#######################################
$max=10;
//print "<br>FRIutilities: try to require $filenamex at cwd=".getcwd()."<br>";
for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
{
	//print "<br>try to require $updir$filenamex";
	if (file_exists("$updir$filenamex"))
	{
		//print "<br>REQUIRE $updir$filenamex";
		require_once("$updir$filenamex"); break;
	}
}

$config=array();



function solr_client_init($solr_host='',$solr_port='', $solr_path='', $solr_core, $solr_timeout)
##########################
{
  $client=null;
  if ($solr_host=='' || $solr_port=='' || $solr_path=='')
  {
    print "<br>solr_client_init(): System error (Wrond SOLR connect parameters): solr_client_init(solr_host=$solr_host, solr_port=$solr_port, solr_path=$solr_path)<br>";
  }
  else
  {
    global $config, $SOLARIUMDIR;
    //SET solarium $config object (do not change structure: solarium specific!!
    $config['adapteroptions']['host']=$solr_host;
    $config['adapteroptions']['port']=$solr_port;
    $config['adapteroptions']['path']=$solr_path;
    $config['adapteroptions']['core']=$solr_core;
    $config['adapteroptions']['timeout']=$solr_timeout;
    include_once("$SOLARIUMDIR/Autoloader.php");

    Solarium_Autoloader::register();

    // create a client instance
    $client = new Solarium_Client();
  }
  return $client;

} #solr_client_init



function solr_synch_update($sid='default_sid',$txt,&$client,&$documents)
################################################
#
# Several processes are synched on a file "$sid.lock"
#
{
  $needsynchlog=false;
  $needDEBUGprecision=false;
  global $SOLR_RODIN_LOCKDIR;
  if ($needsynchlog)
  {
    $LOGfilename="$SOLR_RODIN_LOCKDIR/synch.LOG.txt";
   
    $log=fopen($LOGfilename,"a");
    $now=date("d.m.Y H:i:s").'.'.substr(microtime(false),2,6);
    fwrite($log, "\n\n$now ENTER SYNCH ($sid,$txt)");
    
    if ($needDEBUGprecision)
    {
      fwrite($log, "\n--------------");
      fwrite($log, "\nDOCUMENTS (sid,body or cached info):");
      fwrite($log, "\n--------------");
      foreach($documents as $doc)
      {
        if ($doc->sid)
          fwrite($log, "\n\nSid: ".$doc->sid);
        if ($doc->body) 
          fwrite($log, "\nBody:\n".$doc->body);
        // In case of a cache operation:
        if ($doc->cached) 
        {   
          fwrite($log, "\nid: ".$doc->id);
          fwrite($log, "\nidsource: ".$doc->idsource);
          fwrite($log, "\nCached: \n".$doc->cached);
        }  
      } 
      fwrite($log, "\n\n--------------");
    } 
  }

  
  wait2lock_token($sid);

  if ($needsynchlog)
  {
    $now=date("d.m.Y H:i:s").'.'.substr(microtime(false),2,6);
    fwrite($log, "\n$now SYNCHED ($sid,$txt)");
  }


  // get an update query instance
  $update = $client->createUpdate();
  // add the result document and a commit command to the update query
  $update->addDocuments($documents);
  $update->addCommit();
   // this executes the query and returns the result

  $update_result_code= $client->update($update);



  if ($needsynchlog)
  {
    $now=date("d.m.Y H:i:s").'.'.substr(microtime(false),2,6);
    fwrite($log, "\n$now UPDATED ($sid,$txt)");
  }


  unlock_token($sid); //give free to next one


  if ($needsynchlog)
  {
    $now=date("d.m.Y H:i:s").'.'.substr(microtime(false),2,6);
    fwrite($log, "\n$now EXIT SYNCH ($sid,$txt)");
  }

  return $update_result_code;
}








function wait2lock_token($sid)
##############################
#
# Locks using $sid.lock as name
# 
{
  global $SOLR_RODIN_LOCKDIR;
  $lockfilename="$SOLR_RODIN_LOCKDIR/$sid.lock";
  while (file_exists($lockfilename))
  {
    usleep(mt_rand(10, 100));
  }

  if (file_exists($lockfilename)) wait2lock_token($sid);
  else
  {
    $h=fopen($lockfilename,"w");
    if (!$h) print "<br>Could not create lockfile $lockfilename! Check permissions/paths?";
  }
}



function unlock_token($sid)
################################
#
# Wait 1 second after free ...
#
{
  global $SOLR_RODIN_LOCKDIR;
  $lockfilename="$SOLR_RODIN_LOCKDIR/$sid.lock";

  $unlink_result_code=true;
  if (file_exists($lockfilename))
  {
    //sleep(3);
    $unlink_result_code=unlink($lockfilename);
  }
  return $unlink_result_code;
}


function get_solrbridge($solr_query_url)
{
  
  //http://localhost:8885/solr/rodin_result/select?q=sid:20121205.162229.682.2%20wdatasource:/rodin/x/app/w/RDW_ArXiv.rodin&rows=10&wt=xml&fl=score,*&omitHeader=true
  //==>
  //http://http://195.176.237.62/rodin/x/app/u/solr_interface/solr_bridge.php?path=select&qs=base64endocedstuff;
  global $HOST, $SOLR_BRIDGE;
  
//  print "<br>HOST: $HOST";
//  print "<br>SOLR_BRIDGE: $SOLR_BRIDGE";
  
  if ($HOST<>localhost && strstr($solr_query_url,'localhost'))
  {
    //print "NEED OF SUBST....";
    $BRIDGE=str_replace('localhost:8885',$HOST,$solr_query_url);
    //http://195.176.237.62/solr/rodin_result/select?q=sid:20121205.162229.682.2%20wdatasource:/rodin/x/app/w/RDW_ArXiv.rodin&rows=10&wt=xml&fl=score,*&omitHeader=true
    $PATTERN="/(.*)\/(.*)\/(.*)\?(.*)/";
    if (preg_match($PATTERN,$BRIDGE,$match))
    {
      $collection=$match[2];
      $method=$match[3];
      $qs=$match[4];
    }          

    //$qs64=base64_encode($qs);
    $BRIDGE = "$SOLR_BRIDGE?coll=$collection&method=$method&$qs";

//    print "<br>path: $path";
//    print "<br>qs: $qs";
//    print "<br>BRIDGE: $BRIDGE_NICE";

  }  
  else
    $BRIDGE = $solr_query_url;
  return $BRIDGE;
}
?>