<?php
require_once('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Portaneo - Module Firefox</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="language" content="fr" />
<link rel="stylesheet" type="text/css" href="../styles/main.css" />
<link rel="stylesheet" type="text/css" href="../../app/exposh/styles/main1.css.php?skin=<?php print get_rodin_skin();?>" />

<script type="text/javascript" src="../portal/selections/waiting.js"></script>
<script type="text/javascript" src="../l10n/fr/lang.js?v=2.1.0RC"></script>
<script type="text/javascript" src="../includes/config.js?v=1238172785"></script>
<script type="text/javascript" src="../tools/mootools.js?v=1.2.1"></script>
<script type="text/javascript" src="../includes/ajax.js?v=2.1.0b"></script>
<script type="text/javascript" src="../includes/php/ajax-urls.js?v=2.1.0b"></script>

<script type='text/javascript' src='../l10n/fr/enterprise.js?v=1.0.0' ></script>
<script type='text/javascript' src='../includes/application.js?v=1.0.0' ></script>
<script type='text/javascript' src='../includes/proxy.js?v=1.0.0'></script>
<script type='text/javascript' src='../includes/php/application-urls.js?v=1.0.0'></script>

<link rel='stylesheet' type='text/css' href='../styles/enterprise.css?v=1.0.0' />

<script type='text/javascript' src='../tools/fckeditor/fckeditor.js'></script>
</head>
<style>
#loginbox {
	border: 0px;
	margin: 0px;
	font-size: 0.7em;
	width:500px;
	background-color: #fff;
	padding-bottom: 10px;
}
body {
	background: #efefef;
}
/* CSS taken from http://www.webreference.com/programming/css_borders/2.html */
.raised {
	width:506px;
}
.raised .b2 {
	background:#fff;
}
.raised .b3 {
	background:#fff;
}
.raised .b4 {
	background:#fff;
}
.raised .b4b {
	background:#fff;
}
.raised .b3b {
	background:#fff;
}
.raised .b2b {
	background:#fff;
}
.raised .b1 {
	background:#efefef;
}
.raised .boxcontent {
	background:#fff;
	width:504px;

}
</style>
<?php

$folder="";
$not_access=0;
$isScript=false;
$isPortal=false;
$message = '';
$pagename="portal/moduleff.php";
//includes

?>
</head>
<body onUnload="$p.app.counter.stop();">
	<?php

		if ( empty($_COOKIE["autoi"]) )
		{
	?>
	<div id="cache" style="position:absolute;left:0;top:0;z-index:8;display:none;"></div>
		<div class="noportal">
		<div width="100%">
			<div align="center" style="padding-top:150px">
				<div class="raised">
					<b class="b1"></b><b class="b2"></b><b class="b3"></b><b class="b4"></b>
					<div class="boxcontent">
						<div id="loginbox">

							<div align="left" id="header" bgcolor="#ffffff">
								<div id="logo" style="float: left;">
									<a href="../portal/index.php">
									<img src="../images/s.gif" width="140" height="60" />
									</a>
								</div>
							</div>
		                    <div id="loginscreen" style="padding-top: 10px">

								<div style="float: left;width: 150px;">
									<h2><?php echo lg("connection");?> ></h2>
		                            <div id="msg_conn">&nbsp;</div>
		                            <div id="passhlp">&nbsp;</div>
		                        </div>

								<div id="displayPart">
		<?php
			if ($message!='') echo '<font color="#ff0000"><script type="text/javascript">document.write(lg("'.$message.'"));</script></font><br /><br />';
		?>
									<form method="post" action="" onsubmit="return $p.app.connection.set(this,$p.app.connection.module_ff)">
									<label><b><?php echo (__accountType=="mail"?lg("email"):lg("login"));?></b></label><br />
									<input type="text" name="username" maxlength="64" style="width: 280px;" class="thinbox" /><br /><br />
									<label><b><?php echo lg("password");?></b></label><br />
									<?php launch_hook('display_login_form'); ?>
									<input type="password" name="password" maxlength="16" style="width: 280px;" class="thinbox" /><br /><br />
									<input type="checkbox" name="autoconn" /> <?php echo lg("automaticConnection");?><br /><br />
									<input type="submit" name="submit" class="btn" value="<?php echo lg("connection");?>" />
									</form>
								</div>
		                    </div>
						</div>
					</div>
					<b class="b4b"></b><b class="b3b"></b><b class="b2b"></b><b class="b1b"></b>
				</div>
			</div>
		</div>

		</div>
		<div style="display:none"><disconnected>1</disconnected></div>
		<div id="debug"></div>
		<script type="text/javascript">
		$p.print("passhlp",$p.app.connection.link2MissingPassword());
		</script>
	<?php
	} else {
		if( isset($_POST['a']) ) {
			$_SESSION["title"] = isset($_POST['title'])?$_POST['title']:"";
			$_SESSION["desc"] = isset($_POST['desc'])?$_POST['desc']:"";
			$_SESSION["url"] = isset($_POST['url'])?$_POST['url']:"";
		}
	?>

	<a id="link_logout" onclick="$p.app.logout('mdff');" class="" title="D&amp;eacute;connexion" href="#" name="4">Déconnexion</a>

	<script type="text/javascript"><!--

		document.write(""+$p.friends.menu(5));
		//wip_message="<center><br />loading...<br/><img src='../images/ajax-loader.gif' align='absmiddle' /><br /><a href='#' onclick='$p.app.resetAndReload()'>"+lg("appLoadingIssue")+"</a></center>";
		window.onload=function()
		{
			//load Posh objects
			$p.app.user.init(3,"user1","I","o");
			document.getElementById("p_title").value = "<?php print(addslashes(isset($_SESSION["title"])?$_SESSION["title"]:"")); ?>";
			<?php
				$data = str_replace(Chr(13), "", addslashes(isset($_SESSION["desc"])?$_SESSION["desc"]:""));
				$data = str_replace(Chr(10), "\\n", $data);
			?>
			temp = "<?php print($data); ?>";
			document.getElementById("p_desc").value = temp;
			//noinclusion(); prevent from page inclusion
			$p.app.pageMode();
		}
	// -->
	</script>
	<?php
	}
	?>
</body>
</html>