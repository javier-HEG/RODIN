<?php 
	$filename="../../app/u/FRIutilities.php";
	if (file_exists("$filename")) {
		include_once("$filename");
	}
	
	$filename="../../../posh/includes/session.inc.php";
	if (file_exists("$filename")) {
		include_once("$filename");
	}
	
	/**
	 * Destroy session and idleTimer's lastActive cookie
	 */
	function logOutOnTimeout() {
		global $POSHWEBROOT;
		
		Logger::logAction(Logger::LOGOUT_ACTION, array('msg'=>'session was too old'));
		
		session_destroy();
    	session_unset();

    	header('Location: ' . $POSHWEBROOT . '/portal/login.php?message=' . lg("lblYouHaveBeenDisconnected", round($IDLE_MAXTIMEOUT/60)));
	}
	
	if ($IDLE_MAXTIMEOUT > 0) {
		if (!isset($_COOKIE['lastActive'])) {
			if (isset($_SESSION['lastActive'])) {
				logOutOnTimeout();
			} else {
				$_SESSION['lastActive'] = time();
			}
		}
	}
	
	$setversion=$_GET['setversion'];
	if (!$setversion) $setversion=2013;
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head> 
	<script type="text/javascript">
		RODINleftMenuWidth=<?php print $FACETBOARDMINWIDTH ?>;
	</script>
	<title>Loading ... </title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="language" content="<?php echo __LANG;?>" />
	
	<link rel="stylesheet" type="text/css" href="../styles/main.css?v=<?php echo __POSHVERSION;?>" />
	<link rel="stylesheet" type="text/css" href="../../app/css/rodin.css?v=<?php echo __POSHVERSION;?>" />
	<link rel="stylesheet" type="text/css" href="<?php print "$CSS_URL"; ?>/rodinBoards.css.php" />
	<link rel="stylesheet" type="text/css" href="<?php print $RODINU; ?>/contextmenu/jquery.contextMenu.css" />
	<link rel="stylesheet" type="text/css" href="<?php print $CSS_URL; ?>/contextMenuInRodin.css.php" />
	<link rel="stylesheet" type="text/css" href="<?php print $CSS_URL; ?>/rodinwidget.css.php" />
	
	<script type="text/javascript" src="../portal/selections/waiting.js"></script>
	<script type="text/javascript" src="../../app/exposh/l10n/<?php echo __LANG;?>/lang.js?v=<?php echo __POSHVERSION;?>" ></script>
	<script type="text/javascript" src="../includes/config.js?v=<?php echo __rand;?>"></script>
	<script type="text/javascript" src="../tools/mootools.js?v=1.2.1"></script>
	<script type="text/javascript" src="../../app/exposh/includes/ajax<?php if (!__debugmode) echo '_compressed';?>.js?v=<?php echo __POSHVERSION;?>"></script>
	<script type="text/javascript" src="../includes/php/ajax-urls.js?v=<?php echo __POSHVERSION;?>"></script>
	<!-- RODIN:BEGIN -->
	<script type='text/javascript' src='../../app/u/wordProcessingTools.js'></script>
	<script type='text/javascript' src='../../app/u/querystring.js'></script>
	<script type="text/javascript" src='../../app/u/facetBoardInterface.js.php'></script>
	<script type="text/javascript" src='../../app/u/RodinResult/RodinResultSet.js'></script>
	<script type='text/javascript' src='../../app/u/RODINutilities.js.php?skin=<?php print $RODINSKIN;?>'></script>
	<script type='text/javascript' src='../../app/u/RODINsemfilters.js.php?skin=<?php print $RODINSKIN;?>'></script>
  
	<script type='text/javascript'>
		var isIndexConnected = true;
		// Holds the aggregated status per tab
		var tabAggregatedStatusTabId = new Array();
		var tabAggregatedStatus = new Array();
		// Holds the results shown in the aggregated view per tab
		var allWidgetsResultsSetsTabId = new Array();
		var allWidgetsResultSets = new Array();
		// Save the SID of the last search per tab
		var lastSidTabId = new Array();
		var lastSid = new Array();
	</script>
	<!-- For idle timer and context menus -->
	<script type="text/javascript" src='../../../gen/u/jquery/jquery-1.7.1.min.js'></script>
	<script type="text/javascript" src='../../../gen/u/idletimer/jquery.idle-timer.js'></script>
	<script type="text/javascript" src='<?php print $RODINU; ?>/contextmenu/jquery.contextMenu.js'></script>
	<link rel="stylesheet" type="text/css" href="../../../gen/u/autocomplete/styles.css" />
	<script type="text/javascript" src="../../../gen/u/autocomplete/jquery.autocomplete.js"></script>
	<script type="text/javascript">
		jQuery.noConflict();
	</script>
	<!-- RODIN:END -->
