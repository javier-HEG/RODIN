<?php

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="I";
$pagename="includes/plugins/rss_newspaper/scripts/scr_remove_publication.php";
//includes
require_once('includes.php');

$xmlfile=new xmlFile();

$xmlfile->header();

// check the user is the publication author
$DB->getResults("SELECT a.id FROM rssnewspaper AS a,rssnewspaper_publication AS b WHERE a.id=b.newspaper_id AND b.id=%u AND a.author_id=%u",$DB->escape($_POST["id"]),$DB->escape($_SESSION['user_id']));
if ($DB->nbResults()==0) exit();
//remove newspaper
$chk=$DB->execute("UPDATE rssnewspaper_publication SET status='D' WHERE id=%u",$DB->escape($_POST["id"]));

$xmlfile->status($chk);

$xmlfile->footer();

$DB->close();
?>