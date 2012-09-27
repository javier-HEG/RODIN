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
# Files management class
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

class log
{
	function write($connection,$str,$type)
	{
		$connection->execute('INSERT INTO adm_log (log,logdate,typ) VALUES ("'.$str.'",CURRENT_DATE,"'.$type.'") ');
	}
	function generateRss($connection,$key)
	{
		$str = '<'.'?xml version="1.0" encoding="UTF-8"?>';
		$str.= '<rss version="2.0" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">';
		$str.= '<channel>';
		$str.= '<title>Admin log</title>';
		$str.= '<link>http://www.portaneo.com</link>';
		$str.= '<description />';
		$str.= '<pubDate>'.date("d/m/Y").'</pubDate>';

		$connection->getResults("SELECT id,log,logdate FROM adm_log WHERE typ='A' OR typ='I' ORDER BY logdate DESC,id DESC LIMIT 0,20 ");
		while ($row = $connection->fetch(0))
		{
			$str.= '<item>';
			$str.= '<title><![CDATA['.$row["log"].']]></title>';
            $str.= '<description />';
			$str.= '<guid isPermaLink="false">http://nolink/admin'.$row["id"].'</guid>';
			$str.= '</item>';
		}
		$connection->freeResults();

		$str.= '</channel>';
		$str.= '</rss>';
		
		$outfile=new file("../cache/rssadmin".$key.".xml");
		$outfile->write($str);
	}
}
?>