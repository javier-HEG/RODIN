<?php
//Should use UC3 resources instead of RODIN RESOURCES

include_once('./uc3_widget_precalculations.php');
include_once('../../../../rodin/'.$RODINSEGMENT.'/fsrc/app/u/FRIdbUtilities.php'); // from rodinuc3

//This widget needs a specific css taken from uc3 site:
$SPECIAL_CSS=<<<EOS
<link rel="stylesheet" type="text/css" href="../../../../rodinuc3/$RODINSEGMENT/app/css/rodinBoards.css.php" />
EOS;

//This widget needs a specific js taken from uc3 site:
$SPECIAL_JS=<<<EOS
<script type="text/javascript" src="../../../../rodinuc3/$RODINSEGMENT/app/u/facetBoardInterface.js.php"></script>
<script type="text/javascript" src="../../../../rodinuc3/$RODINSEGMENT/app/u/RODINsemfilters.js.php"></script>
EOS;

include_once('./uc3_widget_resource_includer.php');

//This widget needs a specific php taken from uc3 site:
include_once('./facetBoardInterface.php'); // from rodinuc3
include_once('./arcUtilities.php'); // from rodinuc3
include_once('../../../../rodin/'.$RODINSEGMENT.'/fsrc/app/u/stopwords.php'); // from rodinuc3


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




class RDWuc3_aggtheresults {
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

	

	return true;
}
##############################################




##############################################
##############################################
public static function DEFINITION_RDW_DISPLAYHEADER()
##############################################
##############################################
{

	
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
	
	return true;
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
	return true; // do not chain in state machine
}



public static function DEFINITION_RDW_STORERESULTS()
{
	return true; // nothing to do here
}

/**
 * Called from RodinWidgetSMachine.RDW_SHOWRESULT_WIDGET_EPI(), it is asked
 * to echo the HTML code corresponding to results. The caller method already
 * creates the necessary DIV for all the results.
 */
public static function DEFINITION_RDW_SHOWRESULT_WIDGET($w,$h) {
	global $widget_classname;
	print $widget_classname::build_and_show_SRC();
	
	return true; // nothing to do here
}

/**
 * Called from RodinWidgetSMachine.RDW_SHOWRESULT_FULL_EPI(), it is asked
 * to echo the HTML code corresponding to results.
 */
public static function DEFINITION_RDW_SHOWRESULT_FULL($w,$h) {
		return true; // nothing to do here
}


