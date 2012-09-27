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
// Tutorial specific javascript functions 
//
// !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
// ***************************************

var cols=[];
var module=[];
$p.app.env="tutorial";
$p.app.tabs.sel=999;


/*
 *
 *  Class: $p.tutorial 
 *
 *              functions to manage tutorial process  
 *
 *  file: tutorial.js
 *
 */

$p.tutorial={
    tabname:'index',
    widgetParameters:{},
    widgetUserPrefs:{},
    widgetUserPrefsString:'',
    breadCrumbs:{},
    currentStep:'',
    xmlTemplate:{},
    widgetCategoryPath:{},
    /*
		Function: $p.tutorial.emptyContent
                                       empty the content div
	*/
    emptyContent:function()
    {
        $("content").innerHTML="";
    },
    /*
		Function: $p.tutorial.initWidgetParameters
        
                                       Initialise the default parameters for a widget
	*/
    initWidgetParameters:function()
    {
        var time = new Date().getTime();
        $p.tutorial.widgetUserPrefsString='';
        if (!$p.tutorial.widgetParameters["id"] ) {
            $p.tutorial.widgetParameters["id"]=time+""+$p.app.user.id;
            $p.tutorial.widgetParameters["validate"]="";
        }
        $p.tutorial.widgetParameters["url"]="";
        $p.tutorial.widgetParameters["rssurl"]=0;
        $p.tutorial.widgetParameters["pfid"]="";
        $p.tutorial.widgetParameters["defvar"]="";
        $p.tutorial.widgetParameters["name"]="Your widget name";
        $p.tutorial.widgetParameters["description"]=lg('widgetDefaultDescTxt');
        $p.tutorial.widgetParameters["category_id"]=$p.tutorial.getLangCatIdFromDimension();
        $p.tutorial.widgetParameters["status"]="C";
        $p.tutorial.widgetParameters["type"]="html";
        $p.tutorial.widgetParameters["format"]="I";
        $p.tutorial.widgetParameters["height"]="500";
        $p.tutorial.widgetParameters["minwidth"]="280";
        $p.tutorial.widgetParameters["sizable"]="1";
        $p.tutorial.widgetParameters["website"]="";
        $p.tutorial.widgetParameters["editor_id"]=$p.app.user.id;
        $p.tutorial.widgetParameters["nbvariables"]=0;
        $p.tutorial.widgetParameters["updated"]="Y";
        $p.tutorial.widgetParameters["sorting"]="0";
        $p.tutorial.widgetParameters["lang"]=__lang;
        $p.tutorial.widgetParameters["usereader"]="0";
        $p.tutorial.widgetParameters["autorefresh"]="0";
        $p.tutorial.widgetParameters["icon"]="_deficon10.gif";
		$p.tutorial.widgetParameters["l10nSelected"]="";
		$p.tutorial.widgetParameters["l10n"]="";
		$p.tutorial.locales.hashlangs = new Object();
        $p.tutorial.xmlTemplate['header'] = $p.tutorial.getXmlTemplateHeader();
        $p.tutorial.xmlTemplate['footer'] = "]]>\r\n</Content>\r\n</Module>\r\n";
        /**
                        Don't touch text YOUR HTML
                        text is reference to build  widget from netvibes widget
                    **/
        $p.tutorial.xmlTemplate['content'] = 'YOUR HTML'+"\r\n"+'<script type="text/javascript">'+"\r\n"+'</script>'+"\r\n";
        $p.tutorial.xmlTemplate['dtd'] ='<?xml version="1.0" encoding="UTF-8" ?>'+"\r\n";        
        
		$p.tutorial.widgetParameters["code"]=$p.tutorial.xmlTemplate['dtd']
                                            +$p.tutorial.xmlTemplate['header']
                                            +$p.tutorial.xmlTemplate['content']
                                            +$p.tutorial.xmlTemplate['footer'];
                                            
        $p.tutorial.widgetCategoryPath={};
        $p.tutorial.widgetParameters["auth"]="";
        $p.tutorial.widgetParameters["keywords"]="";	

    },
    getXmlTemplateHeader: function () {
        return '<Module>'+"\r\n"
                +' <ModulePrefs title="'
                    +$p.tutorial.widgetParameters["name"]+'" description="'
                    +$p.tutorial.widgetParameters["description"]+'" height="'
                    +$p.tutorial.widgetParameters["height"]+'" />'+"\r\n"
                + $p.tutorial.widgetUserPrefsString
                +' <Content type="html" view="home"><![CDATA['+"\r\n";
    },
    buildXMLuserPref: function () {
        
    },
    getLangCatIdFromDimension:function()
    {
		for (var i=0;i<__dimension.length;i++)
		{
			if (__dimension[i]["lg"]==__lang) {
                return __dimension[i]["id"];
            }
        }
    },
    /*
		Function: $p.tutorial.initBreadCrumbs
                                       Initialise the breadCrumbs
	*/
    initBreadCrumbs:function()
    {
        $p.tutorial.breadCrumbs['choisir']="document.location.href='#headlink';$p.tutorial.displayIndex();";
        if ( !$p.tutorial.breadCrumbs['creer'] ) {
            $p.tutorial.breadCrumbs['creer']="document.location.href='#headlink';$p.tutorial.expert.displayExpertMode();";
        }
        $p.tutorial.breadCrumbs['configurer']="$p.tutorial.displayConfiguration();";
        $p.tutorial.breadCrumbs['placer']="document.location.href='#headlink';$p.tutorial.displayLinkToCategory();";
        $p.tutorial.breadCrumbs['picto']="document.location.href='#headlink';$p.tutorial.expert.displayPictoChoice();";
        $p.tutorial.breadCrumbs['visualiser']="document.location.href='#headlink';$p.tutorial.widgetPreview();";
        $p.tutorial.breadCrumbs['valider']="document.location.href='#headlink';$p.tutorial.save.initSavePage();";
    },
    /*
		Function: $p.tutorial.buildBreadCrumbs
                                       Display the breadCrumbs
                                
                     Parameters:
                     
                                excludeLinks - array of the elements to display as unclickable link
                                enabledLinks - array of the elements to display as clickable link
	*/
    buildBreadCrumbs:function(excludeLinks,enabledLinks,links2disable,links2enable)
    {
		var actionFct='';
		var stepArrow=' &gt; ';
		var l_s='';
		var i=0;
		var steps = new Array();
        steps['choisir'] = '1.'+lg('tutorialStepChoice');
        steps['creer'] = '2.'+lg('tutorialStepCreate');
        steps['configurer'] = '3.'+lg('tutorialConf');
        steps['placer'] = '4.'+lg('tutorialStepCategory');
		steps['picto'] = '5.'+lg('tutorialStepPicto');;
		steps['visualiser'] = '6.'+lg('tutorialStepTest');
        steps['valider'] = '7.'+lg('tutorialStepSave');
        
        var stylelink = '';
        
        var l_type = $p.tutorial.widgetParameters["type"];
        
        if ($p.tutorial.currentStep != 'choisir' && $p.tutorial.menus.menusProcess[l_type])
        {
            l_s += '<div class="dotted">\
                        <div id="ico">\
                        <img src="'+$p.tutorial.menus.menusProcess[l_type]["icon"]+'" />\
                        </div>\
                        <div id="label">\
                        <h2>'+lg($p.tutorial.menus.menusProcess[l_type]["labelTxt"])+'</h2>\
                        '+lg($p.tutorial.menus.menusProcess[l_type]["labelDesc"])+'\
                        </div>\
                        <div class="float_correction" />\
                    </div>';
        }
        
        for (var w in $p.tutorial.breadCrumbs)
        {      
			actionFct='';
			if(i==6) 
            {
				stepArrow='';
            }
           
            if( !((typeof(enabledLinks)!="undefined") && !enabledLinks.contains(w)) ) {
                actionFct='onclick="eval($p.tutorial.breadCrumbs[\''+w+'\']);return false;"';
            }
            if ( !((typeof(excludeLinks)!="undefined") && (excludeLinks.contains(w))) ) {
                if (w==$p.tutorial.currentStep) {
                    l_s+='<span id="'+w+'">'+steps[w]+'</span> &nbsp; '+stepArrow;
                }
                else {
                    l_s+='<span id="'+w+'" '
                                    +'><a class="tutlink" '+ stylelink +' '+actionFct+' onmouseover=$p.tutorial.switchColor("'+w+'","#E9EDF2"); onmouseout=$p.tutorial.switchColor("'+w+'",""); href="#">'
                                    +steps[w]+'</a></span> &nbsp; '
                                    +stepArrow
                                    ;
                }    
            }
            else {  
                l_s+='<span id="'+w+'" class="tutstrong">'+steps[w]+'</span> &nbsp; '+stepArrow; 
            }


			i++;
        }
		
		switch($p.tutorial.currentStep){
	        case 'choisir'		:	$p.print("pagetitle",$p.html.buildTitle(lg('tutorialStepChoiceTitle')));break;
	        case 'creer'		:	$p.print("pagetitle",$p.html.buildTitle(lg('tutorialStepCreateTitle'))); break;
	        case 'configurer'	:	$p.print("pagetitle",$p.html.buildTitle(lg('tutorialStepConfTitle'))); break;
	        case 'visualiser'	:	$p.print("pagetitle",$p.html.buildTitle(lg('tutorialStepTestTitle'))); break;
	        case 'placer'		:	$p.print("pagetitle",$p.html.buildTitle(lg('tutorialStepCategoryTitle'))); break;
			case 'picto'		:	$p.print("pagetitle",$p.html.buildTitle(lg('tutorialStepPictoTitle'))); break;
	        case 'valider'		:	$p.print("pagetitle",$p.html.buildTitle(lg('tutorialStepSaveTitle'))); break;
			default 			:	$p.print("pagetitle",$p.html.buildTitle(lg('tutorialStepChoiceTitle'))); break;
		}
                        
        $p.print('breadcrumbs',l_s);

        $($p.tutorial.currentStep).style.fontWeight="bolder";
		$($p.tutorial.currentStep).style.color="#000000";	
    },   
    /*
		Function: $p.tutorial.displayIndex
                                       Display the first page (menus list)
	*/ 
    displayIndex:function()
    {
        var l_s="";
        $p.tutorial.widgetParameters={};
        $p.tutorial.breadCrumbs={};
        var excludeLinks = new Array();
       // $p.app.tabs.sel=999;
        $p.tutorial.emptyContent();
        $p.tutorial.initBreadCrumbs();
        $p.tutorial.menus.initMenus();
        $p.tutorial.initWidgetParameters();
        $p.tutorial.currentStep='choisir';
        excludeLinks.push("creer","configurer","visualiser","placer","picto","valider");
        $p.tutorial.buildBreadCrumbs(excludeLinks);
		
        l_s+='<table id="tutorialMenu" cellpadding="4" cellspacing="0">';
        l_s+=$p.tutorial.menus.displayMenus();
        l_s+='</table>';
         
        $p.print('content',l_s);
		
        if ($p.app.user.type=='A') {
            // Load module info
    		if( window.parent.$p.admin.widgets.currentwidgetid != indef) {
    			id = window.parent.$p.admin.widgets.currentwidgetid;
    			$p.tutorial.loadModInfo( id );
    			window.parent.$p.admin.widgets.currentwidgetid = indef;
    		}
		}
    },
    /*
		Function: $p.tutorial.widgetPreview
                                       Display a preview of the widget
	*/   
	widgetPreview:function()
    {
        $('content').empty();
        //$p.tutorial.initBreadCrumbs();
        //don't change text in lable, used in function $p.tutorial.save.afterSaved
        $p.tutorial.currentStep='visualiser'; 
        var excludeLinks = new Array();
        //excludeLinks.push("creer");
        $p.tutorial.buildBreadCrumbs(excludeLinks);
		
        var l_s='<center>\
                    <table id="tbl1" cellpadding="5" cellspacing="5" border="0" style="width:200px">\
                        <tr>\
                            <td id="displaySave" style="text-align:center"></td>\
                        </tr>\
                        <tr>\
                            <td id="widgetcontainer" style="font-size:1.2em"></td>\
                        </tr>\
                    </table>\
                  </center>';
              
        $p.print('content',l_s);
        $p.app.wait('displaySave');
        $p.tutorial.locales.getLocalesInternal();
        $p.tutorial.save.saveInServer();
    },
    action:indef,
    /*
                    Function: $p.tutorial.simpleWidgetPreview
                    
                        display widget to test content
                        
                        Parameters :
                        
                            editmode - edit mode (expert or novice)
            */
    simpleWidgetPreview: function (editMode) {
        $p.tutorial.save.displayAlert=0;
        $p.tutorial.expert.setWidgetCode(editMode);
        $p.tutorial.action='$p.tutorial.displayPreview';
        if ( $('widgetcontainer') ) {
            $p.app.wait('widgetcontainer');
        }
        $p.tutorial.widgetParameters["view"]='home';
        //$p.tutorial.controls.checkViews();
        $p.tutorial.locales.getLocalesInternal();
        $p.tutorial.save.saveInServer();
    },
    /*
                    Function: $p.tutorial.displayPreview
                    
                        display widget preview
            */
    displayPreview:function (showNextStep) { 
    $p.app.tabs.sel=999; 
    tab[999]=new $p.app.tabs.object(
                                    0,
                                    "tuto",
                                    "P",
                                    "",
                                    0,
                                    0,
                                    0,
                                    0,
                                    "",
                                    0
                               );
    tab[999].id=1;
    tab[999].cols[1]=$p.get("widgetcontainer");
    tab[999].moduleAlign=true;
    tab[999].showType=0;
    tab[999].controls='Y';
    //widget type 
    var defvar =  decodeURIComponent($p.tutorial.widgetParameters["defvar"]);
    tab[999].module[0]=new $p.app.widgets.object(
                                1,  //col
                                1,  //pos
                                1,  //posj
                                $p.tutorial.widgetParameters["height"],
                                $p.tutorial.widgetParameters["id"], //id
                                "--",   //urlwebsite
                                $p.tutorial.widgetParameters["name"],
                                defvar, //variables
                                $p.tutorial.widgetParameters["minwidth"],
                                1,  //sizable
                                200, //minmodsize
                                $p.tutorial.widgetParameters["url"],
                                0,  //x
                                0,  //y
                                1,  //maxUniq
                                $p.tutorial.widgetParameters["format"],
                                0,  //number of variables
                                1,  //id
                                0,  //blocked
                                0,  //minimized
                                0,  //use_reader
                                0,  //autorefresh
                                $p.tutorial.widgetParameters["icon"],
                                false, //is loaded status of the module (indef=not loaded, false=loading, true=loaded)
                                indef, //header
                                indef, //footer
                                indef, //auth  for RSS authentified feeds
                                $p.tutorial.widgetParameters["view"],  //views (home or canvas) canvas for full-screen (full-portal)
                                $p.tutorial.widgetParameters["l10n"]
                                );
    tab[999].module[0].create();
    tab[999].module[0].show();
    //$('hmod1_1').style.display="block";
    //$('editbar1_1').style.display="block";
    if (showNextStep!=indef) {
        $('displaySave').innerHTML='<input type="button" onclick=\"eval($p.tutorial.breadCrumbs[\'valider\']);return false;\" value="'+lg("nextStep")+'>>>">';              
    }
    },
    /*
		Function: $p.tutorial.displayKeywordsInput
                                       Display a keyword input fields
                               
                     Returns:
                     
                                HTML code
	*/       
    displayKeywordsInput:function()
    {
        var l_s=lg("keywordsForModule")+' <a href="#" onclick=window.parent.$p.app.alert.show(lg("moduleAddingKeywordHlp"));return false;>\
                <img src="../images/ico_help2.gif" /></a><br />\
                <input id="addrsskeywords" type="text" name="keywords" value="'+$p.tutorial.widgetParameters["keywords"]+'" size="120" onkeyup=$p.tags.autocompletion.get("addrsskeywords"); onblur=$p.tags.autocompletion.hide(); />';
       
        return l_s;
    },
    /*
		Function: $p.tutorial.getCategoryPath
                                       Define the current category path
                               
                     Parameters:
                     
                                name - category name
                                level - category level
	*/    
    getCategoryPath:function(name,level)
    {
        if (typeof($p.tutorial.widgetCategoryPath[level])=="undefined") {
           $p.tutorial.widgetCategoryPath[level]=name;
        }
        else {
            $p.tutorial.widgetCategoryPath={};
            $p.tutorial.widgetCategoryPath[level]=name;
        }
    },
    /*
		Function: $p.tutorial.refreshFullPath
                                       Display the category path
	*/ 
    refreshFullPath:function()
    {
        var fullPath=lg("currentPath")+" /";
        for (var w in $p.tutorial.widgetCategoryPath)
        {
            fullPath+=$p.tutorial.widgetCategoryPath[w]+"/";
        }
        $p.print('completePath',fullPath);
    },
    /*
		Function: $p.tutorial.displayConfiguration
                                       Redirect to the module configuration (function called depends on the widget type)
	*/ 
    displayConfiguration:function()
    {
        if ($p.tutorial.widgetParameters["type"]=='rss') {
            $p.tutorial.rss.checkRssFeed();   
        }
        else {
            $p.tutorial.download.checkXML($p.tutorial.widgetParameters["type"]);
        }
    },
    /*
		Function: $p.tutorial.displayLinkToCategory
        
                                       Display the categories explorer
                                       
	*/ 
    displayLinkToCategory:function()
    {
        var l_s="";
        $('content').empty();
        $p.tutorial.currentStep='placer';
        var excludeLinks = new Array();
        excludeLinks.push("valider");
        $p.tutorial.buildBreadCrumbs(excludeLinks);    
        document.location.href='#breadcrumbs';

        l_s='<div id="tutCategory">\
              <form name="f">\
              '+$p.tutorial.displayLangChoice()+'<br />\
			  <input type="hidden" value="" id="dirid" name="dirid" value="'+$p.tutorial.widgetParameters["category_id"]+'" />\
              <div id="rootdir" name="dirid"></div>\
              <div id="completePath"></div><br />\
              <div id="directory" name="directory"></div>\
              <input type="button" onclick=\"$p.tutorial.setLinkToCategory();eval($p.tutorial.breadCrumbs[\'picto\']);return false;\" value="'+lg("nextStep")+'>>>">\
              </form>\
              </div>';

        $p.print('content',l_s);
        $p.tutorial.refreshFullPath();
        $p.tutorial.showDir($p.tutorial.widgetParameters["category_id"],$p.tutorial.widgetParameters["lang"]);
    },
    /*
		Function: $p.tutorial.setLinkToCategory
                                       Memorise the current widget category id / language
	*/ 
    setLinkToCategory:function()
    {
        $p.tutorial.refreshFullPath();
        $p.tutorial.widgetParameters["category_id"]=document.forms["f"].elements["dirid"].value;
		$p.tutorial.widgetParameters["lang"]=(document.forms["f"].elements["modlang"][0].checked)
															?	document.forms["f"].elements["modlang"][0].value
															:	document.forms["f"].elements["modlang"][1].value;
    },
    /*
		Function: $p.tutorial.switchColor
                                       Switch an element background color
                               
                     Parameters:
                     
                                v_obj - element id
                                v_color - color to set
	*/ 
    switchColor:function(v_obj,v_color)
	{
		var l_obj=$p.get(v_obj);
		l_obj.style.borderColor=v_color;
	},
    /*
		Function: $p.tutorial.showDir
                                       Display the categories on the category tree explorer
                               
                     Parameters:
                     
                                v_root - root category 
                                v_lang - category language
                                
                     Returns:
                     
                                false
	*/ 
	showDir:function(v_root,v_lang)
	{
		var l_s=lg("defineDirectory")+" : ";
		if (v_lang==indef) { v_lang=__lang; }
		$p.tutorial.widgetParameters["lang"]=v_lang;

		for (var i=0;i<__dimension.length;i++)
		{
			if (__dimension[i]["id"]!=0)
			{
				if (__dimension[i]["lg"]=="" 
                    || __dimension[i]["lg"]==v_lang)	{
					if (v_root==indef) v_root=__dimension[i]["id"];
					l_s+="<a id='rootdir"+__dimension[i]["seq"]+"' class='"+(v_root==__dimension[i]["id"]?"sellist":"optlist")+"' href=# onclick=\'return $p.tutorial.showDir("+__dimension[i]["id"]+","+v_lang+")\'>"+__dimension[i]["name"]+"</a> | ";
				}
			}
		}
		l_s+='<a href="#" onclick="return window.parent.$p.app.alert.show(lg(\'moduleAddingDirectoryHlp\'))"><img src="../images/ico_help2.gif" /></a><br /><br />';
		$p.print("rootdir",l_s);
		$p.tutorial.initDir(v_root,v_lang);     
		return false;
	},
	returnPortal:function()
	{
		$p.url.openLink(tutorial["scr_defportal"]);
	},
    /*
		Function: $p.tutorial.initDir
                                       Display the category explorer
                               
                     Parameters:
                     
                                v_id - root category id
                                v_lang - category language
                                
                     Returns:
                     
                                false
	*/ 
	initDir:function(v_id,v_lang)
	{
		// Initialize modules directory menu
		var l_s="<table border='0'><tr>";
		l_s+="<td><div id='level1' class='dirdivi' style='width:230px;'></div></td>";
		l_s+="<td><div id='level2' class='dirdivi' style='width:200px;'></div></td>";
		l_s+="<td><div id='level3' class='dirdivi' style='width:200px;'></div></td>";
		l_s+="</tr></table>"+$p.img("",7,7)+"<br />";
		$p.show("directory","block");
		$p.print("directory",l_s);
		$p.print("level1","Loading ...");
		$p.tutorial.getDir(v_id,1,v_lang);
		return false;
	},
	getDir:function(v_cat,v_level,v_lang)
	{
		// Open the modules directory
		document.forms["f"].dirid.value=v_cat;
		//getXml("../cache/cat_"+v_cat+"_"+v_lang+".xml?rand="+rand,$p.tutorial.displayDir,new Array(v_level,v_cat,v_lang));
		getXml(tutorial["xml_directory"]+"?catid="+v_cat+"&rand="+rand,$p.tutorial.displayDir,new Array(v_level,v_cat,v_lang));
	},
	displayDir:function(response,vars)
	{
		var l_s="";
		if (response.getElementsByTagName("dir")[0])
		{
			if (response.getElementsByTagName("dir")[0])
			{
				var l_i=0,l_dirid;
				l_s+="<table cellpadding=0 cellspacing=1 border=0 style='width:90%'>";
				while (response.getElementsByTagName("dir")[l_i])
				{
					var l_result=response.getElementsByTagName("dir")[l_i];
					l_dirid=$p.ajax.getVal(l_result,"dirid","int",false,0);
					l_dirname=$p.string.removeCot($p.ajax.getVal(l_result,"dirname","str",false,"..."));
					l_secured=$p.ajax.getVal(l_result,"secured","int",false,0);
                    
					l_s+="<tr><td id='dir"+l_dirid+"' class='catopt"+(l_secured==0?"":"s")+"i' onmouseover='catOptOver(\""+l_dirid+"\","+l_secured+")' onmouseout='catOptOut(\""+l_dirid+"\","+l_secured+")'> "
                                    + "<a href=# class='menul' onclick='$p.tutorial.getCategoryPath(\""+l_dirname+"\",\""+vars[0]+"\");$p.tutorial.getDir(\""+l_dirid+"\","+(vars[0]+1)+",\""+vars[2]+"\","+l_secured+");return false;'>"
                                    + l_dirname+"</a>"
                          +"</td></tr>";
					l_i++;
				}
				l_s+="</table>";
			}
		}
		else l_s+=lg("noSubCategory");

		$p.setClass("level"+vars[0],"dirdiva");
		$p.print("level"+vars[0],l_s);
		//clear unused div
		if (!vars[2]) {for (var i=vars[0]+1;i<5;i++){$p.print("level"+i,"");$p.setClass("level"+i,"dirdivi");};}
		catOptSel(vars[1],vars[0]-1);
	},
    /*
		Function: $p.tutorial.iconLoad
                                       Display the icons list
	*/ 
	iconLoad:function()
	{
		var i = 0;
		var icon = "icon"+i;
		var l_s="<table cellpadding=1><tr>";
		var color = "#ffffff";

		if( $p.tutorial.widgetParameters["validate"]=="validated" ) {
			icon = $p.tutorial.widgetParameters["icon"];
			i = $p.tutorial.widgetParameters["id"];
			color = "#ff0000";
		}
		l_s+="<td><a href=# id='icon0' onclick='return $p.tutorial.iconSet(0)' style='border:2px solid "+color+";'>"
                            +$p.img($p.tutorial.widgetParameters["icon"],16,16)+"</a></td>";
							
		for ( i=1;i<__nbicons;i++ )
		{
			l_s+="<td><a href=# id='icon"+i+"' onclick='return $p.tutorial.iconSet("+i+")' style='border:2px solid #ffffff;'>"
                            +$p.img("../modules/pictures/_deficon"+i+".gif",16,16)+"</a></td>";
			if (i%30==29) l_s+="</tr><tr>";
		}
		l_s+="</tr></table>";
		$p.print("iconlist",l_s);
		
		return l_s;
	},
    /*
		Function: $p.tutorial.iconLoad
                                       Give focus and change the bgcolor of the selected icon
                               
                     Parameters:
                     
                                v_id - icon id
                                
                     Returns:
                     
                                false
	*/ 
	iconSet:function(v_id)
	{
		var i=0;
		while($p.get("icon"+i)!=null)
		{
			($p.get("icon"+i)).style.borderColor=(i==v_id?"#ff0000":"#ffffff");
			i++;
		}
		document.forms["f"].elements['icondef'].value=(v_id==__nbicons?-1:v_id);
		return false;
	},
    /*
		Function: $p.tutorial.formatKeywords
                                       format the keywords value
                               
                     Parameters:
                     
                                v_form - form name
	*/ 
	formatKeywords:function(v_form)
	{
		v_form.keywords.value=$p.tags.formatList(v_form.keywords.value);
	},
    /*
		Function: $p.tutorial.updateTabWidgetInformations
        
                                       Define current widget information concerning its configuration
	*/ 
    updateTabWidgetInformations:function()
    {
        //update widget informations   
        if($p.app.user.type=='A' && $p.tutorial.expert.containsURL==false) {
            $p.tutorial.widgetParameters['format'] = (document.forms['f'].elements['format'][0].checked)      ?         document.forms['f'].elements['format'][0].value
                                                                                                              :         document.forms['f'].elements['format'][1].value;
        }
        else if ($p.app.user.type=='A' && $p.tutorial.expert.containsURL==true) {
            $p.tutorial.widgetParameters['format'] = document.forms['f'].elements['format'].value;
        }
        else {
            $p.tutorial.widgetParameters['format'] = document.forms['f'].elements['format'].value;
        }
        $p.tutorial.widgetParameters['name'] = document.forms['f'].elements['title'].value;
        $p.tutorial.widgetParameters['description'] = document.forms['f'].elements['desc'].value;
        $p.tutorial.widgetParameters['height'] = document.forms['f'].elements['heig'].value;
        $p.tutorial.widgetParameters["autorefresh"] = (document.forms['f'].elements['autorefresh'].checked)?1:0;
        $p.tutorial.widgetParameters['keywords'] = document.forms['f'].elements['keywords'].value;
        
        
        
        //add information in xml code
    },
    /*
                    Function:   $p.tutorial.displayLangChoice
                                    
                                       Display the language choice for the widget
									   
		Returns:
		
                                        HTML code
          */
	displayLangChoice:function()
	{
		var avlangSel='';
		var l_s='';
	    if (__AVLANGS.length>1) {
            l_s+=lg("moduleLanguage")+" :";
            for (var i=0;i<__AVLANGS.length;i++)
    		{
				avlangSel='';
                if ($p.tutorial.widgetParameters["lang"]==__AVLANGS[i]) { 
                    avlangSel="checked=checked"; 
                }
    			l_s+='<input type="radio" name="modlang" value="'+__AVLANGS[i]+'" '+avlangSel+' onclick=$p.tutorial.showDir(indef,"'+__AVLANGS[i]+'"); />'+__AVLANGS[i]+' ';
    		}
            l_s+='<br />';
        }
		return l_s;
	},
	/*
                    Function:   $p.tutorial.displayAutoRefeshOption
                                    
                                       Display the choice for  autorefreshing the widget
									   
		Returns:
        
                                        HTML
          */
	displayAutoRefeshOption:function()
	{
		var l_s='';
		var v_checked='';
		if($p.tutorial.widgetParameters["autorefresh"]==1) {
			v_checked='checked';
		}
		l_s+='<input type="checkbox" name="autorefresh" id="autorefresh" '+v_checked+' /> <label for="autorefresh" >'+lg("autorefreshModule")+'</label>';
	
		return l_s;
	},
	/*
                    Function:   $p.tutorial.loadModInfo
                                    
                                       Get module information 
									   
		Returns:
        
                                        HTML
          */
	loadModInfo:function(v_id)
	{
        $p.ajax.call(tutorial["xml_getmoduleinfo"]+'?itemid='+v_id,
            {
                'type':'load',
                'callback':
                {
                    'function':$p.tutorial.setModInfo,
                    'variables':
					{
						'itemid':v_id
					}
                }
           }  
        );
        return false; 
	},
	/*
                    Function:   $p.tutorial.setModInfo
                                    
                                       Set module information 
									   
		Returns:
        
                                        HTML
          */
	setModInfo:function(response,vars)
	{
        var oldid = $p.ajax.getVal(response,"id","int",false,0);
		$p.tutorial.widgetParameters["id"]=vars['itemid'] || oldid;
        $p.tutorial.widgetParameters["url"]=$p.ajax.getVal(response,"url","str",false,"");
        $p.tutorial.widgetParameters["defvar"]=$p.ajax.getVal(response,"defvar","str",false,"");
        $p.tutorial.widgetParameters["nbvariables"]=$p.ajax.getVal(response,"nbvariables","int",false,0);
        $p.tutorial.widgetParameters["name"]=$p.ajax.getVal(response,"name","str",false,"");
        $p.tutorial.widgetParameters["description"]=$p.ajax.getVal(response,"description","str",false,"");
        $p.tutorial.widgetParameters["status"]=$p.ajax.getVal(response,"status","str",false,"");
        $p.tutorial.widgetParameters["type"]=$p.ajax.getVal(response,"type","str",false,"");
        $p.tutorial.widgetParameters["format"]=$p.ajax.getVal(response,"format","str",false,"");
        $p.tutorial.widgetParameters["height"]=$p.ajax.getVal(response,"height","str",false,"");
        $p.tutorial.widgetParameters["minwidth"]=$p.ajax.getVal(response,"minwidth","str",false,"");
        $p.tutorial.widgetParameters["website"]=$p.ajax.getVal(response,"website","str",false,"");
        $p.tutorial.widgetParameters["lang"]=$p.ajax.getVal(response,"lang","str",false,"");
		if( $p.ajax.getVal(response,"icon","str",false,"")!="" ) {
			$p.tutorial.widgetParameters["icon"]=$p.ajax.getVal(response,"icon","str",false,"");
		} else {
			$p.tutorial.widgetParameters["icon"]="../modules/pictures/box0_"+$p.tutorial.widgetParameters["id"];
		}
		$p.tutorial.widgetParameters["autorefresh"]=$p.ajax.getVal(response,"autorefresh","str",false,"");
		$p.tutorial.widgetParameters["usereader"]=$p.ajax.getVal(response,"usereader","str",false,"");
		// Widget validated
		$p.tutorial.widgetParameters["validate"]=$p.ajax.getVal(response,"validate","str",false,"");

        $p.tutorial.widgetParameters["l10n"] = $p.ajax.getVal(response,"l10n","str",false,"");
        //get the XML of the module       
        $p.tutorial.getModXML(response);
        
        //OpenExpertMode
		$p.tutorial.menus.setWidgetType('expert');
		$p.tutorial.expert.displayExpertMode('expert');
		document.location.href='#breadcrumbs';
	},
    getModXML:function(response)
    {   
    
        var i=0,result;
        var content=$p.tutorial.xmlTemplate['dtd']+
                    $p.ajax.getVal(response,"headers","str",false,"");

        while (response.getElementsByTagName("contents")[i])
        {
            result=response.getElementsByTagName("contents")[i];
            var type = $p.ajax.getVal(result,"type","str",false,"html");
            var view = $p.ajax.getVal(result,"view","str",false,"home");
            if (type=='url') {
                var url = $p.ajax.getVal(result,"url","str",false,"");
                content+='<Content type="'+type+'" view="'+view+'" href="'+url+'" >\r\n</Content>\r\n';
            }
            else if (type=='html') {
                var incontent = $p.ajax.getVal(result,"incontent","str",false,"");
                content+='<Content type="'+type+'" view="'+view+'" ><![CDATA[\r\n'+incontent+']]>\r\n</Content>\r\n';
            }
            i++;
        }
        content+="</Module>";  
      

        $p.tutorial.widgetParameters["code"]=content;
    }    
}

