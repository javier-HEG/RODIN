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

//config file generation
$generationPortaneo=false;
$generationPosh=true;
$config="";
$file = 'configinstall_posh.txt';
$filecollab = 'confinstall_collabsuite.inc.php';
$newfile = 'confinstall.inc.php';

if (file_exists("../includes/config.inc.php")) {
    require_once("../includes/config.inc.php");
    if (__enterprise) {
        $generationPortaneo = true;
        $generationPosh=false;
    }
}
elseif ( file_exists($newfile) ) {
    $lines = file($newfile); 
    if(!$lines)  { exit(); } 
    for ($i=0;$i<count($lines);$i++)
    {
        $config.=$lines[$i];
    }
    preg_match('/
                      "I_APPLICATION_ID",([^"]+)\);
                /xmsi',$config,$previous_install_type);
     
    if (isset($previous_install_type[1]) && $previous_install_type[1]==2) {
        $generationPosh=false;
        $generationPortaneo=true;
    }
    elseif (isset($previous_install_type[1]) && $previous_install_type[1]==1) {
        $generationPosh=true;
        $generationPortaneo=false;    
    }
}
if ($generationPosh) {
   if ( !copy($file,$newfile) )  {
        echo "You have no rights to copy this file <strong>$file</strong> , change rights, or better change owner, on your server. Files must be owned by server owner.";
   }
   
}
if ($generationPortaneo) {
    if ( !copy($filecollab,$newfile) ) {
        echo "You have no rights to copy this file <strong>$filecollab</strong>, change rights, or better changer owner, on your server. Files must be owned by server owner. ";
    }
}

require("header.inc.php");
    
?>
       <div class="bottomhr"><h1><?php echo lg("installation".I_APPLICATION_ID)." ";?></h1></div>
       <br />
       <h2><?php echo lg("welcome".I_APPLICATION_ID);?></h2>
       <br />
       <form action="step1.php" name="step1" method="get">
<?php
//	if (__INSTALLATIONSTEP<2){
		echo "<br /><br /><h3>".lg("intro".I_APPLICATION_ID)."</h3>";
		echo "<br /><input type='radio' name='installtype' value='1' checked='checked' onclick='\$p.install.changePluginStatus(0);' /> <b>".lg("upgrade".I_APPLICATION_ID)."</b>";
		echo "<br /><br /><input type='radio' name='installtype' value='2' onclick='\$p.install.changePluginStatus(1);' /> <b>".lg("newInstall".I_APPLICATION_ID)."</b>";
		echo "<br /><br /><br /><center><input type='submit' value='".lg("continue")." >>'></center>";
//	} else {
//		echo "<br /><br /><h3>".lg("installationAlreadyStarted".I_APPLICATION_ID)."</h3>";
//		echo "<br /><br /><br /><center><input type='submit' value=\"".lg("restartInstallation")."\"> <input type='button' value=\"".lg("continuePrevious")."\" onclick=\"window.location='step".__INSTALLATIONSTEP.".php'\"></center>";
//	}
?>
       <br />
       <div id="colsuite"></div>
       </form>
       
<?php
require("footer.inc.php");
?>

   <script>
    $p.install.displayFormSwitch();
    $p.install.changePluginStatus(0);
   </script>