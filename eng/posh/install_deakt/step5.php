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
/* à é ô ù */
require("header.inc.php");

$checks=true;

$DB->getResults($install_getAdminUsers);
if ($DB->nbResults()>0){
	echo "<script type='text/javascript'>window.location='scr_cache_all.php';</script>";
	exit();
}
$DB->close();
?>
<div class="bottomhr"><h1><?php echo lg("installation".I_APPLICATION_ID);?></h1></div>
<br />
<h2><?php echo lg("step5Title");?></h2><br />
<?php
	if (isset($_GET["err"])){
		echo "<font style='font-size:14pt;color:#ff0000;'>";
		switch($_GET["err"])
		{
			case "1":echo lg("passwordConfError");break;
			case "2":echo lg("msgSubPassToShort");break;
		}
		echo "</font><br /><br />";
	}
?>
<form action="scr_step5.php" method="post">
<table cellpadding="5" cellspacing="0">
<tr><td><?php echo lg("username");?></td><td><input type=text name="username" size=30 maxlength=64 /></td></tr>
<tr><td><?php echo lg("password");?></td><td><input type=password name="password" size=30 maxlength=16 /></td></tr>
<tr><td><?php echo lg("confpass");?></td><td><input type=password name="confpass" size=30 maxlength=16 /></td></tr>
</table>
<br /><br />
<center><input type="submit" value="<?php echo lg("continue");?> >>>" />
</form>

<?php
require("footer.inc.php");
?>