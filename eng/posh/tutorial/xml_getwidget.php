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
# Expert modules - Generate the module file and save module information
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$pagename="tutorial/xml_getwidget.php";
require_once('includes.php');
include_once('../../app/exposh/l10n/'.__LANG.'/tutorial.lang.php');
$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted=__USERMODULE;

require_once('../includes/file.inc.php');
require_once('../includes/xml.inc.php');
$file=new xmlFile();
if (isset($_POST['code']) && $_POST['code'] ) {
    echo $_POST['code'];
}
?>