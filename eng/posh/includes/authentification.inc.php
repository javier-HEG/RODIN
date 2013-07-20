<?php
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
/*
* é
 * Functions used to authentify a user
 * Can be overwrited
 */
 
 
 
/*
 * Lougout
 * Input : 
 *		-------------
 *	Output :
 *		-------------
 */
if (!function_exists('user_logout')):
function user_logout()
{
	close_session();
	//cancel autoconnexion
	setcookie('autoi','',time()-31536000);
	setcookie('autop','',time()-31536000);
	header("location:index.php");
	launch_hook('logout');
}
endif;


/*returns the 3 first parts of IP*/
function getCleanIP()
{
	$ips = explode(".",$_SERVER['REMOTE_ADDR']);
	unset($ips[3]);
	$ip = implode(".",$ips);  
	return $ip;
}

/*increments the number of try if the maximum try number is not reached*/

function increment_try_number($id)
{
	global  $DB,
            $authentication_get_logins,
            $authentication_enter_new_try,
            $authentication_get_number_of_try,
            $authentication_increment_number_of_try;
	
	$ip = getCleanIP();
	$found = false;
	$DB->getResults($authentication_get_logins,
                        $DB->quote($ip),
                        $DB->quote($id),
                        __connectionDateRange);

	while ($row = $DB->fetch(0))
	{
		$found = true;
		if(__connectionDateRange > 0)
			$DB->execute($authentication_increment_number_of_try,
                            $DB->quote($id),
                            $DB->quote($ip),
                            __connectionDateRange);
					
	}
	if(!$found)
	{
		if(__connectionDateRange > 0)
			$DB->execute($authentication_enter_new_try,
                                $DB->quote($id),
                                $DB->quote($ip));
	}
	$DB->freeResults();
}
/*
 * Connects the user
 * Input :
 *	$id (int or string) : user login if logging in with a form, user id if logging in by cookie
 *	$pass (string) : password (form) or md5pass (coookie)
 *	$md5pass (bool) : true if the pass is in MD5 and $id is the user id
 * Output :
 *	$user (object) : user data
 *	$errormsg (string) : error string
 * Returns :
 *	True if loggued in correctly, false if not
 */
if (!function_exists('user_login')) :
function user_login($id,$pass,$md5pass=false,&$user,&$errormsg)
{
	$errormsg = '';
	if(login_try_number_control($id))
	{
		if ($md5pass)
		{
			$user = (object) null;
			return user_login_cookie($id,$pass,$user,$errormsg);
		}		
		else
		{
			$user = (object) null;
			return user_login_form($id,$pass,$user,$errormsg);
		}
	}
	else
	{
		$user = (object) null;
		return user_login_form($id,$pass,$user,$errormsg);
	}
}
endif;

/*
 * Logging using cookie informations
 * Input :
 *	$id (int) : user id in the DB
 *	$md5pass (string) : password to verify
 * Output :
 *	$user (object) : informations about the user
 *	$errormsg (string) : error string if an error occcurs
 * Returns :
 *	True if the user is connected, false if not
 */
if (!function_exists('user_login_cookie')) :
function user_login_cookie($id,$md5pass,&$user,&$errormsg)
{
	global  $DB,
            $authentif_getUser,
            $authentication_updateConnectDate;

	launch_hook('user_login_cookie',$id,$md5pass);
	
	$DB->getResults($authentif_getUser,
                        $DB->escape($id),
                        $DB->quote($md5pass));

	if ($DB->nbResults() > 0)
	{
		$row = $DB->fetch(0);
		if ($row["typ"] == "B" || $row["typ"] == "J")
		{
			$errormsg .= lg("userNotValidated");
		}
		else
		{
			$user->id = $id;
			$user->username = $row["username"];
			$user->type = $row["typ"];
			$user->lang = $row["lang"];
			$user->longname = $row["long_name"]=="" ? $row["username"] : $row["long_name"];
			$user->pass = $md5pass;
      $user->activity = $row["activity"];
			$user->positext = $row["positext"];
			$user->negatext = $row["negatext"];
			$DB->freeResults();

			$DB->execute($authentication_updateConnectDate,$DB->escape($id));

			return true;
		}
	}
	$DB->freeResults();
	return false;
}
endif;

