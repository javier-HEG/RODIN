<?php
####################################
#
# install_rodin.php
#
# (re)install a rodin tar
# after it has been un-tared
#
# Autor: Fabio Ricci (fabio.fr.ricci@hesge.ch)
# HEG Geneve
# June 2011
#
# Open: mkdir should make files writable for the current user (under the web server... maybe start it with php from CLI?)
# Open: XSLT service should be resolved as on laptop (complex java solution not included)
#


chdir("../../eng/app/u");
include_once 'FRIdbUtilities.php';
include_once '../css/skins/first/RODIN_COLORS.php';

##############################
#
# RODIN DB Installation
# RODIN paths Installation
# the user can install selveral segments
# and keep the _gen segment as a basis
#
##############################

$verbose=param_named('VERBOSE',$_REQUEST);

$marginleft=10;
$marginleft3=$marginleft * 3;
$textwidth=800;
$inputwidth=200;
$allwidth=$textwidth+$inputwidth;
$tableallwidth=$marginleft+$textwidth+$inputwidth + 10;

$INPUTTEXTSTYLERED="style=\"color:red;\"";
$INPUTTEXTSTYLEBLUE="style=\"color:blue;\"";
$INPUTTEXTSTYLEGREEN="style=\"color:green;\"";
$INPUTTEXTSTYLEGRAY="style=\"color:gray;\"";

$STEPEXPLANATION=array(1=>'Create RODIN USERS and Databases + generate new RODIN instance', 
											 2=>'Set proxy and/or web service authorizations',
											 3=>'Confirmation and finishing up',
											 );

$ICON_W=30;
$ICON_H=30;


$ICON_DONE="&nbsp;<img width='$ICON_W' height='$ICON_H' src='$RODINIMAGES/okhaken.png' >&nbsp;";
$ICON_SKIPPED="&nbsp;<img width='$ICON_W' height='$ICON_H' src='$RODINIMAGES/skipped.png'  >&nbsp;";

###################################################################
###################################################################
###################################################################
$step=$_REQUEST['step'];
if (!$step) $step=1;



$STEPS=count($STEPEXPLANATION);
print <<<EOP
<html>
<head>
	<title>RODIN INSTALLATION step=$step</title>	
	<link rel="stylesheet" type="text/css" href="install_rodin.css">
	<script type="text/javascript" src="install_rodin.js"></script>	
	
</head>
<body bgcolor="$COLOR_PAGE_BACKGROUND">
<form name='f'>
<h2 align=middle> RODIN Installation </h2>
<table cellspacing="1" cellpadding="0" class='installrodin' border=0 width="$tableallwidth">

<tr height="10"></tr>
<tr></tr>
<tr height="10"></tr>
<tr><td width="$marginleft">
		<td width="$textwidth" valign="top" align="left"><span class="explHead"> Step $step/$STEPS: {$STEPEXPLANATION[$step]}</span></td>
		<td valign="top">
		</td>
</tr>
<tr ><td width="$marginleft">
		<td colspan="3">
			<hr size=1>
		</td>
</tr>
<tr height="10"></tr>
EOP;
		




