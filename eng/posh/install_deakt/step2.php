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

if (isset($_GET["installtype"])) setStep(2,$_GET["installtype"]);
$installtype=isset($_GET["installtype"])?$_GET["installtype"]:1;

$collab_suite = isset($_POST['collab_suite']) ? $_POST['collab_suite'] : '';

//setStep(2,__INSTALLTYPE);
?>
<div class="bottomhr"><h1><?php echo lg("installation".I_APPLICATION_ID);?></h1></div>
<br />
<h2><?php echo lg("step2Title");?></h2><br />
<?php
	if (isset($_GET["err"])){
		echo "<FONT style='font-size:14pt;color:#ff0000;'>";
		switch($_GET["err"]){
			case "1":echo lg("badUserPassServer");break;
			case "2":echo lg("missingCreateDbRights");break;
			case "3":echo lg("previousInstallationExist");break;
			case "4":echo lg("tableCreationIssue");break;
		}
		echo "</FONT><br /><br />";
	}
?>
<form action="scr_step2.php" method="post">
<table cellpadding="5" cellspacing="0">
<?php
echo "<tr><td>".lg("dbtype")."</td><td><select name='dbtype'><option value='mysql'".((defined('__DBTYPE') && __DBTYPE=='mysql')?" selected='selected'":"").">mysql</option></select></td></tr>\n";
echo "<tr><td>".lg("server")."</td><td><input type='text' name='server' size='30' value='".((defined('__SERVER'))?__SERVER:"localhost")."' /></td></tr>\n";
echo "<tr><td>".lg("login")."</td><td><input type='text' name='login' size='30' value='".((defined('__LOGIN'))?__LOGIN:"")."' /></td></tr>\n";
echo "<tr><td>".lg("password")."</td><td><input type='password' name='pass' size='30' value='' /></td></tr>\n";
echo "<tr><td>".lg("database")."</td><td><input type='text' name='db' size='30' value='".((defined('__DB'))?__DB:"")."' /></td></tr>\n";
?>
</table>
<?php
    if ($collab_suite == "on") {
        if ($installtype==2) echo '<p><input type="checkbox" name="reprev" /> '.lg("removePrevInstall") . "</p>";
        echo '<input type="hidden" name="collab_suite" value="on" />';
    }
?>

    <br /><center><input type="submit" value='<?php echo lg("createDatabase");?> >>>'></center>
</form>

<?php
require("footer.inc.php");
?>