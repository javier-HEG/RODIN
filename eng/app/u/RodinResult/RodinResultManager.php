<?php

//FRI: More flexible way to load code indep from position

$filename='u/RodinResult/RodinArticleResult.php'; $maxretries=10;
####################################################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}

$filename='u/RodinResult/RodinBookResult.php'; $maxretries=10;
####################################################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}

$filename='u/RodinResult/RodinUrlResult.php'; $maxretries=10;
####################################################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}

$filename='u/RodinResult/RodinPictureResult.php'; $maxretries=10;
####################################################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}

$filename='u/FRIdbUtilities.php'; $maxretries=10;
####################################################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}




/**
 * A class responsible for managing the results collected by the
 * widgets. It works also as a builder for the instances that will
 * hold single results, a builder based on the type of result to
 * be saved.
 * 
 * @author Javier Belmonte
 */
class RodinResultManager {
	const RESULT_TYPE_BASIC = 0;
	const RESULT_TYPE_ARTICLE = 1;
	const RESULT_TYPE_BOOK = 2;
	const RESULT_TYPE_PICTURE = 3;
	const RESULT_TYPE_MAP = 4;
	const RESULT_TYPE_URL = 5;
	
	public static function buildRodinResultByType($resultType = RodinResultManager::RESULT_TYPE_BASIC) {
		
		switch ($resultType) {
			case RodinResultManager::RESULT_TYPE_ARTICLE:
				return new RodinArticleResult();
				break;
			case RodinResultManager::RESULT_TYPE_BOOK:
				return new RodinBookResult();
				break;
			case RodinResultManager::RESULT_TYPE_URL:
				return new RodinUrlResult();
				break;
			case RodinResultManager::RESULT_TYPE_PICTURE:
				return new RodinPictureResult();
				break;
			default:
				return new BasicRodinResult();
				break;
		}
	}



  public static function saveRodinSearch($sid, $query) {
    global $RESULTS_STORE_METHOD;
    switch($RESULTS_STORE_METHOD)
    {
      case 'mysql':
            RodinResultManager::saveRodinSearchInSearchTable($sid, $query);
            break;
      case 'solr':
            RodinResultManager::saveRodinSearchInSOLR($sid, $query);
            break;
    }
  }



  public static function saveRodinSearchInSOLR($sid, $query)
  {
    require_once("../u/SOLRinterface/solr_interface.php");
    global $SOLR_RODIN_CONFIG;
    global $RODINSEGMENT;
    global $SOLARIUMDIR;
    global $USER;
    $resultNumber=0;
    #USE SOLR COLLECTION 'rodin_result':
    $solr_host   =$SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['host'];
    $solr_port   =$SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['port'];
    $solr_path   =$SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['path'];
    $solr_core   =$SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['core'];
    $solr_timeout=$SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['timeout'];

//    print "<br>saveRodinResultsInResultsSOLR SOLARIUMDIR=$SOLARIUMDIR";
//    print "<br>saveRodinResultsInResultsSOLR SOLR_RODIN_CONFIG:<br>"; var_dump($SOLR_RODIN_CONFIG);

    if (($client=solr_client_init($solr_host,$solr_port,$solr_path,$solr_core,$solr_timeout)))
    {
        
       $document = BasicRodinResult::toInsertSOLRsearch($client, $sid, $query, $RODINSEGMENT, $USER);

        //print "<hr>SAVING DOCUMENTS:<br>"; var_dump($documents);
        $documents= array($document);
        solr_synch_update($sid,$solr_path,$client, $documents);
    }
    else {
      print "saveRodinSearchInSOLR system error init SOLR client";
    }

  }



	public static function saveRodinSearchInSearchTable($sid, $query) {
		try {
			$db = new RODIN_DB();
			$conn = $db->DBconn;

			// Save search to 'search' table if no other widget has already
			$countSidInSearchTable = "SELECT count(*) FROM SEARCH WHERE SEARCH.sid = '$sid';";
			$sid_ret = mysql_query($countSidInSearchTable, $conn);

			if ($sid_ret) {
				$sid_exists = (mysql_result($sid_ret, 0) == 1);
			}

			if ($sid_exists) {} // Do nothing, results are already in DB
			else { // Insert query results in the DB
				$dbq = addslashes($query);
				$insertSearchInDb = "INSERT INTO SEARCH (`sid`,`query`) values('$sid','$dbq');";

				mysql_query($insertSearchInDb, $conn);
					
				if (mysql_affected_rows() < 1)
					throw(New Exception(mysql_error($DBconn)."<hr>Query:".$insertSearchInDb."<br><br>"));
			}
			
			$db->close();
		} catch (Exception $e) {
			print "RodinResultManager EXCEPTION: $e";
		}
		
	}
	
	public static function saveRodinResults($results, $sid, $datasource, $timestamp='') {
		$modified = 0;
    global $RESULTS_STORE_METHOD;
		
		if (is_array($results) && count($results) > 0) {
			try {
				switch($RESULTS_STORE_METHOD)
        {
          case 'mysql': 
        				RodinResultManager::saveRodinResultsInResultsTableMySQL($results, $sid, $datasource, $timestamp, $conn);
                break;
          case 'solr':
        				RodinResultManager::saveRodinResultsInResultsSOLR($results, $sid, $datasource, $timestamp);
                break;
        }
			} catch (Exception $e) {
				print "RodinResultManager EXCEPTION: $e";
			}
		}
		
		return $modified;
	}


