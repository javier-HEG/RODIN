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
    print "<br>solr_client_init(): System error (Wrong SOLR connect parameters): solr_client_init(solr_host=$solr_host, solr_port=$solr_port, solr_path=$solr_path)<br>";
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



function solr_synch_update($sid='default_sid',$txt,&$client,&$documents,$needsynchlog=false,$needDEBUGprecision=false)
################################################
#
# Several processes are synched on a file "$sid.lock"
# no lock if $sid==false or ''
{
  global $SOLR_RODIN_LOCKDIR;
  $want_label_dump=false;
  if ($needsynchlog)
  {
    $LOGfilename="$SOLR_RODIN_LOCKDIR/index.LOG.txt";
   
    $log=fopen($LOGfilename,"a");
    $now=date("d.m.Y H:i:s").'.'.substr(microtime(false),2,6);
    fwrite($log, "\n\n$now ENTER SYNCH ($sid,$txt)");
    
    if ($needDEBUGprecision)
    {
      $solr_adapter=$client->getAdapter();
      $solr_collection=basename($solr_adapter->getPath());
      fwrite($log, "\n--------------");
      fwrite($log, "\nDOCUMENTS (sid,body or cached info) into collection $solr_collection:");
      fwrite($log, "\n--------------");
      foreach($documents as $doc)
      { fwrite($log, "\n--------------");
        foreach($doc AS $field => $value)
        {
          if (is_array($value))
          {
            foreach($value as $x)
            {
              fwrite($log, "\n$field: ".$x);
              if($want_label_dump) fwrite($log, "\n$field: ".string_to_ascii($x));
            } 
          }  
          else 
          {
            fwrite($log, "\n$field: ".$value);
            if($want_label_dump) fwrite($log, "\nanalyse: ".string_to_ascii($value));

          }
        } 
      } 
      fwrite($log, "\n\n--------------");
    } 
  }

  if ($sid) wait2lock_token($sid);

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


  if ($sid) unlock_token($sid); //give free to next one


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
    $h=fopen($lockfilename,"a");
    if (!$h) 
    {
    print "<br>Could not create lockfile $lockfilename! Check permissions/paths? (execution halted)";
    filesystempermissionanalyse($lockfilename);
    exit;
  } }
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
  global $HOST, $SOLR_BRIDGE;
  
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


// System for quickly getting init inside loops
$SOLRCLIENT=null;
function init_SOLRCLIENT($collection_name,$errortext_not_init)
{
  global $SOLR_RODIN_CONFIG;
  global $SOLARIUMDIR;
  global $SOLRCLIENT;
	
  $resultNumber=0;
  #USE SOLR COLLECTION 'rodin_result':
  if (! $SOLRCLIENT)
  {
    $solr_host   =$SOLR_RODIN_CONFIG[$collection_name]['adapteroptions']['host'];
    $solr_port   =$SOLR_RODIN_CONFIG[$collection_name]['adapteroptions']['port'];
    $solr_path   =$SOLR_RODIN_CONFIG[$collection_name]['adapteroptions']['path'];
    $solr_core   =$SOLR_RODIN_CONFIG[$collection_name]['adapteroptions']['core'];
    $solr_timeout=$SOLR_RODIN_CONFIG[$collection_name]['adapteroptions']['timeout'];
    $solr_cache_expiry=$SOLR_RODIN_CONFIG[$collection_name]['rodin']['cache_expiring_time_hour']; 

  //    print "<br>saveRodinResultsInResultsSOLR SOLARIUMDIR=$SOLARIUMDIR";
  //    print "<br>saveRodinResultsInResultsSOLR SOLR_RODIN_CONFIG:<br>"; var_dump($SOLR_RODIN_CONFIG);

    if ((! ($SOLRCLIENT=solr_client_init($solr_host,$solr_port,$solr_path,$solr_core,$solr_timeout))))
    { print $errortext_not_init; } 
  } 

  return $SOLRCLIENT;
}




function string_to_ascii($string)
{
    $ascii = NULL;
    for ($i = 0; $i < strlen($string); $i++) 
    { 
    	$ascii .=' '. $string[$i].'('. ord($string[$i]).')'; 
    }
    
    return($ascii);
}





/*
 * Tests if there SOLR responds
 * => inner http services
 */
function test_solr_connected($collections)
{
  $connected=false;
  $problem='';
 	//print "<br>test_solr_connected()";
	
  foreach($collections as $collection)
  {
  	//print "<br>test_solr_connected($collection)";
    if (($SOLRCLIENT = init_SOLRCLIENT($collection,'test_solr_connected system error init SOLRCLIENT')))
    {
      $ping = $SOLRCLIENT->createPing();
      try
      {
        $SOLRCLIENT->ping($ping);
        $connected= true;    
      } 
      catch(Solarium_Exception $e)
      {
        $connected= false;
        $problemtext="The following SOLR collections seems not to respond: $collection";
        return array($problemtext,$connected);
      }
    }
    else
    {
      $connected= false;
      $problemtext="The following SOLR collection seems not to respond: \"<b>$collection</b>\"";
      return array($problemtext,$connected);
    } 
  }
  
  return array('',true);
}





