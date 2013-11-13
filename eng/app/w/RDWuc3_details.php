<?php
//Should use UC3 resources instead of RODIN RESOURCES

include_once('./uc3_widget_precalculations.php');

$APIKEYANUBIS='AIzaSyDgLQbKls3n_bZUCUrEpTJbXCnyxXd_x38';
$APIKEYANUBIS='AIzaSyDgLQbKls3n_bZUCUrEpTJbXCnyxXd_x38';
$APIKEYSERVERAPPS='AIzaSyCsAC9xqBodkhiwdsyzmJ0hQAhE45H-_i8';


//This widget needs a specific css taken from uc3 site
$SPECIAL_CSS=<<<EOS
<link rel="stylesheet" type="text/css" href="../../../../rodinuc3/$RODINSEGMENT/app/css/rodinBoards.css.php" />
EOS;
$SPECIAL_JS=<<<EOS
<script type="text/javascript"
      src="http://maps.googleapis.com/maps/api/js?key=$APIKEYANUBIS&sensor=false">
</script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
EOS;


include_once('./uc3_widget_resource_includer.php');
include_once('./arcUtilities.php');

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




class RDWuc3_details {
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
	
	$res = $widget_classname::show_details();
	
	return true; // stop here
} // DEFINITION_RDW_DISPLAYHEADER
##############################################





