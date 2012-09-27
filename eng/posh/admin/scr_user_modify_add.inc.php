<?php
# ******************************************* LICENCE******************************************* 
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
# ********************************************************************************** 
# POSH Users management - Apply user modifications
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ********************************************************************************** 
require_once('../includes/mail.inc.php');
require_once('../includes/refreshcache.inc.php');
require_once('../../app/exposh/l10n/'.__LANG.'/admin.lang.php');
require_once('../includes/xml.inc.php');
require_once('../includes/admin_tools.php');
$file=new xmlFile();

$file->header("usermod_add");

launch_hook('admin_scr_rootdirectory_modify');

if (!isset($userid)) {
    $userid=isset($_POST["user"]) ? $_POST["user"] : 0 ;
}

$user       = $_POST["username"];
$password   = $_POST["pass"];
$key        = md5($user.$password);
$nbSpecificFields = 0;

if (isset($_POST["nbSpecificFields"]))	{ 
    $nbSpecificFields=$_POST["nbSpecificFields"]; 
}

// userid=0 => new user
if ($userid == 0)
{
	//control that the mail adress does not already exists
	$DB->getResults($users_controlExistingAccount,$DB->noHTML($user));
	$row = $DB->fetch(0);
	if ($row['nb']>0)   {
        $errormsg=lg("alreadyMember");
		$file->error($errormsg);
        $file->status(0);
        $file->footer();
        exit();
	}

	//add the new user
	$DB->execute($users_addNew,$DB->quote($user),$DB->quote($_POST["long_name"]),
                                $DB->quote($_POST["usertype"]),
                                $DB->quote($password),
                                $DB->quote($key),
                                $DB->quote($_POST["lang"]));
	// new user_id
	$userid = $DB->getId();
	//add to log
	$DB->execute($users_logUserAdd,$userid);
	
	//specific criterias for the user
	if ($nbSpecificFields != 0)   {
		for ($i = 1;$i <= $nbSpecificFields;$i++)
		{
			$parameters = "";
			$id = $_POST["uniq_id".$i];
			if (isset($_POST["userinfo".$i]))   {
                if (is_array($_POST["userinfo".$i]))    {
                    if(count($_POST["userinfo".$i]) == 1 && ereg(';',$_POST["userinfo".$i][0])) {
                        $parameters = str_replace(";", ",", $_POST["userinfo".$i][0]);
                    }
                    else {
                        for ($j = 0;$j < count($_POST["userinfo".$i]);$j++)
                        {	
                            if ($j == 0)	$parameters = $_POST["userinfo".$i][$j];
                            else	$parameters .= ",".$_POST["userinfo".$i][$j];	
                        }	
                    }
                }	
                else    {
                    if ($_POST["userinfo".$i]!="")	$parameters=$_POST["userinfo".$i];
                }
			}					
			//sql query to add	
			$DB->execute($users_addUserInfos,$DB->escape($userid),$DB->escape($id),$DB->quote($parameters));
		}
	}
	//if the account created is for a classic user
	if ($_POST["usertype"] == 'I')    {
		//add the defaults public pages in profile table
		$DB->getResults($users_getPublicAndGroupPages,0);
		while ($row = $DB->fetch(0))
		{
			$DB->execute($user_addProfilePages,
                                    $DB->escape($userid),
                                    $DB->quote($row['name']),
                                    $DB->escape($row['nbcol']),
                                    $DB->escape($row['style']),
                                    $DB->quote($row['controls']),
                                    $DB->quote($row['showtype']),
                                    $DB->escape($row['seq']),
                                    $DB->quote($row['icon']),
                                    $DB->quote($row['modulealign']),
                                    $DB->quote($row['type']),
                                    $DB->quote($row['param']),
                                    $DB->escape($row['id']),
                                    $DB->escape($row['removable'])
                        );
			$profid=$DB->getId();
			//must check the modules
			$temp_id=$row['id'];
			if ($row['type'] == 1)    {
				$DB2->getResults($page_getPagesModule,$temp_id);
				while ($rows = $DB2->fetch(0))
				{
					$DB2->execute($scrconfigplace_addNewMod,
                                    $DB2->escape($rows['item_id']),
                                    $DB2->escape($userid),
                                    $DB2->escape($profid),
                                    $DB2->escape($rows['posx']),
                                    $DB2->escape($rows['posy']),
                                    $DB2->escape($rows['posj']),
                                    $DB2->escape($rows['x']),
                                    $DB2->escape($rows['y']),
                                    $DB2->quote($rows['variables']),
                                    $DB2->escape($rows['uniq']),
                                    $DB2->escape($rows['blocked']),
                                    $DB2->escape($rows['minimized']) 
                                  );
				}
				$DB2->freeResults();
			}
		}
		
		//group pages
		$cpt=0;	
		while (isset($_POST["group".$cpt]))
		{
			$DB->getResults($users_getPublicAndGroupPages,$DB->escape($_POST["group".$cpt]));
			while ($row = $DB->fetch(0))
			{		
				$DB->execute($user_addProfilePages,
                                    $DB->escape($userid),
                                    $DB->quote($row['name']),
                                    $DB->escape($row['nbcol']),
                                    $DB->escape($row['style']),
                                    $DB->quote($row['controls']),
                                    $DB->quote($row['showtype']),
                                    $DB->escape($row['seq']),
                                    $DB->quote($row['icon']),
                                    $DB->quote($row['modulealign']),
                                    $DB->quote($row['type']),
                                    $DB->quote($row['param']),
                                    $DB->escape($row['id']),
                                    $DB->escape($row['removable'])
                             );			
				$profid=$DB->getId();
				//must check the modules
				$temp_id=$row['id'];
				if ($row['type']==1)    {
					$DB2->getResults($page_getPagesModule,$temp_id);
					while ($rows = $DB2->fetch(0))
					{
						$DB2->execute($scrconfigplace_addNewMod,
                                                    $DB2->escape($rows['item_id']),
                                                    $DB2->escape($userid),
                                                    $DB2->escape($profid),
                                                    $DB2->escape($rows['posx']),
                                                    $DB2->escape($rows['posy']),
                                                    $DB2->escape($rows['posj']),
                                                    $DB2->escape($rows['x']),
                                                    $DB2->escape($rows['y']),
                                                    $DB2->quote($rows['variables']),
                                                    $DB2->escape($rows['uniq']),
                                                    $DB2->escape($rows['blocked']),
                                                    $DB2->escape($rows['minimized']) 
                                             );
					}
					$DB2->freeResults();
				}
			}
			$cpt++;
			$DB->freeResults();
		}
		launch_hook('admin_create_user',$userid);
	}
	//if the user created is an admin
	else {
        //get pages from admin which create other admin 
        $listpages = getAdminPages(array( 'db' => $DB, 'user_id' => $_SESSION['user_id'] ));
		//Tabs the admin can access
		if (isset($_POST['admTabs']))   {
			$tabList=explode(',',$_POST['admTabs']);          
			for ($i=0;$i<count($tabList);$i++)
			{
                $pageid = $tabList[$i];
                //can create page only if creator has right on pages
                if (  $listpages[$pageid] ) {
                    $DB->execute($users_addAdmTabMap,$DB2->escape($userid),$DB2->escape($pageid));
                }
			}
		}
	}
}
//user update
else {
	 //modify the user information
	 if ($user!="" && $password!="" && $password!="xxxxxxxx") {
		$DB->execute($users_updateWithPass,
                            $DB->quote($user),
                            $DB->quote($_POST["long_name"]),
                            $DB->quote($_POST["usertype"]),
                            $DB->quote($password),
                            $DB->quote($key),
                            $DB->quote($_POST["lang"]),
                            $DB->escape($userid)
                        );
     }
     else { 
		$DB->execute($users_updateWithoutPass,
                                $DB->quote($user),
                                $DB->quote($_POST["long_name"]),
                                $DB->quote($_POST["usertype"]),
                                $DB->quote($_POST["lang"]),
                                $DB->escape($userid)
                        
                    );        
     }

	// change portals languages
	$DB->execute($users_updatePortalsLanguage,$DB->quote($_POST["lang"]),$DB->escape($userid));

	//update the user specifics infos
	if ($nbSpecificFields!=0) {
		for ($i=1;$i<=$nbSpecificFields;$i++)
		{
			$parameters="";
			$id = $_POST["uniq_id".$i];
			//if it's a checkbox (array)
			if (isset($_POST["userinfo".$i])) {
                if (is_array($_POST["userinfo".$i])) {
                    for ($j=0;$j<count($_POST["userinfo".$i]);$j++)
                    {	
                        if ($j==0) { $parameters = $_POST["userinfo".$i][$j]; }
                        else { $parameters .= ",".$_POST["userinfo".$i][$j];  }
                    }		
                }	
                else    {
                    if ($_POST["userinfo".$i]!="")	{ $parameters=$_POST["userinfo".$i]; }
                }
			}					
			//sql query to add	or update
            $DB->getResults($users_getSpecificCriteria,$DB->escape($userid),$DB->escape($id));
            if ($DB->nbResults()>0) {   
                $DB->execute($users_updateUserInfos,$DB->quote($parameters),$DB->escape($userid),$DB->escape($id));	 
            }
            else {  
                $DB->execute($users_addUserInfos,$DB->escape($userid),$DB->escape($id),$DB->quote($parameters));  
            }
		}
	}
	
    //Delete the users pages for the groups he's been suppressed
    $user_group_id = array ();
    $user_pages_id = array ();
    $user_newgroup_id = array ();
    $cpt=0;
    //get new groups
    while (isset($_POST["group".$cpt])) {
        array_push ($user_newgroup_id, $_POST["group".$cpt]);
        $cpt++;
    }
    
    //get old group mapping
    $DB->getResults($users_getUserGroup,$DB->escape($userid));
    while ($row = $DB->fetch(0)) {
       array_push ($user_group_id, $row['id']);
    }
    $DB->freeResults();
    
    //compare the two tabs on delete the user pages if not in the group anymore
    for ($i=0;$i<count($user_group_id);$i++) {
        if (!in_array($user_group_id[$i],$user_newgroup_id)) {
            $DB->getResults($pages_getGroupPages,$DB->escape($user_group_id[$i]));
            while ($row = $DB->fetch(0)) {
                array_push ($user_pages_id, $row['id']);
            }
            $DB->freeResults();
            
            for ($j=0;$j<count($user_pages_id);$j++)
            {
                //delete all modules
                $DB->getResults($users_getUserProfilesId,$DB->escape($user_pages_id[$j]),$DB->escape($userid));
                while ($row = $DB->fetch(0)) {
                    $profile_id=$row['id'];
                    $DB2->execute($users_deleteProfileModules,$DB2->escape($profile_id));
                }
                $DB->freeResults();
                //delete in profile
                $DB->execute($users_deleteToUpdateTabs,$DB->escape($user_pages_id[$j]),$DB->escape($userid));
            }
        }
    }
        
	//group pages
	$cpt=0;
	while (isset($_POST["group".$cpt]))
	{
		//verify if the page doesn't already exists
		$DB2->getResults($users_getExistingUserGroup,$DB2->escape($_POST["group".$cpt]),$DB2->escape($userid));
		if ($DB2->nbResults()==0)   {
			$DB->getResults($users_getPublicAndGroupPages,$DB->escape($_POST["group".$cpt]));
			while ($row = $DB->fetch(0))
			{	
                $DB->execute($user_addProfilePages,
                                        $DB->escape($userid),
                                        $DB->quote($row['name']),
                                        $DB->escape($row['nbcol']),
                                        $DB->escape($row['style']),
                                        $DB->quote($row['controls']),
                                        $DB->quote($row['showtype']),
                                        $DB->escape($row['seq']),
                                        $DB->quote($row['icon']),
                                        $DB->quote($row['modulealign']),
                                        $DB->quote($row['type']),
                                        $DB->quote($row['param']),
                                        $DB->escape($row['id']),
                                        $DB->escape($row['removable'])
                              );			
                $profid=$DB->getId();
                //must check the modules
                $temp_id=$row['id'];
                if ($row['type']==1) {
                    $DB2->getResults($page_getPagesModule,$temp_id);
                    while ($rows = $DB2->fetch(0))
                    {
                        $DB2->execute($scrconfigplace_addNewMod,
                                                $DB2->escape($rows['item_id']),
                                                $DB2->escape($userid),
                                                $DB2->escape($profid),
                                                $DB2->escape($rows['posx']),
                                                $DB2->escape($rows['posy']),
                                                $DB2->escape($rows['posj']),
                                                $DB2->escape($rows['x']),
                                                $DB2->escape($rows['y']),
                                                $DB2->quote($rows['variables']),
                                                $DB2->escape($rows['uniq']),
                                                $DB2->escape($rows['blocked']),
                                                $DB2->escape($rows['minimized']) 
                                      );
                    }
                    $DB2->freeResults();
                }
			}
            $DB->freeResults();
		}
		$cpt++;
	}
	
    //tabs are not editable for the big root admin - admin own account
    if ($DB->escape($userid) != 1 && $DB->escape($userid)!=$_SESSION['user_id'])  {
    	//delete the previous tabs
    	$DB->execute($users_deleteAdmTabMap,$DB->escape($userid));
    	//update the admin tabs
    	if (isset($_POST['admTabs'])) {
    		$tabList=explode(',',$_POST['admTabs']);
    		for ($i=0;$i<sizeof($tabList);$i++)
    		{
    			$DB->execute($users_addAdmTabMap,$DB2->escape($userid),$DB2->escape($tabList[$i]));
    		}
    	}
    }
	launch_hook('admin_modify_user',$userid);
}

