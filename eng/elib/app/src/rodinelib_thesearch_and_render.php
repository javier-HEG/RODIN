<?php

/**
 * FILE: rodinelib_THEsearch_and_render
 * AUTHOR: Fabio Ricci, fabio.ricci@semweb.ch, Tel. +41-76-5821961
 * ON BEHALF OF: HEG - Haute Ecole de Gestion, Geneva
 * DATE: August 2013
 * 
 * This script searches in the thesauries of RODIN, and shows the results (semantic facetts) e-lib-like
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
###############################################################

$NOTIFY=$_REQUEST['notify'];
$DEBUG=$_REQUEST['DEBUG']; if(!$DEBUG) $DEBUG=0;
$QUERY=str_replace(' ','%20',$_REQUEST['query']);
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
$url = "$base_url?query=$QUERY&thesources=$THESOURCES&userid=$USERID&m=$M&DEBUG=$DEBUG";

// Call $url

if ($DEBUG) print "<a href='$url' target='blank'>$url</a>";
$jsonResultsDecoded = json_decode(file_get_contents($url), true);

$query 					= $jsonResultsDecoded{'query'};
$skostheresults = $jsonResultsDecoded{'skostheresults'};
$errortxt				= $jsonResultsDecoded{'error'};

###############################################################
#
# TODO: Cambiare i Links (dinamici !!! Server !!!) /-/rodin/gen/u/images ....
#
###############################################################


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
								."Please choose one available action on the right side by clicking on the corresponding icon; "
								."select any text portion of the term with your mouse to adapt the text upon which the action should be started; "
								."left-click on the term to erase the selection (the selection is also erased by selecting in another term)";
if($NOTIFY)
$ONTONOTIFICATION_SECTION=<<<EON
<table class='onotification visible'>
	<tr class='onotification'>
	<td class='onotification'>
		<a id='aonotification' ><label id='lonotification'>Semantical facets for <br><b>&#171;$QUERYB&#187;</b></label></a>
	</td>
</tr>
<tr class='whitetr'>
	<td class='whitetd'>
	</td>
</tr>
</table>
EON;

$HTML=$ONTONOTIFICATION_SECTION;

//Scan and render semantical facets
if (is_array($skostheresults) && ($c=count($skostheresults)))
{
	$HTML.=<<<EOH
<div id='facetBoardContent'>
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
			print "<br><b>xxx $srcname</b> SRCID=($SRCID) : "; 
			print "<br>broaders:";
			foreach($broaders as $BBB)
			{
				print "<br> B $BBB";
			}	
			print "<br>end broaders<br>";
		}
		$LOCALLOG.= "<br><b>$srcname</b> ($SRCID).SKOS:";
		$SRCTERMSCOUNT = "32";
		
		
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
								iconsrc_off_on="$RODINIMAGESDIR/ico_open.png" 
								iconsrc_on_off="$RODINIMAGESDIR/ico_close.gif" 
								shouldchecked="false" 
								checked="true" 
								style="visibility: hidden;" 
								title="Click to temporarily deactivate facets from $srcname" id="tyn_$SRCID" 
								href="#">
									<img 	width="16" height="16" 
												src="$RODINIMAGESDIR/ico_close.gif" 
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
			$counter=0;
			foreach($broaders as $b)
			{
				$b=trim($b);
				$rootbase46=''; // siehe javascript:src_widget_morelikethis in RODIN
				$LOCALLOG.="<br>&nbsp;&nbsp; $b";
				//Construct one table line (tr) for term $b:
				$HTML.=make_ontofacet_tr('b',$b,$counter,$SRCID);
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
			foreach($narrowers as $n)
			{
				$n=trim($n);
				$rootbase46=''; // siehe javascript:src_widget_morelikethis in RODIN
				$LOCALLOG.="<br>&nbsp;&nbsp; $n";
				//Construct one table line (tr) for term $n:
				$HTML.=make_ontofacet_tr('n',$n,$counter,$SRCID);
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
			foreach($related as $r)
			{
				$r=trim($r);
				$rootbase46=''; // siehe javascript:src_widget_morelikethis in RODIN
				$LOCALLOG.="<br>&nbsp;&nbsp; $r";
				//Construct one table line (tr) for term $r:
				$HTML.=make_ontofacet_tr('r',$r,$counter,$SRCID);
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


}

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
 * @param $bnr - either 'b' or 'n' or 'r'
 * @param $term - the facet term
 * @param $counter - diversifying counter
 * @param $SRCID - diversifying SRC ID
 * @return - An HTML expression included in <tr>...</tr>
 * 
 * Example: make_ontofacet_tr('b',$term,$counter,$SRCID)
 */
function make_ontofacet_tr($bnr,$term,$counter,$SRCID)
{
	global $DEBUG;
	global $RODINIMAGESDIR;
	global $ELIBIMAGESDIR;
	global $TTPNEWLINE;
	$TERM_ID='ft'.$SRCID.'_'.$bnr.$counter;
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
			class="fb-term-row"
	>
		<td align="left" class='fb'>
			<a class="fb-term" >$term</a>
		</td>
		<td align="right" class='fb icons hidden'
				id="ricons_{$bnr}_{$SRCID}_$counter" 
			><img 
						src="$RODINIMAGESDIR/add-to-breadcrumb.png" 
						class="ontofacetterm bc" 
						tt="Click to use ___ as a filter" 
						onclick="do_bc('$TERM_ID');"
			><img 
						src="$RODINIMAGESDIR/magnifier-onto-small.png" 
						class="ontofacetterm xp" 
						tt="Click to explore further using ___"
						onclick="do_xp('$TERM_ID',$DEBUG)"
			><img 
						src="$ELIBIMAGESDIR/input_right_search.png" 
						class="ontofacetterm sc" 
						tt="Click to set ___ as search text"
						onclick="do_sc('$TERM_ID')"
		><img 
						onclick="mlt_fb('$TERM_ID'));" 
						tt="Filter results with ___" 
						class="ontofacetterm4exwr mlt" 
						src="$RODINIMAGESDIR/docsemfilter_fb_16x16.png"
			></td>
	</tr>
EOH;
	return $HTML;
}
	

?>