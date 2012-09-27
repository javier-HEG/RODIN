<?php
# ************** LICENCE ****************
/*
	Copyright (c) PORTANEO.

	This file is part of COLLABORATION SUITE of POSH http://sourceforge.net/projects/posh/.

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
 * name: Enterprise main tools
 * description: Main enterprise features (social network, proxy & ldap, ..)<br /><br />> Attention !<br />> We advise you to not change the status of this plugin. <br />> Activate / inactivate this plugin can make you application unstable.<br />
 * display:  style="font-weight:bolder; color:red;background-color: #FFD363;"
 * dependencies: no
 * author: Portaneo
 * url: http://www.portaneo.net
 */

register_hook('install_enterprise_edition/enterprise_edition','enterprise_install');
register_hook('uninstall_enterprise_edition/enterprise_edition','enterprise_uninstall');
register_hook('userinterface_header','loadEnterpriseJavascript',5,0);
register_hook('admin_header','loadAdminEnterpriseJavascript',5,0);
register_hook('admin_frame_header','loadAdminEnterpriseJavascript',5,0);
register_hook('xmlfeeds_headeradditions','proxyConnexion',5,2);

/*
* Install function
*/
function enterprise_install()
{
    global $DB;
    $sql = "UPDATE adm_config 
                SET value=1 
                WHERE parameter='useChat' 
                OR parameter='useSharing' 
                OR parameter='useNotebook'
                OR parameter='useNetwork'
                OR parameter='useArchive'";
    $DB->execute($sql);
}
/*
* Uninstall function
*/
function enterprise_uninstall()
{
    global $DB;
    $sql = "UPDATE adm_config 
                SET value=0 
                WHERE parameter='useChat' 
                OR parameter='useSharing' 
                OR parameter='useNotebook'
                OR parameter='useNetwork'
                OR parameter='useArchive'";
    $DB->execute($sql);
} 
 
function loadEnterpriseJavascript(){
	echo "<script type='text/javascript' src='../l10n/".__LANG."/enterprise.js?v=1.0.0' ></script>";
	echo "<script type='text/javascript' src='../includes/application.js?v=1.0.0' ></script>";
	echo "<script type='text/javascript' src='../includes/proxy.js?v=1.0.0'></script>";
	echo "<script type='text/javascript' src='../includes/php/application-urls.js?v=1.0.0'></script>";
	if (__proxypacfile!="") echo '<script type="text/javascript" src="'.__proxypacfile.'"></script>';
	echo "<link rel='stylesheet' type='text/css' href='../styles/enterprise.css?v=1.0.0' />";
	echo "<script type='text/javascript' src='../tools/fckeditor/fckeditor.js'></script>";
}
function loadAdminEnterpriseJavascript(){
	echo "<script type='text/javascript' src='../includes/application.js?v=1.0.0' ></script>";
	echo "<script type='text/javascript' src='../l10n/".__LANG."/enterprise.js?v=1.0.0' ></script>";
    echo "<script type='text/javascript' src='../includes/php/application-urls.js?v=1.0.0'></script>";
	echo "<script type='text/javascript' src='../includes/proxy.js?v=1.0.0'></script>";
	if (__proxypacfile!="") echo '<script type="text/javascript" src="'.__proxypacfile.'"></script>';
	require_once('../../app/exposh/l10n/'.__LANG.'/enterprise.php');
}
function proxyConnexion($v_feed,$v_proxy){
	if (__useproxy && ($v_proxy!="" || __proxypacfile=="")){
		$proxy=$v_proxy;
		$pos=strpos($proxy,":");
		$proxyserver=str_replace("http://","",substr($proxy,0,$pos));
		$proxyport=substr($proxy,$pos+1);
		
		$v_feed->http->proxy_auth="Proxy-Authorization: Basic ".__PROXYCONNECTION."\r\n\r\n";
		$v_feed->http->cProtocol="";
		if (__proxypacfile==""){
			$v_feed->http->cServer=__PROXYSERVER;
			$v_feed->http->cPort=__PROXYPORT;
		} else {
			$v_feed->http->cServer=$proxyserver;
			$v_feed->http->cPort=$proxyport;
		}
	}
}
?>