  public static function saveRodinResultsInResultsSOLR(&$results, $sid, $datasource, $timestamp)
  {
    //print "<br>saveRodinResultsInResultsSOLR(<b> $sid, $datasource</b>)";
    require_once("../u/SOLRinterface/solr_interface.php");
    global $SOLR_RODIN_CONFIG;
    global $RODINSEGMENT;
    global $USER;
    global $SOLARIUMDIR;
    $resultNumber=0;
    #USE SOLR COLLECTION 'rodin_result':
    $solr_host   =$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['host'];
    $solr_port   =$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['port'];
    $solr_path   =$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['path'];
    $solr_core   =$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['core'];
    $solr_timeout=$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['timeout'];

//    print "<br>saveRodinResultsInResultsSOLR SOLARIUMDIR=$SOLARIUMDIR";
//    print "<br>saveRodinResultsInResultsSOLR SOLR_RODIN_CONFIG:<br>"; var_dump($SOLR_RODIN_CONFIG);

    if (($client=solr_client_init($solr_host,$solr_port,$solr_path,$solr_core,$solr_timeout)))
    {
        // Code to insert the number of results in xPointer -1
        $numberOfResults = count($results);

        $resultNumber = 0;
        $documents = array();

        foreach ($results as $result)
        {
          // print "\n<hr><br>RESULT: ";
          // var_dump($result);

          $documents[] = $result->toInsertSOLRdocument( $client, 
          																							$result->getId(), // if set -> update result record in SOLR
                                                        $sid, 
                                                        $datasource, 
                                                        str_pad($resultNumber, $numberOfResultsOrderOfMagnitude + 1, '0', STR_PAD_LEFT), 
                                                        $RODINSEGMENT, 
                                                        $USER,
                                                        $timestamp );
          
          $resultNumber++;
        } // foreach record

        //print "<hr>SAVING DOCUMENTS:<br>"; var_dump($documents);
        solr_synch_update($sid,"$datasource",$client,$documents);

    }
    else {
      print "saveRodinResultsInResultsSOLR system error init SOLR client";
    }
 
		return $resultNumber;
	}
  

	public static function saveRodinResultsInResultsTableMySQL(&$results, $sid, $datasource, $timestamp, $conn = null) {
		if ($conn == null) {
			$db = new RODIN_DB();
			$safeConn = $db->DBconn;
		}
		// Code to insert the number of results in xPointer -1
		$numberOfResults = count($results);
		$sql = 'INSERT INTO result (`sid`,`datasource`,`xpointer`,`node`,`follow`,`attribute`,`type`,`value`,`url`,`visible`) '
			. "values ('$sid','$datasource','-1','','','','integer','$numberOfResults','','')";
		
		$numberOfResultsOrderOfMagnitude = floor(log($numberOfResults)/log(10));
		
		// Append the code to insert each result in results
		$resultNumber = 0;
		foreach ($results as $result) {
			$sql .= $result->toInsertSqlCommand($sid, $datasource, str_pad($resultNumber, $numberOfResultsOrderOfMagnitude + 1, '0', STR_PAD_LEFT), ($conn == null ? $safeConn : $conn));
			$resultNumber++;
		}

    $sql.=';';
    
    mysql_query($sql, $safeConn);
		$modified = mysql_affected_rows();

    if ($modified < 0) {
      return mysql_errno() . ": " . mysql_error();
    }

    //print "$modified lines with <br>$sql"; exit;
    
		
		
	}
	

  public static function getRodinResultsForASearch($sid,$datasource='',$internal=true,$external=false) 
  {
  	global $RESULTS_STORE_METHOD;
		global $aggView;
	  switch($RESULTS_STORE_METHOD)
    {
      case 'mysql':
            return RodinResultManager::getRodinResultsForASearchFromDB($sid);
            break;
      case 'solr':
			      $aggView=true;
            return RodinResultManager::getRodinResultsFromSOLR($sid,$datasource,$internal,$external,$slrq_base64='') ;
            break;
    }
	}




