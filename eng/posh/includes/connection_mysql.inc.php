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
# MYSQL classes
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

define('FETCH_OBJECT',1);
define('FETCH_ARRAY',2);

class connection
{
	var $id;
	var $sql;
	var $result;
	/*
		connection : Ctor
		Inputs:
			$server (string) : mySQL server
			$usernamedb (string) : database login
			$passworddb (string) : database password
			$dbname (string) : database name
		output :
	*/
	function connection($server,$usernamedb,$passworddb,$dbname)
	{
		$this->id = @mysql_connect($server, $usernamedb, $passworddb);
		
		if ($this->id)
		{
			$this->selectDB($dbname);
			$this->setUtf8();
		}
		else
		{
			$this->warning();
		}
	}
	/*
		selectDB : select a database
		inputs:
			$name (string) : database name
		output :
			true or error number
	*/
	function selectDB($name)
	{
		return @mysql_select_db($name);
	}
	/*
		close : close the server connexion
	*/
	function close()
	{
		if ($this->id)
		{
			@mysql_close($this->id);
		}
		else
		{
			$this->warning();
		}
	}
	/*
		getResults : build array with query results
		Inputs :
			$sql (string) : SQL query
	*/
	function getResults($sql)
	{
		if (func_num_args()>1)
		{
			$args = func_get_args();
			array_shift($args);
			$this->sql=vsprintf($sql,$args);
		}
		else
		{
			$this->sql=$sql;
		}
		$this->result = mysql_query($this->sql,$this->id);
		if (!$this->result) $this->warning();
	}
	/*
		nbResults : return last query results number
		Output : number of rows
	*/
	function nbResults()
	{
		return mysql_num_rows($this->result);
	}
	/*
		fetch : return a row of mySQL query results
		Inputs :
			$type (integer) : result type
		Output : array
	*/
	function fetch($type)
	{
		$l_type=($type==0)?MYSQL_ASSOC:MYSQL_NUM;
		return mysql_fetch_array($this->result, $l_type);
	}
	/*
		freeResults : Free all memory associated with last query
	*/
	function freeResults()
	{
		@mysql_free_result($this->result);
	}
	/*
		execute : execute a SQL query
		Inputs :
			$sql (string) :  SQL query
		Output : true or error number
	*/
	function execute($sql)
	{
		//FRI
		if (func_num_args()>1)
		{
			$args = func_get_args();
			array_shift($args);
		
		//print("<hr><br>FRI portaneo Db Execute: (". vsprintf($sql,$args) .")<hr>");
		
		$this->sql=vsprintf($sql,$args);
		}
		else
		{
			$this->sql=$sql;
		}
		
        	
		return mysql_query($this->sql,$this->id);
	}
	/*
		getId : get new record ID for last SQL query
		Output : integer, new record ID
	*/
	function getId()
	{
		return mysql_insert_id($this->id);
	}
	/*
		nbAffected : return number of rows affected by last query
		Output : integer
	*/
	function nbAffected()
	{
		return mysql_affected_rows($this->id);
	}
	/*
		showError : Return mySQL error message for last query
		output : string
	*/
	function showError()
	{
		return mysql_error();
	}
	/*
		warning : display latest MySQL error message
		Output : string, error message
	*/
	function warning()
	{
		Global $pagename;
		$errormsg=((mysql_error()!==false)?mysql_error():'Unknown error')."(".((mysql_errno()!==false)?mysql_errno():0).")";
		echo $errormsg;
		error_log("WARNING SQL : ".$this->sql." SET AN ERROR => ".$errormsg);
        mysql_query('INSERT INTO adm_log (log,logdate,typ) VALUES ("'.$pagename.' : '.$errormsg.'",CURRENT_DATE,"O") ');
        
	}
	function noSqlInjection($str,$type)
	{
		/*	$type=1 : numeric value
			$type=2 : string value with no " or ' or /or \ or ? or = ... ex: password, login
			$type=3 : string that can contain " or ' but no = or # or -- */
		if ($type==1 && !is_numeric($str)) return "";
		if ($type==2) $str=mysql_real_escape_string($str);
		if ($type==3){
			$str=str_replace("--","",$str);
			$str=strtr($str,"=#","  ");
		}
		return $str;
	}
	/*
	 * Prevent SQL injection
	 * Input :
	 *	$str (string) : string to escape
	 *	$force (boolean) : escape even if magic_quotes are on
	 * Return :
	 *	$str escaped
	 */
	function escape($str,$force=false)
	{
		// If we need to escape the string
		if (get_magic_quotes_gpc() && !$force)
		{
			$str = stripslashes($str);
		}
		$str = mysql_real_escape_string($str);
		return $str;
	}
	
	/*
	 * Quotes a string for a query
	 * Input :
	 * 	$str (string) : string to quote
	 *	$force (bool) : take a look at $this->escape
	 * Returns :
	 *	$str quoted and escaped
	 */
	function quote($str,$force=false)
	{
		return "'".$this->escape($str,$force)."'";
	}
	/*
	 * Avoid javascript injection 
	 * Input :
	 * 	$str (string) : string to check
	 * Returns :
	 *	$str with javascript removed, quoted and escaped
	 */
	function noJavascript($str,$force=false)
	{
        //remove script balise
        $str = preg_replace('
                            /<[^<]*script[^\>]*>
                            .*?
                            <\/script>
                            /xmsi',"",$str);
        //remove javascript added inside tag with on* command and with " delimiter                    
        $str = preg_replace('/
                                <[^<]*
                                    on[a-z]*\s*=
                                    [^>]*>
                                    .*?
                                    <\s*\/[^>]*>
                            /xmsi',"",
                            $str);
        //remove javascript added inside simple tag with on* command and with " delimiter                       
        $str = preg_replace('/
                                <   [^<]*
                                    on[a-z]*\s*=
                                    [^>]*
                                >
                            /xmsi',"",
                            $str);                            
       return $this->quote($str);
    //
	//	return $this->quote(strip_tags($str,'<a><p><div><img><br><b><em><font><hr><u><strong><li><i>'),$force);
	}
	function noHTML($str,$force=false)
	{
		return $this->quote(strip_tags($str),$force);
	}
	function setUtf8()
	{
		$this->execute("SET NAMES 'utf-8' ");
		$this->execute("SET CHARACTER SET 'utf8' ");
	}
	
	/*
	 * Select
	 * Input :
	 *	$type (int) : FETCH_OBJECT or FETCH_ARRAY
	 *	args : sql select with parameters
	 * Returns :
	 *	(array of objects) if $type==FETCH_OBJECT 
	 *	(array of arrays) if $type==FETCH_ARRAY
	 */
	function select($type=FETCH_OBJECT)
	{
		if (func_num_args()>1)
		{
			$args = func_get_args();
			array_shift($args);
			$sql=array_shift($args);
			if (count($args)!=0) $sql=vsprintf($sql,$args);
		}
		else
		{
			$sql = $this->sql;
		}
		$this->getResults($sql);
		// Get the result
		$last_result = array();
		if ($type == FETCH_ARRAY)
		{
			while($r = @mysql_fetch_array($this->result))
			{
				$last_result[] = $r;
			}
		}
		else
		{
			while($r = @mysql_fetch_object($this->result))
			{
				$last_result[] = $r;
			}
		}
		
		return $last_result;
	}
	
	function get_row()
	{
		$r = $this->select();
		if (count($r)>=1)
			return $r[0];
		return array();
	}
}
?>