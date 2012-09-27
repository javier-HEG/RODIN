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
#  widget import XML
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=0;
$pagename="tutorial/frm_widget_download.php";
//includes
require_once('includes.php');
//require_once('header.inc.php');
include_once('../includes/file.inc.php');
include_once('../../app/exposh/l10n/'.__LANG.'/tutorial.lang.php');

if (isset($_GET['id'])) { 
    $id=$_GET['id'];
}
if (isset($_POST['id'])) { 
    $id=$_POST['id'];
}

if (isset($_POST['MAX_FILE_SIZE'])) {
    // Initialize the module in the DB
    $id=$_POST['id'];

    $file_loc="../modules/quarantine/module".$id.".xml";
    $err="";
    $Fichier="";

	if (empty($_FILES["fichier"]["name"])) { $err.=lg("badFileName")."<br />"; }
	$nomFichier=$_FILES["fichier"]["name"] ;
	if ($_FILES["fichier"]["size"]>200000) { $err.=lg("fileOversize")."<br />"; }
	//if ($_FILES["fichier"]["error"]>0) fileError($_FILES["fichier"]["error"]);
	$ext=substr($nomFichier,-4);
	if ($ext!=".xml") { $err.=lg("fileNotXmlExt")."<br />"; }

	if (is_uploaded_file($_FILES['fichier']['tmp_name'])){
		copy($_FILES['fichier']['tmp_name'], $file_loc);
	} else {    $err.=lg("fileNotLoaded")."<br />";   }
    
    if ($err!="") {  
        ?><script type="text/javascript">window.parent.window.parent.$p.app.alert.show("<?php echo $err;?>",3);</script><?php
        displayDownloadForm($id);
    }
    else {
        
            if ($fp = fopen($file_loc,"r")) {
            $i=0;
            	while(!feof($fp)) {
            		$Ligne = fgets($fp,255);
                    if ( preg_match('!//!',$Ligne) ) {
                        //remove ' in comment
                        $Ligne=preg_replace("/'/"," ",$Ligne);
                        //protect // if not coments disable code
                        $Ligne=preg_replace("!//!","\/\/",$Ligne);
                    } else {
                       $Ligne=preg_replace("/'/xmsi","\\'",$Ligne);
                    }
                   
                    $Ligne=preg_replace('/(?:\r\n|\r|\n)/xms','\\n',$Ligne);
                    //protect \" if not \ is removed when received by browser
                    $Ligne=preg_replace('!\\\"!xms','\\\\\"',$Ligne);
                    $Fichier .= $Ligne;
                    $i++;
                }
            	fclose($fp);
            }
            $file_loc=$file_loc."?"; 
            $Fichier=str_replace('</script>','</script&gt;',$Fichier);

            /*
           $Fichier = preg_replace('/
                                    \\\"
                                    /xmsi','\\\\\"',$Fichier );
                                    */
/*
                                    $Fichier = preg_replace("/
                        \\\'
                        /xmsi","\\\\'",$Fichier );  
*/
/*
            $Fichier = preg_replace("!
                        //
                        !xmsi","\/\/",$Fichier );   
                        */       
                             
       ?> 
            <script type="text/javascript">      

                window.parent.$p.tutorial.widgetParameters["url"]=window.parent.tutorial["xml_getwidget"];
                var code='<?php echo $Fichier;?>';
                var reg=new RegExp("(&gt;)", "g");
                window.parent.$p.tutorial.widgetParameters["code"]=code.replace(reg,">");
                window.parent.$p.tutorial.displayConfiguration();       
                
          </script> 
          <?php
    }
}
else 
{ 
    displayDownloadForm($id); 
}

function displayDownloadForm($id) 
{
    echo "<br /><form name='upload' method='post' action='frm_widget_download.php' enctype='multipart/form-data'>";
    echo lg('uploadYourMod')." :<br /><br />";
    echo "<input type='hidden' name='MAX_FILE_SIZE' value='200000' />";
    echo "<input type='hidden' name='id' value='".$id."' />";
    echo "<input type='file' name='fichier' size='30' />";
    echo "<br /><input type='submit' value='".lg("nextStep").">>>'  />";
    echo "</form>";
}

?>