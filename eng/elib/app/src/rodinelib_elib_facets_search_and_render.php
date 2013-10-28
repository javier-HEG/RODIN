<?php

/**
 * FILE: rodinelib_elib_facets_search_and_render.php
 * AUTHOR: Fabio Ricci, fabio.ricci@semweb.ch, Tel. +41-76-5821961
 * ON BEHALF OF: HEG - Haute Ecole de Gestion, Geneva
 * DATE: Oktober 2013
 * 
 * MOCKUP
 * 
 * This script shows a number of result reducing categories, document types
 * like the current facets inside e-lib.ch portal - it should remember to that portal
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
$filename="app/tests/Logger.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}
###############################################################





//$NOTIFY=$_REQUEST['notify'];
$NOTIFY=1;
$DEBUG=$_REQUEST['DEBUG']; if(!$DEBUG) $DEBUG=0;
$QUERY=$_REQUEST['query']; // replace blank by and in elib search to match all
$QUERYB=urldecode($QUERY);
$M						=$ELIB_WIDGET_S_M; 
$USER=$USERID	=$ELIB_USERID; 

if ($DEBUG)
{
	print "<br>USING Param QUERY: $QUERY";
	print "<br>USING Param ELIB_WIDGET_S_M: $ELIB_WIDGET_S_M";
	print "<br>USING Param ELIB_USERID: $ELIB_USERID";
}


###############################################################
###############################################################

$ELIB_FACETS = get_elib_facets_from_elibdatasource($QUERY,$M);

$SRCFACETTITLE=	"Click to filter results with this facet, alternatively choose one available action on the right side by clicking on the corresponding icon; "
								."select any text portion of the term with your mouse to adapt the text upon which the action should be started; "
								."left-click on the term to erase the selection (the selection is also erased by selecting in another term)";


if ($DEBUG)
{
	print  "<br>elib_facets_from_elibdatasource for query<br>"; 
	var_dump($ELIB_FACETS);
}


if (count($ELIB_FACETS))
{
	$HTML.=<<<EOH
		<hr class="elibfacetdeco">
		<h1 style="padding-bottom:0;font-size:15px;font-weight:bold" >Refine results</h1>
	<div id='facetBoardContentElib'>
	<!-- FRI WITHOUT TITLE BECAUSE WAIT FOR MORE INSPIRATION 
	<div id="facetsBoardTitleBar" class="rodinBoardTitleBar" onclick="this.getElementById('faceBoardTogle').click()">
		<h1 Style="text-align:left"> Semantical Facets </h1>
	</div> --> 
	<!--Opening SRC section -->
	<div id="facetboard-container" name="facetboard-container" tt='$SRCFACETTITLE'>
		<table class='tableA' cellpadding=0 cellspacing=0 border=0><!-- table A -->
EOH;
	foreach($ELIB_FACETS as $theme=>$values)
	{
		$theme_encoded=str_replace(' ','-',$theme); //In case of blanks -> this breaks id's tech
		
		$THEMEID++;
		$SRCTITLE = lg('ttp'.$theme);
		$SRCPRETTYNAME=lg('lbl'.$theme);
		
		if ($values && !is_array($values))
			$values=array($values);
		
		$has_values= count($values);
		
		if ($DEBUG) {
			print "<br><b>theme</b> $theme : "; 
			print "<br>values:";
			foreach($values as $BBB)
			{
				print "<br> V $BBB";
			}	
			print "<br>end V<br>";
		}
		$VALUESCOUNT = count($values);
		
		$HTML.=<<<EOH
				<tr>
					<td class='fb'>
						<div id="fb_itemcontent_$THEMEID" class="facetgroup-active">
EOH;
		
		$counter=1;
		
		####################################################
		#
		# VALUES: (all the single facets)
		#
		####################################################
		if (($vc=is_array($values) * ($count_vt=count($values)))>0)
		{
			$ONTO_HEADER_DIV = onto_th_header_div($theme,$theme_encoded,$count_vt,$THEMEID);
			$HTML.=<<<EOH
						$ONTO_HEADER_DIV
						<div id="fb_itemcontent_b_$THEMEID" class="facetlist-active">
							<table class='tableD' cellspacing="0" cellpadding="0" border="0" ><!--table D-->
EOH;
			foreach($values as $v)
			{
				$v=trim($v);
				//Construct one table line (tr) for term $v:
				$HTML.=make_ontofacet_tr($theme_encoded,$v,'en',$counter,$THEMEID);
				$counter++;
			} // foreach $values as $v
			$HTML.="</table><!--table D-->
						</div><!--id=fb_itemcontent_$THEMEID class=facetgroup-active-->";
		}
		####################################################
		#
		# VALUES (END)
		#
		####################################################
	
		
	} // foreach $EXPANSIONS as $SKOS
	//$LOCALLOG.="<hr>";
	# Closing SRC section
	
	$HTML.=<<<EOH
								</div><!--id=fb_itemcontent_b_$THEMEID class=facetlist-active-->
							</div><!--facetboard-container-->
						</td>
					</tr>
				</table><!-- table A -->
			</div><!--facetBoardContent-->
		</div>
EOH;

	$NOTIFYTXT="Restricting facets for <br><b>&#171;$QUERYB&#187;</b>";
	//$NOTIFY=true;
}
else {
	$NOTIFY=true;
	$NOTIFYTXT="Sorry, no restricting facets for <br><b>&#171;$QUERYB&#187;</b>";
}


if($NOTIFY)
$ONTONOTIFICATION_SECTION=<<<EON
<table class='pnotification visible'>
	<tr class='pnotification'>
	<td class='pnotification' style="margin-left:0">
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
 * @param $facetgroup - either 'author' or 'date' or ...
 * @param $count_t - number of facets for relation
 * @param $THEMEID - ID of SRC
 * @return A DIV with mouse events
 * @example onto_th_header_div('Norrower',$count_nt,$THEMEID)
 */