/*
 * User logging (with form)
 * Input :
 *	$username (string)
 *	$password (string) : pass without md5
 * Output :
 *	$user (object) : contains user data
 *	$errormsg (string) : error string if an error occurs
 * Returns :
 *	True if correctly logged in, false if not 
 */
if (!function_exists('user_login_form')):
function user_login_form($username,$password,&$user,&$errormsg)
{
	global  $DB,
            $authentif_getUserByName,
            $authentication_updateConnectDate;

	launch_hook('user_login_form',$username,$password);
	//Check the number if try
	if(!login_try_number_control($username))
	{		
		$ip = getCleanIP(); 
		$errormsg .= lg("tooManyTry").'. '.lg("pleaseTryLater")." ".get_hours($username,$ip)." ".lg('hours');
	}
	else
	{
		//Check the password
		$DB->getResults($authentif_getUserByName,$DB->quote($username));
		if ($DB->nbResults() <= 0)
		{
			$errormsg .= lg("incorrectLogin");
			//increment the number of try
			increment_try_number($username);
		}
		else
		{
			$row = $DB->fetch(0);
			if ($row["typ"] == "B" || $row["typ"] == "J")
			{
				$errormsg .= lg("userNotValidated");
			}
			else if ($row["password"] <> md5($password))
			{
				$errormsg .= lg("incorrectLogin");
				//increment the number of try
				increment_try_number($username);
			}
			else
			{
				$user->id = $row["id"];
				$user->username = $username;
				$user->type = $row["typ"];
				$user->lang = $row["lang"];
				$user->longname = $row["long_name"]=="" ? $username : $row["long_name"];
				$user->pass = md5($password);
        $user->activity = $row["activity"];
				$user->positext = $row["positext"];
				$user->negatext = $row["negatext"];
				
				$DB->freeResults();

				$DB->execute($authentication_updateConnectDate,$user->id);

				return true;
			}
		}
	}
	$DB->freeResults();
	return false;
}
endif;
 

/*
 * Set the user cookie
 * Input :
 *	$user (object) : user data
 */
if (!function_exists('user_setcookie')) :
function user_setcookie($user)
{
	setcookie('autoi',$user->id,time()+31536000);
	setcookie('autop',$user->pass,time()+31536000);
}
endif;


function get_hours($id,$ip)
{
	global  $DB,
            $authentication_get_date;

	$DB->getResults($authentication_get_date,
                        $DB->quote($id),
                        $DB->quote($ip),
                        __connectionDateRange);

	$row = $DB->fetch(0);
	$sum = (__connectionDateRange-(time()-$row['date']));
	$DB->freeResults();
	$hours = (int)($sum/3600);
	if($hours <= 0)
    {
		$hours = 1;
	}
	return $hours;
}

/*controls if the  number of try is not over the maximum number of try
* Returns :
 *	False if the user is blocked because his number of try is bigger than the maximum, true if not
*/

function login_try_number_control($id)
{
	if(__numberOfTry==0)
        return true;
	
	global  $DB,
            $authentication_get_logins,
            $authentication_enter_new_try,
            $authentication_get_number_of_try,
            $authentication_increment_number_of_try;
	
	$ip = getCleanIP();
	$DB->getResults($authentication_get_logins,
                        $DB->quote($ip),
                        $DB->quote($id),
                        __connectionDateRange);

	while ($row = $DB->fetch(0))
	{
		$DB->getResults($authentication_get_number_of_try,
                            $DB->quote($id),
                            $DB->quote($ip),
                            __connectionDateRange);

		$row2 = $DB->fetch(0);
		
		if($row2['number_of_try'] >= __numberOfTry)
		{
			return false;
		}		
	}
	$DB->freeResults();
	
	return true;
}

?>