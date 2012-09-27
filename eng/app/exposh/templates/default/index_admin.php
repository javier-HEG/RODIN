<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Loading ... </title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="language" content="<?php echo __LANG;?>" />
<link rel="stylesheet" href="../styles/main.css?v=<?php echo __POSHVERSION;?>" type="text/css" />
<link rel="stylesheet" href="../../app/exposh/styles/main1.css?v=<?php echo __POSHVERSION;?>" type="text/css" />
<link rel="stylesheet" href="../styles/admin.css?v=<?php echo __POSHVERSION;?>" type="text/css" />
<script type="text/javascript" src="../../app/exposh/l10n/<?php echo __LANG;?>/admin.lang.js?v=<?php echo __POSHVERSION;?>" ></script>
<script type="text/javascript" src="../includes/config.js?v=<?php echo __rand;?>"></script>
<script type="text/javascript" src="../tools/mootools.js?v=1.2.1"></script>
<script type="text/javascript" src="../includes/ajax.js?v=<?php echo __POSHVERSION;?>"></script>
<script type="text/javascript" src="../includes/php/ajax-urls.js?v=<?php echo __POSHVERSION;?>"></script>
<script type="text/javascript" src="../includes/php/admin-urls.js?v=<?php echo __POSHVERSION;?>"></script>
<script type="text/javascript" src="../includes/admin.js?v=<?php echo __POSHVERSION;?>"></script>
</head>
<body class="admin_body"> 
<div id="cache" style="position:absolute;left:0;top:0;z-index:8;display:none;"></div>
<div id='headlink' name='headlink'></div>
<div id='information'></div>
<div id="tabs" class="index admheader"></div>
<div id="headmenu"></div>
<div style="width: 100%;text-align: right;display: none;" id="plugindiv"></div>
<div id="admin_breadcrumbs" class="breadcrumbs"></div>
<!--<div id="suboptions"></div>-->
<div id="content"></div>
<br /><br />
<div id='extracontent'></div>

<div id="debug"></div>
<script type='text/javascript'>
allowSave=true;
window.onload=function()
{
	$p.app.user.init(<?php echo $_SESSION['user_id'];?>,"<?php echo $_SESSION['longname'];?>","<?php echo $_SESSION['type'];?>");
	$p.admin.init();
}
</script>
</body>
</html>