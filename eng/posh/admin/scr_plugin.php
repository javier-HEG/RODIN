<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<title>Loading plugin</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php
require('includes.php');
require_once('../includes/file.inc.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
//display headers 
//and form ?

$plugname = (isset($_GET["plugname"])) ? $_GET['plugname'] : '';
if (!$plugname) {
    $plugname = (isset($_POST["plugname"])) ? $_POST['plugname'] : '';
}

?>
<meta name="language" content="<?php echo __LANG;?>" />
<link rel="stylesheet" href="../styles/main.css?v=<?php echo __POSHVERSION;?>" type="text/css" />
<link rel="stylesheet" href="../../app/exposh/styles/main1.css?v=<?php echo __POSHVERSION;?>" type="text/css" />

<link rel="stylesheet" href="../styles/admin.css?v=<?php echo __POSHVERSION;?>" type="text/css" />

<script type="text/javascript" src="../tools/mootools.js?v=1.2.1"></script>
<script type="text/javascript" src="../includes/ajax.js?v=<?php echo __POSHVERSION;?>"></script>
<script type="text/javascript" src="../includes/php/ajax-urls.js?v=<?php echo __POSHVERSION;?>"></script>
<script type="text/javascript" src="../includes/php/admin-urls.js?v=<?php echo __POSHVERSION;?>"></script>
<script type="text/javascript" src="../includes/admin.js?v=<?php echo __POSHVERSION;?>"></script>
</head>
<body> 
<div class="plugin">
<form method="POST" action="../admin/scr_plugin.php">
<input type="hidden" name="plugname" value="<?php echo $plugname; ?>">
<?php
    //insert form to manage plugin
    launch_hook("iframe_plugin",$plugname);
?>
    <input type="submit" name="enregistrer" value="<?php echo lg("submit"); ?>">
</form>
</div>
<?php
    if( isset($_POST["enregistrer"] ) ) {
        ?>
        <script type='text/javascript'>
            parent.$p.admin.config.setPlugins();
        </script>
        <?php
        
    }
?>
</body>
</html>
<?php
//require_once("../includes/plugins/plugin_ldap/plugin_ldap_config.php");

?>