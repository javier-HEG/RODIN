<?php
$not_access=0;
$pagename="modules/moddef.php";
//includes

require_once('includes.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="fr">
<body bgcolor="#FFDFDE" style="margin:10px">
<div style="overflow:auto;height:100%;width:100%;">
<?php
if (isset($_GET["st"])){
	if ($_GET["st"]=="W"){
			echo lg("moduleTemporalyUnavailable");
	} else {

		echo lg("moduleNotAvailableAnymore");
		$DB->sql = "SELECT d2.id, d2.name ";
		$DB->sql.= "FROM dir_item AS d2, dir_cat_item c1, dir_cat_item c2 ";
		$DB->sql.= "WHERE c1.item_id=" . $DB->escape($_GET["id"]) . " AND c1.category_id=c2.category_id ";
		$DB->sql.= "AND c2.item_id=d2.id ";
		$DB->sql.= "AND d2.status='O' ORDER BY sorting DESC ";
		$DB->sql.= "LIMIT 0,10 ";
		$DB->getResults($DB->sql);
		if ($DB->nbResults()!=0){
			echo lg("moddefEquivalentModules");
			while($row=$DB->fetch(0)){
					echo "<br /><a href='#' onclick=\"top.location.href='../portal/scr_exchangemod.php?prof=" .urlencode($_GET["prof"]) . "&amp;id1=" .urlencode($_GET["id"]) . "&amp;id2=" .urlencode($row["id"]) . "';return false;\">" . $row["name"] . "</a>";
			}
		}
		$DB->freeResults();
	}
}
?>
</div>
</body>
</html>