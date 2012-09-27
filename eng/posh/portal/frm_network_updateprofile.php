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
# Update profile information
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$pagename="portal/frm_network_updateprofile.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../includes/image.inc.php');
require_once('../includes/file.inc.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
require_once('../../app/exposh/l10n/'.__LANG.'/enterprise.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');

$error="";
$nocache="";	

if (isset($_POST["act"]))
{
	$usedPicture="";
	$file_loc="";

	//format the user picture
	if (isset($_FILES["fichier"]["name"]) AND !empty($_FILES["fichier"]["name"]))
	{
		$nomFichier=$_FILES["fichier"]["name"] ;
		if ($_FILES["fichier"]["size"]>=1060000) $error= "error : file too big";
		if ($_FILES["fichier"]["error"]>0) $error= "error : ".$_FILES["fichier"]["error"];
		$ext=strtolower(substr($nomFichier,-4));
		if ($ext!=".jpg" && $ext!=".png" && $ext!=".gif") {
			$error= "error : picture must be jpg format";
		}
		if (is_uploaded_file($_FILES['fichier']['tmp_name']))
		{
			$usedPicture=$_FILES['fichier']['tmp_name'];
		}
		else
		{
			$error="error : file loading issue";
		}
	}
	
	if ($usedPicture!="" && $error=="")
	{
		if( !is_dir("../upload/profile/") )
		{
			mkdir("../upload/profile/","0600");
		}
		$file_loc="../upload/profile/".md5($_SESSION['longname'].$_SESSION['user_id'].time());
		$img=new img($usedPicture);
		if ($img->height==128 && $img->width==128)
		{
			copy($usedPicture,$file_loc);
			$decx=0;$decy=0;
		}
		else
		{
			if ($img->height>$img->width)
			{
				$decx=0;
				$decy=($img->height-$img->width)/2;
				$img->height=$img->width;
				$nocache=$decy;
			}
			else
			{
				$decx=($img->width-$img->height)/2;
				$decy=0;
				$img->width=$img->height;
				$nocache=$decx;
			}
			if( $ext == ".jpg" ) {
				$img->createJpg();
			} elseif ( $ext == ".gif" ) {
				$img->createGif();
			} elseif ( $ext == ".png" ) {
				$img->createPng();
			}
			$img->resize($file_loc,128,128,$decx,$decy);
		}
	}

	$desc = isset($_POST["desc"])?$_POST["desc"]:"";
	$keyWords = isset($_POST["keywords"])?$_POST["keywords"]:"";
	$kwformated = isset($_POST["kwformated"])?$_POST["kwformated"]:"";
	//update user profile
	if ($usedPicture=="")
	{
		$DB->execute($frmnetworkupdateprofile_updUser,$DB->noHTML($desc),$DB->noHTML($keyWords),$_SESSION['user_id']);
	}
	else
	{
		//generate a private key used in application
		$str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$pkey="";
		srand((double)microtime()*1000000);
		for($i=0;$i<10;$i++) $pkey.= $str[rand()%62]; 
		
		$DB->execute($frmnetworkupdateprofile_updUserWPict,$DB->noHTML($desc),$DB->noHTML($keyWords),$DB->noHTML($file_loc."?rand=".$pkey),$_SESSION['user_id']);
	}
	
	//add keywords
	$DB->execute($frmnetworkupdateprofile_removeKeywords,$DB->escape($_SESSION['user_id']));
	$keyword=explode(",",$keyWords);
	$keywordSimplified=explode(",",$kwformated);
	for ($i=0;$i<count($keyword);$i++)
	{
		$selkw=$keywordSimplified[$i];
		$DB->getResults($frmnetworkupdateprofile_getNetwork,$DB->noHTML($selkw));
		if ($DB->nbResults()==0)
		{
			$DB->execute($frmnetworkupdateprofile_addKeyword,$DB->noHTML($keyword[$i]),$DB->noHTML($selkw));
			$kwid=$DB->getId();
		}
		else
		{
			$row = $DB->fetch(0);
			$kwid=$row["id"];
		}
		$DB->freeResults();
	
		$DB->execute($frmnetworkupdateprofile_linkKeyword,$DB->escape($_SESSION['user_id']),$DB->escape($kwid));
	}
}

//get user information
$DB->getResults($frmnetworkupdateprofile_getUser,$DB->escape($_SESSION['user_id']));
$row=$DB->fetch(0);
$picture=($row["picture"]==""?"../images/nopicture.gif":$row["picture"]);
$desc=$row["description"];
$DB->freeResults();
////get user keywords
$DB->getResults($frmnetworkupdateprofile_getKeywords,$DB->escape($_SESSION['user_id']));
$keywords="";
while($row=$DB->fetch(0))
{
	$keywords.=",".$row["label"];
}
$keywords=substr($keywords,1);
$DB->freeResults();

$DB->close();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<link rel="stylesheet" href="../styles/main.css?v=<?php echo __POSHVERSION;?>" type="text/css" />
	<script type="text/javascript" src="../l10n/<?php echo __LANG;?>/lang.js?v=<?php echo __POSHVERSION;?>" ></script>
	<script type="text/javascript" src="../includes/config.js?v=<?php echo __POSHVERSION;?>" ></script>
	<script type="text/javascript" src="../tools/mootools.js?v=1.2.1"></script>
	<script type="text/javascript" src="../includes/ajax.js?v=<?php echo __POSHVERSION;?>" ></script>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <script type="text/javascript" src="../includes/php/ajax-urls.js"></script>
	<script type="text/javascript">
		function profileFormatTags()
		{
			document.forms["f"].kwformated.value=$p.string.formatForSearch($p.tags.formatList(document.forms["f"].keywords.value));
			document.forms["f"].keywords.value=$p.tags.formatList(document.forms["f"].keywords.value);
		}
	</script>
</head>
<body bgcolor="#ffffff">
	<div class="maindiv">
	<form name="f" action="" method="post" enctype="multipart/form-data" onsubmit="return profileFormatTags()"><input type="hidden" name="act" value="upd" />
	<table>
		<tr>
		<td width="90" align="center"><img src="<?php echo $picture;?>?nocache=<?php echo $nocache;?>" width="64" height="64" class="picture" /></td>
		<td>
		<?php echo ($error==""?lg("changePicture"):"<font color='#ff0000'><b>".lg("changePicture")."</b></font>") ?>  :<br /><br />
		<input type="hidden" name="MAX_FILE_SIZE" value="1060000" /><input type="file" name="fichier" style="width: 300px" />
		</td>
		</tr>
	</table>
	<br /><?php echo lg("keywords");?> <script type="text/javascript">document.write(tooltip("helpMyTags"));</script><br /><input type="hidden" name="kwformated" /><input type="text" id="mykeywordsinput" name="keywords" maxlength="250" value="<?php echo $keywords;?>" onkeyup="$p.tags.autocompletion.get('mykeywordsinput')" onblur='$p.tags.autocompletion.hide()' style="width: 470px" /><br />
	<br /><?php echo lg("description");?><br /><textarea name="desc" rows="7" style="width: 470px"><?php echo $desc;?></textarea><br />
	<br /><div id='otherCriteria' ></div><br />
	<center><input type="submit" class='submit' value="<?php echo lg("update");?>" /></center>
	</form>
	</div>
</body>
</html>