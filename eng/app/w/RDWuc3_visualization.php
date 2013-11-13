<?php
//Should use UC3 resources instead of RODIN RESOURCES

include_once('./uc3_widget_precalculations.php');

//This widget needs a specific css taken from uc3 site
$SPECIAL_CSS=<<<EOS
<link rel="stylesheet" type="text/css" href="../../../../rodinuc3/$RODINSEGMENT/app/css/rodinBoards.css.php" />
EOS;

$SPECIAL_JS=<<<EOS
<script type="text/javascript" src="../../../../rodinuc3/$RODINSEGMENT/app/u/facetBoardInterface.js.php"></script>
<script type="text/javascript" src="../../../../rodinuc3/$RODINSEGMENT/app/u/RODINsemfilters.js.php"></script>
<script type="text/javascript" src="../../../../rodinuc3/$RODINSEGMENT/app/u/RODINsurvista.js.php"></script>
EOS;


include_once('./uc3_widget_resource_includer.php');

global $SEARCHSUBMITACTION;
$widget_icon_width = 55;
$widget_icon_height = 20;


if (!$WEBSERVICE)
{
	print_htmlheader("SEARCH DETAILS");
	
##############################################
# HTML SEARCH CONTROLS:
##############################################

// Query input : q (rodin internal query tag)
##############################################
$title=lg("titleWidgetTypeSearch");

if ($WANT_WIDGET_SEARCH)
{
$htmldef=<<<EOH
	<input class="localSearch" name="q" type="text" value="$qx" title='$title' onchange="$SEARCHSUBMITACTION">
EOH;
add_search_control('q',$qx,'$q',$htmldef,1);
##############################################

// Number of results : m (default)
##############################################
$title=lg("titleWidgetMaxResults");
$m = $_REQUEST['m']; if(!$m) $m=$DEFAULT_M;
$htmldef=<<<EOH
	<input class="localMaxResults" name="m" type="text" value="$m" title='$title'/>
EOH;
add_search_control('m',$m,20,$htmldef,1);
##############################################

// Search Button : ask (default)
##############################################
$title=lg("titleWidgetButtonAsk");
$label=lg("labelWidgetButtonAsk");
$htmldef=<<<EOH
	<input name="ask" class="localSearchButton" type="button" onclick="$SEARCHSUBMITACTION" value="$label" title='$title'/>
EOH;
add_search_control('ask','','',$htmldef,1);
}
##############################################
} // WEBSERVICE




class RDWuc3_visualization {
##############################################
##############################################
public static function DEFINITION_RDW_SEARCH_FILTER()
##############################################
##############################################
{
	global $SEARCHFILTER_TEXT_SIZE;
	global $RODINUTILITIES_GEN_URL;
	global $FORMNAME;
	global $thisSCRIPT;

	##############################################
	# Each filter param is prefixed by "x"
	# You have to provide a real name also
	##############################################
	# Site xcc (in rerodoc  real name: cc)
	# Please insert value=''
	##############################################
	
	// Defint some style for controls
	global $_w;
	$w=$_w - 15; // fix width in accordance to Widget desired width
	
	$STYLE =<<<EOS
		style="min-width: {$w}px; max-width: {$w}px; width : {$w}px;";
EOS;


	return true;
}
##############################################




##############################################
##############################################
public static function DEFINITION_RDW_DISPLAYHEADER()
##############################################
##############################################
{
	global $widget_classname;

	//Show directly infos
	
	return true; // stop here

} // DEFINITION_RDW_DISPLAYHEADER
##############################################





##############################################
##############################################
public static function DEFINITION_RDW_DISPLAYSEARCHCONTROLS()
##############################################
##############################################
{
	global $widget_classname;

	//Show directly infos
	$res = $widget_classname::show_free_term_in_survista();
	
	return false;
} // DEFINITION_RDW_DISPLAYSEARCHCONTROLS
##############################################








/**
 * Method called from RodinWidgetSMachine.RDW_COLLECTRESULTS_EPI() to collect
 * results from the source and save them to the database. It used to return a
 * table with an old structure for results but now returns the number of results
 * found only.
 * 
 * @param string $chaining_url
 */
public static function DEFINITION_RDW_COLLECTRESULTS($chaining_url = '') 
{
	$DEBUG=0;
	global $datadir;
	global $datasource;
	global $searchsource_uri;
	global $REALNAME;
	global $RDW_REQUEST;
	global $WEBROOT,$RODINU,$WEBROOT,$BASERODINROOT,$RODINSEGMENT;
	global $WEBSERVICE;
		
	if ($WEBSERVICE) //need to set again url:
	{
	}

	foreach ($RDW_REQUEST as $querystringparam => $d)
	{
		if ($DEBUG) print "<br>RDW_REQUEST eval $querystringparam => $d";
		if ($WEBSERVICE) 
				 eval( "global \${$querystringparam}; \${$querystringparam} = '$d';" );
		else eval( "global \${$querystringparam};" );
	}

	$datasource=get_called_class();

	
	return 0;
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
	
	$res = $widget_classname::show_free_term_in_survista();

	
	return true; 
}

/**
 * Called from RodinWidgetSMachine.RDW_SHOWRESULT_FULL_EPI(), it is asked
 * to print the HTML code corresponding to results.
 */
public static function DEFINITION_RDW_SHOWRESULT_FULL($w,$h) {
	global $sid;
	global $datasource;
	global $slrq;
  global $WIDGET;
	
	return true; 
}


/* ******************************************
 * Utility functions, widget dependent.
 ****************************************** */

