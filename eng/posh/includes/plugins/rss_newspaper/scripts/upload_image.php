<?php

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="I";
$pagename="includes/plugins/rss_newspaper/scripts/upload_image.php";
//includes
require_once('includes.php');

if (isset($_FILES["imagefile"]["name"]) AND !empty($_FILES["imagefile"]["name"]))
{
	$file=$_FILES["imagefile"]["name"];
	$ext=strtolower(substr($file,-4));

	if ($_FILES["imagefile"]["size"]<200000 && $_FILES["imagefile"]["error"]<=0 && ($ext==".jpg" || $ext==".png") && is_uploaded_file($_FILES['imagefile']['tmp_name']))
	{
		$str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$pkey="";
		srand((double)microtime()*1000000);
		for($i=0;$i<10;$i++) $pkey.= $str[rand()%62];

		$encfile=$pkey.(str_replace(' ','',$file));
		copy($_FILES['imagefile']['tmp_name'],"../upload/".$encfile);

		echo $file;
		echo "<script type='text/javascript'>parent.rssNewspaper.publish.insertImage(".$_POST["id"].",'".$encfile."');</script>";
		exit();
	}
}

?>
<form method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="200000" /><input type="file" name="imagefile" size="30" />
<input type="hidden" name="id" value="<?php echo $_GET["id"];?>" />
<input type="submit" value="OK" /><br />(jpg / png image)
</form>
