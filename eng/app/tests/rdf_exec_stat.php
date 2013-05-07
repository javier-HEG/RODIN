<?php
require_once("../u/FRIdbUtilities.php");
require_once("../u/FRIutilities.php");
require_once("../u/RodinResult/RodinResultManager.php");

$sid=$_GET['sid'];
$USER_ID=$_GET['user_id'];
$USER=$USER_ID;
$USER_NAME=$_GET['username'];
$SEG=$RODINSEGMENT;
$_SESSION['user_id']=$USER_ID;
$_SESSION['username']=$USER_NAME;
	
$allpercent=0; // cumulated percent value

$TITLEPAGE="RDF STATISTICS on sid=$sid";

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





$computation_records=get_logger_records($sid);

foreach($computation_records as $computation_record)
{
	//Catch values:
	
	$timestamp=$computation_record['timestamp_prog'];
	$msg=$computation_record['msg'];
		
	//print "<br>$timestamp $msg";
	
	if (strstr($msg,'log_rdf_parameters'))
		$log_rdf_parameters = $msg;
	else if (strstr($msg,'get_related_subjects_expansions_using_thesauri'))
	{
		$subjects_expansions_using_thesauri[]=$computation_record;
	}
	else if (strstr($msg,'rdfLODfetchDocumentsOnSubjects'))
	{
		$rdfLODfetchDocumentsOnSubjects[]=$computation_record;
	}
	else if (strstr($msg,'RDF Start using'))
	{
		$rdf_start_r=$computation_record;
	}
	else if (strstr($msg,'RDF End using'))
	{
		$rdf_end_r=$computation_record;
	}
} // foreach




#######################################################
#
# ANALYSE subjects_expansions_using_thesauri
#
#######################################################
//print "<br>subjects_expansions_using_thesauri:<br>";
$subexp_src_single_stat = array();
if (count($subjects_expansions_using_thesauri)>0)
foreach($subjects_expansions_using_thesauri as $record)
{
	//print "<br>".$record['msg'];
	
	//$$starting=$src_parameters=$used_persisted_data='';
	//if (!$subexp_cycle)
	{
		if (strstr($record['msg'],'src_parameters'))
		{
			$src_parameters=$subexp_src_single_stat['src_parameters']=$record;
		}
		else if (strstr($record['msg'],'CALLING subexp SRC'))
		{
			$subexp_src_single_stat['calling_subexp_src']=$record;
		}
		else if (strstr($record['msg'],'USING PERSISTED DATA'))
		{
			if (!$subexp_src_single_stat['src_parameters'])
				$subexp_src_single_stat['src_parameters']=$src_parameters;
			$subexp_src_single_stat['src_used_persisted_data']=$record;
			$subexp_src_stats[]=$subexp_src_single_stat;
			$subexp_src_single_stat = array();
			//Add data to obj
		}
		else if (strstr($record['msg'],'REQUESTING NEW DATA'))
			$subexp_src_single_stat['src_requested_new_data']=$record;
		else if (strstr($record['msg'],'EXIT NEW DATA'))
		{
			$subexp_src_single_stat['src_exit_new_data']=$record;
			$subexp_src_stats[]=$subexp_src_single_stat;
			$subexp_src_single_stat = array();
		}
		else if (strstr($record['msg'],'Exit subexp'))
		{
			$exit_r=$record;
			// $subexp_src_stats[]=$subexp_src_single_stat;
			// $subexp_src_single_stat = array();
		}
	}
} // subjects_expansions_using_thesauri