switch($step)
{

###################################################################
###################################################################
###################################################################
	case 2:
###################################################################
###################################################################
###################################################################
{
	$nextstep=3;
	
	$skipdb=$_REQUEST['skipdb']=='true'?true:false; 
	$newrodinsegment=$_REQUEST['newrodinsegment'];
	$newdbusername=$_REQUEST['newdbusername'];
	$newdbuserpass=$_REQUEST['newdbuserpass'];
	$newrodindb_dbname=$_REQUEST['newrodindb_dbname'];
	$newrodinposhdb_dbname=$_REQUEST['newrodinposhdb_dbname'];
	$newarcdb_dbname=$_REQUEST['newarcdb_dbname'];
	$newsrcdb_dbname=$_REQUEST['newsrcdb_dbname'];
	$newrodinadmindb_dbname=$_REQUEST['newrodinadmindb_dbname'];
	$newadmindbuser0name=$_REQUEST['newadmindbuser0name'];
	$newadmindbusername=$_REQUEST['newadmindbusername'];
	$newadmindbuser0pass=$_REQUEST['newadmindbuser0pass'];
	$newadmindbuserpass=$_REQUEST['newadmindbuserpass'];
	
	
	
	
	$HREFOPURL="$RODINROOT/$newrodinsegment/app/u/maintain_widgetkeys.php?adminusername=$newadmindbuser0name&adminuserpass=$newadmindbuser0pass";
	$HREFOPERATIONAL="<a href='$HREFOPURL' target='_blank' title='Click to open a view on RODINs operational parameters in a new tab of RODINs operational parameters'> RODINs operational parameters</a>";
	
	
	
	#print "Welcome to STEP $step <br>";
	$step1_result = step_1_execution(
																	$skipdb,
																	$newrodinsegment,
																	$newdbusername,
																	$newdbuserpass,
																	$newrodindb_dbname,
																	$newrodinposhdb_dbname,
																	$newarcdb_dbname,
																	
																	$newsrcdb_dbname,
																	$newrodinadmindb_dbname,
																	$newadmindbuser0name,
																	$newadmindbuser0pass
																	);
																	
	if ($step1_result)
	{
		$STEP2_ONKEYUP=" onkeyup=\"update_rodin_install_inputs_step1()\" ";
		$STEP2_ONCLICK=" onclick=\"update_rodin_install_inputs_step1()\" ";
		$STEP2_PARAMS="'?step=3"	
		
								."&newrodinsegment=$newrodinsegment"

								."&newproxyname='+document.f.NEWPROXYNAME.value+'"
								."&newproxyport='+document.f.NEWPROXYPORT.value+'"
								."&newproxyusername='+document.f.NEWPROXYUSER.value+'"
								."&newproxyuserpass='+document.f.NEWPROXYPASS.value+'"
								."&newproxyauthtype='+document.f.NEWPROXYAUTHTYPE.value+'"
								."&newselfusername='+document.f.NEWSELFNAME.value+'"
								."&newselfuserpass='+document.f.NEWSELFPASS.value+'"
								."&newselfauthtype='+document.f.NEWSELFAUTHTYPE.value+'"
								#The Parameters from step 1:
								."&newdbusername=$newdbusername"
								."&newdbuserpass=$newdbuserpass"
								."&newrodindb_dbname=$newrodindb_dbname"
								."&newrodinposhdb_dbname=$newrodinposhdb_dbname"
								."&newarcdb_dbname=$newarcdb_dbname"
								
								."&newsrcdb_dbname=$newsrcdb_dbname"
								."&newrodinadmindb_dbname=$newrodinadmindb_dbname"
								."&newadmindbusername=$newadmindbusername"
								."&newadmindbuserpass=$newadmindbuserpass"
								."&newadmindbuser0name=$newadmindbuser0name"
								."&newadmindbuser0pass=$newadmindbuser0pass"
								."'"							
								;
								
		$ACTION_2="'$WEBROOT$thisSCRIPT'+$STEP2_PARAMS";
		#$STEP2_ONSUBMIT=" onclick=\"alert($ACTION_2);window.open($ACTION_2,'_self');\" ";
		$STEP2_ONSUBMIT=" onclick=\"window.open($ACTION_2,'_self');\" ";
		
	
	print <<<EOP
<!-- THE BUTTON -->
<tr height="10"></tr>
<tr ><td width="$marginleft">
		<td colspan="3"><span class='expl0'>
			Please type in your:</span>
		</td>
EOP;
	$NEWPROXYAUTHTYPE = $NEWSELFAUTHTYPE = 'basic';
	$NEWADMIEMAILADDR ='kingjohn@scherwood.xxx';
	
	
	
	
	
	print userinputline('NEWPROXYNAME',$NEWPROXYNAME,20,$STEP2_ONKEYUP,true,
											"Proxy address (IP or simbolic) if a proxy is needed",
											"Please type your proxy address (IP or simbolic) or leave it blank if no proxy is required",'expl0');
	
	print userinputline('NEWPROXYPORT',$NEWPROXYPORT,5,$STEP2_ONKEYUP,false,
											"Proxy port if needed",
											"Please type your proxy port (a number from 1 to 60000) or leave it blank for =80",'expl0');
	
	print userinputline('NEWPROXYUSER',$NEWPROXYUSER,20,$STEP2_ONKEYUP,false,
											"Proxy user if needed",
											"Please type your proxy user or leave it blank",'expl0');
	
	print userinputline('NEWPROXYPASS',$NEWPROXYPASS,20,$STEP2_ONKEYUP,false,
											"Proxy password if needed",
											"Please type your proxy password or leave it blank",'expl0');
	
	print userinputline('NEWPROXYAUTHTYPE',$NEWPROXYAUTHTYPE,20,$STEP2_ONKEYUP,false,
											"Proxy authorization type if needed",
											"Please type your proxy authorization type (basic or digest) or leave it blank",'expl0');
	
	print userinputline('NEWSELFNAME',$NEWSELFNAME,20,$STEP2_ONKEYUP,true,
											"Web service authorization user if needed",
											"Please type your web service authorization user or leave it blank",'expl0');
			
	print userinputline('NEWSELFPASS',$NEWSELFPASS,20,$STEP2_ONKEYUP,false,
											"Web service authorization password if needed",
											"Please type your web service authorization password or leave it blank",'expl0');
	
	print userinputline('NEWSELFAUTHTYPE',$NEWSELFAUTHTYPE,20,$STEP2_ONKEYUP,false,
											"Web service authorization type if needed",
											"Please type your Web service authorization type (basic or digest) or leave it blank",'expl0');
	
EOP;
	
	
	print <<<EOP
<!-- THE BUTTON -->
<tr height="10"></tr>
<tr ><td width="$marginleft">
		<td colspan="3">
			<hr size=1>
		</td>
</tr><tr height="10"></tr>
<tr ><td width="$marginleft">
		<td colspan="3" id="installbuttontd">
			<input width="100" type="button" style="width:{$allwidth}px" name="installbutton" value="Install security issues" $STEP2_ONSUBMIT>
		</td>
</tr>
<tr height="10"></tr>
EOP;
	} // $step1_result ok
} # case 2
	break;
###################################################################
###################################################################
###################################################################
	case 3:
###################################################################
###################################################################
###################################################################
	{
	$nextstep='done';
	$newrodinsegment			=$_REQUEST['newrodinsegment'];
		#Parameters from step 2:
	$newproxyname					=$_REQUEST['newproxyname'];
	$newproxyport					=$_REQUEST['newproxyport'];
	$newproxyusername			=$_REQUEST['newproxyusername'];
	$newproxyuserpass			=$_REQUEST['newproxyuserpass'];
	$newproxyauthtype			=$_REQUEST['newproxyauthtype'];
	$newproxyauthtype			=$_REQUEST['newproxyauthtype'];
	$newselfusername			=$_REQUEST['newselfusername'];
	$newselfuserpass			=$_REQUEST['newselfuserpass'];
	$newselfauthtype			=$_REQUEST['newselfauthtype'];
	#Parameters from step 1:
	#RODINDB
	$newdbusername				=$_REQUEST['newdbusername'];
	$newdbuserpass				=$_REQUEST['newdbuserpass'];
	$newrodindb_dbname		=$_REQUEST['newrodindb_dbname'];
	$newrodinposhdb_dbname=$_REQUEST['newrodinposhdb_dbname'];
	$newarcdb_dbname			=$_REQUEST['newarcdb_dbname'];
	$newsrcdb_dbname			=$_REQUEST['newsrcdb_dbname'];
	$newrodinadmindb_dbname=$_REQUEST['newrodinadmindb_dbname'];
		
	$newadmindbuser0name	=$_REQUEST['newadmindbuser0name'];
	$newadmindbusername		=$_REQUEST['newadmindbusername'];
	$newadmindbuser0pass	=$_REQUEST['newadmindbuser0pass'];
	$newadmindbuserpass		=$_REQUEST['newadmindbuserpass'];
	
	
	$step3_result=true;
	
	$NEWRODINURL="$WEBROOT$RODINROOT/$newrodinsegment";
	$NEWRODINMAINTURL="$WEBROOT$RODINROOT/$newrodinsegment/posh/portal/login.php?MAINT";
	$NEWRODINSELECTFSRC="$WEBROOT$RODINROOT/$newrodinsegment/fsrc/app/u/select_src.php?u=2";
	
	$HREFNEWRODINMAINTURL="<a href='$NEWRODINMAINTURL' target='_blank' title='Click to start and initialize your newly configurated RODIN in another tab'>$NEWRODINMAINTURL</a>";
	$HREFNEWRODINURL="<a href='$NEWRODINURL' target='_blank' title='Click to start your newly configurated RODIN in another tab'>$NEWRODINURL</a>";
	$HREFNEWRODINSELECTFSRC="<a href='$NEWRODINSELECTFSRC' target='_blank' title='Click to start your newly configurated RODIN FSRC Maintenance console in another tab'>$NEWRODINSELECTFSRC</a>";
	
	
	
	$NEWRODINADMINURL="$WEBROOT$RODINROOT/$newrodinsegment/posh/admin";
	$HREFNEWRODINADMINURL="<a href='$NEWRODINADMINURL' target='_blank' title='Click to start your newly configurated portaneo (c) in another tab'>$NEWRODINADMINURL</a>";
	
	$RODINLOGO="$POSHWEBROOT/images/logo_portal.gif";
	$NOKIMAGE="$WEBROOT$RODINIMAGES/arrowx2backL.png";
	$HISTORYBACK="<a title='click to step back' href=\"javascript:history.back()\"><img src='$NOKIMAGE'></a>";

	$NEWDEFAULTRODINUSER='rodinuser';
	$NEWDEFAULTRODINUSERPASS=$NEWDEFAULTRODINUSER;
	$NEWDEFAULTRODINADMINUSER='administrator';
	$NEWDEFAULTRODINADMINUSERPASS=$NEWDEFAULTRODINADMINUSER;
	$HREFOPURL="$RODINROOT/$newrodinsegment/app/u/maintain_widgetkeys.php?adminusername=$newadmindbuser0name&adminuserpass=$newadmindbuser0pass";
	$HREFOPERATIONAL="<a href='$HREFOPURL' target='_blank' title='Click to open a view on RODINs operational parameters in a new tab of RODINs operational parameters'> RODINs operational parameters</a>";
		
	$step2_result = step_2_execution(
													$newrodinsegment,

													$newadmiemaladdr,
													$newproxyname,
													$newproxyport,
													$newproxyusername,
													$newproxyuserpass,
													$newproxyauthtype,
													$newproxyauthtype,
													$newselfusername,
													$newselfuserpass,
													$newselfauthtype,
											
													$newdbusername,
													$newdbuserpass,
													$newrodindb_dbname,
													$newrodinposhdb_dbname,
													$newarcdb_dbname,
													$newsrcdb_dbname,
													$newrodinadmindb_dbname,
													$newadmindbuser0name,
													$newadmindbuser0pass	
													);
	
													
	$step3_result =	step_3_execution(
													$newrodinsegment,
													$newadmipseudo,
													$newadmiemailaddr,
													$newadmipass,
													$newrodinuseremailaddr,
													$newrodinuserpass
													);
	
	if ($step2_result && $step3_result)
	{
	print <<<EOP
</tr><tr height="10"></tr>	
<tr ><td width="$marginleft">
		<td colspan="4" class='explDONE'>
			Please view/adapt operational parameters of RODIN with your new user <b>$newadmindbusername</b> under $HREFOPERATIONAL
		</td>
<tr height="50"></tr>	
<tr><td width="$marginleft">
	<td colspan="3" width="$textwidth" valign="top" align="left"><span class="explDONE"> $ICON_DONE CONGRATULATIONS! <img src="$RODINLOGO"> is now configured for your new segment '<b>$newrodinsegment</b>'</span></td>
</tr>
		<tr height="50"></tr>	
		<tr><td width="$marginleft">
		<td colspan="3" width="$textwidth" valign="top" align="left"><span class="explDONE"> <i><b>You should now be able to:</b></i></span></td>
<tr height="10"></tr>	
		<tr><td width="$marginleft">
		<td colspan="3" width="$textwidth" valign="top" align="left"><span class="explDONE">initialize (once) and access RODIN using $HREFNEWRODINMAINTURL <br>with user/passwd: <i>$NEWDEFAULTRODINUSER/$NEWDEFAULTRODINUSERPASS</i> </span></td>
</tr>
<tr height="10"></tr>	
		<tr><td width="$marginleft">
		<td colspan="3" width="$textwidth" valign="top" align="left"><span class="explDONE">access RODIN using $HREFNEWRODINURL <br>with user/passwd: <i>$NEWDEFAULTRODINUSER/$NEWDEFAULTRODINUSERPASS</i> </span></td>
</tr>
<tr height="10"></tr>	
		<tr><td width="$marginleft">
		<td colspan="3" width="$textwidth" valign="top" align="left"><span class="explDONE">access RODIN FSRC Engines using $HREFNEWRODINSELECTFSRC <br>with <i>u=2</i> </span></td>
</tr>
<tr height="10"></tr>	
<tr><td width="$marginleft">
		<td colspan="3" width="$textwidth" valign="top" align="left"><span class="explDONE">access RODIN administration - portaneo (c) - using $HREFNEWRODINADMINURL <br>with user/passwd: <i>$NEWDEFAULTRODINADMINUSER/$NEWDEFAULTRODINADMINUSERPASS</i></span></td>
</tr><tr height="100"></tr>	
<!-- THE BUTTON -->
<tr height="10"></tr>

EOP;

	} // $step2_result ok
	
	else {
	print <<<EOP
<tr height="50"></tr>	
<tr><td width="$marginleft">
		<td colspan="3" width="$textwidth" valign="top" align="left"><span class="explDONE"> UNFORTUNATELY there was some problem installing RODIN. Please step back $HISTORYBACK, change some parameters or your infrastructure and retry this step. </span></td>
</tr>
<tr height="100"></tr>	
<!-- THE BUTTON -->
<tr height="10"></tr>
EOP;

		}
	} #case 3
	break;
###################################################################
###################################################################
###################################################################
		case 1:
###################################################################
###################################################################
###################################################################
{	
	$NEWRODINSEGMENT=$_REQUEST['NEWRODINSEGMENT'];
	if ($NEWRODINSEGMENT=='') $NEWRODINSEGMENT='p';// Assumption PROD -> Input
	
	#############################
	#
	# RODINDB
	#
	$RODINDB_BASENAME=$_REQUEST['RODINDB_BASENAME'];
	if (!$RODINDB_BASENAME) $RODINDB_BASENAME="RODIN";
	
	$RODINDB_DBNAME		=strtolower("RODIN_$NEWRODINSEGMENT");
	$RODINDB_USERNAME=$_REQUEST['RODINDB_USERNAME'];
	if (!$RODINDB_USERNAME) $RODINDB_USERNAME="RODINhood"; //proposal
	
	$RODINDB_PASSWD=$_REQUEST['RODINDB_PASSWD'];
	if (!$RODINDB_PASSWD) $RODINDB_PASSWD	="yesyoucan123";
	
	#####################################
	# RODINPOSHDB
	
	$RODINPOSHDB_DBNAME="RODINPOSH";
	$RODINPOSHDB_USERNAME=$RODINDB_USERNAME;
	$RODINPOSHDB_PASSWD=$RODINDB_PASSWD;	
	
	#####################################
	# ARC Local Store:
	$ARCDBBASENAME="SRC_ARC";
	$ARCDB_DBNAME=$ARCDBBASENAME.$NEWRODINSEGMENT;
	$ARCDB_USERNAME=$RODINDB_USERNAME;
	$ARCDB_USERPASS=$RODINDB_PASSWD;	
	
	#####################################
	# SRC DB
	$SRCDBBASENAME="SRC";
	$SRCDB_DBNAME=$SRCDBBASENAME.$NEWRODINSEGMENT;
	$SRCDB_USERNAME=$RODINDB_USERNAME;
	$SRCDB_USERPASS=$RODINDB_PASSWD;	
	
	#####################################
	# ADMIN DB
	$RODINROOTstr					= substr($RODINROOT,1,strlen($RODINROOT));
	$RODINADMIN_HOST			= $_SERVER["HTTP_HOST"];
	$ADMINDBBASENAME			= limitusernamelength($RODINROOTstr."_root");
	$RODINADMIN_DBBASENAME= limitusernamelength($RODINROOTstr."_root");
	$RODINADMIN_DBNAME 		= limitusernamelength($RODINROOTstr."_root_".$NEWRODINSEGMENT);
	$RODINADMIN_USERNAME 	= limitusernamelength($RODINROOTstr."_root_".$NEWRODINSEGMENT);
	$RODINADMIN_USERPASS 	= strrev($RODINADMIN_USERNAME);
	$RODINADMIN_USER0PASS = "apple0";
	
	
	//$RODIN="$DOCROOT$RODINROOT/$RODINSEGMENT";
	$OLDRODINDOCROOT="$DOCROOT$RODINROOT";
	$OLDRODINWEBROOT="$WEBROOT$RODINROOT";
	$NEWRODINDOCROOT="$DOCROOT$RODINROOT/$NEWRODINSEGMENT";
	$NEWRODINWEBROOT="$WEBROOT$RODINROOT/$NEWRODINSEGMENT";
	
	
	#############################
	#
	# USERNAMES MUST NOT EXCEED 16 CHARS LENGTH !!!!
	#
	
	
	
	$STEP1_ONKEYUP=" onkeyup=\"update_rodin_install_inputs_step1()\" ";
	$STEP1_ONCLICK=" onclick=\"update_rodin_install_inputs_step1()\" ";
	$STEP1_PARAMS="'?step=2"	
							."&newrodinsegment='+get_newrodinsegment()+'"
							."&newdbusername='+document.getElementById('RODINDB_USERNAME').innerHTML+'"
							."&newdbuserpass='+document.getElementById('RODINDB_PASSWD').innerHTML+'"
							."&newrodindb_dbname='+document.getElementById('RODINDB_DBNAMEx').innerHTML+'"
							."&newrodinposhdb_dbname='+document.getElementById('RODINPOSHDB_DBNAMEx').innerHTML+'"
							."&newarcdb_dbname='+document.getElementById('ARCDB_DBNAMEx').innerHTML+'"
							."&newsrcdb_dbname='+document.getElementById('SRCDB_DBNAMEx').innerHTML+'"
							."&newrodinadmindb_dbname='+document.getElementById('ADMINDB_DBNAMEx').innerHTML+'"
							."&newadmindbuser0name='+document.getElementById('ADMINDB_DBNAMEd0gen').innerHTML+'"
							."&newadmindbusername='+document.getElementById('RODINADMIN_USERNAME0gen').innerHTML+'"
							."&newadmindbuser0pass='+document.getElementById('RODINADMIN_USER0PASS').innerHTML+'"
							."&newadmindbuserpass='+document.getElementById('RODINADMINDB_PASSWD').innerHTML+'"
							."&skipdb='+document.f.skipdb.checked"
							
							;
	$ACTION_1="'$WEBROOT$thisSCRIPT'+$STEP1_PARAMS";
	#$STEP1_ONSUBMIT=" onclick=\"alert($ACTION_1);window.open($ACTION_1,'_self');\" ";
	$STEP1_ONSUBMIT=" onclick=\"window.open($ACTION_1,'_self');\" ";
	
	$TITLEUSERS="Users, passwords and rights needed in your mysql DBMS before your install RODIN. Please copy-paste these mysql commands into your SQL slot inside your mysql DBMS";
print <<<EOP

<tr><td width="$marginleft">
		<td width="$textwidth" valign="top" align="left"><span class="expl0"> Please enter a logical name defining your RODIN segment instance (for instance 'p')</span></td>
		<td valign="top">-><input $INPUTTEXTSTYLE class="rodinseg" name='NEWRODINSEGMENT' value='$NEWRODINSEGMENT' size="1" $STEP1_ONKEYUP title="Please type a segment name (for instance 'p')">
		</td>
</tr>
<tr height="10"></tr>
<tr><td width="$marginleft">
		<td width="$textwidth"><span class="expl0">The databases below will be installed using the mysql <b>user</b></span></td>
		<td width="$inputwidth" valign="top">
			-><input $INPUTTEXTSTYLEGREEN name='RODINDB_USERNAME' value='$RODINDB_USERNAME' size="20" $STEP1_ONKEYUP title="Please type a user name">
		</td>
<tr>
<tr height="10"></tr>
<tr><td width="$marginleft">
		<td width="$textwidth"><span class="expl0">The databases below will be installed with the user <label id='RODINDB_USERNAME1' class="username"></label> with password</span></td>
		<td width="$inputwidth" valign="top">
			-><input $INPUTTEXTSTYLEBLUE name='RODINDB_PASSWD' value='$RODINDB_PASSWD' size="20" $STEP1_ONKEYUP "Please type the password for your user">
		</td>
<tr>

<tr height="10"></tr>
<tr></tr>
<tr><td width="$inputwidth" width="$marginleft"><td title='' colspan=*3*><span class='expl4'> The following mysql databases will be created:</span>
		</td>
<tr><tr><td width="$marginleft"><td title='' colspan=*3*><span class='expl2'> 
<input type="hidden" name='RODINDB_BASENAMEx' value='$RODINDB_BASENAME'>
<label id="RODINDB_DBNAMEx" class="dbname">{$RODINDB_BASENAME}_{$NEWRODINSEGMENT}</label> - holding RODIN results information<br>
<input type="hidden" name="RODINPOSHDB_DBNAMEx" value="$RODINPOSHDB_DBNAME">
<label id="RODINPOSHDB_DBNAMEx" class="dbname">{$RODINPOSHDB_DBNAME}</label> - holding portaneo (c) portal information<br>
<input type="hidden" name="ARCDBBASENAMEx" value="$ARCDBBASENAME">
<label id="SRCDB_DBNAMEx" class="dbname">{$SRCDB_DBNAME}</label> - holding specific information like stopwords <br>

<input type="hidden" name="ADMINDBBASENAMEx" value="$ADMINDBBASENAME">
<input type="hidden" name="RODINADMINDB_USERPASSx" value="$RODINADMIN_USERPASS">

<input type="hidden" name="RODINADMINDB_USERNAMEx" value="$RODINADMIN_DBBASENAME">
<label id="ADMINDB_DBNAMEx" class="dbname">{$ADMINDB_DBNAME}</label> - holding operational parameters <br>

<input type="hidden" name="SRCDBBASENAMEx" value="$SRCDBBASENAME">
<label id="ARCDB_DBNAMEx" class="dbname">{$ARCDB_DBNAME}</label> - holding semantic web local stores
</span>
		</td>
<tr>


<tr height="10"></tr>
<tr></tr>
<tr><td width="$inputwidth" width="$marginleft"><td title='Example of user you may create in your mysql DBMS before your install RODIN' colspan=*3*><span class='expl1'> The following users
<b> should be created in your mysql DBMS </b> by yourself or by your mysql database administrator <b>before</b> you install RODIN DBs (i.e. before you push the button below, copy-paste the following 11 mysql commands into your SQL slot inside your mysql DBMS):</span>
		</td>
</tr>
<tr class='mysqltext'><td></td><td title='$TITLEUSERS' colspan=*3*><!--span class='explsql'--> 
CREATE USER '<label id="ADMINDB_DBNAMEd0gen" class="xusername">$RODINADMIN_DBNAME-0</label>'@'%' IDENTIFIED BY '<label id="RODINADMIN_USER0PASS" class='passwd'>$RODINADMIN_USER0PASS</label>'; -- or some other / please adapt directly - this is your base user for adapting operational parameters'<br>
CREATE USER '<label id="ADMINDB_DBNAMEd0" class="ausername">$RODINADMIN_DBNAME</label>'@'%' IDENTIFIED BY '<label id="RODINADMINDB_PASSWD" class='passwd'>$RODINADMIN_USERPASS</label>'; -- or some other / please adapt directly - this an application user accessing operational parameters'<br>
CREATE USER '<label id="RODINDB_USERNAME" class="username">$RODINDB_USERNAME</label>'@'%' IDENTIFIED BY '<label id="RODINDB_PASSWD" class='passwd'>$RODINDB_PASSWD</label>';<br>
GRANT USAGE ON *.* TO '<label id="RODINDB_USERNAME2" class="username">$RODINDB_USERNAME</label>'@'%' IDENTIFIED BY '<label id="RODINDB_PASSWD2" class="userpass">$RODINDB_PASSWD</label>' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;<br>
GRANT ALL PRIVILEGES ON `<label id='RODINDB_DBNAMEd' class="dbname">$RODINDB_DBNAME</label>`.* TO '<label id="RODINDB_USERNAME3" class="username">$RODINDB_USERNAME</label>'@'%';<br>
GRANT ALL PRIVILEGES ON `<label id='RODINPOSHDB_DBNAMEd' class="dbname">$RODINPOSHDB_DBNAME</label>`.* TO '<label id="RODINDB_USERNAME4" class="username">$RODINDB_USERNAME</label>'@'%';<br>
GRANT ALL PRIVILEGES ON `<label id='ARCDB_DBNAMEd' class="dbname">$ARCDBBASENAME</label>`.* TO '<label id="RODINDB_USERNAME5" class="username">$RODINDB_USERNAME</label>'@'%';<br>
GRANT ALL PRIVILEGES ON `<label id='SRCDB_DBNAMEd' class="dbname">$SRCDBBASENAME</label>`.* TO '<label id="RODINDB_USERNAME5" class="username">$RODINDB_USERNAME</label>'@'%';<br>
GRANT SELECT ON `<label id='ADMINDB_DBNAMEd' class="dbname">$RODINADMIN_DBNAME</label>`.* TO '<label id="RODINADMIN_USERNAME" class="ausername">$RODINADMIN_DBNAME</label>'@'%';<br>
GRANT ALL PRIVILEGES ON `<label id='ADMINDB_DBNAMEd0a' class="dbname">$RODINADMIN_DBNAME</label>`.* TO '<label id="RODINADMIN_USERNAME0gen" class="xusername">$RODINADMIN_DBNAME-0</label>'@'%';<br>
<!-- /span --> </td></tr>
<tr><td/><td class='expl2'>
<input type="checkbox" name='USERWASCREATEDINMYSQLDB' $STEP1_ONCLICK> Yes I created these users in my mysql DBMS
		</td>
<tr><td/><td class='expl2'>
<input type="checkbox" name='skipdb' > Skip install databases because already done (takes too long to re-execute)
		</td>
<tr>




<tr height="10"></tr>
<tr></tr>
<tr><td width="$inputwidth" width="$marginleft"><td title='' colspan=*3*><span class='expl4'> The following rodin instance will be created:</span>
		</td>
<tr><tr><td width="$marginleft"><td title='' colspan=*3*><span class='expl2'> 
<input type="hidden" name="OLDRODINDOCROOTx" value="$OLDRODINDOCROOT">
<label id="NEWRODINDOCROOTx" class="codeinstance">{$NEWRODINDOCROOT}</label> - holding your new RODIN instance 
<input type="hidden" name="OLDRODINWEBROOTx" value="$OLDRODINWEBROOT">
<label id='NEWRODINWEBROOTx' class='codeinstance'>$NEWRODINWEBROOT</label><br>
</span>
		</td>
<tr>



<!-- THE BUTTON -->
<tr height="10"></tr>
<tr ><td width="$marginleft">
		<td colspan="3">
			<hr size=1>
		</td>
</tr><tr height="10"></tr>
<tr ><td width="$marginleft">
		<td colspan="3" id="installbuttontd">
			<input width="100" disabled type="button" style="width:{$allwidth}px" name="installbutton" value="Install RODIN DBs and continue" $STEP1_ONSUBMIT>
		</td>
</tr>
<tr height="10"></tr>

<script type="text/javascript"><!--

update_rodin_install_inputs_step1();

// --></script>

EOP;

	} # case 1
} # switch 


