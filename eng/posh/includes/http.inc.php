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
 * PHP HTTP client
 # !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
 */
class http
{
	// Server address without http:// and without the path
	var $server;
	var $cServer="";
	// File to retrieve in the server
	var $file;
	
	// Port used to contact the server
	var $port;
	var $cPort;
	// Header sent to the server
	var $header;
	// Header of the response FROM the server
	var $head;
	// Body of the response
	var $body;

	// Accepted type (header sent)
	var $type;
	// User agent (header sent)
	var $user_agent;
	// Protocol : ssl:// or nothing
	var $protocol;
	var $cProtocol;
	// Code of the response
	var $code;
	// String associated to the response code
	var $codeStr;
	// Header authorization
	var $authorization;
	var $proxy_auth;
	
	// Max redirections
	var $max_d=5;
	// Timeout for http connection
	var $timeout=10;
	// Image type
	var $imgType=10;
	
	/*
	 * Ctor
	 * Input :
	 *	$url (string) : url to retrieve, including the protocol (http or https)
	 */
	function http($url)
	{
		$this->url($url);
		
		$this->user_agent = 'posh';
		
		$this->header = "";
		
		$this->code = "";
		$this->codeStr = "";
		
		$this->max_d = 5;
		
		$this->authorization = "";
		$this->proxy_auth = "";
		
		$this->url = $url;
	}
	
	/*
	 * To change the url to contact, with the same client
	 * Useful for redirections
	 * Input :
	 *	$url (string) : new URL to retrieve
	 */
	function url($url)
	{
		preg_match('/^http[s]?:\/\/([^\/]+)([\/\?].*)?$/',$url,$matches);

		if (count($matches) == 0)
		{
			$this->file = $url;
		}
		else
		{
			$pos = strpos($matches[1],":");
			if ($pos === false)
			{
				$this->server = $matches[1];
				$this->port = (strpos($url,'https://')!==false ? 443:80);
			}
			else
			{
				$this->server = substr($matches[1],0,$pos);
				$this->port = substr($matches[1],$pos+1);
			}
			$this->file = count($matches)<3 ? "":$matches[2];
			//if ($matches[2]!="")
			//	$this->port=$matches[2];

			$this->protocol = (strpos($url,'https://')!==false ? 'ssl://':'http://');
		}
	}
	
