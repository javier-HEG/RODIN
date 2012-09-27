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

//setStep(2);
?>
<div class="bottomhr"><h1><?php echo lg("installation".I_APPLICATION_ID);?></h1></div>
<br />
<h2><?php echo lg("step4Title");?></h2><br />
<h3><?php echo lg("selectModules");?></h3><br /><br />
<form action="scr_step4.php" method="post">
<table cellpadding="5" cellspacing="0">
<?php
$nbModules=0;

if (!moduleUsed($DB,84)){echo '<tr><td><input type="checkbox" name="notes" CHECKED /></td><td>'.lg("notes").'</td></tr>';$nbModules++;}
if (!moduleUsed($DB,85)){echo '<tr><td><input type="checkbox" name="favorites" CHECKED /></td><td>'.lg("favorites").'</td></tr>';$nbModules++;}
if (!moduleUsed($DB,295)){echo '<tr><td><input type="checkbox" name="tasks" CHECKED /></td><td>'.lg("tasks").'</td></tr>';$nbModules++;}
if (!moduleUsed($DB,86)){echo '<tr><td><input type="checkbox" name="rssreader" CHECKED /></td><td>'.lg("rssreader").'</td></tr>';$nbModules++;}
if (!moduleUsed($DB,152)){echo '<tr><td><input type="checkbox" name="clock" CHECKED /></td><td>'.lg("clock").'</td></tr>';$nbModules++;}
if (!moduleUsed($DB,111)){echo '<tr><td><input type="checkbox" name="calculator" CHECKED /></td><td>'.lg("calc").'</td></tr>';$nbModules++;}
if (!moduleUsed($DB,340)){echo '<tr><td><input type="checkbox" name="analogclock" CHECKED /></td><td>'.lg("analogc").'</td></tr>';$nbModules++;}
if (!moduleUsed($DB,112)){echo '<tr><td><input type="checkbox" name="calendar" CHECKED /></td><td>'.lg("calendar").'</td></tr>';$nbModules++;}
if (!moduleUsed($DB,350)){echo '<tr><td><input type="checkbox" name="email" CHECKED /></td><td>'.lg("email").'</td></tr>';$nbModules++;}
if (!moduleUsed($DB,401)){echo '<tr><td><input type="checkbox" name="addressbook" CHECKED /></td><td>'.lg("addressbook").'</td></tr>';$nbModules++;}
if (!moduleUsed($DB,402)){echo '<tr><td><input type="checkbox" name="html" CHECKED /></td><td>'.lg("htmlWidget").'</td></tr>';$nbModules++;}
?>
</table>
<?php
	if ($nbModules==0){echo lg("noNewModuleToInstall");}
?>
<br /><br />
<h3><?php echo lg("youWillBeAbleToCreateModules");?></h3>
<br /><br />
<center><input type="submit" value="<?php echo lg("continue");?> >>>" />
</form>

<?php
require("footer.inc.php");
?>