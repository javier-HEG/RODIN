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
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script type='text/javascript' src='../u/RODINutilities.js.php?skin=<?php print $RODINSKIN;?>'></script>
	</head>	
	<body bgcolor='<?php print $COLOR_PAGE_BACKGROUND;?>' >

<?php

#######################################
#Get switches:
#######################################
#
$sid=$_GET['sid'];
if (!$sid) $sid=$sid_example;

$listwr=$_GET['listwr']=='on';
$checked_listwr=$listwr?' checked ':'';

$list3pls=$_GET['list3pls']=='on';
$checked_list3pls=$list3pls?' checked ':'';

$viz3pls=$_GET['viz3pls']=='on';
$checked_viz3pls=$viz3pls?' checked ':'';

$viz3search=$_GET['viz3search']=='on';
$checked_viz3search=$viz3search?' checked ':'';
#
#########################################
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
	
	//Recall results from SOLR using sid but no datasource! (get for every datasource)
	$allResults = RodinResultManager::getRodinResultsForASearch($sid,$datasource='');
	$resultCount = count($allResults);

	$CONTENT2="$resultCount Widget results found for sid $sid";
	
	//var_dump($allResults);
	// Both a maximum size and a maximum number of results are set
	$resultMaxSetSize = $resultCount;
	
	$uptoResult = min($resultCount, $fromResult + $resultMaxSetSize);
	
	Logger::logAction(27, array('from'=>'rdflab','msg'=>"Start using $resultCount results on search term '$search_term'"));
	
	$searchres_timestamp = timestamp_fortripleannotation();
	
	$i = $fromResult;
	$added_triples=0;
	while ($i < $uptoResult) {
		$result = $allResults[$i];
		//print "<hr>Result: ";var_dump($result);
		$datasource = $result->getProperty('datasource');
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
		{ 
			list($store,$count_triples_added) = $result->rdfize($sid,$datasource,$search_term,$USER_ID,$searchres_timestamp);
			$added_triples+=$count_triples_added;
			$RDFLOG.="<br>rdfize: $count_triples_added (of $added_triples) triples added";
		}
		
		if($store && $want_rdfexpand)
		{
			list($ok,$count_triples_added)=$result->rdfLODfetchDocumentsOnSubjects($sid,$datasource,$search_term,$USER_ID);
			$ok=$result->rerank_rdf_documents_related_to_search($sid,$datasource,$search_term,$USER_ID);
			
			$added_triples+=$count_triples_added;
			$RDFLOG.="<br>LODfetch: $count_triples_added (of $added_triples) triples added";
		}
		Logger::logAction(27, array('from'=>'rdflab','msg'=>"Exit RDF on $resultCounter result"));

		//$singleResult['minContent'] = ($result->toInWidgetHtml('min'));
		//$singleResult['tokenContent'] = ($result->toInWidgetHtml('token'));
		//$singleResult['allContent'] = ($result->toInWidgetHtml('all'));
	
		// Check the size of the response if this result was added
		//$allResults[] = $singleResult;
		$i++;
	 } // while

	 if ($result)
	 {
		if (!$listwr)
			$CONTENT2='';
	
		if ($list3pls)
		{
			$CONTENT3 = get_triples_as_html_table($result->RDFenhancement,$added_triples,$list3page,'',' for WIDGET RESULTS:','tripletable');
		}
		
		if ($viz3search)
		{
		  $png = $result->RDFenhancement->get_jpeg_display_store_with_graphviz($search_term);	
		}
		else if ($viz3pls)
		{
		  $png = $result->RDFenhancement->get_jpeg_display_store_with_graphviz();	
		}
	}
	else fontprint( "Sorry: No data for this sid" , 'red' );
	
	Logger::logAction(27, array('from'=>'rdflab','msg'=>"Exit using $resultCount results on search term '$search_term'"));
} // $sid


