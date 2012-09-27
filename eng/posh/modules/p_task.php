<?php
// this file needs to be utf8 without BOM. Check accentuated chars : é à è ù
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
$shared=isset($_POST['shared'])?$_POST['shared']:0;
$shared = is_numeric($shared)?$shared:0;
$sharedmd5key=isset($_POST['sharedmd5key'])?$_POST['sharedmd5key']:'';
$widgetid = isset($_POST['widgetid'])?$_POST['widgetid']:0;
?>

<Module>
<UserPref name="taskid" display_name="." datatype="hidden" default_value="0" />
<Content><![CDATA[
<head>
<script>
var id__MODULE_ID__=<?php echo $taskid;?>;
var inc__MODULE_ID__=0;
var tasks__MODULE_ID__=[];
var shared__MODULE_ID__=<?php echo $shared;?>;
taskObj__MODULE_ID__=function(id,name,comment,status){
	this.id=id;
	this.name=name;
	this.comment=comment;
	this.status=status;
};
add__MODULE_ID__=function(v_form){
	var oName=$p.string.esc($p.string.doubleToSimpleCot(v_form.n__MODULE_ID__.value));
	var oCat=$p.string.esc($p.string.doubleToSimpleCot(v_form.r__MODULE_ID__.value));
	var oId=v_form.id.value;
	
	executescr("../modules/wid_tasks.php","modid="+id__MODULE_ID__+"&taskid="+oId+"&act=add&name="+oName+"&cat="+oCat,false,true,refr__MODULE_ID__);
}
sup__MODULE_ID__=function(id,newL){
	response=confirm(lg("msgAreYouSureSupElement"));
	if (response==1)
	{executescr("../modules/wid_tasks.php","modid="+id__MODULE_ID__+"&act=sup&taskid="+id,false,true,refr__MODULE_ID__);}
}
refr__MODULE_ID__=function(v_id){
	if (v_id!=indef){
		var l_id=v_id.split("_");
		if (l_id[0]!=id__MODULE_ID__){
			id__MODULE_ID__=l_id[0];
			tab[$p.app.tabs.sel].module[MODULE_ID_to_id("__MODULE_ID__")].changeVar("taskid",id__MODULE_ID__);
		}
	}
	tab[$p.app.tabs.sel].module[MODULE_ID_to_id("__MODULE_ID__")].refresh();
}
opt__MODULE_ID__=function(v_id){
	if (<?php echo $userid;?>==0){alert(lg("msgNeedToBeConnected"));}
	else {
		var obj=$p.get("addb__MODULE_ID__");
		obj.innerHTML="<form action='' onsubmit='add__MODULE_ID__(this);return false;'><table cellpadding='0'><tr><td>"+lg("lblTitle")+"</td><td><input name='n__MODULE_ID__' type='text' maxlength='50' size='20' value='"+(v_id==indef?"":tasks__MODULE_ID__[v_id].name)+"' /></td></tr><tr><td>"+lg("Comment")+"</td><td><input name='r__MODULE_ID__' type='text' maxlength='255' size='35' value='"+(v_id==indef?"":tasks__MODULE_ID__[v_id].comment)+"' /></td></tr><tr><td></td><td><input name='id' type='hidden' value='"+(v_id==indef?"":tasks__MODULE_ID__[v_id].id)+"' /><input class='btn' type='submit' value='"+(v_id==indef?lg("lblAdd"):lg("lblModify"))+"' /> <a href='#' onclick='noopt__MODULE_ID__();return false;'><img src='../images/ico_close.gif' /></a></td></tr></table></form>";
	}
}
noopt__MODULE_ID__=function(){
	if (shared__MODULE_ID__==1) return;
	var obj=$p.get("addb__MODULE_ID__");
	obj.innerHTML="<a href='#' onclick='opt__MODULE_ID__();return false;'><img src='../images/ico_add.gif' /> "+lg("lblAddTask")+"</a>";
}
done__MODULE_ID__=function(id,obj){
	executescr("../modules/wid_tasks.php","act=done&val="+(obj.checked?"Y":"N")+"&taskid="+id,false,true,refr__MODULE_ID__);
}
show__MODULE_ID__=function(){
	var l_s = '';

	for (var i=0;i<tasks__MODULE_ID__.length;i++)
    {
		l_s += '<div class="articleborder">';
        
   		if (shared__MODULE_ID__==0)
		{
			l_s += '<div style="float: right;line-height: 16px;">'
                +'<a href="#" title="'+lg("lblModify")+'" onclick="opt__MODULE_ID__('+i+');return false;">'
                +'<?php echo $userid==0?'':'<img src="../images/ico_edit.gif" align="absmiddle" />';?>'
                +'</a>'
                +'<a href="#" onclick="sup__MODULE_ID__('+tasks__MODULE_ID__[i].id+',false);return false;">'
                +'<?php echo $userid==0?'':'<img src="../images/ico_close.gif" align="absmiddle" />';?>'
                +'</a>'
                +'</div>';
		}

        l_s += (shared__MODULE_ID__ == 0 ? '<input class="nostyle" type="checkbox" onclick="done__MODULE_ID__('+tasks__MODULE_ID__[i].id+',this)"'+(tasks__MODULE_ID__[i].status=='Y'?'checked="checked" ':'')+'/>' : '')
			+ (tasks__MODULE_ID__[i].status=='Y'?'<strike>':'')
			+ '<a onmouseover=\'mouseBox("'+tasks__MODULE_ID__[i].comment+'",event)\' onmouseout=\'mouseBox("")\'">'
			+ tasks__MODULE_ID__[i].name
			+ '</a>'
			+ (tasks__MODULE_ID__[i].status=='Y'?'</strike>':'')
            + '</div>';
	}

	$p.print("c__MODULE_ID__",l_s);
}
init__MODULE_ID__=function(){
	noopt__MODULE_ID__();
<?php
if ($taskid!=0 && isset($_SESSION['user_id'])) {
    if( (empty($sharedmd5key))
        || (!isset($sharedmd5key)) 
        || ($sharedmd5key=='undefined') ) {
            $DB->getResults($wid_getTasks,  
                                $DB->escape($taskid),
                                $DB->escape($_SESSION['user_id'])
                            );
    } 
    else {
        $DB->getResults($wid_getTasksShared,
                            $DB->quote("%taskid=".$DB->escape($taskid)."%"),
                            $DB->quote($sharedmd5key),
                            $DB->escape($_SESSION['user_id']),
                            $DB->escape($widgetid),
                            $DB->escape($taskid)
                        );
    }	
    $inc=0;
    while ($row = $DB->fetch(0)){
        echo "tasks__MODULE_ID__[".$inc."]=new taskObj__MODULE_ID__(".$row["task_id"].",\"".$row["name"]."\",\"".str_replace("'"," ",$row["comments"])."\",\"".$row["done"]."\");";
        $inc++;
    }
	$DB->freeResults();
}
?>
	show__MODULE_ID__();
}
init__MODULE_ID__();
</script>
</head>
<body>
<table cellpadding="4" cellspacing="0" width="100%">
<tr><td id="c__MODULE_ID__" style='padding:6px'>
</td></tr>
</table>
<div id="addb__MODULE_ID__" style='padding:2px 0 4px 4px;'></div>
</body>
]]></Content>
</Module>