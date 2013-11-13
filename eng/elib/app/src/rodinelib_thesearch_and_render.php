<?php

/**
 * FILE: rodinelib_THEsearch_and_render
 * AUTHOR: Fabio Ricci, fabio.ricci@semweb.ch, Tel. +41-76-5821961
 * ON BEHALF OF: HEG - Haute Ecole de Gestion, Geneva
 * DATE: August 2013
 * 
 * This script detects the language of query and searches in the thesauries of RODIN, and shows the results (semantic facetts) e-lib-like
 * It returns an HTML content which should be rendered in a DIV inside another HTML page
 */

$filenamex="app/elibroot.php";
###############################################################
$max=10; for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
{ if (file_exists("$updir$filenamex")) 
	{	require_once("$updir$filenamex"); break;}	}

$filenamex="app/u/arcUtilities.php";
###############################################################
$max=10; for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
{ if (file_exists("$updir$filenamex")) 
	{	require_once("$updir$filenamex"); break;}	}
###############################################################
$filename="app/u/LanguageDetection.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}
###############################################################

$NOTIFY=$_REQUEST['notify'];
$DEBUG=$_REQUEST['DEBUG']; if(!$DEBUG) $DEBUG=0;


//Strip:
$QUERY=$_REQUEST['query'];
$QUERY=str_replace(' AND ',' ',$QUERY);
$QUERY=str_replace(' OR ',' ',$QUERY);
$QUERY=str_replace('"','',$QUERY);
$QUERY=str_replace(' ','%20',$QUERY);

$THESOURCES	=$ELIB_THESAURI_TO_USE;
$M					=$ELIB_THESAURI_S_M; 
$USERID			=$ELIB_USERID; 

if ($DEBUG)
{
	print "<br>USING Param QUERY: $QUERY";
	print "<br>USING Param THESOURCES: $THESOURCES";
	print "<br>USING Param ELIB_WIDGET_S_M: $ELIB_WIDGET_S_M";
	print "<br>USING Param ELIB_USERID: $ELIB_USERID";
}


###############################################################
###############################################################



$base_url="$WEBROOT$RODINROOT/$RODINSEGMENT/app/webs/thesearch.php";
$url = "$base_url?query=$QUERY&thesources=$THESOURCES&userid=$USERID&m=$M&ontocontext=1&DEBUG=$DEBUG";

// Call $url

if ($DEBUG) print "<a href='$url' target='blank'>$url</a>";
$jsonResultsDecoded = json_decode(file_get_contents($url), true);

$query 					= $jsonResultsDecoded{'query'};
$searchtermlang = $jsonResultsDecoded{'searchtermlang'};
$skostheresults = $jsonResultsDecoded{'skostheresults'};
$errortxt				= $jsonResultsDecoded{'error'};

###############################################################
#
# TODO: Cambiare i Links (dinamici !!! Server !!!) /-/rodin/gen/u/images ....
#
###############################################################

$searchtermlang	=$searchtermlang? $searchtermlang: detectLanguage($query);
if (in_array($searchtermlang, $languages))
	$stopwords =get_stopwords_from_db($searchtermlang);
else 
	$stopwords =get_stopwords_from_db();



if ($DEBUG)
{
	if(0)
	print  "<br>skostheresults: ".count($skostheresults)
				."<br>errortxt: $errortxt<br>"
				;
	//var_dump($skostheresults); print "<br><br>";
	$RDFLOG = tell_skos_subjects($skostheresults,'TEST SKOS');
	print $RDFLOG ."<br>"; 
}
$QUERYB=urldecode($QUERY);
$SRCFACETTITLE="___{$TTPNEWLINE}{$TTPNEWLINE}"
								."Click to search with this facet term or choose one available action on the right side by clicking on the corresponding icon; "
								."select any text portion of the term with your mouse to adapt the text upon which the action should be started; "
								."left-click on the term to erase the selection (the selection is also erased by selecting in another term)";

