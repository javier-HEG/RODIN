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




class RDWuc3_tagchistory {
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
	$res = $widget_classname::show_taghistory();
	
	return false; // stop here

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
	$res = $widget_classname::show_taghistory();
	
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

 private static function show_taghistory()
 {
 	global $USER;
	global $POSHIMAGES;
	global $TAG_CLOUD_ICON;
	global $APP_ID;
	global $FRAMENAME;
	
	$APP_ID_CLEAN=str_replace(':', '-', $APP_ID);
	$x = $APP_ID_CLEAN; // widget obj discriminator
	
	$lblTagCloudeReloadTitle	=lg('lblTagCloudeReloadTitle');
	$lblHistoricalRecency			=lg('lblHistoricalRecency');
	$lblSortHistoricalRecency			=lg('lblSortHistoricalRecency');
	$lblTagCloudeEraseTitle		=lg('lblTagCloudeEraseTitle');
	$lblHistoryReloadTitle		=lg('lblHistoryReloadTitle');
//Display a cloud + history - independently from any query
	$ICON_REFRESH=$POSHIMAGES.'/ico_refresh.gif';
	$ICON_CLOSE=$POSHIMAGES.'/ico_close.gif';
	$MAXFREQTAGS=50;
	$MAXHISTORYTAGS=10000000;
	
	$HTML=<<<EOH
		<div id="cloudboard$x" class="singleRodinUC3Board">
			<div id="cloudBoardContent$x" name="boardContent" class="boardContent">
				<div class="boardConfiguration">
					<img id="cloudBoardIcon$x" src="$TAG_CLOUD_ICON" class="rodinBoardTitleImage" />
					<label class='boardlabel'>Tag Cloud</label>
					<button id="tagCloudReloadButton$x" title="$lblTagCloudeReloadTitle" class="cloudbutton"
						onclick=" parent.refreshCloudBoard($USER,'$FRAMENAME','frequency$x','$APP_ID_CLEAN','frequency',$MAXFREQTAGS);"
						><img src="$ICON_REFRESH" /></button>
					<button id="tagCloudEraseButton$x" title="$lblTagCloudeEraseTitle" class="cloudbutton"
						onclick="if(confirm(lg('lblConfirmWant2EraseTagCloud'))) { parent.resetCloudBoard($USER); }"
						><img src="$ICON_CLOSE" /></button>
				</div>
				<div id="frequency$x" class="tagcloudfrequency">
				</div>
				<div class="boardConfiguration">
					<img id="cloudBoardIcon$x" src="$TAG_CLOUD_ICON" class="rodinBoardTitleImage" />
					<label class='boardlabel'>Search History</label>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<label class='boardselectlabel'>Sort:</label>
					<select id="selectsort$x" class="boardtagcloudselect" title="$lblSortHistoricalRecency"
						onchange="parent.refreshCloudBoard($USER,'$FRAMENAME','recency$x','$APP_ID_CLEAN','recency',$MAXHISTORYTAGS,this.value);" >
						<option value="norhc" selected="selected">inv. chronologically</option>
						<option value="chron">chronologically</option>
						<option value="alfa">alphabetically</option>
						<option value="afla">inv. alphabetically</option>
					</select>
					<button id="tagCloudReloadButton$x" title="$lblHistoryReloadTitle" class="cloudbutton"
						onclick=" parent.refreshCloudBoard($USER,'$FRAMENAME','recency$x','$APP_ID_CLEAN','recency',$MAXHISTORYTAGS,jQuery('#selectsort$x').val());"
						><img src="$ICON_REFRESH" /></button>
					<button id="tagCloudEraseButton$x" title="$lblTagCloudeEraseTitle" class="cloudbutton"
						onclick="if(confirm(lg('lblConfirmWant2EraseTagCloud'))) { parent.resetCloudBoard($USER); }"
						><img src="$ICON_CLOSE" /></button>
				</div>
				<div id="recency$x" class="tagcloudrecency">
				</div>
				<script type="text/javascript">
					parent.refreshCloudBoard($USER,'$FRAMENAME','frequency$x','$APP_ID_CLEAN','frequency',$MAXFREQTAGS,'');
					parent.refreshCloudBoard($USER,'$FRAMENAME','recency$x','$APP_ID_CLEAN','recency',$MAXHISTORYTAGS,'norhc');
				</script>
			</div>
		</div>
EOH;
	print $HTML;
	// just in case ... this is the only widget and the latest to complete
	// when this widget presents its data, a cache2 should be hidden
	// to allow use (in case of fresh login)
	print inject_javascript("parent.release_cache2()"); 
	 	
 } // show_taghistory
 
 

/* ********************************************************************************
 * Decide which function the state machine is on.
 ******************************************************************************* */

} // class RDWuc3_tagchistory

include_once("../u/RodinWidgetSMachine.php");

?>