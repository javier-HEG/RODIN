<?php
# ************** LICENCE ****************
/*
	Copyright (c) PORTANEO.

	This file is part of POSH (Portaneo Open Source Homepage) http://sourceforge.net/projects/posh/.

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
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=0;
$isScript=false;
$isPortal=false;
$pagename="portal/addtoapplication.php";
//includes
require_once('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');

// get module information
$nomodule=false;
$var="";

$md5key = isset($_GET["chk"])?$_GET["chk"]:'';
if (isset($_GET["chk"]) && isset($_GET["id"]) && isset($_GET["tab"]))
{
	$DB->getResults($addtoapplication_getModule,$DB->quote($_GET["chk"]),$DB->escape($_GET["id"]),$DB->escape($_GET["tab"]));
	if ($DB->nbResults()==0)
	{
		$nomodule=true;
	}
	else
	{
		$row = $DB->fetch(0);
		$name=$row["name"];
		$mid=$row["id"];
		$murl=$row["url"];
		$size=$row["height"];
		$mformat=$row["format"];
        $var=$row["variables"];
		$nbvars=$row["nbvariables"];
	}
	$DB->freeResults();
}
//if module id is sent
elseif (isset($_GET["pid"]))
{
	//security improvement : get only modules shared in directory
	$var="";
	if (isset($_GET["rssurl"]))
	{
		if ($_GET["pid"]==86)
		{
			$DB->getResults($addtoapplication_getUserRssInfo,$DB->quote('rssurl='.$_GET["rssurl"]));
			$var="rssurl=".urlencode($_GET["rssurl"])."&";
		}
		else
		{
			$DB->getResults($addtoapplication_getRssInfo,$_GET["rssurl"],$DB->escape($_GET["pid"]));
		}
	}
	else
	{
		$DB->getResults($addtoapplication_getModuleById,$DB->escape($_GET["pid"]));
	}
	if ($DB->nbResults()==0)
	{
		$nomodule=true;
	}
	else
	{
		$row = $DB->fetch(0);
		$name=$row["name"];
		$mid=$row["id"];
		$murl=$row["url"];
		$size=$row["height"];
		$mformat=$row["format"];
		$var.=$row["defvar"];
		$nbvars=$row["nbvariables"];
	}
	$DB->freeResults();
}
//if rss link is sent
elseif (isset($_GET["url"]) AND isset($_GET["name"]))
{
	$name=$_GET["name"];
	$var="nb=5&rssurl=".urlencode($_GET["url"]);
	$mid=86;
	$size=246;
	$murl="../modules/p_rss.php?";
	$mformat="R";
	$nbvars=1;
}
else
{
	$nomodule=true;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>... Loading</title>
	<link rel="stylesheet" href="../styles/main.css?v=<?php echo __POSHVERSION;?>" type="text/css" />
	<link rel="stylesheet" href="../../app/exposh/styles/main1.css.php?v=<?php echo __POSHVERSION;?>&skin=<?php print get_rodin_skin();?>" type="text/css" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="text/javascript" src="../l10n/<?php echo __LANG;?>/lang.js?v=<?php echo __POSHVERSION;?>" ></script>
	<script type="text/javascript" src="../includes/config.js?v=<?php echo __rand;?>" ></script>
	<script type="text/javascript" src="../tools/mootools.js?v=1.2.1"></script>
	<script type="text/javascript" src="../includes/ajax.js?v=<?php echo __POSHVERSION;?>" ></script>
	<script type="text/javascript" src="../includes/php/ajax-urls.js?v=<?php echo __POSHVERSION;?>"></script>
<?php
	launch_hook('userinterface_header',$pagename);
?>
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
	<div id="newmod"></div>
	<table cellpadding="10" cellspacing="0" border="0" width="100%">
		<tr>
		<td align="left" valign="top" style="padding-top:30px">
<?php
if ($nomodule)
{
	echo "<b>".lg("missingModule")." !</b><br /></td></tr><tr><td style='height:476px' valign='top'><br /><a href='index.php'>".lg("returnHome")."</a>.</td></tr>";
	exit();
}
?>
		<b><?php echo lg("moduleInvitationTxt");?> <u><?php if ($mid != 86) echo $name;?></u></b>
		<br /><br />
		<div id="widgetcontainer" width="400"></div>

<?php if (!is_array($_SESSION) || !array_key_exists("user_id",$_SESSION)) echo '<img src="../images/ico_logon.gif" align="absmiddle" /> <b>'.((__defaultmode=="connected")?lg("pleaseConnectToYourPortals"):lg("addModAlreadyAccount")." ?").'</b><br /><br />';?>


<?php
if (__defaultmode!="connected")
{
	echo '<div class="cleardiv">';
    echo '<center><input type="button" value="'.lg("showPageWithModule").'" onclick=\'$p.navigator.openLink("index.php")\' /></center>';

?>
		<input type="hidden" name="open" value="<?php echo $mid;?>" /><input type="hidden" name="vars" value="<?php echo $var;?>" />
		</center>
		</form>
		</div>
		<br />


<?php
}
echo '<div id="connectiondiv" class="cleardiv">';
if (__defaultmode!="connected") echo "<b><u>".lg("yes")."</u></b>, ".lg("addModuleOnPortal")."<br /><br />";
?>


			<form name="f" method="post" onsubmit="return $p.app.connection.set(this,getAvailPortals,false,'<?php echo $md5key;?>')">
			<div id="msg_conn"></div>
			<table cellspacing="0" cellpadding="0" border="0" width="400">
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
<script type="text/javascript"><!--
__useoverview=false;

$p.app.standalone($('widgetcontainer'),1,true);

<?php
if ($mformat=="R")
{
	echo "jspass='';";
	echo "$"."p.app.widgets.rss.checkFeed($"."p.string.getVar('".$var."','rssurl'));";
}
else
{
?>
// add the widget
tab[0].module[0]=new $p.app.widgets.object(1,1,1,<?php echo $size;?>,<?php echo $mid;?>,"--","--","<?php echo $var;?>",280,1,280,"<?php echo $murl;?>",0,0,1,"<?php echo $mformat;?>",<?php echo $nbvars;?>,1);
tab[0].module[0].create();
tab[0].module[0].show();
<?php
}
?>
$p.cookie.check();
$p.navigator.noinclusion();
//get the user pages
<?php
   // if (isset($_SESSION['user_id'])) {
?>
 //       getAvailPortals(true,'<?php echo $md5key;?>');
<?php
  ///  }
?>

//change page title
$p.navigator.changeTitle("<?php echo $name;?>");
</script>
<?php
$DB->close();
?>
</body>
</html>