//Scan and render semantical facets
if (is_array($skostheresults) && ($c=count($skostheresults)))
{
	$HTML.=<<<EOH
<div id='facetBoardContent'>
	<hr class="elibfacetdeco">
	<h1 style="padding-bottom:0;font-size:15px;font-weight:bold;margin-bottom:5px" >Semantical facets</h1>
	<!-- FRI WITHOUT TITLE BECAUSE WAIT FOR MORE INSPIRATION 
	<div id="facetsBoardTitleBar" class="rodinBoardTitleBar" onclick="this.getElementById('faceBoardTogle').click()">
		<h1 Style="text-align:left"> Semantical Facets </h1>
	</div> --> 
	<!--Opening SRC section -->
	<div id="facetboard-container" name="facetboard-container" tt='$SRCFACETTITLE'>
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
			print "<br><b>$srcname</b> SRCID=($SRCID) : "; 
			print "<br>broaders:";
			foreach($broaders as $BBB)
			{
				print "<br> B $BBB";
			}	
			print "<br>end broaders<br>";
		}
		$LOCALLOG.= "<br><b>$srcname</b> ($SRCID).SKOS:";
		$SRCTERMSCOUNT = count($broaders) + count($narrowers) + count($related);
		
		
		$HTML.=<<<EOH
				<tr>
					<td align="left" 
							valign="top" 
							onmouseout="document.getElementById('tyn_$SRCID').style.visibility ='hidden'" 
							onmouseover="document.getElementById('tyn_$SRCID').style.visibility ='visible'" 
							class="onoff"
							>
							<a onclick="fb_toggleonto_temponoff(this,$SRCID,false)" 
								srcname="$srcname" 
								title_off_on="Click to (re)activate ontological facets from $srcname" 
								title_on_off="Click to temporarily deactivate facets from $srcname" 
								iconsrc_off_on="$RODINIMAGESURL/ico_open.png" 
								iconsrc_on_off="$RODINIMAGESURL/ico_close.gif" 
								shouldchecked="false" 
								checked="true" 
								style="visibility: hidden;" 
								title="Click to temporarily deactivate facets from $srcname" id="tyn_$SRCID" 
								href="#">
									<img 	width="16" height="16" 
												src="$RODINIMAGESURL/ico_close.gif" 
												id="iyn_$SRCID" 
												style="width: 15px; height: 15px;"/>
							</a>
					</td>
					<td class='fb'>
						<table class='tableB' cellspacing="0" cellpadding="0" border="0" bgcolor=#eee><!--table B-->
							<tr onmouseover="document.getElementById('tyn_$SRCID').style.visibility ='visible'" 
									onmouseout="document.getElementById('tyn_$SRCID').style.visibility ='hidden'" 
									style="cursor: pointer;" class="ontotr">
								<td class='fb' valign="center" title="Click to show/hide facets from $srcname" 
										onclick="fb_toggle_allItemContent('$SRCID');" 
										alt="Expand" class="fb-collapser" 
										id="fb_itemname_expander_$SRCID">
								</td>
								<td align="left" 
										valign="center" 
										onclick="document.getElementById('fb_itemname_expander_$SRCID').onclick();" 
										title="$SRCTITLE" 
										class="facetcontrol-td" 
										id="fb_itemname_expander2_$SRCID">$SRCPRETTYNAME</td>
								<td valign='top' class="facet-result-count" id="fb_itemcount_$SRCID">$SRCTERMSCOUNT terms
								</td>
							</tr>
						</table><!--table B-->
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
			$ONTO_HEADER_DIV = onto_th_header_div('Broader',$count_bt,$SRCID);
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
				$HTML.=make_ontofacet_tr('b',$b,$b_semanticcontextbase64,$searchtermlang,$counter,$SRCID);
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
			$ONTO_HEADER_DIV = onto_th_header_div('Narrower',$count_nt,$SRCID);
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
				$HTML.=make_ontofacet_tr('n',$n,$n_semanticcontextbase64,$searchtermlang,$counter,$SRCID);
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
			$ONTO_HEADER_DIV = onto_th_header_div('Related',$count_rt,$SRCID);
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
				$HTML.=make_ontofacet_tr('r',$r,$r_semanticcontextbase64,$searchtermlang,$counter,$SRCID);
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

	header('Content-Type: text/html; charset=utf-8');
	print $OUTPUT;

