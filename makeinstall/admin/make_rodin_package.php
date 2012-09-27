<?php
//print "<br>package: at  cwd=".getcwd()."<br>";

#REFERENCE DIR: /app/u
chdir("../../eng/app/u");

require_once("FRIutilities.php");
require_once("../../fsrc/app/sroot.php");

/*DB RODINPOSH:
	Dump every table 
	exclude data from:   log, users_mail_providers, 
	
	becareful data in: users, users_controls
	!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		
	STOPWORD table missing!!! -> in SRC_eng
	
*/

														;
	$verbose=param_named('VERBOSE',$_REQUEST);
														
	$action=$_REQUEST['action'];
	$makeinstalldir=$_REQUEST['serverpackagedir'];
	if (!$makeinstalldir) $makeinstalldir="$DOCROOT$RODINROOT";
	
	if ($serverpackagedir && !is_writable($serverpackagedir))
	{
		fontprint("Nothing done: <br> Parameter serverpackagedir=",'black');
		fontprint("<b>$serverpackagedir</b>",'red');
		fontprint(" should be an existing and writable Directory!<br>Please check this directory on your server and restart this script.",'black');
		exit;
	}
	
	$HEADER=   "<br>"."RODIN Packager - makeinstaller/package.php"  
						."<br>"."(constructing RODIN installation)"
						."<br>"."This program should be only used at HEG"
						."<br>"."This program will be installing on dir $makeinstalldir"
						."<br>"."Please wait - this will take some minutes ..."
						."<br>Autor: Fabio Ricci (fabio.fr.ricci@hesge.ch)"
						."<br>Creation date: 24.05.2011";
	
	if ($action<>'package')
		print $HEADER
				."<br>"
				."<br>USAGE: <a href='$thisScript?action=package&VERBOSE' title='Click to reexecute this script directly with the package action'>?action=package</a> &serverpackagedir='&LT;the path on the server where you want the tar to be written&GT;'"
				;

	
	if ($action=='package')
	{
		$makeinstalldirname='makeinstall';
		
		$dir= "$makeinstalldir/$makeinstalldirname"; // in user dir
		print "<br>DIR=$dir";
		$dirdb= "$dir/db";
		$dirtar= "$dir/tar";
		if ($verbose) fontprint(     $HEADER
														."<br>Please install rodin using /install/osx/install_rodin.php"
														,'blue');
														
		if (  (!is_writable($makeinstalldir))  )
		{
			fontprint("make_rodin_package: writable directory ($dir) not found - check permissions?<br>",'red');
			return false;
		}									
		else { #dir ok
															
			$res1 = dump_rodinposh_db($dirdb,'rodinposh.sql');
			$res2 = dump_rodin_db($dirdb,'rodin.sql');
			$res3 = dump_arc_db($dirdb,'arcstore.sql');
			$res4 = dump_src_db($dirdb,'src.sql');
			$res5 = dump_rodinadmin_db($dirdb,'admin.sql');
			if (!$res1) fontprint("<br>DATABASE rodinposh not dumped",'red');
			if (!$res2) fontprint("<br>DATABASE rodin not dumped",'red');
			if (!$res3) fontprint("<br>DATABASE ARC not dumped",'red');
			if (!$res4) fontprint("<br>DATABASE SRC not dumped",'red');
			if (!$res5) fontprint("<br>DATABASE ADMIN not dumped",'red');
			
			# tar everything into $dir
			if (is_writable($dirtar))
			{
				$rodinsourcedir="$DOCROOT$RODINROOT";
				//($tardir,$rodinsourcedir,$dbdir,$verbose=0)
				$res = tar_it($dirtar,$rodinsourcedir,$dirdb,true);
				print "<br>$res";
				
				// DELETE ./db/* if tar successful !
			} //tar all
			else fontprint("<br>Could not make tar ball, since dir $dirtar appear to be not writable for the user used by the web server",'red');
		} // dir ok
		#
		# ?? TOMCAT XSLT SERVICE ?
		# ?? JAVA XSLT SERVICE ? some XML based widgets won't run
		#
		
		
		if ($verbose) print "<br><hr><a href='file:$dirtar' target='_b' title='Click here to check RODIN package in directory'>Click here to check RODIN package</a> in $dirtar"
												."<br><br><br>";
	}


	
	
		

function dump_rodinposh_db($dir,$filename)
#
# Try to find $dirname at the same level of the current 
# installation, write the db dump to $dirname/$filename
#
{
	global 	$RODINPOSHDB_DBNAME,
					$RODINPOSHDB_HOST,
					$RODINPOSHDB_USERNAME,
					$RODINPOSHDB_PASSWD,
					$RODINSEGMENT;
						
	$tablesnodata = array('log','users_mail_providers');
	// becareful data in: users, users_controls
	
	 $filepath = "$dir/$filename";
	 
	dump_tables($filepath, $RODINPOSHDB_HOST,$RODINPOSHDB_USERNAME,$RODINPOSHDB_PASSWD,$RODINPOSHDB_DBNAME,'*',$tablesnodata,$columnsnodata, true);
	 return true;
} //dump_rodinposh_db