print <<<EOP
</table>
</form>



</body>
</html>
EOP;


###### TODOs
###### --> PFAD HTDOCS WEB SERVER (Siehe Root.php) im Dialog abfragen und setzen
###### --> Variablen aus dem jew. root.php ansehen und im Dialog ggf. abfragen und in die neue root-osxphp setzen
###### --> DB+Usersnames in den jeweiligen root-osx.php/sroot-osx.php setzen 







function step_1_execution(	
													$skipdb,
													$newrodinsegment,
													$newdbusername,
													$newdbuserpass,
													$newrodindb_dbname,
													$newrodinposhdb_dbname,
													$newarcdb_dbname,
													$newsrcdb_dbname,
													$newrodinadmindb_dbname, //maybe here not processed
													$newadmindbusername,
													$newadmindbuserpass
												 )
####################################################
####################################################
#
# Perform input security values (proxy+web server)
# Assume: Print is inside a <table>
{
	$result_ok=true;
	global $ICON_DONE;
	global $verbose;
	global $DOCROOT,$RODINROOT;
	
	
	$rodinroot="$DOCROOT$RODINROOT";
	
	if ($verbose)
	{
		print "step_1data_execution<br>";
		print "<br>rodinroot:<b>$rodinroot</b>";
		print "<br>newrodinsegment:<b>$newrodinsegment</b>";
		print "<br>newdbusername:<b>$newdbusername</b>";
		print "<br>newdbuserpass:<b>$newdbuserpass</b>";
		print "<br>newrodindb_dbname:<b>$newrodindb_dbname</b>";
		print "<br>newrodinposhdb_dbname:<b>$newrodinposhdb_dbname</b>";
		print "<br>newarcdb_dbname:<b>$newarcdb_dbname</b>";
	
		
		
		fontprint ("<br> adapt ? XSLT service ?",'red');
		
	}
	$dbcnt=0;
	$result_ok=true;
	# CREATE DATABASES
	try {
		if (!$skipdb) {
		#############################################################################################
		
			if(!$RODINDB_imported = $result_ok = 
									DropCreateImportDB(	$newrodindb_dbname,
																			$newdbusername,
																			$newdbuserpass,
																			"rodin.sql" ))
							fontprint("<br>$newrodindb_dbname not successfully imported",'red');
			else {
				$dbcnt++;
				$databases=$newrodindb_dbname;
			}
		}																						
		#############################################################################################
		if (!$skipdb && $result_ok)
		{
			if (!$RODINPOSHDB_imported = $result_ok = 
								DropCreateImportDB(	$newrodinposhdb_dbname,
																		$newdbusername,
																		$newdbuserpass,
																		"rodinposh.sql" ))
						fontprint("<br>$newrodinposhdb_dbname not successfully imported",'red');
			else {
				$dbcnt++;
				$databases.=", $newrodinposhdb_dbname";
			}
		}
		#############################################################################################
		if (!$skipdb && $result_ok)
		{
			if (!$ARCDB_imported= $result_ok = 
								DropCreateImportDB(	$newarcdb_dbname,
																		$newdbusername,
																		$newdbuserpass,
																		"arcstore.sql")) 
						fontprint("<br>$newarcdb_dbname not successfully imported",'red');
			else {
				$dbcnt++;
				$databases.=", $newarcdb_dbname";
			}
		}
		#############################################################################################
		if (!$skipdb && $result_ok)
		{
			if (!$ARCDB_imported= $result_ok = 
								DropCreateImportDB(	$newsrcdb_dbname,
																		$newdbusername,
																		$newdbuserpass,
																		"src.sql")) 
						fontprint("<br>$newsrcdb_dbname not successfully imported",'red');
			else {
				$dbcnt++;
				$databases.=", $newsrcdb_dbname";
			}
		}
		if (!$skipdb && $result_ok)
		{
			if (!$ARCDB_imported= $result_ok = 
								DropCreateImportDB(	$newrodinadmindb_dbname,
																		$newadmindbusername,
																		$newadmindbuserpass,
																		"admin.sql")) 
						fontprint("<br>$newrodinadmindb_dbname not successfully imported",'red');
			else {
				$dbcnt++;
				$databases.=", $newsrcdb_dbname";
			}
		}
		if (!$skipdb)
			print action_done("<b>$dbcnt databases created: $databases</b>");

			
			else 
	 		print action_skipped("<b>Databases creation skipped! Please make sure You have created all databases successfully</b>");
	 ###########################################################################################################################
		
	} 
	catch (Exception $e)
	{
		inform_bad_db($e);
	}
	
	
	
	if ($result_ok)
	{
		##########################################
		#
		# DUPLICATE RODIN eng -> $newrodinsegment
		#
		$SOURCEDIR="$DOCROOT$RODINROOT/eng";
		$TARGETDIR="$DOCROOT$RODINROOT/$newrodinsegment";
		
		if ($verbose) print "<br>DUPLICATION RODIN INTO SEGMENT $newrodinsegment"
												."<br>(You might have to modify file access permissions on your mac)"
												."<br>SOURCEDIR: $SOURCEDIR"
												."<br>TARGETDIR: $TARGETDIR";
												
												
												
		$SKIP = array ("/\.svn/",
									 "/\.DS_Store/",
									 "/root-fri/",
									 "/root-heg/",
									 "/root-jav/"
									);
												
												
		if (is_dir($TARGETDIR))
		{
			
			if (!rrmdir($TARGETDIR))
				fontprint("<br>Could not delete TARGETDIR $TARGETDIR - please do it yourself and repeat this step",'red');
			
		}
									
		if (!$result_ok = copy_recursive($SOURCEDIR, $TARGETDIR, $SKIP))
			fontprint("<br>Problem copying some files from $SOURCEDIR to $TARGETDIR - you might want to check file permissions on your file system and repeat this step",'red');
		if ($result_ok)
			print action_done("<b>RODIN eng duplicated to --> $newrodinsegment</b>. Please change permissions for your files on the new duplicated segment $newrodinsegment");
			
	}

	return $result_ok;
} #step_1_execution


	
	function step_2_execution(	
														$newrodinsegment,
														
														$newadmiemaladdr,
														$newproxyname,
														$newproxyport,
														$newproxyusername,
														$newproxyuserpass,
														$newproxyauthtype,
														$newproxyauthtype,
														$newselfusername,
														$newselfuserpass,
														$newselfauthtype,
														
														$newdbusername,
														$newdbuserpass,
														$newrodindb_dbname,
														$newrodinposhdb_dbname,
														$newarcdb_dbname,
														$newsrcdb_dbname,
														$newrodinadmindb_dbname,
														$newadmindbusername,
														$newadmindbuserpass	
															
														)
	####################################################
	####################################################
	#
	# Perform input security values (proxy+web server)
	# Assume: Print is inside a <table>
	{
		#print "<br>Entering step_2_execution.";
		
		$result_ok=true;
		global $verbose, $ICON_DONE;
		global $DOCROOT,$RODINROOT;
		
		$rodinroot="$DOCROOT$RODINROOT";
		
		if ($verbose)
		{
			print "step_2data_execution<br>";
			print "<br>newadmiemaladdr:<b>$newadmiemaladdr</b>";
			print "<br>newproxyname:<b>$newproxyname</b>";
			print "<br>newproxyport:<b>$newproxyport</b>";
			print "<br>newproxyusername:<b>$newproxyusername</b>";
			print "<br>newproxyuserpass:<b>$newproxyuserpass</b>";
			print "<br>newproxyauthtype:<b>$newproxyauthtype</b>";
			print "<br>newselfusername:<b>$newselfusername</b>";
			print "<br>newselfuserpass:<b>$newselfuserpass</b>";
			print "<br>newselfauthtype:<b>$newselfauthtype</b>";
			print "<br> <b>adapt new root-osx.php ()";
			print "<br> <b>adapt new sroot-osx.php ()";
		}	
			
		$ok=true;
		$DB_HOST	=$_SERVER["HTTP_HOST"];
		
		
		#insert into the specific new segment operational DB the DB
		# - the PROXY-/User access parameters:
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'RODINADMINEMAILADDR',$newadmiemaladdr,'text');
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'PROXY_NAME',$newproxyname,'text');
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'PROXY_PORT',$newproxyport,'number');
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'PROXY_AUTH_USERNAME',$newproxyusername,'text');
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'PROXY_AUTH_PASSWD',$newproxyuserpass,'text');
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'PROXY_AUTH_TYPE',$newproxyauthtype,'text');
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'AUTH_SELF_USERNAME',$newselfusername,'text');
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'AUTH_SELF_PASSWD',$newselfuserpass,'text');
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'AUTH_SELF_TYPE',$newselfauthtype,'text');

		
		# - the DB access parameters:
		//// importare i paramteri 
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'RODINDB_DBNAME',$newrodindb_dbname,'text');
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'RODINDB_USERNAME',$newdbusername,'text');
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'RODINDB_PASSWD',$newdbuserpass,'text');
		
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'RODINPOSHDB_DBNAME',$newrodinposhdb_dbname,'text');
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'RODINDB_USERNAME',$newdbusername,'text');
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'RODINDB_PASSWD',$newdbuserpass,'text');
		
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'RODINPOSHDB_DBNAME',$newrodinposhdb_dbname,'text');
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'RODINPOSHDB_USERNAME',$newdbusername,'text');
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'RODINPOSHDB_PASSWD',$newdbuserpass,'text');
		
		
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'ARCDB_DBHOST',$DB_HOST,'text');
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'ARCDB_DBNAME',$newarcdb_dbname,'text');
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'ARCDB_USERNAME',$newdbusername,'text');
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'ARCDB_USERPASS',$newdbuserpass,'text');
		
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'SRCDB_DBHOST',$DB_HOST,'text');
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'SRCDB_DBNAME',$newsrcdb_dbname,'text');
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'SRCDB_USERNAME',$newdbusername,'text');
		$ok = $ok && update_admin_key($newrodinsegment,$newadmindbusername,$newadmindbuserpass,'SRCDB_USERPASS',$newdbuserpass,'text');

		
		#print "<br>Exiting step_2_execution.";
		
		
		return $ok;
	} #step_2_execution
	
	
	
	
	function step_3_execution(
														$newrodinsegment,
														$newadmipseudo,
														$newadmiemailaddr,
														$newadmipass,
														$newrodinuseremailaddr,
														$newrodinuserpass
														)
	####################################################
	####################################################
	#
	# Perform input security values (proxy+web server)
	# Assume: Print is inside a <table>
	{
		$result_ok=true;
		global $verbose;
		global $DOCROOT,$RODINROOT;
		
		$rodinroot="$DOCROOT$RODINROOT";
		
		if ($verbose)
		{
			print "step_3data_execution<br>";
			print "<br>newadmiemaladdr:<b>$newrodinsegment</b>";
			print "<br>newadmipseudo:<b>$newadmipseudo</b>";
			print "<br>newadmiemailaddr:<b>$newadmiemailaddr</b>";
			print "<br>newrodinuseremailaddr:<b>$newrodinuseremailaddr</b>";
			print "<br>newrodinuserpass:<b>$newrodinuserpass</b>";
			print "<br> <b>adapt new ADMIN users in portaneo";
			print "<br> <b>adapt new RODIN user in portaneo";
		}
		
		
		if ($result_ok)
		{
	
	
			
			
		}
		 // result ok
		
		return $result_ok;
	} #step_3_execution
														
	
	
	

	
	function DropCreateImportDB($DBNAME,$USERNAME,$PASSWD,$filename)
	################################################################
	{
		global $verbose;
		global $ICON_DONE;
		
		$filepath	= "../../../makeinstall/db/".$filename;
		$DB_HOST	=$_SERVER["HTTP_HOST"];
		
		if (!$DBconn 	= mysql_connect($DB_HOST,$USERNAME,$PASSWD)) 
		{
			fontprint("Could not connect to DBMS using $DB_HOST/$USERNAME/$PASSWD <br>Please control user password",'red');
			exit;
		}
		print "<br><hr> Trying to create RODIN database <b>$DBNAME</b> with user <b>$USERNAME</b>";fontprint("/$PASSWD",'white');
		@mysql_select_db($DBNAME,$DBconn) or $NODB=1;
		if ($NODB)
		{
			$query  = "CREATE DATABASE `$DBNAME` ;";
			$result = mysql_query($query,$DBconn);
			if (mysql_error($DBconn)) {fontprint(mysql_error($DBconn),'red'); exit;}
		}
		else 
		{
			mysql_query("DROP DATABASE `$DBNAME` ;",	$DBconn);	if (mysql_error($DBconn)) {fontprint(mysql_error($DBconn),'red'); exit;}
			mysql_query("CREATE DATABASE `$DBNAME` ;",$DBconn);	if (mysql_error($DBconn)) {fontprint(mysql_error($DBconn),'red'); exit;}
			#Again select it
			mysql_select_db($DBNAME,$DBconn); if (mysql_error($DBconn)) {fontprint(mysql_error($DBconn),'red'); exit;}
		}
	
		$errors='';
		@mysql_select_db($DBNAME,$DBconn) or $errors = "Could not create database $DBNAME with user $USERNAME/$PASSWD : ".mysql_error($DBconn);
		if ($errors) fontprint("<br>".$errors,'red');
		else {
			//import the DB Dump into thie DB
			$DB_imported = db_import($DBconn,$filepath,$DBNAME);
			if ($DB_imported) {
				print action_done("Database <b>$DBNAME</b> created");
			}
			mysql_close($DBconn);
		}
		return $DB_imported;
	}
	
	
	
	
	
	
	
	function db_import($DBconn,$filepath,$DBNAME)
	################################################
	{
		$ok=true;
		global $verbose;
		$recordcount=-1;
		 $import = file_get_contents($filepath) or $ok=false;
		 if ($ok)
		 {
		   $import = preg_replace ("%/\*(.*)\*/%Us", '', $import);
		   $import = preg_replace ("%^--(.*)\n%mU", '', $import);
		   $import = preg_replace ("%^$\n%mU", '', $import);
			 try {
			   mysql_real_escape_string($import); 
			   if (mysql_error($DBconn)) fontprint("<br>".mysql_error($DBconn),'red');
			   
			   //print "<br>IMPORT: $import";
			   
			   $import_arr = explode (";", $import); 
				 $i=0;
				 $oldtablename='';
			   foreach ($import_arr as $imp)
			   {
			    if (trim($imp) != '')
			    {
			    	//print "<br>IMPORT $i: $imp";
			    	
			    	$FRIEXPECTEDRECORDSPATTERN="/FRI:INFOTABLE $currenttable: (\d+) RECORDS/";
			    	$FRIENDPATTERN="/FRI:END/";
			    	if (preg_match($FRIEXPECTEDRECORDSPATTERN,$imp,$exprecs))
			    	{	if ($verbose) print "<br> $currenttable expected records: ".$exprecs[1];
				    	$expectedRecords=$exprecs[1];
			    	}
			    	else if (preg_match($FRIENDPATTERN,$imp))
			    	{	
			    		print_added_records($recordcount); #END OF FILE
			    	}
			    	else 
			    	{
			    		//print "<br>NOMATCH $FRIEXPECTEDRECORDSPATTERN in $imp";
				    	if (preg_match("/INSERT INTO $currenttable/",$imp))
				    	{	#print " count*";
				    		$decoded_imp = decode_on_base64text($imp);
					    	if ($verbose)
					    	{
					    		if ($decoded_imp<>$imp)
					    		{
						    		fontprint('<br>base64codedterm: '.$imp,'blue');
						    		fontprint('<br>base64decodedterm: '.$decoded_imp,'green');	    		
					    		}
					    		else 
					    			fontprint('<br>term: '.$imp,'blue');
					    	}
					    	$imp = $decoded_imp;
					    	$recordcount++;
				    	}
				    	else if (preg_match("/CREATE TABLE `(.*)` \(/",$imp,$match))
				    	{	
				    		if ($oldtablename) if ($oldtablename) print_added_records($recordcount);
				    		
				    		$oldtablename = $currenttable;
				    		#print "<br>Setting $currenttable to ".$match[1];
				    		$currenttable=$match[1];
					    	print "<br>Creating table <b>".$currenttable.'</b> ...';
					    	$recordcount=0;
				    	}
				    	else {
				    		//END OF VALUES (NO VALUES AND NO CREATES)
				    		if ($oldtablename) print_added_records($recordcount);
				    		
				    		if ($expectedRecords <> $recordcount) fontprint("<br>Expected $expectedRecords but found $recordcount records for table $currenttable!!!",'red');			    			
				    		
				    		$oldtablename = $currenttable;
				    		$currenttable = '';
				    	}
				    	
				    	//fontprint ("<br> Calling ($imp)",'red');
				    	mysql_query($imp);
				     $i++;
				     if (mysql_error($DBconn)) {throw(New Exception(mysql_error($DBconn)."<hr>$i Import: ".$imp."<br><br>"));}
				    }
				   }  
			   } // expected records
			 }
			 catch (Exception $e)
			 {
			 	inform_bad_db($e);
			 	$ok=false;
			 }
		 }
		 return $ok;
	}




	
	function print_added_records($recordcount) 
	{
		if ($recordcount) fontprint(" ($recordcount records)",'green');
		else fontprint(" ($recordcount records)",'gray');
	}
	
	
	
	
	
	
	
	function decode_on_base64text($imp)
	###################################
	#
	# Returns the base64_decoded iff $imp containts a VALUES() statement
	# INSERT INTO name VALUES("NDM=",...)
	{
		global $verbose;
		
		$PATTERN='/INSERT INTO(.*)VALUES\((.*)\)/';
		if (preg_match($PATTERN,$imp,$match))
		{
			//for($i=0;$i<count($match);$i++) print "<br>".$i.": ".$match[$i];
			$tbd_arr = explode(',',$match[2]);
			$tablename=$match[1];
			$tbd_record='';
		 		
		 foreach ($tbd_arr as $tbd)	
		 {
		 	if ($tbd_record<>'') $tbd_record.=',';
		 	if ($tbd=='""')	
		 	  $tbd_record.='""';
		 	else
		 	{
	  		$value=base64_decode($tbd);
	  		
	  		if (is_numeric($value))
		  		$tbd_record.=$value;
		  	else 
		  		$tbd_record.='"'.$value.'"';
		 	}	
		 }
		 $tbd_record ='INSERT INTO '.$tablename.' VALUES(' .$tbd_record. ')';
		 $imp=$tbd_record;	
		 //print "<br>DECODED: ".$tbd_record;
		 
		}
		//else print "<br> NOMATCH $PATTERN on $imp";
		
		return $imp;
	}