#######################################################
#
# ANALYSE rdfLODfetchDocumentsOnSubjects
#
#######################################################
//print "<br>subjects_expansions_using_thesauri:<br>";
$lodfetch_src_single_stat = array();
if (count($rdfLODfetchDocumentsOnSubjects)>0)
foreach($rdfLODfetchDocumentsOnSubjects as $record)
{
	//print "<br>lodfetch 1 record: ".$record['msg'];
	
	//$$starting=$src_parameters=$used_persisted_data='';
	if (strstr($record['msg'],'src_parameters'))
	{
		$src_parameters=$lodfetch_src_single_stat['src_parameters']=$record;
	}
	else if (strstr($record['msg'],'CALLING lodfetch SRC'))
	{
		$lodfetch_src_single_stat['calling_lodfetch_src']=$record;
	}
	else if (strstr($record['msg'],'START HOMOGENIZE'))
	{
		$lodfetch_src_single_stat['start_homogenize']=$record;
	}
	else if (strstr($record['msg'],'END HOMOGENIZE'))
	{
		$lodfetch_src_single_stat['end_homogenize']=$record;
	}
	else if (strstr($record['msg'],'START RERANK'))
	{
		$lodfetch_src_single_stat['start_rerank']=$record;
	}
	else if (strstr($record['msg'],'END RERANK'))
	{
		$lodfetch_src_single_stat['end_rerank']=$record;
	}
	else if (strstr($record['msg'],'START remove_unused_src_triples_on_service_and_search'))
	{
		$lodfetch_src_single_stat['start_rdfremove']=$record;
	}	
	else if (strstr($record['msg'],'END remove_unused_src_triples_on_service_and_search'))
	{
		$lodfetch_src_single_stat['end_rdfremove']=$record;
	}
	
	else if (strstr($record['msg'],'START WRES ASSEMBLING'))
	{
		$lodfetch_src_single_stat['start_wres_ass']=$record;
	}	
	else if (strstr($record['msg'],'END WRES ASSEMBLING'))
	{
		$lodfetch_src_single_stat['end_wres_ass']=$record;
	}
	
	else if (strstr($record['msg'],'USING PERSISTED DATA'))
	{
		if (!$lodfetch_src_single_stat['src_parameters'])
			$lodfetch_src_single_stat['src_parameters']=$src_parameters;
		$lodfetch_src_single_stat['used_persisted_data']=$record;
		$lodfetch_src_stats[]=$lodfetch_src_single_stat;
		//print "<br>PUTTING RECORD: ";var_dump($lodfetch_src_single_stat);
		
		$lodfetch_src_single_stat = array();
		//Add data to obj
	}
	else if (strstr($record['msg'],'REQUESTING NEW DATA'))
	{
		$lodfetch_src_single_stat['requested_new_data']=$record;
	}
	else if (strstr($record['msg'],'EXIT NEW DATA'))
	{
		$lodfetch_src_single_stat['lod_exit_new_data']=$record;
		$lodfetch_src_stats[]=$lodfetch_src_single_stat;
		//print "<br>PUTTING RECORD: ";var_dump($lodfetch_src_single_stat);
		
		$lodfetch_src_single_stat = array();
	}
	else if (strstr($record['msg'],'Exit lodfetch'))
	{
		$exit_r=$record;
		
		// $lodfetch_src_stats[]=$lodfetch_src_single_stat;
		// print "<br>PUTTING RECORD: ";var_dump($lodfetch_src_single_stat);
		// $lodfetch_src_single_stat=array();
	}
} // rdfLODfetchDocumentsOnSubjects






//Compute whole computation duration

list($rdfization_duration_secs,
		 $rdfization_duration_str) = get_timestamp_diff_logger($rdf_end_r, $rdf_start_r);



#######################################################
#
# PRETTY PRINTING $subexp_src_stats
#
#######################################################