########

 
 /**
 * @param $relation_bnr - either 'Broader' or 'Narrower' or 'related'
 * @param $count_t - number of facets for relation
 * @param $SRCID - ID of SRC
 * @return A DIV with mouse events
 * @example onto_th_header_div('Norrower',$count_nt,$SRCID)
 */
function onto_th_header_div($relation_bnr,$count_t,$SRCID)
{
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
}


/**
 * @return a string without stop words
 */
function clean_facet_term($s)
{
	global $stopwords;
	$s_words=explode(' ',$s);
	$s_words_cleaned = array_unique(cleanup_stopwords($s_words, $stopwords));
	$s_cleaned=implode(' ',$s_words_cleaned);
	return $s_cleaned;
}



/**
 * @param $bnr - either 'b' or 'n' or 'r'
 * @param $term - the facet term
 * @param $counter - diversifying counter
 * @param $SRCID - diversifying SRC ID
 * @return - An HTML expression included in <tr>...</tr>
 * 
 * Example: make_ontofacet_tr('b',$term,$counter,$SRCID)
 */
function make_ontofacet_tr($bnr,$term,$semanticcontextbase64,$lang,$counter,$SRCID)
{
	global $DEBUG;
	global $RODINIMAGESURL;
	global $ELIBIMAGESDIR;
	global $TTPNEWLINE;
	$TERM_ID='ft'.$SRCID.'_'.$bnr.$counter;
	$term_cleaned=clean_facet_term($term);
	if (trim($semanticcontextbase64))
	{
		$EVTL_MLTSEARCH=<<<EOE
<img 
	onclick="src_widget_morelikethis(this,'$semanticcontextbase64','$term','$lang');" 
	tt="Filter results with ___" 
	class="ontofacetterm4exwr mlt" 
	src="$RODINIMAGESURL/docsemfilter_fb_16x16.png">
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
	<tr onmouseover="fomh('$bnr',$SRCID,$counter,this)" 
			onmouseout="fomo('$bnr',$SRCID,$counter,this)" 
			onmousedown="fomd('$bnr',$SRCID,$counter,event)" 
			onmouseup="fomu('$bnr',$SRCID,$counter,this,event)"
			id='$TERM_ID'
			st = '$term'
			stc= '$term_cleaned'
			class="fb-term-row"
	>
		<td align="left" class='fb' onclick="do_bc('$TERM_ID','$bnr')">
			<a class="fb-term" >$term</a>
		</td>
		<td align="right" class='fb icons hidden'
				id="ricons_{$bnr}_{$SRCID}_$counter" 
			><img 
						src="$RODINIMAGESURL/add-to-breadcrumb.png" 
						class="ontofacetterm bc" 
						tt="Click to use ___ as a filter" 
						onclick="do_bc('$TERM_ID','$bnr');"
			><img 
						src="$RODINIMAGESURL/magnifier-onto-small.png" 
						class="ontofacetterm xp" 
						tt="Click to explore further using ___"
						onclick="do_xp('$TERM_ID',$DEBUG)"
			><img 
						src="../img/input_right_search_hover.png" 
						class="ontofacetterm sc" 
						tt="Click to search directly with ___"
						onclick="do_sc('$TERM_ID')"
		>$EVTL_MLTSEARCH</td>
	</tr>
EOH;
	return $HTML;
}
	

?>