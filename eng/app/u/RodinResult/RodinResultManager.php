<?php

require_once 'RodinArticleResult.php';
require_once 'RodinBookResult.php';
require_once 'RodinUrlResult.php';
require_once 'RodinPictureResult.php';

if (basename(getcwd()) == 'RodinResult') {
	require_once '../../u/FRIdbUtilities.php';
} else {
	require_once '../u/FRIdbUtilities.php';
}

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


  public static function saveRodinResultsInResultsSOLR($results, $sid, $datasource, $timestamp)
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
//          print "\n<hr><br>RESULT: ";
//          var_dump($result);

          $documents[] = $result->toInsertSOLRdocument($client, 
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
	

  public static function getRodinResultsForASearch($sid) 
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
            return RodinResultManager::getRodinResultsFromSOLR($sid,$datasource='',$slrq_base64='') ;
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
 * In case datasource is not set, results for all widgets are retrieved 
 */
public static function getRodinResultsFromSOLR($sid,$datasource,$slrq_base64) {

		global $aggView;
		$max=10;
		$filenamex='/u/SOLRinterface/solr_interface.php';
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
    global $SOLR_RODIN_CONFIG;
    global $SOLR_MLT_MINSCORE;
    global $USER;
    global $RODINSEGMENT;
    global $m; if($m==0) $m=1000; //we do not know how may rows are to retrieve
    
    $slrq='';
    if ($slrq_base64)
      $slrq=base64_decode($slrq_base64);
    
    $solr_user= $SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['user'];
    $solr_host= $SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['host']; //=$HOST;
    $solr_port= $SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['port']; //=$SOLR_PORT;
    $solr_path= $SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['path']; //='/solr/rodin_result/';

//    print "<br>solr_user: $solr_user";
//    print "<br>solr_host: $solr_host";
//    print "<br>solr_port: $solr_port";
//    print "<br>solr_path: $solr_path";
//    print "<br>RODINSEGMENT: $RODINSEGMENT";
//    print "<br>datasource: $datasource";
//    print "<br>sid: $sid";
//    print "<br>USER: $USER";
    
    #Fetch result from SOLR using even $slrq (handler and some parameters) if set instead of the standard Handler
    $EVTL_datasource=($datasource<>'')?"%20wdatasource:$datasource":'';
		
    if ($slrq=='' && $sid<>'')
        $solr_select= "http://$solr_host:$solr_port$solr_path"
                       ."select?q=sid:$sid{$EVTL_datasource}%20user:$USER%20seg:$RODINSEGMENT&rows=$m";
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
//      print "<hr>SOLR SELECT: <br>(((".htmlentities($solr_select).")))";
//      print "<hr>SOLR QUERY: <br>(((".htmlentities($solr_result_query_url).")))";
 
      $filecontent=file_get_contents($solr_result_query_url);
      $solr_sxml= simplexml_load_string($filecontent);
			
      if ((!$aggView) &&
      		(($RODINSEGMENT=='eng' || $RODINSEGMENT=='x' || $RODINSEGMENT=='st') 
              || ( $RODINSEGMENT=='p' && $USER==4) ) ) //fabio=developer on x, st (not on p=4)
      {  
        $solr_real_url=get_solrbridge($solr_result_query_url);
        $EVTL_MLT=($MLT)?" (mlt)":""; 
        print "<hr><a href='$solr_real_url' target='_blank' title='Get SOLR raw data in a new TAB'>raw data</a>$EVTL_MLT<br>";
      }
      //print "<hr>SOLR_CONTENT: <br>(((".htmlentities($filecontent).")))";
      //print "<hr>SOLR_RESULT: <br>"; var_dump($solr_sxml);
      
      //if (!$owner) $PRECISATION="[@name='response']";
      $DOCS = $solr_sxml->xpath("/response/result$PRECISATION/doc"); //find the doc list results
      //print "<hr>".count($DOCS)." SOLR_DOCS: <br>"; var_dump($solr_sxml);
       
      $CNT=0;
      $NO_OF_DISPLAYED_RESULTS=0;
      $dedup_hash= array();
      foreach($DOCS as $DOC)
      {
        if ($NO_OF_DISPLAYED_RESULTS>$m)
             break; // stop parsing and displyaing results
          
        $CNT++;
        $FORGET_RESULT=false;
        $id='';
        $row=array();
//        print "<hr>DOC:";
//        print "<hr> SOLR_DOC: <br>"; var_dump($DOC);

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
              //print "<br> CHILD: <br>"; var_dump($AC);
              $value=$AC;
            }
          }
          else
            $value=$ATTRVAL[0];

          if ($name=='id') // we need the SOLR id...
            $id=$value;
          else if ($name=='wdatasource')
          {
            $owner= ($datasource==$value);
            $reference= ($CNT==1 && $owner);
          } //  $name=='wdatasource'
          else if ($name=='body') // compute hash deduplication discarding new result 
          {
            if ($MLT)
            {
              $md5_body=md5($value);
//              print "<hr>$value";
//              print "<br>$CNT: $md5_body";
              if (! ($dedup_hash{$md5_body})) // still a new body value?
              {
                //print " NEW";
                $dedup_hash{$md5_body}=true;
              } 
              else // known body -> cancel result display
              {  //print " OLD";
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

        // print "<hr>ROW: <br>"; var_dump($row);

        #################################
        #
        # Process result:
        #
        $pointer = explode('.', $row['xpointer']);
        $pointerBase = intval($pointer[0]);
        $pointerRemainder = count($pointer) > 1 ? intval($pointer[1]) : -1;
        
        if ($owner || $aggView)
        {  
          $result = RodinResultManager::buildRodinResultByType(intval($row['type']));

          $result->setSid($sid);
          $result->setId($id);

          foreach($row as $attribute=>$value)
          {
            //$result = $allResults[$pointerBase];
            switch ($attribute) {
              case 'score': //print "<br>Score: $value reference=$reference MLT:$MLT";
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
            $allResults[] = $result;
            $NO_OF_DISPLAYED_RESULTS++;
          }
        } // owner
      } // foreach DOCS
     
      //foreach($dedup_hash as $k=>$v) print "<br>$k=>$v"; //debug - show hash
     
		} catch (Exception $e) {
			print "RodinResultManager EXCEPTION: $e";
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
        		$allResults = RodinResultManager::getRodinResultsFromSOLR($sid, $datasource, $slrq);
    }

		if (count($allResults) > 0) {

      //print "<br>".count($allResults)." results :<br>";
			$resultCounter = 1;
			foreach ($allResults as $result) {

        //print "<br>RESULT :<br>"; var_dump($result);

        // Is result coming from a cache?
        $timestamp = $result->getCacheTimeStamp();
        //Is timestamp set? => mark container as cached
				$CACHED=($timestamp)?" wdscached":'';
        print "<div class='oo-result-container{$CACHED}' >";
				
				$resultIdentifier = str_replace('.rodin', '', substr($datasource, strrpos($datasource, '/') + 1)) . '-' . $resultCounter;

				print $result->headerDiv($resultIdentifier);
				print $result->contentDiv($resultIdentifier);
				print $result->jsScriptAndRender($resultIdentifier, $resultCounter, $sid);
				
				print '</div>';
				
				$resultCounter++;
			}
			
			print '<script type="text/javascript">widgetResultSet.askResulsToRender("' . $render . '")</script>';
		} else {
			print '<p class="widgetResultCount">' . lg('lblGotNoResults') . '</p>';
		}
	}

	public static function renderAllResultsInOwnTab($sid, $datasource, $slrq) {
		RodinResultManager::renderAllResultsInWidget($sid, $datasource, $slrq, 'all');
		return true;
	}
}
