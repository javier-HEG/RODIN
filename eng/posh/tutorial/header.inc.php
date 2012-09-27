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
# Tutorial header
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

//includes
include_once('../../app/exposh/l10n/'.__LANG.'/tutorial.lang.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>PORTANEO :: Tutorial</title>
<link rel="stylesheet" href="<?php echo __LOCALFOLDER;?>styles/main.css" type="text/css" />
<link rel="stylesheet" href="<?php echo __LOCALFOLDER;?>../app/exposh/styles/main1.css.php" type="text/css" />
<style type="text/css">
		.dotted{padding:8px 8px 2px 4px;margin:0px;vertical-align: top;}
		.dotted div#ico{float:left;width:45px;}
		.dotted div#label{float:left;margin-left:50px;}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!--<script type="text/javascript" src="../l10n/<?php echo __LANG;?>/lang.js?v=<?php echo __POSHVERSION;?>"></script>-->
<script type="text/javascript" src="<?php echo __LOCALFOLDER;?>l10n/<?php echo __LANG;?>/tutorial.lang.js?v=<?php echo __POSHVERSION;?>"></script>
<script type="text/javascript" src="<?php echo __LOCALFOLDER;?>includes/config.js?v=<?php echo __rand;?>"></script>
<script type="text/javascript" src="<?php echo __LOCALFOLDER;?>tools/mootools.js?v=1.2.1"></script>
<script type="text/javascript" src="<?php echo __LOCALFOLDER;?>includes/php/ajax-urls.js?v=<?php echo __POSHVERSION;?>"></script>
<script type="text/javascript" src="<?php echo __LOCALFOLDER;?>includes/ajax.js?v=<?php echo __POSHVERSION;?>"></script>
<script type="text/javascript" src="<?php echo __LOCALFOLDER;?>includes/tutorial.js?v=<?php echo __POSHVERSION;?>"></script>
<script type="text/javascript" src="<?php echo __LOCALFOLDER;?>includes/php/tutorial-url.js?v=<?php echo __POSHVERSION;?>"></script>
<script type='text/javascript' src='<?php echo __LOCALFOLDER;?>tools/fckeditor/fckeditor.js?v=<?php echo __POSHVERSION;?>'></script>

<script type="text/javascript">
$p.app.user.init(<?php echo $_SESSION['user_id'];?>,"<?php echo $_SESSION['longname'];?>","<?php echo $_SESSION['type'];?>");
</script>
<?php
	launch_hook('userinterface_header',$pagename);
?>
</head>
<body style="background: #fff;">
<div id="cache" style="position:absolute;left:0;top:0;z-index:8;display:none;"></div>
<!-- Header -->
<div id="breadcrumbs"></div>
<div id="pagetitle"></div>
<div id="headmenu"></div>
<div id="box"></div>
<div id="messages"></div>
<div id="divxml" style="display:none;"></div>
<div id="mymodules"></div>
<div id="myarticles"></div>
<div class="noportal">