</head>

<?php 
	launch_hook('userinterface_header',$pagename);
	//FRI:
	$WCONLOG=$_REQUEST['WANTCONSOLELOG'];
	$WANTCONSOLELOG= ($WCONLOG!=0?'true':'false');
	
	require_once("../../app/u/facetBoardInterface.php");
	require_once("../../app/u/tagcloud_styles.php");
	// For Language Detection (Google API laden)
	$EXTRAINCLUDE=<<<EOI
		<script type='text/javascript'>	
		LANGUAGE_OF_RESULT_CODED='en';
		RODINSKIN='$RODINSKIN';
		</script>
EOI;
	print $EXTRAINCLUDE;
	print $EXTRAINCLUDE_GOOGLE_LANGUAGE_LOAD;
	$s='';
	$m=$DEFAULT_M;
	
	$FRI_START = $_SESSION['FRI_START'];
	$_SESSION['FRI_START'] = 0; //only 1 time
	
	if ($FRI_START || 1) {
		$INIT_SRC_OBJ = initialize_SRC_MODULES( $_SESSION['user_id'],' AND UsedAsThesaurus=1 ');
		$INIT_SRC_CODE = $INIT_SRC_OBJ['ajax_init_src_code'];
		$INIT_SRC_REF_TABS = $INIT_SRC_OBJ['src_interface_specs'];
	}

#------------------ ZOOM BUTTONS ----------------
	$zoom_button_width = "20px";
	$zoom_button_height = "20px";
	
	global $B_MIN_ICON_SELECTED, $B_MIN_ICON_HOVER, $B_MIN_ICON_NORMAL;
	global $B_TOKEN_ICON_SELECTED, $B_TOKEN_ICON_HOVER, $B_TOKEN_ICON_NORMAL;
	global $B_ALL_ICON_SELECTED, $B_ALL_ICON_HOVER, $B_ALL_ICON_NORMAL;
	global $B_FILTER_ICON_SELECTED, $B_FILTER_ICON_HOVER, $B_FILTER_ICON_NORMAL;
	
	$title1=lg("titleTextZoomOne");
	$title2=lg("titleTextZoomTwo");
	$title3=lg("titleTextZoomThree");
	$title4=lg("titleTextZoomFour");
	
	$textZoomLabel = lg("lblMetaSearchPrefsTextZoom");
	
	$textZoomButtons=<<<EOH
  <script type="text/javascript">
    RESULTFILTEREXPR='';
    TEXTZOOM='token';
	</script>
		<div id="textZoomButtonsDiv" class="searchOptionDiv">
			<span class="optionLabel">{$textZoomLabel}:</span>
			<img id="img_mainzoombutton1" class="optionButton" src="$B_MIN_ICON_NORMAL"
				title="$title1"
				onClick=" reload_frames_render('min');"
				onMouseOver=" i1 = document.getElementById('img_mainzoombutton1');zoomb1=i1.src; src='$B_MIN_ICON_HOVER'" 
				onMouseOut=" i1 = document.getElementById('img_mainzoombutton1');i1.src=zoomb1;" />
			<img id="img_mainzoombutton2" class="optionButton" src="$B_TOKEN_ICON_NORMAL"
				title="$title2"
				onClick=" reload_frames_render('token');"	
				onMouseOver=" i2 = document.getElementById('img_mainzoombutton2');zoomb2=i2.src;src='$B_TOKEN_ICON_HOVER'" 
				onMouseOut=" i2 = document.getElementById('img_mainzoombutton2');i2.src=zoomb2;">
			<img id="img_mainzoombutton3" class="optionButton" src="$B_ALL_ICON_NORMAL"
				title="$title3" 
				onClick=" reload_frames_render('all');"
				onMouseOver=" i3 = document.getElementById('img_mainzoombutton3');zoomb3=i3.src;src='$B_ALL_ICON_HOVER'" 
				onMouseOut=" i3 = document.getElementById('img_mainzoombutton3');i3.src=zoomb3;">
			<!--img id="img_mainzoombutton4" class="optionButton" src="$B_FILTER_ICON_NORMAL"
				title="$title4"
				onClick=" hide_un_highlighted_results();"
				onMouseOver=" i4 = document.getElementById('img_mainzoombutton4');zoomb4=i4.src;src='$B_FILTER_ICON_HOVER'"
				onMouseOut=" i4 = document.getElementById('img_mainzoombutton4');i4.src=zoomb4;"-->

			<input id="selectedTextZoom" type="hidden" value="" />
		</div>
		<script type="text/javascript">set_zoom_text_icons("token");</script>