function onto_th_header_div($facetgroup_title,$facetgroup,$count_t,$THEMEID)
{
	$VERTICALSEPARATION=10;
	$facetgroup_lc = strtolower($facetgroup);
	$facetgroup_bartitle = make_bar_title($facetgroup_title);
	//$PLURAL = $count_t>1?'s':'';
	$segment='_'.$facetgroup_lc[0].'_';
				$HTML.=<<<EOH
						<div class="facetgroup-header-{$facetgroup_lc}" 
								id="fb_item_n_$THEMEID" 
								style="visibility: visible; display: block;">
							<table class='tableC' cellspacing="0" cellpadding="0" border="0" ><!--table C-->
							 	<!--tr height='$VERTICALSEPARATION'></tr-->
								<tr>
								<td style="padding:0">
										<hr class="elibfacetdeco_2">
								</td>
								</tr>
								<tr title="Show/hide more specialised terms in $srcname" 
										onclick="fb_toggle_itemcontent('$THEMEID','$segment')" 
										style="cursor: pointer;font-style:italic;">
									<td width="12" valign="center" 
											alt="Expand" 
											class="fb-collapser" id="fb_itemname_expander{$segment}$THEMEID">
									</td>
									<td class='fb_relationname' align="left" class="facetcontrol-td"><span class='elibh4'>{$facetgroup_bartitle}$PLURAL</span></td>
									<td class="facet-result-count" id="fb_itemcount{$segment}$THEMEID" style="display: none;" align='right'>$count_t terms</td>
								</tr>
							</table><!--table C-->
						</div><!--class='facetgroup-header-$facetgroup'-->
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
	$cleaned_s_words=cleanup_stopwords($s_words, $stopwords);
	if ($cleaned_s_words)
	{
		$s_words_cleaned = array_unique(cleanup_stopwords($s_words, $stopwords));
		$s_cleaned=implode(' ',$s_words_cleaned);
	}
	else $s_cleaned=array();
	return $s_cleaned;
}



function make_bar_title($title)
{
	$bartitle=strtoupper(substr($title,0,1)) . strtolower(substr($title,1));
	return $bartitle;	
}

/**
 * @param $elibfacetname - name of the (elib) facet group 
 * @param $term - the facet term
 * @param $counter - diversifying counter
 * @param $THEMEID - diversifying SRC ID
 * @return - An HTML expression included in <tr>...</tr>
 * 
 * Example: make_ontofacet_tr('b',$term,$counter,$THEMEID)
 * SPECIAL: Click on the row does the same as add2breadcrumb!
 */