  public static function getRodinResultsForASearchFromDB($sid) 
  {
		$allResults = array();
		
		try {
			$db = new RODIN_DB();
			$conn = $db->DBconn;
			
			$query = "SELECT * FROM result WHERE sid = '$sid' ORDER BY datasource ASC, xpointer ASC";
			$resultset = mysql_query($query, $conn);
			
			if ($resultset)	{
				while ($row = mysql_fetch_assoc($resultset)) {
					$pointer = explode('.', $row['xpointer']);
					$pointerBase = intval($pointer[0]);
					$pointerRemainder = count($pointer) > 1 ? intval($pointer[1]) : -1;
					$datasource = $row['datasource'];
					
					if ($pointerBase > -1) {
						// A new result to be loaded
						if ($pointerRemainder == 0) {
							$result = RodinResultManager::buildRodinResultByType(intval($row['value']));
							$allResults[$datasource . '-' . $pointerBase] = $result;
						} else {
							$result = $allResults[$datasource . '-' . $pointerBase];
							$attribute = $row['attribute'];
							switch ($attribute) {
								case 'title':
									$result->setTitle($row['value']);
								break;
								case 'authors':
									$result->setAuthors($row['value']);
								break;
								case 'date':
									$result->setDate($row['value']);
								break;
								case 'urlPage':
									$result->setUrlPage($row['value']);
								break;
								default:
									$result->setProperty($attribute, $row['value']);
								break;
							}
						}
					}
				}
			}
			
			$db->close();
		} catch (Exception $e) {
			print "RodinResultManager EXCEPTION: $e";
		}
		
		return array_values($allResults);
	}




public static function getRodinResultsFromResultsTable($sid, $datasource) {
		$allResults = array();

    try {
			$db = new RODIN_DB();
			$conn = $db->DBconn;

			$query = "SELECT * FROM result WHERE sid = '$sid' AND datasource='$datasource' ORDER BY xpointer ASC";
			$resultset= mysql_query($query, $conn);

      //print "Getting results to $sid ... $query";
      
			if ($resultset)	{
				while ($row = mysql_fetch_assoc($resultset)) {
					$pointer = explode('.', $row['xpointer']);
					$pointerBase = intval($pointer[0]);
					$pointerRemainder = count($pointer) > 1 ? intval($pointer[1]) : -1;
					$datasource = $row['datasource'];

					if ($pointerBase > -1) {
						// A new result to be loaded
						if ($pointerRemainder == 0) {
							$result = RodinResultManager::buildRodinResultByType(intval($row['value']));
							$allResults[$datasource . '-' . $pointerBase] = $result;
						} else {
							$result = $allResults[$datasource . '-' . $pointerBase];
							$attribute = $row['attribute'];
							switch ($attribute) {
								case 'title':
									$result->setTitle($row['value']);
								break;
								case 'authors':
									$result->setAuthors($row['value']);
								break;
								case 'date':
									$result->setDate($row['value']);
								break;
								case 'urlPage':
									$result->setUrlPage($row['value']);
								break;
								default:
									$result->setProperty($attribute, $row['value']);
								break;
							}
						}
					}
				}
			}

			$db->close();
		} catch (Exception $e) {
			print "RodinResultManager EXCEPTION: $e";
		}

		return array_values($allResults);
	}

/*
 * Retrieves all results for a given $datasource
 * In case $datasource is not set, results for all widgets are retrieved 
 * The latter is used for the aggregated view
 */
public static function getRodinResultsFromSOLR($sid,$datasource,$internal,$external,$slrq_base64) 
{
		$DEBUG=0;
		$max=10;
		
		$filenamex='/u/SOLRinterface/solr_interface.php';
		
		for ($x=1,$updir='';$x<=$max;$x++,$updir.="../"){ 
			if (file_exists("$updir$filenamex")) 
			{require_once("$updir$filenamex"); break;}}
		
    global $aggView;
		global $webservice;
		global $WANT_RFLAB, $RDFSEMEXPLABURL, $RDFIZEURL ; // DEBUG
		global $SOLR_RODIN_CONFIG;
		global $RODINUTILITIES_GEN_URL;
    global $SOLR_MLT_MINSCORE;
    global $USER;
    global $RODINSEGMENT;
    global $m; if($m==0) $m=1000; //we do not know how may rows are to retrieve
    $rank_format_factor = 1000000;
		
		$LOD = $external && $internal
    			 ?'*'
    			 :($external?1:0);
    
    $RDFLAB_ICON = "$RODINUTILITIES_GEN_URL/images/icon_arrow_right2.png";
		$RDFLAB_LINK="<img src='$RDFLAB_ICON' width='15'>";
    
    $slrq='';
    if ($slrq_base64)
      $slrq=base64_decode($slrq_base64);
    
    $solr_user= $SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['user'];
    $solr_host= $SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['host']; //=$HOST;
    $solr_port= $SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['port']; //=$SOLR_PORT;
    $solr_path= $SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['path']; //='/solr/rodin_result/';

    if ($DEBUG)
		{
		   print "\n<br>solr_user: $solr_user";
		   print "\n<br>solr_host: $solr_host";
		   print "\n<br>solr_port: $solr_port";
		   print "\n<br>solr_path: $solr_path";
		   print "\n<br>RODINSEGMENT: $RODINSEGMENT";
		   print "\n<br>datasource: $datasource";
		   print "\n<br>sid: $sid";
		   print "\n<br>USER: $USER";
		   print "\n<br>USER_ID: $USER_ID";
		}
    #Fetch result from SOLR using even $slrq (handler and some parameters) if set instead of the standard Handler
    $EVTL_datasource=($datasource<>'')?"%20wdatasource:$datasource":'';
		
    if ($slrq=='' && $sid<>'')
        $solr_select= "http://$solr_host:$solr_port$solr_path"
                       ."select?q=sid:$sid{$EVTL_datasource}%20user:$USER%20seg:$RODINSEGMENT%20lod:$LOD&rows=$m";
    else
        $solr_select= "http://$solr_host:$solr_port$solr_path".$slrq;
      
    $MLT = (strstr($solr_select,'/mlt'));
    //print "MLT=strstr('/mlt',$solr_select)=$MLT ";
    
		$allResults = array();
		try {
 
     //$solr_result_query_url=$solr_select."wt=xml&q=sid:$sid&wdatasource:$datasource&qt=/lucid&req_type=main&user=$solr_user&role=DEFAULT";
			$solr_result_query_url=$solr_select
                              ."&wt=xml"
                              ."&fl=score,*"
                            //  ."&omitHeader=true"
                              ; 
      if ($DEBUG) print "<hr>SOLR SELECT: <br>(((".htmlentities($solr_select).")))";
      if ($DEBUG) print "<hr>SOLR QUERY: <br>(((".htmlentities($solr_result_query_url).")))";
 
      $filecontent=file_get_contents($solr_result_query_url);
      $solr_sxml= simplexml_load_string($filecontent);
			
			
      if (0 && (!$aggView) &&
      		(($RODINSEGMENT=='eng' || $RODINSEGMENT=='x') 
              || ( $RODINSEGMENT=='p' && $USER==4) ) ) //fabio=developer on p, x, st 
      {  
        $solr_real_url=get_solrbridge($solr_result_query_url);
        $EVTL_MLT=($MLT)?" (mlt)":""; 
        print "<a href='$solr_real_url' target='_blank' title='Get SOLR raw data in a new TAB'>raw data</a>$EVTL_MLT";
        $needbr=true;
      }
      
      /**
			 * During development write on each widget a pointer to rdflab
			 * in order to use automatically stored results under sid.
			 */
      if ((!$aggView) && $WANT_RFLAB &&
      		(($RODINSEGMENT=='eng' || $RODINSEGMENT=='x' || $RODINSEGMENT=='st') 
              || ( $RODINSEGMENT=='p' && $USER==4) ) ) //fabio=developer on p, st 
      {
      	$LABPARAMS="?sid=$sid&datasource=$datasource&listwr=on&user_id=".$_SESSION['user_id']."&username=".$_SESSION['username'];  
      	$RDFIZEURL.=$LABPARAMS;
      	$RDFSEMEXPLABURL.=$LABPARAMS;
        print "&nbsp;<a href='$RDFIZEURL' target='_blank' title='Click to open RDFIZE (new efficient prototype) on these results in a new TAB'>rdfize $RDFLAB_LINK</a>";
	     	$needbr=true;
			}
			if ($needbr) print "<br>";
      
      if ($DEBUG) print "<hr>Got from SOLR: (((".htmlentities($filecontent).")))";
			if ($DEBUG) {print "<hr>XML: "; var_dump($solr_sxml);}
			
      //if (!$owner) $PRECISATION="[@name='response']";
      $DOCS = $solr_sxml->xpath("/response/result$PRECISATION/doc"); //find the doc list results
      if ($DEBUG) {print "<hr>".count($DOCS)." SOLR_DOCS: <br>"; var_dump($solr_sxml);}
       
      $CNT=0;
      $NO_OF_DISPLAYED_RESULTS=0;
      $dedup_hash= array();
      foreach($DOCS as $DOC)
      {
      	$rank=null;
				$explanation=null;
        $lod=null;
          
        if ($NO_OF_DISPLAYED_RESULTS>$m)
             break; // stop parsing and displyaing results
          
        $CNT++;
        $FORGET_RESULT=false;
        $id='';
        $row=array();
	      if ($DEBUG) {print "<hr>DOC:"; print "<hr> SOLR_DOC: <br>"; var_dump($DOC);}

        foreach($DOC->children() as $ATTRVAL)
        {
        	//print "<hr> ATTRVAL: <br>"; var_dump($ATTRVAL);
          $attributes=$ATTRVAL->attributes();
          //print "<hr> ATTRIBUTES: <br>"; var_dump($attributes);
          $name=$attributes['name'];

          if (count($ATTRVAL->children()))
          {
            foreach($ATTRVAL->children() as $AC)
            {
              if($DEBUG) {print "<br> CHILD: <br>"; var_dump($AC);}
              $value=$AC;
            }
          }
          else
            $value=$ATTRVAL[0];

          if ($name=='id') // we need the SOLR id...
            $id=$value;
					else if ($name=='rank')
						$rank = floatval($value);
					else if ($name=='explanation')
						$explanation=$value;
					else if ($name=='lod')
					{
						$lod=intval($value);
          }
          else if ($name=='wdatasource')
          {
          	$wdatasource=$value;
            $owner= ($datasource==$value);
            $reference= ($CNT==1 && $owner);
          } //  $name=='wdatasource'
          else if ($name=='body') // compute hash deduplication discarding new result 
          {
            if ($MLT)
            {
              $md5_body=md5($value);
							if ($DEBUG)
							{
		            print "<hr>$value";
		            print "<br>$CNT: $md5_body";
							}
              if (! ($dedup_hash{$md5_body})) // still a new body value?
              {
                if($DEBUG) print "<br> NEW RESULT";
                $dedup_hash{$md5_body}=true;
								$FORGET_RESULT=false;
              } 
              else // known body -> cancel result display
              {  if($DEBUG)  print "<br> OLD RESULT -> FORGET IT";
                 $FORGET_RESULT=true; 
              }
            }
          } // body
          #####################################################
          # Name/Value pair of result here available
          // print "<br>$name = <b>$value</b>";
          # Exclude SOLR fields
          else
          if (
              $name<>'_version_'
            &&$name<>'timestamp'
              )
            { //We have to utf8_decode, since we coded on storing
              $row[$name.'']=utf8_decode($value).''; 
            }
          #####################################################
        } // foreach DOC->Children()

        if($DEBUG) {print "<hr>ENDLOOP ROW: <br>"; var_dump($row);}

        #################################
        #
        # Process result:
        #
        $pointer = explode('.', $row['xpointer']);
        $pointerBase = intval($pointer[0]);
        $pointerRemainder = count($pointer) > 1 ? intval($pointer[1]) : -1;
        
        if ($owner || $aggView || $webservice)
        {
          $result = RodinResultManager::buildRodinResultByType(intval($row['type']));
        	if ($DEBUG) print "<hr> SETTING RES TYPE to ".print_r($result);  

          $result->setSid($sid);
          $result->setId($id);
					$result->setProperty('datasource', $wdatasource); 
          $result->setLod($lod); //mark result as coming from LOD sources
          $result->setRank($rank);
          $result->setExplanation($explanation);
          					
          foreach($row as $attribute=>$value)
          {
          	//print "<br>$attribute=>$value";
            //$result = $allResults[$pointerBase];
            switch ($attribute) {
              case 'score': 
              	if ($DEBUG) print "<br>Score: $value reference=$reference MLT:$MLT";
                if ($MLT) // only on mlt we are interested in scoring
                {
                  if ($reference)
                    $result->setScore("Reference");
                  else
                  { // SET ONLY IF SOME RELEVANCE FOUND
                    //if ($value >= $SOLR_MLT_MINSCORE)
                        $result->setScore(($value));
                    //else $FORGET_RESULT=true;
                  }
                }
              break;
              case 'title':
                $result->setTitle($value);
              break;
              case 'authors':
                $result->setAuthors($value);
              break;
              case 'date':
                $result->setDate($value);
              break;
              case 'urlPage':
                $result->setUrlPage($value);
              break;
							case 'wdscachetimestamp':
                $result->setCacheTimeStamp($value);
              break;
							
              default:
              if (trim($attribute))
              {
                //print "<br>default attribute($attribute)=value($value)";
                $result->setProperty($attribute, $value);
              }
              break;
            } // switch attribute
          } // foreach $row value

           //print "<br>Setting new result to pointerBase=$pointerBase";
          if (!$FORGET_RESULT)
          {
          	if($DEBUG) {print "<hr>RESULT: <br>"; var_dump($result);}
          	//Create an ID which keeps tracks of the ranking
          	$rank = number_format($rank,6); // take 6 decimals pad 12 padded positions
          	$rank_formatted=str_pad(floor($rank * 1000000), 12, '0', STR_PAD_LEFT);
          	$resseq_formatted=str_pad($NO_OF_DISPLAYED_RESULTS, 4, '0', STR_PAD_LEFT);
          	$result_show_id="r_{$rank_formatted}_{$resseq_formatted}";
						if ($debug) print "\n<!--result_show_id (rank=$rank): $result_show_id (".$result->getTitle().")-->\n";
						$tmp_allResults{$result_show_id} = $result;						
            $NO_OF_DISPLAYED_RESULTS++;
          }
					else {
						if ($DEBUG) {
							print "<hr>FORGOTTEN RESULT: <br>"; var_dump($result);
						}
					}
        } // owner
      } // foreach DOCS
     
      //foreach($dedup_hash as $k=>$v) print "<br>$k=>$v"; //debug - show hash
      //Since $allResults should be read both from javascript and php
      //reorder the dataset so that it is already sorted when looped.
      if($DEBUG)
			{
	      print "<hr>PRESORT results:<br> "; var_dump($tmp_allResults); 
				while (list($rrrank, $result) = each($tmp_allResults))
				{
					print "<hr>RRRR Result: "; var_dump($result);
					print "<hr>RRRR $rrrank for (".$result->getTitle()."): ".$result->getScore();
				}
	    }

      //Sort results on score top biggest
	    if (count($tmp_allResults) > 1)
			{
				if ($DEBUG) print "<hr>RESORTING<hr>";
				krsort($tmp_allResults);
      	//reset($tmp_allResults);
			}
			
			if($DEBUG)
			{
	      print "<hr>AFTERSORT results:<br> "; var_dump($tmp_allResults); 
	      while (list($rrrank, $result) = each($tmp_allResults))
				{
					print "<hr>AAAA Result: "; var_dump($result);
					print "<hr>AAAA $rrrank for (".$result->getTitle()."): ".$result->getScore();
				}
			}
			
			if (count($tmp_allResults))
			{
				if ($DEBUG) {print "<br> FINALLY:<br>"; var_dump($tmp_allResults);print "<br>";}
				foreach($tmp_allResults as $rrrank=>$result)
				{
					if ($DEBUG) {print "<hr>FFFF ADDING Result: "; var_dump($result);}
					
	  	    $allResults[]=$result;
				}
			}
			 
		} catch (Exception $e) {
			print "RodinResultManager EXCEPTION: $e";
		}
		
		
		if ($DEBUG)
		{
			print "<hr> RETURNING: "; var_dump($allResults);
		} 
			 
		//exit;
		return $allResults;
	}





  
	public static function getRodinResultTypeName($resultType) {
		switch ($resultType) {
			case RodinResultManager::RESULT_TYPE_ARTICLE:
				return 'article';
				break;
			case RodinResultManager::RESULT_TYPE_BOOK:
				return 'book';
				break;
			case RodinResultManager::RESULT_TYPE_URL:
				return 'url';
				break;
			case RodinResultManager::RESULT_TYPE_PICTURE:
				return 'picture';
				break;
			case RodinResultManager::RESULT_TYPE_MAP:
				return 'map';
				break;
			case RodinResultManager::RESULT_TYPE_BASIC:
			default:
				return 'basic';
		}
	}
	
