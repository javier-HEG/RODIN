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
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
// get feed content with basic authentification access
require('../includes/config.inc.php');

$t=$_POST["url"];
if ($t=="") exit();

if (isset($_POST["proxy"]))
{
	$proxy=$_POST["proxy"];
	$pos=strpos($proxy,":");
	$proxyserver=substr($proxy,0,$pos);
	$proxyport=substr($proxy,$pos+1);
}
else
{
	$proxyserver=__PROXYSERVER;
	$proxyport=__PROXYPORT;
}
require_once('../includes/http.inc.php');

$h = new http($t);

$h->cServer=$proxyserver;
$h->cPort=$proxyport;
$h->proxy_auth = "Proxy-Authorization: Basic ".__PROXYCONNECTION."\r\n";
if (isset($_POST["auth"])) $h->put_authorization("Basic ".$_POST["auth"]);

$body = $h->get(); 
echo $body;
?>