function dump_rodin_db($dir,$filename)
#
# Try to find $dirname at the same level of the current 
# installation, write the db dump to $dirname/$filename
#
{
	global 	$RODINDB_HOST,
					$RODINDB_DBNAME,
					$RODINDB_USERNAME,
					$RODINDB_PASSWD,
					$RODINSEGMENT;
	
	
	$tablesnodata = array('dblog_client',
												'search',
												'result',
												'R_CALL');
	// becareful data in: users, users_controls
	
	 $filepath = "$dir/$filename";
	 
	dump_tables($filepath, $RODINDB_HOST,$RODINDB_USERNAME,$RODINDB_PASSWD,$RODINDB_DBNAME,'*',$tablesnodata,$columnsnodata, true);
	 return true;
} //dump_rodin_db



function dump_arc_db($dir,$filename)
#
# Try to find $dirname at the same level of the current 
# installation, write the db dump to $dirname/$filename
# EVENTUALLY DELETE STORE zbwdbpedia in ARC database
# EVENTUALLY DELETE STORE dbpedia in ARC database
{
	global 	$ARCDB_DBNAME,
					$ARCDB_USERNAME,
					$ARCDB_USERPASS,
					$ARCDB_DBHOST,
					$RODINSEGMENT;
	

	$tablesnodata = array();
	// becareful data in: users, users_controls
	
	 $filepath = "$dir/$filename";
	 
	dump_tables($filepath, $ARCDB_DBHOST,$ARCDB_USERNAME,$ARCDB_USERPASS,$ARCDB_DBNAME,'*',$tablesnodata, $columnsnodata, true);
	 return true;
} //dump_arc_db



function dump_src_db($dir,$filename)
#
# Try to find $dirname at the same level of the current 
# installation, write the db dump to $dirname/$filename
#
{
	global 	$SRCDB_DBNAME,
					$SRCDB_USERNAME,
					$SRCDB_USERPASS,
					$SRCDB_DBHOST,
					$RODINSEGMENT;
		
	$tablesnodata = array();
	// becareful data in: users, users_controls
	
	$filepath = "$dir/$filename";
	 
	dump_tables($filepath, $SRCDB_DBHOST,$SRCDB_USERNAME,$SRCDB_USERPASS,$SRCDB_DBNAME,'*',$tablesnodata, $columnsnodata, true);
	 return true;
} //dump_src_db


function dump_rodinadmin_db($dir,$filename)
#
# Try to find $dirname at the same level of the current 
# installation, write the db dump to $dirname/$filename
#
{
	global	$RODINADMIN_HOST,
					$RODINADMIN_DBNAME,
					$RODINADMIN_USERNAME,
					$RODINADMIN_USERPASS,
					$RODINSEGMENT;
					
					$RODINADMIN_USERNAME.='-0';
					$RODINADMIN_USERPASS ='valelapena';
					$TMPDB='tmp';

	$tablesnodata = array();
	# becareful data in: users, users_controls
	$filepath = "$dir/$filename";

	#duplcate table to table tmp
	$ok = duplicate_db($RODINADMIN_HOST,$RODINADMIN_DBNAME,$RODINADMIN_USERNAME,$RODINADMIN_USERPASS,$TMPDB);
	
	#delete values with S=true
	
	$SQL=" UPDATE `$TMPDB`.`administration` SET value='please define for your site' WHERE S=1";
	if (!mysql_query($SQL)) {fontprint ("dump_rodinadmin_db: Problem updating S-records in $NEWDB using ($SQL)",'red'); exit();}
 
	dump_tables($filepath, $RODINADMIN_HOST,$RODINADMIN_USERNAME,$RODINADMIN_USERPASS,$TMPDB,'*',$tablesnodata, $columnsnodata, true);
	
	#delete $NEWDB
	$SQL=" DROP database `$TMPDB`";
	if (!mysql_query($SQL)) {fontprint ("dump_rodinadmin_db: Problem re-dropping $TMPDB using ($SQL)",'red'); exit();}
 
	 return true;
} //dump_src_db
















function tar_it($tardir,$rodinsourcedir,$dbdir,$verbose=0)
{		global $verbose;
		if ($verbose)
				print "<hr>Constructing <b>tar ball</b> into <b>$tardir</b> ...";
												
		$ignores = array('/gen/u/tabber'
			,'/eng/fsrc/app/engines'	 
			,'/make_rodin_package.php');

		foreach($ignores as $ignore)
			$EXCLUDES.=" --exclude='*{$ignore}*' ";
		
		// tar -cvzfp ./tar/rodin.gz ../gen/ ../eng/ ./db/ >
		 
		$TARCOMMAND="chmod 666 $tardir/rodin.gz; tar $EXCLUDES -cvzf $tardir/rodin.gz $rodinsourcedir/gen/ $rodinsourcedir/eng/ $rodinsourcedir/install/ $dbdir &> $tardir/rodintarlog.txt ";
		
		print "<br> $TARCOMMAND";
	  $res = system($TARCOMMAND);
		print "<br>$res";
		// DELETE ./db/* if tar successful !
		return $res;
}		





