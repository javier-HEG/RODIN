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
# POSH menu management
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="../portal/";
$not_access=1;
$granted="A";
$pagename="admin/xml_tabs.php";
//includes
require('includes.php');
require_once('../includes/xml.inc.php');
require_once('../../app/exposh/l10n/'.__LANG.'/admin.lang.php');

$file=new xmlFile();

$file->header("tabs");

$bdd = Array(
                'admin/modules.php' => '$p.admin.widgets.buildPage();',
                'admin/users.php' => '$p.admin.users.buildPage();',
                'admin/pages.php' => '$p.admin.pages.buildPage();',
                'admin/config.php' => '$p.admin.config.buildPage();',
                'admin/stats.php' => '$p.admin.stats.buildPage();',
                'admin/communication.php' => '$p.admin.communication.buildPage();',
                'admin/applications.php' => '$p.admin.application.buildPage();',
                'admin/support.php' => '$p.admin.support.buildPage();'
            );
  
$DB->getResults($tabs_getAdminTabs,$DB->escape($_SESSION['user_id']));
while ($row = $DB->fetch(0))
{
    $id = $row['id'];
    $name = $row["name"];
    echo "<tab>";
    echo "<id>".$id."></id>";    
    echo "<name><![CDATA[".$name."]]></name>";
    echo "</tab>";
}
$DB->freeResults();

echo "<alltabs>";
echo "<id>0</id>";
echo "<name>maintab</name>";
echo "<label><![CDATA[".lg('home')."]]></label>";
echo '<param>$p.admin.welcome.buildPage();</param>';
echo "<type>5</type>";
echo "</alltabs>";

$DB->getResults($tabs_getTabs);
while ($row = $DB->fetch(0))
{
    $id = $row['id'];
    $param = $row["param"];
    $name = $row["name"];
    $label = $row["label"];
    $type = $row["type"];
    echo "<alltabs>";
    echo "<id>".$id."></id>";    
    echo "<name><![CDATA[".$name."]]></name>";
    echo "<label><![CDATA[".$label."]]></label>";
    echo "<param><![CDATA[".$bdd[$param]."]]></param>";
    echo "<type>".$type."</type>";
    echo "</alltabs>";
}

$DB->freeResults();


// Admin tabs added by plugins
if (isset($ADMIN_TABS) && is_array($ADMIN_TABS)) {
    foreach ($ADMIN_TABS as $name => $tab)
    {
        echo "<plugintab>";
        echo "<pluginname><![CDATA[".$name."]]></pluginname>";
        echo "<pluginlabel><![CDATA[".$tab["label"]."]]></pluginlabel>";
        echo "</plugintab>";
    }
}

$file->footer("tabs");
?>