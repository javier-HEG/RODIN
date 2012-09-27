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

//close previous admin session
close_session();

//set new version in the adm_config configuration table
$DB->execute($install_setInstalledVersion);
//setStep(2);

//if plugins file not existing -> create
if (!is_file("../includes/plugins.inc.php")){
	$outfile=new file("../includes/plugins.inc.php");
	$outfile->write("");
}
//generate config file
generateConfigFile(true,"","","","");

$DB->close();
?>

<div class="bottomhr"><h1><?php echo lg("installation".I_APPLICATION_ID);?></h1></div>
<br />
<h2><?php echo lg("stepEndTitle");?></h2><br />
<h3><?php echo lg("portaneoNowInstalled".I_APPLICATION_ID);?></h3><br /><br />
<font color='#ff0000'><?php echo lg("installSupression");?></font>
<br /><br />
<b><?php echo lg("adminConnection");?></b>
<br /><br /><br />
<center><input type="button" value="<?php echo lg("openPortal");?>" onclick="window.location='../index.php';" /></center>

<?php
require("footer.inc.php");
?>
