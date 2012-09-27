<?php
// this file needs to be utf8 without BOM. Check accentuated chars : é à è ù
header("content-type: application/xml");
$folder = "";
$not_access = 0;
$pagename = "modules/p_notes.php";
//includes
require_once('includes.php');

echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>';

$noteid = isset($_POST["noteid"]) ? $_POST["noteid"] : 0;
$noteid = is_numeric($noteid)?$noteid:0;
$userid = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$shared = isset($_POST['shared']) ? $_POST['shared'] : 0;
$sharedmd5key=isset($_POST['sharedmd5key'])?$_POST['sharedmd5key']:'';
$shared = is_numeric($shared)?$shared:0;
$widgetid = isset($_POST['widgetid'])?$_POST['widgetid']:0;
?>
<Module>
<UserPref name="color" display_name="lblBgColor" datatype="enum" default_value="ffffff">
  <EnumValue value="ffffff" display_value="Blanc"/>
  <EnumValue value="F7F384" display_value="Jaune"/>
  <EnumValue value="C3D9FF" display_value="Bleu"/>
  <EnumValue value="FFDFDE" display_value="Rouge"/>
  <EnumValue value="B8E0BA" display_value="Vert"/>
  <EnumValue value="FADCB3" display_value="Orange"/>
  <EnumValue value="F3E7B3" display_value="Marron"/>
  <EnumValue value="E1C7E1" display_value="Violet"/>
</UserPref>
<UserPref name="noteid" display_name="." datatype="hidden" default_value="0" />
<Content><![CDATA[
<head>
<script>
var odata__MODULE_ID__,
    id__MODULE_ID__ = <?php echo $noteid;?>,
    shared__MODULE_ID__ = <?php echo $shared;?>,
    note__MODULE_ID__ = <?php
	if ( $noteid!=0 && isset($_SESSION["user_id"]) ){
        if( empty($sharedmd5key) 
            || (!isset($sharedmd5key)) 
            || $sharedmd5key=='undefined') {
                $DB->getResults($xml_getUserNotes,$DB->escape($noteid),$DB->escape($_SESSION["user_id"]));
		} else {
                $DB->getResults($xml_getUserNotesShared,
                                $DB->quote("%noteid=".$DB->escape($noteid)."%"),
                                $DB->quote($sharedmd5key),
                                $DB->escape($_SESSION['user_id']),
                                $DB->escape($widgetid),
                                $DB->escape($noteid)
                                );
        }
        $row = $DB->fetch(0);
		echo '"'.$row["notes"].'"';
		$DB->freeResults();
		$DB->close();
	} else {echo "__txtnote";}
?>;
write__MODULE_ID__ = function()
{
	if (shared__MODULE_ID__ == 1) return false; // if widget shared, writing is desabled

	var mainobj = $p.get("bn__MODULE_ID__"),
        obj = $p.get("bnc__MODULE_ID__"),
        data=obj.innerHTML;

	//firefox bug correction
	data = data.replace(/\r\n/g," ");
	odata__MODULE_ID__ = data;
	data = $p.string.htmlToText(data);
	var oWidth = obj.offsetWidth;
	var oHeight = (obj.offsetHeight>100)?obj.offsetHeight:100;

	mainobj.innerHTML = "<textarea id='bntxt__MODULE_ID__' style='width:"+oWidth+"px;height:"+oHeight+"px;border:1px dashed #ff0000;font-size:10px;FONT-FAMILY:Verdana,Arial;' onblur='save__MODULE_ID__();return false;' onmouseover='this.focus()'>"
        + data
        + "</textarea>";
}
save__MODULE_ID__=function()
{
	var mainobj = $p.get("bn__MODULE_ID__"),
        obj = $p.get("bntxt__MODULE_ID__"),
        data = $p.string.textToHtml($p.string.doubleToSimpleCot(obj.value));

	if (data != odata__MODULE_ID__)
    {
        executescr("../modules/wid_notes.php","noteid="+id__MODULE_ID__+"&notes="+$p.string.esc(data)+"&userid=<?php echo $userid;?>",false,true,getId__MODULE_ID__);
    }
	else
        init__MODULE_ID__();
}
getId__MODULE_ID__=function(v_id)
{
	if (v_id != indef) {
		id__MODULE_ID__ = v_id;
		tab[$p.app.tabs.sel].module[MODULE_ID_to_id("__MODULE_ID__")].changeVar("noteid",v_id);
	}

	//added 1.4 to refresh module options
	tab[$p.app.tabs.sel].module[MODULE_ID_to_id("__MODULE_ID__")].refresh();
}
init__MODULE_ID__=function()
{
	var obj = $p.get("bn__MODULE_ID__");
	obj.innerHTML = "<table cellpadding='0' cellspacing='0' border='0' width='100%'>"
        + "<tr>"
        + "<?php if ($userid == 0){echo "<td height='20' onclick='alert__MODULE_ID__();'>";} else {echo "<td height='20' onclick='javascript:write__MODULE_ID__();' id='bnc__MODULE_ID__'>";};?>"
        + note__MODULE_ID__
        + "</td>"
        + "</tr>"
        + "</table>";
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
 <tr>
  <td id="bn__MODULE_ID__" bgcolor="<?php echo (isset($_POST["color"])?"#".$_POST["color"]:"#F7F384");?>">
  </td>
 </tr>
</table>
</body>
]]></Content>
</Module>