EOH;

//------------------ Agregated view controls ----------------
	$aggregatedViewSwitch = '<div id="aggregateButtonDiv" class="searchOptionDiv">' . "\n"
		. '<span class="optionLabel" id="aggregateButtonLabel">' . lg("lblEnableAggregation") . ':</span>' . "\n"
		. '<img id="aggregateButton" class="optionButton" src="'.$RODINUTILITIES_GEN_URL.'/images/button-aggregate-on.png"' . "\n"
		. 'onClick=" toggle_aggregation();" title="" />' . "\n"
		. '</div>' . "\n";

//-------- prepare and set here context menu for aggregated view:
$restrictToOntoTermLabel = lg('lblContextMenuRestrictToOntoTermX1');
$addToBreadcrumbLabel = lg('lblContextMenuAddToBreadcrumb');
$exploreOntologicalFacetsLabel = lg('lblContextMenuExploreOntoFacets');
$HOVERIN_RESTRICT="onmouseover=\"var t=parent.document.getElementById('widgetContextMenuLabelaggv').innerHTML; hf(t)\"";
$HOVEROUT_RESTRICT="onmouseout=\"var t=parent.document.getElementById('widgetContextMenuLabelaggv').innerHTML; uh(t)\"";
$aggregatedViewContextMenu=<<<EOAM

<form name="famenux" action="">
	<ul id="aggViewContextMenu" class="contextMenu contextMenuAggView">
		<li><h1 id="widgetContextMenuLabelaggv"></h1></li>
		<li class="addToBreadcrumb"><a href="#addToBreadcrumb">$addToBreadcrumbLabel</a></li>
		<!--li class="restricttoontoterm" $HOVERIN_RESTRICT $HOVEROUT_RESTRICT><a href="#restricttoontoterm">$restrictToOntoTermLabel</a></li-->
		<li class="exploreOntoFacets"><a href="#exploreInOntologicalFacets">$exploreOntologicalFacetsLabel</a></li>
	</ul>
 </form>
EOAM;

