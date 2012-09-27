<?php
/*
Delay possible values :  
H (hour) 
J = D (jour/ day)
W = S (week/semaine)
M (month/mois)
                                      
Key:
Application key
         
Examples:
notebook_notif.php?key=43Gf2ft&delay=3m     Send the new comments for the last three months
notebook_notif.php?key=43Gf2ft&delay=1j       Send the new comments for the last day (24hours)
notebook_notif.php?key=43Gf2ft&delay=2s      Send the new comments for the last two weeks 
*/

//if many feeds, loading can be long
set_time_limit(1000); 
//notebook process
require_once('includes/config.inc.php');
require_once('includes/mail.inc.php');
require_once('includes/connection_'.__DBTYPE.'.inc.php');
require('db_layer/'.__DBTYPE.'/admin.php');

//database connection
global $DB,$DB2;
if (empty($DB))
$DB = new connection(__SERVER,__LOGIN,__PASS,__DB);				
if (empty($DB2))
$DB2 = new connection(__SERVER,__LOGIN,__PASS,__DB);

//now
$today=gmdate('Y-m-d H:i:s');
$time="";				
		
//key control to protect the file access
if(isset($_GET['key']) && $_GET['key']==__KEY)
	{
		if (isset($_GET['delay']) && $_GET['delay']!='' && strlen($_GET['delay'])<4 && strlen($_GET['delay'])>1)
		{
			$delay = stripslashes($_GET['delay']);
			//Defines if the delay is like H - J - D - W - S - M
			$type = strtoLower(substr($delay,-1));
			//retrieves the number
			$number = substr($delay,0,strlen($delay)-1);
			
			//make the equivalence between french and english (Day=Jour...)
			if ($type=='d') $type='j';
			if ($type=='w') $type='s';				
						
			//calculate the date from the delay
			switch ($type)
			{
			    case 'h' :
					$time = mktime(date("H")-$number-2, date("i"), date("s"), date("m"), date("d"), date("Y"));
					break;
			    case 'j' :
					$time = mktime(date("H")-2, date("i"), date("s"), date("m"), date("d")-$number, date("Y"));
					break;
			    case 's' :
					$time = mktime(date("H")-2, date("i"), date("s"), date("m"), date("d")-($number*7), date("Y"));
					break;
			    case 'm' :
					$time = mktime(date("H")-2, date("i"), date("s"), date("m")-$number, date("d"), date("Y"));
					break;
				//delay specified is in a wrong format : error message
				default : 
					echo "<font color='red'>Error delay format</font><br />";
					$DB->close();
					exit(1);
					break;
			} 
			//time with delay applied
			$time = date("Y-m-d H:i:s", $time);
			$req = "SELECT notebook_article.id AS IDARTICLE, notebook_article.title AS TITLEARTICLE, long_name, users.id as USERID, lang, username, COUNT( notebook_comments.id ) AS NBCOMMENTS
					FROM notebook_article, notebook_article_users, users, notebook_comments
					WHERE notebook_article.id = notebook_article_users.article_id
					AND notebook_article_users.user_id = users.id
					AND notebook_comments.article_id = notebook_article.id
					AND notebook_comments.status = 'O'
					AND notebook_comments.pubdate BETWEEN '".$time."' AND '".$today."'
					GROUP BY notebook_article.id
					ORDER BY username";
		
			$DB->getResults($req);
			if ($DB->nbResults()!=0) {
				//mail tab
				$tabMails = array();
				$texte="";
				$flag="";
                
                $req = "SELECT notebook_article.id AS IDARTICLE, notebook_article.title AS TITLEARTICLE, long_name, users.id as USERID, lang, username, COUNT( notebook_comments.id ) AS NBCOMMENTS
					FROM notebook_article, notebook_article_users, users, notebook_comments
					WHERE notebook_article.id = notebook_article_users.article_id
					AND notebook_article_users.user_id = users.id
					AND notebook_comments.article_id = notebook_article.id
					AND notebook_comments.status = 'O'
					AND notebook_comments.pubdate BETWEEN '".$time."' AND '".$today."'
					GROUP BY notebook_article.id
					ORDER BY username";
		
    			$DB->getResults($req);   
                
				while ($row=$DB->fetch(0))
				{
					$userId=$row['USERID'];
					$userMail=$row['username'];
					$userName=$row['long_name'];
					$userlang=$row['lang'];
					$nbComments=$row['NBCOMMENTS'];
					$titleArticle=$row['TITLEARTICLE'];
					$idArticle=$row['IDARTICLE'];			

                    require_once("l10n/".$userlang."/lang.php");
                    
                    $articlesPreview=""; 
                    $DB2->getResults("SELECT message FROM notebook_comments WHERE article_id=%u",$idArticle);
                    while($row2 = $DB2->fetch(0))
                    {
                        $articlesPreview.="- ".substr($row2['message'],0,100)."...\n";
                    }
                    $DB2->freeResults();
                    
                    if (in_array($userMail, $tabMails))
                        $texte.=$titleArticle." (".$nbComments.") \n".$articlesPreview."\n\n";
                    else {
                        if (sizeof($tabMails)!=0) {
                                $totalTab = sizeof($tabMails);
                                $texte.="\n".lg("bestRegards").",\n".__APPNAME."\n".__LOCALFOLDER;
                                $notif_subject=stripslashes(lg("newCommentsOnNotebook"));
                                $notif_message=stripslashes($texte);
                                $notif_sender="";
                                $notif_copy="";
                            
                                $s_mail = new mail();
                                $s_mail->addSender($notif_sender);
                                $s_mail->addSubject($notif_subject);
                                $s_mail->addMessage($notif_message);
                                $s_mail->configArray($notif_copy,'1');
                                $s_mail->configArray($tabMails[$totalTab-1],'2');
                                if(!$s_mail->sendMail()) {
                                    $s_mail->getErrLog();
                                    $s_mail->getInfos();
                                }												
                            }
                            array_push($tabMails,$userMail);
                            $texte="";
                            $texte = lg("lblHello")." ".$userName.",\n".lg("commentsOnFollowingArticles")."\n\n".$titleArticle." (".$nbComments.")\n\n";
                            $texte.= $articlesPreview."\n";
                            //__LOCALFOLDER."notebook/detail.php?id=".$userId."&artid=".$idArticle."#comment".$commId."\n\n";
                    }
                }		
				//send the last mail
				$totalTab = sizeof($tabMails);
                $texte.="\n".lg("bestRegards").",\n".__APPNAME."\n".__LOCALFOLDER;
                $notif_subject=stripslashes(lg("newCommentsOnNotebook"));
				
				$notif_message=stripslashes($texte);
				$notif_sender="";
				$notif_copy="";
				$s_mail = new mail();
				$s_mail->addSender($notif_sender);
				$s_mail->addSubject($notif_subject);
				$s_mail->addMessage($notif_message);
				$s_mail->configArray($notif_copy,'1');
				$s_mail->configArray($tabMails[$totalTab-1],'2');
				if(!$s_mail->sendMail())
				{
					$s_mail->getErrLog();
					$s_mail->getInfos();
				}								
				
				$DB->freeResults();
			}				
			//close the current database connection
			$DB->close();
			$DB2->close();
		}
		else
		{
			//delay format isn't correct
			echo "<font color='red'>Delay acces denied</font><br />";
			exit(1);
		}
	}
else
	{
    	//if key access refused
    	echo "<font color='red'>Key Acces denied</font><br />";
    	exit(1);
	}
?>