	public static function getRodinResultTypeColor($resultType) {
		switch ($resultType) {
			case RodinResultManager::RESULT_TYPE_ARTICLE:
				if ($lod)
					return 'red';
				else
					return '#9fc5e8';
				break;
			case RodinResultManager::RESULT_TYPE_BOOK:
				return '#b6d7a8';
				break;
			case RodinResultManager::RESULT_TYPE_URL:
				return '#d5a6bd';
				break;
			case RodinResultManager::RESULT_TYPE_PICTURE:
				return '#b4a7d6';
				break;
			case RodinResultManager::RESULT_TYPE_MAP:
				return '#ffe599';
				break;
			case RodinResultManager::RESULT_TYPE_BASIC:
			default:
				return 'gray';
		}
	}

	/**
	 * Requires that the RodinResultSet.js file is included in page where
	 * these results are rendered.
	 */
	public static function renderAllResultsInWidget($sid, $datasource, $slrq, $render) {
    global $RESULTS_STORE_METHOD;
    switch($RESULTS_STORE_METHOD)
    {
      case 'mysql': 
            $allResults = RodinResultManager::getRodinResultsFromResultsTable($sid, $datasource);
            break;
      case 'solr':
        		$allResults = RodinResultManager::getRodinResultsFromSOLR($sid, $datasource, true, true, $slrq);
    }
		if (count($allResults) > 0) {

      //print "<br>".count($allResults)." results :<br>";
			$resultCounter = 1;
			foreach ($allResults as $result) {

        //print "<hr>RESULT :<br>"; var_dump($result);

        // Is result coming from a cache?
        $timestamp = $result->getCacheTimeStamp();
        //Is timestamp set? => mark container as cached
				$CACHED=($timestamp)?" wdscached":'';
        print "<div class='oo-result-container{$CACHED}' >";
				
				$resultIdentifier = str_replace('.rodin', '', substr($datasource, strrpos($datasource, '/') + 1)) . '-' . $resultCounter;

				print $result->headerDiv($resultIdentifier,$result->getLod());
				print $result->contentDiv($resultIdentifier);
				print $result->jsScriptAndRender($resultIdentifier, $resultCounter, $sid);
				
				print '</div>';
				
				$resultCounter++;
			}
			
			print '<script type="text/javascript">widgetResultSet.askResultsToRender("' . $render . '")</script>';
		} else {
			print '<p class="widgetResultCount">' . lg('lblGotNoResults') . '</p>';
		}
	}

