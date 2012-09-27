<?php
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
header("content-type: application/xml");
$folder="";
$not_access=0;
$pagename="modules/p_task.php";
//includes
require_once('includes.php');

echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>';

$taskid=isset($_POST["taskid"])?$_POST["taskid"]:0;
$taskid = is_numeric($taskid)?$taskid:0;
$userid=isset($_SESSION['user_id'])?$_SESSION['user_id']:0;
?>
<Module>
<UserPref name="taskid" display_name="." datatype="hidden" default_value="0" />
<Content><![CDATA[
<head>
<script>
var id__MODULE_ID__=<?php echo $taskid;?>;
var inc__MODULE_ID__=0;
add__MODULE_ID__=function()
{
	var oName=_gel("n__MODULE_ID__");
	var oCat=_gel("r__MODULE_ID__");
	
	executescr("wid_tasks_shared.php","modid="+id__MODULE_ID__+"&act=add&name="+_esc(oName.value)+"&cat="+_esc(oCat.value),false,true,refr__MODULE_ID__);
}
sup__MODULE_ID__=function(id,newL)
{
	response=confirm(lg("msgAreYouSureSupElement"));
	if (response==1)
	{executescr("wid_tasks_shared.php","modid="+id__MODULE_ID__+"&act=sup&taskid="+id,false,true,refr__MODULE_ID__);}
}
refr__MODULE_ID__=function(v_id)
{
	if (v_id!=indef)
	{
		var l_id=v_id.split("_");
		if (l_id[0]!=id__MODULE_ID__)
		{
			id__MODULE_ID__=l_id[0];
			tab[$p.app.tabs.sel].module[MODULE_ID_to_id("__MODULE_ID__")].changeVar("taskid",id__MODULE_ID__);
		}
	}
	tab[$p.app.tabs.sel].module[MODULE_ID_to_id("__MODULE_ID__")].refresh();
}
opt__MODULE_ID__=function(obj)
{
	if (<?php echo $userid;?>==0){alert(lg("msgNeedToBeConnected"));}
	else {obj.parentNode.innerHTML="<form action='' onsubmit='add__MODULE_ID__();return false;'><table cellpadding='0'><tr><td>"+lg("lblTitle")+"</td><td><input id='n__MODULE_ID__' type='text' maxlength='50' size='20' /></td></tr><tr><td>"+lg("Comment")+"</td><td><input id='r__MODULE_ID__' type='text' maxlength='255' size='35' /></td></tr><tr><td></td><td><input class='btn' type='submit' value='"+lg("lblAdd")+"' /> <a href='#' onclick='noopt__MODULE_ID__();return false;'><img src='../images/ico_close.gif' /></a></td></tr></table></form>";}
}
noopt__MODULE_ID__=function()
{
	var obj=_gel("addb__MODULE_ID__");obj.innerHTML="<a href='#' onclick='opt__MODULE_ID__(this);return false;'><img src='../images/ico_add.gif' /> "+lg("lblAddTask")+"</a>";
}
done__MODULE_ID__=function(id,obj)
{
	executescr("wid_tasks_shared.php","act=done&val="+(obj.checked?"Y":"N")+"&taskid="+id,false,true,refr__MODULE_ID__);
}
noopt__MODULE_ID__();
</script>
</head>
<body>
<table cellpadding="4" cellspacing="0" width="100%">
<tr><td id="c__MODULE_ID__" style='padding:6px'><table cellpadding='0' cellspacing='0' width='100%'>
<?php
if ($taskid!=0)
{
	$DB->getResults("SELECT task_id,comments,name,done FROM users_tasks ut, users_tasks_id uti WHERE ut.id=%u AND ut.id=uti.id AND uti.user_id=%u ORDER BY name ",$taskid,$DB->escape($_SESSION["user_id"]));
	$cat="";
	if ($DB->nbResults()>0)
	{
		while ($row = $DB->fetch(0))
		{
			//if ($row["category"]!=$cat) {echo "<tr><td><B>".$row["category"]."</B></td><td></td></tr>";$cat=$row["category"];}
			echo '<tr><td><input class="nostyle" type="checkbox" onclick="done__MODULE_ID__('.$row["task_id"].',this)"'.($row["done"]=='Y'?'checked="checked" /> <strike><a href="#" onclick="return false" title="'.$row["comments"].'">'.$row["name"].'</a></strike>':'/> <a href="#" onclick="return false" title="'.$row["comments"].'">'.$row["name"]).'</a></td><td align="right"><A href="#" onclick="sup__MODULE_ID__('.$row["task_id"].',false);return false;"><IMG src="../images/ico_close.gif" align="absmiddle" /></A></td></tr>';
		}
	}
	$DB->freeResults();
	$DB->close();
}
?>
</table>
</td></tr>
</table>
<div id="addb__MODULE_ID__" style='padding:2px 0 0 4px;height:19px;'></div>
</body>
]]></Content>
</Module>