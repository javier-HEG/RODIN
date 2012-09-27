
<?php


	include_once("../u/FRIdbUtilities.php");
    
	
	$sid				=$_REQUEST['sid'];
	$datasource	=$_REQUEST['datasource'];
	$rescnt			=$_REQUEST['rescnt'];    // the xth document of the result
	$recordcnt	=$_REQUEST['recordcnt']; // the nth record to the document
	$xpointercnt=$_REQUEST['xpointer']; // the nth record to the document

	$STYLEFONTRESULT=" style=\"color:black;font-size:normal;font-weight:bold\" ";
	$FONTRESULT="<font $STYLEFONTRESULT>";

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
	
	$tags = collect_tags($datasource);
	
	
	
	print <<<EOP
	<html>
	<head>
	<title> SEM $sid-$datasource-$rescnt</title>
	</head>
	<body>
	<hr>
	
	
	<table border=0>
	<tr><td colspan=2><h1> Document data from DB avalaible for search refinement</h1></td><td>
		<img src="/$RODINUTILITIES_GEN_URL/images/mandala_pferd.gif" width=200 height=200 />
	</td></tr>
	<tr><td align="right" valign="middle"><b>Widget </b></td><td>W= $datasource</td><td/></tr>
	<tr><td align="right" valign="middle"><b>Tags </b></td><td>&nbsp;T&nbsp;= $tags</td><td/></tr>
	<tr><td align="right" valign="middle"><b>Query	</b></td><td>q&nbsp;&nbsp;= $q</td><td/></tr>
	<tr><td align="right" valign="middle"><b>sid	</b></td><td>&nbsp;&nbsp;&nbsp;&nbsp;=  $sid</td><td/></tr>
	<tr><td align="right" valign="middle"><b>Document </b></td><td>&nbsp;d&nbsp;= $rescnt</td>
	$RECORDINFO
	
	</tr>
	</table>
	<hr>
	<h3> <a href='' title='Visualize records to this document' onclick="var x=document.getElementById('semshow');if(x.style.visibility=='visible') {x.style.visibility='hidden';} else  {x.style.visibility='visible';} return false;">Available data in DB</a> </h3>
	
	<div id='semshow' style="visibility:hidden">
EOP;
	
	
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

print <<<EOP
	<table  cellpadding=2 cellspacing=0 border=0>
		<tr>
			<td>$FONTLABEL Query: $ENDFONTLABEL</td><td colspan=999>$FONTRESULT $q $ENDFONTRESULT</td>
		</tr>
		<tr height=50><td colspan=999>$FONTRESULT Available records in DB:</td></tr>
		<tr>
			<td align=left>$FONTLABEL xpointer 	$ENDFONTRESULT</td>
			<td align=left>$FONTLABEL node 		$ENDFONTRESULT</td>
			<td align=left>$FONTLABEL attribute $ENDFONTRESULT</td>
			<td align=left>$FONTLABEL type 		$ENDFONTRESULT</td>
			<td align=left>$FONTLABEL value 	$ENDFONTRESULT</td>
			<td align=left>$FONTLABEL url 		$ENDFONTRESULT</td>
			<td align=left>$FONTLABEL visible 	$ENDFONTRESULT</td>
			<td align=left>$FONTLABEL follow 	$ENDFONTRESULT</td>
		</tr>
		<tr height=2><td colspan=999><hr></td></tr>
EOP;


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


					$XLEVEL=compute_XPointerLevel($xpointer);
					
					if ($XLEVEL==1)
					{
						$LEVEL1_XPOINTER = $xpointer;
					
						if ($LEVEL1_XPOINTER <> $OLD_LEVEL1_XPOINTER)
						{
							$xrescnt++;
						
							//print "<br> xrescnt=$xrescnt rescnt=$rescnt";
						
							$OLD_LEVEL1_XPOINTER=$LEVEL1_XPOINTER;
							if ($xrescnt == $rescnt)
							{
								//print "<br> SWITCH bei xpointer=$xpointer xrescnt=$xrescnt";
								$showres=true;			
							}
							else if ($xrescnt > $rescnt)
							{
								//print "<br> STOP bei $xrescnt ";
								$stop=true;
								break;
							}						
							//print "<br> sep  rowcnt=$rowcnt";
						} // <>
					} // switch			
					
					if ($stop) ;
					else
					{
						if ($showres)
						{
							if (is_a_value($recordcnt))
							{
									$visualize = ($xpointercnt == $xpointer);
							}
							
							if($type=='base64html')
								$url=base64_decode($url);
						
							if ($visualize) 
							{
								$STYLEFONTRESULT=" style=\"color:blue;font-size:normal;font-weight:bold\" ";
								$FONTRESULT="<font $STYLEFONTRESULT>";
							}
							
							else if (is_a_value($recordcnt) && !$visualize)
							{
								$STYLEFONTRESULT=" style=\"color:#aaaaaa;font-size:normal;font-weight:bold\" ";
								$FONTRESULT="<font $STYLEFONTRESULT>";
							}
							else if (!is_a_value($recordcnt))
							{
								$STYLEFONTRESULT=" style=\"color:black;font-size:normal;font-weight:bold\" ";
								$FONTRESULT="<font $STYLEFONTRESULT>";
							}
							
						
						
					print<<<EOP
							<tr>
								<td align=left valign=top>$FONTRESULT $xpointer 	$ENDFONTRESULT</td>
								<td align=left valign=top>$FONTRESULT $node 		$ENDFONTRESULT</td>
								<td align=left valign=top>$FONTRESULT $attribute 	$ENDFONTRESULT</td>
								<td align=left valign=top>$FONTLABEL $type 		$ENDFONTRESULT</td>
								<td align=left valign=top>$FONTRESULT $value 		$ENDFONTRESULT</td>
								<td align=left valign=top>$FONTRESULT $url 		$ENDFONTRESULT</td>
								<td align=left valign=top>$FONTRESULT $visible 	$ENDFONTRESULT</td>
								<td align=left valign=top>$FONTRESULT $follow 		$ENDFONTRESULT</td>
							</tr>	
							<tr height=2><td colspan=999><hr></td></tr>
EOP;
						} // show
										
					}

				}

			} // $resultset
			else print " no results for $QUERY_RESULTS";	
	
print <<<EOP
	$ENDFONTRESULT</td></tr>
EOP;
	
	









	
?>
</table>
</div>	
</body>
</html>
