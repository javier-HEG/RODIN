<?php

/**
 * FILE: rodinelib_result_filter_and_rerender
 * AUTHOR: Fabio Ricci, fabio.ricci@semweb.ch, Tel. +41-76-5821961
 * ON BEHALF OF: HEG - Haute Ecole de Gestion, Geneva
 * DATE: August 2013
 * 
 * THis script searches in RODIN, and shows all results similar to the one with rid e-lib-like
 * only content for rodin_main_column
 * 
 * two behaviours:
 * 
 * 1) mlt - if rid and sid are set
 * 2) sfacets / efacets refilter and rerender if no rid but one of (sfacets,efacets) is set
 * 
 */

$filenamex="app/elibroot.php";
###############################################################
$max=10; for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
{ if (file_exists("$updir$filenamex")) 
	{	require_once("$updir$filenamex"); break;}	}

###############################################################
$filenamex="app/u/arcUtilities.php";
#######################################
$max=10; for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
{ if (file_exists("$updir$filenamex")) 
	{	require_once("$updir$filenamex"); break;}	}
###############################################################
$filename="app/u/LanguageDetection.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}
###############################################################

 
$DEBUG=$_REQUEST['DEBUG']; if (!$DEBUG) $DEBUG=0;
$NOTIFY=$_REQUEST['notify'];
$NOTIFYTXT=$_REQUEST['txt'];
$sid= $_REQUEST['sid'];
$rid= $_REQUEST['rid'];

$sfacets = $_REQUEST['sfacets'];
$wfacets = $_REQUEST['wfacets'];
$efacets = $_REQUEST['efacets'];
$SEMFACETTERMS	= $sfacets? explode('|',$sfacets): array(); 
$WDGFACETTERMS	= $wfacets? explode('|',$wfacets): array(); 
$ELIBFACETTERMS	= $efacets? explode('|',$efacets): array(); 


$MLT=($rid<>null);
$FILTER= ! $MLT;


//QUERY and QUERYTERMS concern both previousquery here:
$QUERY=str_replace(' ','%20',$_REQUEST['previousquery']);
$QUERYTERMS=	$QUERY
							?explode(' ', $_REQUEST['previousquery'])
							:null;
$searchtermlang = $jsonResultsDecoded{'searchtermlang'};
$searchtermlang	=$searchtermlang? $searchtermlang: detectLanguage($_REQUEST['query']);

if(!$languages) $languages = array('en','fr','de','it','es');
if (in_array($searchtermlang, $languages))
	$stopwords =get_stopwords_from_db($searchtermlang);
else 
	$stopwords =get_stopwords_from_db();

if ($DEBUG) {
	print "<br>previousquery: ".$QUERY;
	print "<br>Detection languages: ".$languages;
	print "<br>Detected language: ".$searchtermlang;
	//print "<br>stopwords<br>: "; var_dump($stopwords);
	print "<br>QUERYTERMS0 ($QUERY	): <br>"; var_dump($QUERYTERMS);
	print "<br>stopwords: " .count($stopwords);
}




$WIDGETS=$ELIB_WIDGETS_TO_USE;
$M			=$ELIB_WIDGET_S_M; 
$USERID	=$ELIB_USERID; 

if ($DEBUG)
{
	if ($MLT)
	{
		print "<br>USING Param sid: $sid";
		print "<br>USING Param rid: $rid";
	}
	if ($FILTER)
	{
		print "<br>USING Param previousquery: $QUERY";
		print "<br>USING Param sfacets: $sfacets";
		print "<br>USING Param wfacets: $wfacets";
		print "<br>USING Param efacets: $efacets";
	}
	print "<br>";
}

