<?php

/* ********************************************************************************
 * Using the flickr php library phpFlickr v.2.3.0.1
 *
 * @author Fabio Ricci <fabio.fr.ricci@hesge.ch>
 * @author Javier Belmonte, refactoring on 10.09.2012
 ******************************************************************************** */

include_once("../u/RodinWidgetBase.php");
require_once '../u/RodinResult/RodinResultManager.php';
include_once("$DOCROOT/$RODINUTILITIES_GEN_URL/flickr/phpFlickr-2.3.0.1/phpFlickr.php");

global $SEARCHSUBMITACTION;

$FLICKR_AUTH_KEY = getWK('FLICKR_AUTH_KEY');

$NEED_PHP_INTERNET_ACCESS = true;

// Since widgets are loaded inside an iFrame, they need
// a HTML header.
print_htmlheader("FLICKR RODIN WIDGET");

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
add_search_control('q', $qx, '$q', $htmldef, 1);

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


/**
 * Method called from RodinWidgetSMachine.RDW_COLLECTRESULTS_EPI() to collect
 * results from the source and save them to the database. It used to return a
 * table with an old structure for results but now returns the number of results
 * found only.
 * 
 * @param string $chaining_url
 */
function DEFINITION_RDW_COLLECTRESULTS($chaining_url = '') {
	global $PROXY_NAME, $PROXY_PORT, $PROXY_AUTH_USERNAME, $PROXY_AUTH_PASSWD;
	global $FLICKR_AUTH_KEY;
	global $datasource;
	global $RDW_REQUEST;



	foreach ($RDW_REQUEST as $querystringparam => $d)
		eval( "global \${$querystringparam};" );

	// Create new phpFlickr
	$flickr = new phpFlickr($FLICKR_AUTH_KEY);

	// If necessary add the authentication keys
	if ($PROXY_NAME != '')
		$flickr->req->setProxy($PROXY_NAME, $PROXY_PORT, $PROXY_AUTH_USERNAME, $PROXY_AUTH_PASSWD);

	// Search for most interesting photos with the query in their text
	$searchConfig = array("text"=>"$q", "sort"=>"interestingness-desc", "per_page"=>$m);
  /*
   * DO NOT try to cache this: you have to store serializations of both objects
   * $flickr and $searchResults and after deserialization the class and the subcoasses are
   * just stdClass ... every class is lost ... very difficult to reconstruct
   */
  
	$searchResults = $flickr->photos_search($searchConfig);

	// Parse the list and fill the result set
	$allResults = array();

	if (count($searchResults)>0) {
		foreach ((array)$searchResults['photo'] as $photo) {
			// Create the result object
			$singleResult = RodinResultManager::buildRodinResultByType(RodinResultManager::RESULT_TYPE_PICTURE);
			$singleResult->setSid($sid);

			// General fields
			$photoInfo = $flickr->photos_getInfo($photo['id']);

			$singleResult->setTitle($photoInfo['title']);
			$singleResult->setUrlPage($photoInfo['urls']['url'][0]['_content']);
			$singleResult->setDate($photoInfo['dates']['taken']);
			$singleResult->setAuthors($photoInfo['owner']['realname']);
			
			// Picture specific fields
			$singleResult->setProperty('pictureUrl', $flickr->buildPhotoURL($photo, "Square"));

			$singleResult->setProperty('description', substr(strip_tags($photoInfo['description']), 0, 2048));

			if (count($photoInfo['location']) > 2)
				$singleResult->setProperty('geoloc', implode(', ', array_slice($photoInfo['location'], 0, 2)));

			if (count($photoInfo['tags']['tag']) > 0) {
				$tags = array();
				foreach ($photoInfo['tags']['tag'] as $tag)
					$tags[] = $tag['raw'];

				$singleResult->setProperty('tags', implode(', ', array_slice($tags, 0, 10)));
			}

			// Add single result to table
			$allResults[] = $singleResult;
		}
	}

	// Save search to DB
	RodinResultManager::saveRodinSearch($sid, $q);
	
	// Save all articles found to DB
	RodinResultManager::saveRodinResults($allResults, $sid, $datasource);
	
	return count($allResults);
}

/**
 * Called from RodinWidgetSMachine.RDW_SHOWRESULT_WIDGET_EPI(), it is asked
 * to print the HTML code corresponding to results. The caller method already
 * creates the necessary DIV for all the results.
 */
function DEFINITION_RDW_SHOWRESULT_WIDGET($w,$h) {
	global $sid;
	global $datasource;
  global $slrq;
	global $render;
	
	RodinResultManager::renderAllResultsInWidget($sid, $datasource, $slrq, $render);
	
	return true; 
}

/**
 * Called from RodinWidgetSMachine.RDW_SHOWRESULT_FULL_EPI(), it is asked
 * to print the HTML code corresponding to results.
 */
function DEFINITION_RDW_SHOWRESULT_FULL($w,$h) {
	global $sid;
	global $datasource;
  global $slrq;
	
	RodinResultManager::renderAllResultsInOwnTab($sid,$datasource,$slrq);
	
	return true; 
}

/**
 * 
 * ... ?
 */
function DEFINITION_RDW_STORERESULTS() {
	return true; // nothing to do here
}

/** 
 * 
 * This function was used to diplay a header containing a logo for the service.
 * 
 * @deprecated
 */
function DEFINITION_RDW_DISPLAYHEADER() {
	return true;
}

/**
 * ... ?
 * Check ZBZ Widget, it is the only one returning 'true'
 */
function WANT_RENDERBUTTONS() {
	return false;
}

/**
 * ... ?
 */
function DEFINITION_RDW_DISPLAYSEARCHCONTROLS() {
	return true;
}

/* ********************************************************************************
 * Decide which function the state machine is on.
 ******************************************************************************* */

include_once("../u/RodinWidgetSMachine.php");

?>