##############################################
##############################################
public static function DEFINITION_RDW_DISPLAYSEARCHCONTROLS()
##############################################
##############################################
{
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
	$DEBUG=1;
	global $datadir;
	global $datasource;
	global $searchsource_uri;
	global $REALNAME;
	global $RDW_REQUEST;
	global $WEBROOT,$RODINU,$WEBROOT,$BASERODINROOT,$RODINSEGMENT;
	global $WEBSERVICE;
	
	return false;
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
	global $widget_classname;

	
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

 private static function show_details()
 {
 	
	$DEBUG=0;
	
 	global 	$USER;
	global 	$POSHIMAGES;
	global 	$ICON_WIKIPEDIA;
	global 	$APP_ID;
	global 	$FRAMENAME;
	global	$RDW_REQUEST;
	global 	$lang;
	global 	$widget_classname;
	
	foreach ($RDW_REQUEST as $querystringparam => $d)
	{
		if ($DEBUG) print "<br>RDW_REQUEST eval $querystringparam => $d";
		if ($WEBSERVICE) 
				 eval( "global \${$querystringparam}; \${$querystringparam} = '$d';" );
		else eval( "global \${$querystringparam};" );
	}
	
	$APP_ID_CLEAN=str_replace(':', '-', $APP_ID);
	$x = $APP_ID_CLEAN; // widget obj discriminator
	
	$lblTagCloudeReloadTitle	=lg('lblTagCloudeReloadTitle');
	$lblHistoricalRecency			=lg('lblHistoricalRecency');
	$lblSortHistoricalRecency	=lg('lblSortHistoricalRecency');
	$lblTagCloudeEraseTitle		=lg('lblTagCloudeEraseTitle');
	$lblHistoryReloadTitle		=lg('lblHistoryReloadTitle');
//Display a cloud + history - independently from any query
	$ICON_REFRESH=$POSHIMAGES.'/ico_refresh.gif';
	$ICON_CLOSE=$POSHIMAGES.'/ico_close.gif';
	$MAXFREQTAGS=50;
	$MAXHISTORYTAGS=10000000;
	
	$BARTITLEWIKI="Details";
	
	//PUT DISAMBIGUATOR HERE
	$dbpedia_disambiguated_token=str_replace(' ','_',$q);
	$dbpedia_base="http://dbpedia.org/data";
	$wikipedia_base="http://en.wikipedia.org/wiki";
	//$dbpedia_oneshot_url="http://dbpedia.org/sparql?default-graph-uri=http%3A%2F%2Fdbpedia.org&query=DESCRIBE+%3Chttp://dbpedia.org/resource/".$q."%3format=text/csv";
	
	$NAMESPACES = array(
					'dbpedia'		=> 'http://dbpedia.org/resource/',
					'dbpedia-owl'		=> 'http://dbpedia.org/ontology/',
					);
	
	//print "<br>Try with $dbpedia_disambiguated_token";
	$wikipediaurl = $wikipedia_base.'/'.$dbpedia_disambiguated_token;	
	list($triple,$triple_subject,$triple_predicate,$triple_object) = read_triples_from_dbpedia($dbpedia_base,$dbpedia_disambiguated_token,$search=false,$use_ns=true,$NAMESPACES);
	if ($triple)
	{
		if ($DEBUG)
		{
			print "<hr>";
			foreach($triple as $T)
			{
				list($s,$p,$o)=$T;
				print "<br>($s)($p)($o)";
			}
			print "<hr>";
		} // DEBUG
	} // try some token randomly
	
	
	else if(0) // try one of the tokens (randomly)
	{
		print "<br>exploding Q=($q)";
		$tokens=explode(' ',$q);
		if ($c=count($tokens)>1)
		{
			$x = rand(0,$c - 1);
			$token=$tokens[$x];
			$dbpedia_disambiguated_token=$token;
			
			print "<br>$q was bad. Try with $token ...";
			
			$wikipediaurl = $wikipedia_base.'/'.$dbpedia_disambiguated_token;	
			list($triple,$triple_subject,$triple_predicate,$triple_object) = read_triples_from_dbpedia($dbpedia_base,$dbpedia_disambiguated_token,$search=false,$use_ns=true,$NAMESPACES);
	  }
	}
	
	if ($triple)
	{
		//search for an abstract in the same language as $lang;
		$abstracts 		= search_in_triples('dbpedia-owl:en@abstract','en', $triple,$triple_subject,$triple_predicate,$triple_object, $literal=true);
		$abstract=$abstracts[0];
		
		//search for a thumbnail if existent
		$thumbnailurls = search_in_triples('dbpedia-owl:thumbnail','', $triple,$triple_subject,$triple_predicate,$triple_object, $literal=false);
		$thumbnailurl = $thumbnailurls[0];
		
		//search for a singluar birth place
		$birthplaces = search_in_triples('dbpedia-owl:birthPlace','', $triple,$triple_subject,$triple_predicate,$triple_object, $literal=false);
		$birthplace  =$birthplaces[0];
		
		if ($birthplace)
		{
			if (preg_match("/\w*\:(.*)/",$birthplace,$match)) $birthplace=$match[1];
			
			if ($DEBUG)
			{
				print "<br>birthplace: ".$birthplace; //can be a token!
			}
			print geocodeandshow_googlemaps($birthplace,"gmaps$x");
		}
		
		$dbpedia_disambiguated_token_recleaned = str_replace('_',' ',$dbpedia_disambiguated_token);
		
		
		if ($DEBUG)
		{
			print "<br>Abstract: ".$abstract;
			print "<br>thumbnailurl: ".$thumbnailurl;
		}
	} 
		
	if ($abstract)
	{
	$HTML=<<<EOH
		<div id="wikiboard$x" class="singleRodinUC3Board">
			<div id="cloudBoardContent$x" name="boardContent" class="boardContent">
				<div class="boardConfiguration">
					<img id="cloudBoardIcon$x" src="$ICON_WIKIPEDIA" class="rodinBoardTitleImage" />
					<label class='boardlabel'>$BARTITLEWIKI</label>
				</div>
				<div id="wiki$x" class="wikiBoard">
				<table class='wikitable' onclick="window.open('$wikipediaurl','_blank')" title='Click to see the whole article on wikipedia'>
					<tr>
						<td valign='top'>
							<img src='$thumbnailurl' height='120'/>
						</td>
						<td>
							<table>
								<tr>
									<td><b>
										$dbpedia_disambiguated_token_recleaned
										</b>
								</tr>
								<tr>
									<td>
									$abstract
							</table>
						</td>
					</tr>
				</table>
			</div>
			<div id="gmaps$x" class="mapsBoard">
			</div>			
		</div>
EOH;
	print $HTML;
	// just in case ... this is the only widget and the latest to complete at loading time
	// when this widget presents its data, a cache2 should be hidden
	// to allow use (in case of fresh login)
	}
	print inject_javascript("parent.release_cache2()"); 
	 	
 } // show_details
 
 
 
/* ********************************************************************************
 * Decide which function the state machine is on.
 ******************************************************************************* */
} // class RDWuc3_details



/* ******************************************
 * Utility functions, widget independent.
 ****************************************** */




 





include_once("../u/RodinWidgetSMachine.php");

?>