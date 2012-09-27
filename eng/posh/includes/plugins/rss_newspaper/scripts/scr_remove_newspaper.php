<?php

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="I";
$pagename="includes/plugins/rss_newspaper/scripts/scr_remove_newspaper.php";
//includes
require_once('includes.php');

$xmlfile=new xmlFile();

$xmlfile->header();

//remove newspaper
$chk=$DB->execute("UPDATE rssnewspaper SET status='D' WHERE id=%u AND author_id=%u",$DB->escape($_POST["id"]),$DB->escape($_SESSION['user_id']));

$xmlfile->status($chk);

$xmlfile->footer();

$DB->close();
?>