<?php

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="I";
$pagename="includes/plugins/rss_newspaper/scripts/upload_header.php";
//includes
require_once('includes.php');

if (isset($_FILES["headerfile"]["name"]) AND !empty($_FILES["headerfile"]["name"]))
{
	$file=$_FILES["headerfile"]["name"];
	$ext=strtolower(substr($file,-4));

	if ($_FILES["headerfile"]["size"]<200000 && $_FILES["headerfile"]["error"]<=0 && ($ext==".jpg" || $ext==".png") && is_uploaded_file($_FILES['headerfile']['tmp_name']))
	{
		$str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$pkey="";
		srand((double)microtime()*1000000);
		for($i=0;$i<10;$i++) $pkey.= $str[rand()%62];

		$encfile=$pkey.$file;
		copy($_FILES['headerfile']['tmp_name'],"../upload/".$encfile);

		echo $file;
		echo "<script type='text/javascript'>parent.rssNewspaper.uploadHeader('".$encfile."');</script>";
		exit();
	}
}

?>
<form method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="200000" /><input type="file" name="headerfile" size="30" value="gif, jpg or png" />
<input type="submit" value="OK" /><br />(jpg / png image)
</form>
