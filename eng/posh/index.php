<?php 
if ( !file_exists('includes/config.inc.php') )  {
    header("location:install/");
    exit;
}
header("location:portal/index.php");

?>

