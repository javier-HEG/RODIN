<?php
$folder="";
$not_access=0;
$granted="I";
$pagename="portal/xmlcaptcha.php";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

$code_gen=$_REQUEST["code_gen"];
$code_ent=$_REQUEST["code_ent"];
$DB->getResults($authentication_get_code,$DB->quote($code_ent));
$row=$DB->fetch(0);
$code_key=$row["code"];

$file=new xmlFile();
$file->header();

echo "<key>$code_key</key>";
if($code_key==$code_gen){
	echo "<resulta>1</resulta>";
}else{
	echo "<resulta>0</resulta>";
}
$file->footer();

?>