if ($result->RDFenhancement)
{
	//Construct small limit resumee on used limits
	$C=get_class($result->RDFenhancement);
	
	$TOLERATED_SRC_SOLR_DATA_AGE_SEC=$C::$TOLERATED_SRC_SOLR_DATA_AGE_SEC;
	$TOLERATED_SRC_RDF_DATA_AGE_SEC	=$C::$TOLERATED_SRC_RDF_DATA_AGE_SEC;
	$THRESHOLD_DATASOURCE_MIN_SUBJECTS = $C::$THRESHOLD_DATASOURCE_MIN_SUBJECTS;
	$MAX_SRC_SUBJECT_EXPANSION			=$C::$MAX_SRC_SUBJECT_EXPANSION;
	$MAX_LOD_SUBJECT_DOCFETCH				=$C::$MAX_LOD_SUBJECT_DOCFETCH;
		
	$TDR    		 ="align=right";
	$LIMITSSTAT .= "<table>";
	$LIMITSSTAT .= "<tr><th align=left colspan=3>LIMITS used during RDFization:</th></tr>";
	$LIMITSSTAT .= "<tr><td title='(Re)Use SOLR CACHE with a maximum age of $TOLERATED_SRC_SOLR_DATA_AGE_SEC secs '>Limit SOLR SRC CACHE AGE TO: </td><td $TDR>$TOLERATED_SRC_SOLR_DATA_AGE_SEC</td><td> secs</td></tr>";
	$LIMITSSTAT .= "<tr><td title='(Re)Use RDF STORE HOLD TRIPLES with a maximum age of $TOLERATED_SRC_RDF_DATA_AGE_SEC secs '>Limit RDF STORE TRIPLE AGE TO: </td><td $TDR>$TOLERATED_SRC_RDF_DATA_AGE_SEC</td><td> secs</td></tr>";
	$LIMITSSTAT .= "<tr><td title='Threshold to trigger title driven subjects computation during analysis of a single RODIN widget result document'>TRHESHOLD DECISION TITLE SUBJECTS: </td><td $TDR>$MAX_SRC_SUBJECT_EXPANSION</td><td> subjects</td></tr>";
	$LIMITSSTAT .= "<tr><td title='LIMIT expansion loop (in subject expansion) to maximum $MAX_SRC_SUBJECT_EXPANSION of the gathered RDF STORE subjects'>Limit RDF SUBJECT EXPANSION LOOP TO: </td><td $TDR>$MAX_SRC_SUBJECT_EXPANSION</td><td> subjects</td></tr>";
	$LIMITSSTAT .= "<tr><td title='LIMIT expansion loop (in LOD DOC FETCH) to maximum $MAX_SRC$MAX_LOD_SUBJECT_DOCFETCH_SUBJECT_EXPANSION of the gathered RDF STORE subjects'>Limit RDF DOC FETCH LOOP TO: </td><td $TDR>$MAX_LOD_SUBJECT_DOCFETCH</td><td> subjects</td></tr>";
	$LIMITSSTAT .= "</table>";
}


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
				graphviz search graph:<input type='checkbox' name='viz3search' $checked_viz3search title='Visualize search subgraph for $search_term' >&nbsp;&nbsp;&nbsp;
				graphviz whole graph:<input type='checkbox' name='viz3pls' $checked_viz3pls title='Visualize all triples graphically' >&nbsp;&nbsp;&nbsp;
				Search term: '<label><b>$search_term</b></label>'
				</tr>
				</table>
		</form>
	</div>
	
	
	<div id='div2' class='rdf_widgetinfo'>
		$CONTENT2
	</div>
	
	<br>
	<div id='div2' class='limitstats'>
		$LIMITSSTAT
	</div>
	<br>
	
	
	<div id='div3' class='tripletable'>
		$CONTENT3
	</div>
	<br>

	<div id='div4' class='graphviz'>
	<img src="data:image/png;base64, $png "/>
	</div>
EOP;


?>
	</body>
</html>