 private static function show_free_term_in_survista()
 {
 	$DEBUG=0;
 	global $USER;
	global $POSHIMAGES;
	global $VISUALIZATION_ICON;
	global $APP_ID;
	global $FRAMENAME;
	global $RDW_REQUEST;
	global $RODINSEGMENT;
	global $ICON_REFRESH;
	
	$TEST=1; // USE FIX TERMS FOR TESITNG THE VISUALIZER
	
	
	foreach ($RDW_REQUEST as $querystringparam => $d)
	{
		//if ($DEBUG) print "<br>RDW_REQUEST eval $querystringparam => $d";
		if ($WEBSERVICE) 
				 eval( "global \${$querystringparam}; \${$querystringparam} = '$d';" );
		else eval( "global \${$querystringparam};" );
	}

	$APP_ID_CLEAN=str_replace(':', '-', $APP_ID);
	$x = $APP_ID_CLEAN; // widget obj discriminator
	
	$BARTITLE='Connections';
	
	if ($TEST)
	{
		$TESTTERM="Advertising Psychology and Consumer Behaviour";
		$TESTTERMURI= "http://zbw.eu/stw/thsys/70292";
		$TERM=$TESTTERM;
		$TERMURI=$TESTTERMURI;
	}
	
	//PUT DISAMBIGUATOR HERE
	$dbpedia_disambiguated_token=str_replace(' ','_',$q);
	$dbpedia_base="http://dbpedia.org/data";
	$wikipedia_base="http://en.wikipedia.org/wiki";
	//$dbpedia_oneshot_url="http://dbpedia.org/sparql?default-graph-uri=http%3A%2F%2Fdbpedia.org&query=DESCRIBE+%3Chttp://dbpedia.org/resource/".$q."%3format=text/csv";
	
	$NAMESPACES = array(
				//	'dbpedia'				=> 'http://dbpedia.org/resource/',
					'dbpedia-owl'		=> 'http://dbpedia.org/ontology/',
					'dcterms' 			=> 'http://purl.org/dc/terms/'
					);
	
	$wikipediaurl = $wikipedia_base.'/'.$dbpedia_disambiguated_token;
	
	if ($DEBUG) print "<br>$wikipediaurl";
	list($triple,$triple_subject,$triple_predicate,$triple_object) = read_triples_from_dbpedia($dbpedia_base,$dbpedia_disambiguated_token,$search=false,$use_ns=true,$NAMESPACES);
	
	if ($triple)
	{
		if ($DEBUG && 0)
		{
			print "<hr>";
			foreach($triple as $T)
			{
				list($s,$p,$o)=$T;
				print "<br>($s)($p)($o)";
			}
			print "<hr>";
		}
		//search for an abstract in the same language as $lang;
		$categories 		= search_in_triples('dcterms:subject','', $triple,$triple_subject,$triple_predicate,$triple_object, $literal=false);
		if (count($categories))
		{
			$cnt= count($categories);
			$idx= rand(0,$cnt - 1);
			
			$category=$categories[$idx];
			if ($DEBUG) 
			{
				print "<br>Visualization of categories: ";	
				var_dump($categories);
				print "<br>";
			}
			$TERM= str_replace('_',' ',str_replace('http://dbpedia.org/resource/Category:','',urldecode($category)));
			$TERMURI=str_replace('dcterm:',$NAMESPACES{'dcterm'},$category);
		
			if ($DEBUG || 1)
			{
				print "Starting graph with category=$category TERM=($TERM) and TERMURI=($TERMURI)";
			}
	
	$dbpedia_disambiguated_token_recleaned = str_replace('_',' ',$dbpedia_disambiguated_token);
	$lblClickToReloadSurvistaFrame = 'Click to show (if any) other categories of "'.$dbpedia_disambiguated_token_recleaned.'"';
	$HTML=<<<EOH
		<div id="VistualBoard$x" class="singleRodinUC3Board">
			<div id="survistaBoardContent$x" class="boardContent">
				<div class="boardConfiguration">
					<img id="cloudBoardIcon$x" src="$VISUALIZATION_ICON" class="rodinBoardTitleImage" />
					<label class='boardlabel'>$BARTITLE</label>
					<button id="tagCloudReloadButton$x" title="$lblClickToReloadSurvistaFrame" class="cloudbutton"
						onclick=" var iframe = parent.document.getElementById('$FRAMENAME');iframe.src = iframe.src;"
						><img src="$ICON_REFRESH" /></button>
				</div>
				<div id="visualizer$x" class="visualizer">
					<iframe id="{$FRAMENAME}survista$x" class="survista_visualizer" width="100%" height="100%" frameborder="0" scrolling="no">
					</iframe>
				</div>
				<script type="text/javascript">
					placeSurvista('$TERMURI', '$TERM', '$lang', '$RODINSEGMENT', "{$FRAMENAME}survista$x");  
				</script>
			</div>
		</div>
EOH;
			print $HTML;
		} // count($categories)
		// just in case ... this is the only widget and the latest to complete
		// when this widget presents its data, a cache2 should be hidden
		// to allow use (in case of fresh login)
		// print inject_javascript("parent.release_cache2()"); 
		 	
	 } // $triple
 } // show_free_term_in_survista
 

/* ********************************************************************************
 * Decide which function the state machine is on.
 ******************************************************************************* */

} // class RDWuc3_visualization

include_once("../u/RodinWidgetSMachine.php");

?>