/*
 * used by SRC components to initialize
 * Tests if there is data in the SOLR collection
 */
function solr_collection_empty($collection)
{
   if (($SOLRCLIENT = init_SOLRCLIENT($collection,'solr_collection_empty system error init SOLRCLIENT')))
    {
      // get a select query instance
      $query = $SOLRCLIENT->createSelect();
      $query->setQuery(('*'));

      // set start and rows param (comparable to SQL limit) using fluent interface
      $query->setStart(0)->setRows(0); // wee need enaugh data ... ;-)

      // this executes the query and returns the result
      $resultset = $SOLRCLIENT->select($query);
      $noofresults=$resultset->getNumFound();
      
      //print "$noofresults noofresults found!!";
      
      $empty=($noofresults==0);
    }
   
    return $empty;
} // empty






/*
 * Tests if there is data in the SOLR collection
 */
function solr_collection_http_access($collection='rodin_search')
{
  global $SOLR_RODIN_CONFIG;
  //Try to open a solr stream
  $collection='';

  $queries = array();

  $solr_user= $SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['user'];
  $solr_host= $SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['host']; //=$HOST;
  $solr_port= $SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['port']; //=$SOLR_PORT;
  $solr_path= $SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['path']; //='/solr/rodin_result/';

  #Fetch result from SOLR
  $solr_select= "http://$solr_host:$solr_port$solr_path".'select?';

  try {

    $solr_result_query_url=$solr_select
        ."wt=xml"
        ."&q=*"
        ."&fl=query"
        ."&omitHeader=true"
        ."&rows=1"
        ;
    $filecontent=file_get_contents($solr_result_query_url);
    $solr_sxml= simplexml_load_string($filecontent);
//    print "<hr>collect_queries_tag_SOLR: <a href='$solr_result_query_url' target='_blank'>$solr_result_query_url</a><br>";
//    print "<hr>SOLR_CONTENT: <br>(((".htmlentities($filecontent).")))";
//    print "<hr>SOLR_RESULT: <br>"; var_dump($solr_sxml);
  }
  catch (Exception $e) {
    return array("Could not locally http access SOLR",false);
  }
  
  if (!trim($filecontent)) {
      return array("Could not locally http access SOLR",false);
  }
  
  return array('',true);
} 





/**
 * Removes in $collection all docs matching $DELETEQUALIFICATION
 * e.g.: $DELETEQUALIFICATION="id:$sid*"
 */
function 	solr_delete_documents($collection,$DELETEQUALIFICATION,$DEBUG=0)
{
	global $SOLR_RODIN_CONFIG;
	if ($DEBUG) print "<br>solr_delete_documents($collection,$DELETEQUALIFICATION): ";
	$solr_user= $SOLR_RODIN_CONFIG[$collection]['adapteroptions']['user'];
  $solr_host= $SOLR_RODIN_CONFIG[$collection]['adapteroptions']['host']; //=$HOST;
  $solr_port= $SOLR_RODIN_CONFIG[$collection]['adapteroptions']['port']; //=$SOLR_PORT;
  $solr_path= $SOLR_RODIN_CONFIG[$collection]['adapteroptions']['path']; //='/solr/rodin_result/';
  if (($client = init_SOLRCLIENT($collection,'solr_collection_empty system error solr_delete_documents')))
  {
  	/*
  	$update = $client->createUpdate();
		$update->addDeleteQuery($DELETEQUALIFICATION);
		$update->addCommit();
		$result = $client->update($update);
		*/
		//Delete is not executed in solarium here (but when separated!!)
		//Try to use a URL command instead of a solarium command
		//curl http://localhost:8983/solr/update?commit=true -H "Content-Type: text/xml" --data-binary '<delete><query>*:*</query></delete>'
		$DELETEURL="http://$solr_host:$solr_port/solr/$collection/update?commit=true&stream.body=".("<delete><query>$DELETEQUALIFICATION</query></delete>")."";
		$fileres=get_file_content($DELETEURL);
		
		if ($DEBUG) 
		{
			//var_dump($SOLR_RODIN_CONFIG[$collection]);
			print "<br>RESULT OF <hr>".htmlentities($DELETEURL)." :<hr> ((".htmlentities($fileres)."))";
			//print "<br>STATUS 2: ".$result->getStatus();
		}
	}
}




/*
 * Used for updating in SOLR
 */
function encode4solr($txt)
{
  return utf8_encode(html_entity_decode($txt));
}


?>