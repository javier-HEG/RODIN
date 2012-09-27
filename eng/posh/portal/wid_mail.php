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
# Notes modules PHP scripts
# $Id: wid_mail.php,v 1.1.1.1 2010/05/23 08:50:40 fabio Exp $
# $Revision: 1.1.1.1 $
# $Author: fabio $
# $Date: 2010/05/23 08:50:40 $
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

header("content-type: application/xml");
$folder="";
$not_access=1;
$pagename="portal/wid_mail.php";
$granted="I";
//includes
require_once('includes.php');

echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'><channel>';

$act=$_GET["act"];

if ($act=="check")
{
	$DB->getResults($widmail_checkProvider,$DB->quote($_GET["provider"]));
	if ($DB->nbResults()!=0)
	{
		$row=$DB->fetch(0);
		echo "<provider>".$row["provider"]."</provider>";
		echo "<webmail>".$row["webmail"]."</webmail>";
		echo "<server>".$row["serveur"]."</server>";
		echo "<port>".$row["port"]."</port>";
		echo "<protocol>".$row["protocole"]."</protocol>";
	}
	$DB->freeResults();
}
if ($act=="conf")
{
	$DB->execute($widmail_configure,$DB->escape($_SESSION['user_id']),$DB->quote($_POST["provider"]),$DB->noHTML($_POST["webmail"]),$DB->quote($_POST["server"]),$DB->quote(":".$_POST["port"]),$DB->quote($_POST["protocol"]),$DB->noHTML($_POST["user"]),$DB->quote($_POST["pass"]));
	echo "<id>".$DB->getId()."</id>";
}

if ($act=="get")
{
	$nbaccount=0;
	$DB->getResults($widmail_getMailInfo,$DB->escape($_POST["id"]),$DB->escape($_SESSION['user_id']));
	if ($DB->nbResults()==0)
	{
		echo "<number>-1</number>";
	}
	else
	{
		$row = $DB->fetch(0);
		$nbaccount++;
		//check if imap is configured
		if (!function_exists('imap_open'))
		{
			echo "<number>-1</number><debug><![CDATA[Imap is not configured on your server]]></debug>";
		}
		else
		{
			if ($mail_cnx = @imap_open ("{".$row["serveur"].$row["port"].$row["protocole"]."}INBOX", $row["username"], $row["pass"], OP_SILENT) )
			{
				$nbmail=@imap_num_msg($mail_cnx);
				$nbunread=@imap_num_recent($mail_cnx);
				echo "<number>".$nbmail."</number>";
				echo "<unread>".$nbunread."</unread>";
				echo "<webmail><![CDATA[".$row["webmail"]."]]></webmail>";
				$maxNbMail=isset($_POST["nb"])?$_POST["nb"]:5;
				$minid=$nbmail>$maxNbMail?$nbmail-$maxNbMail:0;
				for ($i = $nbmail; $i != $minid; $i--)
				{
					if ($entetes = @imap_header($mail_cnx, $i))
					{
						echo "<mail>";
						echo "<id>".$i."</id>";
						echo "<subject><![CDATA[".$entetes->subject."]]></subject>";
						echo "<sender><![CDATA[".$entetes->fromaddress."]]></sender>";
						echo "<status>".$entetes->Recent."</status>";
						echo "</mail>";
					}
				}
			}
			else
			{
				echo "<error>(".print_r(imap_errors()).")</error><number>-1</number>";
				echo "<debug><![CDATA[Can not initialize connection with server=".$row["serveur"].",port=".$row["port"].",protocol=".$row["protocole"].",username=".$row["username"]." in wid_mail.php. Errors :".print_r(imap_errors())."]]></debug>";
			}
		}
	}
	if ($mail_cnx) @imap_close($mail_cnx);

	$DB->freeResults();
}
// read an email
if ($act=="read")
{
	$DB->getResults($widmail_getMailInfo,$DB->escape($_POST["id"]),$DB->escape($_SESSION['user_id']));
	if ($DB->nbResults()==0)
	{
		echo "<number>-1</number>";
	}
	else
	{
		$row = $DB->fetch(0);
		if ($mail_cnx = @imap_open ("{".$row["serveur"].$row["port"].$row["protocole"]."}INBOX", $row["username"], $row["pass"], OP_SILENT) )
		{
			$message=@imap_fetchbody($mail_cnx, $_POST["messid"],"1");
			if ($message)
			{
				if ($message=="")
				{
					$message=@imap_fetchbody($mail_cnx, $_POST["messid"],"2");
					echo "<message><![CDATA[".$message."]]></message>";
				}
				else
				{
					echo "<message><![CDATA[".$message."]]></message>";
				}
			}
			else echo "<error>Can not read this email (".print_r(imap_errors()).")</error><number>-1</number>";
		}
		else
		{
			echo "<error>Can not open this email (".print_r(imap_errors()).")</error><number>-1</number>";
		}
	}
	if ($mail_cnx) @imap_close($mail_cnx);

	$DB->freeResults();
}
$DB->close();

echo '</channel>';
?>