//print "<hr>subexp SRC stats";
if (count($subexp_src_stats) > 0)
foreach($subexp_src_stats as $subexp_src_stat)
{
	if ($subexp_src_stat)
	{
		//print "<hr>subexp_src_stat: ";var_dump($subexp_src_stat);print "<br>";
		$src_parameters_r=$subexp_src_stat['src_parameters'];
		
		list($max_triples,$max_subjects,$max_docs) = scan_src_params($src_parameters_r);
		
		$calling_subexp_src_r= $subexp_src_stat['calling_subexp_src'];
		$ending_subexp_r= ($used_subexp_remote=$subexp_src_stat['src_requested_new_data'])
											?$subexp_src_stat['src_exit_new_data']
											:$subexp_src_stat['src_used_persisted_data'];
	
		if (!$ending_subexp_r)
		{
			print "<br>AAATENTION: ending_subexp_r is NULL. >Record: "; var_dump($subexp_src_stat);
		}
											
		list($src_name,$subject) = scan_src_subexp($calling_subexp_src_r);
		
		//print "<br>SUB:";
		$resultcount=($subexp_src_stat['src_exit_new_data']
				?scan_resultcount_newdata($ending_subexp_r) 
				:scan_resultcount_persisted_rdf_data($ending_subexp_r));
		
		//Compute duration:
		list($subexp_duration_secs,
			 	 $subexp_duration_str) = get_timestamp_diff_logger($ending_subexp_r,$calling_subexp_src_r);
		
		$verbose=$used_subexp_remote?' NEW ':' RDF ';
		
		//print "<br>STAT $verbose $src_name($subject)=$resultcount $subexp_duration_secs secs x subexp (max_triples=$max_triples, max_subjects=$max_subjects) "; 
		
		$all_duration_subexp+=$subexp_duration_secs;
		$all_duration_subexp_percent = tell_percent_of($all_duration_subexp,$rdfization_duration_secs,$cumulate=false);
		
		$subexp_calls++;
		if ($used_subexp_remote)
		{
			$subexp_remote_calls+=1;
			$all_duration_subexp_remote+=$subexp_duration_secs;
			
			if ($resultcount)
			{
				$succesful_subexp_remote_calls+=1;
				$duration_succesful_subexp_remote_calls+=$subexp_duration_secs;
			}
			else
			{
				$unsuccesful_subexp_remote_calls+=1;
				$duration_unsuccesful_subexp_remote_calls+=$subexp_duration_secs;
			}
		}
		else // !$used_subexp_remote
		{
			$rdf_subexp_rdf_saved_calls+=1;
			$all_duration_subexp_rdf_saved+=$subexp_duration_secs;
			if ($resultcount)
			{
				$succesful_subexp_rdf_saved_calls+=1;
				$duration_succesful_subexp_rdf_saved_calls+=$subexp_duration_secs;
			}
			else
			{
				$unsuccesful_subexp_rdf_saved_calls+=1;
				$duration_unsuccesful_subexp_rdf_saved_calls+=$subexp_duration_secs;
			}
		}
	}
} //subexp

//Make zero to avoid null



if (!$rdf_subexp_rdf_saved_calls) $rdf_subexp_rdf_saved_calls=0;
if (!$succesful_subexp_rdf_saved_calls) $succesful_subexp_rdf_saved_calls=0;
if (!$unsuccesful_subexp_rdf_saved_calls) $unsuccesful_subexp_rdf_saved_calls=0;

if (!$all_duration_subexp_rdf_saved) $all_duration_subexp_rdf_saved=0; 
		$all_duration_subexp_rdf_saved_percent = tell_percent_of($all_duration_subexp_rdf_saved,$rdfization_duration_secs,$cumulate=true);
if (!$duration_succesful_subexp_rdf_saved_calls) $duration_succesful_subexp_rdf_saved_calls=0;
		$duration_succesful_subexp_rdf_saved_calls_percent=tell_percent_of($duration_succesful_subexp_rdf_saved_calls,$rdfization_duration_secs,$cumulate=false);
if (!$duration_unsuccesful_subexp_rdf_saved_calls) $duration_unsuccesful_subexp_rdf_saved_calls=0;
		$duration_unsuccesful_subexp_rdf_saved_calls_percent=tell_percent_of($duration_unsuccesful_subexp_rdf_saved_calls,$rdfization_duration_secs);

if (!$subexp_remote_calls) $subexp_remote_calls=0;
if (!$succesful_subexp_remote_calls) $succesful_subexp_remote_calls=0;
	$succesful_subexp_remote_calls_percent = tell_percent_of($succesful_subexp_remote_calls,$rdfization_duration_secs,$cumulate=false);
if (!$unsuccesful_subexp_remote_calls) $unsuccesful_subexp_remote_calls=0;
	$unsuccesful_subexp_remote_calls_percent = tell_percent_of($unsuccesful_subexp_remote_calls,$rdfization_duration_secs,$cumulate=false);

if (!$all_duration_subexp_remote) $all_duration_subexp_remote=0;
	$all_duration_subexp_remote_percent = tell_percent_of($all_duration_subexp_remote,$rdfization_duration_secs,$cumulate=false);
if (!$duration_succesful_subexp_remote_calls) $duration_succesful_subexp_remote_calls=0;
	$duration_succesful_subexp_remote_calls_percent = tell_percent_of($duration_succesful_subexp_remote_calls,$rdfization_duration_secs,$cumulate=false);
if (!$duration_unsuccesful_subexp_remote_calls) $duration_unsuccesful_subexp_remote_calls=0;
	$duration_unsuccesful_subexp_remote_calls_percent = tell_percent_of($duration_unsuccesful_subexp_remote_calls,$rdfization_duration_secs,$cumulate=false);
#
# end PRETTY PRINTING $subexp_src_stats
#
#######################################################




