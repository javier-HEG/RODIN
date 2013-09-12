<?php

/* ********************************************************************************
 * The EconBiz API follows a RESTful API design. The base URL is
 * - http://dev.library.ethz.ch/rib/v1/primo/documents?q=bern
 * 
 * @author Fabio Ricci
 ******************************************************************************** */

include_once("../u/RodinWidgetBase.php");
require_once('../u/RodinResult/RodinResultManager.php');

global $SEARCHSUBMITACTION;

// Since widgets are loaded inside an iFrame, they need
// a HTML header.
if (!$WEBSERVICE) {
	print_htmlheader("EconBiz RODIN Widget");

	$searchsource_baseurl="http://dev.library.ethz.ch/rib/v1/primo/documents?";		


/* ********************************************************************************
 * Generate the HTML search input fields and controls
 ******************************************************************************* */

// - Query (Rodin default is 'q')
$title=lg("titleWidgetTypeSearch");
if ($WANT_WIDGET_SEARCH)
{
$htmldef=<<<EOH
	<input class="localSearch" name="q" type="text" value="$qx" title='$title' onchange="$SEARCHSUBMITACTION">
EOH;
add_search_control('q',$qx,'$q',$htmldef,1);

// - Number of results (Rodin default is 'm')
$title=lg("titleWidgetMaxResults");
$m=$_REQUEST['m']; if(!$m) $m=$DEFAULT_M;
$htmldef=<<<EOH
	<input class="localMaxResults" name="m" type="text" value="$m" title='$title'/>
EOH;
add_search_control('m',$m,20,$htmldef,1);

// - Search button (Rodin default is 'ask') 
$title=lg("titleWidgetButtonAsk");
$label=lg("labelWidgetButtonAsk");
$htmldef=<<<EOH
	<input name="ask" class="localSearchButton" type="button" onclick="$SEARCHSUBMITACTION" value="$label" title='$title'/>
EOH;
add_search_control('ask','','',$htmldef,1);
}
	} // $WEBSERVICE
	
/* ********************************************************************************
 * Widget functions
 ******************************************************************************* */
