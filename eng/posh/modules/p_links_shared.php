<?php
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
header("content-type: application/xml");
$folder="";
$not_access=0;
$pagename="modules/p_links_shared.php";
//includes
require_once('includes.php');

echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>';

$linkid=isset($_POST["linkid"])?$_POST["linkid"]:0;
$linkid = is_numeric($linkid)?$linkid:0;
$userid=isset($_SESSION['user_id'])?$_SESSION['user_id']:0;
?>
<Module>
<UserPref name="linkid" display_name="." datatype="hidden" default_value="0" />
<Content><![CDATA[
<head>
<script>
var id__MODULE_ID__=<?php echo $linkid;?>;
var inc__MODULE_ID__=0;
var name__MODULE_ID__="";
add__MODULE_ID__=function()
{
	var oName=_gel("n__MODULE_ID__");
	var oLink=_gel("l__MODULE_ID__");
	var link=oLink.value;
	if (link.indexOf("http://")==-1) link="http://"+link;
	name__MODULE_ID__=oName.value;
	executescr("wid_links_shared.php","modid="+id__MODULE_ID__+"&act=add&name="+_esc(oName.value)+"&link="+_esc(link),false,true,shows__MODULE_ID__);
}
shows__MODULE_ID__=function(v_id)
{
	var l_id=v_id.split("_");
	if (l_id[0]!=id__MODULE_ID__)
	{
		id__MODULE_ID__=l_id[0];
		tab[$p.app.tabs.sel].module[MODULE_ID_to_id("__MODULE_ID__")].changeVar("linkid",id__MODULE_ID__);
	}
	refr__MODULE_ID__();
}
sup__MODULE_ID__=function(id,newL)
{
	response=confirm(lg("msgAreYouSureSupElement"));
	if (response==1){executescr("wid_links_shared.php","modid="+id__MODULE_ID__+"&act=sup&linkid="+id,false,false,refr__MODULE_ID__);}
}
opt__MODULE_ID__=function(obj)
{
	if (<?php echo $userid;?>==0){alert(lg("msgNeedToBeConnected"));}
	else {obj.parentNode.innerHTML="<form action='' onsubmit='add__MODULE_ID__();return false;'><input id='n__MODULE_ID__' type='text' maxlength='30' value='"+lg("lblName")+"' size='10' onfocus=\"this.value=''\" />&nbsp;&nbsp;URL <input id='l__MODULE_ID__' type='text' maxlength='199' value='http://' size='15' /> <input class='btn' type='submit' value=' + ' /> <a href='#' onclick='noopt__MODULE_ID__();return false;'><img src='../images/ico_close.gif' /></a></form>";}
}
noopt__MODULE_ID__=function()
{
	var obj=_gel("addb__MODULE_ID__");obj.innerHTML="<a href='#' onclick='opt__MODULE_ID__(this);return false;'><img src='../images/ico_add.gif' /> "+lg("lblAddFav")+"</a>";
}
refr__MODULE_ID__=function()
{
	tab[$p.app.tabs.sel].module[MODULE_ID_to_id("__MODULE_ID__")].refresh();
}
noopt__MODULE_ID__();
</script>
</head>
<body>
<table cellpadding="4" cellspacing="0" width="100%">
<tr><td id="lkd__MODULE_ID__" style='padding:6px'><table cellpadding='0' cellspacing='0' width='100%'>
<?php
if ($linkid!=0)
{
	$DB->sql = "SELECT link_id,name,url FROM users_favorites, users_favorites_id ";
    $DB->sql.= "WHERE users_favorites.id=".$DB->escape($linkid)." ";
    $DB->sql.= "AND users_favorites_id.user_id=".$DB->escape($_SESSION['user_id'])." ";
    $DB->sql.= "ORDER BY name ";
	$DB->getResults($DB->sql);
	$inc=0;
	while ($row = $DB->fetch(0))
	{
		$inc++;
		echo '<tr><td><a id="lk__MODULE_ID___'.$inc.'" href="'.$row["url"].'" target="_blank" style="color:#000000">'.$row["name"].'</a></td><td align="right"><a href="#" onclick="sup__MODULE_ID__('.$row["link_id"].',false);return false;">'.($userid==0?'':'<img src="../images/ico_close.gif" align="absmiddle" />').'</a></td></tr>';
	}
	$DB->freeResults();
	$DB->close();
}
?>
</table></td></tr></table>
<div id="addb__MODULE_ID__" style='padding:2px 0 0 4px;height:19px;'></div>
</body>
]]></Content>
</Module>