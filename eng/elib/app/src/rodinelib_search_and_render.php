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
###############################################################
 
 
$DEBUG=$_REQUEST['DEBUG']; if (!$DEBUG) $DEBUG=0;
$QUERY=str_replace(' ','%20',$_REQUEST['query']);

$WIDGETS=$ELIB_WIDGET_TO_USE;
$M			=$ELIB_WIDGET_S_M; 
$USERID	=$ELIB_USERID; 

if ($DEBUG)
{
	print "<br>USING Param QUERY: $QUERY";
	print "<br>USING Param WIDGETS: $WIDGETS";
	print "<br>USING Param ELIB_WIDGET_S_M: $ELIB_WIDGET_S_M";
	print "<br>USING Param ELIB_USERID: $ELIB_USERID";
	print "<br>";
}


$base_url="$WEBROOT$RODINROOT/$RODINSEGMENT/app/webs/search.php";
$url = "$base_url?query=$QUERY&widgets=$WIDGETS&userid=$USERID&m=$M&DEBUG=$DEBUG";

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
	$abstract						= separateWordsInSpans( $toDetails->abstract );
	$score							= $toDetails->score; 							
	$authors						= separateWordsInSpans($toDetails->authors);
	$date								= separateWordsInSpans($toDetails->date);
	$url2								= $toDetails->url;
	$properties					= ($toDetails->properties);

		
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
	$CLICKTOSEARCHMLT="title=\"Click to search with this document\" onclick=\"mlt_wdoc('wd$resultNr')\"";
	
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
						<img src='$RODINIMAGESDIR/docsemfilter16x16.png' /> Get similar
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
		<h1 class='maintitle'> RESULTS for your SEARCH </h1>
	</div id='rodin_header'>
	<div id='rodin_content'>
		<div id='rodin_left_column'>
				<script type="text/javascript">show_rodin_thesearch_results('$QUERY', $DEBUG );</script>
				&nbsp;
		</div>
		<div id='rodin_main_column'>   
			<table cellspacing='5'>
				$WRNOTIFICATION_SECTION
				$RESULTS_HTML
			</table>
		  </div>
		<div id='rodin_right_column'> </div>
	</div>
</div>
EOO;


	print $OUTPUT;

###############################################################################################
###############################################################################################
###############################################################################################


	function separateWordsInSpans($text) 
	{
		//print "<br><b>enter.separateWordsInSpans</b>($text) ";
		//Filter bad chars as nl or tabs
		$pattern = '/[\n\t\b]/';
		$text = (trim(preg_replace($pattern, ' ', $text)));
		$language_specialchars='ßÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ';
		
		$pattern = '/[A-Za-z0-9'.$language_specialchars.'\-_]+/u';
		$msdelay = 300; //only for onmouseover
		$replace = '<span class="result-word" '
		." onmousedown=\"omd(this,event); \" "
		." onmouseover=\"omo(this,event,$msdelay); \" "
		." onmouseup=\"omu(this,event); \" "
		." onmouseout=\"mut(this,event); \" "
		// ." onclick=\"alert('visualize docs with $0')\" "
		// ." onrclick=\"prr('$0')\" "
		.'>$0</span>';

		$result= preg_replace($pattern, $replace, $text);
		
		//print "<br><b>exit.separateWordsInSpans</b>($text):<br>(((".htmlentities($result).')))';
		return $result;
	}	


?>