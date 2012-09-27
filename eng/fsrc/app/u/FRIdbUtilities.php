<?php

$filenamex="app/sroot.php";
#######################################
$max=10;
//print "<br>FRIutilities: try to require $filenamex at cwd=".getcwd()."<br>";
for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
{ 
	//print "<br>try to require $updir$filenamex";
	if (file_exists("$updir$filenamex")) 
	{
		//print "<br>REQUIRE $updir$filenamex";
		require_once("$updir$filenamex"); break;
	}
}

$FONTRED = "<font style=\"color:red;\">";

class SRC_DB {
	private $DB_HOST;
	private $DB_UNAME;
	private $DB_PWORD;
	private $DB_DB;
	//private $DBBASEDIR="H:/MySQL/Data/$DB_DB";

	public $DBconn;

	public function __construct($DB='src')
	{	
		global $ARCDB_DBNAME,	$ARCDB_USERNAME, $ARCDB_USERPASS, $ARCDB_DBHOST;	
		global $SRCDB_DBNAME,	$SRCDB_USERNAME, $SRCDB_USERPASS, $SRCDB_DBHOST;	
		global $RODINSEGMENT;

		if ($DB=='src')
		{
			$this->DB_DB 		= $SRCDB_DBNAME;
			$this->DB_HOST 	= $SRCDB_DBHOST;
			$this->DB_UNAME =	$SRCDB_USERNAME;
			$this->DB_PWORD =	$SRCDB_USERPASS;
		}
		
		$errors="";
		try {
			//print "<br> trying to connect($DB): {$this->DB_DB} {$this->DB_HOST},{$this->DB_UNAME},{$this->DB_PWORD}";
		
			$DBconn = mysql_connect($this->DB_HOST,$this->DB_UNAME,$this->DB_PWORD) or $errors = $errors . "Could not connect to database.\n";
			@mysql_select_db($this->DB_DB) or $errors = $errors . "Unable to select database $DB_DB\n";
			$this->DBconn=$DBconn;
		}
		catch (Exception $e)
		{
			inform_bad_db($e);
		}
	}

	//'mysqli://benutzerasswort@server/datenbank'
	public function close()
	{
		mysql_close($this->DBconn);
	}
} // DB

?>
