<?php
// this file needs to be utf8 without BOM. Check accentuated chars : é à è ù
header("content-type: application/xml");
$folder="";
$not_access=0;
$pagename="modules/p_html.php";
//includes
require_once('includes.php');

echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>';

$htmlid=isset($_POST["htmlid"])?$_POST["htmlid"]:0;
$htmlid = is_numeric($htmlid)?$htmlid:0;
$userid=isset($_SESSION['user_id'])?$_SESSION['user_id']:0;
?>
<Module>
<UserPref name="htmlid" display_name="." datatype="hidden" default_value="0" />
<Content><![CDATA[
<head>
<script>
var id__MODULE_ID__=<?php echo $htmlid;?>;
var html__MODULE_ID__="<?php
	if ($htmlid!=0){
		$DB->sql = "SELECT content FROM widget_html ";
		$DB->sql.= "WHERE id=".$DB->escape($htmlid)." ";
        //$DB->sql.= "AND user_id=".$DB->escape($_SESSION["user_id"])." ";
		$DB->getResults($DB->sql);
		$row = $DB->fetch(0);
		echo $row["content"];
		$DB->freeResults();
		$DB->close();
	}
?>";
save__MODULE_ID__=function(v_form){
	var newHTML=v_form.html_text__MODULE_ID__.value;
	newHTML=newHTML.replace(/\n/g,"");
	newHTML=newHTML.replace(/\r/g,"");
	newHTML=$p.string.doubleToSimpleCot(newHTML);
	//newHTML=($p.navigator.IE)?newHTML.replace(/\n/g,""):newHTML.replace(/\n/g,"");
	executescr("../modules/wid_html.php","htmlid="+id__MODULE_ID__+"&html="+$p.string.esc(newHTML),false,true,getId__MODULE_ID__);
}
getId__MODULE_ID__=function(v_id){
	if (v_id!=indef){
		id__MODULE_ID__=v_id;
		tab[$p.app.tabs.sel].module[MODULE_ID_to_id("__MODULE_ID__")].changeVar("htmlid",v_id);
	}
	//added 1.4 to refresh module options
	tab[$p.app.tabs.sel].module[MODULE_ID_to_id("__MODULE_ID__")].refresh();
}
init__MODULE_ID__=function(){
	var html=html__MODULE_ID__;
	($p.get("html_disp__MODULE_ID__")).innerHTML=html;
<?php
	if (isset($_SESSION["type"]) && $_SESSION["type"]=='A')
	{
?>
	($p.get("html_edit__MODULE_ID__")).innerHTML="<center><form onsubmit=\"save__MODULE_ID__(this);return false;\"><textarea style='width:100%;height:150px;' name='html_text__MODULE_ID__'>"+html+"</textarea><input type='submit' value='"+lg("modify")+"'/></form></center>";
<?php
	}
?>
}
init__MODULE_ID__();
</script>
</head>
<body>
<div id="html_disp__MODULE_ID__"></div>
<div id="html_edit__MODULE_ID__"></div>
</body>
]]></Content>
</Module>