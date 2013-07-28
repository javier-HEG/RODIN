<?php

$filename="/u/arcUtilities.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}

$filename="/u/FRIutilities.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}

$filename="/u/RodinResult/RodinResultManager.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}

$filename="/u/RDFprocessor.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}


$sid=$_GET['sid'];
$reqhost=$_GET['reqhost']; //important - defines the store!
if (!$reqhost) $reqhost=$_SERVER['SERVER_NAME'];
$USER_ID=$_GET['user_id']; 
$USER=$USER_ID;
$USER_NAME=$_GET['username'];
$SEG=$RODINSEGMENT;
//In case of separate execution
$_SESSION['user_id']=$USER_ID;
$_SESSION['username']=$USER_NAME;
	
if ($sid<>'')
{
	$search_term = collect_queries_tag($SEG,$USER_ID,$sid);
}

$TITLEPAGE="'$search_term' RDFIZE";

?>
<html>
	<head>
		<title><?php print $TITLEPAGE; ?></title>
		<link rel="stylesheet" type="text/css" href="../css/rodin.css.php?" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script type='text/javascript' src='../u/RODINutilities.js.php?skin=<?php print $RODINSKIN;?>'></script>
	</head>	
	<body bgcolor=white >

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

$rdfize=$list3pls || $_GET['rdfize']=='on';
$checked_rdfize=$rdfize?' checked ':'';

$wps=$_GET['wps'];
$checked_wps=$wps?' checked ':'';
$WANT_USER_RESONANCE=($wps=='on' || $wps==1);

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
$want_rdfexpand=$rdfize || $list3page;

//For every interaction case cleanup db
//Attention: you can destroy database records
//Measure time
if ($list3page && $rdfize) 
{
	Logger::remove_db_logger_records($sid);
}

if ($rdfize && $listwr)
{
	$start_msec =  microtime(true);
}
############
//print "unlinking $RODIN_PROFILING_PATH";
if (file_exists($RODIN_PROFILING_PATH)) unlink($RODIN_PROFILING_PATH);
############

//TomaNota::vaciar();

