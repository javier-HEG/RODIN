<?php
include_once("../u/RodinWidgetBase.php");
require_once '../u/RodinResult/RodinResultManager.php';

		##############################################
		##############################################
		print_htmlheader("DELICIOUS RODIN WIDGET");
		##############################################
		##############################################
	
	
		global $SEARCHSUBMITACTION;


$DELICIOUS_search_baseFEED="http://feeds.delicious.com/v2/rss/tag/";

$widget_icon_width=55;
$widget_icon_height=20;

		##############################################
		# HTML SEARCH CONTROLS:  
		##############################################

		// add_search_control($nale,$leftlable, $rightlable, $defaultvalueQS,$htmldef,$pos)

	
			
		// QUERY TAG: q (rodin internal query tag)
		##############################################
		$title=lg("titleWidgetTypeSearch");
		$htmldef=<<<EOH
			<input class="localSearch" name="q" type="text" value="$qx" title='$title' onchange="$SEARCHSUBMITACTION">
EOH;
		add_search_control('q',$qx,'$q',$htmldef,1);
		##############################################



		// Number of results m (default)
		##############################################
		$title=lg("titleWidgetMaxResults");
		$m=$_REQUEST['m']; if(!$m) $m=20;
		$htmldef=<<<EOH
			<input class="localMaxResults" name="m" type="text" value="$m" title='$title'/>
EOH;
		add_search_control('m',$m,20,$htmldef,1);
		##############################################
		
		if (1)
		{
		// Button ask (default)
		##############################################
		$title=lg("titleWidgetButtonAsk");
		$label=lg("labelWidgetButtonAsk");
		$htmldef=<<<EOH
			<input name="ask" class="localSearchButton" type="button" onclick="$SEARCHSUBMITACTION" value="$label" title='$title'/>
EOH;
		add_search_control('ask','','',$htmldef,1);
		##############################################
		}




	/*
	##############################################
	##############################################
	function DEFINITION_RDW_SEARCH_FILTER()
	##############################################
	##############################################
	{	
		global $SEARCHFILTER_TEXT_SIZE;
	
	
		return true;	
		
	}// DEFINITION_RDW_SEARCH_FILTER		
	*/
	

	
	
	
	
	##############################################
	##############################################
	function DEFINITION_RDW_DISPLAYHEADER()
	##############################################
	##############################################
	{
		//Widget Icon is displayed directly on title bar
		//Instead of name
		return true;
		
	} // DEFINITION_RDW_DISPLAYHEADER
	##############################################











	##############################################
	##############################################
	function DEFINITION_RDW_DISPLAYSEARCHCONTROLS()
	##############################################
	##############################################
	{
	
		$res=true;
		// ADD QUERY CONTROLS
		
		return $res;

	} // DEFINITION_RDW_DISPLAYSEARCHCONTROLS
	##############################################







	#The following tells the widget state machine to check 
	#once for internet connection and warn if no one found
	#(timeout) before collecting results
	$NEED_PHP_INTERNET_ACCESS=true;


/**
 * Method called from RodinWidgetSMachine.RDW_COLLECTRESULTS_EPI() to collect
 * results from the source and save them to the database. It used to return a
 * table with an old structure for results but now returns the number of results
 * found only.
 * 
 * @param string $chaining_url
 */
function DEFINITION_RDW_COLLECTRESULTS($chaining_url='') {
	global $datasource;
	global $DELICIOUS_search_baseFEED;
	global $REALNAME;
	global $RDW_REQUEST;
	global $RODINBASEDATADIR; // for testing from fixed file

	foreach ($RDW_REQUEST as $querystringparam => $d) {
		eval("global \${$querystringparam};");
	}
	
	$FILTER_SECTION = '';
	foreach($REALNAME as $rodin_name => $needed_name) {
		if ("${$rodin_name}" != '') {
			$FILTER_SECTION .= "&$needed_name=${$rodin_name}";
		}
	}

	$parameters = urlencode(deletequote(stripslashes($q))) . "?count=$m" . $FILTER_SECTION;
	$feed = "$DELICIOUS_search_baseFEED$parameters";

	//$rssContent = file_get_contents($feed);
  $rssContent = get_cached_widget_response($feed);

	$rss = str_get_html($rssContent);
	
	// Browse RSS content looking for results 
	$allResults = array();
	
	$channel = $rss->find('channel', 0);
	foreach ($channel->find('item') as $item) {
		// Create the result object
		$singleResult = RodinResultManager::buildRodinResultByType(RodinResultManager::RESULT_TYPE_URL);

		// General fields
		$singleResult->setTitle($item->find('title', 0)->innertext);
		$singleResult->setUrlPage($item->find('guid', 0)->innertext);
		$singleResult->setDate($item->find('pubDate', 0)->innertext);

		// URL specific fields
		// - Link, can't be obtained using SimpleHtmlDom library
		$item->setAttribute("xmlns:dc", "http://purl.org/dc/elements/1.1/");
		$item->setAttribute("xmlns:wfw", "http://wellformedweb.org/CommentAPI/");
		$itemSimpleXml = simplexml_load_string((string) $item);
		$itemAsArray = json_decode(json_encode($itemSimpleXml), true);
		$singleResult->setProperty('url', $itemAsArray['link']);
		// - Description
		$singleResult->setProperty('description', $item->find('description', 0)->innertext);
		// - Tags
		$tagArray = array();
		foreach ($item->find('category') as $category) {
			$tagArray[] = $category->innertext;
		}
		$singleResult->setProperty('tags', implode(', ', $tagArray));

		// Add single result to table
		$allResults[] = $singleResult;
	}
	
	// Save search to DB
	RodinResultManager::saveRodinSearch($sid, $q);
	
	// Save all articles found to DB
	RodinResultManager::saveRodinResults($allResults, $sid, $datasource);
	
	return count($allResults);
}

/**
 * 
 * ... ?
 */	
function DEFINITION_RDW_STORERESULTS()
{
	return true; // nothing to do here
}

/**
 * Called from RodinWidgetSMachine.RDW_SHOWRESULT_WIDGET_EPI(), it is asked
 * to print the HTML code corresponding to results. The caller method already
 * creates the necessary DIV for all the results.
 */
function DEFINITION_RDW_SHOWRESULT_WIDGET($w,$h) {
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
function DEFINITION_RDW_SHOWRESULT_FULL($w,$h) {
	global $sid;
	global $datasource;
	
	RodinResultManager::renderAllResultsInOwnTab($sid,$datasource);
	
	return true; 
}


/* ********************************************************************************
 * Decide which function the state machine is on.
 ******************************************************************************* */

include_once("../u/RodinWidgetSMachine.php");

?>