// Example: dump_tables('rodinposh_eng','username','password','blog');

// from http://davidwalsh.name/backup-mysql-database-php
/* backup the db OR just a table */
#
# $tablesnodata = array ()
# $columnnodata = array (columnname, colummnname, ...) whose values should NOT be exported
#
#
function dump_tables($filepath, $host,$user,$pass,$name,$tables,$tablesnodata,$columnnodata,$verbose=0)
{
	global $verbose;
	if($name=='') 
	{
		fontprint("<br><hr>dump_tables: Called with empty database name! Nothing dumped!!",'red');
		return false;
	}
	
	$place=getcwd()."/$filepath";
	$URLfilepath="<a href='file:$place' target='_blank'>$place</a>";
  if($verbose) print "<hr><b>Dumping Tables of $name to $URLfilepath ...</b>";
  
  $link = mysql_connect($host,$user,$pass);
  if (!mysql_select_db($name,$link)) {fontprint("Problem selecting $name",'red');};
  
  //get all of the tables
  if($tables == '*')
  {
    $tables = array();
    $result = mysql_query('SHOW TABLES');
    while($row = mysql_fetch_row($result))
    {
      $tables[] = $row[0];
    }
  }
  else
  {
    $tables = is_array($tables) ? $tables : explode(',',$tables);
  }
  
  $reccnt = -1;
  //cycle through
  foreach($tables as $table)
  {
    if($verbose) print "<br>Dumping Table $table ...";
  	
    $result = mysql_query('SELECT * FROM '.$table);
    $num_fields = mysql_num_fields($result);
    
    $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
    $table_def= "\n".$row2[1].";\n";
    $tablerecords='';
    if (in_array($table,$tablesnodata))
    {
      $return	.= #"\nDROP TABLE ".$table.";\n"
					   		$table_def
					      ."FRI:INFOTABLE $table: 0 RECORDS;\n\n"
					      ;
    	if ($verbose) fontprint(" (skip data for table $table)",'gray');
    }
    else
    {	$table_inforecords="FRI:INFOTABLE $table: 0 RECORDS;\n\n"; #In case there is no rec
	    for ($i = 0; $i < $num_fields; $i++) 
	    {
	    	$reccnt= 0;
	      while($row = mysql_fetch_row($result))
	      {
	      	$reccnt++;
	        $record= 'INSERT INTO '.$table.' VALUES(';
	        for($j=0; $j<$num_fields; $j++) 
	        {
	          $row[$j] = addslashes($row[$j]);
	          $row[$j] = ereg_replace("\n","\\n",$row[$j]);
	          
	          if (isset($row[$j])) { $record.= '"'.base64_encode($row[$j]).'"' ; } else { $record.= '""'; }
	          if ($j<($num_fields-1)) { $record.= ','; }
	        }
	        $record.= ");\n";
	       	$table_inforecords="FRI:INFOTABLE $table: $reccnt RECORDS;\n\n";
	        
	        $tablerecords.= $record;
	      }
	      if ($verbose && $reccnt) fontprint(" ($reccnt records)",'green');
//	      $return.= 'DROP TABLE '.$table.';'
//   								.$table_def
//	      					."FRI:INFOTABLE $table: $reccnt RECORDS;\n\n"
//	      					.$tablerecords
//	      					.="\n\n\n";
	    }
      $return	.= #"\nDROP TABLE ".$table.";\n"
	 				   		$table_def
					      .$table_inforecords
					      .$tablerecords
					      ;
   } // $tablenodata
    $return.= "\n\n\n";
  }
  
  //save file
  $handle = fopen($filepath,'w+');
  fwrite($handle,$return);
  fclose($handle);
}








function duplicate_db($HOST,$DB,$DBUSER,$DBPASS,$NEWDB)
{
	
 $ok=true;
	if (!mysql_connect($HOST,$DBUSER,$DBPASS)) {fontprint ("duplicate_db: Problem connecting to DMBS with user $HOST/$DBUSER/$DBPASS",'red'); exit();}
	if (!mysql_select_db($DB)) 	{fontprint ("duplicate_db: Problem switching to $DB",'red'); exit();}
 
	$result=mysql_query("show tables");
 
	$table_names=array();
	while($row=mysql_fetch_array($result)){
		$table_names[]=$row[0];
	}
 
	if (!mysql_query("create database $NEWDB")) 
	{  	mysql_query("DROP database $NEWDB");
			mysql_query("create database $NEWDB"); }
	if (!mysql_select_db($NEWDB)) 	{fontprint ("duplicate_db: Problem switching to $NEWDB",'red'); exit();}
 
 
	for($i=0;$i<count($table_names);$i++){
		$SQL="create table ".$table_names[$i]." select * from `$DB`.`".$table_names[$i]."`";
		if (!mysql_query($SQL)) 	{fontprint ("duplicate_db: Problem creating values into $NEWDB using $SQL",'red'); exit();}
	}
	
	return $ok;
} // duplicate_db





?>
