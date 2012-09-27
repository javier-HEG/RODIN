<?php
/*
 * name: Test Plugin
 * description: This plugin add a tab in the admin panel
 * dependencies: no
 * author: you
 * url: http://yourwebsite.com
 */
 
 // Your lib
 require_once('plugin_lib.php');
 
 /*
  * Registers the action in order to install the plugin
  * replace install_plugin test 2.0/plugin by install_fileNameWithout.Php
  */
 register_hook('install_plugin test 2.0/plugin','myplug2_install');
 register_hook('uninstall_plugin test 2.0/plugin','myplug2_uninstall');
 
 
 /*
  * Do something really useless
  */
 register_hook("display_plugin","myplug2_display",10,1);
 
 register_admin_tab('myplug2_tab','Your plugin 2.0','myplug2_admin_display');
 
 function myplug2_admin_display()
 {
	echo "<h2>Your plugin config</h2>";
 }
 
 
 // Something really useless
 function myplug2_display($infos)
 {
    /*
	echo "<tr>";
	echo "<td>&nbsp;</td>";
	echo "<td>Link : ".$infos["link"]." (provided by the useless test plugin)</td>";
	echo "</tr>";
    */
 }
 
 /*
  * This plugin does something when admin is looking at the plugins list
  */
 
 /*
  * Install function
  */
 function myplug2_install()
 {
 /*
	// Do something
	$sql = "SHOW TABLES LIKE 'PluginTest'";
	global $DB;
	$tables = $DB->select(FETCH_OBJECT,$sql);
	{
	if (empty($tables))
		$sql = "CREATE TABLE PluginTest (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			
			KEY id(id)
		)";
		$DB->execute($sql);
	}*/
	
 }
 
 /*
  * Uninstall function
  */
  function myplug2_uninstall()
 {
 /*
	global $DB;
	$DB->sql = "DROP TABLE PluginTest";
	$DB->execute();
*/
 }

?>