<html><head><title>Delete docs</title></head><body>

<?php
//require('../../gen/u/solarium/library/solarium/init.php');
$filenamex="app/root.php";
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

include_once("solr_interface.php");

$collection=$_GET['collection'];
if ($collection=='')
print "Please give a collection name...";
else
{
	solr_delete_documents($collection,$DELETEQUALIFICATION='*') ;
	
	print "Data in collection '$collection' erased";
}


?>
</body></html>