#######################################################
#
# PRETTY PRINTING $lodfetch_src_stats
#
#######################################################
//print "<hr>subexp SRC stats";
$lodfetch_homo=0;
if (count($lodfetch_src_stats) > 0)
foreach($lodfetch_src_stats as $lodfetch_src_stat)
{
	//print "<hr>lodf record:<br>";var_dump($lodfetch_src_stat);print "<br>";
	$src_parameters_r=$lodfetch_src_stat['src_parameters'];
	
	list($max_triples,$max_subjects,$max_docs) = scan_src_params($src_parameters_r);
	
	$calling_lodfetch_src_r= $lodfetch_src_stat['calling_lodfetch_src'];
	$ending_lodfetch_r= ($used_lodfetch_remote=$lodfetch_src_stat['lod_exit_new_data'])
											?$lodfetch_src_stat['lod_exit_new_data']
											:$lodfetch_src_stat['used_persisted_data'];

	list($src_name,$subject) = scan_src_lodfetch($calling_lodfetch_src_r);
	
	$lodfetch_resultcount=($lodfetch_src_stat['lod_exit_new_data']  
			?scan_resultcount_newdata($ending_lodfetch_r) 
			:scan_resultcount_persisted_rdf_data($ending_lodfetch_r));


	$lodfetch_homo_duration_secs=0;
	$start_homogenize_r= $lodfetch_src_stat['start_homogenize'];
	if ($start_homogenize_r)
	{
		$end_homogenize_r= $lodfetch_src_stat['end_homogenize'];
		list($lodfetch_homo_duration_secs,
		 	 $lodfetch_homo_duration_str) = get_timestamp_diff_logger($end_homogenize_r,$start_homogenize_r);
	} 

	$start_rerank_r= $lodfetch_src_stat['start_rerank'];
	if ($start_rerank_r)
	{
		$end_rerank_r= $lodfetch_src_stat['end_rerank'];
		list($lodfetch_rerank_duration_secs,
		 	 $lodfetch_rerank_duration_str) = get_timestamp_diff_logger($end_rerank_r,$start_rerank_r);
	} 

	$start_rdfremove_r= $lodfetch_src_stat['start_rdfremove'];
	if ($start_rdfremove_r)
	{
		$end_rdfremove_r= $lodfetch_src_stat['end_rdfremove'];
		list($lodfetch_rdfremove_duration_secs,
		 	 $lodfetch_rdfremove_duration_str) = get_timestamp_diff_logger($end_rdfremove_r,$start_rdfremove_r);
	} 

	$start_wresass_r= $lodfetch_src_stat['start_wres_ass'];
	if ($start_wresass_r)
	{
		$end_wresass_r= $lodfetch_src_stat['end_wres_ass'];
		list($lodfetch_wresass_duration_secs,
		 	 $lodfetch_wresass_duration_str) = get_timestamp_diff_logger($end_wresass_r,$start_wresass_r);
	} 
	
	
	//Compute duration:
	list($lodfetch_duration_secs,
		 	 $lodfetch_duration_str) = get_timestamp_diff_logger($ending_lodfetch_r,$calling_lodfetch_src_r);
	
	$verbose=$used_lodfetch_remote?' NEW ':' RDF ';
	
	//print "<br>STAT $verbose $src_name($subject)=$resultcount $lodfetch_duration_secs secs x lodfetch (max_triples=$max_triples, max_subjects=$max_subjects) "; 
	
	$lodfetch_homo = ($start_homogenize_r)?($lodfetch_homo+1):$lodfetch_homo;
	$lodfetch_homo_all_duration_secs+= $lodfetch_homo_duration_secs;
	
	$all_duration_lodfetch+=$lodfetch_duration_secs;
	$all_duration_rerank_duration_secs+=$lodfetch_rerank_duration_secs;
	$all_duration_rdfremove_duration_secs+=$lodfetch_rdfremove_duration_secs;
	$all_duration_wresass_duration_secs+=$lodfetch_wresass_duration_secs;
	
	$lodfetch_calls++;
	if ($used_lodfetch_remote)
	{
		$remote_lodfetch_calls+=1;
		$all_duration_lodfetch_remote+=$lodfetch_duration_secs;
		
		if ($resultcount)
		{
			$succesful_lodfetch_remote_calls+=1;
			$duration_succesful_lodfetch_remote_calls+=$lodfetch_duration_secs;
		}
		else
		{
			$unsuccesful_lodfetch_remote_calls+=1;
			$duration_unsuccesful_lodfetch_remote_calls+=$lodfetch_duration_secs;
		}
	}
	else
	{
		$lodfetch_rdf_saved_calls+=1;
		$all_duration_lodfetch_rdf_saved+=$lodfetch_duration_secs;
		if ($lodfetch_resultcount)
		{
			$succesful_lodfetch_rdf_saved_calls+=1;
			$duration_succesful_lodfetch_rdf_saved_calls+=$lodfetch_duration_secs;
		}
		else
		{
			$unsuccesful_lodfetch_rdf_saved_calls+=1;
			$duration_unsuccesful_rdf_saved_calls+=$lodfetch_duration_secs;
		}
	}
} //lodfetch