	public static function renderAllResultsInOwnTab($sid, $datasource, $slrq) {
		RodinResultManager::renderAllResultsInWidget($sid, $datasource, $slrq, 'all');
		return true;
	}
	
	
	
	public static function create_rodinResult_for_lod(  $rodin_result_type,
																											$rank,
																											$explanation,
																											$title,
																											$description,
																											$date_created,
																											$source_url,
																											$identifier_url,
																											&$authors,  // $authorFieldNames = array('creator'=>, 'person'=>, 'contributor'=>);
																											&$subjects  )
	{
		
		$debug=0;
		global $RDFLOG;
		
		if ($debug)
		{
			$RDFLOG.="<br>create_rodinResult($rodin_result_type)"
			."<br>title=($title)"
			."<br>description=($description)"
			."<br>date_created=($date_created)"
			."<br>source_url=($source_url)"
			."<br>identifier_url=($identifier_url)"
			."<br>authors=["; if (is_array($authors)&&count($authors)) foreach($authors as $kind=>$values) { $RDFLOG.="$kind: "; foreach($values as $a) $RDFLOG.="$a,"; }
			$RDFLOG.="]<br>subjects=";foreach($subjects as $a) $RDFLOG.="$a,";
			$RDFLOG.="]<br>";
		}
		
		
		switch ($rodin_result_type) {
				case 'article':
					if ($debug)
						$RDFLOG.="<br>create_rodinResult(article ....)";
								// Create the result object
					$singleResult = RodinResultManager::buildRodinResultByType(RodinResultManager::RESULT_TYPE_ARTICLE);
					
					// General fields
					$singleResult->setTitle($title);
					$singleResult->setDate($date_created);
					
					if (isset($source_url)) {
						$singleResult->setUrlPage($source_url);
					} 
					else
					if (isset($identifier_url)) {
						$singleResult->setUrlPage($jsonRecordInfo['identifier_url'][0]);
					} 
					
					$authorArray = array();
					$authorFieldNames = array('creator', 'person', 'contributor');
					foreach ($authorFieldNames as $fieldName) {
						$authorElements = $authors[$fieldName];
						
						if (count($authorElements) > 0) {
							foreach ($authorElements as $author) {
								// Put first name first, remove ','
								$authorArray[] = implode(' ', array_reverse(explode(',', $author)));
							}
							
							break;
						}
					}
					$singleResult->setAuthors(implode(', ', $authorArray));
						
					// Article specific fields
					// TODO Check if it's possible to have multiple abstracts
					$singleResult->setProperty('abstract', $description);
					
					if (isset($jsonRecordInfo['subject']) && is_array($subjects)) {
						$singleResult->setProperty('keywords', implode(', ', $subjects));
					}
					break;
				case 'book':
					// Create the result object
					$singleResult = RodinResultManager::buildRodinResultByType(RodinResultManager::RESULT_TYPE_BOOK);
		
					// General fields
					$singleResult->setTitle($title);
					$singleResult->setDate($date_created);
					
					if (isset($source_url)) {
						$singleResult->setUrlPage($source_url);
					} 
					
					$authorArray = array();
					$authorFieldNames = array('creator', 'person', 'contributor');
					foreach ($authorFieldNames as $fieldName) {
						$authorElements = $authors[$fieldName];
						
						if (count($authorElements) > 0) {
							foreach ($authorElements as $author) {
								// Put first name first, remove ','
								$authorArray[] = implode(' ', array_reverse(explode(',', $author)));
							}
							
							break;
						}
					}
					$singleResult->setAuthors(implode(', ', $authorArray));
						
					// Book specific fields
					$singleResult->setProperty('description', strip_tags($jsonRecordInfo['abstract'][0]));
					
					if (isset($jsonRecordInfo['subject']) && is_array($jsonRecordInfo['subject'])) {
						$singleResult->setProperty('subjects', implode(', ', $jsonRecordInfo['subject']));
					}
					break;
				default:
					// Create a dummmy result object
					$singleResult = RodinResultManager::buildRodinResultByType(RodinResultManager::RESULT_TYPE_BASIC);
					break;
			}
		
		$singleResult->setRank($rank);
		$singleResult->setExplanation($explanation);
		$singleResult->setLod(true);
	
		return $singleResult;
	} // create_rodinResult
	
	
	
