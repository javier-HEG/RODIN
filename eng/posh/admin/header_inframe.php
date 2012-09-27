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
# POSH - Admin frames header
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

//includes
require_once('../../app/exposh/l10n/'.__LANG.'/admin.lang.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>PORTANEO :: Administration</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="../styles/main.css?v=<?php echo __POSHVERSION;?>" type="text/css" />
<link rel="stylesheet" href="../styles/admin.css?v=<?php echo __POSHVERSION;?>" type="text/css" />
<script type="text/javascript" src="../l10n/<?php echo __LANG;?>/admin.lang.js?v=<?php echo __POSHVERSION;?>"></script>
<script type="text/javascript" src="../includes/config.js?v=<?php echo __rand;?>"></script>
<script type="text/javascript" src="../tools/mootools.js?v=1.2.1"></script>
<script type="text/javascript" src="../includes/ajax.js?v=<?php echo __POSHVERSION;?>"></script>
<script type="text/javascript" src="../includes/php/ajax-urls.js?v=<?php echo __POSHVERSION;?>"></script>
<script type="text/javascript" src="../includes/admin.js?v=<?php echo __POSHVERSION;?>"></script>
<script type="text/javascript" src="../includes/php/ajax-urls.js"></script>
<?php
	launch_hook('admin_frame_header');
?>
</head>
<body>
<div style="font-size: 0.7em">