//**************************************** WIDGETS FACTORY TYPE "DOWNLOAD" FUNCTIONS *************************************************************************************
/*
    Class: $p.tutorial.download
            Widget type "download"
*/
$p.tutorial.download={
	/*
                    Function:   $p.tutorial.download.displayDownloadForm
                                    
                                       Display the download form
          */
    displayDownloadForm:function()
    {
        var l_s="";
        $p.tutorial.expert.containsURL=true;
        $p.tutorial.breadCrumbs['creer']='$p.tutorial.download.displayDownloadForm();';
        $p.tutorial.emptyContent();
        $p.tutorial.initWidgetParameters();
        $p.tutorial.widgetParameters["type"]="download";
        $p.tutorial.currentStep='creer';
        var excludeLinks = new Array();
        //$p.tutorial.initBreadCrumbs();
        excludeLinks.push("visualiser","placer","configurer","picto","valider");
        $p.tutorial.buildBreadCrumbs(excludeLinks);
        $p.plugin.hook.launch('expertmodule_options');
        
        $p.print("content","<iframe src='"+tutorial["frm_widget_download"]+"?id="+$p.tutorial.widgetParameters['id']+"' frameborder='no' marginwidth='0' marginheight='0' width='500' height='300'></iframe>");
	},
    /*
                    Function: $p.tutorial.download.checkXML
                                     Prepare the html div to control the XML syntax
                                     
                    Parameters:
                    
                                    v_fct -  function called when the user/admin wants to cancel his changes
          */
    checkXML:function(v_fct)
    {	
        $p.tutorial.currentStep='configurer';
        var excludeLinks = new Array();
        excludeLinks.push("visualiser","placer","picto","valider","creer");
        $p.tutorial.buildBreadCrumbs(excludeLinks);
        var l_s= "<table cellpadding='10' cellspacing='0' border='0' width='100%'>\
                    <tr>\
                    <td>\
                    <form name='f' method='post' ><br />\
                    <input type='hidden' name='modcontent' />\
                    <div id='loadstat'></div>\
                    </form>\
                    <br />\
                    </td>\
                    </tr>\
                  </table>";
                  
       $p.print('content',l_s);
       $p.tutorial.expert.getFields(v_fct);
    }
}


