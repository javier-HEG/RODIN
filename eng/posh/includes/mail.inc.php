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
# Mail class
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

/* 
This class helps to manage the emailing (notifications to users)
*/ 
class Mail 
{
	var $receivers; //array
	var $copy; //array (emails in copy)
	var $sender; 
	var $subject;
	var $message;
	var $errorlog; //debug log file
	
	//constructor
	function Mail() {
		$this->receivers = array();
		$this->copy = array();
		$this->subject = '';
		$this->message = '';
		$this->sender = '';
		$this->errorlog = ''; //log of the errors
		$this->sendmail_path = '';	
	}

	//desctructor
	function __destruct() {}
	
	//controls that the email adress is correct return true if the email adresse is valid, false if not
	function verifMail($email) {
		//compares with a regular expression
		$search = "/^[a-z0-9\.\-_]+@[a-z0-9\.\-_]+\.[a-z]{2,3}$/i";
		return (preg_match($search,$email) !=0);
	}
	//avoid bad encoding in emails
	function encode($element) {
		return stripslashes(utf8_decode($element));
	}	
	/* 
	pseudoCodeConverter()
	input :
		 string : string which contains the pseudocode to convert
		 val : array of all the php values corresponding to the pseudocode
		 tab : array with all the pseudoCode
	*/
	function pseudoCodeConverter($string,$val,$tab)
	{
		if (sizeof($tab)!=0)
		{
			for ($i=0;$i<sizeof($tab);$i++)
			{
				$string = str_replace($tab[$i], $val[$i] ,$string);
			}
		}
		return $string;
	}
	
	//this function builds the headers of the email. only called in sendMail
	function constructHeader() 
	{
		$headers = "From: ".$this->sender."\r\n";
/*		.
		"Date: ". date('r') ."\r\n" .
		"X-Mailer: PHP/" . phpversion() . "\r\n" .
		"MIME-Version: 1.0\r\n" .
		"Content-Type: text/plain; charset=\"UTF-8\"\r\n" .
		"Content-Transfer-Encoding: 8bit\r\n\r\n"; 
*/
		if(!empty($this->copy))
			$headers .= "Cc: " . implode(',', $this->copy) . "\n";

		return $headers;
	}

	//defines the sender's email
	function addSender() 
	{
		if(func_num_args() == 0)
		{
		    if (__NOTIFICATIONEMAIL=="")
				$this->errorlog .= 'error, default sender not defined and no specific sender defined in database<br />';
			else
				$this->sender=__NOTIFICATIONEMAIL;
		}
		else
		{
		    $tmpSender=func_get_args();		
			if ($tmpSender[0]=="" && __NOTIFICATIONEMAIL!="")
				$this->sender=__NOTIFICATIONEMAIL;
			elseif ($tmpSender[0]=="" && __NOTIFICATIONEMAIL=="")
			{
				$this->errorlog .= 'error, sender "'. $tmpSender[0] .'" invalid<br />';
				return FALSE;
			}
			else
			{
				if($this->verifMail($tmpSender[0])) 
				{
					$this->sender = $tmpSender[0];
					return TRUE;
				}
				else 
				{
					$this->errorlog .= 'error, sender "'. $tmpSender[0] .'" invalid<br />';
					return FALSE;
				}		
			}		
		}
	}	
	
	//the user can add several receivers - returns true if everything's ok, false if not	
	function addReceiver($tab)
	{
	    //error type
		$error = '';
		//in case no destination defined
		if(sizeof($tab) == 0)
		   $error .= 'error, you must specify a recipient<br />';
		else
		{
				foreach($tab as $dest) 
				{
					if(!in_array($dest, $this->receivers) && !in_array($dest, $this->copy)) 
						{
							//Verify that the email adress is in a correct model
							if($this->verifMail($dest)) 
							{
								//add the email in the array() 
								array_push($this->receivers,$dest);
							} 
							else
								//the mail model is not correct
								$error .= 'error, recipient "'. $dest .'" invalid<br />';
						}
						else
							//the email adress has alredy been added
							$error .= 'error, recipient "'. $dest .'" already exists<br />';
				} 
		}
				if(empty($error))
			return TRUE;
		else 
		{
			$this->errorlog .= $error;
			return FALSE;
		}
	}
	
	//$type ( 2=addReceivers, 1=addCopy)
	function configArray($tab,$type)
	{		
		$arrayCopy=array();
		switch ($type)
		{
			//copy
			case 1:
			{
				if ($tab!="")
				{
					$tok = strtok($tab,";");
					while ($tok !== false) 
					{
						if ($this->verifMail($tok))
							 $arrayCopy[] = $tok;
						else
						{
							$this->errorlog .= 'error, copy "'. $tok .'" is not valid<br />';
							return false;
						}		
						$tok = strtok(";");
					} 
						
					if(sizeof($arrayCopy)>0)
					{
						$this->addCopy($arrayCopy);
						return true;
					}
				}
				break;
			}
			//receiver
			case 2:
			{
				if ($tab=="")
				{
					$this->errorlog .= 'error, no receiver adress defined<br />';
					return false;
				}
				else
				{
					$tok = strtok($tab,";");
					while ($tok !== false) 
					{
						if ($this->verifMail($tok))
							 $arrayCopy[] = $tok;
						else
							$this->errorlog .= 'error, copy "'. $tok .'" is not valid<br />';
								
						$tok = strtok(";");
					} 
					if(sizeof($arrayCopy)>0)
					{
						$this->addReceiver($arrayCopy);
						return true;
					}
					else
					{
						$this->errorlog .= 'error, no valid receiver adress  defined<br />';
						return false;
					}
				}
			}
		}		
	}
	
