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

/*! \brief 404.php
    document error page which is sent by the server if a page doesn't exist.

    You have the choice  to install it or not.

    If you want to install it.

    - add in your httpd file conf
    ErrorDocument 404 "/path/to/your/404.php"
    - reload it
*/

	$folder="";
	$not_access=0;
	$pagename=$_SERVER['SCRIPT_NAME'];
    $indexpage = eregi_replace("404",'index',$pagename);
	//includes
	require_once('includes/config.inc.php');
	require_once('includes/session.inc.php');
	require_once('includes/connection_'.__DBTYPE.'.inc.php');

    $error = '';
    $old_email = '';
    $old_text = '';
    if (isset($_POST["text"])) $old_text =  $_POST["text"];
    $old_subject = '';
    $old_subject = isset($_POST["subject"]) ? $_POST["subject"] : "missing page: " . $_SERVER['REQUEST_URI'];

    if ( isset($_POST["action"]) && $_POST["action"])
	{
        if (isset($_POST["email"]) && !empty($_POST["email"])) $old_email = $_POST["email"];
        //minimalistic control

        if (eregi("^[^\@]*\@[a-z\.\-\_\']*\.[a-z]{2,}$", $old_email)  ) $valid_email = 1;
		if ( !$old_email || !isset($valid_email))
        {
            $error =  '<p style="font-weight:bolder;background-color: #FFFFAB;color:red;margin:0 25% 0 25%;text-align:center;padding:2px 0 2px 0;">Please enter valid email</p>';
        }
        else
		{
            $old_email = $_POST["email"];

            //Connect to the database
			$DB=new connection(__SERVER,__LOGIN,__PASS,__DB);

			$contuserid=0;
			if (isset($_SESSION['user_id'])){	$contuserid=$_SESSION['user_id'];}

			$addtext="";
			if (isset($_POST["popularity"])) $addtext="(" . $_POST["popularity"] . ")";

            $insert = "INSERT INTO app_contact
                                (dest_id, user_id, name, email, titre,
                                    texte, statut, modifdate)
                                VALUES
                                (%u,%u,'internaute','%s','%s',
                                    '%s','I',NOW())";
            $dest_id = isset( $_POST["dest_id"] ) ?  $_POST["dest_id"] : 1;
            $DB->execute($insert,
                            $DB->quote($dest_id),
                            $DB->quote($contuserid),
                            $DB->escape($old_email),
                            $DB->escape($old_subject),
                            $DB->escape($old_text . $addtext)
                            );

			header("location:$indexpage");
		}
	}
    $old_email = htmlspecialchars($old_email);
    $old_text = htmlspecialchars($old_text);
    $old_subject = htmlspecialchars($old_subject);

    ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<HEAD>
	<TITLE>PORTANEO :: 404 file not found</TITLE>
	<LINK rel="stylesheet" href="styles/main.css" type="text/css" />
	<LINK rel="stylesheet" href="../app/exposh/styles/main1.css.php?skin=<?php print get_rodin_skin();?>" type="text/css" />
	</HEAD>
<body>
<div class="noportal">
<table cellpadding="0" cellspacing="0" border="0" style="width:100%">
	<tr>
	<td id="header">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
		<td id="logo"><img src="images/s.gif" width="140" height="60" /></td>
		</tr>
	</table>
	</td>
	</tr>
	<tr>
	<td align="center" valign="top">
	<table cellpadding="10" cellspacing="0" border="0" width="100%">
		<tr>
		<td style="padding-top:30px">
            <?php echo $error;?>
			<strong>
            The page you have requested is no longer available. </strong><BR /><BR />
			Please contact us:
			<FORM method="POST" action="<?php echo $pagename;?>">
            <INPUT type="hidden" name="popularity" value="none" />
            <INPUT type="hidden" name="dest_id" value="8" />
			<TABLE cellpadding=4 cellspacing=0 border=0 width="100%">
				<TR>
                    <TD>Your E-mail :</TD>
                    <TD><INPUT type="text" name="email" size=30 maxlength=40 value="<?php echo $old_email ?>" /></TD>
				</TR>
				<TR>
                    <TD>Subject :</TD>
                    <TD><INPUT type="text" name="subject" size=30 maxlength=40 value="<?php echo $old_subject ?>" /></TD>
				</TR>
				<TR>
                    <TD valign="top"><BR /><BR />Your message :</TD>
                    <TD><TEXTAREA name="text" cols=30 rows=7><?php echo $old_text;?></TEXTAREA></TD>
				</TR>
				<TR>
				<TD colspan="2"><INPUT type="submit" name="action" value="send" ><BR /></TD>
				</TR>
			</TABLE>
			</FORM>
		<A href="<?php echo $indexpage;?>">Return to homepage</A><BR />
		</TD>
		</TR>

	</TABLE>
	</TD>
	</TR>
</TABLE>
</div>

</BODY>
</HTML>
