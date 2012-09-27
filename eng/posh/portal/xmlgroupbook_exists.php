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

    * !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù

    */
/*! \brief xmlgroupbook_exists.php
* \details 
        check that group exists from groupname
        portal/xmlgroupbook_exists.php
        request:  $xmlgroupbook_getByName
        
        SELECT count(*) as exist FROM notebook_groups where UCASE(name)=%s
        
    \see mysql/enterprise.php
    \see application.js -> function $p.groupbook.add
    
*/

$folder="";
$not_access=1;
$granted="I";
$pagename=$_SERVER['SCRIPT_NAME']; #"portal/xmlgroupbook_exists.php";


//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();
$file->header("result");

    $name=$_GET["name"];
if($name)
{
	$DB->getResults($xmlgroupbook_getByName, strtoupper($DB->quote($name)));
	
	while($row=$DB->fetch(0))
	{
		echo "<exist>".$row["exist"]."</exist>";
	}
	$DB->freeResults();
	
	$DB->close();
}else{
	echo "<exist>1</exist>";	
}

$file->footer("result");

?>