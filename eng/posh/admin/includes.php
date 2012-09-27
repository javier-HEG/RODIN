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
 * Do all the core includes for admin pages
 * In the good order
 * 	1) Core
 *	2) Plugins
 */

require_once('../includes/config.inc.php');
require_once('../includes/connection_'.__DBTYPE.'.inc.php');
require_once('../includes/session.inc.php');
require('../db_layer/'.__DBTYPE.'/admin.php');
require_once('../includes/plugin.api.php');
if (file_exists('../includes/plugins.inc.php'))
include_once('../includes/plugins.inc.php');
require_once('../includes/admin.inc.php');

global $DB,$DB2;
if (empty($DB))
	$DB = new connection(__SERVER,__LOGIN,__PASS,__DB);
if (empty($DB2))
	$DB2 = new connection(__SERVER,__LOGIN,__PASS,__DB);

global $PLUGINS;
$PLUGINS = array();

?>