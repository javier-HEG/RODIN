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
# Generate new modules rss file
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

header("content-type: application/xml");
echo '<'.'?xml version="1.0" encoding="UTF-8"?>';

//includes
require_once('includes.php');;
?>
<rss version="2.0" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
<channel>
<title>PORTANEO: New modules</title>
<link>http://www.portaneo.com</link>
<description>Latest modules proposed on Portaneo.com</description>
<copyright>Portaneo 2006</copyright>
<image><url>http://www.portaneo.com/images/logofr.gif</url><title>PORTANEO</title><link>http://www.portaneo.com</link></image>
<pubDate><?php echo date("d/m/Y");?></pubDate> 
<?php

$DB->getResults($temcache_getLastModules);
while ($row = $DB->fetch(0))
{
	echo "<item>";
	echo "<title>" . $row["name"] . "</title>";
	echo "<link>http://www.portaneo.com/cache/dir_item" . $row["id"] . "_Y.html</link>";
	echo "<description>" . $row["description"] . "</description>";
	echo "<guid isPermaLink=\"false\">http://www.portaneo.com/cache/dir_item" . $row["id"] . "_Y.html</guid>";
	echo "</item>";
}
$DB->freeResults();
?>
</channel>
</rss>