//**************************************** WIDGETS FACTORY, MENUS FUNCTIONS *************************************************************************************
/*
    Class: $p.tutorial.menus
            Menus management functions
*/
$p.tutorial.menus={
    menusProcess:{},
    /*
                    Function: $p.tutorial.menus.initMenus
                                     Initialise the main page default menus (different types of widgets)
          */
    initMenus:function()
    {
        $p.tutorial.menus.menusProcess['rss']={'label':'addYourModRSS',
                            'labelTxt':'addYourModRSS',
                            'labelDesc':'addYourModRSSDesc',
                            'fct':'$p.tutorial.rss.displayRssIndex();',
							'icon':'../images/ico_tutorial_rss.gif'};
        $p.tutorial.menus.menusProcess['expert']={'label':'addYourModExpert',
                            'labelTxt':'addYourModExpert',
                            'labelDesc':'addYourModExpertDesc',
                            'fct':'$p.tutorial.expert.displayExpertMode(\'expert\');',
							'icon':'../images/ico_tutorial_expert.gif'};
        //$p.tutorial.menus.menusProcess['editor']={'label':'addEditorMod','labelTxt':'addEditorModTxt','fct':'$p.tutorial.expert.displayEditorMode();return false;'}; 
        //$p.tutorial.menus.menusProcess['novice']={'label':'novice','labelTxt':'novicetxt','fct':'return false;'};
        $p.tutorial.menus.menusProcess['external']={'label':'addExternalMod',
                            'labelTxt':'addExternalModTitle',
							'labelDesc':'addExternalModDesc',
							'fct':'$p.tutorial.external.displayExternalOption();',
							'icon':'../images/ico_tutorial_external.gif'};
        $p.tutorial.menus.menusProcess['download']={'label':'addDownloadMod',
                            'labelTxt':'addDownloadModTitle',
							'labelDesc':'addDownloadModDesc',
							'fct':'$p.tutorial.download.displayDownloadForm();',
							'icon':'../images/ico_tutorial_download.gif'};        
        $p.plugin.hook.launch('addmodule_options');
    },
    /*
                    Function: $p.tutorial.menus.displayMenus
                                     Display the menus
                                     
                    Returns:
                    
                                    HTML code
          */
    displayMenus:function()
    {
		
        var l_s="";
		
        for (var w in $p.tutorial.menus.menusProcess) 
        {
           var labelTxt = lg($p.tutorial.menus.menusProcess[w]["labelTxt"]);
           var labelDesc = lg($p.tutorial.menus.menusProcess[w]["labelDesc"]);
		   var icoMenu = $p.tutorial.menus.menusProcess[w]["icon"];
           var fct = $p.tutorial.menus.menusProcess[w]["fct"];
           l_s+='<tr>\
                    <td style="cursor:hand;cursor:pointer;" onmouseover="$p.tutorial.switchColor(\''+w+'\',\'#0E679A\');" onmouseout="$p.tutorial.switchColor(\''+w+'\',\'#E9EDF2\');" onclick="document.location=\'#breadcrumbs\';$p.tutorial.menus.setWidgetType(\''+w+'\');'+fct+'">\
                        <div id="'+w+'" class="dotted" style="border:1px solid #E9EDF2;">\
							<div id="ico">\
							<img src="'+icoMenu+'" />\
							</div>\
							<div id="label">\
							<h2>'+labelTxt+'</h2>\
                            '+labelDesc+'\
							</div>\
                            <div class="float_correction" />\
                        </div>\
                    </td>\
                </tr>';
        }

        return l_s;        
    },
    /*
                    Function: $p.tutorial.menus.setWidgetType
                                     Define the widget type
                                     
                    Parameters:
                    
                                    label - widget type
          */
    setWidgetType:function(label)
    {
        $p.tutorial.widgetParameters["editmode"]=label;
        $p.tutorial.widgetParameters["type"]=label;
    }
}


