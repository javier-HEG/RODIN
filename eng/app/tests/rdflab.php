<?php
require_once("../u/arcUtilities.php");
require_once("../u/FRIutilities.php");
require_once("../u/RodinResult/RodinResultManager.php");

$sid=$_GET['sid'];
$USER_ID=$_GET['user_id'];
$USER=$USER_ID;
$USER_NAME=$_GET['username'];
$SEG=$RODINSEGMENT;
$_SESSION['user_id']=$USER_ID;
$_SESSION['username']=$USER_NAME;
	
	
if ($sid<>'')
{
	$search_term = collect_queries_tag($SEG,$USER_ID,$sid);
}

$TITLEPAGE="'$search_term' RDFLAB";

?>
<html>
	<head>
		<title><?php print $TITLEPAGE; ?></title>
		<link rel="stylesheet" type="text/css" href="../css/rodin.css.php?" />
		<script type='text/javascript' src='../u/RODINutilities.js.php?skin=<?php print $RODINSKIN;?>'></script>
	</head>	
	<body bgcolor='<?php print $COLOR_PAGE_BACKGROUND;?>' >

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


############
//print "unlinking $RODIN_PROFILING_PATH";
unlink($RODIN_PROFILING_PATH);
############



if ($sid<>'')
{
	$fromResult = 0;
	
	//Recall results from SOLR using sid
	$allResults = RodinResultManager::getRodinResultsForASearch($sid,$datasource);
	$resultCount = count($allResults);

	$CONTENT2="$resultCount Widget results found for sid $sid";
	
	//var_dump($allResults);
	// Both a maximum size and a maximum number of results are set
	$resultMaxSetSize = $resultCount;
	
	$uptoResult = min($resultCount, $fromResult + $resultMaxSetSize);
	
	Logger::logAction(27, array('from'=>'rdflab','msg'=>"Start using $resultCount results on search term '$search_term'"));
	
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
		//print "<br>collect_queries_tag($SEG,$USER,$sid) = $search_term";
		$store=null;
		
		
		Logger::logAction(27, array('from'=>'rdflab','msg'=>"Start RDF on $resultCounter result"));
		
		if ($list3pls)
			$store = $result->rdfize($sid,$datasource,$search_term,$USER_ID);
	
		if($store && $want_rdfexpand)
		{
			$ok=$result->rdfLODfetchDocumentsOnSubjects($sid,$datasource,$search_term,$USER_ID);
		}
		Logger::logAction(27, array('from'=>'rdflab','msg'=>"Exit RDF on $resultCounter result"));

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
		$CONTENT3 = get_triples_as_html_table($result->RDFenhancement,$list3page,'',' for WIDGET RESULTS:','tripletable');
	
	}
	else fontprint( "Sorry: No data for this sid" , 'red' );
	
	Logger::logAction(27, array('from'=>'rdflab','msg'=>"Exit using $resultCount results on search term '$search_term'"));
} // $sid


$PAGEWIDTH="400px";
$SRCLINK="$SRCLINKBASE/select_src.php?nl=0&u=$USER_ID&showuser=$USER_ID";
##########################################
# The following is filled by the programs:
# $RDFLOG
##########################################

print<<<EOP
	<div id='div1' style="width:810px;height:400px;scroll:auto">
	<h2>$TITLEPAGE</h2>
	<p>
		<input type='button' title='Click to open LOCAL STORE SPARQL Explorer in new tab' value='OPEN LOCAL STORE SPARQL Explorer' onclick="window.open('$RDFSEMEXP_STOREEXPLORER')">
		&nbsp;&nbsp;&nbsp;
		<input type='button' title='Click to toggle RDF log display' value='show/hide RDF Logs' onclick="var l=document.getElementById('divLOGGING');toggle_visibility(l)">
		&nbsp;&nbsp;&nbsp;
		<input type='button' title='Click to see profiling execution times for optimization in new tab' value='Open profiling'onclick="window.open('$RODIN_PROFILING_LINK','_blank')">
		&nbsp;&nbsp;&nbsp;
		<input type='button' title='Click to open SRC-Management in new tab' value='Open SRC management'onclick="window.open('$SRCLINK','_blank')">
	</p>
		<div id='divLOGGING'>
			$RDFLOG
		</div>
	<div id='formdiv'>
		<form name='fsid' action=''>
		<input type='hidden' name='user_id' value='$USER_ID'>
		<input type='hidden' name='username' value='$USER_NAME'>
		<table style="width:100%">
			<tr>
				<td colspan="2">
					<input type='button' name='go' value='press to RDFize' style="width:800px" onclick="fsid.submit()">
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
				RDFize & display triples:<input type='checkbox' name='list3pls' $checked_list3pls>&nbsp;&nbsp;&nbsp;
				Search was: '<label><b>$search_term</b></label>'
				</tr>
				</table>
		</form>
	</div>
	
	
	<div id='div2' class='rdf_widgetinfo'>
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
