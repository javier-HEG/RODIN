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
// Modules javascript functions
//
// !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é à ù è
// ***************************************

/*
 *  file: modules.js
 *
 *  javascript functions used by Iframe widgets
 *
 *
 */

 
var indef;
var __lang=parent.__lang;
var __LOCALFOLDER=parent.__LOCALFOLDER;

if (!__MODULE_ID__)
{
	var l_items=(window.location.search.substring(1)).split("&");
	for (var i=0;i<l_items.length;i++) 
	{
		var l_arr=l_items[i].split("=");
		if (l_arr[0]=="p")
		{
			var __MODULE_ID__=l_arr[1];
		}
	}
}

/*
    Function : __
    
            module translation

       Parameters:
       
                v_s - label to translate
 */
function __(v_s)
{
	return parent.lg(v_s);
}
/*
            function: _IG_RegisterOnloadHandler
            
            set onload handler
            
            Parameters:
            
                v_fct - function to handle
*/
function _IG_RegisterOnloadHandler(v_fct)
{
	window.onload=v_fct;
}
/*

    Function: _IG_AdjustIFrameHeight
            
            resize frame

     Parameters: 
     
        v_height -  default height
            
 */
function _IG_AdjustIFrameHeight(v_height)
{
	var l_height;
	if (v_height)
	{
		l_height=v_height;
	}
	else
	{
		if (document.all)
		{
			if (document.compatMode && document.compatMode != 'BackCompat')
			{
				l_height=document.documentElement.scrollHeight + 5;
			}
			else
			{
				l_height = document.body.scrollHeight + 5;
			}
		}
		else if (document.height)
		{
			l_height=document.height;
		}
	}
	var l_tab=parent.$p.app.tabs.sel;
	var l_id=parent.$p.app.widgets.uniqToId(__MODULE_ID__,l_tab);
	parent.tab[l_tab].module[l_id].setHeight(l_height);
}
/*

    Function: _IG_SetTitle
            
            change widget title

     Parameters: 
     
        title -  new title of the widget
            
 */
function _IG_SetTitle(title)
{
	var l_tab=parent.$p.app.tabs.sel;
	var l_id=parent.$p.app.widgets.uniqToId(__MODULE_ID__,l_tab);
	parent.$p.app.widgets.changeName(l_id,title,l_tab);
}

function _IG_MiniMessage (id) {
    this.createDismissibleMessage = createDismissibleMessage;
    this.createStaticMessage = createStaticMessage;  
}

function createDismissibleMessage (msghtml, dismiss) {
    return text;
}

function createStaticMessage (html) {
    var d = document.createElement("div");
    d.innerHTML = html;
    return d;
}
/*

    Function: _IG_Prefs
            
            get Module preferences

     Parameters: 
     
        uniq - the new iniq id of the widget
            
 */
function _IG_Prefs(uniq)
{
	this.uniq = uniq;
	this.seltab = parent.$p.app.tabs.sel;
	this.id = parent.$p.app.widgets.uniqToId(uniq,this.seltab);
	this.vars = parent.tab[this.seltab].module[this.id].vars;
	this.getString = getString;
	this.getInt = getInt;
	this.getBool = getBool;
	this.set = setPrefVal;
	this.open = openPref;
    this.addOption = addPrefOption;
    this.removeOption = removePrefOption;
    this.getMsg = getMsg;
}

function getMsg (label) {
    
    if (label == 'default_hl') {
        return __lang;
    }
    return label;
}
/*

    Function: getString
            
            Get variable value from this widget URL 

     Parameters: 
     
        v_var - variable searched
			
	Returns:
                    
                value of the variable
 */
function getString(v_var)
{
	return parent.$p.string.getVar(this.vars,v_var,'string');
}
/*

    Function: getInt
            
    Get variable value from this widget URL as an int

     Parameters: 
     
        v_var - variable searched
			
	Returns:
                    
                value of the variable (int)
 */
function getInt(v_var)
{
	return parseInt(parent.$p.string.getVar(this.vars,v_var,'int'),10);
}
/*

    Function: getBool
            
    Get variable value from this widget URL as a boolean

     Parameters: 
     
        v_var - variable searched
			
	Returns:
                    
                value of the variable (boolean)
 */
