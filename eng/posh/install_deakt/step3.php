<?php
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
/* UTF8 encoding : é ô à ù */
require("header.inc.php");

$checks=true;
?>

<div class="bottomhr"><h1><?php echo lg("installation".I_APPLICATION_ID);?></h1></div>
<br />
<h2><?php echo lg("step3Title");?></h2><br />
<?php
	if (isset($_GET["err"])){
		echo "<FONT style='font-size:14pt;color:#ff0000;'>";
		switch($_GET["err"]){
			case "1":echo lg("badlocalfolder");break;
		}
		echo "</FONT><br /><br />";
	}
?>
<form action="scr_step3.php" method="post">
<?php
	$localfolder=getParam($DB,"LOCALFOLDER","");
	if ($localfolder==""){
		$pageUrl="http://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		$localfolder=str_replace("install/step3.php","",$pageUrl);
		if (strpos($localfolder,"?")>0){
			$localfolder=substr($localfolder,0,strpos($localfolder,"?"));
		}
	}
	$appname=getParam($DB,"APPNAME","");
	if ($appname==''){
		//help user to configure Portaneo with application location
		$appname=$_SERVER["SERVER_NAME"];
		if (strpos($appname,"/")===true){
			$appname=substr($appname,0,strpos($appname,"/"));
		}
		$appname=str_replace("www.","",$appname);
		if (strpos($appname,".")===true){
			$appname=substr($appname,0,strpos($appname,"."));
		}
	}
	$managegroups=getParam($DB,"useGroup","false");
	$usermodule=getParam($DB,"USERMODULE","I");
	$menupos=getParam($DB,"menuposition","");
	$registerfeeds=getParam($DB,"registerfeeds","true");
	$useproxy=getParam($DB,"useproxy","false");
	
	$DB->getResults($install_getFirstTheme);
	$row=$DB->fetch(0);
	$deftheme=$row["name"];
	$DB->freeResults();
	
	$DB->close();
?>
<table cellpadding="5" cellspacing="0">
<tr><td><?php echo lg("appname");?></td><td><input type="text" name="appname" size="30" maxlength="60" value="<?php echo $appname;?>" /></td></tr>
<tr><td><?php echo lg("localfolder");?></td><td><input type="text" name="localfolder" size="30" maxlength="150" value="<?php echo $localfolder;?>" /></td></tr>
<tr><td><?php echo lg("manageGroups");?></td><td><input type="checkbox" name="managegroups" <?php if ($managegroups=="true"){echo "checked='checked' ";};?>/></td></tr>
<tr><td><?php echo lg("usermodule");?></td><td><input type="checkbox" name="usermodule" <?php if ($usermodule=="I"){echo "checked='checked' ";};?>/></td></tr>
<tr><td><?php echo lg("menuPosition");?></td><td><input type="radio" name="menuposition" value="v" <?php if ($menupos=="v"){echo "checked='checked' ";};?>/> <?php echo lg("vertical");?> &nbsp; <input type="radio" name="menuposition" value="h" <?php if ($menupos=="h"){echo "checked='checked' ";};?>/> <?php echo lg("horizontal");?></td></tr>
<?php
	if ($deftheme=="classic_blue"){
		echo "<tr><td></td><td><input type='hidden' name='resettheme' value='1' /></td></tr>";
	} else {
		echo "<tr><td valign='top'>".lg("resetDefaultTheme")." :</td><td><input type='radio' name='resettheme' value='0' checked='checked' /> ".lg("continueUsingTheme")."<br /><input type='radio' name='resettheme' value='1' /> ".lg("resetTheme")."</td></tr>";
	}
?>
<tr><td valign='top'><?php echo lg("readRssFeed");?></td><td><input type="radio" name="registerfeeds" value="false" <?php if ($registerfeeds=="false"){echo "checked='checked' ";};?>/> <?php echo lg("onTheFly");?><br /><input type="radio" name="registerfeeds" value="true" <?php if ($registerfeeds=="true"){echo "checked='checked' ";};?>/> <?php echo lg("storedInDB");?></td></tr>
<?php
	if (I_APPLICATION_ID==2)
	{
		echo "<tr><td>".lg("useproxy")."</td><td><input type='checkbox' name='useproxy' ".($useproxy=="true"?"checked='checked' ":"")."/></td></tr>";
	}
?>
</table>
<br /><br />
<center><input type="submit" value="<?php echo lg("continue");?> >>>" />
</form>

<?php
require("footer.inc.php");
?>