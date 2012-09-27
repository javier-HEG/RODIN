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
# igoogle widget import
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=0;
$pagename="tutorial/xml_igoogle.php";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

if (isset($_GET["moduleurl"]))
{
	if (preg_match("/url\=(\S+)/xmsi",$_GET["moduleurl"], $parts)){

		$url=urldecode($parts[1]);
		if (strpos($url,"http")===false) $url="http://".$url;
		if (strpos($url,"http")!=0) $url="http://".$url;
		require_once('../includes/http.inc.php');
        $h = new http($url);
        $body = $h->get();    
        if ($body) {
            echo $body;
        } else {

            $file->header("igoogle");
            echo "<nowidget>1</nowidget>";
            $file->footer();
        }
	} 
} else {
    $file->header("igoogle");
    echo "<nourl>1</nourl>";
    $file->footer();
}


?>