function getBool(v_var)
{
	return (parent.$p.string.getVar(this.vars,v_var)==1?true:false);
}
/*

    Function: setPrefVal
            
    set the variable searched to the input value

     Parameters: 
     
        v_var - variable searched
        v_value - new value for the variable
 */
function setPrefVal(v_var,v_value)
{
	parent.tab[this.seltab].module[this.id].changeVar(v_var,v_value);
	parent.$p.app.widgets.param.getModuleParam(this.id);
}
/*

    Function: openPref
            
		opens the preferences menu.
	
	Returns:
				false;
 */
function openPref()
{
	parent.$p.app.widgets.param.show(this.uniq);
	return false;
}
/*

    Function: addPrefOption
            
    add value to a preferences option

     Parameters: 
     
		v_selectName - the option name
		v_value - value to add
		v_displayValue - for display the value
	   
 */
function addPrefOption(v_selectName,v_value,v_displayValue)
{
    var l_tab = parent.$p.app.tabs.sel;
//	var l_id = parent.$p.app.widgets.uniqToId(__MODULE_ID__,l_tab);
    var l_select = parent.$('editboxinput_'+parent.tab[l_tab].id+'_'+__MODULE_ID__+'_'+v_selectName);
    l_select.options[l_select.options.length] = new Option(v_displayValue, v_value);
}
/*

    Function: removePrefOption
            
    remove a value from a preferences option

    Parameters: 
     
		v_selectName - the option name
		v_value - value to remove
		
	Returns:
		true if the value has been found
		false if the value has vot been found
	   
 */
function removePrefOption(v_selectName,v_value)
{
    var l_tab = parent.$p.app.tabs.sel;
    var l_select = parent.$('editboxinput_'+parent.tab[l_tab].id+'_'+__MODULE_ID__+'_'+v_selectName);
    for (var i = 0;i < l_select.options.length;i ++)
    {
        if (l_select.options[i].value == v_value)
        {
            l_select.options[i] = null;
            return true;
        }
    }
    return false;
}

// XML management

/*

    Function: _IG_FetchContent
            
		Get XML results (text)

    Parameters:
	 
		v_url (string) - url of the XML page containing data to retrieve
		v_fct(function) - callback function that will treat the results. responseXML and fct_vars are the arguments sent to this function.

 */
function _IG_FetchContent(v_url, v_fct)
{
	parent.getXml(v_url,v_fct,"","html","","GET")
}
/*

    Function: _IG_FetchXmlContent
            
		Get XML results (xml)

    Parameters:
	 
		v_url (string) - url of the XML page containing data to retrieve
		v_fct(function) - callback function that will treat the results. responseXML and fct_vars are the arguments sent to this function.
		v_vars(string) - variables sent to the xml page
		v_type (string) - "post"=send v_vars as post variables, "get" ...

 */
function _IG_FetchXmlContent(v_url, v_fct,v_var,v_type)
{
	if (v_var==indef) v_var="";
	if (v_type==indef) v_type="GET";
	parent.getXml(v_url,v_fct,"","xml",v_var,v_type);
}
/*

    Function: _IG_FetchFeedAsJSON
            
		Get XML results (xml) from an RSS feed

    Parameters:
	 
		v_url (string) - url of the XML page containing data to retrieve
		v_fct(function) - the function to execute on this page
		v_enties - number of articles to display
		v_summaries - for sumaries display

 */
function _IG_FetchFeedAsJSON(v_url,v_fct,v_entries,v_summaries)
{
	if (v_entries==indef) v_entries=5;
	if (v_summaries==indef) v_summaries=false;
	getXml(v_url,getRssFromXml,new Array(v_fct,v_entries,v_summaries),"xml","","GET");
}

/*

    Function: _IG_FetchFeedAsJSON
            
		????????????

    Parameters:
	 
		document - ?????
		mousemove - ???????
		id - ?????
 */
function _IG_AddDOMEventHandler(document, mousemove, id) {
    
}