//Make zero to avoid null

if (!$lodfetch_homo) $lodfetch_homo=0;
if (!$lodfetch_homo_all_duration_secs) $lodfetch_homo_all_duration_secs=0;
		$lodfetch_homo_all_duration_secs_percent = tell_percent_of($lodfetch_homo_all_duration_secs,$rdfization_duration_secs,$cumulate=false);

if (!$all_duration_rerank_duration_secs) $all_duration_rerank_duration_secs=0;
		$all_duration_rerank_duration_secs_percent = tell_percent_of($all_duration_rerank_duration_secs,$rdfization_duration_secs,$cumulate=false);

if (!$all_duration_rdfremove_duration_secs) $all_duration_rdfremove_duration_secs=0;
		$all_duration_rdfremove_duration_secs_percent = tell_percent_of($all_duration_rdfremove_duration_secs,$rdfization_duration_secs,$cumulate=false);

if (!$all_duration_wresass_duration_secs) $all_duration_wresass_duration_secs=0;
		$all_duration_wresass_duration_secs_percent = tell_percent_of($all_duration_wresass_duration_secs,$rdfization_duration_secs,$cumulate=false);

if (!$lodfetch_rdf_saved_calls) $lodfetch_rdf_saved_calls=0;
if (!$succesful_lodfetch_rdf_saved_calls) $succesful_lodfetch_rdf_saved_calls=0;
if (!$unsuccesful_lodfetch_rdf_saved_calls) $unsuccesful_lodfetch_rdf_saved_calls=0;

if (!$all_duration_lodfetch_rdf_saved) $all_duration_lodfetch_rdf_saved=0;
		$all_duration_lodfetch_rdf_saved_percent = tell_percent_of($all_duration_lodfetch_rdf_saved,$rdfization_duration_secs,$cumulate=false);
if (!$duration_succesful_lodfetch_rdf_saved_calls) $duration_succesful_lodfetch_rdf_saved_calls=0;
		$duration_succesful_lodfetch_rdf_saved_calls_percent = tell_percent_of($duration_succesful_lodfetch_rdf_saved_calls,$rdfization_duration_secs,$cumulate=false);
if (!$duration_unsuccesful_rdf_saved_calls) $duration_unsuccesful_rdf_saved_calls=0;
		$duration_unsuccesful_rdf_saved_calls_percent = tell_percent_of($duration_unsuccesful_rdf_saved_calls,$rdfization_duration_secs,$cumulate=false);

if (!$remote_lodfetch_calls) $remote_lodfetch_calls=0;
if (!$succesful_lodfetch_remote_calls) $succesful_lodfetch_remote_calls=0;
if (!$unsuccesful_lodfetch_remote_calls) $unsuccesful_lodfetch_remote_calls=0;

if (!$all_duration_lodfetch_remote) $all_duration_lodfetch_remote=0;
		$all_duration_lodfetch_remote_percent = tell_percent_of($all_duration_lodfetch_remote,$rdfization_duration_secs,$cumulate=false);
if (!$duration_succesful_lodfetch_remote_calls) $duration_succesful_lodfetch_remote_calls=0;
		$duration_succesful_lodfetch_remote_calls_percent = tell_percent_of($duration_succesful_lodfetch_remote_calls,$rdfization_duration_secs,$cumulate=false);
if (!$duration_unsuccesful_lodfetch_remote_calls) $duration_unsuccesful_lodfetch_remote_calls=0;
		$duration_unsuccesful_lodfetch_remote_calls_percent = tell_percent_of($duration_unsuccesful_lodfetch_remote_calls,$rdfization_duration_secs,$cumulate=false);



