<?php
/* ********************************************************************************
 * Comments for widget : Journal De Genève
 * =================== = ======= == ======
 * 
 * Because of the lack of a proper API, I'm force to do some HTML Scraping for this
 * widget.
 *   
 * A URL request with possibly all the parameters.
 * 
 * http://www.letempsarchives.ch/Default/Scripting/SearchView.asp?
 * sQuery=&
 * x=12&y=6&
 * skin=LeTempsFr&AppName=2&
 * sSorting=Score%2Cdesc&
 * sDateFrom=01%2F01%2F1826&sDateTo=03%2F02%2F1999&
 * sPublication=JDG&sPublication=GDL&sPublication=LNQ&
 * sLanguage=English
 * 
 * The parameter StartFrom=5 can be used to get next five results. 
 * 
 * The "__formChanged()" method in GUICommons.js script is responsible for building
 * this URL with all its parameters. And there is another one in that same file that
 * transforms a search query "formatHumanReadableSearchQuery()" so that it can be
 * printed to the user together with the results.
 * 
 * @author Javier Belmonte
 ******************************************************************************** */

include_once("../u/RodinWidgetBase.php");

// Library helping with the HTML Scraping
include_once "$DOCROOT/$RODINUTILITIES_GEN_URL/simplehtmldom/simple_html_dom.php";

global $SEARCHSUBMITACTION;

// Since widgets are loaded inside an iFrame, they need
// a HTML header.
print_htmlheader("Le Temps - Archives historiques");

$searchsource_baseurl="http://www.letempsarchives.ch/Default/Scripting/SearchView.asp?skin=LeTempsFr&AppName=2&";		


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



/* ********************************************************************************
 * Widget functions
 ******************************************************************************* */

/**
 * 
 * This function is used to create the form for the widget preferences.
 * 
 * @author Javier Belmonte <javier.belmonte@hesge.ch>
 */
function DEFINITION_RDW_SEARCH_FILTER()
{	
	global $SEARCHFILTER_TEXT_SIZE;
		
	// Fix width in accordance to Widget preferred widht
	global $_w;
	$w = $_w - 15;
	
	// Define the style for the controls
	$PREFS_STYLE =<<<EOS
	style="min-width: {$w}px; max-width: {$w}px; width : {$w}px;";
EOS;
	
	// Choosing the publications in which the search should be launched.
	$title="Choose in which publications to search.";
	$publications=$_REQUEST['xPub'];
	$htmldef=<<<EOH
		<p>$title</p>
		<table>
			<tr>
				<td style="vertical-align: top;">
    				<input type="radio" name="xPub" value="all">All publications<br>
    			</td>
    			<td style="vertical-align: top;">
    		    	<input type="radio" name="xPub" value="JDG">Journal de Genève<br>
    				<input type="radio" name="xPub" value="GDL">Gazette de Lausanne<br>
    				<input type="radio" name="xPub" value="LNQ">Le nouveau quotidien<br>
    			</td>
    		</tr>
    	</table>
EOH;

	//add_searchfilter_control($name,$realname,$value,$defaultvalueQS,$htmldef,$pos)
	add_searchfilter_control('xPub','xPub',$publications,'all',$htmldef,1);
	
	// Choosing the sorting criterion
	$title="Choose how the results should be sorted.";
	$sorting=$_REQUEST['xSort'];
	$htmldef=<<<EOH
		<p>$title</p>
		<ul style="margin:0px; margin-bottom:6px;">
			<li><input type="radio" name="xSort" value="score"> By Score.</li>
			<li><input type="radio" name="xSort" value="dateasc"> By Date of Issue (Ascending).</li>
			<li><input type="radio" name="xSort" value="datedesc"> By Date of Issue (Descending).</li>
		</ul>
EOH;

	//add_searchfilter_control($name,$realname,$value,$defaultvalueQS,$htmldef,$pos)
	add_searchfilter_control('xSort','xSort',$sorting,'score',$htmldef,2);

	// Set default preferences in the DB
	register_default_prefs("xPub=all&xSort=score");
	
	return true;		
}		 	

/** 
 * 
 * This function was used to diplay a header containing a logo for the service.
 * 
 * @deprecated
 */
function DEFINITION_RDW_DISPLAYHEADER()
{
	return true;
}

/**
 * 
 * ... ?
 */
function DEFINITION_RDW_DISPLAYSEARCHCONTROLS()
{
	return true;
}

/**
 * 
 * ... ?
 * 
 * @param string $chaining_url the string used to send query tokens together to the search engine.
 */
