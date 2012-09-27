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
/**
 * Call this function to display admin tabs
 */
function write_admin_tabs($selected,$write_script=true)
{
	global $DB;
	global $tabs_getAdminTabs;

	if ($write_script)  {
		echo "<script type='text/javascript'>
		/* <![CDATA[ */";
	}
?>

	$p.app.env="admin";
	$p.app.user.init(-1,"<?php echo $_SESSION['longname'];?>","A");
	$p.app.tabs.selId=-1;
	var pfolder="../admin/";
	//var __headlinks=new Array({"fct":"","label":"<strong>"+lg("admin")+"<\/strong>","img":"-","comment":"","anonymous":true,"connected":true},{"fct":"","label":"<?php echo $_SESSION['username'];?>","img":"-","comment":"","anonymous":true,"connected":true},{"fct":"logout();","label":lg("lblDisconnect"),"img":"-","comment":"","anonymous":true,"connected":true});

	<?php
	// Default tab
	add_admin_tab("maintab",'Accueil',"index.php",4,false,0,false);
	
	$DB->getResults($tabs_getAdminTabs,$DB->escape($_SESSION['user_id']));
	while($row = $DB->fetch(0))
	{
		add_admin_tab($row["name"],$row["label"],__LOCALFOLDER.$row["param"],$row["type"]);
	}
	
	// Admin tabs added by plugins
	global $ADMIN_TABS;
	if (is_array($ADMIN_TABS))
	foreach ($ADMIN_TABS as $name => $tab)
	{
		add_admin_tab($name,$tab['label'],__LOCALFOLDER.'admin/admin_tab.php?page='.$name);
	}
	select_admin_tab($selected);
	$DB->freeResults();
	?>
	$p.app.mainMenu();
	$p.app.tabs.create($p.app.tabs.sel);
	
	<?php
	if ($write_script)
		echo "/* ]]> */
		</script>";
}

/*
 * Adds a tab
 * Input :
 *	$name (string) : tab name
 *	$label (string) : tab label, text displayed
 *	$type (int) : tab type (don't really know what it is for the moment)
 *	$t1 (bool)
 *	$t2 (int)
 *	$t3 (bool)
 */
function add_admin_tab($name,$label,$link,$type=4,$t1=false,$t2=1,$t3=false)
{
	$l1 = ($t1 ? 'true' : "false");
	$l3 = ($t3 ? 'true' : "false");
	echo 'tab.push(new $'.'p.app.tabs.object("'.$name.'",lg("'.$label.'"),'.$type.',"'.$link.'"'.",$l1,$t2,$l3));\n";
}

/*
 * Select a tab by name
 */
function select_admin_tab($name)
{
	echo "$"."p.app.tabs.selectTab('$name',tab);\n";
}

?>