<?php

	$filename="u/FRIdbUtilities.php";
#######################################
	$max=10;
	for ($x=1;$x<=$max;$x++,$updir.="../")
	{ ;
		if (file_exists("$updir$filename")) 
		{ require_once("$updir$filename");break;}
	}
	#######################################
	include_once('../../app/root.php');
?>

<html>
<head>
<script type="text/javascript" src="../u/RODINutilities.js.php?skin=<?php print $RODINSKIN;?>" > </script>
<title> <?php echo strtoupper($RODINSEGMENT); ?> Calls RODINGUI/SRC </title>
</head>
<body>

<?php

	$quick		=$_REQUEST['quick']; if ($quick=='on') $QUICKSEL=' checked ';
	$user_id	=$_REQUEST['user_id']; 
	$cto		=$_REQUEST['cto']; if (!$cto)$cto="%";
	$cfrom		=$_REQUEST['cfrom']; if (!$cfrom)$cfrom="%";
	$shown		=$_REQUEST['shown']; if (!$shown)$shown=10;
	$sid		=$_REQUEST['sid'];
	$datasource	=$_REQUEST['datasource'];
	

	
	
	
	$QUERY_CALLS=<<<EOQ
		SELECT * 
FROM R_CALL 
WHERE `to` like '%$cto%' 
AND `from` like '%$cfrom%'
ORDER BY CALL_TIMESTAMP 
EOQ;

print "<form name='f'>".$FONTRESULTSEGMENT.strtoupper($RODINSEGMENT)." 
<input type='submit' name='cgo' value='(re)Load' title='Reload current datasets using your filter'>
 the last 
<input name='shown' value='$shown' size=2 align='right' onkeyup=submitonenter2(document.f,event) title='Constraint your search to the last $shown elements'>
<input name='cfrom' value='$cfrom' size=5 align='left' onkeyup=submitonenter2(document.f,event) title='Filter only datasets with `from`= \"$cfrom\"'>
<input name='cto' value='$cto' size=5 align='left' onkeyup=submitonenter2(document.f,event) title='Filter only datasets with `to`= \"$cto\"'>
SEM Calls for User
<input name='user_id' value='$user_id' size=1 align='right' onkeyup=submitonenter2(document.f,event) title='Constraint your search to calls made by this user'>
quickly <input type='checkbox' name='quick' title='quickly: Show only refine and v->srv' $QUICKSEL onChange='document.f.submit()'>

