<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>



<title>RODINPOSH Login</title>
<link rel="stylesheet" type="text/css" href="../styles/main.css" />
<link rel="stylesheet" type="text/css" href="../../app/exposh/styles/main1.css" />
<script type="text/javascript" src="../../app/exposh/l10n/<?php echo __LANG;?>/lang.js?v=<?php echo __POSHVERSION;?>" ></script>
<script type="text/javascript" src="../includes/config.js?v=<?php echo __rand;?>" ></script>
<script type="text/javascript" src="../tools/mootools.js?v=1.2.1"></script>
<script type="text/javascript" src="../../app/exposh/includes/ajax<?php if (!__debugmode) echo '_compressed';?>.js?v=<?php echo __POSHVERSION;?>"></script>
<script type="text/javascript" src="../includes/php/ajax-urls.js?v=<?php echo __POSHVERSION;?>"></script>
<?php 
	launch_hook('userinterface_header',$pagename);
	
	$chaos_toggle_uniq=time() ;
	$chaos_toggle=fmod($chaos_toggle_uniq , 2);
	if ($chaos_toggle)
		$ALIGN_PIC = "left center";
	else
		$ALIGN_PIC = "right center";
	//from /posh/portal:
	include_once("../../app/root.php");
	include_once("../../app/u/FRIdbUtilities.php");
	
	// Destroy the session if no user is logged in
	// in order to prevent use of cookies mixed
	// between different segments
	if (!isset($_SESSION["username"])) {
		session_destroy();
	}
	
	// Check if param 'MAINT' is set and launch intallations checks
	if (param_named('MAINT',$_REQUEST)) {
		// print "<br>calling check_rodin_installation($PROT,$HOST,$PORT,$RODINROOT,$RODINSEGMENT)...";	
		session_destroy();
		check_rodin_installation($PROT,$HOST,$PORT,$RODINROOT,$RODINSEGMENT);
	}
		
		
	//print "COLOR_RODINSEGMENT:$COLOR_RODINSEGMENT ($RODINSEGMENT)";
	#$BG_IMAGE= "$RODINIMAGES/rodin_bg_{$RODINSEGMENT}.png";
	//print "<br>RODINSEGMENT=$RODINSEGMENT";
	$BG_IMAGE= get_RODINIMAGE($RODINSEGMENT);
	

?>
<style>

input.btn {
	font-size: 9pt;
	height: 20px;
	cursor: hand;
	cursor: pointer;
	border: 1px solid #003568;
    color: #fff;
    background: <?php echo $COLOR_RODINSEGMENT; ?>;
}

#noportal {
	background-image: url( <?php echo $BG_IMAGE; ?> );
	background-attachment:scroll;
	background-position: <?php echo $ALIGN_PIC; ?>;
	background-repeat:no-repeat;
}




#loginbox {
	border: 0px;
	margin: 0px;
	font-size: 0.7em;
	width:500px;
	background-color: #555;
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
</head>
<body>
<div id="cache" style="position:absolute;left:0;top:0;z-index:8;display:none;"></div>

<div class="noportal" id="noportal">

<div width="100%">
	<div align="center" style="padding-top:150px">
		<div class="raised">
			<b class="b1"></b><b class="b2"></b><b class="b3"></b><b class="b4"></b>
			<div class="boxcontent">
				<div id="loginbox">
                
					<div align="left" id="header" bgcolor="#ffffff">
						<div id="no-logo" style="float: left;">
							<a href="../portal/index.php">
								<img src="../images/logo_portal.gif" style="width: 150px;" />
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
							<form method="post" action="" onsubmit="return $p.app.connection.set(this,$p.app.pages.isPageExisting)">
							<label><b><?php echo (__accountType=="mail"?lg("email"):lg("login"));?></b></label><br />
							<input type="text" name="username" maxlength="64" style="width: 280px;" class="thinbox" /><br /><br />
							<label><b><?php echo lg("password");?></b></label><br />
							<?php launch_hook('display_login_form'); ?>
							<input type="password" name="password" maxlength="16" style="width: 280px;" class="thinbox" /><br /><br />
							<input type="checkbox" name="autoconn" /> <?php echo lg("automaticConnection");?><br /><br />
							<input type="submit" class="btn" value="<?php echo lg("connection");?>" />
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
</body>
</html>