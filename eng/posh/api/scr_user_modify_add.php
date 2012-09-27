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
# POSH Users management - Delete de criteria
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

require_once("includes_api.php");
require_once("../includes/admin_tools.php");
require_once("authentication.php");

$username = isset($_POST['username']) ? $_POST['username'] : $_GET['username'];
$userdatas = getUserByUsername($username);
$userid   = $userdatas[0];
$md5    = $userdatas[1];


require_once("../admin/scr_user_modify_add.inc.php");
?>