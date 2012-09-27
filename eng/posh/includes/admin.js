// ************** LICENCE ****************
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
// ***************************************
// POSH Admin specific javascript
//
// be careful, this file must be saved under uft8 format, and display an e accentuated here : é
// FRI: Changed maximal widh/height of Widgets to 9999 9999
// ***************************************

var widgetDecalY=314;
$p.app.env="admin";

var admPortals={
	catid:0,
	catname:0,
	levCatid:[],
	currLevel:0,
	init:function(v_reload)
	{
		$p.print("optmod",admPortals.menu());
		if (!v_reload) admPortals.getList(1,0);
		admPortals.getNbModule();
	},
	menu:function(v_sel)
	{
		var l_s="<a id='btn0' class='optlist' style='font-size:11pt;font-weight:bold;' href='#' onclick='return admPortals.getList(1,0)'>"+lg("portals")+" <span id='btn0nb'></span></a> | ";
		admPortals.catSel(0,0);
		return l_s+"<br /><br />";
	},
	portalBar:function(v_id)
	{
		var l_s="";
		for (var i=0;i<__dimension.length;i++)
		{
			if (__dimension[i]["id"]!=0)
			{
				l_s+="<a id='rootdir"+__dimension[i]["id"]+"' class='optlist' href='#' onclick='return admPortals.initDir("+__dimension[i]["id"]+","+__dimension[i]["seq"]+")'>"+__dimension[i]["name"]+"</a> | ";
			}
			if (__dimension[i]["id"]==v_id)
			{
				admPortals.catid=v_id;
				admPortals.catname=__dimension[i]["name"];
			}
		}
		return l_s;
	},
	activateBtn:function(v_id)
	{
		for (var i=0;i<3;i++)
		{
			$p.setClass("btn"+i,"optlist");
		}
		$p.setClass("btn"+v_id,"sellist");	
	},
	catSel:function(v_id,v_name)
	{
		admPortals.catid=v_id;
		admPortals.catname=v_name;
	},
	getList:function(v_page,v_type,v_seq)
	{
		var l_link=padmin["xml_allportals"];
		admPortals.hideFrame();
		admPortals.currLevel=0;
		admPortals.activateBtn(v_type);
		if (v_type==1) l_link=padmin["xml_portalstovalidate"];
		getXml(l_link+"?p="+v_page,admPortals.displayList,new Array(v_page,v_type));
		return false;
	},
	displayList:function(response,vars)
	{
		var l_s="";
		l_s+="<table cellpadding='0' cellspacing='0'>";
		if (response.getElementsByTagName("page")[0])
		{
			var l_id;
			l_s+="<tr><td><table width='960'>";
			l_s+="<tr><td valign='top' class='dirdiva'><table cellpadding='0' cellspacing='0' width='900'><tr><td valign='top'><table cellpadding='1' cellspacing='1' width='300'>";
			var i=0;
			while (response.getElementsByTagName("portal")[i])
			{
				result=response.getElementsByTagName("portal")[i];
				l_id=$p.ajax.getVal(result,"id","int",false,0);
				l_s+="<tr><td style='border-bottom:1px solid #c6c3c6'><a class='menul' href='#' onclick='admPortals.loadPortal("+l_id+")' "+(($p.ajax.getVal(result,"status","str",false,"")=='O')?"":" style='color:#c6c3c6;text-decoration: line-through;'")+">"+$p.ajax.getVal(result,"name","str",false,"--")+"</a></td></tr>";
				i++;
				if (i%7==0&&i<21) l_s+="</table></td><td valign='top'><table cellpadding='1' cellspacing='1' width='300'>"
			}
			if (i==0) l_s+="<tr><td>"+lg("lblNoPortal")+"</td></tr>";
			l_s+="</table></td></tr></table>";
			l_s+="<br /><table width='100%'><tr>";
			if (vars[0]>1)
			{
				l_s+="<td><a href='#' onclick='admPortals.getList("+(vars[0]-1)+");return false;'>"+$p.img("prev.gif",14,27,lg("lblPrevPortal"))+"</a></td>";
			}
			if (i==21)
			{
				l_s+="<td align='right'><a href='#' onclick='admPortals.getList("+(vars[0]+1)+");return false;'>"+$p.img("next.gif",14,27,lg("lblNextPortal"))+"</a></td>";
			}
			l_s+="</tr></table>";
		}
		else
		{
			l_s+=lg("lblDisplayErr");
		}
		l_s+="</td></tr></table>";
		$p.show("listmod","block");
		$p.print("listmod",l_s);
		$p.setClass("rootdirlist","sellist");
	},
	initDir:function(v_id,v_seq)
	{
		// Initialize modules directory menu
		var l_s="";
		if (__portaldirtype=="module")
		{
			l_s+=admPortals.portalBar(v_id);
		}
		admPortals.activateBtn(2);
		l_s+="<table border='0'><tr>";
		l_s+="<td><div id='level1' class='dirdivi' style='width:230px;'></div></td>";
		l_s+="<td><div id='level2' class='dirdivi' style='width:200px;'></div></td>";
		l_s+="<td><div id='level3' class='dirdivi' style='width:200px;'></div></td>";
		l_s+="<td><div id='level4' class='dirdivi' style='width:170px;'></div></td>";
		l_s+="</tr></table>"+$p.img("",7,7)+"<br />";
		$p.show("listmod","block");
		$p.print("listmod",l_s);
		$p.print("level1","Chargement ...");
		admPortals.getDir(v_id,1);
		$p.setClass("rootdir"+v_id,"sellist");
		return false;
	},
	getDir:function(v_cat,v_level,v_norefresh)
	{
		// Open the modules directory
		if (!v_norefresh) v_norefresh=false;
		admPortals.levCatid[v_level]=v_cat;
		admPortals.currLevel=v_level;
		getXml(padmin["xml_portals_dir"]+"?catid="+v_cat,admPortals.displayDir,new Array(v_level,v_cat,v_norefresh));
	},
	displayDir:function(response,vars)
	{
		var l_s="";
		if (response.getElementsByTagName("dir")[0]||response.getElementsByTagName("portal")[0])    {
			l_s+="<table cellpadding='0' cellspacing='1' border='0' style='width:90%'>";
			if (response.getElementsByTagName("dir")[0])    {
				var l_i=0,l_dirid;
				while (response.getElementsByTagName("dir")[l_i])
				{
					var l_result=response.getElementsByTagName("dir")[l_i];
					l_dirid=$p.ajax.getVal(l_result,"dirid","int",false,0);
					l_dirname=$p.ajax.getVal(l_result,"dirname","str",false,"...");
					l_s+="<tr><td id='dir"+l_dirid+"' class='catopti' onmouseover='catOptOver(\""+l_dirid+"\")' onmouseout='catOptOut(\""+l_dirid+"\")'> <a href='#' class='menul' onclick='admPortals.catSel(\""+l_dirid+"\",\""+$p.string.removeCot(l_dirname)+"\");admPortals.getDir(\""+l_dirid+"\","+(vars[0]+1)+");return false;'>"+l_dirname+"</a></td></tr>";
					l_i++;
				}
			}
			if (response.getElementsByTagName("portal")[0]) {
				var l_i=0,l_itemid;
				while (response.getElementsByTagName("portal")[l_i])
				{
					var l_result=response.getElementsByTagName("portal")[l_i];
					l_itemid=$p.ajax.getVal(l_result,"id","int",false,0);
					l_s+="<tr><td>"+$p.img("portal.gif",16,16,"","imgmid")+" <a href='#' class='menul' onclick='admPortals.loadPortal("+l_itemid+");return false;'>"+$p.ajax.getVal(l_result,"name","str",false,"--")+"</a></td></tr>";
					l_i++;
				}
			}
			l_s+="</table>";
		}
		else    {   l_s+=lg("lblNoPortal");  }

		$p.setClass("level"+vars[0],"dirdiva");
		$p.print("level"+vars[0],l_s);
		//clear unused div
		if (!vars[2]) {for (var i=vars[0]+1;i<5;i++){$p.print("level"+i,"");$p.setClass("level"+i,"dirdivi");};}
		catOptSel(vars[1],vars[0]-1);
	},
	hideFrame:function()
	{
		$p.app.pages.clean($p.get("newmod"));
	},
	loadPortal:function(v_id)
	{
		admPortals.hideFrame();
		$p.print("newmod","<iframe id='frm' src="+padmin['frm_portal_modify']+"?id="+v_id+" width='980' height='300' frameborder='no' marginwidth='0' marginheight='0' style='border:1px solid #efefef'></iframe>");
	},
	overview:function(v_id)
	{
		$p.show("modules","none");
		$p.app.startLoading();
		$p.print("moduleshdr","Portal overview [<a href='#' onclick='return admPortals.overviewHide();'>Fermer</a>]");
		$p.app.pages.load(padmin["xml_portal"]+'?id='+v_id);
	},
	overviewHide:function()
	{
		$p.app.pages.clean($p.app.pages.root);
		$p.print("moduleshdr","");
	},
	getNbModule:function()
	{
		getXml(padmin["xmlnbportals"],admPortals.updateVar,"btn0nb");
	},
	getNbValidate:function()
	{
		getXml(padmin["xmlnbportals_tovalidate"],admPortals.updateVar,"btn1nb");
	},
	updateVar:function(response,vars)
	{
		$p.get(vars).innerHTML="("+$p.ajax.getVal(response,"return","str",false,"?")+")";
	},
	refreshGroup:function(v_prev)
	{
		admPortals.init();
	}
}





tab = new Array;

//************************************* ADMIN  FUNCTIONS ***************************************************************************************************************
/*
    Class: $p.admin

         Admin functions

    file: admin.js

*/
$p.admin={
    subTabs:{},
    key:'',
    md5key:'',
    activity:0,
    groupsAccess:[],
    userDBid:0,
    /*
		Function : set
                            $p.admin.login : connect user
		Parameters:

			v_form - form used to get user connection information
			v_function - function called when connected
			v_type - connection type
	*/
	login:function(v_form,v_type)
	{
		// connect to profile
        var username = v_form.username.value;
        
        username = $p.string.replacePlus(username);
		var l_connStr = "u="+username+"&pass="+$p.string.esc(v_form.password.value);
		if (v_form.autoconn && v_form.autoconn.checked)
            l_connStr+="&auto=1";

		if (v_type) l_connStr+="&rtype=1";
		if (username=="") 
            $p.app.alert.show(lg("lblEmailChk")+".\r\n");
		else
        {
            
			$p.ajax.call(padmin["scr_connect"],
				{
					'type':'execute',
					'variables':l_connStr,
					'alarm':true,
					'forceExecution':true,
					'callback':
					{
						'function':$p.admin.controlLogin
					}
				}
			);
		}
		return false;
	},
    /*
                Function: $p.admin.controlLogin
                                        
                                Control login identification
	*/    
    controlLogin:function(v_sess)
    {
		if (v_sess!=indef)  {
			if (v_sess=="admin")    {
				$p.url.openLink(padmin["admin_index"]);
			}
			else    {
				$p.app.alert.show(lg('adminOnly'));
           }
		}
        return false;
    },
    /*
                Function: $p.admin.init
                                        
                                Initialise admin panel
	*/    
	init:function()
	{
        $p.app.getVersion();
        $p.app.loadStyles();
        $p.admin.tools.emptyAll();
        $p.admin.widgets.loadWidgetsInfos();
        $p.admin.users.loadUsersInfos();
        $p.admin.pages.loadPagesInfos();
        $p.admin.tabs.init(); //load the admin tabs (from the database)
        $p.admin.tabs.defineAllTabs();
        $p.admin.loadAllSubTabs(); //load the tabs subTabs  
        $p.app.mainMenu(); //load top right horizontal menu
	},
    /*
		Function: $p.admin.setState 
        
                            define the page Portaneo have to load when next user connection
                            
                            function called at application opening
                            
                            write javascript in cookie, javascript will be evaluate later (lastate=id)
                            
		Parameters:

			 v_fct - id last page
	*/
	setState:function(v_fct)
	{
		if ($p.app.user.id>0 || $p.app.user.type == 'A') {
            $p.cookie.write("admlaststate="+v_fct);
        }
	},
    /*
                Function: $p.admin.loadAllSubTabs
                                        
                         Define all the tabs sub menus functions
	*/ 
    loadAllSubTabs:function()
    {
        //ajax call to load subTabs infos
        $p.ajax.call(padmin["xml_get_subtabs"],
			{
				'type':'load',
                'forceExecution':true,
				'callback':
				{
					'function':$p.admin.defineAllSubTabs
				}
			}
		);  
    },
    defineTabsClass:function()
    {
        var tabsClass = {
                            'comtab':'$p.admin.communication',
                            'statstab':'$p.admin.stats',
                            'configstab':'$p.admin.config',
                            'pagestab':'$p.admin.pages',
                            'userstab':'$p.admin.users',
                            'modulestab':'$p.admin.widgets',
                            'applicationtab':'$p.admin.application'
                       };
        return tabsClass;
    },
    /*
                Function: $p.admin.defineAllSubTabs
                                        
                        Define the sub tabs informations
                
                Parameters:
                
                        response - XML object
                        vars - 
	*/ 
    defineAllSubTabs:function(response,vars)
    {
        var i=0;
        var tabTmp=new Array;
        var oldTabname="";
        
        var tabsFunc=$p.admin.defineTabsClass(); 
        while (response.getElementsByTagName("subtab")[i])
		{
			var l_result=response.getElementsByTagName("subtab")[i];
			var st_tabname=$p.ajax.getVal(l_result,"tabname","str",false,"");
			var st_fctname=$p.ajax.getVal(l_result,"fctname","str",false,"");
			var st_label=$p.ajax.getVal(l_result,"label","str",false,"");
			var st_description=$p.ajax.getVal(l_result,"description","str",false,"");        
            var ajaxFunction="'"+tabsFunc[st_tabname]+"."+st_fctname+"()'";

            if (oldTabname=="") { oldTabname=st_tabname; }
            if (oldTabname!=st_tabname) {  
                $p.admin.subTabs[oldTabname]=tabTmp;
                var tabTmp=new Array;  
                var oldTabname=st_tabname; 
                tabTmp.push({'label':st_label,'fct':ajaxFunction,'description':st_description});
            }
            else  {     
                tabTmp.push({'label':st_label,'fct':ajaxFunction,'description':st_description});    
            }
            i++;
        }
        $p.admin.subTabs[oldTabname]=tabTmp; 
        //$p.app.tabs.open(0);
        //in casual situation, tab 0 cannot be set in cookie, but if case happens, it cannot  be harmful
        var regexp = /\$p\.app\.tabs\.open\(0\)/i;
        if (   !$p.cookie.get("admlaststate").match(regexp) ) {
            eval($p.cookie.get("admlaststate"));
        }
    },
    /*
                Function: $p.admin.buildTableContent
                                        
                         Build a table and its content
                
                Parameters:
                
                        tableContent - array of contents
                        
                Returns:
                
                        HTML code
	*/ 
    buildTableContent:function(tableContent)
    {
        var l_s='';
        l_s+='<table cellpadding="2" cellspacing="0" border="0">';
        for (var label in tableContent) {
            l_s+='<tr><td class="label">'+tableContent[label]['label']+'</td><td><strong>'+tableContent[label]['fct']+'</strong></td></tr>'; 
        } 
        l_s+='</table>';
        
        return l_s;
    },
    /*
         Function: $p.admin.buildSubTabs
         
                     Build the subTabs
                    
         Parameters:
         
                    v_tab - tab hash => v_tab['label'] , v_tab['fct'] , v_tab['description'] , v_tab['tabname']
                    tabname - tab name
         */ 
    buildSubTabs:function(v_tab,tabname)
    {
        var l_s="";
        for (var i=0;i<v_tab[tabname].length;i++)
        {
            l_s+='<div class="box tabsfunctions">\
                     <h3><img src="../images/puce.gif" /><a href="#" onclick="eval('+v_tab[tabname][i]['fct']+');return false;">'+lg(v_tab[tabname][i]['label'])+'</a></h3>\
                     <p>'+lg(v_tab[tabname][i]['description'])+'</p>\
                  </div>'; 
        }  
        $('content').innerHTML+=l_s;
    },
	fillBreadCrumbs: function(v_s)
	{
		$p.print('admin_breadcrumbs',v_s);
	},
    /*
         Function: $p.admin.cacheGenerateAll
         
                     Generate all the cache
         
         Returns:
         
                    false
         */  
    cacheGenerateAll:function()
    {   
        $p.ajax.call(padmin["scr_cache_generate_all"],
            {
                'type':'execute',
                'forceExecution':true,
                'callback':
                {
                    'function':$p.admin.generateConfigFiles
                }
            }
        );
        return false;
    },
    /*
                Function: $p.admin.generateConfigFiles
                
                                Regenerate the config files from the database
         
                Returns:
         
                                false
         */
    generateConfigFiles:function(response,vars)
    {
        //"$p.app.pages.change("+v_prof+")"
        if ( $p.app.tabs.sel && $p.app.tabs.sel > 0) {
            $p.admin.setState("$p.app.tabs.open("+$p.app.tabs.sel+")");
        }
        $p.ajax.call(padmin["scr_config_generate_configfiles"],
            {
                'type':'execute',
                'forceExecution':true,
                'callback':
                {
                    'function':document.location.reload()
                }
            }
        );
        return false;   
    }
}

//************************************* USER  FUNCTIONS ***************************************************************************************************************
/*
    Class: $p.admin.users
         users functions
*/
$p.admin.users={
    usersInfos:[],
    usersSubTabs:[],
    tabname:'',
	groupid:0,
	groupname:0,
	levGroupid:[],
	currLevel:0,
	selId:0,
    keys:[],
    selGroup:[],
    groups:[],
    isNew:true,
    total:1,
    selGroupId:-1,
    selAvailGroupId:-1,
    nbCriterias:0,
    nbUserCriterias:0,
    criteriasTab:{}, // list of all criterias
    userCriterias:{}, // user criterias informations
    userTabs:{},
	init:function()
	{
		// Initialize user lists
		$p.admin.users.menu(1,1);
		$p.admin.users.loadAll(1);
		$p.admin.users.getNbUsers();
        //$p.admin.activeTab(1);
	},
	menu:function(type,v_selOption)
	{
        if (v_selOption == indef)
			v_selOption = 1;
		
        var l_s = '<div class="feature">';
    	if (type==1)    {
            
            l_s += $p.html.buildFeatureMenu(v_selOption,[
                    {'id':1,'fct':'$p.admin.users.menu(1,1);$p.admin.users.loadAll(1);return false;','label':lg("users")+" <span id='listlinknb'></span>",'icon':''},
                    {'id':2,'fct':'$p.admin.users.menu(1,2);$p.admin.users.initGroup(0);return false;','label':(__useGroup ? lg("lblUsersbyGroup") : ''),'icon':''}
                ]);
//			var l_s="<a id='listlink' class='optlist' style='font-size:11pt;font-weight:bold;' href='#' onclick='$p.admin.users.loadAll(1);return false;'>"+lg("users")+" <span id='listlinknb'></span></a>";
//			if (__useGroup) l_s+=" | <a id='dirlink' class='optlist' style='font-size:11pt;font-weight:bold;' href='#' onclick='$p.admin.users.initGroup(0);return false;'>"+lg("lblUsersbyGroup")+"</a>";
//			return l_s;
		}
		else if (type==2)   {
            l_s += $p.html.buildFeatureMenu(v_selOption,[
                    {'id':1,'fct':'$p.admin.users.menu(2,1);$p.admin.users.displayCriteria();return false;','label':lg("lblInfosDefine")+" <span id='listlinknb'></span>",'icon':''},
                    {'id':2,'fct':'$p.admin.users.menu(2,2);return false;','label':lg("lblInfosAdd"),'icon':''},
                    {'id':3,'fct':'$p.admin.users.menu(2,3);return false;','label':lg("lblUsersInfos"),'icon':''}
                ]);
//			var l_s="<a id='listlink' class='optlist' style='font-size:11pt;font-weight:bold;' href='#' onclick=$p.admin.users.displayCriteria();return false;>"+lg("lblInfosDefine")+" <span id='listlinknb'></span></a>
//            | <a id='dirlink' class='optlist' style='font-size:11pt;font-weight:bold;' href='#' onclick='return false;'>"+lg("lblInfosAdd")+"</a>
//            | <a id='dirlink' class='optlist' style='font-size:11pt;font-weight:bold;' href='#' onclick='return false;'>"+lg("lblUsersInfos")+"</a>";
//			return l_s;		
		}
        l_s += '</div>';
        
        $p.print("optmod",l_s);
	},
    criteriaObj:function(id,label,type,options,mandatory,editable)
    {
    	this.id=id;
    	this.label=label;
    	this.type=type;
    	this.options=options;
    	this.mandatory=mandatory;
    	this.editable=editable;
    },
	deleteCriteria:function(c_id)
	{
		executescr(padmin["scr_user_criteriadelete"],"id="+c_id,indef,true,$p.admin.users.loadExistingCriteria);
	},
	loadExistingCriteria:function()
	{
		getXml(padmin["xml_user_loadexistingcriteria"],$p.admin.users.displayExistingCriteria,indef,'html');
	},
	displayExistingCriteria:function(response,vars)
	{
		$p.print('ExistingCriterias',response);
	},
	displayCriteria:function()
	{
		var l_s=lg("valuesFieldsForTypes")+"<br /> ";
		l_s+="<font color='red'>NB :</font>"+lg("separateWithA")+" <font color=red>;</font> <br />Example (type=radiobox) : <b>yes;no;sometimes</b>  "+lg("willGenerateThis")+" yes<input type=radio value='yes' name='sample' />no<input type=radio value='no' name='sample'/>sometimes<input type=radio value='sometimes'name='sample' /><br /><br />";
		var noDisplay="";			
		if ($p.admin.users.keys.length>0)
		{
			for (var i=0;i<$p.admin.users.keys.length;i++)
			{
				noDisplay="";
				l_s+="<br /><table cellpadding='2' cellspacing='0' border='0'><tbody>";
			    l_s+="<tr><td>"+lg('lblLabel')+" : </td><td><input type='text' name='label"+i+"' value='"+$p.admin.users.keys[i].label+"' size='20' maxlength='30' />&nbsp;&nbsp;<a href='#' onclick='$p.admin.users.suppCriteria("+i+")'>"+$p.img("ico_suppress.gif",13,10,lg("lblSuppress"))+"</a></td></tr>";
				l_s+="<tr><td>"+lg('lblType')+" : </td><td><select name='type"+i+"' onchange=$p.admin.users.displayOptions(this.value,"+i+");><option value='1'"+($p.admin.users.keys[i].type==1?" selected='selected'":"")+">Text</option><option value='2'"+($p.admin.users.keys[i].type==2?" selected='selected'":"")+">List</option><option value='3'"+($p.admin.users.keys[i].type==3?" selected='selected'":"")+">Checkbox</option><option value='4'"+($p.admin.users.keys[i].type==4?" selected='selected'":"")+">Radio</option><option value='5'"+($p.admin.users.keys[i].type==5?" selected='selected'":"")+">Textarea</option></select></td></tr>";
				if ($p.admin.users.keys[i].type==1 || $p.admin.users.keys[i].type==5)		var noDisplay="style='display:none'";
				l_s+="</tr><td>"+lg('lblValues')+" :</td><td><input type='text' "+noDisplay+" size='40' name='options"+i+"' value='"+$p.admin.users.keys[i].options+"'/></td></tr>";
				l_s+="<tr><td>"+lg('lblMandatory')+" : </td><td><select name='mandatory"+i+"'><option value='1'"+($p.admin.users.keys[i].mandatory==1?" selected='selected'":"")+">"+lg("yes")+"</option><option value='0'"+($p.admin.users.keys[i].mandatory==0?" selected='selected'":"")+">"+lg("no")+"</option></select></td></tr>";
				l_s+="<tr><td>"+lg('lblEditable')+" : </td><td><select name='editable"+i+"'><option value='1'"+($p.admin.users.keys[i].editable==1?" selected='selected'":"")+">"+lg("yes")+"</option><option value='0'"+($p.admin.users.keys[i].editable==0?" selected='selected'":"")+">"+lg("no")+"</option></select></td></tr>";
				l_s+="</tbody></table><hr width='35px'>";
			}	
		}
		l_s+="<br />";
		l_s+="<table cellpadding='2' cellspacing='0' border='0'><tbody>";
		l_s+="<tr><td>"+lg('lblLabel')+" : </td><td><input type='text' name='label' value='' size='20' maxlength='30' /></td></tr>";
		l_s+="<tr><td>"+lg('lblType')+" : </td><td><select name='type' onchange=$p.admin.users.displayOptions(this.value,indef) ><option value='1' selected='selected'>Text</option><option value='2'>List</option><option value='3' >Checkbox</option><option value='4' >Radio</option><option value='5' >Textarea</option></select></td></tr>";
		l_s+="</tr><td>"+lg('lblValues')+" :</td><td><input type='text' size='40' name='options' style='display:none' /></td></tr>";
		l_s+="<tr><td>"+lg('lblMandatory')+" : </td><td><select name='mandatory'><option value='1' selected='selected'>"+lg("yes")+"</option><option value='0'>"+lg("no")+"</option></select></td></tr>";
		l_s+="<tr><td>"+lg('lblEditable')+" : </td><td><select name='editable'><option value='1' selected='selected'>"+lg("yes")+"</option><option value='0'>"+lg("no")+"</option></select></td></tr>";
		l_s+="</tbody></table><hr width='35px'>";
		
		l_s+="<br /><input type='button' value='"+lg("addCriteria")+"' onclick='$p.admin.users.addCriteria()' />";
		$p.print("listmod",l_s);		
		l_s="<br /><input type='submit' value='"+lg("lblBtnValid")+"' onclick='$p.admin.users.setUsersInfosConfig();return false;' />";
		$p.print("listmod2",l_s);
	},	
	displayOptions:function(l_value,l_i)
	{
		if (l_i==indef) {
			if ( (l_value==2) || (l_value==3) || (l_value==4) ) { 
                document.forms[0].options.style.display="block"; 
            }
			else    {
					document.forms[0].options.value="";
					document.forms[0].options.style.display="none";
			}
		}	
		else {
			var optName = "options"+l_i;
			if ( (l_value==2) || (l_value==3) || (l_value==4) ) {
                document.forms[0].elements[optName].style.display="block";
            }
			else {	
				document.forms[0].elements[optName].value="";
				document.forms[0].elements[optName].style.display="none";
			}		
		}
	},
	addCriteria:function()
	{
		l_form=document.forms["formCritere"];
		$p.admin.users.keys[$p.admin.users.keys.length]=new $p.admin.users.criteriaObj($p.admin.users.keys.length,l_form.label.value,l_form.type.value,l_form.options.value,l_form.mandatory.value,l_form.editable.value);
		$p.admin.users.displayCriteria();
	},
	suppCriteria:function(v_id)
	{
		$p.admin.users.keys.splice(v_id,1);
		$p.admin.users.displayCriteria();
	},	
	createHiddenField:function()
	{
		document.formCritere.totalCriteria.value=$p.admin.users.keys.length;
	},
	controlForm:function()
	{
		var nbErrLabel=0;
		var nbErrValues=0;
		//Control for each criteria
		for (var i=0;i<$p.admin.users.keys.length;i++)
		{
			var eltLabel='label'+i;
			var eltValues='options'+i;
			var eltType='type'+i;
			var fieldLabel=document.formCritere.elements[eltLabel].value;
			var fieldValues=document.formCritere.elements[eltValues].value;
			var fieldType=document.formCritere.elements[eltType].value;	
			//empty label control
			if (fieldLabel=="") {
                nbErrLabel++;
            }
			//control the values field
			if ( (fieldType==2) || (fieldType==3) || (fieldType==4) )   {
				if (fieldValues=="")   {
                    nbErrValues++;
                }
				else    {
					//control the number of 
					var tabValues = fieldValues.split(";");	
					var tabValuesLen = tabValues.length;
					if (tabValues.length<=1){ nbErrValues++; }
					else {
						//if the string finishes with ';'
						if (tabValues[tabValuesLen-1]=="") { nbErrValues++; }
					}
				}	
			}
		}

		//error messages
		if (nbErrLabel!=0)  {
			$p.app.alert.show(lg("Label error"))
			return false;
		}
		else if (nbErrValues!=0)  {
			$p.app.alert.show(lg("Values error"))
			return false;
		}
		else
		return true;	
	},	
	getNbUsers:function()
	{
	getXml(padmin["xmlnbusers"],$p.admin.users.updateVar,"listlinknb");
	},
	updateVar:function(response,vars)
	{
		$p.get(vars).innerHTML="("+$p.ajax.getVal(response,"return","str",false,"?")+")";
	},
	getSearch:function(v_s,v_page)
	{
		// Open the search results
		$p.setClass("dirlink","optlist");
		$p.setClass("listlink","optlist");
		$p.admin.users.hideGroupOptions();
		$p.admin.users.currLevel=0;
		$p.admin.users.hideFrame();
		v_s=$p.string.formatForSearch(v_s);
		$p.print("listmod","Recherche en cours ...");
		getXml(padmin["xml_search_user"]+"?searchtxt="+$p.string.esc(v_s)+"&p="+v_page,$p.admin.users.displaySearch,new Array(v_s,v_page));
	},
	displaySearch:function(response,vars)
	{
		var l_s="",l_result;
		l_s+=$p.admin.users.userBar();
		l_s+="<table cellpadding='0' cellspacing='0'>";
		if (response.getElementsByTagName("nbres")[0])
		{
			var l_id;
			l_s+="<tr><td><br /><b>"+lg("searchUser")+" : "+vars[0]+"</b> <a href='#' onclick='return $p.admin.users.loadAll(1)'>"+lg("returnWholeUsersList")+"</a><br /></td></tr>";
			l_s+="<tr><td><table width='960'>";
			l_s+="<tr><td valign='top' class='dirdiva'><table cellpadding='0' cellspacing='0' width='900'><tr><td valign='top'><table cellpadding='1' cellspacing='1' width='300'>";
			var i=0;
			while (response.getElementsByTagName("user")[i])
			{
				l_result=response.getElementsByTagName("user")[i];
				l_s+="<tr><td style='border-bottom:1px solid #c6c3c6'>"
                        +$p.img("ico_user_"+$p.ajax.getVal(l_result,"typ","str",false,"I")+".gif",11,9)
                        +" <a class='menul' href='#' onclick='$p.admin.users.loadUser("+$p.ajax.getVal(l_result,"id","int",false,0)+")'>"
                        +$p.ajax.getVal(l_result,"name","str",false,$p.ajax.getVal(l_result,"username","str",false,"???"))
                        +"</a></td></tr>";
				i++;
				if (i%7==0&&i<21) l_s+="</table></td><td valign='top'><table cellpadding='1' cellspacing='1' width='300'>"
			}
			if (i==0) l_s+="<tr><td>"+lg("lblSrchNoUser")+"</td></tr>";
			l_s+="</table></td></tr>";
			l_s+="</table><br /><table width='100%'><tr>";
			if (vars[1]!=0){l_s+="<td><a href='#' onclick=\"$p.admin.users.getSearch('"+vars[0]+"',"+(parseInt(vars[1])-1)+");return false;\">"+$p.img("ico_previous2.gif",12,11,lg("lblPrevUsers"))+" "+lg("lblPrevUsers")+"</a></td>";}
			if (i==21){l_s+="<td align='right'><a href='#' onclick=\"$p.admin.users.getSearch('"+vars[0]+"',"+(parseInt(vars[1])+1)+");return false;\">"+lg("lblNextUsers")+" "+$p.img("ico_next2.gif",12,11,lg("lblNextUsers"))+"</a></td>";}
			l_s+="</tr></table></td></tr></table>";
			l_s+="<br />";
		}
		else {
			l_s+="<tr><td><table><tr><td style='color:#ff0000'>"+lg("lblSrch3car");
		}
		l_s+="</td></tr></table></td></tr></table>";
		$p.show("listmod","block");
		$p.print("listmod",l_s);
	},
	userBar:function()
	{
		var l_s="<div style='padding:5px;'><form name='search' onsubmit='$p.admin.users.getSearch(document.search.searchtxt.value,0);return false;'>"+$p.img("ico_search.gif",13,13)+" "+lg("searchUser")+" : <input class='thinbox' name='searchtxt' type='text' size='20' style='height:20px' value='' /><INPUT type='submit' name='buts' class='btn' value='Go' />";
		l_s+="&nbsp; | &nbsp; <a href='#' onclick='return $p.admin.users.newUser();'>"+$p.img("ico_adm_add.gif",12,12)+" "+lg("addUser")+"</a>";
		l_s+=" &nbsp; | &nbsp; "+$p.img("ico_user_I.gif",11,9)+" "+lg("users")+" . "+$p.img("ico_user_A.gif",11,9)+" "+lg("administrators");
		l_s+="</form></div>";
		return l_s;
	},
	loadAll:function(v_page,v_keepframe)
	{
		$p.setClass("dirlink","optlist");
		$p.setClass("listlink","sellist");
		$p.admin.users.currLevel=0;
		if (v_keepframe==indef || !v_keepframe) $p.admin.users.hideFrame();
        //var groupList=$p.admin.users.convertTabGroupAccessToString();
		getXml(padmin["xml_all_users"]+"?p="+v_page,$p.admin.users.displayAll,v_page);
		$p.admin.users.hideGroupOptions();
		return false;
	},
	displayAll:function(response,vars)
	{
		var l_s="";
		l_s+=$p.admin.users.userBar();
		l_s+="<table cellpadding='0' cellspacing='0'>";
		if (response.getElementsByTagName("page")[0])
		{
			var l_id;
			l_s+="<tr><td><table width='960'>";
			l_s+="<tr><td valign='top' class='dirdiva'><table cellpadding='0' cellspacing='0' width='900'><tr><td valign='top'><table cellpadding='1' cellspacing='1' width='300'>";
			var lastvalid=0;
			for (var i=0;i<21;i++)
			{
				if (response.getElementsByTagName("user")[i])
				{
					l_result=response.getElementsByTagName("user")[i];
					var l_type=$p.ajax.getVal(l_result,"typ","str",false,"I");
					l_s+="<tr><td style='border-bottom:1px solid #c6c3c6'>"
                            +$p.img("ico_user_"+l_type+".gif",11,9)
                            +" <a class='menul' href='#' onclick='$p.admin.users.loadUser("+$p.ajax.getVal(l_result,"id","int",false,0)+",\""+l_type+"\")'"+((l_type=='B' || l_type=='J')?' style="color:#c6c3c6"':'')+">"
                            +$p.ajax.getVal(l_result,"name","str",false,$p.ajax.getVal(l_result,"username","str",false,"???"))
                            +"</a></td></tr>";
					lastvalid=i;
				}
				else
				{
					l_s+="<tr><td></td></tr>";
				}
				if (i%7==6&&i<20) l_s+="</table></td><td valign='top'><table cellpadding='1' cellspacing='1' width='300'>"
			}
			if (lastvalid==0) l_s+="<tr><td>"+lg("lblNoUser")+"</td></tr>";
			l_s+="</table></td></tr></table>";
			l_s+="<br /><table width='100%'><tr>";
			if (vars>1){l_s+="<td><a href='#' onclick='$p.admin.users.loadAll("+(vars-1)+");return false;'>"+$p.img("ico_previous2.gif",12,11,lg("lblPrevUsers"))+" "+lg("lblPrevUsers")+"</a></td>";}
			if (lastvalid==20){l_s+="<td align='right'><a href='#' onclick='$p.admin.users.loadAll("+(vars+1)+");return false;'>"+lg("lblNextUsers")+" "+$p.img("ico_next2.gif",12,11,lg("lblNextUsers"))+"</a></td>";}
			l_s+="</tr></table>";
		}
		else
			l_s+=lg("lblDisplayErr");
		
		l_s+="</td></tr></table>";
		$p.show("listmod","block");
       
		$p.print("listmod",l_s);
	},
	initGroup:function(v_id)
	{
		$p.print("newmod","");
		$p.setClass("dirlink","sellist");
		$p.setClass("listlink","optlist");
		var l_s="<table>";
		l_s+="<td><div id='level1' class='dirdivi' style='width:230px;'></div></td>";
		l_s+="<td><div id='level2' class='dirdivi' style='width:200px;'></div></td>";
		l_s+="<td><div id='level3' class='dirdivi' style='width:200px;'></div></td>";
		l_s+="<td><div id='level4' class='dirdivi' style='width:170px;'></div></td>";
		l_s+="</tr></table><br />";
		$p.show("listmod","block");
		$p.print("listmod",l_s);
		$p.print("level1","Chargement ...");
		$p.admin.users.loadGroup(v_id,1);
		$p.admin.users.groupSel(0,"");
	},
	loadGroup:function(v_group,v_level,v_norefresh)
	{
		// Open the modules directory
		if (!v_norefresh) v_norefresh=false;
		$p.admin.users.levGroupid[v_level]=v_group;
		$p.admin.users.currLevel=v_level;
		getXml(padmin["xml_group"]+"?group="+v_group,$p.admin.users.displayGroup,new Array(v_level,v_group,v_norefresh));
	},
	displayGroup:function(response,vars)
	{
		var l_s="";
		if (response.getElementsByTagName("group")[0]||response.getElementsByTagName("user")[0])
		{
			if (response.getElementsByTagName("group")[0])
			{
				var i=0,l_groupid;
				l_s+="<table cellpadding='0' cellspacing='1' border='0' style='width:90%'>";
				while (response.getElementsByTagName("group")[i])
				{
					var l_result=response.getElementsByTagName("group")[i];
					l_groupid=$p.ajax.getVal(l_result,"groupid","int",false,0);
					l_groupname=$p.ajax.getVal(l_result,"groupname","str",false,"...");
					l_s+="<tr><td id='dir"+l_groupid+"' class='diropti' onmouseover='dirOptOver("+l_groupid+")' onmouseout='dirOptOut("+l_groupid+")'>"
                        +$p.img("ico_group.gif",11,9)
                        +" <a href='#' class='menul' onclick='$p.admin.users.groupSel("+l_groupid+",\""+$p.string.removeCot(l_groupname)+"\","+vars[0]+");$p.admin.users.loadGroup("+l_groupid+","+(vars[0]+1)+");return false;'>"
                        +l_groupname+"</a></td></tr>";
					i++;
				}
				l_s+="</table>";
			}
			if (response.getElementsByTagName("user")[0])
			{
				var i=0,l_userid;
				l_s+="<table cellpadding='0' cellspacing='1' border='0' style='width:90%'>";
				while (response.getElementsByTagName("user")[i])
				{
					l_result=response.getElementsByTagName("user")[i];
					l_s+="<tr><td>"
                            +$p.img("ico_user_"+$p.ajax.getVal(l_result,"typ","str",false,"I")+".gif",11,9)
                            +" <a class='menul' href='#' onclick='$p.admin.users.loadUser("+$p.ajax.getVal(l_result,"id","int",false,0)+")'>"
                            +$p.ajax.getVal(l_result,"name","str",false,$p.ajax.getVal(l_result,"username","str",false,"???"))
                            +"</a></td></tr>";
					i++;
				}
				l_s+="</table>";
			}
		}
		else
		{
			l_s+=lg("lblNoGroup")+"/"+lg("lblNoUser");
		}

		$p.setClass("level"+vars[0],"dirdiva");
		$p.print("level"+vars[0],l_s);
		//clear unused div
		if (!vars[2])
		{
			for (var i=vars[0]+1;i<5;i++)
			{
				$p.print("level"+i,"");
				$p.setClass("level"+i,"dirdivi");
			}
		}
		dirOptSel(vars[1],vars[0]-1);
	},
	groupSel:function(v_id,v_name,v_level)
	{
		$p.admin.users.groupid=v_id;
		$p.admin.users.groupname=v_name;
		$p.admin.users.currLevel=(v_level==indef?1:v_level);
		$p.admin.users.showGroupOptions();
	},
	refreshGroup:function(v_prev)
	{
		if ($p.admin.users.currLevel!=0)
		{
			var l_currLevel=(v_prev&&$p.admin.users.currLevel>1)?($p.admin.users.currLevel-1):$p.admin.users.currLevel;
			$p.admin.users.loadGroup($p.admin.users.levGroupid[l_currLevel],l_currLevel,true);
			if (l_currLevel<4) {$p.print("level"+(l_currLevel+1),"");$p.setClass("level"+(l_currLevel+1),"dirdivi");}
			//if group suppressed or changed, hide the options
			if (v_prev) {$p.admin.users.groupSel(0,0);}
		}
		else
		{
			$p.admin.users.loadAll(1,true);
		}
		$p.admin.users.getNbUsers();
	},
	showGroupOptions:function()
	{
		var l_s="";
		if ($p.admin.users.groupid!=0)
		{
			l_s+="<b>"+lg("lblGroupSelected")+" : <font color='#ff0000'>"+$p.admin.users.groupname;
			l_s+="</font></b> | <a href='#' onclick='return $p.admin.users.groupModify($p.admin.users.groupid);'>"+lg("lblGroupModify")+"</a>";
			l_s+=" | <a href='#' onclick='return $p.admin.users.groupSuppress($p.admin.users.groupid);'>"+lg("lblGroupSuppress")+"</a>";
			l_s+=" | <a href='#' onclick='return $p.admin.users.groupMove($p.admin.users.groupid);'>"+lg("lblGroupMove")+"</a>";
			if ($p.admin.users.currLevel<3) l_s+=" | <a href='#' onclick='return $p.admin.users.groupAdd($p.admin.users.groupid);'>+ "+lg("lblGroupAdd")+"</a>";
		}
		else
		{
			l_s+="<b>"+lg("youAreOnGroupRoot")+"</b> | <a href='#' onclick='return $p.admin.users.groupAdd($p.admin.users.groupid);'>+ "+lg("lblRootGroupAdd")+"</a>";
		}
		$p.print("directory",l_s);
	},
	hideGroupOptions:function()
	{
		$p.print("directory","");
	},
	hideFrame:function()
	{
		$p.app.pages.clean($p.get("newmod"));
	},
    /*
                    Function: $p.admin.users.groupModify
                    
                                     Load group informations
                                     
                    Parameters:
                    
                                    v_id - group id
                                    
                    Returns:
                            
                                    false
          */
	groupModify:function(v_id)
	{
		var l_s="";
        $p.admin.users.hideFrame();
        $p.ajax.call(padmin["xml_group_modify"]+"?group="+v_id,
            {
                'type':'load',
                'callback':
                {
                    'function':$p.admin.users.displayGroupModifyForm,
                    'variables':
                    {
                        'id':v_id
                    }
                }
            }	
        );
		return false;
	},
    /*
                    Function: $p.admin.users.displayGroupModifyForm
                    
                                     Display modification group form
                                     
                    Parameters:
                    
                                    response - XML object
                                    vars(hash) - variables
                                    
                    Returns:
                            
                                    false
          */
    displayGroupModifyForm:function(response,vars)
    {
        var groupid=vars['id'];
        var groupname=$p.ajax.getVal(response,"group","str",false,"");
        var l_s='<form name="f"><br />\
                '+lg('modifyGroup')+' <input type="text" name="groupname" maxlength="64" value="'+groupname+'" />\
                <input type="hidden" name="groupid" value="'+groupid+'" />\
                <input type="submit" onclick=\"$p.admin.users.setGroupModify();return false;\" value="'+lg("modify")+'" />\
                </form>';
                
        $p.print("newmod","<h2>"+lg("lblModifyGroup")+"</h2>"+l_s);
        return false;
    },
    /*
                    Function: $p.admin.users.setGroupModify
                    
                                     Set group modifications
                                   
                    Returns:
                            
                                    false
          */
    setGroupModify:function()
    {
        var groupid=document.forms['f'].groupid.value;
        var groupname=document.forms['f'].groupname.value;
        $p.ajax.call(padmin["scr_group_modify"],
            {
                'type':'execute',
                'variables':"groupname="+groupname+"&groupid="+groupid,
                'forceExecution':true,
                'callback':
                {
                    'function':$p.admin.users.displayGroupModified
                }
            }
        );   
		return false;
    },
    /*
                    Function: $p.admin.users.displayGroupModified
                    
                                     Display a message when group sucessfully modified
                                     
                    Returns:
                            
                                    false
          */
    displayGroupModified:function()
    {
        $p.print('newmod',lg("modifProcessed"));
        $p.admin.users.initGroup(0);
        return false;
    },
    /*
                    Function: $p.admin.users.groupAdd
                    
                                     Display the form for adding a new group or sub group
                                     
                    Parameters:
                    
                                    v_id - group id
                                    
                    Returns:
                            
                                    false
          */
	groupAdd:function(v_id)
	{
		var l_s="";
        var lblGroup=lg("addGroup");
        if (v_id!=0)    lblGroup=lg("addSubGroup");
        $p.admin.users.hideFrame();
        l_s+=' <form name="f">\
                '+lblGroup+'\
                <input type="text" name="groupname" maxlength="60" value="" />\
                 <input type="hidden" name="groupid" value="'+v_id+'" /><input type="submit" onclick=\"$p.admin.users.setGroupAdd();return false;\" value="'+lg("add")+'" />\
                </form>';
                
        $p.print("newmod","<h2>"+lg("lblAddGroup")+"</h2>"+l_s);
        document.forms["f"].groupname.focus();
        return false;
    },
    /*
                    Function: $p.admin.users.setGroupAdd
                    
                                     Add the new group in the database

                    Returns:
                            
                                    false
          */
    setGroupAdd:function()
    {
        var groupname=document.forms["f"].groupname.value;
        var groupid=document.forms["f"].groupid.value;
        $p.ajax.call(padmin["scr_group_add"],
            {
                'type':'execute',
                'variables':"groupname="+groupname+"&groupid="+groupid,
                'forceExecution':true,
                'callback':
                {
                    'function':$p.admin.users.displayGroupAdded
                }
            }
        );            
        return false;
    },
    /*
                    Function: $p.admin.users.displayGroupAdded
                    
                                     Display sucessfully message

                    Returns:
                            
                                    false
          */
    displayGroupAdded:function()
    {
        $p.print('newmod',lg("groupadded"));
        $p.admin.users.refreshGroup();
        return false;
    },
    /*
                    Function: $p.admin.users.groupSuppress
                    
                                     Load group information to control before deleting

                    Parameters:

                                     v_id - group id
                                     
                    Returns:
                            
                                    false
          */
	groupSuppress:function(v_id)
	{
		l_response=confirm(lg("msgGroupSup",$p.admin.users.groupname));
		if (l_response==1)
		{
			$p.admin.users.hideFrame();
            $p.ajax.call(padmin["xml_group_suppress"]+"?group="+v_id,
                {
                    'type':'load',
                    'callback':
                    {
                        'function':$p.admin.users.setGroupSuppress,
                        'variables':
                        {
                            'id':v_id
                        }
                    }      
                }	
            );
		}
		return false;
	},
    /*
                    Function: $p.admin.users.setGroupSuppress
                    
                                     Control group information, delete the group if ok
                                     
                    Parameters:

                                     response - XML object
                                     vars(array) - variables
                                     
                    Returns:
                            
                                    false
          */
    setGroupSuppress:function(response,vars)
    {
        var nbgroup=$p.ajax.getVal(response,"nbgroup","int",false,0);
        var nbuser=$p.ajax.getVal(response,"nbuser","int",false,0);
        var v_id=vars['id'];
        var l_s="";
        
        if ((nbuser+nbgroup)>0)    {
        	l_s="<div id='errormessage'>"+lg("groupNotEmpty")+" ("+nbuser+" "+lg("users")+" et "+nbgroup+" "+lg("subGroups")+")</div>";
            $p.print("newmod","<h2>"+lg("lblSuppressGroup")+"</h2>"+l_s);
        }
        else    {
            $p.ajax.call(padmin["scr_group_delete"],
                {
                    'type':'execute',
                    'variables':"groupid="+v_id,
                    'forceExecution':true,
                    'callback':
                    {
                        'function':$p.admin.users.displayGroupSuppressed
                    }
                }
            );
        }
		return false;
    },
    /*
                    Function: $p.admin.users.displayGroupSuppressed
                    
                                     Display sucessfully deleted group message

                    Returns:
                            
                                    false
          */
    displayGroupSuppressed:function()
    {
        $p.print("newmod",lg("groupSuppressed")+" !");
        $p.admin.users.refreshGroup(true);
        return false;
    },
	groupMoveInit:function(){
        var groupid=$p.admin.users.groupid;
        var groupname=$p.admin.users.groupname;
		var l_s="<b>"+lg("lblMoveGroup")+" <font color='#ff0000'>"+groupname+"</font> : </b>"+lg("lblMoveGroupTxt",groupname)+" <input type=hidden name=groupid value='"+groupid+"' /><input type=hidden name=parentid value='0' /><input type=button onclick=$p.admin.users.setGroupMove("+groupid+",'"+groupname+"'); value='"+lg("lblMove")+"' />";
		$p.print("movediv",l_s);
	},
	getNewParent:function(groupid,groupname){
		var l_error=false,l_step=0;
		if ($p.admin.users.currLevel==0){l_error=true;l_step=1;}
		for (var i=1;i<=$p.admin.users.currLevel;i++){
			if (groupid==$p.admin.users.levGroupid[i]){l_error=true;l_step=2;}
		}
		var l_id=$p.admin.users.groupid;
		if (l_id==0){l_error=true;l_step=3;}
		if (l_error){
			alert(lg("lblIncorrectDestGroup")+" ! (err "+l_step+")");
			return false;
		} else {
			document.forms["f"].parentid.value=l_id;
			return true;
		}
	},
    /*
                    Function: $p.admin.users.groupMove
                    
                                    Display the form of group moving
                    
                    Returns:
                    
                                    False
          */
	groupMove:function()
	{
		var l_s="";
        $p.admin.users.hideFrame();
        l_s+='<form name="f">\
              <div id="movediv" style="padding:10px"></div>\
              </form>';
                
        $p.print("newmod","<h2>"+lg("lblMoveGroup")+"</h2>"+l_s);
        $p.admin.users.groupMoveInit();
        return false;
	},
    /*
                    Function: $p.admin.users.setGroupMove
                    
                                    Update the new group hierarchy
                    
                    Parameters:
                    
                                    groupid - source groupid
                                    groupname - source groupname
                    
                    Returns:
                    
                                    False
          */
    setGroupMove:function(groupid,groupname)
    {
        if ($p.admin.users.getNewParent(groupid,groupname))  { 
            var groupid=document.forms['f'].groupid.value;
            var parentid=document.forms['f'].parentid.value;
            $p.ajax.call(padmin["scr_group_move"],
                {
                    'type':'execute',
                    'variables':"groupid="+groupid+"&parentid="+parentid,
                    'forceExecution':true,
                    'callback':
                    {
                        'function':$p.admin.users.displayGroupMoved
                    }
                }
            );            
        }
        return false;
    },
    /*
                    Function: $p.admin.users.displayGroupMoved
                    
                                    Display group sucessfully moved message
          */
    displayGroupMoved:function()
    {
       $p.print("newmod",lg("modifProcessed")+" !");
       $p.admin.users.refreshGroup(true);
    },
    /*
                    Function: $p.admin.users.displaySelectLang
                    
                                    Display a select list with the available languages
                                    
                    Parameters:
                    
                                    sellang - user language
                    
                    Returns:
                        
                                    HTML code
          */
    displaySelectLang:function(sellang)
    {
        var langField="";
        if (__AVLANGS.length==1) {
            langField=__AVLANGS[0]+'<input type="hidden" name="lang" value="'+__AVLANGS[0]+'" />';
        }
        else {
            langField+='<select name="lang">';
            for (var i=0;i<__AVLANGS.length;i++)
            {
                var selected="";
                if (__AVLANGS[i]==sellang)  selected="selected=selected";
                langField+='<option value="'+__AVLANGS[i]+'" '+selected+'>'+__AVLANGS[i]+'</option>';            
            }
            langField+='</select>';
        }
        return langField;
    },
                                 
    displaySelectUsertype:function(usertype)
    {
        var langField="";
        var selected="selected=selected";
        langField+='<select name="usertype" onchange="$p.admin.users.changeType();">';
        langField+='<option value="I" ';
        if (usertype=='I')  langField+=selected;
        langField+=' >'+lg("portalUser")+'</option>';
        langField+='<option value="A" ';
        if (usertype=='A')  langField+=selected;
        langField+=' >'+lg("administrator")+'</option>';   
        langField+='</select>';
        
        return langField;
    },

    /*
                    Function: $p.admin.users.loadUser
                    
                                    Load a specific user informations
          */
	loadUser:function(v_id,type)
	{
		$p.admin.users.userCriterias={};
		$p.admin.users.selGroup=[];
        $p.admin.users.hideFrame();
        $p.admin.users.selId=v_id;
        $p.ajax.call(padmin["xml_get_user_infos"]+"?id="+v_id+"&type="+type,
            {
                'type':'load',
                'callback':
                {
                    'function':$p.admin.users.existingUser
                }
            }
        );
		return false;
    },
    getUserCriteriasParameters:function(response)
    {
        var i=0;
        while (response.getElementsByTagName("criteria")[i])
        {
            var l_result=response.getElementsByTagName("criteria")[i];
            var id=$p.ajax.getVal(l_result,"id","int",false,0);
            var parameters=$p.ajax.getVal(l_result,"parameters","str",false,"");
            $p.admin.users.userCriterias[id]={
                                                 'parameters':parameters
                                             }; 
            i++;
        }
    },
    suppress:function()
    {
    	l_response=confirm(lg("msgUserRemoving"));
    	if (l_response==1)
    	{
            $p.ajax.call(padmin["scr_user_suppress"],
                {
                    'type':'execute',
                    'variables':"id="+$p.admin.users.selId+"&action=delete",
                    'forceExecution':true,
                    'callback':
                    {
                        'function':$p.admin.users.refreshUsersMenu
                    }
                }
            );
    	}
    	return false;
    },
    inactivate:function(){
       $p.ajax.call(padmin["scr_user_suppress"],
            {
                'type':'execute',
                'variables':"id="+$p.admin.users.selId+"&action=inactivate",
                'forceExecution':true,
                'callback':
                {
                    'function':$p.admin.users.refreshUsersMenu
                }
            }
        );
    },
    activate:function(){
       $p.ajax.call(padmin["scr_user_suppress"],
            {
                'type':'execute',
                'variables':"id="+$p.admin.users.selId+"&action=activate",
                'forceExecution':true,
                'callback':
                {
                    'function':$p.admin.users.refreshUsersMenu
                }
            }
        );
    },
    getUserGroups:function(response)
    {
        //get user groups
        var i=0;
		$p.admin.users.selGroup=[];
        while (response.getElementsByTagName("group")[i]) 
        {
            var l_result=response.getElementsByTagName("group")[i];
            var group_id=$p.ajax.getVal(l_result,"g_id","int",false,0);
            var group_name=$p.ajax.getVal(l_result,"g_name","str",false,"--");
			group_name=group_name.substr(1,(group_name.length)-2);
            $p.admin.users.selGroup.push(new $p.admin.widgets.groupObj(group_id,group_name));
            i++;
        } 
    },
    buildUserModifyAddForm:function(type,response)
    {
        var l_s="";
        var lblmail=lg("login");
        if (__accountType=="mail")  lblmail=lg("email");
                   
        var i=0;
        
        $p.admin.users.userTabs=new Array();
        while (response.getElementsByTagName("tab")[i]) 
        {
            var l_result=response.getElementsByTagName("tab")[i];
            var tab_id=$p.ajax.getVal(l_result,"tab_id","int",false,0);
            $p.admin.users.userTabs.push(tab_id);
            i++;
        } 
        
        var username,long_name,user_type,sellang,totalCriterias,password,confirm_password,cpwd;
        if (type=='mod')    {
            $p.admin.users.isNew=false;
            password="xxxxxxxx";
            username=$p.ajax.getVal(response,"username","str",false,"--");
        	long_name=$p.ajax.getVal(response,"long_name","str",false,"--");
        	user_type=$p.ajax.getVal(response,"user_type","str",false,"I");
        	sellang=$p.ajax.getVal(response,"sellang","str",false,"en");
        	totalCriterias=$p.ajax.getVal(response,"totalCriterias","int",false,0);
            $p.admin.users.nbUserCriterias=totalCriterias;
            cpwd=password;
            $p.admin.users.getUserCriteriasParameters(response);
            $p.admin.users.getUserGroups(response); 
        }
        else    {
            $p.admin.users.hideFrame();
            $p.admin.users.selId=0;
            $p.admin.users.isNew=true;
            username="";
        	long_name="";
        	user_type="I";
        	password="";
        	confirm_password="";
        	sellang=__lang;
            cpwd=confirm_password;
        }
        var langField=$p.admin.users.displaySelectLang(sellang);
        var usertypeField=$p.admin.users.displaySelectUsertype(user_type);

        l_s+='<form id="f" name="f">\
              <input type="hidden" value="'+$p.admin.users.selId+'" name="userid">\
                <table cellpadding="10">\
                    <tr><td valign="top">\
                        <table cellpadding="2">\
                        <tr><td colspan="2"><div id="errormessage">'+lg("lblMandatoryFields")+'</div></td></tr>\
                        <tr><td></td></tr>\
                        <tr><td>'+lblmail+'</td>\
                            <td><input type="text" name="username" size="30" maxlength="64" onFocus="$p.admin.widgets.giveFocus();" value="'+username+'" />\
                            &nbsp;&nbsp;<span id="errormessage">*</span>&nbsp;&nbsp;\
                            <input type="button" value="'+lg("verifyAdress")+'" onclick="$p.admin.users.existAccount(document.forms[\'f\'].username.value);" /></td>\
                        </tr>\
                		<tr>\
                			<td></td><td><div id="availibiliy" name="availibiliy"></div></td>\
                		</tr>\
                		<tr><td>'+lg("name")+'</td>\
                		    <td><input type="text" name="long_name" size="30" maxlength="100" value="'+long_name+'" />&nbsp;&nbsp;<span id="errormessage">*</span></td>\
                		</tr>\
                		<tr><td></td><tr>\
                		<tr><td nowrap="nowrap">'+lg("password")+'</td>\
                		    <td><input type="password" name="pass" size="16" maxlength="16"  value="'+password+'" />&nbsp;&nbsp;<span id="errormessage">*</span><td>\
                		</tr>\
                        <tr>\
                            <td nowrap="nowrap">'+lg("confirmPassword")+'</td>\
                            <td><input type="password" name="pass2" size="16" maxlength="16"  value="'+cpwd+'" />&nbsp;&nbsp;<span id="errormessage">*</span><td>\
                        </tr>\
                        <tr><td></td><tr>\
                        <tr><td nowrap="nowrap">'+lg("userType")+'</td>\
                            <td>\
                            '+usertypeField+'\
                            </td>\
                        </tr>\
                        <tr><td nowrap="nowrap">'+lg("language")+'</td>\
                            <td>\
                            '+langField+'\
                            </td>\
                        </tr>\
                        <tr>\
                            <td><input type="hidden" name="nbSpecificFields" value="'+$p.admin.users.nbCriterias+'" /></td>\
                        </tr>';
                        if ($p.admin.users.isNew==true) { l_s+=$p.admin.users.buildCriterias(); }
                        else { l_s+=$p.admin.users.buildUserCriterias(); }
                      l_s+='<tr>\
                            <td></td>\
                            <td><input type="checkbox" name="notify" /> '+lg("notifyUserModification")+'</td>\
                          </tr>\
                          </table></td>\
                          <td valign="top" id="groupdiv">\
                            <table>\
                                <tr>\
                                    <td>'+lg("userGroups")+' :<br /><br /><div id="selgroupdiv"></div></td>\
                                    <td><input type="button" value="<" onclick="return $p.admin.users.addSelectedGroup();" style="font-size: 10pt"><br /><br /><input type="button" value=">" onclick="return $p.admin.users.supSelectedGroup();" style="font-size: 10pt"></td>\
                                    <td>'+lg("availableGroups")+' :<br /><br /><div id="allgroupdiv"><div id="exp0"></div></div></td>\
                                </tr>\
                            </table>\
                            <div id="groupinputs"></div>\
                          </td>\
                          <td valign="top" >\
                            <div id="tabdiv">';
                            if ($p.admin.users.userTabs.length>0 && user_type=='A') { l_s+=$p.admin.users.displayAdminAvailableTabs(); }
                            else { l_s+=$p.admin.users.displayAvailableTabs(); }     
                          l_s+='</div>\
                              </td>\
                            </tr>\
                            <tr>\
                                <td valign="top" colspan="2" align="center">\
                                <input type="hidden" name="user" value="0" />\
                                <div id="validationbtn">';
                                
                                if ($p.admin.users.isNew)  {
                                    l_s+='<input class="btn" id="submitForm" name="submitForm" type="button" onclick="$p.admin.users.setUser();return false;" value="'+lg("add")+'" style="width:200px" />';
                                }
                                else {
                                    l_s+='<input class="btn" id="submitForm" type="button" onclick="$p.admin.users.setUser();return false;" value="'+lg("saveModifications")+'" /><br /><br />';
                                	if (user_type=='I' 
                                        || user_type=='A') {    l_s+='<a href="#" onclick="return $p.admin.users.inactivate();return false;">'+lg("inactivateUser")+'</a>';    }
                                	else {    l_s+='<a href="#" onclick="return $p.admin.users.activate();return false;">'+lg("activateUser")+'</a>';    }
                                	
                                    l_s+='<br /><a href="#" onclick="return $p.admin.users.suppress();return false;"><img src="../images/ico_suppress.gif" /> '+lg("suppressUser")+'</a>';
                                }
                                
                                l_s+='</div>\
                               </td>\
                            </tr>\
                         </table>\
                      </form>';       

        return l_s;
    },
    /*
                    Function: $p.admin.users.existingUser
                    
                                     Display the form to modify a user
                    
                    Parameters:
                    
                                     response - XML object
                                     vars - 
         */
    existingUser:function(response,vars)
    {        
        var l_s=$p.admin.users.buildUserModifyAddForm('mod',response);    
		var selgroupAff="";
        $p.print("newmod","<h2>"+lg("lblEditNewUser")+"</h2>"+l_s);
        $p.admin.users.changeType();
        $p.admin.users.controlGroupsManagement();
    },
    /*
                    Function: $p.admin.users.controlGroupsManagement
                    
                                     Control that an admin can't modify the groups he manages
                    
                    Parameters:
                    
                                     response - XML object
                                     vars - 
         */    
    controlGroupsManagement:function()
    {
        if ($p.admin.userDBid==$p.admin.users.selId) {
            $('groupdiv').style.display="none";
        }
    },
    /*
                    Function: $p.admin.users.newUser
                    
                                     Display the form to add a new user
                    
                    Parameters:
                    
                                     response - XML object
                                     vars - 
         */
	newUser:function()
	{         
		        $p.ajax.call(padmin["xml_get_user_infos"]+"?id=-1",
	            {
	                'type':'load',
	                'callback':
	                {
	                    'function':$p.admin.users.createElementNewsUser
	                }
	            }	
	        );	
	},
	createElementNewsUser:function(response) {
		$p.admin.users.getUserGroups(response);
        $p.admin.users.selGroup=[];
		var l_s=$p.admin.users.buildUserModifyAddForm('add',response);                        
		$p.print("newmod","<h2>"+lg("lblAddNewUser")+"</h2>"+l_s);
		$p.admin.users.changeType();
		$p.admin.users.showSelectedGroups();
	},	
    controlFirstDefaultFields:function()
    {
        //the first 3 fields are mandatory
        for (i=2;i<4;i++)   {
            if (document.forms['f'].elements[i].value=="")	{
                $p.app.alert.show(lg("errorEmptyFieldForm"),3);
                return false;
            }
        }
        //trim on password
        document.forms['f'].pass.value = $p.string.trim(document.forms['f'].pass.value);
        document.forms['f'].pass2.value = $p.string.trim(document.forms['f'].pass2.value);
        //password match
        if (document.forms['f'].pass.value!=document.forms['f'].pass2.value)    {
            $p.app.alert.show(lg("msgSubPassDiff"),3);
            return false;
        }
        //password length
        if (document.forms['f'].pass.value==document.forms['f'].pass2.value)    {
            if (document.forms['f'].pass.value.length<6)    {
                $p.app.alert.show(lg("msgSubPassToShort"),3);
                return false;
            }
        }
        return true;
    },
    /*
                Function: $p.admin.users.setUser
                
                        set user 
                        
                        get data from form then send to server
                        
                        No parameters
            */
    setUser:function()
    {
        var formElements=document.forms['f'].elements.length;
        if($p.admin.users.controlFirstDefaultFields() && $p.network.controlMandatoryFields(formElements,'f',4))
        //if($p.admin.users.controlMandatoryFields(formElements,'f'))
        {
            //get form values
            var parametersList="";
            var userid=document.forms['f'].userid.value;
            var username=document.forms['f'].username.value;
            username = $p.string.replacePlus(username);
            var long_name=document.forms['f'].long_name.value;
            var pass=document.forms['f'].pass.value;
            var usertype=document.forms['f'].usertype.value;
            var lang=document.forms['f'].lang.value;
			var notify=document.forms['f'].notify.checked;
            var nbSpecificFields=document.forms['f'].nbSpecificFields.value;
            var criteriasList="";
            var groupList="";
            var tabs=new Array();
            
            //get admin available tabs
            if (usertype=='A') {                
                //get available  admin tabs
                for (var w in $p.admin.tabs.tabsName) 
                {
                	if (typeof(w) != 'undefined')
                	{
	                    if (w!="0") {
	                    	
	                    	if (typeof(document.forms['f'].elements['tab'+w]) !='undefined') 
	                    	{   
			                    if (document.forms['f'].elements['tab'+w].checked) {   
		                            tabs.push(document.forms['f'].elements['tab'+w].value); 
		                        }
	                    	}
	                    }
                	}
                }
            }
            //get groups
		/*	if($p.admin.users.selGroup.length<=0 && usertype=='A') {
				alert(lg("adminSelectGroup"));
			} else {
			*/
	            if ($p.admin.users.selGroup.length>0) { 
	                for (var i=0;i<$p.admin.users.selGroup.length;i++)
	                { 
	                    groupList+="group"+i+"="+$p.admin.users.selGroup[i].id+"&";
	                }
	            }
	            
	            //get criterias
	            for (var i=1;i<=nbSpecificFields;i++)
	            {
	                var type=$p.admin.users.criteriasTab[document.forms['f'].elements['uniq_id'+i].value]['type'];       
	                var uniq_id=document.forms['f'].elements['uniq_id'+i].value;
	                var id='&uniq_id'+i+'='+uniq_id;
	                if (type=='3') {
	                    var selectList=new Array();
	                    var options=$p.admin.users.criteriasTab[document.forms['f'].elements['uniq_id'+i].value]['options'];
	                    var tabOptions=options.split(';');
	                    for(var j=0;j<tabOptions.length;j++)
	                    {
	                        if (document.forms['f'].elements['check'+tabOptions[j]].checked==1) {
	                            selectList.push(tabOptions[j]);
	                        }
	                    }
	                    criteriasList+="userinfo"+i+"="+selectList+id+"&";
	                }
	                else if (type=='4') {
	                    var options=$p.admin.users.criteriasTab[document.forms['f'].elements['uniq_id'+i].value]['options'];
	                    var tabOptions=options.split(';');
	                    for(var j=0;j<tabOptions.length;j++)
	                    {
	                        if (document.forms['f'].elements['radio'+tabOptions[j]].checked==1) {
	                           criteriasList+="userinfo"+i+"="+tabOptions[j]+id+"&";
	                        }
	                    }
	                }
	                else {  criteriasList+="userinfo"+i+"="+document.forms['f'].elements['userinfo'+i].value+id+"&";    }
	            }
	                                
	            parametersList+="user="+userid+"&username="+username
	                          +"&long_name="+long_name
	                          +"&pass="+pass
	                          +"&lang="+lang
	                          +"&usertype="+usertype
	                          +"&admTabs="+tabs+"&"
							  +"&notify="+notify+"&"
	                          +criteriasList
	                          +groupList
	                          +"nbSpecificFields="+nbSpecificFields;
	           
	            //add user in the database
	            $p.ajax.call(padmin["scr_user_modify_add"],
	                {
	                    'type':'execute',
	                    'variables':parametersList,
	                    'forceExecution':true,	                    
                        'callback':
	                    {
	                        'function':$p.admin.users.refreshUsersMenu
	                    }
	                }
	            );
			//}
            return false;    
        }        
    },  
    /*
                   Function: $p.admin.users.refreshUsersMenu
                    
                                    Refresh the users menu
          */
    refreshUsersMenu:function()
    {
        $p.admin.users.loadUsersInfos();
        $p.admin.users.usersmgmt();
        $p.admin.users.loadAll(1);
        $('newmod').innerHTML=lg("modifProcessed")+" !";
    },
    displayAvailableTabs:function()
    {
        var l_s="";      
        for (var w in $p.admin.tabs.tabsName) 
        {
            if (w>0 && w<8)    {
                l_s+='<input type="checkbox" name="admTabs[]" id="tab'+w+'" checked=checked value="'+w+'" /> '+lg($p.admin.tabs.tabsName[w])+"<br />";
            }
            else if (w!=0 && w>7)    {
				l_s+='<input type="checkbox" name="admTabs[]" id="tab'+w+'" checked=checked value="'+w+'" /> '+$p.admin.tabs.tabsName[w]+'<br />';
            }
        }
        return l_s;        
    },
    displayAdminAvailableTabs:function()
    {
        var l_s="";        
        if ($p.admin.userDBid==$p.admin.users.selId) return '';
        for (var w in $p.admin.tabs.tabsName) 
        {
            var selected="";
            for (var i=0;i<$p.admin.users.userTabs.length;i++)
            {
                if ($p.admin.users.userTabs[i]==w) { selected="checked=checked"; }
            }
            if (w>0 && w<8) {
                l_s+='<input type="checkbox" name="admTabs[]" id="tab'+w+'" value="'+w+'" '+selected+'/> '+lg($p.admin.tabs.tabsName[w])+"<br />";
            }
            else if (w!=0 && w>7) {
				l_s+='<input type="checkbox" name="admTabs[]" id="tab'+w+'" value="'+w+'" '+selected+'/> '+$p.admin.tabs.tabsName[w]+'<br />';
            }
        }
        return l_s;
    },
    controlMandatoryFields:function(n,name)
	{	
        var i=0;
        var total=0; //numbers of unfilled fields
        var minicount=0;
        var tabRadio=[];
        var oldname="";
        var temp;	
            

        for (i=4;i<n;i++)
        {
            switch (document.forms['f'].elements[i].type)
            {
                case 'text':
                    //is the field mandatory and is it filled with something
                    var mandatory = document.forms['f'].elements[i].getAttribute("mandatory");
                    if (mandatory==1 && document.forms['f'].elements[i].value=="")  { total++; }
                    break;
                case 'textarea':
                    //is the field mandatory and is it filled with something
                    var mandatory = document.forms['f'].elements[i].getAttribute("mandatory");
                    if (mandatory==1 && document.forms['f'].elements[i].value=="")    { total++; }
                    break;
                case 'radio':
                    var maxIndex=tabRadio.length;
                    var escap=0;						
                    var currentName = document.forms['f'].elements[i].name;
                    var mandatory = document.forms['f'].elements[i].getAttribute("mandatory");
                    if (mandatory==1)   {
                        if (maxIndex>0)  {
                            for (var j=0;j<maxIndex;j++)    {
                                if (tabRadio[j]==currentName)   { escap=1; }
                                else    { tabRadio[maxIndex]=currentName; }
                            }
                        }
                        else { tabRadio[0]=currentName; }
                        
                        if (escap==0)   {
                            var cpt=i;
                            var correct=0;
                            //if the first element isn't checked						
                                if (document.forms['f'].elements[cpt].checked!=true)  {
                                    //we scan the others
                                    while (document.forms['f'].elements[cpt+1].name==currentName)
                                    {
                                        if (document.forms['f'].elements[cpt+1].checked==true)    { correct=1; }     
                                        cpt++;
                                    }
                                }												
                                else    {correct=1;}
                            if (correct!=1) { total++; }
                        }
                    }
                    break;
                case 'checkbox':								
                    var mycurrentName = document.forms['f'].elements[i].name;
                    var passage=0;
                    if (oldname=="")    {
                        oldname=mycurrentName;
                        passage=1;
                        minicount=0;
                        temp=0;
                    }
                    else if (oldname!=mycurrentName)    {
                        oldname=mycurrentName;
                        passage=1;
                        minicount=0;
                        temp=0;
                    }   
                    //If the field is mandatory, verify that at least one checkbox is checked
                    var mandatory = document.forms['f'].elements[i].getAttribute("mandatory");
                    if (mandatory==1)   {
                        var cpt=i;
                        if (passage==1)  {
                            if (document.forms['f'].elements[i].checked!=true)  { minicount++; }
                            while (document.forms['f'].elements[cpt+1].name==mycurrentName)
                            {
                                temp = document.forms['f'].elements[cpt+1].value;
                                if (document.forms['f'].elements[cpt+1].checked!=true)  { minicount++; }
                                cpt++;
                            }	
                            if (minicount==temp) { total++; }
                        }
                    }
                    break;	
            } //end switch
        } // end for
                        
        if (total!=0)   {
            $p.app.alert.show(lg("errorEmptyFieldForm"),3);
            return false;
        }
        else{
            return true;
        }
    },
    displayUserCriterias:function(id,label,type,options,mandatory,info,parameters)
    {
        var l_s='';
        switch (type)
        {
            //TEXT
            case 1:
            l_s+='<tr><td nowrap="nowrap">'+label+'<input type="hidden" name="uniq_id'+$p.admin.users.total+'" value="'+id+'" /></td>\
                  <td><input type="text" name="userinfo'+$p.admin.users.total+'" value="'+parameters+'" size="20" mandatory="'+mandatory+'" />'+info+'<td>\
                  </tr>';
            break;
            //LIST
            case 2:
            l_s+='<tr><td nowrap="nowrap">'+label+'<input type="hidden" name="uniq_id'+$p.admin.users.total+'" value="'+id+'" ></td>\
                  <td><select name="userinfo'+$p.admin.users.total+'" mandatory="'+mandatory+'" >';  
            var tabOptions=options.split(';');
            for(var i=0;i<tabOptions.length;i++)
            {   
                var selected="";
                if (parameters==i+1) { selected="selected=selected"; }    
                l_s+='<option value="'+(i+1)+'" '+selected+'>'+tabOptions[i]+'</option>';
            }
            l_s+='</select>'+info+'<td></tr>';
            break;
            //CHECKBOX
            case 3:
            var tabParameters=parameters.split(',');   
            l_s+='<tr><td nowrap="nowrap">'+label+'<input type="hidden" name="uniq_id'+$p.admin.users.total+'" value="'+id+'" /></td><td>';
            var tabOptions=options.split(';');
            for(var i=0;i<tabOptions.length;i++)
            {
                var selected="";
                if (tabParameters.contains(tabOptions[i])) { selected="checked=checked"; }    
                if ((i+1)==1)
                    l_s+='<INPUT type="checkbox" name="userinfo'+$p.admin.users.total+'[]" id="check'+tabOptions[i]+'" value="'+(i+1)+'" mandatory="'+mandatory+'" '+selected+'>'+tabOptions[i]+' '+info+'<br />';
                else
                    l_s+='<INPUT type="checkbox" name="userinfo'+$p.admin.users.total+'[]" id="check'+tabOptions[i]+'" value="'+(i+1)+'" mandatory="'+mandatory+'" '+selected+'>'+tabOptions[i]+'<br />'; 
            }
            l_s+='<td></tr>';
            break;
            //RADIO
            case 4:
            l_s+='<tr><td nowrap="nowrap">'+label+'<input type="hidden" name="uniq_id'+$p.admin.users.total+'" value="'+id+'" /></td><td>';
            var tabOptions=options.split(';');
            for(var i=0;i<tabOptions.length;i++)
            {
                var selected="";
                if (tabOptions[i]==parameters) { selected="checked=checked"; }
                if ((i+1)==1)   
                    l_s+='<INPUT type="radio" id="radio'+tabOptions[i]+'" name="userinfo'+$p.admin.users.total+'" value="'+(i+1)+'" mandatory="'+mandatory+'" '+selected+'>'+tabOptions[i]+' '+info+'<br />';
                else
                    l_s+='<INPUT type="radio" id="radio'+tabOptions[i]+'" name="userinfo'+$p.admin.users.total+'" value="'+(i+1)+'" mandatory="'+mandatory+'" '+selected+'>'+tabOptions[i]+'<br />';
            }                    
            l_s+='<td></tr>';
            break;
            //TEXTAREA
            case 5:
            l_s+='<tr><td nowrap="nowrap">'+label+'<input type="hidden" name="uniq_id'+$p.admin.users.total+'" value="'+id+'" ></td>\
                  <td><textarea cols="45" rows="5" name="userinfo'+$p.admin.users.total+'" mandatory="'+mandatory+'">'+$p.admin.users.userCriterias[id]['parameters']+'</textarea>'+info+'<td></tr>';
            break;
        }
        $p.admin.users.total++;
        return l_s;
    },
    buildUserCriterias:function()
    {
        var l_s="";
        if ($p.admin.users.nbUserCriterias==0)  { return $p.admin.users.buildCriterias(); }
        $p.admin.users.total=1;

        for (var w in $p.admin.users.criteriasTab) 
        {
            var id=w;  
            var label=$p.admin.users.criteriasTab[w]['label'];
            var type=$p.admin.users.criteriasTab[w]['type'];
            var options=$p.admin.users.criteriasTab[w]['options'];
            var mandatory=$p.admin.users.criteriasTab[w]['mandatory'];
			var info=$p.admin.users.criteriasTab[w]['info'];
            if (mandatory==1) { info="&nbsp;&nbsp;<span id='errormessage'>*</span>"; }
            //control for each criteria if the user already set a value for it. 
            //if not propose the basic criteria field.
            //if yes propose to update of the current value
            if(info==indef) {
                info="";
            }
            if (typeof($p.admin.users.userCriterias[id])=='undefined') {
                l_s+=$p.admin.users.displaySpecificCriteria(id,label,type,options,mandatory,info); 
            }
            else {
                var parameters=$p.admin.users.userCriterias[id]['parameters'];
                l_s+=$p.admin.users.displayUserCriterias(id,label,type,options,mandatory,info,parameters);
            }
         }
         return l_s;
    },
    displaySpecificCriteria:function(id,label,type,options,mandatory,info)
    {
        var l_s='';
        switch (type)
        {
            //TEXT
            case 1:
            l_s+='<tr><td nowrap="nowrap">'+label+'<input type="hidden" name="uniq_id'+$p.admin.users.total+'" value="'+id+'" /></td>\
                  <td><input type="text" name="userinfo'+$p.admin.users.total+'" value="" size="20" mandatory="'+mandatory+'" />'+info+'<td>\
                  </tr>';
            break;
            //LIST
            case 2:
            l_s+='<tr><td nowrap="nowrap">'+label+'<input type="hidden" name="uniq_id'+$p.admin.users.total+'" value="'+id+'" ></td>\
                  <td><select name="userinfo'+$p.admin.users.total+'" mandatory="'+mandatory+'" >';  
            var tabOptions=options.split(';');
            for(var i=0;i<tabOptions.length;i++)
            {
                l_s+='<option value="'+(i+1)+'">'+tabOptions[i]+'</option>';
            }
            l_s+='</select>'+info+'<td></tr>';
            break;	             
            //CHECKBOX
            case 3:
            l_s+='<tr><td nowrap="nowrap">'+label+'<input type="hidden" name="uniq_id'+$p.admin.users.total+'" value="'+id+'" /></td><td>';
            var tabOptions=options.split(';');
            for(var i=0;i<tabOptions.length;i++)
            {
                if ((i+1)==1)
                    l_s+='<INPUT type="checkbox" name="userinfo'+$p.admin.users.total+'[]" id="check'+tabOptions[i]+'" value="'+(i+1)+'" mandatory="'+mandatory+'" >'+tabOptions[i]+' '+info+'<br />';
                else
                    l_s+='<INPUT type="checkbox" name="userinfo'+$p.admin.users.total+'[]" id="check'+tabOptions[i]+'" value="'+(i+1)+'" mandatory="'+mandatory+'" >'+tabOptions[i]+'<br />'; 
            }
            l_s+='<td></tr>';
            break;
            //RADIO
            case 4:
            l_s+='<tr><td nowrap="nowrap">'+label+'<input type="hidden" name="uniq_id'+$p.admin.users.total+'" value="'+id+'" /></td><td>';
            var tabOptions=options.split(';');
            for(var i=0;i<tabOptions.length;i++)
            {
                if ((i+1)==1)
                    l_s+='<INPUT type="radio" id="radio'+tabOptions[i]+'" name="userinfo'+$p.admin.users.total+'" value="'+(i+1)+'" mandatory="'+mandatory+'" >'+tabOptions[i]+' '+info+'<br />';
                else
                    l_s+='<INPUT type="radio" id="radio'+tabOptions[i]+'" name="userinfo'+$p.admin.users.total+'" value="'+(i+1)+'" mandatory="'+mandatory+'" >'+tabOptions[i]+'<br />';
            }                    
            l_s+='<td></tr>';
            break;
            //TEXTAREA
            case 5:
            l_s+='<tr><td nowrap="nowrap">'+label+'<input type="hidden" name="uniq_id'+$p.admin.users.total+'" value="'+id+'" ></td>\
                  <td><textarea cols="45" rows="5" name="userinfo'+$p.admin.users.total+'" mandatory="'+mandatory+'"></textarea>'+info+'<td></tr>';
            break; 
        }
        $p.admin.users.total++;
        return l_s;   
    },
    buildCriterias:function()
    {
        var l_s="";
        $p.admin.users.total=1;
        for (var w in $p.admin.users.criteriasTab) 
        {
            var id=w;
            var info="";
            var label=$p.admin.users.criteriasTab[w]['label'];
            var type=$p.admin.users.criteriasTab[w]['type'];
            var options=$p.admin.users.criteriasTab[w]['options'];
            var mandatory=$p.admin.users.criteriasTab[w]['mandatory'];
            if (mandatory==1) info="&nbsp;&nbsp;<span id='errormessage'>*</span>";                   
            l_s+=$p.admin.users.displaySpecificCriteria(id,label,type,options,mandatory,info);
        }          
        return l_s;     
    },
	changeType:function()
	{
		var v_form=document.forms["f"];
		$p.admin.users.showSelectedGroups();
		$p.admin.users.loadAvailGroups(0);
        
		if (v_form.usertype.value=="I") {
			$('tabdiv').setStyle('display','none');
			$('groupdiv').setStyle('display','block');
            $('tabdiv').innerHTML=$p.admin.users.displayAvailableTabs();
		}
		if (v_form.usertype.value=="A") {
			$('tabdiv').setStyle('display','block');
			$('groupdiv').setStyle('display','block');    
            $('tabdiv').innerHTML=$p.admin.users.displayAdminAvailableTabs();
            
            //display only admin available tabs
            if ($p.admin.users.isNew && $p.admin.userDBid!=1) {
                var i=1;
                while (typeof(v_form.elements["tab"+i])!="undefined") {
                    if (!v_form.elements["tab"+i].checked) {
                        v_form.elements["tab"+i].disabled=true;
                    }
                    i++;
                }
            }
		}

		if (v_form.usertype.value=="R") {
			var l_s="";
			l_s+="<input type='checkbox' name='feedcreator'"+(redacCreator?" checked='checked'":"")+" />"+lg("userCanCreateFeeds")+"<br /><br />";
			l_s+=lg("availableFeeds")+" :<br /><select name='feedlist' size='4' style='width:300px'></select><br />";
			l_s+="<input type='checkbox' name='feedadmin' /> "+lg("administrator");
			l_s+="<input type='button' value='"+lg("lblAdd")+"' onclick='$p.admin.users.addRedactorFeed();' /><br /><br />";
			l_s+=lg("feedsForThisRedactor")+" :<br /><div id='selfeed' style='border: 1px solid #c6c3c6'></div>";
			$p.print("groupdiv",l_s);
			$p.admin.users.loadFeeds();
			$p.admin.users.loadRedactorFeeds();
		}
	},
	loadFeeds:function()
	{
		getXml(padmin["xml_feeds"],$p.admin.users.displayFeeds,"");
	},
	displayFeeds:function(response,vars)
	{
		if (response.getElementsByTagName("feed")[0])   {
			var i=0,l_form=document.forms["f"];
			while (response.getElementsByTagName("feed")[i])
			{
				var l_result=response.getElementsByTagName("feed")[i];
				l_form.feedlist.options[l_form.feedlist.length] = new Option($p.ajax.getVal(l_result,"title","str",false,"--"),$p.ajax.getVal(l_result,"id","int",false,0));
				i++;
			}
			l_form.feedlist.selectedIndex=0;
		}
	},
	loadRedactorFeeds:function()
	{
		getXml(padmin["xml_redactor_feeds"]+"?id="+$p.admin.users.selId,$p.admin.users.getRedactorFeeds,"");
	},
	getRedactorFeeds:function(response,vars)
	{
		if (response.getElementsByTagName("feed")[0])   {
			var i=0,l_form=document.forms["f"];
			while (response.getElementsByTagName("feed")[i])
			{
				var l_result=response.getElementsByTagName("feed")[i];
				redacfeedid.push($p.ajax.getVal(l_result,"id","int",false,0));
				redacfeedname.push($p.ajax.getVal(l_result,"title","str",false,"--"));
				redacfeedadmin.push($p.ajax.getVal(l_result,"admin","int",false,0));
				i++;
			}
		}
		$p.admin.users.displayRedactorFeeds();
	},
	displayRedactorFeeds:function()
	{
		var l_s="";
		if (redacfeedid.length==0)  {   l_s=lg("noFeedForUser");    }
		else    {
			for (var i=0;i<redacfeedid.length;i++)
			{
				l_s+=redacfeedname[i]+(redacfeedadmin[i]==1?" (admin)":"")+"<input type='hidden' name='feedid"+i+"' value='"+redacfeedid[i]+"' /><input type='hidden' name='feedadmin"+i+"' value='"+redacfeedadmin[i]+"' /> <a href='#' onclick='return $p.admin.users.supRedactorFeed("+i+")'>"+$p.img("ico_close.gif",9,9)+"</a><br />";
			}
		}
		$p.print("selfeed",l_s);
	},
	addRedactorFeed:function()
	{
		var l_form=document.forms["f"];
		if (l_form.feedlist.selectedIndex>=0) {
			var ind=l_form.feedlist.selectedIndex;
			redacfeedid.push(l_form.feedlist.options[ind].value);
			redacfeedname.push(l_form.feedlist.options[ind].text);
			redacfeedadmin.push((l_form.feedadmin.checked?1:0));
			$p.admin.users.displayRedactorFeeds();
		}
	},
	supRedactorFeed:function(v_id)
	{
		redacfeedid.splice(v_id,1);
		redacfeedname.splice(v_id,1);
		redacfeedadmin.splice(v_id,1);
		$p.admin.users.displayRedactorFeeds();
		return false;
	},
	showSelectedGroups:function()
	{
		var l_s="",l_input="";
		for (var i=0;i<$p.admin.users.selGroup.length;i++)
		{
			l_s+="<a href='#' onclick='return $p.admin.users.selectSelectedGroup("+i+");'"+($p.admin.users.selGroupId==i?" style='background-color:#c6c3c6;'":"")+">"+$p.admin.users.selGroup[i].name+"</a><br />";
			l_input+="<input type='hidden' name='group"+i+"' value='"+$p.admin.users.selGroup[i].id+"' />";
		}
		$p.print("selgroupdiv",l_s);
		$p.print("groupinputs",l_input);
	},
	selectSelectedGroup:function(v_id)
	{
		$p.admin.users.selGroupId=v_id;
		$p.admin.users.showSelectedGroups();
		return false;
	},
	supSelectedGroup:function()
	{
		//suppress a group from user list
		if ($p.admin.users.selGroupId==-1) return false;
		$p.admin.users.selGroup.splice($p.admin.users.selGroupId,1);
		$p.admin.users.selGroupId=-1;
		$p.admin.users.showSelectedGroups();
		return false;
	},
	addSelectedGroup:function()
	{
		//add a group from group list to user list
		if ($p.admin.users.selAvailGroupId==-1) return false;
		//check if group is already added for this user
		for (var i=0;i<$p.admin.users.selGroup.length;i++)
		{
			if ($p.admin.users.selGroup[i].id==$p.admin.users.groups[$p.admin.users.selAvailGroupId].id) return false;
		}
		$p.admin.users.selGroup.push(new $p.admin.widgets.groupObj($p.admin.users.groups[$p.admin.users.selAvailGroupId].id,$p.admin.users.groups[$p.admin.users.selAvailGroupId].name));
		$p.admin.users.selAvailGroupId=-1;
		$p.admin.users.selGroupId=-1;
		$p.admin.users.showSelectedGroups();
		//clean up all groups list
		$p.admin.users.selectAvailGroup(-1);
		return false;
	},
	loadAvailGroups:function(v_group,v_open)
	{
		if (v_open==indef) { v_open=1; }
		getXml(padmin["xml_group"]+"?group="+v_group,$p.admin.users.displayAvailGroups,new Array(v_group,v_open));
		return false;
	},
	displayAvailGroups:function(response,vars)
	{
		var l_s="";
		if (vars[0]!=0) {
            l_s+="<a href='#' class='dirlink' onclick='return $p.admin.users.loadAvailGroups("+vars[0]+","+(vars[1]==0?"1":"0")+");'>"+$p.img((vars[1]==1?"ico_minus.gif":"ico_plus.gif"),9,9)+"</a> <a id='expa"+vars[0]+"' href='#' onclick='return $p.admin.users.selectAvailGroup("+vars[0]+");'>"+$p.ajax.getVal(response,"selgroupname","str",true,"--")+"</a><br />";
        }
			
		if (response.getElementsByTagName("group")[0] && vars[1]==1)    {
			if (response.getElementsByTagName("group")[0])  {
				var i=0,l_groupid,l_groupname;
				while (response.getElementsByTagName("group")[i])
				{
					var l_result=response.getElementsByTagName("group")[i];
					l_groupid=$p.ajax.getVal(l_result,"groupid","int",false,0);
					l_groupname=$p.ajax.getVal(l_result,"groupname","str",false,"...");
					$p.admin.users.groups.push(new $p.admin.widgets.groupObj(l_groupid,l_groupname));
					l_s+="<div class='expdir' id='exp"+l_groupid+"'><a href='#' onclick='return $p.admin.users.loadAvailGroups("+l_groupid+");'>"+$p.img("ico_plus.gif",9,9)+"</a> <a id='expa"+l_groupid+"' href='#' onclick='return $p.admin.users.selectAvailGroup("+l_groupid+");'>"+l_groupname+"</a></div>";
					i++;
				}
			}
		}
		$p.print("exp"+vars[0],l_s);
	},
	selectAvailGroup:function(v_id)
	{
		for (var i=0;i<$p.admin.users.groups.length;i++)
		{
			if ($p.get("expa"+$p.admin.users.groups[i].id)!=null) {
				if ($p.admin.users.groups[i].id==v_id)    {
					$p.admin.users.selAvailGroupId=i;
					($p.get("expa"+$p.admin.users.groups[i].id)).style.backgroundColor="#c6c3c6";
				}
    				else    {   ($p.get("expa"+$p.admin.users.groups[i].id)).style.backgroundColor="#ffffff"; }
			}
		}
		return false;
	},
	/*
		$p.admin.users.existAccount
		Verify if a username is available (already existing in the database or not)
		input : username
	*/
	existAccount:function(username)
	{
        username = $p.string.replacePlus(username);
        if (username=="")	{   $p.app.alert.show(lg('msgNoAdressSpecified'));	 }
        else    {
			$p.ajax.call(padmin["xml_existingaccountcontrol"]+'?username='+username,
				{
					'type':'load',
					'method':'get',
					'callback':
					{
						'function':$p.admin.users.getExistAccount
					}
				}	
			);
			return false;
		}
	},
	getExistAccount:function(response,vars)
	{
		var existAccount=$p.ajax.getVal(response,"exist","int",false,0);
		if (existAccount==0)	$('availibiliy').set("html","<img src='../images/ico_install_ok.gif' width='16' height='16' align='absmiddle'>&nbsp;&nbsp;<font color='green'>"+lg('availableAdress')+"</font>");
		else    {
			$('availibiliy').set("html","<img src='../images/ico_install_nok.gif' width='16' height='16' align='absmiddle'>&nbsp;&nbsp;<font color='red'>"+lg('unavailableAdress')+"</red>"); 
			document.forms['f'].submitForm.disabled=true;
		}   
	},
    /*
                Function: $p.admin.users.buildPage
                
                                Build users index page
         */   
    buildPage:function()
    {
        $p.app.tabs.sel=2;
        $p.app.tabs.select(2);
        $p.admin.users.tabname='userstab';
        $p.admin.tools.emptyContent();
        $p.admin.users.defineSubTabs();
		$p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':lg('userMgmt'),'link':'','fct':'$p.admin.users.buildPage();return false;'}
			]
		));		
/*        $p.print('content','<h1><a href="#" onclick="$p.admin.setState();$p.app.tabs.open(0);return false;">'+lg("Accueil")+'</a>&nbsp;>&nbsp;\
		<img src="../images/ico_adm_users.gif" align="absmiddle" />'+lg("userMgmt")+' :</h1>');
*/
        $p.admin.buildSubTabs($p.admin.users.usersSubTabs,$p.admin.users.tabname);
        if ($p.app.tabs.sel && $p.app.tabs.sel > 0  ) {$p.admin.setState("$p.app.tabs.open("+$p.app.tabs.sel+")");}
    },
    /*
                Function: $p.admin.users.defineSubTabs
                
                                define the users sub tabs
         */   
    defineSubTabs:function()
    {
        var tabTmp=new Array;
        for (var i=0;i<$p.admin.subTabs[$p.admin.users.tabname].length;i++)
        {
            tabTmp.push({'label':$p.admin.subTabs[$p.admin.users.tabname][i]['label'],
                         'fct':$p.admin.subTabs[$p.admin.users.tabname][i]['fct'],
                         'description':$p.admin.subTabs[$p.admin.users.tabname][i]['description']});
        } 
        $p.admin.users.usersSubTabs[$p.admin.users.tabname]=tabTmp;         
    },
    /*
                Function: $p.admin.users.loadUsersInfos
                
                                Load users general informations
         */    
    loadUsersInfos:function()
    {
          $p.ajax.call(padmin["xml_get_users_infos"],
            {
                'type':'load',
                'forceExecution':true,
                'priority':1,
                'asynchron':true,
                'callback':
                {
                    'function':$p.admin.users.getUsersInfos
                }
            }	
        );
        return false;
    },
    /*
                Function: $p.admin.users.getUsersInfos
                
                                get users general informations
         */    
    getUsersInfos:function(response,vars)
    {
        var i=0;
        var nbUsers=$p.ajax.getVal(response,"nbUsers","int",false,0);
        $p.admin.userDBid=$p.ajax.getVal(response,"id","int",false,0);
        $p.admin.users.usersInfos['nbUsers']=nbUsers;
        //get the admin groups
        while (response.getElementsByTagName("groups")[i]) 
        {
            var l_result=response.getElementsByTagName("groups")[i];
            var group_id=$p.ajax.getVal(l_result,"group","int",false,0);
            $p.admin.groupsAccess.push(group_id);
            i++;
        } 
    },
    /*
                Function: $p.admin.users.convertTabGroupAccessToString
                
                                convert the tab to string (to use in query IN() )
         */  
    convertTabGroupAccessToString:function()
    {
         var groupList="";
         for (var i=0;i<$p.admin.groupsAccess.length;i++) {
            groupList+="'"+$p.admin.groupsAccess[i]+"',";
         }
         groupList=groupList.substring(0,groupList.length-1);
         return groupList;
    },
    /*
                Function: $p.admin.users.usersinfos
                
                                display the users criteria
         */    
    usersinfos:function()
    {
        var l_s="";
        $p.admin.users.keys=[];
        $p.admin.tools.emptyContent();
		$p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':lg('userMgmt'),'link':'','fct':'$p.admin.users.buildPage();return false;'},
				{'label':lg('userInfos'),'link':'','fct':''}	
			]
		));
/*        l_s+='<h1><a href="#" onclick="$p.admin.setState();$p.app.tabs.open(0);return false;">'+lg("Accueil")+'</a>&nbsp;>&nbsp;\
			<img src="../images/ico_adm_users.gif" /> <a href="#" onclick="$p.admin.users.buildPage();return false;">'+lg("userMgmt")+'</a>&nbsp;>&nbsp;\
			'+lg("userInfos")+' :</h1>\*/
		l_s+= '<span id="optmod"></span>\
            <form name="formCritere">\
            <div id="ExistingCriterias"></div>\
            <input type="hidden" value="checkpoint" name="checkpoint" />\
            <input type="hidden" value="0" name="totalCriteria" id="totalCriteria" />\
            <div id="listmod" class="greydiv"></div>\
            <div id="listmod2" class="greydiv"></div>\
            </form>';
        $p.print('content',l_s);
        $p.admin.users.loadExistingCriteria();
        $p.admin.users.displayCriteria();
    },
    /*
                Function: $p.admin.users.setUsersInfosConfig
                
                                Set the new criterias and updated ones
         */  
    setUsersInfosConfig:function()
    {
        var correct=false;
        var setUpdateList="";
        if (document.forms['formCritere'].updateAction) {
            setUpdateList=$p.admin.users.getCriteriaUpdates();
        }
        $p.admin.users.createHiddenField();
        if($p.admin.users.controlForm()) {
            var setNewList=$p.admin.users.getCriteriaNews();
            $p.admin.users.nbCriterias=$p.admin.users.keys.length;
            $p.ajax.call(padmin["scr_users_infos_config"],
                {
                    'type':'execute',
                    'variables':setNewList+setUpdateList,
                    'forceExecution':true,
                    'callback':
                    {
                        'function':$p.admin.users.usersinfos
                    }
                }
            );            
            return false;
        }
    },
    /*
                Function: $p.admin.users.getCriteriaNews
                
                                list the parameters of the new criterias
                                
                Returns:
                
                                String
         */  
    getCriteriaNews:function()
    {
        var labelList="";
        var typeList="";
        var optionsList="";
        var mandatoryList="";
        var editableList="";
        var nbcriterias=$p.admin.users.keys.length;
        for (var i=0;i<$p.admin.users.keys.length;i++)
        {
            labelList+="label"+i+"="+document.forms['formCritere'].elements['label'+i].value+"&";
            typeList+="type"+i+"="+document.forms['formCritere'].elements['type'+i].value+"&";
            optionsList+="options"+i+"="+document.forms['formCritere'].elements['options'+i].value+"&";
            mandatoryList+="mandatory"+i+"="+document.forms['formCritere'].elements['mandatory'+i].value+"&";
            editableList+="editable"+i+"="+document.forms['formCritere'].elements['editable'+i].value+"&";
        }    
        var newCriteriaList="checkpoint=1&totalCriteria="+nbcriterias+"&"+labelList+typeList+optionsList+mandatoryList+editableList;
        return newCriteriaList;
    },
    /*
                Function: $p.admin.users.getCriteriaUpdates
                
                                list the parameters of the updated criterias
                                
                Returns:
                
                                String
         */  
    getCriteriaUpdates:function()
    {
        var nbUpdate="";
        var upLabList="";
        var upManList="";
        var upEdiList=""
        var updateList="";
        var upIdList="";
        nbUpdate=document.forms['formCritere'].updateAction.value;
        for (var i=0;i<nbUpdate;i++)
        {
            upIdList+="updateId"+i+"="+document.forms['formCritere'].elements['updateId'+i].value+"&";
            upLabList+="updateLab"+i+"="+document.forms['formCritere'].elements['updateLab'+i].value+"&";
            upManList+="updateMan"+i+"="+document.forms['formCritere'].elements['updateMan'+i].value+"&";
            upEdiList+="updateEdi"+i+"="+document.forms['formCritere'].elements['updateEdi'+i].value+"&";
        }
        upEdiList=upEdiList.substring(0,upEdiList.length-1);
        updateList="updateAction="+nbUpdate+"&"+upIdList+upLabList+upManList+upEdiList;
        return updateList;
    },
    /*
                Function: $p.admin.users.usersmgmt
                
                                Display the users smanagement page
         */  
    usersmgmt:function()
    {
        var l_s="";
        $p.admin.tools.emptyContent();        
        $p.plugin.hook.launch('users_mgmt_afteroptions');
        $p.plugin.hook.launch('users_mgmt_afterdirectory');
		$p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':lg('userMgmt'),'link':'','fct':'$p.admin.users.buildPage();return false;'},
				{'label':lg('userMgmt'),'link':'','fct':''}	
			]
		));
        l_s+= '<span id="optmod"></span>\
                <div class="bottomhr"></div>\
                <div id="listmod" class="greydiv"></div>\
                <div class="greydiv" id="directory" style="height:20px"></div>\
                <div class="tophr" id="newmod"></div>';                
        $p.print('content',l_s);
        
        
        $p.admin.users.init(); 
        $p.admin.users.initCriterias();
    },
    initCriterias:function()
    {
        $p.ajax.call(padmin["xml_user_get_criterias"],
            {
                'type':'load',
                'method':'get',
                'callback':
                {
                    'function':$p.admin.users.getCriterias
                }
            }	
        );
        return false;
    },
    getCriterias:function(response)
    {
        $p.admin.users.nbCriterias=$p.ajax.getVal(response,"nb","int",false,0);
        if ($p.admin.users.nbCriterias!=0)
        {
            var i=0;
            while (response.getElementsByTagName("criteria")[i])
            {
                var l_result=response.getElementsByTagName("criteria")[i];
                var id=$p.ajax.getVal(l_result,"id","int",false,0);
                var type=$p.ajax.getVal(l_result,"type","int",false,0);
                var label=$p.ajax.getVal(l_result,"label","str",false,"");
                var options=$p.ajax.getVal(l_result,"options","str",false,"");
                var mandatory=$p.ajax.getVal(l_result,"mandatory","int",false,0);
                //var parameters=$p.ajax.getVal(l_result,"parameters","int",false,0);
                $p.admin.users.criteriasTab[id]={
                                                     'type':type,
                                                     'label':label,
                                                     'options':options,
                                                     'mandatory':mandatory
                                                }; 
                i++;
            }
        }
    }
}




//************************************* TABS  FUNCTIONS ***************************************************************************************************************
/*
    Class: $p.admin.tabs
         tabs functions
*/
$p.admin.tabs={
    tabAccess:{},
    notAccess:[],
    tabsName:{},
    allTabsBlocs:[], 
    totalnbtabs:0,
    /*
                Function: init
                         $p.admin.tabs.init
                                        
                         Initialise the tabs
	*/    
	init:function(selected)
	{
        var pfolder="../admin/";

        $p.app.user.init(-1,$p.app.user.name,"A");
        $p.app.tabs.selId=-1;
        //load tabs
        $p.ajax.call(padmin["xml_tabs"],
            {
                'type':'load',
                'callback':
                {
                    'function':$p.admin.tabs.displayTabs,
                    'variables':
                    {
                        'selected':selected
                    }
                }
            }	
        );
	},
    /*
                Function: $p.admin.tabs.defineAllTabs
                
                                Define for a tab id the javascript function associated to build the bloc in the index page
         */
    defineAllTabs:function()
    {
        $p.admin.tabs.allTabsBlocs={     
                                        '0':'$p.admin.welcome._buildGeneral()',
                                        '1':'$p.admin.welcome._buildWidgets()',
                                        '2':'$p.admin.welcome._buildUsers()',
                                        '3':'$p.admin.welcome._buildPages()',
                                        '4':'$p.admin.welcome._buildApplication()'
                                   };
    }, 
    /*
                Function: $p.admin.tabs.crossTabs
                
                                Define the javascript function for the user tab
                
                Parameters:
                
                               l_id - user tab ID           
         */    
    crossTabs:function(l_id)
    {
       for (var x in $p.admin.tabs.allTabsBlocs) {
            if (x==l_id)    {
                $p.admin.tabs.tabAccess[l_id]=$p.admin.tabs.allTabsBlocs[x];
            }            
       } 
    },
    loadAllAdminTabs:function(response)
    {
        var l_result=response.getElementsByTagName("alltabs");
        $p.admin.tabs.totalnbtabs=l_result.length-1;
        if (l_result.length==0) return ;
        for (var j=0;j<l_result.length;j++)
        {
            var l_id=$p.ajax.getVal(l_result[j],"id","int",false,0);
            var l_name=$p.ajax.getVal(l_result[j],"name","str",false,"");                
            var l_label=$p.ajax.getVal(l_result[j],"label","str",false,"");
            var l_type=$p.ajax.getVal(l_result[j],"type","int",false,4); 
            var l_param=$p.ajax.getVal(l_result[j],"param","str",false,"");

            //$p.admin.tabs.crossTabs(l_id);
            $p.admin.tabs.add(l_name,l_label,l_param,l_type);

            switch(l_name) {
                case "modulestab" : l_name=lg('modMgmt'); break;
                case "userstab" : l_name=lg('userMgmt'); break;
                case "pagestab" : l_name=lg('tabMgmt'); break;
                case "configstab" : l_name=lg('Configuration'); break;
                case "applicationtab" : l_name=lg('applicationConfiguration'); break;
                case "statstab" : l_name=lg('statistics'); break;
                case "comtab" : l_name=lg('communication'); break;
                case "supporttab" : l_name=lg('supportMgmt'); break;
                default : break;
            }
            $p.admin.tabs.tabsName[l_id]=l_name; 
        }
    },
    /*
         Function: $p.admin.tabs.loadPoshTabs
         
                    Load admin default tabs from the database
         
         Parameters:
         
                    response - XML object
         */   
    loadPoshTabs:function(response)  
    {
        $p.admin.tabs.loadAllAdminTabs(response);
        var l_result=response.getElementsByTagName("tab");
        if (l_result.length==0) return '';
        else {
            $p.admin.tabs.crossTabs(0);
			for (var j=0;j<l_result.length;j++)
			{
                var l_name=$p.ajax.getVal(l_result[j],"name","str",false,"");
                var l_id=$p.ajax.getVal(l_result[j],"id","int",false,0);
                $p.admin.tabs.crossTabs(l_id);
                $p.admin.tabs.notAccess.push(l_name);
            }
        }
    },
    /*
                    Function: $p.admin.tabs.hideUnavailableTabs
         
                                     Hide the tabs the admin cannot see
         */ 
    hideUnavailableTabs:function()
    {
        for (var i=1;i<=$p.admin.tabs.totalnbtabs;i++) {
            var exist=false;
            var currentId=i;
            for (var j=0;j<$p.admin.tabs.notAccess.length;j++) {
                if (tab[i].id==$p.admin.tabs.notAccess[j]) {
                    exist=true;  
                }                
            }            
            if (!exist) {
                $('tab'+currentId).style.display='none';
            }
        } 
    },
    /*
         Function: $p.admin.tabs.loadPluginTabs
         
                    Load plugin tabs from the database
         
         Parameters:
         
                    response - XML object
         */   
    loadPluginTabs:function(response)  
    {
		var p_result=response.getElementsByTagName("plugintab");
        if (p_result.length==0) return '';
		else
		{
			for (var j=0;j<p_result.length;j++)
			{
				var p_name=$p.ajax.getVal(p_result[j],"pluginname","str",false,"");
				var p_label=$p.ajax.getVal(p_result[j],"pluginlabel","str",false,"");
                $p.admin.tabs.add(p_name,p_label,"$p.admin.tabs.displayPluginsTabs('"+p_name+"','"+p_label+"');",5);
            }
        } 
    },
    /*
             Function: $p.admin.tabs.displayPluginsTabs
             
                        Display admin plugins tabs
             
             Parameters:
             
                        plugname - plugin name 
         */     
    displayPluginsTabs:function(plug_name,plug_label)
    {
        var l_s='';
        $p.admin.tools.emptyContent();
        $p.admin.users.tabname=plug_name;
        $p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':plug_label,'link':'','fct':''}
			]
		));
        l_s+='<iframe id="plugin'+plug_name+'" src="'+__LOCALFOLDER+'admin/'+padmin["admin_tab"]+'?page='+plug_name+'" width="600" height="300" frameborder="0"></iframe>';    
        $('content').innerHTML=l_s;
    },
    /*
         Function: $p.admin.tabs.displayTabs
         
                    Display admin tabs
         
         Parameters:
         
                    response - XML object
                    vars - variables
         */     
    displayTabs:function(response,vars)
    {
        //default tab
        $p.admin.tabs.loadPoshTabs(response);
        $p.admin.tabs.loadPluginTabs(response);
        $p.app.mainMenu();
        $p.app.tabs.sel=0;
        $p.app.tabs.create($p.app.tabs.sel);  
        $p.app.tabs.open($p.app.tabs.sel);
        $p.app.tabs.select($p.app.tabs.sel);
        $p.admin.tabs.hideUnavailableTabs();
        //$p.admin.welcome.buildPage();
    },    
    /*
         Function: $p.admin.tabs.add
         
                    Add a tab
         
         Parameters:
         
                    name - id
                    label - label 
                    link - page url 
                    type - page type (4) 
                    
               See:
               
                    $p.app.tabs.object in ajax.js
         */    
    add:function(name,label,link,type)
    {
        if (type==indef)  {  
            type=5;  
        }
        tab.push(new $p.app.tabs.object(
                                        name,
                                        lg(label),
                                        type,
                                        link
                                        )
                );  
    }
}


//************************************* PAGES  FUNCTIONS ***************************************************************************************************************
/*
    Class: $p.admin.pages
         pages functions
*/
$p.admin.pages={
    pagesInfos:[],
    usersSubTabs:[],
    tabname:'',
    group:0,
    groups:{},
    groupPages:{},
    admParam:'',
    init:function()
	{
		this.initme();
	},
	initme:function()
	{
		// init pages moves
		var l_pages=$("pages");
		for(var i=0;i<l_pages.childNodes.length-1;i++){
			var l_mod=l_pages.childNodes[i];
			$p.app.widgets.move.set(l_mod,"admpage",l_pages,"vertical",false);
		}
	},
	/*
		$p.admin.pages.save
			display or not the complementary options
	*/
	save:function(v_id)
	{
		// Save the pages order changes
		var l_pages=$p.get("pages");
		var l_seq=1;
		for(var i=0;i<l_pages.childNodes.length-1;i++)
		{
			if (l_pages.childNodes[i].id && (l_pages.childNodes[i].id).indexOf("admpage")!=-1)
			{
				l_id=(l_pages.childNodes[i].id).replace("admpage","");
				if (l_id==v_id) {
					executescr(padmin["scr_pages_move"],"id="+v_id+"&seq="+l_seq,false);
				}
				l_seq++;
			}
		}
	},
	saveModules:function()
	{
		//check if anonymous or connected page :
		var l_mode=document.forms["f"].mode.value;		
		var l_action=document.forms["f"].act.value;
		
		//if it's an update, and the checkbox to erase previous tab is checked, need confirmation to submit the form
		if (l_action=='upd' && document.forms["f"].formaction.checked==true)    {
			if (!confirm(lg('firstConfirmationAlert')) || (!confirm(lg('secondConfirmationAlert'))))
				return false;
		}
		
		//adjust module position
		var l_s="<input type='hidden' name='showtype' value='"+tab[$p.app.tabs.sel].showType+"' /><input type='hidden' name='npnb' value='"+tab[$p.app.tabs.sel].newspapernb+"' /><input type='hidden' name='nbcol' value='"+tab[$p.app.tabs.sel].colnb+"' /><input type='hidden' name='style' value='"+tab[$p.app.tabs.sel].style+"' /><input type='hidden' name='modulealign' value='"+(tab[$p.app.tabs.sel].moduleAlign?"Y":"N")+"' /><input type='hidden' name='controls' value='"+tab[$p.app.tabs.sel].controls+"' />";
		for (var i=0;i!=tab[$p.app.tabs.sel].module.length;i++)
		{
			var widgetX=tab[$p.app.tabs.sel].module[i].newx>0?tab[$p.app.tabs.sel].module[i].newx:10;
			var widgetY=tab[$p.app.tabs.sel].module[i].newy>0?tab[$p.app.tabs.sel].module[i].newy:150;
			//<input type="hidden" name="b'+i+'" value="'+(l_mode=='2'?'1':(tab[$p.app.tabs.sel].module[i].blocked?'1':'0'))+'" />
			l_s+='<input type="hidden" name="i'+i+'" value="'+tab[$p.app.tabs.sel].module[i].id+'" /><input type="hidden" name="p'+i+'" value="'+tab[$p.app.tabs.sel].module[i].newpos+'" /><input type="hidden" name="c'+i+'" value="'+tab[$p.app.tabs.sel].module[i].newcol+'" /><input type="hidden" name="pj'+i+'" value="'+tab[$p.app.tabs.sel].module[i].newposj+'" /><input type="hidden" name="x'+i+'" value="'+widgetX+'" /><input type="hidden" name="y'+i+'" value="'+widgetY+'" /><input type="hidden" name="v'+i+'" value="'+tab[$p.app.tabs.sel].module[i].vars+'" /><input type="hidden" name="b'+i+'" value="'+(l_mode=='2'?'1':(tab[$p.app.tabs.sel].module[i].blocked?'1':'0'))+'" /><input type="hidden" name="m'+i+'" value="'+(tab[$p.app.tabs.sel].module[i].minimized?'1':'0')+'" />';
		}
		$p.print("moduleslist",l_s);
		return true;
	},
	setColNb:function(v_nb)
	{
		var l_allowSuppress=$p.app.pages.columns.check(v_nb);
		if (l_allowSuppress) {
			var l_nbCol=v_nb;
			if (tab[$p.app.tabs.sel].showType==1&&l_nbCol!=tab[$p.app.tabs.sel].colnb) alert(lg("lblColNbErr"));
			if (l_nbCol>tab[$p.app.tabs.sel].colnb) {
				$p.app.pages.columns.add(l_nbCol);
			}
			else if (l_nbCol<tab[$p.app.tabs.sel].colnb) {
				$p.app.pages.columns.sup(l_nbCol);
			}
			tab[$p.app.tabs.sel].colnb=l_nbCol;
			return true;
		}
		else {
			document.forms['option'].nbcol[(tab[$p.app.tabs.sel].colnb-2)].checked=true;
			return false;
		}
	},
	addCol:function()
	{
		l_nbCol=tab[$p.app.tabs.sel].colnb+1;
		var l_allowSuppress=$p.app.pages.columns.isEmpty(l_nbCol);
		if (l_allowSuppress && tab[$p.app.tabs.sel].colnb<9)
		{
			$p.app.pages.columns.add(l_nbCol);
			tab[$p.app.tabs.sel].colnb=l_nbCol;
			document.forms['option'].nbcol.value=tab[$p.app.tabs.sel].colnb;
		}
	},
	supCol:function()
	{
		l_nbCol=tab[$p.app.tabs.sel].colnb-1;
		var l_allowSuppress=$p.app.pages.columns.isEmpty(l_nbCol);
		if (l_allowSuppress && tab[$p.app.tabs.sel].colnb>1) {
			$p.app.pages.columns.sup(l_nbCol);
			tab[$p.app.tabs.sel].colnb=l_nbCol;
			document.forms['option'].nbcol.value=tab[$p.app.tabs.sel].colnb;
		}
	},
    /*
                Function: $p.admin.pages.buildPage
                
                                Build pages index page
         */   
    buildPage:function()
    {
        $p.app.env="admin";
        $p.admin.tools.emptyContent();
        $p.app.tabs.sel=3;
        $p.app.tabs.select($p.app.tabs.sel);  
        $p.admin.pages.tabname='pagestab';
        $p.admin.pages.getTabs();
        if ($p.app.tabs.sel && $p.app.tabs.sel > 0) {$p.admin.setState("$p.app.tabs.open("+$p.app.tabs.sel+")");}   
    },
    /*
                Function: $p.admin.pages.getTabs
                
                                get the pages for a group
                                
                 Parameters:
                 
                               groupId - id of the group (public pages => group 0)
         */   
    getTabs:function(groupId)
    {
        $p.admin.tools.emptyContent();
        $p.admin.pages.groupPages={};
        
        tab[$p.app.tabs.sel].module=new Array();
        if(!groupId) { 
            groupId=$p.admin.pages.group; 
        }
        else { 
            $p.admin.pages.group=groupId; 
        }
        $p.ajax.call(padmin["xml_get_tabs_infos"]+'?group='+$p.admin.pages.group,
            {
                'type':'load',
                'forceExecution':true,
                'callback':
                {
                    'function':$p.admin.pages.buildAddPages
                }
            }	
        );
        $p.admin.pages.loadPagesInfos();
    },
    /*
                Function: $p.admin.pages.defineSubTabs
                
                                define the pages sub tabs
         */   
    defineSubTabs:function()
    {
        var tabTmp=new Array;
        for (var i=0;i<$p.admin.subTabs[$p.admin.pages.tabname].length;i++)
        {
            tabTmp.push({'label':$p.admin.subTabs[$p.admin.pages.tabname][i]['label'],
                         'fct':$p.admin.subTabs[$p.admin.pages.tabname][i]['fct'],
                         'description':$p.admin.subTabs[$p.admin.pages.tabname][i]['description']});
        } 
        $p.admin.pages.pagesSubTabs[$p.admin.pages.tabname]=tabTmp;         
    },
    /*
         Function: $p.admin.pages.buildAddPages
         
                         Display the available groups pages
                         
         Returns:
         
                         HTML code
         */  
    buildAddPages:function(response,vars)
    {
        $p.admin.pages.groupPages={};
        var l_s="";
        var l_i=0;
        var deleteLink="";
        if (response.getElementsByTagName("option")[0]) {
            while (response.getElementsByTagName("option")[l_i])
            {
                var l_result=response.getElementsByTagName("option")[l_i];
                var g_id=$p.ajax.getVal(l_result,"id","int",false,0);
                var g_name=$p.ajax.getVal(l_result,"name","str",false,"...");  
                $p.admin.pages.groups[g_id]={'name':g_name}; 
                l_i++;
            }
        }
        
        l_i=0;
        var nb=$p.ajax.getVal(response,"nb","int",false,0);
        if (response.getElementsByTagName("tab")[0]) {
            while (response.getElementsByTagName("tab")[l_i])
            {
                var l_result=response.getElementsByTagName("tab")[l_i];
                var id=$p.ajax.getVal(l_result,"id","int",false,0);
                var name=$p.ajax.getVal(l_result,"name","str",false,"...");    
                var description=$p.ajax.getVal(l_result,"description","str",false,"...");    
                var mode=$p.ajax.getVal(l_result,"mode","str",false,"...");    
                var type=$p.ajax.getVal(l_result,"type","str",false,"");   
                var param=$p.ajax.getVal(l_result,"param","str",false,"");   
                $p.admin.pages.groupPages[id]={'name':name,'description':description,'mode':mode,'type':type,'param':param}; 
                l_i++;
            }
        }
 
		$p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':lg('tabMgmt'),'link':'','fct':''}
			]
		));
                    
            if (__defaultmode=="anonymous")   {
                l_s+=lg("tabMgmtTxt")+'<div style="display:inline" id="onlineButton"><input type="button" class="btn" value="'+lg("setOnline")+'" style="color: #ff0000;font-size: 10pt;" onclick="$p.admin.pages.setOnline();" /></div>';
            }               
                      
            if (__useGroup 
                && __defaultmode=="connected")   {
                    l_s+=$p.admin.pages.buildGroupSelect(nb);             
            } 

            l_s+='<div id="pages">';                
            
            if (nb>0) {
                l_s+='<br /><div><a href="#" onclick="$p.admin.pages.add('+$p.admin.pages.group+');return false;">+ '+lg("addPage")+'</a>&nbsp;</div><br />';     
            }

            for (var w in $p.admin.pages.groupPages) 
            {
                if ($p.admin.pages.group==0 && nb==1) { deleteLink=""; }
                else {
                    deleteLink='<a href="#" onclick=$p.admin.pages.suppress('+w+',"'+escape($p.admin.pages.groupPages[w]["name"])+'",'+$p.admin.pages.group+');return false;" >['+lg("suppressThisPage")+']</a></p>';
                }
            
                l_s+='<div id="admpage'+w+'" class="module" style="font-size: 1em">\
                            <table class="bmod">\
                                <tr><td width="20" id="admpage'+w+'_h" style="cursor:move;height:100px;background-image:url(../images/fmove.gif);">&nbsp;</td>\
                                <td valign="top" style="padding:4px">\
                                    <h3>'+$p.admin.pages.groupPages[w]["name"]+'</h3>\
                                    <p>'+$p.admin.pages.groupPages[w]["description"]+'</p>\
                                    <p><strong>'+lg("mode")+'</strong> : '+$p.admin.pages.groupPages[w]["mode"]+'</p>\
                                    <p><strong>'+lg("type")+'</strong> : '+$p.admin.pages.groupPages[w]["type"]+' ('+$p.admin.pages.groupPages[w]["param"]+')</p>\
                                    <p><a href="#" onclick=$p.admin.pages.loadModify('+w+','+$p.admin.pages.group+');return false; >['+lg("modifyThisPage")+']</a>\
                                    '+deleteLink+'\
                                    </td>\
                                </tr>\
                            </table>\
                      </div>';
            }  
            
            if ($p.admin.pages.group==0
                && $p.admin.userDBid==1) {
                    l_s+='<div><a href="#" onclick="$p.admin.pages.add('+$p.admin.pages.group+');return false;">+'+lg("addPage")+'</a></div>'; 
            }
            else if ($p.admin.pages.group==0 && $p.admin.userDBid!=1) {
                l_s+='<br /><strong>'+lg('cantAddPageToPublic')+'</strong>';
            }   
            
            if ($p.admin.pages.group!=0) {
                l_s+='<div><a href="#" onclick="$p.admin.pages.add('+$p.admin.pages.group+');return false;">+'+lg("addPage")+'</a></div>';
            }
            
            l_s+='</div><br />';
                        
        $('content').innerHTML=l_s;
		$p.admin.pages.init();
    },
	showHideBox:function(val)
	{
		var divObj=$('moreOpt');
		if(val==2)
			divObj.setStyle("display","none");
		else
			divObj.setStyle("display","block");
	},
	showModules:function()
	{
		$p.show("modulesctl","block");
		$p.show("header","block");
		$p.show("modules","block");
		$p.show("newmod","block");
		$p.show("box","block");
		$p.show("vmenu","block");
		$p.show("hmenu","none");

		if (($p.get("modulesctl")).innerHTML=="") {
			$p.admin.pages.modulesBar();
			$p.app.pages.init();
			$p.app.cache.init();
            $p.app.menu.init();
            $p.app.menu.open(indef,true);
			$p.app.menu.widget.build();
		}
	},
	hideModules:function()
	{
		$p.show("modulesctl","none");
		$p.show("header","none");
		$p.show("modules","none");
		$p.show("newmod","none");
		$p.show("box","none");
		$p.show("hmenu","none");
		$p.show("vmenu","none");
	},
    controlMenuOpen:function()
    {
        if ($p.app.menu.isOpen) {
            return false;
        }
        else {
            $p.app.menu.init();
            return $p.app.menu.widget.build();
        }
    },
	modulesBar:function()
	{
		var l_s="<table cellpadding='3' cellspacing='0' border='0' width='100%'><tr><td class='advise' align='center'><form name='option'>";
		l_s+="<a href='#' id='addBtn' class='iconei' onclick='return $p.admin.pages.controlMenuOpen();'><img class='imgmid' src='../images/ico_menu_add.gif' alt='"+lg("lblAddContent2")+"' /> "+lg("lblAddContent")+"</a>";
		l_s+=" | "+lg("lblColNb")+" <input type='button' value='-' style='width:20px' onclick='$p.admin.pages.supCol()' /> <input type='text' name='nbcol' value='"+tab[$p.app.tabs.sel].colnb+"' style='text-align:right;width:20px;' size='1' disabled /> <input type='button' value='+' style='width:20px' onclick='$p.admin.pages.addCol()' />";
		if (__themeList.length>1) l_s+=" | "+lg("lblColors")+" <input type='button' value='<' style='width:20px' onclick='$p.styles.prevstyle();' /> <input type='text' name='selstyle' value='"+__themeList[tab[$p.app.tabs.sel].style-1]+"' style='text-align:center;width:80px;' size='14' disabled /> <input type='button' value='>' style='width:20px' onclick='$p.styles.nextstyle();' />";
		l_s+=" | "+lg("lblModuleAlign")+" <input type='checkbox' name='modulealign' onclick='$p.app.widgets.align(tab[$p.app.tabs.sel].moduleAlign);'"+(tab[$p.app.tabs.sel].moduleAlign?" checked='checked'":"")+" />";
		l_s+=" | "+lg("lblMoveMods")+" <input type='checkbox' name='modulecontrol' onclick='$p.app.widgets.switchHdr();'"+(tab[$p.app.tabs.sel].controls=='Y'?" checked='checked'":"")+" />";
		l_s+="</form>";
		l_s+="</td></tr></table>";
		$p.print("modulesctl",l_s);
	},
	typeSelection:function()
	{
		var l_s="";
		switch(document.forms["f"].type.value)
		{
			case "1":{
				l_s="<input type='hidden' name='param' value='/' />"+lg("lblAdmPagePortal");
                tab[$p.app.tabs.sel].type=1;
				$p.admin.pages.showModules();                
                $p.show("TitleManageWidget","block");
                $('savePage').value=lg('lblNewPageManageWidgets');
                break;}
			case "2":{
				l_s='URL de la page : <input class="thinbox" type="text" name="param" value="'+$p.admin.pages.admParam+'" maxlength="150" size="80" />';
				$p.admin.pages.hideModules();
                $p.show("TitleManageWidget","none");
                $('savePage').value=lg('lblSave');
				break;}
			case "3":{
				l_s='Fonction javascript : <input class="thinbox" type="text" name="param" value="'+$p.admin.pages.admParam+'" maxlength="150" />';
				$p.admin.pages.hideModules();
                $p.show("TitleManageWidget","none");
                $('savePage').value=lg('lblSave');
				break;}
			case "4":{
				l_s='URL de la page destination : <input class="thinbox" type="text" name="param" value="'+$p.admin.pages.admParam+'" maxlength="150" size="80" />';
				$p.admin.pages.hideModules();
                $p.show("TitleManageWidget","none");
                $('savePage').value=lg('lblSave');
				break;}
		}
		$p.print("paramctl",l_s);
	},
    add:function(group)
    {      
        $p.admin.tools.emptyContent();
        var l_s="";
        var pageid=0;
    	var name="";
    	var desc="";
    	var mode="";
    	var typep=1;
    	var param="";
    	var nbcol=3;
    	var showtype=0;
    	var npnb=15;
    	var style=1;
    	var moduleAlign="Y";
    	var controls="Y";
    	var icon="";
        widgetDecalX=0;
        $p.app.menu.initialized=false;
        
		$p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':lg('tabMgmt'),'link':'','fct':'$p.admin.pages.buildPage();return false;'},
				{'label':lg('NewPageManageOptions'),'link':'','fct':''}
			]
		));        
        l_s+='<div id="moveddiv" style="display: none;position: absolute;cursor: move;background: #c6c3c6;padding-bottom:0px;"></div>\
            <table cellpadding="10" cellspacing="0" border="0" width="100%">\
            <tr>\
            <td valign="top" style="border-bottom:1px solid #c6c3c6">\
            <form id="f">\
            <input type="hidden" name="act" value="add" />\
            <input type="hidden" name="id" value="'+pageid+'" />\
            <input type="hidden" name="group" value="'+group+'" />\
            <table cellpadding="4" cellspacing="0">\
            	<tr><td width="8%">'+lg("name")+' : </td><td><input type="text" name="name" maxlength="30" value="'+name+'" /></td></tr>\
            	<tr><td width="8%">'+lg("description")+' : </td><td><textarea name="desc" rows="2" cols="60">'+desc+'</textarea></td></tr>\
                '+$p.admin.pages.defineDefaultMode(mode)+'\
        	<tr><td width="8%">'+lg("type")+' : </td><td>\
            '+$p.admin.pages.defineSelectType(typep)+'\
            &nbsp; <span id="paramctl"></span><span id="moduleslist"></span></td></tr>\
            <tr>\
                <td width="8%">'+lg("lblIcon")+' :</td>\
                <td>\
                    <div id="pageicons" style="width:60%"></div>\
                    <input type="hidden" id="hiddenIconValue" name="hiddenIconValue" />\
                </td>\
            </tr>\
            <tr>\
                <td width="8%">'+lg("lblPersonalize")+'</td>\
                <td><input type="checkbox" name="removable" checked="checked" />'+lg("lblRemovable")+'</td>\
            </tr>\
            <tr>\
    			<td>'+lg("application")+' :</td>\
    			<td id="moreOpt"><input type="hidden" value="1" id="hiddenformaction" name="hiddenformaction" />\
                <input type="radio" name="formaction" name="formaction"value="1" checked="checked" onclick=javascript:$("hiddenformaction").value=this.value; />'+lg("lblFuturUsers")+'<br />\
    			<input type="radio" name="formaction" value="2" onclick=javascript:$("hiddenformaction").value=this.value; />'+lg("lblEveryone")+'</td>\
    		</tr>\
        	<tr><td colspan="2"><input id="savePage" type="submit" class="btn" value="'+lg("saveNewPage")+'" onclick="$p.admin.pages.submitAddPage();return false;" /></td></tr>\
            </table>\
            </form>\
            </td>\
            </tr>\
            </table>\
            </div>\
            '+$p.admin.pages.displayPortalConfigArea()+'\
            '+$p.admin.pages.buildPortalDivs();

        $('content').innerHTML=l_s;
        $p.app.menu.config.showIcons();

        if (__menuposition=='v')    {
            $('verticalMenu').innerHTML='<div id="vmenu">\
                                            <div id="vmenuoptions"></div>\
                                            <div id="vmenusuboptions"></div>\
                                            <div id="vmenucontent"></div>\
                                         </div>';
        }

        $p.admin.pages.showHideBox(document.forms[0].mode.value);
   
        var allowSave=false,jspass="";
        $p.admin.pages.admParam=param;
        $p.app.widgets.showAdminProperties=true;
        tab[$p.app.tabs.sel].showType=showtype;
        tab[$p.app.tabs.sel].newspapernb=npnb;
        tab[$p.app.tabs.sel].colnb=nbcol;
        tab[$p.app.tabs.sel].style=style;
        tab[$p.app.tabs.sel].type=3;
        tab[$p.app.tabs.sel].root=$p.get("modules");        
        tab[$p.app.tabs.sel].moduleAlign=(moduleAlign=="Y"?"true":"false");
        tab[$p.app.tabs.sel].controls=controls;
        tab[$p.app.tabs.sel].editable=true;
        __useNotation=false;
        createDivDynamically=false; //added with 1.4.3 due to issue with appendChild and IE
        $p.admin.pages.typeSelection();
        document.forms["f"].name.focus();
        /*FRI PHP Parametrierung*/
        for (var i=2;i<=__themeList.length;i++)
        {
        	$p.styles.addCssFile("../styles/main"+i+".css.php","style "+i,"alternate stylesheet");
        }
        
        //$p.styles.setActiveStyleSheet(style);
    },
    submitAddPage:function()
    {
        var paramsList="";
        var removable=(document.forms["f"].removable.checked)?1:0;
        var act=document.forms["f"].act.value;
        var id=document.forms["f"].id.value;
        var group=document.forms["f"].group.value;
        var name=document.forms["f"].name.value;
        var desc=document.forms["f"].desc.value;
        var mode=document.forms["f"].mode.value;
        var type=document.forms["f"].type.value;
        var icon=document.forms["f"].hiddenIconValue.value;
        var param=document.forms["f"].param.value;
        var action=document.forms["f"].hiddenformaction.value;
        paramsList+='act='+act
                  +'&id='+id
                  +'&hiddenIconValue='+icon
                  +'&group='+group
                  +'&name='+name
                  +'&desc='+desc
                  +'&mode='+mode
                  +'&type='+type
                  +'&param='+param
                  +'&removable='+removable;
        if (action!=0)  {  paramsList+="&formaction="+action; }
        if ($p.admin.pages.saveModules())
        {
            var showtype=document.forms["f"].showtype.value;
            var npnb=document.forms["f"].npnb.value;
            var nbcol=document.forms["f"].nbcol.value;
            var style=document.forms["f"].style.value;
            var modulealign=document.forms["f"].modulealign.value;
            var controls=document.forms["f"].controls.value;
            paramsList+='&showtype='+showtype
                       +'&npnb='+npnb
                       +'&nbcol='+nbcol
                       +'&style='+style
                       +'&modulealign='+modulealign
                       +'&controls='+controls;

            if (type==1)    {
                for (var i=0;i!=tab[$p.app.tabs.sel].module.length;i++)
                {
                    var wid=document.forms["f"].elements["i"+i].value;
                    var newpos=document.forms["f"].elements["p"+i].value;
                    var newcol=document.forms["f"].elements["c"+i].value;
                    var newposj=document.forms["f"].elements["pj"+i].value;
                    var widgetX=document.forms["f"].elements["x"+i].value;
                    var widgetY=document.forms["f"].elements["y"+i].value;
                    var vars=$p.string.esc(document.forms["f"].elements["v"+i].value);
                    var blocked=document.forms["f"].elements["b"+i].value;
                    var minimized=document.forms["f"].elements["m"+i].value;
                    
                    paramsList+='&i'+i+'='+wid
                                +'&c'+i+'='+newcol
                                +'&p'+i+'='+newpos
                                +'&pj'+i+'='+newposj
                                +'&x'+i+'='+widgetX
                                +'&y'+i+'='+widgetY
                                +'&v'+i+'='+vars
                                +'&b'+i+'='+blocked
                                +'&m'+i+'='+minimized;
                }
            }

            $p.ajax.call(padmin["scr_pages_modify_add"],
                {
                    'type':'execute',
                    'variables':paramsList,
                    'callback':
                    {
                        'function':$p.admin.pages.getTabs
                    }
                }
            );
            return false;
        }             
    },
    /*
                    Function:   $p.admin.pages.loadModify
                                    
                                       Load the page information
                            
                    Parameters:
                    
                                      page_id - page_id (foreign key)
                    
                    Returns:
                    
                                    false
          */
    loadModify:function(page_id,group)
    {
        $p.ajax.call(padmin["xml_load_page_infos"]+'?pageid='+page_id+"&group="+group,
            {
                'type':'load',
                'callback':
                {
                    'function':$p.admin.pages.modify
                }
           }  
        );
        return false;
    },
    modify:function(response,vars)
    {   
        $p.admin.tools.emptyContent();
        var l_s="";
        var l_i=0;
        var pageid=$p.ajax.getVal(response,"pageid","int",false,0);
        var group=$p.ajax.getVal(response,"group","int",false,0);
    	var name=$p.ajax.getVal(response,"name","str",false,"---");
    	var desc=$p.ajax.getVal(response,"description","str",false,"---");
    	var mode=$p.ajax.getVal(response,"position","int",false,0);
        var typep=$p.ajax.getVal(response,"type","int",false,0);
        var param=$p.ajax.getVal(response,"param","str",false,"/");
    	var nbcol=$p.ajax.getVal(response,"nbcol","int",false,3);
    	var showtype=$p.ajax.getVal(response,"showtype","int",false,0);
    	var npnb=$p.ajax.getVal(response,"npnb","int",false,15);
    	var style=$p.ajax.getVal(response,"style","int",false,1);
    	var moduleAlign=$p.ajax.getVal(response,"modulealign","str",false,"Y");
    	var controls=$p.ajax.getVal(response,"controls","str",false,"Y");
    	var icon=$p.ajax.getVal(response,"icon","str",false,"");
    	var removable=$p.ajax.getVal(response,"removable","int",false,1);
        widgetDecalX=0;
        $p.app.menu.initialized=false;

		$p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':lg('tabMgmt'),'link':'','fct':'$p.admin.pages.buildPage();return false;'},
				{'label':lg('NewPageManageOptions'),'link':'','fct':''}
			]
		));    
        l_s+='<div id="moveddiv" style="display: none;position: absolute;cursor: move;background: #c6c3c6;padding-bottom:0px;"></div>\
            <table cellpadding="10" cellspacing="0" border="0" width="100%">\
            <tr>\
            <td valign="top" style="border-bottom:1px solid #c6c3c6">';
            //<h3>'+lg("NewPageManageOptions")+'</h3>\
        l_s+='<form id="f">\
            <input type="hidden" name="act" value="upd" />\
            <input type="hidden" name="id" value="'+pageid+'" />\
            <input type="hidden" name="group" value="'+group+'" />\
            <table cellpadding="4" cellspacing="0">\
            	<tr><td width="8%">'+lg("name")+' : </td><td><input type="text" name="name" maxlength="30" value="'+name+'" /></td></tr>\
            	<tr><td width="8%">'+lg("description")+' : </td><td><textarea name="desc" rows="2" cols="60">'+desc+'</textarea></td></tr>\
                '+$p.admin.pages.defineDefaultMode(mode)+'\
            	<tr><td width="8%">'+lg("type")+' : </td><td>\
                '+$p.admin.pages.defineSelectType(typep)+'\
                &nbsp; <span id="paramctl"></span><span id="moduleslist"></span></td></tr>\
                <tr>\
                    <td width="8%">'+lg("lblIcon")+' :</td>\
                    <td>\
                        <div id="pageicons" style="width:60%"></div>\
                        <input type="hidden" id="hiddenIconValue" name="hiddenIconValue" />\
                    </td>\
                </tr>\
                '+$p.admin.pages.defineRemovable(removable)+'\
                <tr>\
        			<td>'+lg("application")+' :</td>\
        			<td id="moreOpt">\
                    <input type="hidden" value="0" id="hiddenformaction" name="hiddenformaction" />\
                    <input type="checkbox" name="formaction" value="3" onclick=javascript:$("hiddenformaction").value=this.value; />'+lg("lblReplaceOldtab")+'<br /></td>\
        		</tr>\
            	<tr><td colspan="2"><input class="btn" id="savePage" type="button" value="'+lg("saveNewPage")+'" onclick="$p.admin.pages.submitAddPage();return false;" /></td></tr>\
                </table>\
                </form>\
                </td>\
                </tr>\
                </table>\
                '+$p.admin.pages.displayPortalConfigArea()+'\
                </div>';
                l_s+=$p.admin.pages.buildPortalDivs();
                
        $('content').innerHTML=l_s;

        while (response.getElementsByTagName("module")[l_i])
        {
            var l_result    =response.getElementsByTagName("module")[l_i];
            var posx        =$p.ajax.getVal(l_result,"posx","int",false,0);
            var posy        =$p.ajax.getVal(l_result,"posy","int",false,0);
            var posj        =$p.ajax.getVal(l_result,"posj","int",false,0);
            var height      =$p.ajax.getVal(l_result,"height","int",false,0);
            var item_id     =$p.ajax.getVal(l_result,"item_id","int",false,0);
            var website     =$p.ajax.getVal(l_result,"website","str",false,"---");
            var name        =$p.ajax.getVal(l_result,"name","str",false,"---");
            var variables   =$p.ajax.getVal(l_result,"variables","str",false,"---");
            var minwidth    =$p.ajax.getVal(l_result,"minwidth","int",false,280);        
            var sizable     =$p.ajax.getVal(l_result,"sizable","str",false,"1");
            var x           =$p.ajax.getVal(l_result,"x","int",false,0);
            var y           =$p.ajax.getVal(l_result,"y","int",false,0);
            var uniq        =$p.ajax.getVal(l_result,"uniq","int",false,1);
            var format      =$p.ajax.getVal(l_result,"format","str",false,'I');
            var nbvariables =$p.ajax.getVal(l_result,"nbvariables","int",false,0);
            var blocked     =$p.ajax.getVal(l_result,"blocked","int",false,0);
            var minimized   =$p.ajax.getVal(l_result,"minimized","int",false,0);
            var variables   =$p.ajax.getVal(l_result,"variables","str",false,"");
            var url         =$p.ajax.getVal(l_result,"url","str",false,"");
            var l_views     = $p.ajax.getVal(l_result,"views","str",false,'home');
            var l_icon      = $p.ajax.getVal(l_result,"icon","str",false,indef);
            var l_l10n      = $p.ajax.getVal(l_result,"l10n","str",false,'');

            tab[$p.app.tabs.sel].module[l_i]=new $p.app.widgets.object(
                            posx,
                            posy,
                            posj,
                            height,
                            item_id,
                            website,
                            name,
                            variables,
                            minwidth,
                            sizable,
                            minwidth,
                            url,
                            x,
                            y,
                            uniq,
                            format,
                            nbvariables,
                            tab[$p.app.tabs.sel].id,
                            blocked,
                            minimized,
                            0,      //usereader
                            0,      //autorefresh
                            false, //is loaded status of the module (indef=not loaded, false=loading, true=loaded)
                            indef, //header
                            indef, //footer
                            indef, //auth  for RSS authentified feeds
                            l_views,  //views (home or canvas) canvas for full-screen (full-portal)
                            l_l10n    //lang parameters for l10n widgets                              
                            );
			
            l_i++;
        }

        $p.app.menu.config.showIcons();
        if (__menuposition=='v')    {
            $('verticalMenu').innerHTML='<div id="vmenu">\
                                            <div id="vmenuoptions"></div>\
                                            <div id="vmenusuboptions"></div>\
                                            <div id="vmenucontent"></div>\
                                         </div>';
        }
        $p.admin.pages.showHideBox(document.forms[0].mode.value);
        var allowSave=false,jspass="";
        $p.admin.pages.admParam=param;
        $p.app.widgets.showAdminProperties=true;
        tab[$p.app.tabs.sel].showType=showtype;
        tab[$p.app.tabs.sel].newspapernb=npnb;
        tab[$p.app.tabs.sel].colnb=nbcol;
        tab[$p.app.tabs.sel].style=style;
        tab[$p.app.tabs.sel].type=3;
        tab[$p.app.tabs.sel].root=$p.get("modules");        
        tab[$p.app.tabs.sel].moduleAlign=(moduleAlign=="Y"?"true":"false");
        tab[$p.app.tabs.sel].controls=controls;
        tab[$p.app.tabs.sel].editable=true;
        __useNotation=false;
        createDivDynamically=false; //added with 1.4.3 due to issue with appendChild and IE
        $p.admin.pages.typeSelection();
        document.forms["f"].name.focus();
        for (var i=2;i<=__themeList.length;i++)
        {	/*FRI PHP-Parametrierung */
        	$p.styles.addCssFile("../styles/main"+i+".css.php","style "+i,"alternate stylesheet");
        }
        //$p.styles.setActiveStyleSheet(style); 
    },
    displayPortalConfigArea:function()
    {
        var l_s='';
        l_s+='<h3 id="TitleManageWidget" style="display:block">'+lg("NewPageManageWidgets")+'</h3>\
            <div id="cache" style="position:absolute;left:0;top:0;z-index:8;display:none;"></div>\
            <div id="modulesctl"></div>\
            <table cellpadding="0" cellspacing="0" border="0" style="width:100%;">\
                <tr>\
                <td id="header" style="border-top: 3px solid rgb(198, 195, 198); border-bottom: 1px solid rgb(198, 195, 198); display: block;">\
                <table cellpadding="0" cellspacing="0" border="0" width="100%">\
                    <tr>\
                    <td id="logo"><a href="#"><img id="logolink" src="../images/s.gif" /></a></td>\
                    <td valign="bottom">\
                    <div id="profile" class="index">\
                    <div id="headlink"></div>\
                    </div>\
                    </td>\
                    </tr>\
                </table>\
                </td>\
                </tr>\
            </table>';
            
        return l_s;
    },
    /*
             Function: $p.admin.pages.defineRemovable
             
                             Display the option removable page
             
             Parameters:
                    
                             removable - is the page removable (0=no 1=yes)
             
             Returns:
             
                             HTML code
         */  
    defineRemovable:function(removable)
    {
        var l_s="";
        var check=(removable==0)?'':'checked=checked';
        l_s+='<tr>\
                <td width="8%">'+lg("lblPersonalize")+'</td>\
                <td><input type="checkbox" name="removable" '+check+' />'+lg("lblRemovable")+'</td>\
              </tr>';
            
        return l_s; 
    },
    /*
             Function: $p.admin.pages.defineSelectType
             
                             Display the type select list
             
             Parameters:
                    
                             typep - page type (1,2,3,4)
             
             Returns:
             
                             HTML code
         */  
    defineSelectType:function(typep)
    {
        var l_s="";
        var selectT1="";
        var selectT2="";
        var selectT3="";
        var selectT4="";
        if (typep==1) { selectT1="selected"; }
        if (typep==2) { selectT2="selected"; }
        if (typep==3) { selectT3="selected"; }
        if (typep==4) { selectT4="selected"; }
        
        l_s+='<select name="type" onchange="$p.admin.pages.typeSelection();">\
            <option value="1" '+selectT1+' >'+lg("personalizedPortal")+'</option>\
            <option value="2" '+selectT2+'>'+lg("htmlPage")+'</option>\
            <option value="3" '+selectT3+'>'+lg("javascriptFct")+'</option>\
            <option value="4" '+selectT4+'>'+lg("redirection")+'</option>\
            </select>';
            
        return l_s; 
    },
    /*
             Function: $p.admin.pages.buildPortalDivs
             
                             Display the defaultmode
             
             Parameters:
                    
                             mode - anonymous or connected (1,2)
             
             Returns:
             
                             HTML code
         */  
    defineDefaultMode:function(mode)
    {
        var selectM1="";
        var selectM2="";
        var defaultMode="";
        if (mode==1) selectM1="selected";
        if (mode==2) selectM2="selected";
        if (__defaultmode=="connected")
            defaultMode='<tr><td><input type="hidden" name="mode" value="1" /></td></tr>';
        else {
            defaultMode='<tr><td>'+lg("mode")+' : </td><td>\
                         <select name="mode" OnChange=$p.admin.pages.showHideBox(this.value);>\
                         <option value="1" '+selectM1+'>'+lg("anonymous")+'</option>\
                         <option value="2" '+selectM2+'>'+lg("connected")+'</option>\
                         </select></td></tr>';
        }
        return defaultMode;
    },
    /*
             Function: $p.admin.pages.buildPortalDivs
             
                             Display the divs for the virtual portal
             
             Returns:
             
                             HTML code
         */  
    buildPortalDivs:function()
    {
        var l_s='';
        l_s+='<div id="area">\
            	<div id="hmenu">\
            		<table id="hmenucontainer">\
            			<tr>\
            				<td class="hmenucontainertd">\
            					<div id="hmenuoptions"></div>\
            				</td>\
            				<td class="hmenucontainertd">\
            					<div id="hmenusuboptions"></div>\
            				</td>\
            				<td class="hmenucontainertd">\
            					<div id="hmenucontent"></div>\
            				</td>\
            				<td valign="top">\
            					<div id="hmenusubcontent"></div>\
            				</td>\
            			</tr>\
            		</table>\
            	</div>\
            	<div id="menus">\
            		<div id="box"></div>\
            		<div id="newmod"></div>\
            	</div>\
                <div id="verticalMenu"></div>\
                <div id="modules"></div>\
                <div id="newspaper"></div>\
            </div>';
                      
        return l_s;
    },
    /*
         Function: $p.admin.pages.suppress
         
                         Supress a page
                         
         Parameters:
         
                         v_id - page id
                         v_name - page name
                         v_group - group id
         
         Returns:
         
                         false
         */  
    suppress:function(v_id,v_name,v_group)
	{
		if (v_group==indef) v_group=0;
		l_response=confirm(lg("msgPagesSup",unescape(v_name)));
		if (l_response==1)
		{
             $p.ajax.call(padmin["scr_pages_suppress"],
                {
                    'type':'execute',
                    'variables':"id="+v_id+"&group="+v_group,
                    'callback':
                    {
                        'function':$p.admin.pages.getTabs
                    }
                }
            );             
		}
		return false;
	},
    /*
         Function: $p.admin.pages.buildGroupSelect
         
                         Build the available groups list
         
         Returns:
         
                         HTML code
         */  
    buildGroupSelect:function()
    {
        var l_s="";
        //executescr pages_tabs.php
        l_s+='<form name="f">'+lg("assignToGroup")+' : \
                <select name="group"  onchange=\'$p.admin.pages.getTabs(this.value);\'><option value="0">--'+lg("pageDefault")+'--</option>';
        for (var w in $p.admin.pages.groups) 
        {
            if ($p.admin.pages.group==w) {  var selected=' selected="selected"'; }
            else    {   var selected='';  }
            l_s+='<option value="'+w+'" '+selected+'>'+$p.admin.pages.groups[w]['name']+'</option>';
        }
        l_s+='  </select>\
              </form><br />';

        return l_s;
    },
    /*
         Function: $p.admin.pages.setOnline
         
                         Set pages online
         */ 
	setOnline:function()
	{
		executescr(padmin["scr_pages_setonline"],"",true);	
	},
    /*
         Function: $p.admin.pages.loadPagesInfos
         
                         Load pages informations
         
         Returns:
         
                    false
         */   
    loadPagesInfos:function()
    {
        $p.ajax.call(padmin["xml_get_pages_infos"],
            {
                'type':'load',
                'asynchro':true,
                'forceExecution':true,
                'priority':1,
                'callback':
                {
                    'function':$p.admin.pages.getPagesInfos
                }
            }	
        );
    },
    /*
         Function: $p.admin.pages.getPagesInfos
         
                         get pages infromations
         */  
    getPagesInfos:function(response,vars)
    {
        var pagesList = "";
        //Default portail pages name
        var l_result=response.getElementsByTagName("page");
        if (l_result.length!=0) 
		{
			for (var j=0;j<l_result.length;j++)
			{
				var l_name=$p.ajax.getVal(l_result[j],"name","str",false,"");   
                pagesList+=l_name+' | ';
            }
        }
        //Number of available portals
      //  var nbPortals=$p.ajax.getVal(response,"availablePortals","int",false,0);
        //Number of awaiting portals
      //  var nbAwaitingPortals=$p.ajax.getVal(response,"awaitingPortals","int",false,0);
        
        $p.admin.pages.pagesInfos['pagesList']=pagesList;
      //  $p.admin.pages.pagesInfos['availablePortals']=nbPortals;
      //  $p.admin.pages.pagesInfos['awaitingPortals']=nbAwaitingPortals;
    }
}

//************************************* CONFIG  FUNCTIONS ***************************************************************************************************************
/*
    Class: $p.admin.config
         config functions
*/
$p.admin.config={
    tabname:'',
    configSubTabs:[],
    parameters:{},
    paramList:{},
    languages:{},
    pluginsList:{},
    
    buildPage:function()
    {
        $p.app.tabs.sel=4;
        $p.app.tabs.select(4);
        $p.admin.tools.emptyContent();  
        $p.admin.config.tabname='configstab';
        $p.admin.config.defineSubTabs();
		$p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':lg('appConfiguration'),'link':'','fct':''}
			]
		)); 

        $p.admin.buildSubTabs($p.admin.config.configSubTabs,$p.admin.config.tabname);
        if ($p.app.tabs.sel && $p.app.tabs.sel > 0) {$p.admin.setState("$p.app.tabs.open("+$p.app.tabs.sel+")");}
    },
    /*
                Function: $p.admin.config.defineSubTabs
                
                                define the configuration subtabs
         */   
    defineSubTabs:function()
    {
        var tabTmp=new Array;
        for (var i=0;i<$p.admin.subTabs[$p.admin.config.tabname].length;i++)
        {
            tabTmp.push({'label':$p.admin.subTabs[$p.admin.config.tabname][i]['label'],
                         'fct':$p.admin.subTabs[$p.admin.config.tabname][i]['fct'],
                         'description':$p.admin.subTabs[$p.admin.config.tabname][i]['description']});
        } 
        $p.admin.config.configSubTabs[$p.admin.config.tabname]=tabTmp;         
    },
    /*
                Function: $p.admin.config.generalsettings
                
                                General settings sub tab section
         */ 
    generalsettings:function()
    {
        $p.app.tabs.sel=4;
        $p.app.tabs.select(4);
        if ($p.app.tabs.sel && $p.app.tabs.sel > 0) {$p.admin.setState("$p.app.tabs.open("+$p.app.tabs.sel+")");}
        $p.ajax.call(padmin["xml_config_general_get_values"],
            {
                'type':'load',
                'callback':
                {
                    'function':$p.admin.config.treat_generalsettings
                }
           }  
        );
        return false;        
    },
    treat_generalsettings:function(response,vars)
    {
        var l_i=0;
        var label=""
        var valeur="";
        var category="";
        var datatype="";
        var tabTmp=new Array;
        var oldcat="";
        
        while (response.getElementsByTagName("parameter")[l_i])
        {
            var l_result=response.getElementsByTagName("parameter")[l_i]; 
            label=$p.ajax.getVal(l_result,"label","str",false,"---");
            datatype=$p.ajax.getVal(l_result,"datatype","str",false,"str");
            valeur=$p.ajax.getVal(l_result,"valeur","str",false,""); 
            if (datatype=='int' && valeur=='') { 
                valeur=0; 
            }
            category=$p.ajax.getVal(l_result,"category","str",false,"");
            
            if(l_i==0) { 
                oldcat=category; 
            }
            if (category!=oldcat) { 
                $p.admin.config.parameters[oldcat]=tabTmp;
                oldcat=category;
                var tabTmp=new Array; 
                tabTmp.push({'label':label,'valeur':valeur});
            } 
            else { tabTmp.push({'label':label,'valeur':valeur}); }
            
            l_i++;  
        }
        $p.admin.config.parameters[category]=tabTmp;
        $p.admin.config.buildGeneralSettings();
    },
    buildGeneralSettings:function()
    {
        var l_s='';
		$p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':lg('appConfiguration'),'link':'','fct':'$p.admin.config.buildPage();return false;'},
				{'label':lg('appGeneralConfiguration'),'link':'','fct':''}
			]
		));
        
        l_s+='<form name="f">';
        l_s+=$p.admin.config.buildGeneralSettingsApp();
        l_s+=$p.admin.config.buildGeneralSettingsBdd();
        l_s+=$p.admin.config.buildGeneralSettingsInterface();
        l_s+=$p.admin.config.buildGeneralSettingsPortal();
        l_s+=$p.admin.config.buildGeneralSettingsFeed();
        l_s+=$p.admin.config.buildGeneralSettingsModules();
        l_s+=$p.admin.config.buildGeneralSettingsemailSending();
        l_s+='<p class="submit"><input type="button" value="'+lg("saveModifications")+'" onclick="$p.admin.config.setGeneralSettings();return false;" />\
              <a href="#" onclick="$p.admin.config.buildConfigGeneralAdvanced();return false;">'+lg("advancedOptions")+'</a></p></form>';
         
        $('content').innerHTML=l_s;  
    },
    /*
                Function: $p.admin.config.controlSingleParameters
                
                                Control a specific category parameters in the form
                                
                Parameters:
               
                                label - label name
                                
               Returns :
               
                                paramList(string) -  like label=value& as value beeing the document form value for the label
         */ 
    controlSingleParameters:function(label)
    {
        var paramList="";
        var granted=0;
        if (document.forms['f'].elements[label].type=='text') { granted=1; }
        else if (document.forms['f'].elements[label].type=='textarea') { granted=1;  }
        else if (document.forms['f'].elements[label].type=='hidden') { granted=1;  }
        else if (document.forms['f'].elements[label].type=='password') { granted=1;   }
        else if (document.forms['f'].elements[label].type=='checkbox') {
            if (document.forms['f'].elements[label].checked) { granted=1; }
        }
        else {     
                var taille = document.forms['f'].elements[label].length;
                for(j=0;j<taille;j++){
                      if(document.forms['f'].elements[label][j].checked) { paramList+=label+'='+document.forms['f'].elements[label][j].value+'&'; }
                }        
        }
        if (granted==1) {   paramList+=label+'='+document.forms['f'].elements[label].value+'&';  } 
       return paramList;
    },
    /*
                Function: $p.admin.config.controlParameters
                
                                Control all the category parameters in the form
                                
                Parameters: 
                
                                category - category name
                                
                Returns:
                
                                paramList(string) -  like label=value& as value beeing the document form value for the label
         */  
    controlParameters:function(category)
    {
        var paramList="";
        for (var i=0;i<$p.admin.config.parameters[category].length;i++)
        {
            var label=$p.admin.config.parameters[category][i]['label'];
            paramList+=$p.admin.config.controlSingleParameters(label);
        }
        return paramList;
    },
    /*
                Function: $p.admin.config.setGeneralSettings
                
                                Post the configuration form values
         */     
    setGeneralSettings:function()
    {
        var paramList='';
        paramList+=$p.admin.config.controlParameters("yourApplication");
        paramList+=$p.admin.config.controlParameters("adminInterface");
        paramList+=$p.admin.config.controlParameters("thePortals");
        paramList+=$p.admin.config.controlParameters("theFeeds");
        paramList+=$p.admin.config.controlParameters("theModules");
        paramList+=$p.admin.config.controlParameters("emailSending");
        
        if ($p.admin.config.paramList['useproxy']=='true')  {  
            paramList+=$p.admin.config.controlParameters("dbConnection");  
            paramList+=$p.admin.config.controlSingleParameters('proxyuser');
            paramList+=$p.admin.config.controlSingleParameters('proxypass');
        }
        else {
            //get only basic informations
            paramList+=$p.admin.config.controlSingleParameters('SERVER');
            paramList+=$p.admin.config.controlSingleParameters('LOGIN');
            paramList+=$p.admin.config.controlSingleParameters('DB');
            paramList+=$p.admin.config.controlSingleParameters('PASS');
        }
        paramList=paramList.substr(0,paramList.length-1);
        
        $p.ajax.call(padmin["scr_config_general"],
            {
                'type':'execute',
                'variables':paramList,
                'callback':
                {
                    'function':$p.admin.generateConfigFiles
                }
            }
        );
        return false;
        
    },    
    /*
                Function: $p.admin.config.buildParamList
                
                                Build a hash of parameters for a category
                                
                Parameters
                
                                category - category name
         */   
    buildParamList: function(category)
    {
        for (var i = 0;i < $p.admin.config.parameters[category].length;i ++)
        {
            var label = $p.admin.config.parameters[category][i]['label'];
            var valeur = $p.admin.config.parameters[category][i]['valeur'];
            $p.admin.config.paramList[label] = valeur;   
        }  
    },
    /*
                Function: $p.admin.config.buildGeneralSettingsemailSending
                
                                Build the configuration fields for the email notification parameters
                                
                Returns:
                
                                l_s - HTML code
         */ 
    buildGeneralSettingsemailSending:function()
    {
        var l_s="";
        var category="emailSending";
        var selected="";
        $p.admin.config.buildParamList(category);   
       
        l_s+='<div class="box">\
        	<table cellpadding="0" cellspacing="0">\
        		<tr><td colspan="2"><strong>'+lg("emailSending")+' :</strong><br /><br /></td></tr>\
        		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("supportEmail")+'</td><td><input type="text" name="SUPPORTEMAIL" value="'+$p.admin.config.paramList['SUPPORTEMAIL']+'" size="40" /></td></tr>\
        		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("notificationEmail")+'</td><td><input type="text" name="NOTIFICATIONEMAIL" value="'+$p.admin.config.paramList['NOTIFICATIONEMAIL']+'" size="40" /></td></tr>\
                <tr><td class="label"><input type="hidden" name="FRIENDEMAIL" value="friend@email.com" size="40" /></td></tr>\
        	</table>\
        	</div>';
        
        return l_s;
    },
    /*
                Function: $p.admin.config.buildGeneralSettingsModules
                
                                Build the configuration fields for the widgets parameters
                                
                Returns:
                
                                l_s - HTML code
         */ 
    buildGeneralSettingsModules:function()
    {
        var l_s="";
        var category="theModules";
        var selected="";
        $p.admin.config.buildParamList(category);   
          
        var useoverviewSel="";
        if ($p.admin.config.paramList["useoverview"]=="true") { useoverviewSel="checked=checked"; }
        var showiconSel="";
        if ($p.admin.config.paramList["showicon"]=="true") { showiconSel="checked=checked"; }       
        var displayAllLanguageModulesSel="";
        if ($p.admin.config.paramList["displayAllLanguageModules"]=="true") { displayAllLanguageModulesSel="checked=checked"; }  
        var showModuleRefreshSel="";
        if ($p.admin.config.paramList["showModuleRefresh"]=="true") { showModuleRefreshSel="checked=checked"; }  
        var showModuleCloseSel="";
        if ($p.admin.config.paramList["showModuleClose"]=="true") { showModuleCloseSel="checked=checked"; }        
        var showModuleConfigureSel="";
        if ($p.admin.config.paramList["showModuleConfigure"]=="true") { showModuleConfigureSel="checked=checked"; } 
        var showModuleMinimizeSel="";
        if ($p.admin.config.paramList["showModuleMinimize"]=="true") { showModuleMinimizeSel="checked=checked"; } 
        var showModuleTitleSel="";
        if ($p.admin.config.paramList["showModuleTitle"]=="true") { showModuleTitleSel="checked=checked"; } 
         
    	l_s+='<div class="box">\
    	<table cellpadding="0" cellspacing="0">\
    		<tr><td colspan="2"><strong>'+lg("theModules")+' :</strong><br /><br /></td></tr>\
    		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("maxNbMod")+'</td><td><input type="text" name="maxModNb" size="2" maxlength="2" value="'+$p.admin.config.paramList['maxModNb']+'" /></td></tr>\
    		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("showoverview")+'</td><td><input type="checkbox" name="useoverview" '+useoverviewSel+' /></td></tr>\
    		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("showicon")+'</td><td><input type="checkbox" name="showicon" '+showiconSel+' /></td></tr>\
    		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("defaultTxtNote")+'</td><td><textarea name="txtnote" cols="80" rows="5">'+$p.admin.config.paramList["txtnote"]+'</textarea></td></tr>\
    		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("rssRefreshDelay")+'</td><td><input type="text" name="rssrefreshdelay" size="2" maxlength="2" value="'+$p.admin.config.paramList['rssrefreshdelay']+'" /> '+lg("rssRefreshComment")+'</td></tr>\
    		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("displayAllLanguageModules")+'</td><td><input type="checkbox" name="displayAllLanguageModules" '+displayAllLanguageModulesSel+' /></td></tr>\
            <tr><td class="label"><img src="../images/puce.gif" /> '+lg("showModuleRefresh")+'</td><td><input type="checkbox" name="showModuleRefresh" '+showModuleRefreshSel+' /></td></tr>\
    		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("showModuleClose")+'</td><td><input type="checkbox" name="showModuleClose" '+showModuleCloseSel+' /></td></tr>\
    		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("showModuleConfigure")+'</td><td><input type="checkbox" name="showModuleConfigure" '+showModuleConfigureSel+' /></td></tr>\
    		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("showModuleMinimize")+'</td><td><input type="checkbox" name="showModuleMinimize" '+showModuleMinimizeSel+' /></td></tr>\
    		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("showModuleTitle")+'</td><td><input type="checkbox" name="showModuleTitle" '+showModuleTitleSel+' /></td></tr>\
    	</table>\
    	</div>';     
        
        return l_s;
    },
    /*
                Function: $p.admin.config.buildGeneralSettingsFeed
                
                                Build the configuration fields for the feeds parameters
                                
                Returns:
                
                                l_s - HTML code
         */ 
    buildGeneralSettingsFeed:function()
    {   
        var l_s="";
        var category="theFeeds";
        var selected="";
        $p.admin.config.buildParamList(category);
        
        var displayrssdesc0Sel="";
        var displayrssdesc1Sel="";
        var displayrssdesc2Sel="";
        if ($p.admin.config.paramList["displayrssdesc"]=="0") { displayrssdesc0Sel="checked=checked"; }
        if ($p.admin.config.paramList["displayrssdesc"]=="1") { displayrssdesc1Sel="checked=checked"; }
        if ($p.admin.config.paramList["displayrssdesc"]=="2") { displayrssdesc2Sel="checked=checked"; } 

        var displayrssimages0Sel="";
        var displayrssimages1Sel="";
        var displayrssimages2Sel="";
        if ($p.admin.config.paramList["displayrssimages"]=="0") { displayrssimages0Sel="checked=checked"; }
        if ($p.admin.config.paramList["displayrssimages"]=="1") { displayrssimages1Sel="checked=checked"; }
        if ($p.admin.config.paramList["displayrssimages"]=="2") { displayrssimages2Sel="checked=checked"; }
        
        var displayrsssourceSel="";
        if ($p.admin.config.paramList["displayrsssource"]=="true") { displayrsssourceSel="checked=checked"; }
    		
        l_s+='<div class="box">\
    		<table cellpadding="0" cellspacing="0">\
    			<tr><td colspan="2"><strong>'+lg("theFeeds")+' :</strong><br /><br /></td></tr>\
    			<tr><td class="label"><img src="../images/puce.gif" /> '+lg("displayrssdesc")+'</td><td><input type="radio" value="0" name="displayrssdesc" '+displayrssdesc0Sel+' />'+lg("no")+' <input type="radio" value="1" name="displayrssdesc" '+displayrssdesc1Sel+' />'+lg("articleSummary")+' <input type="radio" value="2" name="displayrssdesc" '+displayrssdesc2Sel+' />'+lg("completeArticle")+' </td></tr>\
    			<tr><td class="label"><img src="../images/puce.gif" /> '+lg("displayrssimages")+'</td><td><input type="radio" value="0" name="displayrssimages" '+displayrssimages0Sel+' />'+lg("none")+' <input type="radio" value="1" name="displayrssimages" '+displayrssimages1Sel+' />'+lg("theFirst")+' <input type="radio" value="2" name="displayrssimages" '+displayrssimages2Sel+' />'+lg("displayAll")+' </td></tr>\
    			<tr><td class="label"><img src="../images/puce.gif" /> '+lg("displayrsssource")+'</td><td><input type="checkbox" name="displayrsssource" '+displayrsssourceSel+' /></td></tr>\
    		</table>\
        </div>';
        
        return l_s;
    },
    /*
                Function: $p.admin.config.buildGeneralSettingsPortal
                
                                Build the configuration fields for the portal parameters
                                
                Returns:
                
                                l_s - HTML code
         */ 
    buildGeneralSettingsPortal:function()
    {
        var l_s="";
        var category="thePortals";
        $p.admin.config.buildParamList(category);

        var defaultmodeAnoSel="";
        var defaultmodeConSel="";
        if ($p.admin.config.paramList["defaultmode"]=="anonymous") { defaultmodeAnoSel="checked=checked"; }
        if ($p.admin.config.paramList["defaultmode"]=="connected") { defaultmodeConSel="checked=checked"; }
      
        var loadlatestpageonstart1Sel="";
        var loadlatestpageonstart2Sel="";
        var loadlatestpageonstart3Sel="";
        if ($p.admin.config.paramList["loadlatestpageonstart"]=="1") { loadlatestpageonstart1Sel="checked=checked"; }
        if ($p.admin.config.paramList["loadlatestpageonstart"]=="2") { loadlatestpageonstart2Sel="checked=checked"; }
        if ($p.admin.config.paramList["loadlatestpageonstart"]=="3") { loadlatestpageonstart3Sel="checked=checked"; }
        
        var menuDefaultStatus1Sel="";
        var menuDefaultStatus2Sel="";
        var menuDefaultStatus3Sel="";
        var menuDefaultStatus4Sel="";
        if ($p.admin.config.paramList["menuDefaultStatus"]=="1") { menuDefaultStatus1Sel="checked=checked"; }
        if ($p.admin.config.paramList["menuDefaultStatus"]=="2") { menuDefaultStatus2Sel="checked=checked"; }
        if ($p.admin.config.paramList["menuDefaultStatus"]=="3") { menuDefaultStatus3Sel="checked=checked"; }
        if ($p.admin.config.paramList["menuDefaultStatus"]=="4") { menuDefaultStatus4Sel="checked=checked"; }
        
        var accountTypeMailSel="";
        var accountTypeLoginSel="";
        if ($p.admin.config.paramList["accountType"]=="mail") { accountTypeMailSel="checked=checked"; }
        if ($p.admin.config.paramList["accountType"]=="login") { accountTypeLoginSel="checked=checked"; }
        
        var menupositionvSel="";
        var menupositionhSel="";
        if ($p.admin.config.paramList["menuposition"]=="v") { menupositionvSel="checked=checked"; }
        if ($p.admin.config.paramList["menuposition"]=="h") { menupositionhSel="checked=checked"; }
    
        var moduleAlignDefaultvSel="";
        var moduleAlignDefaulthSel="";
        if ($p.admin.config.paramList["moduleAlignDefault"]=="true") { moduleAlignDefaultvSel="checked=checked"; }
        if ($p.admin.config.paramList["moduleAlignDefault"]=="false") { moduleAlignDefaulthSel="checked=checked"; }

        var showHomeBar0Sel="";
        var showHomeBar1Sel="";
        var showHomeBar2Sel="";
        if ($p.admin.config.paramList["showHomeBar"]=="0") { showHomeBar0Sel="checked=checked"; }
        if ($p.admin.config.paramList["showHomeBar"]=="1") { showHomeBar1Sel="checked=checked"; }
        if ($p.admin.config.paramList["showHomeBar"]=="2") { showHomeBar2Sel="checked=checked"; }

        var addPagePermissionSel="";
        if ($p.admin.config.paramList["addPagePermission"]=="true") { addPagePermissionSel="checked=checked"; }

        var blockedModulePreventPageRemovalSel="";
        if ($p.admin.config.paramList["blockedModulePreventPageRemoval"]=="true") { blockedModulePreventPageRemovalSel="checked=checked"; }

        var useConditionsSel="";
        if ($p.admin.config.paramList["useConditions"]=="true") { useConditionsSel="checked=checked"; }
 
        var debugmodeSel="";
        if ($p.admin.config.paramList["debugmode"]=="true") { debugmodeSel="checked=checked"; }
 
        var passwordChangePermissionSel="";
        if ($p.admin.config.paramList["passwordChangePermission"]=="true") { passwordChangePermissionSel="checked=checked"; }
 
        var userChangePermissionSel="";
        if ($p.admin.config.paramList["userChangePermission"]=="true") { userChangePermissionSel="checked=checked"; }

        var captchaSel="";
        if ($p.admin.config.paramList["captcha"]=="true") { captchaSel="checked=checked"; }
         
    	l_s+='<div class="box">\
            <table cellpadding="0" cellspacing="0">\
        		<tr><td colspan="2"><strong>'+lg("thePortals")+' :</strong><br /><br /></td></tr>\
        		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("defaultmode")+'</td><td><input type="radio" name="defaultmode" value="anonymous" '+defaultmodeAnoSel+' />'+lg("portalscreen")+' <input type="radio" name="defaultmode" value="connected" '+defaultmodeConSel+' />'+lg("loginscreen")+'</td></tr>\
        		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("loadlatestpageonstart")+'</td><td><input type="radio" name="loadlatestpageonstart" value="1" '+loadlatestpageonstart1Sel+' />'+lg("loadlatestpageonstart1")+' <input type="radio" name="loadlatestpageonstart" value="2" '+loadlatestpageonstart2Sel+' />'+lg("loadlatestpageonstart2")+' <input type="radio" name="loadlatestpageonstart" value="3" '+loadlatestpageonstart3Sel+' />'+lg("loadlatestpageonstart3")+'</td></tr>\
        		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("menuDefaultStatus")+'</td><td><input type="radio" name="menuDefaultStatus" value="1" '+menuDefaultStatus1Sel+' />'+lg("menuDefaultStatus1")+' <input type="radio" name="menuDefaultStatus" value="2" '+menuDefaultStatus2Sel+' />'+lg("menuDefaultStatus2")+' <input type="radio" name="menuDefaultStatus" value="3" '+menuDefaultStatus3Sel+' />'+lg("menuDefaultStatus3")+'<input type="radio" name="menuDefaultStatus" value="4" '+menuDefaultStatus4Sel+' />'+lg("menuDefaultStatus4")+'</td></tr>\
        		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("accountType")+'</td><td><input type="radio" name="accountType" value="mail" '+accountTypeMailSel+' />'+lg("emails")+' <input type="radio" name="accountType" value="login" '+accountTypeLoginSel+' />'+lg("pseudos")+'</td></tr>\
                <tr><td class="label"><img src="../images/puce.gif" /> '+lg("menuPosition")+'</td><td><input type="radio" name="menuposition" value="v" '+menupositionvSel+' />'+lg("vertical")+' <input type="radio" name="menuposition" value="h" '+menupositionhSel+' />'+lg("horizontal")+'</td></tr>\
        		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("moduleAlignDefault")+'</td><td><input type="radio" name="moduleAlignDefault" value="true" '+moduleAlignDefaultvSel+' />'+lg("aligned")+' <input type="radio" name="moduleAlignDefault" value="false" '+moduleAlignDefaulthSel+' />'+lg("freePlacement")+'</td></tr>\
                <tr><td class="label"><img src="../images/puce.gif" /> '+lg("showHomebar")+'</td><td><input type="radio" name="showHomeBar" value="0" '+showHomeBar0Sel+' />'+lg("no")+' <input type="radio" name="showHomeBar" value="1" '+showHomeBar1Sel+' />'+lg("once")+' <input type="radio" name="showHomeBar" value="2" '+showHomeBar2Sel+' />'+lg("always")+' </td></tr>\
        		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("maxNbPages")+'</td><td><input type="text" name="maxPageNb" size="2" maxlength="2" value="'+$p.admin.config.paramList['maxPageNb']+'" /></td></tr>\
        		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("addPagePermission")+'</td><td><input type="checkbox" name="addPagePermission" '+addPagePermissionSel+' /></td></tr>\
        		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("blockedModulePreventPageRemoval")+'</td><td><input type="checkbox" name="blockedModulePreventPageRemoval" '+blockedModulePreventPageRemovalSel+' /></td></tr>\
        	    <tr><td class="label"><img src="../images/puce.gif" /> '+lg("useConditions")+'</td><td><input type="checkbox" name="useConditions" '+useConditionsSel+' /> ('+lg("useConditionsNote")+')</td></tr>\
        		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("showFooter")+'</td><td><textarea name="footer" cols="80" rows="5">'+$p.admin.config.paramList["footer"]+'</textarea></td></tr>\
        		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("debugmode")+'</td><td><input type="checkbox" name="debugmode" '+debugmodeSel+' /> <a href="#" onclick="$p.app.getDebugCookie();return false;">'+lg("getCookieDebug")+'</a> </td></tr>\
                <tr><td class="label"><img src="../images/puce.gif" /> '+lg("passwordChangePermission")+'</td><td><input type="checkbox" name="passwordChangePermission" '+passwordChangePermissionSel+' /></td></tr>\
        		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("userChangePermission")+'</td><td><input type="checkbox" name="userChangePermission" '+userChangePermissionSel+' /></td></tr>\
        		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("numberOfTry")+'</td><td><input type="text" size="2" maxlength="2" name="numberOfTry" value="'+$p.admin.config.paramList['numberOfTry']+'" /></td></tr>\
        		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("connectionDateRange")+'</td><td><input type="text" size="10" name="connectionDateRange" value="'+$p.admin.config.paramList['connectionDateRange']+'" /></td></tr>\
        		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("Captcha")+'</td><td><input type="checkbox" name="captcha" '+captchaSel+' /></td></tr>\
           </table>\
    	</div>';
        
        return l_s;
    },
    /*
                Function: $p.admin.config.buildGeneralSettingsInterface
                
                                Build the configuration fields for the interface parameters
                                
                Returns:
                
                                l_s - HTML code
         */ 
    buildGeneralSettingsInterface:function()
    {
        var l_s="";
        var category="adminInterface";
        $p.admin.config.buildParamList(category);
        
        var useGroupSel="";
        if ($p.admin.config.paramList['useGroup']=="true") useGroupSel="checked=checked"; 
        
        l_s+='<div class="box">\
            	<table cellpadding="0" cellspacing="0">\
            		<tr><td colspan="2"><strong>'+lg("adminInterface")+' :</strong><br /><br /></td></tr>\
            		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("manageUsers")+'</td><td><input type="checkbox" name="useGroup" '+useGroupSel+' /></td></tr>\
            	</table>\
              </div>';
    
        return l_s;
    },
    /*
                Function: $p.admin.config.buildGeneralSettingsBdd
                
                                Build the configuration fields for database parameters
                                
                Returns:
                
                                l_s - HTML code
         */ 
    buildGeneralSettingsBdd:function()
    {
        var l_s="";
        var category="dbConnection";
        var selected="";
        $p.admin.config.buildParamList(category);
        
        l_s+='<div class="box">\
        	<table cellpadding="0" cellspacing="0">\
        	<tr><td colspan=2><strong>'+lg("dbConnection")+' :</strong><br /><br /></td></tr>\
        	<tr><td class="label"><img src="../images/puce.gif" /> '+lg("server")+'</td><td><input type="text" name="SERVER" value="'+$p.admin.config.paramList['SERVER']+'" /></td></tr>\
        	<tr><td class="label"><img src="../images/puce.gif" /> '+lg("login")+'</td><td><input type="text" name="LOGIN" value="'+$p.admin.config.paramList['LOGIN']+'" /></td></tr>\
        	<tr><td class="label"><img src="../images/puce.gif" /> '+lg("password")+'</td><td><input type="password" name="PASS" value="xxxxxxx" /></td></tr>\
        	<tr><td class="label"><img src="../images/puce.gif" /> '+lg("db")+'</td><td><input type="text" name="DB" value="'+$p.admin.config.paramList['DB']+'" /></td></tr>\
        	</table>\
        	</div>';
        
        if ($p.admin.config.paramList['useproxy']=="true")  {
        	l_s+='<div class="box">\
        	<table cellpadding="0" cellspacing="0">\
        	<tr><td colspan=2><strong>'+lg("proxysettings")+' :</strong><br /><br /></td></tr>\
            <tr><td class="label"><input type="hidden" name="useproxy" value="'+$p.admin.config.paramList['useproxy']+'" ></td></tr>\
        	<tr><td class="label"><img src="../images/puce.gif" /> '+lg("proxypacfile")+'</td><td><input type="text" name="proxypacfile" value="'+$p.admin.config.paramList['proxypacfile']+'" /></td></tr>\
        	<tr><td class="label"><img src="../images/puce.gif" /> '+lg("server")+'</td><td><input type="text" name="PROXYSERVER" value="'+$p.admin.config.paramList['PROXYSERVER']+'" /></td></tr>\
        	<tr><td class="label"><img src="../images/puce.gif" /> '+lg("port")+'</td><td><input type="text" name="PROXYPORT" value="'+$p.admin.config.paramList['PROXYPORT']+'" /></td></tr>\
        	<tr><td class="label"><img src="../images/puce.gif" /> '+lg("login")+'</td><td><input type="text" name="proxyuser" value="xxxxxxx" /></td></tr>\
        	<tr><td class="label"><img src="../images/puce.gif" /> '+lg("password")+'</td><td><input type="password" name="proxypass" value="xxxxxxx" /></td></tr>\
        	</table>\
        	</div>';
        }     
        return l_s;    
    },
    /*
                Function: $p.admin.config.buildGeneralSettingsApp
                
                                Build the configuration fields of yourApplication parameters
                                
                Returns:
                
                                l_s - HTML code
         */ 
    buildGeneralSettingsApp:function()
    {
        var l_s="";
        var category="yourApplication";
        var selected="";
        $p.admin.config.buildParamList(category);
                               
        if ($p.admin.config.paramList['USERMODULE']=="I")  {    selected=" checked=checked";    }
        //<tr><td class="label"><img src="../images/puce.gif" /> <?php echo lg("appDomain");?></td><td><input type=text name="ipadd" value="<?php echo $ipadd;?>" size=60 /></td></tr>-->
        l_s+='<div class="box">\
            <table cellpadding="0" cellspacing="0">\
            <tr><td colspan="2"><strong>'+lg("yourApplication")+' :</strong><br /><br /></td></tr>\
            <tr><td class="label"><img src="../images/puce.gif" /> '+lg("appName")+'</td><td><input type="text" name="APPNAME" value="'+$p.admin.config.paramList['APPNAME']+'" size="60" /></td></tr>\
            <tr><td class="label"><img src="../images/puce.gif" /> '+lg("appRoot")+'</td><td><input type="text" name="LOCALFOLDER" value="'+$p.admin.config.paramList['LOCALFOLDER']+'" size="60" /></td></tr>\
            <tr><td class="label"><img src="../images/puce.gif" /> '+lg("usersCreateModule")+'</td><td><input type="checkbox" name="USERMODULE" '+selected+' /></td></tr>\
            </table>\
            </div>';
         
        return l_s;
    },
    buildConfigGeneralAdvanced:function()
    {
        var l_s="";
        $p.admin.tools.emptyContent();
		$p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':lg('appConfiguration'),'link':'','fct':'$p.admin.config.buildPage();return false;'},
				{'label':lg('appGeneralConfiguration'),'link':'','fct':'$p.admin.config.buildGeneralSettings();return false;'},
				{'label':lg('advancedOptions'),'link':'','fct':''}
			]
		));
        l_s+='<form name="f" >\
            <table>';
            
            for (var w in $p.admin.config.parameters) 
            {
                for (var i=0;i<$p.admin.config.parameters[w].length;i++)
                {
                    var label=$p.admin.config.parameters[w][i]['label'];
                    var valeur=$p.admin.config.parameters[w][i]['valeur'];
                    l_s+='<tr>\
                            <td>'+label+'</td>\
                            <td> <textarea rows="2" cols="80" name="'+label+'">'+valeur+'</textarea></td>\
                        </tr>';
                }  
            }   

        	l_s+='</table>\
                <p class="submit"><input type="button" value="'+lg("saveModifications")+'" onclick="$p.admin.config.setConfigGeneralAdvanced();return false;" /></p>\
                </form>';
                //launch_hook('admin_config_general_advanced');
                
                $p.plugin.hook.launch('admin_config_general_advanced');
                
        $('content').innerHTML=l_s;
    },
    setConfigGeneralAdvanced:function()
    {
        var paramList="";
        for (var w in $p.admin.config.parameters) 
        {
            for (var i=0;i<$p.admin.config.parameters[w].length;i++)
            {
                var label=$p.admin.config.parameters[w][i]['label'];
                var result=$p.Base64._utf8_encode(document.forms["f"].elements[label].value);
                result = result.replace(/\+/g, "%2b");
                paramList+=label+'='+result+'&';
            }  
        } 
        paramList=paramList.substr(0,paramList.length-1);
        $p.ajax.call(padmin["scr_config_general_advanced"],
            {
                'type':'execute',
                'variables':paramList,
                'callback':
                {
                    'function':$p.admin.generateConfigFiles
                }
            }
        );
        return false;
    },
    /*
                Function: $p.admin.config.themes
                
                                Themes sub tab section
         */ 
    themes:function()
    {
         $p.app.tabs.sel=4;
         $p.app.tabs.select($p.app.tabs.sel);
         $p.admin.tools.emptyContent();
         $p.admin.config.theme.getThemes(); 
         $p.admin.config.theme.regenerateCurrentTheme=false;
         if ($p.app.tabs.sel && $p.app.tabs.sel > 0) {$p.admin.setState("$p.app.tabs.open("+$p.app.tabs.sel+")");}
    },
    /*
                Function: $p.admin.config.plugins
                
                                Load the plugins
         */ 
    plugins:function()
    {
        $p.admin.tools.emptyContent();
        $p.ajax.call(padmin["xml_load_plugins"],
            {
                'type':'load',
                'callback':
                {
                    'function':$p.admin.config.buildPlugins
                }
            }  
        );
        return false;
    },
    /*
                Function: $p.admin.config.buildPlugins
                
                                Display the plugins form
                
                Parameters:
                
                                response - XML Object
                                vars (array) - 
         */     
    buildPlugins:function(response,vars)
    {   
        var l_s='';
        var l_i=0;
        $p.admin.tools.emptyContent();
        while (response.getElementsByTagName("plugin")[l_i])
        {
            /*
                                id (int) : plugin unique id (number of plugins red)
                             	name (string) : plugin name
                             	description (string)
                             	dependencies (string) : others plugins that have to be installed before
                             	file (string) : plugin file name (if the plugin is in the new format, else it's empty)
                             	dir (string) : dir when the plugin file is found
                             	link (string) : dir/file
                             	installed (boolean) : true if the plugin is already installed
                                */
            var l_result=response.getElementsByTagName("plugin")[l_i]; 
            var id=$p.ajax.getVal(l_result,"id","int",false,0);
            var name=$p.ajax.getVal(l_result,"name","str",false,"---");
            var description=$p.ajax.getVal(l_result,"description","str",false,"---");
            var dependencies=$p.ajax.getVal(l_result,"dependencies","str",false,"no");
            var file=$p.ajax.getVal(l_result,"file","str",false,"---");
            var dir=$p.ajax.getVal(l_result,"dir","str",false,"---");
            var link=$p.ajax.getVal(l_result,"link","str",false,"---");
            var installed=$p.ajax.getVal(l_result,"installed","int",false,0);
            var plugform=$p.ajax.getVal(l_result,"plugform","str",false,"");
            var display=$p.ajax.getVal(l_result,"display","str",false,"");
            
            $p.admin.config.pluginsList[id]={
                                                 'name':name,
                                                 'description':description,
                                                 'dependencies':dependencies,
                                                 'file':file,
                                                 'dir':dir,
                                                 'link':link,
                                                 'installed':installed,
                                                 'display':display,
                                                 'plugform':plugform
                                            };
                                            
            l_i++;
        } 
        $p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':lg('appConfiguration'),'link':'','fct':'$p.admin.config.buildPage();return false;'},
				{'label':lg('appPluginsConfiguration'),'link':'','fct':''}
			]
		));
        l_s+= '<div class="box">\
        	<form name="f">\
        	<h2>'+lg("plugins")+' :</h2>\
        	<table width="100%">';
            l_s+=$p.admin.config.displayPluginBox();
        	l_s+='</table>\
        	<p class="submit"><input type="button" onclick=$p.admin.config.setPlugins(); value="'+lg("saveModifications")+'" /></p>\
        	</form>\
        	</div>';
        
        $('content').innerHTML=l_s;
    },
    /*
            Function: $p.admin.config.displayPluginBox
            
                        Returns plugin form 
                        
            Returns:
            
                        HTML code
         */
    displayPluginBox:function()
    {
        var l_s='';
        for (var w in $p.admin.config.pluginsList)
        {
            var id=w;
            var name=$p.admin.config.pluginsList[w]['name'];
            var description=$p.admin.config.pluginsList[w]['description'];
            var dependencies=$p.admin.config.pluginsList[w]['dependencies'];
            var dir=$p.admin.config.pluginsList[w]['dir'];
            var file=$p.admin.config.pluginsList[w]['file'];
            var installed=$p.admin.config.pluginsList[w]['installed'];
            var link=$p.admin.config.pluginsList[w]['link'];
            var plugform=$p.admin.config.pluginsList[w]['plugform'];
            var display=$p.admin.config.pluginsList[w]['display'];
            var selected="";
               
            if (installed==1) 
            { 
                selected=" checked"; 
            }
            l_s+='<tr><td bgcolor="#efefef" width="30"><input type="checkbox" name="plug'+id+'" '+selected+' />';
            if (selected!="") 
            {
                l_s+='<input type="hidden" name="plugi'+id+'" value="1" />'; 
            }
            else {  
                l_s+='<input type="hidden" name="plugi'+id+'" value="0" />'; 
            }
            l_s+='<input type="hidden" name="name'+id+'" value="'+name+'" />';
        	l_s+='<input type="hidden" name="desc'+id+'" value="'+description+'" />';
        	l_s+='<input type="hidden" name="depend'+id+'" value="'+dependencies+'" />';
            if (file!="---")   
            {  
                l_s+='<input type="hidden" name="file'+id+'" value="'+dir+file+'" />'; 
            }
            l_s+='<input type="hidden" name="dir'+id+'" value="." />';
        	l_s+='<input type="hidden" name="link'+id+'" value="'+link+'" /></td>';
        	l_s+='<td bgcolor="#efefef"><strong>'+name+'</strong></td></tr><tr><td></td><td '+display+'>'+description+'</td></tr>';         
            if (dependencies.toLowerCase()!="no")  { 
                l_s+='<tr><td></td><td style="color:#ff0000">'+lg("dependencies")+' : <strong>'+dependencies+'</strong></td></tr>'; 
            }
            if (plugform) {
                l_s += '<tr><td colspan="2">'+plugform+'</td></tr>';
            }
        } 
        return l_s;
    },
    /*
            Function: $p.admin.config.setPlugins
            
                        Activate/Desactivate plugins
         */    
    setPlugins:function()
    {
        var paramList='';
        for (var w in $p.admin.config.pluginsList)
        {
            var id=w;
            var file=$p.admin.config.pluginsList[w]['file'];
            if (document.forms['f'].elements['plug'+id].checked)    {
                paramList+="plug"+id+"="+document.forms['f'].elements['plug'+id].value+"&";
            }
            paramList+="plugi"+id+"="+document.forms['f'].elements['plugi'+id].value+"&";
            paramList+="name"+id+"="+document.forms['f'].elements['name'+id].value+"&";
            paramList+="desc"+id+"="+document.forms['f'].elements['desc'+id].value+"&";
            paramList+="depend"+id+"="+document.forms['f'].elements['depend'+id].value+"&";
            if (file!="---")   {
                paramList+="file"+id+"="+document.forms['f'].elements['file'+id].value+"&";    
            }
            paramList+="dir"+id+"="+document.forms['f'].elements['dir'+id].value+"&";
            paramList+="link"+id+"="+document.forms['f'].elements['link'+id].value+"&";
        }
        paramList=paramList.substr(0,paramList.length-1);

        $p.ajax.call(padmin["scr_config_plugins"],
            {
                'type':'execute',
                'variables':paramList,
                'callback':
                {
                    'function':$p.admin.generateConfigFiles
                }
            }
        );
        return false;
    },
    /*
                Function: $p.admin.config.featuresaccess
                
                                load the menus configuration informations
         */ 
    featuresaccess:function()
    {
        $p.admin.tools.emptyContent();
        $p.ajax.call(padmin["xml_config_features"],
            {
                'type':'load',
                'callback':
                {
                    'function':$p.admin.config.buildFeaturesaccess
                }
            }  
        );
        return false;
    },
    buildFeaturesaccess:function(response,vars)
    {
        var menuOptions=[];
        var l_s="";  
        menuOptions['usereader']=$p.ajax.getVal(response,"usereader","str",false,"true");
        menuOptions['showtabicon']=$p.ajax.getVal(response,"showtabicon","str",false,"true");
        menuOptions['columnchange']=$p.ajax.getVal(response,"columnchange","str",false,"true");
        menuOptions['ctrlhiding']=$p.ajax.getVal(response,"ctrlhiding","str",false,"true");
        menuOptions['doubleprotection']=$p.ajax.getVal(response,"doubleprotection","str",false,"true");
        menuOptions['showrsscell']=$p.ajax.getVal(response,"showrsscell","str",false,"true");
        menuOptions['showModuleSearch']=$p.ajax.getVal(response,"showModuleSearch","str",false,"true");
        menuOptions['showModuleExpl']=$p.ajax.getVal(response,"showModuleExpl","str",false,"true");
        menuOptions['moduleAlign']=$p.ajax.getVal(response,"moduleAlign","str",false,"true");

		$p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':lg('appConfiguration'),'link':'','fct':'$p.admin.config.buildPage();return false;'},
				{'label':lg('featuresAccess'),'link':'','fct':''}
			]
		));
        l_s+= '<div class="box">\
            '+$p.admin.config.buildFeaturesaccessOptionsForm(menuOptions)+'\
            </div>\
            <div class="box">\
            '+$p.admin.config.buildFeaturesaccessContentForm(menuOptions)+'\
            </div>';
        
        $('content').innerHTML=l_s; 
    },
    buildFeaturesaccessContentForm:function(menuOptions)
    {
        var showrsscellSel=(menuOptions['showrsscell']=='true')?"checked=checked":"";
        var showModuleSearchSel=(menuOptions['showModuleSearch']=='true')?"checked=checked":"";
        var showModuleExplSel=(menuOptions['showModuleExpl']=='true')?"checked=checked":"";
        
        var l_s='<form id="f2" name="f2">\
            <h2>'+lg("addMenu")+'</h2>\
            <table>\
                <tr><td class="label"><img src="../images/puce.gif" /> '+lg("showRssAddCell")+'</td><td><input type="checkbox" name="showrsscell" '+showrsscellSel+' /></td></tr>\
                <tr><td class="label"><img src="../images/puce.gif" /> '+lg("showModuleSearch")+'</td><td><input type="checkbox" name="showModuleSearch" '+showModuleSearchSel+' /></td></tr>\
                <tr><td class="label"><img src="../images/puce.gif" /> '+lg("showModuleExplorer")+'</td><td><input type="checkbox" name="showModuleExpl" '+showModuleExplSel+' /></td></tr>\
            </table>\
            <input type="hidden" name="menuadd" value="1" />\
            <p class="submit"><input type="button" value='+lg("saveMenu")+' onclick=$p.admin.config.setFeaturesaccess("f2");return false; /></p>\
            </form>';
            
        return l_s;
    },
    buildFeaturesaccessOptionsForm:function(menuOptions)
    {
        var usereaderSel=(menuOptions['usereader']=='true')?"checked=checked":"";
        var columnchangeSel=(menuOptions['columnchange']=='true')?"checked=checked":"";
        var ctrlhidingSel=(menuOptions['ctrlhiding']=='true')?"checked=checked":"";
        var moduleAlignSel=(menuOptions['moduleAlign']=='true')?"checked=checked":"";
        var showtabiconSel=(menuOptions['showtabicon']=='true')?"checked=checked":"";
        var doubleprotectionSel=(menuOptions['doubleprotection']=='true')?"checked=checked":"";
       
        var l_s='<form id="f3" name="f3">\
             	<h2>'+lg("editMenu")+'</h2>\
            	<table>\
            		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("proposeReader")+'</td><td><input type="checkbox" name="usereader" '+usereaderSel+' /></td></tr>\
            		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("allowColumnChange")+'</td><td><input type="checkbox" name="columnchange" '+columnchangeSel+' /></td></tr>\
            		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("allowControlsHiding")+'</td><td><input type="checkbox" name="ctrlhiding" '+ctrlhidingSel+' /></td></tr>\
            		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("allowModuleNotAligned")+'</td><td><input type="checkbox" name="modulealign" '+moduleAlignSel+' /></td></tr>\
            		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("allowDoubleProtection")+'</td><td><input type="checkbox" name="doubleprotection" '+doubleprotectionSel+' /></td></tr>\
            		<tr><td class="label"><img src="../images/puce.gif" /> '+lg("showtabicon")+'</td><td><input type="checkbox" name="showtabicon" '+showtabiconSel+' /></td></tr>\
            	</table>\
            	<input type="hidden" name="menuedit" value="1" />\
             	<p class="submit"><input type="button" onclick=$p.admin.config.setFeaturesaccess("f3");return false; value="'+lg('saveMenu')+'" /></p>\
            	</form>';
    
        return l_s;
    },
    /*
                Function: $p.admin.config.setFeaturesaccess
                
                                Set the form informations (options / content)
                                
                Parameters:
                
                                formname - name of the form to post
                                
                Returns:
                
                                false
         */ 
    setFeaturesaccess:function(formname)
    {
        var paramList="";
        if (formname=="f2") {
            paramList="menuadd=1&";
            if (document.forms[formname].elements['showrsscell'].checked) { paramList+="showrsscell="+document.forms[formname].elements['showrsscell'].value+"&"; }
            if (document.forms[formname].elements['showModuleSearch'].checked) { paramList+="showModuleSearch="+document.forms[formname].elements['showModuleSearch'].value+"&"; }
            if (document.forms[formname].elements['showModuleExpl'].checked) { paramList+="showModuleExpl="+document.forms[formname].elements['showModuleExpl'].value+"&"; }   
        }
        else if (formname=="f3") {
            paramList="menuedit=1&";
            if (document.forms[formname].elements['usereader'].checked) { paramList+="usereader="+document.forms[formname].elements['usereader'].value+"&"; }
            if (document.forms[formname].elements['columnchange'].checked) { paramList+="columnchange="+document.forms[formname].elements['columnchange'].value+"&"; }
            if (document.forms[formname].elements['ctrlhiding'].checked) { paramList+="ctrlhiding="+document.forms[formname].elements['ctrlhiding'].value+"&"; }
            if (document.forms[formname].elements['modulealign'].checked) { paramList+="modulealign="+document.forms[formname].elements['modulealign'].value+"&"; }
            if (document.forms[formname].elements['doubleprotection'].checked) { paramList+="doubleprotection="+document.forms[formname].elements['doubleprotection'].value+"&"; }
            if (document.forms[formname].elements['showtabicon'].checked) { paramList+="showtabicon="+document.forms[formname].elements['showtabicon'].value+"&"; }        
        }
        paramList=paramList.substr(0,paramList.length-1);
        $p.ajax.call(padmin["scr_config_features"],
            {
                'type':'execute',
                'variables':paramList,
                'callback':
                {
                    'function':$p.admin.generateConfigFiles
                }
            }
        );
        return false;
    },
    /*
                Function: $p.admin.config.langtab
                
                                languages configuration sub tab section
         */ 
    langtab:function()
    {
        $p.ajax.call(padmin["xml_config_langselection"],
            {
                'type':'load',
                'callback':
                {
                    'function':$p.admin.config.buildLangtab
                }
           }  
        );
        return false;    
    },
    buildLangtab:function(response,vars)
    {
        var l_s="";
        var l_i=0;
        $p.admin.tools.emptyContentLang();
        while (response.getElementsByTagName("language")[l_i])
        {
            var tabTmp=new Array;
            var l_result=response.getElementsByTagName("language")[l_i]; 
            var item=$p.ajax.getVal(l_result,"item","str",false,"---");
            var inc=$p.ajax.getVal(l_result,"inc","int",false,0);
            var check=$p.ajax.getVal(l_result,"check","int",false,0);
            var select=$p.ajax.getVal(l_result,"select","int",false,0);
            tabTmp.push({'inc':inc,
                         'check':check,
                         'select':select});
            $p.admin.config.languages[item]=tabTmp; 
            l_i++;
        } 
        
		$p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':lg('appConfiguration'),'link':'','fct':'$p.admin.config.buildPage();return false;'},
				{'label':lg('langSelection'),'link':'','fct':''}
			]
		));
        l_s+='<div class="box">\
            <a id="btn1" class="optlist" style="font-size:11pt;font-weight:bold;" href="#" onclick=$p.admin.config.buildFormLanguages("1");return false;><span id="btn1nb">'+lg("langSelection")+'</span></a> |\
            <a id="btn2" class="optlist" style="font-size:11pt;font-weight:bold;" href="#" onclick=$p.admin.config.buildFormLanguages("2");return false;><span id="btn2nb">'+lg("langFile")+'</span></a>\
            </div>\
            <div id="contentBox" ><div class="box" id="notificationBox" width="980" height="150" style="border:1px solid #efefef">'+lg("lblLangSettings")+'</div></div>';
        
        $('content').innerHTML=l_s;    
    },
    /*
                Function: $p.admin.config.buildFormLanguages
                
                               build the different languages content sub tabs
                               
                Parameters:
                
                                id - id
                                
                 Returns:
                            
                                false
         */ 
    buildFormLanguages:function(id)
	{
		$p.admin.config.activateBtn(id);
        switch (id)
        {
            case '1':{
                $p.admin.config.buildLangSelection();
                break;}
            case '2':{
                 $p.admin.config.buildLangImport();
                  break;}
        }
		return false;
	},
    /*
                Function: $p.admin.config.buildLangSelection
                
                               Display the differents available languages for the application
         */ 
    buildLangSelection:function()
    {
        var l_s="";
        var cpt=0;
        l_s+='<div class="box" id="notificationBox" width="980" height="150" style="border:1px solid #efefef"><form name="f">\
            <p>\
            <table border="1">\
            <tr>\
            <th width="200"></th>\
            <th>'+lg("available")+'</th>\
            <th>'+lg("byDefault")+'</th>\
            </tr>';	
            for (var w in $p.admin.config.languages)
            {
                var item=w;
                var inc=$p.admin.config.languages[w][0]['inc'];
                var checked=($p.admin.config.languages[w][0]['check']==1)?"checked=checked":"";
                var selected=($p.admin.config.languages[w][0]['select']==1)?"checked=checked":"";
                l_s+='<tr>\
                        <td>'+item+'<input type="hidden" name="lang'+inc+'" value="'+item+'" /></td>\
                        <td><input type="checkbox" name="langsel'+inc+'" '+checked+' /></td>\
                        <td><input type="radio" name="langdefault" value="'+item+'" '+selected+' /></td>\
                    </tr>';
                cpt++;
            }
       l_s+='</table>\
            </p>\
            <p><input type="button" value="'+lg("save")+'" onclick=$p.admin.config.setLangSelection('+cpt+');return false; /></p>\
            </form></div>';
        
        $('contentBox').innerHTML=l_s;
    },
    /*
                Function: $p.admin.config.setLangSelection
                
                               set the differents available languages for the application
                               
                 Parameters:
                 
                                cpt - number of languages availables in ../l10n
                  
                 Returns:
                 
                                false
         */ 
    setLangSelection:function(cpt)
    {   
        var paramList="";
        for (var i=0;i<cpt;i++)
        {
            paramList+='lang'+i+'='+document.forms['f'].elements['lang'+i].value+'&';    
            if (document.forms['f'].elements['langsel'+i].checked)  {
                paramList+='langsel'+i+'='+document.forms['f'].elements['langsel'+i].value+'&';
            }
            if (document.forms['f'].elements['langdefault'][i].checked) {
                paramList+='langdefault='+document.forms['f'].elements['langdefault'][i].value+'&'; 
            }
        }
        if ($p.admin.config.checkLangSelection) {
            $p.ajax.call(padmin["scr_config_lang"],
                {
                    'type':'execute',
                    'variables':paramList,
                    'callback':
                    {
                        'function':$p.admin.generateConfigFiles
                    }
                }
            );
        }
        return false;
    },
    /*
                 Function: $p.admin.config.checkLangSelection
                
                               control the available languages form

                 Returns:
                 
                                true / false
         */
    checkLangSelection:function()
    {
        var i=0,langs=[];
        while (document.forms["f"].elements["lang"+i])
        {
            if (document.forms["f"].elements["langsel"+i].checked) langs.push(document.forms["f"].elements["lang"+i].value);
            i++;
        }
        if (langs.length==0)    {
            alert(lg("noLangSelected"));
            return false;
        }
        var selLang=$p.app.tools.getRadioValue(document.forms["f"].langdefault);
        if (!$p.array.find(langs,selLang))  {
            alert(lg("noDefaultLangSelected"));
            return false;
        }
        return true;
    },
	/*
                    Function: $p.admin.config.buildLangImport
                    
                                    Display the language importation section
         */  
    buildLangImport:function()
    {
        $p.admin.tools.emptyContentLang();
		$p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':lg('appConfiguration'),'link':'','fct':'$p.admin.config.buildPage();return false;'},
				{'label':lg('langSelection'),'link':'','fct':''}
			]
		));
        var l_s="";
        l_s+='<iframe id="langimport" src="'+padmin["frm_config_langimport"]+'" width="600" height="300" frameborder="0"></iframe>';      
        $('contentBox').innerHTML=l_s;
    },
	/*
                    Function: $p.admin.config.hideFrame
                    
                                    Hide the newmod div
         */  
	hideFrame:function()
	{
		$p.app.pages.clean($p.get("newmod"));
	},
	activateBtn:function(v_id)
	{
		for (var i=0;i<3;i++)
		{
			$p.setClass("btn"+i,"optlist");
		}
		$p.setClass("btn"+v_id,"sellist");	
	},
	/*
                    Function: $p.admin.config.hideDirOptions
                    
                                    Hide the directory div
         */                    
	hideDirOptions:function()
	{
		$p.print("directory","");
	},
	/*
                    Function: $p.admin.config.createLangMenu
                    
                                    Creates the lang menu 
                    
                    Parameters:
                    
                                    id - 
                                    lang - language
                    
                    Returns:
                    
                                    true
           */
	createLangMenu:function(id,lang)
	{
        var l_s='';
        for (var i=0;i<__AVLANGS.length;i++)
        {
            l_s+='<a class="optlist" onclick=$p.admin.config.initDir("'+id+'","'+__AVLANGS[i]+'");><strong>'+__AVLANGS[i]+'</strong></a> |';
        }
		$p.get("langBox").innerHTML=l_s;
		$p.admin.config.initDir(id,lang);
		return true;
	},
	/*
		Function: $p.admin.config.initDir
        
                                selects the default noticifaction's values in adm_mail
		
		Parameters :
        
			v_id - notification id ('validInscription','validWidget','getPassword'...)
				
		Returns: 
        
                                false				
	*/	
	initDir:function(v_id,lang)
	{
		$p.admin.config.activateBtn(v_id);
        $p.ajax.call(padmin["xml_config_notifications"]+"?lang="+lang+"&id="+v_id,
            {
                'type':'load',
                'callback':
                {
                    'function':$p.admin.config.buildNotiticationsForm
                }
           }  
        );
        return false; 
	},
    buildNotiticationsForm:function(response,vars)
    {
        var l_s="";
        var n_id=$p.ajax.getVal(response,"id","int",false,0);
        var n_lang=$p.ajax.getVal(response,"lang","str",false,"--");
        var n_subject=$p.ajax.getVal(response,"subject","str",false,"");
        var n_message=$p.ajax.getVal(response,"message","str",false,"");
        var n_sender=$p.ajax.getVal(response,"sender","str",false,"");
        var n_copy=$p.ajax.getVal(response,"copy","str",false,"");
        var n_type=$p.ajax.getVal(response,"type","str",false,"");
        var n_msgalert = $p.ajax.getVal(response,"msgalert","str",false,"");
        if (n_sender=="") n_sender=__NOTIFICATIONEMAIL;
    	if (lg('lbl'+n_type)!='lbl'+n_type) { l_s+=lg('lbl'+n_type); }
        
        if ( n_msgalert ) {
            l_s += '<p class="warning" style="margin: 1% 20% 0 2%">'+lg(n_msgalert)+'</p>';
        }
        
        l_s+='<FORM name="f">\
            <INPUT type="hidden" name="check" value="1" />\
            <INPUT type="hidden" name="id" value="'+n_id+'" />\
            <INPUT type="hidden" name="nlang" value="'+n_lang+'" />\
            <INPUT type="hidden" name="type" value="'+n_type+'" />\
        	<TABLE border="0" cellpadding="5" cellspacing="5" bgcolor="#EFEFEF">\
        		  <TR>\
        			 <TD><label><u>'+lg('notificationSender')+'</u> : </label></TD>\
        			 <TD><input type="text" name="sender" size="40" value="'+n_sender+'" /></TD>\
        		  </TR>\
        		  <TR>\
        			 <TD><label><u>'+lg("notificationSubject")+'</u> : </label></TD>\
        			 <TD><input type="text" name="subject" size="60" value="'+n_subject+'" /></TD>\
        		  </TR>\
        		  <TR>\
        			 <TD><label><u>'+lg("notificationMessage")+'</u> : </label></TD>\
        			 <TD>\
        				<TEXTAREA name="message" rows="9" COLS="70">'+n_message+'</TEXTAREA>\
        			</TD>\
        		  </TR>\
        			 </TD>\
        		  </TR>\
        		  <TR>\
        			 <TD><label><u>'+lg("notificationCopy")+'</u> : </label></TD>\
        			 <TD><input type="text" name="copy" size="60" value="'+n_copy+'" /></TD>\
        		  </TR>\
        		  <TR>\
        			<TD></TD>\
        			<TD><P class="submit"><input type="button" value='+lg('modify')+' onclick=$p.admin.config.updateNotification();return false; /></P></td>\
        		  </TR>\
        	</TABLE>\
        </FORM>';

        $p.print("notificationBox",l_s);  
    },
    updateNotification:function()
    {
        var err="";
        var paramList="";
        var id=document.forms["f"].elements["id"].value;
        var nlang=document.forms["f"].elements["nlang"].value;
        var type=document.forms["f"].elements["type"].value;
        var sender=document.forms["f"].elements["sender"].value;
        var subject=document.forms["f"].elements["subject"].value;
        var message=document.forms["f"].elements["message"].value;
        var copy=document.forms["f"].elements["copy"].value; 
        paramList="id="+id+"&nlang="+nlang+"&type="+type+"&";
    
    	if (sender=="") {
            if (__NOTIFICATIONEMAIL=="")  {  err+=lg('notificationErrSender')+'<br />';  }
            else {  
                sender=__NOTIFICATIONEMAIL; 
                paramList+="sender="+sender+"&";
            }
        }
        else { paramList+="sender="+sender+"&"; }
    	if (subject=="")    {  err+=lg('notificationErrSubject')+'<br />';  }
        else { paramList+="subject="+subject+"&"; }
    	if (message=="")    {  err+=lg('notificationErrMessage')+'<br />';  }
        else  { 
                message = $p.string.esc(message);       
                paramList+="message="+message+"&";  
        }
        if (copy!="")   {
            var mailTab=copy.split(";");
            for (var i=0;i<mailTab.length;i++) {
                if (!$p.app.tools.checkmail(mailTab[i]))    { err+=mailTab[i]+' '+lg('notificationErrCopy')+'<br />'; }
            }
        }
        if (err!="")  {  $p.app.alert.show(err);  }
        else  {
            paramList+="copy="+copy+"&";
            $p.ajax.call(padmin["scr_config_notification"],
                {
                    'type':'execute',
                    'variables':paramList,
                    'callback':
                    {
                        'function':$p.app.alert.show(lg("notificationUpdated"))
                    }
                }
            );
        }
        return false;
    },   
    /*
                Function: $p.admin.config.notifications
                
                                get the notifications informations
         */ 
    notifications:function(lang)
    {
        if (typeof(lang)=="undefined") var lang=__lang;
        $p.admin.tools.emptyContentLang();
        $p.ajax.call(padmin["xml_config_notifications_titles"]+"?lang="+lang,
            {
                'type':'load',
                'callback':
                {
                    'function':$p.admin.config.buildNotifications
                }
           }  
        );
        return false;         
    },
    buildNotifications:function(response,vars)
    {  
        var l_s="";
        var l_i=0;
		$p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':lg('appConfiguration'),'link':'','fct':'$p.admin.config.buildPage();return false;'},
				{'label':lg('appNotificationConfiguration'),'link':'','fct':''}
			]
		));
        l_s+= '<div class="box">';
        
        while (response.getElementsByTagName("naming")[l_i])
        {
            var l_result=response.getElementsByTagName("naming")[l_i];
            label=$p.ajax.getVal(l_result,"label","str",false,"");
            indice=$p.ajax.getVal(l_result,"indice","int",false,0);
            l_s+='<a id="btn'+indice+'" class="optlist" style="font-size:11pt;font-weight:bold;" href="#" onclick=$p.admin.config.createLangMenu("'+label+'","'+__lang+'");return false;><span id="btn'+indice+'"nb >'+lg(label)+'</span></a> |';
            l_i++;
        }
        
        l_s+='</div>\
        	<div class="box" id="langBox">\
        	</div>\
        	<div id="contentBox"><div class="box" id="notificationBox" width="980" height="150" style="border:1px solid #efefef">\
        	'+lg("lblNotificationSettings")+'\
        	</div></div>';
            
            //launch_hook('admin_config_general');
            $p.plugin.hook.launch('admin_config_general');

        $('content').innerHTML=l_s;
    }
}


//************************************* THEME  FUNCTIONS ***************************************************************************************************************
/*
    Class: $p.admin.config.theme
         theme functions
*/
$p.admin.config.theme={
    selTheme:[],
    theme:[],
    selThemeId:-1,
    selAvailThemeId:-1,
    formulaire:'',
    champ:'',
    regenerateCurrentTheme:false,
     themeObj:function(name){
        this.name=name;
     },
     getThemes:function()
     {
        $('content').innerHTML="";
        $p.admin.config.theme.selTheme=[];
        $p.admin.config.theme.theme=[];
        $p.admin.config.theme.selThemeId=-1;
        $p.admin.config.theme.selAvailThemeId=-1;
	    $p.ajax.call(padmin["xml_config_themes"],
            {
                'type':'load',
                'callback':
                {
                    'function':$p.admin.config.theme.displayPage
                }
           }  
        );
        return false;
    },
    treatThemeResponse:function(response)
    {
        var l_i=0;
        var l_s="";
        while (response.getElementsByTagName("filetheme")[l_i])
        {
            var l_result=response.getElementsByTagName("filetheme")[l_i];
            var filename=$p.ajax.getVal(l_result,"filename","str",false,"");
            $p.admin.config.theme.theme.push(new $p.admin.config.theme.themeObj(filename));            
            l_i++;
        }
        l_i=0;
        while (response.getElementsByTagName("theme")[l_i])
        {
            var l_result=response.getElementsByTagName("theme")[l_i];
            var name=$p.ajax.getVal(l_result,"name","str",false,"");  
            $p.admin.config.theme.selTheme.push(new $p.admin.config.theme.themeObj(name));            
            l_i++;
        }
        l_i=0;
        while (response.getElementsByTagName("templates")[l_i])
        {
            var l_result=response.getElementsByTagName("templates")[l_i];
            var item=$p.ajax.getVal(l_result,"item","str",false,"");        
            var check=$p.ajax.getVal(l_result,"check","int",false,0);   
            var selected=(check==1)?"checked=checked":"";
            l_s+='<input type="radio" name="template" value="'+item+'" '+selected+' /> '+item+'<br />';
            l_i++;
        }
        return l_s;
    },
    buildThemesForm:function()
    {
        var l_s='';
        l_s+='<form name="f1" >\
		<table>\
			<tr>\
			<div id="icons"></div>\
			<td><strong>'+lg("themeSelected")+' :</strong><div id="selthemesdiv" style="width: 200px;height: 100px;overflow: auto;border: 1px solid #c6c3c6;padding: 2px;"></div></td>\
			<td><input type="button" value="<" onclick=$p.admin.config.theme.addSelectedTheme();$p.admin.config.theme.submitForm("f1"); style="font-size: 10pt" /><br />\
			<input type="button" value=">" onclick=$p.admin.config.theme.supSelectedTheme();$p.admin.config.theme.submitForm("f1"); style="font-size: 10pt" /></td>\
			<td><strong>'+lg("themeAvailable")+' :</strong>\
			<div id="allthemesdiv"><div id="exp0"></div></div></td>\
			</tr>\
		</table>\
		<div id="themeinputs"></div>\
		</form>';
        
        return l_s;
    },
    submitForm:function(param)
    {
        var paramList="";
        if (param=='del') {
            if ($p.admin.config.theme.deleteverifname())    {
                paramList+='delete=true&';
                paramList+='todelete='+document.forms['del'].elements['todelete'].value+'&';
                if ($('existingthemes').innerHTML!="") { paramList+='existingthemes='+document.forms["del"].elements["existingthemes"].value; }              
            }
        }
        else if (param=='f') {
            $p.admin.config.theme.regenerateCurrentTheme=true;
            for (var i=0;i<document.forms['f'].elements['template'].length;i++) {
                if (document.forms['f'].elements['template'][i].checked) { 
                    paramList+='template='+document.forms['f'].elements['template'][i].value+'&'; 
                }
            }
            for (var i=0;i<$p.admin.config.theme.selTheme.length;i++)
            {
                paramList+='theme'+i+'='+$p.admin.config.theme.selTheme[i].name+'&';
            }
        }
        else if (param=='f1') {
            for (var i=0;i<$p.admin.config.theme.selTheme.length;i++)
            {
                paramList+='theme'+i+'='+$p.admin.config.theme.selTheme[i].name+'&';
            }
        }
         
        $p.ajax.call(padmin["scr_config_theme"],
            {
                'type':'execute',
                'variables':paramList,
                'callback':
                {
                    'function':($p.admin.config.theme.regenerateCurrentTheme==false)?$p.admin.config.theme.getThemes:$p.admin.config.theme.regenerateTheme
                }
            }
        );
        return false; 
    },
    regenerateTheme:function()
    {
        $p.admin.cacheGenerateAll();
        $p.admin.config.theme.regenerateCurrentTheme=false;
    },
    buildSelectedThemesForm:function()
    {
        var l_s='';
        l_s+='<form name="del">\
			<p class="submit"><input type="button" value='+lg("deleteSelectedTheme")+' onclick=$p.admin.config.theme.submitForm("del");return false; /></p>\
			<div id="todelete"></div>\
			<div id="existingthemes"></div>\
		</form>';
        
        return l_s;
    },
    buildDefaultThemeForm:function(response)
    {
        var l_s='<form name="f">\
        <p><strong>'+lg("templates")+' :</strong></p>\
        <p>'+$p.admin.config.theme.treatThemeResponse(response)+'</p>\
    	<p><input type="button" value="'+lg('save')+'" onclick=\'$p.admin.config.theme.submitForm("f");return false;\' /></p>\
    	</form>';
        
        return l_s;
    },
    displayPage:function(response,vars)
    {
        var l_s="";
        var l_i=0;
        $p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':lg('appConfiguration'),'link':'','fct':'$p.admin.config.buildPage();return false;'},
				{'label':lg('appThemeConfiguration'),'link':'','fct':''}
			]
		));
		
        l_s+= $p.admin.config.theme.buildDefaultThemeForm(response)+'\
			<div class="box">';
            l_s+=$p.admin.config.theme.buildThemesForm();
    		l_s+=$p.admin.config.theme.buildSelectedThemesForm();
    		l_s+='<div id="uploadArea"></div>';
            l_s+=$p.admin.config.theme.buildThemeCreationButton();
        l_s+='</div>';
    
        $('content').innerHTML=l_s;
        $p.admin.config.theme.displayUploadThemeForm();
        $p.admin.config.theme.showAvailThemes();
        $p.admin.config.theme.showSelectedThemes(); 
    },
    buildThemeCreationButton:function()
    {
        return '<br /><div id="themeCreation"><p class="submit"><input type="button" value="'+lg("doMyTheme")+'" onclick=$p.admin.config.theme.displayThemeCreationForm(); /></p></div>';
    },
    displayThemeCreationForm:function()
    {
         $p.print("themeCreation","<iframe id='frm' src='"+padmin["frm_config_theme_create"]+"' width='700' height='370' frameborder='no' marginwidth='0' marginheight='0' style='border:1px solid #efefef'></iframe>");
    },
    displayUploadThemeForm:function()
    {   
        $p.print("uploadArea","<iframe id='frm' src='"+padmin["frm_config_theme_upload"]+"' width='600' height='100' frameborder='no' marginwidth='0' marginheight='0' style='border:1px solid #efefef'></iframe>");
    },
	showAvailThemes:function()
	{
		var l_s="";
		for (var i=0;i<$p.admin.config.theme.theme.length;i++)
		{
			l_s+="<a href='#' onclick='$p.admin.config.theme.selectAvailTheme("+i+")' style='text-decoration:none;"+(i==$p.admin.config.theme.selAvailThemeId?"background-color:#c6c3c6;":"")+"'>"+$p.admin.config.theme.theme[i].name+"</a><br />";
		}
		$p.print("allthemesdiv",l_s);
	},
	callMoveUp:function(i)
	{
		$p.admin.config.theme.moveUp(i);
        if (i==1) { $p.admin.config.theme.regenerateCurrentTheme=true; }
        $p.admin.config.theme.submitForm("f1");
	},
	showSelectedThemes:function()
	{
		var l_s="",l_input="";
		for (var i=0;i<$p.admin.config.theme.selTheme.length;i++)
		{
			l_s+=(i==0?$p.img("s.gif",10,11):"<img src=\"../images/ico_up_arrow2.gif\" onclick=\"$p.admin.config.theme.callMoveUp("+i+");\"/>"+$p.img("s.gif",10,11))+"<a href='#' onclick='$p.admin.config.theme.selectSelectedTheme("+i+")' style='text-decoration:none;"+(i==$p.admin.config.theme.selThemeId?"background-color:#c6c3c6;":"")+"'>"+$p.admin.config.theme.selTheme[i].name+"</a><br />";
			l_input+="<input type='hidden' name='theme"+i+"' value='"+$p.admin.config.theme.selTheme[i].name+"' />";
		}
		$p.print("selthemesdiv",l_s);
		$p.print("themeinputs",l_input);
	},
	selectSelectedTheme:function(v_id)
	{
		$p.admin.config.theme.selThemeId=v_id;
		$p.admin.config.theme.showSelectedThemes();
		return false;
	},
	selectAvailTheme:function(v_id)
	{
		$p.admin.config.theme.selAvailThemeId=v_id;
		$p.admin.config.theme.showAvailThemes();
		return false;
	},
	supSelectedTheme:function()
	{
        if ($p.admin.config.theme.selThemeId==0) {
            $p.admin.config.theme.regenerateCurrentTheme=true;
        }

		//suppress a group from user list
		if ($p.admin.config.theme.selThemeId==-1){
			alert(lg("noThemeSelected"));
			return false;
		}
		if($p.admin.config.theme.selTheme.length==1){
			alert(lg("isYourLastActualTheme"));
			return false;
		}
		$p.admin.config.theme.selTheme.splice($p.admin.config.theme.selThemeId,1);
		$p.admin.config.theme.selThemeId=-1;
		$p.admin.config.theme.showSelectedThemes();

	},
	addSelectedTheme:function()
	{
		//add a Theme in the selected theme list
		if ($p.admin.config.theme.selAvailThemeId==-1){
			alert(lg("noThemeSelected"));
			return false;
		}
		//check if group is already added for this user
		for (var i=0;i<$p.admin.config.theme.selTheme.length;i++)
		{
			if ($p.admin.config.theme.selTheme[i].name==$p.admin.config.theme.theme[$p.admin.config.theme.selAvailThemeId].name) return false;
		}
		$p.admin.config.theme.selTheme.push(new $p.admin.config.theme.themeObj($p.admin.config.theme.theme[$p.admin.config.theme.selAvailThemeId].name));
		$p.admin.config.theme.selAvailThemeId=-1;
		$p.admin.config.theme.selThemeId=-1;
		$p.admin.config.theme.showSelectedThemes();
		//clean up all groups list
		$p.admin.config.theme.selectAvailTheme(-1);	
	},
	moveUp:function(v_id)
	{
		var temp=$p.admin.config.theme.selTheme[v_id];
		var save1=$p.admin.config.theme.selTheme[v_id-1];
		$p.admin.config.theme.selTheme.splice(v_id-1,1,temp);
		$p.admin.config.theme.selTheme.splice(v_id,1,save1);
		
		$p.admin.config.theme.showSelectedThemes();
		return true;
	},
	check:function()
	{
		if ($p.admin.config.theme.selTheme.length==0)
		{
			alert(lg("noThemeSelected"));
			return false;
		}
		return true;
	},
	themeverifname:function()
	{
		var icon = document.tr.elements['logopath'].value;
		var extension = "";
		var indicSlash = icon.lastIndexOf("\\");
		var tabDecomp = icon.substring(indicSlash+1).split(".");
		extension = tabDecomp[tabDecomp.length-1]; 
		
		var valeur = document.tr.elements['themename'].value;
		if (theme.some(function(item,index){return item.name==valeur}))
		{
			alert(lg("themeAlreadyExisting"));
			return false;
		}
		if(valeur==""){
			alert(lg("enterAThemeName"));
			return false;
		}
		if (document.tr.elements['nologo'].checked==false
            &&extension!="gif"
            &&extension!="jpg"
            &&!extension!="jpeg"
            &&extension!="png"
            &&extension!="")
		{
			alert(lg("notAnIcon"));
			return false;
		}
		return true;	
	},
	deleteverifname:function()
	{
		for (var i=0;i<$p.admin.config.theme.theme.length;i++)
		{
			var l_s="<a href='#' onclick='$p.admin.config.theme.selectAvailTheme("+i+")' style='text-decoration:none;"+(i==$p.admin.config.theme.selAvailThemeId?"background-color:#c6c3c6;":"")+"'>"+$p.admin.config.theme.theme[i].name+"</a><br />";
		}
		var v_id=$p.admin.config.theme.selAvailThemeId;
		if(v_id==-1){
			alert(lg("noThemeSelectedOnAvailThemes"));
			return false;
		}
		var l_input ="<input type='hidden' name='todelete' value='"+$p.admin.config.theme.theme[v_id].name+"' />";
		l_input2="";
		for (var i=0;i<$p.admin.config.theme.selTheme.length;i++)
		{
			if($p.admin.config.theme.theme[v_id].name==$p.admin.config.theme.selTheme[i].name){	
				l_input2 ="<input type='hidden' name='todelete' value='1' />";
				alert(lg("isYourActualTheme"));
				return false;
			}
		}
		$p.print("todelete",l_input);
		$p.print("existingthemes",l_input2);
		return true;
	}
}

//************************************* STATS  FUNCTIONS ***************************************************************************************************************
/*
    Class: $p.admin.stats
         stats functions
*/
$p.admin.stats={
    tabname:'',
    statsSubTabs:[],
    col:[],
	container:"",
    ref:'',
    month:'',
    year:'',
    monthList:{},
	init:function(v_div)
	{
		$p.admin.stats.col=[];
		$p.admin.stats.container=v_div;
	},
	createCol:function(v_id,v_name)
	{
		$p.admin.stats.col[v_id]=[];
		$p.admin.stats.col[v_id][0]=v_name;
	},
	addInCol:function(v_col,v_value)
	{
		$p.admin.stats.col[v_col].push(v_value);
	},
    /*
                Function: $p.admin.stats.create
                
                                Display the stats schema
           */
	create:function()
	{
		var l_s="<table cellpadding='2' cellspacing='0' width='500' style='border:1px solid #000000'>";
		var l_style="color:#ffffff;background-color:#000000;";
		for (var i=0;i<$p.admin.stats.col[0].length;i++)
		{
			l_s+="<tr>";
			for (var j=0;j<$p.admin.stats.col.length;j++)
			{
				l_s+="<td style='"+l_style+"'>"+$p.string.unesc($p.admin.stats.col[j][i])+"</td>";
			}
			l_s+="</tr>";
			l_style=(i%2==1)?"color:#000000;background-color:#ffffff;":"color:#000000;background-color:#efefef;";
		}
		$p.print($p.admin.stats.container,l_s);
	},
    /*
                Function: $p.admin.stats.defineMonthList
                
                                Define the month list hash
           */
    defineMonthList:function()
    {
        $p.admin.stats.monthList['01']=lg('Jan');
        $p.admin.stats.monthList['02']=lg('Feb');
        $p.admin.stats.monthList['03']=lg('Mar');
        $p.admin.stats.monthList['04']=lg('Apr');
        $p.admin.stats.monthList['05']=lg('May');
        $p.admin.stats.monthList['06']=lg('Jun');
        $p.admin.stats.monthList['07']=lg('Jul');
        $p.admin.stats.monthList['08']=lg('Aug');
        $p.admin.stats.monthList['09']=lg('Sep');
        $p.admin.stats.monthList['10']=lg('Oct');
        $p.admin.stats.monthList['11']=lg('Nov');
        $p.admin.stats.monthList['12']=lg('Dec');
    },
    /*
                Function: $p.admin.stats.buildPage
                
                                compute stats informations before displaying
                                
                 Returns:
                            
                                false
           */
    buildPage:function()
    {
        $p.app.tabs.sel=6;
        $p.app.tabs.select(6);
        if ($p.app.tabs.sel && $p.app.tabs.sel > 0) {$p.admin.setState("$p.app.tabs.open("+$p.app.tabs.sel+")");}
        $p.admin.tools.emptyContent();  
        $p.admin.stats.tabname='statstab';
        $p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':lg('statsMgmt'),'link':'','fct':''}
			]
        ));
        $p.admin.stats.defineSubTabs(); 
        $p.admin.buildSubTabs($p.admin.stats.statsSubTabs,$p.admin.stats.tabname);
        return false;
    },
    statistics:function()
    {
       allowSave = true;
       $p.admin.tools.emptyContent(); 
       $p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':lg('statsMgmt'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(6);return false;'},
				{'label':lg('appStats'),'link':'','fct':''}
			]
       ));
       $p.ajax.call(padmin["scr_stats_compute"],
            {
                'type':'execute',
                'callback':
                {
                    'function':$p.admin.stats.loadAppStats
                }
            }
        );
        return false;
    },
    /*
                Function: $p.admin.stats.modulestats
                
                                Load the modules stats
           */
    modulestats:function()
    {       
       $p.admin.tools.emptyContent(); 
       $p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':lg('statsMgmt'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(6);return false;'},
				{'label':lg('moduleStats'),'link':'','fct':''}
			]
       ));
       $p.ajax.call(padmin["scr_stats_modules_compute"],
            {
                'type':'execute',
                'callback':
                {
                    'function':$p.admin.stats.loadModulesStats
                }
            }
        );
        return false;
    },
    /*
                Function: $p.admin.stats.loadAppStats
                
                                Load the stats informations
                                
                 Returns:
                            
                                false
           */
    loadAppStats:function()
    {
        $p.admin.stats.defineMonthList();
        var myDate=new Date();
        if ($p.admin.stats.ref=="") {
            $p.admin.stats.ref="month";
            $p.admin.stats.month=myDate.getMonth()+1;
            $p.admin.stats.month=$p.admin.stats.month.toString();
            if ($p.admin.stats.month.length<2) { 
                $p.admin.stats.month='0'+$p.admin.stats.month; 
            }     
            $p.admin.stats.year=myDate.getFullYear().toString();
        }
        var l_s='';        
        $p.ajax.call(padmin["xml_stats_load"]+'?month='+$p.admin.stats.month,
            {
                'type':'load',
                'callback':
                {
                    'function':$p.admin.stats.displayAppStats
                }
           }  
        );
        return false; 
    },
    loadModulesStats:function()
    {
        $p.admin.stats.defineMonthList();
        var myDate=new Date();
        if ($p.admin.stats.ref=="") {
            $p.admin.stats.month=myDate.getMonth()+1;
            $p.admin.stats.month=$p.admin.stats.month.toString();
            if ($p.admin.stats.month.length<2) { 
                $p.admin.stats.month='0'+$p.admin.stats.month; 
            }    
            $p.admin.stats.year=myDate.getFullYear().toString();
        }
        $p.ajax.call(padmin["xml_stats_modules_load"]+'?'+$p.admin.stats.ref+'='+$p.admin.stats.month,
            {
                'type':'load',
                'callback':
                {
                    'function':$p.admin.stats.displayModulesStats
                }
           }  
        );
        return false;
    },
    /*
                Function: $p.admin.stats.createStat1
                
                                get the uniq visitors stats
                                
                 Parameters:
                            
                                response: XML Object
           */
    createStat1:function(response)
    {
        var period='';
        var l_i=0;
        ($p.admin.stats.ref=="month")?period=lg('days'):period=lg('months');
        $p.admin.stats.chart.init("stat1",lg("uniqVisitors"),period);
        while (response.getElementsByTagName("stat1")[l_i])
        {
            var l_result=response.getElementsByTagName("stat1")[l_i];
            var isMonth=$p.ajax.getVal(l_result,"month","int",false,1);
            var ord=$p.ajax.getVal(l_result,"ord","int",false,0);
            var absc=$p.ajax.getVal(l_result,"absc","str",false,"");
            if (isMonth==0) { absc=lg(absc); }
            //greatest ord (results)
            $p.admin.stats.chart.addCoord(ord,absc);
            l_i++;
        }      
        $p.admin.stats.chart.create();
    },
    /*
                Function: $p.admin.stats.createStat2
                
                                get the number of new users stats
                                
                 Parameters:
                            
                                response: XML Object
           */
    createStat2:function(response)
    {
        var period='';
        var l_i=0;
        ($p.admin.stats.ref=="month")?period=lg('days'):period=lg('months');
        $p.admin.stats.chart.init("stat2",lg("stat1"),period);
        while (response.getElementsByTagName("stat2")[l_i])
        {
            var l_result=response.getElementsByTagName("stat2")[l_i];
            var isMonth=$p.ajax.getVal(l_result,"month","int",false,1);
            var ord=$p.ajax.getVal(l_result,"ord","int",false,0);
            var absc=$p.ajax.getVal(l_result,"absc","str",false,"");
            if (isMonth==0) { absc=lg(absc); }
            $p.admin.stats.chart.addCoord(ord,absc);
            l_i++;
        }      
        $p.admin.stats.chart.create();
    }, 
    /*
                Function: $p.admin.stats.createStat3
                
                                get the number of new portals stats
                                
                 Parameters:
                            
                                response: XML Object
           */    
    createStat3:function(response)
    {
        var period='';
        var l_i=0;
        ($p.admin.stats.ref=="month")?period=lg('days'):period=lg('months');
        $p.admin.stats.chart.init("stat3",lg("stat2"),period);
        while (response.getElementsByTagName("stat3")[l_i])
        {
            var l_result=response.getElementsByTagName("stat3")[l_i];
            var isMonth=$p.ajax.getVal(l_result,"month","int",false,1);
            var ord=$p.ajax.getVal(l_result,"ord","int",false,0);
            var absc=$p.ajax.getVal(l_result,"absc","str",false,"");
            if (isMonth==0) { absc=lg(absc); }
            $p.admin.stats.chart.addCoord(ord,absc);
            l_i++;
        }      
        $p.admin.stats.chart.create();
    },
    changePeriod:function(unit)
    {
        var paramList='';
        if (unit.length==2) {
            $p.admin.stats.ref='month';
            $p.admin.stats.month=unit;
            paramList+='month='+$p.admin.stats.month;
        }  
        else {
            $p.admin.stats.ref='year';
            $p.admin.stats.year=unit; 
            paramList+='year='+$p.admin.stats.year;            
        }
        $p.ajax.call(padmin["xml_stats_load"]+'?'+paramList,
            {
                'type':'load',
                'callback':
                {
                    'function':$p.admin.stats.displayAppStats
                }
           }  
        );
        return false;        
    },
    /*
                Function: $p.admin.stats.displayAppStats
                
                                display the stats page
                                
                 Parameters:
                            
                                response: XML Object
           */ 
    displayAppStats:function(response,vars)
    {
        var myDate=new Date();
        var currentMonth=myDate.getMonth()+1;
        currentMonth=currentMonth.toString();
        if (currentMonth.length<2)  { currentMonth='0'+currentMonth; }
        var l_s='';
        if ($p.admin.stats.ref=="month")  {
    		l_s+='<a class="sellist" style="font-size:11pt;font-weight:bold;">'+lg("month")+' ';
            l_s+="<select name='month' onchange=$p.admin.stats.changePeriod(this.value);return false; >";
            for (var w in $p.admin.stats.monthList)
            {
                var selected='';
                if ($p.admin.stats.month!='' && $p.admin.stats.month==w) {  selected="selected=selected"; }
                l_s+='<option value="'+w+'" '+selected+' >'+$p.admin.stats.monthList[w]+'</option>';
            }        
            l_s+="</select>";
            l_s+="</a> | <a href='#' onclick='$p.admin.stats.changePeriod($p.admin.stats.year);return false;' class='optlist' style='font-size:11pt;font-weight:bold;'>"+lg("wholeYear")+"</a><br /><br />";
    	}
        else    {
            l_s+='<a href="#" onclick=\'$p.admin.stats.changePeriod("'+currentMonth+'");return false;\' class="optlist" style="font-size:11pt;font-weight:bold;">'+lg("currentMonth")+'</a> |\
                  <a href="#" onclick=\'$p.admin.stats.changePeriod($p.admin.stats.year);return false;\' class="sellist" style="font-size:11pt;font-weight:bold;">'+lg("wholeYear")+'</a><br /><br />';
        }

        l_s+='<div class="subtitle">'+lg("uniqVisitors")+'</div>\
        <div id="stat1"></div>\
        <div class="subtitle">'+lg("stat1")+'</div>\
        <div id="stat2"></div>\
        <div class="subtitle">'+lg("stat2")+'</div>\
        <div id="stat3"></div>';
        
        $('content').innerHTML=l_s;
        
        $p.admin.stats.createStat1(response);
        $p.admin.stats.createStat2(response);
        $p.admin.stats.createStat3(response);
 
        $p.plugin.hook.launch('admin_stats_showed');    
    },
    changeModulesStatsPeriod:function(unit)
    {
        var paramList='';
        if (unit.length==2) {
            $p.admin.stats.ref='month';
            $p.admin.stats.month=unit;
            paramList+='month='+$p.admin.stats.month;
        }  
        else {
            $p.admin.stats.ref='year';
            $p.admin.stats.year=unit; 
            paramList+='year='+$p.admin.stats.year;            
        }

        $p.ajax.call(padmin["xml_stats_modules_load"]+'?'+paramList,
            {
                'type':'load',
                'callback':
                {
                    'function':$p.admin.stats.displayModulesStats
                }
           }  
        );
        return false;        
    },
    displayModulesStats:function(response,vars)
    {
        var l_s="";
        var ret=$p.ajax.getVal(response,"ref","str",false,"");
        var myDate=new Date();
        var currentMonth=myDate.getMonth()+1;
        currentMonth=currentMonth.toString();
        if (currentMonth.length<2)  { currentMonth='0'+currentMonth; }
        if (ret=="month") {
            var month=$p.ajax.getVal(response,"retval","str",false,"01");
            var year=myDate.getFullYear();
            l_s+='<a class="sellist" style="font-size:11pt;font-weight:bold;">'+lg("month")+'\
                  <select name="month" onchange=$p.admin.stats.changeModulesStatsPeriod(this.value);return false; >';
                  for (var w in $p.admin.stats.monthList)
                  {
                        var selected='';
                        if ($p.admin.stats.month!='' && $p.admin.stats.month==w) {  selected="selected=selected"; }
                        l_s+='<option value="'+w+'" '+selected+' >'+$p.admin.stats.monthList[w]+'</option>';
                  }        
                  l_s+='</select>\
                  </a> | <a href="#" onclick=$p.admin.stats.changeModulesStatsPeriod('+year+');return false; class="optlist" style="font-size:11pt;font-weight:bold;">'+lg("wholeYear")+'</a><br /><br />';
        }
        else {
            l_s+='<a href="#" class="optlist" style="font-size:11pt;font-weight:bold;" onclick=$p.admin.stats.displayModulesCurrentMonth();return false;>'+lg("currentMonth")+'</a> |\
                  <a href="#" class="sellist" style="font-size:11pt;font-weight:bold;">'+lg("wholeYear")+'</a><br /><br />';
        }
        l_s+='<div class="subtitle">'+lg("statsModules")+'</div><br />\
            <div id="stat1"></div><br />\
            <div class="subtitle">'+lg("statsRssDirectory")+'</div><br />\
            <div id="stat2"></div><br />\
            <div class="subtitle">'+lg("statsRssUsers")+'</div><br />\
            <div id="stat3"></div><br />\
            </table>';
               
        $p.print('content',l_s);
        $p.admin.stats.createModulesStats1(response,vars);
        $p.admin.stats.createModulesStats2(response,vars);
        $p.admin.stats.createModulesStats3(response,vars);      
    },
    displayModulesCurrentMonth:function()
    {
        $p.admin.stats.ref="month";
        var myDate=new Date();
        var currentMonth=myDate.getMonth()+1;
        currentMonth=currentMonth.toString();
        if (currentMonth.length<2)  { 
            currentMonth='0'+currentMonth; 
        }
        $p.admin.stats.changeModulesStatsPeriod(currentMonth);
        return false;
    },  
    /*
                    Function: $p.admin.stats.createModulesStats2
                
                                Display the modules best ranking stats
         */  
    createModulesStats1:function(response,vars)
    {
        var l_i=0;
        $p.admin.stats.table.init('stat1');
        $p.admin.stats.table.createCol(0,lg("rank"));
        $p.admin.stats.table.createCol(1,lg("modules")+' ('+lg("displays")+')');
        while (response.getElementsByTagName("topmodules")[l_i])
        {
            var l_result=response.getElementsByTagName("topmodules")[l_i];
            var inc=$p.ajax.getVal(l_result,"inc","int",false,0);
            var name=$p.ajax.getVal(l_result,"name","str",false,"...");
            var tot=$p.ajax.getVal(l_result,"tot","int",false,0);
            $p.admin.stats.table.addInCol(0,inc);
            $p.admin.stats.table.addInCol(1,name+" ("+tot+")");
            l_i++;
        }
        $p.admin.stats.table.create();   
    },
    /*
                    Function: $p.admin.stats.createModulesStats2
                
                                Display the rss best ranking stats (from widget library)
         */  
    createModulesStats2:function(response,vars)
    {
        var l_i=0;
        $p.admin.stats.table.init('stat2');
        $p.admin.stats.table.createCol(0,lg("rank"));
        $p.admin.stats.table.createCol(1,lg("modules")+' ('+lg("displays")+')');
        while (response.getElementsByTagName("toprss")[l_i])
        {
            var l_result=response.getElementsByTagName("toprss")[l_i];
            var inc=$p.ajax.getVal(l_result,"inc","int",false,0);
            var name=$p.ajax.getVal(l_result,"name","str",false,"...");
            var tot=$p.ajax.getVal(l_result,"tot","int",false,0);
            $p.admin.stats.table.addInCol(0,inc);
            $p.admin.stats.table.addInCol(1,name+" ("+tot+")");
            l_i++;
        }
        $p.admin.stats.table.create();   
    },
    /*
                    Function: $p.admin.stats.createModulesStats3
                
                                Display the rss best ranking stats
         */   
    createModulesStats3:function(response,vars)
    {
        var l_i=0;
        $p.admin.stats.table.init('stat3');
        $p.admin.stats.table.createCol(0,lg("rank"));
        $p.admin.stats.table.createCol(1,lg("modules")+' ('+lg("displays")+')');
        while (response.getElementsByTagName("topusersrss")[l_i])
        {
            var l_result=response.getElementsByTagName("topusersrss")[l_i];
            var inc=$p.ajax.getVal(l_result,"inc","int",false,0);
            var result1=$p.ajax.getVal(l_result,"result1","str",false,"...");
            var tot=$p.ajax.getVal(l_result,"tot","int",false,0);
            $p.admin.stats.table.addInCol(0,inc);
            $p.admin.stats.table.addInCol(1,result1+" ("+tot+")");
            l_i++;
        }
        $p.admin.stats.table.create();   
    },       
    /*
                Function: $p.admin.stats.defineSubTabs
                
                                define the stats sub tabs
         */   
    defineSubTabs:function()
    {
        var tabTmp=new Array;
        for (var i=0;i<$p.admin.subTabs[$p.admin.stats.tabname].length;i++)
        {
            tabTmp.push({'label':$p.admin.subTabs[$p.admin.stats.tabname][i]['label'],
                         'fct':$p.admin.subTabs[$p.admin.stats.tabname][i]['fct'],
                         'description':$p.admin.subTabs[$p.admin.stats.tabname][i]['description']});
        } 
        $p.admin.stats.statsSubTabs[$p.admin.stats.tabname]=tabTmp;         
    }
}


//************************************* STATS  TABLE FUNCTIONS ***************************************************************************************************************
/*
    Class: $p.admin.stats.table
         stats chart functions
*/
$p.admin.stats.table={
	col:[],
	container:"",
	init:function(v_div)
	{
		$p.admin.stats.table.col=[];
		$p.admin.stats.table.container=v_div;
	},
	createCol:function(v_id,v_name)
	{
		$p.admin.stats.table.col[v_id]=[];
		$p.admin.stats.table.col[v_id][0]=v_name;
	},
	addInCol:function(v_col,v_value)
	{
		$p.admin.stats.table.col[v_col].push(v_value);
	},
	create:function()
	{
		var l_s="<table cellpadding='2' cellspacing='0' width='500' style='border:1px solid #000000'>";
		var l_style="color:#ffffff;background-color:#000000;";
		for (var i=0;i<$p.admin.stats.table.col[0].length;i++)
		{
			l_s+="<tr>";
			for (var j=0;j<$p.admin.stats.table.col.length;j++)
			{
				l_s+="<td style='"+l_style+"'>"+$p.string.unesc($p.admin.stats.table.col[j][i])+"</td>";
			}
			l_s+="</tr>";
			l_style=(i%2==1)?"color:#000000;background-color:#ffffff;":"color:#000000;background-color:#efefef;";
		}
		$p.print($p.admin.stats.table.container,l_s);
	}
}


//************************************* STATS  CHART FUNCTIONS ***************************************************************************************************************
/*
    Class: $p.admin.stats.chart
         stats chart functions
*/
$p.admin.stats.chart={
    ord:[],
	maxOrd:0,
	absc:[],
	container:"",
	init:function(v_div,v_ordName,v_abscName)
	{
		$p.admin.stats.chart.ord=[];
		$p.admin.stats.chart.ord[0]=v_ordName;
		$p.admin.stats.chart.absc=[];
		$p.admin.stats.chart.absc[0]=v_abscName;
		$p.admin.stats.chart.container=v_div;
	},
    /*
                Function: addCoord
               
                            $p.admin.stats.chart.addCoord
                
                   Parameters:

                                    v_ord - results for a day
                                    
                                    v_absc  - day 
        */
	addCoord:function(v_ord,v_absc)
	{
		$p.admin.stats.chart.ord.push(v_ord);
		$p.admin.stats.chart.maxOrd=Math.max($p.admin.stats.chart.maxOrd,v_ord);
		$p.admin.stats.chart.absc.push(v_absc);
	},
	create:function()
	{
		var l_measuresNb=$p.admin.stats.chart.ord.length;
		var l_s=$p.admin.stats.chart.ord[0]+"<br /><table><tr><td><table cellpadding='1' cellspacing='0' style='border-left:1px solid #000000;'>";
		l_s+="<tr><td colspan='"+l_measuresNb+"'>"+$p.admin.stats.chart.maxOrd+"</td></tr>";
		l_s+="<tr>";
		for (var i=1;i<l_measuresNb;i++)
		{
			l_s+="<td style='text-align: center;width: 30px;height: 100px;border-bottom: 1px solid #000000;vertical-align: bottom;' onmouseover=\"mouseBox('"+$p.admin.stats.chart.ord[i]+"',event)\" onmouseout=\"mouseBox('')\">";
			l_s+=$p.admin.stats.chart.ord[i]==0?"&nbsp;":$p.img("bgstat.gif",17,(100*($p.admin.stats.chart.ord[i]/$p.admin.stats.chart.maxOrd)),""+$p.admin.stats.chart.ord[i])+"<br />";
			l_s+="</td>";
		}
		l_s+="</tr>";
		l_s+="<tr>";
		for (var i=1;i<l_measuresNb;i++)
		{
			l_s+="<td style='text-align:center;padding-left:4px;padding-right:4px;'>"+$p.admin.stats.chart.absc[i]+"</td>";
		}
		l_s+="</tr>";
		l_s+="</table></td><td style='vertical-align:bottom'>"+$p.admin.stats.chart.absc[0]+"</td></tr></table><br />";
		$p.print($p.admin.stats.chart.container,l_s);
        $p.admin.stats.chart.maxOrd=0;
	}
}


//************************************* COMMUNICATION  FUNCTIONS ***************************************************************************************************************
/*
    Class: $p.admin.communication
         communication functions
*/
$p.admin.communication={
    tabname:'',
    commSubTabs:[],
    buildPage:function()
    {
        var l_s='';
        $p.app.tabs.sel=7;
        $p.app.tabs.select(7);
        $p.admin.tools.emptyContent();  
        $p.admin.communication.tabname='comtab';
        $p.admin.communication.defineSubTabs(); 
		$p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':lg('communication'),'link':'','fct':''}
			]
		));
        $p.admin.buildSubTabs($p.admin.communication.commSubTabs,$p.admin.communication.tabname);
        if ($p.app.tabs.sel && $p.app.tabs.sel > 0) {$p.admin.setState("$p.app.tabs.open("+$p.app.tabs.sel+")");}
    },
    /*
                Function: $p.admin.communication.defineSubTabs
                
                                define the communication sub tabs
         */   
    defineSubTabs:function()
    {
        var tabTmp=new Array;
        for (var i=0;i<$p.admin.subTabs[$p.admin.communication.tabname].length;i++)
        {
            tabTmp.push({'label':$p.admin.subTabs[$p.admin.communication.tabname][i]['label'],
                         'fct':$p.admin.subTabs[$p.admin.communication.tabname][i]['fct'],
                         'description':$p.admin.subTabs[$p.admin.communication.tabname][i]['description']});
        } 
        $p.admin.communication.commSubTabs[$p.admin.communication.tabname]=tabTmp;         
    },
    /*
                Function: $p.admin.communication.infobar
                
                                load the information bar datas
                                
                Returns: 
                 
                                false
         */   
    infobar:function()
    {
        var l_s='';
        $p.admin.tools.emptyContent(); 
        $p.ajax.call(padmin["xml_communication_load_infobar"],
            {
                'type':'load',
                'callback':
                {
                    'function':$p.admin.communication.displayInfobar
                }
           }  
        );
        return false;
    },
    /*
                Function: $p.admin.communication.displayInfobar
                
                                display the information bar form page
                                
                Parameters: 
                 
                                response - XML Object
                                vars - 
         */  
    displayInfobar:function(response,vars)
    {
        var l_s='';
        var bartype=$p.ajax.getVal(response,"bartype","int",false,0);
        var rssinfo=$p.ajax.getVal(response,"rssinfo","str",false,"");
        var texthtml=$p.ajax.getVal(response,"texthtml","str",false,"");
        var barclosing=$p.ajax.getVal(response,"barclosing","str",false,"false");
        var bartypeSel=(bartype==0)?"checked=checked":"";
        var bartypeSel1=(bartype==1)?"checked=checked":"";
        var bartypeSel2=(bartype==2)?"checked=checked":"";
        /*
                    //after bartexthtml
                    <?php $barclosing=false;?>
                    <p><label><input type="checkbox" name="barclosing"<?php if ($barclosing=="true") echo " checked='checked'";?> /> <?php echo lg("allowClosingBar");?></label></p>
                    */
		$p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':lg('communication'),'link':'','fct':'$p.admin.communication.buildPage();return false;'},
				{'label':lg('informationBar'),'link':'','fct':''}
			]
		));
        l_s+='<p><strong>'+lg("chooseInfoBarType")+' :</strong></p>\
        <form name="f">\
        <div class="box">\
        <p><label><input type="radio" name="bartype" value="0" '+bartypeSel+' /> <strong>'+lg("nothing")+'</strong></label></p>\
        </div>\
        <div class="box">\
        <p><label><input type="radio" name="bartype" value="1" '+bartypeSel1+' /> <strong>'+lg("textHtml")+'</strong></label></p>\
        <p><input type="text" name="bartexthtml" size="80" value="'+texthtml+'" ></p>\
        <p style="display: none;"><label><input type="checkbox" name="baralert" /> '+lg("InfobarMsgAlert")+'</label></p>\
        </div>\
        <div class="box">\
        <p><label><input type="radio" name="bartype" value="2" '+bartypeSel2+' /> <strong>'+lg("infoFeedUrl")+'</strong></label></p>\
        <p><input type="text" name="rssinfo" size="120" maxlength="150" value="'+rssinfo+'" /> </p>\
        </div>\
        <p class="submit"><input type="button" value="'+lg("saveModifications")+'" onclick="$p.admin.communication.setinfobar();return false;" /></p>\
        </form>';
        //if (is_file("communication_vote.php")) require("communication_vote.php");
        
        $('content').innerHTML=l_s;
    },
    /*
                Function: $p.admin.communication.setinfobar
                
                                update the infobar informations
                                
                Returns: 
                 
                                false
         */ 
    setinfobar:function()
    {
        var paramList='';
        for (var i=0;i<document.forms['f'].elements['bartype'].length;i++)
        {
            if (document.forms['f'].elements['bartype'][i].checked) {
                var bartype=document.forms['f'].elements['bartype'][i].value;
                paramList+='bartype='+bartype+'&';
            }
        }
        var rssinfo=document.forms['f'].elements['rssinfo'].value;
        paramList+='rssinfo='+rssinfo+'&';
        var bartexthtml=document.forms['f'].elements['bartexthtml'].value;
        paramList+='bartexthtml='+bartexthtml+'&';
        if (document.forms['f'].elements['baralert'].checked) {
            paramList+='baralert='+document.forms['f'].elements['baralert'].value;
        }

        $p.ajax.call(padmin["scr_communication_infobar"],
            {
                'type':'execute',
                'variables':paramList,
                'callback':
                {
                    'function':$p.admin.generateConfigFiles
                }
            }
        );
        return false;
    },
    /*
                Function: $p.admin.communication.emailing
                
                                display the emailing form
         */ 
    emailing:function()
    {
        var l_s='';
        $p.admin.tools.emptyContent();
		$p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':lg('communication'),'link':'','fct':'$p.admin.communication.buildPage();return false;'},
				{'label':lg('emailing'),'link':'','fct':''}
			]
		));
        l_s+='<div id="report"></div>\
        <a class="sellist" style="font-size:11pt;font-weight:bold;">'+lg("sendAnEmail")+'</a> |\
        <a href="#" onclick="$p.admin.communication.emailing_history();return false;" class="optlist" style="font-size:11pt;font-weight:bold;">'+lg("sentMessages")+'</a><br /><br />\
        <form name="f">\
        '+lg("contact")+' :<br /><br />\
        <input type="radio" name="emailtype" value="1" /> '+lg("usersListComaSeparated")+' : <input type="text" name="emaillist" size="50" /><br />\
        <input type="radio" name="emailtype" value="2" checked="checked" /> '+lg("allUsersWithEmail")+'<br /><br /><br />\
        <input type="checkbox" name="emailcopy" /> '+lg("keepCopy")+'<br /><br /><br />\
        <table cellpadding="0" cellspacing="10">\
        	<tr><td>'+lg("sender")+'</td><td><input type="text" name="sender" size="50" maxlength="60" /></td></tr>\
        	<tr><td>'+lg("subject")+'</td><td><input type="text" name="subject" size="50" maxlength="60" /></td></tr>\
        	<tr><td valign="top">'+lg("message")+'</td><td><textarea name="message" cols="50" rows="10" ></textarea></td></tr>\
        </table>\
        <br />\
        <input type="button" value="'+lg("send")+'" onclick="$p.admin.communication.setemailing();return false; " />\
        </form>';

        $('content').innerHTML=l_s;
    },
    /*
                Function: $p.admin.communication.setemailing
                
                                set the emailing informations
                                
                Returns:
                
                                false
         */ 
    setemailing:function()
    {
        var paramList='';
        var emailtype='';
        var emaillist='';
        var subject='';
        var message='';
        var sender='';
        
        for (var i=0;i<document.forms['f'].elements['emailtype'].length;i++)
        {
            if (document.forms['f'].elements['emailtype'][i].checked)   {
                emailtype=document.forms['f'].elements['emailtype'][i].value;
                paramList+='emailtype='+emailtype+'&';
                if (emailtype==1)   {
                    emaillist=document.forms['f'].elements['emaillist'].value;
                    paramList+='emaillist='+emaillist+'&';
                }
            }
        }
        
        if (document.forms['f'].elements['emailcopy'].checked)  {
            paramList+='emailcopy=yes&';
        }
        
        subject=document.forms['f'].elements['subject'].value;
        subject=subject.replace(/\&/gi,"%26"); 
        subject=subject.replace(/\+/gi,"%2b"); 
        message=document.forms['f'].elements['message'].value;
        message=message.replace(/\&/gi,"%26"); 
        message=message.replace(/\+/gi,"%2b"); 
        sender=document.forms['f'].elements['sender'].value;
        paramList+='subject='+subject+'&message='+message+'&sender='+sender;
        $('report').innerHTML='';
        $p.ajax.call(padmin["scr_communication_emailing"]+"?"+paramList,
            {
                'type':'load',
                'callback':
                {
                    'function':$p.admin.communication.display_email_sent
                }
            }
        ); 
        return false;
    },
    /*
                Function: $p.admin.communication.display_email_sent
                
                                Display a message to warn the administrator that the emails were sent
         */ 
    display_email_sent:function(response,vars)
    {
        var label = response.getElementsByTagName("label")[0].firstChild.nodeValue;
        if (response.getElementsByTagName("state")[0] == 2) { 
            $('report').innerHTML='<p style="padding:1% 1% 1% 1%;margin:1% 30% 1% 10%" class="errordiv">'+lg(label)+"</p>";
        } else {
            $('report').innerHTML='<p style="padding:1% 1% 1% 1%;margin:1% 30% 1% 10%" class="warningok">'+lg(label)+"</p>";
        }
    },
    /*
                Function: $p.admin.communication.emailing_history
                
                                Load mailing history informations
                                
                 Returns:
                 
                                false
         */ 
    emailing_history:function()
    {   
        $p.admin.tools.emptyContent();
        $p.ajax.call(padmin["xml_communication_emailing_history"],
            {
                'type':'load',
                'callback':
                {
                    'function':$p.admin.communication.display_emailing_history
                }
           }  
        );
        return false;
    },
    /*
                Function: $p.admin.communication.display_emailing_history
                
                                Display the emailing history informations
         */
    display_emailing_history:function(response,vars)
    {
		$p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':lg('communication'),'link':'','fct':'$p.admin.communication.buildPage();return false;'},
				{'label':lg('emailing'),'link':'','fct':''}
			]
		));
        var l_s='<p><a href="#" onclick="$p.admin.communication.emailing();return false;" class="optlist" style="font-size:11pt;font-weight:bold;">'+lg("sendAnEmail")+'</a> |\
        <a class="sellist" style="font-size:11pt;font-weight:bold;">'+lg("sentMessages")+'</a></p>\
        <table cellpadding="5" cellspacing="0" border="1" width="100%">\
        <tr bgcolor="#efefef">\
        <td>'+lg("sentDate")+'</td>\
        <td>'+lg("subject")+'</td>\
        <td>'+lg("sender")+'</td>\
        <td>'+lg("receiver")+'</td>\
        </tr>';
       
        var nb=$p.ajax.getVal(response,"nb","int",false,0);
        if (nb==0)   {
            l_s+='<tr><td colspan="4"><center>'+lg("noEmailSent")+'</center></td></tr>';
        }
        else    {
            var l_i=0;
            while (response.getElementsByTagName("contact")[l_i])
            {
                var l_result=response.getElementsByTagName("contact")[l_i];
                var maildate=$p.ajax.getVal(l_result,"maildate","str",false,"---");
                var id=$p.ajax.getVal(l_result,"id","int",false,0);
                var subject=$p.ajax.getVal(l_result,"subject","str",false,"---");
                var sender=$p.ajax.getVal(l_result,"sender","str",false,"---");
                var receiver=$p.ajax.getVal(l_result,"receiver","str",false,"---");
                l_s+='<tr>\
                        <td>'+maildate+'</td>\
                        <td><a href="#" onclick="$p.admin.communication.emailing_detail('+id+');return false;">'+subject+'</a></td>\
                        <td>'+sender+'</td>\
                        <td>'+receiver+'</td>\
                      </tr>';
                
                l_i++;
            }
        }
        
        l_s+='</table>';
        $('content').innerHTML=l_s;
    },
    /*
                Function: $p.admin.communication.emailing_detail
                
                                Load the detailed informations for a specific mailing
                                
                Returns:
                
                                false
         */
    emailing_detail:function(id)
    {
        $p.admin.tools.emptyContent();
        $p.ajax.call(padmin["xml_communication_emailing_detail"]+'?id='+id,
            {
                'type':'load',
                'callback':
                {
                    'function':$p.admin.communication.display_emailing_detail
                }
           }  
        );
        return false;
    },
    /*
                Function: $p.admin.communication.display_emailing_detail
                
                                Display the detailed informations for a specific mailing
                                
                Parameters:
                        
                                response - XML obect 
                                vars (array) - 
                                
                Returns:
                
                                false
         */
    display_emailing_detail:function(response,vars)
    {
        var l_s='';
        var sentdate=$p.ajax.getVal(response,"sentdate","str",false,"---");  
        var sender=$p.ajax.getVal(response,"sender","str",false,"---");
        var receiver=$p.ajax.getVal(response,"receiver","str",false,"---");
        var subject=$p.ajax.getVal(response,"subject","str",false,"---");        
        var message=$p.ajax.getVal(response,"message","str",false,"---");        
        
        l_s+='<h1><img src="../images/ico_adm_emailing.gif" /> '+lg("emailing")+' :</h1><br />\
        <a href="#" onclick="$p.admin.communication.emailing_history();return false;" >'+lg("backPrevPage")+'</a><br /><br />\
        <table>\
        <tr><td>'+lg("sentDate")+'</td><td><strong>'+sentdate+'</strong></td></tr>\
        <tr><td>'+lg("sender")+'</td><td><strong>'+sender+'</strong></td></tr>\
        <tr><td>'+lg("receiver")+'</td><td><strong>'+receiver+'</strong></td></tr>\
        <tr><td>'+lg("subject")+'</td><td><strong>'+subject+'</strong></td></tr>\
        <tr><td valign="top">'+lg("message")+'</td><td><strong>'+message+'</strong></td></tr>\
        </table>';
        
        $('content').innerHTML=l_s;
    }
}

//************************************* CONFIG  FUNCTIONS ***************************************************************************************************************
/*
    Class: $p.admin.application
         applications functions
*/
$p.admin.application={
    tabname:'',
    appSubTabs:[],
    buildPage:function()
    {
        var l_s='';
        $p.app.tabs.sel=5;
        $p.app.tabs.select(5);
        $p.admin.tools.emptyContent();  
        $p.admin.application.tabname='applicationtab';
        $p.admin.application.defineSubTabs(); 
        $p.admin.buildSubTabs($p.admin.application.appSubTabs,$p.admin.application.tabname);
        if ($p.app.tabs.sel && $p.app.tabs.sel > 0) {$p.admin.setState("$p.app.tabs.open("+$p.app.tabs.sel+")");}
        
    	$p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':lg('applicationConfiguration'),'link':'','fct':''}
			]
		));
		l_s+='<h2>'+lg("applications")+' :</h2>\
			<div id="applicationsdiv" class="greydiv" style="padding: 8px"></div><br />\
			<div id="newmod"></div>'; 
			//<p><a href="#" onclick=$p.admin.application.buildPage();return false;>'+lg("backPrevPage")+'</a></p>\
        
        $p.plugin.hook.launch('admin_applications'); 
        
        $('content').innerHTML=l_s;      
        $p.admin.application.load(1);
    },
    /*
                Function: $p.admin.application.defineSubTabs
                
                                define the application sub tabs
         */   
    defineSubTabs:function()
    {
        var tabTmp=new Array;
        for (var i=0;i<$p.admin.subTabs[$p.admin.application.tabname].length;i++)
        {
            tabTmp.push({'label':$p.admin.subTabs[$p.admin.application.tabname][i]['label'],
                         'fct':$p.admin.subTabs[$p.admin.application.tabname][i]['fct'],
                         'description':$p.admin.subTabs[$p.admin.application.tabname][i]['description']});
        } 
        $p.admin.application.appSubTabs[$p.admin.application.tabname]=tabTmp;         
    },
	load:function(v_page)
	{
		getXml(padmin["xml_applications"]+"?p="+v_page,$p.admin.application.display,v_page);
	},
	refresh:function()
	{
		$p.admin.application.load(1);
	},
	display:function(response,v_page)
	{
		var l_s="",l_result=response.getElementsByTagName('application');
		if(l_result.length==0){
			l_s+=lg('noApplication');
		}
		for (var i=0;i<l_result.length;i++)
		{
			l_s+="<a href='#' onclick=\"$p.admin.application.modify("+$p.ajax.getVal(l_result[i],'id','int',false,0)+");return false;\">"+$p.img($p.ajax.getVal(l_result[i],'icon','str',false,''),indef,indef,"","imgmid")+" "+$p.ajax.getVal(l_result[i],'title','str',false,'???')+"</a></br>";
		}
		$p.print('applicationsdiv',l_s);
	},
	modify:function(v_id)
	{
		$p.admin.widgets.hideFrame();
		$p.print("newmod","<h2>"+lg("applicationDetails")+"</h2><iframe id='frm' src='"+padmin['frm_application_modify']+"?appid="+v_id+"' width='980' height='600' frameborder='no' marginwidth='0' marginheight='0' style='border:1px solid #efefef'></iframe>");
		return false;
	}
}

//************************************* MODULES  FUNCTIONS ***************************************************************************************************************
/*
    Class: $p.admin.widgets
         modules functions
*/
$p.admin.widgets={
    widgetsInfos:[],
    menuid:0,
	catid:0,
	catname:0,
    dirid:0,
    dirname:0,
	levCatid:[],
	currLevel:0,
    keys:[], //initialize keywords
	isModuleShown:true,
    groups:[],
    selGroup:[],
    selGroupId:-1,
    selAvailGroupId:-1,
    catnames:[],
	catids:[],
	oldids:[],
	oldcatseqs:[],
	catlangs:[],
	currentwidgetid:0,
    /*
                  Function: $p.admin.widgets.init
                  
                                   Initialise the widgets management menu
         */
	init:function(v_reload)
	{
        $p.admin.widgets.loadWidgetsInfos();
		$p.admin.widgets.menu();
		if (!v_reload) $p.admin.widgets.getList(1,0);
		$p.admin.widgets.getNbModule();
		$p.admin.widgets.getNbValidate();
	},
    /*
                  Function: $p.admin.widgets.menu
                  
                                   Build the widgets management tab menu
                 
                  Returns:
                  
                                  HTML code
         */
	menu:function(v_selOption)
	{
		if (v_selOption == indef) {
			v_selOption = 1;
        }
        $p.admin.widgets.menuid=v_selOption;	

		var l_s = '<div class="feature">'
			+$p.html.buildFeatureMenu(v_selOption,[
				{'id':1,'fct':'$p.admin.widgets.menu(1);$p.admin.widgets.getList(1,0);','label':lg("lblModules"),'icon':''},
				{'id':2,'fct':'$p.admin.widgets.menu(2);$p.admin.widgets.initDir();','label':lg("lblDirectories"),'icon':''},
				{'id':3,'fct':'$p.admin.widgets.menu(3);$p.admin.widgets.getList(1,1);','label':lg("lblToValidate"),'icon':''},
				{'id':4,'fct':'$p.admin.widgets.menu(4);$p.admin.widgets.displayTutorialArea();','label':lg("createNewModule"),'icon':''}
			])
			+ '</div>';
            //{'id':5,'fct':'','label':lg("deletedModules"),'icon':''}
            //{'id':5,'fct':'$p.admin.widgets.menu(5);$p.admin.widgets.getModulesTmpDeleted();','label':lg("deletedModules"),'icon':''}

        $p.admin.widgets.catSel(0,0);
		$p.print("optmod",l_s);
	},
    /*
                  Function: $p.admin.widgets.defineMenuId
                  
                                   Set menu id and launch the javascript function
                 
                  Parameters:
                  
                                  v_id - button id
                                  action -  function to launch
        */
    defineMenuId:function(id,action)
    {
        $p.admin.widgets.menuid=id;
        eval(action);
    },
    
    displayTutorialArea:function(v_id)
    {
        //$p.admin.widgets.activateBtn(3);
		$p.admin.widgets.currentwidgetid = v_id;
        $('listmod').empty();
        $('directory').empty();
        $('newmod').empty();
        $p.print('newmod','<iframe src="'+padmin["tutorial"]+'" width="100%" height="1000" frameborder="0"></iframe>');
    },
    getModulesTmpDeleted:function()
    {},
    displayModulesTmpDeleted:function()
    {   
        //$p.admin.widgets.activateBtn(3);
        $('listmod').empty();
        $('directory').empty();
        $('newmod').empty();
    },
    keywordObj:function(id,priority,name)
    {
    	this.id=id;
    	this.priority=priority;
    	this.name=name;
    },
    /*
                  Function: $p.admin.widgets.activateBtn
                  
                                   Set a new class for the selected button
                 
                  Parameters:
                  
                                  v_id - button id
         */
	activateBtn:function(v_id)
	{
		for (var i=0;i<4;i++)
		{
			$p.setClass("btn"+i,"optlist");
		}
		$p.setClass("btn"+v_id,"sellist");	
	},
    /*
                  Function: $p.admin.widgets.initDir
                  
                                   Initialise the initial widget directory
                 
                  Parameters:
                  
                                  v_id - id
                                  v_name - name
         */
	initDir:function(v_id,v_name)
	{ 
        $p.admin.widgets.initDirBuildTable($p.admin.widgets.initDir,v_id,v_name);  
	},
    initDirParent:function(v_id,v_name)
    {
        $p.admin.widgets.initDirBuildTable($p.admin.widgets.initDirParent,v_id,v_name);
    },
    initDirBuildTable:function(v_fct,v_id,v_name)
    {
        // Initialize modules directory menu
		var l_s='<table border="0">\
                  <tr>\
                   <td>\
                    <div id="level0" class="dirdiva" style="width:170px;">\
                     <table cellpadding="0" cellspacing="0" width="90%">';
		
        for (var i=0;i<__dimension.length;i++)
		{
			if (__dimension[i]["id"]!=0)
			{
				if (v_id==indef){
                    v_id=__dimension[i]["id"];
                    v_name=__dimension[i]["name"];
                }   
                l_s+="<tr>\
                      <td id='dir"+__dimension[i]["id"]+"' class='"+((v_id==__dimension[i]["id"])?"catopta":"catopti")+"' onmouseover='catOptOver(\"dir"+__dimension[i]["id"]+"\")' onmouseout='catOptOut(\"dir"+__dimension[i]["id"]+"\")'>\
                      <a href='#' onclick=\"return "+v_fct+"('"+__dimension[i]["id"]+"','"+$p.string.removeCot(__dimension[i]["name"])+"')\">"+__dimension[i]["name"]+"("+__dimension[i]["lg"]+")</a>\
                      </td>\
                      </tr>";
            }
		}
		l_s+='</table><br />\
             [<a href="#" onclick="return $p.admin.widgets.mainDirModify();">'+lg('lblModifyTheDir')+'</a>]\
             </div></td>\
             <td><div id="level1" class="dirdivi" style="width:210px;"></div></td>\
             <td><div id="level2" class="dirdivi" style="width:200px;"></div></td>\
             <td><div id="level3" class="dirdivi" style="width:200px;"></div></td>\
             <td><div id="level4" class="dirdivi" style="width:170px;"></div></td>\
    		 </tr></table>'+$p.img("",7,7)+'<br />';
        
        //if its displayed in the same page
        if (v_fct==$p.admin.widgets.initDir){
            $p.admin.widgets.activateBtn(1);
            $p.print("newmod","");
            $p.show("listmod","block");
            $p.print("listmod",l_s);
        }
        //if it needs to update the parent page
        else if (v_fct==$p.admin.widgets.initDirParent){
            parent.$p.print('listmod',l_s);
        } 
            
        $p.print("level1","Chargement ...");
        $p.admin.widgets.getDir(v_id,1);
        $p.admin.widgets.catSel(v_id,v_name);
            
        return false;
    },
	getDir:function(v_cat,v_level,v_norefresh,v_secured)
	{
		// Open the modules directory
		if (!v_norefresh) v_norefresh=false;
		$p.admin.widgets.levCatid[v_level]=v_cat; 
		$p.admin.widgets.currLevel=v_level;
		getXml(padmin["xml_directory"]+"?catid="+v_cat,$p.admin.widgets.displayDir,new Array(v_level,v_cat,v_norefresh,v_secured));
	},
	displayDir:function(response,vars)
	{
		var l_s="";
		if (response.getElementsByTagName("dir")[0]||response.getElementsByTagName("item")[0])
		{
			l_s+="<table cellpadding='0' cellspacing='1' border='0' style='width:90%'>";
			if (response.getElementsByTagName("dir")[0])
			{
				var l_i=0,l_dirid;
				while (response.getElementsByTagName("dir")[l_i])
				{
					var l_result=response.getElementsByTagName("dir")[l_i];
					l_dirid=$p.ajax.getVal(l_result,"dirid","int",false,0);
					l_dirname=$p.string.removeCot($p.ajax.getVal(l_result,"dirname","str",false,"..."));
					l_secured=$p.ajax.getVal(l_result,"secured","int",false,0);
					var l_quantity=$p.ajax.getVal(l_result,"quantity","int",false,0);

					l_s+="<tr><td id='dir"+l_dirid+"' class='catopt"+(l_secured==0?"":"s")+"i' onmouseover='catOptOver(\""+l_dirid+"\","+l_secured+")' onmouseout='catOptOut(\""+l_dirid+"\","+l_secured+")'> <a href='#' class='menul' onclick='$p.admin.widgets.getDir(\""+l_dirid+"\","+(vars[0]+1)+",indef,"+l_secured+");$p.admin.widgets.catSel(\""+l_dirid+"\",\""+l_dirname+"\");return false;' "+(l_quantity==0?"style='color: #aaaaaa'":"")+">"+l_dirname+" ("+l_quantity+")</a></td></tr>";
					l_i++;
				}
			}
			if (response.getElementsByTagName("item")[0])
			{
				var l_i=0,l_itemid;
				while (response.getElementsByTagName("item")[l_i])
				{
					var l_result=response.getElementsByTagName("item")[l_i];
					l_itemid=$p.ajax.getVal(l_result,"id","int",false,0);
					v_icon = $p.ajax.getVal(l_result,"icon","str",false,0);
					if( v_icon!=0 ) {
						v_icon = $p.img(v_icon+"?rand="+rand,16,16,"","imgmid",l_itemid);
					} else {
						v_icon = $p.img("../modules/pictures/box0_"+l_itemid+"?rand="+rand,16,16,"","imgmid",l_itemid);
					}					
					l_s+="<tr><td>"+v_icon+" <a href='#' class='menul' onclick='$p.admin.widgets.loadMod("+l_itemid+");return false;'>"+$p.ajax.getVal(l_result,"name","str",false,"--")+"</a></td></tr>";
					l_i++;
				}
			}
			l_s+="</table>";
		}
		else
		{
			l_s+=lg("lblNoModule");
		}

		$p.setClass("level"+vars[0],"dirdiva");
		$p.print("level"+vars[0],l_s);
		//clear unused div
		if (!vars[2])
		{
			for (var i=vars[0]+1;i<5;i++)
			{
				$p.print("level"+i,"");
				$p.setClass("level"+i,"dirdivi");
			}
		}
		catOptSel(vars[1],vars[0]-1,vars[3]);
	},
	moduleBar:function()
	{
		var l_s="<div style='padding:5px;'><form name='search' onsubmit='$p.admin.widgets.getSearch(document.search.searchtxt.value,0);return false;'>"+lg("searchMod")+" : <input class='thinbox' name='searchtxt' type='text' size='20' style='height:20px' value='' /><input type='submit' name='buts' class='btn' value='Go' />";
		l_s+="</form></div>";
		return l_s;
	},
	validateModuleBar:function()
	{
		var l_s="<div style='padding:5px;'><form name='search' onsubmit='$p.admin.widgets.getSearch(document.search.searchtxt.value,0);return false;'>"+lg("searchMod")+" : <input class='thinbox' name='searchtxt' type='text' size='20' style='height:20px' value='' /><input type='submit' name='buts' class='btn' value='Go' />";
		l_s+=" &nbsp; | &nbsp; <a href='#' onclick='$p.admin.widgets.validateAllModules();return false;'>"+lg("validateAllModules")+"</a>";
		l_s+="</form></div>";
		return l_s;
	},
    /*
                    Function: $p.admin.widgets.validateAllModules
                    
                                     Validate all modules
          */
    validateAllModules:function()
    {
        $p.ajax.call(padmin["scr_module_validateall"],
            {
                'type':'execute',
                'forceExecution':true,
                'callback':
                {
                    'function':$p.admin.widgets.refreshTabs
                }
            }
        );
    },
    /*
                    Function: $p.admin.widgets.refreshTabs
                    
                                     Refresh the menu and the awaiting modules
          */
    refreshTabs:function()
    {
        $p.admin.widgets.init();
        $p.admin.widgets.menu(1);
        $p.admin.widgets.getList(1,0);
        $p.admin.widgets.loadWidgetsInfos();
        //$p.admin.widgets.defineMenuId(2,"$p.admin.widgets.getList(1,1);");
    },
	getNbModule:function()
	{
        getXml(padmin["xmlnbmodules"],$p.admin.widgets.updateVar,"btn0nb");
	},
	getNbValidate:function()
	{
		getXml(padmin["xmlnbtovalidate"],$p.admin.widgets.updateVar,"btn2nb");
	},
	updateVar:function(response,vars)
	{
        $p.print(vars,"("+$p.ajax.getVal(response,"return","str",false,"?")+")");
	},
	refreshDir:function(v_prev)
	{
		reset_rand();//to reset all images
		if ($p.admin.widgets.currLevel!=0)
		{
			var l_currLevel=(v_prev&&$p.admin.widgets.currLevel>1)?($p.admin.widgets.currLevel-1):$p.admin.widgets.currLevel;
			//re apply current dir selection
			$p.admin.widgets.getDir($p.admin.widgets.levCatid[l_currLevel],l_currLevel,true,dirOptSelSec[l_currLevel-1]);
            if (l_currLevel<4)
			{
				$p.print("level"+(l_currLevel+1),"");
				$p.setClass("level"+(l_currLevel+1),"dirdivi");
			}
			//if group suppressed or changed, hide the options
			if (v_prev)
			{
				$p.admin.widgets.catSel(0,0);
			}
			$p.admin.widgets.getNbModule();
			$p.admin.widgets.getNbValidate();
		}
		else
		{
			$p.admin.widgets.init();
		}
	},
	catSel:function(v_id,v_name)
	{
		$p.admin.widgets.catid=v_id;
		$p.admin.widgets.catname=v_name;
		$p.admin.widgets.showDirOptions();
	},
	loadMod:function(v_id)
	{
		$p.admin.widgets.hideFrame();
        $p.ajax.call(padmin["xml_widgets_modify"]+'?itemid='+v_id,
            {
                'type':'load',
                'callback':
                {
                    'function':$p.admin.widgets.getMod,
                    'variables':
					{
						'itemid':v_id
					}
                }
           }  
        );
        return false;
    },
    getMod:function(response,vars)
    {
        var l_s="";
        var l_i=0;
        var typOption="";
        var selection="";
        var statusOption="";
        var watch="";

        //get category information
        $p.admin.widgets.dirid=$p.ajax.getVal(response,"dirid","int",false,0);
        $p.admin.widgets.dirname=$p.ajax.getVal(response,"dirname","str",false,"/");
        //get module information
        var minwidth=$p.ajax.getVal(response,"minwidth","int",false,280);
        var typ=$p.ajax.getVal(response,"typ","str",false,"P");
        var status=$p.ajax.getVal(response,"status","str",false,"O");
        var url=$p.ajax.getVal(response,"url","str",false,"");
        var id=$p.ajax.getVal(response,"id","int",false,0);
        var name=$p.ajax.getVal(response,"name","str",false,"");
        var description=$p.ajax.getVal(response,"description","str",false,"");
        var website=$p.ajax.getVal(response,"website","str",false,"");
        var format=$p.ajax.getVal(response,"format","str",false,"I");
        var height=$p.ajax.getVal(response,"height","int",false,0);
        var defvar=$p.ajax.getVal(response,"defvar","str",false,"");
        var nbvariables=$p.ajax.getVal(response,"nbvariables","str",false,"");
		var icon=$p.ajax.getVal(response,"icon","str",false,0);
			icon=(icon!=0)?icon:'../modules/pictures/box0_'+id;
		var views = $p.ajax.getVal(response,"views","str",false,0);
		var editable = $p.ajax.getVal(response,"editable","str",false,0);
        //get keywords
		$p.admin.widgets.keys=[];
        while (response.getElementsByTagName("keyword")[l_i])
        {
            var l_result=response.getElementsByTagName("keyword")[l_i];
            var kid=$p.ajax.getVal(l_result,"kid","int",false,0);
            var kweight=$p.ajax.getVal(l_result,"kweight","int",false,0);
            var klabel=$p.ajax.getVal(l_result,"klabel","str",false,"");
            $p.admin.widgets.keys[l_i]=new $p.admin.widgets.keywordObj(kid,kweight,klabel);
            l_i++;
        }
        
        if (typ=="R")   { typOption='<option value="R" selected="selected">RSS Module</option>';   }
        else  { 
            if (typ=="E")  { selection="selected=selected";   }
            typOption='<option value="E" '+selection+'>'+lg("enterprise")+'</option>';
            selection="";
            if (typ=="P")  { selection="selected=selected"; }
            typOption+='<option value="P" '+selection+'>Portaneo</option>';
            selection="";
        }
        
        if (status=="O")  { selection="selected=selected";  }
            statusOption+='<option value="O" '+selection+'>'+lg("active")+'</option>';
        selection="";
        if (status=="W")  { selection="selected=selected";  }
            statusOption+='<option value="W" '+selection+'>'+lg("notactive")+'</option>';
        selection="";
        if (status=="N")  { selection="selected=selected";  }
            statusOption+='<option value="N" '+selection+'>'+lg("waiting")+'</option>';
        selection="";
        if (status=="S")  { selection="selected=selected";  }
            statusOption+='<option value="S" '+selection+'>'+lg("suppressed")+'</option>';
        selection="";
        
        if (format=="I" 
            || format=="U") { watch=lg("watch"); }
       
        
        //HTML bloc
        l_s+='<table cellpadding="0" cellspacing="10" border="0">\
            	<tr>\
            	<td valign="top" width="430">\
            	<form name="f" accept-charset="UTF-8">\
            	<table cellpadding="10" cellspacing="0" border="0" width="100%">\
            		<tr>\
            		<td width="440">\
            		<div class="bottomhr"><strong>'+lg("generalParameter")+'</strong></div>\
            		<table>\
                        <tr>\
                            <td>'+lg("title")+' :</td>\
                            <td><input type="text" name="name" value="'+name+'" maxlength="30" /></td>\
                        </tr>\
                        <tr>\
                            <td>'+lg("id")+' : </td>\
                            <td>'+id+'</td>\
                        </tr>\
                        <tr>\
                            <td valign="top">'+lg("desc")+' :</td>\
                            <td><textarea name="desc" cols=30 rows=3>'+description+'</textarea></td>\
                        </tr>\
                        <tr>\
                            <td>'+lg("url")+' :</td>\
                            <td><input type="text" name="url" value="'+url+'" size="40" maxlength="250" /></td>\
                        </tr>\
                        <tr>\
                            <td>'+lg("website")+' :</td>\
                            <td><input type="text" name="website" value="'+website+'" size="40" maxlength="50" /></td>\
                        </tr>\
            			<tr>\
                            <td>'+lg("type")+' :</td>\
                            <td><select name="typ">'+typOption+'</select></td>\
                        </tr>\
                        <tr>\
                            <td>'+lg("status")+' :</td>\
                            <td><select name="status">'+statusOption+'</select></td>\
                        </tr>\
                        <tr>\
                            <td>'+lg("height")+' :</td>\
                            <td><input type="text" name="size" value="'+height+'" size="3" maxlength="5" />\
                                <a href="#" onclick="$p.admin.widgets.applyHeight();return false;">'+lg("watch")+'</a>\
                            </td>\
                        </tr>\
                        <tr>\
                            <td>'+lg("minWidth")+' :</td>\
                            <td><input type="text" name="minwidth" value="'+minwidth+'" size="3" maxlength="5" />\
                                <a href="#" onclick="$p.admin.widgets.applyWidth();return false;">'+lg("watch")+'</a>\
                            </td>\
                        </tr>\
                        <tr>\
                            <td>'+lg("dir")+' :<input type="hidden" name="catid" value="'+$p.admin.widgets.dirid+'" />\
                            <input type="hidden" name="oldcatid" value="'+$p.admin.widgets.dirid+'" /></td>\
                            <td id="dirdiv"></td>\
                        </tr>\
            		</table>\
            		<br /><br />\
					<input type="hidden" name="views" value="'+views+'" />\
            		<div class="bottomhr"><strong>'+lg("tags")+'</strong></div>\
            		<div id="keywordlist"></div>\
            		<br /><br />\
            		<div class="bottomhr"><strong>'+lg("icon")+'</strong></div>\
            		<input type="hidden" name="icon" value="" />\
            		<div id="iconlist"></div>\
            		<br /><br />\
            		<input type="hidden" name="itemid" value="'+vars['itemid']+'" />\
            		<input type="button" value="'+lg("saveModifications")+'" class="largesubmit" onclick="$p.admin.widgets.update_module();return false;" />\
                    <br /><br />\
                    <br /><br />\
            		<div class="bottomhr"><strong>'+lg("otherOptions")+'</strong></div><br />';
					if ( editable==1 )
						l_s +='<a href="#" onclick="$p.admin.widgets.menu(4);$p.admin.widgets.displayTutorialArea('+id+');">'+lg("modifWidgetContent")+'</a><br />';
					
                    l_s +='<a href="#" onclick="$p.admin.widgets.duplicate('+vars['itemid']+',\''+icon+'\');">\
                    <img src="../images/ico_menu_examples.gif" align="absmiddle" />'+lg("duplicateModule")+'</a><br />\
                    <a href="#" onclick="$p.admin.widgets.suppress_module()"><img src="../images/ico_suppress.gif" align="absmiddle" />'+lg("suppressModule")+'</a><br /><br />\
            		<div class="bottomhr"><strong>Embed</strong></div><br />\
            		<textarea rows="4" cols="70"><iframe id="portaneowidget" src="'+__LOCALFOLDER+'portal/widgetforyoursite.php?id='+id+'" width="400" height="300" frameborder="0"></iframe>\
                    </textarea>\
                    </td>\
            		</tr>\
                    </table>\
                    </form>\
                </td>\
                <td valign="top" width="430">\
                <center>\
                    <table id="tbl1" cellpadding="0" cellspacing="0" border="0" style="width:400px;height:'+height+'px;border:1px dotted black;">\
                        <tr>\
                            <td id="widgetcontainer" style="font-size:1.2em"></td>\
                        </tr>\
                    </table>\
                </center><br />\
                </td>\
                <td valign="top" bgcolor="#efefef">\
                </tr>\
            </table>\
            </div>';              

        
        $p.print("newmod",l_s);
        //widget modification
        $p.app.standalone($('widgetcontainer'),1,true,1);
		// add columns
        tab[1].module[0]=new $p.app.widgets.object(1,1,1,height,id,'--','--',defvar,'1',1,1,url,0,0,1,format,nbvariables,1);
        tab[1].module[0].tab='modulestab';
        tab[1].module[0].create( height );
        tab[1].module[0].show();
        $p.admin.widgets.applyWidth();
        $p.admin.widgets.showDir();
        $p.admin.widgets.displayKeywords();
        $p.admin.widgets.refreshIcons(icon,id); 
		
    },    
    duplicate:function(v_id, icon)
    {
        $p.ajax.call(padmin["scr_module_duplicate"],
            {
                'type':'execute',
                'variables':"itemid="+v_id+"&icon="+icon,
                'forceExecution':true
            }
        );
        $p.admin.widgets.refreshDir();
        return false;
    },
    getModifyFormValues:function()
    {
        var formValues = [];
        var keywords="";
        var priority="";
        formValues['name']=document.forms["f"].name.value;
        formValues['desc']=document.forms["f"].desc.value;
        formValues['url']=document.forms["f"].url.value;
        formValues['size']=document.forms["f"].size.value;
        formValues['status']=document.forms["f"].status.value;
        formValues['website']=document.forms["f"].website.value;
        formValues['typ']=document.forms["f"].typ.value;
		//formValues['height']=document.forms["f"].size.value;
        formValues['minwidth']=document.forms["f"].minwidth.value;
        formValues['catid']=document.forms["f"].catid.value;
        formValues['oldcatid']=document.forms["f"].oldcatid.value;
        formValues['icon']=document.forms["f"].icon.value;
        formValues['itemid']=document.forms["f"].itemid.value;
		formValues['views']=document.forms["f"].views.value;
        for (var i=0;i<$p.admin.widgets.keys.length;i++)
        {
            keywords+='kw'+i+'='+document.forms["f"].elements['kw'+i].value+'&';
            priority+='w'+i+'='+document.forms["f"].elements['w'+i].value+'&';
        }
        keywords=keywords.substring(0,keywords.length-1);
        priority=priority.substring(0,priority.length-1);
        formValues['keywords']=keywords;
        formValues['priority']=priority;
        
        return formValues;
    },
	suppress_module:function()
    {
        var widValues=$p.admin.widgets.getModifyFormValues();
        widValues['status']="S";   
		$p.admin.widgets.setModifications(widValues);  
		$p.admin.widgets.refreshDir();
        return false;
	},
    update_module:function()
    {
        var widValues=$p.admin.widgets.getModifyFormValues();
        $p.admin.widgets.setModifications(widValues);  
        return false;
    },
    setModifications:function(widValues)
    {
        widValues['url'] = $p.string.esc(widValues['url']);
		//alert('FRI setModifications url='+widValues['url']);
        $p.ajax.call(padmin["scr_module_modify"],
			{
				'type':'execute',
				'variables':widValues['keywords']+"&"+widValues['priority']+"&itemid="
                                +widValues['itemid']+"&catid="+widValues['catid']+"&oldcatid="
                                +widValues['oldcatid']+"&minwidth="+widValues['minwidth']+"&name="
                                +widValues['name']+"&typ="+widValues['typ']+"&desc="+widValues['desc']
                                +"&status="+widValues['status']+"&size="+widValues['size']
                                +"&website="+widValues['website']+"&icon="+widValues['icon']
                                +"&url="+widValues['url']+"&views="+widValues['views'],
				'forceExecution':true,
                'callback':
				{
					'function':$p.admin.widgets.refreshWidgetsList
                }
			}
		);
        return false;
    },
    /*
                    Function: $p.admin.widgets.refreshWidgetsList
                    
                                     Load widgets list depending on the menu opened
          */
    refreshWidgetsList:function()
    {   
        if ($p.admin.widgets.menuid==1) { $p.admin.widgets.init(); }
        if ($p.admin.widgets.menuid==2) { $p.admin.widgets.initDir(); }  
        $p.admin.widgets.loadWidgetsInfos();
        return false;
    },
    /*
                    Function: $p.admin.widgets.loadValMod
                    
                                     Load waiting to be validated widgets informations
          */
	loadValMod:function(v_id)
	{
		$p.admin.widgets.hideFrame();
        $p.ajax.call(padmin["xml_module_validate"]+'?itemid='+v_id,
            {
                'type':'load',
                'callback':
                {
                    'function':$p.admin.widgets.displayValMod,
                    'variables':
					{
						'itemid':v_id
					}
                }
           }  
        );
        return false; 
	},
    displayValMod:function(response,vars)
    {
        var v_id=vars['itemid'];
        var l_s="";
        $p.admin.widgets.keys=[];
        $p.admin.widgets.dirid=0;
        $p.admin.widgets.dirname="/";
        $p.admin.widgets.dirid=$p.ajax.getVal(response,"dirid","int",false,0);
        $p.admin.widgets.dirname=$p.ajax.getVal(response,"dirname","str",false,"/");     
        var minwidth=$p.ajax.getVal(response,"minwidth","int",false,200);
        var url=$p.ajax.getVal(response,"url","str",false,"");
        var username=$p.ajax.getVal(response,"username","str",false,"");
        username = $p.string.replacePlus(username);
        var long_name=$p.ajax.getVal(response,"long_name","str",false,"");
        var id=$p.ajax.getVal(response,"id","int",false,0);
        var description=$p.ajax.getVal(response,"description","str",false,"");
        var views=$p.ajax.getVal(response,"views","str",false,"home");
        var name=$p.ajax.getVal(response,"name","str",false,"");
        var website=$p.ajax.getVal(response,"website","str",false,"");
        var height=$p.ajax.getVal(response,"height","int",false,100);
        var lang=$p.ajax.getVal(response,"lang","str",false,"fr");
        var format=$p.ajax.getVal(response,"format","str",false,"");
        var defvar=$p.ajax.getVal(response,"defvar","str",false,"");
        var nbvariables=$p.ajax.getVal(response,"nbvariables","int",false,0);
        var keywords=$p.ajax.getVal(response,"keywords","str",false,"");
        var tabKeyword=keywords.split(',');
        var watch="";
        var displayedName=long_name;
        for (var i=0;i<tabKeyword.length;i++)
        {
            if (tabKeyword[i]!="")  { 
                $p.admin.widgets.keys[i]=new $p.admin.widgets.keywordObj(i,2,tabKeyword[i]); 
            }
        }
        if (format=="I" || format=="U") {   
            watch=lg("watch");    
        }
        if (long_name=="")  {  
            displayedName=username;  
        }
                                  
        //HTML bloc construction
        l_s+='<table cellpadding="0" cellspacing="10" border="0">\
            	<tr>\
            	<td valign="top" width="430">\
            	<center>\
            	<table id="tbl1" cellpadding="0" cellspacing="0" border="0">\
            		<tr>\
            		<td id="widgetcontainer"></td>\
            		</tr>\
            	</table>\
            	</center>\
            	</td>\
            	<td valign="top" bgcolor="#efefef">\
            	<form method="post" name="f" accept-charset="UTF-8">\
            	<table cellpadding="0" cellspacing="0" border="0" width="100%">\
               		<input type="hidden" name="username" value="'+username+'" />\
            		<tr>\
                        <td>'+lg("proposedBy")+' :</td>\
                        <td><strong>'+displayedName+'</strong></td>\
                    </tr>\
            		<tr>\
                        <td>'+lg("tempId")+' :</td>\
                        <td>'+id+'</td>\
                    </tr>\
            		<tr>\
                        <td>'+lg("title")+' :</td>\
                        <td><input type="text" name="name" value="'+name+'" maxlength="30" /></td>\
                    </tr>\
            		<tr>\
                        <td valign="top">'+ lg("description")+' :</td>\
                        <td><textarea name="desc" cols="30" rows="3">'+description+'</textarea></td>\
                    </tr>\
            		<tr>\
                        <td>'+lg("url")+' :</td>\
                        <td><input type="text" name="url" value="'+url+'" size="40" maxlength="250" /></td>\
                    </tr>\
            		<tr>\
                        <td>'+lg("website")+' :</td>\
                        <td><input type="text" name="website" value="'+website+'" size="40" maxlength="50" /></td>\
                    </tr>\
            		<tr>\
                        <td>'+lg("height")+' :</td>\
                        <td><input type="text" name="size" value="'+height+'" size="3" maxlength="3" /><a href="#" onclick="$p.admin.widgets.applyHeight();return false;">'+lg("watch")+'</a></td>\
                    </tr>\
                    <tr>\
                        <td>'+lg("minWidth")+' :</td>\
                        <td><input type="text" name="minwidth" value="'+minwidth+'" size="3" maxlength="3" /><a href="#" onclick="$p.admin.widgets.applyWidth();return false;">'+lg("watch")+'</a></td>\
                    </tr>\
                    <tr><td>'+lg("language")+' : </td>\
                        <td>'+lang+'</td>\
                    </tr>\
                    <tr>\
                        <td>'+lg("dir")+' :<input type="hidden" name="catid" value="'+$p.admin.widgets.dirid+'" /><input type="hidden" name="oldcatid" value="'+$p.admin.widgets.dirid+'" /></td>\
                        <td id="dirdiv"></td>\
                    </tr>\
            	</table>\
            	<div valign="top" id="keywordlist"></div>\
            	<br /><br />\
            	<center><input type="hidden" name="itemid" value="'+v_id+'" />\
            	<input type="hidden" name="views" value="'+views+'" />\
            	<input type=submit value="'+lg("modValidate")+'" id="largesubmit" onclick="$p.admin.widgets.setValMod();return false;" /><br /><br />\
                <a href="#" onclick="$p.admin.widgets.deleteValMod('+v_id+');return false;"><img src="../images/ico_suppress.gif" />'+lg("suppressModule")+'</a><br />\
            	</center>\
            	</form>\
            	</td>\
            	</tr>\
            </table>\
            </div>\
            <div id="debug"></div>';
                   
        $p.print("newmod","<h2>"+lg("lblValidateModule")+"</h2>"+l_s);
        $p.admin.widgets.displayKeywords();
        $p.app.env="tutorial"; // to get widget files in quarantine folder
        //widget modification
        $p.app.standalone($('widgetcontainer'),1,true,1);
		// add columns
        tab[1].module[0]=new $p.app.widgets.object(1,1,1,height,id,'--','--',defvar,'1',1,1,url,0,0,1,format,nbvariables,1);
        tab[1].module[0].tab='modulestab';
        $p.admin.widgets.security(1,0);    
        $p.admin.widgets.applyWidth();
        $p.admin.widgets.showDir();
    },
    setValMod:function()
    {
        var keywords="";
        var priority="";
        var username=document.forms['f'].username.value;
        var itemid=document.forms['f'].itemid.value;
        var catid=document.forms['f'].catid.value;
        var url=document.forms['f'].url.value;
        var size=document.forms['f'].size.value;
        var minwidth=document.forms['f'].minwidth.value;
        var desc=document.forms['f'].desc.value;
        var website=document.forms['f'].website.value;
        var name=document.forms['f'].name.value;
        var views=document.forms['f'].views.value;
        
        username = $p.string.replacePlus(username);
        
        for (var i=0;i<$p.admin.widgets.keys.length;i++)
        {
			if( document.forms["f"].elements['kw'+i] ) {
				keywords+='&kw'+i+'='+document.forms["f"].elements['kw'+i].value;
            }    
			if( document.forms["f"].elements['w'+i] ) {
				priority+='&w'+i+'='+document.forms["f"].elements['w'+i].value;
            }
        }

        url = $p.string.esc(url);
        $p.ajax.call(padmin["scr_module_validate"],
			{
				'type':'execute',
				'variables':"username="+username
                             +"&itemid="+itemid
                             +"&catid="+catid
                             +"&url="+url
                             +"&size="+size
                             +"&minwidth="+minwidth
                             +"&desc="+desc
                             +"&views="+views
                             +"&website="+website
                             +"&name="+name
                             +"&"+keywords
                             +"&"+priority,
				'forceExecution':true,
                'callback':
				{
					'function':$p.admin.widgets.init
                }
			}
		);   
    },
	
    /*
                    Function: $p.admin.widgets.deleteValMod
                    
                                    Delete a module
                                 
                    Parameter:
                    
                                    v_id - module ID
          */
    deleteValMod:function(v_id)
    {
       $p.ajax.call(padmin["scr_module_validate_remove"],
            {
                'type':'execute',
                'forceExecution':true,
                'variables':'itemid='+v_id,
                'callback':
				{
					'function':$p.admin.widgets.refreshTabs
                }
            }
       );
    },
	dirModify:function(v_id)
	{
        $p.admin.widgets.hideFrame();
        $p.ajax.call(padmin["xml_directory_modify"]+'?catid='+v_id,
            {
                'type':'load',
                'callback':
                {
                    'function':$p.admin.widgets.displayDirModify,
                    'variables':
					{
						'id':v_id
					}
                }
           }  
        );
        return false;
    },  
    displayDirModify:function(response,vars)
    {            
        $p.admin.widgets.groups=[];
        $p.admin.widgets.selGroup=[];
        $p.admin.widgets.selGroupId=-1;
        $p.admin.widgets.selAvailGroupId=-1;
        $p.admin.users.selGroup=[];
        
        var l_s="";
        var i=0;
        var v_id=vars['id']; 
        var name=$p.ajax.getVal(response,"name","str",false,"");
        var lang=$p.ajax.getVal(response,"lang","str",false,"");
        
        l_s+='<form name="f" method="post"><br />\
                 '+lg("dirModify")+' <input type=text name="dirname" maxlength=60 value="'+name+'" />\
                 <input type="hidden" name="dirlang" value="'+lang+'" />\
                 <input type="hidden" name="dirid" value="'+v_id+'"/><br /><br /><br />\
                 <strong>'+lg("addGroupsAccessOnDirectory")+' :</strong><br /><br />\
                  <div id="groupdiv">\
                	<table>\
                		<tr>\
                		<td>'+lg("userGroups")+' :<br /><br /><div id="selgroupdiv"></div></td>\
                		<td><input type="button" value="<" onclick="return $p.admin.users.addSelectedGroup();"><br /><br />\
                            <input type="button" value=">" onclick="return $p.admin.users.supSelectedGroup();">\
                        </td>\
                		<td>'+lg("availableGroups")+' :<br /><br /><div id="allgroupdiv" ><div id="exp0"></div></div></td>\
                		</tr>\
                	</table>\
                	<div id="groupinputs"></div>\
                 </div>\
                 <br /><br /><input type="submit" onclick="$p.admin.widgets.setModifyDir();return false;" value="'+lg("modify")+'" id="largesubmit" />\
              </form>';
        
        $p.print("newmod","<h2>"+lg("lblModifydir")+"</h2>"+l_s);
        
        $p.admin.users.loadAvailGroups(0);
        while (response.getElementsByTagName("directory")[i])
        {
            l_result=response.getElementsByTagName("directory")[i];
            var l_id=$p.ajax.getVal(l_result,"did","int",false,0);
            var l_name=$p.ajax.getVal(l_result,"dname","str",false,"");
            //$p.admin.widgets.selGroup.push(new $p.admin.widgets.groupObj(l_id,l_name));
			$p.admin.users.selGroup.push(new $p.admin.widgets.groupObj(l_id,l_name));
            i++;
        }
        $p.admin.users.showSelectedGroups();
	},
    setModifyDir:function()
    {
       var dirid=document.forms["f"].dirid.value;
       var dirname=document.forms["f"].dirname.value;
       var dirlang=document.forms["f"].dirlang.value;
       var groupList="";
       if (dirname!="") {
           for (var i=0;i<$p.admin.users.selGroup.length;i++)
           {
               groupList+="group"+i+"="+$p.admin.users.selGroup[i].id+"&";
           }
           groupList=groupList.substring(0,groupList.length-1);
           $p.ajax.call(padmin["scr_directory_modify"],
    			{
    				'type':'execute',
    				'forceExecution':true,
                    'variables':groupList+"&dirid="+dirid+"&dirname="+dirname+"&dirlang="+dirlang,
                    'callback':
    				{
    					'function':$p.admin.widgets.directoryModify
    				}
    			}
    	   );
       }   
       return false;
    },
    /*
                        Function: $p.admin.widgets.directoryModify
                                
                                        confirm the directory was sucessfully modified
          */
    directoryModify:function(response,vars)
    {
        $p.print("newmod",lg("modifProcessed")+" !");
        $p.admin.widgets.initDir();
    },
    /*
                        Function: $p.admin.widgets.dirAdd
                                
                                        Load datas required to display the form
                                        
                        Parameters:
                        
                                        v_id - category ID
          */
	dirAdd:function(v_id)
	{
       $p.admin.users.selGroup=[];
       $p.ajax.call(padmin["xml_directory_add"]+"?catid="+v_id,
			{
				'type':'load',
                'forceExecution':true,
				'callback':
				{
					'function':$p.admin.widgets.displayDirAdd,
					'variables':
					{
						'id':v_id
					}
				}
			}
		);
     },
    /*
                    Function: $p.admin.widgets.displayDirAdd
    
                                    Display the 'add a directory' form
                                   
                    Parameters:
                    
                                    response - XML object
                                    vars - vars['v_id'] category id
          */
     displayDirAdd:function(response,vars)
     {    
        var l_s="";
        var v_id=vars['id'];
        var typ=$p.ajax.getVal(response,"typ","str",false,"O");
        var lang=$p.ajax.getVal(response,"lang","str",false,"");
        $p.admin.widgets.hideFrame();
        
        l_s+='<form name="f" method="post"><br />\
             <input type=hidden name="dirtyp" value="'+typ+'" />\
             <input type=hidden name="dirlang" value="'+lang+'" />\
             '+lg("addSubDir")+' : <input type="text" name="dirname" maxlength="60" value="" />\
                 <input type="hidden" name="dirid" value="'+v_id+'" /><br /><br /><br />\
                 <strong>'+lg("addGroupsAccessOnDirectory")+' :</strong><br /><br />\
                  <div id="groupdiv">\
                	<table>\
                		<tr>\
                		<td>'+lg("userGroups")+' :<br /><br /><div id="selgroupdiv"></div></td>\
                		<td><input type="button" value="<" onclick="return $p.admin.users.addSelectedGroup();"><br /><br /><input type="button" value=">" onclick="return $p.admin.users.supSelectedGroup();"></td>\
                		<td>'+lg("availableGroups")+' :<br /><br /><div id="allgroupdiv"><div id="exp0"></div></div></td>\
                		</tr>\
                	</table>\
                	<div id="groupinputs"></div>\
                 </div>\
                 <br /><br /><input type="button" onclick="$p.admin.widgets.setNewDirectory();return false;" value="'+lg("add")+'" id="largesubmit"/>\
                </form>';
       
        $p.print("newmod","<h2>"+lg("lblCategoryAdd")+"</h2>"+l_s);
        $p.admin.users.loadAvailGroups(0);
        document.forms["f"].dirname.focus();
		return false;
	},
    /*
                    Function: $p.admin.widgets.setNewDirectory
    
                                    Add the new directory in the database
          */
    setNewDirectory:function()
    {
       var dirid=document.forms["f"].dirid.value;
       var dirname=document.forms["f"].dirname.value;
       var dirtyp=document.forms["f"].dirtyp.value;
       var dirlang=document.forms["f"].dirlang.value;
       var groupList="";
       if (dirname!="")  {
           for (var i=0;i<$p.admin.users.selGroup.length;i++)
           {
               groupList+="group"+i+"="+$p.admin.users.selGroup[i].id+"&";
           }
           groupList=groupList.substring(0,groupList.length-1);
           
           $p.ajax.call(padmin["scr_directory_add"],
    			{
    				'type':'execute',
    				'forceExecution':true,
                    'variables':groupList+"&dirid="+dirid+"&dirname="+dirname+"&dirtyp="+dirtyp+"&dirlang="+dirlang,
                    'callback':
    				{
    					'function':$p.admin.widgets.displayNewDirectory
    				}
    			}
    	   );
       }
       return false;
    },
    /*
                function: $p.admin.widgets.displayNewDirectory
    
                                 Inform the user that the category was successfully added 
          */
    displayNewDirectory:function() 
    {
        $p.app.alert.show(lg("dirAdded")+" !");
        $p.admin.widgets.initDir();
        return false;
    },
    /*
                function: $p.admin.widgets.dirSuppress
    
                                 Delete a widget category 
          */
	dirSuppress:function(v_id)
	{
		l_response=confirm(lg("msgDirSup",$p.admin.widgets.catname));
		if (l_response==1) {
			$p.admin.widgets.hideFrame();
            $p.ajax.call(padmin["xml_directory_suppress"]+"?catid="+v_id,
    			{
    				'type':'load',
                    'forceExecution':true,
    				'callback':
    				{
    					'function':$p.admin.widgets.displayDirSuppress,
    					'variables':
    					{
    						'id':v_id
    					}
    				}
    			}
    		);
        }
    },
    /*
                Function: $p.admin.widgets.displayDirSuppress
    
                                 Inform the user that the category was successfully deleted
                
                Parameters:
                
                                  response - XML object
          */   
    displayDirSuppress:function(response,vars)
    {
        var error=$p.ajax.getVal(response,"error","int",false,1);
        if (error==1)   {
            var nbitem=$p.ajax.getVal(response,"nbitem","int",false,1);
            var nbcat=$p.ajax.getVal(response,"nbcat","int",false,1);
            $p.print('newmod',"<h2>"+lg("lblSuppressDir")+"</h2><font color=#ff0000>"+lg("dirNotEmpty")+" ("+nbitem+" module(s) et "+nbcat+" "+lg("dir")+"(s))</font>");
        }
        else    {
            $p.app.alert.show(lg("dirSuppressed")+" !");
            $p.admin.widgets.initDir();
        }
        return false;
	},
    /*
                    Function: $p.admin.widgets.dirMove
                    
                                     Display the direcotry moving form
           */
	dirMove:function()
	{
		$p.admin.widgets.hideFrame();
        var l_s='<form name="f"><div id="movediv"></div></form>';
        $p.print("newmod",l_s);
        $p.admin.widgets.directoryMoveInit();
	},
    /*
                     Function: $p.admin.widgets.setDirMove
                    
                                     Update the new directory hierarchy
                                     
                     Parameter:
                     
                                    catid - category id
                                    catname - category name
                     
                     Returns:
                     
                                    false
           */
    setDirMove:function(catid,catname)
    {
        if ($p.admin.widgets.getNewParent(catid,catname))  { 
            var catid=document.forms['f'].catid.value;
            var parentid=document.forms['f'].parentid.value;          
            $p.ajax.call(padmin["scr_directory_move"],
                {
                    'type':'execute',
                    'variables':"catid="+catid+"&parentid="+parentid,
                    'forceExecution':true,
                    'callback':
                    {
                        'function':$p.admin.widgets.displayDirMoved
                    }
                }
            );                     
        }
        return false; 
    },
    /*
                    Function: $p.admin.widgets.displayDirMoved
                    
                                     Display directory sucessfully moved message
           */
    displayDirMoved:function()
    {
       $p.print("newmod",lg("modifProcessed")+" !");
       $p.admin.widgets.refreshDir(true);        
    },
	directoryMoveInit:function(){
        var catid=$p.admin.widgets.catid;
        var catname=$p.admin.widgets.catname;
		var l_s="<strong>"+lg("lblMoveDir")+" <font color='#ff0000'>"+catname+"</font> : </strong>"+lg("lblSelectDir","<I>"+catname+"</I>")+" <input type='hidden' name='catid' value='"+catid+"' /><input type='hidden' name='parentid' value='0' /><input type='button' onclick=$p.admin.widgets.setDirMove("+catid+",\""+catname+"\"); value='"+lg("lblMove")+"' />";
		$p.print("movediv",l_s);
	},
	getNewParent:function(catid,catname){
		var l_error=false,l_step=0;
		if ($p.admin.widgets.currLevel==0 
            || $p.admin.widgets.currLevel>3){
                l_error=true;l_step="1 ("+$p.admin.widgets.currLevel+")";
        }
		for (var i=1;i<=$p.admin.widgets.currLevel;i++){
			if (catid==$p.admin.widgets.levCatid[i]){l_error=true;l_step=2;}
		}
		var l_id=$p.admin.widgets.catid;
		if (l_id==0){l_error=true;l_step=3;}
		if (l_error){
			alert(lg("msgIncorrectFolder")+" ! (err "+l_step+")");
			return false;
		} else {
			document.forms["f"].parentid.value=l_id;
			return true;
		}
	},
    /*
                    Function: $p.admin.widgets.mainDirModify
                    
                                    Load information about the main dir
                                    
                    Returns:
                    
                                    false
          */
	mainDirModify:function()
	{
        $p.admin.widgets.hideFrame(); 
        $('newmod').empty();
        //reset class variables
        $p.admin.widgets.catnames=[];
        $p.admin.widgets.catids=[];
        $p.admin.widgets.oldids=[];
        $p.admin.widgets.oldcatseqs=[];
        $p.admin.widgets.catlangs=[]; 

        $p.ajax.call(padmin["xml_rootdirectory_modify"],
            {
                'type':'load',
                'forceExecution':true,
                'callback':
                {
                    'function':$p.admin.widgets.displayMainDirModify
                }
            }
        );
        return false;
	},
    /*
                    Function: $p.admin.widgets.displayMainDirModify
                    
                                    Display the main directory informations
                                    
                    Parameters:
                    
                                    response - XML object
                                    vars(array) - 
          */    
    displayMainDirModify:function(response,vars)
    {  
        var i=0;
        var l_s='<form name="f" method="post">\
                '+lg("dirManagement")+' :<br /><br />\
                <div id="catdiv"></div>\
                <br /><input type="submit" value="'+lg("saveModifications")+'" onclick="$p.admin.widgets.setMainDirModify();return false;" />\
                </form>';
       
        $p.print("newmod","<h2>"+lg("lblModifyTheDir")+"</h2>"+l_s);

        while (response.getElementsByTagName("directory")[i])
        {
            l_result=response.getElementsByTagName("directory")[i];
            var l_catid=$p.ajax.getVal(l_result,"catid","int",false,0);
            var l_catoldid=$p.ajax.getVal(l_result,"catoldid","int",false,0);    
            var l_catname=$p.ajax.getVal(l_result,"catname","str",false,"");    
            var l_catseq=$p.ajax.getVal(l_result,"catseq","int",false,0);    
            var l_catlang=$p.ajax.getVal(l_result,"catlang","str",false,"");    
            $p.admin.widgets.catids.push(l_catid);
            $p.admin.widgets.oldids.push(l_catoldid);
            $p.admin.widgets.catnames.push(l_catname);
            $p.admin.widgets.oldcatseqs.push(l_catseq);
            $p.admin.widgets.catlangs.push(l_catlang);
            i++;
        } 
        $p.admin.widgets.showDirectory();     
    },
    /*
                    Function: $p.admin.widgets.setMainDirModify
                    
                                    Update the main directory information
          */      
    setMainDirModify:function()
    { 
       var paramList=$p.admin.widgets.getMainDirParameters(); 
       $p.ajax.call(padmin["scr_rootdirectory_modify"],
            {
                'type':'execute',
                'variables':paramList,
                'forceExecution':true,
                'callback':
                {
                    'function':$p.admin.widgets.setConfig
                }
            }
       );     
       return false;
    },
    setConfig:function()
    {
       $p.ajax.call(padmin["scr_rootdirectory_setconfig"],
            {
                'type':'execute',
                'forceExecution':true,
                'callback':
                {
                    'function':$p.admin.generateConfigFiles
                }
            }
       );
       return false;
    },    
    /*
                    Function: $p.admin.widgets.getMainDirParameters
                    
                                     Define a string with all parameters to modify mainDir
                                     
                    Returns
                    
                                    String of all parameters concatened
          */
    getMainDirParameters:function()
    {
       var catIdList="";
       var catOldIdList="";
       var catNameList="";
       var catOldSeqList="";
       var catSeqList="";
       var catLangList="";
       
       for (var i=0;i<$p.admin.widgets.catids.length;i++)   {
		   catIdList+="catid"+i+"="+document.forms["f"].elements['catid'+i].value+"&";
       } 
       for (var i=0;i<$p.admin.widgets.oldids.length;i++)   {
		   catOldIdList+="oldcatid"+i+"="+document.forms["f"].elements['oldcatid'+i].value+"&";
       }    
       for (var i=0;i<$p.admin.widgets.catnames.length;i++)   {
			catNameList+="catname"+i+"="+document.forms["f"].elements['catname'+i].value+"&";
       }
       for (var i=0;i<$p.admin.widgets.oldcatseqs.length;i++)   {
		   catOldSeqList+="oldcatseq"+i+"="+document.forms["f"].elements['oldcatseq'+i].value+"&";
       }
       for (var i=0;i<$p.admin.widgets.catids.length;i++)   {
           catSeqList+="catseq"+i+"="+document.forms["f"].elements['catseq'+i].value+"&";
       }        
       for (var i=0;i<$p.admin.widgets.catlangs.length;i++)   {
           catLangList+="catlg"+i+"="+document.forms["f"].elements['catlg'+i].value+"&";
       }
       catLangList=catLangList.substring(0,catLangList.length-1);      
       
       return catIdList+catOldIdList+catNameList+catOldSeqList+catSeqList+catLangList;
    },
    /*
                Function: $p.admin.widgets.getList
                
                 Parameters: 
                 
                    v_page - 
                    
                    v_type - 
        */
	getList:function(v_page,v_type)
	{
        $p.admin.users.selGroup=[];
		var l_link=(v_type==1)?padmin["xml_tovalidate"]:padmin["xml_allservices"];
		$p.admin.widgets.hideFrame();
		$p.admin.widgets.currLevel=0;
		//$p.admin.widgets.activateBtn((v_type==1)?2:0);
		getXml(l_link+"?p="+v_page,$p.admin.widgets.displayList,new Array(v_page,v_type));
		$p.admin.widgets.hideDirOptions();
		return false;
	},
	displayList:function(response,vars)
	{
		var l_s="",l_action="loadMod";
		l_s+=vars[1]==0?$p.admin.widgets.moduleBar():$p.admin.widgets.validateModuleBar();
		if (vars[1]==1) {l_action="loadValMod";}
		l_s+="<table cellpadding='0' cellspacing='0'>";
		if (response.getElementsByTagName("page")[0])
		{
			var l_id;
			l_s+="<tr><td><table style='width:760px'>";
			l_s+="<tr><td valign='top' class='dirdiva'><table cellpadding='0' cellspacing='0'><tr><td valign='top'><table cellpadding='0' cellspacing='1' style='width:233px'>";
			var i=0;
			while (response.getElementsByTagName("item")[i])
			{
				result=response.getElementsByTagName("item")[i];
				v_icon = $p.ajax.getVal(result,"icon","str",false,"--");
				l_id=$p.ajax.getVal(result,"id","int",false,0);
				if( v_icon!="--" ) {
					if( v_icon.substr(0,1)=="_" ) {
						v_icon = $p.img("../modules/pictures/"+v_icon+"?rand="+rand,16,16,indef,indef,l_id);
					} else {
						v_icon = $p.img(v_icon+"?rand="+rand,16,16,indef,indef,l_id);
					}
				} else {
						v_icon = $p.img("../modules/pictures/box0_"+l_id+"?rand="+rand,16,16,indef,indef,l_id);
				}
				if (vars[1]==0) l_s+="<tr><td width='20'>"+v_icon+"</td><td style='border-bottom:1px solid #c6c3c6'><a class='menul' href='#' onclick='$p.admin.widgets."+l_action+"("+l_id+")'"+(($p.ajax.getVal(result,"status","str",false,"")=='O')?"":" style='color:#c6c3c6;text-decoration: line-through;'")+">"+$p.ajax.getVal(result,"name","str",false,"--")+"</a></td></tr>";
				else l_s+="<tr><td width='20'>"+v_icon+"</td><td style='border-bottom:1px solid #c6c3c6;'><a class='menul' href='#' onclick='$p.admin.widgets."+l_action+"("+l_id+")'"+(($p.ajax.getVal(result,"status","str",false,"")=='O' || vars[1]==1)?"":" style='color:#c6c3c6;text-decoration: line-through;'")+">"+$p.ajax.getVal(result,"name","str",false,"--")+"</a></td></tr>";
				i++;
				if (i%7==0&&i<21) l_s+="</table></td><td valign='top'><table cellpadding='0' cellspacing='1' style='width:233px'>";
			}
			if (i==0) l_s+="<tr><td>"+lg("lblNoModule")+"</td></tr>";
			l_s+="</table></td></tr>";
			l_s+="<br /><table width='100%'><tr>";
			if (vars[0]>1){l_s+="<td><a href='#' onclick='$p.admin.widgets.getList("+(vars[0]-1)+","+vars[1]+");return false;'>"+$p.img("ico_previous2.gif",12,11,lg("lblPrevMods"))+" "+lg("lblPrevMods")+"</a></td>";}
			if (i==21){l_s+="<td align='right'><a href='#' onclick='$p.admin.widgets.getList("+(vars[0]+1)+","+vars[1]+");return false;'>"+lg("lblNextMods")+" "+$p.img("ico_next2.gif",12,11,lg("lblNextMods"))+"</a></td>";}
			l_s+="</tr></table>";
		}
		else
		{
			l_s+=lg("lblDisplayErr");
		}
		l_s+="</td></tr></table>";
		$p.show("listmod","block");
		$p.print("listmod",l_s);
	},
	getSearch:function(v_s,v_page)
	{
		// Open the search results
		v_s=$p.string.formatForSearch(v_s);
		$p.admin.widgets.hideFrame();
		$p.admin.widgets.currLevel=0;
		$p.admin.widgets.hideDirOptions();
		//$p.admin.widgets.activateBtn();
		$p.print("listmod","Recherche en cours ...");
		getXml(padmin["xmlsearchadmin"]+"?searchtxt="+$p.string.esc(v_s)+"&p="+v_page,$p.admin.widgets.displaySearch,new Array(v_s,v_page));
	},
	displaySearch:function(response,vars)
	{
		var l_s="",l_result;
		l_s+=$p.admin.widgets.moduleBar();
		l_s+="<table cellpadding='0' cellspacing='0'>";
		if (response.getElementsByTagName("nbres")[0])
		{
			var l_id;
			l_s+="<tr><td><table style='width:760px'>";
			l_s+="<tr><td valign='top' class='dirdiva'><table cellpadding='0' cellspacing='0'><tr><td valign='top'><table cellpadding='0' cellspacing='1' style='width:230px'>";
			var i=0;
			while (response.getElementsByTagName("item")[i])
			{
				l_result=response.getElementsByTagName("item")[i];
				var l_id=$p.ajax.getVal(l_result,"id","int",false,0);
				l_s+="<tr><td width='20'>"+$p.img("../modules/pictures/box0_"+l_id,16,16)+"</td><td style='border-bottom:1px solid #c6c3c6'> <a class='menul' href='#' onclick='$p.admin.widgets.loadMod("+l_id+")'>"+$p.ajax.getVal(l_result,"name","str",false,"...")+"</a></td></tr>";
				i++;
				if (i%7==0&&i<21) l_s+="</table></td><td valign='top'><table cellpadding='0' cellspacing='1' style='width:230px'>"
			}
			if (i==0) l_s+="<tr><td>"+lg("lblSrchNoMod")+"</td></tr>";
			l_s+="</table></td></tr>";
			l_s+="<br /><table width='100%'><tr>";
			if (vars[1]!=0){l_s+="<td><a href='#' onclick=\"$p.admin.widgets.getSearch('"+vars[0]+"',"+(parseInt(vars[1])-1)+");return false;\">"+$p.img("ico_previous2.gif",12,11,lg("lblPrevMods"))+" "+lg("lblPrevMods")+"</a></td>";}
			if (i==21){l_s+="<td align='right'><a href='#' onclick=\"$p.admin.widgets.getSearch('"+vars[0]+"',"+(parseInt(vars[1])+1)+");return false;\">"+lg("lblNextMods")+" "+$p.img("ico_next2.gif",12,11,lg("lblNextMods"))+"</a></td>";}
			l_s+="</tr></table>";
		}
		else
		{
			l_s+="<tr><td><table><tr><td style='color:#ff0000'>"+lg("lblSrch3car");
		}
		l_s+="</td></tr></table></td></tr></table>";
		$p.show("listmod","block");
		$p.print("listmod",l_s);
	},
	showDirOptions:function()
	{
		var l_s="";
		l_s+=lg("lblDirSelected")+" : <b><font color='#ff0000'>"+$p.admin.widgets.catname+"</font></b>";
		if ($p.admin.widgets.currLevel>1)
		{
			l_s+=" | <a href='#' onclick='return $p.admin.widgets.dirModify($p.admin.widgets.catid);'>"+lg("lblCategoryModify")+"</a>";
			l_s+=" | <a href='#' onclick='return $p.admin.widgets.dirSuppress($p.admin.widgets.catid);'>"+lg("lblCategorySuppress")+"</a>";
			l_s+=" | <a href='#' onclick='return $p.admin.widgets.dirMove($p.admin.widgets.catid);'>"+lg("lblCategoryMove")+"</a>";
		}
		if ($p.admin.widgets.currLevel<3) l_s+=" | <a href='#' onclick='return $p.admin.widgets.dirAdd($p.admin.widgets.catid);'>+ "+lg("lblCategoryAdd")+"</a>";
		$p.print("directory",l_s);
	},
	hideDirOptions:function()
	{
		$p.print("directory","");
	},
	hideFrame:function()
	{
		$p.app.pages.clean($p.get("newmod"));
	},
    /**
                Function: $p.admin.widgets.displayKeywords
                
                    display list of keywords linked to a widget
    
            **/
	displayKeywords:function()
	{
		var l_s="";

		if ($p.admin.widgets.keys.length>0)
		{
			l_s+="<table cellpadding='2' cellspacing='0' border='0'><tr><td>"+lg("lblKeyword")+"</td><td>"+lg("lblPriority")+"</td></tr>";
			for (var i=0;i<$p.admin.widgets.keys.length;i++)
			{
				l_s+="<tr><td><input type='text' name='kw"+i+"' value='"+$p.admin.widgets.keys[i].name+"' size='20' maxlength='30' /></td>";
				l_s+="<td><select name='w"+i+"'><option value='3'"+($p.admin.widgets.keys[i].priority==3?" selected='selected'":"")+">"+lg("high")+"</option><option value='2'"+($p.admin.widgets.keys[i].priority==2?" selected='selected'":"")+">"+lg("normal")+"</option><option value='1'"+($p.admin.widgets.keys[i].priority==1?" selected='selected'":"")+">"+lg("low")+"</option></select></td>";
				l_s+="<td><a href='#' onclick='$p.admin.widgets.suppKeyword("+i+")'>"+$p.img("ico_suppress.gif",13,10,lg("lblSuppress"))+"</a></td></tr>";
			}
			l_s+="</table>";
		}
		else
		{
			l_s+=lg("noKeyword");
		}
		l_s+="<br /><br />";
		l_s+="<b>+ "+lg("addKeyword")+"</b><br />";
		l_s+="<input type='text' name='kw' value='' size='20' maxlength='30' />";
		l_s+="<select name='weight'><option value='3'>"+lg("high")+"</option><option value='2' selected='selected'>"+lg("normal")+"</option><option value='1'>"+lg("low")+"</option></select>";
		l_s+="<br /><input type='button' value='"+lg("lblAdd")+"' onclick='$p.admin.widgets.addKeyword()' />";
		$p.print("keywordlist",l_s);
	},
	addKeyword:function()
	{
		l_form=document.forms["f"];
		$p.admin.widgets.keys[$p.admin.widgets.keys.length]=new $p.admin.widgets.keywordObj($p.admin.widgets.keys.length,l_form.weight.value,l_form.kw.value);
		$p.admin.widgets.displayKeywords();
	},
	suppKeyword:function(v_id)
	{
		$p.admin.widgets.keys.splice(v_id,1);
		$p.admin.widgets.displayKeywords();
	},
    /*
                    Function: $p.admin.widgets.security
                    
                    
            */
	security:function(v_tab,v_id)
	{
        var module_id=0;
        if (v_id) {
            module_id=v_id;
        }
        var xmlurl = posh["xmlgetwidget"]+"?getxml=1&pitem="+tab[v_tab].module[module_id].id;
		if (tab[v_tab].module[module_id].format=="I" || tab[v_tab].module[module_id].format=="U")    {
            $p.get('col1').innerHTML='<table width="100%" height="200px" border="1">'
                                +'<tr><td align="center" valign="middle">'+lg("moduleDisplaySecurity")+'<br />'
                                +'<a href="#" onclick="$p.admin.widgets.securityPassed('+v_tab+','+v_id+');return false;">'+lg("yes")+'</a><br />'
                                +'<a href="'+xmlurl+'" target="_blank" onclick="$p.app.popup.show($p.admin.widgets.setIframeXmlPreview('+tab[v_tab].module[module_id].id+'),800,indef,\''+lg("preview")+'\',true);return false;" >'+lg("checkSourceCode")+'</a>'
                                +'</td></tr></table>';
			
            $p.admin.widgets.isModuleShown=false;
		}
		else
		{
			$p.admin.widgets.securityPassed(v_tab,module_id);
		}
	},
    /*
                Function : $p.admin.widgets.securityPassed
                
                    Show widget to validate without check if widget is secure or not
            */
	securityPassed:function(v_tab,v_id)
	{
        var module_id=0;
        if (v_id) {
            module_id=v_id;
        }
		$p.get('col1').innerHTML='';
		tab[v_tab].module[module_id].create();
		tab[v_tab].module[module_id].show();
		$p.admin.widgets.isModuleShown=true;

	},
    /*
                Function: $p.admin.widgets.setIframeXmlPreview
                
                    display link to xmlcode in an iframe popup
            */
    setIframeXmlPreview: function (id) {
        var xmlurl = posh["xmlgetwidget"]+"?getxml=1&pitem="+id;
        return '<iframe src="'+xmlurl+'" width="95%" height="400"></iframe>';

    },
    /*
                Function: $p.admin.widgets.buildPage
                
                                Build the widgets page
         */
    buildPage:function()
    {
        $p.app.tabs.sel=1;
        $p.app.tabs.select(1);
        $p.admin.tools.emptyContent();
        $p.admin.widgets.loadManagementPage();
        if ($p.app.tabs.sel && $p.app.tabs.sel > 0) {$p.admin.setState("$p.app.tabs.open("+$p.app.tabs.sel+")");}
    },
    /*
                Function: $p.admin.widgets.loadManagementPage
                
                                Build the widgets management page   
         */
    loadManagementPage:function()
    {
		$p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':lg('modMgmt'),'link':'','fct':''}
			]
		));

        var l_s= '\
                 <div class="bottomhr">\
                 <span id="optmod"></span>\
                 </div>\
                 <div id="listmod" class="greydiv"></div>\
                 <div id="directory" class="greydivh"></div>\
                 <div class="tophr" id="newmod"></div>';

        $p.print($('content'),l_s);
        $p.admin.widgets.init();
    },
    /*
                Function: $p.admin.widgets.loadWidgetsInfos
                
                                Load widgets general informations (stats like number of available, awaiting ...)
         */    
    loadWidgetsInfos:function()
    {
          $p.ajax.call(padmin["xml_get_widgets_infos"],
            {
                'type':'load',
                'callback':
                {
                    'function':$p.admin.widgets.getWidgetsInfos
                }
           }  
        );
        return false;
    },
    /*
                Function: $p.admin.widgets.getWidgetsInfos
                
                                get widgets general informations
         */    
    getWidgetsInfos:function(response,vars)
    {
        var nbAvailable=$p.ajax.getVal(response,"availableWidgets","int",false,0);
        var nbAwaiting=$p.ajax.getVal(response,"awaitingWidgets","int",false,0);
        var activity=$p.ajax.getVal(response,"activity","int",false,0);
        $p.admin.key=$p.ajax.getVal(response,"key","str",false,"");
        $p.admin.md5key=$p.ajax.getVal(response,"md5key","str",false,"");
        $p.admin.widgets.widgetsInfos['nbAvailable']=nbAvailable;
        $p.admin.widgets.widgetsInfos['nbAwaiting']=nbAwaiting;
        $p.admin.activity=activity;
    },
	/*
		Function:$p.admin.widgets.showDir
		
	*/
	showDir:function()
	{
		var l_s="";
        //if there is no specified category
		if ($p.admin.widgets.dirid==0){
            l_s+=$p.admin.widgets.dirname+" [<a href='#' onclick='$p.admin.widgets.displayInitDir();return false;'>"+lg('msgNoCategoryForModule')+"</a>]";
        } 
        else if ($p.admin.widgets.dirid=='free'){
            $p.admin.widgets.initDirParent();
			l_s+="<font color='#ff0000'>"+lg("lblDirModChangeTxt")+" [<a href='#' onclick='return $p.admin.widgets.setDir();'>"+lg("lblClickHere")+"</a>]</font>";        
            dirid=0;
        }
        else {
			l_s+=$p.admin.widgets.dirname+" [<a href='#' onclick='$p.admin.widgets.editDir();return false;'>"+lg("lblModify")+"</a>]";
			document.forms["f"].catid.value=$p.admin.widgets.dirid;
		}
		$p.print("dirdiv",l_s);
	},
	/*
		Function: $p.admin.widgets.editDir
		
	*/
	editDir:function()
	{
        $p.admin.widgets.initDirParent();
		$p.admin.widgets.dirid='free';
		$p.admin.widgets.showDir();
		return false;
	},
	/*
		Function: $p.admin.widgets.displayInitDir
		
	*/
	displayInitDir:function(){
        var l_s="";
        l_s+="<font color='#ff0000'>"+lg("lblDirModChangeTxt")+" [<a href='#' onclick='return $p.admin.widgets.setDir();'>"+lg("lblClickHere")+"</a>]</font>";
        $p.print("dirdiv",l_s);
        $p.admin.widgets.initDirParent();
    },
	/*
		Function : $p.admin.widgets.setDir
		
	*/
	setDir:function(){
		$p.admin.widgets.dirid=$p.admin.widgets.catid;
		$p.admin.widgets.dirname=$p.admin.widgets.catname;
		$p.admin.widgets.showDir();
		return false;
	},
	/*
		Function: $p.admin.widgets.applyWidth
		
	*/
	applyWidth:function(v_width){
        if (!$p.admin.widgets.isModuleShown) return false;
    	var l_width=v_width==indef?parseInt(document.forms["f"].minwidth.value,10):v_width;
    	document.getElementById('tbl1').style.width=l_width+"px";
    	return false;
    },
	/*
		Function: $p.admin.widgets.applyHeight
		
	*/
    applyHeight:function(v_height){
    	if (!$p.admin.widgets.isModuleShown) return false;
    	var l_height=(v_height==indef)?parseInt(document.forms["f"].size.value,10):v_height;
    	//tab[1].module[0].setHeight(l_height,"bmodmodulestab");
		//tab[1].module[0].setHeight(l_height,"FeedDisplayInfo");
        document.getElementById('tbl1').style.height=l_height+"px";
    	return false;
    },
	/*
		Function: $p.admin.widgets.groupObj
		
	*/
    groupObj:function(id,name){
    	this.id=id;
    	this.name=name;
    },
	/*
		Function: $p.admin.widgets.showDirectory
	
	*/
	showDirectory:function(){
		var l_s="<table>",i=0;
		for (var i=0;i<$p.admin.widgets.catids.length;i++){
			l_s+='<tr><td>Rubrique '+(i+1)+' <input type=text name="catname'+i+'" value="'+$p.admin.widgets.catnames[i]+'" size=40 maxlength=60 /> <input type=hidden name="catseq'+i+'" value="'+i+'" /><input type=hidden name="oldcatseq'+i+'" value="'+$p.admin.widgets.oldcatseqs[i]+'" /><INPUT type=hidden name="oldcatid'+i+'" value="'+$p.admin.widgets.oldids[i]+'" /><INPUT type=hidden name="catid'+i+'" value="'+$p.admin.widgets.catids[i]+'" /><INPUT type=text name="catlg'+i+'" value="'+$p.admin.widgets.catlangs[i]+'" size=2 maxlength=2 /> ';
			if (i>0) l_s+=' <a href=# onclick="$p.admin.widgets.moveDirectory('+i+');return false;" title="'+lg("lblRaiseDir")+'"><img src="../images/ico_up_arrow2.gif" /></a>';
			l_s+='</td><td> <a href=# onclick="$p.admin.widgets.supDirectory('+i+');return false;"><img src="../images/ico_suppress.gif" /> '+lg("lblSuppress")+'</a></td></tr>';
		}
		l_s+="</table>";
		l_s+="<br />"+lg("lblAddDir")+" <input type=text name='newcat' value='' size=20 maxlenth=20 /> "+lg("lblLang")+" <input type=text name='catlg' value='' size=2 maxlength=2 /> <input type=button onclick='$p.admin.widgets.addDirectory();return false;' value='"+lg("lblAdd")+"' />";
		$p.print("catdiv",l_s);
	},
	/*
		Function: $p.admin.widgets.moveDirectory
	
	*/
	moveDirectory:function(v_id){
		var l_catname=$p.admin.widgets.catnames[v_id];
		var l_catid=$p.admin.widgets.catids[v_id];
		var l_oldcatid=$p.admin.widgets.oldids[v_id];
		var l_oldcatseq=$p.admin.widgets.oldcatseqs[v_id];
		var l_catlang=$p.admin.widgets.catlangs[v_id];
		$p.admin.widgets.catnames[v_id]=$p.admin.widgets.catnames[(v_id-1)];
		$p.admin.widgets.catids[v_id]=$p.admin.widgets.catids[(v_id-1)];
		$p.admin.widgets.oldids[v_id]=$p.admin.widgets.oldids[(v_id-1)];
		$p.admin.widgets.oldcatseqs[v_id]=$p.admin.widgets.oldcatseqs[(v_id-1)];
		$p.admin.widgets.catlangs[v_id]=$p.admin.widgets.catlangs[(v_id-1)];
		$p.admin.widgets.catnames[(v_id-1)]=l_catname;
		$p.admin.widgets.catids[(v_id-1)]=l_catid;
		$p.admin.widgets.oldids[(v_id-1)]=l_oldcatid;
		$p.admin.widgets.oldcatseqs[(v_id-1)]=l_oldcatseq;
		$p.admin.widgets.catlangs[(v_id-1)]=l_catlang;
		$p.admin.widgets.showDirectory();
		return false;
	},
	/*
		Function: $p.admin.widgets.addDirectory
	
	*/
	addDirectory:function(){
		var l_catname=document.forms["f"].newcat.value;
		var l_catlang=document.forms["f"].catlg.value;
		$p.admin.widgets.catnames.push(l_catname);
		$p.admin.widgets.catids.push(0);
		$p.admin.widgets.oldids.push(0);
		$p.admin.widgets.oldcatseqs.push(0);
		$p.admin.widgets.catlangs.push(l_catlang);
		$p.admin.widgets.showDirectory();
		return false;
	},
	/*
		Function: $p.admin.widgets.supDirectory
	
	*/
	supDirectory:function(v_id){
		if ($p.admin.widgets.catids[v_id]==0){
			$p.admin.widgets.catnames.splice(v_id,1);
			$p.admin.widgets.catids.splice(v_id,1);
			$p.admin.widgets.oldids.splice(v_id,1);
			$p.admin.widgets.oldcatseqs.splice(v_id,1);
			$p.admin.widgets.catlangs.splice(v_id,1);
			$p.admin.widgets.showDirectory();
			return false;
		} else {
			l_response=confirm(lg("msgDirSupWarn1"));
			if (l_response==1){
				l_response=confirm(lg("msgDirSupWarn2"));
				if (l_response==1){
                    $p.ajax.call(padmin["scr_rootdirectory_suppress"],
                        {
                            'type':'execute',
                            'variables':"id="+$p.admin.widgets.oldids[v_id]+"&seq="+$p.admin.widgets.oldcatseqs[v_id],
                            'forceExecution':true,
                            'callback':
                            {
                                'function':$p.admin.widgets.setConfig
                            }
                        }
                    );     
               }
			}
			return false;
		}
	},
	/*
		Function: $p.admin.widgets.showModuleDir
	
	*/
	showModuleDir:function(){
		var l_s="";
		if ($p.admin.widgets.dirid==0){
			l_s+=" /";
		} else if ($p.admin.widgets.dirid==-1){
			$p.admin.widgets.initDir(__dimension[0]["id"],__dimension[0]["name"]);
			l_s+="<strong class='important'>"+lg("lblDirModChangeTxt")+" [<a href='#' onclick='$p.admin.widgets.setModuleDir();return false;'>"+lg("lblClickHere")+"<\/a>]<\/strong>";
		} else {
			l_s+=$p.admin.widgets.dirname+" [<a href='#' onclick='$p.admin.widgets.editModuleDir();return false;'>"+lg("lblModify")+"<\/a>]";
			document.forms["f"].catid.value=$p.admin.widgets.dirid;
		}
		$p.print("dirdiv",l_s);
	},
	/*
		Function: $p.admin.widgets.setModuleDir
	
	*/
	setModuleDir:function(){
		$p.admin.widgets.dirid=$p.admin.widgets.catid;
		$p.admin.widgets.dirname=$p.admin.widgets.catname;
		$p.admin.widgets.showModuleDir();
		return false;
	},
	/*
		Function: $p.admin.widgets.editModuleDir
	
	*/
	editModuleDir:function(){
		$p.admin.widgets.dirid=-1;
		$p.admin.widgets.showModuleDir();
		return false;
	},
	/*
		Function: $p.admin.widgets.giveFocus
	
	*/
    giveFocus:function(){
    	document.forms['f'].submitForm.disabled=false;
    },
	/*
		Function: $p.admin.widgets.refreshIcons
	
	*/
    refreshIcons:function(v_id,id,v_icon){
        var box0="#ff0000";
        (v_id.substr(0,1)!="_")?box0="#ff0000":box0="transparent";
		v_icon = (v_id.substr(0,1)!="_")?v_id:v_icon;
    	var l_s='<a href="#" onclick="$p.admin.widgets.refreshIcons(\''+v_icon+'\','+id+',\''+v_icon+'\');return false;">\
                <img id=_'+id+' src="'+v_icon+'" width="16" height="16" style="border: 2px solid '+box0+'" /></a>';
				
    	for (var i=0;i<__nbicons;i++)
    	{
            var deficon="";
            (v_id=='_deficon'+i+'.gif')?deficon='#ff0000':deficon='transparent';
    		l_s+='<a href="#" onclick="$p.admin.widgets.refreshIcons(\'_deficon'+i+'.gif\','+id+',\''+v_icon+'\');return false;">\
                 <img src="../modules/pictures/_deficon'+i+'.gif" style="border: 2px solid '+deficon+' "></a>';
    	}
        
    	$p.print('iconlist',l_s);
    	document.forms['f'].icon.value=v_id;
    },
	/*
                    Function: $p.admin.widgets.updateIcons
                    
          */
	updateIcons:function(item_id, icon){
        if($(item_id) && icon.substr(0,1)=="_" ){
            $(item_id).src="../modules/pictures/"+icon+"?rand=23";
            $("_"+item_id).src="../modules/pictures/"+icon;
        }    
	}
}

//************************************* WELCOME  FUNCTIONS ***************************************************************************************************************
/*
    Class: $p.admin.welcome
         welcome index page functions
*/
$p.admin.welcome={
    tabHTML:{},
    /** see below in buildPage function**/
    dejavu:null, 
    /*
                Function: $p.admin.welcome.buildPage
                                
                                Build the index of administration
         */ 
    buildPage:function()
    {  
        $p.app.tabs.sel=0;
        $p.app.tabs.select(0);
        //dejavu (english idomatic ;)  ) created because this function is called before all tabs,, to build resume of admin
        //but this preventys to set cookie for come to last seen page 
        //solution: first access, don't modify cookie, then modify everything
        if ($p.admin.welcome.dejavu) {
            $p.admin.setState();
        }
        $p.admin.welcome.dejavu=1;
        
      //  if ($p.app.tabs.sel==0) {$p.admin.setState("$p.app.tabs.open("+$p.app.tabs.sel+")");}
        $p.admin.tools.emptyContent();
        $p.admin.widgets.loadWidgetsInfos();
        //get available tabs
        for (var i in $p.admin.tabs.tabAccess) {
           $p.admin.welcome.tabHTML[i] = $p.admin.tabs.tabAccess[i];
        }       
        //display user available tabs
        for (var j in $p.admin.welcome.tabHTML) {
           $("content").innerHTML+=eval($p.admin.welcome.tabHTML[j]);
        }    
        //extra content to display
        $("content").innerHTML+=$p.admin.welcome.displayCacheRefresh(); 
    },
    /*
                Function: $p.admin.welcome._buildGeneral
                                
                                Build the general section of the index
                
                Returns:
                    
                                HTML code
         */         
    _buildGeneral:function()
    {
       var msg = "";
       if (__NOTIFICATIONEMAIL == "")   { 
           msg="<div id='alert' class='warning'>"+lg("notificationMailWarning")+"</div>"; 
       }
       var tableContent = $p.admin.welcome.getGeneralTableContent(); 
       var completeTable=$p.admin.buildTableContent(tableContent);
	   $p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			 [{'label':lg('Accueil'),'link':'','fct':''}]
	   ));
       var l_s = msg+'\
                <div id="admin0">\
                    <div class="box">\
                        '+completeTable+'\
                    </div>\
               </div>';

       return l_s;
    },
    /*
                Function: $p.admin.welcome._buildApplication
                                
                                Build the application section of the index
                
                Returns:
                    
                                HTML code
         */   
    _buildApplication:function()
    {
       var title = '<h2><img src="../images/ico_adm_config.gif" />&nbsp;'+lg("yourApplication")+'</h2>';
       var tableContent=$p.admin.welcome.getApplicationTableContent();
       var completeTable=$p.admin.buildTableContent(tableContent);
       var l_s='<div id="admin1">\
                    <div class="box">\
                        '+title+'\
                        '+completeTable+'\
                    </div>\
               </div>';
               
        return l_s;  
    },
    /*
                Function: $p.admin.welcome._buildWidgets
                                
                                Build the widget section of the index
                
                Returns:
                    
                                HTML code
         */  
    _buildWidgets:function()
    {
       var title = '<h2><img src="../images/ico_adm_module.gif" />&nbsp;'+lg("modules")+'</h2>';       
       var tableContent=$p.admin.welcome.getWidgetsTableContent(); 
       var completeTable=$p.admin.buildTableContent(tableContent);
       
       var l_s='<div id="admin2">\
                    <div class="box">\
                        '+title+'\
                        '+completeTable+'\
                    </div>\
               </div>';  
      
       return l_s;
    },
    /*
                Function: $p.admin.welcome._buildUsers
                                
                                Build the users section of the index
                
                Returns:
                    
                                HTML code
         */  
    _buildUsers:function()
    {
       var title = '<h2><img src="../images/ico_adm_users.gif"/>&nbsp;'+lg("userss")+'</h2>';       
       var tableContent=$p.admin.welcome.getUsersTableContent(); 
       var completeTable=$p.admin.buildTableContent(tableContent);
       
       var l_s='<div id="admin3">\
                    <div class="box">\
                        '+title+'\
                        '+completeTable+'\
                    </div>\
               </div>';  
      
       return l_s;
    },
    /*
                Function: $p.admin.welcome._buildPages
                                
                                Build the pages section of the index
                
                Returns:
                    
                                HTML code
         */  
    _buildPages:function()
    {
       var title = '<h2><img src="../images/ico_adm_page.gif" />&nbsp;'+lg("Pages")+'</h2>';
       var tableContent=$p.admin.welcome.getPagesTableContent(); 
       var completeTable=$p.admin.buildTableContent(tableContent);
       
       var l_s='<div id="admin4">\
                    <div class="box">\
                        '+title+'\
                        '+completeTable+'\
                    </div>\
               </div>';  
      
       return l_s;
    },    
    /*
                Function: $p.admin.welcome.getPagesTableContent
                
                                Return an array of all the entries of the pages setion
                
                Returns
                    
                                Array
         */ 
    getPagesTableContent:function()
    {    
        var pagescontent= {
                                "tabused": {
                                            'label':lg("tabused"),
                                            'fct':$p.admin.pages.pagesInfos['pagesList']+' [<a href="#" onclick="$p.admin.pages.buildPage();return false;" >'+lg("tabMgmt")+'</a>]'
                                           }
                          };
                             
    /*    if (__useSharing) {
            pagescontent["usersPortal"]= {'label':lg("usersPortal"),'fct':$p.admin.pages.pagesInfos['availablePortals']};
            pagescontent["usersPortalVal"]= {'label':lg("usersPortalVal"),'fct':$p.admin.pages.pagesInfos['awaitingPortals']+' [<a href="#" onclick=$p.admin.pages.buildPage();return false;>'+lg("exampleMgmt")+'<\/a>]'};
        }
             */
        return pagescontent;
    },    
    /*
                Function: $p.admin.welcome.getWidgetsTableContent
                
                                Return an array of all the entries of the widget setion
                
                Returns
                    
                                Array
         */ 
    getWidgetsTableContent:function()
    {    
        var url_rss_widget = __LOCALFOLDER+'admin/'+padmin["rss_modulestovalidate"]+'?k='+$p.admin.key;
        var widcontent= {
                            "modActivity": {'label':lg("followModulesActivity"),'fct':'<a style="text-decoration:none;" target="_blank" href="'+url_rss_widget+'">'+url_rss_widget+'</a>'},
                            "modAvailable": {'label':lg("modAvailable"),'fct':'<span class="modules_nb">'+$p.admin.widgets.widgetsInfos['nbAvailable']+'</span>'},
                            "modWaitingVal": {'label':lg("modWaitingVal"),'fct':'<span class="modules_nb">'+$p.admin.widgets.widgetsInfos['nbAwaiting']+'</span> [<a href="#" onclick="$p.admin.widgets.buildPage();">'+lg("modMgmt")+'</a>]'}
                        };
                        
        return widcontent;
    }, 
    /*
                Function: $p.admin.welcome.getUsersTableContent
                
                                Return an array of all the entries of the widget setion
                
                Returns
                    
                                Array
         */ 
    getUsersTableContent:function()
    {    
        var usrcontent= {
                           "usrAvailable": {'label':lg("userss"),'fct':$p.admin.users.usersInfos['nbUsers']+' [<a href="#" onclick="$p.admin.users.buildPage();return false;" >'+lg("userMgmt")+'</a>]'}
                        };
                        
        return usrcontent;
    },
    /*
                Function: $p.admin.welcome.getGeneralTableContent
                
                                Return an array of all the entries of the general setion
                
                Returns
                    
                                Array
         */ 
    getGeneralTableContent:function()
    {
        var activity_flux_url=lg("noNews");
        var completeActivityLink="<span style='text-decoration:none;'>"+activity_flux_url+"</span>";
        
        if ($p.admin.activity==1) {
             activity_flux_url = __LOCALFOLDER+"cache/rssadmin"+$p.admin.md5key+".xml";
             completeActivityLink='<a style="text-decoration:none;" href="'+activity_flux_url+'" target="_blank">\
                                   <img src="../images/ico_rss.gif"/>&nbsp;'+activity_flux_url+'</a>';
        }
        
        var gencontent= {
                                "application": {'label':lg("application"),'fct':__application},
                                "version": {'label':lg("version"),'fct':p_version},
                                "activity": {'label':lg("followPoshActivity"),'fct':completeActivityLink},
                                "adminLanguage": {'label':lg("adminLanguage"),'fct':$p.admin.welcome.displayLangForm()}
                        };
                        
        return gencontent;
    },
    /*
                Function: $p.admin.welcome.displayCacheRefresh
                
                                Display the cache refresh button
                
                Returns
                    
                                HTML code
         */ 
    displayCacheRefresh:function()
    {
        var l_s='<div class="submit">\
                    '+lg("cacheRefreshTxt")+'\
                    <input type="button" style="width:200px" value='+lg("cacheRefresh")+' class="btn" onclick="$p.admin.cacheGenerateAll();return false;" />\
                 </div>';
                   
        return l_s;
    },   
    /*
                Function: $p.admin.welcome.getApplicationTableContent
                
                                Return an array of all the entries of the application section
                
                Returns
                    
                                Hash
         */ 
    getApplicationTableContent:function()
    {
        var appcontent={
                            "appname": {'label':lg("appName")+':',
                                        'fct':'<strong>'+__APPNAME+'</strong> [<a href="#" onclick="$p.admin.config.generalsettings();return false;" >'+lg("modify")+'</a>]'},
                            "theme": {'label':lg("themeUsed"),
                                      'fct':'<strong>'+__theme+'</strong> [<a href="#" onclick="$p.admin.config.themes();return false;" >'+lg("modify")+'</a>]'}
                       };
                       
        return appcontent;
    },
    /*
                Function: $p.admin.welcome.changeLang
                
                                Change the user language in the database
         */   
    changeLang:function(language)
    {
        $p.ajax.call(padmin["scr_admin_lang"],
            {
                'type':'execute',
                'variables':"lang="+language,
                'forceExecution':true,
                'callback':
                {
                    'function':$p.admin.tools.redirection(padmin["index"]+'?lang='+language)
				}
            }  
        );   
    },
    /*
                Function: $p.admin.welcome.displayLangForm
         */   
    displayLangForm:function()
    {
       var selected="";
       var l_s="<form>\
                <select name='lang' onchange='$p.admin.welcome.changeLang(this.value);'>";
       
       for (var i=0;i<__AVLANGS.length;i++)
       {
            (__lang==__AVLANGS[i])?selected='selected=selected':selected='';
            l_s+="<option value='"+__AVLANGS[i]+"' "+selected+" >"+__AVLANGS[i]+"</option>";        				
       }
       
       l_s+="</select>\
             </form>";   
       
       return l_s;
    }
}

$p.admin.support={
    page:0,
    tabname:'',
    
    buildPage:function()
    {
        $p.admin.support.page=0;
        $p.admin.tools.emptyContent();
        $p.app.tabs.sel=8;
        $p.app.tabs.select(8);
        $p.admin.support.tabname='supporttab';
      
        $p.admin.fillBreadCrumbs($p.html.breadCrumbs(
			[
				{'label':lg('Accueil'),'link':'','fct':'$p.admin.setState();$p.app.tabs.open(0);return false;'},
				{'label':lg('supportMgmt'),'link':'','fct':''}
			]
		));	
        if ($p.app.tabs.sel && $p.app.tabs.sel > 0  ) {$p.admin.setState("$p.app.tabs.open("+$p.app.tabs.sel+")");}
        $p.admin.support.loadSupportMain();
    },
    loadSupportMain:function()
    {
        $p.ajax.call(padmin["xml_getsupport"]+"?page="+$p.admin.support.page,
            {
                'type':'load',
                'callback':
                {
                    'function':$p.admin.support.displaySupportMain
                }
           }  
        );
        return false;
    },  
    deleteAllLogs:function()
    {
		if (confirm(lg("msgLogSup")))  {
            $p.ajax.call(padmin["scr_support_delete"],
                {
                    'type':'execute',
                    'callback':
                    {
                        'function':$p.admin.support.loadSupportMain
                    }      
                }	
            );
        }
    },
    deleteOldLogs:function()
    {
		if (confirm(lg("msgLogSup")))  {
            $p.ajax.call(padmin["scr_support_delete"],
                {
                    'type':'execute',
                    'variables':"delay=1",
                    'callback':
                    {
                        'function':$p.admin.support.loadSupportMain
                    }      
                }	
            );
        }
    },
    displaySupportMain:function(response,vars)
    {
        var l_s="";
        var i=0;
        var total=$p.ajax.getVal(response,"total","int",false,0);

        l_s='<div class="subtitle">'+lg("log")+'</div><br />\
            <div style="width:98%;height:300px;overflow:auto;">\
            <div id="deleteAll"><a href="#" onclick=$p.admin.support.deleteAllLogs();>'+lg("deleteAllLogs")+'</a></div>\
            <div id="deleteOld"><a href="#" onclick=$p.admin.support.deleteOldLogs();>'+lg("deleteOldLogs")+'</a></div>\
            <table width="98%" border="1">\
            	<tr align="center">\
                    <td>ID</td>\
                    <td>log</td>\
                    <td>Date</td>\
                </tr>';
    
        while (response.getElementsByTagName("logs")[i])
        {
            result=response.getElementsByTagName("logs")[i];
            var id=$p.ajax.getVal(result,"id","int",false,0);
            var log=$p.ajax.getVal(result,"log","str",false,"");
            var logdate=$p.ajax.getVal(result,"logdate","str",false,""); 
            l_s+='<tr><td>'+id+'</td><td>'+log+'</td><td>'+logdate+'</td></tr>';
            i++;
        }
        
        if ($p.admin.support.page>0){
            l_s+='<img src="../images/ico_previous2.gif" align="absmiddle" />\
                <a href="#" onclick=$p.admin.support.page--;$p.admin.support.loadSupportMain();return false;>'+lg("previous")+'</a> &nbsp; ';
        }
        if (total==50) {
            l_s+='<a href="#" onclick=$p.admin.support.page++;$p.admin.support.loadSupportMain();return false;>'+lg("next")+'</a> &nbsp;\
                <img src="../images/ico_next2.gif" align="absmiddle" />';  
        }
        
        l_s+='</table></div><br />';
        
        $p.print("content",l_s);
    }
}


//************************************* TOOLS  FUNCTIONS ***************************************************************************************************************
/*
    Class: $p.admin.tools
         tools functions
*/
$p.admin.tools={
    /*
                    Function: $p.admin.tools.emptyContent
                    
                                     Empty the content div
           */
    emptyContent:function()
    {
        $p.print($("content"),"");
    },
    /*
                    Function: $p.admin.tools.emptyContentLang
                    
                                     Empty the content div
           */
    emptyContentLang:function()
    {
        $p.print($("contentBox"),"");
    },
    /*
                    Function: $p.admin.tools.emptyAll
                    
                                     Empty all div from the document
           */
    emptyAll:function()
    {
        var divList = document.getElementsByTagName('div');
        for (var i=0;i<divList.length;i++) { $(divList[i]).empty(); }
    },
    /*
                    Function: $p.admin.tools.redirection
                    
                                     Redirect the current document location 
                                    
                    Parameters:
                                
                                    url - url to redirect
           */
    redirection:function(url)
    {  
        document.location=url;
    },
    checkEmail:function(v_email)
    {
    	var l_ret=true;
    	var l_reg = /^[a-z0-9._-]+@[a-z0-9.-]{2,}[.][a-z]{2,3}$/;
    	if (l_reg.exec(v_email)==null){l_ret=false;}
    	return l_ret;
    }
}