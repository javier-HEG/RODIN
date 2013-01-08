<?php

/* ********************************************************************************
 * Using the BibSonomy PHP API.
 ******************************************************************************** */

include_once("../u/RodinWidgetBase.php");
include_once "$DOCROOT/$RODINUTILITIES_GEN_URL/bibsonomy/bibsonomy_api.php";
require_once '../u/RodinResult/RodinResultManager.php';

$BIBSONOMY_APPLICATION_ID = getWK('BIBSONOMY_APPLICATION_ID');
$BIBSONOMY_USER = getWK('BIBSONOMY_USER');

// Since widgets are loaded inside an iFrame, they need
// a HTML header.
print_htmlheader("BIBSONOMY RODIN WIDGET");
		

/* ********************************************************************************
 * Generate the HTML search input fields and controls
 ******************************************************************************* */

// - Query (Rodin default is 'q')
$title = lg("titleWidgetTypeSearch");
$htmldef=<<<EOH
	<input class="localSearch" name="q" type="text" value="$qx" title='$title' onchange="$SEARCHSUBMITACTION">
EOH;
add_search_control('q', $q, '$q', $htmldef, 2);
	
// - Number of results (Rodin default is 'm')
$title = lg("titleWidgetMaxResults");
$m = $_REQUEST['m']; if(!$m) $m = 20;
$htmldef=<<<EOH
	<input class="localMaxResults" name="m" type="text" value="$m" title='$title'/>
EOH;
add_search_control('m', $m, 20, $htmldef, 2);
		
// - Search button (Rodin default is 'ask')
$title = lg("titleWidgetButtonAsk");
$label = lg("labelWidgetButtonAsk");
$htmldef=<<<EOH
	<input name="ask" class="localSearchButton" type="button" onclick="$SEARCHSUBMITACTION" value="$label" title='$title'/>
EOH;
add_search_control('ask', '', '', $htmldef, 2);


/* ********************************************************************************
 * Widget functions
 ******************************************************************************* */

/**
 * This function is used to create the form for the widget preferences.
 * 
 * NB. Each filter param is prefixed by 'x', you have to provide a real
 * name also. If called 'xcc' here, real name is 'cc' (Rerodoc). Please
 * insert value=''.
 */
