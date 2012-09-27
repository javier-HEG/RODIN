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
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é

global $HOOKS;
global $DESACTIVATED_REGISTRATIONS;

/*
 * Functions that manage plugins
 */

/*
 * register_hook : registers a function for a specified hook
 * Input :
 *	$hookname (string) : hook for which you register the function
 *	$function (string) : function to call when calling this hook
 *	$priority (int) : lowest priority value => first to be called
 * 	$args (int) : number of arguments that the function accepts
 */ 
function register_hook($hookname,$function,$priority=10,$args=0)
{
	global $HOOKS;
	global $DESACTIVATED_REGISTRATIONS;
	
	// if not desactivated
	if (!is_array($DESACTIVATED_REGISTRATIONS) || !array_key_exists($hookname,$DESACTIVATED_REGISTRATIONS) 
		|| array_search($function,$DESACTIVATED_REGISTRATIONS[$hookname])===false)
		$HOOKS[$hookname][$priority][] = new hook($function,$args);
}

/*
 * unregister_hook : unregisteres a function
 * Input : 
 *	$hookname (string) : hook for which the function was registered
 *	$function (string) : function to remove
 */
function unregister_hook($hookname,$function)
{
	global $HOOKS;
	global $DESACTIVATED_REGISTRATIONS;
	// If something to remove
	if (is_array($HOOKS) && array_key_exists($hookname,$HOOKS) && is_array($HOOKS[$hookname]))
	{
		// Whe don't know the priority
		foreach ($HOOKS[$hookname] as $priority => $phook)
		{
			if (is_array($HOOKS[$hookname][$priority]))
			foreach ($HOOKS[$hookname][$priority] as $id => $hook)
			{
				if ($hook->f_name == $function)
				{
					unset($HOOKS[$hookname][$priority][$id]);
					break;
				}
			}
		}
	}
	$DESACTIVATED_REGISTRATIONS[$hookname][]=$function;
}

/*
 * launch_hook : calls all the registered functions for this action
 * Input :
 *	$hookname (string) : hook to launch
 *	Others args to pass to functions
 */
function launch_hook($hookname)
{
	global $HOOKS;
	// if something is registered
	if (is_array($HOOKS) && array_key_exists($hookname,$HOOKS) && is_array($HOOKS[$hookname]))
	{
		// Get the args
		$args = array();
		for ($i=1;$i<func_num_args();$i++)
		{
			$args[] = func_get_arg($i);
		}
		// lower priorities first
		uksort($HOOKS[$hookname],"strnatcasecmp");
		foreach ($HOOKS[$hookname] as $p => $hooks)
		{
			if (is_array($hooks))
			foreach ($hooks as $hook)
			{
				$hook->launch($args);
			}
		}
	}
}

/*
 * register_admin_tab
 * For plugins, adding an admin tab
 * Input :
 * 	$tab_name (string) : unique tab name
 *	$label (string) : label printed in the tab
 *	$function (string) : function to call when the tab is displayed
 */
function register_admin_tab($tab_name,$label,$function)
{
	global $ADMIN_TABS;
	$ADMIN_TABS[$tab_name]=array('function' =>$function,'label'=>$label);
}

/************************************************
 * Plugin installation
 *************************************************/

/*
 * fetch_plugin_header : extracts the header from a plugin file
 * Input :
 *	$filecontent (string) : file content
 * Output :
 *	$header (string) : the file part that contents header
 * Returns :
 *	true if the header was found
 * 	false if it was not
 */
function fetch_plugin_header($filecontent,&$header)
{
	if (preg_match('/\/\*.*\*\//s',$filecontent,$matches))
	{
		$header = $matches[0];
		return true;
	}
	return false;
}

/*
 * fetch_plugin_metadata : extracts a particular value from the header
 * Input :
 *	$header (string) : file header
 *	$var (string) : name of the var to find
 * Ouput :
 *	$value (string) : value that corresponds to the var name
 * Returns :
 *	True if found, false if not
 */
function fetch_plugin_metadata($header,$var,&$value)
{
	if (preg_match("/[ 	]*\*+[ 	]*$var:(.*)[\\r\\n]+/",$header,$match))
	{
		if (count($match)>1)
		{
			$value=$match[1];
			$value=trim($value);
			return true;
		}
	}
	return false;
}


/*
 * Objects that are stored into $HOOKS
 * Data about registered functions
 */
class hook
{
	// Registered function name
	// String
	var $f_name;
	// Number of arguments accepted
	// by this function
	var $args_number;
	
	/*
	 * Ctor
	 */
	function hook($function,$args)
	{
		$this->f_name = $function;
		$this->args_number = $args;
	}
	
	function launch($args=array())
	{
		call_user_func_array($this->f_name,array_slice($args,0,$this->args_number));
	}
}

?>