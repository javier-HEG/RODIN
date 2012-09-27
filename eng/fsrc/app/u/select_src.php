<?php
/*
$ADVICE="$Please contact the system administrator.";

if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="RODIN"');
    header('HTTP/1.0 401 Unauthorized');
    echo "You need a valid authorisation to use this page. $ADVICE";
    exit;
} else {
    
		$user=$_SERVER['PHP_AUTH_USER'];
		$passwd=$_SERVER['PHP_AUTH_PW'];
		
		$expected_user="tu007";
		$expected_user_id=2; //fabio
		$expected_passwd="SaTrinxxa06";
		
    //echo "<p>You ($user) entered {$passwd} as your password.</p>";
		
		if ($user==$expected_user && $passwd==$expected_passwd)
		{
			//print "<br>Weiter mit CODE";
			$access_granted=true;
		}
		else
		{
			$user=$_SERVER['PHP_AUTH_USER'];
			$passwd=$_SERVER['PHP_AUTH_PW'];
		
			print "$user/$passwd Wrong Authorization - $ADVICE";
		}
		
		
ALTER TABLE `src_interface` CHANGE `Path` `Path_Refine` VARCHAR( 512 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'Refine Path of Interface from server';
ALTER TABLE `src_interface` CHANGE `Servletname` `Servlet_Refine` VARCHAR( 80 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name of the Refine Servlet or program serving';
ALTER TABLE `src_interface` ADD `Path_Start` VARCHAR( 512 ) NOT NULL AFTER `Port` ,
ADD `Servlet_Start` VARCHAR( 80 ) NOT NULL AFTER `Path_Start`; 		
ALTER TABLE `src_interface` ADD `Path_Test` VARCHAR( 512 ) NOT NULL AFTER `Servlet_Refine` ,
ADD `Servlet_Test` VARCHAR( 80 ) NOT NULL AFTER `Path_Test` ;
ALTER TABLE `src_interface` DROP `Type` ;
ALTER TABLE `src_interface` ADD `POS` INT NOT NULL COMMENT 'Position for display - this value has only an ordering effect on this page';
ALTER TABLE `src_interface` ADD `mode` VARCHAR( 20 ) NOT NULL COMMENT 'Call mode for this SRC: direct|segmented' AFTER `Type` 
*/		

$BASIS="../../../app/u/";
			
