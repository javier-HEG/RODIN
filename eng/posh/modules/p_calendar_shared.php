<?php
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
header("content-type: application/xml");
$folder="";
$not_access=0;
$pagename="modules/p_calendar.php";
//includes
require_once('includes.php');

echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>';

$calid=isset($_POST["calid"])?$_POST["calid"]:0;
$calid = is_numeric($calid)?$calid:0;
$userid=isset($_SESSION['user_id'])?$_SESSION['user_id']:0;
?>
<Module>
<UserPref name="calid" display_name="." datatype="hidden" default_value="0" />
<Content><![CDATA[
<head>
<script>
var id__MODULE_ID__=<?php echo $calid;?>;
var date__MODULE_ID__=new Date();
var mod__MODULE_ID__=0;
add__MODULE_ID__=function(v_form)
{
	var stime=v_form.hour__MODULE_ID__.value+":"+v_form.minute__MODULE_ID__.value;
	var etime=v_form.hourf__MODULE_ID__.value+":"+v_form.minutef__MODULE_ID__.value;
	if (etime<stime){alert(lg("endTimeMustBeHigher"));return;}
	var ndate=$p.date.getDbFormat(v_form.day__MODULE_ID__.value,v_form.mon__MODULE_ID__.value,v_form.yea__MODULE_ID__.value);
	executescr("wid_calendar_shared.php","modid="+id__MODULE_ID__+"&act=add&t="+v_form.tit__MODULE_ID__.value+"&c="+v_form.com__MODULE_ID__.value+"&d="+ndate+"&h="+stime+"&end="+etime,false,true,get__MODULE_ID__);
}
get__MODULE_ID__=function(v_id)
{
	navPrint("ev__MODULE_ID__",lg("lblLoading"));
	if (v_id!=indef)
	{
		var l_id=v_id.split("_");
		if (l_id[0]!=id__MODULE_ID__)
		{
			id__MODULE_ID__=l_id[0];
			tab[$p.app.tabs.sel].module[MODULE_ID_to_id("__MODULE_ID__")].changeVar("calid",id__MODULE_ID__);
		}
	}
	if (mod__MODULE_ID__==0)
	{
		getXml("wid_calendar_shared.php",displayMonth__MODULE_ID__,"","xml","modid="+id__MODULE_ID__+"&act=month&m="+(date__MODULE_ID__.getMonth()+1)+"&y="+date__MODULE_ID__.getFullYear(),"POST");
	}
	else
	{
		var gdate=$p.date.getDbFormat(date__MODULE_ID__.getDate(),(date__MODULE_ID__.getMonth()+1),date__MODULE_ID__.getFullYear())
		getXml("wid_calendar_shared.php",display__MODULE_ID__,"","xml","modid="+id__MODULE_ID__+"&act=get&d="+gdate,"POST");
	}
}
display__MODULE_ID__=function(response,vars)
{
	noopt__MODULE_ID__();
	l_s="<a href='#' onclick='selMonth__MODULE_ID__()'>"+lg("returnWholeMonth")+"</a><br />";
	if (response.getElementsByTagName("event")[0])
	{
		var i=0;
		l_s+="<table cellpadding='1' cellspacing='0' width='100%'>";
		while (response.getElementsByTagName("event")[i])
		{
			var l_event=response.getElementsByTagName("event")[i];
			l_s+="<tr><td width='72'>"+$p.ajax.getVal(l_event,"time","str",false,"--")+" - "+$p.ajax.getVal(l_event,"endtime","str",false,"--")+"</td><td onmouseover=\"mouseBox('"+($p.ajax.getVal(l_event,"comment","str",false,"--").replace(/'/g," "))+"',event)\" onmouseout=\"mouseBox('')\">"+$p.ajax.getVal(l_event,"title","str",false,"--")+"</td><td width='15'><a href='#' onclick='sup__MODULE_ID__("+$p.ajax.getVal(l_event,"id","id",false,0)+")'><img src='../images/ico_close.gif' /></a></td></tr>";
			i++;
		}
		l_s+="</table>";
	}
	else
	{
		l_s+=lg("lblNoEvent");
	}
	navPrint("ev__MODULE_ID__",l_s);
}
displayMonth__MODULE_ID__=function(response,vars)
{
	noopt__MODULE_ID__();
	var l_s="<table cellpadding='1' cellspacing='1' border='0' width='100%' style='background-color:#c6c3c6'>";
	l_s+="<tr><td>"+lg("monday2")+"</td><td>"+lg("tuesday2")+"</td><td>"+lg("wednesday2")+"</td><td>"+lg("thirsday2")+"</td><td>"+lg("friday2")+"</td><td>"+lg("saturday2")+"</td><td>"+lg("sunday2")+"</td></tr>"
	var l_date=new Date(date__MODULE_ID__.getFullYear(),date__MODULE_ID__.getMonth(),1);
	var inc=0,l_day,xmlinc=0,l_comment;
	var l_start=(l_date.getDay()==0?7:l_date.getDay());
	for (var i=1;i<l_start;i++)
	{
		if (i==1)l_s+="<tr>";
		l_s+="<td height='30' valign='top'></td>";
		inc++;
	}
	while ((l_date.getMonth())==date__MODULE_ID__.getMonth())
	{
		if (inc % 7==0)l_s+="<tr>";
		l_day=l_date.getDate();
		l_comment="";
		if (response.getElementsByTagName("event")[xmlinc])
		{
			while(response.getElementsByTagName("event")[xmlinc] && $p.ajax.getVal(response.getElementsByTagName("event")[xmlinc],"day","int",false,0)==l_day){
				var l_event=response.getElementsByTagName("event")[xmlinc];
				l_comment+=$p.ajax.getVal(l_event,"time","str",false,"--")+"/"+$p.ajax.getVal(l_event,"endtime","str",false,"--")+" "+($p.ajax.getVal(l_event,"title","str",false,"--").replace(/'/g," "))+"<br />";
				xmlinc++;
			}
		}
		if (l_comment=="")
		{
			l_s+="<td height='30' valign='top' bgcolor='#ffffff' onmouseover=\"mouseBox('')\">"+l_day+"</td>";
		}
		else
		{
			l_s+="<td height='30' valign='top' bgcolor='#efefef' style='cursor:pointer;cursor:hand;font-weight:bold;' onclick='selDay__MODULE_ID__("+l_day+")' onmouseover=\"mouseBox('"+l_comment+"',event)\" onmouseout=\"mouseBox('')\">"+l_day+"</td>";
		}
		l_date.setDate(l_date.getDate()+1);
		inc++;
		if (inc % 7==0)l_s+="</tr>";
	}
	navPrint("ev__MODULE_ID__",l_s);
}
selDay__MODULE_ID__=function(day)
{
	date__MODULE_ID__=new Date(date__MODULE_ID__.getFullYear(),date__MODULE_ID__.getMonth(),day);
	mod__MODULE_ID__=1;
	showdate__MODULE_ID__();
	get__MODULE_ID__();
}
selMonth__MODULE_ID__=function()
{
	mod__MODULE_ID__=0;
	showdate__MODULE_ID__();
	get__MODULE_ID__();
}
sup__MODULE_ID__=function(id,newL)
{
	response=confirm(lg("msgAreYouSureSupElement"));
	if (response==1){executescr("wid_calendar_shared.php","modid="+id__MODULE_ID__+"&act=sup&calid="+id,false,true,get__MODULE_ID__);}
}
opt__MODULE_ID__=function(obj)
{
	var l_s="";
	if (<?php echo $userid;?>==0)
	{
		alert(lg("msgNeedToBeConnected"));
	}
	else
	{
		l_s+="<form name='f__MODULE_ID__' action='' onsubmit='add__MODULE_ID__(this);return false;'><table cellpadding='1' cellspacing='0'><tr><td>"+lg("lblTitle")+"</td><td><input type='text' name='tit__MODULE_ID__' maxlength='64' size='32' /></td></tr><tr><td>"+lg("Comment")+"</td><td><input type='text' name='com__MODULE_ID__' maxlength='250' size='35' /></td></tr>";
		l_s+="<tr><td>"+lg("lblDate")+"</td>";
		l_s+="<td><select name='day__MODULE_ID__'><option value='1'>01</option><option value='2'>02</option><option value='3'>03</option><option value='4'>04</option><option value='5'>05</option><option value='6'>06</option><option value='7'>07</option><option value='8'>08</option><option value='9'>09</option><option value='10'>10</option><option value='11'>11</option><option value='12'>12</option><option value='13'>13</option><option value='14'>14</option><option value='15'>15</option><option value='16'>16</option><option value='17'>17</option><option value='18'>18</option><option value='19'>19</option><option value='20'>20</option><option value='21'>21</option><option value='22'>22</option><option value='23'>23</option><option value='24'>24</option><option value='25'>25</option><option value='26'>26</option><option value='27'>27</option><option value='28'>28</option><option value='29'>29</option><option value='30'>30</option><option value='31'>31</option></select>";
		l_s+="<select name='mon__MODULE_ID__'><option value='1'>"+lg("month1")+"</option><option value='2'>"+lg("month2")+"</option><option value='3'>"+lg("month3")+"</option><option value='4'>"+lg("month4")+"</option><option value='5'>"+lg("month5")+"</option><option value='6'>"+lg("month6")+"</option><option value='7'>"+lg("month7")+"</option><option value='8'>"+lg("month8")+"</option><option value='9'>"+lg("month9")+"</option><option value='10'>"+lg("month10")+"</option><option value='11'>"+lg("month11")+"</option><option value='12'>"+lg("month12")+"</option></select>";
		l_s+="<select name='yea__MODULE_ID__'><option value='2006'>2006</option><option value='2007'>2007</option><option value='2008'>2008</option><option value='2009'>2009</option><option value='2010'>2010</option><option value='2011'>2011</option><option value='2012'>2012</option></select></td></tr>";
		//l_s+="<tr><td>"+lg("time")+"</td><td><input type='text' name='time__MODULE_ID__' size='5' maxlength='5' value='hh:mm' /></td></tr><tr><td></td><td><input class='btn' type='submit' value='"+lg("lblAdd")+"' /> <a href='#' onclick='noopt__MODULE_ID__();return false;'><img src='../images/ico_close.gif' /></a></td></tr></table></form>";
		l_s+="<tr><td>"+lg("lblFrom")+"</td><td><select name='hour__MODULE_ID__'><option value='00'>00 h</option><option value='01'>01 h</option><option value='02'>02 h</option><option value='03'>03 h</option><option value='04'>04 h</option><option value='05'>05 h</option><option value='06'>06 h</option><option value='07'>07 h</option><option value='08'>08 h</option><option value='09'>09 h</option><option value='10'>10 h</option><option value='11'>11h</option><option value='12'>12 h</option><option value='13'>13 h</option><option value='14'>14h</option><option value='15'>15 h</option><option value='16'>16 h</option><option value='17'>17 h</option><option value='18'>18 h</option><option value='19'>19 h</option><option value='20'>20 h</option><option value='21'>21 h</option><option value='22'>22 h</option><option value='23'>23 h</option></select> ";
		l_s+="<select name='minute__MODULE_ID__'><option value='00'>00 mn</option><option value='05'>05 mn</option><option value='10'>10 mn</option><option value='15'>15 mn</option><option value='20'>20 mn</option><option value='25'>25 mn</option><option value='30'>30 mn</option><option value='35'>35 mn</option><option value='40'>40 mn</option><option value='45'>45 mn</option><option value='50'>50 mn</option><option value='55'>55 mn</option></select></td></tr>";
		l_s+="<tr><td>"+lg("until")+"</td><td><select name='hourf__MODULE_ID__'><option value='00'>00 h</option><option value='01'>01 h</option><option value='02'>02 h</option><option value='03'>03 h</option><option value='04'>04 h</option><option value='05'>05 h</option><option value='06'>06 h</option><option value='07'>07 h</option><option value='08'>08 h</option><option value='09'>09 h</option><option value='10'>10 h</option><option value='11'>11h</option><option value='12'>12 h</option><option value='13'>13 h</option><option value='14'>14h</option><option value='15'>15 h</option><option value='16'>16 h</option><option value='17'>17 h</option><option value='18'>18 h</option><option value='19'>19 h</option><option value='20'>20 h</option><option value='21'>21 h</option><option value='22'>22 h</option><option value='23'>23 h</option></select> ";
		l_s+="<select name='minutef__MODULE_ID__'><option value='00'>00 mn</option><option value='05'>05 mn</option><option value='10'>10 mn</option><option value='15'>15 mn</option><option value='20'>20 mn</option><option value='25'>25 mn</option><option value='30'>30 mn</option><option value='35'>35 mn</option><option value='40'>40 mn</option><option value='45'>45 mn</option><option value='50'>50 mn</option><option value='55'>55 mn</option></select></td></tr>";
		l_s+="<tr><td></td><td><input class='btn' type='submit' value='"+lg("lblAdd")+"' /> <a href='#' onclick='noopt__MODULE_ID__();return false;'><img src='../images/ico_close.gif' /></a></td></tr></table></form>";
		obj.parentNode.innerHTML=l_s;
		
		l_form=document.forms["f__MODULE_ID__"];
		l_form.yea__MODULE_ID__.value=date__MODULE_ID__.getFullYear();
		l_form.mon__MODULE_ID__.value=(date__MODULE_ID__.getMonth()+1);
		l_form.day__MODULE_ID__.value=date__MODULE_ID__.getDate();
	}
}
noopt__MODULE_ID__=function()
{
	var obj=_gel("addb__MODULE_ID__");obj.innerHTML="<a href='#' onclick='opt__MODULE_ID__(this);return false;'><img src='../images/ico_add.gif' /> "+lg("lblAddEvent")+"</a>";
}
init__MODULE_ID__=function()
{
	showdate__MODULE_ID__();
	get__MODULE_ID__();
}
next__MODULE_ID__=function()
{
	if (mod__MODULE_ID__==0){date__MODULE_ID__.setMonth(date__MODULE_ID__.getMonth()+1);}
	else {date__MODULE_ID__=new Date(date__MODULE_ID__.getTime()+86400000);}
	showdate__MODULE_ID__();
	get__MODULE_ID__();
	return false;
}
prev__MODULE_ID__=function()
{
	if (mod__MODULE_ID__==0){date__MODULE_ID__.setMonth(date__MODULE_ID__.getMonth()-1);}
	else {date__MODULE_ID__=new Date(date__MODULE_ID__.getTime()-86400000);}
	showdate__MODULE_ID__();
	get__MODULE_ID__();
	return false;
}
showdate__MODULE_ID__=function()
{
	var mon= date__MODULE_ID__.getMonth(); 
	var dat= date__MODULE_ID__.getDate(); 
	var yea= date__MODULE_ID__.getFullYear();
	navPrint("currdate__MODULE_ID__","<b>"+(mod__MODULE_ID__==0?"":dat+" ")+lg("month"+(mon+1))+" "+yea+"</b>");
}
noopt__MODULE_ID__();
init__MODULE_ID__();
</script>
</head>
<body>
<table cellpadding="4" cellspacing="0" width="100%" bgcolor="#efefef">
<tr><td style="text-align:center"><a href="#" onclick="return prev__MODULE_ID__()"><img src="../images/ico_previous.gif" /></a></td><td id="currdate__MODULE_ID__" style="text-align:center"></td><td style="text-align:center"><a href="#" onclick="return next__MODULE_ID__();"><img src="../images/ico_next.gif" /></a></td></tr>
</table>
<table cellpadding="4" cellspacing="0" width="100%">
<tr><td id="ev__MODULE_ID__" style='padding:6px;height:70px;' valign='top'>
</td></tr></table>
<div id="addb__MODULE_ID__" style='padding:3px 0 5px 4px;'></div>
</body>
]]></Content>
</Module>