if ($sid<>'')
{
	$fromResult = 0;
	
	//Recall results from SOLR using sid but no datasource! (get for every datasource)
	$allResults = RodinResultManager::getRodinResultsForASearch($sid,$datasource='',true,false);
	$resultCount = count($allResults);	

	$CONTENT2="$resultCount Widget result documents found for sid $sid";
	
	//var_dump($allResults);
	// Both a maximum size and a maximum number of results are set
	$resultMaxSetSize = $resultCount;
	
	$uptoResult = min($resultCount, $fromResult + $resultMaxSetSize);
	
	list($searchres_timestamp,$_) = timestamp_for_rdf_annotation();
	
	$i = $fromResult;
	$added_triples=0;

	if (!$sid) exit;

	###############################################################################################
	#
	$COUNTTRIPLES=true;
	if ($rdfize)
	{
		Logger::logAction(27, array('from'=>'rdfize','msg'=>"RDF Start using $resultCount results on search term '$search_term'"),$sid);
		
		if (($rdfprocessor = new RDFprocessor($sid,$USER_ID,$reqhost)))
		{
			if ($WANT_RDF_STORE_INITIALIZED_AT_EVERY_SEARCH) 
			{
				Logger::logAction(27, array('from'=>'rdfize','msg'=>"RDF INITSTORE'"),$sid);
				$rdfprocessor->reinit_store();
			}
			else {
				Logger::logAction(27, array('from'=>'rdfize','msg'=>"RDF PERSIST STORE'"),$sid);
			}
			
			
			$rdf_parameters = $rdfprocessor->log_rdf_parameters();
			$C=get_class($rdfprocessor);
			list( $search_subjects, 
						$result_subjects, 
						$searchuid,
						$count_added_triples_rdfize_extract_subjects ) = $rdfprocessor->rdfize_extract_subjects($COUNTTRIPLES);
						
				if ($COUNTTRIPLES) print "<br>rdfize_extract_subjects: globally $count_added_triples_rdfize_extract_subjects added triples";
			
				if(1 && $list3pls)
				{
					$RDFLOG.="<br> search_subjects: ".count($search_subjects);
					$RDFLOG.="<br> result_subjects: ".count($result_subjects);
				}
				//Continue if result_subjects expanded
				if (is_array($result_subjects) && count($result_subjects))
				{ // expand subjects
					if (is_array($search_subjects) && count($search_subjects))
					{
						if(1 && $list3pls)
						{
							$COUNTS=count($search_subjects);
							$RDFLOG.="<br>$COUNTS SEARCH SUBJECTS:";
							foreach($search_subjects as $label=>$uid)
								$RDFLOG.="<br>sub: <b>$label</b>=>$uid";
						}
					} else {
						if (1 && $list3pls) 
							$RDFLOG.="<br><b>NO search subjects extracted!</b>";
					}
					if (is_array($result_subjects) && count($result_subjects))
					{
						if(1 && $list3pls)
						{
							$COUNTS=count($result_subjects);
							$RDFLOG.="<br>$COUNTS RESULTS SUBJECTS:";
							foreach($result_subjects as $label=>$uid)
								$RDFLOG.="<br>sub: <b>$label</b>=>$uid";
						}
					} else {
						if (1 && $list3pls) 
							$RDFLOG.="<br><b>NO result subjects extracted!</b>";
					}
				
				//DEBUG: Reduce to some labels
				foreach($result_subjects as $label=>$uid)
				if ( 1 
						  // $label=='graphit' 
					 // || $label=='diamant'
					 // //|| $label=='articles d'
					 // || $label=='diamonds'
				) $limited_result_subjects{$label}=$uid;
	
				if(1)			
				list(	$skos_search_subjects_expansions,
							$skos_result_subjects_expansions,
							$count_added_triples_expand_rdfize_subjects ) = 
								$rdfprocessor->expand_rdfize_subjects($search_subjects,$searchuid,$limited_result_subjects,$COUNTTRIPLES);
								
				if ($COUNTTRIPLES) print "<br>expand_rdfize_subjects: globally $count_added_triples_expand_rdfize_subjects added triples";
				
				if (is_array($skos_result_subjects_expansions) 
				 		&& count($skos_result_subjects_expansions)==0)
				{
						if (1 && $list3pls) $RDFLOG.=htmlprint("<br>NO skos_subjects_expansions produced!",'red');
				}
				if (count($skos_result_subjects_expansions) || (is_array($result_subjects) && count($result_subjects)))
				{
					if(1 && $list3pls)
					{
						if (1) tell_skos_subjects($skos_search_subjects_expansions, 'SKOS *search* subjects espansions');
						if (1) tell_skos_subjects($skos_result_subjects_expansions, 'SKOS *result* subjects espansions');
					}					

					//Rerank the RESULT subjects to be used:
					$ranked_result_subjects = $rdfprocessor->rerank_subjects(	$search_subjects, 
																																		$flattened_expanded_search_subject_list 
																																			= flatten_sort_skos_obj_to_array($skos_search_subjects_expansions),
																																		$result_subjects, //do not use all subjects, only the SKOS ones
																																		$flattened_expanded_result_subject_list
																																			= flatten_sort_skos_obj_to_array($skos_result_subjects_expansions),
																																		$expanded_new_subjects = array()  );
					if(1 && $list3pls)
					{
						$RDFLOG.= "<hr><b>Sorted PRE ranked (expanded) result subjects: </b>";
					
						if(is_array($ranked_result_subjects)&&count($ranked_result_subjects))
							foreach($ranked_result_subjects as $label=>$REC)
							{
								list($rank,$k)=$REC;
								$BLA= "<br>$rank: $k: $label";
								if ($rank > 1)
									$RDFLOG.=htmlprint($BLA,'green');
								else 
									$RDFLOG.=htmlprint($BLA,'#fbb');
							} // foreach
					}

					if(1)
					list(	$expandeddocs,
								$expanded_new_subjects,
								$expanded_old_subjects,
								$count_triples_added_lod_subJ_doc_fetch	) = 
									$rdfprocessor->lod_subJ_doc_fetch(	$ranked_result_subjects, // $flattened_expanded_result_subject_list,
																											$result_subjects,
																											$COUNTTRIPLES );
					if ($COUNTTRIPLES) print "<br>lod_subJ_doc_fetch: globally $count_triples_added_lod_subJ_doc_fetch added triples";
					if (is_array($expandeddocs) && count($expandeddocs)
					|| (is_array($expanded_new_subjects) && count($expanded_new_subjects)))
					{
						
						if (1 && $list3pls)
						{
							if (count($expandeddocs))
								$RDFLOG.= "<br><br> ".count($expandeddocs)." LOD EXPANDED DOCS: ";foreach($expandeddocs as $expdocid) $RDFLOG.= "<br>$expdocid";
							if (count($expanded_new_subjects))
								$RDFLOG.= "<br><br> ".count($expanded_new_subjects)." LOD EXPANDED NEW SUBJECTS: ";foreach($expanded_new_subjects as $label=>$suid) $RDFLOG.= "<br>$label=>$suid";
							if (count($expanded_old_subjects))
								$RDFLOG.= "<br><br> ".count($expanded_old_subjects)." LOD EXPANDED OLD SUBJECTS: ";foreach($expanded_old_subjects as $label=>$suid) $RDFLOG.= "<br>$label=>$suid";
						}
						
						//Rerank again the extended RESULT subjects to be used:
						$ranked_result_subjects = $rdfprocessor->rerank_subjects(	$search_subjects, 
																																			$flattened_expanded_search_subject_list 
																																				= flatten_sort_skos_obj_to_array($skos_search_subjects_expansions),
																																			$result_subjects,
																																			$flattened_expanded_result_subject_list,
																																			$expanded_new_subjects  );
																																
						if(1 && $list3pls)
						{
							$RDFLOG.= "<hr><b>Sorted ranked (expanded) result subjects: </b>";
						
							if(is_array($ranked_result_subjects)&&count($ranked_result_subjects))
								foreach($ranked_result_subjects as $label=>$REC)
								{
									list($rank,$k)=$REC;
									$BLA= "<br>$rank: $k: $label";
									if ($rank > 1)
										$RDFLOG.=htmlprint($BLA,'green');
									else 
										$RDFLOG.=htmlprint($BLA,'#fbb');
								} // foreach
						}
																							
						$ranked_docs = $rdfprocessor->rerankadd_docs($sid, $expandeddocs, $ranked_result_subjects, $WANT_USER_RESONANCE);

            //OUTPUT IF RDFLAB/RDFIZE GUI param set
						if (1 && $list3pls)
						{
							$RDFLOG.= "<hr><b>Sorted ranked documents </b>";
							if(is_array($ranked_docs) && count($ranked_docs))
							{
								arsort($ranked_docs); // sory by key
								foreach($ranked_docs as $docuid=>$rank)
									$RDFLOG.="<br>$rank: $docuid";
							}
						}
						
					} // LOD fetch done -> rerankadd_docs
				} // subject expanded
				
			} // subjects computed -> expand subjects
		} // instantiate RDFprocessor
		Logger::logAction(27, array('from'=>'rdfize','msg'=>"RDF End using $resultCount results on search term '$search_term'"),$sid);
	} // rdfize
	#
	###############################################################################################
	
	
	$added_triples 	= $count_added_triples_rdfize_extract_subjects
									+	$count_added_triples_expand_rdfize_subjects
									+ $count_triples_added_lod_subJ_doc_fetch;
	if ($COUNTTRIPLES) print "<br>Globally $added_triples added triples for this rdf expansion";
	
	if($listwr)
	{
		$i=-1;
		foreach($allResults as $result)
		##########################
		{
			$CONTENT2.="<hr>";
			$CONTENT2.="<b>Title:</b> " . utf8_encode($result->getTitle());
			$CONTENT2.="<br><b>ISBN:</b> " . $result->getProperty('isbn');
			if($result->getAuthors())
				$CONTENT2 .= "<br><b>Authors:</b> " . utf8_encode($result->getAuthors());
			$CONTENT2 .= '<br><b>Date:</b> ' . $result->getDate();
			$CONTENT2.=  "<br><b>url:</b> ".$result->getUrlPage();
			
			foreach ($result->getValidProperties() as $property) {
				if ($result->getProperty($property))
				$CONTENT2.= "<br><b>$property:</b>" . utf8_encode($result->getProperty($property));
			}
		}
	}
		
	if ($list3pls && $deakt)
	{
	
		
		$CONTENT3 = get_triples_as_html_table2(	$rdfprocessor->store,
																						$rdfprocessor->storename,
																						$C::$NAMESPACES,
																						$C::$TOBECODED64,
																						$C::$ownnamespacename,
																						$added_triples,
																						$list3page,'',' for WIDGET RESULTS:','tripletable');
		}
	
	if ($viz3search && $rdfprocessor)
	{
	  $png = $rdfprocessor->get_jpeg_display_store_with_graphviz($search_term);	
	}
	else if ($viz3pls)
	{
	  $png = $rdfprocessor->get_jpeg_display_store_with_graphviz();	
	}
	
} // $sid