class RDW_ElibCH {
/**
 * 
 * This function is used to create the form for the widget preferences.
 * 
 * @author Javier Belmonte <javier.belmonte@hesge.ch>
 */
function NOT_IMPLEMENTED_YET() {
//	DEFINITION_RDW_SEARCH_FILTER() {
	// Fix width in accordance to Widget preferred widht
	global $_w;
	$w = $_w - 15;
	
	// Define the style for the controls
	$PREFS_STYLE =<<<EOS
	style="min-width: {$w}px; max-width: {$w}px; width : {$w}px;";
EOS;
	
	// Choosing the record type of interest
	$title="Articles";
	$publications=$_REQUEST['xArticles'];
	$htmldef="<p><input type=\"checkbox\" name=\"xArticles\" value=\"articles\" />$title</p>";

	//add_searchfilter_control($name,$realname,$value,$defaultvalueQS,$htmldef,$pos)
	add_searchfilter_control('xArticles','xArticles',$publications,'articles',$htmldef,1);
	
	// Set default preferences in the DB
	register_default_prefs("xArticles=articles");
	
	$title="Books";
	$publications=$_REQUEST['xBooks'];
	$htmldef="<p><input type=\"checkbox\" name=\"xBooks\" value=\"books\" />$title</p>";

	//add_searchfilter_control($name,$realname,$value,$defaultvalueQS,$htmldef,$pos)
	add_searchfilter_control('xBooks','xBooks',$publications,'books',$htmldef,1);
	
	// Set default preferences in the DB
	register_default_prefs("xBooks=books");
		
	return true;		
}		 	

/** 
 * 
 * This function was used to diplay a header containing a logo for the service.
 * 
 * @deprecated
 */
public static function DEFINITION_RDW_DISPLAYHEADER()
{
	return true;
}

/**
 * 
 * ... ?
 */
public static function DEFINITION_RDW_DISPLAYSEARCHCONTROLS()
{
	return true;
}

/**
 * Method called from RodinWidgetSMachine.RDW_COLLECTRESULTS_EPI() to collect
 * results from the source and save them to the database. It used to return a
 * table with an old structure for results but now returns the number of results
 * found only.
 * 
 * @param string $chaining_url
 */
public static function DEFINITION_RDW_COLLECTRESULTS($chaining_url='') 
{
	global $datasource;
	global $searchsource_baseurl;
	global $RDW_REQUEST;
	global $WEBSERVICE;
	
	if ($WEBSERVICE) //need to set again url:
	{
		$searchsource_baseurl="http://dev.library.ethz.ch/rib/v1/primo/documents?";		
	}
		
	foreach ($RDW_REQUEST as $querystringparam => $d)
	{
		if ($WEBSERVICE) 
				 eval( "global \${$querystringparam}; \${$querystringparam} = '$d';" );
		else eval( "global \${$querystringparam};" );
	}
	
	$qTokens = explode(',', trim($q, ' ,'));
	
	$parameters = array();
	$parameters['q'] = $query = $q;
	$parameters['bulksize'] = $m;
						
	$options = array(	CURLOPT_HTTPHEADER => array('Accept:application/json','Accept-Charset: ISO-8859-1'));
//Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7

	// print "<br> url: $searchsource_baseurl";
	// print "<br> params: ".var_dump($parameters);
	// exit;
//      
	list($timestamp,$jsonString) = get_cached_widget_response_curl($searchsource_baseurl, $parameters, $options);
	//print "FROM DS: ".htmlentities($jsonString);
        
	$jsonInfo = (json_decode($jsonString, true));

	// print "JSON INFO:<br><br>"; var_dump($jsonInfo); exit;

	// Parse JSON result and build results
	$allResults = array();
	
	// TODO Check status is 200
	// TODO Implement iterative access to results, batches of size 10

	$lasthit=$jsonInfo['result']['hits']['lasthit'];
	$firsthit=$jsonInfo['result']['hits']['firsthit'];
	$NoOfResults=$lasthit - $firsthit + 1;
	
	if ($NoOfResults > 0) 
	{
		foreach ($jsonInfo['result']['document'] as $record) 
		{
			// Get result data from record
			$options = array(CURLOPT_HTTPHEADER => array('Accept:application/json'));
      
			//print "ELIB RECORD: "; var_dump($record);exit;
			$recordid			=$record['recordid'];
			$biblio_data	=$record['biblioData'];
			$references		=$record['references'];
			$links				=$record['links'];
			$availability	=$record['availability'];
			$type					=$biblio_data['type'];
			
			switch ($type) 
			{
				case 'article':
				case 'journal':
					// Create the result object
					$singleResult = RodinResultManager::buildRodinResultByType(RodinResultManager::RESULT_TYPE_ARTICLE);
					break;
				case 'book':
					// Create the result object
					$singleResult = RodinResultManager::buildRodinResultByType(RodinResultManager::RESULT_TYPE_BOOK);
				break;
				default:
					// Create a dummmy result object
					$singleResult = RodinResultManager::buildRodinResultByType(RodinResultManager::RESULT_TYPE_BASIC);
					break;
			} // switch
			
			// General fields
			$singleResult->setTitle($biblio_data['title']);
			$singleResult->setDate(scan_last_date($biblio_data['creationdate']));
			
			if (isset($links['wissensportal'])) {
				$singleResult->setUrlPage($links['wissensportal']);
			} else if (isset($links['opac_holdings'])) {
				$singleResult->setUrlPage($links['opac_holdings']);
			} 
			
			$authorArray = array();
			$creator = trim($biblio_data['creator']);
			$contributor = trim($biblio_data['contributor']);
			$person = trim($biblio_data['person']);
			
			if ($creator 			&& !in_array($creator,$authorArray)) 			$authorArray[]=$creator;
			if ($contributor 	&& !in_array($contributor,$authorArray)) 	$authorArray[]=$contributor;
			if ($person 			&& !in_array($person,$authorArray)) 			$authorArray[]=$person;
	
			$singleResult->setAuthors(implode(', ', $authorArray));
			
			// Book specific fields
			$singleResult->setProperty('description', strip_tags($links['abstract']));
			
			if (isset($biblio_data['subject']) && is_array($biblio_data['subject'])) {
				$singleResult->setProperty('subjects', implode(', ', $biblio_data['subject']));
			}
			if (isset($biblio_data['keywords']) && is_array($biblio_data['keywords'])) {
				$singleResult->setProperty('keywords', implode(', ', $biblio_data['keywords']));
			}

			if (isset($biblio_data['identifier']['ISBN'])) {
				$singleResult->setProperty('isbn', $biblio_data['identifier']['ISBN']);
			}
			
			if (isset($biblio_data['description'])) {
				$singleResult->setProperty('description', $biblio_data['description']);
			}
			
			// Add single result to table
			$allResults[] = $singleResult;
		}
	} // 	if ($NoOfResults > 0
	
	// Save search to DB
	RodinResultManager::saveRodinSearch($sid, $q);
	
	// Save all articles found to DB
	RodinResultManager::saveRodinResults($allResults, $sid, $datasource, $timestamp);
	
	return count($allResults);
}

/**
 * 
 * ... ?
 */	
public static function DEFINITION_RDW_STORERESULTS()
{
	return true; // nothing to do here
}

/**
 * Called from RodinWidgetSMachine.RDW_SHOWRESULT_WIDGET_EPI(), it is asked
 * to print the HTML code corresponding to results. The caller method already
 * creates the necessary DIV for all the results.
 */
public static function DEFINITION_RDW_SHOWRESULT_WIDGET($w,$h) {
	global $sid;
	global $datasource;
  global $slrq;
	global $render;
	
	RodinResultManager::renderAllResultsInWidget($sid, $datasource, $slrq, $render);
	
	return true;
}

/**
 * Called from RodinWidgetSMachine.RDW_SHOWRESULT_FULL_EPI(), it is asked
 * to print the HTML code corresponding to results. The caller method already
 * creates the necessary DIV for all the results.
 */
public static function DEFINITION_RDW_SHOWRESULT_FULL($w,$h) {
	global $sid;
	global $datasource;
  global $slrq;
	
	RodinResultManager::renderAllResultsInOwnTab($sid,$datasource,$slrq);
	
	return true;
}



} // RDW_ElibCH
/* ********************************************************************************
 * Decide which function the state machine is on.
 ******************************************************************************* */

include_once("../u/RodinWidgetSMachine.php");

?>