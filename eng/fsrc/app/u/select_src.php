<html>
	<head></head>
	<body>
		
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
ADD `sparql_endpoint` VARCHAR( 80 ) NOT NULL AFTER `Path_Test` ;
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
		$FONTFILTER="<font Style=\"color:blue\">";
		switch ($RODINSEGMENT)
		{
			case 'eng':	$FONTSRC="<font Style=\"color:green\">";  break;
			case 'xxl':	$FONTSRC="<font Style=\"color:darkblue\">";  break;
			case 'x':		$FONTSRC="<font Style=\"color:#f33\">";  break;
			case 'd':		$FONTSRC="<font Style=\"color:#aaf\">";  break;
			case 'st':	$FONTSRC="<font Style=\"color:red\">";  break;
			case 'p':		$FONTSRC="<font Style=\"color:blue\">";  break;
			default:		$FONTSRC=$FONTANTRAZIT;  break;
		}
		$FONTGUI = $FONTSRC;
		$FONTWHITE=$STYLEFONTWHITE;
		
		
		$thisSCRIPT = $_SERVER['SCRIPT_NAME'];


		$DB = new RODIN_DB();
		$DBconn=$DB->DBconn;
		$filter=trim($_REQUEST['filter']);
		$nl=$_REQUEST['nl'];
		$EVTL_NL=$nl?"</tr><tr>":""; // in case of nl show each record item on one line
		$u=$_REQUEST['u']; //defined? User
		$showuser=$_REQUEST['showuser']; //User to show info for
		$a=$_REQUEST['a']; //defined? action !!
		$a=$_REQUEST['a']; //defined? action !!
		$new=$_REQUEST['new']; //defined? UPDATE values!!
		$ID=$_REQUEST['id'];
		$neuenuser = $_REQUEST['neuenuser'];
		$funame=f.uniqid();
		$databaseadjust=$_REQUEST['databaseadjust']; //defined? User
		
		$admin=$_REQUEST['admin'];
		
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
		
		
		
		if ($filter)
			$FONT_BG="background-color:#faa";
		else
			$FONT_BG='';
		
		
		$TDOPTIONS=" style=\"white-space:nowrap;".$FONT_BG."\" ";
		
		
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
	
		if ($a=='duplicate_set')
		{
			//print "<br>TODO duplicate SET from user $showuser for user $neuenuser under filter '$filter'";
			if ($filter)
			{
				$EVT_FILTER=<<<EOF
				AND (
						ID like "%$filter%"
				OR	Name like "%$filter%"
				OR	AuthUser like "%$filter%"
				OR	AuthPasswd like "%$filter%"
				OR	Type like "%$filter%"
				OR	mode like "%$filter%"
				OR	Protocol like "%$filter%"
				OR	Server like "%$filter%"
				OR	Port like "%$filter%"
				OR	Path_Start like "%$filter%"
				OR	Servlet_Start like "%$filter%"
				OR	Path_Refine like "%$filter%"
				OR	Path_Refine like "%$filter%"
				OR	Path_Refine like "%$filter%"
				OR	Servlet_Refine like "%$filter%"
				OR	sparql_endpoint like "%$filter%"
				OR	sparql_endpoint_params like "%$filter%"
				OR	src_parameters like "%$filter%"
				OR	autocomplete_uri like "%$filter%"
				OR	comment like "%$filter%"
				OR	Created like "%$filter%"
				OR	Modified: like "%$filter%"
				OR	POS like "%$filter%"
																			
				)
EOF;
			}
			$DUPSETQUERY=<<<EOQ
			INSERT INTO src_interface 
			(POS,Name,forRODINuser,AuthUser,AuthPasswd,Protocol,Server,Port,Type,mode,Path_Start,Servlet_Start,Path_Refine,comment,sparql_endpoint,sparql_endpoint_params,Servlet_refine,src_parameters,autocomplete_uri,Modified,
			 UsedAsThesaurus,UsedForAutocomplete,UsedForSubjects,UsedForLODRdfExpansion,allow__UsedAsThesaurus, allow__UsedForAutocomplete, allow__UsedForSubjects, allow__UsedForLODRdfExpansion) 
			SELECT POS,Name,$neuenuser,AuthUser,AuthPasswd,Protocol,Server,Port,Type,mode,Path_Start,Servlet_Start,Path_Refine,comment,sparql_endpoint,sparql_endpoint_params,Servlet_refine,src_parameters,autocomplete_uri,CURRENT_TIMESTAMP,
			UsedAsThesaurus,UsedForAutocomplete,UsedForSubjects,UsedForLODRdfExpansion,allow__UsedAsThesaurus, allow__UsedForAutocomplete, allow__UsedForSubjects, allow__UsedForLODRdfExpansion 
			FROM src_interface 
			WHERE forRODINuser=$showuser
			$EVT_FILTER
EOQ;
			$resultset= mysql_query($DUPSETQUERY);
			$numrows = mysql_affected_rows();
			if ($numrows<1) 
				fontprint("<br>Problem duplicating SRC configuration SET for user $neuenuser <br>$numrows lines affected <br><br>$DUPSETQUERY",'red');	
			else {
				fontprint("$numrows duplicated SRC records from user $showuser for user $neuenuser under filter '$filter'",'green');
			}
		}	// duplicate_set
		else
		if ($a=='modify' || $a=='duplicate') 
		{
			//print "<h1>update values for ID=$ID for user $forRODINuser</h1>";
			$POS=$_REQUEST['POS'];
			$Name=$_REQUEST['Name'];
			$UsedAsThesaurus=$_REQUEST['UsedAsThesaurus'];
			$UsedForSubjects=$_REQUEST['UsedForSubjects'];
			$UsedForAutocomplete=$_REQUEST['UsedForAutocomplete'];
			$UsedForLODRdfExpansion=$_REQUEST['UsedForLODRdfExpansion'];
			$allow__UsedAsThesaurus=				$_REQUEST['allow__UsedAsThesaurus'];
			$allow__UsedForSubjects=				$_REQUEST['allow__UsedForSubjects'];
			$allow__UsedForLODRdfExpansion=	$_REQUEST['allow__UsedForLODRdfExpansion'];
			$allow__UsedForAutocomplete=		$_REQUEST['allow__UsedForAutocomplete'];
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
			$sparql_endpoint=$_REQUEST['sparql_endpoint'];
			$sparql_endpoint_params=$_REQUEST['sparql_endpoint_params'];
			$src_parameters=$_REQUEST['src_parameters'];
			$autocomplete_uri=$_REQUEST['autocomplete_uri'];
			$comment=$_REQUEST['comment'];
			$Type=$_REQUEST['Type'];
			$mode=$_REQUEST['mode'];
			
			if (!is_a_value($Port)) $Port=0;

			if (!is_a_value($allow__UsedForAutocomplete)) $allow__UsedForAutocomplete=0;
			else if ($allow__UsedForAutocomplete=='on') $allow__UsedForAutocomplete=1;
			else $allow__UsedForAutocomplete=0;

			if (!is_a_value($allow__UsedAsThesaurus)) $allow__UsedAsThesaurus=0;
			else if ($allow__UsedAsThesaurus=='on') $allow__UsedAsThesaurus=1;
			else $allow__UsedAsThesaurus=0;
			
			if (!is_a_value($allow__UsedForSubjects)) $allow__UsedForSubjects=0;
			else if ($allow__UsedForSubjects=='on') $allow__UsedForSubjects=1;
			else $allow__UsedForSubjects = 0;

			if (!is_a_value($allow__UsedForLODRdfExpansion)) $allow__UsedForLODRdfExpansion=0;
			else if ($allow__UsedForLODRdfExpansion=='on') $allow__UsedForLODRdfExpansion=1;
			else $allow__UsedForLODRdfExpansion=0;
			
			if (!$admin)
			//fetch data for the allow fields!
			{
				$allow_query="SELECT allow__UsedAsThesaurus, allow__UsedForSubjects, allow__UsedForLODRdfExpansion, allow__UsedForAutocomplete
											FROM src_interface
											WHERE ID=$ID";
				$resultset= mysql_query($allow_query);
				$allowed__UsedAsThesaurus = $allowed__UsedForSubjects = $allowed__UsedForLODRdfExpansion = $allow__UsedForAutocomplete = 0;
				if (($row = mysql_fetch_assoc($resultset)))
				{
					foreach($row as $attr=>$value)
					{
						//print "<br>DB VALUE: ($value)";
						if ($attr=='allow__UsedAsThesaurus')
						{
							$allowed__UsedAsThesaurus = $value;
						}
						else 
						if ($attr=='allow__UsedForSubjects')
						{
							$allowed__UsedForSubjects = $value;
						}
						else 
						if ($attr=='allow__UsedForLODRdfExpansion')
						{
							$allowed__UsedForLODRdfExpansion = $value;
						}	
						else 
						if ($attr=='allow__UsedForAutocomplete')
						{
							$allowed__UsedForAutocomplete = $value;
						}	
					}
				}
			} // !$admin
			
			
			if ($UsedAsThesaurus==null) $UsedAsThesaurus=0;
			else if ($UsedAsThesaurus) $UsedAsThesaurus=$allowed__UsedAsThesaurus; 
			
			if ($UsedForSubjects==null) $UsedForSubjects=0;
			else if ($UsedForSubjects) $UsedForSubjects=$allowed__UsedForSubjects; 

			if ($UsedForLODRdfExpansion==null) $UsedForLODRdfExpansion=0;
			else if ($UsedForLODRdfExpansion) $UsedForLODRdfExpansion=$allowed__UsedForLODRdfExpansion; 

			if ($UsedForAutocomplete==null) $UsedForAutocomplete=0;
			else if ($UsedForAutocomplete) $UsedForAutocomplete=$allowed__UsedForAutocomplete; 
			
			//if ($UsedAsThesaurus==null) $UsedAsThesaurus=0;
			//if ($UsedForSubjects==null) $UsedForSubjects=0;
			//if ($UsedForLODRdfExpansion==null) $UsedForLODRdfExpansion=0;
			
			
		/*
			$UsedAsThesaurus=$UsedAsThesaurus?1:0;
			$UsedForSubjects=$UsedForSubjects?1:0;
			$UsedForLODRdfExpansion=$UsedForLODRdfExpansion?1:0;
 		*/
			
			if($a=='modify')
			{
				
				if ($admin)
				{
					$EVTL_ALLOW_STUFF=<<<EOE
					allow__UsedAsThesaurus=$allow__UsedAsThesaurus,
					allow__UsedForSubjects=$allow__UsedForSubjects,
					allow__UsedForLODRdfExpansion=$allow__UsedForLODRdfExpansion,
					allow__UsedForAutocomplete=$allow__UsedForAutocomplete,
EOE;
				}
				
				if (!$UsedAsThesaurus) 	$UsedAsThesaurus=0;
				if (!$UsedForSubjects) 	$UsedForSubjects=0;
				if (!$UsedForAutocomplete) 	$UsedForAutocomplete=0;
				if (!$UsedForLODRdfExpansion) 	$UsedForLODRdfExpansion=0;
				
				$SQL_UPDATE=
				"UPDATE src_interface
				 SET 	Name='$Name',
						UsedAsThesaurus=$UsedAsThesaurus,
						UsedForSubjects=$UsedForSubjects,
						UsedForLODRdfExpansion=$UsedForLODRdfExpansion,
						UsedForAutocomplete=$UsedForAutocomplete,
						$EVTL_ALLOW_STUFF
						forRODINuser='$forRODINuser',
						AuthUser='$AuthUser',
						AuthPasswd='$AuthPasswd',
						Type='$Type',
						mode='$mode',
						Protocol='$Protocol',
						Server='$Server',
						Port=$Port,
						POS=$POS,
						Path_Refine='$Path_Refine',
						Path_Start='$Path_Start',
						sparql_endpoint='$sparql_endpoint',
						autocomplete_uri='$autocomplete_uri',
						sparql_endpoint_params='$sparql_endpoint_params',
						Servlet_Refine='$Servlet_Refine',
						Servlet_Start='$Servlet_Start',
						comment='$comment',
						sparql_endpoint='$sparql_endpoint',
						src_parameters='$src_parameters',
						Modified=CURRENT_TIMESTAMP
				 WHERE id=$ID
				";
				
				//print "<br><br>". $SQL_UPDATE;
				
				$resultset= mysql_query($SQL_UPDATE);
				$numrows = mysql_affected_rows();
				if ($numrows<>1) 
					fontprint("<br>Problem updating record with id=$ID<br><br>$SQL_UPDATE<br><br>",'red');	
			}
			else if ($a=='duplicate') 
			{
				$SQL_DUP=
				"INSERT INTO src_interface
				 (POS,Name,forRODINuser,AuthUser,AuthPasswd,Protocol,Server,Port,Type,mode,Path_Start,Servlet_Start,Path_Refine,comment,sparql_endpoint,sparql_endpoint_params,Servlet_refine,src_parameters,autocomplete_uri,Modified) 
				 SELECT POS,Name,forRODINuser,AuthUser,AuthPasswd,Protocol,Server,Port,Type,mode,Path_Start,Servlet_Start,Path_Refine,comment,sparql_endpoint,sparql_endpoint_params,Servlet_refine,src_parameters,autocomplete_uri,CURRENT_TIMESTAMP 
				 FROM src_interface WHERE id=$ID
				";
				
				//print $SQL_DUP;
				
				$resultset= mysql_query($SQL_DUP);
				$numrows = mysql_affected_rows();
				if ($numrows<1) 
					fontprint("<br>Problem duplicating records <br>$numrows lines affected <br><br>$SQL_DUP",'red');	
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

		$onKeyUpSearchFAction="javascript: if (event.keyCode==13) { document.getElementById(\"bview\").click(); }";

		if ($nl)
		$ALTERNATIVE_VIEW =<<<EOL
<input type=button id='bview' value='Quick view' onclick='window.open("$thisSCRIPT?u=$u&showuser="+document.getElementById("shusr").value+"&filter="+document.getElementById("filter").value+"&nl=0","_self")'>
EOL;
		else 
		$ALTERNATIVE_VIEW =<<<EOL
<input type=button id='bview' value='Detailed view' onclick='window.open("$thisSCRIPT?u=$u&showuser="+document.getElementById("shusr").value+"&filter="+document.getElementById("filter").value+"&nl=1","_self")'>
EOL;
			

		if ($showuser)
		$EVTLDUPLICATE="
		&nbsp;&nbsp;
			<input type='button' name='du' value='+' style=\"background-color:$MARKCOLOR;font-weight:bold;padding:0px\" title='Duplicate the SRC configuration SET for user $showuser under the filter (if set)'
		   onClick=\"if(confirm('Duplicate the SRC configuration SET from user $showuser for user '+document.forms.hmenu.neuenuser.value+(document.forms.hmenu.filter.value?' filtered with '+document.forms.hmenu.filter.value:'?')+' ?')) {document.forms.hmenu.a.value='duplicate_set'; document.forms.hmenu.submit();}\">
			<input type=text size=2 name='neuenuser' value='?' title='Insert here the user id of the target user to which you want the new records will belong and press the + button nearby'>
		";
		

		$NewINTERFACEBUTTON= 
		"<form name='hmenu' action=\"$thisSCRIPT\" method='$FORMMETHOD'>		
		 $FONTGUI
		 <input type='hidden' name='nl' value='$nl'>
	 	 <input type='hidden' name='u' value='$u'>
	 	 <input type='hidden' name='a' value='$a'>
	 	 		 See only records for user: 
	 	 <input id='shusr' title='Type the user number you want to see his records' onkeyup='$onKeyUpSearchFAction' type='text' name='showuser' size=1 value='$showuser' style=\"text-align:center\">
		 &nbsp;&nbsp;Filter with: <input type='text' name='filter' id='filter' value='$filter' onkeyup='$onKeyUpSearchFAction' title='Insert some words to be used as filter to restrict the records view'>
		 &nbsp;&nbsp;Change to: $ALTERNATIVE_VIEW
			$EVTLDUPLICATE
		</font>
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
		$FONT_TD=$FONTGUI;
		
		
			
		if ($numrows)
		while (($row = mysql_fetch_assoc($resultset)))
		{	
			$n++;
			###################################
			# PHP filter section
			###################################
			if ($filter)
			{
				$filter=strtolower($filter);
				$visible=false;
				foreach($row as  $attr=>$value)
				{
					$attr=strtolower($attr);
					$value=strtolower($value);
					
					//print "<br>Check filter ($filter) against ($attr or $value)";
					
					$visible=strstr($attr,$filter) || strstr($value,$filter);
					if ($visible) break;
				}
			}
			else $visible=true;
			
			###################################
			# Show visible records:
			#
			if ($visible)
			{
				if ($start)
				{
print <<<EOP
	$NewINTERFACEBUTTON &nbsp; 
	<br>
	<table  cellpadding=2 cellspacing=0 border=0>
		<tr>
EOP;
				if (!$EVTL_NL) //no Header line if newline=one item at a line mode
				{
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
							{
									$FONT=$FONTSRC;
							}
							print "<td align=left>$FONT $attr $ENDFONTRESULT</td>$EVTL_NL";
						}
						else
							print "<td align=left>$FONT ($attr) $ENDFONTRESULT</td>$EVTL_NL";
						
					} //for
				}
print <<<EOP
		</tr>
		<tr height=2><td colspan=999><hr size='1'></td></tr>
EOP;
		} // start		

					if ($row['UsedAsThesaurus'] or $row['UsedForSubjects'] or $row['UsedForLODRdfExpansion'] or $row['UsedForAutocomplete'] ) 
						$TRSTYLES="bgcolor=$COLOR_RODINSEGMENT";
					else 
					$TRSTYLES='';
					
				
					
	
					print "<tr id='R$n' $TRSTYLES>
								 <form name=\"f$n\" action=\"\" method='$FORMMETHOD'>
								 <input type='hidden' name='a'>
								 <input type='hidden' name='nl' value='$nl'>
								 <input type='hidden' name='nl' value='$nl'>
								 <input type='hidden' name='filter' value='$filter'>
								 <input type='hidden' name='u' value='$u'>
								 ";
		
					$DeleteDuplicateTHISRECORD=
					"<input type='button' name='d' value='X' title='Delete this SRC interface record'
						onClick=\"if (confirm('Do you REALLY want to delete this SRC interface record?')) 
							{f$n.a.value='delete'; f$n.submit();}\">
					 <input type='button' name='c' value='+' style=\"background-color:$MARKCOLOR;font-weight:bold;padding:0px\" title='Duplicate this SRC interface record'
						onClick=\"f$n.a.value='duplicate'; f$n.submit();\">";
		
		
					$firstcol=true;
					

				  $EVTL_TD=$nl?"<td valign=top align=right>$ALTERNATIVE_VIEW</td>":'';	
					print "
						<td align=left nowrap>
							$DeleteDuplicateTHISRECORD
							<span style='color:white'>Active</span>
						</td>$EVTL_TD $EVTL_NL $FONT_TD";
					##############################
					##############################
					foreach($row as $attr=>$value)
					##############################
					##############################
					{
						$TITLE="change '$attr' ?";
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
							
							if ($EVTL_NL)
							//Print fieldname on the left:
							{
								print "<td align=right>$FONT_TD<b>".trim($attr).":</b></font></td>";
							}
							
							if ($attr == 'forRODINuser')
							{		
								$user_info=fri_get_user_info($value);
						 		$userlangname=$user_info['long_name'];
						 		$username=$user_info['username'];
						 		$SIZE=1;
							}
							if ($attr == 'Created' || $attr == 'Modified')
								$DISABLED=' disabled ';
							
							if ($attr == 'UsedAsThesaurus' || $attr == 'UsedForSubjects' || $attr == 'UsedForLODRdfExpansion' || $attr == 'UsedForAutocomplete')
								$TYPE='checkbox';
							else 
							if ($attr == 'allow__UsedAsThesaurus' || $attr == 'allow__UsedForSubjects' || $attr == 'allow__UsedForLODRdfExpansion' || $attr == 'allow__UsedForAutocomplete' )
							{
								$TYPE='checkbox';
								$DISABLED=$admin?'':' disabled ';
								if (!$admin) $TITLE="You cannot change '$attr' - only admins do :-) ";
								
							}
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
							else if ($attr=='Server'
										|| $attr=='Name') 
								$SIZE=12;
							else if ($attr=='Path_Start'
								|| $attr=='Path_Refine'
								|| $attr=='Path_Test'
								|| $attr=='sparql_endpoint'
								|| $attr=='autocomplete_uri'
								|| $attr=='comment'
								|| $attr=='sparql_endpoint_params'
								|| $attr=='src_parameters'
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
							print "<td align=left $TDOPTIONS> $FONT_TD";
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
									<input type='$TYPE' name='$attr' title=\"$TITLE\" $DISABLED $CHECKED $SAVE_ACTION>	
								";
							} 	
								
							print "</font></td>$EVTL_NL";
						} // DI	
							
					} //for
					
					print "
						</form>
						<tr height=2><td colspan=999><hr size=1></td>
						</tr>";
			} // while
		}
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

</body>
</html>