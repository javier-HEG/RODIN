<?php

//Should use UC3 resources instead of RODIN RESOURCES
include_once('./uc3_widget_precalculations.php');
include_once('./uc3_widget_resource_includer.php');

//This widget needs a specific css taken from uc3 site
$SPECIAL_CSS=<<<EOS
<link rel="stylesheet" type="text/css" href="../../../../rodinuc3/$RODINSEGMENT/app/css/rodinBoards.css.php" />
EOS;

$DEBUG=0;


global $SEARCHSUBMITACTION;

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




class RDWuc3_socialaggwresults
{
	

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

	
	##############################################
	// Publications xml file (english):
	$pubxmlfile='alexpub_en_MCM.xml';
	// Project xml file (english):
	$prxmlfile='alexproj_en_MCM.xml';
	

	##############################################
	// Insert as filter element but dont begin with x =>  dont participate to filter prefs
	add_searchfilter_control('update','update','','',$htmldef,3);

	// Load the default preferences to enable direct use
	// of the widget (without setting any preference).
	register_default_prefs("xi=all&xp=publications");

	return true;
}
##############################################




##############################################
##############################################
public static function DEFINITION_RDW_DISPLAYHEADER()
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
public static function DEFINITION_RDW_DISPLAYSEARCHCONTROLS()
##############################################
##############################################
{
	$res=true;

	return $res;
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
	//return; // DEBUG
	$DEBUG=0;
	global $datadir;
	global $datasource;
	global $searchsource_uri;
	global $REALNAME;
	global $RDW_REQUEST;
	global $WEBROOT,$RODINU,$WEBROOT,$RODINROOT,$RODINUC3ROOT,$RODINSEGMENT;
	global $UC3_SOCIALAGGWRESULTS_WIDGETS, $UC3_SOCIALAGGWRESULTS_M, $UC3_SOCIALAGGWRESULTS_RM, $UC3_SOCIALAGGWRESULTS_THESAURI;
	global $WEBSERVICE,$USER;
		
	if ($WEBSERVICE) //need to set again url:
	{
		$searchsource_uri = "$WEBROOT$RODINU/xmlsearch.php";
	}

	foreach ($RDW_REQUEST as $querystringparam => $d)
	{
		//if ($DEBUG) print "<br>RDW_REQUEST eval $querystringparam => $d";
		if ($WEBSERVICE) 
				 eval( "global \${$querystringparam}; \${$querystringparam} = '$d';" );
		else eval( "global \${$querystringparam};" );
	}

	//This widget should now summarize the aggregated results inside a buch of widgets.
	//It uses a selection of RODIN's widget to get information
	//and displays an aggregated view 

	if (!$UC3_SOCIALAGGWRESULTS_M) {
		fontprint("<br>System error empty UC3_SOCIALAGGWRESULTS_M",'red'); exit;
	}
	if (!$UC3_SOCIALAGGWRESULTS_WIDGETS) {
		fontprint("<br>System error empty UC3_SOCIALAGGWRESULTS_WIDGETS",'red'); exit;
	}
	if (!$UC3_SOCIALAGGWRESULTS_THESAURI) {
		fontprint("<br>System error empty UC3_SOCIALAGGWRESULTS_THESAURI",'red'); exit;
	}
	
	$wm = $UC3_SOCIALAGGWRESULTS_M; // maximal requested results to be retrieved each widget
	$rm = $UC3_SOCIALAGGWRESULTS_RM; // maximal requested results to be retrieved each widget
	
	//Mark/adapt propagated sid to be inside this widget:
	$sid='2.'.$sid;
	
	if ($DEBUG) {
		print "<br> retrieving $UC3_SOCIALAGGWRESULTS_RM results for sid=$sid and datasource=null";
		//exit;
	}
	global $m; $m=$UC3_SOCIALAGGWRESULTS_RM; // for following method
	//Use web service for this and force it to use the current uc3sid:
	//wich max $wm results per datasource and max $M
	$base_url="$WEBROOT$RODINROOT/$RODINSEGMENT/app/webs/search.php";
	$url = str_replace(' ','%20',
						"$base_url?query=$q&widgets=$UC3_SOCIALAGGWRESULTS_WIDGETS&userid=$USER&wm=$wm&m=$rm&sid=$sid&suppress_output=0&DEBUG=0");
	
	// Call $url
	if ($DEBUG) print "<br>URL: <a href='$url' target='blank'>$url</a>";
	$jsonResultsDecoded = json_decode($CONTENT=get_file_content($url));

	if ($DEBUG)
	{
		print "<br><br>USING Param QUERY: $q";
		print "<br>USING Param UC3_SOCIALAGGWRESULTS_WIDGETS: $UC3_SOCIALAGGWRESULTS_WIDGETS";
		print "<br>USING Param UC3_SOCIALAGGWRESULTS_M: $UC3_SOCIALAGGWRESULTS_M";
		print "<br>USING Param UC3_SOCIALAGGWRESULTS_THESAURI: $UC3_SOCIALAGGWRESULTS_THESAURI";
				
		print "<br>";
		print "<br> global param uc3sid: ($sid)";
		print "<br> global param USER: ($USER)";
		print "<br> global param WEBROOT: ($WEBROOT)";
		print "<br> global param RODINROOT: ($RODINROOT)";
		print "<br> global param RODINUC3ROOT: ($RODINUC3ROOT)";
		print "<br> global param RODINSEGMENT: ($RODINSEGMENT)";
		print "<br> param wm: ($wm)";
		print "<br> param rm: ($rm)";
		print "<br>";
	}
	
	//Results are already saved on $sid by the used web service "sarch.php"
	
	if ($DEBUG) {
		print "<br> got possible results ($rm wanted) in sid=($sid) from web service using ($url): CONTENT: ";
		print htmlentities($CONTENT);
	}
	
	//Now we wand to RANK/LODify the results and needs another call ...
	$base_url="$WEBROOT$RODINROOT/$RODINSEGMENT/app/webs/rdflodexpand.php";
	$url = str_replace(' ','%20',
					"$base_url?sid=$sid&thesauries=$UC3_SOCIALAGGWRESULTS_THESAURI&lodsources=&lodsearch=0&userid=$USER"
					."&m=$rm&DEBUG=0");
		
	if ($DEBUG) print "<hr>RANKING SECTION:<br><a href='$url' target='blank'>$url</a>";
	
	$jsonRankedResultsDecoded = json_decode($CONTENT=file_get_contents($url));
	
	$sid 						= $jsonRankedResultsDecoded->sid;
	$resultCount		= $jsonRankedResultsDecoded->all;
	$jsonAllResults = $jsonRankedResultsDecoded->results;
	
	if ($DEBUG) {
		print "<br> got $resultCount possible ranked results ($rm wanted) in sid=($sid) from ranking web service using ($url): ";
		print htmlentities($CONTENT);
	}
	
	if ($DEBUG) {
		print '<br>EXIT'; 
		
		exit;
	}
	
	return count($jsonAllResults);
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
	global $UC3_SOCIALAGGWRESULTS_WIDGETS, $UC3_SOCIALAGGWRESULTS_M, $UC3_SOCIALAGGWRESULTS_RM;
	
	//return; // debug
	$DEBUG=0;

	global $WRESICON;
	
	$WRESICON="";
	$BARTITLE="Social results";

		
	//Mark/adapt propagated sid to be inside this widget:
	$sid='2.'.$sid;	
		
	if ($DEBUG) {
		print "<br> retrieving $UC3_SOCIALAGGWRESULTS_RM results for sid=$sid and datasource=null, $slrq, $render";
		//exit;
	}
	
	//TODO: ONly when there ARE SOME RESULTS: (call count results!!!!)
	{
	$htmldef=<<<EOH
<div class="boardConfigurationWidget">
	<img src="$WRESICON" class="rodinBoardTitleImage" />
	<label class='boardlabel'>$BARTITLE</label>
	</div>	
EOH;

	print $htmldef;
	
	global $m; $m=$UC3_SOCIALAGGWRESULTS_RM; // for following method
	RodinResultManager::renderAllResultsInWidget($sid, $datasource=null, $slrq, $render);
	}
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
  
	//Mark/adapt propagated sid to be inside this widget:
	$sid='2'.$sid;	
		
	if ($DEBUG) {
		print "<br> retrieving $UC3_SOCIALAGGWRESULTS_RM results for sid=$sid and datasource=null, $slrq, $render";
		//exit;
	}
	global $m; $m=$UC3_SOCIALAGGWRESULTS_RM; // for following method
	RodinResultManager::renderAllResultsInOwnTab($sid,$datasource_null,$slrq);
	
	return true; 
}


/* ******************************************
 * Utility functions, widget dependent.
 ****************************************** */


/* ********************************************************************************
 * Decide which function the state machine is on.
 ******************************************************************************* */

} // class RDWuc3_socialaggwresults

include_once("../u/RodinWidgetSMachine.php");

?>