function DEFINITION_RDW_SEARCH_FILTER() {
	 // Fix width in accordance to Widget desired width	
	global $_w;
	$w = $_w - 80;
	
	// Type of resource searched
	$title = lg("titleBibsonomyResourceType");
	$xr = $_REQUEST['xr'];
	
	if ($xr=='') {
		$xr='bookmark';
	}
		
	if ($xr=='bookmark') {
		$BOOCKMARK_SEL=' checked ';
	} else if ($xr=='bibtex') {
		$BIBTEX_SEL=' checked ';
	}
	
	$htmldef=<<<EOH
		Resource:</td><td>
		<select name="xr" size="1" title="$title" >
			<option value="bookmark" $BOOCKMARK_SEL>Bookmark</option>
			<option value="bibtex" $BIBTEX_SEL>Bibtex</option>
		</select>
EOH;

	add_searchfilter_control('xr','r',$xr,'$xr',$htmldef,1);

	// BibSonomy username
	$title = lg("titleBibsonomyUserRestrict");
	$xu = $_REQUEST['xu'];
	$htmldef=<<<EOH
		User:</td><td> <input name="xu" type="text" value="" title="$title" style="width: {$w}px;" />
EOH;

	add_searchfilter_control('xu','u',$xu,'$xu',$htmldef,2);
	
	// Set default preferences in the DB
	register_default_prefs("xr=bookmark&xu=");
		
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
 * @param string $chaining_url
 */
function DEFINITION_RDW_COLLECTRESULTS($chaining_url='') {

	global $BIBSONOMY_USER, $BIBSONOMY_APPLICATION_ID;

	global $datasource;
	global $REALNAME;
	global $RDW_REQUEST;
	
	foreach ($RDW_REQUEST as $querystringparam => $d) {
		eval( "global \${$querystringparam};" );
	}
		
	foreach ($REALNAME as $rodin_name=>$needed_name) {
		if ("${$rodin_name}" <> '') { // only if value defined
			$FILTER_SECTION.="&$needed_name=${$rodin_name}";
		}

		if ($rodin_name == 'xr') {
			$r = $xr; // value from RDW_REQUEST
		} else if ($rodin_name == 'xu') {
			$u = $xu;
		}
	}
		
	// TODO Remove if useless
	if (!$r) {
		// Sure is sure: we do need a value here.
		$r = 'bookmark';
	}
	
	$tags = explode(' ', $q);

	// Instantiate BibSonomy accessor
	$bib = new BibSonomy($BIBSONOMY_USER, $BIBSONOMY_APPLICATION_ID );
  
  $sxml = getAllPublicPosts_cached($bib, $r, $tags, $q, $u);
  //$sxml = $bib->getAllPublicPosts($r, $tags, $u);
	
	// Parse results creating results
	$allResults = array();
	
	if (count($sxml->posts->post) > 0) {
		foreach ($sxml->posts->post as $xmlPost) {
			if ($r == 'bookmark') {
				// Create the result object
				$singleResult = RodinResultManager::buildRodinResultByType(RodinResultManager::RESULT_TYPE_URL);

				// General fields
				$singleResult->setTitle($xmlPost->bookmark['title']);
				$singleResult->setUrlPage('http://www.bibsonomy.org/url/' . $xmlPost->bookmark['intrahash']);
				$singleResult->setDate($xmlPost['postingdate']);

				// URL specific fields
				$singleResult->setProperty('url', $xmlPost->bookmark['url']);
				$singleResult->setProperty('description', $xmlPost['description']);
				
				$tagArray = array();
				foreach ($xmlPost->tag as $tag) {
					$tagArray[] = $tag['name'];
				}
				$singleResult->setProperty('tags', implode(', ', $tagArray));
				
				// Add single result to table
				$allResults[] = $singleResult;
			} else if ($r == 'bibtex') {
				// Bibtex kind of results I've seen are books, articles or incollection
				// (there could be other though)
				
				// Create the result object
				switch ($xmlPost->bibtex['entrytype']) {
					case 'article':
						$singleResult = RodinResultManager::buildRodinResultByType(RodinResultManager::RESULT_TYPE_ARTICLE);
						break;
					case 'book':
						$singleResult = RodinResultManager::buildRodinResultByType(RodinResultManager::RESULT_TYPE_BOOK);
						break;
					default:
						$singleResult = RodinResultManager::buildRodinResultByType();
				}
				
				// General fields
				$singleResult->setTitle($xmlPost->bibtex['title']);
				$singleResult->setUrlPage('http://www.bibsonomy.org/bibtex/1' . $xmlPost->bibtex['interhash']);
				$singleResult->setDate($xmlPost['postingdate']);
				
				$authorArray = array();
				$allAuthors = explode(' and ', $xmlPost->bibtex['author']);
				foreach ($allAuthors as $author) {
					$authorArray[] = implode(' ', array_reverse(explode(', ', $author)));
				}
				$singleResult->setAuthors(implode(', ', $authorArray));
				
				// Add specific properties
				switch ($xmlPost->bibtex['entrytype']) {
					case 'article':
						// $singleResult->setProperty('abstract', '');
			
						$tagArray = array();
						foreach ($xmlPost->tag as $tag) {
							$tagArray[] = $tag['name'];
						}
						$singleResult->setProperty('keywords', implode(', ', $tagArray));
						
						$miscData = decodeMiscProperty($xmlPost->bibtex['misc']);
						$singleResult->setProperty('doi', $miscData['ee']);
						
						break;
					case 'book':
						// Book specific fields
						// $singleResult->setProperty('description', '');

						$miscData = decodeMiscProperty($xmlPost->bibtex['misc']);
						$singleResult->setProperty('isbn', $miscData['isbn']);
						
						$tagArray = array();
						foreach ($xmlPost->tag as $tag) {
							$tagArray[] = $tag['name'];
						}
						$singleResult->setProperty('subjects', implode(', ', $tagArray));
						break;
					default:
				}
				
				// Add single result to table
				$allResults[] = $singleResult;
			}
			
			if (count($allResults) >= $m)
				break;
		}
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
  global $slrq;
	global $render;
	
	RodinResultManager::renderAllResultsInWidget($sid, $datasource, $slrq, $render);
	
	return true; 
}

/**
 * use rodin_cache - calls bibsonomy proc to get data
 * and store it into cache.
 */
function getAllPublicPosts_cached(&$bibsonomy_interface,$r, &$tags, $q, $u)
{
  //	$sxml = $bibsonomy_interface->getAllPublicPosts($r, $tags, $u);
  $cacheid = "$r-$q-$u";
  $bibsonomy_xml_response = get_cache_response($cacheid);
  if ($bibsonomy_xml_response && count($bibsonomy_xml_response->posts->post) > 0)
  {   
    $bibsonomy_sxml_response = simplexml_load_string($bibsonomy_xml_response);
  }
  else
  {
    $bibsonomy_sxml_response = $bibsonomy_interface->getAllPublicPosts($r, $tags, $u);
    $xml_content = $bibsonomy_sxml_response->asXML();
    cache_response($cacheid,$xml_content);
  }
  
  return $bibsonomy_sxml_response;
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

/* ********************************************************************************
 * Utility functions, widget independent.
 ******************************************************************************* */
/**
 * Decodes the 'misc' attribute in bookmarks and bibtext records
 */
function decodeMiscProperty($misc) {
	$miscArray = array();
	
	if (trim($misc) != '') {
		$miscItems = explode(',', $misc);

		if ($miscItems) {
			foreach ($miscItems as $item) {
				if (preg_match('/(\w+)\s*=\s*\{(.*)\}/', trim($item), $matches)) {
					$miscArray[$matches[1]]= $matches[2];
				}
			}
		} else {
			if (preg_match('/(\w+)\s*=\s*\{(.*)\}/', trim($item), $matches)) {
				$miscArray[$matches[1]]= $matches[2];
			}
		}
	}
	
	return $miscArray;
}

/* ********************************************************************************
 * Decide which function the state machine is on.
 ******************************************************************************* */

include_once("../u/RodinWidgetSMachine.php");

?>