/*

    Function: getRssFromXml
            
		launch a function with the xml results in inputs

    Parameters:
	 
		response - xml get
		vars - (v_fct - v_enties - v_summaries)

 */
function getRssFromXml(response,vars)
{
	var feed={};
	feed.Title=getXMLval(response,"title","str",false,"no title");
	feed.Link=getXMLval(response,"link","str",false,"");
	feed.Author=getXMLval(response,"author","str",false,"");
	feed.Description=getXMLval(response,"description","str",false,"");
	feed.Entry=[];
	var i=0;
	while (response.getElementsByTagName("item")[i] && i<vars[1])
	{
		var result=response.getElementsByTagName("item")[i];
		feed.Entry[i]={};
		feed.Entry[i].Title=getXMLval(result,"title","str",false,"no title");
		feed.Entry[i].Link=getXMLval(result,"link","str",false,"");
		feed.Entry[i].Summary=(getXMLval(result,"description","str",false,"")).substr(0,200);
		feed.Entry[i].Date=getXMLval(result,"pubdate","str",false,"");
		i++;
	}
	vars[0](feed);
}
/*

    Function: _IG_Tabs
            
		tabs management

    Parameters:
	 
		v_id - the widget's id
		v_selTab - the selected tab

 */
function _IG_Tabs(v_id,v_selTab)
{
	this.moduleId=v_id;
	this.selTab=v_selTab;
	this.ulObj=null;
	this.addTab=addTab;
	this.addDynamicTab=addDynamicTab;
	this.setSelectedTab=setSelectedTab;
	this.moveTab=moveTab;
}
/*

    Function: addTab
            
		add a tab

    Parameters:
	 
		v_name - name of the new tab
		v_divId - id of the div 
		v_fct - function associated to the tab selection

 */
function addTab(v_name,v_divId,v_fct)
{
	//define default tab if not already defined
	if (this.selTab==indef) this.selTab=v_name;
	//create tabs div if not already existing
	if (this.tabsContainer==null)
	{
		this.tabsContainer=document.createElement("div");
		this.tabsContainer.id="tabsdiv";
		document.body.appendChild(this.tabsContainer);
	}
	//create content div if not already existing
	if (v_divId==indef) v_divId="contentdiv";
	if (_gel(v_divId)==null)
	{
		var l_obj=document.createElement("div");
		l_obj.id=v_divId;
		document.body.appendChild(l_obj);
	}
	else
	{
		var l_obj=_gel(v_divId);
	}
	//create ul obj if not already existing
	if (this.ulObj==null)
	{
		this.ulObj=document.createElement("ul");
		this.ulObj.className="tablist";
		this.tabsContainer.appendChild(this.ulObj);
	}
	//add the new tab
	var l_liObj=document.createElement("li");
	l_liObj.className=(this.selTab==v_name)?"tabsel":"tab";
	l_liObj.id=v_name;
	
	l_aObj = document.createElement("a");
    l_aObj.appendChild(document.createTextNode(v_name));
    l_aObj.href = "javascript:void(null);";
    l_aObj.title = v_name;
	l_aObj.main = this;
	l_aObj.selDiv = v_divId;
	l_aObj.fct = v_fct;
    l_aObj.onmouseup = function(){changeSelectedTab(this.title,this.main);this.fct(this.selDiv);};
	l_liObj.appendChild(l_aObj);
	this.ulObj.appendChild(l_liObj);
	
	if (this.selTab==v_name) v_fct(v_divId);
	
	return l_obj;
}
/*

    Function: addDynamicTab
            
		add a dynamic tab

    Parameters:
	 
		v_name - name of the new tab
		v_fct - function associated to the tab selection

 */
function addDynamicTab(v_name,v_fct)
{
	addTab(v_name,v_fct);
}
/*

    Function: setSelectedTab
            
		set a default selected tab

    Parameters:
	 
		v_index - name of the default tab
		
 */
function setSelectedTab(v_index)
{
	//define selected tab
	for (var i=0;i<this.ulObj.childNodes.length;i++)
	{
		var l_node=this.ulObj.childNodes[i];
		l_node.className=(i==v_index?"tabsel":"tab");
	}
}
/*

    Function: changeSelectedTab
            
		set a default selected tab

    Parameters:
	 
		v_index - name of the default tab
		
 */
