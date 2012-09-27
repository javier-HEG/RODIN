<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Loading ... </title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="language" content="<?php echo __LANG;?>" />
<link rel="stylesheet" type="text/css" href="../styles/main.css?v=<?php echo __POSHVERSION;?>" />
<script type="text/javascript" src="../portal/selections/waiting.js"></script>
<script type="text/javascript" src="../../app/exposh/l10n/<?php echo __LANG;?>/lang.js?v=<?php echo __POSHVERSION;?>" ></script>
<script type="text/javascript" src="../includes/config.js?v=<?php echo __rand;?>" ></script>
<script type="text/javascript" src="../tools/mootools.js?v=1.2.1"></script>
<script type="text/javascript" src="../../app/exposh/includes/ajax<?php if (!__debugmode) echo '_compressed';?>.js?v=<?php echo __POSHVERSION;?>"></script>
<script type="text/javascript" src="../includes/php/ajax-urls.js?v=<?php echo __POSHVERSION;?>"></script>
<?php 
	launch_hook('userinterface_header',$pagename);
?>
</head>
<?php
if (__defaultmode=="connected") { ?> <script type='text/javascript'>$p.url.openLink('login.php')</script> <?php exit();} ?>
<body onUnload="$p.app.counter.stop();">
<div id="cache" style="position:absolute;left:0;top:0;z-index:8;display:none;"></div>
<div id="vmenu"></div>
<!-- Header -->
<div id='headlink' name='headlink'></div>
<div id='information'></div>
<div id="hmenu"></div>
<table cellpadding="0" cellspacing="0" border="0" style="width:100%;">
	<tr>
	<td id="header">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
		<td id="logo"><a href="#" onclick="return $p.app.openHome();"><img id="logolink" src="../images/s.gif" /></a></td>
		<td valign="bottom">
		<div id="tabs"></div>
		</td>
		</tr>
	</table>
	</td>
	</tr>
</table>
<table id="area" cellpadding="0" cellspacing="0" border="0" width="100%">
<!--do not suppress this part (for xcss improvement), else the portal could be unstable-->
	<tr>
	<td>
<!-- Menus -->
<div id="headmenu"></div>
<div id="menus"><div id="audio"></div><div id="advise"></div><div id="magic"></div>
<!--<div id="loading" class="loading"></div>-->
<a name="contac"></a><div id="contact"></div><div id="box"></div><div id="other"></div><div id="newmod"></div>
</div>
<!-- Modules -->
<div id="messages"></div>
<div id="modules" class="maintbl"></div>
<div id="newspaper"></div>
<div id="plugin"></div>
<div id="empty" style="display:none"></div>
<!-- Footer -->
<div id="footer"></div>
<div id="debug"></div>
	</td>
	</tr>
</table>
<script type="text/javascript"><!--
//Work in progress message
//wip_message="<center><br /><img src='../images/loading.gif' align='absmiddle' /> <strong>"+lg("lblLoadingPage")+"<\/strong><br \/>- <a href='../portal/blank.html' class='smalll'>"+lg("lblCancel")+"<\/a> -<br /><br /><br />"+waiting();
wip_message="<center><br />loading...<br/><img src='../images/ajax-loader.gif' align='absmiddle' /><br /><a href='#' onclick='$p.app.resetAndReload()'>"+lg("appLoadingIssue")+"</a></center>";
//$p.app.loading();
window.onload=function()
{
	//load Posh objects
	$p.app.pageMode(0);
	//noinclusion(); prevent from page inclusion
}
// -->
</script>
<?php launch_hook('userinterface_end',$pagename);?>
</body>
</html>