if ($MLT)
{
	$base_url="$WEBROOT$RODINROOT/$RODINSEGMENT/app/webs/mlt_search.php";
	$url  = "$base_url?rid=$rid&sid=$sid&userid=$USERID&m=$M&DEBUG=$DEBUG";
	$urlj = "$base_url?rid=$rid&sid=$sid&userid=$USERID&m=$M";
}
else if ($FILTER)
{
	$strSEMT = $strWDGS = array();
	//Repeat the data retrieval using $WDGFACETTERMS and $SEMFACETTERMS and using elib facets:
	if ($WDGFACETTERMS && count($WDGFACETTERMS) & is_array($WDGFACETTERMS))
	foreach($WDGFACETTERMS as $WDGT)
		$strWDGS[]=strstr($WDGT,' ')?'"'.$WDGT.'"':$WDGT;
	if ($SEMFACETTERMS && count($SEMFACETTERMS) & is_array($SEMFACETTERMS))
	foreach($SEMFACETTERMS as $SEMT)
		$strSEMT[]=strstr($SEMT,' ')?'"'.$SEMT.'"':$SEMT;
			
	$prefacets=trim(implode(' AND ',$strWDGS).' '.trim(implode(' AND ',$strSEMT)));
	if ($prefacets)
		$query=trim(str_replace(' ','%20', $QUERY.' AND '.$prefacets));
	else {
		$query=trim(str_replace(' ','%20', $QUERY));
	}

	$efacets_encoded = $efacets?urlencode($efacets):'';
	$base_url="$WEBROOT$RODINROOT/$RODINSEGMENT/app/webs/search.php";
	$url = "$base_url?query=$query&efacets=$efacets_encoded&userid=$USERID&m=$M&widgets=$WIDGETS&DEBUG=$DEBUG";
	$urlj = "$base_url?query=$query&efacets=$efacets_encoded&userid=$USERID&m=$M&widgets=$WIDGETS";
}
// Call $url

if ($DEBUG) print "<br>Calling <a href='$url' target='blank'>$urlj</a>";
$jsonResultsDecoded = json_decode(file_get_contents($urlj));

$sid 						= $jsonResultsDecoded->sid;
$resultCount		= $jsonResultsDecoded->resultCOunt;
$upto						= $jsonResultsDecoded->upto;
$jsonAllResults	= $jsonResultsDecoded->results;
$errortxt				= $jsonResultsDecoded->error;

$DISPLAY_ABSTRACT=true;
//$ONMOUSEOVER_ELIBRESULT ="onmouseover=\"elib_tooltip_hide();\"";

$TITLExEACHWORD = "Highlight actions"
								. "{$TTPNEWLINE}{$TTPNEWLINE}Higlight similar words by simply hovering on a word; display available actions on a word by "
								. "shift-hovering or clicking on it "
								. "or select a sequence of words by dragging with the left mouse button over some text "
								. "starting from within a word and ending within a word."
								;


		
if ($DEBUG)
{
	print "<br>sid: $sid"
				."<br>resultCount: $resultCount"
				."<br>upto: $upto"
				."<br>jsonAllResults: ".count($jsonAllResults)
				."<br>errortxt: $errortxt"
				."";
	if(1) var_dump($jsonResultsDecoded);
}

if ($NOTIFY) 
		$visible='visible';


$TITLExEACHSTOPWORD='';


