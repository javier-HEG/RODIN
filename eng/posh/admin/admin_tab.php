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
/*
 * Displays tabs for plugins
 */
$folder="../portal/";
$not_access=1;
$isScript=false;
$isPortal=false;
$granted="A";
$pagename="admin/admin_tab.php";
require_once('includes.php');

global $ADMIN_TABS;

// No specified tab or not a valid one
if (!is_array($_GET) || !array_key_exists('page',$_GET) 
	|| !array_key_exists($_GET['page'],$ADMIN_TABS) || !array_key_exists('function',$ADMIN_TABS[$_GET['page']])
	|| !function_exists($ADMIN_TABS[$_GET['page']]['function']))    {
	//header('Location: index.php');
}

$tabname=$_GET['page'];

require('header_inframe.php');

//Call the tab content
call_user_func_array($ADMIN_TABS[$_GET['page']]['function'],array());

//require('footer.inc.php'); 

?>