//**************************************** WIDGETS FACTORY, TYPE "EXTERNAL" FUNCTIONS *************************************************************************************
/*
    Class: $p.tutorial.external
            Widgets type "external" functions
*/
$p.tutorial.external={
    defCode:'',
    baseCode:'',
    v_url:'',
    /*
                    Function: $p.tutorial.external.displayExternalOption
                                     Display the different external widgets category (netvibes/igoogle)
          */
	displayExternalOption:function()
    {
        var l_s="";
        $p.tutorial.breadCrumbs['creer']='$p.tutorial.external.displayExternalOption();';
        var excludeLinks = new Array();
        $p.tutorial.emptyContent();
        $p.tutorial.currentStep='creer';
        excludeLinks.push("visualiser","placer","configurer","picto","valider");
        $p.tutorial.buildBreadCrumbs(excludeLinks);

		var l_s='<br /><a href="#" onclick="document.location.href=\'#headlink\';$p.tutorial.external.buildNetvibesSourceForm();return false;">Netvibes Widgets</a>\
                 <br /><br /><a href="#" onclick="document.location.href=\'#headlink\';$p.tutorial.external.buildIGoogleWidgetForm();return false;">iGoogle Gadgets</a>';
		
		$p.print('content',l_s);
	},
    initClassVars:function()
    {
        $p.tutorial.external.v_url='';
        $p.tutorial.external.getCode('_template');
    },
    /*
                    Function: $p.tutorial.external.buildNetvibesSourceForm
                                     Display the netvibes form
          */
    buildNetvibesSourceForm:function()
	{
        $p.tutorial.external.initClassVars();
		var l_s='<br /><form name="f">'+lg('tutorialNetvibesHelp')+'<div id="msg"></div>\
			<input type="text" name="moduleurl" style="width: 450px;" />\
            <input type="button" value="'+lg("nextStep")+'>>>" onclick="$p.tutorial.external.displayNetvibesSource(document.forms[\'f\'].elements[\'moduleurl\'].value);return false;" />\
			</form><br />'+
            '<a onclick="$p.app.popup.show($p.tutorial.external.iframeNetVibesHelp(),800,indef,\'help\',true);return false;" href="#">'+lg("lblHelp")+'&nbsp;<img align="absmiddle" src="../images/ico_popup.gif"/></a>';
        $p.print('content',l_s);          
	},
    /*
                    Function: $p.tutorial.external.displayNetvibesSource
                                     Define the widget code, based on the netvibes URL specified in the form
                                     
                    Parameters:
                    
                                    v_url - URL of the Netvibes widget 
          */
	displayNetvibesSource:function(v_url)
	{
        $p.tutorial.external.v_url="";
        var excludeLinks = new Array();
        $p.tutorial.external.defCode=$p.tutorial.external.baseCode;
        
        if (v_url!="") {
            $p.tutorial.external.v_url=v_url;
            $p.tutorial.expert.containsURL=true;
            $p.tutorial.widgetParameters["url"]=tutorial["xml_getwidget"];
            //$p.tutorial.external.defCode=($p.tutorial.external.defCode).replace(/YOUR HTML/g,'');
            $p.tutorial.external.addJavascript('callNetvibesWidget("'+$p.tutorial.external.v_url+'");');
            $p.tutorial.widgetParameters["code"]=$p.tutorial.external.defCode;
            $p.tutorial.currentStep='configurer';
            excludeLinks.push("creer");
            $p.tutorial.displayConfiguration();
            $p.tutorial.breadCrumbs['configurer'];
        }
        else {
            window.parent.$p.app.alert.show(lg('noUrlSpecified'),3);
            $p.tutorial.currentStep='creer';
            excludeLinks.push("visualiser","placer","picto","valider","configurer");
        }
        $p.tutorial.buildBreadCrumbs(excludeLinks);  
	},
    addJavascript:function(v_javascript)
	{
//		var l_code=$p.tutorial.external.defCode;
	//	var l_pos=l_code.indexOf('</script>');
		$p.tutorial.external.defCode= $p.tutorial.xmlTemplate['dtd']
                                +   $p.tutorial.getXmlTemplateHeader()
                                +'<script type="text/javascript">'+"\r\n"
                                + v_javascript
                                +'</script>'+"\r\n"
                                + $p.tutorial.xmlTemplate['footer'];
        

        //l_code.substr(0,l_pos)+v_javascript+l_code.substr(l_pos,l_code.length);
        window.parent.$p.app.alert.show(lg('widgetCodeUpdated'),1);
    },
    /*
                    Function: $p.tutorial.external.getCode
                                     Get widget code
                                     
                    Parameters:
                    
                                    v_id - module id
          */
    getCode:function(v_id)
	{
		getXml("../modules/quarantine/module"+v_id+".xml",$p.tutorial.external.showCode,"","html");
	},
	showCode:function(response,vars)
	{
        $p.tutorial.external.baseCode=response;
		$p.tutorial.external.defCode=response;
	},    
    /*
                    Function: $p.tutorial.external.buildIGoogleWidgetForm
                                     Display the external Igoogle widget form
          */
    buildIGoogleWidgetForm:function()
	{
		var l_s='<br /><form name="igoogle">'+lg('tutorialIgoogleHelp')+'<div id="msg"></div>\
                 <input type="text" name="moduleurl" style="width: 450px;" />\
                 <input type="button" onclick="$p.tutorial.external.setIGoogleWidget();return false;" value="'+lg("nextStep")+'>>>" />\
                 </form>';
        $p.print('content',l_s);
	},
    /*
                    Function: $p.tutorial.external.setIGoogleWidget
                                     Control the URL specified in the form
                                     
                    Returns:
                    
                                    false
          */
    setIGoogleWidget:function()
    {
        var url="";
        url=document.forms['igoogle'].elements['moduleurl'].value;
        if (url=="") {
            window.parent.$p.app.alert.show(lg('noUrlSpecified'));
        }
        else {
            $p.tutorial.expert.containsURL=true;
            url=$p.string.esc(url); 
            $p.ajax.call(tutorial["xmligoogle"]+'?moduleurl='+url,
                {
                    'type':'load',
                    'source':'html',
                    'callback':
                    {
                        'function':$p.tutorial.external.setIGoogleWidgetCode,
                        'variables':
    					{
    						'url':url
    					}
                    }
               }  
           );
        }
        return false; 
    },
    /*
                    Function: $p.tutorial.rss.setIGoogleWidgetCode
                                    Analyse the google widget content type
                                    
                    Parameters:
                    
                                    response - 'added' if the rss feed added correctly
                                    vars (hash) - [
                                                                'url' - complete url link to google widget
                                                           ]                                   
          */
    setIGoogleWidgetCode:function(response,vars)
    {   
       $p.tutorial.widgetParameters["code"]=$p.string.trim(response);
       //RSS widget
       if ($p.tutorial.widgetParameters["code"].indexOf("<rss") != -1 )  {
            $p.tutorial.widgetParameters["rssurl"] = $p.string.getVar($p.string.unesc(vars['url']),'url');
            $p.tutorial.widgetParameters["url"] = tutorial["p_rss"];
            $p.tutorial.widgetParameters["format"]='R';
            $p.tutorial.widgetParameters["defvar"]="nb=5";
            $p.tutorial.widgetParameters["nbvariables"]=1;
            $p.tutorial.widgetParameters["height"]="246";
            $p.tutorial.widgetParameters["icon"]="_deficon42.gif";
            $p.tutorial.menus.setWidgetType('rss');
            $p.tutorial.breadCrumbs['creer']='$p.tutorial.rss.displayRssIndex();';
            $p.tutorial.rss.checkRssFeed(); 
       }       
       else{
           //set content in content
           $p.tutorial.widgetParameters["url"]=tutorial["xml_getwidget"];
           //need  to add some item
           $p.tutorial.widgetParameters["format"]='I';
           $p.app.alert.show(lg('widgetCodeUpdated'));
           eval($p.tutorial.breadCrumbs['configurer']);
       }
    },
    iframeNetVibesHelp: function () {
       return '<iframe width="650" height="600" frameborder="0" src="http://www.portaneo.com/solutions/en/netvibes-widget-en.php"></iframe>';
    }
}