	/*
	 * Contacts the URL and retrieve the content
	 */
	function get()
	{
		// Contructs the header
		$this->get_header();
		// Open connection
		if ($this->proxy_auth == "")
		{
			$socket = @fsockopen($this->protocol.$this->server,$this->port,$errNo,$errStr,$this->timeout);
			if (!$socket) $socket = @fsockopen($this->server,$this->port,$errNo,$errStr,$this->timeout);
		}
		else
		{
			if ($this->cServer == "")
			{
				$socket = @fsockopen($this->server,$this->port,$errNo,$errStr,$this->timeout);
			}
			else
			{
				$socket = @fsockopen($this->cServer,$this->cPort,$errNo,$errStr,$this->timeout);
			}
		}
		if (!$socket)
		{
			echo "<error><![CDATA[Can not connect to resource ".$this->server." ".$this->port." (in http->get method)]]></error>";
			return false;
		}
		// Send the headers
		fputs($socket,$this->header);
		
		$response = "";
		$inc = 0;
		// Get the response
		while (!feof($socket))
		{	
			$response .= fgets($socket,4092);
			$inc ++;
		}
		// Bye
		fclose($socket);

		// Detect the body and header
		preg_match('/^(((?!\\r\\n\\r\\n).)*)(\\r\\n){2}(.*)$/sm',$response,$matches);
		$utf8 = "";
		$this->head = $matches[1]."\r\n";
		preg_match('/
					encoding\s*=(\"|\')([^\(\"|\')]+)(\"|\')
					/xmsi', $matches[4], $encoding);
		preg_match("/
				(text\/xml|application\/xml)
					/xmsi",$matches[1],$content_type);
		if( count($encoding) == 0 &&  count($content_type)>0 )
        {
			$utf8 = "<?xml version='1.0' encoding='UTF-8'?>";
		}
		$matches[4] = $utf8.$matches[4];
		$this->body = $matches[4];
		// Check for errors
		preg_match('/^HTTP\/[0-9\.]* ([0-9]*) (.*)\\r\\n/m',$this->head,$matches);
		$this->code = $matches[1];
		$this->codeStr = $matches[2];
		
		// Redirection
		//if (($this->code == 301 || $this->code == 302) && $this->max_d > 0)
		if (($this->code >= 300 && $this->code < 400) && $this->max_d > 0)
		{
			if (preg_match('/Location: (.*)\\r\\n/',$this->head,$matches))
			{
				$this->max_d --;
				$this->url($matches[1]);
				$this->get_header(true);
				return $this->get();
			}
		}
		if ($this->code >= 400) return $this->code;
		return $this->body;
	}
	
	/*
	 * Get Image type
	 */
	function getImageType( $url )
	{
		$data = @getImageSize( $url );
		$this->imgType = ".ico";
		switch( $data[2] )
		{
			case 1 : $this->imgType = ".gif"; break;
			case 2 : $this->imgType = ".jpg"; break;
			case 3 : $this->imgType = ".png"; break;
			case 4 : $this->imgType = ".swf"; break;
			case 5 : $this->imgType = ".psd"; break;
			case 6 : $this->imgType = ".bmp"; break;
			default : break;
		}
		return $this->imgType;
	}
	
	/*
	 * Constructs the header, according to the object's data
	 * Input : 
	 *	$force (bool) : if true the headers are re-calculated, if not and if the header already exists, nothing is done
	 */
	function get_header($force = false)
	{
		if ($this->header == "" || $force)
		{
			$this->file = ($this->file=="" ? "/":$this->file);
			//$this->header = "GET ".$this->protocol.$this->server.$this->file." HTTP/1.0\r\n";
			if ($this->proxy_auth=="")
			{
				$this->header = "GET ".$this->file." HTTP/1.0\r\n";
			}
			else
			{
				$this->header = "GET ".$this->protocol.$this->server.$this->file." HTTP/1.0\r\n";
			}
			$this->header .= "Host: ".$this->server."\r\n";
			//$this->header .= "Accept: ".$this->type."\r\n";
			$this->header .= "User-Agent: ".$this->user_agent."\r\n";
			if ($this->authorization !="")
				$this->header .= "Authorization: ".$this->authorization."\r\n";
			if ($this->proxy_auth !="")
				$this->header .= $this->proxy_auth;
			$this->header .= "\r\n";
		}
		return $this->header;
	}
	
	/*
	 * Need to specify authentification data in the header ?
	 * Use this
	 * Input :
	 *	$str (string) : Authorization header data
	 */
	function put_authorization($str)
	{
		$this->authorization = $str;
	}
	
	/*
	 * Returns a header value of the response
	 * Input :
	 *	$name (string) : header name
	 * Returns :
	 *	The header value or false is nothing is found
	 */
	function get_header_value($name)
	{
		if (preg_match("/$name: ?([^\\r\\n]*)\\r\\n/m",$this->head,$matches) !== false)
		{
			if (count($matches) > 1)
            {
                return $matches[1];
            }
		}
		return false;
	}
	
	/*
	 * Returns a sub_header value
	 * Example : Content-Type: text/html; charset=ISO-8859-4
	 *                                    -------------------
	 * Input :
	 *	$header (string) : header value
	 *	$sub (string) : sub value to find
	 * Returns :
	 *	The value or false
	 */
	function get_header_subvalue($header,$sub)
	{
		$h = $this->get_header_value($header);
		if ($h !== false && preg_match("/.*$sub=([^ \\r\\n;]*).*/",$h,$matches) !== false)
		{
            if (count($matches) > 1) return $matches[1];
		}
		return false;
	}
}

?>