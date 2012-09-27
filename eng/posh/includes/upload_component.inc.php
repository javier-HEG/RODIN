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
# Upload file component
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="";
$not_access=1;
$pagename="includes/upload_component.php";
$granted="I";
//includes
require_once('config.inc.php');
require_once('connection_'.__DBTYPE.'.inc.php');
require_once('session.inc.php');
require_once('plugin.api.php');
require_once('../db_layer/'.__DBTYPE.'/portal.php');
if (file_exists('plugins.inc.php'))
include_once('plugins.inc.php');

global $DB;
$DB = new connection(__SERVER,__LOGIN,__PASS,__DB);

require_once('file.inc.php');
require_once('image.inc.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
$error = "";

if (isset($_FILES['file']))
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
</head>
<body>

<?php
	if (isset($_FILES["file"]["name"]) AND !empty($_FILES["file"]["name"]))
	{
		$fileName = $_FILES["file"]["name"] ;
		$fileSize = $_FILES["file"]["size"];
		if ($fileSize > __uploadMaxFileSize) $error = lg('fileTooBig').'('.__uploadMaxFileSize.')';
		if ($_FILES["file"]["error"] > 0) $error = $_FILES["file"]["error"];
		
		//Check file extension
		$ext = strtolower(substr($fileName,-4));
		if (strpos(__uploadAllowedExtensions,$ext) === false)
		{
			$error = lg('extensionNotAllowed');
		}
		else
		{
			if (is_uploaded_file($_FILES['file']['tmp_name']))
			{
				$uploadFile = $_FILES['file']['tmp_name'];
			}
			else
			{
				$error = lg('uploadNotSuccessful');
			}
		}
	}
	else
	{
		$error = lg('uploadNotSuccessful');
	}

	if ($error == "")
	{
        $subFolder = str_replace('\\','',str_replace('/','',str_replace('.','',$_POST['subfolder'])));

        if( !is_dir("../upload/".$subFolder."/") )
    	{
    		mkdir("../upload/".$subFolder."/","0600");
    	}
		//generate a key to build file name with
		$str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$pkey = "";
		srand((double)microtime()*1000000);
		for($i = 0;$i < 32;$i++) $pkey .= $str[rand()%62]; 
	
		$newFileName = 'file_'.$pkey.$ext;
		$fileLocation = '../upload/'.$subFolder.'/'.$newFileName;

        if ($_POST['type'] == 'image')
        {
    		$img = new img($uploadFile);
    		if ($img->height != $_POST['h'] || $img->width == $_POST['w'])
    		{
    			if ($img->height > $img->width)
    			{
    				$decx=0;
    				$decy=($img->height - $img->width)/2;
    				$img->height = $img->width;
    			}
    			else
    			{
    				$decx = ($img->width - $img->height)/2;
    				$decy = 0;
    				$img->width = $img->height;
    			}
    			if( $ext == ".jpg" ) {
    				$img->createJpg();
    			} elseif ( $ext == ".gif" ) {
    				$img->createGif();
    			} elseif ( $ext == ".png" ) {
    				$img->createPng();
    			}
    			$img->resize($fileLocation,$_POST['w'],$_POST['h'],$decx,$decy);
    		}
            else
            {
                copy($uploadFile,$fileLocation);
            }
    	}
        else
        {
            copy($uploadFile,$fileLocation);
        }

		echo '
			<script type="text/javascript">
				if (top.window.location.href != window.location.href && (top.window.location.href+"/").indexOf("'.__LOCALFOLDER.'") != -1)
				{
					parent.'.$_POST['fct'].'("add","'.$fileName.'","'.$newFileName.'",'.$fileSize.');
				}
			</script>
		';
	}
	else
	{
		echo '<img src="../images/ico_alert.gif" align="absmiddle" border="0" /> '.$error;
	}
?>

</body>
</html>

<?php
}
if (!isset($_POST['closeafter']) || $_POST['closeafter'] == 'no')
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
</head>
<body style="margin: 0px;">
<form name="f" action="" method="post" enctype="multipart/form-data" style="margin-top: 0px;">
	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo __uploadMaxFileSize;?>" />
	<input type="file" name="file" />
    <input type="hidden" name="closeafter" value="<?php echo (isset($_REQUEST['closeafter']) ? $_REQUEST['closeafter'] : 'no');?>" />
    <input type="hidden" name="type" value="<?php echo (isset($_REQUEST['type']) ? $_REQUEST['type'] : 'document');?>" />
    <input type="hidden" name="w" value="<?php echo (isset($_REQUEST['w']) ? $_REQUEST['w'] : '100');?>" />
    <input type="hidden" name="h" value="<?php echo (isset($_REQUEST['w']) ? $_REQUEST['w'] : '100');?>" />
	<input type="hidden" name="subfolder" value="<?php echo $_GET['subfolder'];?>" />
	<input type="hidden" name="fct" value="<?php echo $_GET['fct'];?>" />
	<input type="submit" value="<?php echo lg("lblUpload");?>" />
	<a href="#" onclick="parent.<?php echo $_GET['fct'];?>('cancel');return false;"><?php echo lg('cancel');?></a>
</form>

<script type="text/javascript">
//prevent from inclusion in other web site
	if (top.window.location.href==window.location.href || (top.window.location.href+"/").indexOf('<?php echo __LOCALFOLDER;?>') == -1)
	{
		window.location='../index.php';
	}
</script>

</body>
</html>
<?php
}
?>
