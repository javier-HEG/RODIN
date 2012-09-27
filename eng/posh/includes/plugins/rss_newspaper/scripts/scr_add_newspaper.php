<?php

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="I";
$pagename="includes/plugins/rss_newspaper/scripts/scr_add_newspaper.php";
//includes
require_once('includes.php');

$xmlfile=new xmlFile();

$xmlfile->header();

$id=isset($_POST["id"])?$_POST["id"]:exit();

if ($id==0)
{
	//add newspaper
	$chk=$DB->execute("INSERT INTO rssnewspaper (title,description,author_id,status,header_img) VALUES (%s,%s,%u,'O',%s)",$DB->quote($_POST["t"]),$DB->quote($_POST["d"]),$DB->escape($_SESSION['user_id']),$DB->quote($_POST["h"]));
	$id=$DB->getId();
}
else
{
	//security : check the user is the author
	$DB->getResults("SELECT id FROM rssnewspaper WHERE id=%u AND author_id=%u",$DB->escape($id),$DB->escape($_SESSION['user_id']));
	if ($DB->nbResults()==0) exit("user is not the author");

	//update newspaper
	$chk=$DB->execute("UPDATE rssnewspaper SET title=%s,description=%s,header_img=%s WHERE id=%u",$DB->quote($_POST["t"]),$DB->quote($_POST["d"]),$DB->quote($_POST["h"]),$DB->escape($id));

	//remove old feeds
	$DB->execute("DELETE FROM rssnewspaper_feeds WHERE newspaper_id=%u",$DB->escape($id));
	//remove old tags
	$DB->execute("DELETE FROM rssnewspaper_keywords WHERE newspaper_id=%u",$DB->escape($id));
}

// add feeds
$inc=0;
While (isset($_POST["f".$inc]))
{
	$DB->execute("INSERT INTO rssnewspaper_feeds (newspaper_id,feed_id,latest_read_id) VALUES (%u,%u,0)",$id,$DB->escape($_POST["f".$inc]));
	$inc++;
}

//add tags
if ($_POST["kw"]!="")
{
	$keyword=explode(",",$_POST["kw"]);
	$keywordSimplified=explode(",",$_POST["kwformated"]);
	for ($i=0;$i<count($keyword);$i++)
	{
		$selkw=$keywordSimplified[$i];
		$DB->getResults("SELECT id FROM search_keyword WHERE label_simplified=%s ",$DB->quote($selkw));
		if ($DB->nbResults()==0)
		{
			$DB->execute("INSERT INTO search_keyword (label,label_simplified) VALUES (%s,%s) ",$DB->quote($keyword[$i]),$DB->quote($selkw));
			$kwid=$DB->getId();
		}
		else
		{
			$row = $DB->fetch(0);
			$kwid=$row["id"];
		}
		$DB->freeResults();

		$DB->execute("INSERT INTO rssnewspaper_keywords (newspaper_id,kw_id) VALUES (%u,%u) ",$id,$kwid);
	}
}

$xmlfile->status($chk);

$xmlfile->footer();

$DB->close();
?>