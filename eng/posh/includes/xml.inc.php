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
 * PHP XML class
 # !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
 */
class xmlFile
{
	var $charset;
	var $rootTag;
	/*
		xmlFile : Ctor
		Inputs :
			$header (boolean) : is the xml format defined in HTTP header
			$encoding (string) : file encoding
	*/
	function xmlFile($header = true,$encoding = "UTF-8")
	{
		$this->charset = $encoding;
		if ($header)
			header("content-type: application/xml; charset=".$this->charset);
	}
	/*
		header : write xml file header
		Inputs :
			$tag (string) : main file tag
	*/
	function header($tag = "phpscript")
	{
		$this->rootTag = $tag;
		echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'><'.$this->rootTag.'>';
	}
	/*
		footer: write xml file footer
		Inputs :
			$tag (string) : main file tag
	*/	
	function footer($tag = "")
	{
		if ($tag != "")
			$this->rootTag = $tag;
		echo '</'.$this->rootTag.'>';
	}
	/*
		status : send status code
		Inputs :
			$status (string) : status code returned
	*/
	function status($status = 1)
	{
		echo '<status>'.$status.'</status>';
	}
	/*
		error : send error to client
		Inputs:
			$errorMsg (string) : error message alias (will be treated by lg() javascript function)
	*/
	function error($errorMsg)
	{
		echo '<err><![CDATA['.$errorMsg.']]></err>';
	}
	/*
		message : send message to client 
		Inputs:
			$msg(string) : message alias (will be treated by lg() javascript function)
	*/
	function message($msg)
	{
		echo '<msg><![CDATA['.$msg.']]></msg>';
	}
	/*
		returnData : return data to client
		Inputs:
			$data (string) : data returned
	*/
	function returnData($data)
	{
		echo '<ret><![CDATA['.$data.']]></ret>';
	}
}
?>