$CONTROLPARAMS = get_CONTROLPARAMS($log_rdf_parameters);
$SAVEDOPT=" class='rdfsavedstat' ";
$CALLEDOPT=" class='rdfcalledstat' ";
$HOMOOPT =" class='rdfhomogstat' ";
$PERCENTSTYLE = " class='percentstat' ";

print <<<EOP
	<table>
		<tr>
			<td colspan=3><hr></td>
		</tr>
		<tr>	
			<td colspan=3><b>RODIN RDF STATISTICS on sid $sid</b></td>
		</tr>
		<tr height=10 />	
		<tr>	
			<td colspan=2><b>CONTROL PARAMETERS:</b></td>
		</tr>
		$CONTROLPARAMS
		<tr height=10 />	
		<tr>	
			<td>
				<b>RDFization DURATION: <b>
			</td>
			<td>
				<b>$rdfization_duration_secs secs </b>
			</td>
			</td>
			<td $PERCENTSTYLE><b>100.000%</b>
			</td>
		</tr>
		</tr>
		<tr>
			<td colspan=3><hr></td>	
		</tr>
		<tr>
			<td><b>Duration $subexp_calls RDF SERVED SRC CALLs subexp:</b>
			</td>
			<td><b>$all_duration_subexp secs</b>
			</td>
			<td $PERCENTSTYLE><b>$all_duration_subexp_percent</b>
			</td>
		</tr>
		<tr>
			<td $SAVEDOPT>Duration $rdf_subexp_rdf_saved_calls RDF PERSISTED SRC CALLs subexp:
			</td>
			<td $SAVEDOPT>$all_duration_subexp_rdf_saved secs
			</td>
			<td align=right $PERCENTSTYLE> 
				$all_duration_subexp_rdf_saved_percent
			</td>
		</tr>
EOP;
if ($rdf_subexp_rdf_saved_calls)
print <<<EOP
		<tr>	
			<td align=right $SAVEDOPT>$succesful_subexp_rdf_saved_calls SUCCESFUL (with results):&nbsp;&nbsp;
			</td>
			<td $SAVEDOPT>$duration_succesful_subexp_rdf_saved_calls secs
			</td>
			</td>
				<td $PERCENTSTYLE>$duration_succesful_subexp_rdf_saved_calls_percent
			</td>
		</tr>
		<tr>	
			<td align=right $SAVEDOPT>$unsuccesful_subexp_rdf_saved_calls UNSUCCESFUL (no results):&nbsp;&nbsp;
			</td>
			<td $SAVEDOPT>$duration_unsuccesful_subexp_rdf_saved_calls secs
			</td>
			<td $PERCENTSTYLE>
				$duration_unsuccesful_subexp_rdf_saved_calls_percent
			</td>
		</tr>
EOP;
print<<<EOP
		<tr>	
			<td $CALLEDOPT>Duration $subexp_remote_calls REMOTE NEW SRC CALLs subexp:
			</td>
			<td $CALLEDOPT>$all_duration_subexp_remote secs
			</td>
			<td $PERCENTSTYLE>
				$all_duration_subexp_remote_percent
			</td>
		</tr>
		</tr>
EOP;
if ($subexp_remote_calls)
print <<<EOP
		<tr>	
			<td align=right $CALLEDOPT>$succesful_subexp_remote_calls SUCCESFUL (with results):&nbsp;&nbsp;
			</td>
			<td $CALLEDOPT>$duration_succesful_subexp_remote_calls secs
			</td>
			<td $PERCENTSTYLE>
				$duration_succesful_subexp_remote_calls_percent
			</td>
		</tr>
		<tr>	
			<td align=right $CALLEDOPT>$unsuccesful_subexp_remote_calls UNSUCCESFUL (no results):&nbsp;&nbsp;
			</td>
			<td $CALLEDOPT>$duration_unsuccesful_subexp_remote_calls secs
			</td>
			<td $PERCENTSTYLE>
				$duration_unsuccesful_subexp_remote_calls_percent
			</td>
		</tr>
EOP;


#################################################
#
# LODFETCH (and homogenization) :
#
#################################################