//**************************************** WIDGETS FACTORY, TYPE "RSS" FUNCTIONS *************************************************************************************
/*
    Class: $p.tutorial.rss
            Widgets type "rss" functions
*/
$p.tutorial.rss={
    /*
                    Function: $p.tutorial.rss.displayRssIndex
                                    Display the type RSS main page
                                    RSS specific parameters :
                                    $p.tutorial.widgetParameters["rssurl"] = <FEED RSS URL>
                                    $p.tutorial.widgetParameters["url"] = tutorial["p_rss"];
                                    $p.tutorial.widgetParameters["format"]='R';
                                    $p.tutorial.widgetParameters["defvar"]="nb=5";
                                    $p.tutorial.widgetParameters["nbvariables"]=1;
                                    $p.tutorial.widgetParameters["icon"]="_deficon42.gif";
                                    $p.tutorial.menus.setWidgetType('rss');
                                    $p.tutorial.breadCrumbs['creer']='$p.tutorial.rss.displayRssIndex();';
            
                    Parameters:
                    
                                    errmsg (optionnal) - 'added' if the rss feed added correctly
          */
    displayRssIndex:function(errmsg)
    {
        var l_s="";
        $p.tutorial.breadCrumbs['creer']='$p.tutorial.rss.displayRssIndex();';
        $p.tutorial.emptyContent();
        $p.app.tabs.sel=999; 
        $p.tutorial.widgetParameters["format"]="R";
        $p.tutorial.widgetParameters["url"]=tutorial["p_rss"];
        $p.tutorial.widgetParameters["rssurl"]="";
        $p.tutorial.widgetParameters["height"]="246";
        $p.tutorial.widgetParameters["defvar"]="nb=5";
        $p.tutorial.widgetParameters["nbvariables"]=1;
        $p.tutorial.widgetParameters["icon"]="_deficon42.gif";
        $p.tutorial.currentStep='creer';
        var excludeLinks = new Array();
        excludeLinks.push("visualiser","configurer","placer","picto","valider");
        $p.tutorial.buildBreadCrumbs(excludeLinks);
        
        l_s+='<table cellpadding="10" cellspacing="0" border="0" width=100% >\
            	<tr>\
                	<td>\
                        <div id="errmsg"></div>\
                    	<form name="rss" onsubmit="$p.tutorial.rss.checkRssFeed();return false;">\
                       	'+lg("rssFeedUrl")+'\
                    	<input type="text" name="vars" size="50" maxlength="150" value="http://" />\
                        <input type="button" onclick="$p.tutorial.rss.checkRssFeed();return false;" value="'+lg("nextStep")+'>>> "/><br />\
                        <div id="authrss" style="width: 220px; text-align: left; display: none;"/>\
                    	</form>\
                    	<br />\
                	</td>\
            	</tr>\
             </table>';
        
        $p.print('content',l_s);       
        if (typeof(errmsg!="undefined") && errmsg=='added') { $('errmsg').innerHTML="<font color='#ff0000' size='4'>"+lg("addRssSuccess")+"</font><br />"; }
    },
    /*
    
                Function: $p.tutorial.rss.loadRssAddToDirectory_step2
                
                Parameters: 
                        
                        v_id - id of module
                        
                        v_auth - authentication
    */
    loadRssAddToDirectory_step2:function(v_id,v_auth)
    {
       $p.tutorial.emptyContent();
       $p.ajax.call(tutorial["xml_rssaddtodirectory_step2"]+"?id="+v_id+"&auth="+v_auth,
            {
                'type':'load',
                'callback':
                {
                    'function':$p.tutorial.rss.displayRssAddToDirectory_step2,
                    'variables':
					{
						'auth':v_auth,
                        'id':v_id
					}
                }
           }  
        );
        return false;
    },
    /*
                    Function: $p.tutorial.rss.setAuth
                                     Set if the rss feed is protected by a password
          */
    setAuth:function()
    {
        $p.tutorial.widgetParameters["auth"] = (document.forms["f"].auth.checked)
                                                    ? 1
                                                    : (document.forms["f"].usecache.checked
                                                            ? 'x'
                                                            : '');
    },
    /*
                    Function: $p.tutorial.rss.setUsereader
                                     Set if the rss feed use the internal reader to display the articles
          */
    setUsereader:function()
    {
        $p.tutorial.widgetParameters["usereader"]=(document.forms["f"].usereader.checked)?1:0;
    },
    /*
                    Function: $p.tutorial.rss.setRssConfiguration
                                     Set the rss configuration
          */
    setRssConfiguration:function()
    {
        $p.tutorial.rss.setAuth();
        $p.tutorial.rss.setUsereader();
        $p.tutorial.widgetParameters["pfid"]=document.forms["f"].pfid.value;
        $p.tutorial.widgetParameters["rssurl"]=document.forms["f"].rssurl.value;
        $p.tutorial.widgetParameters["keywords"]=document.forms["f"].keywords.value;
        $p.tutorial.widgetParameters["name"]=document.forms["f"].title.value;
        $p.tutorial.widgetParameters["description"]=document.forms["f"].desc.value;
        $p.tutorial.widgetParameters["website"]=document.forms["f"].website.value;
        $p.tutorial.widgetParameters["defvar"]=$p.string.esc("nb=5&pfid="
                        +$p.tutorial.widgetParameters["pfid"]
                        +($p.tutorial.widgetParameters["auth"] == '' ? '' : '&auth='+$p.tutorial.widgetParameters["auth"])
                        +"&rssurl="
                        +$p.string.esc($p.tutorial.widgetParameters["rssurl"])
                        );                    
    },
    /*
                    Function: $p.tutorial.rss.displayRssAddToDirectory_step2
                                     Display the rss widget configuration page
                                     
                    Parameters:
                                
                                    response - XML object
                                    vars -
          */
    displayRssAddToDirectory_step2:function(response,vars)
    {
        $p.tutorial.emptyContent();
        var l_s="";
        var rssalreadyregistered=false;
        var avlangSel="";
        var authSel="";
        var excludeLinks = new Array();
        $p.tutorial.currentStep='configurer';
        excludeLinks.push("valider");
        $p.tutorial.buildBreadCrumbs(excludeLinks);
        var url=$p.ajax.getVal(response,"url","str",false,"");
        var id=$p.ajax.getVal(response,"id","int",false,0);
        var registered=$p.ajax.getVal(response,"registered","int",false,0);
        if (registered==1) { rssalreadyregistered=true; }
        l_s+='<div id="modules" valign="top" style="padding: 8pt;">';
        if (rssalreadyregistered) { 
            l_s+='<div style="padding:12px"><font color="#ff0000"><strong>'+lg("rssFeedAlreadyExist")+'</strong></font></div>'; 
        }
        l_s+='<div style="padding:12px">'+url+' <span id="feedstatus"></span> &nbsp; | <a href="#" onclick="$p.tutorial.rss.displayRssIndex();return false;">'+lg("changeFeed")+'</a></div>';

        if (vars['auth'] != indef && vars['auth'] != 'x') { authSel="checked=checked"; }
        
        l_s+='<form name="f">\
		<input type="button" value="'+lg("nextStep")+'>>>" onclick="$p.tutorial.rss.setRssConfiguration();eval($p.tutorial.breadCrumbs[\'placer\']);return false;" />\
        <input type="hidden" name="typ" value="U" />\
        <input type="hidden" name="fid" value="0" />\
        <input type="hidden" name="pfid" value="'+id+'" />\
        <input type="hidden" name="rssurl" value="'+url+'" />\
        <div id="loadstat" class="bottomhr"><img src="../images/ico_waiting.gif" /> Loading ...</div>\
        <div style="padding:12px" class="bottomhr">\
        <input type="checkbox" name="auth" '+authSel+' onclick=$p.tutorial.rss.setAuth(); /> '+lg("authentifiedFeed")+'<br />\
        <input type="checkbox" name="usecache" '+(vars['auth'] == 'x' ? 'checked="checked" ' : '')+' onclick=$p.tutorial.rss.setAuth(); /> '+lg("doNotCacheFeeds")+'\
        </div>\
        <div style="padding:12px" class="bottomhr"><input type="checkbox" name="usereader" onclick="$p.tutorial.rss.setUsereader();" /> '+lg("openFeedInReader")+'\</div>\
        <div style="padding:12px" class="bottomhr" id="keywords">\
        '+$p.tutorial.displayKeywordsInput()+'\
        </div>\
        <input type="button" value="'+lg("nextStep")+'>>>" onclick="$p.tutorial.rss.setRssConfiguration();eval($p.tutorial.breadCrumbs[\'placer\']);return false;" />\
        </form>\
        <br />\
        </div>\
        <div id="debug" />';
        
        $p.print('content',l_s);
        $p.tutorial.rss.setAuth();
        
        if ($p.tutorial.widgetParameters["keywords"]!="") {
            document.forms["f"].elements["keywords"].value=$p.tutorial.widgetParameters["keywords"];
        }
        
        if ($p.tutorial.widgetParameters["rssurl"]!="") {
            document.forms["f"].elements["usereader"].checked=$p.tutorial.widgetParameters["usereader"];
            document.forms["f"].elements["auth"].checked=$p.tutorial.widgetParameters["auth"];
        }
        var RSSauth=vars['auth'];
        var RSSurl=url;
        
        if (RSSauth==indef)    
        {  
            $p.tutorial.rss.getFields(RSSurl);  
        }
        else    
        {
        	if (__useproxy) {
        		$p.tutorial.rss.getFields(tutorial["xmltunproxy"]+"?ptyp=xml&auth="+$p.string.esc(RSSauth)+"&url="+RSSurl);
        	}
        	else    {
        		$p.tutorial.rss.getFields(tutorial["xmltunauth"]+"?ptyp=xml&auth="+$p.string.esc(RSSauth)+"&url="+RSSurl);
        	}
        }      
    },
    /*
                    Function: $p.tutorial.rss.checkRssFeed
                                     Get the rss URL
          */
    checkRssFeed:function()
    {
        var url=$p.tutorial.widgetParameters["rssurl"];
        if (url=="") {
            url=document.forms["rss"].elements["vars"].value;
        }
        $p.app.widgets.rss.checkFeed(url);       
    },
    /*
            Function: $p.tutorial.rss.getFields
            
             Parameters: 
             
                    v_rss - url to rss
    
           */
	getFields:function(v_rss)
	{
		getXml(v_rss,$p.tutorial.rss.checkFeed,v_rss,"html");
	},
    /*
                    Function: $p.tutorial.rss.checkFeed
                                     Check the rss XML header syntax
                                     
                    Parameters:
                                
                                    response - XML object
                                    vars -
          */
	checkFeed:function(response,vars)
	{
		//analyse content of the rss feed  if not rss
		if (response.indexOf("<?xml")==-1 && response.indexOf("<rss")==-1) {
			if (response.indexOf("404")!=-1) {
				$p.app.alert.show(lg("invalidRss"));
			}
			else if (response=="") {
				$p.app.alert.show(lg("invalidRss"));
			}
			else if (response.indexOf("401")!=-1) {
				$p.url.openLink(tutorial["rssaddtodirectory"]+"?auth=1");
			}
			else {
				$p.app.alert.show(lg("msgIncorrectFeed"));
			}
		}
		else {
			getXml(vars,$p.tutorial.rss.validFields,vars,"xml");
		}
		return false;
	},
    /*
                    Function: $p.tutorial.rss.validFields
                                    Control the widget code (userprefs..Etc)
                                     
                    Parameters:
                                
                                    response - XML object
                                    vars - vars['fct'] => function called if user/admin cancel
          */
	validFields:function(response,vars)
	{
		var gErr=false,l_s="<div style='padding: 12px'>";
		if (!response.getElementsByTagName("title")[0]) gErr=true;
        var title=$p.ajax.getVal(response,"title","str",false,lg("titleMissing"));
        var desc=$p.ajax.getVal(response,"description","str",false,lg("descMissing"));
        var website=$p.ajax.getVal(response,"link","str",false,lg("linkMissing"));
        var pfid = $p.ajax.getVal(response,"id","int",false,'');
        if ( (title!=$p.tutorial.widgetParameters['name']) && ($p.tutorial.widgetParameters['name']!="Your widget name") ) {
            title=$p.tutorial.widgetParameters['name'];
        }
        if ( (desc!=$p.tutorial.widgetParameters['description']) && ($p.tutorial.widgetParameters['description']!="widget description") ) {
            desc=$p.tutorial.widgetParameters['description'];
        }
        if ( (website!=$p.tutorial.widgetParameters['website']) && ($p.tutorial.widgetParameters['website']!="") ){
            website=$p.tutorial.widgetParameters['website'];
        }

		l_s+="<table cellpadding='0'><tr><td>";
		l_s+="<table cellpadding='0' cellspacing='2'>";
		l_s+='<tr><td>'+lg("title")+' : </td><td><input type="text" name="title" value="'+title+'" size="30" maxlength="30" /> <a href="#" onclick=\'window.parent.$p.app.alert.show(lg("rssFeedAddingTitleHlp"));return false;\'><img src="../images/ico_help2.gif" /></a></td></tr>';
		l_s+='<tr><td>'+lg("desc")+' : </td><td><textarea name="desc" cols="30" rows="5">'+desc+'</textarea> <a href="#" onclick=\'window.parent.$p.app.alert.show(lg("rssFeedAddingDescHlp"));return false;\'><img src="../images/ico_help2.gif" /></a></td></tr>';
		l_s+='<tr><td>'+lg("siteUrl")+' : </td><td><input type="text" name="website" value="'+website+'" size="30" maxlength="150" /></td></tr>';
		l_s+="</table></td>";

		if (response.getElementsByTagName("item")[0])
		{
			l_s+="<td width='40'></td><td valign='top'>"+lg("latestArticles")+" :<br />";
			var i=0,result;
			while (response.getElementsByTagName("item")[i] && i<5)
			{
				result=response.getElementsByTagName("item")[i];
				l_s+="- "+$p.ajax.getVal(result,"title","str",false,"--")+"<br />";
				i++;
			}
			l_s+="</td>";
		} else if( response.getElementsByTagName("entry")[0] ) {
			l_s+="<td width='40'></td><td valign='top'>"+lg("latestArticles")+" :<br />";
			var i=0,result;
			while (response.getElementsByTagName("entry")[i] && i<5)
			{
				result=response.getElementsByTagName("entry")[i];
				l_s+="- "+$p.ajax.getVal(result,"title","str",false,"--")+"<br />";
				i++;
			}
			l_s+="</td>";
		}
		l_s+="</tr></table></div>";
		if (gErr)   {
			return;
		}

		$p.print("loadstat",l_s);
		$p.print("feedstatus","<img src='../images/ico_confirm.gif' />");
		
        $p.tutorial.rss.setRssConfiguration();
	},
	getIcon:function(v_rss)
	{
		$p.app.widgets.rss.getFeed({'rss':v_rss},$p.tutorial.rss.displayIcon);
	},
	displayIcon:function(response,vars)
	{
		var l_s="<input type='hidden' name='icondef' value='"+__nbicons+"' />"+lg("defaultIcon")+" : ";
		if (!response.getElementsByTagName("error")[0])
		{
			l_icon="rss"+$p.ajax.getVal(response,"id","str",false,"0");
			l_s+="<a href='#' id='icon"+__nbicons+"' onclick='return $p.tutorial.iconSet("+__nbicons+")' style='border:2px solid #ffffff;'><img src='../modules/pictures/"+l_icon+"' width='16' height='16' /></a><br /><br />"+lg("orChooseIconInList")+" :";
			l_s+="<input type='hidden' name='icondefurl' value='"+l_icon+"' />";
		}
		l_s+="<div id='iconlist'></div>"
		$p.print("icondiv",l_s);
		
		$p.tutorial.iconLoad();
		$p.tutorial.iconSet(__nbicons);
	},
	validForm:function(v_step)
	{
		if (v_step!=indef) document.forms["f"].action+="?step="+v_step;
		document.forms["f"].submit();
	},
	showOther:function()
	{
		var l_container=$p.get("otherarea");
		var l_content=document.forms["f"].other.value;
		l_container.style.backgroundColor="#ffffff";
		l_container.style.border=0;
		l_container.innerHTML=l_content;
	},
    /*
                    Function: $p.tutorial.rss.createMod
                                    Create a rss module
                                     
                    Parameters:
                                
                                    v_url - url of the XML file (p_rss.php)
          */
	createMod:function(v_url)
	{
		cols[1]=$p.get("col1");
		tab[0].module[0]=new $p.app.widgets.object(1,1,1,100,86,"--","--",
                    "nb=5",280,1,280,v_url,0,0,1,"R",1,1);
		tab[0].module[0].create();
		tab[0].module[0].show();
	},
	save:function(v_sess)
	{
		if (v_sess){
			document.forms["f"].action=tutorial["scr_rss_save"];
			document.forms["f"].submit();
		}
	}
}



$p.tutorial.html={
	vars:[],
	validForm:function(v_step)
	{
		if (v_step!=indef) document.forms["f"].action+="?step="+v_step;
		document.forms["f"].submit();
	},
	createMod:function(v_url,v_vars,v_height,v_minsize,v_nbvars)
	{
		cols[1]=$p.get("col1");
		tab[0].module[0]=new $p.app.widgets.object(1,1,1,v_height,1,"--","--",
                        v_vars,220,1,v_minsize,v_url,0,0,1,"I",v_nbvars);
		tab[0].module[0].create();
		tab[0].module[0].show();
	},
	save:function(v_sess)
	{
		if (v_sess)
		{
			document.forms["f"].action=tutorial["scr_html_save"];
			document.forms["f"].submit();
		}
	},
	resizeMod:function(v_sign,v_range,v_direction)
	{
		var l_obj=$p.get("col1");
		var l_frm=$p.get("modfram1");
		var l_size=(v_direction=="h")?l_obj.style.width:l_frm.style.height;
		l_size=parseInt(l_size,10);
		l_size=(v_sign=="+")?l_size+v_range:l_size-v_range;
		if (v_direction=="h"&&l_size<220) l_size=220;
		if (v_direction=="h"&&l_size>400) l_size=400;
		if (v_direction=="v"&&l_size<30) l_size=30;
		if (v_direction=="v"&&l_size>800) l_size=800;
		if (v_direction=="h")
		{
			l_obj.style.width=l_size;
			l_frm.style.width=l_size;
			document.forms["f"].elements["minwidth"].value=l_size;
		}
		else
		{
			l_frm.style.height=l_size;
			document.forms["f"].elements["height"].value=l_size;
		}
		return false;
	},
	newVar:function()
	{
		var l_parent=$p.get("vararea");
		var l_obj=document.createElement("TABLE");
		l_s="<TR><TD>nom : <input ";
		l_s+="</TD><TR>";
		l_parent.appendChild(l_obj);
		l_obj.innerHTML=l_s;
		vars.push(l_obj)
	},
	delVar:function(v_id)
	{
		var l_parent=$p.get("vararea");
		l_parent.removeChild(vars[v_id]);
	}
}


