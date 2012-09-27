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
# Missing password form
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************


$folder="";
$not_access=0;
$isScript=false;
$isPortal=false;
$pagename="portal/password_missing.php";
//includes
require_once('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
require_once('../includes/mail.inc.php');
launch_hook('password_missing_php');

$error = '';
$message='';

//display form to change password
if (isset($_POST['update'])) {
    $mdp = $_POST['md5pass'];

   $password = $_POST['newpassword'];
   $username = $_POST['username'];
   $passwd_length =  strlen($password);
   if ($mdp && ($passwd_length> 6) && ($password == $_POST['newpassword2']) ) {


        $chk = $DB->execute($scrchangepwd_changePass_md5,
                                $DB->quote($password),
                                $DB->quote($mdp),
                                $DB->quote($DB->escape($username))
                            );

        if ($chk != 0) {
            $message = '<p class="warningok" style="margin-right:50%;margin-left:10%;">'. lg('passwordModified').'</p>';
        }
   } else {
        if (!$mdp) $error = "MD5 is missing! Are you really sure of what you want  do";
        if ( $password != $_POST['newpassword2']) $error = lg('msgSubPassDiff');
        if ($passwd_length <= 6) $error = lg('msgSubPassToShort');
        $message = '<p class="warning" style="margin-right:50%;margin-left:10%;">'.$error.'</p>';
   }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>PORTANEO :: Missing password</title>
	<link rel="stylesheet" href="../styles/main.css" type="text/css" />
	<link rel="stylesheet" href="../../app/exposh/styles/main1.css.php?skin=<?php print get_rodin_skin();?>" type="text/css" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script type="text/javascript" src="../l10n/<?php echo __LANG;?>/lang.js?v=<?php echo __POSHVERSION;?>"></script>
	<script type="text/javascript" src="../includes/config.js?v=<?php echo __rand;?>"></script>
	<script type="text/javascript" src="../tools/mootools.js?v=1.2.1"></script>
    <script type="text/javascript" src="../includes/php/ajax-urls.js?v=<?php echo __POSHVERSION;?>"></script>
	<script type="text/javascript" src="../includes/ajax.js?v=<?php echo __POSHVERSION;?>"></script>
<?php
	launch_hook('userinterface_header',$pagename);
?>
</head>
<body>
<div class="noportal">
<table cellpadding="0" cellspacing="0" border="0" style="width:100%">
	<tr>
	<td id="header">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
		<td id="logo"><img src="../images/s.gif" width="140" height="60" /></td>
		</tr>
	</table>
	</td>
	</tr>
	<tr>
	<td align="center" valign="top">
    <?php echo $message; ?>
	<table cellpadding="10" cellspacing="0" border="0" width="100%">
		<tr>
		<td align="left" valign="top" style="padding-top:30px">
<?php
if (isset($_GET['mdp']) && isset($_GET['email']))
{
	$mdp = $_GET['mdp'];
	$username = $_GET['email'];
?>
			<form name="help" method="post" action="password_missing.php">
			<input type="hidden" name="md5pass" value="<?php echo $mdp; ?>">
            <input type="hidden" name="username" value="<?php echo $username; ?>">
			<br/>
			<table>
				<tr>
				<td><label><?php echo lg('lblNewPassword'); ?></label>: </td><td><input type="password" name="newpassword" /></td>
				</tr>
				<tr>
				<td><label><?php echo lg('confirmPassword'); ?></label>: </td><td><input type="password" name="newpassword2" /></td>
				</tr>
			</table>
			<br />
            <input type="submit" class="btn" name="update" value="<?php  echo lg('lblBtnSend');?>" />
<?php
}
else
{
?>
			<form name="help" method="post" action="scr_sendmd5.php">
			<label><?php echo lg('yourEmail'); ?></label>: <input type="text" name="username" />
			<input type="hidden" name="redirect" value="password_missing" />
			<input type="submit" class="btn" name="update" value="<?php  echo lg('lblBtnSend');?>" />
<?php
}
?>
			</form>
			<br />

		</td>
		</tr>
		<tr>
		<td style="padding:8px">
		<br />
		</td>
		</tr>
	</table>
	</td>
	</tr>
</table>
</div>

</body>
</html>