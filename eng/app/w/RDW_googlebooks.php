<?php

/* ********************************************************************************
 * Using the Google Books search API
 * 
 * New Google API key can be obtained at:
 * - https://code.google.com/apis/console
 * 
 * @author Fabio Ricci <fabio.fr.ricci@hesge.ch>, created on 10.08.2009
 * @author Javier Belmonte, refactoring on 4.09.2012
 ******************************************************************************** */

include_once("../u/RodinWidgetBase.php");
require_once '../u/RodinResult/RodinResultManager.php';

// This widget depends on the a Javascript for the AJAX calls
$ajaxFile = make_ajax_widget_filename();

// Since widgets are loaded inside an iFrame, they need a HTML header.
print_htmlheader("GOOGLEBOOKS RODIN WIDGET", $ajaxFile);

/* ********************************************************************************
 * Generate the HTML search input fields and controls
 ******************************************************************************* */

// - Query (Rodin default is 'q')
$title = lg("titleWidgetTypeSearch");
$qx = $_REQUEST['q'];
$htmldef = <<<EOH
	<input class="localSearch" name="q" type="text" value="$qx" title="$title" onchange="$SEARCHSUBMITACTION">
EOH;
add_search_control('q', $qx, '$q', $htmldef, 1);

// - Number of results (Rodin default is 'm')
$title = lg("titleWidgetMaxResults");
$m = $_REQUEST['m']; if(!$m) $m=20;
$htmldef = <<<EOH
	<input class="localMaxResults" name="m" type="text" value="$m" title='$title'/>
EOH;
add_search_control('m', $m, 20, $htmldef, 1);

// - Search button (Rodin default is 'ask') 
$title = lg("titleWidgetButtonAsk");
$label = lg("labelWidgetButtonAsk");
$htmldef = <<<EOH
	<input name="ask" class="localSearchButton" type="button" onclick="$SEARCHSUBMITACTION" value="$label" title='$title'/>
EOH;
add_search_control('ask', '', '', $htmldef, 1);

// The following tells the widget state machine to check
// once for internet connection and warn if no one found
// (timeout) before collecting results
$NEED_PHP_INTERNET_ACCESS=true;
$NEED_AJAX_INTERNET_ACCESS=true;

$SEARCH_RESULTS	= $_REQUEST['sr' ];


/**
 * 
 * This function is used to create the form for the widget preferences.
 * 
 * @author Javier Belmonte <javier.belmonte@hesge.ch>
 */