function make_ontofacet_tr($elibfacetname,$term,$lang,$counter,$THEMEID)
{
	global $DEBUG;
	global $RODINIMAGESURL;
	global $ELIBIMAGESDIR;
	global $TTPNEWLINE;
	$TERM_ID='ft'.$THEMEID.'_'.$elibfacetname.$counter;
	$term_cleaned=clean_facet_term($term);

 /* HANDLERS see RODINsemfilters.js: */	
 /* customize: (st) /*
 /* set st (selected text) to term, later it can be changed using mouse text selection */
 	$HTML.=<<<EOH
	<tr onmouseover="fomh('$elibfacetname',$THEMEID,$counter,this)" 
			onmouseout="fomo('$elibfacetname',$THEMEID,$counter,this)" 
			onmousedown="fomd('$elibfacetname',$THEMEID,$counter,event)" 
			onmouseup="fomu('$elibfacetname',$THEMEID,$counter,this,event)" 
			onclick="do_bc('$TERM_ID','$elibfacetname');"
			id='$TERM_ID'
			st = '$term'
			stc= '$term_cleaned'
			class="fb-term-row"
	>
		<td align="left" class='fb'>
			<a class="fb-term" >$term</a>
		</td>
		<td align="right" class='fb icons hidden'
				id="ricons_{$elibfacetname}_{$THEMEID}_$counter" 
			><img 
						src="$RODINIMAGESURL/add-to-breadcrumb.png" 
						class="ontofacetterm bc" 
						tt="Click to use ___ as a filter" 
						onclick="do_bc('$TERM_ID','$elibfacetname');"
			><img 
						src="$RODINIMAGESURL/magnifier-onto-small.png" 
						class="ontofacetterm xp" 
						tt="Click to explore further using ___"
						onclick="do_xp('$TERM_ID',$DEBUG)"
			><img 
						src="../img/input_right_search.png" 
						class="ontofacetterm sc" 
						tt="Click to set ___ as search text"
						onclick="do_sc('$TERM_ID')"
		>$EVTL_MLTSEARCH</td>
	</tr>
EOH;
	return $HTML;
}
	
	
	
	
/**
 * This method calls e-lib widget data source
 * and returns all the facet information
 * to be used inside the right elib rodin mockup column
 * 
 * @param $q - the query
 * @param $m - the number of records to gather from the data source
 * @param $q - the query
 */
