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
# Expert modules - Step3 : set icon
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$isScript=false;
$isPortal=false;

$pagename="tutorial/frm_icon_upload.php";
$previous = 0;
require_once('includes.php');
require_once('../includes/image.inc.php');
require_once('../includes/log.inc.php');
require_once('../includes/file.inc.php');
require_once('../../app/exposh/l10n/'.__LANG.'/tutorial.lang.php');
require('header.inc.php');

$mid=isset($_GET['mid'])?$_GET['mid']:0;
$mid=isset($_POST["mid"])?$_POST["mid"]:$mid;
$icondef=isset($_POST["icondef"])?$_POST["icondef"]:0;
$validate=isset($_GET["val"])?$_GET["val"]:"";
$id=$mid;
$file_loc="../modules/quarantine/icon".$id;
$usedIcon="";
$newIcon = isset($_GET["icon"])?$_GET["icon"]:"";
$error = false;
//upload  the pictogram in the quarantine directory
if(isset($_POST["valider"]))
{
	if (!isset($_FILES["fichier"]["name"]) OR empty($_FILES["fichier"]["name"]))    {
		$usedIcon="../modules/pictures/_deficon".$_POST["icondef"].".gif";
	}
	else    {
		$nomFichier=$_FILES["fichier"]["name"];
		if ($_FILES["fichier"]["size"]>30000){
			$msgErr=lg("fileOversize");
			$error = true;
		}
		if ($_FILES["fichier"]["error"]>0){
			$msgErr=lg("fileError");
			$error = true;
		}
		$ext=strtolower(substr($nomFichier,-4));
		if ($ext!=".gif"){
			$msgErr=lg("badFileName");
			$error = true;
		}
		if (is_uploaded_file($_FILES['fichier']['tmp_name']) and !$error )   {
			$usedIcon=$_FILES['fichier']['tmp_name'];
            $msgErr=lg("modificationApplied");
		}
		elseif($msgErr=="") {
			$msgErr=lg("fileNotLoaded");
			$error = true;
		}
	}

	if ( !empty($usedIcon) and !$error and $_POST['icondef']>=0 )  {
		$file_loc="../modules/quarantine/icon".$id.".gif";
		$img=new img($usedIcon);
		if ($img->height==16 && $img->width==16 )    {
			copy($usedIcon,$file_loc);
		}
		else    {
			$img->createGif();
			$img->resize($file_loc,16,16,0,0);
		}
		$msgErr=lg("modificationApplied");
	}

	if($_POST['icondef']==-1 && file_exists( $newIcon ) and !$error ) {
		$msgErr=lg("modificationApplied");
		$file_loc = $newIcon;
	}
	if( !empty($validate) && $_POST['icondef']==0 and !$error ) {
		$file_loc="../modules/quarantine/box0_".$id.".gif";
		copy($newIcon,$file_loc);
		$msgErr=lg("modificationApplied");
	}
    if( empty($validate) && $_POST['icondef']==0 && empty($usedIcon)) {
        $msgErr=lg("errNoSelectedIcon");
		$error=true;
	}
}

///echo '		<img src= />';
echo '		<form name="f"  action="frm_icon_choice_upload.php?val='.$validate.'&icon='.$newIcon.'"  method="post" enctype="multipart/form-data">';
if( file_exists( $newIcon ) && empty( $validate ) ) {
	echo '		<br />'.lg('defaultIcon').' : ';
	echo '		<a id="icon80" style="border: 2px solid rgb(255, 0, 0);" onclick="return $p.tutorial.iconSet(80)" href="#">';
	echo '		<img src="'.$newIcon.'" /></a><br /><br />';
	$icondef = -1;
}
echo '		<input type="hidden" name="mid" value="'.$mid.'" />';
echo '		<input type="hidden" name="icondef" value="'.$icondef.'" />';
echo 		lg('uploadIcon').' : ';
echo '		<input type="hidden" name="MAX_FILE_SIZE" value="30000" /><input type="file" name="fichier" size="30" />';
echo '		<br /><br />'.lg('orChooseInSelection').' :<br />';
echo '		<div id="iconlist"></div>';
echo '		<br /><br />';
echo '		<input type="submit" value="'.lg('nextStep').'>>>" name="valider" />';
echo '		</form>';
?>
<script type="text/javascript">

	$p.print("iconlist",window.parent.$p.tutorial.iconLoad());
	<?php  
    if(isset($msgErr)){ ?>
		window.onload=function(){
			window.parent.window.parent.$p.app.alert.show("<?php echo $msgErr;  ?>");
		}
	<?php   if(!$error) { ?>
		window.parent.$p.tutorial.widgetParameters['icon'] = '<?php echo $file_loc; ?>';
        window.parent.$p.tutorial.widgetPreview();
        //document.location.href='#headlink';
	<?php
	 }
    }
	?>

</script>