function changeSelectedTab(v_name,v_obj)
{
	v_obj.selTab=v_name;
	for (var i=0;i<v_obj.ulObj.childNodes.length;i++)
	{
		var l_node=v_obj.ulObj.childNodes[i];
		if (l_node.id==v_name) v_obj.setSelectedTab(i);
	}
}
/*

    Function: moveTab
            ??????		
 */
function moveTab(){}

/*

    Function: _IG_Callback
            
		Callback function

    Parameters:
	 
		v_fct - the callback function
		v1,v2,v3,v4,v5 - arguments
		
 */
function _IG_Callback(v_fct,v1,v2,v3,v4,v5)
{
	var args=arguments;
	return function()
	{
		var l_vars=[];
		for(var i=0;i<arguments.length;i++)
		{
			l_vars[l_vars.length]=arguments[i];
		}
		for(var i=1;i<args.length;i++)
		{
			l_vars[l_vars.length]=args[i];
		}
		v_fct.apply(null,l_vars);
	}
}

/*

    Function: _bringToFront
            
		bring widjet to front

    Parameters:
	 
		v_uniq -the uniq id of the widget
		
 */
function _bringToFront(v_uniq)
{
	var l_tab=parent.$p.app.tabs.sel;
	var l_mod=parent.parent.$p.app.widgets.uniqToId(uniq,l_tab);
	parent.tab[l_tab].module[l_mod].bringToFront();
}
/*

    Function: _gel
            
		Get object with its ID

    Parameters:
	 
		v_id - id of the element
		
 */
function _gel(v_id){return document.getElementById?(document.getElementById(v_id)?document.getElementById(v_id):null):document.all[v_id];}

// Get objects with their tag name
function _gelstn(v_t){return document.getElementsByTagName?document.getElementsByTagName(v_t):new Array();}
function _esc(v_s){return parent._esc(v_s);}
function _unesc(v_s){return parent._unesc(v_s);}
function _uc(v_s){return parent._uc(v_s);}
function _lc(v_s){return parent._lc(v_s);}
function _min(v1,v2){return parent._min(v1,v2);}
function _max(v1,v2){return parent._max(v1,v2);}
function _hesc(v_s){return parent._hesc(v_s);}
function _args(v_s){return parent._args(v_s);}

// Suppress unused blanks before string
function _trim(v_s){return parent._trim(v_s);}

//unused functions
function _IG_Analytics(v1,v2){}

/*

    Function: navPrint
            
		replace the content of an element

    Parameters:
	 
		v_id - id of the element
		v_s (string)- new content 
		
 */
function navPrint(v_id,v_s)
{
	var l_obj=_gel(v_id);
	if (l_obj) l_obj.innerHTML=v_s;
}
/*

    Function: navClass
            
		Change CSS Class of an object

    Parameters:
	 
		v_id - id of the element
		v_class - new class
		
 */
function navClass(v_id,v_class)
{
	var l_obj=_gel(v_id);
	if (l_obj) l_obj.className=v_class;
}
/*

    Function: navWait
            
		Display waiting icon in the defined object

    Parameters:
	 
		v_id - id of the element
		
 */
function navWait(v_id)
{
	navPrint(v_id,'<img src="../images/ico_waiting.gif" />');
}
/*

    Function: navShow
            
		Show/Hide object

    Parameters:
	 
		v_id - id of the element
		v_disp - display or neot the element
		
 */
function navShow(v_id,v_disp)
{
	var l_obj=_gel(v_id);
	if (l_obj) l_obj.style.display=v_disp;
}
/*

    Function: navIsShown
            
		say if the object is visible or not

    Parameters:
	 
		v_id - id of the element
			
	Returns:
		
		true if the object is visible
		false if it's not visible
		
 */
function navIsShown(v_id)
{
	var l_obj=_gel(v_id);
	if (l_obj)
	{
		return (l_obj.style.display=='block'?true:false);
	}
	else
	{
		return false;
	}
	l_obj.style.display=v_disp;
}
/*

    Function: _toggle
            
		Show an object if it's hide, hide it if it's visible

    Parameters:
	 
		v_el - the element
		
 */
