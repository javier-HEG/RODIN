<?php
/*
	Copyright (c) PORTANEO.

	This file is part of POSH (Portaneo Open Source Homepage) http://sourceforge.net/projects/posh/.

	POSH is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version

	POSH is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Posh.  If not, see <http://www.gnu.org/licenses/>.
*/
/* UTF8 encoding : é ô à ù */
$not_access=0;
$pagename="install";
require_once('confinstall.inc.php');
require_once('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/install.lang.php');
require_once('../includes/file.inc.php');
require_once('functions.inc.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Portaneo installation</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" media="screen" title="default" href="../styles/main.css" />
	<link rel="stylesheet" type="text/css" media="screen" title="default" href="../styles/install.css" />
    <script type="text/javascript" src="../tools/mootools.js?v=1.2.1"></script>
    <script type="text/javascript" src="../includes/install.js"></script>
    <script type="text/javascript" src="../includes/php/ajax-urls.js"></script>
    <script type="text/javascript" src="../l10n/<?php echo __LANG;?>/install.lang.js?v=2.1.0a1"></script>

<!--	<SCRIPT type="text/javascript" src="../includes/main.js"></SCRIPT>-->
<style>
body {
	background: #efefef;
}
/* CSS taken from http://www.webreference.com/programming/css_borders/2.html */
.raised {
	width:806px;
}
.raised .b2 {
	background:#fff; 
}
.raised .b3 {
	background:#fff; 
}
.raised .b4 {
	background:#fff; 
}
.raised .b4b {
	background:#fff;
}
.raised .b3b {
	background:#fff;
}
.raised .b2b {
	background:#fff; 
}
.raised .b1 {
	background:#efefef;
}
.raised .boxcontent {
	background:#fff; 
	width:784px;
    text-align: left;
}
</style>

</head>
<body>
<div width="100%">
 <div align="center" style="padding-top:40px">
  <div class="raised">
   <b class="b1"></b><b class="b2"></b><b class="b3"></b><b class="b4"></b>
    <div class="boxcontent" style="padding: 10px;">
     <div id="header">
      <table id="headertbl" cellspacing="0" cellpadding="0">
       <tr>
        <td style="padding:4px">
         <a href="index.php">
         <img src="../images/logo_install.gif" />
         </a>
        </td>
        <td align="right" valign="top">
	     <a href="?lang=en"><img class="flag" src="../images/languk.gif"></a><br />
	     <a href="?lang=fr"><img class="flag" src="../images/langfr.gif"></a><br />
         <a href="?lang=de"><img class="flag" src="../images/langde.gif"></a><br />
        </td>
       </tr>
      </table>
     </div>
     <div>
      <div valign="top">