$p.tutorial.expert={
    containsURL:false,
	defCode:'',
    /*
                    Function: $p.tutorial.expert.getFields
                                    Get a widget code (from database or javascript)
                                     
                    Parameters:
                                
                                    v_fct - function called when cancel
                    
                    Returns:
                            
                                    false
          */
	getFields:function(v_fct)
	{

        var xmlcode = $p.string.textToXml(
                    $p.tutorial.widgetParameters["code"]
                    );
        $p.tutorial.expert.validFields(xmlcode);
	},
    /*
                    Function: $p.tutorial.expert.validFields
                    
                                 Control the XML
                    
                    Parameters:
                        
                                vars[0] - id
                                vars['fct']- hash key of the menu ($p.tutorial.menus.menusProcess)
           */     
	validFields:function(response,vars)
	{
        var excludeLinks = new Array();
        $p.tutorial.currentStep='configurer';
        excludeLinks.push("valider");
        $p.tutorial.buildBreadCrumbs(excludeLinks);
        $p.tutorial.widgetParameters["defvar"]='';

		var result,gErr=false,l_s="<table cellpadding='2' cellspacing='0' border='0'>";
		if (response.getElementsByTagName("ModulePrefs")[0]) {
        
			result=response.getElementsByTagName("ModulePrefs")[0];
			var title=$p.ajax.getProp(result,"title","str",false,lg("titleMissing"));
			var desc=$p.ajax.getProp(result,"description","str",false,lg("descMissing"));
			var height=$p.ajax.getProp(result,"height","str",false,"100");
			if (response.getElementsByTagName("Content")[0]) var modType=$p.ajax.getProp(response.getElementsByTagName("Content")[0],"type","str",false,"html");
			l_s+="<tr><td>"+$p.img("ico_validated.gif")+"</td><td>"+lg("modCaract")+" :</td></tr>";
			l_s+="<tr><td></td><td><table cellpadding='1'>";
			l_s+='<tr><td>'+lg("title")+' : </td><td id="tdwidtitle"><input type="text" name="title" id="widtitle" value="'+title+'" size="30" maxlength="30" />  </td></tr>';
			l_s+='<tr><td>'+lg("desc")+' : </td><td><textarea name="desc" id="widdesc" cols="30" rows="5">'+desc+'</textarea></td></tr>';
			l_s+='<tr><td>'+lg("height")+' : </td><td><input type="text" name="heig" value="'+height+'" size="3" maxlength="3" /></td></tr>';
			l_s+="<tr><td>"+lg("moduleType")+" : </td><td>"+(modType?modType:"html")+"<input type='hidden' name='type' value='"+(modType?modType:"html")+"' /></td></tr>";
			l_s+="<tr><td>"+$p.tutorial.expert.displayWidgetTypeOption()+"</td></tr>";
			l_s+="<tr><td colspan='2'><br />"+$p.tutorial.displayKeywordsInput()+"</td></tr>";
			l_s+="<tr><td colspan='2'><br />"+$p.tutorial.displayAutoRefeshOption()+"</td></tr>";
			l_s+="</table></td></tr>";
		}
		else {
			l_s+="<tr><td>"+$p.img("ico_suppress.gif")+"</td><td>"+lg("propertiesMissing")+"</td></tr>";
            gErr=true;
		}
        $p.tutorial.locales.getLocales(response,vars);

        var langListe =$p.tutorial.locales.displayLang();
        l_s+="<tr><td>&nbsp;</td><td>"+langListe+"</td></tr>";
		l_s+="<tr><td>&nbsp;</td><td></td></tr>";
        $p.tutorial.widgetUserPrefsString='';
        $p.tutorial.widgetParameters["nbvariables"]=0;
		if (response.getElementsByTagName("UserPref")[0]) {
			l_s+="<tr><td>"+$p.img("ico_validated.gif")+"</td><td>"+lg("someOptions")+" :</td></tr><tr><td></td><td>";
			var i=0,err=false;
			while (response.getElementsByTagName("UserPref")[i])
			{
				result=response.getElementsByTagName("UserPref")[i];
				l_s+="<u>"+lg("variable")+" "+(i+1)+"</u> : ";
                
				var l_name=$p.ajax.getProp(result,"name","str",false,indef);
                //$p.tutorial.widgetUserPrefs[i];
                
				if (l_name==indef) {
					l_s+=" <font color='#ff0000'>"+lg("varNameMissing")+"</font>";
					err=true;
				}
				else 
                {
					l_s+=' '+l_name+'<input type="hidden" name="varname'+i+'" value="'+l_name+'" />';
					var l_dispname=$p.ajax.getProp(result,"display_name","str",false,l_name);
					l_s+=' ('+l_dispname+')<input type="hidden" name="vardname'+i+'" value="'+l_dispname+'" />';
					var l_defval=$p.ajax.getProp(result,"default_value","str",false,"");
					var l_datatype=$p.ajax.getProp(result,"datatype","str",false,"string");
					l_s+=" type:"+l_datatype+"<input type='hidden' name='vartype"+i+"' value='"+l_datatype+"' />";
                    $p.tutorial.widgetParameters["nbvariables"]+=1;
                    $p.tutorial.widgetParameters["defvar"]+=l_name+"="+l_defval+"&"   
                    $p.tutorial.widgetUserPrefsString+='<UserPref name="'+ l_name +'" ';
                    $p.tutorial.widgetUserPrefsString+= 'datatype="'+l_datatype+'" ';
                    $p.tutorial.widgetUserPrefsString+= 'default_value="'+l_defval+'" ';
                    $p.tutorial.widgetUserPrefsString+= 'display_name="'+l_dispname+'" ';
                    $p.tutorial.widgetUserPrefsString+= '>' + "\r\n";
					if (l_datatype=="enum") {
						var j=0,l_enum,result2;
						l_s+=" (";
						while (result.getElementsByTagName("EnumValue")[j])
						{
							result2=result.getElementsByTagName("EnumValue")[j];
							l_enum=$p.ajax.getProp(result2,"value","str",false,indef);
							if (l_enum==indef) {
								l_s+=" <font color='#ff0000'>"+lg("varValueMissing",(j+1))+"</font>";
								err=true;
							}
							else {
								l_s+='<input type="hidden" name="val'+i+'_'+j+'" value="'+l_enum+'" />';
								if (j==0&&l_defval=="") l_defval=l_enum;
								l_s+=(l_enum==l_defval)?"<span style='font-weight:bold'>":"<span>";
								var l_enumd=$p.ajax.getProp(result2,"display_value","str",false,l_enum);
								l_s+=l_enumd+'<input type="hidden" name="vald'+i+'_'+j+'" value="'+l_enumd+'" />';
								l_s+="</span>,";
                                $p.tutorial.widgetUserPrefsString+='<EnumValue value="'+l_enum+'" display_value="'+l_enumd+'" />'+"\r\n";
							}
							j++;
						}
						l_s+=")";
					}
					else {
						if (l_defval!="") {
                            l_s+=" ("+l_defval+")";
                        }
					}
                    $p.tutorial.widgetUserPrefsString+='</UserPref>'+"\r\n";
					l_s+='<input type="hidden" name="vardef'+i+'" value="'+l_defval+'" />';
				}
				i++;
				l_s+="<br />";
			}
			l_s+="<br /></td></tr>";
			if (err) {
				l_s=l_s.replace("ico_validated.gif","ico_suppress.gif");
				gErr=true;
			}
		}
		else {
			l_s+="<tr><td>"+$p.img("ico_validated.gif")+"</td><td>"+lg("noOptions")+"</td></tr>";
		}

		l_s+="<tr><td>&nbsp;</td><td></td></tr>";

		if (response.getElementsByTagName("Content")[0]) {
			l_s+="<tr><td>"+$p.img("ico_validated.gif")+"</td><td>"+lg("contentOk")+"</td></tr>";
			if (modType=="html") {
				document.forms["f"].modcontent.value=$p.ajax.getVal(response,"Content","str",false,"");
			}
			else {
				document.forms["f"].modcontent.value=$p.ajax.getProp(response.getElementsByTagName("Content")[0],"href","str",false,"");
			}
		}
		else {
			l_s+="<tr><td>"+$p.img("ico_suppress.gif")+"</td><td>"+lg("contentNok")+"</td></tr>";
			gErr=true;
		}
	
		l_s+="<tr><td>&nbsp;</td><td></td></tr>";

		l_s+="</table>";
		if (!gErr) l_s+='<input type="button" value="'+lg('nextStep')+'>>>" onclick="$p.tutorial.updateTabWidgetInformations();eval($p.tutorial.breadCrumbs[\'placer\']);return false;" />';
		//l_s+=" <a href='#' onclick=\"return $p.url.openLink('"+tutorial['expert']+"'?id="+vars+"')\"> "+lg("startAgain")+"</a>";  
        //l_s+='&nbsp; &gt; <a href="#" onclick="'+ $p.tutorial.breadCrumbs['creer'] +'"> '+lg("startAgain")+'</a>';
        
		$p.print("loadstat",l_s);
        if ($p.tutorial.locales.langs.length > 0 ) {
            $('widtitle').disabled=true;
            $('widdesc').disabled=true;
            $('tdwidtitle').innerHTML+=tooltip("l10nTitleDescHelp");
        }
	},
	erase:function(reset)
	{
		if ($p.tutorial.expert.defCode=='' || reset)
		{
			$p.tutorial.expert.getCode('_template');
		}
		else
		{	
			document.forms["test"].elements["modcode"].value=$p.tutorial.expert.defCode;
		}
		return false;
	},
	getCode:function(v_id)
	{
		getXml("../modules/quarantine/module"+v_id+".xml",$p.tutorial.expert.showCode,"","html");
	},
	showCode:function(response,vars)
	{
		document.forms["test"].elements["modcode"].value=response;
		$p.tutorial.expert.defCode=response;
	},
	/*  
	Function: $p.tutorial.expert.addJavascript
                            Add javascript to the code writen
                            
	Parameters: 
    
                            (string)  Javascript content
	*/
	addJavascript:function(v_javascript)
	{
		var l_code=document.forms["test"].elements["modcode"].value;
		var l_pos=l_code.indexOf('</script>');
		document.forms["test"].elements["modcode"].value=l_code.substr(0,l_pos)+v_javascript+l_code.substr(l_pos,l_code.length);
	},
	use_fckEditor:function(txtAreaName)
	{
			//$p.tutorial.expert.formatKeywords();		
			var oFCKeditor=new FCKeditor(txtAreaName);
			//var oFCKeditor=new FCKeditor('desc');
			var sBasePath=__LOCALFOLDER;
			oFCKeditor.BasePath=sBasePath+'tools/fckeditor/';
			oFCKeditor.Config['CustomConfigurationsPath']=sBasePath+'includes/fckconfig.js';
			//oFCKeditor.Width ='100%';
			oFCKeditor.Width ='800';
			oFCKeditor.Height='500';
			//oFCKeditor.ToolbarSet='portaneo';
			//oFCKeditor.Events.AttachEvent( 'OnChange' , $p.tutorial.expert.FCKeditor_OnComplete);
			oFCKeditor.ReplaceTextarea();
	},
	FCKeditor_OnComplete:function( editorInstance ){
        editorInstance.LinkedField.form.onsubmit = doSave;
	},
	FCKeditor_OnFocus:function( editorInstance )
	{
		editorInstance.ToolbarSet.Expand() ;
	},
	formatKeywords:function()
	{
		//pbm ici!!!
		//document.forms['editmodule'].kwformated.value=$p.string.formatForSearch(document.forms['editmodule'].kw.value);
		document.forms['test'].modcode.value=$p.string.formatForSearch(document.forms['test'].modcode.value);
		return false;
	},
	/*  
	Function: $p.tutorial.expert.displayTestOption
                            Display the "test"  button
                            
	Returns: 
    
                            HTML code
	*/
	displayTestOption:function()
	{
		var l_s='<form name="testsend" method="post" action="'+tutorial["testmodule"]+'" target="testpage" onsubmit="$p.tutorial.expert.test.check(); return false;">\
			<img src="../images/ico_accept.gif" align="absmiddle" />\
            <input type="submit" value="'+lg("test")+'" />\
			<input type="hidden" name="testcode" /><input type=hidden name="testvars" /></form>';
		return l_s;
	},
    /*  
	Function: $p.tutorial.expert.displayEraseOption
                            Display the "erase"  button
                            
	Returns: 
    
                            HTML code
	*/
	displayEraseOption:function()
	{
		var l_s='<br /><img src="../images/ico_stop.gif" align="absmiddle" /> <input type="button" onclick="$p.tutorial.expert.erase();" value="'+lg('erase')+'" /><br /><br />';
		return l_s;
	},
    /*  
	Function: $p.tutorial.expert.displayWidgetTypeOption
                            Display the widget type (div/iframe)  options
                            
	Returns: 
    
                            HTML code
	*/
	displayWidgetTypeOption:function()
	{
		var l_s='';
		var v_checkedIframe='checked';
		var v_checkedDIV='';
        var i=0;
        $p.tutorial.widgetParameters["code"] = $p.string.trim(
                                                $p.tutorial.widgetParameters["code"]
                                                );
        var xmlcode = $p.string.textToXml($p.tutorial.widgetParameters["code"]);
        var totalContents=xmlcode.getElementsByTagName("Content").length;
		while (xmlcode.getElementsByTagName("Content")[i])
		{
			if (xmlcode.getElementsByTagName("Content")[i].getAttribute("type")=="url") {
                $p.tutorial.expert.containsURL=true;
                $p.tutorial.widgetParameters['format']='I';
            }
			i++;
		}
        
		if($p.tutorial.widgetParameters['format']=='M') {
			v_checkedDIV='checked';
			v_checkedIframe='';
		}
		
		if ($p.app.user.type=='A')  {
			l_s+='<br /><br />';
			l_s+='<input type="radio" name="format" value="I" id="iframe" '+v_checkedIframe+' /><label for="iframe">'+lg('iframe')+'</label><br />';
            if(!$p.tutorial.expert.containsURL) {
                l_s+='<input type="radio" name="format" value="M" id="m" '+v_checkedDIV+' /><label for="m">'+lg('integratedInPage')+'</label>';
            }
		}
		else { 
            l_s+='<input type="hidden" name="format" value="I" />';
        }

		return l_s;
	},
    EditorMode:null,
    displayEditorMode: function () {
        $p.tutorial.breadCrumbs['creer']="document.location.href='#headlink';$p.tutorial.expert.displayExpertMode('novice');";
        $p.tutorial.expert.EditorMode='novice';
        var myregexp = /YOUR HTML/im;
        if ( $p.tutorial.xmlTemplate['content'].match(myregexp) ) {
            $p.tutorial.xmlTemplate['content']='';
        }
        $p.tutorial.expert.displayExpertMode('novice');
    },
    previewInPopup: function (width) {
        return '\
            <div id="widgetcontainer" style="width:'+(width-30)+'px;"></div>\
        ';
    },
    iframeHelp: function () {
       return '<iframe src="../l10n/'+__lang+'/notice_expert.html" width="95%" height="400"></iframe>';
    },
    /*
                    Function:   $p.tutorial.expert.displayExpertMode
                    
                                      Display the editing mode of the widget
                            
                    Parameters:
                    
                                      editMode - editing mode (1:novice,0:expert)
                    
                    Returns:
                    
                                    false
          */
	displayExpertMode:function(editMode)
	{
        if ( editMode == "") {
            $p.tutorial.expert.EditorMode=null;
        }
        $p.tutorial.expert.containsURL=false;
        $p.tutorial.breadCrumbs['creer']='$p.tutorial.expert.displayExpertMode(editMode)'; 
	    var excludeLinks = new Array();
        
        var tmpcode = $p.tutorial.widgetParameters["code"];
    //    $p.tutorial.widgetParameters={};
        $p.tutorial.initWidgetParameters();
        $p.tutorial.widgetParameters["code"] = tmpcode;
        $p.tutorial.emptyContent();
    
        //$p.tutorial.initBreadCrumbs();
        $p.tutorial.currentStep='creer';
        excludeLinks.push("creer");
        $p.tutorial.buildBreadCrumbs(excludeLinks);
		$p.tutorial.widgetParameters["editmode"]=editMode;

        var help = '<img src="../images/ico_help2.gif" align="absmiddle" />&nbsp; <a href="../l10n/'+__lang+'/notice_expert.html" target="_blank" >'+lg('modDevGuide')+'</a>&nbsp;';
			help += '&nbsp;<a href="#" onclick="$p.app.popup.show($p.tutorial.expert.iframeHelp(),800,indef,\'help\',true);return false;"  ><img src="../images/ico_popup.gif" align="absmiddle" /></a>';    
        //var help='<img src="../images/ico_help2.gif" align="absmiddle" /><a href="../l10n/'+__lang+'/notice_expert.html" target=_blank>'+lg('modDevGuide')+'</a>&nbsp; ';
        var buttons = '\
            <input type="button" onclick="$p.tutorial.simpleWidgetPreview(\''+editMode+'\');return false;" name="tester" value="tester">\
            <input type="button" onclick=\'$p.tutorial.expert.cancelWidgetCode("'+editMode+'");return false;\' name="'+lg('cancel')+'"  value="'+lg('cancel')+'" />\
			<input type="button" onclick=\'$p.tutorial.expert.nextStep("'+editMode+'");eval($p.tutorial.breadCrumbs["configurer"]);return false;\' name="'+lg('lblSave')+'" value="'+lg('nextStep')+'>>>" />';
		var l_s='';
        //<div id="widgetcontainer"></div>
		l_s+='\
            <div id="testdiv">\
            <div class="tutformheader">'
            +buttons+
            '<div id="helpContent">'+help+'</div>\
            <div id="addContent"></div>\
            </div><div id="tutform">\
			<form name="test">\
            <textarea name="modcode" onchange="$p.tutorial.expert.disableLinks();" onfocus="$p.tutorial.expert.disableLinks();"  id="modcode" cols="150" rows="37" >'
            +$p.tutorial.expert.getWidgetCode(editMode)+'</textarea>\
			</form></div>';
		l_s+='<div class="tutformheader">'
            +buttons+
			'</div>';
	
		$p.print('content',l_s);

		if(editMode=="novice") {
            $p.tutorial.expert.use_fckEditor("modcode");            
        } else {
            $p.tutorial.breadCrumbs['creer']="document.location.href='#headlink';$p.tutorial.expert.displayExpertMode('expert');";
        }
        //remplacer le onclick du champ créer par le onclick de cet élément
        //  $p.tutorial.menus.setWidgetType('expert');$p.tutorial.expert.displayExpertMode('expert');document.location.href='#headlink';return false;
        // $p.tutorial.breadCrumbs['creer']="$p.tutorial.expert.displayExpertMode();document.location.href='#headlink';";
		$p.tutorial.expert.disableLinks( );
	},
    nextStep: function (editMode) {
        $p.tutorial.expert.setWidgetCode(editMode);
        $p.tutorial.expert.prepareBeforeNextStep(editMode);
    },
	setWidgetCode:function(editMode)
	{	
        if (editMode=="expert") {
            // if type url save format to U and save url            
			$p.tutorial.widgetParameters["code"]=$p.string.trim(document.forms["test"].modcode.value);
            $p.tutorial.widgetParameters["code"] = $p.string.trim(
                                        $p.tutorial.widgetParameters["code"]
                                        );
            var xmlcode = $p.string.textToXml($p.tutorial.widgetParameters["code"]);
            $p.tutorial.controls.checkFormat(xmlcode);
            if ( $p.tutorial.widgetParameters["type"] == "html" && $p.tutorial.widgetParameters["validate"]=="" ) {
                $p.tutorial.widgetParameters["url"]=tutorial["xml_getwidget"];
            }
            var moduletag=xmlcode.getElementsByTagName("Module")[0];
            if (moduletag.getElementsByTagName("Content").length > 1) {
                    //url type must be xml_get_widget
                   $p.tutorial.widgetParameters["url"]=tutorial["xml_getwidget"]; 
                   $p.tutorial.widgetParameters["type"]="html";
                   $p.tutorial.widgetParameters["format"] = 'I';
               }
		} else {
			$p.tutorial.xmlTemplate['content']=FCKeditorAPI.GetInstance('modcode').GetXHTML();
			$p.tutorial.widgetParameters["code"]= $p.tutorial.xmlTemplate['dtd']
												+$p.tutorial.xmlTemplate['header']
												+$p.tutorial.xmlTemplate['content']
												+$p.tutorial.xmlTemplate['footer'];
            $p.tutorial.widgetParameters["url"]=tutorial["xml_getwidget"];
		}
    },
    /*
                function: $p.tutorial.expert.prepareBeforeNextStep
    */
    prepareBeforeNextStep: function (editMode) {
		$p.tutorial.expert.enableLinks();
        if ($("savewidgetcode")) {
            $("savewidgetcode").disabled=true;
        }
		//$p.tutorial.download.checkXML(editMode);
		//window.parent.$p.app.alert.show(lg('lblSave'));
	},
	getWidgetCode:function(editMode)
	{
		if (editMode=="expert") {
			return $p.tutorial.widgetParameters["code"];
		} else {
            var myregexp = /YOUR HTML/im;
            if ( $p.tutorial.xmlTemplate['content'].match(myregexp) ) {
                return '';  
            } else {
                return  $p.tutorial.xmlTemplate['content'];
            }
        }
		//$p.tutorial.expert.erase();
	},
	/*
		Function: $p.tutorial.expert.displayPictoChoice
                                      Display the choices of the pictogram
	*/
	displayPictoChoice:function()
	{
        $p.app.tabs.sel=999;
        //$p.tutorial.initBreadCrumbs();
        $p.tutorial.currentStep='picto';
        var excludeLinks = new Array();
        excludeLinks.push("valider");
        $p.tutorial.buildBreadCrumbs(excludeLinks);
		$p.tutorial.expert.getTempDirItemId();
	},
	getTempDirItemId:function()
	{
		$p.tutorial.emptyContent();
		var l_s='<table cellpadding="10" cellspacing="0" border="0" width="100%">\
			<tr>\
			<td>\
			<iframe id="iconchoice" width="800" src="'+tutorial["frm_icon_choice_upload"]
				+'?mid='+$p.tutorial.widgetParameters['id']
				+'&val='+$p.tutorial.widgetParameters['validate']
				+'&icon='+$p.tutorial.widgetParameters['icon']
				+'" width="600" height="300" frameborder="0"></iframe>\
			</td>\
			</tr>\
			</table>';
		
		$p.print('content',l_s);
	},
    /*
                    Function:   $p.tutorial.expert.disableLink
                                    
                                       Disable links for creating widget
          */
	disableLinks:function()
	{
        var enabledLinks = new Array();
        enabledLinks.push("choisir");
		$p.tutorial.buildBreadCrumbs("",enabledLinks);
	},
    /*
                    Function:   $p.tutorial.expert.enableLink
                                    
                                       Enable links for creating widget

          */
	enableLinks:function()
	{
		var enabledLinks = new Array();
		enabledLinks.push("choisir","configurer","visualiser","placer","tester","ajouter","picto","valider");
		$p.tutorial.buildBreadCrumbs("",enabledLinks);
	},
    /*
                    Function:   $p.tutorial.expert.cancelWidgetCode
                                    
                                       Reset the editor of the widget in expert and novice mode
                            
                    Parameters:
                    
                                      editMode - editing mode (1:novice,0:expert)
                    
          */
	cancelWidgetCode:function(editMode)
	{
		if (editMode=="expert") {
			$p.tutorial.widgetParameters["code"]= $p.tutorial.xmlTemplate['dtd']
												+$p.tutorial.xmlTemplate['header']
												+$p.tutorial.xmlTemplate['content']
												+$p.tutorial.xmlTemplate['footer'];
			document.forms["test"].elements["modcode"].value=$p.tutorial.widgetParameters["code"];
		} else {
			$p.tutorial.xmlTemplate['content']="";
			$p.tutorial.widgetParameters["code"]= $p.tutorial.xmlTemplate['dtd']
												+$p.tutorial.xmlTemplate['header']
												+$p.tutorial.xmlTemplate['content']
												+$p.tutorial.xmlTemplate['footer'];
            if (  $p.tutorial.expert.EditorMode == "novice") {                                    
                FCKeditorAPI.GetInstance('modcode').SetHTML($p.tutorial.xmlTemplate['content']);
            } else {
                FCKeditorAPI.GetInstance('modcode').SetHTML("");
            }
		
        }
	},
	/*
                    Function:   $p.tutorial.expert.widgetConfig
                                    
				Display the configuration interface of the widget
									   
		returns:
        
                                            HTML code
          */
    widgetConfig:function()
    {
        var l_s="";
        $('content').empty();
        //$p.tutorial.initBreadCrumbs();
        $p.tutorial.currentStep='placer';
        var excludeLinks = new Array();
        //excludeLinks.push("creer");
        $p.tutorial.buildBreadCrumbs(excludeLinks);    
        l_s+=$p.tutorial.expert.displayWidgetTypeOption();
        $p.print('content',l_s);
	}
}

