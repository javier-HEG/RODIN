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

class file
{
	var $location;
	var $handle;
	/*
		Ctor
		Inputs :
			$loc (string) : file location
	*/
	function file($loc)
	{
		$this->location = $loc;
	}
	/*
		write : Write in a file
		Inputs :
			$str (string) : string inserted in the file
			$encode (boolean) : define if the file is UTF8 encoded
	*/
	function write($str,$encode=false)
	{
		$this->handle = @fopen ($this->location , "w" );
		if (!$this->handle) $this->warning(1);
		//LOCK the file to avoid multi input
		if ( !flock ( $this->handle , LOCK_EX + LOCK_NB )) $this->warning(2);
		//Write the HTML in this file
		if ($encode)
		{
			@fwrite ( $this->handle , utf8_encode($str));
		}
		else
		{
			@fwrite ( $this->handle , $str);
		}
		//@fwrite ( $this->handle , utf8_encode($str) , strlen ( $str ) ); // utf8 add cars and length is higher
		//Unlock the file & close
		@flock ( $this->handle , LOCK_UN );
		@fclose ( $this->handle );
	}
	/*
		read : read a file
	*/
	function read()
	{
		return file_get_contents($this->location);
	}
	/*
		readExternal : read external file
		Inputs :
			$server (string) : server hosting the file
			$port (string) : port to access the file
			$connection (string) : authentification, if applied
	*/
	function readExternal($server,$port,$connection)
	{
		$return="";
		$proxy_fp = fsockopen($server,$port);
		if (!$proxy_fp)    {return false;}
		fputs($proxy_fp, "GET $this->location HTTP/1.0\r\n");
		fputs($proxy_fp, "Proxy-Authorization: Basic ".$connection."\r\n\r\n");
		while(!feof($proxy_fp))
			$return .= fgets($proxy_fp,4092);
		fclose($proxy_fp);
		$return = substr($return, strpos($return,"\r\n\r\n")+4);
		return $return;
	}
	function warning($id)
	{
		echo "File issue (error ".$id.")";
		exit();
	}
}
?>