#------------------ RODINCONTROL ----------------
	$launchMetaSearchCode = "eclog(Date() + ' Metasearch Start'); "
		 . "var s = document.getElementById('rodinsearch_s'); "
		 . "bc_registerMetaSearchText(s.value); "
		 . "hide_autocomplete_bruteforce(); "
		 . "\$p.ajax.call('../../app/tests/LoggerResponder.php?action=8&query=' + get_search_text(), {'type':'load'}); "
		 . "return fri_parallel_metasearch(get_search_text(),document.getElementById('rodinsearch_m').value,-1,-1,{$_SESSION['user_id']},true,false,window.\$p)"
		 ;
	
	$onKeyUpSearchFAction=" bc_clearBreadcrumbIfNeeded(this.value); if (event.keyCode==13) { $launchMetaSearchCode }";

	$SOLR_LOGO='';
  if ($RESULTS_STORE_METHOD=='solr')
  {
  	if ($RODINSEGMENT=='eng' && 0)
 			$SOLR_LOGO="<img id='solrlogo' src='$RODINUTILITIES_GEN_URL/images/solr_logo.png' title='Using SOLR as persistency and search engine'/>";
  }

  $DBRODIN_LOGO="<img id='dbrodinlogo' src='$RODINUTILITIES_GEN_URL/images/icon_arrow_right2.png' title=''/>";
	$OPENDBRODINLODBROWSER="open_lod_browser('$WEBROOT$RODINROOT/$RODINSEGMENT/app/lod/resource/a/','_blank');return false;";
  $DBRODIN_HREF="<a href='' onclick=\" $OPENDBRODINLODBROWSER\" target='_blank' title='Click to open dbRODIN LoD browser (on your last search) in a new tab'>$DBRODIN_LOGO</a>";
  
  $RDFIZE_LOGO="<img id='rdfizelogo' src='$RODINUTILITIES_GEN_URL/images/white.PNG' title=''/>";
 	$LABPARAMS="?listwr=on&user_id=".$_SESSION['user_id']."&username=".$_SESSION['username'];  
	$OPENRDFIZELAB="open_rdfize('$RDFIZEURL$LABPARAMS','_blank');return false;";
  $RDFIZE_HREF= "<a href='#' onclick=\" $OPENRDFIZELAB\" target='_blank' title='Click to open RDFIZE LAB (new efficient prototype)  (on your last search) in a new TAB'>$RDFIZE_LOGO</a>";
	
	
  list($message,$test_rodin_komponenten_ok,$noofproblems) = rodin_service_diagnostics();
  if ($test_rodin_komponenten_ok)
  {
    print<<<EOP
 		<script type="text/javascript">
    DIAGNOSTIC_MESSAGE='';
    DIAGNOSTIC_PROBLEMS=0;
    DIAGNOSTIC_OK=true;
    </script>
EOP;
  } else {

    print<<<EOP
		<script type="text/javascript">
    DIAGNOSTIC_MESSAGE='$message';
    DIAGNOSTIC_PROBLEMS=$noofproblems;
    DIAGNOSTIC_OK=false;
    </script>
EOP;
    }
?>

<body onUnload="$p.app.counter.stop();" bgcolor=<?php print $COLOR_PAGE_BACKGROUND;?>> 
<div id="cache" style="position:absolute;left:0;top:0;z-index:8;display:none;"></div>
<div id="headlink" name="headlink"></div>
<div id="information"></div>
<div id="crashwarning" align=center></div>
<div id="rodinmetasearch">
  <?php print $SOLR_LOGO ?>
	<input id="rodinsearch_s" type="text" value="<?php print $s; ?>"
		onkeyup="<?php print $onKeyUpSearchFAction; ?>" />
	<input id="metasearchrodinbutton" type="button"
		name="rodingensearchbutton" title="" value=""
		onclick=" <?php print $launchMetaSearchCode; ?>" />
		<div id='linksdiv'>
			<?php print $DBRODIN_HREF ?><?php print $RDFIZE_HREF ?>
		</div>
 </div>

<div id="breadcrumbs" class="breadCrumbsHidden">
		<div id="breadcrumbs_title" onclick="bc_clear_breadscrumbs();"
			title="<?php print lg("titleBreadcrumbsTitle");?>">
			<?php print lg("lblBreadcrumbsTitle"); ?>:
		</div>
		<div id="breadcrumbs_terms"></div>
</div>

<div id="metaSearchOptionsBar">
	<?php echo $aggregatedViewSwitch; ?>
	<?php echo $textZoomButtons; ?>
	<div id="maxResultsPerWidgetDiv" class="searchOptionDiv">
		<span class="optionLabel"><?php print lg("lblMetaSearchPrefsNbResults");?>:</span>
		<input id="rodinsearch_m" style="text-align: right" type="text" size="2" value="<?php print $m; ?>"
			title="<?php print lg("titleGlobalMaxResults");?>">
	</div>
</div>

