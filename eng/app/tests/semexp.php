<?php
require_once("../u/arcUtilities.php");
require_once("../u/FRIutilities.php");
require_once("../u/RodinResult/RodinResultManager.php");


?>
<html>
	<head>
		<title>USECASE III semantic expansion lab</title>
		<link rel="stylesheet" type="text/css" href="../css/rodin.css.php?" />
		<script type='text/javascript' src='../u/RODINutilities.js.php?skin=<?php print $RODINSKIN;?>'></script>
	</head>	
	<body bgcolor='<?php print $COLOR_PAGE_BACKGROUND;?>'

<?php

$sid_example='20130308.191559.379.305';
$datasource_swissbib="/rodin/$RODINSEGMENT/app/w/RDW_swissbib.rodin";

$sid=$_GET['sid'];
if (!$sid) $sid=$sid_example;

$datasource=$_GET['datasource'];
if (!$datasource) $datasource=$datasource_swissbib;

$listwr=$_GET['listwr']=='on';
$checked_listwr=$listwr?' checked ':'';

$list3pls=$_GET['list3pls']=='on';
$checked_list3pls=$list3pls?' checked ':'';

//Automatically show triple page pointers
//$list3page=$_GET['list3page']=='on';
//$checked_list3page=$list3page?' checked ':'';
$list3page=$list3pls;
$checked_list3page=$list3page?' checked ':'';

//If $list3page then also $want_rdfexpand
$want_rdfexpand=$list3page;


if ($sid<>'')
{
	$fromResult = 0;
	if (!$USER_ID) $USER_ID=2; // set it for shorten up
	$SEG=$RODINSEGMENT;
	$USER=$USER_ID;
	
	//Recall results from SOLR using sid
	$allResults = RodinResultManager::getRodinResultsForASearch($sid,$datasource);
	$resultCount = count($allResults);

	$CONTENT2="$resultCount Results retrieved";
	
	//var_dump($allResults);
	// Both a maximum size and a maximum number of results are set
	$resultMaxSetSize = $resultCount;
	
	$uptoResult = min($resultCount, $fromResult + $resultMaxSetSize);
	
	$i = $fromResult;
	while ($i < $uptoResult) {
		$result = $allResults[$i];
		$resultCounter = $i + 1;
	
		$resultIdentifier = 'aggregatedResult-' . $resultCounter . ($suffix != '' ? '_' . $suffix : '');
	
		$singleResult = array();
	
		$singleResult['count'] = $resultCounter;
		//$CONTENT2.=  "<br>tokenContent: ".$result->toInWidgetHtml('all');
		//Ausgabe extra:
	
		$CONTENT2.="<hr>";
		$CONTENT2.="<b>Title:</b> " . $result->getTitle();
		$CONTENT2.="<br><b>ISBN:</b> " . $result->getProperty('isbn');
		if($result->getAuthors())
			$CONTENT2 .= "<br><b>Authors:</b> " . $result->getAuthors();
		$CONTENT2 .= '<br><b>Date:</b> ' . $result->getDate();
		$CONTENT2.=  "<br><b>url:</b> ".$result->getUrlPage();
		
		foreach ($result->getValidProperties() as $property) {
			if ($result->getProperty($property))
			$CONTENT2.= "<br><b>$property:</b>" . $result->getProperty($property);
		}
	
		//print "<br>SEG: $SEG, USER: $USER ";
		$search_term = collect_queries_tag($SEG,$USER_ID,$sid);
		//print "<br>collect_queries_tag($SEG,$USER,$sid) = $search_term";
		$store=null;
		if ($list3pls)
			$store = $result->rdfize($sid,$datasource,$search_term,$USER_ID);
	
		if($store && $want_rdfexpand)
		{
			$ok=$result->rdfLODexpand($sid,$datasource,$search_term,$USER_ID);
		}
		//$singleResult['minContent'] = ($result->toInWidgetHtml('min'));
		//$singleResult['tokenContent'] = ($result->toInWidgetHtml('token'));
		//$singleResult['allContent'] = ($result->toInWidgetHtml('all'));
	
		// Check the size of the response if this result was added
		//$allResults[] = $singleResult;
		$i++;
	}

	if ($result)
	{
	if (!$listwr)
		$CONTENT2='';

	if ($list3pls)
		$CONTENT3 = get_triples_as_html_table($result->RDFenhancement,$list3page,'','TRIPLES for WIDGET RESULTS:','tripletable');
	
	}
	else fontprint( "Sorry: No data for this sid" , 'red' );
} // $sid


$PAGEWIDTH="400px";


print<<<EOP
	<div id='div1' style="width:100%;height:400px">
	<h2>RODIN USECASE III semantic expansion LAB</h2>
	<p>
		<a href='$RDFSEMEXP_STOREEXPLORER' target='_blank' title='Click to open LOCAL STORE SPARQL Explorer in new tab'> LOCAL STORE SPARQL Explorer </a>
	</p>
	<form name='fsid' action=''>
	<table style="width:100%">
		<tr>
		<td colspan="2">
		</tr>
		<tr>
			<td colspan="2">
				<input type='button' name='go' value='press to recalculate' style="width:100%" onclick="fsid.submit()">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				SID:<input type='text' name='sid' value='$sid' title='Enter a SID like '$sid_example' choose some options and recalculate'>&nbsp;
				DS:<input type='text' name='datasource' 
					style='text-align:center'
					value='$datasource' size="40" title='Enter a Datasource like '$datasource_swissbib' choose some options and recalculate'>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			List widget results:<input type='checkbox' name='listwr' $checked_listwr>&nbsp;&nbsp;&nbsp;
			List triples:<input type='checkbox' name='list3pls' $checked_list3pls>&nbsp;&nbsp;&nbsp;
			Search was: '<label><b>$search_term</b></label>'
			</tr>
			</table>
	</form>	
	</div>
	
	<div id='div2' style="background-color:white;border-color:black;border-width:1px;border-style:solid;width:100%;">
		$CONTENT2
	</div>
	
	
	<div id='div3' class='tripletable'>
		$CONTENT3
	</div>
	
	
	<div id='div4' class='triplepagediv'>
		$CONTENT4
	</div>
	
EOP;


?>
	</body>
</html>
