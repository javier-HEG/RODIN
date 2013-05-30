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
 
//Include RodinRDFResult - obsolete
$filename="u/RodinResult/RodinRDFResult.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}
 
//Include RDFprocessor
$filename="u/RDFprocessor.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}

$filename="u/FRIdbUtilities.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}

$uid=$_GET['token'];
$seeonly=$_GET['seeonly'];
$PAGETITLE="dbRODIN LoD import browser ($RODINSEGMENT)";
$PAGETITLE_BIG=strtoupper($PAGETITLE)." FOR ENTITY $uid";
?>

<html>
	<head>
		<title><?php print $PAGETITLE_BIG?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="../../../css/rodin.css.php" type="text/css" />
		<script type="text/javascript" src="../../../u/RODINutilities.js.php"></script>
	</head>
	<body class='triplepage_rodin_e'>
<?php

	
//$uid=str_replace("/","\\/",$uid);

if ($uid)
	display_interactive_lod_entity_triples($uid,$PAGETITLE,'rodin_e',$seeonly);



?>
</body></html>