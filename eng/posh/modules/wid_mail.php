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
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

header("content-type: application/xml");
error_reporting(0); // error message make the xml unreadable - do not remove except for debug

$folder     = "";
$not_access = 1;
$pagename   = "modules/wid_mail.php";
$granted    = "I";

//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

$xmlfile = new xmlFile();

$xmlfile->header("channel");

$act = $_REQUEST["act"];

/**protocols where /notls is needed or not**/
/**  default port: IMAP **/
$mail_params = array(
                '/pop3'         => array (
                                        'port'  => '110',
                                        'tls'   => "/notls"
                                    ),
                '/imap'              => array ( 
                                        'port'  => '143',
                                        'tls'   => "/notls"                                    
                                   ),
                ''              => array ( 
                                        'port'  => '143',
                                        'tls'   => "/notls"                                    
                                   ),
                '/imap/ssl'   => array(
                                        'port'  => '993',
                                        'tls'   => ""                
                                        ),
                '/pop3/ssl/novalidate-cert'   => array(
                                        'port'  => '995',
                                        'tls'   => ""                
                                                      )
            );


if ($act == "check")
{
	$DB->getResults($widmail_checkProvider,
                                $DB->quote($_REQUEST["provider"])
                    );
        
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
if ($act == "conf")
{
    /* check if account exists or not before creates it , it would be better use unique index, but cannot be added without risk on old databases **/
    $DB->getResults($widmail_isExistAccount,
                      $DB->noHTML($_POST["user"]),
                      $DB->escape($_SESSION['user_id']),
                      $DB->quote($_POST["server"]),
                      $DB->quote(":".$_POST["port"]),
                      $DB->quote($_POST['pass']),
                      __KEY
                ); 
    if (  $DB->nbResults() == 0 ) 
    {
        $protocol = $_POST["protocol"];
        if (  empty($_POST["port"]) )
        { 
            $port = $mail_params[$protocol]['port']; 
        } else {
            $port = $_POST["port"];
        }
        $DB->execute($widmail_configure,
                        $DB->escape($_SESSION['user_id']),
                        $DB->quote($_POST["provider"]),
                        $DB->noHTML($_POST["webmail"]),
                        $DB->quote($_POST["server"]),
                        $DB->quote(":".$port),
                        $DB->quote($_POST["protocol"]),
                        $DB->noHTML($_POST["user"]),
                        $DB->quote($_POST["pass"]),
                        __KEY
                );
            
        echo "<id>".$DB->getId()."</id>"; 
    } else {
        $row = $DB->fetch(0);
        echo "<id>".$row['id']."</id>"; 
    }
	
}

if ($act == "get")
{
	$nbaccount=0;
	$DB->getResults($widmail_getMailInfo,
                            __KEY,
                            $DB->escape($_POST["id"]),
                            $DB->escape($_SESSION['user_id'])
                    ); 
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
            
            $protocol = $row["protocole"];
            $notls = $mail_params[$protocol]['tls']; 
			if ($mail_cnx = @imap_open ("{".$row["serveur"].$row["port"].$row["protocole"]."$notls}INBOX", $row["username"], $row["dpass"], OP_SILENT) )
			{
				$nbMail=@imap_num_msg($mail_cnx);
				$nbunread=@imap_num_recent($mail_cnx);

				echo "<number>".$nbMail."</number>";
				echo "<unread>".$nbunread."</unread>";
				echo "<webmail><![CDATA[".$row["webmail"]."]]></webmail>";

				$nbToDisplay = (isset($_POST["nb"]) ? $_POST["nb"] : 5) + 1;
                $start = (isset($_POST["start"]) ?$_POST["start"] : 0);
                $lastToGet = $nbMail - $start;
				$firstToGet = $lastToGet > $nbToDisplay ? $lastToGet - $nbToDisplay : 0;

				for ($i = $lastToGet; $i != $firstToGet; $i--)
				{
					if ($entetes = @imap_header($mail_cnx, $i))
					{
						echo "<mail>";
						echo "<id>".$i."</id>";
						echo "<subject><![CDATA[".imap_utf8($entetes->subject)."]]></subject>";
						echo "<sender><![CDATA[".$entetes->fromaddress."]]></sender>";
						echo "<status><![CDATA[".$entetes->Recent."]]></status>";
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
	$DB->getResults($widmail_getMailInfo,
                        __KEY,
                        $DB->escape($_POST["id"]),
                        $DB->escape($_SESSION['user_id'])
                    );
	if ($DB->nbResults()==0)
	{
		echo "<number>-1</number>";
	}
	else
	{
		$row = $DB->fetch(0);
        $protocol = $row["protocole"];
        $notls = $mail_params[$protocol]['tls']; 
		if ($mail_cnx = @imap_open ("{".$row["serveur"].$row["port"].$row["protocole"]."$notls}INBOX", $row["username"], $row["dpass"], OP_SILENT) )
		{
			$message=@imap_fetchbody($mail_cnx, $_POST["messid"],"1");
			if ($message)
			{
				if ($message=="")
				{
					$message=@imap_fetchbody($mail_cnx, $_POST["messid"],"2");
                }
                echo "<message><![CDATA[".imap_utf8($message)."]]></message>";
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

$xmlfile->footer();
?>