<?php

//print " show ".$_SERVER['QUERY_STRING'];

$filename="app/root.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}

//Include ARC2 LOCAL STORE INFOS
$filename="u/arcUtilities.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}
 
//Include RodinRDFResult
$filename="u/RodinResult/RodinRDFResult.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}
 
$filename="u/FRIdbUtilities.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}

$uid=$_GET['token'];
$PAGETITLE="RODIN w3s triples page";
$PAGETITLE_BIG=strtoupper($PAGETITLE)." FOR ENTITY $uid";
?>

<html>
	<head>
		<title><?php print $PAGETITLE_BIG?></title>
		<link rel="stylesheet" href="../../../css/rodin.css.php" type="text/css" />
		<script type="text/javascript" src="../../../u/RODINutilities.js.php"></script>
	</head>
	<body class='triplepage_rodin_a'>
<?php

	


if ($uid)
	print_triplespage($uid,$PAGETITLE,'rodin_a');



?>
</body></html>