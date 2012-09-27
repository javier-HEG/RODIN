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
//get feed content for first checks
$t=$_POST["url"];
if ($t=="") exit();

require_once('../includes/http.inc.php');
require_once('../includes/misc.inc.php');

$h = new http($t);

$body = $h->get(); 
if (!isUTF8($body)) {
    $body = utf8_encode($body);
}
echo $body;
?>