print <<<EOP
		<tr>
			<td colspan=3><hr></td>
		</tr>
		<tr>	
			<td><b>Duration $lodfetch_calls SERVED LOD CALLs lodfetch:</b>
			</td>
			<td><b>$all_duration_lodfetch secs</b>
			</td>
			<td $PERCENTSTYLE>
				<b>$all_duration_lodfetch_percent</b>
			</td>
		</tr>
		<tr>	
			<td $SAVEDOPT>Duration $lodfetch_rdf_saved_calls RDF PERSISTED LOD CALLs lodfetch:
			</td>
			<td $SAVEDOPT>$all_duration_lodfetch_rdf_saved secs
			</td>
			<td $PERCENTSTYLE>
				$all_duration_lodfetch_rdf_saved_percent
			</td>
		</tr>
EOP;
		
if ($lodfetch_rdf_saved_calls)
print <<<EOP
		<tr>	
			<td align=right $SAVEDOPT>$succesful_lodfetch_rdf_saved_calls SUCCESFUL (with results):&nbsp;&nbsp;
			</td>
			<td $SAVEDOPT>$duration_succesful_lodfetch_rdf_saved_calls secs
			</td>
			<td $PERCENTSTYLE>
				$duration_succesful_lodfetch_rdf_saved_calls_percent
			</td>
		</tr>
		<tr>	
			<td align=right $SAVEDOPT>$unsuccesful_lodfetch_rdf_saved_calls UNSUCCESFUL (no results):&nbsp;&nbsp;
			</td>
			<td $SAVEDOPT>$duration_unsuccesful_rdf_saved_calls secs
			</td>
			<td $PERCENTSTYLE>
				$duration_unsuccesful_rdf_saved_calls_percent
			</td>
		</tr>
EOP;
print <<<EOP
		<tr>	
			<td $CALLEDOPT>Duration $remote_lodfetch_calls REMOTE NEW SRC CALLs subexp:
			</td>
			<td $CALLEDOPT>$all_duration_lodfetch_remote secs
			</td>
			<td $PERCENTSTYLE>
				$all_duration_lodfetch_remote_percent
			</td>
		</tr>
EOP;

if ($remote_lodfetch_calls)
print <<<EOP
		<tr>	
			<td align=right $CALLEDOPT>$succesful_lodfetch_remote_calls SUCCESFUL (with results):&nbsp;&nbsp;
			</td>
			<td $CALLEDOPT>$duration_succesful_lodfetch_remote_calls secs
			</td>
			<td $PERCENTSTYLE>
				$duration_succesful_lodfetch_remote_calls_percent
			</td>
		</tr>
		<tr>	
			<td align=right $CALLEDOPT>$unsuccesful_lodfetch_remote_calls UNSUCCESFUL (no results):&nbsp;&nbsp;
			</td>
			<td $CALLEDOPT>$duration_unsuccesful_lodfetch_remote_calls secs
			</td>
			<td $PERCENTSTYLE>
				$duration_unsuccesful_lodfetch_remote_calls_percent
			</td>
		</tr>
EOP;
if ($lodfetch_homo)
{
	$EVTLS=($lodfetch_homo>1)?'s':'';
print <<<EOP
		<tr>	
			<td align=right $HOMOOPT>$lodfetch_homo HOMOGENIZATION$EVTLS:&nbsp;&nbsp;
			</td>
			<td $HOMOOPT>$lodfetch_homo_all_duration_secs secs
			</td>
			<td $PERCENTSTYLE>
				$lodfetch_homo_all_duration_secs_percent
			</td>
		</tr>
EOP;
}

if ($all_duration_rerank_duration_secs)
{
print <<<EOP
		<tr>	
			<td align=right $HOMOOPT>RERANK:&nbsp;&nbsp;
			</td>
			<td $HOMOOPT>$all_duration_rerank_duration_secs secs
			</td>
			<td $PERCENTSTYLE>
				$all_duration_rerank_duration_secs_percent
			</td>
		</tr>
EOP;
}

if ($all_duration_rdfremove_duration_secs)
{
print <<<EOP
		<tr>	
			<td align=right $HOMOOPT>RDF REMOVE:&nbsp;&nbsp;
			</td>
			<td $HOMOOPT>$all_duration_rdfremove_duration_secs secs
			</td>
			<td $PERCENTSTYLE>
				$all_duration_rdfremove_duration_secs_percent
			</td>
		</tr>
EOP;
}


if ($all_duration_rdfremove_duration_secs)
{
print <<<EOP
		<tr>	
			<td align=right $WRESASS>WIDGET RESULT ASSEMBLING:&nbsp;&nbsp;
			</td>
			<td $WRESASS>$all_duration_wresass_duration_secs secs
			</td>
			<td $PERCENTSTYLE>
				$all_duration_wresass_duration_secs_percent
			</td>
		</tr>
EOP;
}



