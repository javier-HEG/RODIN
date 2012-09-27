<?php
# ************** LICENCE ****************
/*
	Copyright (c) PORTANEO.

	This file is part of COLLABORATION SUITE of POSH http://sourceforge.net/projects/posh/.

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
# modules management main page
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="../portal/";
$not_access=1;
$isScript=false;
$isPortal=false;
$granted="A";
$pagename="admin/pages_portals.php";
$tabname="pagestab";

require_once('includes.php');
set_include_path("../");
require('header.inc.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
?>


<table cellpadding="10" cellspacing="0" border="0" width="100%">
<tr>
<td valign="top">
<h1><img src="../images/ico_adm_page.gif" align="absmiddle" /> <?php echo lg("usersPortalMgmt");?> :</h1>
<a href="pages.php"><?php echo lg("backPrevPage");?></a><br />
<div id="optmod" style='padding-top:8px'></div>
<div class="bottomhr"></div>
<div id="listmod" class="greydiv"></div><br />
<div id="moduleshdr"></div><div id="modules"></div>
<div id="loading" class="loading" style='display:none;'>Loading ...</div>
<div id="newmod" class="tophr"></div>
</td>
</tr>
</table>


<?php
write_admin_tabs($tabname);
?>


<script type="text/javascript">

$p.app.pages.columns.nb=3;
//p_area.root=_gel("modules");
admPortals.init();
</script>


<?php
require('footer.inc.php');
?>