$_SERVER['PHP_SELF']=$BASIS; //Damit die sonstigen Progs Ihren Ort wiederfinden
if (!chdir($BASIS)) print "<br>PROBLEM CHANGING DIR TO $BASIS";
?>
<?php
###########################################
###########################################
###########################################

	{
		require_once("FRIdbUtilities.php");
		$FORMMETHOD='POST';

		//if (!$ROOT) print "NOROOT!";
		//print "<br>RODINSEGMENT: $RODINSEGMENT";


		print <<<EOP
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>SRC Magement Tool ($RODINSEGMENT)</title>
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
	

		$u=$_REQUEST['u']; //defined? User
		$showuser=$_REQUEST['showuser']; //User to show info for
		$a=$_REQUEST['a']; //defined? action !!
		$a=$_REQUEST['a']; //defined? action !!
		$new=$_REQUEST['new']; //defined? UPDATE values!!
		$ID=$_REQUEST['id'];
		$funame=f.uniqid();
		$databaseadjust=$_REQUEST['databaseadjust']; //defined? User
		
		
		###############################
		#
		# If you do not have a VPN and you must update the db...
		# 
		if ($databaseadjust)
		{
			
			$QUERY="ALTER TABLE src_interface ADD mode VARCHAR( 20 ) NOT NULL COMMENT 'Call mode for this SRC: direct|segmented' AFTER Type;";
			print "<br>ADJUSTING DATABASE...";
			print "QUERY=$QUERY";
			$resultset= mysql_query($QUERY);
			$numrows = mysql_affected_rows();
			 
			fontprint("<br>$numrows affected rows: ".mysql_error($DBconn),'red');
			
			exit;
		}
		
		
		
		
		
		
		$TDOPTIONS=" style=\"white-space:nowrap\" ";
		
		
		if (!is_a_value($u))
			$ASKUSER="<form name='$funame' method='$FORMMETHOD'>
Please specify a valid RODIN user id: <input type='text' name='u' size='1' title='You have to provide a RODIN user id!' onChange=\"$funame.submit()\"></form>";
		else
			$USERSPE=" (for $RODINSEGMENT/$u)";

print<<<EOP
<h2><a href="$thisSCRIPT?u=$u" title="Reload this page">{$FONTANTRAZIT}SRC Management Tool $USERSPE</font></a></h2> 
$ASKUSER
EOP;
	
		if ($ASKUSER) 
		{
			print "</body></html>";
			exit; // no sense to cont.
			
		}	
	
			
		if ($a=='modify' || $a=='duplicate') 
		{
			//print "<h1>update values for ID=$ID for user $forRODINuser</h1>";
			
			$POS=$_REQUEST['POS'];
			$Name=$_REQUEST['Name'];
			$Activated=$_REQUEST['Activated'];
			$forRODINuser=$_REQUEST['forRODINuser'];
			$AuthUser=$_REQUEST['AuthUser'];
			$AuthPasswd=$_REQUEST['AuthPasswd'];
			$Protocol=$_REQUEST['Protocol'];
			$Server=$_REQUEST['Server'];
			$Port=$_REQUEST['Port'];
			$Path_Refine=$_REQUEST['Path_Refine'];
			$Path_Start=$_REQUEST['Path_Start'];
			$Path_Test=$_REQUEST['Path_Test'];
			$Servlet_Start=$_REQUEST['Servlet_Start'];
			$Servlet_Refine=$_REQUEST['Servlet_Refine'];
			$Servlet_Test=$_REQUEST['Servlet_Test'];
			$Type=$_REQUEST['Type'];
			$mode=$_REQUEST['mode'];
			
			if (!is_a_value($Port)) $Port=0;
			if (!is_a_value($Activated)) $Activated=0;
			else if ($Activated=='on') $Activated=1;
			
			
			if($a=='modify')
			{
				$SQL_UPDATE=
				"UPDATE src_interface
				 SET 	Name='$Name',
						Activated=$Activated,
						forRODINuser='$forRODINuser',
						AuthUser='$AuthUser',
						AuthPasswd='$AuthPasswd',
						Type='$Type',
						mode='$mode',
						Protocol='$Protocol',
						Server='$Server',
						Port=$Port,
						POS=$POS,
						Path_Test='$Path_Test',
						Path_Refine='$Path_Refine',
						Path_Start='$Path_Start',
						Servlet_Test='$Servlet_Test',
						Servlet_Refine='$Servlet_Refine',
						Servlet_Start='$Servlet_Start',
						Modified=CURRENT_TIMESTAMP
				 WHERE id=$ID
				";
				
				//print $SQL_UPDATE;
				
				$resultset= mysql_query($SQL_UPDATE);
				$numrows = mysql_affected_rows();
				if ($numrows<>1) 
					fontprint("<br>Problem updating record with id=$ID<br>",'red');	
			}
			else if ($a=='duplicate') 
			{
				$SQL_DUP=
				"INSERT INTO src_interface
				 (POS,Name,forRODINuser,AuthUser,AuthPasswd,Protocol,Server,Port,Type,mode,Path_Start,Servlet_Start,Path_Refine,Path_Test,Servlet_refine,Servlet_Test,Modified) 
				 SELECT POS,Name,forRODINuser,AuthUser,AuthPasswd,Protocol,Server,Port,Type,mode,Path_Start,Servlet_Start,Path_Refine,Path_Test,Servlet_refine,Servlet_Test,CURRENT_TIMESTAMP 
				 FROM src_interface WHERE id=$ID
				";
				
				//print $SQL_DUP;
				
				$resultset= mysql_query($SQL_DUP);
				$numrows = mysql_affected_rows();
				if ($numrows<>1) 
					fontprint("<br>Problem duplicating record with id=$ID<br>$numrows lines affected",'red');	
				} // duplicate
		} // END MODIFY
	

		#####################################
		if (isset($new)) 
		{
			//print "<h1>Create New Interface record for user $forRODINuser</h1>";
			$uniq=uniqid();
			$SQL="INSERT INTO src_interface 
						() VALUES() ";
			$resultset= mysql_query($SQL);
			$numrows = mysql_affected_rows();
			if ($numrows<>1) 
				fontprint("<br>Problem creating new record ",'red');			
			
		}
		

		#####################################
		if ($a=='delete') 
		{
			//print "<h1>delete ($Name) Interface record id=$ID</h1>";
			
			$SQL="DELETE FROM src_interface 
						WHERE id=$ID";
						
						
			//print "SQL: $SQL";			
						
			$resultset= mysql_query($SQL);
			$numrows = mysql_affected_rows();
			if ($numrows<>1) 
				fontprint("<br>Problem deleting record id=$ID!<br>",'red');
		}


		$NewINTERFACEBUTTON= 
		"<form action=\"$thisSCRIPT\" method='$FORMMETHOD'>		
		 <input type='hidden' name='u' value='$u'>
		 See only records for user: <input title='Type the user number you want to see his records' type='text' name='showuser' size=1 value='$showuser' style=\"text-align:center\">
		</form>";


# Show existing Interfaces:

if ($showuser<>'') $USER_EINSCHRAENKUNG=" where forRODINuser='$showuser' ";
		
$SQL="
SELECT * from src_interface  $USER_EINSCHRAENKUNG ORDER BY POS;
";

		
		//print "<br>SQL:$SQL";

		$resultset= mysql_query($SQL);
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
	<table  cellpadding=2 cellspacing=0 border=0>
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
						print "<td align=left>$FONT $attr $ENDFONTRESULT</td>";
					}
					else
						print "<td align=left>$FONT ($attr) $ENDFONTRESULT</td>";
					
				} //for
			
