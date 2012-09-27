<?php
// this file needs to be utf8 without BOM. Check accentuated chars : é à è ù
header("content-type: application/xml");
$folder="";
$not_access=0;
$pagename="modules/p_links.php";
//includes
require_once('includes.php');

echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>';

$linkid=isset($_POST["linkid"])?$_POST["linkid"]:0;
$linkid = is_numeric($linkid)?$linkid:0;
$userid=isset($_SESSION['user_id'])?$_SESSION['user_id']:0;
$selkey=isset($_POST["selkey"])?htmlentities($_POST["selkey"]):"";
$shared=isset($_POST['shared'])?$_POST['shared']:0;
$shared = is_numeric($shared)?$shared:0;
$sharedmd5key=isset($_POST['sharedmd5key'])?$_POST['sharedmd5key']:'';
$widgetid = isset($_POST['widgetid'])?$_POST['widgetid']:0;

?>
<Module>
<UserPref name="linkid" display_name="." datatype="hidden" default_value="0" />
<Content><![CDATA[
<head>
<script>
var id__MODULE_ID__=<?php echo $linkid;?>;
var inc__MODULE_ID__=0;
var name__MODULE_ID__="";
var links__MODULE_ID__=[];
var keys__MODULE_ID__=[];
var selKey__MODULE_ID__="<?php echo $selkey;?>";
var shared__MODULE_ID__=<?php echo $shared;?>;
linkObj__MODULE_ID__=function(id,name,url,tags){
	this.id=id;
	this.name=name;
	this.url=url;
	this.tags=tags;
}
add__MODULE_ID__=function(v_form){
	var oName=v_form.n__MODULE_ID__.value;
	var oLink=v_form.l__MODULE_ID__.value;
	var oTags=v_form.t__MODULE_ID__.value;
	var oId=v_form.id.value;
	//remove unused chars
	oTags=oTags.replace(";",",");
	oTags=$p.string.trim(oTags);
	oTags=oTags.replace(" ,",",");
	oTags=oTags.replace(", ",",");
	if (oLink.indexOf("http://") == -1 && oLink.indexOf("https://") == -1 && oLink.indexOf("file://") == -1) oLink="http://"+oLink;
	name__MODULE_ID__=oName;
	executescr("../modules/wid_links.php","modid="+id__MODULE_ID__+"&linkid="+oId+"&act=add&user_id=<?php echo $userid;?>&name="+$p.string.esc(oName)+"&link="+$p.string.esc(oLink)+"&tags="+$p.string.esc(oTags),false,true,treat__MODULE_ID__);
}
treat__MODULE_ID__=function(v_id){
	if (v_id!=indef){
		var l_id=v_id.split("_");
		if (l_id[0]!=id__MODULE_ID__){
			id__MODULE_ID__=l_id[0];
			tab[$p.app.tabs.sel].module[MODULE_ID_to_id("__MODULE_ID__")].changeVar("linkid",id__MODULE_ID__);
		}
	}
	refr__MODULE_ID__();
}
sup__MODULE_ID__=function(id,newL){
	response=confirm(lg("msgAreYouSureSupElement"));
	if (response==1){executescr("../modules/wid_links.php","modid="+id__MODULE_ID__+"&act=sup&linkid="+id,false,true,treat__MODULE_ID__);}
}
opt__MODULE_ID__=function(v_id){
	if (<?php echo $userid;?>==0){alert(lg("msgNeedToBeConnected"));}
	else {
		var obj=$p.get("addb__MODULE_ID__");
		obj.innerHTML="<form action='' onsubmit='add__MODULE_ID__(this);return false;'><table><tr><td>"+lg("lblTitle")+"</td><td><input name='n__MODULE_ID__' type='text' maxlength='30' size='30' value='"+(v_id==indef?"":links__MODULE_ID__[v_id].name)+"' /></td></tr><tr><td>URL</td><td><input name='l__MODULE_ID__' type='text' maxlength='199' size='30' value='"+(v_id==indef?"http://":links__MODULE_ID__[v_id].url)+"' /></td></tr><tr><td>"+lg("tags")+"</td><td><input name='t__MODULE_ID__' type='text' maxlength='100' size='28' value='"+(v_id==indef?"":links__MODULE_ID__[v_id].tags)+"' /> "+tooltip('linkKeysHlp')+"</td></tr><tr><td></td><td><input name='id' type='hidden' value='"+(v_id==indef?"":links__MODULE_ID__[v_id].id)+"' /><input class='btn' type='submit' value='"+(v_id==indef?lg("lblAdd"):lg("lblModify"))+"' /> <a href='#' onclick='noopt__MODULE_ID__();return false;'><img src='../images/ico_close.gif' /></a></td></tr></table></form>";
	}
}
noopt__MODULE_ID__=function(){
	if (shared__MODULE_ID__==1) return false;
	var obj=$p.get("addb__MODULE_ID__");
	obj.innerHTML="<a href='#' onclick='opt__MODULE_ID__();return false;'><img src='../images/ico_add.gif' /> "+lg("lblAddFav")+"</a>";
}
refr__MODULE_ID__=function(){
	tab[$p.app.tabs.sel].module[MODULE_ID_to_id("__MODULE_ID__")].refresh();
}
init__MODULE_ID__=function(){
	noopt__MODULE_ID__();  
<?php
if ($linkid!=0 && isset($_SESSION['user_id'])) {
    if( (empty($sharedmd5key))
        || (!isset($sharedmd5key)) 
        || ($sharedmd5key=='undefined') ) {
        $DB->getResults($wid_getUserLinks,
                            $DB->escape($linkid),
                            $DB->escape($_SESSION['user_id'])
                       );
    } 
    else {
        $DB->getResults($wid_getUserLinksShared,
                            $DB->quote("%linkid=".$DB->escape($linkid)."%"),
                            $DB->quote($sharedmd5key),
                            $DB->escape($_SESSION['user_id']),
                            $DB->escape($widgetid),
                            $DB->escape($linkid)
                        );
    }	
    $inc=0;
	while ($row = $DB->fetch(0)){
		echo "links__MODULE_ID__[".$inc."]=new linkObj__MODULE_ID__(".$row["link_id"].",\"".$row["name"]."\",\"".$row["url"]."\",\"".$row["tags"]."\");";
		$inc++;
	}
	$DB->freeResults();
}
?>
	getKeys__MODULE_ID__();
	show__MODULE_ID__();
}
getKeys__MODULE_ID__=function(){
	for (var i=0;i<links__MODULE_ID__.length;i++){
		var arr=links__MODULE_ID__[i].tags.split(",");
		for (var j=0;j<arr.length;j++){
			if (arr[j]!="" && !$p.array.find(keys__MODULE_ID__,arr[j])){
				keys__MODULE_ID__.push(arr[j]);
			}
		}
	}
	keys__MODULE_ID__.sort();
}
show__MODULE_ID__=function(){
	var l_s = "",
        l_slink = "";

	for (var i=0;i<links__MODULE_ID__.length;i++)
    {
		if (selKey__MODULE_ID__=="" || $p.array.find((links__MODULE_ID__[i].tags.split(",")),selKey__MODULE_ID__))
        {
            l_slink += '<div class="articleborder" style="padding-bottom: 3px;background: url(../images/ico_menu_star.gif) no-repeat top left;padding-left: 20px;">';
			if (shared__MODULE_ID__==0)
			{
				l_slink += '<div style="float: right">'
				+ '<a href="#" title="'+lg("lblModify")+'" onclick="opt__MODULE_ID__('+i+');return false;">'
				+ '<?php echo $userid==0?'':'<img src="../images/ico_edit.gif" align="absmiddle" />';?>'
				+ '</a> '
				+ '<a href="#" title="'+lg("lblSuppress")+'" onclick="sup__MODULE_ID__('+links__MODULE_ID__[i].id+',false);return false;">'
				+ '<?php echo $userid==0?'':'<img src="../images/ico_close.gif" align="absmiddle" />';?>'
				+ '</a>'
				+ '</div>'
			}
            l_slink += '<a id="lk__MODULE_ID___'+i+'" href="'+links__MODULE_ID__[i].url+'" target="_blank" style="color:#000000">'
                +links__MODULE_ID__[i].name
                +'</a>'
                +'</div>';
		}
	}
	if (keys__MODULE_ID__.length>0){
		l_s+="<div style='background-color:#efefef;padding:1px;'><form>"+lg("tags")+" : <select name='keywords' onchange='changeKey__MODULE_ID__(this.value);'><option value=''>* "+lg("all")+" *</option>";
		keys__MODULE_ID__.sort();
		for (var i=0;i<keys__MODULE_ID__.length;i++){
			l_s+='<option value="'+keys__MODULE_ID__[i]+'"'+(keys__MODULE_ID__[i]==selKey__MODULE_ID__?' selected="selected" ':'')+'>'+keys__MODULE_ID__[i]+'</option>';
		}
		l_s+="</select></form></div>";
	}

	l_s += l_slink;

	$p.print("lkd__MODULE_ID__",l_s);
}
changeKey__MODULE_ID__=function(v_key){
	selKey__MODULE_ID__=v_key;
	tab[$p.app.tabs.sel].module[MODULE_ID_to_id("__MODULE_ID__")].changeVar("selkey",v_key);
	show__MODULE_ID__();
}
init__MODULE_ID__();
</script>
</head>
<body>
<table cellpadding="4" cellspacing="0" width="100%">
<tr><td id="lkd__MODULE_ID__" style='padding:6px'></td></tr></table>
<div id="addb__MODULE_ID__" style='padding:2px 0 4px 4px;'></div>
</body>
]]></Content>
</Module>