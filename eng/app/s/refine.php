
<?php

	#Technical sem refining interface

	include_once("../u/FRIdbUtilities.php");
  $MAXDUR_REFINESINGLECALL_msec = 5000;
	
	
	$sid		=$_REQUEST['sid'];
	$v			=base64_decode($_REQUEST['v']);
	$datasource	=$_REQUEST['datasource'];
	$rescnt		=$_REQUEST['rescnt'];    // the xth document of the result
	$recordcnt	=$_REQUEST['recordcnt']; // the nth record to the document
	$xpointercnt=$_REQUEST['xpointer']; // the nth record to the document

	$widget_info=fri_get_dir_infos($datasource);
	$widget_id=$widget_info['id'];

	if (is_a_value($recordcnt))
	{
		if ($recordcnt==2) $DECL="nd";
		else if ($recordcnt==3) $DECL="rd";
		else  $DECL="th";
		$RECORDINFO=<<<EOT
	<tr><td align="right" valign="middle"><b>Record </b></td><td>&nbsp;r&nbsp;&nbsp;= $recordcnt (the $recordcnt-$DECL visualized record with xpointer=$xpointercnt inside d)</td>
EOT;
	}

	$q = get_query($sid);
		
	if (!$v) // vektor nicht mitgegeben -> Daten aus DB holen
	{
		$QUERY_RESULTS=<<<EOQ
			SELECT
				xpointer,
				node,
				follow,
				attribute,
				type,
				value,
				url,
				visible
			FROM result
			WHERE sid = '$sid' 
			AND datasource='$datasource'
EOQ;
	
		$DB = new RODIN_DB();
		$DBconn=$DB->DBconn;
	
		$resultset= mysql_query($QUERY_RESULTS);
		if ($resultset)
		{
			$OLD_LEVEL1_XPOINTER='';
			$old_xpointer=-1;
			$xrescnt=-1;
			$xrecordcnt=0;
			$stop = false;
			while (($row = mysql_fetch_assoc($resultset)) && (!$stop))
			{
		
				$xpointer	=$row['xpointer'];
				$attribute	=$row['attribute'];
				$type		=$row['type'];
				$value		=$row['value'];
				$url		=$row['url'];
				$visible	=$row['visible'];
				$node		=$row['node'];
				$follow		=$row['follow'];
				print "<br> ($xpointer) $attribute: $value<br>";
	
				$XLEVEL=compute_XPointerLevel($xpointer);
		
				if ($wordvector_MULTIVALUE) $wordvector_MULTIVALUE.=" ";
				$wordvector_MULTIVALUE.= $value; // sum up all words
			}
	
		} // $resultset
	} // Daten aus DB holen
	
	
	if ($v)
	{
			$wordVector= $v;
	}
	else 
	{
			$wordVector= $wordvector_SINGLEVALUE;
	}
	
	
	$wordVector = cleanUpWordVector($wordVector);
	##########################################
	/*Testausgabe
	http://rodinsrc/refine 
						?sid=((sid)) 
						& q=((q))
						& v=((V)) 			//possibly base64 coded
						& w=((Wid))			//Widget  id (see above)
						& maxdur=((milliseconds))    	// Defines the maximal allowed computation duration
						& p=((s=syncrhonous call - default, a=asynchronous call))
						& id=((identifies « this » call – in case of non sinch processing))
*/
	
	
	print "refine:";
	print "<br>((($wordVector)))";
	print "<br><br><br>";

	$base64v=base64_encode($v);
	$call_id = uniqid();

	$REFINE_URL= "http://rodinsrc/refine" 
												."?sid=sid" 
												."&q=$q"
												."&v=$base64v" 			//possibly base64 coded
												."&w=$widget_id"		//Widget  id (see above)
												."&maxdur=$MAXDUR_REFINESINGLECALL_msec"    
												."&p=s" 						// s=syncrhonous call - default, a=asynchronous call))
												."&id=$call_id" // identifies « this » call – in case of non sinch processing))
												;	
EOU;

	print "<hr>REFINE URL:<br>";
	print "$REFINE_URL";

	


function cleanUpWordVector($wordVector)
	##########################################
{
	
	//dummy
	return $wordVector;

}


	
?>
