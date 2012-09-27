<?php
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
header("content-type: application/xml");
$folder="";
$not_access=0;
$pagename="modules/p_notes_shared.php";
//includes
require_once('includes.php');

echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>';

$noteid=isset($_POST["noteid"])?$_POST["noteid"]:0;
$noteid = is_numeric($noteid)?$noteid:0;
$userid=isset($_SESSION['user_id'])?$_SESSION['user_id']:0;
?>
<Module>
<UserPref name="color" display_name="Couleur du fond" datatype="enum" default_value="EFE752">
  <EnumValue value="EFE752" display_value="Jaune"/>
  <EnumValue value="E7EFFF" display_value="Bleu"/>
  <EnumValue value="FFDFDE" display_value="Rouge"/>
</UserPref>
<UserPref name="noteid" display_name="." datatype="hidden" default_value="0" />
<Content><![CDATA[
<head>
<script>
var odata__MODULE_ID__;
var id__MODULE_ID__=<?php echo $noteid;?>;
var note__MODULE_ID__=<?php
	if ($noteid!=0)
	{
		$DB->getResults("SELECT notes FROM users_notes WHERE id=%u AND user_id=%u",$DB->escape($noteid),$DB->escape($_SESSION["user_id"]));
		$row = $DB->fetch(0);
		echo '"'.$row["notes"].'"';
		$DB->freeResults();
		$DB->close();
	}
	else
	{
		echo "__txtnote";
	}
?>;
write__MODULE_ID__=function()
{
	var mainobj=_gel("bn__MODULE_ID__");
	var obj=_gel("bnc__MODULE_ID__");
	var data=obj.innerHTML;
	//firefox bug correction
	data=data.replace(/\r\n/g," ");
	odata__MODULE_ID__=data;
	data=$p.string.htmlToText(data);
	var oWidth=obj.offsetWidth;
	var oHeight=(obj.offsetHeight>100)?obj.offsetHeight:100;
	mainobj.innerHTML="<textarea id='bntxt__MODULE_ID__' style='width:"+oWidth+"px;height:"+oHeight+"px;border:1px dashed #ff0000;font-size:10px;FONT-FAMILY:Verdana,Arial;' onblur='save__MODULE_ID__();return false;' onmouseover='this.focus()'>"+data+"</textarea>";
}
save__MODULE_ID__=function()
{
	var mainobj=_gel("bn__MODULE_ID__");
	var obj=_gel("bntxt__MODULE_ID__");
	var data=$p.string.textToHtml(obj.value);
	mainobj.innerHTML="<table cellpadding='3' cellspacing='0' border='0' width='100%'><tr><td height='20' onclick='write__MODULE_ID__();return false;' id='bnc__MODULE_ID__'>"+data+"</td></tr></table>";
	if (data!=odata__MODULE_ID__) executescr("wid_notes_shared.php","noteid="+id__MODULE_ID__+"&notes="+_esc(data),false,true,getId__MODULE_ID__);
}
getId__MODULE_ID__=function(v_id)
{
	if (v_id!=indef)
	{
		id__MODULE_ID__=v_id;
		tab[$p.app.tabs.sel].module[MODULE_ID_to_id("__MODULE_ID__")].changeVar("noteid",v_id);
	}
}
init__MODULE_ID__=function()
{
	var obj=_gel("bn__MODULE_ID__");
	obj.innerHTML="<table cellpadding='0' cellspacing='0' border='0' width='100%'><tr><?php if ($userid==0){echo "<td height='20' onclick='alert__MODULE_ID__();'>";} else {echo "<td height='20' onclick='javascript:write__MODULE_ID__();' id='bnc__MODULE_ID__'>";};?>"+note__MODULE_ID__+"</td></tr></table>";
}
alert__MODULE_ID__=function()
{
	alert(lg('msgNeedToBeConnected')+' !');
}
init__MODULE_ID__();
</script>
</head>
<body>
<table cellpadding="3" cellspacing="0" width="100%" border="0">
<tr><td id="bn__MODULE_ID__" bgcolor="<?php echo (isset($_POST["color"])?"#".$_POST["color"]:"#EFE752");?>">
</td></tr></table>
</body>
]]></Content>
</Module>