function _toggle(v_el)
{
	if (el.style.display=='block')
	{
		el.style.display='none';
	}
	else
	{
		el.style.display='block';
	}
}
/*

    Function: _IG_GetImage
            
		get the url of an image

    Parameters:
	 
		v_url the image url
			
	Returns
	
		the url of the image
 */
function _IG_GetImage(v_url)
{
	return v_url;
}
/*

    Function: _IG_GetImageUrl
            
		get the url of an image

    Parameters:
	 
		v_url the image url
			
	Returns
	
		the url of the image
 */
function _IG_GetImageUrl(v_url)
{
	return v_url;
}
/*

    Function: _IG_GetCachedUrl
            
		get the url of the cache

    Parameters:
	 
		v_url the cache url
			
	Returns
	
		the url of the cache
 */
function _IG_GetCachedUrl(v_url)
{
	return v_url;
}
/*

    Function: _IG_EmbedFlash
            
		create a new flash object

    Parameters:
	 
		swf_url - url of the flash object
		swf_container - container object
		opt_params - the flash object parameters

 */
function _IG_EmbedFlash(swf_url, swf_container, opt_params)
{
	var so = new SWFObject(swf_url, "flash", "100%", "100%", "6");
	for(opt_param in opt_params)
	{
		so.addParam(opt_param,opt_params[opt_param]);
	}
    so.write(swf_container);
}
/*

    Function: _IG_GetFlashMajorVersion
            
		??????????????????????
 */
function _IG_GetFlashMajorVersion()
{
}

function _IG_RegisterMaximizeHandler () {

}

var gadgets={}

gadgets.views={
    ViewType:'home',
    View:function (mode) {
        var views = gadgets.views.getSupportedViews(); 
        return views[mode];
    },
    /*
                Function: gadgets.views.getCurrentView
                
                    get current view
    
        */
    getCurrentView: function () {
        this.getName = gadgets.views.getName;
        this.l_tab = parent.$p.app.tabs.sel;
        this.l_id = parent.$p.app.widgets.uniqToId(__MODULE_ID__,this.l_tab);
    },
    /*
                Function: gadgets.views.getName
                
                    return currentview name
        */
    getName: function () {
        var l_tab=parent.$p.app.tabs.sel;
        var l_id=parent.$p.app.widgets.uniqToId(__MODULE_ID__,l_tab);
        gadgets.views.ViewType=parent.tab[this.l_tab].module[this.l_id].currentView;
        return parent.tab[this.l_tab].module[this.l_id].currentView;
    },
    getParams: function () {
    
    },
    /*
                Function: gadgets.views.getSupportedViews
                
                        return supported view and function linked
                        
                        
        */
    getSupportedViews: function () {
        var l_tab=parent.$p.app.tabs.sel;
        var l_id=parent.$p.app.widgets.uniqToId(__MODULE_ID__,l_tab); 
        var viewTab = {
            'canvas'  :  'parent.$p.app.widgets.maximize(__MODULE_ID__,l_id)',
            'home'      : 'parent.$p.app.widgets.homeView(l_id)'
        };
        return viewTab;
    },
    url:null,
    /*
            function: gadgets.views.changeUrl
            
                change url to open any url in a full scree view
                
                parameters:
                
                    url - url
                    
        */
    changeUrl: function (url) {
        gadgets.views.url = url;
    },
    /*
                function: gadgets.views.requestNavigateTo

                        function to open a canvas content in full screen view
                  
                  parameters :
                
                        view  - type of wiew (home or canvas)
                        opt_params - 
                        opt_ownerId - 
            */
    requestNavigateTo: function (view,opt_params, opt_ownerId) {
        //modify url to add parameters (id of this widget, who become parent id, to return to old widget)
        var l_tab=parent.$p.app.tabs.sel;
        var l_id=parent.$p.app.widgets.uniqToId(__MODULE_ID__,l_tab);
        //get object then send to windows
        if (gadgets.views.url) {
            parent.tab[l_tab].module[l_id].newUrl = gadgets.views.url;
            parent.tab[l_tab].module[l_id].newFormat='U';
            gadgets.views.url=null;
        }
        eval(view);
    }
}