<div id="area">
	<div id="headmenu"></div>
	<div id="menus">
		<div id="audio"></div><div id="advise"></div><div id="message"></div><a name="contac"></a><div id="contact"></div><div id="box"></div><div id="other"></div><div id="newmod"></div>
		<!--<div id="loading" class="loading"></div>-->
	</div>
	
	<div id="rodinBoards" class="allBoards">
		<div id="addWidgetBoard" class="singleRodinBoard"
			onclick="this.getElementById('vmenuToggle').click()"></div>
		<div id="cloudboard" class="singleRodinBoard">
			<div id="cloudBoardTitleBar" class="rodinBoardTitleBar"
			onclick="this.getElementById('cloudBoardToggle').click()"> 
				<img id="cloudBoardIcon" src="<?php print $TAG_CLOUD_ICON; ?>" class="rodinBoardTitleImage" />
				<span id="cloudBoardTitle" class="rodinBoardTitleLabel">
					<?php print lg("lblHistoricalBoardTitle"); ?>
				</span>
				<img id="cloudBoardToggle" onClick="toggleBoardExpanded('cloudboard','cloudBoardTitleBar');" class="toggleBoardIcon" />
				<script type="text/javascript">forceBoardExpanded('cloudboard');</script>
			</div>
		
			<div id="cloudBoardContent" name="boardContent">
				<div class="boardConfiguration">
					<select id="sizeBySelect" onchange="refreshCloudBoard('<?php print $_SESSION['user_id'] ?>');">
						<option value="frequency" selected="selected">Tag-cloud</option>
						<option value="recency"><?php print lg("lblHistoricalRecency"); ?></option>
					</select>
					<button id="tagCloudEraseButton" title="<?php print lg('lblTagCloudeEraseTitle');?>"
						onclick="if(confirm(lg('lblConfirmWant2EraseTagCloud'))) { resetCloudBoard('<?php print $_SESSION['user_id'] ?>'); }"
						style="height: 20px; float: right;"><img src="<?php print "$POSHIMAGES/ico_close.gif"; ?>" /></button>
					<button id="tagCloudReloadButton" title="<?php print lg('lblTagCloudeReloadTitle');?>"
						onclick=" refreshCloudBoard('<?php print $_SESSION['user_id'] ?>');"
						style="height: 20px; float: right;"><img src="<?php print "$POSHIMAGES/ico_refresh.gif"; ?>" /></button>
				</div>
				<div id="cloudBoardTags"></div>
				<script type="text/javascript">refreshCloudBoard('<?php print $_SESSION['user_id'] ?>');</script>
			</div>
		</div>
		<div id="messages"></div>
	</div>		
		
		
	
  <div id="facetboard" class="singleRodinBoard" 
  		 onmouseover="mark_ontoterms_on_resultmatch()"
  >
		<div id="facetsBoardTitleBar" class="rodinBoardTitleBar"
			onclick="this.getElementById('faceBoardTogle').click()"> 
			<img id="refining_busy_info2" src="<?php print $IMG_REFINING_DONE; ?>" class="rodinBoardTitleImage" />
			<span id="facetboard_title" class="rodinBoardTitleLabel">
				<?php print lg("lblFacetsBoardTitle"); ?>
			</span>
			<img id="faceBoardTogle" onClick="toggleBoardExpanded('facetboard','facetsBoardTitleBar');" class="toggleBoardIcon" />
			<script type="text/javascript">forceBoardExpanded('facetboard');</script>
		</div>
			
		<div id="facetBoardContent" name="boardContent">
			<div id="fb_itemname_$src_service_id" class="facetcontrol-normal">
				<table cellpadding=0 cellspacing=0 width='<?php print $FACETBOARDMINWIDTH ?>' border=0>
				<?php print generate_facetboard($INIT_SRC_REF_TABS); ?>
				</table>
			</div>
		</div>
	</div>
	

	
	<div id="modules" class="maintbl">
		<div id="tabs">
		</div>
	</div>
	<div id="plugin"></div>
	<div id="newspaper"></div>
	<div id="empty" style="display:none"></div>
	<div id="footer"></div>
	<div id="debug"></div>
		</td>
		</tr>
	<script type="text/javascript"><!--
		var pfolder="../portal/";
		//Work in progress message
		//wip_message="<center><br /><img src='../images/loading.gif' align='absmiddle' /> <strong>"+lg("lblLoadingPage")+"<\/strong><br \/>- <a href='../portal/blank.html' class='smalll'>"+lg("lblCancel")+"<\/a> -<br /><br /><br />"+waiting()+"<br /><br /><a href='#' onclick='$p.app.resetAndReload()'>"+lg("appLoadingIssue")+"</a>";
		wip_message="<center><br />" + lg("lblWelcomeToRodin", <?php echo "'" . $_SESSION['longname'] . "'";?>) + "<br/><br/>" + lg("lblRodinIsLoading") + "<br/><br/><img src='../images/ajax-loader.gif' align='absmiddle' /><br /><a href='#' onclick='$p.app.resetAndReload()'>"+"</a></center>";
		$p.app.loading();
		/* Initialising JS */
		window.onload = function() {
			<?php print( register_SRC_REFINE_INTERFACES($_SESSION['user_id']) ); ?>
			//load Posh objects
			$p.app.user.init(<?php echo $_SESSION['user_id'];?>,"<?php echo $_SESSION['longname'];?>","<?php echo $_SESSION['type'];?>","<?php echo $_SESSION['availability'];?>");
			//noinclusion(); prevent from page inclusion
			$p.app.pageMode();
			
			//adjusting some graphical elements / setting titles according to languages
			var msb = document.getElementById('metasearchrodinbutton');
			msb.value=lg('lblmetasearchrodinbuttonValue');
			msb.title=lg('lblmetasearchrodinbuttonTitle');
		
			var msb = document.getElementById('rodinsearch_s');
			msb.title=lg('lblmetasearchrodinTitle');
		
			parent.OLDSEARCH ='';
		
			get_stopwordlist($p); /* set parent.STOPWORDS */
			
			fri_warn_nosrc();
			SRC_INTERACTING=false;
			WANTCONSOLELOG=<?php print $WANTCONSOLELOG;?>;
			<?php print $INIT_SRC_CODE; ?>

			// Set autocomplete plugin
			(function(jQuery){
				var options = {
					serviceUrl : '<?php print $AUTOCOMPLETERESPONDER ?>',
					delimiter: ', ',
					user_id: '<?php print  $_SESSION['user_id'] ?>',
					setversion: '<?php print  $setversion ?>',
					deferRequestBy: 500
				};
				jQuery('#rodinsearch_s').autocomplete(options);
			})(jQuery);
		}

		window.onresize = function(event) {
			adapt_widgetsareas_on_openclose_widgetmenu();
		}
	// --></script>
	
	<script type="text/javascript">
		function setCookieExpireInSeconds(name, value, seconds) {
			if (seconds) {
		        var date = new Date();
		        date.setTime(date.getTime() + seconds * 1000);
		        var expires = "; expires=" + date.toGMTString();
		    } else
			    var expires = "";
		    
		    document.cookie = name + "=" + value + expires + "; path=/";
		}
	
		function setLogoutTimeout() {
			// jQuery setting the idleTimer timeout and binding it to the logout function,
			// set $IDLE_MAXTIMEOUT to -1 to disable.
			(function(jQuery){
				jQuery(document).on('idle.idleTimer', function() {
					$p.ajax.call('../../app/tests/LoggerResponder.php?action=1&msg=too much idle time (ignore next logout logged)', {'type':'load'});
					setTimeout('$p.app.resetAndReload();', 10);
				});

				jQuery(document).on('active.idleTimer', function() {
					setCookieExpireInSeconds('lastActive', Math.round(new Date().getTime() / 1000), get_max_idle_timeout());
				});
				
				jQuery.idleTimer(1000 * get_max_idle_timeout());
			})(jQuery);
		}
	
		function resetLogoutTimeout() {
			(function(jQuery){
				jQuery.idleTimer('destroy');
			})(jQuery);
				
			setLogoutTimeout();
		}

		if (get_max_idle_timeout() > 0) {
			setCookieExpireInSeconds('lastActive', Math.round(new Date().getTime() / 1000), get_max_idle_timeout());
			setLogoutTimeout();
		}
	</script>



	<?php launch_hook('userinterface_end',$pagename);
    $HOVER1IN_RESTRICT="onmouseover=\"hf(\$('facetsContextMenuLabel').innerHTML)\"";
    $HOVER1OUT_RESTRICT="onmouseout=\"uh(\$('facetsContextMenuLabel').innerHTML)\"";
  ?>
  
	<!-- The following is the aggregatedVIew menu:  -->
  <?php print $aggregatedViewContextMenu ?>
  
	<!-- The following is the ontofacets menu:  -->
  <form name="famenu" action="">
	<ul id="facetsContextMenu" class="extendedcontextMenu contextMenu">
    <li><h1 id="facetsContextMenuLabel"></h1></li>
    <!-- commented out because of demo in Hamburg ...:
    <li class="morphofilter">
      <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td colspan="4">
            <a href="#morphofilter"><?php echo lg('lblContextMenuMorphoFilterX1');?>:
            </a>
          </td>
        </tr>
        <tr>
          <td width="20px"/>
          <td onmouseover="document.getElementById('ontomorphofilter1').checked=true;" title="Use direct word match">
            <input id="ontomorphofilter1" checked type="radio" value="1" name="ontomorphofilters">direct
          </td>
          <td onmouseover="document.getElementById('ontomorphofilter2').checked=true;" title="Use Levensthein algorithm to match words">
            <input id="ontomorphofilter2" type="radio" value="2" name="ontomorphofilters">leve
          </td>
          <td onmouseover="document.getElementById('ontomorphofilter3').checked=true;" title="Use soundex algorithm to match words">
            <input id="ontomorphofilter3" type="radio" value="3" name="ontomorphofilters">soundex
          </td>
        </tr>
      </table>
    </li>
    -->
    <!-- 20121202 FRI: contextmenu should be simpler!!! functionality now in direct icon buttons.
    <li class="restricttoontoterm">
      
      <table border="1" cellpadding="0" cellspacing="0">
        <tr>
          <td colspan="3">
            <?php echo lg('lblContextMenuRestrictToOntoTermX1');?>:
          </td>
        </tr>
        <tr>
          <td <?php print $HOVER1IN_RESTRICT ?>
              <?php print $HOVER1OUT_RESTRICT ?>
            >
            <table>
              <tr>
                <td>
                  <ul>
                  <li class="restricttoontoterm_f1" title="Use direct word correspondence to filter results">
                    <a href="#restricttoontoterm_f1"> <b><?php echo lg('lblContextMenuRestrictToOntoTerm1');?></b>  <b><label/></b> </a>
                  </li>
                  <li class="restricttoontoterm_f2"  title="Use skos text distance to filter results">
                    <a href="#restricttoontoterm_f2"> <b><?php echo lg('lblContextMenuRestrictToOntoTerm2');?></b>  <b><label/></b> </a>
                  </li>
                  <li class="restricttoontoterm_f3">
                    <a href="#restricttoontoterm_f3"> <?php echo lg('lblContextMenuRestrictToOntoTerm3');?>  <b><label/></b> </a>
                  </li>
                </ul>
              </tr>
            </table>
        </tr>
      </table>
      
    </li>
    -->
		<li class="addToBreadcrumb"><a href="#addToBreadcrumb"><?php echo lg('lblContextMenuAddToBreadcrumb'); ?></a></li>
    <li class="exploreOntoFacets"><a href="#exploreInOntologicalFacets"><?php echo lg('lblContextMenuExploreOntoFacets');?></a></li>
	</ul>
  </form>

</div>
	<script type="text/javascript">
	if (!parent) parent=document; //Workaround chrome
		toggleBoardExpanded('cloudboard'); //close cloudboard
		//Simulate click on onto (speedup for dev)
		//document.getElementById('ontofacet_center').value='Social Policy'; //Search for Time
		SETVERSION='<?php print $setversion ?>';
    if (! DIAGNOSTIC_OK)
    {
      var d = $("crashwarning");
      d.innerHTML=DIAGNOSTIC_MESSAGE;
      /*Center DIV*/
      d.style.left=((document.body.offsetWidth / 2) - 530 / 2)   +'px';
      d.style.height=DIAGNOSTIC_PROBLEMS * 60 + 190;
      d.style.visibility='visible';
      d.style.display='inline-block';
    }  
	</script>

</body>
</html>