	/**
	 * Calls and code search results
	 * Returns a json representation of them
	 */
	public static function get_json_searchresults($sid, $internalresults, $externalresults, $fromResult = 0, $uptoResult = 0)
	{
		$DEBUG=0;
		$ok_to_retrieve=true;
		
		if (!$sid)
		{
			$errortxt='get_json_searchresults() - fatal - no sid provided!';
			$ok_to_retrieve=false;
		}
		if ($DEBUG)
			print "\n\n\n<br> get_json_searchresults($sid, $internalresults, $externalresults)";
		
		if ($ok_to_retrieve)
		{
			$allResults = RodinResultManager::getRodinResultsForASearch($sid,'',$internalresults, $externalresults); 
			$resultCount = count($allResults);
			
			// Both a maximum size and a maximum number of results are set
			if ($fromResult==0 && $uptoResult==0 )
			{
				$resultMaxSetSize = 4;
				$resultMaxLength = 1000000;
				$fromResult = 0;
				$uptoResult = $resultCount;
			}
			if ($DEBUG) print "<br>$resultCount results read... SHOW from $fromResult to < $uptoResult";
			
			$i = $fromResult;
			while ($i <= $uptoResult) 
			{
				$result = $allResults[$i];
				
				if($result)
				{
					$resultCounter = $i + 1;
				
					if ($DEBUG) 
					{
						print "\n\n\n-------\n\nRes nr. $resultCounter \n\n"; var_dump($result); print "\n\n";
					}
				
					$resultIdentifier = 'aggregatedResult-' . $resultCounter . ($suffix != '' ? '_' . $suffix : '');
				
					$singleResult = array();
				
					$singleResult['count'] = $resultCounter;
					$singleResult['url'] = $result->getUrlPage();
					$singleResult['resultIdentifier'] = $resultIdentifier;
				
					$singleResult['headerDiv'] = json_encode($result->headerDiv($resultIdentifier));
					$singleResult['contentDiv'] = json_encode($result->contentDiv($resultIdentifier));
				
					$singleResult['header'] = json_encode($result->htmlHeader($singleResult['resultIdentifier'], $resultCounter, $sid, true));
					$singleResult['minHeader'] = json_encode($resultCounter . '<br />');
				
					$singleResult['minContent'] = json_encode($result->toInWidgetHtml('min'));
					$singleResult['tokenContent'] = json_encode($result->toInWidgetHtml('token'));
					$singleResult['allContent'] = json_encode($result->toInWidgetHtml('all'));
				
				
					if ($DEBUG) {
						
						print "\n\n jsonSingleResult['count']: ".$singleResult['count'];
						print "\n\n jsonSingleResult['url']: ".$singleResult['url'];
						print "\n\n jsonSingleResult['resultIdentifier']: ".$singleResult['resultIdentifier'];
						print "\n\n jsonSingleResult['headerDiv']: ".$singleResult['headerDiv'];
						print "\n\n jsonSingleResult['contentDiv']: ".$singleResult['contentDiv'];
						print "\n\n jsonSingleResult['header']: ".$singleResult['header'];
						print "\n\n jsonSingleResult['minHeader']: ".$singleResult['minHeader'];
						print "\n\n jsonSingleResult['minContent']: ".$singleResult['minContent'];
						print "\n\n jsonSingleResult['tokenContent']: ".$singleResult['tokenContent'];
						print "\n\n jsonSingleResult['allContent']: ".$singleResult['allContent'];
					}
				
				
				
					// Check the size of the response if this result was added
					$tmpAllResults = $jsonAllResults;
					$tmpAllResults[] = $singleResult;
					$tmpJsonResponse = json_encode(array('sid' => $sid, 'count' => $resultCount, 'upto' => $uptoResult, 'results' => $tmpAllResults));
					$tmpJsonResponseLength = strlen($tmpJsonResponse);
				
					if ($tmpJsonResponseLength > $resultMaxLength)
					{
						if ($DEBUG) print "\n\n\nBREAKING!!! length($tmpJsonResponseLength > $resultMaxLength)";
						break;
					}
					$jsonAllResults[] = $singleResult;
				} // result!=null
				$i++;
			} // while
		} // $ok_to_retrieve
		return json_encode(array('sid' => $sid, 'count' => $resultCount+1, 'from' => $fromResult, 'upto' => $uptoResult, 'results' => $jsonAllResults, 'error'=>$errortxt));
	} // get_json_searchresults
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * Calls and code onto (thesaurus) search results
	 * broaders, narrowers, related and relative contextual node (for mlt vs widget results)
	 * @return a text json representation of them
	 * To be used in a webservice for a javaserver
	 */
	public static function get_json_thesearchresults4webservice($query, $userid, $m, $ontocontext, $sortfacets_lexicographically, &$the_records)
	{
		$DEBUG=0;
		
		global $RDFLOG, $HOST;
		$errortxt='';
		$subjects{$query}=0; // take compound as is (0 is just dummy)
		if (strstr($query,' '))
		{
			foreach(explode(' ',$query) as $qsegment)
				$subjects{$qsegment}=0;
		}
				
		if ($DEBUG) {
			print "<br>get_json_thesearchresults4webservice using ontocontext=$ontocontext";	
			print "<br>USING the foll subjects: "; var_dump($subjects);}
		//$sid=uniqid('thesearch');
		$sid=null;
		$NAMESPACES = array(); // not used but needed
		$servicename = ''; // not used
		$lang=detectLanguageAndLog($query,'get_json_thesearchresults4webservice',$sid);
		$searchuid = ''; // not used
		$COUNTTRIPLES = false; 
		$max_s = 100000; // =no limit

		if (($rdfprocessor = new RDFprocessor(null, 2, $HOST)))
		{
			RDFprocessor::$rdfp_MAX_SRC_SUBJECT_EXPANSION_PRO_SRC = 1000000; // no limit
			RDFprocessor::$rdfp_MAX_SRC_SUBJECT_EXPANSION = 1000000; // no limit
			RDFprocessor::$rdfp_TOLERATED_SRC_LOD_DATA_AGE_SEC = RDFprocessor::$rdfp_TOLERATED_SRC_LOD_DATA_AGE_SEC; // no change (for now)
			
			list($skos_subject_expansion,$count_all_added_triples) =
					$rdfprocessor->get_subjects_expansions_using_thesauri(  $subjects,
																																	$sid,
																																	$servicename,
																																	$userid,
																																	$NAMESPACES,
																																	$lang,
																																	$searchuid,
																																	$COUNTTRIPLES,
																																	$the_records,
																																	$max_s,
																																	$m,
																																	$single_subject_expansion=false,
																																	$ontocontext,
																																	$sortfacets_lexicographically  );
																																	
			/**Collect/refactorize every item so we have only one THESAURUS record
			 * with its content (skos node)
			 * STW 
			 *  broaders (for subject "digital economy")
			 *  broaders (for subject "digital")
			 *  broaders (for subject "economy")
			 *  Narrowers (for subject "digital economy")
			 *  Narrowers (for subject "digital")
			 *  Narrowers (for subject "economy")
			 *  Related (for subject "digital economy")
			 *  Related (for subject "digital")
			 *  Related (for subject "economy")
			 */ 
			 if ($DEBUG)
			 {
			 		print "<hr>get_subjects_expansions_using_thesauri returns: <br>"; 
					var_dump($skos_subject_expansion);
			 }
				$refactorized_skos_subject_expansion
			 					= refactorize_uniquely_skos_subject_expansion($skos_subject_expansion);
			 
		}		
		if($DEBUG) {
			print "<hr>get_subjects_expansions_using_thesauri($query) returning: <br>";
			var_dump($skos_subject_expansion);
			tell_skos_subjects($skos_subject_expansion, 'RESULTS expand_subjects');
			
			print "<hr>refactorize_uniquely_skos_subject_expansion() returning: <br>";
			var_dump($refactorized_skos_subject_expansion);
			print "<hr>";
			tell_skos_subjects($refactorized_skos_subject_expansion, 'RESULTS refactorized');
			
			
			print $RDFLOG;
			$RDFLOG='';
		}
		
		
		return json_encode(array('query' => $query, 'skostheresults' => $refactorized_skos_subject_expansion,'error'=>$errortxt));
	} // get_json_thesearchresults4webservice
	
	
	
	
	
	
	
	
	/**
	 * MLT result for a given result
	 * @param  $rid - solr id for widget result
	 * @param  $sid - search id
	 * @param  $fromResult - starting index for displaying results
	 * @param  $m - Number of results to be output
	 * @param  $internalresults - whether to use only
	 * @param  $externalresults - whether to use only
	 * @return JSON results
	 */
	public static function get_json_mltresults4webservice($rid, $sid, $fromResult, $m, $internalresults, $externalresults)
	{
		$DEBUG=0;
		global $SOLR_RODIN_CONFIG;
		global $webservice;
		$webservice = true;
		$ok_to_retrieve=true;
		
		if (!$rid)
		{
			$errortxt='get_json_searchresults() - fatal - no rid provided!';
			$ok_to_retrieve=false;
		} 
		else if($m==0)
		{
			$errortxt='get_json_searchresults() - fatal - no m provided!';
			$ok_to_retrieve=false;
		}
		if ($DEBUG)
			print "\n<br> get_json_searchresults($sid, $internalresults, $externalresults)";
		
		if ($ok_to_retrieve)
		{
			$slrq='mlt?q=id:'.$rid.'&mlt.fl=body&fl=score,*&mlt.minwl=3&mlt.mintf=1';
			$slrq_base64 = base64_encode($slrq);
			$allResults = RodinResultManager::getRodinResultsFromSOLR($sid,'',$internalresults, $externalresults,$slrq_base64); 
			$resultCount = count($allResults);
			if (!$fromResult) $fromResult=0;
			// Both a maximum size and a maximum number of results are set
			$uptoResult = min($resultCount, $fromResult + $m);
		
			if ($DEBUG) print "<br>\n$resultCount results read..."
												."<br>Showing $m results from $fromResult upto $uptoResult";
			
			$i = $fromResult;
			while ($i < $uptoResult) 
			{
				$result = $allResults[$i];
				if ($result)
				{
					$resultCounter = $i + 1;
				
					if ($DEBUG)
					{
						print "<hr>\n\nRESULT: <br>\n"; var_dump($result);
					}
				
					$resultIdentifier = 'aggregatedResult-' . $resultCounter . ($suffix != '' ? '_' . $suffix : '');
				
					$singleResult = array();
				
					$singleResult['count'] = $resultCounter;
					$singleResult['url'] = $result->getUrlPage();
					$singleResult['rid'] = $result->getId() ."";  if ($DEBUG) print "<br>SOLR ID: ".$singleResult['rid'];
					$singleResult['resultIdentifier'] = $resultIdentifier;
				
					//$singleResult['headerDiv'] = json_encode($result->headerDiv($resultIdentifier));
					//$singleResult['contentDiv'] = json_encode($result->contentDiv($resultIdentifier));
				
					//$singleResult['header'] = json_encode($result->htmlHeader($singleResult['resultIdentifier'], $resultCounter, $sid, true));
					//$singleResult['minHeader'] = json_encode($resultCounter . '<br />');
				
					$singleResult['toString'] = $result->__toString();
					$singleResult['toDetails'] = json_encode($result->__toDetails());
				
					// Check the size of the response if this result was added
					//$tmpAllResults = $jsonAllResults;
					
					$jsonAllResults[] = $singleResult;
					}
				$i++;
			}
		} // $ok_to_retrieve
		if ($DEBUG) print "<hr>";
		return json_encode(array( 'sid' => $sid, 
															'count' => count($jsonAllResults), 
															'from'=>$fromResult, 
															'upto' => $uptoResult - 1, 
															'all'=>$resultCount, 
															'results' => $jsonAllResults, 
															'error'=>$errortxt ) );
	} // get_json_mltresults4webservice
	
	
	
	
	
	
	
	
	/**
	 * Calls and code search results
	 * Returns a text json representation of them
	 * To be used in a webservice for a javaserver
	 * Returns first $m results from $fromResult
	 */
	public static function get_json_searchresults4webservice($sid, $fromResult, $m, $internalresults, $externalresults)
	{
		$DEBUG=0;
		$ok_to_retrieve=true;
		if (!$fromResult) $fromResult=0;
		if (!$sid)
		{
			$errortxt='get_json_searchresults() - fatal - no sid provided!';
			$ok_to_retrieve=false;
		} 
		else if($m==0)
		{
			$errortxt='get_json_searchresults() - fatal - no m provided!';
			$ok_to_retrieve=false;
		}
		if ($DEBUG)
			print "\n<br> get_json_searchresults($sid, $internalresults, $externalresults)";
		
		if ($ok_to_retrieve)
		{
			$allResults = RodinResultManager::getRodinResultsForASearch($sid,'',$internalresults, $externalresults); 
			$resultCount = count($allResults);
			
			// Both a maximum size and a maximum number of results are set
			$uptoResult = min($resultCount, $fromResult + $m);
		
			if ($DEBUG) {
				print "<br>\n$resultCount results read..."
												."<br>Showing $m results from $fromResult upto $uptoResult"
												."<br>DUMPING THE RESULT OBJECT: <hr>"; 
												
												var_dump($allResults);
												print "<hr>";
												
			}
			$i = $fromResult;
			while ($i < $uptoResult) 
			{
				if ($DEBUG) print "<br>reading result ($i) ...";
				
				$result = $allResults[$i];
				if ($result==null)
				{
					if($DEBUG) fontprint("<br>RESULT $resultCounter ($i) is NULL",'red');
				}
				else
				{
					$resultCounter = $i + 1;
				
					if ($DEBUG)
					{
						print "<hr>\n\nRESULT $resultCounter ($i): <br>\n"; var_dump($result);
					}
				
					$resultIdentifier = 'aggregatedResult-' . $resultCounter . ($suffix != '' ? '_' . $suffix : '');
				
					$singleResult = array();
				
					$singleResult['count'] = $resultCounter;
					$singleResult['rid'] = $result->getId() ."";  if ($DEBUG) print "<br>SOLR ID: ".$singleResult['rid'];
					$singleResult['url'] = $result->getUrlPage();
					$singleResult['resultIdentifier'] = $resultIdentifier;
					$singleResult['toString'] = $result->__toString();
					$singleResult['toDetails'] = json_encode($result->__toDetails());
				
					// Check the size of the response if this result was added
					//$tmpAllResults = $jsonAllResults;
					
					$jsonAllResults[] = $singleResult;
					}
				$i++;
			}
		} // $ok_to_retrieve
		if ($DEBUG) print "<hr>";
		return json_encode(array( 'sid' => $sid, 
															'count' => count($jsonAllResults), 
															'from'=>$fromResult, 
															'upto' => $uptoResult - 1, 
															'all'=>$resultCount, 
															'results' => $jsonAllResults, 
															'error'=>$errortxt ) );
	} // get_json_searchresults4webservice
	
	
	
}

?>