if (is_array($jsonAllResults) && count($jsonAllResults))
foreach ($jsonAllResults as $jsonResult)
{
	if ($DEBUG && 0) {
			print "<hr>\n\nRESULT: <br>\n\n";
			var_dump($jsonResult);
	}
	$resultNr 					= $jsonResult->count;
	$resultUrl 					= $jsonResult->url;
	$resultIdentifier 	= $jsonResult->resultIdentifier;
	$toDetails 					= json_decode($jsonResult->toDetails);
	
	$type								= $toDetails->type;
	$title							= separateWordsInSpans( $toDetails->title );

	if ($type=='BASIC' && strstr($title,'pathcontext-to'))
	{}
	else // Print result 
	{
	 
	/**
	 separateWordsInSpans() using:
   global $QUERYTERMS;
	 global $stopwords;
	 global $TITLExEACHSTOPWORD;
 */
		
		$displayed_results++;
		
		$abstract						= separateWordsInSpans( $toDetails->abstract );
		$score							= $toDetails->score; 							
		$authors						= surround_if_contained($ELIBFACETTERMS,separateWordsInSpans($toDetails->authors),'e');//blue
		$date								= surround_if_contained($ELIBFACETTERMS,separateWordsInSpans($toDetails->date),'e');//blue;
		$url2								= $toDetails->url;
		$properties					= ($toDetails->properties);
		$solr_result_id			= $jsonResult->rid;
		
			
		if ($score) 
		{
			$HTMLSCORE="Score: $score";
		}
		
		$typelower=strtolower($type);
		$RESULTNUMBER	= $HTMLSCORE? "$resultNr / $HTMLSCORE": $resultNr;
		$IMGTYPESRC			= $DOCROOT.$RODINROOT.'/'.$RODINSEGMENT.'/elib/app/img/'.$typelower.'.png';
	
		if ($DEBUG)
		{
			print "<br>resultNr: $resultNr";
			print "<br>solr_result_id: $solr_result_id";
			print "<br>resultUrl: $resultUrl";
			print "<br>resultIdentifier: $resultIdentifier";
			//print "<br>DUMP toDetails: "; var_dump($toDetails); print "<br>";
			print "<br><b>type</b>: $type";
			print "<br><b>title</b>: $title";
			print "<br><b>abstract</b>: $abstract";
			print "<br><b>score</b>: $score";
			print "<br><b>authors</b>: $authors";
			print "<br><b>date</b>: $date";
			print "<br><b>url2</b>: $url2";
			print "<br><b>properties</b>: $properties";
		}
		
		$abstractclass=$DISPLAY_ABSTRACT?'elibresvisibleabstract':'elibreshiddenabstract';
		$CLICKTOSEERECORD="title=\"Click to see original information in a new tab\" onclick=\"window.open('$resultUrl')\"";
		$CLICKTOSEARCHMLT=" title=\"Click to search with this document\" onclick=\"show_rodin_mlt('$solr_result_id','$sid','Documents filtered with abstract of:<br><b>&#171;".urlencode($toDetails->title)."&#187;</b>','$QUERY',$DEBUG);\" ";
		
		$jsMLT = "widget_morelikethis('$id','$sid','$solr_mlt');";
		
		
		$ONCLICKABSTRACT_DEACKT = " title=\"Click to display/hide abstract section below\" 
			onclick=\"$('#a$resultNr').toggleClass('elibresvisibleabstract elibreshiddenabstract');\" ";
		######################################
		#Construct an elib like result display
		######################################
		$RESULTS_HTML.=<<<EOR
<tr id='wd$resultNr' class='rresult'>
	<td class='resthumbnail' width='100'>
		<table>
			<tr>
				<td class='resultnumber' align='right' valign='top'>$resultNr</td>
				<td class='$typelower wdocaction' $CLICKTOSEERECORD>
					$typelower <label class='score'>$score</label>
				</td>
			</tr>
			<tr>
				<td/>
				<td class='wdocaction' $CLICKTOSEARCHMLT>
					<img src='$RODINIMAGESURL/docsemfilter16x16.png' /> Get similar
				</td>
			</tr>
		</table>
	</td><!--resthumbnail-->
	<td class='elibresult' title='$TITLExEACHWORD'>
		<table>
			<tr><td class='elibrestitle' $ONCLICKABSTRACT> $title </td></tr>
			<tr><td class='elibresauth'> $authors </td></tr>
			<tr><td class='elibresdate'> $date </td></tr>
			<tr><td class='$abstractclass' id='a$resultNr'> $abstract </td></tr>
			
		</table>
	</td><!--elibresult-->
</tr>
<tr class='whitespace'><td colspan="2" class='whitespace'></td></tr>
EOR;
	}	// Print result 
} // foreach $jsonResult


if ($displayed_results==0)
{
	$NOTIFYTXT = "Sorry, no ".strtolower(substr($NOTIFYTXT,0,1)).substr($NOTIFYTXT,1);	
}

if ($FILTER && $displayed_results>0)
{
	$NOTIFYTXT='';
}

if ($NOTIFYTXT)
$WARNOTIFICATION_SECTION=<<<EON
<table class='wnotification $visible'>
	<tr class='wnotification'>
		<td colspan='3' class='wnotification'>
			<a id='wnotification' ><label id='lnotification'>$NOTIFYTXT</label></a>
		</td>
	</tr>
	<tr class='wnotification whitetr'>
		<td class='whitetd' colspan='3'>
		</td>
	</tr>
</table>
<hr style="margin-bottom:0;margin-top:5px;height:0">
EON;

$OUTPUT=<<<EOO
			<hr class='elibfacetdeco_0'>
			$WARNOTIFICATION_SECTION
			<table cellspacing='5'>
				$RESULTS_HTML
			</table>
EOO;


	print $OUTPUT;

###############################################################################################
###############################################################################################
###############################################################################################



?>