function get_elib_facets_from_elibdatasource($q,$m) 
{
	global $DEBUG;
	
  $searchsource_baseurl="http://www.library.ethz.ch/rib/v1/primo_elib/documents?";
	$options = array(	CURLOPT_HTTPHEADER => array('Accept:application/json','Accept-Charset: ISO-8859-1' ));
	$parameters['q'] = $query = $q;
	$parameters['bulksize'] = $m;

	if ($DEBUG) print "<br>CALLING $searchsource_baseurl + ($q)";

	list($timestamp,$jsonString) = get_cached_widget_response_curl($searchsource_baseurl, $parameters, $options);
	//print "FROM DS: ".htmlentities($jsonString);
        
	$jsonInfo = (json_decode($jsonString, true));

	if($DEBUG) {print "<br>JSON INFO:<br>"; var_dump($jsonInfo); }

	// Parse JSON result and build results
	$allResults = array();
	
	// TODO Check status is 200
	// TODO Implement iterative access to results, batches of size 10

	$lasthit=$jsonInfo['result']['hits']['lasthit'];
	$firsthit=$jsonInfo['result']['hits']['firsthit'];
	$NoOfResults=$lasthit - $firsthit + 1;
	
	if ($DEBUG)
	{
		print "<br>firsthit: $firsthit";
		print "<br>firsthit: $firsthit";
		print "<br>NoOfResults: $NoOfResults";
	}
	
	$ELIB_FACETS=array();
	if ($NoOfResults > 0) 
	{
		if (is_array($jsonInfo['result']['document']))
		foreach ($jsonInfo['result']['document'] as $record) 
		{
			// Get result data from record
			//print "ELIB RECORD: "; var_dump($record);exit;
			$recordid			=$record['recordid'];
			$biblio_data	=$record['biblioData'];
			$references		=$record['references'];
			$links				=$record['links'];
			$availability	=$record['availability'];
			$type					=$biblio_data['type'];
			
			add_unique_to_assocvector($ELIB_FACETS,'type',$type);
			
			$creation_date=$biblio_data['creationdate'];
			
			if ($creation_date<>0)
				$creation_date=scan_last_date($biblio_data['creationdate']);
			
			add_unique_to_assocvector($ELIB_FACETS,'creation date',$creation_date);
			
			if ($DEBUG) {
				print "<hr>SHOW ASSOC: <br>";
				var_dump($ELIB_FACETS);
				
				print "<hr>BIBLIODATA: <br>";
				var_dump($biblio_data);
			}
			
			$authorArray = array();
			$creator = trim(cleanup_author_desc($biblio_data['creator']));
			$contributor = trim(cleanup_author_desc($biblio_data['contributor']));
			$person = trim(cleanup_author_desc($biblio_data['person']));
			/* Since in e-lib may appear repetitions of a same word, 
			 * we stop the creator/contributor/person information 
			 * up to the first ';' found
			 */
			
			$creator=limit_to_symbol_if_found($creator,';');
			$contributor=limit_to_symbol_if_found($contributor,';');
			$person=limit_to_symbol_if_found($person,';');
			
			if ($creator 			&& !in_array($creator,$authorArray)) 			add_unique_to_assocvector($ELIB_FACETS,'author',$creator);
			if ($contributor 	&& !in_array($contributor,$authorArray)) 	add_unique_to_assocvector($ELIB_FACETS,'author',$contributor);
			if ($person 			&& !in_array($person,$authorArray)) 			add_unique_to_assocvector($ELIB_FACETS,'author',$person);
	
			// Book specific fields
			$RESULT{'description'} = strip_tags($links['abstract']);
			
	  	if ($DEBUG) print "<br>KEYW: ".$biblio_data{'keywords'};

			$KEYWS=$biblio_data{'keywords'};
			
			if($DEBUG) {
				print "<hr>KEYWORDS: "; var_dump($KEYWS);
			}
			
			//Keywords - attention - a lot of special cases!
			if (isset($KEYWS))
			{
				if ($DEBUG) print "<br>KEYWS";
				 if(! is_array($KEYWS)) 
				 {
					if ($DEBUG) print "<br>KEYWS not ARRAY";
					if (strstr($KEYWS,';'))
					{
						if ($DEBUG) print "<br>KEYWS contains ; ";
						$KEYWS=explode(';',$KEYWS);
					}
					else {
						if ($DEBUG) print "<br>TAKING KEYWS by ; ";
							$KEYWS = array($KEYWS);
					}
				 }
				foreach($KEYWS as $keyword_candidate)
				{
					if ($DEBUG) print "<br>Adding keyword facet as string ($keyword_candidate)";
					
					//Eliminate --XXXX comments
					if (strstr($keyword_candidate,'--'))
						$keyword_candidate=substr($keyword_candidate,0,strpos($keyword_candidate,'--'));
					
					if (strstr($keyword_candidate,';'))
					{
						if ($DEBUG) print " CONTAINS ; !!!";
						$KEYWORDS_SINGLES=explode(';',$keyword_candidate);
						
						//Repair tokens in case there was a "(" ... search/add all until ")" found
						$collect_closed=false;
						$recollected_token='';
						foreach($KEYWORDS_SINGLES as $kws)
						{
							if ($collect_closed)
							{
								$recollected_token.=';'.$kws;
								if (strstr($kws,')'))
								{
									if ($DEBUG) print "<br>RECOLLECTED TOKEN: ($recollected_token)";
									$collect_closed=false;
									$KEYWORD_SINGLES_REPAIRED[]=$recollected_token;
									$recollected_token='';
								}
							}
							else
							if (strstr($kws,'(') && !strstr($kws,')')) {
								$recollected_token.=$kws;
								$collect_closed=true;
							}
							else {
								$KEYWORD_SINGLES_REPAIRED[]=$kws;
							}	
						}
						
						foreach($KEYWORD_SINGLES_REPAIRED as $kws)
						{
							if ($DEBUG) print "<br>TOKEN: ($kws)";
							add_unique_to_assocvector($ELIB_FACETS,'keyword',trim($kws));
						}
					}
					else						
						add_unique_to_assocvector($ELIB_FACETS,'keyword',trim($keyword_candidate));
				}
			} // keyword
			

			
			if (isset($biblio_data['collection'])) {
				add_unique_to_assocvector($ELIB_FACETS,'collection',$biblio_data['collection']);
			}
			
			
			$LAN=$biblio_data['language'];
			if (isset($LAN)) {
				if ($DEBUG) print "<br>language: ".$LAN;
				if(strstr($LAN,';'))
				{
					foreach(explode(';',$LAN) as $L)
						add_unique_to_assocvector($ELIB_FACETS,'language',$L);
				}
				else
					add_unique_to_assocvector($ELIB_FACETS,'language',$LAN);
			}
			
			if (isset($availability['holdingsLds07'])) {
				if (is_array($availability['holdingsLds07']))
				{
					foreach($availability['holdingsLds07'] as $library)
					{
						$library=limit_to_symbol_if_found($library,'|');
						$library=limit_to_symbol_if_found($library,',');
						add_unique_to_assocvector($ELIB_FACETS,'library',$library);
					}
				}
				else {
						$library=limit_to_symbol_if_found($library,'|');
						$library=limit_to_symbol_if_found($library,',');
						add_unique_to_assocvector($ELIB_FACETS,'librarie',$availability['holdingsLds07']);
				}
			}

			
		}
	} // 	if ($NoOfResults > 0
	
	if ($DEBUG) {
		print "<hr>Returning FACETS: <br>"; var_dump($ELIB_FACETS);
	}
	
	
	return $ELIB_FACETS;
}
		

function limit_to_symbol_if_found($txt,$symbols)
{
	$DEBUG=0;
	if ($DEBUG)
 		print "<br>limit_to_symbol_if_found ($txt,$symbol)";
	
	$pos = strpos($txt, $symbols);
	if ($pos !== false) {
		if ($DEBUG) print "<br>YESm pos=$pos"; 
	  $txt=trim(substr($txt,0,$pos));
	} else {
		if ($DEBUG)
			print "<br>NO";
	}
	
	if ($DEBUG) print "<br> returning '$txt'";
	return $txt;
} // limit_to_symbol_if_found
	

?>