in reverse order (most recent first):</font></format>
<script type='text/javascript'>
document.f.shown.select();
document.f.shown.focus();
</script><br><br><br></font>";


	$DB = new RODIN_DB();
	$DBconn=$DB->DBconn;
	$TDOPTIONS=" align=left valign=top width=200 wrap:force ";

	print $QUERY_CALLS;

	$resultset= mysql_query($QUERY_CALLS);
	if ($resultset)
	{
	
		$numrows = mysql_num_rows($resultset);
		print " Numrows: ".$numrows;
		$n=0;
		while (($row = mysql_fetch_assoc($resultset)))
		{
			$n++;
			$result[]=$row;
			
		}
		
		
		if (count($result))
		$rows_reverse=array_reverse($result);
	}
	if (count($result))
	{	
	  $start=true;
		$n=0;

		foreach($rows_reverse as $row)
		{
			$n++;
			if ($n>=$shown) break; // soll erreicht
			else 
			{		
			$cid	=$row['cid'];
			$sid	=$row['sid'];
			$newsid	=$row['newsid'];
			$from	=$row['from'];
			$to		=$row['to'];
			$call_timestamp	=$row['call_timestamp'];
			$input	=$row['input'];
			$output	=$row['output'];
			$answer_timestamp =$row['answer_timestamp'];
			$show_dataset=true;
	
			############## INPUT ##############
			$inputs = explode('&',$input);
			$user_found=false;
			
			$Tin=$TinQuick="<table cellpadding=0 cellspacing=0>";
			foreach($inputs as $in)
			{
				$BOLD=$BOLDAUS='';
				$pair=explode('=',$in);
				
				$att=$pair[0];
				$val=$pair[1];
				
				if ($att=='v')
				{
					$val="[[".base64_decode($val)."]]";
				
					$BOLD='<b>';
					$BOLDAUS='</b>';
				}
				else if ($att=='action')
				{
					$BOLD="<b>$FONTGRAY";
					$BOLDAUS='</font></b>';
				}
				
				else if ($att=='user')
				{	
					$show_dataset=($val==$user_id)        ; // only for this user show the dataset!!
					$user_found=true;
				}
				else 	
				if ($att=='w')
				{
					$tab_id='';
					$WIDGETINFO=collect_widget_infos($val);
					$val.=" (".$WIDGETINFO[0]['name'].")";
				}
				
				if ($quick=='on' && ($att=='v' || $att=='action'))
					$TinQuick.="<tr><td align=right valign=top>$att:&nbsp;</td><td>$BOLD$val$BOLDAUS</td></tr>";
				$Tin.="<tr><td align=right valign=top>$att:&nbsp;</td><td>$BOLD$val$BOLDAUS</td></tr>";
			} // for
			$Tin.="</table>";
			$TinQuick.="</table>";
			
			if ($quick=='on') $Tin = $TinQuick;
			
			
			if (!$user_found)
			#Try to take user-id from sid ... .user_id
			{
				$show_dataset = preg_match("/.$user_id$/",$sid);
			}
			############## INPUT ##############


			############## OUTPUT (XML) ##############
			if ($output<>''
			&& !preg_match("/Operation timed out after/",$output) 
			&& !preg_match("/Network Access Message/",$output))
			{
				//print "<br>output: (($output))";
			
				$sxml_output= new SimpleXMLElement($output);
				$Tout=$ToutQuick="<table cellpadding=0 cellspacing=0>";
	
				foreach($sxml_output->children() as $att=>$val)
				{			
					$BOLD=$BOLDAUS='';
					if ($att=='v' || $att=='srv')
					{
						$val="[[".base64_decode($val)."]]";
					
						if ($att=='srv')
						{
							$BOLD='<b>';
							$BOLDAUS='</b>';
						}
					}
					
					else 	
					if ($att=='w' )
					{
						$tab_id='';
						$WIDGETINFO=collect_widget_infos($val);
						$val.=" (".$WIDGETINFO[0]['name'].")";
						}
					if ($quick=='on' && $att=='srv')	
								$ToutQuick.="<tr><td align=right valign=top>$att:&nbsp;</td><td>$BOLD$val$BOLDAUS</td></tr>";
	
					$Tout.="<tr><td align=right valign=top>$att:&nbsp;</td><td>$BOLD$val$BOLDAUS</td></tr>";
					
					
				} // for
				
				$Tout.="</table>";
				$ToutQuick.="</table>";
				if ($quick=='on') $Tout = $ToutQuick;
				
				//print "<br>$Tout";
			}
			}
			############## OUTPUT (XML) ##############


		
			if ($start)
			{
				$start=false;
print <<<EOP
	<table  cellpadding=2 cellspacing=0 border=0>
		<tr>
			<td align=left>$FONTLABEL n 		$ENDFONTRESULT</td>
			<td align=left>$FONTLABEL call t	$ENDFONTRESULT</td>
			<td align=left>$FONTLABEL answer t 	$ENDFONTRESULT</td>
			<td align=left>$FONTLABEL from 		$ENDFONTRESULT</td>
			<td align=left>$FONTLABEL to 		$ENDFONTRESULT</td>
			<td align=left>$FONTLABEL input 	$ENDFONTRESULT</td>
			<td align=left>$FONTLABEL output 	$ENDFONTRESULT</td>
			<td align=left>$FONTLABEL cid 		$ENDFONTRESULT</td>
			<td align=left>$FONTLABEL sid 		$ENDFONTRESULT</td>
			<td align=left>$FONTLABEL newsid 	$ENDFONTRESULT</td>
		</tr>
		<tr height=2><td colspan=999><hr></td></tr>
EOP;
  	} //start
			
			if ($show_dataset)
			print<<<EOP
					<tr>
						<td align=left $TDOPTIONS>$FONTLABEL $n 				$ENDFONTRESULT</td>
						<td align=left $TDOPTIONS>$FONTGUI $call_timestamp 		$ENDFONTRESULT</td>
						<td align=left $TDOPTIONS>$FONTSRC $answer_timestamp 	$ENDFONTRESULT</td>
						<td align=left $TDOPTIONS>$FONTGUI $from 				$ENDFONTRESULT</td>
						<td align=left $TDOPTIONS>$FONTSRC $to 					$ENDFONTRESULT</td>
						<td align=left $TDOPTIONS>$FONTGUI $Tin 				$ENDFONTRESULT</td>
						<td align=left $TDOPTIONS>$FONTSRC $Tout 				$ENDFONTRESULT</td>
						<td align=left $TDOPTIONS>$FONTGRAY $cid 				$ENDFONTRESULT</td>
						<td align=left $TDOPTIONS>$FONTGRAY $sid 				$ENDFONTRESULT</td>
						<td align=left $TDOPTIONS>$FONTGRAY $newsid 			$ENDFONTRESULT</td>
					</tr>	
					<tr height=2><td colspan=999><hr></td></tr>
EOP;
			}
		}
		else print " no results in table for this filter";	
	
print <<<EOP
	$ENDFONTRESULT</table>
EOP;
	
	









	
?>
</table>
</div>	
</body>
</html>
