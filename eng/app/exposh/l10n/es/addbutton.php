<?php
require_once('../../includes/config.inc.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<HEAD>
	<TITLE>PORTANEO :: add button</TITLE>
	<link rel="stylesheet" href="../../styles/main.css" type="text/css" />
	<LINK rel="stylesheet" href="../../../app/exposh/styles/main1.css.php" type="text/css" />
	<META http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</HEAD>
<body>
<div class="noportal">
<table cellpadding="0" cellspacing="0" border="0" style="width:100%">
	<tr>
	<td id="header">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
		<td id="logo"><img src="../../images/s.gif" width="140" height="60" /></td>
		</tr>
	</table>
	</td>
	</tr>
	<tr>
	<td align="center" valign="top">
	<table cellpadding="10" cellspacing="0" border="0" width="100%">
		<tr>
		<td align="left" valign="top" style="padding-top:30px">
A&ntilde;adir este botón en tu web<br /><br />
<img src="../../images/addtoapplication.gif"><br /><br />
<textarea cols=60 rows=10>
<A href="<?php echo __LOCALFOLDER;?>portal/addtoapplication.php?pid=<?php echo $_GET["id"];?>" title="añade este modulo en <?php echo __APPNAME;?>"><img src="<?php echo __LOCALFOLDER;?>images/addtoapplication.gif" alt="" style="border:0px" align=absmiddle /></A>
</textarea>

		</TD>
		</TR>
	</TABLE>
	</TD>
	</TR>
</TABLE>
<SCRIPT type="text/javascript">
	if (navigator.appName=="Netscape")
	{	 top.outerWidth=550; top.outerHeight=550;} 
	else top.resizeTo(550,550);
	top.moveTo(30,30);
</SCRIPT>
</div>
</BODY>
</HTML>
