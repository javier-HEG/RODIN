<?php

/**
 * FILE: rodinelib_search_and_render
 * AUTHOR: Fabio Ricci, fabio.ricci@semweb.ch, Tel. +41-76-5821961
 * ON BEHALF OF: HEG - Haute Ecole de Gestion, Geneva
 * DATE: August 2013
 * 
 * THis script searches in RODIN, and shows the results e-lib-like
 * adding RODIN's semantic facetts
 * 
 * It returns an HTML content which should be rendered in a DIV inside another HTML page
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
$QUERY=str_replace(' ','%20',$_REQUEST['query']);
$QUERYTERMS=explode(' ', $_REQUEST['query']);
$searchtermlang = $jsonResultsDecoded{'searchtermlang'};
$searchtermlang	=$searchtermlang? $searchtermlang: detectLanguage($_REQUEST['query']);

if(!$languages) $languages = array('en','fr','de','it','es');

if ($DEBUG && !$languages) {fontprint("System error: \$languages not known",'red');exit;}

if (in_array($searchtermlang, $languages))
	$stopwords =get_stopwords_from_db($searchtermlang);
else 
	$stopwords =get_stopwords_from_db();

if ($DEBUG) {
	print "<br>Detection languages: ".$languages;
	print "<br>Detected language: ".$searchtermlang;
	//print "<br>stopwords<br>: "; var_dump($stopwords);
	print "<br>stopwords: " .count($stopwords);
}




$THESOURCES	=$ELIB_THESAURI_TO_USE;
$WIDGETS=$ELIB_WIDGETS_TO_USE;
$M			=$ELIB_WIDGET_S_M; 
$USERID	=$ELIB_USERID; 
$THESAURINAMES = get_used_thesauri_sources($USERID);



if ($DEBUG)
{
	print "<br>USING Param QUERY: $QUERY";
	print "<br>USING Param WIDGETS: $WIDGETS";
	print "<br>USING Param ELIB_WIDGET_S_M: $ELIB_WIDGET_S_M";
	print "<br>USING Param ELIB_USERID: $ELIB_USERID";
	print "<br>";
}

$numwidgets_in_this_search = substr_count($ELIB_WIDGETS_TO_USE,',') + 1;
$wm=$numwidgets_in_this_search * $M;

$base_url="$WEBROOT$RODINROOT/$RODINSEGMENT/app/webs/search.php";
$url = "$base_url?query=$QUERY&widgets=$WIDGETS&userid=$USERID&m=$M&wm=$wm&DEBUG=$DEBUG";

// Call $url

if ($DEBUG) print "<a href='$url' target='blank'>$url</a>";
$jsonResultsDecoded = json_decode(file_get_contents($url));

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


$TITLExEACHSTOPWORD = ""
								;

		
if ($DEBUG)
{
	print "<br>sid: $sid"
				."<br>resultCount: $resultCount"
				."<br>upto: $upto"
				."<br>jsonAllResults: ".count($jsonAllResults)
				."<br>errortxt: $errortxt"
				;
}
if (is_array($jsonAllResults) && count($jsonAllResults))
foreach ($jsonAllResults as $jsonResult)
{
	if ($DEBUG && 0) 
	{
			print "<hr>\n\nRESULT: <br>\n\n";
			var_dump($jsonResult);
	}
	$resultNr 					= $jsonResult->count;
	$resultUrl 					= $jsonResult->url;
	$resultIdentifier 	= $jsonResult->resultIdentifier;
	$toDetails 					= json_decode($jsonResult->toDetails);
	
	$type								= $toDetails->type;
	$title							= separateWordsInSpans( $toDetails->title );
	$abstract						= separateWordsInSpans( $toDetails->abstract );
	$description				= separateWordsInSpans( $toDetails->description );
	$EVTLBR 						= $description?'<br>':'';
	$score							= $toDetails->score; 							
	$authors						= separateWordsInSpans($toDetails->authors);
	$date								= separateWordsInSpans($toDetails->date);
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
		print "<hr><br>resultNr: $resultNr";
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
	
	$CLICKTOSEARCHMLT=" title=\"Click to search with this document\" onclick=\"show_rodin_mlt('$solr_result_id','$sid','Documents similar to:<br><b>&#171;".urlencode($toDetails->title)."&#187;</b>','$QUERY',$DEBUG);\" ";
	
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
						<img src='$RODINIMAGESURL/docsemfilter25x25.png' /> Get similar
					</td>
				</tr>
			</table>
		</td><!--resthumbnail-->
		<td class='elibresult' title='$TITLExEACHWORD'>
			<table>
				<tr><td class='elibrestitle' $ONCLICKABSTRACT> $title </td></tr>
				<tr><td class='elibresauth'> $authors </td></tr>
				<tr><td class='elibresdate'> $date </td></tr>
				<tr><td class='$abstractclass' id='a$resultNr'> $description$EVTLBR$abstract </td></tr>
				
			</table>
		</td><!--elibresult-->
	</tr>
	<tr class='whitespace'><td colspan="2" class='whitespace'></td></tr>
EOR;
	
} // foreach $jsonResult


$WRNOTIFICATION_SECTION=<<<EON
<tr class='wnotification'>
	<td colspan='3' class='wnotification'>
		<a id='anotification' ><label id='lnotification'></label></a>
	</td>
</tr>
<tr class='wnotification whitetr'>
	<td class='whitetd' colspan='3'>
	</td>
</tr>
EON;


$OUTPUT=<<<EOO
<div id='rendered_rodinelib'>
	<div id='rodin_top_header'></div>
	<div id='rodin_header'>
		<input type='hidden' id='sid' name='sid' value='$sid'>
		<h1 class='maintitle'> RESULTS for your SEARCH </h1>
	</div id='rodin_header'>
	<div id='rodin_content'>
		<table cellpadding="0" cellspacing="0">
			<tr>
			<td>
				<div id='rodin_left_column'>
					<script type="text/javascript">show_rodin_thesearch_results('$QUERY', $DEBUG );</script>
						Collecting semantical facets from thesauri:<br>$THESAURINAMES ...
				</div>
			</td>
			<td>
				<div id='rodin_main_column'>
					<table cellspacing='5'>
						$WRNOTIFICATION_SECTION
						<hr class='elibfacetdeco_0'>
						$RESULTS_HTML
					</table>
			  </div>
			 </td>
			 <td>
					<div id='rodin_right_column'> 
							<script type="text/javascript">show_rodinelib_elib_facets('$QUERY', $DEBUG );</script>
							Collecting e-lib.ch facets ...
					</div>
				</td>
				</tr>
			</table>
	</div>
</div>
EOO;


	print $OUTPUT;



function get_used_thesauri_sources($USERID)
{
	$SRCS = get_active_THESAURI_sources( $USERID );
	$SRCrecords = $SRCS['records'];
	if(is_array($SRCrecords) && count($SRCrecords))
	foreach($SRCrecords as $SRC)
	{
		$i++;
		$sourcename.=$sourcename?', ':'';
		$sourcename.= lg('lbl'.$SRC['Name']);
	}
	
	return $sourcename;
}

?>