/*

    Class: $p.tutorial.expert.test
    
        test widget
*/
$p.tutorial.expert.test={
    /*
                Function: $p.tutorial.expert.test.open
                
                    display testdiv where is set the widget to test
        */
	open:function()
	{
		($p.get("testdiv")).style.display="block";
		return false;
	},
    /*
                Function: $p.tutorial.expert.test.clode
                
                    hide testdiv where is set the diget to test
    
        */
	close:function()
	{
		($p.get("testdiv")).style.display="none";
		return false;
	},
	check:function()
	{
		var l_s=$p.string.trim(document.forms["test"].modcode.value);
		var l_height=200,l_content="No &lt;Content&gt; Tag !! Pas de tag &lt;Content&gt; !!";
		var l_xml;

		if (document.implementation.createDocument)
		{ 
			var parser = new DOMParser(); 
			l_xml = parser.parseFromString(l_s, "text/xml"); 
		}
		else if (window.ActiveXObject)
		{ 
			l_xml = new ActiveXObject("Microsoft.XMLDOM"); 
			l_xml.async="false";
			l_xml.loadXML(l_s);
		}

		if (l_xml.getElementsByTagName("ModulePrefs")[0])
		{
			if (l_xml.getElementsByTagName("ModulePrefs")[0].getAttribute("height"))
			{
				l_height=parseInt(l_xml.getElementsByTagName("ModulePrefs")[0].getAttribute("height"));
			}
		}
		window.open("testmodule_wait.html", "testpage", "height="+(l_height+20)+",width=400,menubar=0,toolbar=0,location=0,status=0,scrollbars=0,resizable=1");

		var i=0,l_vars="";
		while (l_xml.getElementsByTagName("UserPref")[i])
		{
			var result=l_xml.getElementsByTagName("UserPref")[i];
            
			l_vars+=result.getAttribute("name")+"=";
			if (result.getAttribute("default_value")) l_vars+=result.getAttribute("default_value");
			l_vars+="&";
			i++;
		}
        if (document.forms["testsend"]) {
            document.forms["testsend"].testvars.value=l_vars;
        }
		l_content=$p.ajax.getVal(l_xml,"Content","str",false,"");

		l_contentObj=l_xml.getElementsByTagName("Content")[0];
		l_type=$p.ajax.getProp(l_contentObj,"type","str",false,"html");
		if (l_type=="html")
		{
			//l_content=l_content.replace(/\<\?/g,"");
			l_content=l_content.replace(/__MODULE_ID__/g,"0");
		}
		else if (l_type=="url")
		{
			l_url=$p.ajax.getProp(l_contentObj,"href","str",false,"");
			l_content="<iframe src='"+l_url+"' frameborder='0' width='100%' height='100%'></iframe>";
		}
		document.forms["testsend"].testcode.value=l_content;
		return true;
	}
}