function DEFINITION_RDW_SEARCH_FILTER() {	
	$title = "Search only full Books.";
	$publications = $_REQUEST['xBooks'];
	$htmldef = <<<EOH
		<p>
			<input type="checkbox" name="xBooks" value="fullOnly" />
			$title
		</p>
EOH;

	//add_searchfilter_control($name,$realname,$value,$defaultvalueQS,$htmldef,$pos)
	add_searchfilter_control('xBooks', 'xBooks', $publications, 'fullOnly', $htmldef, 1);
	
	// Set default preferences in the DB
	register_default_prefs("xBooks=fullOnly");
	
	return true;		
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
 * 
 * ... ?
 */
function DEFINITION_RDW_DISPLAYSEARCHCONTROLS() {
	return true;
}


/**
 * Method called from RodinWidgetSMachine.RDW_COLLECTRESULTS_EPI() to collect
 * results from the source and save them to the database. It used to return a
 * table with an old structure for results but now returns the number of results
 * found only.
 *
 * Since we have to make an AJAX call to the Google Books API, this widget state
 * machine is stopped at this method (by returning 'false'). It is later restarted
 * by the AJAX call-back at the STORERESULTS state.
 * 
 * $urlPage $title; $authors; $date;
 * description', 'subject', 'publisher', 'review', 'cover', 'isbn
 *
 * On call-back we expect the list of results in the following JSON format:
 *    { search: [sid, query]
 *   	 results: [
 *   		0: [
 *				urlPage64: '',
 *				title: '',
 *				authors: 'René, ... ',
 *				date: '',
 *				description: '',
 *				subject: '',
 *				publisher: '',
 *				review: '',
 *				cover64: '',
 *				isbn: '' ]
 *   		1: [ IDEM ], ... ] }
 * 
 * @param string $chaining_url
 */
function DEFINITION_RDW_COLLECTRESULTS($chaining_url='') {
	global $datasource;
	global $RDW_REQUEST;
	global $GOOGLEAPIKEY;
	global $thisSCRIPT;

	foreach ($RDW_REQUEST as $querystringparam => $d)
		eval( "global \${$querystringparam};" );

	if ($_REQUEST['xBooks'] != "'fullOnly'")
		$restriction = "google.search.BookSearch.TYPE_FULL_VIEW_BOOKS";
	else
		$restriction = "google.search.BookSearch.TYPE_ALL_BOOKS";
		
	echo <<< EOAPIR
<script src="http://www.google.com/jsapi?key=$GOOGLEAPIKEY" type="text/javascript"></script>
<script type="text/javascript">
	// Activate Google Books search
	google.load('search', '1');

	// Create the JSON Object that will hold search results
	var allResults = new Object;
	allResults.search = null;
	allResults.results = new Array();

	/**
	* Collects and stores search results. Also calls the store
	* and render states of the widget.
	*/
	function processSearchResults() {
		var theResults = bookSearch.results;
		var nbResults = theResults.length;
		
		// The search info to be returned
		var searchInfo = new Object;
		searchInfo.sid = sid;
		searchInfo.query = q;
		
		for (var i = 0; i < nbResults; i++) {
			var resultItem = theResults[i];
			
			// The result to be added to the result list
			var singleResult = new Object;
			singleResult.urlPage64 =  Base64.encode(resultItem.unescapedUrl),
			singleResult.title = resultItem.title,
			singleResult.authors = resultItem.authors;
			singleResult.date = resultItem.publishedYear,
			singleResult.description = '',
			singleResult.subjects = '',
			singleResult.publisher = '',
			singleResult.review = '',
			singleResult.cover = '',
			singleResult.isbn = resultItem.bookId;

			allResults.results.push(singleResult);
		}

		allResults.search = searchInfo;

		JSONstring = JSON.stringify(allResults);
		codedJSONstring = encodeURIComponent(JSONstring);
		
		// Processing complete! Make the AJAX call and leave page
		var url = '$thisSCRIPT';
		var poststr = 'ajax=1&sid='+sid+'&q='+q+'&m='+m+'&sr='+codedJSONstring+'&uncache='+uncache+'&show='+show;
		var request = makeRequest(url, poststr, "window.open('$chaining_url','_self')");
	}

	function OnLoad() {
		// Parse the current page's querystring
		var qs = new Querystring();

		// TODO The following parameters are used in
		// processSearchResults() as global parameters

		// The number of results is limited to 5 because the
		// request URL made in processSearchResults() would
		// be too long
		m = Math.min(qs.get("m", "5"), 5);
		q = qs.get('q', qs.get('qe', ''));
		sid = qs.get("sid", "0");
		uncache = qs.get("uncache", "0");
		show = qs.get("show", "RDW_widget");
		
		bookSearch = new google.search.BookSearch();
		
		// Set restriction according to preferences
		bookSearch.setRestriction($restriction, null);
		bookSearch.setResultSetSize(bookSearch.LARGE_RESULTSET);
		
  		var searchControl = new google.search.SearchControl();
		searchControl.setResultSetSize(google.search.Search.LARGE_RESULTSET);
		searchControl.addSearcher(bookSearch);
	 	
		// Set the callback function
		bookSearch.setSearchCompleteCallback(this, processSearchResults, null);
		
		// Find me a beautiful car.
		bookSearch.execute(q);
	} // onload
	
	google.setOnLoadCallback(OnLoad);
</script>
</head>
<body>
	<p id="heading_p" />
</body>
</html>
EOAPIR;

	return false; // stop chaining at php/ajax level
}


/**
* Called from RodinWidgetSMachine.RDW_STORERESULTS_EPI(), only when
* there a POST is detected and holds parameters 'sr' and '_p'. It is
* responsible for saving the results passed as a JSON object
*/
function DEFINITION_RDW_STORERESULTS() {
	$DecodedSearchresults = json_decode($_REQUEST['sr']);

	$cnt = saveGoogleBooksResults($DecodedSearchresults);
	
	// This output is not visible since it's made by an AJAX query
	print "$cnt results for $sid stored in DB";

	return true;
}



/**
 * Called from RodinWidgetSMachine.RDW_SHOWRESULT_WIDGET_EPI(), it is asked
 * to print the HTML code corresponding to results. The caller method already
 * creates the necessary DIV for all the results.
 */
function DEFINITION_RDW_SHOWRESULT_WIDGET($w, $h) {
	global $sid;
	global $datasource;
	global $render;
	
	RodinResultManager::renderAllResultsInWidget($sid, $datasource, $render);

	return true;
}


/**
 * Called from RodinWidgetSMachine.RDW_SHOWRESULT_FULL_EPI(), it is asked
 * to print the HTML code corresponding to results.
 */
function DEFINITION_RDW_SHOWRESULT_FULL($w, $h) {
	global $sid;
	global $datasource;
	
	RodinResultManager::renderAllResultsInOwnTab($sid,$datasource);
	
	return true; 
}



/* ********************************************************************************
 * Utility functions, mainly widget independent.
 ******************************************************************************* */

/**
* Uses the RodinResultsManager to save the results sent through
* the AJAX call. Not a general function since the format of the
* results may depend on the widgets.
* 
* @param mixed $decodedResults the JSON decoded results
*/
function saveGoogleBooksResults($decodedResults) {
	global $datasource;

	$allResults = array();

	foreach ($decodedResults->results as $item) {
		// Create the result object
		$singleResult = RodinResultManager::buildRodinResultByType(RodinResultManager::RESULT_TYPE_BOOK);

		// General fields
		$singleResult->setTitle($item->title);
		$singleResult->setUrlPage(base64_decode($item->urlPage64));

		if ($item->date != 'unknown')
			$singleResult->setDate('01.01.' . $item->date);

		$authorArray = array();
		$authorElements = explode(',', $item->authors);
		if (count($authorElements) > 0)
			foreach ($authorElements as $author)
				$authorArray[] = trim($author);
		
		$singleResult->setAuthors(implode(', ', $authorArray));

		// Book specific fields: description, subject, publisher, review, cover, isbn
		$singleResult->setProperty('description', $item->description);
		$singleResult->setProperty('subjects', $item->subjects);
		$singleResult->setProperty('publisher', $item->publisher);
		$singleResult->setProperty('review', $item->review);
		$singleResult->setProperty('cover', $item->cover);
		$singleResult->setProperty('isbn', $item->isbn);

		// Add single result to table
		$allResults[] = $singleResult;

		echo $singleResult->toBasicHtml();
	}

	// Save search to DB
	$sid = $decodedResults->search->sid;
	$query = $decodedResults->search->query;
	RodinResultManager::saveRodinSearch($sid, $query);
	echo 'saveRodinSearch(' . $sid . ', ' . $query . ')<br />';
	
	// Save all articles found to DB
	RodinResultManager::saveRodinResults($allResults, $sid, $datasource);
	
	return count($allResults);
}


##################################################
##################################################
# Decide what to run:
include_once("../u/RodinWidgetSMachine.php");
##################################################
##################################################

?>