/*
	print <<<EOP
<tr height="10"></tr>
<tr><td width="$marginleft">
		<td width="$textwidth"><span class="expl0">Please fill in your proxy IP if you need one </span></td>
		<td width="$inputwidth" valign="top">
			-><input name='NEWPROXYNAME' value='$NEWPROXYNAME' size="20" $STEP2_ONKEYUP title="Please type a your proxy address (IP or simbolic)">
		</td>
<tr>
EOP;
*/
// function userinputline('NEWPROXYNAME',$NEWPROXYNAME,20,$STEP2_ONKEYUP,true,"Please type your proxy address (IP or simbolic) if a proxy is needed","Please type your proxy address (IP or simbolic)",'expl0')
function userinputline($INPUTNAME,$INPUTVALUE,$INPUTSIZE,$ONKEYUP,$NL,$TEXT,$TITLE,$EXPLCLASS)
{
	global $marginleft;
	global $textwidth;
	global $inputwidth;
	
	if ($NL) $NEWLINE="<tr height=\"10\"></tr>";
	return <<<EOP
$NEWLINE
<tr><td width="$marginleft">
		<td width="$textwidth"><span class="$EXPLCLASS">$TEXT </span></td>
		<td width="$inputwidth" valign="top">
			-><input name='$INPUTNAME' value='$INPUTVALUE' size="$INPUTSIZE" $ONKEYUP title="$TITLE">
		</td>
<tr>
EOP;
}


	
	function copy_recursive($source,$dest,&$SKIP)
	{
		#Try to make $dest or warn user
		if ($dest<>'' and !is_dir($dest))
		{
			$rootdir = dirname($dest);
			$rootdirpermissions=substr(sprintf('%o', fileperms($rootdir)), -4); 
			$betterrootdirpermissions = $rootdirpermissions + 12; #writable for others
			if (!$res=mkdir($dest))
				fontprint("Could not create destination dir ($dest inside dir $rootdir with permissions=$rootdirpermissions) - please make upper dir $rootdir writable (chmod $betterrootdirpermissions $rootdir) and repeat this step<br><br>",'red');
			else 
				$res = copyr($source,$dest,$SKIP); 
		}
		return $res;
	}



	function copyr($source, $dest, &$SKIP)
	#####################################
	{
		global $verbose;
	
		// Simple copy for a file
		if (skipfile(basename($source),$SKIP))
		{
			$ok=true;
			if ($verbose) fontprint("<br>Skiping $source",'green');
		}
		else 
		{
			
			if (is_file($source)) 
			{
				if ($verbose) print "<br>copy $dest";
				$c = copy($source, $dest);
				chmod($dest, fileperms($source));
				return $c;
			}
		 
			// Make destination directory
			if (!is_dir($dest)) 
			{
				if ($verbose)
					print "<br>Creating dir ($dest)...";
				$oldumask = umask(0);
				if (!$res = mkdir($dest, fileperms($source)))
					fontprint("<br>Problem mkdir($dest,".fileperms($source),'red');
				if ($res)
					umask($oldumask);
			}
		 
			// Loop through the folder taking the return success value
			$dir = dir($source);
			$ok=true;
			while ((false !== $entry = $dir->read()) && $ok) 
			{
				// Skip pointers
				if ($entry == "." || $entry == "..") 
				{
					continue;
				}
			 
				// Deep copy directories
				if ($dest !== "$source/$entry") 
				{
					$ok= copyr("$source/$entry","$dest/$entry", $SKIP);
				}
			}
		 
			// Clean up
			$dir->close();
		} // skip
		return $ok;
	}




	function skipfile($filename,$SKIP)
	##################################
	#
	# Returns true if file is to be skipped
	# i.e. the filename matches one of
	# the patterns in $SKIP array
	{
		foreach($SKIP as $PATTERN)
		{
			if (preg_match($PATTERN,$filename))
			{
				$found=true;
				break;
			}
		}
		
		return $found;
		
	}





	function rrmdir($dir) {
	   if (is_dir($dir)) {
	     $objects = scandir($dir);
	     foreach ($objects as $object) {
	       if ($object != "." && $object != "..") {
	         if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
	       }
	     }
	     reset($objects);
	     $res = rmdir($dir);
	   }
	   return $res;
	 } 

 
 
	 

	 
	 function action_done($txt)
	 ##########################################
	 {
	 	global $ICON_DONE;
	 	$txt =<<<EOT
	 	<tr><td width="$marginleft">
	<td colspan="3" width="$textwidth" valign="top" align="left"><span class="explDONE"> $ICON_DONE $txt </span></td>
</tr>	
EOT;
		return $txt;
	 }
	 
	 
	 
	 
	 function action_skipped($txt)
	 ##########################################
	 {
	 	global $ICON_SKIPPED;
	 	$txt =<<<EOT
	 	<tr><td width="$marginleft">
	<td colspan="3" width="$textwidth" valign="top" align="left"><span class="explDONE"> $ICON_SKIPPED $txt </span></td>
</tr>	
EOT;
		return $txt;
	 }
	 
	 
	 
	 
	 
	 function miau_nowrite($filepath,$e,$filepermissions)
	 ####################################################
	 {
	 		
		 	$here=getcwd();
		 	if ($e) $EXEPT=" - ($e)";
	 		fontprint("<br> Could not write access $filepath in $here $EXEPT with file permissions $filepermissions.<br>"
		 						."Please make it and its directory WRITE-accessible for the web server and retry this step",'red');
	 }
	 
	 
	 
?>