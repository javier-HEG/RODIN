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
# A widget for other web site
# inputs :
#	id : widget ID
#	container : frame ID containing widget file
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=0;
$pagename="portal/widgetforyoursite.php";
//includes
require_once('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');

$id=$_GET["id"];

$container=isset($_GET["container"])?$_GET["container"]:'portaneowidget';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>... Loading</title>
	<link rel="stylesheet" href="../styles/main.css?v=<?php echo __POSHVERSION;?>" type="text/css" />
	<link rel="stylesheet" href="../../app/exposh/styles/main1.css.php?v=<?php echo __POSHVERSION;?>&skin=<?php print $_SESSION['RODINSKIN'];?>" type="text/css" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="text/javascript" src="../l10n/<?php echo __LANG;?>/lang.js?v=<?php echo __POSHVERSION;?>" ></script>
	<script type="text/javascript" src="../includes/config.js?v=<?php echo __rand;?>" ></script>
	<script type="text/javascript" src="../tools/mootools.js?v=1.2.1"></script>
	<script type="text/javascript" src="../includes/ajax.js?v=<?php echo __POSHVERSION;?>" ></script>
    <script type="text/javascript" src="../includes/php/ajax-urls.js?v=2.1.0a1"></script>

<?php 
	launch_hook('userinterface_header',$pagename);
?>
</head>
<body>

<div id="widgetcontainer" width="100%"></div>
<div id="debug"></div>
<?php
	$DB->getResults($widgetforyoursite_getWidget,$DB->escape($id));
	if ($DB->nbResults()==0)
	{
		exit("Widget is not available !");
	}
	$row=$DB->fetch(0);
?>
<script type="text/javascript"><!--
function _IG_AdjustIFrameHeight(v_height)
{
	var l_height;
	if (v_height)
	{
		l_height=v_height;
	}
	else
	{
		if (document.all)
		{
			if (document.compatMode && document.compatMode != 'BackCompat')
			{
				l_height=document.documentElement.scrollHeight + 5;
			}
			else
			{
				l_height = document.body.scrollHeight + 5;
			}
		}
		else if (document.height)
		{
			l_height=document.height;
		}
	}
	if (parent.window.document.getElementById("<?php echo $container;?>"))
	{
		parent.document.getElementById('<?php echo $container;?>').style.height=l_height+'px';
	}
}
__useoverview=false;

$p.app.standalone($('widgetcontainer'),1,true);


// add the widget
tab[0].module[0]=new $p.app.widgets.object(
                                            1,      //col
                                            1,      //pos
                                            1,      //posj
                                            <?php echo $row["height"];?>,
                                            <?php echo $row["id"];?>,
                                            "--",   //link
                                            "--",       //name
                                            "<?php echo $row["defvar"];?>",
                                            280,    //minmodsize
                                            1,      //updmodsize
                                            280,    //size
                                            "<?php echo $row["url"];?>",
                                            0,      //x
                                            0,      //y
                                            1,      //uniq_db
                                            "<?php echo $row["format"];?>",
                                            <?php echo $row["nbvariables"];?>,
                                            1,      //tab
                                            0, //blocked
                                            0, //minimizzed
                                            0,      //userreader
                                            0,       //autorefresh
                                            '',     //icon
                                            false,  //is loaded status of the module (indef=not loaded, false=loading, true=loaded)
                                            '',  //header
                                            '',  //footer
                                            '',   //auth  for RSS authentified feeds
                                            "",  //views (home or canvas) canvas for full-screen (full-portal)
                                            "<?php echo $row["l10n"];?>"      //lang parameters for l10n widgets
                                            );
tab[0].module[0].create();
tab[0].module[0].show();

_IG_AdjustIFrameHeight();
</script>
<?php
	$DB->freeResults();
$DB->close();
?>
<div style="width: 100%;text-align: right;font-size: 8pt;">Powered by <a href="<?php echo __LOCALFOLDER;?>" target="_blank"><?php echo __APPNAME;?></a> &nbsp;</div>
</body>
</html>