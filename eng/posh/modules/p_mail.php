<?php
// this file needs to be utf8 without BOM. Check accentuated chars : é à è ù
header("content-type: application/xml");
$folder="";
$not_access=0;
$pagename="modules/p_mail.php";
//includes
require_once('includes.php');

echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>';

$mailid=isset($_POST["mailid"])?$_POST["mailid"]:0;
$mailid = is_numeric($mailid)?$mailid:0;
$userid=isset($_SESSION['user_id'])?$_SESSION['user_id']:0;
?>
<Module>
<UserPref name="email" display_name="Email" datatype="hidden" default_value=" " />
<UserPref name="mailid" display_name="." datatype="hidden" default_value="0" />
<UserPref name="nb" display_name="lblNbEmails" datatype="enum" default_value="5">
  <EnumValue value="1" display_value="1"/>
  <EnumValue value="2" display_value="2"/>
  <EnumValue value="3" display_value="3"/>
  <EnumValue value="5" display_value="5"/>
  <EnumValue value="10" display_value="10"/>
  <EnumValue value="15" display_value="15"/>
</UserPref>
<Content><![CDATA[
<head>
<script>
var id__MODULE_ID__ = <?php echo $mailid;?>;
var prof__MODULE_ID__ = <?php echo $_POST["prof"];?>;
var current__MODULE_ID__ = {};
var email__MODULE_ID__ = $p.string.getVar(tab[$p.app.tabs.sel].module[MODULE_ID_to_id("__MODULE_ID__")].vars,"email");
var emails__MODULE_ID__ = [];

askEmail__MODULE_ID__ = function(v_errmsg)
{
	if (<?php echo $userid;?> == 0)
    {
		$p.print('em__MODULE_ID__',lg('msgNeedToBeConnected'));
	}
    else
    {
        var l_savedEmail = $p.string.getVar(tab[$p.app.tabs.sel].module[MODULE_ID_to_id("__MODULE_ID__")].vars,"email");
        var l_email = ( l_savedEmail == '' ? '<?php echo $_SESSION['username'];?>'
                                           : l_savedEmail);
        var l_s = (v_errmsg == indef ? '' : lg(v_errmsg) + '<br />')
            + '<form onsubmit="return checkProvider__MODULE_ID__(this)">'
            + lg("lblYourEmail") + ' :<br />'
            + '<input type="text" size="50" maxlength="60" name="email" value="'+l_email+'" />'
            + '<br />'
            + lg('lblPassword') + ' : <br />'
            + '<input type="password" size="20" name="pass" /> '
            + '<input type="submit" value="'+lg('submit')+'" />'
            + '</form>';
		$p.print('em__MODULE_ID__',l_s);
	}
}
checkProvider__MODULE_ID__=function(v_form)
{
	var l_email = v_form.email.value;
	var l_pass = v_form.pass.value;
	if (checkEmail(l_email))
    {
		tab[$p.app.tabs.sel].module[MODULE_ID_to_id("__MODULE_ID__")].changeVar("email",l_email);
		//module[MODULE_ID_to_id("__MODULE_ID__")].changeVar("pass",l_pass);
		getXml("../modules/wid_mail.php?act=check&provider="+l_email.substring(l_email.indexOf("@")+1),getProvider__MODULE_ID__,l_pass);
	} else {
		alert(lg("msgSubEmailValid"));
	}
	return false;
}
getProvider__MODULE_ID__=function(response,vars)
{
	if (response.getElementsByTagName("provider")[0])
    {
		saveParam__MODULE_ID__($p.string.getVar(tab[$p.app.tabs.sel].module[MODULE_ID_to_id("__MODULE_ID__")].vars,"email"),
            vars,
            $p.ajax.getVal(response,"provider","str",false,""),
            $p.ajax.getVal(response,"protocol","str",false,""),
            $p.ajax.getVal(response,"port","str",false,""),
            $p.ajax.getVal(response,"server","str",false,""),
            $p.ajax.getVal(response,"webmail","str",false,"")
        );
	} else {
		askParam__MODULE_ID__(vars);
	}
}

askParam__MODULE_ID__=function(v_pass)
{
    //set language with lg function
	var l_s = "<form onsubmit='return getParam__MODULE_ID__(this)'>"
        + "<table>"
        + "<tr>"
        + "<td>"
        + lg("lblAccount")
        + "</td>"
        + "<td>"
        + "<input type=text name='euser' value='' size=22 maxlength=32 />"
        + "</td>"
        + "</tr>"
        + "<tr>"
        + "<td>"
        + lg("lblProtocol")
        + "</td>"
        + "<td>"
        + "<select onChange=\"$(\'port\').value=getPort__MODULE_ID__(this.value);\" name='protocole'>"
        + "<option value='/pop3'>POP3</option>"
        + "<option value=''>IMAP</option>"
        + "<option value='/pop3/ssl/novalidate-cert'>POP3 SSL</option>"
        + "<option value='/imap/ssl'>IMAP SSL</option>"
        + "</select>"
        + "</td>"
        + "</tr>"
        + "<tr>"
        + "<td>"
        + lg("lblPort")
        + "</td>"
        + "<td>"
        + "<input type=text id='port' name='port' value='110' size=3 maxlength=5 />"
        + "</td>"
        + "</tr>"
        + "<tr>"
        + "<td>"
        + lg("lblServerPop")
        + "</td>"
        + "<td>"
        + "<input type=text name='server' value='' size=22 maxlength=32 />"
        + "</td>"
        + "</tr>"
        + "<tr>"
        + "<td>"
        + "</td>"
        + "<td>"
        + "<input type='hidden' name='pass' value='"+v_pass+"' />"
        + "<input type='submit' value='"+lg("lblBtnValid")+"' />"
        + "</td>"
        + "</tr>"
        + "</table>"
        + "</form>";

	$p.print("em__MODULE_ID__",l_s);
}

getPort__MODULE_ID__=function(port)
{
    var ports = {'/pop3':110,'/pop3/ssl/novalidate-cert':995,'':143,'/imap':143,'/imap/ssl':993};
    return ports[port];
}

getParam__MODULE_ID__=function(v_form)
{
	saveParam__MODULE_ID__(v_form.euser.value,v_form.pass.value,"",v_form.protocole.value,v_form.port.value,v_form.server.value,"");
	return false;
}
saveParam__MODULE_ID__ = function(v_user,v_pass,v_provider,v_protocol,v_port,v_server,v_webmail)
{
	getXml("../modules/wid_mail.php?act=conf",getEmailId__MODULE_ID__,"","xml","user="+v_user+"&pass="+v_pass+"&provider="+v_provider+"&protocol="+v_protocol+"&port="+v_port+"&server="+v_server+"&webmail="+v_webmail,"post");
}
getEmailId__MODULE_ID__=function(response,vars)
{
	if (response.getElementsByTagName("id")[0])
    {
		id__MODULE_ID__ = $p.ajax.getVal(response,"id","int",false,0);
		tab[$p.app.tabs.sel].module[MODULE_ID_to_id("__MODULE_ID__")].changeVar("mailid",id__MODULE_ID__);
		getEmails__MODULE_ID__();
	} else {
        $p.app.alert.show(lg("lblEmailIssue"));
    }
}
getEmails__MODULE_ID__=function(v_start)
{
    if (v_start == indef) v_start = 0;

    var l_tabid = $p.app.tabs.idToPos(prof__MODULE_ID__),
        l_modid = MODULE_ID_to_id("__MODULE_ID__",l_tabid)
        l_nb = $p.string.getVar(tab[l_tabid].module[l_modid].vars,"nb") == "" ? 5 : $p.string.getVar(tab[l_tabid].module[l_modid].vars,"nb");
        
    $p.print("em__MODULE_ID__",lg("lblLoading"));

    //reset emails array
    emails__MODULE_ID__.length = 0;

	getXml("../modules/wid_mail.php?act=get",showEmails__MODULE_ID__,new Array(l_tabid,l_modid,v_start,l_nb),"xml","id="+id__MODULE_ID__+"&nb="+l_nb+"&start="+v_start,"post");
}
showEmails__MODULE_ID__=function(response,vars)
{
	var l_s = "",
        l_nb = $p.ajax.getVal(response,"number","int",false,-1),
        l_unread = $p.ajax.getVal(response,"unread","int",false,0),
        l_webmail = $p.ajax.getVal(response,"webmail","str",false,""),
        l_nbToDisplay = parseInt(vars[3],10),
        l_start = parseInt(vars[2],10);

    if (l_nb == -1)
    {
		askEmail__MODULE_ID__(lg("lblEmailIssue")+" "+$p.ajax.getVal(response,"error","str",false,""));
		$p.app.debug($p.ajax.getVal(response,"debug","str",false,"Unknown error"),"error");
		return;
	}

    var l_email = "<?php if(isset($_POST["email"]))echo $_POST["email"];?>";

	l_s += (l_webmail == "" ? " "
                            : "<a href='"+l_webmail+"' target='_blank'>"
            )
        + (l_email == "" ? "" : l_email + "<b> : </b>")
        + (l_webmail == "" ? "" : "</a>");

	if (l_nb == 0) {
        l_s += lg("noNewMail");
    } else {
		l_s += l_nb+" "+lg("emails")
            + (l_unread == l_nb ? "" : " | "+l_unread+" "+lg('unread')) 
            + "<br /><br />"
            + "<div id='emaillist__MODULE_ID__'>";

        l_emailList = response.getElementsByTagName("mail");
        
        for (var i = 0; i < l_emailList.length; i++)
        {
            if (i < l_nbToDisplay)
            {
                var l_result = l_emailList[i],
                    l_subject = correctMailEncoding($p.string.lc($p.ajax.getVal(l_result,"subject","str",false,"-"))),
                    l_id = $p.ajax.getVal(l_result,"id","int",false,0),
                    l_status = $p.ajax.getVal(l_result,"status","str",false,""),
                    l_sender = $p.ajax.getVal(l_result,"sender","str",false,"-");
                
    			l_s += "<div class='articleborder' style='padding-bottom: 3px;background: url(../images/ico_mail_unread.gif) no-repeat top left;padding-left: 20px;'>"
                    + '<a href="#" onclick=\'return readEmail__MODULE_ID__('+i+')\' '+(l_status == 'N' ? ' style="font-weight:bold;"' : '')+'>'
                    + "<b>"
                    + l_subject.substr(0,50)
                    + "</b>"
                    + "</a>"
                    + "<br />"
                    + lg("lblFrom")
                    + " : "
                    + correctMailEncoding($p.string.lc(l_sender))
                    + "</div>";
                
                emails__MODULE_ID__.push({'id':l_id,'subject':l_subject,'status':l_status,'sender':l_sender});
			}
		}
        if (l_emailList.length > l_nbToDisplay || l_start > 0)
        {
            l_s += '<table width="90%">'
                + '<tr>';
            if (l_start > 0){
                l_s += '<td align="left">'
                    + '<a href="#" onclick="getEmails__MODULE_ID__('+(l_start-l_nbToDisplay)+');return false;">'
                    + $p.img('ico_previous3.gif',8,11,lg('previous'),'imgmid')
                    + ' '
                    + lg('previous')
                    + '</a>'
                    + '</td>';
            }
            if (l_emailList.length > l_nbToDisplay){
                l_s += '<td style="text-align: right;">'
                    + '<a href="#" onclick="getEmails__MODULE_ID__('+(l_start+l_nbToDisplay)+');return false;">'
                    + lg('next')
                    + " "
                    + $p.img('ico_next3.gif',8,11,lg('next'),"imgmid")
                    + '</a>'
                    + '</td>';
            }
            l_s += '</tr>'
                + '</table>';
        }

		l_s += "</div>"
            + "<div id='emaildetail__MODULE_ID__'></div>";
	}
	$p.print("em__MODULE_ID__",l_s);
	
	$p.app.widgets.changeName(vars[1],"("+l_unread+") "+l_email,vars[0]);
}
readEmail__MODULE_ID__ = function(v_id)
{
//    $p.show("emaillist__MODULE_ID__","none");
//    $p.show("emaildetail__MODULE_ID__","block");
//    $p.app.wait("emaildetail__MODULE_ID__");

    $p.app.widgets.rss.reader.init();
    $p.app.widgets.rss.reader.displayInSourceList(email__MODULE_ID__);
    
    loadEmail__MODULE_ID__(v_id);
}
loadEmail__MODULE_ID__ = function(v_id)
{
    $p.app.wait('npdetail');
    
    $p.app.widgets.rss.reader.selArticle = emails__MODULE_ID__[v_id].id;
    
    //generate sidebar
    var l_sb = [];
    for (var i = 0;i < emails__MODULE_ID__.length;i++)
    {
        l_sb.push({'id':emails__MODULE_ID__[i].id,'html':'<a class="title" href="#" onclick="loadEmail__MODULE_ID__('+i+');return false;">'+emails__MODULE_ID__[i].subject+'</a> '+(__useNotebook ? ' | <a href=\'#\' onclick=\'shareEmail__MODULE_ID__();return false;\'>'+$p.img('ico_share_s.gif',13,10)+'</a>' : '')});
    }
    $p.app.widgets.rss.reader.buildSideBar(l_sb);

	getXml("../modules/wid_mail.php?act=read",showEmailBody__MODULE_ID__,emails__MODULE_ID__[v_id].subject,"xml","id="+id__MODULE_ID__+"&messid="+emails__MODULE_ID__[v_id].id,"post");
	return false;
}
showEmailBody__MODULE_ID__ = function(response,vars)
{   
    var l_desc = $p.string.removeTags($p.string.removeScriptTag($p.string.removeStyleTag($p.string.removeHeadTag($p.string.lc($p.ajax.getVal(response,"message","str",false,""))))));
    if (l_desc == '') l_desc = 'email not readable.';
    current__MODULE_ID__ = {'title':vars,'description':correctMailEncoding(l_desc,false)};
    
    $p.app.widgets.rss.reader.displayContent('<div style="padding: 8px">'+correctMailEncoding(l_desc,true)+'</div>');
}
shareEmail__MODULE_ID__ = function()
{
    $p.friends.menu(6,0,current__MODULE_ID__);
}
restoreEmailList__MODULE_ID__=function()
{
	$p.print("emaildetail__MODULE_ID__","");
	$p.show("emaildetail__MODULE_ID__","none");
	$p.show("emaillist__MODULE_ID__","block");
	return false;
}
if (id__MODULE_ID__ == 0)
{
	askEmail__MODULE_ID__();
} else {
	getEmails__MODULE_ID__();
}
</script>
</head>
<body>
<div style="width: 100%;">
<div style="padding: 8px;" id="em__MODULE_ID__" />
</div>
</body>
]]></Content>
</Module>