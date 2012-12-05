<?php
// Load the session used by posh
session_name('myhomepage');
session_start();
	if (!isset($_SESSION['lang'])) {
		if (isset($_REQUEST['l10n']) && $_REQUEST['l10n'] != 'undefined') {
			$_SESSION['lang'] = $_REQUEST['l10n'] != '' ? $_REQUEST['l10n'] : 'en';
		}
	}
	
	require_once("FRIdbUtilities.php");
	include_once "$DOCROOT/$RODINUTILITIES_GEN_URL/simplehtmldom/simple_html_dom.php";
	
	$CACHE_EXTENSION='rodin'; //used by apache mod_rewrite to cache .php ext.

	// CONSTANTS FOR WIDGETS INTERFACE
	define ("RDW_widget",'RDW_widget'); // open as widget
	define ("RDW_full",'RDW_full'); // open as widget

	// STATE MACHINE
	define ("RDW_DISPLAYHEADER", 0); // 
	define ("RDW_DISPLAYSEARCHCONTROLS", 1); // 
	define ("RDW_COLLECTRESULTS", 2); // 
	define ("RDW_STORERESULTS", 3); // 
	define ("RDW_SHOWRESULT_WIDGET", 4); // 
	define ("RDW_SHOWRESULT_FULL", 5); // 
	
	$WIDGETSEARCHBUTTON_SIZE = 17;
	
	$thisSCRIPT = $_SERVER['SCRIPT_NAME'];
	$thisSCRIPT = str_replace(".php", ".rodin", $thisSCRIPT); // cache real url
	
	$remoteuser = $_ENV['USER'];

	// The Widget form name:
	$FORMNAME='sf_'.uniqid();
	$WID= $WIDGET_CALL_INSTANCE_ID = uniqid();
		
	// If the user want to add extra search control 
	// this array will be filled by add_search_control
	$SEARCHcontrol = array();
	$SEARCHFILTERcontrol = array();
	$QS_VALUE = array();
	$REALNAME = array(); // translation RODIN->app
	$DBUSERPREF = array(); // DB values of search filter 
	
	$TAB_DB_ID = $_REQUEST['prof']; // 0, 1, 2 ... tab_db_id of the current tab
	$WIDGET_ID = $_REQUEST['p']; // uniq id
	$skin = $_GET['skin']; // uniq id
	
	//thee app id identifies a widget(p) for a user(pid) inside a tab(prof)
	$PID = $_REQUEST['pid'];
	
	$APP_ID = $_REQUEST['app_id'];
	if (!is_a_value($APP_ID)) {
		$APP_ID = $_REQUEST['pid'] . ':' . $_REQUEST['prof'] . ':' . $_REQUEST['p'];
		$_REQUEST['app_id']=$APP_ID;
	}
	
	if (!is_a_value($THIS_TAB) && is_a_value($APP_ID)) {	
		$TTT = explode(':', $APP_ID);
		$THIS_TAB = $TTT[1];
	}
	
	if (!is_a_value($WIDGET_ID) && is_a_value($APP_ID)) {	
		$TTT = explode(':', $APP_ID);
		$WIDGET_ID = $TTT[2];
	}
	
	if (!is_a_value($USER_ID) && is_a_value($APP_ID)) {	
		$TTT = explode(':', $APP_ID);
		$USER_ID = $TTT[0];
	}
	
	if ($TAB_DB_ID) {
		$FRAMENAME = 'modfram'.$TAB_DB_ID.'_'.$WIDGET_ID; 
	} else if ($THIS_TAB) {
		$FRAMENAME = 'modfram'.$THIS_TAB.'_'.$WIDGET_ID;
	} 
	
	$datasource = datasource_enhance($thisSCRIPT, $APP_ID);

	// Standard parameter RODIN WIDGET
	$RDW_REQUEST['show'] = RDW_widget;
	$RDW_REQUEST['sid'] = 0;
	$RDW_REQUEST['nosrc'] = 0;
	$RDW_REQUEST['slrq'] = ''; //solr extra select/ranking info
	$RDW_REQUEST['n'] = $DEFAULTRODINSKIN;
	$RDW_REQUEST['m'] = 10;
	$RDW_REQUEST['q'] = 0;

	$RDW_REQUEST['textZoomRender'] = is_a_value($_REQUEST['textZoomRender']) ? $_REQUEST['textZoomRender'] : 'token';
	$render = $RDW_REQUEST['textZoomRender'];
	
	$_W = $_REQUEST['_w'];
	if (!is_a_value($_W)) {
		$_W=300;
	}
	
	if (is_a_value($APP_ID)) {
		$RDW_REQUEST['app_id']	= '$APP_ID';
	}
	
	if (is_a_value($USER_ID)) {
		$RDW_REQUEST{'pid'}	= '$USER_ID';
	}
	
	$RDW_REQUEST['uncache'] = 0;
	$RDW_REQUEST['_w'] = $_W; //iframe width
	$RDW_REQUEST['_h'] = -1; //iframe height
	$RDW_REQUEST['_x'] = -1; //iframe id - if not -1

	$RDW_DELETE_PREFS = is_a_value($_REQUEST['delete']);
	
	//useful constant for defining DISPLAY CONTROL OPTIONS:
	$ANY = '(Any)';

	// Eval get request using $RDW_REQUEST see below:
	evalRDW_REQUEST();

	//Get widget infos from PORTANEO DATABASE:
	$WIDGET_INFOS = fri_get_dir_infos($thisSCRIPT);
	$minwidth = $WIDGET_INFOS['minwidth'];
	
	$Query = "select * from dir_item where url like \"%$thisSCRIPT%\";";
	$widgetData	= fetch_record($Query,'posh');
	$headerAreaHeight = 27; // header area height
	$widgetVertScrollDelta = 12; //on the right this take some pixels
	$widgetInnerWidth = $widgetData['minwidth'];
	$widgetInnerWidthPics = $widgetInnerWidth - $widgetVertScrollDelta;
	$widgetInnerHeight = $widgetData['height'];
	$widgetInnerHeightPics = $widgetInnerHeight - $headerAreaHeight;
  	$datasourcename = $widgetData['name'];
 
    $widget_icon_width=20;  // should be changed by Widget
    $widget_icon_height=20;

	// For local search field in widget
	$qx = stripslashes($_REQUEST['q']);
	if (!$qx) {
		$qx=stripslashes($_REQUEST['qe']);
	}
    
	// This action is the same for all widgets
	$SEARCHSUBMITACTION = "correctParent.\$p.ajax.call('../../app/tests/LoggerResponder.php?action=9&query=' + document.$FORMNAME.q.value + '&name=' + get_datasource_name('$datasource'), {'type':'load'}); "
		. "checksidandsubmit(document.$FORMNAME,document.$FORMNAME.sid,'$_W','$rodinuserid','$FRAMENAME');";
		
	/**
	 * Returns the opening tag for the div to be held in the widget's iFrame. This div
	 * contains a hidden global zen filter box.
	 * 
	 * @param String $widgetdivid
	 * @param int $iframe_height
	 * @param int $headerAreaHeight
	 */
 	function make_widget_div($widgetdivid, $iframe_height, $headerAreaHeight) {
   	$widgetInnerHeight = $iframe_height - $headerAreaHeight;
 		$widgetSpotlightBox = '<div class="spotlightbox" style="visibility:hidden;" id="spotlight-box-' . $widgetdivid . '" title=""></div>';
		return '<div class="widgetResultsDiv" id="$widgetdivid" style="height: ' . $widgetInnerHeight . 'px;\">' . $widgetSpotlightBox;
	}

	/**
	 * Returns the opening tags for the widget's hidden preferences div.
	 * 
	 * @param String $filterdivid
	 * @param int $iframe_width
	 */
	function make_filter_div($filterdivid,$iframe_width) {
		global $widgetData; // take again and recompute on $headerAreaHeight
		$widgetVertScrollDelta = 16; //on the right this take some pixels
		$widgetInnerWidth = $iframe_width - $widgetVertScrollDelta;
		
		$DIV =<<<EOD
			<div name="filter-div" id="$filterdivid"
				style="z-index:100;position:relative;overflow:auto; bordercolor:black; border:1;
				overflow-y:hidden; overflow-x:hidden; background-color:$COLOR_PAGE_BACKGROUND2;
				width:100%; visibility:hidden; display:none;">
				<div class="widgetPrefsDiv">
EOD;
		return $DIV;
	}



  function datasource_enhance($datasource)
	{
		global $APP_ID;
		
		//delete the .rodin or .php extension and add the app_id
		$datasource_cleaned=preg_replace("/^(.*)\/e\/w\/(.*)\.(\w+)$/","$2",$datasource);
		return $datasource_cleaned;
	}



	function evalRDW_REQUEST()
	##########################################
	// Eval get request using $RDW_REQUEST :
	{
		global $RDW_REQUEST;
		global $RDW_POST;
		
		if ($RDW_POST) 
			 $METHODE='POST';
		else $METHODE='REQUEST';
		 
		foreach ($RDW_REQUEST as $querystringparam => $defaultvalue)
		{
			
			$STR = 	"global \${$querystringparam};".
					"\${$querystringparam}=\$_{$METHODE}['{$querystringparam}'];";
			
			if ($defaultvalue)
			
				$STR.=" if (!\${$querystringparam}) \${$querystringparam}=$defaultvalue;";
			
			//print "<br>evalRDW_REQUEST:  $STR";
			eval($STR);
		
		}
	
	}// evalQUERYSTRING_PHP
	
	
	function makeRDW_qs_params()
	##########################################
	// returns segments x=$x&y=$y ... for querystring using $RDW_REQUEST :
	{
		global $RDW_REQUEST;
		global $REALNAME;
		global $RDW_POST;
		global $USER_ID;
		global $APP_ID;
		global $CLONEDFROM_APPID;
		
		foreach ($RDW_REQUEST as $querystringparam => $defaultvalue) eval( "global \${$querystringparam};" );
	 
		foreach ($RDW_REQUEST as $querystringparam => $xx)
		{
		
			//print "<br>makeRDW_qs_params: querystringparam:$querystringparam und realname:".$REALNAME{$querystringparam}."";
			
			#if ($REALNAME{$querystringparam})
			#{
			#	$name = $REALNAME{$querystringparam};
			#}	
			#else
			
				$name = $querystringparam;
				
				
 			eval("\$value = \"\${$querystringparam}\";");
			if ($STR && substr($STR,strlen($STR)-1,1)<>'&') $STR.='&';
			$hide_it=false;
				
			if ($name=='q') // special case
			{
				$value=addslashes($_GET['q']);
				//print "<br>YES: q=$value";
			}
			else if ($name=='app_id' && $value=='') // special case
			{
				$value=$APP_ID;
				//print "<br>YES: q=$value";
			}
			else	
			if ($name=='qe') // special case
			{
				$hide_it=true;
				$name='qq'; // qe must desapper, otherwise: loop
				$value=$_GET['qe'];
				//print "<br>YES: qe=$value";
			}			
	
			if (!$hide_it)
			$STR .= 	"{$name}={$value}"; // set only the translated name!
			
			//print "<br>evalRDW_REQUEST:  $name=$value";
		}
		
			if (strpos($STR,'app_id')===false)
				$STR.="&app_id=$APP_ID";
			if (strpos($STR,'pid')===false)
				$STR.="&pid=$USER_ID";
				
			//print "<hr>evalRDW_REQUEST returns: $STR";	

		return $STR;
	
	}// evalQUERYSTRING_PHP

	
	
	
	
	
	function check_recompute($sid,$datasource,$remoteuser)
	######################################################
	{
		if ($sid=='')
		{
			$sid= compute_sid($datasource,$remoteuser);
			//print "<br><b>FRI: sid recomputed: $sid</b>";
		}
		
		return $sid;
	}
	
	

	function add_searchfilter_control($name,$realname,$value,$defaultvalueQS,$htmldef,$pos)
	#######################################################################
	#
	# Adds an extra search filter control between the standard search controls and the go button
	#
	{
		global $SEARCHFILTERcontrol;
		
		$res=add_control(&$SEARCHFILTERcontrol,$name,$realname,$value,$defaultvalueQS,$htmldef,$pos);
		//print "<br>$res elements SEARCHFILTERcontrol";
		return $res;
	} // add_searchfilter_control
	
	function add_search_control($name,$value,$defaultvalueQS,$htmldef,$pos)
	#######################################################################
	#
	# Adds an extra search control between the standard search controls and the go button
	#
	{
		global $SEARCHcontrol;
	
		$res= add_control(&$SEARCHcontrol,$name,'',$value,$defaultvalueQS,$htmldef,$pos);
		
		//print "<br>$res elements SEARCHcontrol";
	
		return $res;
	} // add_search_control
	
	
	
	function add_control(&$CONTROL,$name,$realname,$value,$defaultvalueQS,$htmldef,$pos)
	#######################################################################
	#
	# Adds ()to $CONTROL an extra search control between the standard search controls and the go button
	# 
	{
		global $RDW_REQUEST;
		global $REALNAME;
		global $QS_VALUE;
		evalRDW_REQUEST(); // take values from Query string if any
		$RDW_REQUEST{$name}=$defaultvalueQS; // add to be read from query string
		
		$CONTROL[] = array($htmldef,$pos,$name,$value);
		$QS_VALUE{$name}=$value;
	
		if ($realname)
			$REALNAME{$name}=$realname;
	
		$cnt= count($CONTROL);
	
		if (0) // debug
		{
			print "<hr>CONTROL hat jetzt: $cnt Elemente";
			foreach ( $CONTROL as $C)
			{
				$linenr++;
				list($HTMLCONTROL,$linenr) = $C;
			
				print "<br>$linenr: ".urlencode($HTMLCONTROL);
			}
		}
		
		return $cnt;
	} // add_control
	

	function make_ajax_widget_filename($userneeedfilename='thisWidget') {
		if (!$userneeedfilename) {
			return null;
		} else {
			global $CACHE_EXTENSION;
			global $thisSCRIPT; // "e.g. /$RODINROOT/st/app/w/RDW_bla.php
			
			$widget_basename = basename($thisSCRIPT);

			if ($userneeedfilename == 'thisWidget') {
				$ajax_basename = str_replace(".$CACHE_EXTENSION",".js.php",$widget_basename);
			} else {
				$ajax_basename = "$userneeedfilename";
			}
				
			$ajaxfilename = str_replace($widget_basename, $ajax_basename, $thisSCRIPT);
			
			return $ajaxfilename;
		}
	}


	/**
	 * Generates the HTML header for the widgets
	 * @param String $windowtitle
	 * @param String $AJAX_INCLUDE
	 * @param String $EXTRAINCLUDES
	 */
	function print_htmlheader($windowtitle, $AJAX_INCLUDE = null, $EXTRAINCLUDES = null) {
		global $DOCROOT, $RODINSEGMENT, $RODINUTILITIES_GEN_URL;
		global $EXTRAINCLUDE_GOOGLE_LANGUAGE_LOAD;
		global $COLOR_WIDGET_BG, $COLOR_PAGE_BACKGROUND, $COLOR_PAGE_BACKGROUND2;
		global $RODINSKIN;
    global $RODINU;
    global $RODINSEGMENT;
		
		$ADDITIONALINCLUDES = "\n$EXTRAINCLUDE_GOOGLE_LANGUAGE_LOAD";
		
		// Don't generate if the call comes from an AJAX request
		$RDW_GENERICAJAXREQUEST = is_a_value($_REQUEST['ajax']);
		if (!$RDW_GENERICAJAXREQUEST) {
			print <<<EOP
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<script type="text/javascript" src="../../app/exposh/l10n/{$_SESSION["lang"]}/lang.js" ></script>
	<script type="text/javascript" src="../u/RODINutilities.js.php?skin=$RODINSKIN" > </script>
	<script type='text/javascript' src='../../app/u/RODINsemfilters.js.php?skin=<?php print $RODINSKIN;?>'></script>
	<script type="text/javascript" src="../u/querystring.js" > </script>
	<link rel="stylesheet" type="text/css" href="../css/rodinwidget.css.php" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
EOP;

			if ($AJAX_INCLUDE) {
				if (file_exists($DOCROOT . $AJAX_INCLUDE)) {
					if (fopen($DOCROOT . $AJAX_INCLUDE, "a")) {
						print include_javascript($AJAX_INCLUDE);
					} else {
						fontprint("Problem opening $DOCROOT$AJAX_INCLUDE ...",'red');
					} 
				} else {
					fontprint("File does not exist $DOCROOT$AJAX_INCLUDE ...",'red');
				}
			}

			if ($EXTRAINCLUDES != null) {
				$ADDITIONALINCLUDES .= $EXTRAINCLUDES;
			}

			$restrictToOntoTermLabel = lg('lblContextMenuRestrictToOntoTermX1');
			$addToBreadcrumbLabel = lg('lblContextMenuAddToBreadcrumb');
			$exploreOntologicalFacetsLabel = lg('lblContextMenuExploreOntoFacets');

			$appIdElements = explode(':', $_REQUEST['app_id']);
			$iFrameIdFromAppId = "modfram{$appIdElements[1]}_{$appIdElements[2]}";

			$widgetDatasource = datasource_enhance(str_replace(".php", ".rodin", $_SERVER['SCRIPT_NAME']), $_REQUEST['app_id']);
      $HOVERIN_RESTRICT="onmouseover=\"var t=document.getElementById('widgetContextMenuLabel').innerHTML; simple_highlight_semfilterresults(t,true)\"";
      $HOVEROUT_RESTRICT="onmouseout=\"var t=document.getElementById('widgetContextMenuLabel').innerHTML; simple_highlight_semfilterresults(t,false)\"";

			print<<<EOP
	$ADDITIONALINCLUDES
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<meta name="author" content="Fabio Ricci, fabio.fr.ricci@hesge.ch, tel.+41-76-5281961" /> 
	<title>$windowtitle ($RODINSEGMENT)</title>
	
	<!-- JB : For idle timer & contextMenu -->
	<script type="text/javascript" src='../../../gen/u/jquery/jquery-1.7.1.min.js'></script>
	<script type="text/javascript" src='../../../gen/u/idletimer/jquery.idle-timer.js'></script>
	<script type="text/javascript" src='$RODINU/contextmenu/jquery.contextMenu.js'></script>
	<script type="text/javascript">
		jQuery.noConflict();
	
		var correctParent = (typeof parent.isIndexConnected == 'undefined') ? window.opener : parent;
		var correctParentOk = correctParent != null
			&& typeof correctParent.get_max_idle_timeout == 'function'
			&& correctParent.get_max_idle_timeout() > 0;

		if(correctParentOk) {
			// jQuery set an "activation" message to global timeout
			(function(jQuery){
				jQuery(document).on("active.idleTimer", function(){
					correctParent.resetLogoutTimeout();
				});
				jQuery.idleTimer(1000);
			})(jQuery);
		}
	</script>
	<script type="text/javascript">
		function setContextMenu() {
			(function(jQuery){
				jQuery(document).ready(function() {
					jQuery("span.result-word").add(".spotlightbox p.terms a").hover(
						function () { jQuery(this).addClass("hovered-word"); },
						function () { jQuery(this).removeClass("hovered-word");	});
					jQuery("span.result-word").add(".spotlightbox p.terms a").contextMenu({
						menu: 'widgetContextMenu',
            premenuitem_callback: 'check_semfilterresults',
            min_occurrences: 2, /*Build menuitem starting from 2 occurrences*/
            conditioned_menuitem_id: 2 /*give menuitem obj to callback function for change*/
					},

            function(action, el, pos) {
						var correctParent = (typeof parent.isIndexConnected == 'undefined') ? window.opener : parent;
						
						switch(action) {
							case "addToBreadcrumb":
								correctParent.bc_add_breadcrumb_unique(jQuery(el).text(),'result');
							break;
							case "restricttoontoterm":
                correctParent.RESULTFILTEREXPR = jQuery(el).text();
                correctParent.reload_frames_render(correctParent.TEXTZOOM);
                correctParent.RESULTFILTEREXPR='';
             	break;
							case "exploreInOntologicalFacets":
								correctParent.fb_set_node_ontofacet(jQuery(el).text().toLowerCase());
								correctParent.detectLanguageInOntoFacets_launchOntoSearch(jQuery(el).text(), 0, 0, 0, 0, 0, 0, correctParent.\$p);
								correctParent.\$p.ajax.call('../../app/tests/LoggerResponder.php?action=10&query=' + jQuery(el).text() + '&from=widget&name=' + get_datasource_name('$widgetDatasource'), {'type':'load'});
							break;
              default: 
						}
					});
  				});
			})(jQuery);
		}


		// set the context menu items
		setContextMenu();
	</script>
	<!-- JB: Links to all results displayed in widget -->
	<script type="text/javascript" src="/rodin/$RODINSEGMENT/app/u/RodinResult/RodinResultSet.js" > </script>
	<script type="text/javascript">
		var widgetResultSet = new RodinResultSet();
	</script>
	<!-- JB: Resize widget elements when ready. -->
	<script type="text/javascript">
		function adaptWidgetInterfaceToWith(frameId) {
			if (typeof parent.isIndexConnected == 'boolean') {
				var correctParent = parent; // the only case wher the resizing matters
				correctParent.adapt_widget_search_input_width(frameId);
			}
		}
		
		jQuery(document).ready(function() {
			adaptWidgetInterfaceToWith('$iFrameIdFromAppId');
		});
	</script>
	<link href="$RODINU/contextmenu/jquery.contextMenu.css" rel="stylesheet" type="text/css" />
	<link href="../css/contextMenuInRodin.css.php" rel="stylesheet" type="text/css" />
</head>
<body bgcolor='$COLOR_WIDGET_BG'>
  <form name="famenu" action="">
	<ul id="widgetContextMenu" class="contextMenu">
		<li><h1 id="widgetContextMenuLabel"></h1></li>
		<li class="addToBreadcrumb"><a href="#addToBreadcrumb">$addToBreadcrumbLabel</a></li>
		<li class="restricttoontoterm" $HOVERIN_RESTRICT $HOVEROUT_RESTRICT><a href="#restricttoontoterm">$restrictToOntoTermLabel</a></li>
		<li class="exploreOntoFacets"><a href="#exploreInOntologicalFacets">$exploreOntologicalFacetsLabel</a></li>
	</ul>
 </form>
EOP;
		}
	}
	
	
	/**
	 * Saves the default preferences for the widget, only if
	 * the said widget doesn't have any prefs already in the DB.
	 */
	function register_default_prefs($prefs) {
		global $USER_ID;
		global $APP_ID;
		global $datasource;
		global $PID;
		global $HOST;
		global $PORT;
		global $remoteuser;
		global $thisSCRIPT;
		
		// Check if the widget has already some prefs
		$checkQuery = "SELECT count(*) as CNT FROM userRDWprefs WHERE prefsuser = '$USER_ID' AND "
			. "datasource = '$datasource' AND application_id = '$APP_ID';";
		
		$DB = new RODIN_DB();
		$ret = mysql_query($checkQuery, $DB->DBconn);

		$count = 0;
		if ($ret != null) {
			$REC = mysql_fetch_assoc($ret);
			$count = $REC['CNT'];
		}
		
		$DB->close();
		
		// Save only if the widget doesn't already have saved prefs
		if ($count == 0) {
			// URL reference to save preferences :
			// http://localhost/rodin/eng/app/w/RDW_alexandria_sg.rodin?
			// ajax=1&save=1&prefsuser=fabio&app_id=2:735:24&pid=2&xi[]=&xp=publications
			$params="ajax=1&save=1&reload=1&prefsuser=$USER_ID&app_id=$APP_ID&pid=$PID";
			
			$PORTEXPR = ($PORT==80 || $PORT=='') ? '' : ":$PORT";
			$DEFAULTSAVEACTION="http://{$HOST}$PORTEXPR{$thisSCRIPT}?".("$params&$prefs");
			$RES = get_file_content($DEFAULTSAVEACTION,false);
		}
	}	


	
	
	function unregister_default_prefs($app_id)
	/*
	http://localhost/rodin/eng/app/w/RDW_alexandria_sg.rodin?
	ajax=1&save=1&prefsuser=fabio&app_id=2:735:24&pid=2&xi[]=&xp=publications
	*/
	{
		$QUERY_DELETE="
		DELETE 
		FROM userRDWprefs
		WHERE
		application_id = '$app_id';";
		
		$DB = new RODIN_DB();
		$qresult = mysql_query($QUERY_DELETE);
		if (($affected_rows= mysql_affected_rows())<1)
		{
			throw(New Exception(mysql_error($DB->DBconn)."<hr>Query:".$QUERY_DELETE."<br><br>"));
		}
		else print "<br>unregister_default_prefs  $app_id PREFS DELETED";
	}	


	

	
?>