#####################################################
#####################################################
#####################################################
#####################################################
#####################################################





function scan_src_params(&$logger_record)
{
	$PATTERN_MAXTRIPLES="/max_triples=(\d+);/";
	$PATTERN_MAXDOCS="/max_docs=(\d+);/";
	$PATTERN_MAXSUB="/max_subjects=(\d+);/";
		if (preg_match($PATTERN_MAXTRIPLES,$logger_record['msg'],$match))
		$max_triples = $match[1];
		
	if (preg_match($PATTERN_MAXDOCS,$logger_record['msg'],$match))
		$max_docs = $match[1];
		
	if (preg_match($PATTERN_MAXSUB,$logger_record['msg'],$match))
		$max_subj = $match[1];
		
	return array($max_triples,$max_subj,$max_docs);
} // scan_src_params&$logger_record





function get_CONTROLPARAMS($log_rdf_parameters)
{

	//tests:
	if ($log_rdf_parameters)
	{
		if (preg_match("/rdf_exec_params\=(.*)$/",$log_rdf_parameters,$match))
		{
			$src_parameters=$match[1].';';
			
			//print "<br>eval($src_parameters)";
			
			eval($src_parameters); // extract $LOGPARAMS[]
			
			
			foreach($LOGPARAMS as $name=>$value)
			{
				$CONTROLPARAMS.="<tr><td>$name:</td><td>$value</td></tr>";
			}
		}
	}
	return $CONTROLPARAMS;
} // get_CONTROLPARAMS


function scan_resultcount_persisted_rdf_data(&$logger_record)
{
	$debug=0;
	//print "<br>scan_resultcount_persisted_rdf_data (".$logger_record['msg'].") ";
	$PATTERN="/USING PERSISTED DATA \((\d+)\) FROM/";
	if (preg_match($PATTERN,$x=$logger_record['msg'],$match))
		$count_results = $match[1];

	if ($debug)
		print "<br>scan_resultcount_persisted_rdf_data returning count_results=($count_results) reading $x";

	return $count_results;
} // scan_resultcount_persisted_rdf_data


	
function scan_resultcount_newdata(&$logger_record)
{
	$debug=0;
	$PATTERN="/EXIT NEW DATA \((\d+)\) FROM/";
	
	//print "<br>scan_resultcount_newdata (".$logger_record['msg'].") ";
	
	if (preg_match($PATTERN,$x=$logger_record['msg'],$match))
		$count_results = $match[1];
	
	if ($debug)
		print "<br>scan_resultcount_newdata returning count_results=($count_results) reading $x";
		
	return $count_results;
} // scan_resultcount_newdata
	
	

function 	scan_src_subexp(&$logger_record)
{
	$PATTERN="/CALLING subexp SRC (.*) on subject (.*) with max/";
	//print "<br>PAT scan_src_subexp($PATTERN in (".$logger_record['msg']."))";
	if (preg_match($PATTERN,$logger_record['msg'],$match))
	{
		//print "<br>YES ".$match[0];
		$src_name = $match[1];
		$subject = $match[2];
	}
	//else print "<br>no $PATTERN in (".$logger_record['msg'].")";
	
	//print "<br>RETURNING $src_name,$subject";
	
	return array($src_name,$subject);
} // scan_src_subexp
	

	
	
function 	scan_src_lodfetch(&$logger_record)
{
	$PATTERN="/CALLING lodfetch SRC (.*) on subject (.*) with max/";
	//print "<br>PAT scan_src_subexp($PATTERN in (".$logger_record['msg']."))";
	if (preg_match($PATTERN,$logger_record['msg'],$match))
	{
		//print "<br>YES ".$match[0];
		$src_name = $match[1];
		$subject = $match[2];
	}
	//else print "<br>no $PATTERN in (".$logger_record['msg'].")";
	
	//print "<br>RETURNING $src_name,$subject";
	
	return array($src_name,$subject);
} // scan_src_lodfetch
		

		
		
		
function tell_percent_of($value,$whole)
{
	global $allpercent; // cumulated percent
	if ($whole>0)
	{
		$allpercent+= $value/$whole;
		$ratio = round($value * 100 / $whole,3);
		$ratio=number_format($ratio,3);
	}
	return "$ratio %";
}


	
?>
</body>
</html>