//group management
$i=0;
$userType=$_POST["usertype"];
if ($userType=='I') {
    $DB->execute($user_removeFromGroup,$DB->escape($userid));
    while (isset($_POST["group".$i]))
    {
    	$DB->execute($users_addInGroup,$DB->escape($userid),$DB->escape($_POST["group".$i]));
    	launch_hook('admin_set_user_group',$userid,$_POST["group".$i]);
    	$i++;
    }
}
else if ($userType=='A') {
    $DB->execute($admin_removeFromGroup,$DB->escape($userid));
	while (isset($_POST["group".$i]))
	{
		$DB->execute($admin_addInGroupMap,$DB->escape($userid),$DB->escape($_POST["group".$i]));
		$i++;
	}
}

//notification
if (isset($_POST["notify"]) && strcmp($DB->escape($_POST["notify"]),"true")==0) {
	if ($password=="xxxxxxxx") {
		//Generate a new password
		$str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$password="";
		srand((double)microtime()*1000000);
		for($i=0;$i<10;$i++) $password.= $str[rand()%62]; 
		
		// update password
		$DB->setUtf8();
		$DB->execute($users_updatePassword,$DB->quote(md5($password)),$DB->escape($userid));
	}
	
	//add unsubscribe to the message
	$unsubscribeLink = __LOCALFOLDER.'portal/login.php?id='.$userid.'&md5='.$key;
	$unsubscribe = lg('accountUnsubscribe').lg('lblClickHere').' : '.$unsubscribeLink;
		
    //tab with all the php values to include into the mail 
    $val = array($user, $password, __APPNAME, __LOCALFOLDER, $unsubscribe);
    //tab with all the pseudoCode tags
    $tab = array("%email", "%password", "%site", "%link","%unsubscribe");
	$lang=$_POST['lang'];
	$DB->getResults($config_getNotification,$DB->quote($_POST["lang"]),'validInscription');

	while ($row = $DB->fetch(0))
	{
		$notif_subject=stripslashes($row["subject"]);
		$notif_message=stripslashes($row["message"]);
		$notif_sender=$row["sender"];
		$notif_copy=$row["copy"];
	}
	$DB->freeResults();
		
	$s_mail = new mail();
	$s_mail->addSender($notif_sender);
	$s_mail->addSubject($notif_subject,$val,$tab);
	$s_mail->addMessage($notif_message,$val,$tab);
	$s_mail->configArray($notif_copy,'1');
	$s_mail->configArray($user,'2');

    if(!$s_mail->sendMail())    {
		$file->error(lg('mailNotSendIncorrectAdress'));
        $file->status(0);
        $file->footer();
		exit();
	}   
}

$DB->close();
$DB2->close();
$file->status(1);
$file->footer();
?>