print <<<EOP
		</tr>
		<tr height=2><td colspan=999><hr size='1'></td></tr>
EOP;
		} // start		

				if ($row['Activated']) 
					$TRSTYLES="bgcolor=$COLOR_RODINSEGMENT";
				else 
				$TRSTYLES='';
				
			
				

				print "<tr id='R$n' $TRSTYLES>
							 <form name=\"f$n\" action=\"\" method='$FORMMETHOD'>
							 <input type='hidden' name='a'>
							 <input type='hidden' name='showuser' value='$showuser'>
							 
							 <input type='hidden' name='u' value='$u'>
							 ";
	
				$DeleteDuplicateTHISRECORD=
				"<input type='button' name='d' value='X' title='Delete this SRC interface record'
					onClick=\"if (confirm('Do you REALLY want to delete this SRC interface record?')) 
						{f$n.a.value='delete'; f$n.submit();}\">
				 <input type='button' name='c' value='+' style=\"background-color:$MARKCOLOR;font-weight:bold;padding:0px\" title='Duplicate this SRC interface record'
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
						$INPUTOPTIONS='';
						
						if ($firstcol)
						{
							$firstcol=false;
						}
						else ;
						
						
						if ($attr == 'forRODINuser')
						{		
							$user_info=fri_get_user_info($value);
					 		$userlangname=$user_info['long_name'];
					 		$username=$user_info['username'];
					 		$SIZE=1;
						}
						if ($attr == 'Created' || $attr == 'Modified')
							$DISABLED=' disabled ';
						
						if ($attr == 'Activated')
							$TYPE='checkbox';
						else
						if ($attr == 'AuthPasswd')
							//$TYPE='password';
							;
						else 
							$TYPE='text';
						
						if ( $attr=='Created' 
							|| $attr=='Modified')  
							$SIZE=18;
						else if ($attr=='AuthUser' 
									|| $attr=='AuthPasswd' 
									|| $attr=='Servlet_Start'
									|| $attr=='Servlet_Refine'
									)
							$SIZE=10;
						else if ($attr=='Servlet_Test'
								)
						$SIZE=28;
						else if ($attr=='Server'
									|| $attr=='Name') 
							$SIZE=12;
						else if ($attr=='Path_Start'
							|| $attr=='Path_Refine'
							|| $attr=='Path_Test'
							)
							$SIZE=60;
						else 
							$SIZE=3;
						
						
						if ($attr=='Type' || $attr=='mode')
						{	
							$INPUTOPTIONS="style=\"background-color:$COLOR_RODINSEGMENT;color:white\"";
							if ($attr=='Type') $SIZE=3;
							if ($attr=='mode') $SIZE=12;
							
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
					<tr height=2><td colspan=999><hr size=1></td>
					</tr>";
		} // while
		
		else print "No SRC interfaces defined <br>";	
	
	

		print 
		"</table>
		 <br>$NewINTERFACEBUTTON";
	
	
	
		print<<<EOP
</body>
</html>
EOP;

} // access_granted
?>