//open Netvibes widgets
callNetvibesWidget=function(url)
{
	/*
	License:
	  Copyright (c) 2005-2008 Netvibes (http://www.netvibes.org/).

	  This file is part of Netvibes Widget Platform.

	  Netvibes Widget Platform is free software: you can redistribute it and/or modify
	  it under the terms of the GNU Lesser General Public License as published by
	  the Free Software Foundation, either version 3 of the License, or
	  (at your option) any later version.

	  Netvibes Widget Platform is distributed in the hope that it will be useful,
	  but WITHOUT ANY WARRANTY; without even the implied warranty of
	  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	  GNU Lesser General Public License for more details.

	  You should have received a copy of the GNU Lesser General Public License
	  along with Netvibes Widget Platform.  If not, see <http://www.gnu.org/licenses/>.
	*/
	if (typeof UWA == 'undefined') UWA = {};

	UWA.iFrameMessaging = function(){}

	UWA.iFrameMessaging.prototype = {
	    _options : {},
	    
	    init: function(options){
	        var _this = this;
	        
	        if (typeof options!='object' || !options.eventHandler) {
	            return;
	        }
	        
	        this._options  = options;
	        
	        if (typeof document.postMessage === 'function' || typeof window.postMessage === 'function') {
	            window.addEventListener('message',  function(msg){
	                var origin = msg.origin;
	                if (origin){ // Common case
	                    origin = origin.split('//')[1];
	                } else { // Opera case
	                    origin = msg.domain;
	                }
	                _this.dispatch(msg.data, origin, 'postMessage');
	            }, false);
	        }
	    },
	    
	    dispatch: function(msg, msgOrigin, msgCommType){
	        var options = this._options;
	        msgOrigin = unescape(msgOrigin);
	        if (typeof options.trustedOrigin == 'undefined' || msgOrigin==options.trustedOrigin){
	            var msg = this.decodeJson(msg);
	            if (msg) {
	                msg.commType = msgCommType;
	                options.eventHandler(msg);
	            };
	        } else {
	             throw new Error('Origin ' + msgOrigin + ' is not trusted.');
	        }
	    },
	    
	    decodeJson: function(json){
	        var ret = false;
	        if ((/^[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]*$/).test(unescape(json).replace(/\\./g, '@').replace(/"[^"\\\n\r]*"/g, ''))) {
	            ret = eval('(' + unescape(json) + ')');
	        }
	        return ret;
	    }
	}
	msgHandler = function(message) {
	  var id = message.id;
	  switch (message.action) {
	    case 'resizeHeight':
	      var frame = document.getElementById('frame_' + id);
	      if (frame) {
	        frame.setAttribute('height', message.value);
			_IG_AdjustIFrameHeight(message.value);
	      }
	      break;
        default:
	   //   console.log(message.action + ': not implemented - ' + message.name + ':' + message.value);
	      break;
	  }
	};
	UWA.MessageHandler = new UWA.iFrameMessaging;
	UWA.MessageHandler.init({
	    'eventHandler': msgHandler,
	    'trustedOrigin' : 'nvmodules.netvibes.com'
	});
    //document.write("http://nvmodules.netvibes.com/widget/frame/?uwaUrl='+_esc(url)+'&id=123456&ifproxyUrl='+_esc(__LOCALFOLDER+'tools/netvibesproxy.html')+'");
	document.write('<iframe id="frame_123456" scrolling="no" frameborder="0" height="300" width="100%" src="http://nvmodules.netvibes.com/widget/frame/?uwaUrl='+_esc(url)+'&id=123456&ifproxyUrl='+_esc(__LOCALFOLDER+'tools/netvibesproxy.html')+'"></iframe>');
}

$p={}
$p.app={}
$p.app.widgets={}

$p.modules={
    
    tooltip:function(v_msg) {

        return "<a href='#' onclick='return false' onmouseover=\"parent.mouseBox('hello'),event)\" onmouseout=\"parent.mouseBox('')\">"+$p.img("ico_help_s.gif",12,12,"","imgmid")+"</a>";
    }

}