/* ******************************************
 * Utility functions, widget dependent.
 ****************************************** */

 private static function build_and_show_SRC()
 {
 	$DEBUG=0;
	
 	global $USER;
	global $LANG;
	global $POSHIMAGES;
	global $TAG_CLOUD_ICON;
	global $APP_ID;
	global $FRAMENAME;
	global $widget_classname;
	global $IMG_REFINING_DONE;
	global $RDW_REQUEST;
	global $RODINSEGMENT;
	global $FACETBOARDMINWIDTH;
	global $UC3_THESAURI_TO_USE, $UC3_THESAURI_S_M;
	global $WEBROOT, $RODINROOT, $RODINSEGMENT;
	global $BASISRODINIMAGESURL;
	global $RDFLOG;
	$mode=$_REQUEST['mode']; if(!$mode) $mode='rele';
	
	$SEMWEBICON=$BASISRODINIMAGESURL.'/semantic_web_icon.png';
	
	$APP_ID_CLEAN=str_replace(':', '-', $APP_ID);
	$x = $APP_ID_CLEAN; // widget obj discriminator
	
	//Eval the 
	foreach ($RDW_REQUEST as $querystringparam => $d)
	{
		//if ($DEBUG) echo "<br>RDW_REQUEST eval $querystringparam => $d";
		if ($WEBSERVICE) 
				 eval( "global \${$querystringparam}; \${$querystringparam} = '$d';" );
		else eval( "global \${$querystringparam};" );
	}
	echo "<div id='widgetarea$x' class='widgetarea'>";
	#######################################################################################
	# OPTI:  LOAD INIT SRC MODULES (THIS SHOULD HAPPEN JUST ONCE FOR EACH USER OVER ALL UNIVERSES)
	# MODULES SHOULD BE INIT AT UPPER PARENT LEVEL:
	
	//Strip:
	$QUERY=trim($q);

	if ($QUERY)
	{
	$QUERY=str_replace(' AND ',' ',$QUERY);
	$QUERY=str_replace(' OR ',' ',$QUERY);
	$QUERY=str_replace('"','',$QUERY);
	$QUERY=str_replace(' ','%20',$QUERY);
	
	$THESOURCES	=$UC3_THESAURI_TO_USE;
	$M					=$UC3_THESAURI_S_M; 
	
	if ($DEBUG)
	{
		echo "<br>USING Param QUERY: $QUERY";
		echo "<br>USING Param UC3_THESAURI_TO_USE: $UC3_THESAURI_TO_USE";
		echo "<br>USING Param UC3_WIDGET_S_M: $UC3_THESAURI_S_M";
		echo "<br>USING Param USER: $USER";
		echo "<br>USING Param LANG: $LANG";
	}
		
	$base_url="$WEBROOT$RODINROOT/$RODINSEGMENT/app/webs/thesearch.php";
	$url = "$base_url?query=$QUERY&thesources=$THESOURCES&userid=$USER&m=$M&ontocontext=1&lang=$LANG&unifyedskosresults=1&mode=$mode&DEBUG=0";
	
	// Call $url
	
	if ($DEBUG) echo "<br><a href='$url' target='blank'>$url</a>";
	$jsonResultsDecoded = json_decode(($CONTENT=file_get_contents($url)), true);
	
	$query 					= $jsonResultsDecoded{'query'};
	$searchtermlang = $jsonResultsDecoded{'searchtermlang'};
	$skostheresults = $jsonResultsDecoded{'skostheresults'};
	$errortxt				= $jsonResultsDecoded{'error'};
	
	if ($DEBUG)
	{
		echo  "<br>skostheresults: ".count($skostheresults)." jsonResultsDecoded:$jsonResultsDecoded"
					."<br>errortxt: $errortxt<br>"
					;
		var_dump($jsonResultsDecoded); echo "<br><br>";
		$RDFLOG = tell_skos_subjects($skostheresults,'TEST SKOS');
		echo $RDFLOG ."<br>"; 
	}
	switch($mode) {
		case 'rele': $RELE_SELECTED='selected="selected"'; break;
		case 'alfa': $ALFA_SELECTED='selected="selected"'; break;
		case 'afla': $AFLA_SELECTED='selected="selected"'; break;
	}
	
	$QUERYB=urldecode($QUERY);
$SRCFACETTITLE="___{$TTPNEWLINE}{$TTPNEWLINE}"
								."Click to search with this facet term or choose one available action on the left side by clicking on the corresponding icon; "
								."select any text portion of the term with your mouse to adapt the text upon which the action should be started; "
								."left-click on the term to erase the selection (the selection is also erased by selecting in another term)";

//Scan and render semantical facets
if (is_array($skostheresults) && ($c=count($skostheresults)))
{
	$HTML.=<<<EOH
<div id='facetBoardContent'>
				<div class="boardConfiguration">
					<img id="srcBoardIcon$x" src="$SEMWEBICON" class="rodinBoardTitleImage" />
					<label class='boardlabel'>Semantical facets</label>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<label class='boardselectlabel'>Sort:</label>
					<select id="selectsort$x" class="boardtagcloudselect" title="$lblSortHistoricalRecency"
						onchange="refreshSRCBoard($USER,'$FRAMENAME','srcFacetBoard$x','$APP_ID_CLEAN','$QUERY','$THESOURCES',$M,this.value);" >
						<option value="rele" $RELE_SELECTED>by relevance</option>
						<option value="alfa" $ALFA_SELECTED>alphabetically</option>
						<option value="afla" $AFLA_SELECTED>inv. alphabetically</option>
					</select>
				</div>	
	<!--Opening SRC section -->
	<div id="srcFacetBoard$x" name="facetboard-container" tt='$SRCFACETTITLE'>
		<table class='tableA' cellpadding=0 cellspacing=0 border=0><!-- table A -->
EOH;
	foreach($skostheresults as $srcname=>$SKOS)
	{
		$SRCTITLE = lg('ttp'.$srcname);
		$SRCPRETTYNAME=lg('lbl'.$srcname);
		$SRCID = $SKOS['id'][0];
		$broaders = array_key_exists('b', $SKOS)? $SKOS['b']:array();
		$narrowers = array_key_exists('n', $SKOS)? $SKOS['n']:array();
		$related = array_key_exists('r', $SKOS)? $SKOS['r']:array();
		
		if ($DEBUG) {
			echo "<br><b>$srcname</b> SRCID=($SRCID) : "; 
			echo "<br>broaders:";
			foreach($broaders as $BBB)
			{
				echo "<br> B $BBB";
			}	
			echo "<br>end broaders<br>";
		}
		$LOCALLOG.= "<br><b>$srcname</b> ($SRCID).SKOS:";
		$SRCTERMSCOUNT = count($broaders) + count($narrowers) + count($related);
		
		
		$HTML.=<<<EOH
				<tr>
					<td class='fb'>
				
						<div id="fb_itemcontent_$SRCID" class="facetgroup-active">
EOH;
		
		$counter=1;
		
		####################################################
		#
		# BROADER TERMS:
		#
		####################################################
		if (($bc=is_array($broaders) * ($count_bt=count($broaders)))>0)
		{
			$LOCALLOG.="<br><i>$bc Broaders:</i>";
			$ONTO_HEADER_DIV = $widget_classname::onto_th_header_div('Broader',$count_bt,$SRCID,$APP_ID_CLEAN);
			$HTML.=<<<EOH
						$ONTO_HEADER_DIV
						<div id="fb_itemcontent_b_$SRCID" class="facetlist-active">
							<table class='tableD' cellspacing="0" cellpadding="0" border="0" ><!--table D-->
EOH;

			$ONTOCONTEXT=$SKOS['data']['b'];
			foreach($broaders as $b)
			{
				$b=trim($b);
				$b_semanticcontextbase64=$ONTOCONTEXT{$b}; // siehe javascript:src_widget_morelikethis in RODIN
				$LOCALLOG.="<br>&nbsp;&nbsp; $b";
				//Construct one table line (tr) for term $b:
				$HTML.=$widget_classname::make_ontofacet_tr('b',$b,$b_semanticcontextbase64,$searchtermlang,$counter,$SRCID,$APP_ID_CLEAN);
				$counter++;
			} // foreach $broaders as $b
			$HTML.="</table><!--table D-->
						</div><!--id=fb_itemcontent_$SRCID class=facetgroup-active-->";
		}
		####################################################
		#
		# BROADER TERMS (END)
		#
		####################################################
		
		
		####################################################
		#
		# NARROWER TERMS:
		#
		####################################################
		if (($nc=is_array($narrowers) * ($count_nt=count($narrowers)))>0)
		{
			$LOCALLOG.="<br><i>$nc Narrower:</i>";
			$ONTO_HEADER_DIV = $widget_classname::onto_th_header_div('Narrower',$count_nt,$SRCID,$APP_ID_CLEAN);
			$HTML.=<<<EOH
						$ONTO_HEADER_DIV
						<div id="fb_itemcontent_n_$SRCID" class="facetlist-active">
							<table class='tableD' cellspacing="0" cellpadding="0" border="0" ><!--table D-->
EOH;
			$ONTOCONTEXT=$SKOS['data']['n'];

			foreach($narrowers as $n)
			{
				$n=trim($n);
				$n_semanticcontextbase64=$ONTOCONTEXT{$n}; // siehe javascript:src_widget_morelikethis in RODIN
				$LOCALLOG.="<br>&nbsp;&nbsp; $n";
				//Construct one table line (tr) for term $n:
				$HTML.=$widget_classname::make_ontofacet_tr('n',$n,$n_semanticcontextbase64,$searchtermlang,$counter,$SRCID,$APP_ID_CLEAN);
				$counter++;
			} // foreach $narrowers as $n
			$HTML.="</table><!--table D-->
						</div><!--id=fb_itemcontent_n_$SRCID class=facetlist-active-->";
		}
		####################################################
		#
		# NARROWER TERMS (END)
		#
		####################################################
		
		
		
		
		
		####################################################
		#
		# RELATED TERMS:
		#
		####################################################
		if (($rc=is_array($related) * ($count_rt=count($related)))>0)
		{
			$LOCALLOG.="<br><i>$nc Related:</i>";
			$ONTO_HEADER_DIV = $widget_classname::onto_th_header_div('Related',$count_rt,$SRCID,$APP_ID_CLEAN);
			$HTML.=<<<EOH
						$ONTO_HEADER_DIV
						<div id="fb_itemcontent_r_$SRCID" class="facetlist-active">
							<table class='tableD' cellspacing="0" cellpadding="0" border="0" ><!--table D-->
EOH;
			$ONTOCONTEXT=$SKOS['data']['r'];

			foreach($related as $r)
			{
				$r=trim($r);
				$r_semanticcontextbase64=$ONTOCONTEXT{$r}; // siehe javascript:src_widget_morelikethis in RODIN
				$rootbase46=''; // siehe javascript:src_widget_morelikethis in RODIN
				$LOCALLOG.="<br>&nbsp;&nbsp; $r";
				//Construct one table line (tr) for term $r:
				$HTML.=$widget_classname::make_ontofacet_tr('r',$r,$r_semanticcontextbase64,$searchtermlang,$counter,$SRCID,$APP_ID_CLEAN);
				$counter++;
			} // foreach $related as $r
			$HTML.="</table><!--table D-->
						</div><!--id=fb_itemcontent_r_$SRCID class=facetlist-active-->";
		}
		####################################################
		#
		# RELATED TERMS (END)
		#
		####################################################
		
	} // foreach $EXPANSIONS as $SKOS
	//$LOCALLOG.="<hr>";
	# Closing SRC section
	
	$HTML.=<<<EOH
								</div><!--id=fb_itemcontent_b_$SRCID class=facetlist-active-->
							</div><!--facetboard-container-->
						</td>
					</tr>
				</table><!-- table A -->
			</div><!--facetBoardContent-->
		</div>
EOH;

	$NOTIFYTXT="Semantical facets for <br><b>&#171;$QUERYB&#187;</b>";
	//$NOTIFY=true;
}
else {
	$NOTIFY=true;
	$NOTIFYTXT="Sorry, no semantical facets for <br><b>&#171;$QUERYB&#187;</b>";
}


if($NOTIFY)
$ONTONOTIFICATION_SECTION=<<<EON
<table class='onotification visible'>
	<tr class='onotification'>
	<td class='onotification'>
		<a id='aonotification' ><label id='lonotification'>$NOTIFYTXT</label></a>
	</td>
</tr>
<tr class='whitetr'>
	<td class='whitetd'>
	</td>
</tr>
</table>
EON;

$HTML=$ONTONOTIFICATION_SECTION.$HTML;

$OUTPUT=<<<EOO
$HTML
$LOCALLOG__DEAKT
EOO;
	echo $OUTPUT;
	echo '</div>'; // widgetarea
	} // QUERY
 } // build_and_show_SRC
 
 
 
	 /**
	 * @param $relation_bnr - either 'Broader' or 'Narrower' or 'related'
	 * @param $count_t - number of facets for relation
	 * @param $SRCID - ID of SRC
	 * @return A DIV with mouse events
	 * @example onto_th_header_div('Norrower',$count_nt,$SRCID)
	 */
	private static function onto_th_header_div($relation_bnr,$count_t,$SRCID,$APPID)
	{
		global $widget_classname;
		$relation_bnr_lc = strtolower($relation_bnr);
		$segment='_'.$relation_bnr_lc[0].'_';
				$HTML.=<<<EOH
						<div class="facetgroup-header-{$relation_bnr_lc}" 
								id="fb_item_n_$SRCID" 
								style="visibility: visible; display: block;">
							<table class='tableC' cellspacing="0" cellpadding="0" border="0" ><!--table C-->
								<tr title="Show/hide more specialised terms in $srcname" 
										onclick="fb_toggle_itemcontent('$SRCID','$segment')" 
										style="cursor: pointer;font-style:italic;">
									<td width="12" valign="center" 
											alt="Expand" 
											class="fb-collapser" id="fb_itemname_expander{$segment}$SRCID">
									</td>
									<td class='fb_relationname' align="left" class="facetcontrol-td">$relation_bnr terms</td>
									<td class="facet-result-count" id="fb_itemcount{$segment}$SRCID" style="display: none;" align='right'>$count_t terms</td>
								</tr>
							</table><!--table C-->
						</div><!--class='facetgroup-header-$relation_bnr'-->
EOH;
	return $HTML;
	} // onto_th_header_div
		
	
	/**
	 * @return a string without stop words
	 */
	private static function clean_facet_term($s)
	{
		global $stopwords;
		$s_words=explode(' ',$s);
		$s_words_cleaned = array_unique(cleanup_stopwords($s_words, $stopwords));
		$s_cleaned=implode(' ',$s_words_cleaned);
		return $s_cleaned;
	} // clean_facet_term
	


	/**
	 * @param $bnr - either 'b' or 'n' or 'r'
	 * @param $term - the facet term
	 * @param $counter - diversifying counter
	 * @param $SRCID - diversifying SRC ID
	 * @return - An HTML expression included in <tr>...</tr>
	 * 
	 * Example: make_ontofacet_tr('b',$term,$counter,$SRCID)
	 */
	private static function make_ontofacet_tr($bnr,$term,$semanticcontextbase64,$lang,$counter,$SRCID,$appid)
	{
		global $DEBUG;
		global $BASISRODINIMAGESURL;
		global $ELIBIMAGESDIR;
		global $TTPNEWLINE;
		global $widget_classname;
		$TERM_ID='ft'.$SRCID.'_'.$bnr.$counter;
		$term_cleaned=$widget_classname::clean_facet_term($term);
		if (trim($semanticcontextbase64))
		{
			$EVTL_MLTSEARCH=<<<EOE
<img 
	onclick="src_widget_morelikethis(this,'$semanticcontextbase64','$term','$lang');" 
	tt="Filter results with ___" 
	class="ontofacetterm4exwr mlt" 
	src="$BASISRODINIMAGESURL/docsemfilter_fb_16x16.png">
EOE;
	}
/*
 * 	<tr onmouseover="document.getElementById('ricons_{$bnr}_{$SRCID}_$counter').style.visibility='visible'" 
			onmouseout="document.getElementById('ricons_{$bnr}_{$SRCID}_$counter').style.visibility='hidden'" 
 * 
 */
 /* HANDLERS see RODINsemfilters.js: */	
 /* customize: (st) /*
 /* set st (selected text) to term, later it can be changed using mouse text selection */
 	$HTML.=<<<EOH
	<tr onmouseover="fomh('$bnr',$SRCID,$counter,this,'$appid')" 
			onmouseout="fomo('$bnr',$SRCID,$counter,this,'$appid')" 
			onmousedown="fomd('$bnr',$SRCID,$counter,event,'$appid')" 
			onmouseup="fomu('$bnr',$SRCID,$counter,this,event,'$appid')"
			onclick="do_bc('$TERM_ID','$bnr',,'$appid')"
			id='$TERM_ID'
			st = '$term'
			stc= '$term_cleaned'
			class="fb-term-row"
	>
		<td align="right" class='fb icons hidden'
				id="ricons_{$bnr}_{$SRCID}_$counter" 
			><img 
						src="$BASISRODINIMAGESURL/add-to-breadcrumb.png" 
						class="ontofacetterm bc" 
						tt="Click to use ___ as a filter" 
						onclick="do_bc('$TERM_ID','$bnr','$appid');"
			><img 
						src="$BASISRODINIMAGESURL/magnifier-onto-small.png" 
						class="ontofacetterm xp" 
						tt="Click to explore further using ___"
						onclick="do_xp('$TERM_ID',$DEBUG,'$appid')"
			><img 
						src="$BASISRODINIMAGESURL/input_right_search_hover.png" 
						class="ontofacetterm sc" 
						tt="Click to search directly with ___"
						onclick="do_sc('$TERM_ID','$appid')"
		>$EVTL_MLTSEARCH&nbsp;</td>
		<td align="left" class='fb'>
			<a class="fb-term" >$term</a>
		</td>
	</tr>
EOH;
	return $HTML;
}
	
 
 
 
 

/* ********************************************************************************
 * Decide which function the state machine is on.
 ******************************************************************************* */

} // class RDWuc3_aggtheresults

include_once("../u/RodinWidgetSMachine.php");

?>