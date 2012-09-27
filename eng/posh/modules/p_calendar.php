<?php
// this file needs to be utf8 without BOM. Check accentuated chars : é à è ù
header("content-type: application/xml");
$folder="";
$not_access=0;
$pagename="modules/p_calendar.php";
//includes
require('includes.php');

echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>';

$calid=isset($_POST["calid"])?$_POST["calid"]:0;
$calid = is_numeric($calid)?$calid:0;
$userid=isset($_SESSION['user_id'])?$_SESSION['user_id']:0;
$shared=isset($_POST['shared'])?$_POST['shared']:0;
$sharedmd5key=isset($_POST['sharedmd5key'])?$_POST['sharedmd5key']:'';
$shared = is_numeric($shared)?$shared:0;
$widgetid = isset($_POST['widgetid'])?$_POST['widgetid']:0;
?>
<Module>
<UserPref name="calid" display_name="." datatype="hidden" default_value="0" />
<Content><![CDATA[
<head>
<script>
var id__MODULE_ID__ = <?php echo $calid;?>;
var date__MODULE_ID__ = new Date();
var mod__MODULE_ID__ = 0;
var events__MODULE_ID__ = [];
var shared__MODULE_ID__ = <?php echo $shared;?>;
add__MODULE_ID__=function(v_form){
	var stime=v_form.hour__MODULE_ID__.value+":"+v_form.minute__MODULE_ID__.value;
	var etime=v_form.hourf__MODULE_ID__.value+":"+v_form.minutef__MODULE_ID__.value;
	if (etime<stime){alert(lg("endTimeMustBeHigher"));return;}
	var ndate=$p.date.getDbFormat(v_form.day__MODULE_ID__.value,v_form.mon__MODULE_ID__.value,v_form.yea__MODULE_ID__.value);
	executescr("../modules/wid_calendar.php","modid="+id__MODULE_ID__+"&act=add&t="+v_form.tit__MODULE_ID__.value+"&c="+v_form.com__MODULE_ID__.value+"&d="+ndate+"&h="+stime+"&end="+etime,false,true,get__MODULE_ID__);
}
get__MODULE_ID__=function(v_id)
{
	if (v_id != indef){
		var l_id = v_id.split("_");
		if (l_id[0] != id__MODULE_ID__){
			id__MODULE_ID__ = l_id[0];
			tab[$p.app.tabs.sel].module[MODULE_ID_to_id("__MODULE_ID__")].changeVar("calid",id__MODULE_ID__);
		}
	}
	if (mod__MODULE_ID__==0){
        $p.print("ev__MODULE_ID__",lg("lblLoading"));
		getXml("../modules/wid_calendar.php",displayMonth__MODULE_ID__,"","xml","modid="+id__MODULE_ID__+"&act=month&m="+(date__MODULE_ID__.getMonth()+1)+"&y="+date__MODULE_ID__.getFullYear()+"&sharedmd5key=<?php echo $sharedmd5key; ?>&widgetid=<?php echo $widgetid; ?>","POST");
	} else {
		var gdate=$p.date.getDbFormat(date__MODULE_ID__.getDate(),(date__MODULE_ID__.getMonth()+1),date__MODULE_ID__.getFullYear())
		getXml("../modules/wid_calendar.php",display__MODULE_ID__,"","xml","modid="+id__MODULE_ID__+"&act=get&d="+gdate+"&sharedmd5key=<?php echo $sharedmd5key; ?>&widgetid=<?php echo $widgetid; ?>","POST");
	}
}
display__MODULE_ID__=function(response,vars)
{
	noopt__MODULE_ID__();
	l_s = "<a href='#' onclick='displayMonthEvents__MODULE_ID__();return false;'>&laquo; "+lg("returnWholeMonth")+"</a><br /><br />";

	if (response.getElementsByTagName("event")[0]){
		var i=0;
		l_s += "<table cellpadding='1' cellspacing='0' width='100%'>";
		while (response.getElementsByTagName("event")[i])
        {
			var l_event = response.getElementsByTagName("event")[i];
			l_s += "<tr>"
                + "<td width='72'>"
                + $p.ajax.getVal(l_event,"time","str",false,"--")
                + " - "
                + $p.ajax.getVal(l_event,"endtime","str",false,"--")
                + "</td>"
                + "<td onmouseover=\"mouseBox('"+($p.ajax.getVal(l_event,"comment","str",false,"--").replace(/'/g," "))+"',event)\" onmouseout=\"mouseBox('')\">"
                + $p.ajax.getVal(l_event,"title","str",false,"--")
                + "</td>"
                + "<td width='15'>"
                + "<a href='#' onclick='sup__MODULE_ID__("+$p.ajax.getVal(l_event,"id","id",false,0)+")'><img src='../images/ico_close.gif' /></a>"
                + "</td>"
                + "</tr>";
			i++;
		}
		l_s += "</table>";
	} else {l_s += lg("lblNoEvent");}
    
	$p.print("ev_list__MODULE_ID__",l_s);
}
displayMonth__MODULE_ID__=function(response,vars)
{
	noopt__MODULE_ID__();
    
    events__MODULE_ID__.length = 0;
    
	var l_s = '<table cellpadding="0" cellspacing="0" style="border: 1px solid #0E679A;">'
        + '<tr style="background: #E9EDF2;">'
        + '<td style="text-align: center;height: 20px;">'
        + '<a href="#" onclick="return prev__MODULE_ID__()"><img src="../images/ico_previous.gif" /></a>'
        + '</td>'
        + "<td colspan='5' id='currdate__MODULE_ID__' style='text-align:center'>"
        + showdate__MODULE_ID__()
        + '</td>'
        + '<td style="text-align:center">'
        + '<a href="#" onclick="return next__MODULE_ID__();"><img src="../images/ico_next.gif" /></a>'
        + '</td>'
        + '</tr>'
    	+ '<tr style="background: #E9EDF2;">'
        + "<td style='width: 20px;height: 20px;text-align: center;'>" + lg("monday2")+ "</td>"
        + "<td style='width: 20px;height: 20px;text-align: center;'>"+lg("tuesday2")+"</td>"
        + "<td style='width: 20px;height: 20px;text-align: center;'>"+lg("wednesday2")+"</td>"
        + "<td style='width: 20px;height: 20px;text-align: center;'>"+lg("thirsday2")+"</td>"
        + "<td style='width: 20px;height: 20px;text-align: center;'>"+lg("friday2")+"</td>"
        + "<td style='width: 20px;height: 20px;text-align: center;'>"+lg("saturday2")+"</td>"
        + "<td style='width: 20px;height: 20px;text-align: center;'>"+lg("sunday2")+"</td>"
        + "</tr>";

    var current_month = new Date().getMonth()+1;
	var l_date = new Date(date__MODULE_ID__.getFullYear(),date__MODULE_ID__.getMonth(),1),
        l_today = new Date,
        inc = 0,
        l_day,
        l_month = l_date.getMonth()+1,
        l_yea = l_date.getFullYear(),
        xmlinc = 0,
        l_comment;

	var l_start = (l_date.getDay() == 0 ? 7 : l_date.getDay());
	for (var i = 1;i < l_start;i++)
    {
		if (i == 1)
            l_s += "<tr>";
		l_s += "<td style='width: 20px;height: 20px;text-align: center;'></td>";
		inc++;
	}
	while ((l_date.getMonth()) == date__MODULE_ID__.getMonth())
    {
		if (inc % 7 == 0) l_s += "<tr>";
		l_day = l_date.getDate();
		l_comment = "";
		if (response.getElementsByTagName("event")[xmlinc])
        {
			while(response.getElementsByTagName("event")[xmlinc] && $p.ajax.getVal(response.getElementsByTagName("event")[xmlinc],"day","int",false,0)==l_day)
            {
				var l_event = response.getElementsByTagName("event")[xmlinc];
                var l_title = $p.ajax.getVal(l_event,"time","str",false,"--")+"&raquo;"+$p.ajax.getVal(l_event,"endtime","str",false,"--")+" "+($p.ajax.getVal(l_event,"title","str",false,"--").replace(/'/g," "));
				l_comment += '' + l_title;
                
                // Only 24 events
                if ( events__MODULE_ID__.length < 24 && (l_month>current_month || ( l_month==current_month &&  l_day >= l_today.getDate() ) ) )
                    events__MODULE_ID__.push(l_day+'/'+l_month+'/'+l_yea+' '+l_title);
                
				xmlinc++;
			}
		}
		if (l_comment == "")
        {
			l_s+="<td style='width: 20px;height: 20px;text-align: center;' bgcolor='#ffffff' onmouseover=\"mouseBox('')\">"+l_day+"</td>";
		} else {
			l_s+="<td style='width: 20px;height: 20px;text-align: center;' bgcolor='#efefef' style='cursor:pointer;cursor:hand;font-weight:bold;' onclick='selDay__MODULE_ID__("+l_day+")' onmouseover=\"mouseBox('"+l_comment+"',event)\" onmouseout=\"mouseBox('')\">"+l_day+"</td>";
		}

		l_date.setDate(l_date.getDate()+1);
		inc++;
		if (inc % 7 == 0) l_s += "</tr>";
	}
	$p.print("ev__MODULE_ID__",l_s);
    
    displayMonthEvents__MODULE_ID__();
}
selDay__MODULE_ID__=function(day)
{
	date__MODULE_ID__ = new Date(date__MODULE_ID__.getFullYear(),date__MODULE_ID__.getMonth(),day);
	mod__MODULE_ID__ = 1;
	get__MODULE_ID__();
}
selMonth__MODULE_ID__=function(){
	mod__MODULE_ID__ = 0;
	get__MODULE_ID__();
}
sup__MODULE_ID__=function(id,newL){
	response = confirm(lg("msgAreYouSureSupElement"));
	if (response == 1)
    {
        executescr("../modules/wid_calendar.php","modid="+id__MODULE_ID__+"&act=sup&calid="+id,false,true,get__MODULE_ID__);
    }
}
opt__MODULE_ID__=function(obj)
{
	var l_s = "";
    
	if (<?php echo $userid;?>==0){alert(lg("msgNeedToBeConnected"));}
	else {
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
noopt__MODULE_ID__=function(){
	if (shared__MODULE_ID__==1) return false;
	var obj = $p.get("addb__MODULE_ID__");
	obj.innerHTML = "<a href='#' onclick='opt__MODULE_ID__(this);return false;'><img src='../images/ico_add.gif' /> "+lg("lblAddEvent")+"</a>";
}
init__MODULE_ID__=function(){
	get__MODULE_ID__();
}
next__MODULE_ID__=function(){
	if (mod__MODULE_ID__==0){date__MODULE_ID__.setMonth(date__MODULE_ID__.getMonth()+1);}
	else {date__MODULE_ID__=new Date(date__MODULE_ID__.getTime()+86400000);}
	get__MODULE_ID__();
	return false;
}
prev__MODULE_ID__=function(){
	if (mod__MODULE_ID__==0){date__MODULE_ID__.setMonth(date__MODULE_ID__.getMonth()-1);}
	else {date__MODULE_ID__=new Date(date__MODULE_ID__.getTime()-86400000);}
	get__MODULE_ID__();
	return false;
}
showdate__MODULE_ID__=function(){
	var mon = date__MODULE_ID__.getMonth(); 
	var dat = date__MODULE_ID__.getDate(); 
	var yea  = date__MODULE_ID__.getFullYear();
	return "<b>"
        + (mod__MODULE_ID__ == 0 ? "" : dat+" ")
        + lg("month"+(mon+1))
        + " "
        + yea
        + "</b>";
}
displayMonthEvents__MODULE_ID__=function()
{
    mod__MODULE_ID__ = 0;

    var l_s = '<b>'+lg('nextEventsForTheMonth')+' :</b><br /><br />';
    
    if (events__MODULE_ID__.length == 0)
    {
        l_s += lg('lblNoEvent');
    }
    else
    {
        l_s += '<ul>';
        for (var i = 0;i < events__MODULE_ID__.length;i++)
        {
            l_s += '<li>'
                + events__MODULE_ID__[i]
                + (__useNotebook ? ' <a href="#" onclick="shareEvent__MODULE_ID__('+i+');return false;">'+$p.img('ico_share_s.gif',13,10)+'</a>' : '')
                + '</li>';
            
        }
        l_s += '</ul>';
    }
    
    $p.print("ev_list__MODULE_ID__",l_s);
}
shareEvent__MODULE_ID__ = function(v_id)
{
    $p.friends.menu(6,0,{'title':events__MODULE_ID__[v_id],'description':''});
}
noopt__MODULE_ID__();
init__MODULE_ID__();
</script>
</head>
<body>
<div id="ev__MODULE_ID__" style="padding: 6px;float: left;"></div>
<div id="ev_list__MODULE_ID__" style="padding: 6px;float: left;"></div>
<hr class="float_correction" />
<div id="addb__MODULE_ID__" style="padding: 3px 0 5px 4px;"></div>
</body>
]]></Content>
</Module>