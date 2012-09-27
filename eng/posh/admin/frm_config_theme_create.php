<?php
# ************** LICENCE ****************
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
# ***************************************
# POSH theme creation
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=false;
$isPortal=false;
$granted="A";
$pagename="admin/frm_config_theme_create.php";
$errLog="";
$tabname="configstab";

require_once("includes.php");
require_once('header_inframe.php');

?>

<script type="text/javascript">
champ="";
formulaire="";

function ouvrir_palette(formulaire_recupere,champ_recupere)
{
	formulaire=formulaire_recupere;
	champ=champ_recupere;	 
	ma_palette=window.open("../tools/palette/palette.html","Palette_de_couleur","height=380,width=400,status=0, scrollbars=0,,menubar=0");
}
 
function valid_couleur(couleur)
{
	document.forms[formulaire].elements[champ].value=couleur;
	document.forms[formulaire].elements[champ].style.backgroundColor=couleur;
}

function noLogo(no_logo)
{
	if	(no_logo)
		document.tr.logopath.style.display='none';
	else
		document.tr.logopath.style.display='block';
}
</script>

<?php


$fontColor="#ffffff";
$headerFontColor="#ffffff";
$lettersColor="#000000";
$logoPath="";
$widgetBarColor="#ffffff";
$widgetTitleColor="#000000";
$themeName="";
$lettersSize=10;

echo '      <p><strong>'.lg("doMyTheme").'</strong></p>';
echo '		<div class="box">';
echo '		<form method="post" action="scr_config_theme_add.php" name="tr" enctype="multipart/form-data" onsubmit=return $p.admin.config.theme.themeverifname(); >';
echo '				<table>';
echo '					<input type="hidden" name="MAX_FILE_SIZE" value="500000" />';
echo '					<tr><td class="label"><img src="../images/puce.gif" /> '.lg("themeName").'</td><td><input type="text" name="themename" size="40" maxlength="60" value="'.$themeName.'" /></td></tr>';
echo '					<tr><td class="label"><img src="../images/puce.gif" /> '.lg("logo").'</td><td><input type="file" name="logopath" size="30"/></td><td>'.lg("noLogo").' <input type="checkbox" name="nologo" onclick=noLogo(this.checked); size="30"/></td></tr>';
echo '					<tr><td class="label"><img src="../images/puce.gif" /> '.lg("fontColor").'</td><td><input type="text" style="background-color:'.$fontColor.'" name="fontcolor" size="7" maxlength="7" value="'.$fontColor.'" /><input type="button"  value="Ouvrir la palette" onclick=ouvrir_palette("tr","fontcolor"); /></tr>';
echo '					<tr><td class="label"><img src="../images/puce.gif" /> '.lg("lettersColor").'</td><td><input type="text" style="background-color:'.$lettersColor.'" name="letterscolor" size="7" maxlength="7" value="'.$lettersColor.'" /><input type="button" value="Ouvrir la palette" onclick=ouvrir_palette("tr","letterscolor"); /></tr>';
echo '					<tr><td class="label"><img src="../images/puce.gif" /> '.lg("lettersSize").'</td><td><input type="text"  name="letterssize" size="2" maxlength="" value="'.$lettersSize.'" /></td></tr>';
echo '					<tr><td class="label"><img src="../images/puce.gif" /> '.lg("widgetBarColor").'</td><td><input type="text" name="widgetbarcolor" style="background-color:'.$widgetBarColor.'" size="7" maxlength="7" value="'.$widgetBarColor.'" /><input type="button" value="Ouvrir la palette" onclick=ouvrir_palette("tr","widgetbarcolor"); /></tr>';
echo '					<tr><td class="label"><img src="../images/puce.gif" /> '.lg("widgetTitleColor").'</td><td><input type="text" name="widgettitlecolor" style="background-color:'.$widgetTitleColor.'" size="7" maxlength="7" value="'.$widgetTitleColor.'" /><input type="button" value="Ouvrir la palette" onclick=ouvrir_palette("tr","widgettitlecolor"); /></tr>';
echo '					<tr><td class="label"><img src="../images/puce.gif" /> '.lg("headerFontColor").'</td><td><input type="text" name="headerfontcolor" style="background-color:'.$headerFontColor.'" size="7" maxlength="7" value="'.$headerFontColor.'" /><input type="button" value="Ouvrir la palette" onclick=ouvrir_palette("tr","headerfontcolor"); /></tr>';
echo '				</table>';
echo '				<table>';
echo '					<tr><p class="submit"><input type="submit" value="'.lg("addMyTheme").'" /></p></tr>';
echo '					<input type="hidden" name="add_new" value="true" />';
echo '				</table>';
echo '			</form>';
echo '	</div>';

?>