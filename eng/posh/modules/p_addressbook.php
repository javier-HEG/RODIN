<?php
// this file needs to be utf8 without BOM. Check accentuated chars : é à è ù
header("content-type: application/xml");
$folder="";
$not_access=0;
$pagename="modules/p_addressbook.php";
//includes
require_once('includes.php');

echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>';

$addId=isset($_POST["addid"])?$_POST["addid"]:0;
$addId = is_numeric($addId)?$addId:0;
$userid=isset($_SESSION['user_id'])?$_SESSION['user_id']:0;
$selTag=isset($_POST["seltag"])?htmlentities($_POST["seltag"]):"";
$shared=isset($_POST['shared'])?$_POST['shared']:0;
$sharedmd5key=isset($_POST['sharedmd5key'])?$_POST['sharedmd5key']:'';
$shared = is_numeric($shared)?$shared:0;
$widgetid = isset($_POST['widgetid'])?$_POST['widgetid']:0;
?>
<Module>
<UserPref name="addid" display_name="." datatype="hidden" default_value="0" />
<Content><![CDATA[
<head>
<script>
var id__MODULE_ID__=<?php echo $addId;?>;
var inc__MODULE_ID__=0;
var name__MODULE_ID__="";
var addr__MODULE_ID__=[];
var keys__MODULE_ID__=[];
var selKey__MODULE_ID__="<?php echo $selTag;?>";
var shared__MODULE_ID__=<?php echo $shared;?>;
addObj__MODULE_ID__=function(id,firstname,lastname,mail,company,func,phone1,phone2,other,tags){
	this.id=id;
	this.firstname=firstname;
	this.lastname=lastname;
	this.mail=mail;
	this.company=company;
	this.func=func;
	this.phone1=phone1;
	this.phone2=phone2;
	this.other=other;
	this.tags=tags;
};
add__MODULE_ID__=function(v_form){
	var input_firstname=v_form.fn__MODULE_ID__.value;
	var input_lastname=v_form.ln__MODULE_ID__.value;
	var input_mail=v_form.m__MODULE_ID__.value;
	var input_company=v_form.c__MODULE_ID__.value;
	var input_function=v_form.f__MODULE_ID__.value;
	var input_phone1=v_form.p1__MODULE_ID__.value;
	var input_phone2=v_form.p2__MODULE_ID__.value;
	var input_other=v_form.o__MODULE_ID__.value;
	
	var oId=v_form.id.value;
	
	var oTags=v_form.t__MODULE_ID__.value;
	//remove unused chars
	oTags=oTags.replace(";",",");
	oTags=$p.string.trim(oTags);
	oTags=oTags.replace(" ,",",");
	oTags=oTags.replace(", ",",");
	
	name__MODULE_ID__=input_firstname+" "+input_lastname;
	executescr("../modules/wid_addressbook.php","modid="+id__MODULE_ID__+"&addid="+oId+"&act=add&fn="+$p.string.esc(input_firstname)+"&ln="+$p.string.esc(input_lastname)+"&m="+$p.string.esc(input_mail)+"&c="+$p.string.esc(input_company)+"&f="+$p.string.esc(input_function)+"&p1="+$p.string.esc(input_phone1)+"&p2="+$p.string.esc(input_phone2)+"&o="+$p.string.esc(input_other)+"&tags="+$p.string.esc(oTags),false,true,treat__MODULE_ID__);
}
treat__MODULE_ID__=function(v_id){
	if (v_id!=indef){
		var l_id=v_id.split("_");
		if (l_id[0]!=id__MODULE_ID__){
			id__MODULE_ID__=l_id[0];
			tab[$p.app.tabs.sel].module[MODULE_ID_to_id("__MODULE_ID__")].changeVar("addid",id__MODULE_ID__);
		}
	}
	refr__MODULE_ID__();
}
sup__MODULE_ID__=function(id,newL){
	response=confirm(lg("msgAreYouSureSupElement"));
	if (response==1){executescr("../modules/wid_addressbook.php","modid="+id__MODULE_ID__+"&act=sup&addid="+id,false,true,treat__MODULE_ID__);}
}
opt__MODULE_ID__=function(v_id){
	if (<?php echo $userid;?>==0){alert(lg("msgNeedToBeConnected"));}
	else {
		var l_s = "<form action='' onsubmit='add__MODULE_ID__(this);return false;'><table>";
		l_s+= "<tr><td>"+lg("lblFirstName")+"</td><td><input name='fn__MODULE_ID__' type='text' maxlength='30' size='30' value='"+(v_id==indef?"":addr__MODULE_ID__[v_id].firstname)+"' /></td></tr>";
		l_s+= "<tr><td>"+lg("lblName")+"</td><td><input name='ln__MODULE_ID__' type='text' maxlength='30' size='30' value='"+(v_id==indef?"":addr__MODULE_ID__[v_id].lastname)+"' /></td></tr>";
		l_s+= "<tr><td>"+lg("lblEmail")+"</td><td><input name='m__MODULE_ID__' type='text' maxlength='60' size='30' value='"+(v_id==indef?"":addr__MODULE_ID__[v_id].mail)+"' /></td></tr>";
		l_s+= "<tr><td>"+lg("company")+"</td><td><input name='c__MODULE_ID__' type='text' maxlength='60' size='30' value='"+(v_id==indef?"":addr__MODULE_ID__[v_id].company)+"' /></td></tr>";
		l_s+= "<tr><td>"+lg("function")+"</td><td><input name='f__MODULE_ID__' type='text' maxlength='30' size='30' value='"+(v_id==indef?"":addr__MODULE_ID__[v_id].func)+"' /></td></tr>";
		l_s+= "<tr><td>"+lg("phone")+" 1</td><td><input name='p1__MODULE_ID__' type='text' maxlength='30' size='30' value='"+(v_id==indef?"":addr__MODULE_ID__[v_id].phone1)+"' /></td></tr>";
		l_s+= "<tr><td>"+lg("phone")+" 2</td><td><input name='p2__MODULE_ID__' type='text' maxlength='30' size='30' value='"+(v_id==indef?"":addr__MODULE_ID__[v_id].phone2)+"' /></td></tr>";
		l_s+= "<tr><td>"+lg("other")+"</td><td><input name='o__MODULE_ID__' type='text' maxlength='150' size='30' value='"+(v_id==indef?"":addr__MODULE_ID__[v_id].other)+"' /></td></tr>";
		l_s+= "<tr><td>"+lg("tags")+"</td><td><input name='t__MODULE_ID__' type='text' maxlength='100' size='30' value='"+(v_id==indef?"":addr__MODULE_ID__[v_id].tags)+"' /> <a href='#' onclick=\"return $p.app.alert.show('"+lg("linkKeysHlp")+"',2)\">"+$p.img("ico_help_s.gif",12,12,"imgmid")+"</a></td></tr>";
		l_s+= "<tr><td></td><td><input name='id' type='hidden' value='"+(v_id==indef?"":addr__MODULE_ID__[v_id].id)+"' /><input class='btn' type='submit' value='"+(v_id==indef?lg("lblAdd"):lg("lblModify"))+"' /> <a href='#' onclick='noopt__MODULE_ID__();return false;'><img src='../images/ico_close.gif' /></a></td></tr>";
		l_s+= "</table></form>";
		$p.print("addb__MODULE_ID__",l_s);
	}
}
noopt__MODULE_ID__=function(){
	if (shared__MODULE_ID__==1) return false;
	var obj=$p.get("addb__MODULE_ID__");
	obj.innerHTML="<a href='#' onclick='opt__MODULE_ID__();return false;'><img src='../images/ico_add.gif' /> "+lg("addContact")+"</a>";
}
refr__MODULE_ID__=function(){
	tab[$p.app.tabs.sel].module[MODULE_ID_to_id("__MODULE_ID__")].refresh();
}
init__MODULE_ID__=function(){
	noopt__MODULE_ID__();
<?php

if ($addId!=0  && isset($_SESSION["user_id"]) ){
    if( empty($sharedmd5key) 
        || (!isset($sharedmd5key)) 
        || $sharedmd5key=='undefined') {
            $DB->getResults($widAddr_getAddressBook,
                            $DB->escape($addId),
                            $DB->escape($_SESSION["user_id"])
                            );
    } else {
            $DB->getResults($widAddr_getAddressBookShared,
                            $DB->quote("%addid=".$DB->escape($addId)."%"),
                            $DB->quote($sharedmd5key),
                            $DB->escape($_SESSION['user_id']),
                            $DB->escape($widgetid),
                            $DB->escape($addId)
                            );
    }
    
	$inc=0;
	while ($row = $DB->fetch(0)){
		echo "addr__MODULE_ID__[".$inc."]=new addObj__MODULE_ID__(".$row["add_id"].",\"".$row["firstname"]."\",\"".$row["lastname"]."\",\"".$row["email"]."\",\"".$row["company"]."\",\"".$row["func"]."\",\"".$row["phone1"]."\",\"".$row["phone2"]."\",\"".$row["other"]."\",\"".$row["tags"]."\");";
		$inc++;
	}
	$DB->freeResults();
}
?>
	getKeys__MODULE_ID__();
	show__MODULE_ID__();
}
getKeys__MODULE_ID__=function(){
	for (var i=0;i<addr__MODULE_ID__.length;i++){
		var arr=addr__MODULE_ID__[i].tags.split(",");
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
        l_sAddr = "";

	for (var i=0;i<addr__MODULE_ID__.length;i++)
    {
		if (selKey__MODULE_ID__=="" || $p.array.find((addr__MODULE_ID__[i].tags.split(",")),selKey__MODULE_ID__))
        {
			l_sAddr += '<div class="articleborder" style="padding-bottom: 3px;background: url(../images/ico_adressbook.gif) no-repeat top left;padding-left: 20px;">';

            if (shared__MODULE_ID__==0)
			{
				l_sAddr += '<div style="float: right">'
    				+ '<a href="#" title="'+lg("lblModify")+'" onclick="opt__MODULE_ID__('+i+');return false;">'
    				+ '<?php echo $userid==0?'':'<img src="../images/ico_edit.gif" align="absmiddle" />';?>'
    				+ '</a> '
    				+ '<a href="#" title="'+lg("lblSuppress")+'" onclick="sup__MODULE_ID__('+addr__MODULE_ID__[i].id+',false);return false;">'
    				+ '<?php echo $userid==0?'':'<img src="../images/ico_close.gif" align="absmiddle" />';?>'
    				+ '</a>'
                    + '</div>';
			}
          
			l_sAddr	+= '<a id="lk__MODULE_ID___'+i+'" href="#" onclick="return detail__MODULE_ID__('+i+')" style="color:#000000">'
				+ addr__MODULE_ID__[i].lastname+', '+addr__MODULE_ID__[i].firstname
				+ '</a>'
                + '</div>';
		}
	}
	if (keys__MODULE_ID__.length>0)
    {
		l_s += '<div style="background-color:#efefef;padding:1px;">'
            + '<form>'
            + lg("tags")
            + ' : <select name="keywords" onchange="changeKey__MODULE_ID__(this.value);">'
            + '<option value="">* '
            + lg("all")
            + ' *</option>';

		keys__MODULE_ID__.sort();
		for (var i=0;i<keys__MODULE_ID__.length;i++)
        {
			l_s += '<option value="'+keys__MODULE_ID__[i]+'"'+(keys__MODULE_ID__[i]==selKey__MODULE_ID__?' selected="selected" ':'')+'>'
                + keys__MODULE_ID__[i]
                + '</option>';
		}
		l_s += '</select>'
            + '</form>'
            + '</div>';
	}

	l_s += l_sAddr;
    
	$p.print("lkd__MODULE_ID__",l_s);
}
changeKey__MODULE_ID__=function(v_key){
	selKey__MODULE_ID__=v_key;
	tab[$p.app.tabs.sel].module[MODULE_ID_to_id("__MODULE_ID__")].changeVar("selkey",v_key);
	show__MODULE_ID__();
}
detail__MODULE_ID__=function(v_id){
	var l_s = "<b>"+addr__MODULE_ID__[v_id].firstname+" "+addr__MODULE_ID__[v_id].lastname+"</b><table>";
	l_s+= "<tr><td>"+lg("lblEmail")+"</td><td>"+addr__MODULE_ID__[v_id].mail+"</td></tr>";
	l_s+= "<tr><td>"+lg("company")+"</td><td>"+addr__MODULE_ID__[v_id].company+"</td></tr>";
	l_s+= "<tr><td>"+lg("function")+"</td><td>"+addr__MODULE_ID__[v_id].func+"</td></tr>";
	l_s+= "<tr><td>"+lg("phone")+" 1</td><td>"+addr__MODULE_ID__[v_id].phone1+"</td></tr>";
	l_s+= "<tr><td>"+lg("phone")+" 2</td><td>"+addr__MODULE_ID__[v_id].phone2+"</td></tr>";
	l_s+= "<tr><td>"+lg("other")+"</td><td>"+addr__MODULE_ID__[v_id].other+"</td></tr>";
	l_s+= "<tr><td>"+lg("tags")+"</td><td>"+addr__MODULE_ID__[v_id].tags+"</td></tr>";
	l_s+= "<tr><td></td><td>";
	if (shared__MODULE_ID__==0) l_s+= "<input class='btn' type='button' onclick='opt__MODULE_ID__("+v_id+");return false;' value='"+lg("lblModify")+"' /> ";
	l_s+= "<a href='#' onclick='noopt__MODULE_ID__();return false;'><img src='../images/ico_close.gif' /></a></td></tr>";
	l_s+= "</table>";
	$p.print("addb__MODULE_ID__",l_s);
	return false
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