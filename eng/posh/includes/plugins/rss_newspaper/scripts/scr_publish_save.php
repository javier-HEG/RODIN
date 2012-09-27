<?php

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="I";
$pagename="includes/plugins/rss_newspaper/scripts/scr_publish_save.php";
//includes
require_once('includes.php');

$xmlfile=new xmlFile();

$xmlfile->header();

//add new published newspaper
$chk=$DB->execute("INSERT INTO rssnewspaper_publication (newspaper_id,pubdate,access,status) SELECT id,CURRENT_DATE,1,'O' FROM rssnewspaper WHERE id=%u AND author_id=%u",$DB->escape($_POST["id"]),$DB->escape($_SESSION['user_id']));
$id=$DB->getId();

// add articles information
$page=0;
$inc=0;
while (isset($_POST["w".$inc]) && isset($_POST["ih".$inc]))
{
	if ($_POST["p".$inc]>$page)
	{
		$page=$_POST["p".$inc];
		$DB->execute("INSERT INTO rssnewspaper_publication_page (publication_id,page_nb,layout) VALUES(%u,%u,%u) ",$id,$page,$DB->escape($_POST["l".$page]));
	}

	$DB->execute("INSERT INTO rssnewspaper_publication_article (publication_id,title,pubdate,feed,body,link,page_nb,x,y,width,img,imgx,imgy,imgwidth,imgheight) VALUES (%u,%s,%s,%s,%s,%s,%u,%u,%u,%u,%s,%u,%u,%u,%u)",$id,$DB->quote($_POST["t".$inc]),$DB->quote($_POST["d".$inc]),$DB->quote($_POST["f".$inc]),$DB->quote($_POST["b".$inc]),$DB->quote($_POST["a".$inc]),$DB->escape($_POST["p".$inc]),$DB->escape($_POST["x".$inc]),$DB->escape($_POST["y".$inc]),$DB->escape($_POST["w".$inc]),$DB->quote($_POST["i".$inc]),$DB->escape($_POST["ix".$inc]),$DB->escape($_POST["iy".$inc]),$DB->escape($_POST["iw".$inc]),$DB->escape($_POST["ih".$inc]));
	
	$inc++;
}

$xmlfile->status($chk);
$xmlfile->returnData($id);

$xmlfile->footer();

$DB->close();
?>