$p.tutorial.locales = {
    langs:new Array(),
    nblangs:0,
    /*
                function: $p.tutorial.locales.getLocales
                    
                    get list of lang available in widget xml code
                    
               parameters:
               
                response - xml response
                
                vars - vars from cllaback
                
                see:
                
                $p.tutorial.locales.getLocales
                
    */
    getLocales: function (response,vars) {
        var hasEnDefined=false;
        $p.tutorial.locales.nblangs=0;
        $p.tutorial.locales.langs = new Array();
        if (response.getElementsByTagName("Locale")) {
           var nblocale = response.getElementsByTagName("Locale").length;
           for (var i =0; i < nblocale;i++) {
                result=response.getElementsByTagName("Locale")[i];
                var lang = $p.ajax.getProp(result,"lang","str",false,'');
                var messages = $p.ajax.getProp(result,"messages","str",false,'');
                if (lang != 'undefined' 
                    && lang!='' 
                    && messages!='undefined' 
                    && messages!='') {
                        $p.tutorial.locales.langs.push(lang);
                        $p.tutorial.locales.nblangs++;
                        if (lang=='en') { hasEnDefined=true; }
                } 
           }
           if (hasEnDefined==false 
               && $p.tutorial.locales.nblangs!=0) {
                   $p.tutorial.locales.langs.push('en');
                   $p.tutorial.locales.nblangs++;                   
           }
        }
    },
    /*
                function: $p.tutorial.locales.getLocalesInternal
                    
                    get list of lang available in widget xml code
                    
                    without any request to server, xml come from javascript tutorial
                    
               parameters:
               
                response - xml response
                
                vars - vars from cllaback
                
                see:
                
                $p.tutorial.expert.validFields
                
    */    
    getLocalesInternal: function () {
        $p.tutorial.locales.nblangs=0;
        $p.tutorial.widgetParameters["code"] = $p.string.trim(
                                        $p.tutorial.widgetParameters["code"]
                                        );
        var xmlcode = $p.string.textToXml($p.tutorial.widgetParameters["code"]);
        if (xmlcode.getElementsByTagName("Locale")) {
           var nblocale = xmlcode.getElementsByTagName("Locale").length;
           $p.tutorial.widgetParameters["l10n"]='';
           for (var i =0; i < nblocale;i++) {
                result=xmlcode.getElementsByTagName("Locale")[i];
                var lang = $p.ajax.getProp(result,"lang","str",false,'');
                var messages = $p.ajax.getProp(result,"messages","str",false,'');
                if (lang != 'undefined' 
                    && lang!='' 
                    && messages!='undefined' 
                    && messages!='') {
                    $p.tutorial.widgetParameters["l10n"]+=lang+',';
                    $p.tutorial.locales.nblangs++;
                }                 
           }
        }
    },
    hashlangs: new Object(),
    /*
            function: $p.tutorial.locales.displayLang
            
                display list of lang and checkbox to save it
        
    */
    displayLang: function () {
        
        for (var i=0;i<__AVLANGS.length;i++ ) {
            var lang = __AVLANGS[i];
            $p.tutorial.locales.hashlangs[lang]=1;
        }

        if ($p.tutorial.locales.nblangs==0) {
            return lg("noLanguages");
        }
        l_s=lg("availLangs")+'<br />';
        var cols=false;
        if ($p.tutorial.locales.nblangs >= 10) {
            cols=true;
            l_s += '<div style="float:left">';
        }
        
        for (var i =0; i < $p.tutorial.locales.nblangs;i++) {
            var lang = $p.tutorial.locales.langs[i];
            var checked = '';
            if ($p.tutorial.locales.hashlangs[lang]==1) {  
                checked=" checked";
				if( $p.tutorial.widgetParameters["l10nSelected"].indexOf(lang+",") == -1 ) {
					$p.tutorial.widgetParameters["l10nSelected"] += lang+',';
				}
            } else {
                $p.tutorial.locales.hashlangs[lang]=2;
            }
            if (lang && lang!='')  {

                if (cols && i%10==0) {
                    l_s += '</div><div class="tutlangcols">';
                }
                l_s +=  '<p class="tutlangpara">'
                        + '<span class="colright">'
                        + '&nbsp;:&nbsp;<input type="checkbox" onchange="$p.tutorial.locales.savedLocale(\''+lang+'\')" value="'+lang+'" name="tutl10n" id="tutl10n'+lang+'" '+checked+'>'
                        + '</span>'
                        + lang + "&nbsp;</p>";
            }       
        }
        if (cols) {
            l_s += "</div>";
        }
        l_s += '';
        return l_s;
    },  
    /*
    
            $p.tutorial.locales.savedLocale
            
                save lang
    */
    savedLocale: function ( lang ) {
		if( $p.tutorial.widgetParameters["l10nSelected"].indexOf(lang+",") == -1 ) {
			if (lang != 'undefined' && lang!='') {
				$p.tutorial.widgetParameters["l10nSelected"] += lang+',';
	        }
		} else {
			$p.tutorial.widgetParameters["l10nSelected"] = $p.tutorial.widgetParameters["l10nSelected"].replace(lang+",","");
		}
    }
}

$p.tutorial.save = {
    initSavePage: function () {
        $p.app.tabs.sel=999;
        //$p.tutorial.initBreadCrumbs();
        $p.tutorial.currentStep='valider';
        var excludeLinks = new Array();
        //excludeLinks.push("creer");
        $p.tutorial.buildBreadCrumbs(excludeLinks);
		$p.tutorial.save.displaySavePage(); 	
    },
    displaySavePage: function () {
        $p.tutorial.emptyContent();
        var l_s='<div><div id="saveMessage"></div><div id="displaySave"></div></div>';
		$p.print('content',l_s);
        $p.app.wait('displaySave');
        $p.tutorial.widgetParameters["status"]="N";
        $p.tutorial.save.displayAlert=1;
        $p.tutorial.locales.getLocalesInternal();
        $p.tutorial.save.saveInServer();
    },
    /*
                function:  $p.tutorial.save.saveInServer
            
                        returns id of widget
    $p.tutorial.widgetParameters["format"]
            */
    saveInServer: function () {
        var params='insert=1'; 
        for ( param in  $p.tutorial.widgetParameters ) {
            if (param == "code" || param == 'name' || param == "description" ) {
                params += "&"+param+"="
                    + $p.string.esc($p.tutorial.widgetParameters[param]);
            } else if (param == 'defvar') {
                params += "&defvar="+ $p.string.esc($p.tutorial.widgetParameters["defvar"]);    
            } else {
                params += "&"+param+"="
                    + $p.tutorial.widgetParameters[param];
            }
        }
        $p.ajax.call(tutorial["scr_savewidget"],
                {
                    'type':'load',
                    'variables':params,
                    'forceExecution':true,
                    'method':'post',
                    'alarm':true,
                    'callback':{
                        'function':$p.tutorial.save.afterSaved
                    }
                    
             }
        );      
    },
    afterSaved: function (response,vars) {
        var dir_id;
        var codeError="";
        codeError = $p.ajax.getVal(response,"unsecured","str",false,""); 
        if (codeError!="") {
            window.parent.$p.app.alert.show(codeError);
            $p.tutorial.widgetParameters["code"] = $p.tutorial.xmlTemplate['dtd']
                                                 + $p.tutorial.xmlTemplate['header']
                                                 + $p.tutorial.xmlTemplate['content']
                                                 + $p.tutorial.xmlTemplate['footer'];
        }
        else {
            if (response.getElementsByTagName("dir_id")[0]) {
                dir_id = $p.ajax.getVal(response,"dir_id","int",true,0);                    
                if (dir_id) {       
                    $p.tutorial.widgetParameters["validate"]="";
                    $p.tutorial.widgetParameters["id"] = dir_id;
                    if ($p.tutorial.save.displayAlert==1) {
                        if ( $('saveMessage') ) {
                           $('saveMessage').className="warningok";
                           $('saveMessage').style.margin="0 20% 0 10%";
                           $('saveMessage').innerHTML = lg("tutorialWidgetRegistered");
                        } else {
                            window.parent.$p.app.alert.show(lg("tutorialWidgetRegistered"),1);
                        }
                        if ($p.app.user.type=='A') {
                            window.parent.$p.admin.widgets.loadWidgetsInfos();
                        }
                    }
                } else {
                    if ($p.tutorial.save.displayAlert==1) {
                        window.parent.$p.app.alert.show(lg("tutorialWidgetNotRegistered"),3);
                    }
                }
                if (  $('displaySave') ) {
                    $('displaySave').innerHTML='';
                }
            }
            //reset displayAlert
            $p.tutorial.save.displayAlert=0;

            if ( $p.tutorial.widgetParameters["format"] == 'I' 
                || $p.tutorial.widgetParameters["format"] == 'U' 
                || $p.tutorial.widgetParameters["format"] == 'M' 
                ) {
                if ($p.tutorial.widgetParameters["type"] != "url") {
                    $p.tutorial.widgetParameters["url"] = 
                                            tutorial["xml_getwidget"]
                                            + "?getsource=1&format="
                                            + $p.tutorial.widgetParameters["format"]
                                            + "&pitem="
                                            + $p.tutorial.widgetParameters["id"]+"&";
                }
            }
            
            if ($p.tutorial.currentStep=='creer') {
                $p.app.popup.show($p.tutorial.expert.previewInPopup(600),600,indef,'test widget',true); 
            }
            if ($p.tutorial.currentStep=='visualiser') {
                $p.tutorial.displayPreview("showNextStep");
            }
            if ( $p.tutorial.action ) {
                var action=$p.tutorial.action;
                $p.tutorial.action=indef;
                $('widgetcontainer').innerHTML='';
                $p.tutorial.displayPreview();
            }
        }
    },
    displayAlert:0
}

$p.tutorial.controls = {
    checkViews: function () {
        $p.tutorial.widgetParameters["code"] = $p.string.trim(
                                        $p.tutorial.widgetParameters["code"]
                                        );
        var xmlcode = $p.string.textToXml($p.tutorial.widgetParameters["code"]);
        if (xmlcode.getElementsByTagName("Content")) {
           var nbcontent = xmlcode.getElementsByTagName("Content").length;
           $p.tutorial.widgetParameters["view"]='home';
           for (var i =0; i < nbcontent;i++) {
                result=xmlcode.getElementsByTagName("content")[i];
                var view = $p.ajax.getProp(result,"view","str",false,'home');
                if (view != 'undefined') {
                    $p.tutorial.widgetParameters["views"]+=view+',';
                }
           }
           
        }    
    },
    /*
                Function:  $p.tutorial.controls.checkFormat
                
                    check format and if type= url -> set url parameters
        */
    checkFormat: function (xmlcode) {
        if (xmlcode.getElementsByTagName('Content').length > 0) {
            var type=xmlcode.getElementsByTagName('Content')[0].getAttribute('type');
            if ( type=="url") {
               $p.tutorial.widgetParameters["type"]="url";
               $p.tutorial.widgetParameters["format"]="U";
               if ( xmlcode.getElementsByTagName('Content')[0].getAttribute('href')) {
                    $p.tutorial.widgetParameters["url"]=xmlcode.getElementsByTagName('Content')[0].getAttribute('href') + "?";
                    if ($p.tutorial.widgetParameters["url"].substring(0,7)!="http://") {
                        $p.tutorial.widgetParameters["url"]="http://"+$p.tutorial.widgetParameters["url"];
                    }
                }        
            }
        }
    }
}

/*
    class to add plugins in tutorial

*/

$p.tutorial.plugins={}

/*
	Function:  FCKeditor_OnComplete
                
                        called when the load of the editor FCKeditor is  complete

 */
function FCKeditor_OnComplete( editorInstance )
{
	editorInstance.Events.AttachEvent( 'OnFocus', $p.tutorial.expert.disableLinks) ;
	editorInstance.Events.AttachEvent( 'OnSelectionChange', $p.tutorial.expert.disableLinks) ;
	 
}

