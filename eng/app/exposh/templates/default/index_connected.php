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
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head> 
	<title>Loading ... </title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="language" content="<?php echo __LANG;?>" />
	
	<link rel="stylesheet" type="text/css" href="../styles/main.css?v=<?php echo __POSHVERSION;?>" />
	<link rel="stylesheet" type="text/css" href="../../app/css/rodin.css?v=<?php echo __POSHVERSION;?>" />
	<link rel="stylesheet" type="text/css" href="<?php print "$CSS_URL"; ?>/rodinBoards.css.php" />
	<link rel="stylesheet" type="text/css" href="<?php print $RODINUTILITIES_GEN_URL; ?>/contextmenu/jquery.contextMenu.css" />
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
	<script type="text/javascript" src='../../app/w/RodinResult/RodinResultSet.js'></script>
	<script type='text/javascript' src='../../app/u/RODINutilities.js.php?skin=<?php print $RODINSKIN;?>'></script>
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
	<script type="text/javascript" src='../../../gen/u/contextmenu/jquery.contextMenu.js'></script>  
	<script type="text/javascript" src="../../../gen/u/autocomplete/jquery.autocomplete-min.js"></script>
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
	$m=10;
	
	$FRI_START = $_SESSION['FRI_START'];
	$_SESSION['FRI_START'] = 0; //only 1 time
	
	if ($FRI_START || 1) {
		$INIT_SRC_OBJ = initialize_SRC_MODULES( $_SESSION['user_id'] );
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
				onClick="javascript: reload_frames_render('min');"
				onMouseOver="javascript: i1 = document.getElementById('img_mainzoombutton1');zoomb1=i1.src; src='$B_MIN_ICON_HOVER'" 
				onMouseOut="javascript: i1 = document.getElementById('img_mainzoombutton1');i1.src=zoomb1;" />
			<img id="img_mainzoombutton2" class="optionButton" src="$B_TOKEN_ICON_NORMAL"
				title="$title2"
				onClick="javascript: reload_frames_render('token');"	
				onMouseOver="javascript: i2 = document.getElementById('img_mainzoombutton2');zoomb2=i2.src;src='$B_TOKEN_ICON_HOVER'" 
				onMouseOut="javascript: i2 = document.getElementById('img_mainzoombutton2');i2.src=zoomb2;">
			<img id="img_mainzoombutton3" class="optionButton" src="$B_ALL_ICON_NORMAL"
				title="$title3" 
				onClick="javascript: reload_frames_render('all');"
				onMouseOver="javascript: i3 = document.getElementById('img_mainzoombutton3');zoomb3=i3.src;src='$B_ALL_ICON_HOVER'" 
				onMouseOut="javascript: i3 = document.getElementById('img_mainzoombutton3');i3.src=zoomb3;">
			<img id="img_mainzoombutton4" class="optionButton" src="$B_FILTER_ICON_NORMAL"
				title="$title4"
				onClick="javascript: hide_un_highlighted_results();"
				onMouseOver="javascript: i4 = document.getElementById('img_mainzoombutton4');zoomb4=i4.src;src='$B_FILTER_ICON_HOVER'"
				onMouseOut="javascript: i4 = document.getElementById('img_mainzoombutton4');i4.src=zoomb4;">

			<input id="selectedTextZoom" type="hidden" value="" />
		</div>
		<script type="text/javascript">set_zoom_text_icons("token");</script>
EOH;

//------------------ Agregated view controls ----------------
	$aggregatedViewSwitch = '<div id="aggregateButtonDiv" class="searchOptionDiv">' . "\n"
		. '<span class="optionLabel" id="aggregateButtonLabel">' . lg("lblEnableAggregation") . ':</span>' . "\n"
		. '<img id="aggregateButton" class="optionButton" src=""' . "\n"
		. 'onClick="javascript: toggle_aggregation();" title="" />' . "\n"
		. '</div>' . "\n";
	
#------------------ RODINCONTROL ----------------
	$launchMetaSearchCode = "eclog(Date() + ' Metasearch Start'); "
		 . "var s = document.getElementById('rodinsearch_s'); "
		 . "bc_registerMetaSearchText(s.value); "
		 . "\$p.ajax.call('../../app/tests/LoggerResponder.php?action=8&query=' + get_search_text(), {'type':'load'}); "
		 . "return fri_parallel_metasearch(get_search_text(),document.getElementById('rodinsearch_m').value,-1,-1,{$_SESSION['user_id']},true,false,window.\$p)";
	
	$onKeyUpSearchFAction="javascript: bc_clearBreadcrumbIfNeeded(this.value); if (event.keyCode==13) { $launchMetaSearchCode }";
?>

<body onUnload="$p.app.counter.stop();" bgcolor=<?php print $COLOR_PAGE_BACKGROUND;?>> 
<div id="cache" style="position:absolute;left:0;top:0;z-index:8;display:none;"></div>
<div id="headlink" name="headlink"></div>
<div id="information"></div>

<div id="rodinmetasearch">
	<input id="rodinsearch_s" type="text" value="<?php print $s; ?>"
		onkeyup="<?php print $onKeyUpSearchFAction; ?>" />
	<input id="metasearchrodinbutton" type="button"
		name="rodingensearchbutton" title="" value=""
		onclick="javascript: <?php print $launchMetaSearchCode; ?>" />
	<img id="rodinSearchHelpButton" src="<?php print "$RODINUTILITIES_GEN_URL/images/help.png"; ?>" title="Help coming soon" />	
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
		<input id="rodinsearch_m" type="text" size="2" value="<?php print $m; ?>"
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
		<div id="addWidgetBoard" class="singleRodinBoard"></div>
    <div id="facetboard" class="singleRodinBoard" onmouseover="mark_ontoterms_on_resultmatch()">
			<div id="facetsBoardTitleBar" class="rodinBoardTitleBar"> 
				<img id="refining_busy_info2" src="<?php print $IMG_REFINING_DONE; ?>" class="rodinBoardTitleImage" />
				<span id="facetboard_title" class="rodinBoardTitleLabel">
					<?php print lg("lblFacetsBoardTitle"); ?>
				</span>
				<img id="faceBoardTogle" onClick="javascript: toggleBoardExpanded('facetboard');" class="toggleBoardIcon" />
				<script type="text/javascript">forceBoardExpanded('facetboard');</script>
			</div>
				
			<div id="facetBoardContent" name="boardContent">
				<?php print generate_facetboard($INIT_SRC_REF_TABS); ?>
			</div>
		</div>
		
		<div id="cloudboard" class="singleRodinBoard">
			<div id="cloudBoardTitleBar" class="rodinBoardTitleBar"> 
				<img id="cloudBoardIcon" src="<?php print $TAG_CLOUD_ICON; ?>" class="rodinBoardTitleImage" />
				<span id="cloudBoardTitle" class="rodinBoardTitleLabel">
					<?php print lg("lblHistoricalBoardTitle"); ?>
				</span>
				<img id="cloudBoardToggle" onClick="javascript: toggleBoardExpanded('cloudboard');" class="toggleBoardIcon" />
				<script type="text/javascript">forceBoardExpanded('cloudboard');</script>
			</div>
		
			<div id="cloudBoardContent" name="boardContent">
				<div class="boardConfiguration">
					<select id="sizeBySelect" onchange="javascript: refreshCloudBoard('<?php print $_SESSION['user_id'] ?>');">
						<option value="frequency" selected="selected">Tag-cloud</option>
						<option value="recency"><?php print lg("lblHistoricalRecency"); ?></option>
					</select>
					<button id="tagCloudEraseButton" title="<?php print lg('lblTagCloudeEraseTitle');?>"
						onclick="javascript: if(confirm(lg('lblConfirmWant2EraseTagCloud'))) { resetCloudBoard('<?php print $_SESSION['user_id'] ?>'); }"
						style="height: 20px; float: right;"><img src="<?php print "$POSHIMAGES/ico_close.gif"; ?>" /></button>
					<button id="tagCloudReloadButton" title="<?php print lg('lblTagCloudeReloadTitle');?>"
						onclick="javascript: refreshCloudBoard('<?php print $_SESSION['user_id'] ?>');"
						style="height: 20px; float: right;"><img src="<?php print "$POSHIMAGES/ico_refresh.gif"; ?>" /></button>
				</div>
				<div id="cloudBoardTags"></div>
				<script type="text/javascript">refreshCloudBoard('<?php print $_SESSION['user_id'] ?>');</script>
			</div>
		</div>
		<div id="messages"></div>
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

		window.onload(function() {
			alert('miechi!!!!!');
			init_aggregation();
		});
	</script>



	<?php launch_hook('userinterface_end',$pagename);
    $HOVERIN_RESTRICT="onmouseover=\"simple_highlight_semfilterresults(\$('facetsContextMenuLabel').innerHTML,true)\"";
    $HOVEROUT_RESTRICT="onmouseout=\"simple_highlight_semfilterresults(\$('facetsContextMenuLabel').innerHTML,false)\"";
  ?>
	<!-- The following is the ontofacets menu:  -->
	<ul id="facetsContextMenu" class="contextMenu">
    <li><h1 id="facetsContextMenuLabel"></h1></li>
		<li class="addToBreadcrumb"><a href="#addToBreadcrumb"><?php echo lg('lblContextMenuAddToBreadcrumb'); ?></a></li>
		<li class="restricttoontoterm" 
        <?php print $HOVERIN_RESTRICT ?>
        <?php print $HOVEROUT_RESTRICT ?> ><a href="#restricttoontoterm"><?php echo lg('lblContextMenuRestrictToOntoTerm1');?> <b><label/></b> <?php echo lg('lblContextMenuRestrictToOntoTerm2');?></a></li>
		<li class="exploreOntoFacets"><a href="#exploreInOntologicalFacets"><?php echo lg('lblContextMenuExploreOntoFacets');?></a></li>
	</ul>

</div>

</body>
</html>