function DEFINITION_RDW_COLLECTRESULTS($chaining_url='')
{
	$res=true;

	// Get all necessary global variables
	global $datasource;
	global $searchsource_baseurl;
	global $REALNAME;
	global $RDW_REQUEST;
	
	foreach ($RDW_REQUEST as $querystringparam => $d)
		eval( "global \${$querystringparam};" );
	
	foreach($REALNAME as $rodin_name=>$needed_name)
	{
		//print "<br>REALNAME:  $rodin_name=>$needed_name";
		if ("${$rodin_name}" <> '') // only if value defined
			$FILTER_SECTION.="&$needed_name=${$rodin_name}";
	}
		
	$res = true;
	
	$publicationChoice = "sSearchInAll=true";
	if ($_REQUEST['xPub'] != "'all'")
		$publicationChoice = "sPublication=" . trim($_REQUEST['xPub'], "'");
	
	$search = explode(",","ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u");
	$replace = explode(",","c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u");
	
	$cleanQuery = str_replace($search, $replace, $q);
	
	$qTokens = explode(',', trim($cleanQuery, ' ,'));
	$parameters = 'sQuery=';
	foreach ($qTokens as $token) {
		$token = trim($token);
		if ($token != '') {
			$parameters .= '"' . rawurlencode($token) . '"' . '%3c%45%54%3e';
		}
	}	
	
	$parameters = substr($parameters, 0, strlen($parameters)-12); // delete last <ET>
	
	$sortingOption = "sSorting=Score,desc";
	if ($_REQUEST['xSort'] != "'score'") {
		if ($_REQUEST['xSort'] == "'dateasc'")
			$sortingOption = "sSorting=IssueDateID,asc";
		else
			$sortingOption = "sSorting=IssueDateID,desc";
	}
		
	$url=$searchsource_baseurl.$publicationChoice."&".$parameters."&".$sortingOption;
	
	//print "<br />URL: $url";
	
	// Process results, store them in the rodin database
	$searchid = new SEARCHID;
	$searchid->sid=$sid;
	$searchid->m=0;
	$searchid->q=$q;
	$searchid->datasource=$datasource;
	
	$sr = new SR;
	$sr->searchid = $searchid;
	$sr->result = array();

//	$filename="/Users/vicho/Desktop/output_dbb.xml";
//	$h=fopen($filename,"w");
	
	// Extract the total number of results available
	// FIXME could be improved
	$totalNumberOfResults = extractNumberOfResults($url);
	$numberOfResultsExported = 0;
	
	//print "<br />Total results : $totalNumberOfResults";
	
	//fwrite($h, "Total results : $totalNumberOfResults\n");
	
	// All these results are then explored five by five.
	//print "<br /> Extract results GO! (Max. $totalNumberOfResults)";

	while ($numberOfResultsExported < $m && $numberOfResultsExported < $totalNumberOfResults) {
		
		$resultsInHTML = getResultsFromURL($url, $numberOfResultsExported);
		
		for ($i=0; $i<5 && ($numberOfResultsExported < $m && $numberOfResultsExported < $totalNumberOfResults); $i++) {
//			fwrite($h, "Result [".$resultsInHTML[$i]['id']."] : ".$resultsInHTML[$i]['text']."\n");
//			fwrite($h, " - Fullview : ".$resultsInHTML[$i]['url']."\n");
//			fwrite($h, " - Header : ".$resultsInHTML[$i]['image']."\n");
			
			//print "<br />[$numberOfResultsExported] Results : " . $resultsInHTML[$i]['text'];
			//print "<br /> - Fullview : " . $resultsInHTML[$i]['url'] . "\n";
			//print "<br /> - Header : " . $resultsInHTML[$i]['image'] . "\n";
			
			// First a separator for each result
			$localresult = new RESULT;
			$localresult->xpointer = $numberOfResultsExported;
			$localresult->row[] = array('', 'string', '', $resultsInHTML[$i]['url'], false, '', 'cr'); // (attribute,type,value,url,visible)
			$sr->result[] = $localresult;
			
			// - Process the result's header text as title
			$resultTitle = new RESULT;
			$resultTitle->xpointer = "$numberOfResultsExported.1";
			$resultTitle->row[] = array('title', 'string', $resultsInHTML[$i]['text'], '', true, 'entry', 'cr');
			$sr->result[] = $resultTitle;
			
			// - Process the result's image url
			$resultImageUrl = new RESULT;
			$resultImageUrl->xpointer = "$numberOfResultsExported.2";
			$resultImageUrl->row[] = array('image', 'img', $resultsInHTML[$i]['image'], $resultsInHTML[$i]['image'], true, 'entry', 'cr');
			$sr->result[] = $resultImageUrl;
			
			// - Process the result's url		
			$resultUrl = new RESULT;
			$resultUrl->xpointer = "$numberOfResultsExported.3";
			$resultUrl->row[] = array('link', 'url', $resultsInHTML[$i]['url'], $resultsInHTML[$i]['url'], true, 'entry', 'cr');
			$sr->result[] = $resultUrl;
			
			$numberOfResultsExported++;
		}
	}
	
	//print "<br /> Extract results END! ($numberOfResultsExported exported)";
	
//	fclose($h);
	
	//print "<br>See (($filename)) for content of webpage";
	
	$sr->searchid->m = $numberOfResultsExported;
	
	return $sr;
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
 * 
 * ... ?
 * 
 * @param unknown_type $w
 * @param unknown_type $h
 */
function DEFINITION_RDW_SHOWRESULT_WIDGET($w,$h)
{
	global $sid;
	global $datasource;
  global $slrq;
	global $render;
		
	render_widget_results($sid,$datasource,$slrq,RDW_widget,$render);
	
	return true; 
}

/**
 * 
 * ... ?
 * 
 * @param unknown_type $w
 * @param unknown_type $h
 */
function DEFINITION_RDW_SHOWRESULT_FULL($w,$h)
{
	global $sid;
	global $datasource;
  global $slrq;
	
	render_widget_results($sid,$datasource,$slrq,RDW_full);
	return true; 
}

/* ********************************************************************************
 * HTML Scraping functions
 ******************************************************************************* */

function getResultsFromURL($url, $fromArticle) {
	$fileContent = get_file_content($url."&StartFrom=".$fromArticle,false);
	
//	$filename="/Users/vicho/Desktop/debugging.txt";
//	$h=fopen($filename,"a");
//	fwrite($h, "RDW_JournalDeGeneve.php ::: fileContent encoding : " . mb_detect_encoding($fileContent) . "\n");
//	fclose($h);
	
	$html = str_get_html($fileContent);
	
	return extractResultsFromHTML($html);
}

/**
 * 
 * This function extracts the results by scrapping HTML code.
 * @param String $html is the HTML code to scrap.
 */
function extractResultsFromHTML($html) {
	$resultsDiv = $html->find('div[id=divSearchRes] ', 0);
	$resultsTBody = $resultsDiv->find('table tbody', 0);
	
	$tBodyChildrenCount = count($resultsTBody->children);
	
	$results = array();
	for ($i=1; $i<$tBodyChildrenCount; $i++) {
		// The <tbody> 's first <tr> element has a script and some hidden fields,
		// it is then of no interest
		
		$result = $resultsTBody->children($i);
		
		// Extract the id of a result from the first script in the current <tr>
		$script = trim($result->find('script',0)->innertext);
		$script = trim(substr($script, 0, stripos($script, "=", 0)));
		$resultId = substr($script, 9, 22);
		
		//print "<br><b>RID : $resultId</b>";
		
		// Find out the result's header text from a link
		$resultAddToFavLink = $result->find('a[id=MyCollItem'.$resultId.']',0);
		$resultText = $resultAddToFavLink->getAttribute("entname");
		//print "<br><b>Title : $resultText</b>";
		
		// Get the link to the full article view and to the header image
		$resultMetadata = $result->find('td[class=MetadataRes]',0);
		// $resultFullArticleURL = urlencode('http://www.letempsarchives.ch' . $resultMetadata->find('a[olv_link]',0)->olv_link);
		$resultFullArticleURL = 'http://www.letempsarchives.ch' . $resultMetadata->find('a[olv_link]',0)->olv_link;
		$resultFullArticleURL = str_replace("&amp;", "&", $resultFullArticleURL);
		//print "<br><b>Full URL : $resultFullArticleURL</b>";
		
		// $resultHeaderImageURL = urlencode('http://www.letempsarchives.ch' . $resultMetadata->find('a[olv_link] img',0)->src);
		$resultHeaderImageURL = 'http://www.letempsarchives.ch' . $resultMetadata->find('a[olv_link] img',0)->src;
		$resultHeaderImageURL = str_replace("&amp;", "&", $resultHeaderImageURL);
		//print "<br><b>Image URL : $resultHeaderImageURL</b>";
		
		// I could add, src to the header's image, date, but they
		// are not helpful to our work.
		$results[] = array("id"=>$resultId, "text"=>$resultText, "url"=>$resultFullArticleURL, "image"=>$resultHeaderImageURL);
	}
	
	return $results;
}

/**
 * 
 * This method scraps the total number of results found. It will use the URL
 * and not the HTML code because the latter will be accessed multiple times anyway.
 * @param String $url the URL to be accessed and scrapped.
 */
function extractNumberOfResults($url) {
	$fileContent = get_file_content($url,false);
	$html = str_get_html($fileContent);
	
	//print '<hr />' . $html . '<hr />';

	return (int) $html->find('span[class=resultsAmount]', 0)->innertext;
}

/* ********************************************************************************
 * Decide which function the state machine is on.
 ******************************************************************************* */

include_once("../u/RodinWidgetSMachine.php");
?>