	//the user can define several mail adresses in copy
	function addCopy($tab) 
	{
	    //error type
		$error = '';
		//in case no destination defined
		if(sizeof($tab) == 0)
		   $error .= 'error, you must specify a recipient in copy<br />';
		else
		{
				foreach($tab as $cc) 
				{
					if(!in_array($cc, $this->receivers) && !in_array($cc, $this->copy)) 
					{
						//Verify that the email adress is in a correct model
						if($this->verifMail($cc)) 
						{
							//add the email in the array() 
							array_push($this->copy,$cc);
						} 
						else
							//the mail model is not correct
							$error .= 'error, recipient copy "'. $cc .'" invalid<br />';
					}
					else
						//the email adress has alredy been added
						$error .= 'error, recipient copy "'. $cc .'" already exists<br />';
				} 
		}
		if(empty($error)) return TRUE;
		else 
		{
			$this->errorlog .= $error;
			return FALSE;
		}
	}
	
	function addSubject() 
	{
		// if the only parameter is the subject
	 	if(func_num_args() == 1)
		{
			$vars = func_get_args();	
			$value = $vars[0];
			if(!empty($value) && is_string($value))
			{
				$this->subject = $this->encode($value);
				return TRUE;
			}
			else
				$this->errorlog .= 'error, subject '. $value .' invalid<br />';
		}
		// if there's the subject and the two conversion tabs
		elseif (func_num_args() == 3)
		{
			$vars = func_get_args();	
			$value = $vars[0];
	
				if(!empty($value) && is_string($value))
				{
					//reception of the tabs values
					$val = array();
					$tab = array();
					for ($i=0;$i<sizeof($vars[1]);$i++)
					{
						array_push($val,$vars[1][$i]);
					}
					for ($j=0;$j<sizeof($vars[2]);$j++)
					{
						array_push($tab,$vars[2][$j]);
					}
				
					$value = $this->pseudoCodeConverter($value,$val,$tab);
					$this->subject = $this->encode($value);
					return TRUE;
				}
				else
					$this->errorlog .= 'error, subject '. $value .' invalid<br />';
		}
		else
		$this->errorlog .= 'error, invalid number of parameters for addSubject()<br />';
	}
	
	//defines the body message
	function addMessage() 
	{
		// if the only parameter is the message
	 	if(func_num_args() == 1)
		{
			$vars = func_get_args();	
			$value = $vars[0];
			if(!empty($value) && is_string($value))
			{
				$this->message = $this->encode($value);
				return TRUE;
			}
			else
				$this->errorlog .= 'error, message '. $value .' invalid<br />';
		}
		// if there's the message and the two conversion tabs
		elseif (func_num_args() == 3)
		{
			$vars = func_get_args();	
			$value = $vars[0];
	
				if(!empty($value) && is_string($value))
				{
					//reception of the tabs values
					$val = array();
					$tab = array();
					for ($i=0;$i<sizeof($vars[1]);$i++)
					{
						array_push($val,$vars[1][$i]);
					}
					for ($j=0;$j<sizeof($vars[2]);$j++)
					{
						array_push($tab,$vars[2][$j]);
					}
				
					$value = $this->pseudoCodeConverter($value,$val,$tab);
					$this->message = $this->encode($value);
					return TRUE;
				}
				else
					$this->errorlog .= 'error, message '. $value .' invalid<br />';
		}
		else
		$this->errorlog .= 'error, invalid number of parameters for addMessage()<br />';
	}	
	
	//each time an error occurs, it's reported in $this->errorlog - getErrlog() returns $this->errorlog
	function getErrlog() 
	{
		echo $this->errorlog;
	}
	
	//Sends the mail
	function sendMail() 
	{
		$error = '';
		//verifications
		if(empty($this->receivers))
			$error .= 'error, you must specify at least one recipient!<BR />';
		elseif(empty($this->sender))
			$error .= 'error, you must specify a sender!<BR />';
		elseif(empty($this->subject))
			$error .= 'error, you must define a subject!<BR />';
		elseif(empty($this->message))
			$error .= 'error, you must define a message!<BR />';
		else 
		{
				//if no error
				if(empty($error)) 
				{
						//sends the mail
						if(mail(implode(',', $this->receivers), $this->subject, $this->message, $this->constructHeader()))
							return TRUE;
						else 
						{
							$error .= 'error, mail not sent<br />';
							$this->errorlog .= $error;
							return FALSE;
						}
				}
				else 
				{
					$this->errorlog .= $error;
					return FALSE;
				}
		}
		return TRUE;
	}
	
	//Display all the objects's informations
	function getInfos()
	{
		echo '<u>recipient(s):</u> ' . implode(',', $this->receivers) . '<br />';
		echo '<u>subject:</u> ' . $this->subject . '<br />';
		echo '<u>message:</u>' . $this->message . '<br />';
		echo '<u>sender:</u> ' . $this->sender . '<br />';
		if(!empty($this->copy))
			echo '<u>copy(s):</u> ' . implode(',', $this->copy) . '<br />';
	}
}	
?>
