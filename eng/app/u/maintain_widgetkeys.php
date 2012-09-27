<?php
/*
	maintain_widgetkeys.php
	Autor: Fabio Ricci (fabio.fr.ricci@hesge.ch)
	HEG (Geneva)
	Module for maitenance of widgetskeys
*/		

$availableSegmentsRE="eng|heg|st|p|d|x|f";

$SCRIPTPATH = dirname($_SERVER['PHP_SELF']); // /rodinposh/st/e/w
if (preg_match("/\/($availableSegmentsRE)\//",$_SERVER['PHP_SELF'],$match))
			$SEGMENT = $match[1];


###########################################
###########################################
###########################################

	{
		require_once("FRIdbUtilities.php");
		$FORMMETHOD='get';

		//if (!$ROOT) print "NOROOT!";
		//print "<br>RODINSEGMENT: $RODINSEGMENT";


		print <<<EOP
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>WIDGETKEYS Management Tool ($RODINSEGMENT)</title>
</head>
EOP;

		$MARKCOLOR="#99aa99";
		$STYLEFONTRESULT=" style=\"color:black;font-size:normal;font-weight:bold\" ";
		$STYLEFONTGREEN=" style=\"color:green;font-size:normal;font-weight:bold\" ";
		$STYLEFONTWHITE=" style=\"color:white;font-size:normal;font-weight:bold\" ";
		$STYLEFONTANTRAZIT=" style=\"color:#555555;font-size:normal;font-weight:bold\" ";
		$FONTRESULT="<font $STYLEFONTRESULT>";
		$FONTRESULTGREEN="<font $STYLEFONTGREEN>";
		$FONTANTRAZIT="<font $STYLEFONTANTRAZIT>";
		$FONTGUI=$FONTRESULT;
		$FONTSRC=$FONTANTRAZIT;
		$FONTWHITE=$STYLEFONTWHITE;
		
		
		$thisSCRIPT = $_SERVER['SCRIPT_NAME'];


		$DB = new RODIN_DB();
		$DBconn=$DB->DBconn;
	
		
		$adminusername=$_REQUEST['adminusername']; 
		$adminuserpass=$_REQUEST['adminuserpass']; //defined? User
		$u=$_REQUEST['u']; //defined? User
		$showuser=$_REQUEST['showuser']; //User to show info for
		$a=$_REQUEST['a']; //defined? action !!
		$a=$_REQUEST['a']; //defined? action !!
		$new=$_REQUEST['new']; //defined? UPDATE values!!
		$ID=$_REQUEST['id'];
		$funame=f.uniqid();
		$databaseadjust=$_REQUEST['databaseadjust']; //defined? User
		
		
		$RODINROOTstr					= substr($RODINROOT,1,strlen($RODINROOT));
		$RODINADMIN_HOST			= $_SERVER["HTTP_HOST"];
		$ADMINDBBASENAME			= limitusernamelength($RODINROOTstr."_root_");
		$RODINADMIN_DBNAME 		= limitusernamelength($RODINROOTstr."_root_".$RODINSEGMENT);
		$RODINADMIN_USERNAME 	= limitusernamelength($RODINROOTstr."_root_".$RODINSEGMENT);
		$RODINADMIN_USERPASS 	= strrev($RODINADMIN_USERNAME);
			
		if (! $DBconn = mysql_connect($RODINADMIN_HOST,$RODINADMIN_USERNAME,$RODINADMIN_USERPASS))
		{
				fontprint("Could not connect to database with given USERS. $RODINADMIN_USERNAME/$RODINADMIN_USERPASS",'red');
			exit;
		}
		//print "<br>SQL:$SQL";
		if (!mysql_select_db($RODINADMIN_DBNAME))
		{
			fontprint("Could not select database $RODINADMIN_DBNAME",'red');
			exit;
		}	
		
		###############################
		#
		# If you do not have a VPN and you must update the db...
		# 
		if ($databaseadjust)
		{
			
			$QUERY="ALTER TABLE administration ADD mode VARCHAR( 20 ) NOT NULL COMMENT 'Call mode for this SRC: direct|segmented' AFTER Type;";
			print "<br>ADJUSTING DATABASE...";
			print "QUERY=$QUERY";
			$resultset= mysql_query($QUERY);
			$numrows = mysql_affected_rows();
			 
			fontprint("<br>$numrows affected rows: ".mysql_error($DBconn),'red');
			
			exit;
		}
		
		
		
		
		
		
		$TDOPTIONS=" style=\"white-space:nowrap\" ";
		
		
		if (!is_a_value($adminusername))
			$ASKUSER="<form name='$funame' method='$FORMMETHOD'>
<br>Change User:&nbsp;<input type='text' name='adminusername' value='$RODINADMIN_USERNAME'>
<br>Password:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='password' name='adminuserpass' value=''>
<br><input type='submit' value='                          connect                            '>
</form>";
		else
			$USERSPE=" ($RODINSEGMENT)";

print<<<EOP
<h2><a href="$thisSCRIPT?u=$u" title="Reload this page">{$FONTANTRAZIT}WidgetKey Management Tool $USERSPE</font></a></h2> 
$ASKUSER
EOP;
	
		if ($ASKUSER) 
		{
			print "</body></html>";
			exit; // no sense to cont.
			
		}	
	
		
		if ($a=='modify' || $a=='duplicate' || $a=='delete') 
		{
			//Use special access coming from user
			$RODINADMIN_USERNAME 	= $adminusername;
			$RODINADMIN_USERPASS 	= $adminuserpass;
				
			if (! $DBconn = mysql_connect($RODINADMIN_HOST,$RODINADMIN_USERNAME,$RODINADMIN_USERPASS))
			{
					fontprint("Could not connect to database with given USERS. $RODINADMIN_USERNAME/$RODINADMIN_USERPASS",'red');
				exit;
			}
			//print "<br>SQL:$SQL";
			if (!mysql_select_db($RODINADMIN_DBNAME))
			{
				fontprint("Could not select database $RODINADMIN_DBNAME",'red');
				exit;
			}	
		}
		
		
			
		if ($a=='modify' || $a=='duplicate') 
		{
				//print "<h1>update values for ID=$ID for user $forRODINuser</h1>";
			
			$POS=$_REQUEST['POS'];
			$S=$_REQUEST['S'];
			$name=$_REQUEST['name'];
			$active=$_REQUEST['active'];
			$forRODINuser=$_REQUEST['user']?$_REQUEST['user']:0;
			$value=$_REQUEST['value'];
			$type=$_REQUEST['type'];
			$purpose=$_REQUEST['purpose'];
			$comment=$_REQUEST['comment'];
			
			#foreach bool value: do the following:
			if (!is_a_value($active)) $active=0;
			else if ($active=='on') $active=1;
			if (!is_a_value($S)) $S=0;
			else if ($S=='on') $S=1;
			
			
			if($a=='modify')
			{
				$SQL_UPDATE=
				"UPDATE administration
				 SET 	name='$name',
						active=$active,
						S=$S,
						forRODINuser=$forRODINuser,
						value='$value',
						type='$type',
						purpose='$purpose',
						comment='$comment',
						Modified=CURRENT_TIMESTAMP
				 WHERE id=$ID
				";
				
				//print $SQL_UPDATE;
				
				$resultset= mysql_query($SQL_UPDATE,$DBconn);
				$numrows = mysql_affected_rows();
				if ($numrows<>1) 
					fontprint("<br>Problem updating record with id=$ID $numrows lines affected from ($SQL_UPDATE)<br><br>"
					."PLEASE CHECK YOUR UPDATE RIGHT ON THE DATABASE $RODINADMIN_DBNAME WITH THE GIVEN USER",'red');	
			}
			else if ($a=='duplicate') 
			{
				$SQL_DUP=
				"INSERT INTO administration
				 				(POS,name,forRODINuser,value,S,type,purpose,comment,Modified) 
				 SELECT POS,name,forRODINuser,value,S,type,purpose,comment,CURRENT_TIMESTAMP 
				 FROM administration WHERE id=$ID
				";
				
				//print $SQL_DUP;
				
				$resultset= mysql_query($SQL_DUP);
				$numrows = mysql_affected_rows();
				if ($numrows<>1) 
					fontprint("<br>Problem duplicating record with id=$ID<br>$numrows lines affected from ($SQL_DUP)<br><br>"
					."PLEASE CHECK YOUR INSERT RIGHT ON THE DATABASE $RODINADMIN_DBNAME WITH THE GIVEN USER",'red');	
				} // duplicate
		} // END MODIFY
	

		#####################################
		if (isset($new)) 
		{
			//print "<h1>Create New Interface record for user $forRODINuser</h1>";
			$uniq=uniqid();
			$SQL="INSERT INTO administration 
						() VALUES() ";
			$resultset= mysql_query($SQL);
			$numrows = mysql_affected_rows();
			if ($numrows<>1) 
				fontprint("<br>Problem creating new record <br><br>"
				."PLEASE CHECK YOUR INSERT RIGHT ON THE DATABASE $RODINADMIN_DBNAME WITH THE GIVEN USER",'red');			
			
		}
		

		#####################################
		if ($a=='delete') 
		{
			//print "<h1>delete ($Name) Interface record id=$ID</h1>";
			
			$SQL="DELETE FROM administration 
						WHERE id=$ID";
						
						
			//print "SQL: $SQL";			
						
			$resultset= mysql_query($SQL);
			$numrows = mysql_affected_rows();
			if ($numrows<>1) 
				fontprint("<br>Problem deleting record id=$ID! using ($SQL)<br><br>"
					."PLEASE CHECK YOUR DELETE RIGHT ON THE DATABASE $RODINADMIN_DBNAME WITH THE GIVEN USER",'red');			
		}


		$NewINTERFACEBUTTON= 
		"<form action=\"$thisSCRIPT\" method='$FORMMETHOD'>		
		 <input type='hidden' name='u' value='$u'>
		 <input type='hidden' name='adminusername' value='$adminusername'>
		 <input type='hidden' name='adminuserpass' value='$adminuserpass'>
		 
		 See only records for user: <input title='Type the user number you want to see his records' type='text' name='showuser' size=1 value='$showuser' style=\"text-align:center\">
		</form>";


# Show existing Interfaces:

if ($showuser<>'') $USER_EINSCHRAENKUNG=" where forRODINuser=$showuser ";
		
$SQL="
SELECT * from administration  $USER_EINSCHRAENKUNG ORDER BY POS;
";

		if (! $resultset= mysql_query($SQL))
		{
			fontprint("Problem on ($SQL)",'red');
		}
		$numrows = mysql_num_rows($resultset);
		$start=1;
		
		if ($numrows)
		while (($row = mysql_fetch_assoc($resultset)))
		{	
			$n++;
			if ($start)
			{
print <<<EOP
	$NewINTERFACEBUTTON &nbsp; 
	<br>
	<table  cellpadding=0 cellspacing=0 border=0>
		<tr>
EOP;

				print "
					<td align=left>
					</td>";
				foreach($row as $attr=>$value)
				{
					if ($attr <> 'ID')
					{
						$start=false;
						if ($firstcol)
						{
							$firstcol=false;
							$FONT=$FONTGUI;
						}
						else
						$FONT=$FONTSRC;
						
						if ($attr=='forRODINuser') $attr='user';
						print "<td align=left>$FONT $attr $ENDFONTRESULT</td>";
					}
					else
					{
						
						print "<td align=left>$FONT ($attr) $ENDFONTRESULT</td>";
					}
				} //for
			
print <<<EOP
		</tr>
EOP;
		} // start		

				if ($row['active']) 
					$TRSTYLES="bgcolor=$COLOR_RODINSEGMENT";
				else 
				$TRSTYLES='';
				
				
							
				

				print "<tr id='R$n' $TRSTYLES>
							 <form name=\"f$n\" action=\"\" method='$FORMMETHOD'>
							 <input type='hidden' name='a'>
							 <input type='hidden' name='showuser' value='$showuser'>
							 
		 <input type='hidden' name='adminusername' value='$adminusername'>
		 <input type='hidden' name='adminuserpass' value='$adminuserpass'>
							 
							 <input type='hidden' name='u' value='$u'>
							 ";
	
				$DeleteDuplicateTHISRECORD=
				"<input type='button' name='d' value='X' title='Delete this WidgetKey record'
					onClick=\"if (confirm('Do you REALLY want to delete this WidgetKey record record?')) 
						{f$n.a.value='delete'; f$n.submit();}\">
				 <input type='button' name='c' value='+' style=\"background-color:$MARKCOLOR;font-weight:bold;padding:0px\" title='Duplicate this WidgetKey record'
					onClick=\"f$n.a.value='duplicate'; f$n.submit();\">";
	
	
				$firstcol=true;

				print "
					<td align=left nowrap>
						$DeleteDuplicateTHISRECORD
					</td>";
				##############################
				##############################
				foreach($row as $attr=>$value)
				##############################
				##############################
				{
				
					if ($attr == 'ID')
					{
						print "<input type='hidden' name='id' value='$value'>";
						$attr=' ID ';
					}
					
					{
				
						$start=false;
						$DISABLED='';
						$CHECKED='';
						$SAVE_ACTION='';
						
						if ($row['S']) 
							$INPUTOPTIONS=" style=\"color:red\" ";
						else
							$INPUTOPTIONS='';
						
						if ($firstcol)
						{
							$firstcol=false;
						}
						else ;
						
						
						if ($attr == 'forRODINuser')
						{		
							$attr='user';
							$INPUTOPTIONS="style=\"background-color:$COLOR_RODINSEGMENT;color:white\"";
							
							$user_info=fri_get_user_info($value);
					 		$userlangname=$user_info['long_name'];
					 		$username=$user_info['username'];
					 		$SIZE=1;
						}
			
					
						if ($attr == 'active' || $attr == 'S')
							$TYPE='checkbox';
						else
						if ($attr == 'AuthPasswd')
							//$TYPE='password';
							;
						else 
							$TYPE='text';
						
						if ( $attr=='name')  
							$SIZE=18;
						else if ($attr=='value' 
									)
							$SIZE=40;
						else if ($attr=='comment'
									)
							$SIZE=20;
							else if ($attr=='type'
								)
						$SIZE=6;
						else if ($attr=='purpose'
								)
						$SIZE=7;
						else if ($attr=='Modified'
								)
						$SIZE=16;
						else 
							$SIZE=3;
						
						
						if ($attr=='type')
						{	
							$SIZE=3;
						}

						print "<td align=left $TDOPTIONS>";
						if ($TYPE=='text' || $TYPE=='password' )
						{
							$SAVE_ACTION=
							"onChange=\"f$n.a.value='modify';f$n.submit();\"
							";
							print "
								<input type='$TYPE' value='$value' name='$attr' title='change $attr ?' $DISABLED size=\"$SIZE\" $SAVE_ACTION $INPUTOPTIONS>	
							";
							if ($attr=='forRODINuser')
								print "
								<label><font $FONTWHITE> $userlangname </font></label>
								";
						}
						else if ($TYPE=='checkbox')
						{
						
							if ($value==1)
							{
								$CHECKED=' checked ';
							}
							$SAVE_ACTION=
							"onChange=\"f$n.a.value='modify';f$n.submit();\"
							";
							print "
								<input type='$TYPE' name='$attr' title='change $attr ?' $DISABLED $CHECKED $SAVE_ACTION>	
							";
						} 	
							
						print "</td>";
					} // DI	
						
				} //for
				
				print "
					</form>
					<!--tr height=2><td colspan=999><hr size=1></td>
					</tr-->";
		} // while
		
		else print "No WidgetKey record defined <br>";	
	
	

		print 
		"</table>
		 <br>$NewINTERFACEBUTTON";
	
	
	
		print<<<EOP
</body>
</html>
EOP;

} // access_granted
?>