if (is_array($rdf_parameters) && count($rdf_parameters))
{
	//Construct small limit resumee on used limits
	$C=get_class($rdfprocessor);
	$TDR    		 ="align=right";
	$LIMITSSTAT .= "<table>";
	$LIMITSSTAT .= "<tr><th align=left colspan=3>LIMITS used during RDFization:</th></tr>";
	
	foreach($rdf_parameters as $paramname=>$value)
	{
		$LIMITSSTAT .= "<tr><td > $paramname: </td><td $TDR>$value</td><td>";
	}
	$LIMITSSTAT .= "</table>";
}

$PAGEWIDTH="400px";
$SRCLINK="$SRCLINKBASE/select_src.php?nl=0&u=$USER_ID&showuser=$USER_ID";
$STATLINK="../tests/rdf_exec_stat.php?sid=$sid&u=$USER_ID";


//if (!$list3pls) $RDFLOG=''; // CANCEL LOG IF JUST BATCH

##########################################
# The following is filled by the programs:
# $RDFLOG
##########################################
if ($rdfize && $listwr)
{
	$end_msec =  microtime(true);
	$duration_msec = $end_msec - $start_msec;
	$DURATION_TXT=" - DURATION: $duration_msec sec";
}

if($listwr)
print<<<EOP
	<div id='div1' style="width:1000px;height:400px;scroll:auto">
	<h2>$TITLEPAGE</h2>
	<p>
		<input type='button' title='Click to open LOCAL STORE SPARQL Explorer in new tab' value='OPEN LOCAL STORE SPARQL Explorer' onclick="window.open('$RDFSEMEXP_STOREEXPLORER')">
		&nbsp;&nbsp;&nbsp;
		<input type='button' title='Click to toggle RDF log display' value='show/hide RDF Logs' onclick="var l=document.getElementById('divLOGGING');toggle_visibility(l)">
		&nbsp;&nbsp;&nbsp;
		<input type='button' title='Click to see profiling execution times for optimization in new tab' value='Open profiling'onclick="window.open('$RODIN_PROFILING_LINK','_blank')">
		&nbsp;&nbsp;&nbsp;
		<input type='button' title='Click to open SRC-Management in new tab' value='Open SRC management'onclick="window.open('$SRCLINK','_blank')">
		&nbsp;&nbsp;&nbsp;
		<input type='button' title='Click to open Statistics for sid=$sid in new tab' value='Statistics'onclick="window.open('$STATLINK','_blank')">
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
					<input type='button' name='go' value='press to RDFize' style="width:100%;background-color:green;color:white;height:30px;font-size:large" onclick="fsid.submit()" >
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
				RDFize:<input type='checkbox' name='rdfize' $checked_rdfize title='For programs only ;-)'>&nbsp;&nbsp;&nbsp;
				List widget results:<input type='checkbox' name='listwr' $checked_listwr>&nbsp;&nbsp;&nbsp;
				P. Resonance:<input type='checkbox' name='wps' $checked_wps>&nbsp;&nbsp;&nbsp;
				RDFize & display triples:<input type='checkbox' name='list3pls' $checked_list3pls>&nbsp;&nbsp;&nbsp;
				graphviz search graph:<input type='checkbox' name='viz3search' $checked_viz3search title='Visualize search subgraph for $search_term' >&nbsp;&nbsp;&nbsp;
				graphviz whole graph:<input type='checkbox' name='viz3pls' $checked_viz3pls title='Visualize all triples graphically' >&nbsp;&nbsp;&nbsp;
				Search term: '<label><b>$search_term</b></label> $DURATION_TXT'
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
