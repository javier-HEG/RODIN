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
require("header.inc.php");

$checks=true;

$magic_quotes = array(
        'fr'    => 'http://www.portaneo.com/websites/redactor/index.php?option=com_content&view=article&id=132',
        'en'    => 'http://www.portaneo.com/websites/redactor/index.php?option=com_content&view=article&id=133',
        ''      => 'http://www.portaneo.com/websites/redactor/index.php?option=com_content&view=article&id=133'
);
$magic_quotes_link =  $magic_quotes[__LANG] ? $magic_quotes[__LANG] : $magic_quotes[''];

$collab_suite = isset($_GET['collab_suite']) ? $_GET['collab_suite'] : '';

//if (isset($_POST["installtype"])) setStep(1,$_POST["installtype"]);
?>

<div class="bottomhr"><h1><?php echo lg("installation".I_APPLICATION_ID);?></h1></div>
<br />
<h2><?php echo lg("step1Title");?></h2><br />
<form action="step2.php?installtype=<?php echo $_GET["installtype"];?>" method="post">
<table cellpadding=5 cellspacing="0">
<?php
if ($collab_suite == "on") {
    echo '<input type="hidden" name="collab_suite" id="collab_suite" value="on" />';
}

//check1 : PHP server
if (version_compare(phpversion(), "4.3", ">=")){
	echo "<tr><td><img src='../images/ico_install_ok.gif'></td><td>".lg("phpOk")."</td></tr>";
} else {
	echo "<tr><td><img src='../images/ico_install_nok.gif'></td><td>".lg("phpNok")."</td></tr>";
	$checks=false;
}

//check2 : Mysql module
if (function_exists('mysql_connect')){
	echo "<tr><td><img src='../images/ico_install_ok.gif'></td><td>".lg("mysqlOk")."</td></tr>";
} else {
	echo "<tr><td><img src='../images/ico_install_nok.gif'></td><td>".lg("mysqlNok")."</td></tr>";
	$checks=false;
}
if (get_magic_quotes_gpc()) {
    echo '<tr><td ><img src="../images/ico_install_nok.gif"></td><td ><a target="_blank" style="color:orange;font-weight:bolder;" href="'.$magic_quotes_link.'">'.lg("MagicQuotesMustnotbeActive"). "</a></td></tr>";
	$checks=true;
} else {

}

//ckeck3 : navigator
echo "<tr><td><img src='../images/ico_install_arrow.gif'></td><td>".lg("navigatorNeeds")."</td></tr>";

//check4 : directories
$nonWritablefiles=array();
if (!is_writable("../includes/ajax.js")) array_push($nonWritablefiles,lg("fileMustBeWritable","<i>includes</i>"));
if (!is_writable("../modules/index.html") OR !is_writable("../modules/cache/index.html") OR !is_writable("../modules/external/index.html") OR !is_writable("../modules/pictures/index.html") OR !is_writable("../modules/quarantine/index.html")) array_push($nonWritablefiles,lg("fileMustBeWritable","<i>modules</i>"));
if (!is_writable("../portal/selections/index.html")) array_push($nonWritablefiles,lg("fileMustBeWritable","<i>portal/selections</i>"));
if (!is_writable("../styles/index.html") OR !is_writable("../styles/themes/index.html")) array_push($nonWritablefiles,lg("fileMustBeWritable","<i>styles</i>"));
if (!is_writable("../cache/index.html")) array_push($nonWritablefiles,lg("fileMustBeWritable","<i>cache</i>"));
if (!is_writable("../temp/index.html")) array_push($nonWritablefiles,lg("fileMustBeWritable","<i>temp</i>"));
if (!is_writable("../upload/index.html")) array_push($nonWritablefiles,lg("fileMustBeWritable","<i>upload</i>"));

if (count($nonWritablefiles)>0){
	echo "<tr><td valign='top'><img src='../images/ico_install_nok.gif'></td><td>".lg("fileNok")."<br />";
	echo implode("<br />",$nonWritablefiles);
	echo "<br /><br /><a href='http://www.portaneo.net' target=_blank>".lg("howToFile")."</a>";
	echo "</td></tr>";
	$checks=false;
} else {
	echo "<tr><td><img src='../images/ico_install_ok.gif'></td><td>".lg("fileOk")."</td></tr>";
}
$invalidConf=array();
if (ini_get("allow_url_fopen")=="0" OR ini_get("allow_url_fopen")=="Off" OR ini_get('allow_url_fopen')=="") array_push($invalidConf,lg("fopenError"));
if (count($invalidConf)>0){
	echo "<tr><td valign='top'><img src='../images/ico_install_nok.gif'></td><td>".lg("iniNok")."<br />";
	echo implode("<br />",$invalidConf);
	echo "<br /><br /><a href='http://www.portaneo.net' target=_blank>".lg("howToIni")."</a>";
	echo "</td></tr>";
	$checks=false;
} else {
	echo "<tr><td><img src='../images/ico_install_ok.gif'></td><td>".lg("iniOk")."</td></tr>";
}
?>
</table><br /><br />
<?php
if ($checks){
	echo "<h3>".lg("installationCanStart".I_APPLICATION_ID)."</h3>";
	echo "<br /><br /><center><input type='submit' value='".lg("continue")." >>>'></center>";
} else {
	echo "<h3>".lg("installationCannotStart")."</h3>";
	echo "<br /><br /><center><input type='button' value='".lg("restart")."' onclick=\"window.location='step1.php?installtype=".$_GET["installtype"]."';\"></center>";
}
?>
</form>

<?php
require("footer.inc.php");
?>
