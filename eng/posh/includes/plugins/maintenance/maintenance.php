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
/*
 * name: Maintenance
 * description: Redirect users to the maintenance page.<br />Just disable this plugin to give access again to your application.<br /><br /> > To access your application even if the plugin is enabled, <br />> you can connect on portal/login.php?key=__KEY (defined in the includes/config.inc.php file).<br />
 * display:  style="font-weight:bolder; color:red;background-color: #FFD363;"
 * dependencies: no
 * author: Portaneo
 * url: http://www.portaneo.net
 */
 
register_hook('login_php','redirectToMaintenancePage',5,0);
register_hook('index_php','redirectToMaintenancePage',5,0);

function redirectToMaintenancePage()
{
	if (isset($_GET["key"]))
	{
		if ($_GET["key"]!=__KEY) exit();
	}
	else
	{
		header("location: ../includes/plugins/maintenance/html/redirection.php");
		exit();
	}
}


?>