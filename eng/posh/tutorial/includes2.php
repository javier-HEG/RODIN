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
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é

	include_once('../../config.inc.php');
	$folder="";
	$not_access=1;
	$useTabs=true;
	//$granted=__USERMODULE;
	if (!isset($granted))
		$granted=__USERMODULE;
        
	include_once('../../connection_'.__DBTYPE.'.inc.php');
	include_once('../../session.inc.php');
	include_once('../../plugin.api.php');
	//include_once('../../plugins.inc.php');
	include_once('../../../db_layer/'.__DBTYPE.'/tutorial.php');
	include_once('../../tutorial.inc.php');	
    
    /*
           include_once(__LOCALFOLDER.'includes/connection_'.__DBTYPE.'.inc.php');
	include_once(__LOCALFOLDER.'includes/session.inc.php');
	include_once(__LOCALFOLDER.'includes/plugin.api.php');
	include_once(__LOCALFOLDER.'includes/plugins.inc.php');
	include_once(__LOCALFOLDER.'db_layer/'.__DBTYPE.'/tutorial.php');
	include_once(__LOCALFOLDER.'includes/tutorial.inc.php');
            */
    
	global $DB;
	$DB = new connection(__SERVER,__LOGIN,__PASS,__DB);
    
?>