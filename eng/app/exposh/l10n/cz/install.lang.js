/* File generated by script since posh 2.0.2 */
var lang=new Array;
lang["adminInterface"]="Administration interface";
lang["appname"]="Name of your application / website";
lang["continue"]="CONTINUE";
lang["email"]="Email";
lang["horizontal"]="Under the tabs/horizontal";
lang["installActiveCollabSuite"]="Install the Plugin - Collaboration Suite (optional).";
lang["installActiveCollabSuiteDesc"]="This plugin adds collaboratives functionalities to Posh (social network, books of publication, management of the thematic groups, search engine, chat,...)";
lang["login"]="Username";
lang["menuPosition"]="Menu position";
lang["password"]="Password";
lang["server"]="MySQL server :<br />(eg : localhost)";
lang["vertical"]="Left/vertical";

var __lang='cz';
function lg(v_s,v_p) {
	var l_ret=lang[v_s],indef;
	if(v_p && l_ret){l_ret=l_ret.replace("$$",v_p);}
	if (l_ret==indef)l_ret=v_s;
 	return l_ret;
}