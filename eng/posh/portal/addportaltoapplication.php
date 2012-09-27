<?php
# ************** LICENCE ****************
/*
	Copyright (c) PORTANEO.

	This file is part of COLLABORATION SUITE of POSH http://sourceforge.net/projects/posh/.

	POSH is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version

	POSH is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Posh.  If not, see <http://www.gnu.org/licenses/>.
*/
# ***************************************
# Add a module to my portal (step 1 : existing or new destination portal)
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="";
$not_access=0;
$isScript=false;
$isPortal=false;
$pagename="portal/addportaltoapplication.php";
//includes
require_once('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
require_once('../../app/exposh/l10n/'.__LANG.'/enterprise.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');

$id=$_GET["id"];
$check=$_GET["chk"];

//get portal information
$DB->getResults($addportaltoapplication_getPortal,$DB->escape($id),$DB->quote($check));
if ($DB->nbResults()==0) exit();
$row=$DB->fetch(0);
$pName=$row["name"];
$DB->freeResults();

//get portal widgets information
$pModuleName=array();
$pModuleId=array();
$DB->getResults($addportaltoapplication_getModules,$DB->escape($id),$DB->quote($check));
while ($row=$DB->fetch(0))
{
	$name=$row["name"];
	$vars=explode("&",$row["variables"]);
	for ($i=0;$i<count($vars);$i++)
	{
		$var=explode("=",$vars[$i]);
		if ($var[0]=="ptitl") $name=urldecode($var[1]);
	}
	array_push($pModuleName,$name);
	array_push($pModuleId,$row["id"]);
}
$DB->freeResults();


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>... Loading</title>
	<link rel="stylesheet" href="../styles/main.css?v=<?php echo __POSHVERSION;?>" type="text/css" />
	<link rel="stylesheet" href="../../app/exposh/styles/main1.css.php?v=<?php echo __POSHVERSION;?>&skin=<?php print get_rodin_skin();?>" type="text/css" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<script type="text/javascript" src="../l10n/<?php echo __LANG;?>/lang.js?v=<?php echo __POSHVERSION;?>" ></script>
	<script type="text/javascript" src="../includes/config.js?v=<?php echo __POSHVERSION;?>" ></script>
	<script type="text/javascript" src="../tools/mootools.js?v=1.2.1"></script>
	<script type="text/javascript" src="../includes/ajax.js?v=<?php echo __POSHVERSION;?>" ></script>
	<script type="text/javascript" src="../includes/php/ajax-urls.js?v=<?php echo __POSHVERSION;?>"></script>

<?php
	launch_hook('userinterface_header',$pagename);
?>
</head>
</head>
<body>
<div id="cache" style="position:absolute;left:0;top:0;z-index:8;display:none;"></div>
<div class="noportal">
<table cellpadding="0" cellspacing="0" border="0" style="width:100%">
	<tr>
	<td id="header">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
		<td id="logo"><img src="../images/s.gif" width="140" height="60" /></td>
		</tr>
	</table>
	</td>
	</tr>
	<tr>
	<td align="center" valign="top">
	<table cellpadding="10" cellspacing="0" border="0" width="100%">
		<tr>
		<td align="left" valign="top" style="padding-top:20px">
		<b><?php echo lg("portalInvitationTxt");?> : <u><?php echo $pName;?></u></b><br /><br />
		<b><?php echo lg("modules");?> :</b>
		<br />
<?php
	for ($i=0;$i<count($pModuleName);$i++)
	{
		echo "<img src='../modules/pictures/box0_".$pModuleId[$i].".ico' align='absmiddle' /> ".$pModuleName[$i]."<br />";
	}
?>
		</td><td width="400" align="left" style="padding-top:10px">
		<img src="../images/ico_logon.gif" align="absmiddle" /> <b><?php echo lg("pleaseConnectToYourPortals");?></b><br /><br />
		<div id="msg_conn"></div>
		<div id="connectiondiv" class="cleardiv">
			<form name="f" method="post" onsubmit="return $p.app.connection.set(this,$p.friends.addPortal)">
			<input type="hidden" name="id" value="<?php echo $id;?>" /><input type="hidden" name="check" value="<?php echo $check;?>" />
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
				<td><?php echo lg("email");?> : </td>
				<td><input name="username" id="username" type="text" size="17" maxlength="64" value="" /></td>
				</tr>
				<tr>
				<td style="padding-top:3px" valign="top"><?php echo lg("password");?> : </td>
				<td><input name="password" type="password" size="17" maxlength="16" /> <a href="#" onclick="$p.app.connection.buildmissingPasswordForm();" style="font-size:7pt;color:#000000;"><?php echo lg("passwordMissing");?> ?</a></td>
				</tr>
				<tr><td></td><td><br /><input type="submit" value="<?php echo lg("connection");?>" /></td></tr>
			</table>
			</form>
		</div>
		<div id="displayPart"></div>
		</td>
		</tr>
	</table>
	</td>
	</tr>
</table>
</div>
<div id="debug"></div>
</center>
<script type="text/javascript">
<?php if (isset($_SESSION['user_id'])) echo "$"."p.friends.addPortal(true);";?>
$p.navigator.changeTitle('<?php echo $pName;?>');
</script>
<?php
$DB->close();
?>
</body>
</html>