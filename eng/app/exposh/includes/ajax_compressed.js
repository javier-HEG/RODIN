// ************** LICENCE ****************
/*
	Copyright (c) PORTANEO.

	This file is part of POSH (Portaneo Open Source Homepage) http://sourceforge.net/projects/posh/.

	POSH is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version

	POSH is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Posh.  If not, see <http://www.gnu.org/licenses/>.
*/
// ***************************************
// POSH javascript main file
//
// JAVASCRIPT main functions are defined on portaneo.com
// ***************************************

//###############################################
// FRI=Fabio Ricci HEG Geneva
// Geneva, December 2009
// Modifications and new functions for RODIN
//###############################################
/*
	parseUri 1.2.1
	(c) 2007 Steven Levithan <stevenlevithan.com>
	MIT License
*/

function parseUri (str) {
	var	o   = parseUri.options,
		m   = o.parser[o.strictMode ? "strict" : "loose"].exec(str),
		uri = {},
		i   = 14;

	while (i--) uri[o.key[i]] = m[i] || "";

	uri[o.q.name] = {};
	uri[o.key[12]].replace(o.q.parser, function ($0, $1, $2) {
		if ($1) uri[o.q.name][$1] = $2;
	});

	return uri;
};

parseUri.options = {
	strictMode: false,
	key: ["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"],
	q:   {
		name:   "queryKey",
		parser: /(?:^|&)([^&=]*)=?([^&]*)/g
	},
	parser: {
		strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
		loose:  /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/
	}
};

//alert('parseUri geladen');



//###############################################
/* Client-side access to querystring name=value pairs
	Version 1.3
	28 May 2008
	
	License (Simplified BSD):
	http://adamv.com/dev/javascript/qslicense.txt
	
	Fabio Ricci: Some enhancements for RODIN
	Date: 2.6.2009
*/



function Querystring(qs) { // optionally pass a querystring to parse
	this.params = {};
	
	if (qs == null) qs = location.search.substring(1, location.search.length);
	if (qs.length == 0) return;

// Turn <plus> back to <space>
// See: http://www.w3.org/TR/REC-html40/interact/forms.html#h-17.13.4.1
	qs = qs.replace(/\+/g, ' ');
	// if at the and there is an & then -1
	q = qs.substr(qs.length - 1,1);
	if (q=='&')
		qs=qs.substr(1,qs.length - 2);
	var args = qs.split('&'); // parse out name/value pairs separated via &
	
// split out each name=value pair
	for (var i = 0; i < args.length; i++) {
		var pair = args[i].split('=');
		var name = decodeURIComponent(pair[0]);
		
		var value = (pair.length==2)
			? decodeURIComponent(pair[1])
			: name;
		
		this.params[name] = value;
	}

}

Querystring.prototype.get = function(key, default_) {
	var value = this.params[key];
	return (value != null) ? value : default_;
}

Querystring.prototype.set = function(key, val) {
	this.params[key] = val;
	return val;
}


Querystring.prototype.contains = function(key) {
	var value = this.params[key];
	return (value != null && value != 'undefined');
}

Querystring.prototype.toString = function() {

	var str='';
	var i=0;
	for (var key in this.params) 
	{ i++;
		if (i>1 )  str+='&';
		str+=key+'='+this.params[key];
	}
	return str;
}
// FRI End: Have to include querystring for convenience
//###############################################




// Menu variables
var showNewMod    = false;
var showBoxStatus = false;
var showHdr       = false;
var noteSize      = 0;
var newModH;

// Newspaper variables
var searchControl;

var dirOptSelId   = [];
var dirOptSelSec  = [];
var ourSel        = '';
var ourSelSub     = [];
var useMod;
var tab           = [];
var dumtab;
var mDivObj       = null;
var indef;
var pfolder       = '';
var allowSave     = false;
var isPortal      = true;
var rand          = $random(0,100).toInt();
var rssNoCache    = false;
var leftMenuWidth = 250; //left menus width
var p_version;
var createDivDynamically = true;
var wip_message   = "loading ...";
var _current_date = new Date();
var _current_day  = _current_date.getDate();
var _current_month=_current_date.getMonth();
var _current_year = _current_date.getFullYear();
var widgetDecalY  = 0;
var widgetDecalX  = 0;
var _dirImg       = "../modules/pictures/";
var standaloneMode= false;
var widgetHeight = 200;

var language      = "en";

//****************************************************************************************************************************************************
//**
//**                                      FRAMEWORK
//**
//****************************************************************************************************************************************************

//********************* GENERIC FUNCTIONS **********************************************************************************************************

/*
    Class: $p

            Generic functions
            
            $p

    file: ajax.js

*/

$p={
    /*
                Function: get
                        $p.get  
                    
                        Get object based on ID
                    
                Parameters:

                        v_id - ID of the object

                Returns:

                        object or null if no object with the ID    
           */
    
	
	get: function(v_id)
    {
        return $(v_id);
    },
	/*
	Function: print
                     $p.print
                     
                    Change HTML content of an object
	
           Parameters:

		v_id - ID of the object
		v_s - string to display in the object
		v_add - add v_s to the current content of the object. Top= at the beginning, bottom=at the end
	*/
	print: function(v_id,v_s,v_add)
	{
		var l_obj = $(v_id);
		if (v_add != indef && v_add == "bottom")
            v_s=l_obj.innerHTML+v_s;

		if (v_add   !=  indef && v_add  ==  "top")
            v_s = v_s + l_obj.innerHTML;

		if (l_obj)
            l_obj.set('html',v_s);
	},
	/*
	Function: getPos
                    $p.getPos
    
                    Return the absolute position of a node
	
            Parameters:

		v_node - node
		v_ref - Top=get top position  Left=get left position
                     v_parentRef : indef if position computed based on the page margin, object if position is computed based on this object
	
            Returns:

		Top or Left position of the node or 0 if the node is null
	*/
	getPos: function(v_node,v_ref,v_parentRef)
	{
		var l_ret = 0;
		while(v_node != null && (v_parentRef == indef || v_node != v_parentRef))
		{
			l_ret += v_node["offset"+v_ref];
			v_node = v_node.offsetParent;
		}
		return l_ret
	},
	/*
	Function: setClass 
                    $p.setClass
    
          Change the CSS Class of an object
	
         Parameters:

                    v_id - ID of the object
                    v_class - CSS class to apply to the object
	*/
	setClass: function(v_id,v_class)
	{
		var l_obj = $(v_id);
		if (l_obj)
            l_obj.className = v_class;
	},
	/*
	Function: show 
                    $p.show
                    
                    Show/Hide an object
                    
	Parameters:

		v_id - ID of the object
		v_disp - display value (block, none, inline)
	*/
	show: function(v_id,v_disp)
	{
		var l_obj = $(v_id);
		if (l_obj != null)
		{
			//exceptions
			if (v_disp == 'table-cell' && $p.navigator.IE)
                v_disp = 'block';
	
			l_obj.style.display = v_disp;
		}
	},
	/*
	Function: isShown
                    $p.isShown 
                    
                    Check if object is displayed or not
                    
	Parameters:

                    v_id - ID of the object
	
          Returns:
          
                    boolean, true if object is displayed false if not.
	*/
	isShown: function(v_id)
	{
		var l_obj = $(v_id);
		if (l_obj)
		{
			return (l_obj.style.display == 'block' ? true : false);
		}
		else return false;
	},
    /*
	Function: min
                    $p.min
                    
                    Returns the minimum of two values
                    
	Parameters:

                    v1 - first value
                    v2 -  second value
	
          Returns:
          
                    the minimum value.
	*/
	min: function(v1,v2)
	{
		return Math.min(v1,v2);
	},
    /*
	Function: max
                    $p.max
                    
                    Returns the maximum of two values
                    
	Parameters:

                    v1 - first value
                    v2 -  second value
	
          Returns:
          
                    the maximum value.
	*/
	max: function(v1,v2)
	{
		return Math.max(v1,v2);
	},
	/*
	Function: addPropertyToClass 
                    $p.addPropertyToClass

                    Change an object style property
                    
	Parameters:

		v_class - class of the objects which style property is updated
		v_prop - property to be updated
		v_value - new value of the property
	*/
	addPropertyToClass: function(v_class,v_prop,v_value)
	{
		var l_items = _gelstn("*");
		for(var l_item = 0;l_item < l_items.length;l_item ++)
		{
			if(l_items[l_item].className == v_class)
            {
                l_items[l_item].style[v_prop] = v_value;
            }
		}
	},
	/*
	Function: changeId
                    $p.changeId

                    Change an object ID
                    
	Parameters:

		v_id - current object ID
		v_newId - new ID
	*/
	changeId: function(v_id,v_newId)
	{
		var l_obj = $(v_id);
		if (l_obj)
            l_obj.id = v_newId;
	},
	/*
	Function: setHeight
                    $p.setHeight  
                    
                    Define an object height
                    
	Parameters:

		v_id - ID of the object
		v_h - new height
	*/
	setHeight: function(v_id,v_h)
	{
		var l_obj = $(v_id);
		l_obj.setStyle("height",v_h+"px");
	},
	/*
	Function: setWidth
                    $p.setWidth  
                    
                    Define an object width
                    
	Parameters:

		v_id - ID of the object
		v_w - new height
	*/
	setWidth: function(v_id,v_w)
	{
		var l_obj = $(v_id);
		l_obj.setStyle('width',v_w+"px");
	},
	/*
	Function: img
                    $p.img 
                    
                    Return image HTML script
                    
	Parameters:

		v_file - image URL (optional)
		v_w - width of the image (optional)
		v_h - height of the image (optional)
		v_alt - alternative text of the image (optional)
		v_cl - class of the image (optional)
		v_id - id of the image (optional)
	
	Returns:
    
			 HTML script
	*/
	img:function(v_file,v_w,v_h,v_alt,v_cl,v_id)
	{
		if (v_file == '-') return '';
		if (v_file == indef || v_file == '') v_file='s.gif';
		var l_width = v_w ? ' width="'+v_w+'"'
                          : '';
		var l_height = v_h ? ' height="'+v_h+'"'
                           : '';
		if (v_alt==indef) v_alt = '';
		if (v_cl==indef) v_cl = '.';
		if (v_id==indef)
        {
            v_id = '';
        }
        else {
            v_id = ' id="'+v_id+'"';
        }
		//if (v_file.substr(0,4)!='http'  || v_file.substr(0,2)!='..')
        if ( !v_file.match(/^(http|\.\.)/)) 
        {
            v_file = pfolder+'../images/'+v_file;
        }

		return '<img src="'
                + v_file
                + '" alt="'+$p.string.removeCot(v_alt)+'"'
                + l_width
                + l_height
                + ' class="'
                + v_cl+'"'
                + v_id+' />';
	},
	/*
	Function: imgObj
                    $p.imgObj 
                    
                    Return an image as a new MooTools extended HTML Element.
                    
	Parameters:

		v_file - image URL (optional)
		v_w - width of the image (optional)
		v_h - height of the image (optional)
		v_alt - alternative text of the image (optional)
		v_cl - class of the image (optional)
		v_id - id of the image (optional)
	
	Returns:
    
			 The image as an element
	*/   
	imgObj: function(v_file,v_w,v_h,v_alt,v_cl,v_id)
	{
		if (v_file==indef || v_file=="") { v_file="s.gif"; }
		if (v_alt==indef) { v_alt="";}
		if (v_cl==indef) { v_cl=".";}
		if (v_id==indef) { v_id="";}
		if (v_file.substr(0,4)!="http")  { v_file=pfolder+"../images/"+v_file; }
		l_img=new Element('img',
			{
				'src':v_file,
				'alt':$p.string.removeCot(v_alt),
				'class':v_cl,
				'id':v_id
			}
		);
		if (v_w) l_img.setProperty('width',v_w);
		if (v_h) l_img.setProperty('height',v_h);

		return l_img;
	},
	/*
		Function: adjustFrameHeight
                                $p.adjustFrameHeight 
                                
                                Define a frame height based on its content
                                
		Parameters:

			l_frm - frame object
	*/
	adjustFrameHeight:function(l_frm)
	{
		//Currently not working, blocked by browser !!
		//l_obj=l_frm.contentDocument?l_frm.contentDocument:document.frames[l_frm.id].document;
		//if (l_obj.body.offsetHeight)
		//{
		//	navHeight(l_frm.id,l_obj.body.offsetHeight+10);
		//}
	}
}



$p.friutils={
	
	
} // friutils


/*
    Class: $p.string
            Strings functions
            
            $p.string
*/

$p.string={
    /*
	Function: trim
                    $p.string.trim
                    
                    Suppress unused blank space from a string
                    
	Parameters:

                    v_s - string
	
          Returns:
          
                     string without unused blank space
	*/
	trim: function(v_s)
	{
		return v_s.replace(/^[\t\s\n\r]+/g,'').replace(/[\t\s\n\r]+$/g,'');
	},
	/*
	Function: esc
                    $p.string.esc
                    
                    encode URL string
                    
	Parameters:

		v_s - string
	
	Returns:
                    
                    encoded string
	*/
	esc: function(v_s)
	{
		return window.encodeURIComponent ? encodeURIComponent(v_s) : escape(v_s);
	},
	/*
            Function: unesc
                     $p.string.unesc
                                    
                     decode URL string
                                    
            Parameters:

                     v_s - encoded string
		
	 Returns:
			 
                     decoded string
	*/
	unesc: function(v_s)
	{
		return window.decodeURIComponent ? decodeURIComponent(unescape(v_s).replace(/%/g,'%25')) : unescape(v_s);
	},
	/*
	Function: uc
                    $p.string.uc
                    
                    uppercase transformation on a string
                    
	Parameters:

		v_s - string
	
	Returns:
                    
                     string
	*/
	uc:function(v_s)
	{
		return v_s.toUpperCase();
	},
	/*
	Function: lc
                    $p.string.lc
                    
                    lowercase transformation on a string
                    
	Parameters:

		v_s - string
	
	Returns:
                    
                     string
	*/
	lc:function(v_s){
		return v_s.toLowerCase();
	},
	/*
	Function: formatForSearch
                    $p.string.formatForSearch
                    
                    Format the search string
                    
	Parameters:

		v_s - string
	
	Returns:
                    
                     formated search string
	*/
	formatForSearch: function(v_s)
	{
		v_s=$p.string.lc(v_s);
		v_s=v_s.trim();
		v_s=$p.string.removeAccents(v_s);
		v_s=v_s.replace(/\+/gi,",");
		v_s=v_s.replace(/;/gi,",");
		v_s=v_s.replace(/"/gi," ");
		v_s=v_s.replace(/\./gi," ");
		v_s=v_s.replace(/'/gi," ");
		v_s=v_s.replace(/, /gi,",");
		v_s=v_s.replace(/  /gi," ");
		return v_s;
	},
	/*
	Function: removeAccents
                    $p.string.removeAccents
                    
                    Replace accentuated signs by non accentuated
                    
	Parameters:

		v_s - accenutuated string
	
	Returns:
                    
                     non accuentuated string
	*/
	removeAccents:function(v_s)
	{
		v_s=v_s.replace(/[àâä]/gi,"a");
		v_s=v_s.replace(/[éèêë]/gi,"e");
		v_s=v_s.replace(/[îï]/gi,"i");
		v_s=v_s.replace(/[ôö]/gi,"o");
		v_s=v_s.replace(/[ùûü]/gi,"u");
		v_s=v_s.replace(/[ç]/gi,"c");
		return v_s;
	},
	/*
	Function: removeCot
                    $p.string.removeCot
                    
                    Suppress cots from string
                    
	Parameters:

                    v_s - string to format	
                    v_coteType - cots type ('simple','double','both')
        
	Returns:
                    
                    formatted string
	*/
	removeCot:function(v_s,v_coteType)
	{
		//FRI 
		if (v_s == indef) ;
		else
		{
			if (v_coteType==indef) v_coteType='both';
			if (v_coteType=='simple' || v_coteType=='both') v_s = v_s.replace(/'/g,"");
			if (v_coteType=='double' || v_coteType=='both') v_s = v_s.replace(/"/g,"");
		}
		return v_s;
	},
	/*
	Function: doubleToSimpleCot
                    $p.string.doubleToSimpleCot
                    
                    Replace double cots by simple cots
                    
	Parameters:

		v_s - string
	
	Returns:
                    
                     string
	*/
	doubleToSimpleCot:function(v_s)
	{
		return v_s.replace(/"/g,"'");
	},
    replaceSpleQuot: function (v_s)
	{
        return v_s.replace(/'/g,"&#39");
    },
	replaceAmpersand: function (v_s)
	{
		return v_s.replace(/&/g,"%26");
    },
    replacePlus: function (v_s)
	{
        return v_s.replace(/\+/g,"%2B");
    },
    replaceEqual: function (v_s)
	{
        return v_s.replace(/\=/g,"%3D");
    },
    SimpleEncoding: function (v_s) {
        v_s = $p.string.replaceAmpersand(v_s);
        v_s = $p.string.replacePlus(v_s);
        v_s = $p.string.replaceEqual(v_s);
        return v_s;
    },
	/*
	Function: trunk
                    $p.string.trunk
                    
                    Truncate string if longer that limit length
                    
	Parameters:

                     v_s - string
                     v_length - limit length
		v_rangeForPoint - (integer) define the range a '.' is searched to trunk until
		v_functionForMore - (string) function called to get the entire string (no link if =undefined)
	
	Returns:
                    
                      truncated string
	*/
	trunk:function(v_s,v_length,v_rangeForPoint,v_functionForMore)
	{
		var l_s=v_s.substr(0,v_length);

		if (v_rangeForPoint!=indef)
		{
			var endPos = (l_s.indexOf('.',(v_length-v_rangeForPoint))) + 1; //get position of (.) starting from maxLength
			if (endPos!="") l_s=l_s.substr(0,endPos); //use shorter description
		}

		if (v_s.length>v_length)
		{
			l_s=l_s
				+(v_functionForMore==indef
					? ' ...'
					: ' <a href="#" onclick=\''+v_functionForMore+'\'>'+lg('readMore')+'...</a>'
				);
		}
		return l_s;
	},
	/*
	Function: getVar
                    $p.string.getVar
                    
                    Get variable value from a string (URL)
                    
	Parameters:

                     v_s - string
                     v_var - variable searched
                     datatype - type of data : int or string  (optional)
	
	Returns:
                    
                     value of the variable
	*/
	getVar:function(v_s,v_var,datatype)
	{
        var l_ret="";
        if (datatype && datatype=='int') {
            l_ret=0;
        }
        if ( typeof(v_s) =='object') {
            return '';
        }
        var l_items=v_s.split("&");
        for (var i=0;i<l_items.length;i++)
        {
            var l_arr=l_items[i].split("=");
            if (l_arr[0]==v_var) {
                l_ret=$p.string.unesc(l_arr[1]);
            }
        }
        return l_ret;
	},
	/*
	Function: supVar
                    $p.string.supVar
                    
                    Suppress a variable and its value from a string (URL)
                    
	Parameters:

                    v_s - string
                    v_var - variable searched
	
	Returns:
                    
                     string without the variable
	*/
	supVar:function(v_s,v_var)
	{
        if ( typeof(v_s) =='object') {
            return '';
        }    
		var l_items=v_s.split("&");
		for (var i=0;i<l_items.length;i++)
		{
			var l_arr=l_items[i].split("=");
			if (l_arr[0]==v_var) {
				l_items.splice(i,1);
			}
		}
		return l_items.join("&");
	},
	/*
	Function: textToHtml
                    $p.string.textToHtml
                    
                    Transform text to HTML (replace special chars)
                    
	Parameters:

                    v_s - text to transform
	
	Returns:
                    
                    HTML code
	*/
	textToHtml:function(v_s)
	{
		v_s=v_s.replace(/\&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/  /g,"&nbsp; ").replace(/\r/g,"");
		v_s=($p.navigator.IE)?v_s.replace(/\n/g,"<BR>"):v_s.replace(/\n/g,"<br>");
		return v_s;
	},
	/*
	Function: htmlToText
                    $p.string.htmlToText
                    
                    Transform HTML to text (replace special chars)
                    
	Parameters:

                    v_s - text to transform
	
	Returns:
                    
                    Text equivalent
	*/
	htmlToText:function(v_s)
	{
		v_s=v_s.replace(/\&amp;/g,"&").replace(/\&lt;/g,"<").replace(/&gt;/g,">").replace(/\&nbsp;/g," ").replace(/<BR>/g,"\r\n").replace(/<br>/g,"\r\n").replace(/<BR \/>/g,"\r\n").replace(/<br \/>/g,"\r\n");
		return v_s;
	},
	/*
	Function: textToXml
                    $p.string.textToXml
                    
                    Transform text to xml object
                    
	Parameters:

                    v_s - text to transform
	
	Returns:
                    
                    XML object
	*/
	textToXml:function(v_s)
	{
		var l_xml;
		if (document.implementation.createDocument) {
			// Firefox XML object creation 
			var parser = new DOMParser(); 
			l_xml = parser.parseFromString(v_s, "text/xml"); 
		}
		else if (window.ActiveXObject) { 
			// IE XML object creation
			l_xml = new ActiveXObject("Microsoft.XMLDOM"); 
			l_xml.async="false";
			l_xml.loadXML(v_s);
		}
		return l_xml;
	},
	/*
	Function: correctEncoding
                    $p.string.correctEncoding
                    
                    Replace chars encoding in string
                    
	Parameters:

                    v_s - char to decode
	
	Returns:
                    
                    decoded char
	*/
	correctEncoding:function(v_s)
	{
		var l_s=v_s.replace(/\&amp;/g,"&");
		l_s=l_s.replace(/\&#38;/g,"&");
		l_s=l_s.replace(/\&#39;/g,"'");
		l_s=l_s.replace(/\&quot;/g,"'");
		l_s=l_s.replace(/\&apos;/g,"'");
		return l_s;
	},
	/*
	Function: parseTextToHtml
                    $p.string.parseTextToHtml
                    
                    Parse text as html (experimental)
                    
	Parameters:

                    v_s - text to transform
	
	Returns:
                    
                    HTML object
	*/
	parseTextToHtml:function(v_s)
	{
		var l_html;
		if (document.implementation.createDocument) {
			// Firefox XML object creation 
			var parser = new DOMParser(); 
			l_html = parser.parseFromString(v_s, "text/html"); 
		}
		else if (window.ActiveXObject)  {
			// IE XML object creation
			l_html = new ActiveXObject("Microsoft.XMLDOM"); 
			l_html.async="false";
			l_html.loadXML(v_s);
		}
		return l_html;
	},
	/*
		Function: simulateGetElementsByTagName
                                $p.string.simulateGetElementsByTagName
                                
                                Reproduce getElementsByTagName action on a text
                        
	         Parameters:

			v_s - text
			v_tag - tags searched in text without '<' and '>' string
		
                    Returns:
                    
			 Array containing an object for each tags found in the text
                         
                    Example:

                                tagTab will contain 1 object. Its properties are accessible like :
                                tagTab[0][color];
                                tagTab[0][size];
                                
                                (start code)

                                    <script>
                                        var tagTab=[];
                                        var myString = 'This is my example concerning the <font color='red' size='6'> tag and it's properties</font>';
                                        tagTab = $p.string.simulateGetElementsByTagName(myString,'font');
                                    </script>

                                (end)          
	*/
	simulateGetElementsByTagName:function(v_s,v_tag)
	{
		var l_lowercaseString=$p.string.lc(v_s);
		v_tag=$p.string.lc(v_tag);
		var l_starttag=0,l_endtag,l_nocontent,l_endprop,l_resArray=[];
		while (l_lowercaseString.indexOf("<"+v_tag,l_starttag)!=-1)
		{
			var l_nocontent=false;
			l_starttag=l_lowercaseString.indexOf("<"+v_tag,l_starttag)+(v_tag.length+1);
			l_endprop=l_lowercaseString.indexOf(">",l_starttag);
			if (l_lowercaseString.substr(l_endprop-1,1)=="/"){l_endprop--;l_nocontent=true;}
			var l_property={};
			//get tag properties
			var l_propStr=v_s.substring(l_starttag,l_endprop);
			//l_propElmt=l_propStr.split(/(["'] )/);
			var l_propElmt=l_propStr.split(/[("|')][ ]/);
			for (var i=0;i<l_propElmt.length;i++)
			{
				var pair=l_propElmt[i].split(/[=][("|')]/);
				if (pair[1]) {
					pair[0]=$p.string.trim(pair[0]);
					pair[1]=$p.string.trim(pair[1]);
					l_property[pair[0]]=(pair[1].slice(-1)=='"' || pair[1].slice(-1)=="'")?pair[1].substr(0,(pair[1].length-1)):pair[1];
				}
			}		
			// get tag content
			if (v_s.indexOf("<",l_endprop)!=-1 && !l_nocontent) {
				l_endtag=v_s.indexOf("<",l_endprop);
				if (v_s.substr(l_endtag,(v_tag.length+2))=="</"+v_tag) {
					l_property["content"]=v_s.substring(l_endprop+1,l_endtag);
				}
				else l_property["content"]="";
			}
			else {
				l_property["content"]="";l_endtag=l_endprop;
			}
			l_resArray.push(l_property);
			l_starttag=l_endtag;
		}
		return l_resArray;
	},
	/*
	Function: removeTags
                    $p.string.removeTags
                    
                    remove tags from a string
                    
	Parameters:

                    v_s - string
	
	Returns:
                    
                    string without tags
	*/
	removeTags:function(v_s)
	{
		return v_s.replace(/<\/?[^>]+(>|$)/g, "");
	},
	/*
	Function: removeStyleTag
                    $p.string.removeStyleTag
                    
                    remove style tags from a string
                    
	Parameters:

                    v_s - string
	
	Returns:
                    
                    string without style tags
	*/
	removeStyleTag:function(v_s)
	{
		return v_s.replace(/\<styl[^?]+\/style\>/g, "");
	},    
	/*
	Function: removeHeadTag
                    $p.string.removeHeadTag
                    
                    remove head tag from a string
                    
	Parameters:

                    v_s - string
	
	Returns:
                    
                    string without head tags
	*/
	removeHeadTag:function(v_s)
	{
		return v_s.replace(/\<head[^?]+\/head\>/g, "");
	},
	/*
	Function: removeScriptTag
                    $p.string.removeScriptTag
                    
                    remove script tags from a string
                    
	Parameters:

                    v_s - string
	
	Returns:
                    
                    string without script tags
	*/
	removeScriptTag:function(v_s)
	{
		return v_s.replace(/\<script[^?]+\/script\>/g, "");
	},
	/*
	Function: randomize
                    $p.string.randomize
                    
                    build a random string
                    
	Parameters:

                    v_length - string length
	
	Returns:
                    
                    random string
	*/
	randomize:function(v_length)
	{
		var l_list = new Array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","0","1","2","3","4","5","6","7","8","9");
		var l_s='';

		for(i=0;i<v_length;i++)
		{
			l_s+=l_list[Math.floor(Math.random()*l_list.length)];
		}
		return l_s;
	}
}

//********************* ARRAYS FUNCTIONS **************************************************************************************************************
/*
    Class: array
        Arrays functions
        
        $p.array
*/

$p.array={
	/*
	Function: find
                $p.array.find 
                
                search in an array
                
	Parameters:

		v_arr - array
		v_searched - searched string
	
	Returns:
			 result of the search (true or false)

             */
	find:function(v_arr,v_searched)
	{
		for (var i=0;i<v_arr.length;i++)
		{
			if (v_arr[i]==v_searched) return true;
		}
		return false;
	}	
}

//*********************************** NAVIGATOR FUNCTIONS ******************************************************************************
/*
    Class: $p.navigator
        Navigator functions
*/
$p.navigator={
	IE:(document.all)?1:0,
	SAF:navigator.userAgent.indexOf("Safari")>=0,
	NS:navigator.userAgent.indexOf('Netscape')>0,
	OP:navigator.userAgent.indexOf('Opera')>0,
	/*
	Function: noinclusion
                    $p.navigator.noinclusion
                    
                    Avoid that the page is included in a frame
	*/
	noinclusion: function()
	{
		if (parent.frames.length != window.frames.length)
            top.location.href = document.location.href;
	},
	/*
	Function: addFav
                    $p.navigator.addFav
                    
                     Add application to the navigator bookmarks
	*/
	addFav:function()
	{
		var l_url=__LOCALFOLDER;
		window.external.addfavorite(l_url,lg("msgFav"));
	},
	/*
	Function: addHome
                    $p.navigator.addHome
                    
                     Add application URL to the navigator home
	*/
	addHome:function()
	{
		var l_url=__LOCALFOLDER;
		document.body.setStyle('behavior','url(#default#homepage)');
		document.body.setHomePage(l_url);
	},
	/*
	Function: changeTitle
                    $p.navigator.changeTitle
                    
                     Update the navigator title
                     
           Parameters: 
           
                      v_title - string
	*/
	changeTitle:function(v_title)
	{
		if (v_title==indef) {
            v_title = $p.app.tabs.currName
                      +(tab[$p.app.tabs.sel].nbUnread==0?""
                      :" ("+tab[$p.app.tabs.sel].nbUnread
                      +")");
        }
        document.title=__APPNAME+' :: '+v_title;
	},
	/*
	Function: getWidth
                    $p.navigator.getWidth
                    
                     Get the width of the navigator window
                     
           Returns: 
           
                      navigator window width
	*/
	getWidth:function()
	{
		if(typeof(window.innerWidth)=='number'){
			return window.innerWidth;
		}
		else if(document.documentElement && document.documentElement.clientWidth){
			return document.documentElement.clientWidth;
		}
		else if(document.body && document.body.clientWidth){
			return document.body.clientWidth;
		}
	},
	/*
	Function: getHeight
                    $p.navigator.getHeight
                    
                     Get the height of the navigator window
                     
           Returns: 
           
                      navigator window height
	*/
	getHeight:function()
	{
		if(typeof(window.innerHeight)=='number'){
			return window.innerHeight;
		}
		else if(document.documentElement && document.documentElement.clientHeight){
			return document.documentElement.clientHeight;
		}
		else if(document.body && document.body.clientHeight){
			return document.body.clientHeight;
		}
	},
	/*
		Function: addCssFile
                                $p.navigator.addCssFile *(deprecated)*
                                
                                add a css file to the page
                                
		Parameters:

			v_css - url of the css file
			v_title - title of the css
			v_rel -  relationship to the linked ressource
           
                     Deprecated:
                     
                                this function has moved to $p.styles
	*/
	addCssFile:function(v_css,v_title,v_rel)
	{
		var l_head=document.getElementsByTagName("head")[0];
		if (l_head==indef) $p.app.debug("no <head> tag in this page !");
		var l_css=new Element('link', 
			{ 
				'href': v_css,
				'rel': v_rel==indef?"stylesheet":v_rel,
				'title': v_title==indef?"css":v_title,
				'type': "text/css"
			}
		 );				 
		l_head.appendChild(l_css);
	},
    /*
		Function: setActiveStyleSheet
                                $p.navigator.setActiveStyleSheet *(deprecated)* 
                                
                                Activate a stylesheet
                                
		Parameters:

			v_css - css title	

                      Deprecaterd:
                        
                                This function has moved to $p.styles
	*/
	setActiveStyleSheet:function(v_css)
	{
		var l_link;
		for(var i=0;(l_link=document.getElementsByTagName("link")[i]);i++)
		{
			if(l_link.getAttribute("rel").indexOf("style") != -1 && l_link.getAttribute("title")){
				l_link.disabled = true;
				if (l_link.getAttribute("title")=="style "+v_css) l_link.disabled = false;
			}
		}
		$p.app.style=v_css;
		$p.app.menu.config.oldStyle=v_css;
		$p.app.menu.place();
	},
    /*
		Function: nextstyle
                                $p.navigator.nextstyle *(deprecated)*  
                                
                               Select the next portal style sheet	

                      Deprecated:
                      
                                This function has moved to $p.styles
	*/
	nextstyle:function()
	{
		if ($p.navigator.NS)    {
			$p.app.alert.show(lg("msgOptNS"));
		}
		else    {
			if (tab[$p.app.tabs.sel].style==__themeList.length) tab[$p.app.tabs.sel].style=0;
			tab[$p.app.tabs.sel].style++;
			$p.styles.setActiveStyleSheet(tab[$p.app.tabs.sel].style);
			if (document.forms["option"] && document.forms["option"].selstyle) document.forms["option"].selstyle.value=__themeList[tab[$p.app.tabs.sel].style-1];
		}
	},
    /*
		Function: prevstyle
                                $p.navigator.prevstyle  *(deprecated)*  
                                
                              Select the previous portal style sheet	

                      Deprecated:
                      
                                 This function has moved to $p.styles
	*/
	prevstyle:function()
	{
		if ($p.navigator.NS) {
			$p.app.alert.show(lg("msgOptNS"));
        }
		else    {
			if (tab[$p.app.tabs.sel].style==1) tab[$p.app.tabs.sel].style=__themeList.length+1;
			tab[$p.app.tabs.sel].style--;
			$p.styles.setActiveStyleSheet(tab[$p.app.tabs.sel].style);
			if (document.forms["option"] && document.forms["option"].selstyle) {
                document.forms["option"].selstyle.value=__themeList[tab[$p.app.tabs.sel].style-1];
            }
		}
	},
	/*
		Function: openLink
                                $p.navigator.openLink *(deprecated)* 
                                
                                open an URL
        
		Parameters:

			v_url - url where page is redirected
			v_newpage - is the page opened in a new window ?
			v_uselang - 2 chars for the language of the new page (if applicable)
            
                        Returns:
                        
                                false
            
                        Deprecated:
                        
                                This function has moved to $p.url
	*/
	openLink:function(v_url,v_newpage,v_uselang)
	{
		if (v_url == indef) v_url = window.location.href;
		if (v_uselang) v_url = "../l10n/"+__lang+"/"+v_url;
		if (!$p.url.ishttp(v_url)) v_url = pfolder+v_url;
		if (v_newpage)
			window.open(v_url);
		else
			window.location=v_url;
		return false;
	},
	/*
		Function: simpleUrl
                                $p.navigator.simpleUrl *(deprecated)* 
                                
                               Get the first part of the URL (before '?' or '#')
        
		Parameters:

			v_url - url where page is redirected
			v_newpage - is the page opened in a new window ?
			v_uselang - 2 chars for the language of the new page (if applicable)
            
                        Returns:
                        
                                first part of the URL
      
                        Deprecated:
                        
                                This function has moved to $p.url
            
	*/
	simpleUrl:function(v_url)
	{
		if (v_url.indexOf("?")>0){  v_url=v_url.substr(0,v_url.indexOf("?"));}
		if (v_url.indexOf("#")>0){v_url=v_url.substr(0,v_url.indexOf("#"));}
		return v_url;
	},
    /*
	Function: getRadioValue
                    $p.navigator.getRadioValue *(deprecated)* 
                    
                    get a radio input value
	
           Parameters:

			 radio elements
	
	Returns:
			 false or the value of the radio button selected
           
           Deprecated:
                
                                 This function has moved in $p.app.tools                   
	*/
	getRadioValue:function(v_element)
	{
		for (var i=0; i<v_element.length;i++)
		{
			if (v_element[i].checked) return v_element[i].value;
		}
		return false;
	},
    /*
	          Function: inputFocus
                                $p.navigator.inputFocus *(deprecated)*
                                
                                 Clear an input on focus	

                     Parameters:
                        
                                v_input - current input value
                                v_def - default input value
                                
                     Deprecated:
    
                     This function has moved in $p.app.tools
	*/
	inputFocus:function(v_input,v_def)
	{
		if (v_input.value==v_def)   {
			v_input.value='';
			v_input.style.color='#000000';
		}
	},
    /*
		Function: inputLostFocus
                                $p.navigator.inputLostFocus *(deprecated)* 
                                
                               Fill an input field with its default value

                     Parameters:
                        
                                v_input - current input value
                                v_def - default input value
                    
                    Deprecated:
                            
                                This function has moved in $p.app.tools
	*/
	inputLostFocus:function(v_input,v_def)
	{
		if (v_input.value=='')  {
			v_input.value=v_def;
			v_input.style.color='#aaaaaa';
		}
	},
	/*
		Function: ishttp
                            $p.navigator.ishttp  *(deprecated)* 
                        
                          Check if the URL contains 'http://' or 'https://'
		
                     Parameters:

			 URL to check
		
                     Returns:
    
			 true or false
            
                     Deprecated:

                                 This function has moved to $p.url
	*/
	ishttp:function(url)
	{
		return (url.substr(0,7)=="http://" || url.substr(0,8)=="https://");
	},
	/*
		Function: setParamInUrl
                            $p.navigator.setParamInUrl *(deprecated)* 
                        
                            Set a parameter in an URL, or replace an existing one
		
                     Parameters:

                            v_url - URL
                            v_param - parameter name
                            v_value - parameter value
		
                     Returns:
    
                            the url with the parameter and its value in it
                            
                     Deprecated:
                     
                             This function has moved to $p.url
	*/
	setParamInUrl:function(v_url,v_param,v_value)
	{
		if (v_url.indexOf("&"+v_param+"=")==-1 && v_url.indexOf("?"+v_param+"=")==-1)   {
			if (v_url.indexOf("?")==-1) {
				return v_url+"?"+v_param+"="+v_value;
			}
			else    {
				return v_url+"&"+v_param+"="+v_value;
			}
		}
		else    {
			var l_oldValue=$p.string.getVar($p.url.getParamFromUrl(v_url),v_var);
			return v_url.replace(v_param+"="+l_oldValue,v_param+"="+v_value);
		}
	},
	/*
		Function: getParamFromUrl
                            $p.navigator.getParamFromUrl  *(deprecated)* 
                        
                           Get parameters of an URL
		
                     Parameters:

                            v_url - URL
		
                     Returns:
    
                            parameters in a string
                            
                     Deprecated:
                     
                            This function has moved to $p.url
	*/    
	getParamFromUrl:function(v_url)
	{
		return v_url.substr(($p.url.simpleUrl(v_url)).length);
	},
    /*
	          Function: hideObjects
                                $p.navigator.hideObjects
                                
                                 hide Flash / activex objects
	*/
	hideObjects:function()
	{
		var l_objs=$$("object");
		for (var i=0;i<l_objs.length;i++)
		{
			l_objs[i].style.visibility='hidden';
		}
		var l_objs=$$("embed");
		for (var i=0;i<l_objs.length;i++)
		{
			l_objs[i].style.visibility='hidden';
		}
		var l_objs=$$("iframe");
		for (var i=0;i<l_objs.length;i++)
		{
			l_objs[i].style.visibility='hidden';
		}
	},
    /*
	          Function: showObjects
                                $p.navigator.showObjects
                                
                                 show Flash / activex objects
	*/
	showObjects:function()
	{
		var l_objs=$$("object");
		for (var i=0;i<l_objs.length;i++)
		{
			l_objs[i].style.visibility='visible';
		}
		var l_objs=$$("embed");
		for (var i=0;i<l_objs.length;i++)
		{
			l_objs[i].style.visibility='visible';
		}
		var l_objs=$$("iframe");
		for (var i=0;i<l_objs.length;i++)
		{
			l_objs[i].style.visibility='visible';
		}
	},
    /*
	          Function: indicatorElement
                                $p.navigator.indicatorElement
                                
                                 Indicate a specific element of the page with an arrow picture
                                 
                    Parameters: 
                    
                                 v_element- element
	*/
	indicatorElement:function(v_element)
	{
		if (v_element==null) return false;
		if ($('indicator')==null)
		{
			l_obj=new Element('img', 
				{
					'src':'../images/indicator.gif',
					'id':'indicator',
					'styles':
					{
						'position':'absolute',
						'z-index':'10002'
					},
					'events':
					{
						'click':function()
						{
							$p.navigator.hideIndicator();
						}
					}
				}
			);
			document.body.appendChild(l_obj)
		}
		$('indicator').style.top=$p.getPos(v_element,"Top")+((v_element)["offsetHeight"]/2)+"px";
		$('indicator').style.left=$p.getPos(v_element,"Left")+((v_element)["offsetWidth"]/2)+"px";
	},
    /*
	          Function: hideIndicator
                                $p.navigator.hideIndicator
                                
                                 hide the arrow picture
	*/
	hideIndicator:function()
	{
		$('indicator').destroy();
	},
    /*
	          Function: sound
                                $p.navigator.sound
                                
                                 Play sound
                     
                     Parameters:
                     
                                 v_soundFile -  (string)  sound file URL
	*/
	sound:function(v_soundFile)
	{
		if (v_soundFile == indef)
            v_soundFile = __LOCALFOLDER+'tools/beep2.swf';

		if ($('sound') == null)
		{
			var obj = new Element('div',
				{
					'id':'sound'
				}
			);
			document.body.appendChild(obj);
		}
		var l_sound = new Swiff(v_soundFile,
			{
                width:1,
                height:1,
				container: $('sound')
			}
		);
	},
	/*
		Function: getScrollX
			$p.navigator.getScrollX : get the current horizontal scrolling position
	*/
	getScrollX: function()
	{
		if (document.all)
		{
			if (!document.documentElement.scrollLeft)
			{
				return document.body.scrollLeft;
			}
			else
			{
				return document.documentElement.scrollLeft;
			}
		}
		else
        {
			return window.pageXOffset;
         }   
	},
	/*
		Function: getScrollY
			$p.navigator.getScrollY : get the current vertical scrolling position
	*/
	getScrollY: function()
	{
		if (document.all)
		{
			if (!document.documentElement.scrollTop)
			{
				return document.body.scrollTop;
			}
			else
			{
				return document.documentElement.scrollTop;
			}
		}
		else
        {
			return window.pageYOffset;
         }   
	}
}

$p.forms={
    /*
		Function: disableAllButton
                                Disable all button of a form (during ajax saving for example)
        
		Parameters:

			v_form - form object concerned           
	*/
    disableAllButton: function(v_form)
    {
        for (var i = 0; i < v_form.elements.length; i++)
        {
            v_form.elements[i].disabled = true;
        }
    },
    /*
		Function: enableAllButton
                                Enable all button of a form (after ajax saving for example)
        
		Parameters:

			v_form - form object concerned           
	*/
    enableAllButton: function(v_form)
    {
        for (var i = 0; i < v_form.elements.length; i++)
        {
            v_form.elements[i].disabled = false;
        }
    }
}



//*********************************** URL FUNCTIONS **********************************************************************************
/*
    Class: $p.url

        Url functions
*/
$p.url={
        /*
		Function: openLink
                                $p.url.openLink
                                
                                Open an URL
        
		Parameters:

			v_url - url where page is redirected
			v_newpage - is the page opened in a new window ?
			v_uselang - 2 chars for the language of the new page (if applicable)
            
                        Returns:
                        
                                false
	*/
	openLink:function(v_url,v_newpage,v_uselang)
	{
		if (v_url==indef) v_url = window.location.href;
		if (v_uselang) v_url = "../l10n/"+__lang+"/"+v_url;
		if (!$p.url.ishttp(v_url)) v_url = pfolder+v_url;
		v_url = $p.string.correctEncoding(v_url);
		if (v_newpage)
		{
			window.open(v_url);
		}
		else
		{
			window.location=v_url;
		}
		return false;
	},
	/*
		Function: simpleUrl
                                $p.url.simpleUrl
                                
                                Get the first part of the URL (before '?' or '#')
        
		Parameters:

			v_url - url where page is redirected
			v_newpage - is the page opened in a new window ?
			v_uselang - 2 chars for the language of the new page (if applicable)
            
                        Returns:
                        
                                first part of the URL
	*/
	simpleUrl:function(v_url)
	{
		if (v_url.indexOf("?")>0){ v_url = v_url.substr(0,v_url.indexOf("?"));}
		if (v_url.indexOf("#")>0){ v_url = v_url.substr(0,v_url.indexOf("#"));}
		return v_url;
	},
	/*
		Function: ishttp
                            $p.url.ishttp 
                        
                            Check if the URL contains 'http://' or 'https://'
		
                     Parameters:

			 URL to check
		
                     Returns:
    
			 true or false
	*/
	ishttp:function(url)
	{
		return (url.substr(0,7)=="http://" || url.substr(0,8)=="https://");
	},
	/*
		Function: relativeToAbsolute
                            $p.url.relativeToAbsolute 
                        
                            replace a relative local url to an absolute one
		
                     Parameters:

                            v_url - URL
		
                     Returns:
    
                            formated url

	*/
	relativeToAbsolute:function(url)
	{
		return url.replace(/\.\.\//g,__LOCALFOLDER);
	},
	/*
		Function: setParamInUrl
                            $p.url.setParamInUrl 
                        
                           Set a parameter in an URL, or replace an existing one
		
                     Parameters:

                            v_url - URL
                            v_param - parameter name
                            v_value - parameter value
		
                     Returns:
    
                            the url with the parameter and its value in it
	*/
	setParamInUrl:function(v_url,v_param,v_value)
	{
		if (v_url.indexOf("&"+v_param+"=") == -1 && v_url.indexOf("?"+v_param+"=") == -1)
        {
			if (v_url.indexOf("?") == -1) {
				return v_url+"?"+v_param+"="+v_value;
			}
			else    {
				return v_url+"&"+v_param+"="+v_value;
			}
		}
		else    {
			var l_oldValue = $p.string.getVar($p.url.getParamFromUrl(v_url),v_var);
			return v_url.replace(v_param+"="+l_oldValue,v_param+"="+v_value);
		}
	},
	/*
		Function: getParamFromUrl
                            $p.url.getParamFromUrl 
                        
                            Get parameters of an URL
		
                     Parameters:

                            v_url - URL
		
                     Returns:
    
                            parameters in a string
	*/    
	getParamFromUrl:function(v_url)
	{
		return v_url.substr(($p.url.simpleUrl(v_url)).length);
	},
	/*
		Function: $p.url.isLocal 
                                Check if the URL is local

                     Parameters:

                                v_url : URL
                                
                    Returns: 
                    
			true : is local
			false : is from other server
	*/
	isLocal: function(l_url)
	{
		var l_check = (l_url+'/').indexOf(__LOCALFOLDER);
		return (l_check == -1 ? false : true);
	},
    /*
		Function: $p.url.goToAnchor 
                                Go to page HTML anchor

                     Parameters:

                                v_anchor : Anchor name
                                
	*/
    goToAnchor: function(v_anchor)
    {
        document.location = '#'+v_anchor;
    }
}







//*********************************** STYLES FUNCTIONS ******************************************************************************
/*
    Class: $p.styles
        Styles functions
*/
$p.styles={
	/*
		Function: addCssFile
                                $p.styles.addCssFile 
                                
                                add a css file to the page
                                
		Parameters:

			v_css - url of the css file
			v_title - title of the css
			v_rel -  relationship to the linked ressource
	*/
	addCssFile:function(v_css,v_title,v_rel)
	{
		var l_head=document.getElementsByTagName("head")[0];
		if (l_head==indef) $p.app.debug("no <head> tag in this page !");
		var l_css=new Element('link', 
			{ 
				'href': v_css,
				'rel': v_rel==indef?"stylesheet":v_rel,
				'title': v_title==indef?"css":v_title,
				'type': "text/css"
			}
		 );				 
		l_head.appendChild(l_css);
	},
    /*
		Function: setActiveStyleSheet
                                $p.styles.setActiveStyleSheet 
                                
                                Activate a stylesheet
                                
		Parameters:

			v_css - css title	
	*/
	setActiveStyleSheet:function(v_css)
	{
		var l_link;
		for(var i=0;(l_link=document.getElementsByTagName("link")[i]);i++)
		{
			if(l_link.getAttribute("rel").indexOf("style") != -1 && l_link.getAttribute("title")){
				l_link.disabled = true;
				if (l_link.getAttribute("title")=="style "+v_css) l_link.disabled = false;
			}
		}
		$p.app.style=v_css;
		$p.app.menu.config.oldStyle=v_css;
		$p.app.menu.place();
	},
    /*
		Function: nextstyle
                                $p.styles.nextstyle 
                                
                                Select the next portal style sheet	
	*/
	nextstyle:function()
	{
		if ($p.navigator.NS)    {
			$p.app.alert.show(lg("msgOptNS"));
		}
		else    {
			if (tab[$p.app.tabs.sel].style==__themeList.length) tab[$p.app.tabs.sel].style=0;
			tab[$p.app.tabs.sel].style++;
			$p.styles.setActiveStyleSheet(tab[$p.app.tabs.sel].style);
			if (document.forms["option"] && document.forms["option"].selstyle) document.forms["option"].selstyle.value=__themeList[tab[$p.app.tabs.sel].style-1];
		}
	},
    /*
		Function: prevstyle
                                $p.styles.prevstyle 
                                
                                Select the previous portal style sheet	
	*/
	prevstyle:function()
	{
		if ($p.navigator.NS) {
			$p.app.alert.show(lg("msgOptNS"));
        }
		else    {
			if (tab[$p.app.tabs.sel].style==1) tab[$p.app.tabs.sel].style=__themeList.length+1;
			tab[$p.app.tabs.sel].style--;
			$p.styles.setActiveStyleSheet(tab[$p.app.tabs.sel].style);
			if (document.forms["option"] && document.forms["option"].selstyle) {
                document.forms["option"].selstyle.value=__themeList[tab[$p.app.tabs.sel].style-1];
            }
		}
	}

}


//*********************************** COOKIES FUNCTIONS ******************************************************************************
/*
    Class: $p.cookie
        Cookies functions
*/
$p.cookie={
	/*
		Function: check
                                $p.cookie.check 
                                
                                Check that cookies are accepted by the navigator
	*/
	check:function()
	{
		if (__showHomeBar>0)
		{
			if ((__showHomeBar==1 && document.cookie.indexOf("homebar")==-1) || __showHomeBar==2) homebar();
		}
		if (__showHomeBar==1) $p.cookie.write('homebar=1');
		accepteCookies = (navigator.cookieEnabled) ? true : false;
		if (typeof navigator.cookieEnabled == "undefined" && !cookieEnabled)   {
			$p.cookie.write('homebar=1');
			accepteCookies = (document.cookie.indexOf("homebar")==-1)?false:true;
		}
		if (!accepteCookies) {
            $p.url.openLink("cookies_restriction.html",false,true);
        }
	},
	/*
		Function: write
                                $p.cookie.write 
                                
                                Write in cookie
                      
                      Parameters:

			v_vars (string) - variables to write in cookie
			v_delay (int) - cookie expires delay
	*/
	write:function(v_vars,v_delay)
	{
		var l_date=new Date();
		if (v_delay==indef) v_delay=10;
		l_date.setFullYear(l_date.getFullYear()+v_delay);
		document.cookie=v_vars+"; path=/; expires="+l_date.toGMTString()+";";
	},
	/*
		Function: get
                                $p.cookie.get 
                                
                                Get cookie informations
                      
                     Parameters:

			v_name - variable to extract from cookie
            
                     Returns:
                     
			 "" if cookie is not existing else cookie value
	*/    
	get:function(v_name)
	{
		var l_ret='';
		var l_name=v_name+'=';
		if (document.cookie.length > 0)
		{
			var l_arr=document.cookie.split(/;/);
			for (var i=0;i<l_arr.length;i++)
			{
				l_arr[i]=l_arr[i].trim();
				if (l_arr[i].indexOf((l_name))!= -1)
				{
					l_ret=l_arr[i].substring((l_name.length),l_arr[i].length);
				}
			}
		}
		return l_ret;
	}
}


//*********************************** DATES FUNCTIONS ******************************************************************************
/*
    Class: $p.date

        Date functions
*/
$p.date={
	/*
            Function: convertFromRss
                    $p.date.convertFromRss
                    
                                 Format rss date
                                 
                                 See rfc 822 about date format allowed in RSS feeds
		
            Parameters:

			 rss extracted date
		
	 Returns:
			 javascript usable date
	*/
	convertFromRss:function(v_date)
	{
		v_date=v_date.trim();
        //bug fix to some rss date format  who set zone like that ++00:00 which is not allowed see RFC 822
        v_date = v_date.replace(/\+\+/,'+');
        v_date = v_date.replace(/\+(\d+):(\d+)/,'+$1$2');
        //end bug fix 
		v_date=v_date.replace("CEST","CST");
		var l_formatedDate,l_try;
		//RFC2822 date is recognize
		l_date=new Date(v_date);
		if (isNaN(l_date)) {l_try=v_date.substr(0,12)+"20"+v_date.substr(12);l_date=new Date(l_try);}
		//short date
		if (isNaN(l_date)) {l_try=v_date.substr(0,7)+"20"+v_date.substr(7);l_date=new Date(l_try);}
		// DB date (long)
		if (isNaN(l_date)) {l_date=new Date(v_date.substr(0,4).toInt(),(parseInt(v_date.substr(5,7),10)-1),v_date.substr(8,10).toInt(),v_date.substr(11,13).toInt(),v_date.substr(14,16).toInt(),0);}
		// DB date (short)
		if (isNaN(l_date)) {l_date=new Date(parseInt(v_date.substr(0,4),10),(parseInt(v_date.substr(5,7),10)-1),v_date.substr(8,10).toInt());}
		if (isNaN(l_date)) {l_date=new Date(v_date.substr(6,10).toInt(),(parseInt(v_date.substr(3,5),10)-1),v_date.substr(0,2).toInt());}
		//if not a valid date, date = yesterday
		if (isNaN(l_date)) {var l_nDate=new Date();l_formatedDate=new Date(l_nDate.getDate()-7,l_nDate.getMonth(),l_nDate.getYear());}
		else l_formatedDate=l_date;
		return l_formatedDate;
	},
	/*
            Function: format
                    $p.date.format
                    
                                 Format a javascript date (mm/dd/yyyy)
		
            Parameters:

			 javascript date object
		
	 Returns:
			javascript usable date. 
                                french : dd/mm/yyyy
                                other : mm/dd/yyyy
	*/
	format:function(v_date)
	{
		if (v_date==indef) return;
		var l_day=v_date.getDate();
		var l_month=v_date.getMonth()+1;
		var l_year=v_date.getFullYear();
		return (__lang=="fr"?l_day+"/"+l_month+"/"+l_year:l_month+"/"+l_day+"/"+l_year);
	},
	/*
            Function: addLeftZeros
                    $p.date.addLeftZeros
                    
                                 Add 0 on the left if necessary
		
            Parameters:

			v_s - input string
			v_length - length of the returned string
		
	 Returns:
			
                                string with new length
	*/
	addLeftZeros:function(v_s,v_length)
	{
		v_s=v_s+'';
		var nbZero = (v_length)-(v_s.length);
		for (var i=0;i<nbZero;i++)
		{
			v_s='0'+v_s;
		}
		return v_s;
	},
	/*
		Function: formatDateShort
                                $p.date.formatDateShort
                            
                                Format a javascript date
		
                    Parameters:
 
			v_date - javascript date object
			v_useTime - display time or not (true or false)
            
                     Returns:
                     
                                javascript date                               
           */
	formatDateShort:function(v_date,v_useTime)
	{
		if (v_date=='') return '';
        var l_date=(__lang=="fr"    ?   $p.date.addLeftZeros(v_date.getDate(),2)
                                    +   "/"
                                    +   $p.date.addLeftZeros((v_date.getMonth()+1),2)
                                    :   $p.date.addLeftZeros((v_date.getMonth()+1),2)
                                    +   "/"
                                    +   $p.date.addLeftZeros(v_date.getDate(),2))
                                    +   "/"
                                    +   v_date.getFullYear()
                                    ;
                    
        if (v_useTime 
            && v_date.getHours()!=0 
            && v_date.getMinutes()!=0) {
            l_date+=", "
                  +$p.date.addLeftZeros(v_date.getHours(),2)
                  +"h"
                  +$p.date.addLeftZeros(v_date.getMinutes(),2);
        }
        
		return l_date;
	},
	/*
		Function: formatDateLong
                                $p.date.formatDateLong
                            
                                Format a javascript date (ex: 12 april 2007)
		
                    Parameters:
 
			v_date - javascript date object
			v_useTime - display time or not (true or false)
            
                     Returns:
                     
                                date                               
           */
	formatDateLong:function(v_date,v_useTime)
	{
		if (typeof(v_date)!='object') return '';
		var l_date;
        
		if (v_date.getFullYear()==_current_year 
            && v_date.getMonth()==_current_month 
            && v_date.getDate()==_current_day) {
                l_date=lg("today");
        }
		else
		{
			var yesterday=new Date();
			yesterday.setDate(yesterday.getDate()-1);
			if (v_date.getFullYear()==yesterday.getFullYear()
	           && v_date.getMonth()==yesterday.getMonth() 
	           && v_date.getDate()==yesterday.getDate())  {
	                l_date=lg("yesterday");
	        }
			else
			{
				l_date=v_date.getDate()
	                  +" "
	                  +lg("month"+(v_date.getMonth()+1))
	                  +" "
	                  +v_date.getFullYear();
			}
	    }      
		if (v_useTime 
            && v_date.getHours()!=0 
            && v_date.getMinutes()!=0)  {
                l_date+=" "
                      +lg("at")
                      +" "
                      +$p.date.addLeftZeros(v_date.getHours(),2)
                      +"h"
                      +$p.date.addLeftZeros(v_date.getMinutes(),2);
		}

		return l_date;
	},
	/*
		Function: delayFromNow
                                $p.date.delayFromNow
                                
                                Compute delay between a date and now
		
                      Parameters:

			 javascript date object
		
                     Returns:
                     
			 delay (seconds)
	*/
	delayFromNow: function(v_date)
	{
		if (v_date == indef || v_date == '') return;
		var now = _current_date;
		var l_delai = (now.getTime() - v_date.getTime()) / 1000;
		if (l_delai < -1000) l_delai=0;
		return l_delai;
	},
	/*
		Function: formatDelai
                                $p.date.formatDelai
                                
                                Format delay value
		
                      Parameters:

			 delay (seconds)
		
                      Returns:
                        
			 delay (day, hours, minuts, ...)
	*/
	formatDelai: function(v_t)
	{
		var l_ret = "";
		if (!isNaN(v_t))
		{
			if (v_t <= 60){
                ret = lg("lblThereIs",("1 "+lg("lblMinute")));
            }
			else if (v_t <= 3600){
                l_ret = lg("lblThereIs",(Math.floor(v_t/60)+" "+lg("lblMinute")));
            }
			else if (v_t <= 86400){
                l_ret = lg("lblThereIs",(Math.floor(v_t/3600)+" "+lg("lblHour")));
            }
			else {
                l_ret = lg("lblThereIs",(Math.floor(v_t/86400)+" "+lg("lblDay")));
            }
		}
		return l_ret;
	},
	/*
		Function: getDbFormat
                                $p.date.getDbFormat 
                                
                                Convert date to DataBase format (mm-dd-yyyy)
		
                     Parameters:

			 day, month and year
		
                     Returns:
                     
			 DB formated date
	*/
	getDbFormat:function(v_day,v_month,v_year)
	{
		v_month=("00").substr(0,2-v_month.length)+v_month;
		v_day=("00").substr(0,2-v_day.length)+v_day;
		return v_year+"-"+v_month+"-"+v_day;
	},
	/*
                    Function: convertFromDb
                                $p.date.convertFromDb
                                
                                Convert a DataBase date to javascript date object.
		
                    Parameters:

			v_date - date to be formated
		
                    Returns:
                    
			 javascript date object
	*/
	convertFromDb:function(v_date)
	{
		if (v_date=='0000-00-00') return '';
		var l_month=parseInt(v_date.substr(5,2),10)-1;
		if (v_date.length==10){
			var l_date=new Date(v_date.substr(0,4),l_month,v_date.substr(8,2));
		}
		else{
			var l_date=new Date(v_date.substr(0,4),l_month,v_date.substr(8,2),v_date.substr(11,2),v_date.substr(14,2),0);
		}
		return l_date;
	},
	/*
                    Function: getTime
                                $p.date.getTime
                                
                                get time from javascript date object.
		
                    Parameters:

			v_date - date to be formated
		
                    Returns:
                    
			 javascript time string
	*/
	getTime:function(v_date)
	{
		var l_date=$p.date.addLeftZeros(v_date.getHours(),2)
			+"h"
			+$p.date.addLeftZeros(v_date.getMinutes(),2);
		return l_date;
	}
}



//*********************************** TAG FUNCTIONS ******************************************************************************
/*
    Class: $p.tags

        Tags functions
*/
$p.tags={
	/*
            Function: formatList
                    $p.tags.formatList
                    
                                 Format a tags list
		
            Parameters:

			 v_s - string list of tags
		
	 Returns:
     
			 string list of tags formatted
	*/
	formatList:function(v_s)
	{
		var l_s=$p.string.removeTags($p.string.lc(v_s.trim()));
		while (l_s.indexOf("  ")!=-1) l_s=l_s.replace(/  /g," ");
		l_s=l_s.replace(",,",",");
		l_s=l_s.replace(", ,",",");
		if (l_s.slice(-1)==",") l_s=l_s.substr(0,l_s.length-1);
		if (l_s.substr(0,1)==",") l_s=l_s.substr(1);
		return l_s;
	},
	/*
            Function: separate
                    $p.tags.separate
                    
                                 add spaces between tags
		
            Parameters:

			 v_s - string list of tags
		
	 Returns:
     
			 string list of tags formatted
	*/
	separate:function(v_s)
	{
		return v_s.replace(/,/g,', ');
	}
}



//*********************************** TAG AUTOCOMPLETION FUNCTIONS ******************************************************************************
/*
    Class: $p.tags.autocompletion

        Tags autocompletion functions
*/
$p.tags.autocompletion={
	lastTagWithNoResult:"",
	currentInput:"",
	lastAjaxHandle:-1,
	/*
		Function: get
                                $p.tags.autocompletion.get
                                
                                Get autocompletion tags
		
                    Parameters:

			 v_id - input id
             
                     Ajax:
                     
                                Call the file "xmlautocompletion (php)" to get all the keywords alike in the database
                                The callback function is $p.tags.autocompletion.display
	*/
	get: function(v_id)
	{
        if (__restrictOnExistingTags == true) return;

		$p.tags.autocompletion.currentInput = v_id;
		var l_input = $(v_id),
            l_inputValue = l_input.value,
            l_pos = 0,
            fileLink = posh["xmlautocompletion"];

        if ($p.app.env == "tutorial")
		{
			fileLink = tutorial["xmlautocompletion"];
		}

		//kill the previous ajax autocompletion call
		if ($p.tags.autocompletion.lastAjaxHandle != -1 
            && $p.ajax.xmlhttp[$p.tags.autocompletion.lastAjaxHandle]!=null) {
                $p.ajax.xmlhttp[$p.tags.autocompletion.lastAjaxHandle].abort(); 
        }    
		// get the current word
		while (l_inputValue.indexOf(",",l_pos) != -1)
		{
			l_pos = l_inputValue.indexOf(",",l_pos)+1;
		}
		var l_word = l_inputValue.substr(l_pos);
		l_word = $p.string.formatForSearch(l_word);

		if (l_word.length > 2 
            && ($p.tags.autocompletion.lastTagWithNoResult == "" 
            || l_word.indexOf($p.tags.autocompletion.lastTagWithNoResult) != 0))
        {
                $p.tags.autocompletion.lastTagWithNoResult = "";
                $p.tags.autocompletion.lastAjaxHandle = getXml(
                    fileLink+"?tag="+l_word,
                    $p.tags.autocompletion.display,
                    new Array(v_id,l_word)
                );
		}
		else {
			$p.tags.autocompletion.hide();
		}
	},
	/*
		Function: display
                                $p.tags.autocompletion.display
                                
                                Display tags corresponding in the database
		
                    Parameters:

			 response - Object XML document
                                 vars
	*/
	display: function(response,vars)
	{
		var l_divName = vars[0]+"_autocomp";
		var l_result = response.getElementsByTagName("tag");
		if (l_result.length > 0)
		{
            var l_inputObj = $(vars[0]);
			if ($(l_divName) == null)
			{
				l_obj = new Element('div', 
					{
						'styles': {
									'top': $p.getPos(l_inputObj,"Top")+(l_inputObj)["offsetHeight"]+2+"px",
									'left': $p.getPos(l_inputObj,"Left")+"px",
									'width': (l_inputObj)["offsetWidth"]+"px"
								  },
						'id': l_divName,
						'class': 'autocompletion'
					}
				);
				document.body.appendChild(l_obj);
			}
			else
            {
                $(l_divName).style.top = $p.getPos(l_inputObj,"Top")+(l_inputObj)["offsetHeight"]+2+"px";
                $(l_divName).style.left = $p.getPos(l_inputObj,"Left")+"px";
                $(l_divName).style.width = (l_inputObj)["offsetWidth"]+"px";
                $p.show(l_divName,"block");
            }
		
			var l_s = "<table width='100%'>",
                l_label = "";

			for (var i = 0;i < l_result.length;i++)
			{
				l_label = $p.ajax.getVal(l_result[i],"label","str",false,"???");
				l_s += "<tr>"
                    + "<td class='unselected' onmouseout=\"this.className='unselected';\" onmouseover=\"this.className='selected';\" onmousedown=\"$p.tags.autocompletion.select('"+l_label+"')\">"
                    + l_label
                    + "</td>"
                    + "</tr>";
			}
			l_s += "</table>";
            
			//if only one result corresponding to word typed, hide autocompletion
			if (l_result.length == 1 && l_label == vars[1])  {
                $p.tags.autocompletion.hide();
            }
			else   {
                $p.print(l_divName,l_s);
            }
		}
		else if (response.getElementsByTagName("notag")[0])
		{
			$p.tags.autocompletion.lastTagWithNoResult = vars[1];
			$p.tags.autocompletion.hide();
		}
	},
	/*
		Function: select
                                $p.tags.autocompletion.select
                                
                                Select a tag in the autocompletion list
		
                     Parameters:

			v_label - tag label selected
	*/
	select: function(v_label)
	{
		var l_input = $($p.tags.autocompletion.currentInput);
		var l_inputValue = l_input.value;
		var l_pos = 0;
		while (l_inputValue.indexOf(",",l_pos) != -1)
		{
			l_pos = l_inputValue.indexOf(",",l_pos)+1;
		}
		//replace last word with selected one
		($($p.tags.autocompletion.currentInput)).value = l_inputValue.substr(0,l_pos)+v_label;
		$p.tags.autocompletion.hide();
		($($p.tags.autocompletion.currentInput)).focus();
	},
	/*
		Function: hide
                                $p.tags.autocompletion.hide
                                
                                Hide the autocompletion list
	*/
	hide: function()
	{
		$p.show($p.tags.autocompletion.currentInput+"_autocomp","none");
		if ($p.tags.autocompletion.lastAjaxHandle != -1 && $p.ajax.xmlhttp[$p.tags.autocompletion.lastAjaxHandle] != null)
            $p.ajax.xmlhttp[$p.tags.autocompletion.lastAjaxHandle].abort();
		$p.tags.autocompletion.lastAjaxHandle = -1;
	}
}

$p.tags.selectBox={
    /*
		Function: build
                                $p.tags.selectBox.build
                                
                                build the tags select box
                      Parameters:
                                v_inputObj : tags input object
	*/
    build: function(v_inputObj)
    {
        if (__restrictOnExistingTags == false) return;

        var l_size = v_inputObj.style.width;
        if (l_size == '')
            l_size = 400;
        
        var l_inputId = v_inputObj.id;

        var l_s = $p.html.roundBox(
            lg('selectTags')
            + "<br /><br />"
            + "<div class='tagselectbox' id='"+l_inputId+"_tagslist'></div>"
            + "<br /><center><input type='button' onclick=\"$p.tags.selectBox.close('"+l_inputId+"')\" value='"+lg('lblClose')+"'></center><br />",
            '#E9EDF2',
            l_size+'px'
            );
        if ($(l_inputId+'_tagspopup') == null)
        {
            var l_tagspopup = new Element('div',
                {
                    'id':l_inputId+'_tagspopup'
                }
            );
        }
        else
        {
            var l_tagspopup = $(l_inputId+'_tagspopup');
            l_tagspopup.style.display = 'block';
        }
        l_tagspopup.set('html',l_s);

        //insert the tag select box over the input
        v_parentObj = v_inputObj.parentNode;
        v_parentObj.insertBefore(l_tagspopup,v_inputObj);
        v_inputObj.style.display = 'none';
        

        $p.tags.selectBox.input = v_inputObj;
        $p.tags.selectBox.load(l_inputId);
    },
    /*
		Function: load
                                $p.tags.selectBox.load
                                
                                load the tags
                      Parameters:
                                v_divid : ID(unique) of the div container
	*/
    load: function(v_divid)
    {
        $p.ajax.call(posh["xmlautocompletion"]+'?tag=',
            {
                'type':'load',
                'callback':
                {
                    'function':$p.tags.selectBox.display,
                    'variables':
                    {
                        'divid':v_divid
                    }
                }
            }
        );
    },
    display: function(response,vars)
    {
        var l_result = response.getElementsByTagName('tag');
        var l_s = '';
        var l_currentTags = $(vars['divid']).value;
        
        for (var i = 0;i < l_result.length;i++)
        {
            var l_label = $p.ajax.getVal(l_result[i],'label','str','');
            var l_class = (l_currentTags.indexOf(l_label) == -1 ? 'notselected' : 'selected');
            l_s += '<a href="#" class="'+l_class+'" onclick=\'$p.tags.selectBox.select(this,"'+vars['divid']+'");return false;\'>'+l_label+'</a> ';
        }

        $p.print(vars['divid']+'_tagslist',l_s);
    },
    /*
		Function: select
                                $p.tags.selectBox.select
                                
                                select a tag
                      Parameters:
                                v_tagObj : tag item
	*/
    select: function(v_tagObj,v_divid)
    {
        if (v_tagObj.className == 'notselected')
        {
            v_tagObj.className = 'selected';
            $p.tags.selectBox.add(v_tagObj.innerHTML,v_divid);
        }
        else
        {
            v_tagObj.className = 'notselected';
            $p.tags.selectBox.remove(v_tagObj.innerHTML,v_divid);
        }
    },
    /*
		Function: add
                                $p.tags.selectBox.add
                                
                                add a tag
                      Parameters:
                                v_tag : (string) tag added
	*/
    add: function(v_tag,v_divid)
    {
        var inputObj = $(v_divid);
        var l_currentTags = (inputObj.value == lg("keywords") ? '' : inputObj.value);
        
        if (l_currentTags.indexOf(v_tag+',') == -1)
        {
            l_currentTags += v_tag+',';
        }
        inputObj.value = l_currentTags;
    },
    /*
		Function: remove
                                $p.tags.selectBox.remove
                                
                                remove a tag
                      Parameters:
                                v_tag : (string) tag added
	*/
    remove: function(v_tag,v_divid)
    {
        var inputObj = $(v_divid);
        var l_currentTags = inputObj.value;
        l_currentTags = l_currentTags.replace(v_tag+',','');
        inputObj.value = l_currentTags;
    },
    /*
		Function: close
                                $p.tags.selectBox.close
                                
                                close the selectbox
                      Parameters:
                                v_divid : ID(unique) of the div container
	*/
    close: function(v_divid)
    {
        $(v_divid).style.display = 'block';
        $(v_divid+'_tagspopup').empty();
        $(v_divid+'_tagspopup').style.display = 'none';
    }
}


//*********************************** EFFECTS FUNCTIONS ******************************************************************************
/*
    Class: $p.effect

        Effect functions
*/
$p.effect={
	obj:{},
	opacity:0,
	timer:0,
	actionTriggered:indef,
	/*
		Function: fadein
                                $p.effect.fadein 
                               
                                Initialise the fadein effect
                           
                      Parameters:
                      
                                 v_obj - Object to make fadein on
                                 v_triggerAction - Javascript function launched while the effect is applying
                                 v_limit - Opacity of the effect
	*/
	fadein:function(v_obj,v_triggerAction,v_limit)
	{
		if($p.effect.timer!=0) clearTimer($p.effect.timer);
		if (v_limit==indef) v_limit=1;
		//if ($p.effect.timer) clearTimeout($p.effect.timer);
		$p.effect.obj=v_obj;
		$p.effect.opacity=0;
		if (v_triggerAction!=indef) $p.effect.actionTriggered=v_triggerAction;
		$p.effect.fadeinaction(v_limit);
		$p.effect.obj.setStyle("display","block");
	},


	fadeinFRI:function(v_obj,v_triggerAction,v_limit)
	{
		$p.effect.obj=v_obj;
		$p.effect.obj.setStyle("visibility","visible");
		clearTimer($p.effect.timer);
		if (v_limit==indef) v_limit=0.8;
		//if ($p.effect.timer) clearTimeout($p.effect.timer);
		$p.effect.opacity=0;
		if (v_triggerAction!=indef) $p.effect.actionTriggered=v_triggerAction;
		
		eval($p.effect.actionTriggered[0]);
		$p.effect.actionTriggered=indef;
		clearTimer($p.effect.timer);

		$p.effect.fadeinactionFRI(v_limit);
		$p.effect.obj.setStyle("display","block");
	},
	
	/*
		Function: fadeinaction
                                $p.effect.fadeinaction 
                               
                                Apply fadein effect to an object
                           
                      Parameters:
                      
                                 v_obj - Object to make fadein on
                                 v_triggerAction - Javascript function launched while the effect is applying
                                 v_limit - Opacity of the effect
	*/
	fadeinaction:function(v_limit)
	{
		$p.effect.opacity+=0.1;
		$p.effect.obj.setOpacity(""+$p.effect.opacity);
		$p.effect.obj.setStyle("filter","alpha(opacity="+($p.effect.opacity*100)+")");
		if ($p.effect.opacity>=v_limit)  {
			if ($p.effect.actionTriggered!=indef)  {
				if (typeof($p.effect.actionTriggered)=='object')  {
					for (var i=0;i<$p.effect.actionTriggered.length;i++)
					{
						eval($p.effect.actionTriggered[i]);
					}
				}
				else  {
					eval($p.effect.actionTriggered);
				}
				$p.effect.actionTriggered=indef;
			}
			clearTimer($p.effect.timer);
		}
		else  {
			$p.effect.timer=setTimeout("$p.effect.fadeinaction("+v_limit+")",70);
		}
	},
	
	fadeinactionFRI:function(v_limit)
	{
		$p.effect.opacity+=0.02; //FRI extra slow
		$p.effect.obj.setOpacity(""+$p.effect.opacity);
		$p.effect.obj.setStyle("filter","alpha(opacity="+($p.effect.opacity*100)+")");
		if ($p.effect.opacity<v_limit)  
		{
			$p.effect.timer=setTimeout("$p.effect.fadeinactionFRI("+v_limit+")",70);
		}
	},
	/*
		Function: fadeout
                                $p.effect.fadeout 
                               
                                Initialise the fadeout effect
                           
                      Parameters:
                      
                                 v_obj - Object to make fadeout on
	*/
	fadeout:function(v_obj)
	{
		if($p.effect.timer!=0) clearTimer($p.effect.timer);
		$p.effect.obj=v_obj;
		$p.effect.opacity=1;
		$p.effect.fadeoutaction();
	},
	/*
		Function: fadeoutaction
                                $p.effect.fadeoutaction 
                               
                                Apply fadeout effect to an object
	*/
	fadeoutaction:function()
	{
		$p.effect.opacity-=0.1;
		$p.effect.obj.setOpacity(""+$p.effect.opacity);
		$p.effect.obj.setStyle("filter","alpha(opacity="+($p.effect.opacity*100)+")");
		if ($p.effect.opacity>0){
			$p.effect.timer=setTimeout("$p.effect.fadeoutaction()",70);
		}
		else{
			$p.effect.obj.setStyle("display","none");
			clearTimer($p.effect.timer);
		}
	}
}    
    
    
//*********************************** BASE64 FUNCTIONS ******************************************************************************
/*
    Class: $p.Base64

        Base64 encode
        
        <http://www.webtoolkit.info/>
*/

$p.Base64={
	// private property
	_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
    /*
		Function: encode
                                $p.Base64.encode 
                               
                                Encode a string in Base64
                           
                      Parameters:
                      
                                 input - string to encode
                      
                      Returns:
                        
                                 encoded string     
	*/    
    encode:function(input)
	{
		if (input=="" || input==":") return "";
		var output = "";
		var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
		var i = 0;
		input = $p.Base64._utf8_encode(input);
		while (i < input.length)
		{
			chr1 = input.charCodeAt(i++);
			chr2 = input.charCodeAt(i++);
			chr3 = input.charCodeAt(i++);
			enc1 = chr1 >> 2;
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
			enc4 = chr3 & 63;
			if (isNaN(chr2))    {
				enc3 = enc4 = 64;
			}
			else if (isNaN(chr3)) {
				enc4 = 64;
			}
			output = output 
                   + this._keyStr.charAt(enc1) 
                   + this._keyStr.charAt(enc2) 
                   + this._keyStr.charAt(enc3) 
                   + this._keyStr.charAt(enc4);
		}
		return output;
	},
	/*
		Function: _utf8_encode
                                $p.Base64._utf8_encode 
                               
                                Encode a string in utf8
                           
                      Parameters:
                      
                                 string - string to encode
                      
                      Returns:
                        
                                 encoded string     
	*/
	_utf8_encode:function(string)
	{
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";
		for (var n = 0; n < string.length; n++)
		{
			var c = string.charCodeAt(n);
			if (c < 128)    {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048))    {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else    {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}
		}
		return utftext;
	}    
}




//*********************************** AJAX FUNCTIONS ******************************************************************************
/*
    Class: $p.ajax

        Ajax functions
*/
$p.ajax={
	requestId:0,
	requests:[],
	_WAITING:0,
	_RUNNING:1,
	xmlhttp_handle:0,
	xmlhttp:[],
	executeRequest_running:false,
	executeRequest_waitingNb:0,
	/*
		Function: xhr
                                $p.ajax.xhr 
                            
                                Create the xmlhttprequest object based on navigator compatibility
                        
                    Returns:
                    
                                XmlHttpRequest object (object) 
	*/
	xhr:function()
	{ /*FRI: inverted the sequence: try first the XMLHttpRequest(); */
		try {
			return new XMLHttpRequest();
		}
		catch (e){}
		try{
			return new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e){}
		try{
			return new ActiveXObject("Microsoft.XMLHTTP");
		}
		catch(e){}
		$p.app.alert.show(lg("msgXMLnav"));
		return null;
	},
	/*
	         Function: chooseTunnel
                            $p.ajax.chooseTunnel
                            
                            Define tunnel script 
		
                    Parameters:

			v_url(string) - url of the file to read
			v_vars(string) - GET/POST varialbes sent to the file to read
			v_type(string) - type of the returned file XML/HTML
                
                    Returns:
                    
                                File to open, containing tunnel file (string).
	*/
	chooseTunnel:function(v_url,v_vars,v_type,pfolder)
	{
		var l_feed;
        
		// if url not on the same domain, use tunnel
		if ($p.url.ishttp(v_url))
		{
			//if file is located on the current server
			if (v_url.substr(7,(location.hostname).length)==location.hostname)  {
				l_feed=v_url;
			}
			else
			{
				if (__useproxy)
				{
					if (__proxypacfile=="")
					{
						if (v_vars==indef 
                            || $p.string.getVar(v_vars,"auth")=="") {
                                l_feed=pfolder+posh["xmltunproxy"]+"?ptyp="+v_type+"&url="+v_url;
						}
						else{
							l_feed=pfolder+posh["xmltunproxy"]+"?ptyp="+v_type+"&url="+v_url+"&auth="+$p.string.getVar(v_vars,"auth");
						}
					}
					else
					{
						if (getProxy(v_url)==""){
							l_feed=pfolder+posh["xmltun"]+"?ptyp="+v_type+"&url="+v_url;
						}
						else{
							l_feed=pfolder+posh["xmltunproxypac"]+"?ptyp="+v_type+"&url="+v_url+"&proxy="+getProxy(v_url);
						}
					}			
				}
				else
				{
					if (v_vars==indef 
                        || $p.string.getVar(v_vars,"auth")=="") {
                            l_feed=pfolder+posh["xmltun"]+"?ptyp="+v_type+"&url="+v_url;
					}
					else
					{
						l_feed=pfolder+posh["xmltunauth"]+"?ptyp="+v_type+"&auth="+$p.string.getVar(v_vars,"auth")+"&url="+v_url;
					}
				}
			}
		}
		else    {
			l_feed=v_url.substr(0,3)=="../"?v_url:pfolder+v_url;
		}
		return l_feed;
	},
	/*
	     Function: call
                            $p.ajax.call
                            
                            ajax call function
		
                Parameters:

                            v_url(string) - url of the page called
                            v_param(associative array) - parameters used for this call (refer to the online documentation)
                            
                Returns:
                
                            error code
	*/
	call:function(v_url,v_param)
	{
		var l_noerror = true,l_msg,l_err;
		var l_type = (v_param['source'] == indef ? "xml" : v_param['source']);
		var l_method = (v_param['method'] == indef ? "GET" : v_param['method']);
		var l_async = (v_param['asynchron'] == indef ? true : v_param['asynchron']);
		if (!pfolder)
            pfolder = "";
		var l_priority = (v_param['priority'] == indef ? 2 : v_param['priority']);
		if (v_param['callback'] == indef)
            v_param['callback'] = "";
		if (v_param['type']=='load')    {
			$p.ajax.requests.push(new $p.ajax.request("load",[v_url,v_param['callback']['function'],v_param['callback']['variables'],l_type,v_param['variables'],l_method,v_param['escape'],l_async],l_priority,indef,indef,v_param['caller']));
			$p.ajax.executeRequests();
			return false;
		}
		else
		{
			if (allowSave
                ||v_param['forceExecution'])   {
                    $p.ajax.requests.push(new $p.ajax.request("execute",[v_url,v_param['variables'],v_param['alarm'],v_param['callback']['function'],l_async,v_param['callback']['variables']],l_priority));
                    $p.ajax.executeRequests();

				return;
			}
			else    {
				var l_feed=pfolder+v_url;
			}
			//debug
			$p.app.debug("execute : "+l_feed+" (variables: "+v_param['variables']+")");
			return l_noerror;
		}
	},
	/*
	         Function: request
                            $p.ajax.request
                            
                            new ajax process object
		
                    Parameters:

                            type(string) -  'execute', 'load'
                            action(string) - 
                            priority(int) - 1=high priority, 2=normal, 3=low priority
                            status(int) - 0=waiting , 1=running,  2=cancelled
                            loop(int) - loop number (process can be launch several times)
                            callerId(string) - ID of the function calling the request
	*/
	request: function(type,action,priority,status,loop,callerId)
	{
        this.handle = 0;
		this.type = type;
		this.uniqId = $p.ajax.requestId;
		$p.ajax.requestId++;
		this.action = action;
		this.priority = (priority == indef ? 2 : priority);
		this.status = (status == indef ? $p.ajax._WAITING : status);
		this.loop = (loop == indef ? 1 : loop);
        this.callerId = (callerId == indef ? '' : callerId);
        this.kill = $p.ajax.kill;
	},
	/*
		Function: executeRequests
                                $p.ajax.executeRequests 
                            
                                Launch ajax process based on their priority (queue managed)
	*/
	executeRequests:function()
	{
		//avoid that concurrent treads execution function calls runs
		if (!$p.ajax.executeRequest_running)    {
			$p.ajax.executeRequest_waitingNb=0;
		}
		else    {
			$p.ajax.executeRequest_waitingNb++;
			return;
		} 
		$p.ajax.executeRequest_running=true;		
		var l_requests=$p.ajax.requests;
		//sort by status and priority
		l_requests.sort($p.ajax.sortRequests);
		
		//($p.ajax.requests).sort($p.ajax.sortRequests);
		var l_nbRunning=0;
		for (var i=0;i<l_requests.length;i++)
		{
			if (l_requests[i].status==$p.ajax._RUNNING) l_nbRunning++;
			if (l_nbRunning==2) break;
			if (l_requests[i].status==$p.ajax._WAITING) {
				l_requests[i].status=$p.ajax._RUNNING;
				if (l_requests[i].type=="execute")  {
                   //l_requests[i].handle = 0;
					$p.ajax.execute(i);
				}
				else    {
					l_requests[i].handle = $p.ajax.load(i);
				}
			}
		}

		//check if processes are waiting for execution
		$p.ajax.executeRequest_running=false;
		if ($p.ajax.executeRequest_waitingNb!=0) $p.ajax.executeRequests();
	},
	/*
		Function: sortRequests
                                $p.ajax.sortRequests 
                        
                                Sort ajax processes based on their status, ID and priority
                                
                      Parameters: 
                      
                                 a - Ajax Object
                                 b - Ajax Object
	*/
	sortRequests:function(a,b)
	{
		if (a.status<b.status) return 1;
		if (a.status>b.status) return -1;
		if (a.priority<b.priority) return -1;
		if (a.priority>b.priority) return 1;
		if (a.uniqId<b.uniqId) return -1;
		if (a.uniqId>b.uniqId) return 1; 
	},
	/*
		Function: execute
                                $p.ajax.execute   
                            
                                Execute script with ajax process
                                
                      Parameters:
                      
                                v_id (integer) - id of the ajax request to launch (script execution)
	*/
	execute:function(v_id)
	{
		var l_requestUniqId=$p.ajax.requests[v_id].uniqId;
		var v_scr="";
		if($p.ajax.requests[v_id].action[0]){ v_scr = $p.ajax.requests[v_id].action[0]; }
		var v_vars="";
		if($p.ajax.requests[v_id].action[1]){ v_vars = $p.ajax.requests[v_id].action[1]; }
		var v_alarm="";
		if($p.ajax.requests[v_id].action[2]){ v_alarm = $p.ajax.requests[v_id].action[2]; }
		var fct=$p.ajax.requests[v_id].action[3];
		if($p.ajax.requests[v_id].action[3]){ fct = $p.ajax.requests[v_id].action[3]; }
		var v_async=$p.ajax.requests[v_id].action[4];
		if($p.ajax.requests[v_id].action[4]){ v_async = $p.ajax.requests[v_id].action[4]; }
		var fctvars=$p.ajax.requests[v_id].action[5];
		if($p.ajax.requests[v_id].action[5]){ fctvars = $p.ajax.requests[v_id].action[5]; }

		$p.app.setAsWorking();
		var xmlhttp=null;

		xmlhttp = $p.ajax.xhr();
		if (xmlhttp==null) l_noerror=false;

		if (v_async)
		{
			xmlhttp.onreadystatechange = function()
			{
				if (xmlhttp.readyState == 4){
					$p.ajax.endRequest(l_requestUniqId);

					if (xmlhttp.status >= 400){
						$p.app.debug("Xml file not read : "+v_scr,"error");
						$p.app.connection.test();
						l_noerror=false;
					}
					else{
						$p.ajax.callbackExecution(xmlhttp,v_alarm,fct,fctvars,v_scr);
					}
					$p.app.setAsWorking(false);
				}
			}
		}
		var l_feed=pfolder+v_scr;
		xmlhttp.open("POST",l_feed, v_async);
		xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		xmlhttp.send(v_vars);

		if (!v_async){
			$p.ajax.callbackExecution(xmlhttp,v_alarm,fct,fctvars,v_scr);
			$p.ajax.endRequest(l_requestUniqId); // kill ajax process when non asynchron mode
		}
		//$p.app.debug("execute : "+l_feed+" (variables: "+( (v_vars && v_vars.indexOf('pass')==-1) ? v_vars : '***')+")");
	},
 	/*
		Function: callbackExecution
                                $p.ajax.callbackExecution   
                            
                                Execute the callback function
                      
                     Parameters: 
                     
                                xmlhttp - xmlHttp Object
                                v_alarm - display an alarm or not, boolean (true,false)
                                fct - function callled
                                fctvars - parameters of the function
                                v_scr - 
	*/   
	callbackExecution:function(xmlhttp,v_alarm,fct,fctvars,v_scr)
	{
		//FRI
		if (xmlhttp.responseXML)
		{
		
			if (xmlhttp.responseXML.getElementsByTagName("status")[0])
			{
				if (xmlhttp.responseXML.getElementsByTagName("msg")[0]) var l_msg=$p.ajax.getVal(xmlhttp.responseXML,"msg","str",false,lg("msgXMLerror")+"(1)");
				if (xmlhttp.responseXML.getElementsByTagName("err")[0]) var l_err=$p.ajax.getVal(xmlhttp.responseXML,"err","str",false,lg("msgXMLerror")+"(2)");
				if (xmlhttp.responseXML.getElementsByTagName("ret")[0]) var l_ret=xmlhttp.responseXML.getElementsByTagName("ret")[0].firstChild.nodeValue;
				if (v_alarm&&l_msg){$p.app.alert.show(lg(l_msg),1)}
				if (v_alarm&&l_err){$p.app.alert.show(lg(l_err),3)}
				if (fct && !l_err)
				{
					if (l_ret){
						if (fctvars==indef){fct(l_ret);}else{fct(l_ret,fctvars);}
					}
					else{
						if (fctvars==indef){fct();}else{fct(fctvars);}
					}
				}
			}
			else
			{
				$p.app.debug("Xml file not correct : "+v_scr,"error");
				$p.app.debug("Content read or status tag missing : "+xmlhttp.responseText,"error");
				if (xmlhttp.responseXML.getElementsByTagName("disconnected")[0]) return $p.app.connection.errorTest();
				l_noerror=false;
			}
			$p.app.setAsWorking(false);
		} 
		else
		  alert ('FRI: Systemerror: Empty xmlhttp.responseXML');
	},
	/*
		 Function: load
                                $p.ajax.load
                        
                                Load data with ajax process
                      
                      Parameters:
                                
                                v_id : id of the ajax request
                                
                      Returns: 
                      
                                l_handle
	*/
	load:function(v_id)
	{
		var l_requestUniqId=$p.ajax.requests[v_id].uniqId;

		var v_url=$p.ajax.requests[v_id].action[0];
		var fct=$p.ajax.requests[v_id].action[1];
		var v_fctvars=$p.ajax.requests[v_id].action[2];
		var v_type=$p.ajax.requests[v_id].action[3];
		var v_vars=$p.ajax.requests[v_id].action[4];
		var v_method=$p.ajax.requests[v_id].action[5];
		var v_escapefct=$p.ajax.requests[v_id].action[6];
		var v_async=$p.ajax.requests[v_id].action[7];

		Function: $p.ajax.xmlhttp_handle++;
		var l_handle=$p.ajax.xmlhttp_handle;
		$p.ajax.xmlhttp[l_handle]=null;

		$p.ajax.xmlhttp[l_handle] = $p.ajax.xhr();

		if (typeof($p.ajax.requests[v_id])!="undefined") /*FRI*/
		{			
			var xhrTimeout = setTimeout(function(l_handle,v_id,v_url,v_escapefct)
			{
				if ($p.ajax.xmlhttp[l_handle]==indef) return;
				$p.ajax.xmlhttp[l_handle].abort();
				$p.ajax.xmlhttp[l_handle].onreadystatechange = function(){};
				
				if ($p.ajax.requests[v_id].loop<3)  {
					//relaunch process with lowest priority
					$p.app.debug("Relaunch : "+v_url,"error");
					$p.ajax.requests[v_id].loop++;
					$p.ajax.requests[v_id].priority=3;
					$p.ajax.requests[v_id].status=$p.ajax._WAITING;
				}
				else    {
					$p.app.debug("Abort xml file reading : "+v_url,"error");
					$p.ajax.endRequest($p.ajax.requests[v_id].uniqId);
					if (v_escapefct!=indef) v_escapefct();
				}
				$p.ajax.executeRequests();
			}, $p.ajax.requests[v_id].loop*10000);
		}
		if (v_async)
		{
			$p.ajax.xmlhttp[l_handle].onreadystatechange = function()
			{
				if ($p.ajax.xmlhttp[l_handle].readyState == 4)
				{
					clearTimeout(xhrTimeout);
					if ($p.ajax.xmlhttp[l_handle].status >= 400)
					{
						$p.app.debug("Xml file not read : "+v_url+" (status "+$p.ajax.xmlhttp[l_handle].status+")","error");
						$p.app.debug("Content read : "+$p.ajax.xmlhttp[l_handle].responseText,"error");
						$p.ajax.xmlhttp[l_handle].onreadystatechange = function(){};
						$p.ajax.xmlhttp[l_handle]=null;
						$p.ajax.endRequest(l_requestUniqId);

						if (v_escapefct!=indef) v_escapefct();
						else $p.app.connection.test();
					}
					else
					{
						$p.ajax.xmlhttp[l_handle].onreadystatechange = function(){};
						$p.ajax.callback(l_requestUniqId,l_handle,fct,v_fctvars,v_type);
					}
				}
			}
		}

		//if rss feed, check proxy settings
		if (v_vars!=indef 
            && $p.string.getVar(v_vars,"rssurl")!="" 
            && __proxypacfile!="")  {
                v_vars+="&proxy="+getProxy($p.string.getVar(v_vars,"rssurl"));
		}
		
		l_feed=$p.ajax.chooseTunnel(v_url,v_vars,v_type,pfolder);
		$p.ajax.xmlhttp[l_handle].open(v_method,l_feed,v_async);
		
		
		if (v_method=="GET")
		{
			$p.ajax.xmlhttp[l_handle].send(null);
		}
		else
		{
			$p.ajax.xmlhttp[l_handle].setRequestHeader('Content-Type','application/x-www-form-urlencoded');

			$p.ajax.xmlhttp[l_handle].send(v_vars);
		}

		if (!v_async) // kill ajax process when non asynchron mode
		{
			$p.ajax.callback(l_requestUniqId,l_handle,fct,v_fctvars,v_type);
			clearTimeout(xhrTimeout);
			$p.ajax.endRequest(l_requestUniqId); 
		}
		//debug (need to activate debug mode)
		$p.app.debug("read "+v_type+" : "+l_feed+" (variables: "+v_vars+")");

		return l_handle;
	},
	/*
		Function: callback
                                $p.ajax.callback 
                        
                                Call function when ajax loading done
                     
                     Parameters: 
                     
                                 v_requestId - id of the request
                                 v_handle(int) - process uniq number
                                 v_fct - callback functions
                                 v_vars - callback functions parameters
                                 v_type - format XML ou Text
	*/
	callback:function(v_requestId,v_handle,v_fct,v_vars,v_type)
	{
		var l_ret=(v_type=="xml")?$p.ajax.xmlhttp[v_handle].responseXML:$p.ajax.xmlhttp[v_handle].responseText;
		
		$p.ajax.xmlhttp[v_handle]=null;
		$p.ajax.endRequest(v_requestId);
		
		if (v_fct 
            && typeof(v_fct)=="function") {
                v_fct(l_ret,v_vars);
		}
		else    {
			$p.app.debug("getXml Error : callback function is not existing !","error");
		}
	},
	/*
		Function: endRequest
                                $p.ajax.endRequest
                               
                               Remove request
		
                Parameters:

			v_uniqId - uniq ID of the request
	*/
	endRequest:function(v_uniqId)
	{
		for (var i=0;i<$p.ajax.requests.length;i++)
		{
			if ($p.ajax.requests[i].uniqId==v_uniqId)   {
				$p.ajax.requests.splice(i,1);
				break;
			}
		}
		$p.ajax.executeRequests();
	},
    kill: function()
    {
        $p.ajax.xmlhttp[this.handle].abort();
        $p.ajax.endRequest(this.uniqId);
    },
	/*
                   Function: getprop 
                            $p.ajax.getprop
                    
                            Get a property value from an XML object item
		
                   Parameters:

			v_item - xml object item
			v_name - name of the property
			v_type - type of the value of the property (int/str)
			v_required - is this value required (boolean)
			v_default - default return value if the property is missing
		
                   Returns:
    
			 property value
	*/
	getProp:function(v_item,v_name,v_type,v_required,v_default)
	{
		if (v_default==indef) v_default="";
		if (v_required
            &&!v_item.getAttribute(v_name)) {
                $p.app.alert.show(lg("msgModuleIssue") +  " : " + v_name);
                return v_default;
		}
		var l_prop=(v_item.getAttribute(v_name))?v_item.getAttribute(v_name)
                  :v_default;
		
        if (v_type=="int") l_prop=l_prop.toInt();
		return l_prop;
	},
	/*
			Function: getVal
                                        $p.ajax.getVal 
                                        
                                        Get a node value from an XML object item
			
                                Parameters:

				v_item - xml object item
				v_name - name of the node
				v_type - type of the value (int/str)
				v_required - is the value required ?
				v_default - default return value if the node is missing
			
                                 Returns:
                                 
                                           Ouput : node value
	*/
	getVal:function(v_item,v_name,v_type,v_required,v_default)
	{
		if (v_default==indef) v_default="";
		var l_node=v_item.getElementsByTagName(v_name)[0];
		if (!l_node
            ||!l_node.firstChild
            ||!l_node.firstChild.nodeValue) {
                if (v_required) {
                    $p.app.alert.show(lg("msgModuleIssue"));
                }
                return v_default;
		}
		else
		{
			//if there is a space between xml tag and xml value
			var inc=0;
			while (l_node.firstChild!=null
                   &&l_node.firstChild.nodeType==3
                   &&l_node.firstChild.nodeValue.charCodeAt(0)==10
                   &&inc<5) {
                        l_node.removeChild(l_node.firstChild);
                        inc++;
			}
			if (l_node.firstChild==null)    {
				var l_val="";
			}
			else    {
				var l_val=(l_node.firstChild.nodeValue)?l_node.firstChild.nodeValue:v_default;
				if (v_type=="int") {
                    l_val=l_val.toInt();
                }
			}
			return l_val;
		}
	}
}



//************************************** PLUGIN FUNCTIONS ******************************************************************************************
/*
    Class: $p.plugin

            Plugin functions
*/
$p.plugin={
	hooks:{},
	shown:false,
    contentDiv:'',
	page:"",
	/*
		Function: prepare 
                    
                            $p.plugin.prepare 
                            
                            reset application to open the plugin
                     
                     See also:
                        
                        <init>,<$p.plugin.init>
	*/
	prepare:function()
	{
		//close all menus & other divs
		$p.app.pages.closeAllDiv();
		//close current page
		$p.app.pages.hide();
		//hide loading frame, if opened
		$p.app.checkLoading(false,-1);
		//activate default stylesheet
		if ($p.app.style==0) $p.styles.setActiveStyleSheet($p.app.tabs.defTheme);
		//$p.app.tabs.sel=-1;
		//jspass=="";
		//regenerate tabs
		$p.app.tabs.create(-1);
        //refresh menu based on new page
        $p.app.menu.refreshConditionalMenus();
	},
	/*
		Function: init 
                        
                        $p.plugin.init 
                        initialize a plugin
		
                    Parameters:

			v_title - plugin title (written in browser title bar)
			v_id - uniq identifier of the plugin
	*/
	init:function(v_title,v_id)
	{
		//set page name
		if (v_title==indef) v_title="";
		$p.navigator.changeTitle(v_title);
		//init plugin area
		$p.print("plugin","<div id='pluginmenu' class='addonbar'></div><div id='plugincontent'></div>");
		$p.show("plugin","block");
		$p.plugin.shown=true;
		$p.plugin.page='';
		if (v_id!=indef) $p.app.newEnv(v_id);
		//place footer
		$p.app.pages.computeFooterPosition();
	},
	/*
		Function: menu 
        
                        $p.plugin.menu
                        display the plugin menu
		
                    Parameters:

			v_elemts - array of the options of the menu
			v_selected - selection option of the menu
	*/
	menu:function(v_elemts,v_selected)
	{
		var l_elemt=[];
		if (v_elemts==indef) return false;
		for (var i=0;i<v_elemts.length;i++)
		{
			l_elemt.push(
				(v_elemts[i]['icon']=='' ? '' : $p.img(v_elemts[i]['icon'],indef,indef,'','imgmid')+' ')
				+((v_elemts[i]['isLink'] && v_elemts[i]['id']!=v_selected) ? "<a href='#' onclick=\""+v_elemts[i]['fct']+"\">"+lg(v_elemts[i]['title'])+"</a>" : '<span class="selected">'+lg(v_elemts[i]['title'])+'</span>')
			);
		}
		$p.print('pluginmenu',' &nbsp; '+l_elemt.join(' | '));
	},
	/*
		Function: wait 
                            
                            $p.plugin.wait 
                            
                            call to $p.app.wait('plugincontent');
                            which display loading icon
                            
                            show that application is loading information
            
                    See also:
                    
                            <$p.app.wait>
        */
	wait:function()
	{
		$p.app.wait('plugincontent');
	},
	/*
		Function: content 
        
                            $p.plugin.content 
        
                            display HTML in plugin window
		
                     Parameters:

			 HTML to be displayed
	*/
	content:function(v_content)
	{
		if (v_content==indef) v_content="";
		$p.print("plugincontent",v_content);
	},
	/*
		Function: hide 
        
                            $p.plugin.hide
                            
                            close plugin
	*/
	hide:function()
	{
		$p.print("plugin","");
		$p.show("plugin","none");
		$p.plugin.shown=false;
	},
	/*
		Function: clear 
                        $p.plugin.clear 
                        
                        clear plugin area
	*/
	clear:function()
	{
		$p.print("plugin","");
		$p.plugin.init();
	},
	/*
		Function:  useWidget
        
                             method from $p.plugin.useWidget -> useWidget ($p.plugin.useWidget)
                             
                            a widget can be opened in the plugin page
	*/
	useWidget:function()
	{
		tab[$p.app.tabs.sel]=new $p.app.tabs.object(
                                        -1,     //uniq id of the table
                                        "",     //label - title
                                        "P",    //type of the page
                                        "",     //function linked
                                        0,      // lock    
                                        0,      //tab sequence
                                        0,      //editable
                                        0,      //movable
                                        "",     //icon
                                        0,       //status
                                        false,  //loodstart
                                        indef   //page id
                                        );
		$p.app.tabs.selId=0;
	},
    /*
            Function:open
            
                    $p.plugin.open
                    
                    alias for  $p.plugin.prepare
               
               See also:
               
                    <open>,<$p.plugin.prepare>
        */
	open:function(){return $p.plugin.prepare();},
	/*
	         Function: openInTab
                                $p.plugin.openInTab 
                                
                                Open a plugin function in a tab
		
                    Parameters:

			v_tabId - tab ID
			v_fct - function called on tab opening
	*/
	openInTab:function(v_tabId,v_fct,v_anchor)
	{
		//$p.app.banner.option.hide();
		$p.show("box","none");
		$p.app.pages.closeAllDiv();
        //display modules block who contains table with modules
		$p.show("modules","block");

        //change anchor
        if (v_anchor == indef) v_anchor ='';
        $p.url.goToAnchor(v_anchor);

		//$p.app.stopLoading();
		$p.app.tabs.sel=$p.app.tabs.idToPos(v_tabId);
		//change style to default
		$p.styles.setActiveStyleSheet($p.app.tabs.defTheme);
        
        $p.app.newEnv('plugin');

		if (tab[$p.app.tabs.sel].isLoaded)
		{
			$p.show("modules"+v_tabId,"block");
		}
		else
		{
			if ($("modules"+v_tabId)==null)    {
				tab[$p.app.tabs.sel].root=new Element('div', {'id': 'modules'+v_tabId, 'class':'plugin'} );
				($("modules")).appendChild(tab[$p.app.tabs.sel].root);
			}
			else
			{
				$p.show("modules"+v_tabId,"block");
			}
			v_fct("modules"+v_tabId);
			tab[$p.app.tabs.sel].isLoaded=true;
		}

        //refresh menu based on new page
        $p.app.menu.refreshConditionalMenus();
	}
}

/*
    Class: $p.plugin.application
    
       manage plugins
        
*/
$p.plugin.application={
    /*
            Array: item
            
                    array of plugins
                    
                    $p.plugin.application.item
        
        */
	item:[],
    /*
                Function: initMenu
                        
                                $p.plugin.application.initMenu
                                
                                build hash about application menu and push it  in $p.app.menu.options
                                
                                called by $p.app.initMenus()

                   See Also:
                   
                            <$p.app.initMenu()>,<$p.app.menu.options>
            */
	initMenu:function()
	{
		$p.app.menu.options.push({
                                "id":"applications",
                                "label":lg("yourapplications"),
                                "desc":lg("myapplicationsIconDesc"),
                                "icon":"ico_menu_myportaneo.gif",
                                "seq":60,
                                "action":"$p.plugin.application.menu()",
                                "type":"",
                                "pages":[]
                                }
                                );
	},
	/*
		Function:  hideMenu
        
                                    $p.plugin.application.hideMenu
                        
                                    hide the plugin  menu
	
    */
	hideMenu:function()
	{
		for (var i=0;i<$p.app.menu.options.length;i++)
		{
			if ($p.app.menu.options[i].id=='applications')
			{
				//hide application tab
				$p.app.menu.options.splice(i,1);
				$p.show('menuopt_applications','none');
			}
		}
	},
	/*
		Function:  obj
        
                                $p.plugin.application.obj 
            
                                set plugin application object

                     Parameters:
                     
                                id - plugin object id
                                
                                title - plugin object title
                                
                                icon - plugin object icon
                                
                                action - plugin object action 
           */
	obj:function(id,title,icon,action)
	{
		this.id=id;
		this.title=title;
		this.icon=icon;
		this.action=action;
	},
	/*
		Function: menu
        
                            $p.plugin.application.menu 
                        
                            open the  plugin application submenu
           */
	menu:function()
	{
		//$p.app.menu.emptyContent();
		var l_s="";
		for (var i=0;i<$p.plugin.application.item.length;i++)
		{
			l_s+="<a href='#' onclick=\""+$p.plugin.application.item[i].action+";return false;\">"+$p.img($p.plugin.application.item[i].icon,indef,indef,"","imgmid")+" "+$p.plugin.application.item[i].title+"</a><br />";
		}
		//$p.app.menu.addTitle('listapps_1','ico_menu_myportaneo.gif',lg('yourapplications'));
		//$p.app.menu.addHTML('listapps_1',l_s);		
		$p.app.menu.addHTML('listapps_1',l_s);		
	},
	/*
		Function: load
        
                            $p.plugin.application.load 
                            
                            load the plugin applications avail for the user
                            
                            load xml file (from database)
                            
                            SELECT a.id,a.title,a.icon,a.action FROM applications...
                     
                     See also:
                     
                            <$p.plugin.application.get>
	*/
	load:function()
	{
		if ($p.app.user.id>0)
		{
			$p.ajax.call(posh["xmlapplications"],
				{
					'type':'load',
					'callback':
					{
						'function':$p.plugin.application.get
					}
				}
			);
		}
		else
		{
            //hide menu if user no authenticated
			$p.plugin.application.hideMenu();
		}
	},
    /*
            Function: get
            
                        $p.plugin.application.get
                        
                        callback from load
    
                        hidemenu if no data
                        
                        push data in $p.plugin.application.item
    
            parameters:
            
                    response - repsonse from load
                    
                    vars - parameters
                    
        */
	get:function(response,vars)
	{
		if (response!=null)
		{
			var l_result=response.getElementsByTagName('application');
			if (l_result.length==0)
			{
				$p.plugin.application.hideMenu();
			}
			else
			{
				for (var i=0;i<l_result.length;i++)
				{
					$p.plugin.application.item.push(
                                        new $p.plugin.application.obj(
                                                $p.ajax.getVal(l_result[i],'id','int',false,0),
                                                $p.ajax.getVal(l_result[i],'title','str',false,''),
                                                $p.ajax.getVal(l_result[i],'icon','str',false,''),
                                                $p.ajax.getVal(l_result[i],'action','str',false,'')
                                                )
                                                );
				}
			}
		}
	}
}
//********************* HOOKS MANAGEMENT********************************************************************************************************
$p.plugin.hook={
	/*
                Function: launch
                       $p.plugin.hook.launch
                    
                        launch functions linked to hook
                    
                Parameters:

                      v_name: the hook's name
           */
	launch:function(v_name){
		//check if the hook exists
		if(!$p.plugin.hooks[v_name]){
			return false;
		}
		//order by priority 
		//($p.plugin.hooks[v_name]).sort($p.plugin.hook.sortByPriority);to be developed in posh 3.0
		//execute the functions linked to the hook
		for (var key in $p.plugin.hooks[v_name]){
			for(var j=0;j<$p.plugin.hooks[v_name][key].length;j++){
				eval($p.plugin.hooks[v_name][key][j]);
			}
		}
	},
	/*
                Function: sortByPriority
                       $p.plugin.hook.sortByPriority
                    
                        sort the hooks by priority
                    
                Parameters:
		
		(a,b) values to compare
		
	     Returns
		 
		 comparaison value
           */
	sortByPriority:function(a,b){
		if (a<b) return -1;
		if (a>b) return 1;
		return 0;
	},
	 /*
                Function: register
                       $p.plugin.hook.register
                    
                        register a function linked to a hook
                    
                Parameters:
				
		v_name: the hook's name
		v_function:  the function to register
		v_priority: the priority of the hook
           */
	register:function(v_name,v_function,v_priority){
		v_priority+='_';
		//check if the hook is already existing
		if(!$p.plugin.hooks[v_name]){
			$p.plugin.hooks[v_name]={};
		}
		//check if there is a hook with the same priority
		if(!$p.plugin.hooks[v_name][v_priority]){
			$p.plugin.hooks[v_name][v_priority]=[];
		}
		//set the new hook
		$p.plugin.hooks[v_name][v_priority].push(v_function);		
	}
}
$p.plugin.tools={
	fckObject:indef,
	/*
                Function: initializeFckEditor
                       $p.plugin.tools.initializeFckEditor
                    
                        Improve a textarea with FCK Editor
                    
                Parameters:
				
		v_textareaName: The textarea id

           */
	initializeFckEditor:function(v_textareaId,v_height)
	{
        if (v_height == indef) v_height = '300';

		$p.plugin.tools.fckObject=new FCKeditor(v_textareaId);
		sBasePath=__LOCALFOLDER;
		$p.plugin.tools.fckObject.BasePath=sBasePath+'tools/fckeditor/';
		$p.plugin.tools.fckObject.Config['CustomConfigurationsPath']=sBasePath+'includes/fckconfig.js';
		$p.plugin.tools.fckObject.Width ='100%';
		$p.plugin.tools.fckObject.Height=v_height;
		$p.plugin.tools.fckObject.ToolbarSet='portaneo';
		$p.plugin.tools.fckObject.ReplaceTextarea();
	},
	forceFckEditorSaving:function(v_textareaId)
	{
		var oEditor = FCKeditorAPI.GetInstance(v_textareaId);
		oEditor.UpdateLinkedField();
	}
}

//****************************************************************************************************************************************************
//**
//**                                      APPLICATION
//**
//****************************************************************************************************************************************************

//********************* APPLICATION INITIALIZATION ************************************************************************************************
/*
    Class: $p.app

        Application initialization

*/
$p.app={
	style:0,
	env:"portal",
	isLoading:false,
	inactivityTime:0,
	/*
		Function: init 
        
                        $p.app.init 
                        
                        init the application
                    
                    See Also:
                    
                        <$p.app.getVersion>
                        
                        <$p.app.loadStyles>

                        <$p.app.initMenus>

                        <$p.app.mainMenu>
		
                         <$p.app.loadTabs>
		
                        <$p.plugin.application.load>
		
                        <$p.app.footer>
		
                        <$p.app.initEvents>
		
                        <$p.app.counter.reset>
	*/
	init:function()
	{
		$p.plugin.hook.launch('app.init.start');

		// load all themes of the application
		$p.app.getVersion();
		$p.app.loadStyles();
		$p.app.initMenus();
		$p.app.mainMenu();
		$p.app.loadTabs();
		$p.plugin.application.load();
		$p.app.footer();
		$p.app.initEvents();
		$p.app.counter.reset();

		$p.plugin.hook.launch('app.init.end');  
	},
	/*
		Function: initMenus
        
                        $p.app.initMenus
                        
                        init the menus
	*/
	initMenus:function()
	{
		$p.plugin.hook.launch('app.initMenus.start');

		$p.app.pages.initMenu();
		$p.app.widgets.initMenu();
		//if (__useNetwork) $p.network.initMenu();
		//if (__useArchive) $p.article.initMenu();
		//if (__useSharing) $p.app.statistics.initMenu();
		$p.plugin.application.initMenu();

		$p.plugin.hook.launch('app.initMenus.end');
	},
	/*
		Function: fri_getpicturedef
        $p.app.fri_getpicturedef(widgetname,widgeturl,widgetlink)
        get pic def from widget and return it
	*/
	fri_getpicturedef:function(modname,widgeturl,widgetpicsrc) {
		var picdef;
		var datasourcesite='';
		if (widgetpicsrc) {
			picdef='<table cellpadding="0" cellspacing="0" border="0"><tr><td>'
					+'<img src="'+widgetpicsrc+'"/></td><td valign="top">&nbsp;'+modname+'</td></tr></table>';
		} else {
			picdef = name;
		}

		return picdef;
	},
	/*
		Function: logout
        
                                $p.app.logout 
                                
                                logout from Portaneo application
	*/
	logout: function()
	{
		$p.plugin.hook.launch('app.logout.start');
		var response = 1;        
        var cookiesTab=new Array("autoi", "autop", "laststate");
        if ($p.app.user.type=="A") {
            cookiesTab.push("admlaststate");
        }
        
		//the logout confirmation is not applied for admin
		if ( ($p.cookie.get("autoi") != "") && ($p.app.user.id > 0) ) {
			var response = confirm(lg("disconnectCheck"));
		}
		
		if (response == 1) {
            $p.app.deleteCookies(cookiesTab);
            
            $p.ajax.call('../../app/tests/LoggerResponder.php?action=1', {'type':'load'});
            
            var localfolder = __LOCALFOLDER;
            localfolder = localfolder.replace(/http:/,window.location.protocol);
            
            var linkToOpen = localfolder+"portal/"+posh["scr_authentif"]+"?act=logout";
            
            setTimeout("$p.url.openLink('" + linkToOpen + "');", 10);
		}

		$p.plugin.hook.launch('app.logout.end');
	},	
    /*
		Function: $p.app.deleteCookies
        
                            delete useless cookies after deconnection
                            
		Parameters:

			 cookiesTab - array of the cookies names
	*/
    deleteCookies:function(cookiesTab)
    {
        var delay=-15;
        for (var i=0;i<cookiesTab.length;i++)
        {
            if ($p.cookie.get(cookiesTab[i]) != "") {
                $p.cookie.write(cookiesTab[i]+"=",delay);
            }
        }
    },
	/*
		Function: $p.app.wait
        
                            display loading icon in object
                            
		Parameters:

			 v_id - ID of the object
	*/
	wait:function(v_id)
	{
		if ($(v_id)!=null) $(v_id).set('html',$p.img("ico_waiting.gif",16,16));
	},
	/*
		Function: $p.app.getDebugCookie
        
                            Create the debug cookie
	*/
	getDebugCookie:function()
	{
		$p.cookie.write("debug=Y",7);
		$p.app.alert.show("Cookie installed !");
	},
	/*
		Function: $p.app.debug 
        
                            display debug message in debug window
                            
		Parameters:

			v_msg - debug message
			v_type - debug message type (error or warning or info)
	*/
	debug:function(v_msg,v_type)
	{
		if (__debugmode)
		{
			if ($p.cookie.get('debug')=='Y')
			{
				if ($("debug")!=null)
				{
					l_msg=$p.string.textToHtml(v_msg);
					var l_style="";
					if (v_type==indef) v_type="info"
					switch(v_type)
					{
						case "error":
						l_style="color: #ff0000";
						break;
						case "warning":
						l_style="color: orange";
						break;
					}
					var l_obj=$("debug");
					var l_date=new Date();
					l_obj.setStyle("display","block");
					//l_obj.innerHTML()
					l_obj.set('html',"<table cellpadding='0' cellspacing='0'><tr><td width='80' valign='top'>"+l_date.getHours()+":"+l_date.getMinutes()+":"+l_date.getSeconds()+"></td><td style='"+l_style+"'> "+l_msg+"</td></tr></table>"+l_obj.innerHTML);
				}
			}
		}
	},
    /**
            Function: isCustomPortal
            
                        $p.app.isCustomPortal
                        
                        CustomPortal depends on $p.app.env
                            
                            portal_page_anon
                            
                            portal_page_conn
                            
                            admin
                 
                 Returns: 
                 
                        true or false
    
        **/
	isCustomPortal: function()
	{
		if ($p.app.env == 'portal_page_anon' || $p.app.env == 'portal_page_conn' || $p.app.env=='admin')
			return true;

		return false;
	},
	/*
		Function: $p.app.pageMode
                            load application in page mode
                            
                            definie value of allowSave  to know if some datas are save in database or not
                            
                    See Also:
                            <$p.app.init>
                            
                            <$p.app.banner.info.load>
	*/
	pageMode:function()
	{
		$p.plugin.hook.launch('app.pageMode.start');

		//if (v_prof!=indef) $p.app.tabs.selId=v_prof;
		//depending on the user is connected or not
		if ($p.app.user.id==0)
		{
			allowSave=false;
			$p.app.tabs.sel=0;
		}
		else
		{
			allowSave=true;
		}
		useMod=false;
		$p.app.init();
		//$p.app.cache.init();
		$p.app.banner.info.load();
		$p.plugin.hook.launch('app.pageMode.end');
	},
	/*
		Function: $p.app.loadStyles
        
                        load available css (depending on themeNb define in adm_config table)
	*/
	loadStyles:function()
	{	/*FRI PHP-PARAMETRIERUNG */
		for (var i=1;i<=__themeList.length;i++)
		{
			var skinParam;
			if (RODINSKIN!=undefined)
				skinParam="?skin="+RODINSKIN;
			
			$p.styles.addCssFile("../../app/exposh/styles/main"+i+".css.php"+skinParam,"style "+i,"alternate stylesheet");
		}
	},
	/*
		Function: $p.app.setState 
        
                            define the page Portaneo have to load when next user connection
                            
                            function called at application opening
                            
                            write javascript in cookie, javascript will be evaluate later (lastate=id)
                            
		Parameters:

			 v_fct - id last page
	*/
	setState: function(v_fct)
	{
		if ($p.app.user.id>0 || $p.app.user.type == 'A') $p.cookie.write("laststate="+v_fct);
	},
	/*
		Function: $p.app.newEnv 
                            define a new environnement
                            
		Parameters:

			 v_env - environnement name
	*/
	newEnv: function(v_env)
	{
		if ($p.app.env != v_env)
		{
			$p.app.env = v_env;
		}
	},
	/*
		Function: $p.app.home 
        
                            open the Portaneo homepage
	
                    Parameters:

			 v_tabId - page ID
	*/
	home: function(v_tabId)
	{
		$p.plugin.hook.launch('app.home.start');

        //if the page name is in anchor, load it
        if (location.hash != indef && location.hash != '')
        {
            var l_hash = location.hash.replace(/#/g,'');

            if (l_hash.indexOf('tab') != -1)
            {
                var l_tabId = l_hash.replace(/tab\//g,'');
                var l_tab = $p.app.tabs.getId(l_tabId);

                // set the default tab as the one of the anchor
                if (l_tab != indef && tab[l_tab])
                {
                    $p.app.tabs.sel = l_tab;
                    v_tabId = tab[l_tab].id;
                }
            }
        }

		if ($p.app.tabs.sel == indef || $p.app.tabs.sel == -1 || v_tabId == -1)
		{
			//open the latest page opened by the user
			if (__loadlatestpageonstart == 3)
			{
				$p.app.openHome();
			}
			else if (__loadlatestpageonstart == 2 && $p.cookie.get("laststate") != "")
			{
				eval($p.cookie.get("laststate"));
			}
			else
			{
				if (tab.length > 0) $p.app.tabs.open(0);
			}
		}
        else
		{
			$p.app.tabs.open($p.app.tabs.sel);
		}

		$p.plugin.hook.launch('app.home.end');
	},
	/*
		Function: loadTabs 
        
                        $p.app.loadTabs
                        
                        load all the tabs for the user
                        
		Parameters:

			v_prof - selected tab ID
			v_locked -
			v_action - action to launch on tab opening
            
                    See Also:
                    
                            callback function - <$p.app.initTabs>
	*/
	loadTabs: function(v_prof,v_locked,v_action)
	{

		$p.plugin.hook.launch('app.loadTabs.start');

		v_prof = (v_prof == indef ? $p.app.tabs.selId : v_prof);
		if (v_action == indef) v_action = "";
		var l_tabXml = ($p.app.user.id == 0) ? "selections/tabs.xml?nocache="+rand
                                             : posh["xmltabs"];

		if (dumtab!=indef)  
            l_tabXml += "?dumtab="+dumtab.label+"&dumicon="+dumtab.icon;
		//var l_locked=v_locked?true:false; //???
		$p.ajax.call(l_tabXml,
			{
				'type':'load',
				'callback':
				{
					'function':$p.app.initTabs,
					'variables':
					{
						'tabid':v_prof,
						'action':v_action
					}
				}
			}
		);

		$p.plugin.hook.launch('app.loadTabs.end');
	},
	/*
		Function: initTabs
        
                            $p.app.initTabs
                            
                            init all the tabs
                            
		Parameters:

			response - xml object containing tabs information
			vars - variables sent (tab ID,action)
	*/
	initTabs: function(response,vars)
	{

		$p.plugin.hook.launch('app.initTabs.start');

		var l_prof = vars['tabid'];
		var l_action = vars['action'];

		$p.app.tabs.init(response);

		if ($p.app.tabs.sel == indef) $p.app.tabs.sel = -1;
		//if ($p.app.tabs.sel == indef) $p.app.tabs.sel = 0;
		//if new tab, get the id
		if ($p.app.tabs.sel == 999)
		{
			$p.app.tabs.sel = $p.app.tabs.getId(l_prof);
            if ($p.app.tabs.sel == -1) $p.app.tabs.sel = 0;
			$p.app.tabs.create($p.app.tabs.sel);
			//FRI: Show widget menu ONLY IF action="edittab"
			if (l_action=='edittab')
			{
				//$p.app.menu.open();
				$p.app.pages.change(l_prof,l_action);
			}
		}
		else
		{
			//define the page to open on
			$p.app.tabs.create($p.app.tabs.sel);
			$p.app.home($p.app.tabs.sel);
			//init the menu (needs the tabs to be init too)
			$p.app.menu.init();
		}
		//tab name is edited ?
		if (tab.length != 0)
		{
			setTimeout("$p.app.pages.loadOnStart()",2000);
		}
		else
		{
			$p.app.checkLoading(false);
			$p.styles.setActiveStyleSheet(1);
		}

		$p.plugin.hook.launch('app.initTabs.end');
	},
	/*
		Function: $p.app.openHome
        
                        define application loaded on the homepage

	*/
	openHome: function()
	{
		//FRI:DEAKT the following for no exta tabs
		if (__homeDivs.length > 0 && false)
		{
			$p.app.tabs.openTempTab(3,"$p.plugin.openInTab(%tabid%,$p.app.displayHome,'home')",lg('home'),'../images/ico_home.gif');
		}
		else
		{
			if ($p.app.user.id == 0 || ($p.app.tabs.sel != -1 && (tab[$p.app.tabs.sel].id == 0 || tab[$p.app.tabs.sel].id >= 1000000000)))
			{
				if ($p.app.tabs.sel == -1 && tab[0]) $p.app.tabs.sel = 0;
				//$p.app.pages.load("selections/page"+$p.app.tabs.selId+".xml");
				$p.app.tabs.open($p.app.tabs.sel);
			}
			else
			{
				//if first tab is already selected > stop
				if ($p.app.tabs.sel == 0) return false;
				if (tab.length == 0) return false;
				//else load the first tab
				if ($p.app.tabs.sel != -1)
                {
                    $p.app.pages.hide();
                }
				else
                {
                    $p.app.tabs.sel = 0;
                }

				$p.app.tabs.open($p.app.tabs.sel);				
			}
		}
	},
	displayHome:function(v_container)
	{
		$p.plugin.hook.launch('app.openHome.start');

		var l_div=[];
		$p.app.setState("$p.app.openHome()");
		$p.app.newEnv('home');
		//action depend on if the user is connected or not 
		if ($p.app.user.id==0 
            || ($p.app.tabs.sel!=-1 
            && (tab[$p.app.tabs.sel].id==0 
                || tab[$p.app.tabs.sel].id>=1000000000))
           )    {
			for (var i=0;i<__homeDivs.length;i++)
			{
				if (__homeDivs[i]["anonymous"])	l_div.push(i);
			}
			if (l_div.length>0) {
				$p.app.displayFeaturedHome(l_div,v_container);
			}
			else {
				if ($p.app.tabs.sel==-1 && tab[0]) $p.app.tabs.sel=0;
				//$p.app.pages.load("selections/page"+$p.app.tabs.selId+".xml");
				$p.app.tabs.open($p.app.tabs.sel);
			}
		}
		else {
			for (var i=0;i<__homeDivs.length;i++)
			{
				if (__homeDivs[i]["connected"])	l_div.push(i);
			}
			if (l_div.length>0) {
				$p.app.tabs.selId=0;
				$p.app.displayFeaturedHome(l_div,v_container);
			}
			else {
				//if first tab is already selected > stop
				if ($p.app.tabs.sel==0) return false;
				if (tab.length==0) return false;
				//else load the first tab
				if ($p.app.tabs.sel!=-1){$p.app.pages.hide();}
				else {$p.app.tabs.sel=0;}

				$p.app.tabs.open($p.app.tabs.sel);
			}
		}

		$p.plugin.hook.launch('app.openHome.end');

		return false;
	},
	/*
		Function: $p.app.displayFeaturedHome
                                display application homepage
                                
		Parameters:

			 v_div - array containing divs information (divs displayed, ID and content)
	*/
	displayFeaturedHome:function(v_div,v_container)
	{
		var l_s = '';
        
        if (__useSharing)
            l_s += '<div id="homeheader" style="padding: 8px;"></div>';
        
        l_s += '<div id="intromessage" style="padding: 8px;"></div>'
            + '<table cellspacing="8" cellpadding="0" width="100%">'
			+ '<tr>'
			+ '<td valign="top" width="49%">';

		for (var i=0;i<v_div.length;i++)
		{
			if (i>0 && __homeDivs[v_div[i-1]]["col"]!=__homeDivs[v_div[i]]["col"])
            {
                l_s += '</td>'
                    + '<td width="20">&nbsp;</td>'
                    + '<td valign="top" width="49%">';
            }
			l_s += '<div style="padding-bottom: 8px;">'
                + '<div style="padding: 8px;">'
                + '<div id="homediv'+i+'"></div>'
                + '</div>'
                + '</div>';
		}

		l_s += '</td>'
			+ '</tr>'
			+ '</table>';

		$p.print(v_container,l_s);

		$p.app.stopLoading();
		$p.navigator.changeTitle(lg('home'));

		for (var i=0;i<v_div.length;i++)
		{
			eval(__homeDivs[v_div[i]]["fct"]+"('homediv"+i+"')");
		}

        if (__useSharing)
            $p.app.loadHomeHeader();
		$p.app.help.loadIntro();
	},
	/*
		Function: $p.app.loading
                        display loading message
	*/
	loading:function()
	{
		$p.plugin.hook.launch('app.loading.start');

		$p.app.popup.show(wip_message,400,300,indef,false);
		$p.app.isLoading=true;

		$p.plugin.hook.launch('app.loading.end');
	},
	/*
		Function: $p.app.startLoading 
                            display personalized page loading information
	*/
	startLoading:function()
	{
		//tab[$p.app.tabs.sel].isLoaded=false;
		$p.app.loading();
		//$p.show("loading","block");
		$p.app.counter.reset();
	},
	/*
		Function: $p.app.stopLoading 
                            hide personalized page loading information
	*/
	stopLoading:function()
	{
		$p.app.isLoading=false;
		$p.app.popup.hide();
	},
	/*
		Function: $p.app.footer 
                            display application footer
	*/
	footer:function()
	{
		$('footer').set('html',__footer);
	},
	/*
		Function: $p.app.resetAndReload 
                            reset cookies and reload (used if loading issues)
	*/
	resetAndReload:function()
	{
		//reset cookies
		$p.cookie.write("laststate=");
		//reopen page
		$p.url.openLink();
	},
	/*
		Function: $p.app.setAsWorking  
        
                                inform users that application is running a process
                                
		Parameters:

			 v_status - process status (true=running, false=ended)
	*/
	setAsWorking:function(v_status)
	{
		if (v_status==indef) v_status=true;
		if ($("ajaxwork")==null && v_status) {
			var l_obj = new Element('div', { "id": "ajaxwork" } );	
			document.body.appendChild(l_obj);
			$p.app.wait("ajaxwork");
		}
		$p.show("ajaxwork",(v_status?"block":"none"));
	},
	/*
		Function: $p.app.mainMenu 
                            build main menu
                            
		Parameters:

			 v_locked - is the application locked (password required) or not
	*/
	mainMenu:function(v_locked)
	{
		$p.plugin.hook.launch('app.mainMenu.start');
	
		$('headlink').empty();
		$('information').empty();
		
		if (v_locked==indef) v_locked=$p.app.tabs.locked;
		var l_label,l_objContainer;
	    
        for (var i=0;i<__headmenu.length;i++)
        {
			if ((__headmenu[i]["anonymous"] && $p.app.user.id==0) || (__headmenu[i]["connected"] && $p.app.user.id>0) || (__headmenu[i]["admin"] && $p.app.user.id==-1))
			{
				l_objContainer = $(__headmenu[i]['position'] == 'left' ? 'information' : 'headlink' );
				l_label=(__headmenu[i]["label"]).replace("%username%",shortName($p.app.user.name));				
				switch (__headmenu[i]["type"])
				{
					case "label":
						__headmenu[i]["label"]=lg(__headmenu[i]["label"]);/*FRI set language value */
                        var spanObj1 = new Element('span', { 'id':__headmenu[i]["id"],'class':__headmenu[i]["clss"] });
                        spanObj1.set('html',(__headmenu[i]["images"]==''?'':$p.img(__headmenu[i]["images"],false,false,lg(__headmenu[i]["comment"]),"imgmid")+'&nbsp;')+(l_label==''?'':'&nbsp;'+l_label));
                        spanObj1.inject(l_objContainer);
                        var bespace = new Element('b');
                        bespace.set('html','&nbsp;');
                        bespace.inject(l_objContainer);
                        break;
					case "link":
                        var aObj1 = new Element('a', 
                            {
                                'id':__headmenu[i]["id"],
                                'events': {
                                                'click': function()
                                                {
                                                    eval(__headmenu[this.name]["fct"]);
                                                    return false;
                                                },
                                                'mouseover': function(){
                                                $p.app.tabs.showHeadMenu(false);}
                                          },
                                          
                                'class':__headmenu[i]["clss"],
                                'title':lg(__headmenu[i]["comment"]),
                                'href':'#'
                            }
                        );
                        aObj1.name = i;
                        aObj1.set('html',
                            (__headmenu[i]["images"]==''
                                                    ?''
                                                    :'<span style="text-decoration:none">'+$p.img(__headmenu[i]["images"],false,false,lg(__headmenu[i]["comment"]),"imgmid")+'&nbsp;')
                            +(l_label=='</span>'?'':'</span>'+lg(l_label))
                        );	
                        aObj1.inject(l_objContainer);
                        var bespace = new Element('b');
                        bespace.set('html','&nbsp;');
                        bespace.inject(l_objContainer);							
                        break;
					case "menu":
                        var aObj2 = new Element('a', 
                            {
                                'events': {
                                                'click': function(){
                                                $p.app.tabs.initHeadMenu(this,this.name);}
                                          },
                                'class':__headmenu[i]["clss"],
                                'title':lg(__headmenu[i]["comment"]),
                                'href':'#'
                            }
                        );
                        aObj2.name = i;
                        aObj2.set('html',(__headmenu[i]["images"]==''?'':$p.img(__headmenu[i]["images"],false,false,lg(__headmenu[i]["comment"]),"imgmid")+' ')+lg(l_label)+$p.img("puce_down.gif",7,5,"","imgmid"));	
                        aObj2.inject(l_objContainer);
                        var bespace = new Element('b');
                        bespace.set('html','&nbsp;');
                        bespace.inject(l_objContainer);
                        break;
					case "form":
                        var formObj1 = new Element('form', 
                            { 
                                'styles':	{
                                    'margin':'0px',
                                    'padding':'0px',
                                    'display':'inline'
                                    },
                                'events': {
                                    'submit': function(){
                                        eval(this.fct);
                                        return false;
                                        }
                                    },
                                 'class':__headmenu[i]["clss"],
                                 'action':'#'
                             }  
                        );
                        formObj1.fct=__headmenu[i]["fct"];
                        formObj1.set('html',(__headmenu[i]["images"]==''?'':$p.img(__headmenu[i]["images"],false,false,lg(__headmenu[i]["comment"]),"imgmid")+' ')+__headmenu[i]["label"]+'<input name="text" type="text" class="thinbox" style="color:#aaaaaa" onFocus=\'$p.app.tools.inputFocus(this,"'+__headmenu[i]["comment"]+'")\' onBlur=\'$p.app.tools.inputLostFocus(this,"'+__headmenu[i]["comment"]+'")\' value="'+__headmenu[i]["comment"]+'" />'+(__headmenu[i]["options"]==''?'':'<input type="submit" class="btn" value="'+__headmenu[i]["options"]+'" />'));
                        formObj1.inject(l_objContainer);
                        var bespace = new Element('b');
                        bespace.set('html','&nbsp;');
                        bespace.inject(l_objContainer);
                        break;
				}
				(l_objContainer).appendText(' ');
			}
		}
        
        fri_adjust_logo_decoration(); /*FRI*/
        //fri_adjust_breadcrumbs_align1();

		var endSpace=new Element('span');
		endSpace.set('html','&nbsp; &nbsp; &nbsp;');
		endSpace.inject($('headlink'));
		
        if (__useChat && $p.chat) $p.chat.displayStatus('span_availability');

		$p.plugin.hook.launch('app.mainMenu.end');
	},
	/*
		Function:  initEvents 
        
                            init page events
	*/
	initEvents:function()
	{
		document.body.onmouseover=function(){$p.app.inactivityTime=0;}
	},
	/*
		Function: $p.app.checkLoading 
        
                            manage modules loading process
		
                    Parameters:

	:		v_t (boolean) - true=start loading / false=check if loading is ended 
			v_tab - tab sequence ID
	*/
	checkLoading:function(v_t,v_tab)
	{
		if (v_tab==indef) v_tab=$p.app.tabs.sel;
		if (!v_t) {
			if (v_tab!=-1 && v_tab!=indef && tab.length!=0) {
				//only for portals
				if (tab[v_tab].type==1 || tab[v_tab].type==3) {
					if (v_tab==$p.app.tabs.sel) (tab[v_tab].root).setStyle("display","block");
					for (var i=0;i<tab[v_tab].module.length;i++)
                    {
                        if (tab[v_tab].module[i]) tab[v_tab].module[i].show();
                    }
					if (tab[v_tab].showType==1) {
						//Normally, all sources are loaded (showtype=0 on startup) - v1.3.0
						var l_allLoaded=true;
						for (var i=0;i<tab[v_tab].module.length;i++)
						{
							if (tab[v_tab].module[i].format=='R' && !tab[v_tab].module[i].isLoaded)
							{
								l_allLoaded=false;
								//$p.app.widgets.rss.refresh(i); Suppressed with 1.4.2
							}
						}
						if (!l_allLoaded) $p.app.widgets.rss.reader.showArticlesList(true,$p.app.widgets.rss.reader.currSrc);
					}
					additionalMod();
					//check for tab status ( loading or loaded)
					var l_allLoaded=true;
					for (var i=0;i<tab[v_tab].module.length;i++)
					{
						if (    
                                tab[v_tab].module[i]
                                && tab[v_tab].module[i].format=='R' 
                                && !tab[v_tab].module[i].isLoaded)
						{
							l_allLoaded=false;
						}
					}
					if (l_allLoaded)
					{
						tab[v_tab].isLoaded=true;
						$p.app.tabs.refresh($p.app.tabs.sel);
					}
				}
			}

			if ($p.app.isLoading) $p.app.stopLoading();
		}
	},
	/*
		Function:  $p.app.stats 
        
                            statistics management
	*/
	stats:function()
	{
		$p.ajax.call(posh["scr_addstats"],
			{
				'type':'execute',
				'variables':"prof="+tab[$p.app.tabs.sel].id+"&id="+$p.app.user.id,
				'alarm':false
			}
		);
	},
	/*
            Function:  $p.app.getVersion 
            
                        define application versoion
            */
	getVersion:function()
	{
		p_version=__POSHVERSION;
	},
	/*
		Function: $p.app.standalone
        
                        create widget in an HTML page (outside posh)
        
		Parameters:

                                v_rootObj (string) - element ID to print in
			v_columnNumber (integer) - number of columns
			v_widgetAligned (boolean) - define if the widget are aligned
                                v_tabPos - tab position (used in administration to display widget)
	*/
	standalone: function(v_rootObj,v_columnNumber,v_widgetAligned,v_tabPos)
	{
		$p.app.tabs.selId = 0;
        allowSave         = false;
		useMod            = false;
        standaloneMode    = true;

        if (typeof(v_tabPos) == 'undefined') {
            $p.app.tabs.sel = 0;
            tab[$p.app.tabs.sel] = new $p.app.tabs.object(1);
        }
        else {
            $p.app.tabs.sel = v_tabPos;
        }

		//add tab information (even if not physically created)
		tab[$p.app.tabs.sel].root = v_rootObj;
		tab[$p.app.tabs.sel].colnb = v_columnNumber;
		tab[$p.app.tabs.sel].moduleAlign = v_widgetAligned;
		// add columns
		$p.app.pages.columns.createAll($p.app.tabs.sel);
	},
    /*
		Function: $p.app.loadHomeHeader
        
                        Load the header of the home page
        	*/
    loadHomeHeader: function()
    {
        $p.ajax.call(pep["xml_user_properties"],
            {
                'type':'load',
                'callback':
                {
                    'function':$p.app.displayHomeHeader
                }
            }
        );
    },
    displayHomeHeader: function(response,vars)
    {
        var l_result = response.getElementsByTagName('user');
        var l_picture = $p.ajax.getVal(l_result[0],'picture','str','');
        var l_name = $p.ajax.getVal(l_result[0],'longname','str','');

        var l_s = $p.html.buildFeatureHeader({
            'image':(l_picture == '' ? '../images/nopicture.gif' : l_picture),
            'title':'<div style="float: right"><a href="#" onclick="$p.app.help.enableIntro();return false;">'+$p.img('ico_help2.gif',16,16,'','imgmid')+'</a></div>'
                    + lg('lblHello') + ' ' + l_name
                    + '<br /><br />'
                    + '<form onsubmit=\'return $p.network.profile.updateStatus(this)\'>'
                    + '<input class="thinbox" type="text" name="stat" value=\''+lg('myStatus')+'\' maxlength="200" style="width: 400px;"/> '
                    + '<input type="submit" class="btn" value="'+lg("lblBtnSend")+'" />'
                    + '</form>',
            'menu':'<div id="homeheader_menu"></div>'
        });
        $p.print('homeheader',l_s);
    }
}


//********************* TOOLS FUNCTIONS ***********************************************************************************************************************
/*
    class: $p.app.tools
*/
$p.app.tools={
    /*
	Function: getRadioValue
                    $p.app.tools.getRadioValue 
                    
                    get a radio input value
	
           Parameters:

                    radio elements
	
	Returns:
			
                    false or the value of the radio button selected            
	*/
	getRadioValue:function(v_element)
	{
		for (var i=0; i<v_element.length;i++)
		{
			if (v_element[i].checked) return v_element[i].value;
		}
		return false;
	},
    /*
	          Function: inputFocus
                                $p.app.tools.inputFocus 
                                
                                Clear an input on focus	

                     Parameters:
                        
                                v_input - current input value
                                v_def - default input value
	*/
	inputFocus:function(v_input,v_def)
	{
		if (v_input.value==v_def)   {
			v_input.value='';
			v_input.style.color='#000000';
		}
	},
    /*
		Function: inputLostFocus
                                $p.app.tools.inputLostFocus 
                                
                                Fill an input field with its default value

                     Parameters:
                        
                                v_input - current input value
                                v_def - default input value
	*/
	inputLostFocus:function(v_input,v_def)
	{
		if (v_input.value=='')  {
			v_input.value=v_def;
			v_input.style.color='#aaaaaa';
		}
	},
    /*
                Function: buildPreviousLinkIcon
                
                                $p.app.tools.build previous icon
                  
                   Parameters: 
                   
                            jslink - link js to add on an onclick event
                     
                    Returns : HTML
            */
    buildPreviousLinkIcon: function (jslink) {
        return '<a href="#" class="previousIcon" onclick=\''+jslink+';return false;\' >'
               + $p.img('ico_previous3.gif',8,11,lg('previous'),"imgmid")
               +' '
               +lg('previous')
               + '</a>';
    },
    /*
                Function: buildNextLinkIcon
                
                                $p.app.tools.build previous icon
                  
                   Parameters: 
                   
                            jslink - link js to add on an onclick event
                            
                    Returns: HTML
            */    
    buildNextLinkIcon: function (jslink) {
        return '<a href="#" class="nextIcon" onclick=\''+jslink+';return false;\' >'
                +lg('next')
                +' '
               +$p.img('ico_next3.gif',8,11,lg('next'),"imgmid")
               + '</a>';    
    },
    /*
                   Function: checkmail
                
                                $p.app.tools.checkmail 
                                
                                check if an email has valid syntax
                  
                   Parameters: 
                   
                                adress (string) - adress to verify
                            
                   Returns: 
                                
                                true if format correct
            */ 
    checkmail:function(adress)
    {
        var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
        if (!adress) return false;
        else {
            if (!filter.test(adress)) { return false; }
            else { return true; }
        }
    },
    /*
                Function: $p.app.tools.getmetaContents
            */
    getmetaContents: function getmetaContents(meta){

      var m = document.getElementsByTagName('meta'); 
      for(var i in m){ 
           if( m[i].name == meta){ 
             return m[i].content; 
           } 
      } 
    }
}



//********************* USERS FUNCTIONS ***********************************************************************************************************************
/*
    Class: $p.app.user
            User functions
*/
$p.app.user={
    userCriterias:{},
	id:0,
	name:'',
	status:'o',
    type:'',
    /*
                Function: init
                         $p.app.user.init
                                        
                         Initialise a user

                 Parameters:
                    
                            id - user id
                            name - user name
	*/    
	init: function(id,name,type,status)
	{
		$p.app.user.id = id;
		$p.app.user.name = name;
        $p.app.user.type = type;
        $p.app.user.status = (status == indef ? 'o' : status);
	}
}


//********************* MENUS FUNCTIONS ***********************************************************************************************************************
/*
    Class: $p.app.menu
            Menu functions
*/
$p.app.menu={
	template:[],
	initialized:false,
	isOpen:indef,
	options:[],
	optionSelected:1,
	subOptSelected:indef,
    optionSelectedId:indef,
    menuOpt:{},
	/*
		Function: init
                                $p.app.menu.init

                                Init a menu
	*/
	init: function()
	{
		$p.plugin.hook.launch('app.menu.init.start');

		//if menu always closed
		if ( __menuDefaultStatus==4 && $p.app.env!="admin")
            return false;
            
        if ($p.app.menu.initialized)
            return false;

		$p.app.menu.template['h'] = {
			'container':'hmenu',
			'htmltpl':'<div id="menubuttons"></div>'
				+'<table width="100%" cellpadding="0" cellspacing="0">'
				+'<tr>'
				+'<td id="menuoptions"></td>'
				+'<td id="menusuboptions"></td>'
				+'<td id="menucontent"></td>'
				+'</tr>'
				+'</table>',
			'displayType':'table-cell',
			'options':
			{
				'showIcon':true,
				'showLabel':true
			}
		};
		$p.app.menu.template['v'] = {
			'container':'addWidgetBoard',
			'htmltpl':'<div id="menubuttons"></div>'
				+'<div id="menuoptions"></div>'
				+'<div id="menusuboptions"></div>'
				+'<div id="menucontent"></div>',
			'displayType':'block',
			'options':
			{
				'showIcon':true,
				'showLabel':false
			}
		};

		//build the menu
		$p.print($p.app.menu.template[__menuposition].container,$p.app.menu.template[__menuposition].htmltpl);
        //empty the options and the content of the menu
		$p.app.menu.clean();
        //hide the headers
		$p.app.menu.hideOptions();
        $p.app.menu.hide();
        //sort the options
        $p.app.menu.options.sort($p.app.menu.sortOptions);
		
		$p.app.menu.initialized = true;

		if (__menuDefaultStatus == 3
			|| (__menuDefaultStatus == 2 && $p.cookie.get('showmenu') == 1))
		{
			//$p.app.menu.displayCloseButton(); DISABLING WIDGET LIST CLOSING BUTTON
			$p.app.menu.open();
		}
		$p.plugin.hook.launch('app.menu.init.end');
	},
	/*
		Function: clean
                                $p.app.menu.clean
                                
                                Clean the menu area
	*/
	clean: function()
	{
		$p.app.menu.emptyOptions();
		$p.app.menu.emptySubOptions();
		$p.app.menu.emptyContent();
	},
    /*
		Function: emptyOptions
        
                                $p.app.menu.emptyOptions 
                                
                                Empty menu options content
	*/
	emptyOptions: function()
	{
		($('menuoptions')).empty();
	},
    /*
		Function: emptySubOptions
        
                                $p.app.menu.emptySubOptions 
                                
                                Empty menu sub options content
	*/
	emptySubOptions: function()
	{
		($('menusuboptions')).empty();
	},
    enableAllMenuLinks:function()
    {
        for (var i in $p.app.menu.menuOpt)
        {
            $p.app.menu.menuOpt[i]=false;
        }    
    },    
    enableMenuLinks:function(v_id)
    {
        $p.app.menu.menuOpt[v_id]=true;
        for (var i in $p.app.menu.menuOpt)
        {
            if (i!=v_id) {
                $p.app.menu.menuOpt[i]=false;
            }
        }  
    },
	/*
		Function: emptyContent
                                $p.app.menu.emptyContent 
                                
                                Empty menu content
	*/
	emptyContent:function()
	{
		($('menucontent')).empty();
	},
	/*
		Function: hideOptions
                                $p.app.menu.hideOptions
                                
                                Hide the options (if no options in the menu, the menu remains closed)
	*/
	hideOptions:function()
	{
		$p.show('menuoptions','none');
		$p.show('menusuboptions','none');
	},
	/*
		Function: buildOptions
                                $p.app.menu.buildOptions
                                
                                Display the Options
                                
		Parameters:

			v_option (int) - id of the first level menu options tab 
			v_subOption - id of the second level of options
	*/
	buildOptions:function(v_option,v_subOption)
	{
		$p.app.menu.clean();
		$p.app.menu.hideOptions();
        //returns the number of the last opened subOption
		var l_someOptions=$p.app.menu.getOptions(v_option,v_subOption);
		eval($p.app.menu.options[$p.app.menu.optionSelected]["action"]);
    },
	/*
		Function: open
                                $p.app.menu.open 
                                
                                Open the menu
                                
		Parameters:

			v_selOption (string) - option selected on menu opening
			v_forceOpen (boolean) - force the menu to open status
	*/
	open: function(v_selOption,v_forceOpen)
	{
		if ($p.app.menu.isOpen && v_forceOpen == indef) {
			$p.app.menu.close();
		}
		else {
			if (!$p.app.menu.initialized) {
				$p.app.menu.init();
            }
			//check if menu must be opened or not
			if ( __menuDefaultStatus == 4 && $p.app.env != "admin") {  
                return false; 
            }
			$p.app.menu.show();
			$p.app.menu.getActivateOptions(v_selOption);
            if($p.app.env != "admin")
            {
                $p.app.menu.buildOptions($p.app.menu.optionSelected,$p.app.menu.subOptSelected);
            }
            $p.app.menu.place();
		}
	},
	/*
		Function: show
                        
						$p.app.menu.show
                                
                                Show the menu
	*/
	show: function()
	{
		$p.plugin.hook.launch('app.menu.show.start');

        //Menu horizontal
		if (__menuposition == 'h')
        {
            widgetDecalY = $p.getPos($("area"),"Top")+30;
			$p.app.widgets.place($p.app.tabs.sel);
		}
		else
		{
			if (widgetDecalX != leftMenuWidth)
			{
				widgetDecalX = leftMenuWidth;
				
				/*
				if ($("menus") != null)
				{  
                    ($("menus")).style.marginLeft=widgetDecalX+"px";   
                }
				*/
				
				($("modules")).style.marginLeft=widgetDecalX+"px";
				
				if ($("plugin") != null)
				{
                    ($("plugin")).style.marginLeft=widgetDecalX+"px"; 
                }
				if ($("newspaper") != null)
				{
                    ($("newspaper")).style.marginLeft=widgetDecalX+"px"; 
                }			
				$p.app.menu.place();
				$p.app.widgets.place($p.app.tabs.sel);
			}
		}
		//display the menu
		//$p.show($p.app.menu.template[__menuposition].container,'block');
		//define the menu as opened
		$p.app.menu.isOpen = true;
		//save menu open/close status
    	$p.cookie.write('showmenu=1');
		adapt_widgetsareas_on_openclose_widgetmenu(false);
		$p.plugin.hook.launch('app.menu.show.end');
	},
	/*
		Function: place
                                $p.app.menu.place
        
                                Place the vertical menu
	*/
	place:function()
	{
  
		if (__menuposition == 'v') {
			//var l_leftMenuTopPos = $p.getPos($("area"),"Top")-110;
			//($("vmenu")).style.top = "" + (l_leftMenuTopPos + 22) +"px";
		} else {
            widgetDecalY=$('hmenu').offsetHeight;
			$p.app.widgets.place($p.app.tabs.sel);
        }

	},
	/*
		Function: init
                                $p.app.menu.displayCloseButton

                                Display the close button in the menu
	*/   
    displayCloseButton:function()
    {	//orig: menubuttons
		//$p.print('menucontent','<div id="menuclosebutton" style="text-align: left;"><span style="margin: 5px;"><a href="#" onclick="$p.app.menu.close()">'+lg("lblClose")+' </a></span></div>');
		var closeIcon=fri_get_closeIcon();
		$p.print('menubuttons','<div id="menuclosebutton" style="text-align: right;"><span style="margin: 5px;"><a title=\''+lg('lblClose')+'\' href="#" onclick="$p.app.menu.close()"><img src=\''+closeIcon+'\'></a></span></div>');
    },
	/*
		Function: close
                                $p.app.menu.close
                                
                                Close the menu
	*/
	close:function()
	{
		$p.app.menu.isOpen = false;
        $p.app.menu.initialized = false;
		$p.app.menu.hide();
        $p.app.menu.enableAllMenuLinks();
        
        //var divmodules=document.getElementById("modules");
        //var divmodules_left=divmodules.style.left;
    	//alert('divmodules_left: '+divmodules_left);
		adapt_widgetsareas_on_openclose_widgetmenu(true);
	},
	/*
		Function: hide
                                $p.app.menu.hide
                                
                                Hide the menu
	*/
	hide:function()
	{
		if (__menuposition == "h")    {
			widgetDecalY = 0;
			$p.show("hmenu","none");
		} 
		else    {
			if ($("menus") != null)
			{
				widgetDecalX = 0;
				$p.show("vmenu","none");
				if ($("menus") != null) {
                    ($("menus")).setStyle("marginLeft",widgetDecalX+"px"); 
                }    
                ($("modules")).setStyle("marginLeft",widgetDecalX+"px");
                //if a plugin is displayed
				if ($("plugin") != null) {
					($("plugin")).setStyle("marginLeft",widgetDecalX+"px");
                }
                //if the rss reader is opened
				if ($("newspaper") != null)   {
					($("newspaper")).style.marginLeft=widgetDecalX+"px";
					if ($p.app.env == 'portal_reader') $p.app.widgets.rss.reader.framesSize();
				}
			}
		}
        //redefine the widgets place
		$p.app.widgets.place($p.app.tabs.sel);
		if (__menuposition == "v")    { $p.app.pages.resize(); }
//		if ($p.app.banner.option.shown) {   $p.app.banner.option.hide(); }
		
		$p.cookie.write('showmenu=0');
	},
	/*
		Function: getActivateOption
                                $p.app.menu.getActivateOption 
                                
                                Search the activate option and suboption
                                
		Parameters:

			v_selOption (string) - option selected on menu opening
	*/
    getActivateOptions:function(v_selOption)
    {
        //search the menu option to activate
		if (v_selOption == indef) {
			$p.app.menu.optionSelected = indef;
			$p.app.menu.subOptSelected = indef;
		}
		else    {
			for (var i = 0;i < $p.app.menu.options.length;i++)
			{
				if ($p.app.menu.options[i]['id'] == v_selOption)
                {
					$p.app.menu.optionSelected = i;
					$p.app.menu.subOptSelected = indef;
                    $p.app.menu.optionSelectedId = v_selOption;
				}
			}
		}
    },
	/*
		Function: getOptions
                                $p.app.menu.getOptions
        
                                Get the selected options and Generate the menu options
                                
                      Parameters:
                      
                                v_option - id of the first level menu options tab 
			v_subOption - id of the second level of options
            
                        Returns:
                        
                                The id of the selected subOptions
	*/
	getOptions: function(v_option,v_subOption)
	{
        //unlock menus options and widget explorer
        $p.app.menu.menuOpt['confmenu_1']=false;
        $p.app.menu.menuOpt['addmenu_3']=false;
        
		if ($p.app.menu.options.length == 0) return false;

		var l_s = '';
		$p.app.menu.hideOptions();
		$p.app.menu.emptyContent();
		$p.app.menu.emptySubOptions();

		//tab selection
		if (v_option == indef)    {
			if ($p.app.menu.optionSelected == indef)  {
				$p.app.menu.optionSelected = 0;
			}
		}
		else    {
			$p.app.menu.optionSelected = v_option;
 		}
        
		//sub tab selection
		(v_subOption != indef)	? $p.app.menu.subOptSelected=v_subOption
								: '' ;
		if ($("menuoptions") != null)
		{
			if ($p.app.menu.options.length > 0)
			{
				/* FRI
				$p.show("menuoptions",$p.app.menu.template[__menuposition].displayType);
				l_s += '<ul class="menulist">'
				 */
				
			}

			var l_hasOptions = false;
			for (var i = 0;i < $p.app.menu.options.length;i++)
			{
				if ($p.app.menu.options[i]["pages"].length == 0 
                    || $p.array.find($p.app.menu.options[i]["pages"],$p.app.env))
				{
					l_hasOptions = true;
					//if the option is the selected one
					if (i == $p.app.menu.optionSelected)
					{
						l_s += '<li id="menuopt_'+$p.app.menu.options[i]["id"]+'" class="menulistsel">'
						if ($p.app.menu.template[__menuposition].options.showIcon)
						{
							l_s += $p.img($p.app.menu.options[i]["icon"],16,16,$p.app.menu.options[i]["desc"],"imgmid")+' ';
						}
						if ($p.app.menu.template[__menuposition].options.showLabel)
						{
							l_s += $p.app.menu.options[i]["label"];
						}
						l_s += '</li>';
					}
					else
					{
						l_s += '<li id="menuopt_'+$p.app.menu.options[i]["id"]+'">'
							+ '<a href="#" onclick="$p.app.menu.subOptSelected=indef;'
							+ ($p.app.menu.options[i]["action"] == '' ? '$p.app.menu.buildOptions('+i+');' : '$p.app.menu.getOptions('+i+');'+$p.app.menu.options[i]["action"]+';')
							+ 'return false;" title="'+$p.app.menu.options[i]["desc"]+'">';
						if ($p.app.menu.template[__menuposition].options.showIcon)
						{
							l_s += $p.img($p.app.menu.options[i]["icon"],16,16,$p.app.menu.options[i]["desc"],"imgmid")+' ';
						}
						if ($p.app.menu.template[__menuposition].options.showLabel)
						{
							l_s += $p.app.menu.options[i]["label"];
						}
						l_s += '</a>'
							+ '</li>';
					}
				}
			}
			$p.app.menu.emptyOptions();
			
			if ($p.app.menu.options.length > 0)
			{
				l_s += '</ul>';
			}
			$p.print("menuoptions",l_s);
		}
		return l_hasOptions;
	},
	/*
	         Function: addTitle
                                $p.app.menu.addTitle 
                                
                                Add a title element
		
                    Parameters:

			v_id(int) - item id
			v_icon(str) - icon
			v_label(str): item label
			v_fct(function) - additional function called on click on title
	*/
	addTitle:function(v_id,v_icon,v_label,v_fct)
	{
		var l_title=new Element('div',
			{
				'id':v_id,
				'class':'rodinBoardTitleBar'
			}
		);
		l_title.fct = v_fct;
		
		l_img=new $p.imgObj(v_icon,indef,indef,'','rodinBoardTitleImage');
		l_img.injectInside(l_title);
		
		l_label = new Element('span', {
			'id': 'vmenu_title',
			'class': 'rodinBoardTitleLabel'
		});
		l_label.appendText(v_label);
		l_label.injectInside(l_title);

		l_toggle = new Element('img', {
			'id': 'vmenuToggle',
			'onClick': "toggleBoardExpanded('addWidgetBoard')",
			'class': 'toggleBoardIcon'
		});
		l_toggle.injectInside(l_title);
		
		l_title.injectInside($('menucontent'));
		
		forceBoardExpanded('addWidgetBoard');
	},
	/*
		Function: addArea
                                $p.app.menu.addArea 
                                
                                Add a title element
		
                    Parameters:

			v_id(int) - title item ID
			v_html(str) - HTML of area
                                v_visible(boolean) - display le element or not 
	*/
	addArea:function(v_id,v_html,v_visible)
	{   
		var l_content = new Element('div',
			{
				'id':v_id+'content',
				'class': 'menuitemcontent',
				'name': 'boardContent'
			}
		);
		l_content.set('html',v_html);
		
		if (__menuposition == 'h') {
			l_content.injectInside($('menucontent'));
		}
		else {
            if (typeof($p.app.menu.menuOpt[v_id])=='undefined' 
                || $p.app.menu.menuOpt[v_id]==false) {
                    l_content.inject($(v_id),'after');
            }
            $p.app.menu.enableMenuLinks(v_id);
		}
		if (v_visible 
            && __menuposition == 'v') {
               // l_content.setStyle('display','block');
        }
		//compute menu placement and size
        $p.app.menu.place();
	},
 	/*
		Function: addHTML
                                $p.app.menu.addHTML 
                                
                                Add a HTML element
		
                    Parameters:

			v_id(int) - title item ID
			v_html(str) - HTML of area
	*/   
	addHTML:function(v_id,v_html)
	{
		var l_title=new Element('div',{
			'id':v_id,
            'class':'menuitemcontent'
		});
		l_title.set('html',v_html);
		l_title.injectInside($('menucontent'));
	},
	/*
		Function: sortOptions
                                $p.app.menu.sortOptions
                                
                                Sort header items
                      
                      Parameters: 
                      
                                 v_a - header item elment
                                 v_b - header item element
                      
                      Returns:
                            
                                -1, 1 or 0
	*/
	sortOptions:function(v_a,v_b)
	{
		if (v_a.seq < v_b.seq) return -1;
		if (v_a.seq > v_b.seq) return 1;
		return 0;
	},
	/*
		Function: showItem
                                $p.app.menu.showItem
                                
                                Display a menu item
                                
                     Parameters: 
                     
                                v_itemId - id of the item to display
	*/
	showItem:function(v_itemId)
	{
		//$p.show(v_itemId,'block');
	},
	/*
		Function: hideItem
                                $p.app.menu.hideItem
                                
                                Hide a menu item
                                
                     Parameters: 
                     
                                v_itemId - id of the item to hide
	*/
	hideItem:function(v_itemId)
	{
		$p.show(v_itemId,'none');
	},
	/*
                    Function: openSubMenu
                                $p.app.menu.openSubMenu
                    
                                Display sub menu options
		
                    Parameters:

			v_sMenuId - id of the submenu to display
			v_init - true for menu init
	*/
    
    openSubMenu:function(v_sMenuId,v_init)
	{
		if (__menuposition == 'h') {
			$p.app.menu.emptyContent();
			if ($(v_sMenuId).fct != indef) 
	            $(v_sMenuId).fct();
			//change buttons display
			var l_subMenus = ($('menusuboptions')).getChildren();
			for (var i = 0;i < l_subMenus.length;i++)
			{
				l_subMenus[i].className = 'menuitem';
			}
			$(v_sMenuId).className = 'menuitemselected';
		}
		else {          
            if ($(v_sMenuId).fct != indef) {
	            $(v_sMenuId).fct();
            }
			var l_subMenus=($('menucontent')).getChildren();
			for (var i = 0;i < l_subMenus.length;i++)
			{
       
				if (l_subMenus[i].getProperty('class')=='menuitemcontent') {           
                    if (l_subMenus[i].id == v_sMenuId+'content') {
						$p.app.menu.showItem(l_subMenus[i].id);
					}
					else {
						$p.app.menu.hideItem(l_subMenus[i].id);
	                }
				}
			} 
		}
	},
    /*
                    Function: refreshConditionalMenus
                                $p.app.menu.refreshConditionalMenus
                    
                                Refresh the menus which content is based on a contexte (specific page, ...)
	*/
    refreshConditionalMenus: function()
    {
        if ($p.app.menu.isOpen && $p.app.menu.options[$p.app.menu.optionSelected]["id"] == 'poptions')
        {
            $p.app.menu.config.refresh();
        }
    },
    /*
                    Function: openFirstOptionAvailable
                                $p.app.menu.openFirstOptionAvailable
                    
                                Open the first option available in the menu
                      Parameters :
                                v_menuPrefix : prefix of the name of the menus
	*/
    openFirstOptionAvailable: function(v_menuPrefix)
    {
        for (var i = 0;i < 15;i++)
        {
            if ($(v_menuPrefix+i) != null)
            {
                 $p.app.menu.openSubMenu(v_menuPrefix+i,true);
                return true;
            }
        }
        return false;
    }
}



//********************* MENUS WIDGETS FUNCTIONS ***********************************************************************************************************************
/*
    Class: $p.app.menu.widget
            Menu widget functions
*/
$p.app.menu.widget={
	leftmenuinit: false,
	locked: false,
    prevExplorerItem: {'id':indef,'lang':indef,'level':indef,'secured':indef},
	open: function()
	{
		$p.app.menu.open("pwidget",true);
	},
	/*
		Function: build
                                $p.app.menu.widget.build
                                
                                Initialize widget menu
	*/
	build: function()
	{
/*
		if (!$p.app.isCustomPortal())   {
			$p.app.menu.addHTML('featureNotAvailableOutsidePortal',lg("featureNotAvailableInScreen"));
			return false;
		}
		
		if ($p.app.tabs.sel<0 
            || $p.app.tabs.sel==999 
            || tab[$p.app.tabs.sel].locked 
            || $p.app.menu.widget.locked 
            || !tab[$p.app.tabs.sel].editable)  {
			//$p.app.alert.show(lg("featureNotAvailableInScreen"),1);
			$p.app.menu.addHTML('featureNotAvailableOutsidePortal',lg("featureNotAvailableInScreen"));
			return false;
		}
*/		


		if (showBoxStatus)
            hideBox();

        $p.plugin.hook.launch('app.menu.widget.addOptions1');

		if (__showrsscell && false) /*FRI*/
        {
			$p.app.menu.addTitle('addmenu_1','ico_rss2.gif',lg("lblAddRSS"),$p.app.menu.widget.displayRSSForm);
		}
        
        $p.plugin.hook.launch('app.menu.widget.addOptions2');

		if (__showModuleSearch && false) /*FRI*/
        {
			$p.app.menu.addTitle('addmenu_2','ico_search.gif',lg("lblSearch"),$p.app.menu.widget.displaySearchForm);
		}
        
        $p.plugin.hook.launch('app.menu.widget.addOptions3');

		if (__showModuleExpl)
        {
			$p.app.menu.addTitle('addmenu_3','ico_directory.gif',lg("lblExplore"),$p.app.menu.widget.displayExplorerForm);
        }
        
        $p.plugin.hook.launch('app.menu.widget.addOptions4');

		/*if (__useGroup 
			&& $p.app.user.id > 0 
			&& __usePrivateModules)
        {
			$p.app.menu.addTitle('addmenu_4','ico_directory_lock.gif',lg("privateModules"),$p.app.menu.widget.displaySecureExplorerForm);
		}*/
        
        $p.plugin.hook.launch('app.menu.widget.addOptions5');

		if (__userModuleJs=='I' 
			&& false
            && $p.app.user.id > 0) /*FRI*/
		{
			$p.app.menu.addTitle('addmenu_5','mymodules.gif',lg("lblArchive2"),$p.app.menu.widget.displayMyModule);
		}

        $p.plugin.hook.launch('app.menu.widget.addOptions6');

        if ($p.app.env=="admin") {
          //  $p.app.menu.menuOpt['addmenu_3']=false;
              $p.app.menu.enableAllMenuLinks();
        }
        
		if (__showModuleExpl)
		{
			$p.app.menu.openSubMenu('addmenu_3',true);
		}
		else
		{
			$p.app.menu.openSubMenu('addmenu_1',true);
		}

        $p.plugin.hook.launch('app.menu.widget.addOptions7');
	},
	/*
		Function: displayRSSForm
                                $p.app.menu.widget.displayRSSForm
                                
                                Display the rss form

	*/
    displayRSSForm:function()
    {
        var l_s='<form name="rss" onsubmit="return $p.app.widgets.rss.checkFeed();"><br />'
                +lg("enterRssUrl")+' :<br /><br />'
                +'<input class="thinbox" name="vars" type="text" size="25" onFocus=\'$p.app.tools.inputFocus(this,"'+lg("lblRSSFlow")+'")\' onBlur=\'$p.app.tools.inputLostFocus(this,"'+lg("lblRSSFlow")+'")\' value="'+lg("lblRSSFlow")+'" onclick=\'javascript:vars.value="";$p.print("authrss","");\' style="color:#aaaaaa" />'
                +'&nbsp;<input type="submit" name="butr" class="btn" value="Go" /> '+tooltip("msgRSShlp")
                +'<div id="authrss" style="width:190;text-align:left;display:none;"></div>'
                +'</form>';
                
		if ($p.app.user.id>0) {
            l_s+="<br /><a href='#' onclick='return $p.app.widgets.rss.importMenu();'>"+lg("lblImport")+"/"+lg("lblExport")+" (OPML)</a> "+tooltip("msgOpmlhlp");
        }

        $p.app.menu.addArea('addmenu_1',l_s);
    },
	/*
		Function: displaySearchForm
                                $p.app.menu.widget.displaySearchForm : Display the widget search form

	*/
	displaySearchForm: function()
	{
        $p.app.menu.addArea('addmenu_2','<form name="mod__search" onsubmit="$p.app.menu.widget.getSearch(document.mod__search.inputsearchwidget.value,0);return false;"><br />'+lg("searchModuleByKeywords")+' : <br /><br /><input class="thinbox" id="inputsearchwidget" name="inputsearchwidget" type="text" size="25" onFocus=\'$p.app.tools.inputFocus(this,"'+lg("keywords")+'")\' onBlur=\'$p.app.tools.inputLostFocus(this,"'+lg("keywords")+'");$p.tags.autocompletion.hide();\' value="'+lg("keywords")+'" onkeyup=\'$p.tags.autocompletion.get("inputsearchwidget")\' style="color:#aaaaaa" /> <input type="submit" name="buts" class="btn" value="Go" />&nbsp;'+tooltip("helpTags")+'<div id="listmod" style="width:210px;text-align:left;"></div></form>');
    },
	/*
		Function: displayExplorerForm
                                $p.app.menu.widget.displayExplorerForm : Display the widget explorer

	*/
	displayExplorerForm: function()
	{
        var l_s = '<div id="explorer">'
        + '<div id="exp0">loading ...</div>'
        + '</div>';
        $p.app.menu.addArea('addmenu_3',l_s);
        $p.app.menu.widget.getExplorer(0);
	},
	displaySecureExplorerForm: function()
	{
		var l_s = "<div id='exps0'>loading ...</div>";
		$p.app.menu.addArea('addmenu_4',l_s);
        $p.app.menu.widget.getSecuredExplorer(0);
	},
	/*
		Function: displayMyModule
                                $p.app.menu.widget.displayMyModule : Display the users widgets + create a widget link

	*/
	displayMyModule: function()
	{
		$p.app.menu.addArea('addmenu_5',lg('lblLoading'));
        $p.app.menu.widget.getMyModules();
	},
	/*
		Function: getSearch
                                $p.app.menu.widget.getSearch
                                
                                Search modules
                     
                     Parameters:
                     
 			v_s - search string
			v_page - results page number
	*/
	getSearch:function(v_s,v_page)
	{
		// Open the search results
		v_s=$p.string.formatForSearch($p.tags.formatList(v_s));
		v_s = $p.string.removeCot(v_s);
		$p.setClass("dirlink","optlist");
		$p.setClass("listlink","optlist");
		$p.setClass("sellink","optlist");
		$p.print("listmod",lg("searching"));
		v_s = $p.string.removeCot(v_s);
		$p.ajax.call(posh["xmlsearch"]+"?searchtxt="+$p.string.esc(v_s)+"&p="+v_page,
			{
				'type':'load',
				'callback':
				{
					'function':$p.app.menu.widget.displaySearch,
					'variables':
					{
						'searchtxt':v_s,
						'page':v_page
					}
				}
			}
		);
	},
	/*
		Function: displaySearch
                                $p.app.menu.widget.displaySearch
                                
                                Display module search results
                     
                     Parameters:
                     
 			response - XML object
			vars (array) - variables (optionnal)
	*/
	displaySearch:function(response,vars)
	{
		var l_result;

		if (response.getElementsByTagName("nbres1")[0])
		{
			var l_nbres1=$p.ajax.getVal(response,"nbres1","int",false,0);
			var l_nbres = l_nbres1;
			
			if (l_nbres1<10)
			{
				var l_nbres2=$p.ajax.getVal(response,"nbres2","int",false,0);
				l_nbres=eval(l_nbres)+eval(l_nbres2);
			}
			
			$('listmod').set('html',"<br />"+lg("lblResultsFor")+" '"+vars['searchtxt']+"' :");
			var divObj1 = new Element('div',
				{
					'styles': {
						'height': '180px'
					},
					'class':'dirdiva'
				}
			);
			var tableObj1 = new Element('table', { 'cellpadding':'0', 'cellspacing':'1' } );
				var tbodyObj1 = new Element('tbody');
			if (response.getElementsByTagName("item")[0])
			{
				for (var i=0;i<10;i++)
				{
					if (response.getElementsByTagName("item")[i])
					{
						l_result=response.getElementsByTagName("item")[i];
						var trObj1 = new Element('tr');
						var tdObj1 = new Element('td');
						v_icon = $p.ajax.getVal(l_result,"icon","str",false,0);
						if( v_icon!=0 ) {
							v_icon = $p.img(v_icon+"?rand="+rand,16,16);
						} else {
							v_icon = $p.img(+"box0_"+$p.ajax.getVal(l_result,"id","int",false,0),16,16,"","imgmid");
						}
						tdObj1.set('html',v_icon);
						var aObj1 = new Element('a',
							{ 
								'events': {
									'click': function()
									{
										$p.app.widgets.open(this.widId,indef,indef,(this.secured==0?false:true));
									}
								},
								'class':'menu1',
								'href':'#'
							} 
						);
						aObj1.widId=$p.ajax.getVal(l_result,"id","int",false,0);
						aObj1.secured=$p.ajax.getVal(l_result,"secured","int",false,0);
						aObj1.set('html',$p.ajax.getVal(l_result,"name","str",false,"..."));
						aObj1.inject(tdObj1);
						tdObj1.inject(trObj1);
						trObj1.inject(tbodyObj1);
					}
				}
			}
			else
			{
				var trObj2 = new Element('tr');
				var tdObj2 = new Element('td');
				tdObj2.set('html',lg("lblSrchNoMod"));
				tdObj2.inject(trObj2);
				trObj2.inject(tbodyObj1);	
			}
			tbodyObj1.inject(tableObj1);
			tableObj1.inject(divObj1);
			var tableObj2 = new Element ('table',
				{
					'styles': {
						'width': '95%'
					}
				}
			);
			var tbodyObj2 = new Element ('tbody');
			var trObj3 = new Element ('tr');

			if (vars['page']!=0)
			{
				var tdObj3 = new Element ('td');
				var aObj2 = new Element('a',
					{ 
						'events':
						{
							'click': function()
							{
								$p.app.menu.widget.getSearch(vars['searchtxt']+","+(parseInt(vars['page'])-1));
								return false;
							}
						},
						'href':'#'
					}
				);
				aObj2.set('html',$p.img("ico_previous3.gif",8,11,lg("lblPrevMods"))+" "+lg("previous"));
				aObj2.inject(tdObj3);
				tdObj3.inject(trObj3);
			}
			if (l_nbres==11)
			{
				var tdObj4 = new Element ('td',
					{
						'styles': {
							'text-align': 'right'
						}
					}
				);
				var aObj3 = new Element('a',
					{ 
						'events':
						{
							'click': function()
							{
								$p.app.menu.widget.getSearch(vars['searchtxt']+","+(parseInt(vars['page'])+1));
								return false;
							}												
						},
						'href':'#'
					}
				);
				aObj3.set('html',lg("next")+" "+$p.img("ico_next3.gif",8,11,lg("lblNextMods")));
				aObj3.inject(tdObj4);
				tdObj4.inject(trObj3);
			}
			trObj3.inject(tbodyObj2);
			tbodyObj2.inject(tableObj2);
			tableObj2.inject(divObj1);
			$p.show("listmod","block");
			divObj1.inject($('listmod'));
		}
		else
		{
			$('listmod').set('html',"<font style='color:#ff0000'>"+lg("lblSrch3car")+"</font>");
		}
	},
	/*
		Function: clearSearch
                                $p.app.menu.widget.clearSearch
                                
                                Erase search results
                     
                     Returns:
                     
 			false
	*/
	clearSearch:function()
	{
		$p.print("listmod","");
		return false;
	},
	/*
		Function: initDir
                                $p.app.menu.widget.initDir
                                
                                Initialize the modules directory
                     
                     Parameters:
                     
 			v_cat - directory ID
	*/
	initDir:function(v_cat)
	{
		// Initialize modules directory menu
		var tableObj1 = new Element('table', { 'cellpadding':'0', 'cellspacing':'0', 'border':'0' } );
		var tbodyObj1 = new Element('tbody');	
		var trObj1 = new Element('tr');	
		var tdObj1 = new Element('td');	
		var tableObj2 = new Element('table', 
			{ 
				'cellspacing':'0', 
				'width':'100%' 
			} 
		);
		var tbodyObj2  = new Element('tbody');
		var trObj2 = new Element('tr');	
		var tdObj2 = new Element('td');
		tdObj2.set('html',lg("lblNavDir")+" :" );
		tdObj2.inject(trObj2);
		var tdObj3 = new Element('td', { 'align':'right' } );
		tdObj3.inject(trObj2);
		trObj2.inject(tbodyObj2);
		tbodyObj2.inject(tableObj2);
		tableObj2.inject(tdObj1);
		tdObj1.inject(trObj1);
		trObj1.inject(tbodyObj1);
		
		var trObj3 = new Element('tr');	
		var tdObj4 = new Element('td');	
		var tableObj3 = new Element('table');	
		var tbodyObj3 = new Element('tbody');
		var trObj4 = new Element('tr');	
		var tdObj5 = new Element('td');	
		var divObj1 = new Element('div', 
			{
				'styles': {
					'width': '230px'
				},
				'id':'level1',
				'class':'dirdivi'
			}
		 );
		divObj1.inject(tdObj5);						 
		tdObj5.inject(trObj4);			
		var tdObj6 = new Element('td');	
		var divObj2 = new Element('div', 
			{
				'styles': {
					'width': '200px'
				},
				'id':'level2',
				'class':'dirdivi'
			}
		 );
		divObj2.inject(tdObj6);						 
		tdObj6.inject(trObj4);
		var tdObj7 = new Element('td');	
		var divObj3 = new Element('div', 
			{
				'styles': {
					'width': '200px'
				},
				'id':'level3',
				'class':'dirdivi'
			}
		 );
		divObj3.inject(tdObj7);						 
		tdObj7.inject(trObj4);
		var tdObj8 = new Element('td');	
		var divObj4 = new Element('div', 
			{
				'styles': {
					'width': '170px'
				},
				'id':'level4',
				'class':'dirdivi'
			}
		 );
		divObj4.inject(tdObj8);						 
		tdObj8.inject(trObj4);
		trObj4.inject(tbodyObj3);
		tbodyObj3.inject(tableObj3);
		tableObj3.inject(tdObj4);
		tdObj4.inject(trObj3);
		trObj3.inject(tbodyObj1);
		tbodyObj1.inject(tableObj1);
		$p.show("listmod","block");
		tableObj1.inject($("listmod"));
		$("listexample").set('html',$p.img("",7,7)+"<br />");	
		$p.print("level1","Chargement ...");
		$p.app.menu.widget.getDir(v_cat,1);	
	},
	/*
		Function: getDir
                                $p.app.menu.widget.getDir
                                
                                Get the module of the category
                     
                     Parameters:
                     
 			v_cat - directory ID
                                v_level - level
	*/
	getDir:function(v_cat,v_level)
	{
		// Open the modules directory
		$p.ajax.call("../cache/cat_"+v_cat+"_"+__lang+".xml?rand="+rand,
			{
				'type':'load',
				'callback':
				{
					'function':$p.app.menu.widget.displayDir,
					'variables':
					{
						'level':v_level
					}
				}
			}
		);
	},
	/*
		Function: displayDir
                                $p.app.menu.widget.displayDir
                                
                                Display the directory modules
                     
                     Parameters:
                     
 			response - XML object
                                vars (array) - variables (optionnal)
	*/
	displayDir:function(response,vars)
	{
		var l_s="";
		if (response.getElementsByTagName("parent")[0])
		{
			l_s+="<table cellpadding='0' cellspacing='1' border='12' style='width:90%'>";
			if (response.getElementsByTagName("dir")[0])
			{
				var l_i=0,l_dirid;
				while (response.getElementsByTagName("dir")[l_i])
				{
					var l_result=response.getElementsByTagName("dir")[l_i];
					l_dirid=$p.ajax.getVal(l_result,"dirid","int",false,0);
					l_dirquantity=$p.ajax.getVal(l_result,"quantity","int",false,0);
					if (l_dirquantity!=0) l_s+="<tr><td id='dir"+l_dirid+"' class='catopti' onmouseover='catOptOver(\""+l_dirid+"\")' onmouseout='catOptOut(\""+l_dirid+"\")'>&nbsp;<a href='#' class='menul' onclick='$p.app.menu.widget.getDir("+l_dirid+","+(vars['level']+1)+");catOptSel("+l_dirid+","+vars['level']+");return false;'>"+$p.ajax.getVal(l_result,"dirname","str",false,"...")+" ("+l_dirquantity+")</a></td></tr>";
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
					/*v_icon = $p.ajax.getVal(l_result,"icon","str",false,0);
						if( v_icon!=0 ) {
							v_icon = $p.img(v_icon+"?rand="+rand,16,16);
						} else {
							v_icon = $p.img(+"box0_"+$p.ajax.getVal(l_result,"id","int",false,0),16,16,"","imgmid");
						}*/
					l_s+="<tr><td>"+$p.img(_dirImg+"box0_"+l_itemid,16,16,"","imgmid")+"&nbsp;<a href='#' class='menul' onclick='$p.app.widgets.open("+l_itemid+");return false;'>"+$p.ajax.getVal(l_result,"name","str",false,"...")+"</a></td></tr>";
					l_i++;
				}
			}
			l_s+="</table>";
		}
		else {l_s+=lg("lblDisplayErr");}

		$p.setClass("level"+vars['level'],"dirdiva");
		$p.print("level"+vars['level'],l_s);
		//clear unused div
		for (var i=vars['level']+1;i<5;i++){$p.print("level"+i,"");$p.setClass("level"+i,"dirdivi");}
	},
	/*
		Function: 
                                $p.app.menu.widget.getExplorer
                                
                                Load modules directory branches
		
                    Parameters:

			v_cat - directory ID
			v_open - open / close directory branch
			v_lang - widget language
	*/
	getExplorer:function(v_cat,v_open,v_lang,v_secured,v_level)
	{		
		if (v_open == indef) v_open = 1;
        if (v_level == indef) v_level = 1;
        if (v_open == 1)
        {
            if ($p.app.menu.widget.prevExplorerItem.id && v_level == $p.app.menu.widget.prevExplorerItem.level)
            {
                $p.app.menu.widget.getExplorer($p.app.menu.widget.prevExplorerItem.id,0,$p.app.menu.widget.prevExplorerItem.lang,$p.app.menu.widget.prevExplorerItem.secured,$p.app.menu.widget.prevExplorerItem.level);
            }
            $p.app.menu.widget.prevExplorerItem = {'id':v_cat,'level':v_level,'lang':v_lang,'secured':v_secured}
        }
        
		if (v_open == 1) $p.app.wait("exp"+v_cat);

        if (v_secured)
        {
            var l_url = posh["xmlexplorer"]+'?dirid='+v_cat+'&rand='+rand;
        }
        else
        {
            var l_url = "../cache/cat_"+v_cat+(v_cat == 0 ? "" : "_"+v_lang)+".xml?rand="+rand;
        }

		$p.ajax.call(l_url,
			{
				'type':'load',
				'callback':
				{
					'function':$p.app.menu.widget.displayExplorer,
					'variables':
					{
						'category':v_cat,
						'open':v_open,
						'prefix':'',
						'language':v_lang,
                        'secured':v_secured,
                        'level':v_level
					}
				}
			}
		);

		return false;
	},	
	/*
		Function: displayExplorer
                                $p.app.menu.widget.displayExplorer
                                
                                Display modules directory branch
                                
                     Parameters:
                                
                                response - XML object
                                vars (array) - variables (optional) 
	*/
	displayExplorer:function(response,vars)
	{
        (!$p.app.menu.isOpen) ? $p.app.menu.open() : '';

		if (__menuposition == 'h')
        {
            var l_container = $('explorer');
            l_container.empty();
			var l_pathDiv = new Element('div',
				{
					'id':'explorerpath'
				}
			);
			l_pathDiv.set('html',$p.ajax.getVal(response,"path","str",false,""));
			l_pathDiv.inject(l_container);
			var l_divObj = new Element('div',{
					'id':'exp0'
				}
			);
			l_divObj.inject(l_container);
		}
		else
        {
            if ($("exp"+vars['prefix']+vars['category']) == null)
            {
                var l_container = new Element('div');
            }
            else
            {
                var l_container = $("exp"+vars['prefix']+vars['category']);
            }

            l_container.empty();
            var l_divObj = new Element('span'); // keep span instead of div for better display
            l_divObj.inject(l_container);

			if (vars['category'] != 0) {	
				var aObj = new Element('a',
					{
						'events':
						{
							'click': function()
							{
								return $p.app.menu.widget.getExplorer(vars['category'],(vars['open']==0?"1":"0"),vars['language'],vars['secured'],vars['level']);
							}
						},
						'class': 'dirlink',
						'href': '#'
					}
				);
				aObj.set('html',    $p.img((vars['open'] == 1 ? "ico_directory_open.gif" : "ico_directory.gif"),16,13)
                                    + " "+$p.ajax.getVal(response,"dirname","str",true,"--")
                                    + (vars['secured'] == 0 ? "" : " "+$p.img("lock.gif",7,9))
                                    + "<br />");
				aObj.inject(l_divObj);
			}
		}
        

		if (response.getElementsByTagName("parent")[0] && vars['open']==1)
		{
            var l_subDirectoriesExisting = true;

			if (response.getElementsByTagName("dir")[0])
			{
				var l_i=0;
                
				while (response.getElementsByTagName("dir")[l_i])
				{
					var l_result = response.getElementsByTagName("dir")[l_i];
                    
					var l_dirid = $p.ajax.getVal(l_result,"dirid","int",false,0);
					var l_dirquantity = $p.ajax.getVal(l_result,"quantity","int",false,0);
                    var l_dirSecuredquantity = $p.ajax.getVal(l_result,"secured_quantity","int",false,0);
                    var l_secured = $p.ajax.getVal(l_result,"secured","int",false,0);
					var l_lang = $p.ajax.getVal(l_result,"lang","str",false,"");
                    var l_quantity = l_dirquantity + ($p.app.user.id <= 0 ? 0 : l_dirSecuredquantity);
                    
					if ((l_secured == 0 || (__useGroup && $p.app.user.id > 0 && __usePrivateModules))
                        && (
                            __displayAllLanguageModules 
                            || (!__displayAllLanguageModules && (l_lang=="" || l_lang==__lang))
                            )
                        )
					{
						if (l_quantity != 0 || vars['category'] == 0) 
						{
							var divObj1 = new Element('div', 
								{ 
									'class':'expdir',
									'id':"exp"+vars['prefix']+l_dirid
								}
							);
							var aObj1 = new Element('a',
								{
									'events':
									{
										'click': function()
										{
											return $p.app.menu.widget.getExplorer(this.l_dirid,indef,this.l_lang,this.secured,this.level);
										}
									},
									'class': 'dirlink',
									'href': '#'
								}
							);
							
							aObj1.l_dirid = l_dirid;
							aObj1.l_lang = l_lang;
                            aObj1.secured = l_secured;
                            aObj1.level = vars['level']+1;
							aObj1.set('html',   $p.img("ico_directory.gif",16,13)
                                                + " "+$p.ajax.getVal(l_result,"dirname","str",false,"...")
                                                + " ("+l_quantity+")"
                                                + (l_secured == 0 ? "" : " "+$p.img("lock.gif",7,9))
                            );
							aObj1.inject(divObj1);
							divObj1.inject(l_divObj);
						}
					}
					l_i++;
				}
			}
            else
            {
                l_subDirectoriesExisting = false;
            }
			if (response.getElementsByTagName("item")[0])
			{
				var l_i=0;
				while (response.getElementsByTagName("item")[l_i])
				{
					var l_result = response.getElementsByTagName("item")[l_i],
                        l_itemid = $p.ajax.getVal(l_result,"id","int",false,0);
					
					var divObj3 = new Element('div');
                    
					v_icon = $p.ajax.getVal(l_result,"icon","str",false,0);
                    
					if( v_icon != 0 )
                    {
						v_icon = $p.img(v_icon+"?rand="+rand,16,16,"","imgmid",l_itemid);
					} else {
						v_icon = $p.img(_dirImg+"box0_"+l_itemid,16,16,"","imgmid");
					}		
					divObj3.set('html',v_icon+"&nbsp;");
					divObj3.itemid = l_itemid;

					var aObj3 = new Element('a',
						{
							'events':
							{
								'click': function()
								{
									$p.app.widgets.open(this.itemid,indef,indef,this.secured);
								}
							},
							'class': 'modlink',
							'href':'#'
						}
					);
					aObj3.itemid = l_itemid;
                    aObj3.secured = $p.ajax.getVal(l_result,"secured","int",false,0);
					aObj3.set('html',$p.ajax.getVal(l_result,"name","str",false,"..."));
					aObj3.inject(divObj3);
					divObj3.inject(l_divObj);
					//initialize items drag
					$p.app.widgets.move.init(divObj3);
					//item drag actions
					divObj3.onDrag=$p.app.menu.widget.explorerOnDrag;
					divObj3.onDragStart=$p.app.menu.widget.explorerOnDragStart;

					l_i++;
				}
			}
            else if (vars['category'] != 0 && !l_subDirectoriesExisting)
            {
                var l_divNoItem = new Element('div');
                l_divNoItem.set('html',lg('accessRestricted'));
                l_divNoItem.inject(l_divObj);
            }
		}
	},
	/*
		Function: explorerOnDragStart
                                $p.app.menu.widget.explorerOnDragStart
                                
                                Display modules directory branch
	*/
	explorerOnDragStart:function()
	{
		this.isDrag='mousedown';
	},
	/*
		Function: explorerOnDrag
                                $p.app.menu.widget.explorerOnDrag
                                
                                Display modules directory branch
                                
                     Parameters:
                     
                                v_x: coordonate
                                v_y: coordonate
	*/
	explorerOnDrag:function(v_x,v_y)
	{
		if (this.isDrag=='firstmove')
		{
			//display the widget
			this.isDrag=true;
			$p.app.widgets.open(this.itemid,indef,indef,indef,false,false);
			
			$p.ajax.call('../../app/tests/LoggerResponder.php?action=2&id=' + tab[$p.app.tabs.sel].module[tab[$p.app.tabs.sel].module.length-1].id +
					"&name=" + get_datasource_name(tab[$p.app.tabs.sel].module[tab[$p.app.tabs.sel].module.length-1].url) +
					"&tab=" + tab[$p.app.tabs.sel].label, {'type':'load'});

			//initialize widget move
			var newWidget=tab[$p.app.tabs.sel].module[tab[$p.app.tabs.sel].module.length-1].uniq;
			$p.app.widgets.move.obj=$('module'+tab[$p.app.tabs.sel].id+'_'+newWidget+'_h');
			$p.app.widgets.move.start($p.navigator.IE?indef:0);

			var l_obj=$p.app.widgets.move.shadow();
			//e=$p.app.widgets.move.fixE($p.navigator.IE?indef:e);
			l_obj.setStyle("left",v_x-10+"px");
			l_obj.setStyle("top",v_y-10+"px");
		}
		if (this.isDrag=='mousedown') {
			var duplicatedName = $p.app.widgets.isOpenInTab(this.itemid);
			
			if (duplicatedName) {
				alert(lg("lblDuplicatedWidget", duplicatedName));
				this.isDrag=false;
			} else {
				this.isDrag='firstmove';
			}
		}
	},
	/*
                Function: getSecuredExplorer
                            $p.app.menu.widget.getSecuredExplorer
                            
                            Load secured modules (group restrictions) directory branches
		
                Returns:

                            false
	*/
	getSecuredExplorer:function()
	{
		$p.ajax.call(posh["xmlexplorer"]+"?rand="+rand,
			{
				'type':'load',
				'callback':
				{
					'function':$p.app.menu.widget.displaySecuredExplorer
				}
			}
		);
		return false;		
	},
	/*
		Function: displaySecuredExplorer
                                $p.app.menu.widget.displaySecuredExplorer
                                
                                Display modules directory branch
                                
                      Parameters:
                            
                                response - XML object
                                vars (array) - variables (optionnal)
	*/
	displaySecuredExplorer:function(response,vars)
	{
		var l_s="";
		if (response.getElementsByTagName("item")[0])
		{
			var l_i=0,l_itemid;
			while (response.getElementsByTagName("item")[l_i])
			{
				var l_result=response.getElementsByTagName("item")[l_i];
				l_itemid=$p.ajax.getVal(l_result,"id","int",false,0);
				l_s+='<div>'
					+$p.img('../modules/pictures/box0_'+l_itemid,16,16,'','imgmid')
					+'&nbsp;<a href="#" class="modlink" onclick="$p.app.widgets.open('+l_itemid+',indef,indef,true);return false;">'
					+$p.ajax.getVal(l_result,'name','str',false,'...')+'</a>'
					+'</div>';
				l_i++;
			}
		}
		else l_s=lg("lblNoModule");

		//if (vars[0]!=0) $p.setClass("exp"+vars[0],(vars[1]==0?"expdiri":"expdira"));
		$p.print("exps0",l_s);
	},
	/*
    
		Function: 
                                $p.app.menu.widget.getMyModules
                                
                                Load list of the modules I created
	*/
	getMyModules:function()
	{
		$p.ajax.call(posh["xmlmymodules"],
			{
				'type':'load',
				'callback':
				{
					'function':$p.app.menu.widget.displayMyModules
				}
			}
		);
	},
	/*
		Function: displayMyModules
                                $p.app.menu.widget.displayMyModules
                                
                                Display list of the modules I created
                                
                     Parameters:
                     
                                response - XML object
                                vars (array) - variables (optionnal)
	*/
	displayMyModules:function(response,vars)
	{   
		var l_s = '<a href="#" onclick="$p.app.widgets.factory.init();return false;">'
	            + $p.img("mymodules_create.gif",16,16,"","imgmid")
            + ' '
            + lg("createYourModules")
            + '</a>'
            + '<br /><br />';

        var l_result = response.getElementsByTagName("module");

        if (l_result.length == 0)
        {
            l_s += lg("lblNoModule");
        }
        else
        {
    		for (var i = 0;i < l_result.length;i++)
    		{   
    			if ($p.ajax.getVal(l_result[i],"status","str",false,"N")=="O") 
                l_s += '<a href="#" onclick="$p.app.widgets.open('+$p.ajax.getVal(l_result[i],"id","int",false,0)+',indef,indef,'+($p.ajax.getVal(l_result[i],"secured","int",false,0)==0?false:true)+')">'
                    + $p.img("puce.gif",3,5,"","imgmid")
                    + ' '
                    + $p.ajax.getVal(l_result[i],"name","str",false,"???")
                    + '</a><br/>';
    		}
        }
        
		$p.print('addmenu_5content',l_s);
	}
}

//********************* MENUS CONFIG FUNCTIONS ***********************************************************************************************************************
/*
    Class: $p.app.menu.config
            Menu config functions
*/  
$p.app.menu.config={
	oldStyle:1,
	leftmenuinit:false,
	tempIcon:"",
	/*
		Function: $p.app.menu.config.build
                                Build the config menu              
	*/
	build:function()
	{
/*    
		if (!$p.app.isCustomPortal() 
            && $p.app.env!='portal_frame')
		{
			$p.app.menu.addHTML('featureNotAvailableOutsidePortal',lg("featureNotAvailableInScreen"))
			return false;
		}
		if ($p.app.tabs.sel<0 
            || $p.app.tabs.sel==999 
            || tab[$p.app.tabs.sel].locked 
            || !tab[$p.app.tabs.sel].editable)
		{
			//$p.app.alert.show(lg("featureNotAvailableInScreen"),1);
			$p.app.menu.addHTML('featureNotAvailableOutsidePortal',lg("featureNotAvailableInScreen"))
			return false;
		}
 */
		// Open the "edit" menu
		if (showBoxStatus){hideBox();}
		$p.app.menu.config.oldStyle=tab[$p.app.tabs.sel].style;

        $p.plugin.hook.launch('app.menu.config.addOption1');

        if ($p.app.env == 'portal_page_conn'
            || $p.app.env == 'portal_page_anon'
            || $p.app.env == 'portal_frame')
        {
            $p.app.menu.addTitle('confmenu_1','',lg("optionsOfThisPage"),$p.app.menu.config.buildPortalOptions);
        }

        $p.plugin.hook.launch('app.menu.config.addOption2');

		if ($p.app.user.id != 0
            && $p.navigator.IE) {
			$p.app.menu.addTitle('confmenu_2','',lg("optionsOfThePortal"),$p.app.menu.config.buildGeneralOptions);
		}

        $p.plugin.hook.launch('app.menu.config.addOption3');

        $p.app.menu.openFirstOptionAvailable('confmenu_');
        
	},
	buildPortalOptions: function()
	{
		var l_s = '<form name="option">';
        
		if (__menuposition == 'h') {
			l_s += '<table width="100%">'
                + '<tr>';
		}
		
		if ((__columnchange 
             && $p.app.isCustomPortal()
            ) 
                || ($p.app.user.id!=0 
                    && __doubleprotection 
                    && $p.app.isCustomPortal() 
                    && tab[$p.app.tabs.sel].shared==0)
           ) 
        {
			if (__menuposition == 'h')
                l_s+= '<td valign="top" width="30%">';
			l_s += '<div class="title">'
                + lg("organisationOptions")
                + '</div>';
			if (__columnchange && $p.app.isCustomPortal())
            {
                l_s += "<div>"
                    + lg("lblColNb")
                    + " &nbsp; "
                    + "<input type='button' value='-' style='width:20px' onclick='$p.app.menu.config.supCols()' /> "
                    + "<input type='text' name='nbcol' value='"+tab[$p.app.tabs.sel].colnb+"' style='text-align: center;width: 20px;border: 0px;border-bottom: 1px solid #c6c3c6;background: #fff;' size='1' disabled /> "
                    + "<input type='button' value='+' style='width:20px' onclick='$p.app.menu.config.addCols()' />"
                    + "</div>";
            }
			if ($p.app.user.id!=0 
				&& __doubleprotection 
				&& $p.app.isCustomPortal()
				&& tab[$p.app.tabs.sel].shared==0)
			{
    			l_s += '<div>'
    			    + '<input type="checkbox" name="usepass"'
                    + ((tab[$p.app.tabs.sel].lock==1)?' checked="checked"':'')
                    + ' /> '+lg("lblSecurity")+' '+tooltip('msgSecurityhlp')
                    + '</div>';
			}
			if (__menuposition == 'h') l_s += '</td>';
		}

		if ((__themeList.length>1 && __displayThemeSelector) || __showtabicon)
		{
			if (__menuposition == 'h')
                l_s += '<td valign="top" width="30%">';
			
			l_s += '<div class="title">'
                + lg("graphicalOptions")
                + '</div>';
			if (__themeList.length>1 && __displayThemeSelector)
            {
                l_s += "<div>"
                    + lg("lblColors")
                    + " &nbsp; <input type='button' value='<' style='width:20px' onclick='$p.styles.prevstyle();' /> "
                    //+ "<span width='120' id='selstyle' style='border-bottom: 1px solid #c6c3c6;text-align: center;width: 120px;overflow: hidden;'>"+__themeList[tab[$p.app.tabs.sel].style-1]+"</span>"
                    + "<input type='text' name='selstyle' value='"+__themeList[tab[$p.app.tabs.sel].style-1]+"' style='text-align: center;width: 100px;border: 0px;border-bottom: 1px solid #c6c3c6;background: #fff;' size='14' disabled /> "
                    + " <input type='button' value='>' style='width:20px' onclick='$p.styles.nextstyle();' />"
                    + "</div>"
                    + "<br />";
            }
			if (__showtabicon){
				l_s += '<div class="hightlight">'
					+ lg("addIconToPage")+' :'
					+ '<div id="pageicons" style="width:100%">'
                    + '</div>'
					+ '</div>';
			}
			if (__menuposition == 'h') l_s += '</td>';
		}

		if ((__usereader && $p.app.isCustomPortal()) || (__ctrlhiding && $p.app.isCustomPortal()) || (__moduleAlign && $p.app.isCustomPortal()) || $p.app.isCustomPortal())
		{
			if (__menuposition == 'h') l_s += '<td valign="top" width="30%">';
			l_s += '<div class="title">'
                + lg("widgetOptions")
                + '</div>';

			if (__usereader && $p.app.isCustomPortal())
                l_s += "<div><input type='checkbox' name='usereader' "+(tab[$p.app.tabs.sel].usereader==1?"checked='checked' ":"")+"/> "+lg("lblUseInternalReader")+" "+tooltip("msgReaderhlp")+"</div>";
			if (__ctrlhiding && $p.app.isCustomPortal())
                l_s += "<div><input type='checkbox' name='controls' "+((tab[$p.app.tabs.sel].controls=='Y')?"checked='checked' ":"")+"/> "+lg("lblMoveMods")+" "+tooltip("msgCtrlhlp")+"</div>";
			if (__moduleAlign && $p.app.isCustomPortal())
                l_s += "<div><input type='checkbox' name='modulealign' "+((tab[$p.app.tabs.sel].moduleAlign)?"checked='checked' ":"")+"/> "+lg("lblModuleAlign")+" "+tooltip("msgModuleAlign")+"</div>";
			if ($p.app.isCustomPortal())
                l_s += "<div><input type='checkbox' name='loadonstart' "+((tab[$p.app.tabs.sel].loadstart==1)?"checked='checked' ":"")+"/> "+lg("loadOnStart")+" "+tooltip("msgLoadStarthlp")+"</div>";

			if (__menuposition == 'h') l_s += '</td>';
		}

		if (__menuposition == 'h')
		{
			l_s += '</tr>'
				+ '</table>';
		}
        
		l_s += '<center>'
			+ '<br /><input class="btnbig" type="button" value="'+lg("lblBtnValid")+'" onclick="$p.app.menu.config.save();" /><br />'
			+ '</center>'
			+ '</form>';
            
        if (typeof($p.app.menu.menuOpt['confmenu_1'])=='undefined' 
            || $p.app.menu.menuOpt['confmenu_1']==false) {
                $p.app.menu.addArea('confmenu_1',l_s);
                if (__showtabicon) {
                    $p.app.menu.config.showIcons();
                    if (tab[$p.app.tabs.sel].icon!=""){
                        $p.app.menu.config.setIcon(indef,tab[$p.app.tabs.sel].icon);
                    }
                    else    {
                        $p.app.menu.config.setIcon(-1);
                    }
                } 
        }
	},
	buildGeneralOptions: function()
	{
		$p.app.menu.addArea('confmenu_2',"<br />"+$p.img("ico_home.gif",14,14)+" <a class='menul' href='#' onclick='$p.navigator.addHome();return false;'>"+lg("lblHome",__APPNAME)+"</a><br />"+$p.img("ico_favorite.gif",14,14)+" <a class='menul' href='#' onclick='$p.navigator.addFav();return false;'>"+lg("lblFav",__APPNAME)+"</a>");
	},
	/*
		Function: $p.app.menu.config.showIcons
                                Display icons list for portal personalization             
	*/
	showIcons:function()
	{
		//no icon link
		var aLvl1 = new Element('a', 
			{ 
				'events': {
						'click': function(){
								$p.app.menu.config.setIcon(-1); }
						  },
				'href':'#'
			} 
		);
		aLvl1.set('html',lg("noIcon"));
		aLvl1.inject($("pageicons"));
					
		for (i=0;i<__nbicons;i++)
		{
			var imgM = "imgLvlMulti"+i;
			var imgM = new Element('img',
				{ 
					'styles':
					{
						'width':'16px',
						'height':'16px',
						'border':'1px solid #ffffff',
						'vertical-align':'middle'
					},
					'events':
					{
						'click': function()
						{
							$p.app.menu.config.setIcon(this.name); 
						}
					},
					'id':'icon'+i,
					'src':_dirImg+'_deficon'+i+'.gif',
					'align':'absmiddle'
				}
			);
			imgM.name = i;
			imgM.injectInside($("pageicons"));	
		}

	},
	/*
		Function: $p.app.menu.config.hide
                                Hide edit menu                 
	*/
	hide:function()
	{
		$p.app.menu.close();
		//re-initialize the colors (if not saved)
		if (tab[$p.app.tabs.sel].style!=$p.app.menu.config.oldStyle)    {
			tab[$p.app.tabs.sel].style=$p.app.menu.config.oldStyle;
			$p.styles.setActiveStyleSheet(tab[$p.app.tabs.sel].style);
		}
	},
	/*
		Function: $p.app.menu.config.addCols
                                Increase page column
	*/
	addCols:function()
	{
		l_colnb=parseInt(document.forms["option"].nbcol.value,10);
		if (l_colnb<9)  {
			l_colnb++;
			document.forms["option"].nbcol.value=l_colnb;
			tab_columns=l_colnb;
		}
	},
	/*
		Function: $p.app.menu.config.supCols
                                Remove page column
	*/
	supCols:function()
	{
		l_colnb=parseInt(document.forms["option"].nbcol.value,10);
		if (l_colnb>1)  {
			l_colnb--;
			document.forms["option"].nbcol.value=l_colnb;
		}
	},
	/*
		Function: $p.app.menu.config.setIcon
                                Define page icon
                                
                     Parameters:
                     
                                v_id: tab ID
                                v_url: url of the icon
	*/
	setIcon:function(v_id,v_url)
	{
		for (var i=0;i<__nbicons;i++)
		{
			if (v_id==indef)    {
				($("icon"+i)).style.borderColor=(_dirImg+"_deficon"+i+".gif"==v_url?"#ff0000":"#ffffff");
			}
			else    {
				($("icon"+i)).style.borderColor=(i==v_id?"#ff0000":"#ffffff");
			}
		}
		if (v_id==indef)    {
			$p.app.menu.config.tempIcon=v_url;
		}
		else    {
			$p.app.menu.config.tempIcon=(v_id==-1?"":_dirImg+"_deficon"+v_id+".gif");
		}
		//write the icon value in a hidden field to get it with $_post 
		(document.forms[0].hiddenIconValue)?$('hiddenIconValue').value=$p.app.menu.config.tempIcon:'';
	},
	/*
		Function: $p.app.menu.config.save
                                Save page options changes
	*/
	save: function()
	{
		//save the settings
		var l_form = document.forms['option'];
		var l_alarm = true;
        //security
        var l_secu = 0;

        if ($p.app.user.id != 0)  {
            if (l_form.usepass) {
                l_secu = (l_form.usepass.checked)     ?   1
                                                      :   0;
            }
        }
		//info banner
		if (l_form.useadvise && $p.app.isCustomPortal())   {
			$p.app.banner.info.requested = (l_form.useadvise.checked)?"Y":"N";
	/*		if ($p.app.banner.info.requested == "Y" 
                && $p.app.banner.info.loaded 
                && $p.app.banner.info.shown) {
                    $p.app.banner.info.load();
            }*/
		}
		//style
		$p.app.menu.config.oldStyle = tab[$p.app.tabs.sel].style;
		//use reader
		var l_usereader = (l_form.usereader && $p.app.isCustomPortal())   ?   (__usereader
                                                                            ?   (l_form.usereader.checked
                                                                                ?   1
                                                                                :   0)
                                                                            :   0)
                                                                        :   tab[$p.app.tabs.sel].usereader;
		//controls
		var l_controls = (l_form.controls && $p.app.isCustomPortal())     ?       (l_form.controls.checked
                                                                            ?       "Y"
                                                                            :       "N")
                                                                        :       tab[$p.app.tabs.sel].controls;
                                            
		var l_moduleAlign = (l_form.modulealign && $p.app.isCustomPortal()) ?       l_form.modulealign.checked
                                                                            :       tab[$p.app.tabs.sel].moduleAlign;
        
		tab[$p.app.tabs.sel].label = $p.app.tabs.currName;
		tab[$p.app.tabs.sel].lock = l_secu;
		//tab[$p.app.tabs.sel].lock=jspass;
		
		//Columns
		l_nbCol = (l_form.nbcol && $p.app.isCustomPortal()) ?       parseInt(l_form.nbcol.value,10)
                                                            :       tab[$p.app.tabs.sel].colnb;
        if ($p.app.isCustomPortal())
        {
    		var l_allowSuppress = $p.app.pages.columns.isEmpty(l_nbCol);
    		if (l_allowSuppress)    {
    			if (l_nbCol>tab[$p.app.tabs.sel].colnb) {
    				$p.app.pages.columns.add(l_nbCol);
    			}
    			else if (l_nbCol<tab[$p.app.tabs.sel].colnb)    {
    				$p.app.pages.columns.sup(l_nbCol);
    			}
    		}
    		else    {
    			l_nbCol=tab[$p.app.tabs.sel].colnb;
    			l_alarm=false;
    		}
        }
		//if (p_area.showType!=l_showType) changeShowType(l_showType);
		if (tab[$p.app.tabs.sel].controls != l_controls && $p.app.isCustomPortal())  {
			$p.app.widgets.switchHdr();
		}
		if (tab[$p.app.tabs.sel].moduleAlign != l_moduleAlign && $p.app.isCustomPortal())    {
			$p.app.widgets.align(tab[$p.app.tabs.sel].moduleAlign);
		}
		if (tab[$p.app.tabs.sel].showType == 1
            && l_nbCol != tab[$p.app.tabs.sel].colnb
            && $p.app.isCustomPortal()) {
                $p.app.alert.show(lg("lblColNbErr"));
        }
		
		tab[$p.app.tabs.sel].usereader = l_usereader;
		tab[$p.app.tabs.sel].icon = $p.app.menu.config.tempIcon;
		tab[$p.app.tabs.sel].loadstart = (l_form.loadonstart && $p.app.isCustomPortal())      ?       (l_form.loadonstart.checked
                                                                                                  ?       1
                                                                                                  :       0)
                                                                                              :       0;
		$p.app.tabs.create($p.app.tabs.sel);
		$p.ajax.call(posh["scr_config_options"],
			{
				'type':'execute',
				'variables':"prof="+tab[$p.app.tabs.sel].id+"&portstyle="+tab[$p.app.tabs.sel].style+"&portname="+$p.string.esc($p.app.tabs.currName)+"&col="+l_nbCol+"&advise="+$p.app.banner.info.requested+"&usepass="+l_secu+"&usereader="+l_usereader+"&ctrl="+l_controls+"&align="+(l_moduleAlign?"Y":"N")+"&icon="+$p.app.menu.config.tempIcon+"&load="+tab[$p.app.tabs.sel].loadstart,
				'alarm':l_alarm
			}
		);
		//if (l_allowSuppress) $p.app.menu.config.hide();
	},
    refresh: function()
    {
        $p.app.menu.open("poptions",true);
    }
}


//************************************* TABS FUNCTIONS ***************************************************************************************************************
/*
    Class: $p.app.tabs
         Tabs functions
*/
$p.app.tabs={
	isInit:false,
	sel:indef,
	selId:0,
	currName:"",
	overtabid:-1,
	currHeadLink:{},
	locked:false,
	defTheme:indef,
	/*
		Function: object 
                               $p.app.tabs.object  *(Constructor)*
                    
                                Object tab, define a tab 
                      
                      Parameters:

			id - uniq id of the tab
			label - title written in the tab object
			type - type of the tab (personalized page, frame, ...)
			fct - function called when tab is opened
			lock (boolean) - is the tab locked ?
			seq - tab sequence (gives order information)
			editable (boolean) - is the tab editable
			movable (boolean) - is the tab movable
			icon - icon displayed in the tab
			status - is a new page or not
			loadstart (boolean) - is the tab loaded on application startup (application for personalized pages)
			pageId - if pageId 1, page linked to 'pages' in the database.
                    
                    See Also:
                    
                               <$p.plugin.useWidget>, <$p.app.standalone>,<$p.app.tabs.init>,<$p.app.pages.openCreatedTab>
                            
	*/ 
	object: function(id,label,type,fct,lock,seq,editable,movable,icon,loadstart,status,param,pageId,removable)
	{
        if (lock == indef) lock="";
		if (pageId == 1)
            this.pageid = id;
		this.id = id;
		this.label = label;
        this.removable = removable;
		this.type = type;
		this.fct = fct;
		this.param = param;
		//lock = the tab is lockable or not ?
		this.lock = lock;
		this.seq = seq;
		this.editable = (editable == indef ? true : editable);
		this.movable = movable;
		this.icon = icon;
		this.loadstart = (loadstart == indef ? 0 : loadstart);
		this.status = status;
		//added 1.4 to manage these information at tab level
		this.isLoaded = indef;
		this.maxUniq = 0;
		this.root = {};
		this.controls = "Y";
		this.moduleAlign = (this.type == 1) ? __moduleAlignDefault : true;
		this.style = 1;
		this.showType = 0;
		this.colnb = 3;
		this.newspapernb  =20;
		this.usereader = (__usereader ? 1 : 0);
		this.moveIsInit = false;
		this.nbUnread = 0;
		//locked = current lock status of a tab
		this.locked=false;
		this.temporary=false;
		this.shared=0;
		this.module=[];
        this.canvas=[];
        this.RssArticles=new Object();
        this.RssArticles.length=0;
		this.feeds=new Object();
		this.cols=[];
		this.open=$p.app.tabs.open;
		this.rename=$p.app.tabs.rename;
		this.remove=$p.app.tabs.remove;
	},
	/*
		Function: init
                                $p.app.tabs.init 
                        
                                Create the tabs objects                        
                                Each tab is get from xml request, via $p.ajax.getVal
		
                    Parameters:

			response - xml response containing tabs information
            
                    See Also:
                    
                              <$p.ajax.getVal>
	*/
	init:function(response)
	{
		$p.plugin.hook.launch('app.tabs.init.start');

		var i = 0,
            nbTemp = 0,
            result;

		//do not take temporary tabs in account
		while (nbTemp < tab.length && tab[nbTemp].temporary)
		{
			nbTemp++;
		}

		while (response.getElementsByTagName("tab")[i])
		{
			if (i+nbTemp >= tab.length 
                    || tab[i+nbTemp].isLoaded == indef)  {


				result=response.getElementsByTagName("tab")[i];

				//get default tabs style = first tab style
				if ($p.app.tabs.defTheme == indef)
					$p.app.tabs.defTheme = $p.ajax.getVal(result,"style","int",false,1);
				
				tab[i+nbTemp]=new $p.app.tabs.object(
										$p.ajax.getVal(result,"number","int",false,0),
										$p.ajax.getVal(result,"name","str",false,"..."),
										$p.ajax.getVal(result,"type","str",false,"P"),
										$p.ajax.getVal(result,"action","str",false,""),
										$p.ajax.getVal(result,"locked","int",false,0),
										$p.ajax.getVal(result,"seq","int",false,0),
										$p.ajax.getVal(result,"edit","int",false,0),
										$p.ajax.getVal(result,"move","int",false,0),
										$p.ajax.getVal(result,"icon","str",false,""),
										$p.ajax.getVal(result,"loadstart","int",false,0),
										$p.ajax.getVal(result,"status","int",false,0),
										$p.ajax.getVal(result,"param","str",false,""),
										1,
                                        $p.ajax.getVal(result,"removable","int",false,1)
										);
				//get the shared information
				tab[i+nbTemp].shared = $p.app.tabs.checkIfShared($p.ajax.getVal(result,"shared","str",false,''));
			}
			i++;
		}

		$p.plugin.hook.launch('app.tabs.init.end');
	},
	/*
		Function: open
                                $p.app.tabs.open 
                        
                                Open a tab
		
                    Parameters:

			v_id -  id of the tab
	*/
	open: function(v_id)
	{
        $p.app.widgets.rss.stopAllLoadings();

		if (v_id == indef) {
            v_id = $p.app.tabs.idToPos(this.id);
		}

		//v2.1 : pb with tab id=-1
		if (v_id == -1) v_id = 0;

		//add page ID in the URL -FRI: suppressed go to anchor (problems on tagcloud open tab)
		//if (tab[v_id] && !tab[v_id].temporary)  $p.url.goToAnchor('tab/'+tab[v_id].id);
		
		$p.app.tabs.detectModifiedTab(v_id);

		$p.app.pages.hide();

		$p.app.tabs.select(v_id);

		$p.app.tabs.sel = v_id;

		eval(tab[$p.app.tabs.sel].fct);

		$p.app.tabs.select(v_id);
    
		// Check if the aggregation has been initialized
		var tabId = tab[$p.app.tabs.sel].id;
		var index = tabAggregatedStatusTabId.indexOf(tabId);
		if (index == -1)
			init_aggregation();
		else
			refresh_aggregation_toggle();

		
		$p.ajax.call('../../app/tests/LoggerResponder.php?action=4&name=' + tab[$p.app.tabs.sel].label, {'type':'load'});
	},
	/*
		Function: select
                                $p.app.tabs.select 
                        
                                Select a tab
		
                    Parameters:

			v_tab -  id of the tab
	*/
	select:function(v_tab)
	{
		if (v_tab==indef)   {   v_tab=$p.app.tabs.sel; }

		$p.app.tabs.create(v_tab);
        if ($p.app.env=='admin' && $p.app.user.type=="A") {
            $p.admin.tabs.hideUnavailableTabs();
        }
	},
	/*
		Function: refresh
                                $p.app.tabs.refresh 
                        
                                Refresh the tabs selection
		
                     Parameters:

			v_tab -  id of the tab
	*/
	refresh:function(v_tab)
	{
		$p.app.tabs.create(v_tab);
	},
    /*
		Function: displayScrollTab
                                $p.app.tabs.displayScrollTab 
                        
                                display the left or right tab scroll
		
                    Parameters:
                    
                                position - 'right' or 'left'
        
                     Returns:
                     
                                HTML code
	*/
    displayScrollTab:function(position)
    {   
        var l_s='';
        if (position=='left') {
            l_s +='<td class="endtab" style="padding: 6px;">'
    		    +'<a href="#" id="leftTabNav" onmouseover="$p.app.tabs.nav.left();" onmouseout="$p.app.tabs.nav.stop();" onclick="$p.app.tabs.nav.jumpLeft();">'+$p.img('ico_previous.gif',7,9)+'</a>'
    		    +'</td>';
        }   
        else if (position=='right') {
            l_s +='<td class="endtab" style="padding: 6px">'
    		    +'<a href="#" id="rightTabNav" onmouseover="$p.app.tabs.nav.right();" onmouseout="$p.app.tabs.nav.stop();" onclick="$p.app.tabs.nav.jumpRight();">'+$p.img('ico_next.gif',7,9)+'</a>'
    		    +'</td>';
        }           
        return l_s; 
    },
    /*
		Function: newPageLink
                                $p.app.tabs.newPageLink 
                        
                                display the 'add a new page' link
		
                    Parameters:
                    
                                v_type - (0 =navigator 1 =other)
                                i - tab seq
        
                     Returns:
                     
                                HTML code
	*/
    newPageLink:function(v_type,i)
    {   
        var l_s='';
        if (v_type==indef)  return;
        if (v_type==0 && i==indef)  return;
        if (v_type==0) {
            l_s+='<td nowrap="nowrap" class="'+(i==0?'notab':'endtab')+'" style="width:100%">';
			if ($p.app.user.id>=0 
               && __maxPageNb>tab.length 
               && __addPagePermission) {
    				l_s+='<a href="#" id="newtablk" onclick="$p.app.pages.newEmpty();return false;">+ '+lg('lblNewpage')+'&nbsp;</a>';
				}
    		l_s+='</td>';
        } 
        else {
            l_s+="<li><div nowrap='nowrap' class='endtab'>";
			if ($p.app.user.id>=0 
                && __maxPageNb>tab.length) {
				l_s+="<a href='#' onclick='$p.app.pages.newPortal();return false;'>+ "+lg("lblNewpage")+"&nbsp;</a></div>";
			}
			l_s+="</li>";
        }           
        return l_s; 
    },
	/*
		Function: create
                                $p.app.tabs.create 
                        
                                Display the tabs
		
                     Parameters:

			v_id(int) - tab ID
			v_locked (boolean) - is the tab locked (not movable) ?
			v_stopMove - ???
			v_prof - ??
	*/
	create:function(v_id,v_locked,v_stopMove,v_prof)
	{
        $p.plugin.hook.launch('app.tabs.create.start');

        // Create the pages tab
		if (v_locked==indef) { v_locked=$p.app.tabs.locked; }
		var l_movable=!v_stopMove;
    
		//to update with 1.6 : add type lists, and simplify html code
        if (__tabType=="navigator")
		{
			l_s='<table id="alltabs" cellpadding="0" cellspacing="0">'
				+'<tr>'
				+$p.app.tabs.displayScrollTab('left')
				+'<td valign="bottom" width="100%">'
				+'<div id="navfixedbox">'
				+'<div id="navfloatbox">'
				+'<div id="navmovebox">'
				+'<table cellpadding="0" cellspacing="0" width="100%">'
                +'<tr>'
                +'<td style="font-size:1px;line-height:0px;">'
                +'<img id="navctlimg" src="../images/s.gif" width="100%" style="height:1px" />'
                +'</td>'
    			+'</tr>' 
                +'<tr>'
				+'<td>'
				+'<table cellpadding="0" cellspacing="0">'
                +'<tr>';
                
			var i=0,firstMovingTab=true;
			while (i<tab.length)
			{
                if (v_id==indef 
                    && v_prof==tab[i].id)    {
                       v_id=i;
                       $p.app.tabs.sel=i;
            	}
                else    {
                    $p.app.tabs.sel=v_id;
                }
                            
                if (firstMovingTab) {
					if (i==0)   {   
                        l_s+='<td>'
                        +'<table cellpadding="0" cellspacing="0" border="0">'
                        +'<tr'+(tab[i].movable=='1'?' id="tabsframe"':'')+'>';
					}
					else    
                    {
						if (tab[i].movable==1)  {
							l_s+='</tr>'
								+'</table>'
								+'</td>'
								+'<td>'
								+'<table cellpadding="0" cellspacing="0" border="0">'
								+'<tr id="tabsframe">';
						}
                    }
					if (tab[i].movable==1) {  firstMovingTab=false;  }
				}

                //create the tab
                l_s+='<td id="tab'+i+'" width="1" valign="bottom">';
                l_s+=$p.app.tabs.displayTabContent(i,v_id,v_locked,l_movable,v_prof);
                l_s+='</td>'; 
                i++;
    		}
            
            //create the 'new page' link
            l_s+=$p.app.tabs.newPageLink(0,i);     
            
			if (i>0)    {
				l_s +='</tr>'
					+'</table>'
					+'</td>';
			}
            
			l_s+='<td class="endtab">&nbsp;</td>'
				+'</tr>'
				+'</table>'
				+'</td>'
				+'</tr>'
				+'</table>'
				+'</div>'
                +'</div>'
				+'</div>'
				+'</td>'
                +$p.app.tabs.displayScrollTab('right')
                //add a bloc if scrollbars with FF
				+'<td class="endtab">&nbsp;&nbsp;&nbsp;</td>'
				+'</tr>'
				+'</table>';
		}
        
        //__tabType != navigator
		else
		{
			l_s='<ul class="tabnav" id="tabsframe">';
			var i=0,firstMovingTab=true;
			
            while (i<tab.length)
			{
				//if selected tab in not yet defined
				if (v_id==indef 
                    && v_prof==tab[i].id)    {
                       v_id=i;
                       $p.app.tabs.sel=i;
            	}
            	else   {    $p.app.tabs.sel=v_id;   }
                		
				l_s+="<li id='tab"+i+"'"+(i==v_id?" class='active'":"")+">";
                l_s+=$p.app.tabs.displayTabContent(i,v_id,v_locked,l_movable,v_prof);
				l_s+="</li>";
                
				i++;
			}
            
            //display the 'new page' link
            l_s+=$p.app.tabs.newPageLink(1,i);
			l_s+="</ul>";
		}
		$p.print("tabs",l_s);

		$p.app.tabs.isInit=false;

		if (l_movable) $p.app.tabs.initMove();

		if (__tabType=="navigator") $p.app.tabs.nav.init();

		$p.plugin.hook.launch('app.tabs.create.end');
		
	},
	/*
		Function: displayTabContent
                                $p.app.tabs.displayTabContent 
                        
                                Display active and inactive tabs
		
                     Parameters:

			i - tab seq
                                v_id(int) - tab ID
			v_locked (boolean) - is the tab locked (not movable) ?
			l_movable - ???
			v_prof - ??
	*/
    displayTabContent:function(i,v_id,v_locked,l_movable,v_prof)
    {
        var l_s="";
        
        if (i==v_id)    {  
           l_s+=$p.app.tabs.activeTab(i,v_locked,l_movable); 
           $p.navigator.changeTitle($p.app.tabs.currName);
        }
        else    {   
            l_s+=$p.app.tabs.inactiveTab(i,v_prof); 
        }  
        return l_s;
    },
	/*
		Function: activeTab
                                $p.app.tabs.activeTab 
                        
                                Return active tab HTML code
		
                     Parameters:

			v_id(int) - tab ID
			v_locked (boolean) - is the tab locked (not movable) ?
			v_movable - is the tab movable
            
                     Returns:
                     
                                HTML code of the tab
	*/
	activeTab:function(v_id,v_locked,v_movable)
	{        
        var l_s='';
        //prevent the user is the page was modified by the administrator
        //$p.app.tabs.detectModifiedTab(v_id);
		
        $p.app.tabs.currName=tab[v_id].label;
        //if the tab is movable, display the appropriate cursor
        var td_displayMoveCursor = '<td class="tabal" id="tab'+v_id+'_h"'
                                    +( v_movable 
                                       && tab[v_id].movable       ?     ' style="cursor:move"'
                                                                  :     '') 
                                    +'>';
		
        //display the number of unread article
        var span_displayUnreadArticles ='<span class="tabextra" id="tabextra'+v_id+'">'
                                         +((tab[v_id].nbUnread==0 || tab[v_id].temporary)  ?     ''
                                                                   :     '('+tab[v_id].nbUnread+')') 
                                         +'</span>'
        //display the tab icon
        var displayTabIcon = ((tab[v_id].isLoaded==false 
                             && tab[v_id].type==1)            ?      $p.img("ico_waiting.gif",16,16,"","imgmid")
                                                              :      ((tab[v_id].icon==""
                                                                     ||tab[v_id].icon==indef
                                                                     ||!__showtabicon)
                                                              ?      $p.img("s.gif",16,16,"","imgmid")
                                                              :      "<img src='"+tab[v_id].icon+"' width='16' height='16' align='absmiddle'>"))
  
		l_s+='<table class="taba" cellpadding="0" cellspacing="0">'
			+'<tr>'
            //start 'td'
            +td_displayMoveCursor
            //edition du nom de la page
			+'<a href="#" onmousedown="'
            +( $p.app.user.id<0
               ||v_locked
               ||!tab[v_id].editable		?     ''
                                            :     '$p.app.tabs.edit('+v_id+')')
            +'" style="cursor:text">'
            +displayTabIcon
            +'&nbsp;'+tab[v_id].label+'&nbsp;'        
            +span_displayUnreadArticles
            +'</a>'
            //end of 'td' of the variable 'td_displayMoveCursor'
            +'</td>'
            +'<td class="tabar">';

		if ($p.app.user.id>=0)  {
            l_s += '<table cellpadding="0" cellspacing="0" border="0">'
                + '<tr><td style="padding-right: 14px;height: 1px;"></td></tr>' //used to fix the minimal width
                + $p.app.tabs.buildLockOption(v_id)
			    + $p.app.tabs.buildShareOption(v_id)
                + $p.app.tabs.buildEditableOption(v_id,v_locked)
			    + $p.app.tabs.buildRemoveOption(v_id)
                + '</table>';
		}
		else  {  l_s+="&nbsp;"; }

        l_s+='</td>'
           +'<td class="intertaba">'+$p.img('s.gif',1,1)+'</td>'
           +'</tr>'
           +'</table>';

        return l_s;
	},
	/*
		Function: buildLockOption
                                $p.app.tabs.buildLockOption 
                        
                                Display the lock icon if the page is locked
		
                     Parameters:

			v_id(int) - tab ID
            
                     Returns:
                     
                                HTML code
	*/
    buildLockOption:function(v_id)
    {
		//if the tab is shared, it can not be locked
		if (tab[v_id].shared!=0) return '';

        var l_s='';
        //tab lock
        if (tab[v_id].lock!=0) {
             l_s+='<tr>'
                +'<td style="font-size:2pt;">'
                +'<a href="#" onclick="$p.app.pages.lock();return false;">'
                +$p.img('lock.gif',7,9,lg('lblSecuActive'),'imgmid')
                +'</a>'
                +'</td>'
                +'</tr>';
        }
        return l_s;
    },
	buildShareOption: function(v_id)
	{
		var l_s = '';
		if (tab[v_id].shared != 0)
		{
			l_s += '<tr>'
                + '<td style="font-size:2pt;">'
				+ '<a href="#" onclick="$p.app.pages.stopSharing('+tab[v_id].id+');return false;" title="'+lg('clickToStopSharing')+'">'
                + $p.img('ico_share_s.gif',13,10,lg('clickToStopSharing'),'imgmid')
				+ '</a>'
                + '</td>'
                + '</tr>';
		}
		return l_s;
	},
	/*
		Function: buildEditableOption
                                $p.app.tabs.buildEditableOption 
                        
                                Display the option (black arrow) to display the page configuration.
		
                     Parameters:

			v_id(int) - tab ID
			v_locked - locked ?
            
                     Returns:
                     
                                HTML code
	*/
	/*FRI: TABCLOSE: */
    buildEditableOption:function(v_id,v_locked)  
    {
        var l_s='';

        if (tab[v_id].editable 
            && __showTabOptions)    {
                l_s+='<tr>'
                    +'<td style="font-size:2pt;">'
                    +'<a href="#" title='+lg("lblTabDelete")+' onclick="'
                    +(v_locked             ?               ''
                                           :               'return $p.app.pages.suppress()')  /* FRI: TAB sofort entf! $p.app.banner.option.show()   --->  $p.app.pages.suppress() */
                    +'">'
                    +$p.img('ico_close.gif',8,8,'delete','imgmid')
                    +'</a>'
                    +'</td>'
                    +'</tr>';
        }
        /*else    {
            l_s+='<tr>'
                +'<td style="font-size:2pt;">'
                +$p.img('s.gif',11,8,'imgmid')
                +'</td>'
                +'</tr>';
        }*/
        return l_s;
    },
	/*
		Function: buildRemoveOption
                                $p.app.tabs.buildRemoveOption 
                        
                                Display the removal icon on the tab (for temporary tabs)
		
                     Parameters:

			v_id(int) - tab ID
            
                     Returns:
                     
                                HTML code
	*/
	buildRemoveOption:function(v_id)
	{
		var l_s='';
		if (tab[v_id].temporary)
		{
			l_s+='<tr>'
				+'<td style="font-size:2pt;">'
				+'<a href="#" onclick="tab['+v_id+'].remove(false);return false;">'
				+$p.img('ico_close.gif',12,11,lg('lblSuppresspage'),'imgmid')
				+'</a>'
				+'</td>'
				+'</tr>';
		}

		return l_s;
	},
	/*
		Function: inactiveTab
                                $p.app.tabs.inactiveTab 
                        
                                Return inactive tab HTML code
		
                     Parameters:

			v_id(int) - tab ID
			v_prof - ???
            
                     Returns:
                     
                                HTML code of the tab
	*/
	inactiveTab: function(v_id,v_prof)
	{    
        var l_s = '';
        //if the tab is movable, display the appropriate cursor
        var td_displayMoveCursor = '<td class="tabl"'
                                 + (tab[v_id].movable    ?      ' id="tab'+v_id+'_h" style="cursor:move"'
                                                         :      '')
                                 + '>';
                                 
        var href = (tab[v_id].type == 4 
                   && $p.app.env == "admin")
                                            ?   '"' + tab[v_id].fct + '"' 
                                            :   "'#' onclick=\"$p.app.tabs.open("+v_id+");return false;\"";

        //display the tab icon
        var displayTabIcon=((tab[v_id].isLoaded == false 
                            && tab[v_id].type == 1)
                                                        ?            $p.img("ico_waiting.gif",16,16,"","imgmid")
                                                        :            ((tab[v_id].icon==""
                                                                     ||tab[v_id].icon==indef
                                                                     ||!__showtabicon)
                                                        ?            $p.img("s.gif",16,16,"","imgmid")
                                                        :            "<img src='"+tab[v_id].icon+"' width='16' height='16' align='absmiddle'>"))
        //display the number of unread article                  
        var span_displayUnreadArticles = '<span class="tabextra" id="tabextra'+v_id+'">'
                                       + ((tab[v_id].nbUnread==0 || tab[v_id].temporary) ?               ''
                                                                    :               '('+tab[v_id].nbUnread+')')
                                       + '</span>';
        
		l_s += '<table class="tab" cellpadding="0" cellspacing="0"'+(tab[v_id].temporary ?' style="opacity: 0.4;filter: alpha(opacity=40);"' : '')+'><tbody>'
			+ '<tr>'
            //Open the TD
            + td_displayMoveCursor;

        l_s += (v_prof == 0) ?    '<a href="#" onclick="openPage('+tab[v_id].id+');return false;" id="tab'+v_id+'_u">'
                             :    "<a href="+ href +" id='tab"+v_id+"_u'>";
		
		l_s += displayTabIcon
			+ '	 '+tab[v_id].label
            + span_displayUnreadArticles
            + '</a>'
            //Close the TD
			+ '</td>'
			+ '<td class="tabr">'
			+ '<table cellpadding="0" cellspacing="0" border="0"><tbody>'
            + '<tr><td style="padding-right: 14px;height: 1px;"></td></tr>'; //used to fix the minimal width
        
   		if (tab[v_id].lock != 0)  {
            l_s += "<tr><td style='font-size:2pt;'>"
                + $p.img("lock.gif",7,9,lg("lblSecuActive"),"imgmid")
                + "</td></tr>";
        }
       
        l_s += $p.app.tabs.buildAlertOrDeleteIcon(v_id)
			+ $p.app.tabs.buildRemoveOption(v_id)
            + '</tbody></table>'
            + '</td>'
            + '<td class="intertab">'
            + $p.img('s.gif',1,1)
            + '</td>'
            + '</tr>'
            + '</tbody></table>';

		return l_s;
	},
	/*
		Function: buildAlertOrDeleteIcon
                                $p.app.tabs.buildAlertOrDeleteIcon 
                        
                                Display delete icon 
                        
                     Parameters:
                     
                                v_id - current Tab ID
                              
                     Returns:
                     
                                HTML code
           */
    buildAlertOrDeleteIcon:function(v_id)
    {
        var l_s="";
        if (
                (
                    tab[v_id].type==4 || tab[v_id].type==2
                )
             && ($p.app.env=="portal_page_conn" 
             || $p.app.env=="portal_page_anon")) {
                l_s+="<tr><td style='font-size:2pt;'><a href='#' onclick='return $p.app.pages.suppress("+v_id+");'>"
                   +img("ico_close.gif",12,11,lg('lblSuppresspage'),"imgmid")
                   +"</a></td></tr>";
        }
  		/*else {
			l_s+="<tr><td style='font-size:2pt;'>"
               +img("s.gif",11,8,"imgmid")
               +"</td></tr>";
        }*/
        
        return l_s;
    },
	/*
		Function: initMove
                                $p.app.tabs.initMove 
                        
                                Init tabs moving processes
           */
	initMove:function()
	{         
		// Initialize column & modules behaviors, do not initialize the tabs if already done
		if ($p.app.tabs.isInit) return;
		//if no tab existing for user
		if (tab.length==0) return;
		$p.app.tabs.isInit=true;
		var l_tabs=$("tabsframe");
		if (l_tabs!=null)   {
			for (var i=0;i<l_tabs.childNodes.length-1;i++)
			{
				var l_mod=l_tabs.childNodes[i];
				$p.app.widgets.move.set(l_mod,"tab",l_tabs,"horizontal",false);
			}
		}
	},
	/*
		Function: save
                                $p.app.tabs.save 
                        
                                Save tabs changes

                    Parameters:

			 v_movedId - tab ID before moving
           */
	save: function(v_movedId)
	{
		$p.plugin.hook.launch('app.tabs.save.start');

		// Save the tabs changes
		var l_tabs = $("tabsframe");
		var l_prevPlace = 0;
		var l_selectedTabId = tab[$p.app.tabs.sel].id;

		//for all tabs
        var l_currentPlace = 1;
		for (var i = 0;i < l_tabs.childNodes.length-1;i ++)
		{
            if ((l_tabs.childNodes[i].id).indexOf("tab") != -1)
            {
                //get the tab sequence
                l_id = (l_tabs.childNodes[i].id).replace("tab","");
                if (!tab[l_id].temporary)
                {
                    //if object is the tab moved, and if the objet is not placed on the same place
    				if (l_id == v_movedId 
                        && (l_prevPlace+1) != tab[l_id].seq)
                    {
                        $p.ajax.call(posh["scr_movetab"],
                            {
                                'type':'execute',
                                'variables':"id="+tab[l_id].id+"&old="+tab[l_id].seq+"&new="+(l_currentPlace),
                                'alarm':false
                            }
                        );
                    }
    				l_prevPlace = tab[l_id].seq;
                    
                    tab[l_id].seq = l_currentPlace;
                    l_currentPlace ++;
    				//if (l_id==$p.app.tabs.sel) l_newSeq=tab[l_id].seq;
                }
           }     
		}
		//sort tabs based on the new order
		tab.sort($p.app.tabs.sort);
		// v1.4 : if on HTML predefined page, function was not working
		(l_selectedTabId == 0)      ?       $p.app.tabs.create($p.app.tabs.sel)
                                    :       $p.app.tabs.create(indef,false,false,l_selectedTabId);
		

		$p.plugin.hook.launch('app.tabs.save.end');
	},
	/*
		Function: sort
                                $p.app.tabs.sort 
                        
                                Tabs sorting rule

                    Parameters:

			 a - object
                                 b - object
                     
                     Returns:
                                 
                                 -1,1,0
           */
	sort:function(a,b)
	{
		if (a.seq<b.seq) return -1;
		if (a.seq>b.seq) return 1;
		return 0; 
	},
	/*
		Function: edit
                                $p.app.tabs.edit 
                        
                                Edit a tab name

                    Parameters:

			 v_id - tab ID
           */
	edit:function(v_id)
	{
		if (tab[$p.app.tabs.sel].showType==1)   {
			$p.app.widgets.rss.reader.close();
			return;
		}
		if (!__tabsCanBeRenamed)
		{
			return;
		}
		// Edit tab name
		$p.app.tabs.create($p.app.tabs.sel,false,true);
		var l_tab=$("tab"+v_id+"_h");

		l_tab.set('html',"<form name='tabeditform"+v_id+"' id='tabeditform"+v_id+"' onsubmit='return $p.app.tabs.submitNameChange(this.tabinput,"+v_id+");'><input class='thinbox' name='tabinput' id='tabinput' type='text' size='"+$p.max(10,tab[v_id].label.length)+"' maxlength='30' value='"+tab[v_id].label+"' onblur=\"\" /> <input type='image' class='imgmid' src='../images/ico_accept.gif' border='0' title='"+lg('lblOk')+"' /><a href='#' onclick='$p.app.tabs.create($p.app.tabs.sel);'>"+$p.img("ico_stop.gif",16,16,lg("lblCancel"),"imgmid")+"</a></form>");
		var l_input=$("tabinput");
		l_input.select();
	},
	/*
		Function: rename
                                $p.app.tabs.rename 
                        
                                Save tab name changes

                    Parameters:

                                 v_name - new tab name
			 v_id - tab ID
           */
	rename:function(v_name,v_id)
	{
		(v_id==indef)?v_id=$p.app.tabs.idToPos(this.id):'';
		if (v_name!="" 
            && v_name!=tab[v_id].label) {
    			if ($p.app.tabs.sel==v_id) {
                    $p.app.tabs.currName=v_name;
                }
    			tab[v_id].label=v_name;
    			$p.ajax.call(posh["scr_config_options"],
    				{
    					'type':'execute',
    					'variables':"prof="+tab[v_id].id+"&portstyle="+tab[v_id].style+"&portname="+$p.string.esc(tab[v_id].label)+"&col="+tab[v_id].colnb+"&advise="+$p.app.banner.info.requested,
    					'alarm':false
    				}
    			);
    			
    			$p.ajax.call('../../app/tests/LoggerResponder.php?action=7&id=' + tab[v_id].id + '&name=' + tab[v_id].label, {'type':'load'});
		}
		$p.app.tabs.create($p.app.tabs.sel);
		$p.app.widgets.rss.pageUnread();
	},
	/*
		Function: submitNameChange
                                $p.app.tabs.submitNameChange 
                        
                                Format the new tab name

                    Parameters:

                                 v_input - text input object when tab name is typed
			 v_id - tab ID
             
                     Returns:
                     
                                 false
           */
	submitNameChange:function(v_input,v_id)
	{
		var l_name=$p.string.removeTags($p.string.removeCot(v_input.value));
		$p.app.tabs.rename(l_name,v_id);
		return false;
	},
	/*
		Function: initHeadMenu
                                $p.app.tabs.initHeadMenu 
                        
                                Generate tab menu

                    Parameters:

			v_obj - div object where menu is generated
			v_id - tab ID
           */
	initHeadMenu:function(v_obj,v_id)
	{
        var l_s="";
		if (v_obj==indef) {
			if ($("headmenu")!=null) {
                $p.app.tabs.showHeadMenu(false);
            }
        }
		else    {
			$p.app.tabs.currHeadLink=v_obj;			
			var l_top=($p.app.tabs.currHeadLink.getTop())+15;
			var l_left=($p.app.tabs.currHeadLink.getLeft())-3;
			var l_width=$p.app.tabs.currHeadLink.offsetWidth+6;

			$p.app.tabs.showHeadMenu(false);
			var l_obj=$("headmenu");
			l_obj.setStyle("top",l_top+"px");
			l_obj.setStyle("left",l_left+"px");

			for (var i=0;i<__headmenu[v_id]["options"].length;i++)
			{
				l_s+='<a href="#" onclick="$p.app.tabs.showHeadMenu(false);'
                   +__headmenu[v_id]["options"][i]["fct"]
                   +'" onmouseover="$p.app.tabs.showHeadMenu(true)" style="white-space: nowrap;height: 17px;">'
                   +(__headmenu[v_id]["options"][i]["images"]==""           
                                                                ?               ""
                                                                :               $p.img(__headmenu[v_id]["options"][i]["images"],false,false,"","imgmid")
                                                                +" ")
                   +lg(__headmenu[v_id]["options"][i]["label"])
                   +'</a>';
			}
			l_obj.set('html',l_s);
			l_obj.onmouseout=function(){$p.app.tabs.showHeadMenu(false);}
		}
	},
	/*
		Function: showHeadMenu
                                $p.app.tabs.showHeadMenu 
                        
                                Display tab menu

                    Parameters:

			v_show (boolean) - display the menu or not 
           */
	showHeadMenu:function(v_show)
	{	
		(v_show)    ?     $p.show("headmenu","block")
                    :     $p.show("headmenu","none"); 
	},
	/*
		Function: moduleOver
                                $p.app.tabs.moduleOver 
                        
                                Define tab behaviour when a module is over

                    Parameters:

			v_id - tab ID
           */
	moduleOver:function(v_id)
	{
		if (tab[v_id].type==1 
            && __moveWidgetsInTabs) {
    			var l_obj=$("tab"+v_id);
    			l_obj.setStyle("border","1px solid #ff0000");
    			$p.app.tabs.overtabid=v_id;
		}
	},
	/*
	         Function: moduleOut
                                $p.app.tabs.moduleOut 
                        
                                Define tab behaviour when a module is no more over

                    Parameters:

			v_id - tab ID
           */
	moduleOut:function(v_id)
	{
		var l_obj=$("tab"+v_id);
		if (l_obj) {  l_obj.setStyle("border","0px");  }
		$p.app.tabs.overtabid=-1;
	},
	/*
	         Function: moduleOutAll
                                $p.app.tabs.moduleOutAll 
                        
                                Reset all tabs display
           */
	moduleOutAll:function()
	{
		for (var i=0;i<tab.length;i++)
        {
			$p.app.tabs.moduleOut(i);
		}
	},
	/*
		Function: getId
                                $p.app.tabs.getId
		
                                Get tab sequence based on its ID
        
                    Parameters:

			 v_id - tab ID
             
                    Returns:
                    
                                the tab sequence
	*/
	getId:function(v_id)
	{
		for (var i = 0;i < tab.length;i++)
		{
            
			if (tab[i].id == v_id) { 
                return i; 
            }
		}
		return -1;
	},
	/*
		Function: selectTab
                                $p.app.tabs.selectTab
		
                                Select a tab based on its ID (used in admininistration)
        
                    Parameters:

			 name - tab ID
			 tabs(array) - the array of tabs
             
                    See Also:
                    
                                tabs.inc.php
	*/
	selectTab:function(v_id,v_tabs)
	{
		this.sel = 0;
		if (v_tabs==indef) v_tabs = tabs;
        for (var i = 0;i < v_tabs.length;i++)
		{
			if (v_tabs[i].id == v_id)   {
				this.sel = i;
                return;
			}
		}
	},
	/*
		Function: selectTabByTitle
                                $p.app.tabs.selectTabByTitle
		
                                Select a tab based on its name
        
                    Parameters:

			 name - tab Name
			 tabs(array) - the array of tabs
             
                    See Also:
                    
                                tabs.inc.php
	*/
	selectTabByTitle:function(name,v_tabs)
	{
		this.sel = 0;
		if (v_tabs==indef) v_tabs=tab;
		for (var i=0;i<v_tabs.length;i++)
		{
			if (v_tabs[i].label==name)
			{
				this.sel = i;
				return i;
			}
		}
        return;
	},
	selectTempTabByTitle:function(name,v_tabs)
	{
		this.sel = 0;
		if (v_tabs == indef) v_tabs = tab;
		for (var i = 0;i < v_tabs.length;i++)
		{
			if (v_tabs[i].label == name && v_tabs[i].temporary)
			{
				this.sel = i;
				return;
			}
		}
	},
	//open a link in a temporary tab
	openTempLink:function(v_title,v_url)
	{
		$p.app.tabs.openTempTab(2,"$p.app.pages.frame('"+v_url+"',%tabid%)",v_title);
	},
	/*
		Function: openTempTab
                                $p.app.tabs.openTempTab
		
                                Open a temporary tab
        
                    Parameters:

			 v_type -type of the tab (customizable portal, frame, javascripf function)
			 v_action -action triggered when used click on the tab
			 v_title -title of the tab
			 v_icon -url of the icon of the tab
             
	*/
	openTempTab:function(v_type,v_action,v_title,v_icon)
	{
		$p.app.pages.hide();
		//select tab number
		$p.app.tabs.selectTempTabByTitle(v_title);
		
		//If a temporary tab with same name already exists :
		if ($p.app.tabs.sel != 0 || (tab[$p.app.tabs.sel].label == v_title && tab[$p.app.tabs.sel].temporary))
		{
			l_idTab = tab[$p.app.tabs.sel].id;
			// update tab URL
			tab[$p.app.tabs.sel].fct = v_action.replace(/%tabid%/g,l_idTab);
			tab[$p.app.tabs.sel].type = v_type;
			tab[$p.app.tabs.sel].isLoaded = false;
		}
		else
		{
			//generate ID of new tab
			reset_rand();
			var l_idTab = rand+10000000;
			//create a new tab
			tab.unshift(new $p.app.tabs.object(
                                                l_idTab,        //id of tab for a profile
                                                v_title,        //title
                                                v_type,         //type 1 to 5
                                                v_action.replace(/%tabid%/g,l_idTab), //linked function
                                                0,              //lock
                                                0,              //seq   position in sequence of the portal
                                                0,              //editable
                                                0,              //movable
                                                v_icon,             //icon
                                                0,               //loadstart
                                                indef,          //status
                                                indef,          //param
                                                indef,          //pageId
                                                1,          //removable
                                                true        //temporary
                                                )
                        );
		}

		//open new tab
		tab[$p.app.tabs.sel].temporary = true;
		tab[$p.app.tabs.sel].icon = v_icon;

		eval(v_action.replace(/%tabid%/g,l_idTab));

		$p.app.tabs.select($p.app.tabs.sel);
	},
	/*
		Function: idToPos
                                $p.app.tabs.idToPos
		
                                Get tab sequence ID based on its DB ID
        
                    Parameters:

			 v_id - tab DB ID
             
                    Returns:
                    
                                tab sequence ID
	*/
	idToPos:function(v_id)
	{
		if (v_id==-1) {  return -1; }
		if (tab.length==1000) {   return 999;  }
		for (var i=0;i<tab.length;i++)
		{
			if (tab[i].id==v_id) {   return i;  }
		}
		return false;
	},
	/*
		Function: remove
                                $p.app.tabs.remove
		
                                Remove a tab
	*/
	remove:function(v_needConfirmation)
	{
		$p.app.pages.suppress($p.app.tabs.idToPos(this.id),indef,v_needConfirmation);
	},
	/*
		Function: maxId
                                $p.app.tabs.maxId
		
                                Get the maximum ID of all the tabs in tab[]
                                
                      Returns:
                      
                                Maximum ID
	*/
	maxId:function()
	{
		var maximumId=0;
		for (var i=0;i<tab.length;i++)
		{
			if (tab[i].id>maximumId && !tab[i].temporary) {
				maximumId=tab[i].id;
            }
		}
		return maximumId;
	},
	/*
		Function: detectModifiedTab
                                $p.app.tabs.detectModifiedTab
		
                                Change a tab status and display a message to warn the user.
                                
                      Parameters:
                      
                                v_id - id of the tab
	*/
	detectModifiedTab: function(v_id)
	{
        if ($p.app.user.type=="A") { return false; }
				
				if (!(tab[v_id])) tab[v_id]=1; //FRI Default
				
        switch (tab[v_id].status)
        {
            case 1:
                $p.app.alert.show(lg("tabAdded",tab[v_id].label));
                break;
            case 2:
                $p.app.alert.show(lg("tabModified",tab[v_id].label));
                break;
            default:
                return false;
                break;
        }
        
        //update the status of the tab in the database
        $p.ajax.call(posh["scr_changetabstatus"],
            {
                'type':'execute',
                'variables':"new=0&tabId="+tab[v_id].id
            }
        );
        //update status in the pages tab
        tab[v_id].status = 0;
	},
	/*
		Function: ckeckIfShared
                                $p.app.tabs.checkIfShared
		
                                Format the shared information to be usable in the javascript
                                
                      Parameters:
                      
                                v_shared : shared string
	*/
	checkIfShared:function(v_shared)
	{
		if (v_shared=="") return 0;
		if (v_shared.length>1) return 4;
		return v_shared;
	}
}


//************************************* TABS NAVIGATION FUNCTIONS ***************************************************************************************************************
/*
    Class: $p.app.tabs.nav
         Tabs navigation functions
*/
$p.app.tabs.nav={
	increment:2,
	timer:0,
	posx:0,
	movedObj:{},
	oversize:0,
	/*
		Function: init
                                $p.app.tabs.nav.init
                                
                                Init tabs navigation
	*/
	init: function()
	{
		this.timer = 0;
		
		this.movedObj = $('navmovebox');
		//reset position to previous one (in case of re-initialization)
		this.movedObj.setStyle("left",this.posx+"px");
		//content size is gotten with image size
		var navWidth = $p.app.tabs.nav.getNavigatorShownSize();
		var l_cntSize = $p.app.tabs.nav.getNavigatorTotalSize();

		this.oversize = l_cntSize - navWidth;
		//hide navigators button if useless
		if (this.posx > -1) ($("leftTabNav")).setStyle("visibility","hidden");
		if (this.oversize+this.posx <= 0) ($("rightTabNav")).setStyle("visibility","hidden");
	},
	getNavigatorShownSize: function()
	{
		var l_obj = $('navfloatbox');
		return (l_obj.offsetWidth     ?              l_obj.offsetWidth
											:       (l_obj.style.clip.width
												?       l_obj.style.clip.width
												:       0));
	},
	getNavigatorTotalSize:function()
	{
		return ($("navctlimg")).width;
	},
	/*
		Function:  left
                                $p.app.tabs.nav.left 
                                
                                Move tabs left
	*/
	left:function()
	{
		// if some tabs are hidden on the left
		if (this.posx < 0)
		{
			this.posx += this.increment;
			this.movedObj.setStyle("left",this.posx+"px");
			this.timer = setTimeout("$p.app.tabs.nav.left()",10);	
			($("rightTabNav")).setStyle("visibility","");
		}
		else
		{
			($("leftTabNav")).setStyle("visibility","hidden");
		}
	},
	/*
		Function:  jumpLeft
                                $p.app.tabs.nav.jumpLeft 
                                
                                Move tabs left (big move)
	*/
	jumpLeft:function()
	{
		//check if navigator is already on the right
		var centerPartSize = this.getNavigatorShownSize();
		if (this.posx < 0)
		{
			//check if the hidden left part is larger than the center part
			if (this.posx + centerPartSize > 0)
			{
				this.posx = 0;
				($("leftTabNav")).setStyle("visibility","hidden");
			}
			else
			{
				this.posx += centerPartSize;
			}
			
			this.movedObj.setStyle("left",this.posx+"px");
			($("rightTabNav")).setStyle("visibility","");
			$p.app.tabs.nav.stop();
		}
		else
		{
			($("leftTabNav")).setStyle("visibility","hidden");
		}
	},
	/*
		Function: right
                                $p.app.tabs.nav.right
                                
                                Move tabs right
	*/
	right: function()
	{
		if (this.posx + this.oversize > 0)
		{
			this.posx -= this.increment;
			this.movedObj.setStyle("left",this.posx+"px");
			this.timer = setTimeout("$p.app.tabs.nav.right()",10);	
			($("leftTabNav")).setStyle("visibility","");
		}
		else
		{
			($("rightTabNav")).setStyle("visibility","hidden");
		}
	},
	/*
		Function:  jumpRight
                                $p.app.tabs.nav.jumpRight 
                                
                                Move tabs right (big move)
	*/
	jumpRight: function()
	{
		//check if navigator is already on the left
		var rightPartSize = this.posx + this.oversize;
		var centerPartSize = this.getNavigatorShownSize();
		if (this.posx+this.oversize > 0)
		{
			//check if the hidden part on the right is bigger than the navigator size
			if (rightPartSize > centerPartSize)
			{
				this.posx -= centerPartSize;
			}
			else
			{
				this.posx -= rightPartSize;
				($("rightTabNav")).setStyle("visibility","hidden");
			}
			this.movedObj.setStyle("left",this.posx+"px");
			($("leftTabNav")).setStyle("visibility","");
			$p.app.tabs.nav.stop();
		}
		else
		{
			($("rightTabNav")).setStyle("visibility","hidden");
		}
	},
	/*
		Function: 
                                $p.app.tabs.nav.stop
                                
                                Stop moving tabs
	*/
	stop: function()
	{
		clearTimer(this.timer);
	}
}



//************************************* PERSONALIZED PAGES FUNCTIONS ***************************************************************************************************************
/*
    Class: $p.app.pages
         Personalized pages functions
*/
$p.app.pages={
	def:"N",
    tabId:0,
	/*
		Function: initMenu
                                $p.app.pages.initMenu 
                                
                                Init the pages menu
	*/
	initMenu:function()
	{
		if (false) /*FRI*/
		$p.app.menu.options.push({
                                "id":"poptions",
                                "label":lg("options"),
                                "desc":lg("portalIconDesc"),
                                "icon":"ico_custompage.gif",
                                "seq":25,
								"action":"$p.app.menu.config.build()",
                                "type":"",
                                "pages":[]
                                }
        );
	},
	/*
		Function: change
                                $p.app.pages.change 
                                
                                Open a page
		
                    Parameters:

			v_prof - tab ID
			v_action - action to launch on page opening
	*/
	change: function(v_prof,v_action)
	{
		$p.plugin.hook.launch('app.pages.change.start');

        //hide the current page 
		$p.app.pages.hide();
		$p.app.pages.closeAllDiv();
		$p.show("modules","block");

		//select the new tab
		$p.app.tabs.selId = v_prof;
		if (tab.length == 0) return;

		if (v_prof != indef)
        {
            $p.app.tabs.sel = $p.app.tabs.getId(v_prof);
            if ($p.app.tabs.sel == -1) $p.app.tabs.sel = 0;
        }
		//if ($p.app.tabs.sel==indef || $p.app.tabs.sel==-1 || $p.app.tabs.sel>tab.length) $p.app.tabs.sel=$p.app.tabs.getId(v_prof);
        //double check, in case the profile save in cookie is linked to a non existent tab (switch from an account to another)
		v_prof = tab[$p.app.tabs.sel].id; 
		//set the current page as default

		$p.app.setState("$p.app.pages.change("+v_prof+")");

		//$p.app.pages.clean($("modules")); //suppressed with 1.4 because no tabs erase if other items open => hide
		//load page only if not already loaded
		if (tab[$p.app.tabs.sel].isLoaded != indef)   {
			if (tab[$p.app.tabs.sel].style != $p.app.style) $p.styles.setActiveStyleSheet(tab[$p.app.tabs.sel].style);
			//if ($p.app.menu.config.oldStyle!=tab[$p.app.tabs.sel].style) setActiveStyleSheet(tab[$p.app.tabs.sel].style);
			$p.show("modules"+tab[$p.app.tabs.sel].id,"block");
            $p.app.pages.refreshWidgetsNotLoaded();
		}
		else    {
            $p.app.pages.open(v_action);
        }		
		if ($p.app.user.id>0) {
            $p.app.pages.setCurrent(v_prof);
        }

		//activate menus
		$p.app.menu.widget.locked = false;
		$p.app.newEnv(($p.app.user.id==0) ? 'portal_page_anon' : 'portal_page_conn');

		//define widget place
		$p.app.widgets.place();
		$p.app.pages.computeFooterPosition();

        //refresh menu based on new page
        $p.app.menu.refreshConditionalMenus();

		$p.plugin.hook.launch('app.pages.change.end');
	},
	/*
		Function: redirect
                                $p.app.pages.redirect 
                                
                                Open a redirection page
		
                    Parameters:

			v_url - url of the page
			v_id - tab ID
	*/
	redirect:function(v_url,v_id)
    {
        $p.show("footer","none");
		//$p.app.banner.option.hide();
		$p.show("box","none");
		$p.app.pages.closeAllDiv();
		$p.show("modules","block");
		$p.app.popup.hide();
		$p.app.newEnv('portal_frame');
        
		var l_tab=$p.app.tabs.idToPos(v_id);
        window.open(v_url);

		//lock menus
		$p.app.menu.widget.locked=true;
		//$p.app.tabs.selId=tab[$p.app.tabs.sel].id;
		$p.app.tabs.selId=v_id;
		$p.app.pages.computeFooterPosition();
		//apply style
		if (l_tab==$p.app.tabs.sel 
            && ($p.app.style==0 
            || $p.app.style!=tab[l_tab].style)) {
                $p.styles.setActiveStyleSheet(tab[l_tab].style);
        }

        //refresh menu based on new page
        $p.app.menu.refreshConditionalMenus();
    },    
		/*
		Function: openCreatedTab
                                $p.app.pages.openCreatedTab 
                       
                                Open a new tab
		
                    Parameters:

			v_prof - 0   if it's a new empty page, otherwise contains the url of the page
            
                    Returns:
                    
                                false
	*/
	openCreatedTab: function(v_prof)
	{
		$p.plugin.hook.launch('app.pages.opencreatedtab.start');

		//in anonymous portail
		if ($p.app.user.id == 0)
		{
			var lastTabNumber = $p.app.tabs.maxId()+1,
                lastTabSeq = tab[tab.length-1].seq+1;
			$p.app.pages.setCurrent(0);
			$p.app.pages.hide();
            
			//if the user created a new empty page
			if (v_prof == 0)
            {	
				tab.push(new $p.app.tabs.object(lastTabNumber,lg("lblNewpage"),1,"$p.app.pages.change("+lastTabNumber+")",0,lastTabSeq,1,1,"",0,0,"",0,1));
				$p.app.tabs.sel = tab.length - 1;
				tab[$p.app.tabs.sel].isLoaded = true;
				$p.app.pages.init();
				v_prof = tab.length;
			}
			//if the user add a new HTML page v_prof is the url to open in new page
			else
            {
				tab.push(new $p.app.tabs.object(lastTabNumber,lg("lblNewpage"),2,"$p.app.pages.frame('"+v_prof+"',"+lastTabNumber+")",0,lastTabSeq,1,1,"",0,0,v_prof,0,1));
				$p.app.tabs.sel = tab.length - 1;	
				$p.app.pages.init();
				$p.app.pages.frame(v_prof,lastTabNumber);
				v_prof = tab.length;
			}
		}
		//in connected mode
		else
        {
			if (v_prof != $p.app.tabs.selId)  {
				$p.app.pages.setCurrent(v_prof);
				$p.app.pages.hide();
			}
			$p.app.tabs.sel = 999;
			
			$p.ajax.call('../../app/tests/LoggerResponder.php?action=6&id=' + v_prof + '&name=' + lg('lblNewpage'), {'type':'load'});
			
			$p.app.loadTabs(v_prof,indef,"edittab"); // FRI only when v_proof undef	
		}

		$p.plugin.hook.launch('app.pages.opencreatedtab.end');
	},
	/*
		Function:  open
                                $p.app.pages.open 
                                
                                Load page information
                                
                     Parameters: 
                     
                                v_action - ???
	*/
	open: function(v_action)
	{
		tab[$p.app.tabs.sel].isLoaded = false;
        
		//	$p.app.startLoading(); suppressed on 1.4.2, no need when switch from a page to another
		if ($("modules"+tab[$p.app.tabs.sel].id) != null)
		{
			$p.app.pages.clean($("modules"+tab[$p.app.tabs.sel].id));
			$p.show("modules"+tab[$p.app.tabs.sel].id,"block");
		}
		if ($p.app.user.id == 0 
            || tab[$p.app.tabs.sel].id == 0 
            || tab[$p.app.tabs.sel].id >= 1000000000) {
                $p.app.pages.load("selections/page"+$p.app.tabs.selId+".xml?nocache="+rand,indef,indef,v_action);
        }
		else if ($p.app.tabs.selId != 0)
        {
			//$p.app.pages.load(posh["xmlmodules"],"id="+$p.app.tabs.selId,indef,v_action);
			$p.app.pages.load(posh["xmlmodules"],"id="+tab[$p.app.tabs.sel].id,indef,v_action);
        }
	},
	/*
		Function:  summary
                                $p.app.pages.summary 
                                
                                Summary of the page articles in the main portal enterprise page
                                
                                Set within the installation of portaneo, see adm_config
                                parameters are set in install/createdb_5_pee_mysql.sql
                                
                                see $p.app.displayfeaturedHome about displaying this summary
                                
                                Others possible items come from application.js (network articles)
                                
                     Parameters: 
                     
                                v_div - element to display the summary 
                                
                      See Also:
                            
                            <$p.app.displayfeaturedHome>,<summaryLoad>
	*/
	summary: function(v_div)
	{ 
		var l_s = $p.html.buildTitle(lg('latestArticlesOfMyPages'))
			+'<div id="homesummarypages"></div>';

        $p.print(v_div,l_s);

		$p.app.pages.summaryLoad(0,indef,'homesummarypages');
	},
	/*
		Function: initSummary
                                $p.app.pages.initSummary 
                                
                                Init the summary of the page articles
	*/
	initSummary:function()
	{
		$p.article.init();
		$p.print($p.article.contentDiv,$p.html.buildTitle(lg('latestArticlesOfMyPages'))+'<div id="featsummarypages"></div>');

		$p.app.pages.summaryLoad(0,indef,'featsummarypages');
	},
	/*
		Function:  summaryLoad
                                $p.app.pages.summaryLoad 
                                
                                 Load the summary pages from database
                                
                     Parameters: 
                     
                                v_page - number of results per page
                                v_filter - user id filter
			v_div - Div ID to place the results on
	*/
	summaryLoad:function(v_page,v_filter,v_div)
	{
		if (v_filter==indef) v_filter=0;
		$p.ajax.call(posh["xmlpages_latestnews"]+'?p='+v_page+'&f='+v_filter,
			{
				'type':'load',
				'callback':
				{
					'function':$p.app.pages.summaryDisplay,
					'variables':
					{
						'page':v_page,
						'filter':v_filter,
						'div':v_div
					}
				}
			}
		);
	},
	/*
		Function:  summaryDisplay
                                $p.app.pages.summaryDisplay 
                                
                                 Display the summary pages
                                
                     Parameters: 
                     
                                response - xml response
                                vars (array) - variables
	*/
	summaryDisplay:function(response,vars)
	{ 
        //filters
		var l_s = '<p class="linkFilter">';
		l_s += $p.app.pages.addLinktoReloadAllNews(vars);
        //and rest of filters by page, see array tab in $p.app.tabs
        l_s += $p.app.pages.LinkstoNewsbyPages(vars);
        //end filters
        //larticles list
        var l_result = response.getElementsByTagName('article');
		l_s += '<p><div id="lastportalnews">';
            
        l_s += $p.app.pages.displayListArticles(l_result);
        
		l_s += '</div>';

        //next and previous button
		l_s +='<div style="text-align: right">';
            
        var l_max = response.getElementsByTagName('total');
		var max = $p.ajax.getVal(l_max[0],'max','str',false,'#');    
            
		if (vars['page'] != 0) {
            l_s += $p.app.tools.buildPreviousLinkIcon('$p.app.pages.summaryLoad('+
                                                        (vars['page']-1)
                                                        +','
                                                        +vars['filter'] 
                                                        +',"'
                                                        +vars['div']
                                                        +'")');
		}
        l_s += " &nbsp; ";
        if (    l_result.length == 10
                && (max != (vars['page']+1)*10)
            ) {
                l_s += $p.app.tools.buildNextLinkIcon('$p.app.pages.summaryLoad('+
                                                        (vars['page']+1)
                                                        +','
                                                        +vars['filter'] 
                                                        +',"'
                                                        +vars['div']
                                                        +'")');
        }
		l_s += '</div>';
        //end next and previous button
        
		$p.print(vars['div'],l_s);
	},
	//ancre4
	/*
		Function:  summary
                                $p.app.pages.unrated
                                
                                Summary of the unrated page articles in the main portal enterprise page
                                
                     Parameters: 
                     
                                v_div - element to display the summary 
                                
                      See Also:
                            
                            <unratedLoad>
	*/
	unrated:function(v_div)
	{
		if (__useRating)
		{
		var l_s = '<h2>'+$p.img('ico_rss.gif',16,16,'','imgmid')+' '+lg('unratedArticlesOfMyPages')+'</h2><br />'
				+'<div id="homeunratedpages"></div>';

		$p.print(v_div,l_s);

		$p.app.pages.unratedLoad(0,indef,'homeunratedpages');
		}
	},
	/*
		Function:  unratedLoad
                                $p.app.pages.unratedLoad 
                                
                                 Load the unrated summary pages from database
                                
                     Parameters: 
                     
                                v_page - number of results per page
                                v_filter - user id filter
			v_div - Div ID to place the results on
	*/
	unratedLoad:function(v_page,v_filter,v_div)
	{
		if (v_filter==indef) v_filter=0;
		$p.ajax.call(posh["xmlpages_unratedarticles"]+'?p='+v_page+'&f='+v_filter,
			{
				'type':'load',
				'callback':
				{
					'function':$p.app.pages.unratedDisplay,
					'variables':
					{
						'page':v_page,
						'filter':v_filter,
						'div':v_div
					}
				}
			}
		);
	},
	/*
                Function: addLinkToReloadAllUnrated
                
                            $p.app.pages.addLinkToReloadAllUnrated
                            
                            addlink to reload all unrated news
                 
                 Parameters:
                 
                            vars - array containing vars['filter']
                            
                  Returns: HTML     

                    See Also:
                    
                            <unratedDisplay>
        */
	addLinkToReloadAllUnrated: function(vars)
    { 
        if ( vars['filter'] == 0  ) { 
            return  lg('all') + '&nbsp;';
        }
        return '<a href="#" onclick=\'$p.app.pages.unratedLoad(0,0,"'+vars['div']+'");return false;\'>'+lg('all')+'</a>&nbsp;';      
    },
	/*
                    Function: LinksToUnratedByPages
                    
                                $p.app.pages.LinksToUnratedByPages
                     
                                 build list of links from list of tabs existing in the personal portal
                                 
                                 take datas from array tab (see $p.app.tabs)

                     Parameters:
                 
                            vars - array containing vars['filter']

                     Returns : HTML
 
                    See Also:
                    
                            <unratedDisplay>
              */
	linksToUnratedByPages: function(vars) 
	{
        var l_s = '';
        //tab is array of tabs (see $p.app.tabs)
		for (var i=0;i<tab.length;i++)
		{
			if (tab[i].type==1)
			{
				if (tab[i].id==vars['filter'])  { 
                    l_s+=tab[i].label+' &nbsp; '; 
                }
				else    {   
                    l_s+='<a href="#" onclick=\'$p.app.pages.unratedLoad(0,'+tab[i].id+',"'+vars['div']+'");return false;\'>'+tab[i].label+'</a> &nbsp; ';
                }
            }
		}
        return l_s;
    },
	    /*
                Function: displayUnratedListArticles
                
                                $p.app.pages.displayUnratedListArticles
                                
                                display unrated list articles in rss feed
            
                parameters:
                
                            l_result - result from response in summaryDisplay function
                            
                Returns: HTML

                See Also:
                
                            <unratedDisplay>
                
        */
	displayUnratedListArticles: function(l_result) 
	{
        if (l_result.length==0) 
        { 
            return ('<h2>'+lg('lblArchEmpty')+'</h2>');
        }
        var l_s='';
		for (var i=0;i<l_result.length;i++)
		{
			var l_id = $p.ajax.getVal(l_result[i],'id','int',false,0);
			var l_modId = $p.ajax.getVal(l_result[i],'mod_id','int',false,0); 
			var v_tab = $p.ajax.getVal(l_result[i],'tab_id','int',false,0);
			var uniq = $p.ajax.getVal(l_result[i],'uniq','int',false,0); 
			l_s	+= 	'<tr>'
				+	'<td valign="top">'
				+  	$p.img($p.ajax.getVal(l_result[i],'icon','str',false,0),16,16,'','imgmid')
				+  	'</td>'
				+  	'<td valign="top">'
				+  	'<a href="'+$p.ajax.getVal(l_result[i],'link','str',false,'#')
				+ 	'" target="_blank" onclick="$p.app.widgets.rss.saveReadStatus('+l_id+');">'
				+  	$p.ajax.getVal(l_result[i],'title','str',false,'???')
				+ 	'</a>'
				+ 	'<br /><span color="#c6c3c6">'
				+	$p.ajax.getVal(l_result[i],'feed','str',false,'???')
				+	'<span>'
			if (__useRating)
			{ var source = "home";
				$p.app.widgets.rss.loadRating(l_id,uniq-1,'indef',source);  
				$p.app.widgets.rss.loadAverageRating(l_id,uniq-1,'indef',source);
				l_s += 	'<div id="user_rating_home_'+(uniq-1)+'_'+l_id+'"></div>'
					+	'<div id="average_rating_home_'+(uniq-1)+'_'+l_id+'"></div>'
			}
			l_s += '</td>'
				+'</tr>';
		}//ancre5
        return (l_s);
    },
	/*
		Function: unratedDisplay
                                $p.app.pages.unratedDisplay 
                                
                                 Display the unrated pages
                                
                     Parameters: 
                     
                                response - xml response
                                vars (array) - variables
	*/
	unratedDisplay:function(response,vars)
	{
	        //filters
		var l_s = '<p class="linkFilter">';
		l_s += $p.app.pages.addLinkToReloadAllUnrated(vars);
        //and rest of filters by page, see array tab in $p.app.tabs
        l_s += $p.app.pages.linksToUnratedByPages(vars);
        //end filters
        //larticles list
		var l_result = response.getElementsByTagName('article');
		l_s += '<p><div id="unratedportalnews">'
			+ '<table>';
        
        l_s += $p.app.pages.displayUnratedListArticles(l_result);
		
		l_s += '</table>'
			+ '</div>';
                
        //next and previous button
		l_s +='<div style="text-align: right">';
           
        var l_max = response.getElementsByTagName('total');
		var max = $p.ajax.getVal(l_max[0],'max','str',false,'#');    
            
		if (vars['page'] != 0) {
            l_s += $p.app.tools.buildPreviousLinkIcon('$p.app.pages.unratedLoad('+
                                                        (vars['page']-1)
                                                        +','
                                                        +vars['filter'] 
                                                        +',"'
                                                        +vars['div']
                                                        +'")');
		}
		
        l_s += " &nbsp; ";
        if (    l_result.length == 10
                && (max != (vars['page']+1)*10)
            ) {
                l_s += $p.app.tools.buildNextLinkIcon('$p.app.pages.unratedLoad('+
                                                        (vars['page']+1)
                                                        +','
                                                        +vars['filter'] 
                                                        +',"'
                                                        +vars['div']
                                                        +'")');
        }
		l_s += '</div>';
        //end next and previous button
        
		$p.print(vars['div'],l_s);
	},
    /*
                Function: displayListArticles
                
                                $p.app.pages.displayListArticles
                                
                                display list articles in rss feed
            
                parameters:
                
                            l_result - result from response in summaryDisplay function
                            
                Returns: HTML

                See Also:
                
                            <summaryDisplay>
                
        */
    displayListArticles: function (l_result) 
	{
        if (l_result.length==0) 
        { 
            return ('<h2>'+lg('lblArchEmpty')+'</h2>');
        }
        var l_s='';
        //$p.img('../modules/pictures/rss'+$p.ajax.getVal(l_result[i],'feedid','int',false,0)+'.ico',16,16,'','imgmid')
        
		var iconpath = "../modules/pictures/rss.gif";
        
        //icon rss problem
		for (var i=0;i<l_result.length;i++)
		{
            var feedid = $p.ajax.getVal(l_result[i],'feedid','str',false,'');
            var iconid = $p.ajax.getVal(l_result[i],'iconid','str',false,'');
			var l_id=$p.ajax.getVal(l_result[i],'id','int',false,0);
            if (iconid=='') {
                iconpath = "../modules/pictures/rss"+feedid+".ico";
            } else {
                iconpath = iconid;
            }
			l_s += '<div class="homeitem">'
                + '<div style="float: left;padding-right: 5px;">'
				+ $p.img(iconpath,16,16,'','imgmid')
                + '</div>'
                + ' <span color="#c6c3c6">'+$p.ajax.getVal(l_result[i],'feed','str',false,'???')+'<span>  | '
				+ '<a href="'+$p.ajax.getVal(l_result[i],'link','str',false,'#')+'" target="_blank" onclick="$p.app.widgets.rss.saveReadStatus('+l_id+');">'+$p.ajax.getVal(l_result[i],'title','str',false,'???')+'</a>'
				
				+ '</div>';
		}

        return (l_s);
    },
    /*
                Function: addLinktoReloadAllNews
                
                            $p.app.pages.addLinktoReloadAllNews
                            
                            addlink to reload all news
                 
                 Parameters:
                 
                            vars - array containing vars['filter']
                            
                  Returns: HTML     

                    See Also:
                    
                            <summaryDisplay>
        */
    addLinktoReloadAllNews: function(vars)
    {
        if ( vars['filter'] == 0  ) { 
            return  lg('all') + '&nbsp;';
        }
        return '<a href="#" onclick=\'$p.app.pages.summaryLoad(0,0,"'+vars['div']+'");return false;\'>'+lg('all')+'</a>&nbsp;';      
    },
    /*
                    Function: LinkstoNewsbyPages
                    
                                $p.app.pages.LinkstoNewsbyPages
                     
                                 build list of links from list of tabs existing in the personal portal
                                 
                                 take datas from array tab (see $p.app.tabs)

                     Parameters:
                 
                            vars - array containing vars['filter']

                     Returns : HTML
 
                    See Also:
                    
                            <summaryDisplay>
              */
    LinkstoNewsbyPages: function (vars)
    {
        var l_s = '';
        //tab is array of tabs (see $p.app.tabs)
		for (var i = 0;i < tab.length;i++)
		{
			if (tab[i].type == 1)
			{
				if (tab[i].id == vars['filter'])  { 
                    l_s += $p.img(tab[i].icon,16,16,indef,'imgmid')+' '
                        + tab[i].label
                        +' &nbsp; '; 
                }
				else    {   
                    l_s += '<a href="#" onclick=\'$p.app.pages.summaryLoad(0,'+tab[i].id+',"'+vars['div']+'");return false;\'>'
                        + $p.img(tab[i].icon,16,16,indef,'imgmid')+' '
                        + tab[i].label
                        + '</a> &nbsp; ';
                }
            }
		}
        return l_s;
    },
	/*
		Function: setCurrent
                                $p.app.pages.setCurrent
                                
                                Set current tab as the default one (write in cookie)
		
                     Parameters:

			 v_prof - tab ID
	*/
	setCurrent:function(v_prof)
	{
		v_prof=v_prof.toInt();
		$p.app.tabs.selId=v_prof;
		$p.cookie.write("currentpage="+v_prof);
	},
	/*
		Function: loadOnStart
                               $p.app.pages.loadOnStart
                               
                               Load the pages defined by user on startup
	*/
	loadOnStart:function()
	{
        
		for (var i=0;i<tab.length;i++)
		{
			if (tab[i].loadstart==1 
                && tab[i].id!=$p.app.tabs.selId 
                && tab[i].isLoaded==indef) {
                    $p.app.pages.load(posh["xmlmodules"],"id="+tab[i].id,i);
            }
		}
	},
	/*
		Function: newPortal
                                $p.app.pages.newPortal 
                                
                                open popup to create a new page in the portal
                                
                                 Open a popup
                                 
                                 called from $p.app.tabs.newPageLink
	
                    See Also:
                            
                            <displayFormtoGetPageFromURL>,<displaySharingForm>,<$p.app.tabs.newPageLink>
            */
	newPortal:function()
	{
		$p.plugin.hook.launch('app.pages.newPortal.start');

		var l_background='#ffffff';

		var l_s=''
			+'<table width="450" cellspacing="0" cellpading="5">'
			+'<tr bgcolor="'+l_background+'">'
			+'<td valign="top">'+$p.img('page_blank.gif',56,67,'','imgmid')+'</td>'
			+'<td valign="top"><a href="#" onclick="return $p.app.pages.newEmpty();"><b>'+lg('lblNewEmpty')+'</b></a></td>'
			+'</tr>';

		l_background=(l_background=='#ffffff')
			?'#efefef'
			:'#ffffff';
        l_s += $p.app.pages.displayFormtoFindSharePages(l_background);

		l_background=(l_background=='#ffffff')
			?'#efefef'
			:'#ffffff';        
        l_s += $p.app.pages.displayFormtoGetPageFromURL(l_background);
		
		if (__displayPublicPages 
            && $p.app.user.id>0) { 
            l_background=(l_background=='#ffffff')?'#efefef':'#ffffff';
			l_s+='<tr bgcolor="'+l_background+'">'
				+'<td valign="top">'+$p.img('page_favorite.gif',56,67,'','imgmid')+'</td>'
				+'<td valign="top"><b>'+lg('pageSelection')+'</b><br /><div id="publicpages" style="padding: 4px;">Loading ...</div></td>'
				+'</tr>';
        }

		l_s+='</table>';

		$p.app.popup.show(l_s,510,indef,$p.img('ico_newportal.gif',16,16,'','imgmid')+' '+lg('lblNewTit'));

		if (__displayPublicPages 
            && $p.app.user.id>0) {
                $p.app.pages.loadPublicPages();
        }	

		$p.plugin.hook.launch('app.pages.newPortal.end');
	},
    /*
                Function: displayFormtoGetPageFromURL
                
                        $p.app.pages.displayFormtoGetPageFromURL
                        
                        call $p.app.pages.newHtmlPage
                
                 Returns: html 
                 
                 See also:
                 
                 <$p.app.pages.newHtmlPage>
        */
    displayFormtoGetPageFromURL: function (l_background) 
	{
		return'<tr bgcolor="'+l_background+'">'
			+'<td valign="top">'+$p.img('page_html.gif',56,67)+'</td>'
			+'<td valign="top"><b>'+lg('newPageFromUrl')+'</b><br />'
			+'<form name="f2" onsubmit="return $p.app.pages.newHtmlPage(this.url.value)">'
			+'<input type="text" name="url" value="http://" style="width: 250px;" /> <input type="submit" value="'+lg("ok")+'" />'
			+'</form>'
			+'</td>'
			+'</tr>';
    },
    /*
                    Function: displayFormtoFindSharePages
                
                            $p.app.pages.displayFormtoFindSharePages
                            
                            Form to get pages created by other users
                            
                            use function: $p.app.pages.search 
                            
                      Returns : HTML
                      
                      See also:
                      
                            <$p.app.pages.newHtmlPage>,<$p.app.pages.search>
            */
    displayFormtoFindSharePages: function(l_background) 
	{
        if (!$p.app.user.id  ) return '';
        if (!__useSharing) return  '';
		return '<tr bgcolor="'+l_background+'">'
				+'<td valign="top">'+$p.img('page_search.gif',56,67,'','imgmid')+'</td>'
				+'<td valign="top"><b>'+lg('lblNewExample')+'</b><br />'
				+'<form id="formfindsharedpages" name="f" onsubmit="return $p.app.pages.search(this)">'
				+'<br /><input type="text" id="inputsearchsharedpage" name="keywords" style="width: 250px" onkeyup=\'$p.tags.autocompletion.get("inputsearchsharedpage")\' onFocus=\'$p.navigator.inputFocus(this,"'+lg("keywords")+'")\' onBlur=\'$p.navigator.inputLostFocus(this,"'+lg("keywords")+'");$p.tags.autocompletion.hide();\' value="'+lg('keywords')+'" style="color: #aaaaaa" />  '
				+'<input type="submit" value="'+lg("Search")+'" /> '+tooltip('helpTags')
				+'</form>'
				+'<br /><div id="listPagesForKeywords"></div>'
				+'</td>'
				+'</tr>';
    },
	/*
		Function: loadPublicPages
                                $p.app.pages.loadPublicPages 
                                
                                Load the public pages
	*/
	loadPublicPages:function()
	{
		$p.ajax.call(posh["xmlpublicpages"],
			{
				'type':'load',
				'callback':
				{
					'function':$p.app.pages.displayPublicPages
				}
			}
		);
	},
	/*
		Function: displayPublicPages
                                $p.app.pages.displayPublicPages 
                                
                                Display the public pages
                                
                     Parameters:
                     
                                response - xml resposne
                                vars (array) - variables
	*/
	displayPublicPages:function(response,vars)
	{
		l_result=response.getElementsByTagName("page"),l_s="";
		
		for (var i=0;i<l_result.length;i++)
		{
			l_s+="<a href='#' onclick='$p.app.pages.loadPublicPage("+$p.ajax.getVal(l_result[i],"id","int",false,0)+");return false;'>"
               +$p.ajax.getVal(l_result[i],"name","str",false,"")+"</a><br />";
               
			var l_desc=$p.ajax.getVal(l_result[i],"desc","str",false,"");
			if (l_desc!='') l_s+="<i>"+l_desc+"</i><br />";
		}

		$p.print("publicpages",l_s);
	},
	/*
		Function: loadPublicPage
                                $p.app.pages.loadPublicPage 
                                
                                Load one the public page
                                
                     Parameters:
                     
                                v_id - page id
	*/
	loadPublicPage:function(v_id)
	{
		$p.ajax.call(posh["xmlpublicpage"]+'?id='+v_id,
			{
				'type':'load',
				'callback':
				{
					'function':$p.app.pages.createFromXml,
					'variables':
					{
						'reload':false,
                        'id':v_id
					}
				}
				
			}
		);
	},
	/*
		Function: newEmpty
                                $p.app.pages.newEmpty 
                                
                               Create an empty page
                                
                     Parameters:
                     
                                v_name - page name
                     
                     Returns:
                     
                                false
	*/
	newEmpty:function(v_name)
	{
		var l_style=$p.app.tabs.sel==-1     ?     1
                                            :     tab[$p.app.tabs.sel].style;
		
        v_name==indef?v_name=lg('lblNewpage'):'';	
		
        if ($p.app.user.id==0) {
			$p.app.pages.openCreatedTab(0);
        }
		else
		{		
			$p.ajax.call(posh["scr_createportal"],
				{
					'type':'execute',
					'variables':'w=3&s='+l_style+'&n='+v_name+'&t=0&nb=15&a=Y',
					'alarm':false,
					'forceExecution':true,
					'callback':
					{
						'function':$p.app.pages.openCreatedTab
					}
				}
			);
		}
		return false;
	},	

	/*
		Function: newHtmlPage
                                $p.app.pages.newHtmlPage 
                                
                               Create a new tab with html content in frame
                                
                     Parameters:
                     
                                v_url -  html page URL
                                v_name - page name
                     
                     Returns:
                     
                                false
	*/
	newHtmlPage:function(v_url,v_name)
	{
		var l_style=$p.app.tabs.sel==-1     ?    1
                                            :    tab[$p.app.tabs.sel].style;
		
        (v_name==indef)?v_name=lg('lblNewpage'):'';
		
		if ($p.app.user.id==0)  {
            $p.app.pages.openCreatedTab(v_url);
        }
		else
		{
			$p.ajax.call(posh["scr_createportal"],
				{
					'type':'execute',
					'variables':"w=1&s="+l_style+"&n="+v_name+"&t=0&ty=2&p="+$p.string.esc(v_url)+"&a=Y",
					'alarm':false,
					'forceExecution':true,
					'callback':
					{
						'function':$p.app.pages.openCreatedTab
					}
				}
			);
		}
		return false;
	},
	/*
		Function: controlMinimumPage
                                $p.app.pages.controlMinimumPage 
                                
                               Control if the tab can be deleted (to avoid empty portal)
                                
                     Parameters:
                     
			v_tab - tab sequence ID
			v_selectFirst (boolean) - is the first tab selected after removal ? 
			v_needConfirmation (boolean) - define if a confirmation message is displayed before removing the tab
                     
                     Returns:
                     
                                false
	*/
    controlMinimumPage:function()
    {
        var removableTabs=0;
        var numberOfPages=tab.length;
        //if there is only one page, can't delete
        if (numberOfPages==1) { return false; }
        for (var i=0;i<numberOfPages;i++)
        {
            if (tab[i].removable!=indef) {
                removableTabs++;
            }
        }
        return (removableTabs>1?true:false);
    },
	/*
		Function: suppress
                                $p.app.pages.suppress 
                                
                               Remove a tab
                                
                     Parameters:
                     
			v_tab - tab sequence ID
			v_selectFirst (boolean) - is the first tab selected after removal ? 
			v_needConfirmation (boolean) - define if a confirmation message is displayed before removing the tab
                     
                     Returns:
                     
                                false
	*/
	suppress: function(v_tab,v_selectFirst,v_needConfirmation)
	{
        $p.plugin.hook.launch('app.pages.suppress.start');
         
		if (v_tab == indef) { 
            v_tab = $p.app.tabs.sel;
        }
		if (v_selectFirst == indef) {
            v_selectFirst = true;
        }
		if (v_needConfirmation == indef) {
			v_needConfirmation = true;
		}
    
        if(tab[v_tab].removable != indef) {
            if (!$p.app.pages.controlMinimumPage()) {
            	alert(lg("msgSuppressNotAllowed"));
                //$p.app.alert.show(lg("msgSuppressNotAllowed"));
                return false;
            }
        }

		// Suppress a portal
		if (v_needConfirmation)
            response = confirm(lg("msgSuppressConfirm"));
   
		if (!v_needConfirmation || response == 1)    {
            if (v_needConfirmation) {
        		$p.ajax.call('../../app/tests/LoggerResponder.php?action=5&name=' + tab[v_tab].label + '&id=' + tab[v_tab].id, {'type':'load'});
            	
    			$p.ajax.call(posh["scr_suppersonal"],
    				{
    					'type':'execute',
    					'variables':"id="+tab[v_tab].id+"&seq="+tab[v_tab].seq
    				}
    			);
            }

            $p.app.pages.hide();
            $p.app.banner.option.hide();

			//update tabs sequence
			for (var i = 0;i < tab.length;i++)
			{
				if (i > v_tab) tab[i].seq --;
			}
			//delete the tab
			if (tab[v_tab].isLoaded) {
                ($("modules")).removeChild(tab[v_tab].root);
            }
			tab.splice(v_tab,1);
			//refresh tab and choose the first tab
			$p.app.tabs.create(0);
			if (v_selectFirst == true)    {
				//activate the first tab
				if (tab.length >= 1)  {
                    $p.app.tabs.open(0);
                }
				else    {
					$p.app.tabs.sel = -1;
					$p.app.pages.closeAllDiv();
				}
			}
		}

        //refresh menu based on new page
        $p.app.menu.refreshConditionalMenus();

		$p.plugin.hook.launch('app.pages.suppress.end');

		return false;
	},
	/*
		Function: show
                                $p.app.pages.show
                                
                               Display a saved portal
                                
                     Parameters:
                     
			v_id - tab sequence ID
	*/
	show:function(v_id)
	{
		$p.app.widgets.close();
		hideBox();
		$p.app.pages.load("../cache/portal_"+v_id+".xml");
		$p.app.startLoading();
	},
	/*
		Function: hide
                                $p.app.pages.hide
                                
                               Hide the current tab
	*/
	hide:function()
	{
		//hide the pages element before swiching to another page
		if ($p.app.tabs.sel!=-1 
            && $p.app.tabs.sel!=999)
		{
			$p.show("modules"+tab[$p.app.tabs.sel].id,"none");			
            tab[$p.app.tabs.sel].showType=0;
			$p.app.widgets.close();
			//$p.app.menu.config.hide();
			//$p.app.banner.option.hide();
		}
		hideBox();
	},
	/*
		Function: isPageExisting
                                $p.app.pages.isPageExisting : check if page is existing
                                
                               Load a user's pages
                                
                     Parameters:
                     
		         v_sess - type of user ('user' or 'admin')
	*/
	isPageExisting:function(v_sess){
		if (v_sess!=indef)  {
			if (v_sess=="admin")    {
				$p.url.openLink(posh["admin_index"]);
			}
			else    {
				$p.ajax.call('../../app/tests/LoggerResponder.php?action=0', {'type':'load'});
				
				$p.ajax.call(posh["xmlpages"],
					{
						'type':'load',
						'callback':
						{
							'function':$p.app.pages.createFromXml,
							'variables':
							{
								'session':v_sess,
								'reload':true
							}
						}
					}
				);
			}
		}
	},
	/*
		Function: closeAllDiv
                                $p.app.pages.closeAllDiv
                                
                               Hide all opened menu, message box, plugins, banners, ....
	*/
	closeAllDiv:function()
	{
		$p.app.pages.hideFrame();
		/* FRI
		if (__useArchive)   {
			if ($p.article.shown) {
                $p.article.hide();
            }
		}
		*/
		if ($p.network.shown) $p.network.hide();
		if ($p.app.widgets.factory.shown) $p.app.widgets.factory.hide();
		//if ($p.app.banner.option.shown) $p.app.banner.option.hide();
		if ($p.plugin.shown) $p.plugin.hide();

		$p.show("modules","none");
		$p.show("newmod","none");
		$p.app.widgets.rss.reader.hide();
		$p.show("magic","none");//do not suppress !
	},
	/*
	         Function: search
                                $p.app.pages.search
                                
                               Search a page shared by other users

                                called from displayFormtoFindSharePages (in popup to add a new page to portal  $p.app.pages.newPortal)  
                               
                    Parameters:

			v_form - form containing searched string
            
                    Returns:
                    
                                false
	*/
	search:function(v_form)
	{
		$p.app.wait("listPagesForKeywords");	
		var l_keywords=$p.string.formatForSearch($p.tags.formatList(v_form.keywords.value));
		if (l_keywords!="")
		{
			$p.ajax.call(posh["xmlpage_search"]+'?search='+l_keywords,
				{
					'type':'load',
					'callback':
					{
						'function':$p.app.pages.searchResults,
						'variables':
						{
							'keywords':l_keywords
						}
					}
				}
			);
		}
		return false;
	},
	/*
		Function: searchResults
                                $p.app.pages.searchResults
                                
                               Display search results of <search>: lists of pages shared by ohter users for tags wanted
                               
                               links on pages listed call $p.app.pages.loadSharedPortal
                               
                               detail 
                               
                                
                    Parameters:

			response - xml response
                                vars (array) - variables (optional) vars['keywords']
                     
                     See also:
                     
                     <$p.app.pages.loadSharedPortal>
	*/
	searchResults:function(response,vars)
	{
		var l_s="",
		l_result=response.getElementsByTagName("portal");
		if (l_result.length==0) {
			l_s+=lg("noResultForThisSearch");
		}
		else    {
			l_s+=lg("lblResultsFor")+" '"+vars['keywords']+"' :";
			l_s+="<div class='dirdiva' style='height:100px;width:80%;padding:8px;'>";
			for (var i=0;i<l_result.length;i++)
			{
				l_s+="<a href='#' onclick=\"$p.app.pages.loadSharedPortal("+$p.ajax.getVal(l_result[i],"id","int",false,0)+",1,indef,true)\">"
                   +$p.ajax.getVal(l_result[i],"name","str",false,"--")
                   +"</a><br />";
			}
			l_s+="</div>";
		}
		$p.print("listPagesForKeywords",l_s);
	},
	/*
		Function: loadSharedPortal
                                $p.app.pages.loadSharedPortal
                                
                               Load public page who can be shared par member of a same network
                               
                               Page must be shared before
                               
                                called from link created after searchResults
                                
                    Parameters:

			v_id - page id
			v_type - page type (public / private)
			v_check - check string for private pages
            
                    See also:
                    
                        <searchResults>
	*/
	loadSharedPortal: function(v_id,v_type,v_check,v_isOverview,v_reload)
	{
		if (v_type != indef 
            && v_type == 2)   {
                var l_url = posh["xmlfriendportal"]+'?id='+v_id+'&chk='+v_check;
		}
		else    {
			var l_url = posh["xmlpage_detail"]+'?id='+v_id;
		}
		$p.ajax.call(l_url,
			{
				'type':'load',
				'callback':
				{
					'function':(v_isOverview ? $p.app.pages.displayOverview
                                             : $p.app.pages.createFromXml),
					'variables':
					{
						'pageid':v_id,
						'type':v_type,
						'check':v_check,
						'reload':v_reload
					}
				}
			}
		);
	},
    getPageType: function (vars) {
        var page_type = '';
		if (vars['type']==0)    {
			page_type='<br /><br />'
				+'<center>'
				+'<input class="btnbig" type="button" value="'+lg("lblUseExample")+'" onclick="$p.app.pages.show('+vars['pageid']+')" />'
				+'</center>';
		}
		else    {
			page_type='<br /><br />'	
				+'<center>'
				+'<input class="btnbig" type="button" value="'+lg('lblCreateFromExample')+'" onclick=\'$p.app.pages.loadSharedPortal('+vars['pageid']+','+vars['type']+',"'+vars['check']+'",false)\' />'
				+'</center>';
		}
        return page_type;
    
    },
    listModules:function (response,vars) {
        var l_s='';
		if (response.getElementsByTagName("module")[0])
		{
			var l_i=0;
			l_s+="<table cellpadding='0' cellspacing='0'>";
			while (response.getElementsByTagName("module")[l_i])
			{
				var l_result=response.getElementsByTagName("module")[l_i];
				var l_id=$p.ajax.getVal(l_result,"id","int",false,0);
				l_s+='<tr>'
					+'<td>'
					+$p.img("../modules/pictures/box0_"+l_id,16,16,"","imgmid")
					+'</td>'
					+'<td>'
					+'<a href="#" onclick=\'return $p.app.widgets.open('+l_id+',"'+$p.ajax.getVal(l_result,"vars","str",false,"")+'")\'>'
					+$p.ajax.getVal(l_result,"name","str",false,"-")
					+'</td>'
					+'</tr>';
	
				l_i++;
			}
			l_s+='</table>';
		}
		else
		{
			l_s+=lg("lblNoModule");
		}    
        return l_s;
    },
	/*
		Function: displayOverview
                                $p.app.pages.displayOverview
                                
                               Display detail of  a page shared by another user when somebody want accept the page for his own portal
                               
                               Page visible  after search on tags, page without tags are not accessible
                               
                               calback from loadSharedPortal
                                
                     Parameters:

			response - xml response
                                vars (array) - variables (optionnal) 
                                
                        See also:
                        
                            <loadSharedPortal>
           */
	displayOverview: function(response,vars)
	{
		$p.app.popup.hide();
        var l_tit = $p.ajax.getVal(response,"name","str",false,'');

        //if page is not shared anymore
        if (l_tit == '')
        {
            $p.app.alert.show(lg('itemNotSharedAnymore'));
            return;
        }
        
        var page_description = $p.ajax.getVal(response,"description","str",false,'');

		var l_s = ''
			+ '<table cellpadding="0" cellspacing="1" border="0" width="100%">'
			+ '<tr>';

		if (page_description != '')
		{
			l_s += '<td valign="top">'
                + lg("lblDescription")
                + "&nbsp;:<br />"
                +  page_description
                + '</td>';
		}
		l_s += '<td valign="top">'
			+ lg("lblModules")
			+ '&nbsp;:<br />'
            +  $p.app.pages.listModules(response,vars)
            + '</td>'
			+ '</tr>'
			+ '</table>'
			+ $p.app.pages.getPageType(vars);
		
		if (showNewMod) $p.app.widgets.close();
		
        $p.show("newmod","block");
        $p.print("newmod",box(0,l_tit,"$p.app.pages.close('newmod')",l_s));
	},
    close: function (what) {
        $(what).innerHTML='';
    },
	/*
		Function: createFromXml
                                $p.app.pages.createFromXml :
                                
                               Add a new tab based on XML Parameters
                                
                    Parameters:

			response - xml object
                                vars (array) - variables (optionnal) 
           */
	createFromXml: function(response,vars)
	{
		$p.app.popup.hide();
        var canCallback = false,
            l_pageid = 0;

		//if page tag, redirect to this page
		if (response.getElementsByTagName("page")[0])
        {
			$p.url.openLink("../portal/"+$p.ajax.getVal(response.getElementsByTagName("page")[0],"url","str",false,""));
			return;
		}
		v_reload = (vars['reload']==indef ? false : vars['reload']);
        
		// Get modules from XML file for creation of a new portal
		var l_result = response.getElementsByTagName("portal");
		// Get modules from XML file for creation of a new portal
		if (l_result.length != 0)
		{
			for (var j = 0;j < l_result.length;j++)
			{
				var i = 0,
                    l_s = "",
                    l_x = 100,
                    l_y = 200,
                    l_name=$p.ajax.getVal(l_result[j],"name","str",false,lg("lblNewpage")),
                    l_style=$p.ajax.getVal(l_result[j],"style","int",false,1),
                    l_nbcol=$p.ajax.getVal(l_result[j],"nbcol","int",false,3),
                    l_mode=$p.ajax.getVal(l_result[j],"showtype","int",false,0),
    				l_npnb=$p.ajax.getVal(l_result[j],"npnb","int",false,25),
                    l_type=$p.ajax.getVal(l_result[j],"type","int",false,1),
                    l_param=$p.string.esc($p.ajax.getVal(l_result[j],"param","str",false,"")),
    				l_moduleAlign=$p.ajax.getVal(l_result[j],"modulealign","str",false,"Y"),
                    l_removable=$p.ajax.getVal(l_result[j],"removable","int",false,1),
                    l_pageid=$p.ajax.getVal(l_result[j],"pageid","int",false,0);
                
				while (l_result[j].getElementsByTagName("module")[i])
				{
					var l_result2 = l_result[j].getElementsByTagName("module")[i];

					l_s += "&id"+i+"="+$p.ajax.getVal(l_result2,"id","int",false,0)
                        + "&col"+i+"="+$p.ajax.getVal(l_result2,"col","int",false,1)
                        + "&pos"+i+"="+$p.ajax.getVal(l_result2,"pos","int",false,i)
                        + "&posj"+i+"="+$p.ajax.getVal(l_result2,"posj","int",false,i)
                        + "&x"+i+"="+$p.ajax.getVal(l_result2,"x","int",false,l_x)
                        + "&y"+i+"="+$p.ajax.getVal(l_result2,"y","int",false,l_y)
                        + "&var"+i+"="+$p.string.esc($p.ajax.getVal(l_result2,"vars","str",false,""))
                        + "&blocked"+i+"="+$p.ajax.getVal(l_result2,"blocked","int",false,0)
                        + "&minimized"+i+"="+$p.ajax.getVal(l_result2,"minimized","int",false,0);

					i++;
                    l_x += 30;
                    l_y += 30;
				}

                $p.ajax.call(posh["scr_createportal"],
                    {
                        'type':'execute',
                        'variables':"w="+l_nbcol+"&s="+l_style+"&a="+l_moduleAlign+"&n="+$p.string.esc(l_name.substr(0,29))+"&t="+l_mode+"&nb=25&ty="+l_type+"&p="+l_param+"&sess="+vars['session']+"&pageid="+l_pageid+"&removable="+l_removable+l_s,
                        'forceExecution':true,
                        'alarm':false,
                        'callback':{
                            'function':(j < l_result.length - 1
                                            ? null
                                            : v_reload
                                                ? openPage
                                                : $p.app.pages.openCreatedTab
                                        ) 
                        },
                        'priority':2,
                        'asynchron':false
                    }
                );  
            }
        }
	},
	/*
		Function: select
                                $p.app.pages.select
                                
                               Select a page
                                
                    Parameters:

			v_prof - page id
           */
	select:function(v_prof)
	{
		$p.app.pages.change(v_prof);
	},
	/*
		Function: displayLockWindow
        
                                $p.app.pages.displayLockWindow
                                
                                Display connection box for a secured page
	*/
    displayLockWindow:function()
	{
        hideAllBox();
		$p.app.widgets.rss.reader.close();        
		
		var l_s='<div class="protectbox">\
        <div id="loginscreen">\
			<div style="float: left;width: 150px;">\
                <h2 id="lsconnectTitle">'+lg('lblConnect')+'&gt;</h2>\
                <div id="msg_conn">&nbsp;</div>\
                <p><a class="w" id="ls_missingpass" onclick="return $p.app.connection.buildmissingPasswordForm();" href="'+posh["password_missing"]+'" target="_blank">'+lg('lblMissingPassword')+'</a></p>\
			</div>\
			<div id="displayPart">\
                 <form name="identif" method="post" onsubmit="return $p.app.pages.unlock(this);">\
                    <strong><label id="ls_lbl_password" for="password">'+lg('lblPassword')+'</label></strong><br />\
                    <input type="password" name="pass" maxlength="32" class="thinbox" style="width: 250px;"/><br /><br />\
                    <input type="checkbox" id="ls_lbl_autoconnect" name="autoconn" />'+lg('lblAutoConnection')+'<br /><br />\
                    <input type="submit" id="ls_btn_connect" class="btn" value="'+lg('lblConnect')+'" />\
                </form>\
			<br/><br/>\
            </div>\
        </div>\
        </div>';
 
        $p.print("modules"+tab[$p.app.tabs.sel].id,l_s);
        
		$p.show("modules","block");
		$p.styles.setActiveStyleSheet($p.app.tabs.defTheme);

	},
	/*
		Function: unlock
                                $p.app.pages.unlock
                                
                                Unlock secured page
		
                     Parameters:

			 Form object where password is typed
             
                     Returns:
                        
                                false
	*/
	unlock:function(v_form)
	{
				
		$p.ajax.call(posh["scr_unlock"],
			{
				'type':'execute',
				'variables':"id="+tab[$p.app.tabs.sel].id+"&pass="+v_form.pass.value,
				'alarm':true,
				'forceExecution':false,
				'callback':
				{
					'function':$p.app.pages.open
				}
			}
		);
		return false;
	},
	/*
		Function: lock
                                $p.app.pages.lock
                                
                                Lock a secured page
	*/
	lock:function()
	{

$p.app.counter.activityStep=0;
		$p.ajax.call(posh["scr_protect"],
			{
				'type':'execute',
				'variables':"",
				'alarm':false,
				'forceExecution':false,
				'callback':
				{
					'function':$p.app.pages.open
				}
			}
		);
	},
	/*
		Function: load
                                $p.app.pages.load
                                
                                Load personalized page content information
		
                    Parameters:

			v_page - xml file containing page information
			v_id - tab ID
			v_tab - tab sequence id
			v_action - action to launch on tab opening
	*/
	load: function(v_page,v_id,v_tab,v_action)
	{
		$p.plugin.hook.launch('app.pages.load.start');

		if (v_tab==indef) v_tab = $p.app.tabs.sel;
		if (v_tab==indef)
            v_tab = $p.app.tabs.idToPos($p.app.tabs.selId);
		if (v_tab == indef || !v_tab) v_tab = 0;
		tab[v_tab].module.length = 0;
		tab[v_tab].isLoaded = false;
		$p.app.widgets.rss.init(v_tab);
		if (!v_id) v_id = "";
		var l_method = (v_id == "" ? "GET" : "POST");

		//create modules container div
		$p.app.pages.defineWidgetContainer(v_tab);
		$p.ajax.call(v_page,
			{
				'type':'load',
				'callback':
				{
					'function':$p.app.pages.treat,
					'variables':
					{
						'tab':v_tab,
						'action':v_action
					}
				},
				'source':'xml',
				'variables':v_id,
				'method':l_method,
				'priority':1
			}
		);

		$p.plugin.hook.launch('app.pages.load.end');
	},
	/*
		Function: treat
                                $p.app.pages.treat
                                
                                Treat opened page information
		
                    Parameters:

			response - xml object containing page information
                                vars (array) - variables (optionnal)
	*/
	treat:function(response,vars)
	{
		$p.plugin.hook.launch('app.pages.treat.start');

		var l_tab=vars['tab'];
		var l_action=vars['action'];
		var i=0,result;
		//if page is not existing
		if (response.getElementsByTagName("nopage")[0]) openPage(tab[0].id);
		//if not yet install
		if (response.getElementsByTagName("install")[0]) $p.url.openLink(posh["installrequested"]);
		//if connected mode
		//if (response.getElementsByTagName("empty")[0]) $p.url.openLink("../portal/login.php");
		//var l_type=$p.ajax.getVal(response,"type","int",false,1);
		var l_type=tab[l_tab].type;
		if (l_type==1)
		{
			widgetHeight = $p.ajax.getVal(response,"height","int",false);
			//if page locked
			if ($p.ajax.getVal(response,"pagelocked","int",false,0)==1)
			{
				$p.app.popup.hide();
				tab[l_tab].locked=true;
				$p.app.newEnv('locked');
				if (l_tab==$p.app.tabs.sel)  $p.app.pages.displayLockWindow();
			}
			else
			{
				$p.app.newEnv(($p.app.user.id==0)?'portal_page_anon':'portal_page_conn');
                
				
				tab[l_tab].locked=false;
				tab[l_tab].colnb=$p.ajax.getVal(response,"nbcol","int",false,tab[l_tab].colnb);
				tab[l_tab].showType=$p.ajax.getVal(response,"showtype","int",false,tab[l_tab].showType);
				tab[l_tab].newspapernb=$p.ajax.getVal(response,"npnb","int",false,tab[l_tab].newspapernb);
				tab[l_tab].controls=$p.ajax.getVal(response,"ctrl","str",false,tab[l_tab].controls);
				tab[l_tab].moduleAlign=($p.ajax.getVal(response,"modulealign","str",false,(__moduleAlignDefault?"Y":"N"))=="Y"?true:false);
				//var oldstyle=tab[$p.app.tabs.sel].style;
				tab[l_tab].style=$p.ajax.getVal(response,"style","int",false,1);
                //jspass=$p.ajax.getVal(response,"usepass","str",false,"");
                tab[l_tab].lock=$p.ajax.getVal(response,"usepass","str",false,0);
                $p.app.banner.info.requested=$p.ajax.getVal(response,"advise","str",false,$p.app.banner.info.requested);

				tab[l_tab].usereader=__usereader?$p.ajax.getVal(response,"usereader","int",false,tab[l_tab].usereader):0;
				$p.app.pages.def=$p.ajax.getVal(response,"default","str",false,$p.app.pages.def);
                
				while (response.getElementsByTagName("module")[i])
				{
					result=response.getElementsByTagName("module")[i];
					tab[l_tab].module[i] = new $p.app.widgets.object(
                        $p.ajax.getVal(result,"col","int",true,0),
                        $p.ajax.getVal(result,"pos","int",false,0),
                        $p.ajax.getVal(result,"posj","int",false,0),
                        $p.ajax.getVal(result,"height","int",false,100),
                        $p.ajax.getVal(result,"id","int",false,0),
                        $p.ajax.getVal(result,"site","str",false,"/"),
                        $p.ajax.getVal(result,"name","str",false,"--"),
                        $p.ajax.getVal(result,"vars","str",false,""),
                        $p.ajax.getVal(result,"minmodsize","int",false,180),
                        $p.ajax.getVal(result,"updmodsize","int",false,1),
                        $p.ajax.getVal(result,"minmodsize","int",false,180),
                        $p.ajax.getVal(result,"url","str",false,""),
                        $p.ajax.getVal(result,"x","int",false,0),
                        $p.ajax.getVal(result,"y","int",false,0),
                        $p.ajax.getVal(result,"uniq","int",false,0),
                        $p.ajax.getVal(result,"format","str",false,"I"),
                        $p.ajax.getVal(result,"nbvars","int",false,0),
                        tab[l_tab].id,
                        $p.ajax.getVal(result,"blocked","int",false,0),
                        $p.ajax.getVal(result,"minimized","int",false,0),
                        $p.ajax.getVal(result,"usereader","int",false,1),
                        $p.ajax.getVal(result,"autorefresh","int",false,0),
                        $p.ajax.getVal(result,"icon","str",false,indef),
                        false,
                        '',
                        '',
                        '',
                        $p.ajax.getVal(result,"views","str",false,'home'),
                        $p.ajax.getVal(result,"l10n","str",false,''),
                        $p.ajax.getVal(result,"shared","str",false,"")
                        );
                        tab[l_tab].module[i].PositioninTab=i;
                        
                        //alert('4 create widget '+$p.ajax.getVal(result,"name","str",false,"--") );
    					/*FRI register widget*/
                        
					i++;
				}
				$p.app.stats();

			}
		}
		else if (l_type==2 
                && l_tab==$p.app.tabs.sel)  {
			eval(tab[l_tab].fct);
			$p.app.checkLoading(false);
		}
		else if (l_type==3 
                && l_tab==$p.app.tabs.sel)  {
			eval($p.ajax.getVal(response,"param","str",false,""));
			$p.app.checkLoading(false);
		}
		//change style css if the one to show is not the one already selected
		if (l_tab==$p.app.tabs.sel 
            && ($p.app.style==0 
            || $p.app.style!=tab[l_tab].style)) {
                $p.styles.setActiveStyleSheet(tab[l_tab].style);
        }
		//$p.app.menu.config.oldStyle=tab[$p.app.tabs.sel].style;

		$p.app.pages.init(l_tab);

		$p.app.pages.computeFooterPosition();
				
		if (l_action!=indef 
            && l_action!="")    {
			if (l_action=="edittab") $p.app.tabs.edit($p.app.tabs.sel);
		}
		
		$p.plugin.hook.launch('app.pages.treat.end');
	},
	/*
	         Function: init
                                $p.app.pages.init 
                                
                                Initialize personalized page 
		
                    Parameters:

			 v_tab - tab sequence ID
	*/
	init:function(v_tab)
	{
		//init the page
		if (v_tab==indef) v_tab=$p.app.tabs.sel;
		if (v_tab==$p.app.tabs.sel){ //only if initializing active tab
			$p.app.startLoading();
			if (tab[v_tab].showType==1) $p.app.widgets.rss.reader.close();
			$p.show("modules","block");
			if ($p.app.banner.option.shown) $p.show("advise","block");
		}

		$p.app.pages.defineWidgetContainer(v_tab);
		//create cols if modules are aligned in columns
		if (tab[v_tab].moduleAlign) {
                $p.app.pages.columns.createAll(v_tab);
        }

		$p.app.widgets.createAll(v_tab);

		$p.app.widgets.enableMoving(v_tab);
		if (v_tab==$p.app.tabs.sel)//only if initializing active tab
		{
			$p.app.checkLoading(false);
			$p.cookie.check();
			$p.app.mainMenu();
		}
		
		// [RODIN] Add top-border and a name to the division corresponding to tab[v_tab]
		(tab[v_tab].root).setAttribute("class","borderedModuleRODIN");
		
		//prevent cols to be wider than 400px
//		if (p_area.showType==0){for (var i=1;i<=$p.app.pages.columns.nb;i++){cols[i].style.width="400px";};}else {cols[1].style.width="400px";}
	},
	/*
	         Function: clean
                                $p.app.pages.clean 
                                
                                Remove all the objects from the personalized page
		
                    Parameters:

			 v_mainObj - object to be cleaned
	*/
	clean:function(v_mainObj)
	{
		var l_obj=null;
		//suppress all the descendant nodes of mainObj
		if (v_mainObj!=null)
		{
			while (v_mainObj.hasChildNodes())
			{
				l_obj=v_mainObj;
				while (l_obj.hasChildNodes()) l_obj=l_obj.firstChild;
				l_obj=l_obj.parentNode;
				l_obj.removeChild(l_obj.firstChild);
			}
		}
	},
	/*
	         Function: frame
                                $p.app.pages.frame 
                                
                                Open a framed page
		
                    Parameters:

			v_url - url of the page that is opened in the frame
			v_id - tab ID
	*/
	frame:function(v_url,v_id)
	{
		$p.show("footer","none");
		//$p.app.banner.option.hide();
		$p.show("box","none");
		$p.app.pages.closeAllDiv();
		$p.show("modules","block");
		$p.app.stopLoading();
		$p.app.newEnv('portal_frame');

		var l_tab=$p.app.tabs.idToPos(v_id);
//		if (l_tab!=$p.app.tabs.sel)  (tab[l_tab].root).setStyle("display","none");
	
		if (tab[l_tab].isLoaded)
			$p.show("modules"+v_id,"block");
		else
		{
			// frame height calculation
			var l_endPosition = Window.getHeight()-15;
			var l_startPosition = $p.getPos($p.get("modules"),"Top");

			if ($("modules"+v_id) == null)    {
				tab[l_tab].root = new Element('div', {'id': "modules"+v_id} );
				($("modules")).appendChild(tab[l_tab].root);
			}
			
			var l_s = "<iframe id='pagefrm"+v_id+"' src='"+v_url+"' frameborder='no' marginwidth='0' marginheight='0' scrolling='auto' style='height:"+(l_endPosition-l_startPosition)+"px;width:100%;z-index:auto;' onload='$p.adjustFrameHeight(this);'></iframe>";
			$p.print('modules'+v_id,l_s);
			$p.show('modules'+v_id,'block');
			tab[l_tab].isLoaded = true;
			$p.app.tabs.create(l_tab);
		}

		//lock menus
		$p.app.menu.widget.locked=true;
		//$p.app.tabs.selId=tab[$p.app.tabs.sel].id;
		$p.app.tabs.selId=v_id;
		$p.app.pages.computeFooterPosition();
        //refresh menu based on new page
        $p.app.menu.refreshConditionalMenus();
		//apply style
		if (l_tab==$p.app.tabs.sel 
            && ($p.app.style==0 
            || $p.app.style!=tab[l_tab].style)) {
                $p.styles.setActiveStyleSheet(tab[l_tab].style);
        }
	},
	/*
	         Function: hideFrame
                                $p.app.pages.hideFrame 
                                
                                Close a framed page
	*/
	hideFrame:function()
	{
		$p.show("footer","block");
	},
	/*
	         Function: computeSizeAndResize
                                $p.app.pages.computeSizeAndResize 
                                
                                Compute page size based on widgets and resize it
		
                    Parameters:

			v_tab - tab sequence ID
	*/
	computeSizeAndResize:function(v_tab)
	{
		if (tab[v_tab].moduleAlign)
		{
			var l_colWidth=[];
			for (var i=0;i<tab[v_tab].module.length;i++)
			{
				if (l_colWidth[tab[v_tab].module[i].newcol]==indef 
                    || tab[v_tab].module[i].minModSize>l_colWidth[tab[v_tab].module[i].newcol]) {
                        l_colWidth[tab[v_tab].module[i].newcol]=tab[v_tab].module[i].minModSize;
                }
			}
			for (var i=0;i<tab[v_tab].module.length;i++)
			{
				tab[v_tab].module[i].hide();
			}
			var l_screenWidth=($("header")).offsetWidth;
			var l_availWidth=(l_screenWidth-widgetDecalX)-($p.navigator.IE?0:10);
			(tab[v_tab].root).setStyle("width",l_availWidth+"px");
			for (var i=0;i<tab[v_tab].module.length;i++)
			{
				tab[v_tab].module[i].show();
			}
		}
	},
	/*
	         Function: resize
                                $p.app.pages.resize *(unavailable)*
                                
                                Resize a frame page
	*/
	resize:function()
	{
//	$p.app.pages.computeSizeAndResize($p.app.tabs.sel);
	},
	/*
	         Function: displayItems
                                $p.app.pages.displayItems
                                
                                Display and initialize objects move of a personalized page
		
                    Parameters:

			v_tab - tab sequence ID
	*/
	displayItems:function(v_tab)
	{
		if (v_tab==indef) v_tab=$p.app.tabs.sel;
		//changeStyle("optmod","display",""); Removed in 1.5 > too long if many opened pages
		//initialise modules move only if allowed and not in newspaper mode
		if (useMod
            && tab[v_tab].showType==0)  {
			if (!tab[v_tab].moveIsInit) {
				if (tab[v_tab].moduleAlign) {
					$p.app.pages.columns.init(v_tab);
				}
				else    {
					$p.app.widgets.initMove(v_tab);
				}
			}
		}
	},
	/*
	         Function: computeFooterPosition
                                $p.app.pages.computeFooterPosition
                                
                                Place the footer based on the widgets position
	*/
	computeFooterPosition:function()
	{
		var l_footer=$("footer");
		if (l_footer==null) return;
		/*FRI*/ 
		/*
		if ($p.app.tabs.sel==-1 
            || tab[$p.app.tabs.sel].moduleAlign)    {
			l_footer.setStyle("position","relative");
		}
		*/
		if ($p.app.tabs.sel==-1 
	            || tab[$p.app.tabs.sel].moduleAlign)    
		{
			var currentTab=tab[$p.app.tabs.sel];
			var widgetsBottomPos=0,currWidgetBottomPos;
			var widgetsColumnHeight=0;
			var maxWidgetsColumnHeight=0;
			for(var c=1;c<currentTab.cols.length;c++)
			{
				/*FRI: measure max height in col */
				var col=currentTab.cols[c];
				widgetsColumnHeight = col.offsetHeight;
				/*FRI: compute maximum */
				if (widgetsColumnHeight > maxWidgetsColumnHeight)
					maxWidgetsColumnHeight = widgetsColumnHeight;
			}
			widgetsBottomPos = maxWidgetsColumnHeight; /* FRI return same value */
			//alert('computeFooterPosition.height: '+maxWidgetsColumnHeight);
		}
		else
		/*FRI: need to know the actual height: */
		{
			var widgetsBottomPos=0,currWidgetBottomPos;
			for (var i=0;i<tab[$p.app.tabs.sel].module.length;i++)
			{
				currWidgetBottomPos=tab[$p.app.tabs.sel].module[i].y+tab[$p.app.tabs.sel].module[i].height+widgetDecalY+40;
				if (currWidgetBottomPos>widgetsBottomPos)   widgetsBottomPos=currWidgetBottomPos;
			}
			l_footer.setStyle("position","absolute");
			l_footer.setStyle("top",widgetsBottomPos+"px");
			//alert('computeFooterPosition:' +widgetsBottomPos);
		}

		fri_setFacetBoardParameter(widgetsBottomPos);
	},
	/*
	         Function: save
                                $p.app.pages.save
                                
                                Save current page (for non connected users)
                    
                    Parameters:
                        
                                v_type (string) - type ofuser ('user' or 'admin')
	*/
	save:function(v_type)
	{
		if (v_type=="user") {	
			// save the page (modules + personalization)
			for (var cpt=0;cpt<tab.length;cpt++)
			{
				if (!tab[cpt].temporary)
				{
					//var l_ret="pageid="+tab[cpt].pageid+"w="+tab[cpt].colnb+"&s="+tab[cpt].style+"&n="+tab[cpt].label+"&p="+tab[cpt].param+"&t="+tab[cpt].showType+"&ty="+tab[cpt].type+"&nb="+tab[cpt].newspapernb+"&i="+tab[cpt].icon+"&a="+(tab[cpt].moduleAlign?"Y":"N");
					var l_ret="pageid="+tab[cpt].pageid+"w="+tab[cpt].colnb+"&s="+tab[cpt].style+"&n="+tab[cpt].label+"&p="+tab[cpt].param+"&t="+tab[cpt].showType+"&ty="+tab[cpt].type+"&nb="+tab[cpt].newspapernb+"&i="+tab[cpt].icon+"&a="+(tab[cpt].moduleAlign?"Y":"N");
					for (var i=0;i!=tab[cpt].module.length;i++)
					{
						l_ret+="&id"+i+"="+tab[cpt].module[i].id+"&pos"+i+"="+tab[cpt].module[i].newpos+"&col"+i+"="+tab[cpt].module[i].newcol+"&posj"+i+"="+tab[cpt].module[i].newposj+"&x"+i+"="+tab[cpt].module[i].newx+"&y"+i+"="+tab[cpt].module[i].newy+"&var"+i+"="+$p.string.esc(tab[cpt].module[i].vars)+"&blocked"+i+"="+(tab[cpt].module[i].blocked?"1":"0")+"&minimized"+i+"="+(tab[cpt].module[i].minimized?"1":"0");
					}	
			
					$p.ajax.call(posh["scr_createportal"],
						{
							'type':'execute',
							'variables':l_ret,
							'alarm':false,
							'forceExecution':true,
							'method':'post'				
						}
					);
				}
			}
            //openPage("0");
		}
	},
	/*
	         Function: maxModReached
                                $p.app.pages.maxModReached
                                
                                Limit modules number in a page
                                
                    Returns: 
                  
                                True or false
	*/
	maxModReached:function()
	{
		var l_ret=false;
		if (tab[$p.app.tabs.sel].module.length>__maxModNb)  {
			$p.app.alert.show(lg("msgMaxMod",__maxModNb));
			l_ret=true;
		}
		return l_ret;
	},
	/*
	         Function: defineWidgetContainer
                                $p.app.pages.defineWidgetContainer
                                
                                Define a widget container
                    
                    Parameters:
                        
                                v_tab - tab sequence ID
	*/
	defineWidgetContainer:function(v_tab)
	{
		if ($("modules"+tab[v_tab].id)==null)   {
			tab[v_tab].root=new Element('div', {'id': "modules"+tab[v_tab].id} );		
			if (v_tab!=$p.app.tabs.sel)    
            {    
                tab[v_tab].root.setStyle('display', 'none');  
            }
			($("modules")).appendChild(tab[v_tab].root);
		}
	},
	/*
	         Function: getPublicWidgets
                                $p.app.pages.getPublicWidgets
                                
                                get a user public widgets
                    
                    Parameters:
                        
                                v_userId - user ID
			v_destDiv - (string) destination div where result will be printed
	*/
	getPublicWidgets:function(v_userId,v_destDiv)
	{
		$p.ajax.call(posh["xml_userpublicwidgets"]+'?id='+v_userId,
			{
				'type':'load',
				'callback':
				{
					'function':$p.app.pages.displayPublicWidgets,
					'variables':
					{
						'destdiv':v_destDiv
					}
				}
			}
		);
	},
	displayPublicWidgets: function(response,vars)
	{
		var l_result = response.getElementsByTagName('widget');
		var l_s = '';
		var l_prevPortname;

		for (var i = 0;i < l_result.length;i++)
		{
			var l_id = $p.ajax.getVal(l_result[i],'id','int',false,0);
			var l_vars = $p.ajax.getVal(l_result[i],'variables','str',false,'');
			var l_widname = $p.string.getVar(l_vars,'ptitl');
			if (l_widname == '')
			{
				l_widname = $p.ajax.getVal(l_result[i],'widname','str',false,'???');
			}
			var l_portname = $p.ajax.getVal(l_result[i],'portname','str',false,'???');

			if (l_prevPortname != l_portname)
			{
				l_s += '<h2>'+l_portname+'</h2>';
				l_prevPortname = l_portname;
			}

			l_s += '<img src="../modules/pictures/box0_'+l_id+'" class="imgmid"> '
				+ '<a href="#" onclick=\'$p.app.widgets.open('+l_id+',"'+l_vars+'","uniq")\'>'
				+ l_widname
				+ '</a><br />';
		}
		if (l_result.length == 0) l_s += lg('noSharedWidget');

		$p.print(vars['destdiv'],l_s);
	},
	/*	
	         Function: stopSharing
                                $p.app.pages.stopSharing                                
                                stop sharing a page

	*/
	stopSharing:function(v_tabId)
	{
		var response=confirm(lg("msgUnshareConfirm"));
		if (response==1)
		{
			$p.ajax.call(posh["scr_unsharepage"],
				{
					'type':'execute',
					'variables':"id="+v_tabId,
					'callback':
					{
						'function':$p.app.pages.stopSharingConfirmation
					}
				}
			);
		}
	},
	stopSharingConfirmation:function()
	{
		tab[$p.app.tabs.sel].shared = 0;
		$p.app.tabs.refresh($p.app.tabs.sel);
	},
    /*	
	         Function: refreshWidgetsNotLoaded
                                $p.app.pages.refreshWidgetsNotLoaded                                
                                refresh all widget not loaded of a custom page

	*/
    refreshWidgetsNotLoaded:function(v_tab)
    {
        if (v_tab == indef)
            v_tab = $p.app.tabs.sel;

        for (var i = 0;i < tab[v_tab].module.length;i++)
        {
            if (tab[v_tab].module[i].format == 'R' && !tab[v_tab].module[i].isLoaded)
            {
                tab[v_tab].module[i].refresh();
            }
        }
    },
    /*
		Function: getModifiedPages
                                $p.app.pages.getModifiedPages
                                
                                get and return pages modified by admin
                      return :
                                hash containing modified page information
	*/
    getModifiedPages:function()
    {
        var l_pages = [];

        for (var i = 0;i < tab.length;i++)
        {
            if (tab[i].status == 1 || tab[i].status == 2)
            {
                l_pages.push({'id':tab[i].id,'title':tab[i].label,'status':tab[i].status});
            }
        }
        return l_pages;
    }
}



//************************************* PAGES COLUMNS FUNCTIONS***************************************************************************************************************
/*
    Class: $p.app.pages.columns
         Pages columns functions
*/
$p.app.pages.columns={
	/*
	         Function: createAll
                                $p.app.pages.columns.createAll
                                
                                Create personalized page columns
                    
                    Parameters:
                        
                                v_tab - tab sequence ID
	*/
	createAll:function(v_tab)
	{
		//initialise Cols array
		tab[v_tab].cols.length=0;
        var tab_id = tab[v_tab].id;
		//condition suppressed with 1.0.0 : better placement without and reused for version 1.1
		var l_table=new Element('table', 
			{
				'styles': 
				{
					'width': '100%',
					'height': '100%',
					'border': '0'
				},
                'id':'home'+tab_id,
				'cellSpacing': '4'
		    }
		);
        var l_canvas=new Element('div',
            {
                'styles':
                {
                    'width': '100%',
                    'display':'none'
                },
                'class':'mycanvas',
                'id':'canvas'+tab_id
            }
        );
		var l_tbody=new Element('tbody');
		var l_tr=new Element('tr',
                            {
                            'width': '100%',
                            'height': '100%'
                            }
                                    );
		l_tr.id="maintr"+v_tab;
		if (tab[v_tab].showType==0){
            for (var i=1;i<=tab[v_tab].colnb;i++){
                l_tr.appendChild($p.app.pages.columns.create(i,v_tab));
            }
        } 
        else {
            l_tr.appendChild($p.app.pages.columns.create(1,v_tab));
        }
		$p.app.pages.columns.size(v_tab);
		l_tbody.appendChild(l_tr);
		l_table.appendChild(l_tbody);
		(tab[v_tab].root).appendChild(l_table);
        (tab[v_tab].root).appendChild(l_canvas);  
	},
	/*
	         Function: init
                                $p.app.pages.columns.init
                                
                                Init widgets move in a column
                    
                    Parameters:
                        
                                v_tab - tab sequence ID
	*/
	init:function(v_tab)
	{
		// Initialize column & modules behaviors
		// do not initialize the columns if already done
		if(tab[v_tab].moveIsInit)return;
		tab[v_tab].moveIsInit=true;

		//check if widget move init is applied on a widget or on a page
		var l_cptStart=($('col0')!=!null && tab[v_tab].cols.length==1)?0:1;
		var l_cptEnd=($('col0')!=!null && tab[v_tab].cols.length==1)?0:1;

		for (var i=l_cptStart;i<tab[v_tab].cols.length;i++)
		{
			for(var l_item=0;l_item<tab[v_tab].cols[i].childNodes.length-1;l_item++)
			{
				var l_mod=tab[v_tab].cols[i].childNodes[l_item];
 				$p.app.widgets.move.set(l_mod,"module",tab[v_tab].cols,"multidim",true,v_tab);
			}
		}
	},
	/*
	         Function: create
                                $p.app.pages.columns.create
                                
                                Create a personalized page column

                    Parameters:
                        
			v_id - column ID
			v_tab - tab sequence ID
           
                    Returns:
                    
                                td element
	*/
	create:function(v_id,v_tab)
	{
		tab[v_tab].cols[v_id]=new Element('td', 
			{
				'styles':
				{
					'verticalAlign': 'top',
					'height':'100%'
				},
				'id': "col"+v_id
			}
		);
		return tab[v_tab].cols[v_id];
	},
	/*
	         Function: size
                                $p.app.pages.columns.size
                                
                                Size a column based on columns numbers
                    
                    Parameters:
                        
                                v_tab - tab sequence ID
	*/
	size:function(v_tab)
	{
		//if newspaper mode, the cols are not sized (=> if no module on col1, col1 width=0)
		if (tab[v_tab].showType==0)
		{
			for (var i=1;i<tab[v_tab].cols.length;i++)
			{
				tab[v_tab].cols[i].setStyle("width",((100/tab[v_tab].colnb).toInt())+"%");
			}
		}
	},
	/*
	         Function: add
                                $p.app.pages.columns.add
                                
                                Add a new column
                    
                    Parameters:
                        
                                v_id - column ID
	*/
	add:function(v_id)
	{
		// Add columns (until selected one)
		var l_obj=$("maintr"+$p.app.tabs.sel);
		// add the new cols
		if (tab[$p.app.tabs.sel].showType==0)
		{
			for (var i=tab[$p.app.tabs.sel].colnb+1;i<=v_id;i++)
			{
				l_obj.appendChild($p.app.pages.columns.create(i,$p.app.tabs.sel));
				tab[$p.app.tabs.sel].cols[i].appendChild($p.app.widgets.endList());
			}
			$p.app.pages.columns.size($p.app.tabs.sel);
		}
		tab[$p.app.tabs.sel].colnb=v_id;
	},
	/*
	         Function: sup
                                $p.app.pages.columns.sup
                                
                                Remove a column
                    
                    Parameters:
                        
                                v_id - column ID
	*/
	sup: function(v_id)
	{
		var l_obj = $("maintr"+$p.app.tabs.sel);
		if (tab[$p.app.tabs.sel].showType == 0)
		{
			for (var i = tab[$p.app.tabs.sel].colnb;i > v_id;i--)
			{
				//suppress the node
				l_obj.removeChild(tab[$p.app.tabs.sel].cols[i]);
				delete(tab[$p.app.tabs.sel].cols[i]);
				tab[$p.app.tabs.sel].cols.length -= 1;
			}
			$p.app.pages.columns.size($p.app.tabs.sel);
		}
		tab[$p.app.tabs.sel].colnb = v_id;
	},
	/*
	         Function: isEmpty
                                $p.app.pages.columns.isEmpty
                                
                                Check that a column is empty before removal
                    
                    Parameters:
                        
                                v_id - column ID
                                
                    Returns:
                    
                                true or false
	*/
	isEmpty:function(v_id)
	{
		var l_ret=true;
		if (v_id<tab[$p.app.tabs.sel].colnb)
		{
			for (var i=0;i<tab[$p.app.tabs.sel].module.length;i++)
			{
				if (tab[$p.app.tabs.sel].module[i].col>v_id)
                {
                    l_ret=false;
                    $p.app.alert.show(lg("msgColSup",tab[$p.app.tabs.sel].module[i].col));
                    break;
                }
			}
		}
		return l_ret;
	}
}

//************************************* WIDGETS FUNCTIONS ***************************************************************************************************************
/*
    Class: $p.app.widgets
         Widgets functions
*/
$p.app.widgets={
	showAdminProperties:false,
	dragging:false,
	/*
		Function: initMenu
                               $p.app.widgets.initMenu - init the widget menu
                               
                               Modules class
	*/
	initMenu:function()
	{
		
		$p.app.menu.options.push({
                                "id":"pwidget",
                                "label":lg("lblAddContent"),
                                "desc":lg("addContentTxt"),
                              /*  "icon":"ico_menu_add.gif", */
                                "seq":20,
								"action":"$p.app.menu.widget.build()",
                                "type":"",
                                "pages":[]
                                }
		);
		
	},
	/*
		Function: object 
                               $p.app.widgets.object *(Constructor)*
                               
                               Modules class
		
                      Parameters:
       
			col (int) - column of the module  
			pos (int) - position of the column in a column 
                            posj   (int) -  Position of widget in the page
			height (int) - height of the module (not application for modules with type R & M) 
			id (int) - id of the module (int)
			link (str) - link of the module provider  - data form database is filed site
			name (str) - name of the module - str
                            vars (str) - variables
			minModSize (int) - minimal size of the module 
			updModSize (boolean)  - is the module resizable? 
			size  (int)  - ? value form database is minmodsize
			url (str) - ?
			x (int) - left position (if not aligned) 
			y (int) - top position (if not aligned) 
			uniq_db (int) - uniq id of the module 
			format (str) - type of the module (R=rss, M=included module, I= framed module,U=Url)
			nbvars (int) - number of users configuration variables used
			tab  (int)  - id of the tab where the module is (int)
			blocked (boolean) - is the module blocked in the page
			minimized (boolean) - display status of the module (true=minimized, false=normal)
			usereader (boolean) - for RSS modules, is the rss reader is used to read articles
			autorefresh (boolean) - is the module refreshed every x minutes ?
			icon (str) - icon of the module
			isLoaded - loading status of the module (indef=not loaded, false=loading, true=loaded)
			header (str) - HTML header of the module
			footer (str) - HTML footer of the module
			auth (str) - for RSS authentified feeds
                        views (str) - views (home or canvas) canvas for full-screen (full-portal)
                        l10n - l10n parameters, lang comma separated
	*/
	object:function(col,pos,posj,height,
                        id,link,name,vars,minModSize,updModSize,size,
                        url,x,y,uniq_db,format,nbvars,
                        tab,blocked,minimize,usereader,
                        autorefresh,icon,isLoaded,
                        header,footer,auth,views,l10n,sharedmd5key)
	{
		this.col=col;
		this.pos=pos;
		this.posj=posj;
		this.height=height;
		this.size=size;
		this.id=id;
		this.link=link;
		this.name=	name; 
		this.vars=vars;
		this.minModSize=minModSize;
		this.updModSize=updModSize;
		this.url=url;
		this.y=y;
		this.x=x;
		this.uniq=uniq_db;
		this.format=format;
		this.newcol=col;
		this.newpos=pos;
		this.newposj=posj;
		this.newx=x;
		this.newy=y;
		this.nbvars=nbvars;
        this.paramNotFound=1;
		this.tab=(tab==indef)?0:tab;
		this.blocked=(blocked && blocked==1)?true:false;
		this.minimized=(minimize && minimize==1)?true:false;
		this.usereader=(usereader && usereader==1)?true:false;
		this.autorefresh=(autorefresh && autorefresh==1)?true:false;
		this.icon=(icon==indef || icon=="")	?"box0_"+this.id:icon;
		if( format=="R" && (icon==indef || icon=="") ) {
			this.icon = "box0_"+this.id+".ico";
			var myImage = new Image;
			myImage.src = this.icon;
			myImage.onerror=function() { this.icon = "box0_"+this.id}
		}
        this.sharedmd5key  = sharedmd5key;
		this.isLoaded=isLoaded;
		this.header=header;
		this.content='';
		this.footer=footer;
		this.nbunread=0;
		this.start=0;
		this.auth=auth;
        this.className="module";
        this.views = {};
        if (!this.currentView) {
            this.currentView='home';
        }
        this.existsView=$p.app.widgets.existsView;
        $p.app.widgets.setViews(this,views);
        this.l10n=l10n;   
		this.create=$p.app.widgets.create;
		this.fri_create=$p.app.widgets.fri_create;
		this.destruct=$p.app.widgets.destruct;
		this.show=$p.app.widgets.show;
		this.hide=$p.app.widgets.hide;
		this.refresh=$p.app.widgets.refresh;
		this.changeVar=$p.app.widgets.changeVar;
		this.placeinCol=$p.app.widgets.placeinCol;
		this.placeonTop=$p.app.widgets.placeonTop;
		this.placeonBottom=$p.app.widgets.placeonBottom;
		this.placeAfter=$p.app.widgets.placeAfter;
		this.placeBefore=$p.app.widgets.placeBefore;
		this.bringToFront=$p.app.widgets.bringToFront;
		this.setHeight=$p.app.widgets.setHeight;
		this.hdrColor=$p.app.widgets.hdrColor;
		this.getIcon=$p.app.widgets.getIcon;
		this.minimize=$p.app.widgets.reduce;
		this.setName=$p.app.widgets.setName;
		this.setIcon=$p.app.widgets.setIcon;
	},
    /*
                function: $p.app.widgets.setViews
                
                        set views (home or canvas)
            */
    setViews: function (newObject,views) {
            if (!views || views=='') {   return; }
            var reg=new RegExp(",","g");
            var listViews=views.split(reg);
            if (listViews && listViews[0]) {
                for (var i = 0; i < listViews.length;i++) {
                    newObject.views[listViews[i]]=true;
                }
            } else {
                newObject.views[views]=true;
            }  
            
    },
    /*
                    function: $p.app.widgets.existsView
                    
                    return true is view exists for this widget
                    
                    use as method of a widget
            */
    existsView: function (view) {
        if (this.views[view]) {return this.views[view];}
        return false;
    },
	/*
		Function:$p.app.widgets.create
                                Create a module object
	*/
	create:function( wHeight )
	{
		if( wHeight!=indef )
			widgetHeight = wHeight;
		$p.plugin.hook.launch('app.widgets.create.start');

		var l_tabPos=$p.app.tabs.idToPos(this.tab);
		var l_obj=null;
		//for a new widget without uniq ID
		if (this.uniq==0)   {
			tab[l_tabPos].maxUniq++;
			this.uniq=tab[l_tabPos].maxUniq;
			$p.app.widgets.changeUniq($p.app.widgets.uniqToId(this.uniq,l_tabPos),this.uniq);
		}
		l_obj=document.createElement("div");
		l_obj.style.display="none";
		//place new modules
		//use tab.id because it do not change even if the tab is moved
        
        if (this.currentView == 'canvas') {
            l_obj.id="canvas"+this.tab+"_"+this.uniq;
            l_obj.className="canvas";  
            this.className="canvas";
        } else {
            l_obj.id="module"+this.tab+"_"+this.uniq;
            l_obj.className="module";
            this.className="module";
        }

		if (tab[l_tabPos].moduleAlign)  {
			l_obj.style.position="relative";
		}
		else    {
			l_obj.style.position="absolute";
			l_obj.style.width=this.minModSize+"px";
			l_obj.style.left=this.x+widgetDecalX+"px";
			l_obj.style.top=this.y+widgetDecalY+"px";
		}
        
		//attach DIV to the corresponding column. if new module this.col=0 => col[0] need to be created
		if (
            (tab[l_tabPos].moduleAlign 
            || l_tabPos==-1)
            && this.currentView != 'canvas'
            )    {
			if (this.col!=-1)   {
				if (tab[l_tabPos].showType==0 
                    || this.col==0) {
                        tab[l_tabPos].cols[this.col].appendChild(l_obj);
                } 
                else {  tab[l_tabPos].cols[1].appendChild(l_obj);
                }
			}
			else    {
				($p.get("col1")).appendChild(l_obj);
			}
		} else if (this.currentView == 'canvas') {
            this.canvas.appendChild(l_obj);

        }
		else    {
			(tab[l_tabPos].root).appendChild(l_obj);
		}

		var tableObj1 = new Element('table',
			{
				'id': 'bmod'+this.tab+'_'+this.uniq,
				'class': 'bmod',
				'cellspacing': '0',
				'cellpadding': '0'
			}
		);		   
							   
		var tbodyObj1 = new Element('tbody');
		var trObj1 = new Element('tr');
		var tdObj1 = new Element('td',
			{
				'events':
				{
                    'mouseover': function()
					{
						$p.app.widgets.showOptions(this.uniq,true);			
					},
					'mouseout': function()
					{
                        $p.app.widgets.showOptions(this.uniq,false);																
					}
				},
				'class': 'headmod'
			} 
		);
		tdObj1.uniq=this.uniq;
															
		var tableObj2 = new Element('table',
			{
				'id': 'hmod'+this.tab+'_'+this.uniq,
				'class': 'hmod',
				'cellspacing': '0',
				'cellpadding': '0',
				'width': '100%'
			}
		);
		var tbodyObj2 = new Element('tbody');
		var trObj2 = new Element('tr');
		
		var tdObj2 = new Element('td', 
			{
				'class': 'lefttopcornermod'
			}
		);
		tdObj2.set('html','<img src="../images/s.gif" width="1" height="1" />');
		tdObj2.inject(trObj2);
								
		var tdObj3 = new Element('td', 
			{
				'width': '16px'
			}
		);						
		if (__showicon
               &&  this.currentView != 'canvas') 
		{
			var aObj1 = new Element('a',
				{
					'events':((this.blocked || !__showModuleMinimize)?'':
					{
						'click': function()
						{
							$p.app.widgets.minimize(this.uniq);
							return false;														
						}
																	
					}),
					'href': '#'		
				} 
			);
			aObj1.icon=this.icon;
			var _src = this.icon;
			if( this.icon.substr(0,2)!=".." ) {
				_src = _dirImg+this.icon;
			}
			aObj1.uniq = this.uniq;
			var iObj1=new Element('img',
				{
					'events':((this.blocked || !__showModuleMinimize)?'':
					{
						'mouseover': function()
						{
							if (tab[$p.app.tabs.sel].module[$p.app.widgets.uniqToId(this.uniq)].minimized) 
                            {
                                this.src="../images/maximize.gif";
                            }
							else
                            {
                                this.src="../images/minimize.gif";
                            }
						},
						'click': function()
						{
							if(this.src==__LOCALFOLDER+"images/minimize.gif") {
								this.src="../images/maximize.gif";
								
							} else {
								this.src="../images/minimize.gif";
							}
						},
						'mouseout': function()
						{
							$p.app.widgets.changeIcon($p.app.widgets.uniqToId(this.uniq));	
						}
					}),
					'id': 'module'+this.tab+'_'+this.uniq+'_icon',
					'src':_src,
					'width':'16',
					'height':'16',
					'class':'imgmid'
				}
			);
			iObj1.icon=this.icon;
			iObj1.uniq = this.uniq;
			iObj1.inject(aObj1);												
			aObj1.inject(tdObj3);						
		}
        
		tdObj3.inject(trObj2);
		
		var tdObj4 = new Element('td', 
			{
				 'width': '100%'
		    }
		);
								
		//alert('FRI fri_getpicturedef for '+'module'+this.tab+'_'+this.uniq+'_h, d.h. name='+this.name +' url='+this.url);
		//FRI: Load pic from Widget
		
		var divObj1 = new Element('div', // FRI tab ICON here
			{
				'id': 'module'+this.tab+'_'+this.uniq+'_h',
				'class': 'titmod'
			}
		 );	
			 
		if(__showModuleTitle)	
        {  
			   divObj1.set('html',this.name);   
		}
		else    
        {   
            divObj1.set('html',"");     
        }
		
		divObj1.inject(tdObj4);
		tdObj4.inject(trObj2);
										
		var tdObj5 = new Element('td', 
			{
				'class': 'optmodhide',
				'id': 'module'+this.tab+'_'+this.uniq+'_o'
			}
		);					
		var divObj2 = new Element('div',
			{
				'id': 'module'+this.tab+'_'+this.uniq+'_o',
				'class': 'optmoda',
				'nowrap': 'nowrap'
			}
		 );	
		
		//display configure button if allowed or useful
		if ((__showModuleConfigure 
			&& (this.nbvars>0 || __widgetTitleUpdatable))
            || $p.app.user.id==-1)     {
    			var aObj2 = new Element('a',
    				{
    					'events':
    					{
    						'click': function()
    						{
    							return $p.app.widgets.param.show(this.uniq,this.tab);													
    						}
    					},
    					'href': '#'
    				}
    			);			
    			aObj2.uniq = this.uniq;
    			aObj2.set('html',lg("lblConfigure"));
    			aObj2.inject(divObj2);
    			var bObj2 = new Element('b');
    			bObj2.set('html','&nbsp;');
    			bObj2.inject(divObj2);
		}
												
		if ($p.app.user.id!=0
            &&__useSharing)     {
    			var aObj3 = new Element('a',
    				{
    					'events': {
    									'click': function()
    									{
    										$p.friends.menu(3,$p.app.widgets.uniqToId(this.uniq));
                                            return false;
    									}
    							  },
    					'href': '#',
    					'title':lg('lblShareModule')
    				} 
    			);
    			aObj3.uniq = this.uniq;
                aObj3.set('html','<img class="optmod_share" src="../images/s.gif" width="13" height="11" alt="'+lg("lblShareModule")+'" />');
    			//aObj3.set('html',$p.img("ico_bar_share.gif",13,11,lg("lblShareModule")));
    			aObj3.inject(divObj2);
    			var bObj3 = new Element('b');
    			bObj3.set('html','&nbsp;');
    			bObj3.inject(divObj2);
		}
																	
		if(__showModuleRefresh)
		{
			var aObj4 = new Element('a',
				{
					'events':
					{
						'click': function()
						{
							return tab[$p.app.tabs.sel].module[$p.app.widgets.uniqToId(this.uniq)].refresh(0);		
						}
					},
					'href': '#',
					'title':lg('lblRefresh')
				} 
			);
			aObj4.uniq = this.uniq;
			 
            //aObj4.set('html',$p.img("ico_refresh.gif",12,11,lg("lblRefresh")));
			aObj4.set('html','<img class="optmod_refresh" src="../images/s.gif" width="12" height="11" alt="'+lg("lblRefresh")+'" />');
			aObj4.inject(divObj2);
			var bObj4 = new Element('b');
			bObj4.set('html','&nbsp;');
			bObj4.inject(divObj2);
		}
        //minimize button
		if (    
                (
                    !this.blocked   
                    &&  __showModuleMinimize
                )
                && this.currentView != 'canvas'    
            )
		{	
			var aObj5 = new Element('a',
				{
					'events': {
									'click': function()
									{
										$p.app.widgets.minimize(this.uniq);
										return false;							
									}
							  },
					'href': '#',
					'id': 'module'+this.tab+'_'+this.uniq+'_icon_a',
					'title': tab[$p.app.tabs.sel].module[$p.app.widgets.uniqToId(this.uniq)].minimized ? lg('titleWidgetRestore') : lg("minimize")
				} 
			);
			aObj5.uniq = this.uniq;
			//aObj5.set('html',$p.img((this.minimized?"ico_maximize.gif":"ico_minimize.gif"),12,11,"minimize","","imgminimize"+this.uniq));
			aObj5.set('html','<img id="imgminimize'+this.tab+'_'+this.uniq+'" class="'+(this.minimized ? 'optmod_maximize' : 'optmod_minimize')+'" src="../images/s.gif" width="12" height="11" alt="'+lg("minimize")+'" />');
			aObj5.inject(divObj2);
			var bObj5 = new Element('b');
			bObj5.set('html','&nbsp;');
			bObj5.inject(divObj2);
		}
        //display button full-screen if view canvas exists
        if (
                /* FRI commented out: this.existsView('canvas') && */
				// this.currentView != 'canvas'
                //&& tab[$p.app.tabs.sel].moduleAlign
            	true // FRI: always
			) 
        {
            var this_id = this.id;   
			var aObj6 = new Element('a',
				{
					'events': {
									'click': function()
									{
										//openWidgetInNewTab(widget_instance,widget_id,tab_id);
										$p.app.widgets.openWidgetInNewTab(this.uniq,this_id,tab[$p.app.tabs.sel].id);
										/*$p.app.widgets.maximize(this.uniq,this_id);*/
										return false;					
									}
							  },
					'href': '#',
                    'title': lg('titleWidgetOpenInTab')
				} 
			);
			aObj6.uniq = this.uniq;
			//aObj6.set('html',$p.img("ico_view_fullpage.gif",12,11,lg("lblSuppress")));
			aObj6.set('html','<img class="optmod_viewfullpage" src="../images/s.gif" width="12" height="11" alt="'+lg("maximize")+'" />');
			aObj6.inject(divObj2);	
			var bObj6 = new Element('b');
			bObj6.set('html','&nbsp;');
			bObj6.inject(divObj2);		
        }        
		//suppress button					
		if (    (
                    !this.blocked 
                    && (__showModuleClose || $p.app.user.id==-1)
                )
                && this.currentView != 'canvas'
           ) 
		{
			var aObj6 = new Element('a',
				{
					'events': {
									'click': function()
									{
										$p.app.widgets.suppress(this.uniq,true);
										return false;						
									}
							  },
					'href': '#',
					'title':lg('lblClose')
				} 
			);
			aObj6.uniq = this.uniq;
			//aObj6.set('html',$p.img("ico_close.gif",12,11,lg("lblSuppress")));
			aObj6.set('html','<img class="optmod_close" src="../images/s.gif" width="12" height="11" alt="'+lg("lblSuppress")+'" />');
			aObj6.inject(divObj2);	
			var bObj6 = new Element('b');
			bObj6.set('html','&nbsp;');
            
                bObj6.inject(divObj2);
		}

        if (this.currentView == 'canvas' ) {
            var this_id = this.ModulePositionId;         
			var aObj6 = new Element('a',
				{
					'events': {
									'click': function()
									{
										$p.app.widgets.homeView(this_id);
										return false;						
									}
							  },
					'href': '#',
                    'alt':'reduce',
					'title':'reduce'
				} 
			);
			aObj6.uniq = this.uniq;
			//aObj6.set('html',$p.img("ico_view_widget.gif",12,11,'reduce widget'));
			aObj6.set('html','<img class="optmod_viewwidget" src="../images/s.gif" width="12" height="11" alt="'+lg("reduce")+'" />');
			aObj6.inject(divObj2);	
			var bObj6 = new Element('b');
			bObj6.set('html','&nbsp;');
			bObj6.inject(divObj2);				
        }
        
        
		var bObj61 = new Element('b');
		bObj61.set('html','&nbsp;');
		bObj61.inject(divObj2);
					
		divObj2.inject(tdObj5);
		tdObj5.inject(trObj2);
		var tdObj6 = new Element('td', 
			{
				'class': 'righttopcornermod'
			}
		);
		tdObj6.set('html','<img src="../images/s.gif" width="1" height="1" />');
		tdObj6.inject(trObj2);
		trObj2.inject(tbodyObj2);
		tbodyObj2.inject(tableObj2);
		tableObj2.inject(tdObj1);
		var divObj3 = new Element('div',
			{
				'id': 'editbar'+this.tab+'_'+this.uniq,
				'class': 'editbox'
			}
		 );
		divObj3.inject(tdObj1);	
		tdObj1.inject(trObj1);
		trObj1.inject(tbodyObj1);
			
		var trObj3 = new Element('tr');
		var tdObj7 = new Element('td',
			{
				'class': 'contentborder'
			}
		);	
		var divObj4 = new Element('div',
			{
				'id': 'module'+this.tab+'_'+this.uniq+'_i',
				'class': 'content',
				'styles':
                        {
							'width': '100%',
							'height': '100%'
                        }
			}
		);				 
		var tableObj3 = new Element('table', 
			{
				'border': '0',
				'cellspacing': '0',
				'cellpadding': '0',
				'align': 'center',
				'width': '100%',
				'height': '100%'
			}
	    );
												   
		var tbodyObj3 = new Element('tbody');
		var trObj4 = new Element('tr');
		var tdObj8 = new Element('td',
			{
				'id': 'module'+this.tab+'_'+this.uniq+'_c',
				'styles':{
					'display' :(this.minimized?'none':'')
				}
			} 
		);

		if (this.format=="I") 
		{
            var FrameHeight = this.height+"px";
            var scrollMode = 'no';
            
            var iframeObj1 = new Element('iframe',
				{
					'id': 'modfram'+this.tab+'_'+this.uniq,
					'name': 'modfram'+this.tab+'_'+this.uniq,
					'src': this.url+"pid="+$p.app.user.id
                                    +"&prof="+this.tab
                                    +"&p="+this.uniq
                                    +"&view="+this.currentView
                                    +"&format=I"
                                    +"&l10n="+this.l10n
                                    +"&"+this.vars.replace('#','%23')
                                    +"&sharedmd5key="+this.sharedmd5key
                                    +"&skin="+RODINSKIN,
                                    
					'scrolling':scrollMode,
					'frameborder':'0',
					'marginwidth':'0',
					'marginheight':'0',
					'height':FrameHeight,
					'width':'100%',
					'z-index':'auto'
				} 
            );	 
        	fri_register_tab_iframe(this.tab,iframeObj1);
			
			//iframeObj1.set('html','Issue with module display.');
			var aObj7 = new Element('a',
				{
					'href': posh["contact"],
					'target': '_blank'
				} 
			);				
			aObj7.set('html','Contact us');	
			//aObj7.inject(iframeObj1);
			iframeObj1.inject(tdObj8);
			var brObj1 = new Element('br');	
			brObj1.inject(tdObj8);	
			
		}
		if (this.format=="U") 
		{
            var remoteUrl = this.url;
            remoteUrl = $p.string.trim(remoteUrl);
            var reg = /^http/;
            if ( remoteUrl.match(reg) ) {
                var regdomain = /__LOCALFOLDER/;
                if ( remoteUrl.match(regdomain) ) {
                    remoteUrl = remoteUrl 
                        +"pid="+$p.app.user.id
                        +"&prof="+this.tab
                        +"&view="+this.currentView
                        +"&format=U"
                        +"&p="+this.uniq
                        +"&plg="+__lang+"&"+this.vars.replace('#','%23');
                } else {
                    var regPoint = /\?$/;
                    remoteUrl = remoteUrl.replace(regPoint,'');
                }
            } else {
                remoteUrl = remoteUrl 
                        +"pid="+$p.app.user.id
                        +"&prof="+this.tab
                        +"&view="+this.currentView
                        +"&format=U"
                        +"&p="+this.uniq
                        +"&plg="+__lang+"&"+this.vars.replace('#','%23');
            }
            var scrollMode = 'no';

            if (this.currentView=='canvas')  {
                scrollMode="auto";
            }
			var iframeObj2 = new Element('iframe',
				{
					'styles': {
						'height': this.height+"px",
						'width': '100%'
					},
					'id': 'modfram'+this.tab+'_'+this.uniq,
					'name': 'modfram'+this.tab+'_'+this.uniq,
					'src': remoteUrl,
					'frameborder':'no', 
					'marginwidth':'0', 
					'marginheight':'0',
					'scrolling':scrollMode,
					'z-index':'auto'
				  } 
			);
			iframeObj2.inject(tdObj8);
			var brObj2 = new Element('br');	
			brObj2.inject(tdObj8);										
		}
		
		if (this.format=="D") //HTML integrated in Div 
		{
			tdObj8.set('html',this.content);
		}
		
		if (this.format=="H") 
		{
			var iframeObj3 = new Element('iframe',
				{
					'styles': {
									'height': this.height+"px",
									'width': '100%'
							  },
					'id': 'modfram'+this.tab+'_'+this.uniq,
					'name': 'modfram'+this.tab+'_'+this.uniq,
					'src': '',
					'frameborder':'no', 
					'marginwidth':'0', 
					'marginheight':'0', 
					'scrolling':'auto',
					'z-index':'auto'
				} 
			);		
			//iframeObj3.set('html','Issue with module display. ');
			var aObj9 = new Element('a',
				{
					'href': posh["contact"],
					'target': '_blank'
				} 
			);
			aObj9.set('html','Contact us');
			aObj9.inject(iframeObj3);
			iframeObj3.inject(tdObj8);
			var brObj3 = new Element('br');	
			brObj3.inject(tdObj8);										
		}

		if (this.format=="R" || this.format=="M") {
			tdObj8.set('html',lg("lblLoading"));
		}
					
		tdObj8.inject(trObj4);
		trObj4.inject(tbodyObj3);
		tbodyObj3.inject(tableObj3);
		tableObj3.inject(divObj4);

		//Div+image used to force minimal width
		var divObj5 = new Element('div',
			{
				'styles':
				{
					'height': '1px'
				}
			} 
		);			 
		divObj5.set('html',$p.img("s.gif",this.minModSize,1,indef,indef,"line"+this.tab+"_"+this.uniq));
		divObj5.inject(divObj4);
		
		divObj4.inject(tdObj7);
				
		if (__useNotation&&$p.app.user.id>0) 
		p_notation.buildBlock(this.uniq);

		tdObj7.inject(trObj3);	
		trObj3.inject(tbodyObj1);

		//Widget footer
		var footer_trContainer=new Element('tr');
		footer_trContainer.inject(tbodyObj1);
		var footer_tdContainer=new Element('td');
		footer_tdContainer.inject(footer_trContainer);
		var footer_table = new Element('table',
			{
				'id': 'fmod'+this.tab+'_'+this.uniq,
				'class': 'fmod',
				'cellspacing': '0',
				'cellpadding': '0',
				'width': '100%'
			}
		);
		footer_table.inject(footer_tdContainer);
		var footer_tbody = new Element('tbody');
		footer_tbody.inject(footer_table);
		var footer_tr = new Element('tr');
		footer_tr.inject(footer_tbody);
		var footer_leftcorner = new Element('td', 
			{
				'class': 'leftbottomcornermod'
			}
		);
		footer_leftcorner.set('html','<img src="../images/s.gif" style="width: 1px;height: 1px;line-height: 1px;" />');
		footer_leftcorner.inject(footer_tr);
		var footer_center = new Element('td', 
			{
				'class': 'bottommod'
			}
		);
		footer_center.set('html','<img src="../images/s.gif" style="width: 1px;height: 1px;line-height: 1px;" />');
		footer_center.inject(footer_tr);
		var footer_rightcorner = new Element('td', 
			{
				'class': 'rightbottomcornermod'
			}
		);
		footer_rightcorner.set('html','<img src="../images/s.gif" style="width: 1px;height: 1px;line-height: 1px;" />');
		footer_rightcorner.inject(footer_tr);


		tbodyObj1.inject(tableObj1);
		tableObj1.inject(l_obj);

		//check if authentified feed or not
		var l_vars=this.vars;
		this.auth=(l_vars.indexOf("auth=")!=-1 || (l_vars.indexOf("user=")!=-1 && l_vars.indexOf("pass=")!=-1))?true:false;
		//load the content
		if (this.format=="R") 
        {
            var r_id = $p.app.widgets.uniqToId(this.uniq,l_tabPos);
            $p.app.widgets.rss.refresh(r_id,l_tabPos);
        }    
		if (this.format=="M") 
        {
            $p.app.widgets.refreshContent($p.app.widgets.uniqToId(this.uniq,l_tabPos),l_tabPos,this.sharedmd5key);
        }    
		//if (this.format=="H") refreshHtmlCont($p.app.widgets.uniqToId(this.uniq));

		if (this.format=="I" || this.format=="U")   {
			if (this.nbvars>0) {
               $p.app.widgets.param.getModuleParam($p.app.widgets.uniqToId(this.uniq,l_tabPos),l_tabPos);
            }
			else {
				$p.app.widgets.param.fillEditBox(indef,this.uniq,l_tabPos);
			}
		}
		this.hdrColor();
		this.getIcon();
		$p.plugin.hook.launch('app.widgets.create.end');
	},
	/*
		Function:$p.app.widgets.fri_create
                                Create a module object
	*/
	fri_create:function( wHeight )
	{
		if( wHeight!=indef )
			widgetHeight = wHeight;
		$p.plugin.hook.launch('app.widgets.create.start');

		var l_tabPos=$p.app.tabs.idToPos(this.tab);
		var l_obj=null;
		//for a new widget without uniq ID
		if (this.uniq==0)   {
			tab[l_tabPos].maxUniq++;
			this.uniq=tab[l_tabPos].maxUniq;
			$p.app.widgets.changeUniq($p.app.widgets.uniqToId(this.uniq,l_tabPos),this.uniq);
		}
		l_obj=document.createElement("div");
		l_obj.style.display="none";
		//place new modules
		//use tab.id because it do not change even if the tab is moved
        
        if (this.currentView == 'canvas') {
            l_obj.id="canvas"+this.tab+"_"+this.uniq;
            l_obj.className="canvas";  
            this.className="canvas";
        } else {
            l_obj.id="module"+this.tab+"_"+this.uniq;
            l_obj.className="module";
            this.className="module";
        }

		if (tab[l_tabPos].moduleAlign)  {
			l_obj.style.position="relative";
		}
		else    {
			l_obj.style.position="absolute";
			l_obj.style.width=this.minModSize+"px";
			l_obj.style.left=this.x+widgetDecalX+"px";
			l_obj.style.top=this.y+widgetDecalY+"px";
		}
        
		//attach DIV to the corresponding column. if new module this.col=0 => col[0] need to be created
		if (
            (tab[l_tabPos].moduleAlign 
            || l_tabPos==-1)
            && this.currentView != 'canvas'
            )    {
			if (this.col!=-1)   {
				if (tab[l_tabPos].showType==0 
                    || this.col==0) 
				
								{
									if (this.col == -100) //refinement add
									{
										($p.get("col1")).appendChild(l_obj);		
									}
									else
                    tab[l_tabPos].cols[this.col].appendChild(l_obj);
                } 
                else {  tab[l_tabPos].cols[1].appendChild(l_obj);
                }
			}
			else    {
				($p.get("col1")).appendChild(l_obj);
			}
		} else if (this.currentView == 'canvas') {
            this.canvas.appendChild(l_obj);

        }
		else    {
			(tab[l_tabPos].root).appendChild(l_obj);
		}

		var tableObj1 = new Element('table',
			{
				'id': 'bmod'+this.tab+'_'+this.uniq,
				'class': 'bmod',
				'cellspacing': '0',
				'cellpadding': '0'
			}
		);		   
							   
		var tbodyObj1 = new Element('tbody');
		var trObj1 = new Element('tr');
		var tdObj1 = new Element('td',
			{
				'events':
				{
                    'mouseover': function()
					{
						$p.app.widgets.showOptions(this.uniq,true);			
					},
					'mouseout': function()
					{
                        $p.app.widgets.showOptions(this.uniq,false);																
					}
				},
				'class': 'headmod'
			} 
		);
		tdObj1.uniq=this.uniq;
															
		var tableObj2 = new Element('table',
			{
				'id': 'hmod'+this.tab+'_'+this.uniq,
				'class': 'hmod',
				'cellspacing': '0',
				'cellpadding': '0',
				'width': '100%'
			}
		);
		var tbodyObj2 = new Element('tbody');
		var trObj2 = new Element('tr');
		
		var tdObj2 = new Element('td', 
			{
				'class': 'lefttopcornermod'
			}
		);
		tdObj2.set('html','<img src="../images/s.gif" width="1" height="1" />');
		tdObj2.inject(trObj2);
								
		var tdObj3 = new Element('td', 
			{
				'width': '16px'
			}
		);						
		if (__showicon
               &&  this.currentView != 'canvas') 
		{
			var aObj1 = new Element('a',
				{
					'events':((this.blocked || !__showModuleMinimize)?'':
					{
						'click': function()
						{
							$p.app.widgets.minimize(this.uniq);
							return false;														
						}
																	
					}),
					'href': '#'		
				} 
			);
			aObj1.icon=this.icon;
			var _src = this.icon;
			if( this.icon.substr(0,2)!=".." ) {
				_src = _dirImg+this.icon;
			}
			aObj1.uniq = this.uniq;
			var iObj1=new Element('img',
				{
					'events':((this.blocked || !__showModuleMinimize)?'':
					{
						'mouseover': function()
						{
							if (tab[$p.app.tabs.sel].module[$p.app.widgets.uniqToId(this.uniq)].minimized) 
                            {
                                this.src="../images/maximize.gif";
                            }
							else
                            {
                                this.src="../images/minimize.gif";
                            }
						},
						'click': function()
						{
							if(this.src==__LOCALFOLDER+"images/minimize.gif"){this.src="../images/maximize.gif";}
							else{this.src="../images/minimize.gif";}
						},
						'mouseout': function()
						{
							$p.app.widgets.changeIcon($p.app.widgets.uniqToId(this.uniq));	
						}
					}),
					'id': 'module'+this.tab+'_'+this.uniq+'_icon',
					'src':_src,
					'width':'16',
					'height':'16',
					'class':'imgmid'
				}
			);
			iObj1.icon=this.icon;
			iObj1.uniq = this.uniq;
			iObj1.inject(aObj1);												
			aObj1.inject(tdObj3);						
		}
        
		tdObj3.inject(trObj2);
		
		var tdObj4 = new Element('td', 
			{
				 'width': '100%'
		    }
		);
								
		var divObj1 = new Element('div', // FRI tab ICON here
			{
				'id': 'module'+this.tab+'_'+this.uniq+'_h',
				'class': 'titmod'
			}
		 );	
			 
		if(__showModuleTitle)	
        {  
			   divObj1.set('html',this.name);   
		}
		else    
        {   
            divObj1.set('html',"");     
        }
		
		divObj1.inject(tdObj4);
		tdObj4.inject(trObj2);
										
		var tdObj5 = new Element('td', 
			{
				'class': 'optmodhide',
				'id': 'module'+this.tab+'_'+this.uniq+'_o'
			}
		);					
		var divObj2 = new Element('div',
			{
				'id': 'module'+this.tab+'_'+this.uniq+'_o',
				'class': 'optmoda',
				'nowrap': 'nowrap'
			}
		 );	
		
		//display configure button if allowed or useful
		if ((__showModuleConfigure 
			&& (this.nbvars>0 || __widgetTitleUpdatable))
            || $p.app.user.id==-1)     {
    			var aObj2 = new Element('a',
    				{
    					'events':
    					{
    						'click': function()
    						{
    							return $p.app.widgets.param.show(this.uniq,this.tab);													
    						}
    					},
    					'href': '#'
    				}
    			);			
    			aObj2.uniq = this.uniq;
    			aObj2.set('html',lg("lblConfigure"));
    			aObj2.inject(divObj2);
    			var bObj2 = new Element('b');
    			bObj2.set('html','&nbsp;');
    			bObj2.inject(divObj2);
		}
												
		if ($p.app.user.id!=0
            &&__useSharing)     {
    			var aObj3 = new Element('a',
    				{
    					'events': {
    									'click': function()
    									{
    										$p.friends.menu(3,$p.app.widgets.uniqToId(this.uniq));
                                            return false;
    									}
    							  },
    					'href': '#',
    					'title':lg('lblShareModule')
    				} 
    			);
    			aObj3.uniq = this.uniq;
                aObj3.set('html','<img class="optmod_share" src="../images/s.gif" width="13" height="11" alt="'+lg("lblShareModule")+'" />');
    			//aObj3.set('html',$p.img("ico_bar_share.gif",13,11,lg("lblShareModule")));
    			aObj3.inject(divObj2);
    			var bObj3 = new Element('b');
    			bObj3.set('html','&nbsp;');
    			bObj3.inject(divObj2);
		}
																	
		if(__showModuleRefresh)
		{
			var aObj4 = new Element('a',
				{
					'events':
					{
						'click': function()
						{
							return tab[$p.app.tabs.sel].module[$p.app.widgets.uniqToId(this.uniq)].refresh(0);		
						}
					},
					'href': '#',
					'title':lg('lblRefresh')
				} 
			);
			aObj4.uniq = this.uniq;
			 
            //aObj4.set('html',$p.img("ico_refresh.gif",12,11,lg("lblRefresh")));
			aObj4.set('html','<img class="optmod_refresh" src="../images/s.gif" width="12" height="11" alt="'+lg("lblRefresh")+'" />');
			aObj4.inject(divObj2);
			var bObj4 = new Element('b');
			bObj4.set('html','&nbsp;');
			bObj4.inject(divObj2);
		}
        //minimize button
		if (    
                (
                    !this.blocked   
                    &&  __showModuleMinimize
                )
                && this.currentView != 'canvas'    
            )
		{			
			var aObj5 = new Element('a',
				{
					'events': {
									'click': function()
									{
										$p.app.widgets.minimize(this.uniq);
										return false;							
									}
							  },
					'href': '#',
					'title':lg('minimize')
				} 
			);
			aObj5.uniq = this.uniq;
			//aObj5.set('html',$p.img((this.minimized?"ico_maximize.gif":"ico_minimize.gif"),12,11,"minimize","","imgminimize"+this.uniq));
			aObj5.set('html','<img id="imgminimize'+this.tab+'_'+this.uniq+'" class="'+(this.minimized ? 'optmod_maximize' : 'optmod_minimize')+'" src="../images/s.gif" width="12" height="11" alt="'+lg("minimize")+'" />');
			aObj5.inject(divObj2);
			var bObj5 = new Element('b');
			bObj5.set('html','&nbsp;');
			bObj5.inject(divObj2);
		}
        //display button full-screen if view canvas exists
        if (
                /* FRI commented out: this.existsView('canvas') && */
				// this.currentView != 'canvas'
                //&& tab[$p.app.tabs.sel].moduleAlign
            	true // FRI: always
			) 
        {
            var this_id = this.id;   
			var aObj6 = new Element('a',
				{
					'events': {
									'click': function()
									{
										//openWidgetInNewTab(widget_instance,widget_id,tab_id);
										$p.app.widgets.openWidgetInNewTab(this.uniq,this_id,tab[$p.app.tabs.sel].id);
										/*$p.app.widgets.maximize(this.uniq,this_id);*/
										return false;					
									}
							  },
					'href': '#',
					'title': lg('titleWidgetOpenInTab')
				} 
			);
			aObj6.uniq = this.uniq;
			//aObj6.set('html',$p.img("ico_view_fullpage.gif",12,11,lg("lblSuppress")));
			aObj6.set('html','<img class="optmod_viewfullpage" src="../images/s.gif" width="12" height="11" alt="'+lg("maximize")+'" />');
			aObj6.inject(divObj2);	
			var bObj6 = new Element('b');
			bObj6.set('html','&nbsp;');
			bObj6.inject(divObj2);		
        }        
		//suppress button					
		if (    (
                    !this.blocked 
                    && (__showModuleClose || $p.app.user.id==-1)
                )
                && this.currentView != 'canvas'
           ) 
		{
			var aObj6 = new Element('a',
				{
					'events': {
									'click': function()
									{
										$p.app.widgets.suppress(this.uniq,true);
										return false;						
									}
							  },
					'href': '#',
					'title':lg('lblClose')
				} 
			);
			aObj6.uniq = this.uniq;
			//aObj6.set('html',$p.img("ico_close.gif",12,11,lg("lblSuppress")));
			aObj6.set('html','<img class="optmod_close" src="../images/s.gif" width="12" height="11" alt="'+lg("lblSuppress")+'" />');
			aObj6.inject(divObj2);	
			var bObj6 = new Element('b');
			bObj6.set('html','&nbsp;');
            
                bObj6.inject(divObj2);
		}

        if (this.currentView == 'canvas' ) {
            var this_id = this.ModulePositionId;         
			var aObj6 = new Element('a',
				{
					'events': {
									'click': function()
									{
										$p.app.widgets.homeView(this_id);
										return false;						
									}
							  },
					'href': '#',
                    'alt':'reduce',
					'title':'reduce'
				} 
			);
			aObj6.uniq = this.uniq;
			//aObj6.set('html',$p.img("ico_view_widget.gif",12,11,'reduce widget'));
			aObj6.set('html','<img class="optmod_viewwidget" src="../images/s.gif" width="12" height="11" alt="'+lg("reduce")+'" />');
			aObj6.inject(divObj2);	
			var bObj6 = new Element('b');
			bObj6.set('html','&nbsp;');
			bObj6.inject(divObj2);				
        }
        
        
		var bObj61 = new Element('b');
		bObj61.set('html','&nbsp;');
		bObj61.inject(divObj2);
					
		divObj2.inject(tdObj5);
		tdObj5.inject(trObj2);
		var tdObj6 = new Element('td', 
			{
				'class': 'righttopcornermod'
			}
		);
		tdObj6.set('html','<img src="../images/s.gif" width="1" height="1" />');
		tdObj6.inject(trObj2);
		trObj2.inject(tbodyObj2);
		tbodyObj2.inject(tableObj2);
		tableObj2.inject(tdObj1);
		var divObj3 = new Element('div',
			{
				'id': 'editbar'+this.tab+'_'+this.uniq,
				'class': 'editbox'
			}
		 );
		divObj3.inject(tdObj1);	
		tdObj1.inject(trObj1);
		trObj1.inject(tbodyObj1);
			
		var trObj3 = new Element('tr');
		var tdObj7 = new Element('td',
			{
				'class': 'contentborder'
			}
		);	
		var divObj4 = new Element('div',
			{
				'id': 'module'+this.tab+'_'+this.uniq+'_i',
				'class': 'content',
				'styles':
                        {
							'width': '100%',
							'height': '100%'
                        }
			}
		);				 
		var tableObj3 = new Element('table', 
			{
				'border': '0',
				'cellspacing': '0',
				'cellpadding': '0',
				'align': 'center',
				'width': '100%',
				'height': '100%'
			}
	    );
												   
		var tbodyObj3 = new Element('tbody');
		var trObj4 = new Element('tr');
		var tdObj8 = new Element('td',
			{
				'id': 'module'+this.tab+'_'+this.uniq+'_c',
				'styles':{
					'display' :(this.minimized?'none':'')
				}
			} 
		);

		if (this.format=="I") 
		{
            var FrameHeight = this.height+"px";
            var scrollMode = 'no';

            if (this.currentView=='canvas')  {
                scrollMode="auto";
            }
			var iframeObj1 = new Element('iframe',
				{
					'id': 'modfram'+this.tab+'_'+this.uniq,
					'name': 'modfram'+this.tab+'_'+this.uniq,
					'src': this.url+"pid="+$p.app.user.id
                                    +"&prof="+this.tab
                                    +"&p="+this.uniq
                                    +"&view="+this.currentView
                                    +"&format=I"
                                    +"&l10n="+this.l10n
                                    +"&"+this.vars.replace('#','%23')
                                    +"&sharedmd5key="+this.sharedmd5key
                                    +"&skin="+RODINSKIN,
                                   
					'scrolling':scrollMode,
					'frameborder':'0',
					'marginwidth':'0',
					'marginheight':'0',
					'height':FrameHeight,
					'width':'100%',
					'z-index':'auto'
				} 
			);	 
          
			//iframeObj1.set('html','Issue with module display.');
			var aObj7 = new Element('a',
				{
					'href': posh["contact"],
					'target': '_blank'
				} 
			);				
			aObj7.set('html','Contact us');	
			//aObj7.inject(iframeObj1);
			iframeObj1.inject(tdObj8);
			var brObj1 = new Element('br');	
			brObj1.inject(tdObj8);	
			
		}
		if (this.format=="U") 
		{
            var remoteUrl = this.url;
            remoteUrl = $p.string.trim(remoteUrl);
            var reg = /^http/;
            if ( remoteUrl.match(reg) ) {
                var regdomain = /__LOCALFOLDER/;
                if ( remoteUrl.match(regdomain) ) {
                    remoteUrl = remoteUrl 
                        +"pid="+$p.app.user.id
                        +"&prof="+this.tab
                        +"&view="+this.currentView
                        +"&format=U"
                        +"&p="+this.uniq
                        +"&plg="+__lang+"&"+this.vars.replace('#','%23');
                } else {
                    var regPoint = /\?$/;
                    remoteUrl = remoteUrl.replace(regPoint,'');
                }
            } else {
                remoteUrl = remoteUrl 
                        +"pid="+$p.app.user.id
                        +"&prof="+this.tab
                        +"&view="+this.currentView
                        +"&format=U"
                        +"&p="+this.uniq
                        +"&plg="+__lang+"&"+this.vars.replace('#','%23');
            }
            var scrollMode = 'no';

            if (this.currentView=='canvas')  {
                scrollMode="auto";
            }
			var iframeObj2 = new Element('iframe',
				{
					'styles': {
						'height': this.height+"px",
						'width': '100%'
					},
					'id': 'modfram'+this.tab+'_'+this.uniq,
					'name': 'modfram'+this.tab+'_'+this.uniq,
					'src': remoteUrl,
					'frameborder':'no', 
					'marginwidth':'0', 
					'marginheight':'0',
					'scrolling':scrollMode,
					'z-index':'auto'
				  } 
			);
			iframeObj2.inject(tdObj8);
			var brObj2 = new Element('br');	
			brObj2.inject(tdObj8);										
		}
		
		if (this.format=="D") //HTML integrated in Div 
		{
			tdObj8.set('html',this.content);
		}
		
		if (this.format=="H") 
		{
			var iframeObj3 = new Element('iframe',
				{
					'styles': {
									'height': this.height+"px",
									'width': '100%'
							  },
					'id': 'modfram'+this.tab+'_'+this.uniq,
					'name': 'modfram'+this.tab+'_'+this.uniq,
					'src': '',
					'frameborder':'no', 
					'marginwidth':'0', 
					'marginheight':'0', 
					'scrolling':'no',
					'z-index':'auto'
				} 
			);		
			//iframeObj3.set('html','Issue with module display. ');
			var aObj9 = new Element('a',
				{
					'href': posh["contact"],
					'target': '_blank'
				} 
			);
			aObj9.set('html','Contact us');
			aObj9.inject(iframeObj3);
			iframeObj3.inject(tdObj8);
			var brObj3 = new Element('br');	
			brObj3.inject(tdObj8);										
		}

		if (this.format=="R" || this.format=="M") {
			tdObj8.set('html',lg("lblLoading"));
		}
					
		tdObj8.inject(trObj4);
		trObj4.inject(tbodyObj3);
		tbodyObj3.inject(tableObj3);
		tableObj3.inject(divObj4);

		//Div+image used to force minimal width
		var divObj5 = new Element('div',
			{
				'styles':
				{
					'height': '1px'
				}
			} 
		);			 
		divObj5.set('html',$p.img("s.gif",this.minModSize,1,indef,indef,"line"+this.tab+"_"+this.uniq));
		divObj5.inject(divObj4);
		
		divObj4.inject(tdObj7);
				
		if (__useNotation&&$p.app.user.id>0) 
		p_notation.buildBlock(this.uniq);

		tdObj7.inject(trObj3);	
		trObj3.inject(tbodyObj1);

		//Widget footer
		var footer_trContainer=new Element('tr');
		footer_trContainer.inject(tbodyObj1);
		var footer_tdContainer=new Element('td');
		footer_tdContainer.inject(footer_trContainer);
		var footer_table = new Element('table',
			{
				'id': 'fmod'+this.tab+'_'+this.uniq,
				'class': 'fmod',
				'cellspacing': '0',
				'cellpadding': '0',
				'width': '100%'
			}
		);
		footer_table.inject(footer_tdContainer);
		var footer_tbody = new Element('tbody');
		footer_tbody.inject(footer_table);
		var footer_tr = new Element('tr');
		footer_tr.inject(footer_tbody);
		var footer_leftcorner = new Element('td', 
			{
				'class': 'leftbottomcornermod'
			}
		);
		footer_leftcorner.set('html','<img src="../images/s.gif" style="width: 1px;height: 1px;line-height: 1px;" />');
		footer_leftcorner.inject(footer_tr);
		var footer_center = new Element('td', 
			{
				'class': 'bottommod'
			}
		);
		footer_center.set('html','<img src="../images/s.gif" style="width: 1px;height: 1px;line-height: 1px;" />');
		footer_center.inject(footer_tr);
		var footer_rightcorner = new Element('td', 
			{
				'class': 'rightbottomcornermod'
			}
		);
		footer_rightcorner.set('html','<img src="../images/s.gif" style="width: 1px;height: 1px;line-height: 1px;" />');
		footer_rightcorner.inject(footer_tr);


		tbodyObj1.inject(tableObj1);
		tableObj1.inject(l_obj);

		//check if authentified feed or not
		var l_vars=this.vars;
		this.auth=(l_vars.indexOf("auth=")!=-1 || (l_vars.indexOf("user=")!=-1 && l_vars.indexOf("pass=")!=-1))?true:false;
		//load the content
		if (this.format=="R") 
        {
            var r_id = $p.app.widgets.uniqToId(this.uniq,l_tabPos);
            $p.app.widgets.rss.refresh(r_id,l_tabPos);
        }    
		if (this.format=="M") 
        {
            $p.app.widgets.refreshContent($p.app.widgets.uniqToId(this.uniq,l_tabPos),l_tabPos,this.sharedmd5key);
        }    
		//if (this.format=="H") refreshHtmlCont($p.app.widgets.uniqToId(this.uniq));

		if (this.format=="I" || this.format=="U")   {
			if (this.nbvars>0) {
               $p.app.widgets.param.getModuleParam($p.app.widgets.uniqToId(this.uniq,l_tabPos),l_tabPos);
            }
			else {
				$p.app.widgets.param.fillEditBox(indef,this.uniq,l_tabPos);
			}
		}
		this.hdrColor();
		this.getIcon();
		$p.plugin.hook.launch('app.widgets.create.end');
	},
	/*
		Function: $p.app.widgets.destruct
                    
                    Destruct a module
	*/
	destruct:function()
	{
		$p.plugin.hook.launch('app.widgets.destruct.start');

		var l_obj=$("module"+this.tab+"_"+this.uniq);
		var l_col=this.newcol,l_tabPos=$p.app.tabs.idToPos(this.tab);
		if (tab[l_tabPos].showType==1 && this.newcol!=0) l_col=1;
		if (tab[l_tabPos].moduleAlign)  {
			tab[l_tabPos].cols[l_col].removeChild(l_obj);
		}
		else    {
			tab[l_tabPos].root.removeChild(l_obj);
		}

		$p.plugin.hook.launch('app.widgets.destruct.end');
	},
	/*
		Function: $p.app.widgets.refresh
                                Refresh a mdoule
                                
                     Returns:
                                
                                false
	*/
	refresh:function()
	{
		$p.plugin.hook.launch('app.widgets.refresh.start');
		var l_tabPos=$p.app.tabs.idToPos(this.tab);
		var l_id=$p.app.widgets.uniqToId(this.uniq,l_tabPos);

		tab[l_tabPos].module[l_id].isLoaded=false;

		if (this.format=="I" || this.format=="U")   {
			var l_obj=$("modfram"+tab[l_tabPos].id+"_"+this.uniq);
			if (this.format=="I")  {
				var l_url=tab[l_tabPos].module[l_id].url
                                +"pid="+$p.app.user.id
                                +"&prof="+tab[l_tabPos].id
                                +"&format="+this.format
                                +"&view="+this.currentView
                                +"&l10n="+tab[l_tabPos].module[l_id].l10n
                                +"&p="+tab[l_tabPos].module[l_id].uniq
                                +"&plg="+__lang
                                +"&"+tab[l_tabPos].module[l_id].vars.replace('#','%23')
                                +"&sharedmd5key="+this.sharedmd5key
                                +"&skin="+RODINSKIN;
			}
			else   {
				var l_url=tab[l_tabPos].module[l_id].url
                                +"pid="+$p.app.user.id
                                +"&prof="+tab[l_tabPos].id
                                +"&format="+this.format
                                +"&view="+this.currentView
                                +"&l10n="+tab[l_tabPos].module[l_id].l10n
                                +"&p="+tab[l_tabPos].module[l_id].uniq
                                +"&plg="+__lang
                                +("&"+tab[l_tabPos].module[l_id].vars).replace(/&/g,"&up_");
            }
           
            l_obj.src=l_url;
		}
        
		if (this.format=="R")   {
			$p.app.widgets.rss.refresh(l_id,l_tabPos,true);
		}
		if (this.format=="M")   {
			$p.app.widgets.refreshContent(l_id,l_tabPos,this.sharedmd5key);
		}
        
		$p.plugin.hook.launch('app.widgets.refresh.end');        
        
		return false;
	},
	/*
		Function: $p.app.widgets.placeonTop
                                Place a module on top of a column
	*/
	placeonTop:function()
	{
		//change the position of the modules that are placed in the destination column
		var l_tabPos=$p.app.tabs.idToPos(this.tab);
		for (var i=0;i<tab[l_tabPos].module.length;i++) {
			if (tab[l_tabPos].module[i].newcol==this.newcol) tab[l_tabPos].module[i].newpos+=1;
			if (this.format!='R' 
                && tab[l_tabPos].module[i].format!='R') 
                    tab[l_tabPos].module[i].newposj++;
		}
		//set the position of the module added (usefull ?? treated before ??)
		this.newpos=1;
		
		if (tab[l_tabPos].showType==0)  var l_col=this.newcol;
		else    var l_col=1;
        
		$p.app.widgets.placeIn(this,l_tabPos,l_col,false);
	},
	/*
		Function: $p.app.widgets.placeonBottom
                                Place a module on the bottom of a column
                                
                     ATTENTION : the function bugs when it's used to place a widget at the bottom of the first column
                     
	*/
	placeonBottom:function(){
		//change the position of the modules that are placed in the destination column
		var l_tabPos=$p.app.tabs.idToPos(this.tab);
		var nbColumnItem=0;
		for (var i=0;i<tab[l_tabPos].module.length;i++) {
			if (tab[l_tabPos].module[i].newcol==this.newcol) {
                nbColumnItem++;
            }
			if (this.format!='R' 
                && tab[l_tabPos].module[i].format!='R') {
                    tab[l_tabPos].module[i].newposj++;
                    }
		}
		//set the position of the module added (usefull ?? treated before ??)
		this.newpos=nbColumnItem;
			
		if (tab[l_tabPos].showType==0) {
            var l_col=this.newcol;
        }
		else {
            var l_col=1;
        }
        
		$p.app.widgets.placeIn(this,l_tabPos,l_col,true);
	},
	/*
		Function: $p.app.widgets.placeAfter
                                Place a module after a target module
								
		Inputs: target =>(tab[$p.app.tabs.sel].module[i]) is the targeted module
	*/
	placeAfter:function(target){
		//change the position of the modules that are placed in the destination column
		
		var l_tabPos=$p.app.tabs.idToPos(target.tab);
		for (var i=0;i<tab[l_tabPos].module.length;i++) {
			if (tab[l_tabPos].module[i].newcol==target.newcol&& tab[l_tabPos].module[i].newpos>target.newpos){
				tab[l_tabPos].module[i].newpos+=1;
				
			}
			if (this.format!='R' 
                && tab[l_tabPos].module[i].format!='R') 
                    tab[l_tabPos].module[i].newposj++;
		}
		this.newpos=target.newpos++;
		this.newcol=target.newcol;
		
		if (tab[l_tabPos].showType==0)  var l_col=target.newcol;
		else    var l_col=1;
        
		$p.app.widgets.placeIn(this,l_tabPos,l_col,false,target,true);
	},
	/*
	Function: $p.app.widgets.placeBefore
							Place a module before a target module
							
	Inputs: target =>(tab[$p.app.tabs.sel].module[i]) is the targeted module
	*/
	placeBefore:function(target){
		//change the position of the modules that are placed in the destination column
		var l_tabPos=$p.app.tabs.idToPos(target.tab);
		
		for (var i=0;i<tab[l_tabPos].module.length;i++) {
			if (tab[l_tabPos].module[i].newcol==target.newcol&& tab[l_tabPos].module[i].newpos>=(target.newpos-1)) tab[l_tabPos].module[i].newpos+=1;
			if (this.format!='R' 
                && tab[l_tabPos].module[i].format!='R') 
                    tab[l_tabPos].module[i].newposj++;
		}
		this.newcol=target.newcol;
		this.newpos=target.newpos;
		
		
		if (tab[l_tabPos].showType==0)  var l_col=target.newcol;
		else    var l_col=1;
        
		$p.app.widgets.placeIn(this,l_tabPos,l_col,false,target,false);
	},
	/*
	Function: $p.app.widgets.placeIn
							Place a module
	Inputs:nextSibling
	*/
	placeIn:function(module,l_tabPos,l_col,last,target,nextSibling){
		if ($("module"+module.tab+"_"+module.uniq)!=null)
		{
			var l_movedMod=$("module"+module.tab+"_"+module.uniq);
			
			if (tab[l_tabPos].moduleAlign)
			{
				if(last==true){
					var l_inCol=tab[l_tabPos].cols[l_col].lastChild;
				}
				else if(target!=indef){
					var l_inCol=$("module"+target.tab+"_"+target.uniq);
				}
				else{
					var l_inCol=tab[l_tabPos].cols[l_col].firstChild;
				}

				if (tab[l_tabPos].showType==0 || module.format!='R')
				{
					if(nextSibling==true){
						tab[l_tabPos].cols[l_col].insertBefore(l_movedMod,l_inCol.nextSibling);
					}
					else{
						tab[l_tabPos].cols[l_col].insertBefore(l_movedMod,l_inCol);
					}
					$p.app.widgets.move.set(l_movedMod,"module",tab[l_tabPos].cols,"multidim",true,l_tabPos);
				}
				else
					$p.app.widgets.rss.reader.showArticlesList(true);
			}
			else
			{
				tab[l_tabPos].root.appendChild(l_movedMod);
				$p.app.widgets.move.set(l_movedMod,"module",tab[l_tabPos].root,"multidim",true,l_tabPos);
			}
		}
	},	
		/*
	Function: $p.app.widgets.fri_placeIn
							Place a module by CLONING it in JS DOM !!!
	Inputs:nextSibling
	*/
	fri_placeIn:function(module,l_tabPos,newtab_db_id,l_col,last,target,nextSibling){
		if ($("module"+module.tab+"_"+module.uniq)!=null)
		{
			
			var l_movedMod=$("module"+module.tab+"_"+module.uniq);
			var fri_cloned_mod=l_movedMod.cloneNode(true);
			DOM_adapt_newmodule( fri_cloned_mod, module.tab, newtab_db_id); // fritest - subst tab_id in module elems
			
			
			if (tab[l_tabPos].moduleAlign)
			{
				if(last==true){
					var l_inCol=tab[l_tabPos].cols[l_col].lastChild;
				}
				else if(target!=indef){
					var l_inCol=$("module"+target.tab+"_"+target.uniq);
				}
				else{
					var l_inCol=tab[l_tabPos].cols[l_col].firstChild;
				}

				if (tab[l_tabPos].showType==0 || module.format!='R')
				{
					if(nextSibling==true){
						tab[l_tabPos].cols[l_col].insertBefore(fri_cloned_mod,l_inCol.nextSibling);
					}
					else{
						tab[l_tabPos].cols[l_col].insertBefore(fri_cloned_mod,l_inCol);
					}
					//$p.app.widgets.move.set(l_movedMod,"module",tab[l_tabPos].cols,"multidim",true,l_tabPos);
				}
				
			}
			else
			{
				tab[l_tabPos].root.appendChild(l_movedMod);
				$p.app.widgets.move.set(fri_cloned_mod,"module",tab[l_tabPos].root,"multidim",true,l_tabPos);
			}
		}
	},

	//////////////////////////////////////////////
	// Aggregated view functions
	//////////////////////////////////////////////
	/**
	 * $p.app.widgets.openAggregatedView
	 */
	openAggregatedView:function() {
    var tabId = tab[$p.app.tabs.sel].id;
		var index = allWidgetsResultsSetsTabId.indexOf(tabId);

		if (index == -1) {
			var localResultSet = new RodinResultSet();
			localResultSet.containerDivId = 'aggregated_view_results_' + tabId;

			allWidgetsResultsSetsTabId.push(tabId);
			index = allWidgetsResultsSetsTabId.indexOf(tabId);
			allWidgetsResultSets[index] = localResultSet;
		}

		// Let's get a handle on the first column of the table
		var modulesHome = document.getElementById('modules' + tabId);
		var firstChild = modulesHome.getChildren()[0];

		// The aggregated view should be placed over the widgets
		// but under the Survista visualization if it is present
		if (firstChild.getAttribute('id') == 'survista_module') {
			firstChild = modulesHome.getChildren()[1];
		}

		// Check for the aggregated view module presence
		if (firstChild.getAttribute('id') != 'aggregated_view_module_' + tabId) {
			// It is absent so we add it
			var moduleDiv = document.createElement('div');
			moduleDiv.setAttribute("class", "module");
			moduleDiv.setAttribute("id", "aggregated_view_module_" + tabId);
			moduleDiv.setAttribute("style", "display: block; position: relative; margin: 5px;");
      
			var menuDiv = document.createElement('div');
			menuDiv.setAttribute("id", "aggregated_view_menu_" + tabId);
			menuDiv.setAttribute("class", "aggregatedViewMenu");

			var resultsContainer = document.createElement('div');
			resultsContainer.setAttribute("id", allWidgetsResultSets[index].containerDivId);
			resultsContainer.setAttribute("class", "widgetResultsDiv");
			
			moduleDiv.appendChild(menuDiv);
			moduleDiv.appendChild(resultsContainer);
			
			modulesHome.insertBefore(moduleDiv, firstChild);
		}
    
		// Refresh the aggregated widgets' list
		refresh_aggregated_widget_icons();

		// Refresh the view content
		$p.app.widgets.refreshAggregatedView();
	},
	/**
	 * $p.app.widgets.refreshAggregatedView
	 */
	refreshAggregatedView:function() {
		var tabId = tab[$p.app.tabs.sel].id;
		var index = allWidgetsResultsSetsTabId.indexOf(tabId);

		var resultsContainer = jQuery('#' + allWidgetsResultSets[index].containerDivId);
		resultsContainer.empty();

		var params = {
			sid : getLastSidForTab(tabId),
			suffix: tabId
		};

		jQuery.post('../../app/u/RodinResult/RodinResultResponder.php', params, function(data) {
			$p.app.widgets.addRestustsToAggregatedView(data);
		});
	},
	/**
	 * $p.app.widgets.addRestustsToAggregatedView
	 */
	addRestustsToAggregatedView:function(data) {
		var tabId = tab[$p.app.tabs.sel].id;
		var index = allWidgetsResultsSetsTabId.indexOf(tabId);
		
		// Add the results obtained
		// TODO Change the result identifier in the header and content div, create a method
		// that initializes the divs with a new id 
		for (var i = 0; i < data.results.length; i++) {
			var resultObj = new RodinResult(data.results[i].resultIdentifier);
			resultObj.headerDiv = jQuery.parseJSON(data.results[i].headerDiv);
			resultObj.contentDiv = jQuery.parseJSON(data.results[i].contentDiv);
			resultObj.minHeader = jQuery.parseJSON(data.results[i].minHeader);
			resultObj.header = jQuery.parseJSON(data.results[i].header);
			resultObj.minContent = jQuery.parseJSON(data.results[i].minContent);
			resultObj.tokenContent = jQuery.parseJSON(data.results[i].tokenContent);
			resultObj.allContent = jQuery.parseJSON(data.results[i].allContent);
		
			allWidgetsResultSets[index].addResultAndRender(resultObj, jQuery("#selectedTextZoom").val());
		}

		if (data.upto < data.count) {
			var params = {
				sid : data.sid,
				from: data.upto,
				suffix: tabId
			};

			jQuery.post('../../app/w/RodinResult/RodinResultResponder.php', params, function(data) {
				$p.app.widgets.addRestustsToAggregatedView(data);
			});

		}

	},
	/**
	 * $p.app.widgets.closeAggregatedView
	 */
	closeAggregatedView:function() {
    var tabId = tab[$p.app.tabs.sel].id;
	// Let's get a handle on the tab element
		var modulesHome = document.getElementById('modules' + tabId);
		firstChild = modulesHome.getChildren()[0];
		
		// We show and hide only if the first child is defined.
		// Which isn't the case if the tab is loading for the
		// first time, then, nothing needs to be removed anyway
		if (typeof firstChild != 'undefined') {
			// The aggregated view is placed over the widgets but
			// under the Survista visualization if it is present
			if (firstChild.getAttribute('id') == 'survista_module') {
				firstChild = modulesHome.getChildren()[1];
			}

			if (firstChild.getAttribute('id') == 'aggregated_view_module_' + tabId) {
				modulesHome.removeChild(document.getElementById('aggregated_view_module_' + tabId));
			}

		}
	},

	//////////////////////////////////////////////
	// Survista functions
	//////////////////////////////////////////////
	closeSurvista:function() {
		// Let's get a handle on the tab element
		modulesHome = document.getElementById('modules' + tab[$p.app.tabs.sel].id);
		firstChild = modulesHome.getChildren()[0];
		
		if (firstChild.getAttribute('id') == 'survista_module') {
			modulesHome.removeChild(document.getElementById('survista_module'));
		}
		
		$p.app.cache.hideShadow();
		$p.app.cache.obj.setStyle("visibility","visible");
	},
	maximizeSurvista:function(rodinsegment, lang) {
		var maximizeButton = jQuery("#survistaMaximizeButton");
		var restoreButton = jQuery("#survistaRestoreButton");
		maximizeButton.css('display', 'none');
		restoreButton.css('display', 'inline');
		
		$p.app.cache.init();
		$p.app.cache.obj.setStyle("visibility","visible");
		$p.app.cache.shadow();

		// Maximize module holding the visualization
		var maxStyle = "display: block; margin: 5px; position: fixed; left: 250px; top:10px; ";
		maxStyle += "height:" + (window.innerHeight - 50) + "px; ";
		maxStyle += "width:" + (window.innerWidth - 300) + "px; ";
		maxStyle += "z-index: 17;";

		var survistaModule = document.getElementById("survista_module");
		survistaModule.setAttribute("style", maxStyle);

		// Resize visualization iframe
		var survistaIFrame = document.getElementById("survista_iframe");
		survistaIFrame.style.height = (window.innerHeight - 70) + "px";
		survistaIFrame.style.zIndex = "18";
		survistaIFrame.style.visibility = "visible";

		// Call the reload URI
		var innerUrl = '' + survistaIFrame.contentDocument.location;

		if (innerUrl.indexOf('stw') != -1) {
			survistaIFrame.contentDocument.location = "../../../gen/u/rodin_survista/stw/index.php?&rodinsegment="+ escape(rodinsegment) + "&l=" + lang;
		} else {
			survistaIFrame.contentDocument.location = "../../../gen/u/rodin_survista/dbpedia/index.php?&rodinsegment="+ escape(rodinsegment) + "&l=" + lang;
		}
	},
	restoreSurvista:function(rodinsegment, lang) {
		var maximizeButton = jQuery("#survistaMaximizeButton");
		var restoreButton = jQuery("#survistaRestoreButton");
		maximizeButton.css('display', 'inline');
		restoreButton.css('display', 'none');
		
		// Resize visualization iframe
		var survistaModule = document.getElementById('survista_module');
		
		survistaModule.setAttribute("style", "display: block; position: relative; margin: 5px;");
			
		// Visualization inner size 930px / 300px;
		var survistaIFrame = document.getElementById("survista_iframe");
		survistaIFrame.style.height = "300px";
		
		// Call the reload URI
		var innerUrl = '' + survistaIFrame.contentDocument.location;

		if (innerUrl.indexOf('stw') != -1) {
			survistaIFrame.contentDocument.location = "../../../gen/u/rodin_survista/stw/index.php?&rodinsegment="+ escape(rodinsegment) + "&l=" + lang;
		} else {
			survistaIFrame.contentDocument.location = "../../../gen/u/rodin_survista/dbpedia/index.php?&rodinsegment="+ escape(rodinsegment) + "&l=" + lang;
		}
		
		$p.app.cache.hideShadow();
		$p.app.cache.obj.setStyle("visibility","visible");
	},
	/**
	 * $p.app.widgets.placeSurvista
	 */
	placeSurvista:function(uri, term, lang, rodinsegment) {
		//alert('Open Survista visualization with uri:' + uri + ' term:' + term + ' in lang:' + lang);
		//alert('Selected tab:' + tab[$p.app.tabs.sel].id);
		
		// Let's get a handle on the first column of the table
		var modulesHome = document.getElementById('modules' + tab[$p.app.tabs.sel].id);
		var firstChild = modulesHome.getChildren()[0];
		
		if (firstChild.getAttribute('id') != 'survista_module') {
			// If it isn't then add it
			var moduleDiv = document.createElement('div');
			moduleDiv.setAttribute("class", "module");
			moduleDiv.setAttribute("id", "survista_module");
			moduleDiv.setAttribute("style", "display: block; position: relative; margin: 5px;");
			
			var menuDiv = document.createElement('div');
			menuDiv.setAttribute("style", "background-color: #eeeeee; text-align: right;");
			
			var closeButtonImg = document.createElement('img');
			closeButtonImg.setAttribute("style", "margin: 2px; cursor: pointer;");
			closeButtonImg.setAttribute("src", "../images/ico_close.gif");
			closeButtonImg.setAttribute("title", lg("titleSurvistaClose"));
			closeButtonImg.setAttribute("onClick", "javascript: $p.app.widgets.closeSurvista();");
			
			var maximizeButtonImg = document.createElement('img');
			maximizeButtonImg.setAttribute("style", "margin: 2px; cursor: pointer; display: inline;");
			maximizeButtonImg.setAttribute("id", "survistaMaximizeButton");
			maximizeButtonImg.setAttribute("src", "../images/ico_view_fullpage.gif");
			maximizeButtonImg.setAttribute("title", lg("titleSurvistaMaximize"));
			maximizeButtonImg.setAttribute("onClick", "javascript: $p.app.widgets.maximizeSurvista('" + rodinsegment + "', '" + lang + "');");

			var restoreButtonImg = document.createElement('img');
			restoreButtonImg.setAttribute("style", "margin: 2px; cursor: pointer; display: none;");
			restoreButtonImg.setAttribute("id", "survistaRestoreButton");
			restoreButtonImg.setAttribute("src", "../images/ico_view_widget.gif");
			restoreButtonImg.setAttribute("title", lg("titleSurvistaRestore"));
			restoreButtonImg.setAttribute("onClick", "javascript: $p.app.widgets.restoreSurvista('" + rodinsegment + "', '" + lang + "');");
			
			menuDiv.appendChild(restoreButtonImg);
			menuDiv.appendChild(maximizeButtonImg);
			menuDiv.appendChild(closeButtonImg);
			
			var visualizationIFrame = document.createElement('iframe');
			visualizationIFrame.setAttribute("scrolling", "no");
			visualizationIFrame.setAttribute("frameborder", "0");
			visualizationIFrame.setAttribute("marginwidth", "0");
			visualizationIFrame.setAttribute("marginheight", "0");
			visualizationIFrame.setAttribute("id", "survista_iframe");
			visualizationIFrame.style.height = "300px";
			visualizationIFrame.style.width = "100%";
			
			moduleDiv.appendChild(menuDiv);
			moduleDiv.appendChild(visualizationIFrame);
			
			modulesHome.insertBefore(moduleDiv, firstChild);
		}
		
		var moduleDiv = document.getElementById('survista_module');
		var visualizationIFrame = document.getElementById('survista_iframe');
		
		// Check if the term sent is a ZBW URI
		if (uri.indexOf("http://zbw") >= 0) {
			visualizationIFrame.setAttribute("src", "../../../gen/u/rodin_survista/stw/index.php?r=" + uri + "&ul=" + term + "&rodinsegment="+ escape(rodinsegment) + "&l=" + lang + "&l10n=" + __lang);
		} else {
			// Check if the term sent is a DBPedia skos:Concept URI
			if (uri.indexOf("http://dbpedia.org/resource/Category:") >= 0) {
				visualizationIFrame.setAttribute("src", "../../../gen/u/rodin_survista/dbpedia/index.php?r=" + uri + "&rodinsegment="+ escape(rodinsegment) + "&l=" + lang + "&l10n=" + __lang);
			}
		}
	},
	/*
		Function: $p.app.widgets.placeinCol
                                Place a module on a column
		Inputs: mode = 	"top" =>place on the top of a column
						"bottom" => place on the bottom of a column
						"before" => place before the target module
						"after" => place after the target module
						FRI: Place Module only once - eg if there is no other module inside with the same id
	 */
	placeinCol:function(v_col,v_tab,mode,target)
	{
        $p.plugin.hook.launch('app.widgets.placeincol.start');

        var fri_new_module_last_mod =  tab[$p.app.tabs.sel].module.length-1;
		var fri_new_module_cnt =  fri_new_module_last_mod;
		var fri_new_module =  tab[$p.app.tabs.sel].module[fri_new_module_last_mod];
		var fri_new_module_id =  fri_new_module.id;
		var fri_new_module_url =  fri_new_module.url;
		var fri_new_module_name =  get_datasource_name(fri_new_module.url);
		var x=-1;
		var insert=true;
		var modulex=null;
		var fri_modules =  tab[$p.app.tabs.sel].module;
		for(x=0;x<fri_new_module_cnt;x++)
		{
			var modulex=tab[$p.app.tabs.sel].module[x];
			if (fri_new_module_id == modulex.id)
			{
				alert('Widget '+fri_new_module_name+' is already in TAB "'+tab[$p.app.tabs.sel].label+'" and will not be added twice!');
				insert=false;
				break;
			}
		}
		
		if (insert)
		{
			if (v_tab==indef) v_tab=$p.app.tabs.sel;
			if (!$p.app.pages.maxModReached())
			{
				this.newcol=v_col;
				if(mode=="bottom"){
					this.placeonBottom();
				}
				else if(mode=="before"){
					this.placeBefore(target);
				}
				else if(mode=="after"){
					this.placeAfter(target);
				}
				else{
					this.placeonTop();
				}
				
				if (tab[v_tab].controls=='Y') {$p.app.widgets.showHdr(v_tab);}
				var l_vars=[];
				l_vars[0]=v_col;
				$p.app.widgets.saveChanges(l_vars,indef,v_tab);
			}
		}
        $p.plugin.hook.launch('app.widgets.placeincol.end');
	},
	/*
		Function: $p.app.widgets.bringToFront
                                Bring a widget to front of the page, over the others
	*/
	bringToFront:function()
	{
    
		if (!tab[$p.app.tabs.sel].moduleAlign)  {
			($("module"+tab[$p.app.tabs.sel].id+"_"+this.uniq)).setStyle("zIndex",1000);
		}
	},
	/*
		Function: $p.app.widgets.reduce
                                Minimize a widget
	*/
	reduce:function()
	{
		$p.app.widgets.minimize(this.uniq);
	},
	/*
		Function: $p.app.widgets.minimize
                                Minimize a widget
                     
                     Parameters:
                     
                                v_uniq - module uniq ID
	*/
	minimize:function(v_uniq)
	{
		var l_id = $p.app.widgets.uniqToId(v_uniq);
		if (l_id==undefined) 
		{	
				//alert('minimize:function('+v_uniq+'): Kein widget in tab '+$p.app.tabs.sel+' zu uniqid gefunden!');
				return false; //FRI
		}
		else if (tab[$p.app.tabs.sel].module.length==0)
		{
				//alert('minimize:function('+v_uniq+'): Keine widget in tab '+$p.app.tabs.sel+' enthalten!');
				return false; //FRI
		}
		
		
		if (tab[$p.app.tabs.sel].module[l_id].minimized)
        {
			$p.show("module"+tab[$p.app.tabs.sel].id+"_"+v_uniq+"_c","block");
			$p.show("module"+tab[$p.app.tabs.sel].id+"_"+v_uniq+"_i","block");
            $p.setClass('imgminimize'+tab[$p.app.tabs.sel].id+'_'+v_uniq,'optmod_minimize');

			tab[$p.app.tabs.sel].module[l_id].minimized=false;

			$p.ajax.call(posh["scr_minimize"],
				{
					'type':'execute',
					'variables':"m=0&prof="+tab[$p.app.tabs.sel].id+"&u="+v_uniq
				}
			);

			document.getElementById('module'+tab[$p.app.tabs.sel].id+'_'+v_uniq+'_icon_a').title = lg('minimize');
			
			// On restoring, be sure to adapt de width of the iframe as well
			adapt_widget_search_input_width('modfram' + tab[$p.app.tabs.sel].id + '_' + v_uniq);
		}
		else
        {
			$p.show("module"+tab[$p.app.tabs.sel].id+"_"+v_uniq+"_c","none");
			$p.show("module"+tab[$p.app.tabs.sel].id+"_"+v_uniq+"_i","none");
            $p.setClass('imgminimize'+tab[$p.app.tabs.sel].id+'_'+v_uniq,'optmod_maximize');

			tab[$p.app.tabs.sel].module[l_id].minimized=true;

			$p.ajax.call(posh["scr_minimize"],
				{
					'type':'execute',
					'variables':"m=1&prof="+tab[$p.app.tabs.sel].id+"&u="+v_uniq
				}
			);
			
			document.getElementById('module'+tab[$p.app.tabs.sel].id+'_'+v_uniq+'_icon_a').title = lg('titleWidgetRestore');
		}
	},
	/*
		Function: $p.app.widgets.fri_minimize
                                Minimize a widget
                     
                     Parameters:
                     
                                v_uniq - module uniq ID
	*/
	fri_minimize:function(module)
	{
		if (module.minimized)
    {
			$p.show("module"+tab[$p.app.tabs.sel].id+"_"+module.uniq+"_c","block");
			$p.show("module"+tab[$p.app.tabs.sel].id+"_"+module.uniq+"_i","block");
			p.setClass('imgminimize'+tab[$p.app.tabs.sel].id+'_'+module.uniq,'optmod_minimize');

			module.minimized=false;

			$p.ajax.call(posh["scr_minimize"],
				{
					'type':'execute',
					'variables':"m=0&prof="+tab[$p.app.tabs.sel].id+"&u="+module.uniq
				}
			);
		}
		else
    {
			$p.show("module"+tab[$p.app.tabs.sel].id+"_"+module.uniq+"_c","none");
			$p.show("module"+tab[$p.app.tabs.sel].id+"_"+module.uniq+"_i","none");
      $p.setClass('imgminimize'+tab[$p.app.tabs.sel].id+'_'+module.uniq,'optmod_maximize');

			module.minimized=true;

			$p.ajax.call(posh["scr_minimize"],
				{
					'type':'execute',
					'variables':"m=1&prof="+tab[$p.app.tabs.sel].id+"&u="+module.uniq
				}
			);
		}
	},
	/*
		Function: $p.app.widgets.setName
                                Set widget title
                     
                     Parameters:
                     
                                v_name - module name
	*/
	setName:function(v_name)
	{
        if (!v_name) v_name=$p.string.getVar(this.vars,"ptitl");
		$p.app.widgets.changeName($p.app.widgets.uniqToId(this.uniq),v_name,$p.app.tabs.idToPos(this.tab));
	},
	/*
		Function: $p.app.widgets.setIcon
                                Set widget icon
                     
                     Parameters:
                     
                                v_icon - module icon
	*/
	setIcon:function(v_icon)
	{
		$p.app.widgets.changeIcon($p.app.widgets.uniqToId(this.uniq),v_icon,$p.app.tabs.idToPos(this.tab));
	},
	/*
		Function: $p.app.widgets.setHeight
                                Define module height
                     
                     Parameters:
                     
                                v_height - height applied
	*/
	setHeight:function(v_height, v_mod)
	{
		if( v_mod==indef ) {
			$p.setHeight("modfram"+this.tab+"_"+this.uniq,v_height);
		} else {
			if( v_mod == "FeedDisplayInfo" )
				$p.setHeight(v_mod,v_height);
			else
				$p.setHeight(v_mod+"_"+this.uniq,v_height);
		}
	},
	/*
		Function: $p.app.widgets.changeVar
                                Change a module parameter
                     
                     Parameters:
                     
			v_var - variable to update
			v_value - new value
	*/
	changeVar:function(v_var,v_value)
	{
        var dbuniq = this.uniq;
        //canvas management, update parent module in database
        var l_id=$p.app.widgets.uniqToId(this.uniq);
        if ( tab[$p.app.tabs.sel].module[l_id] 
                && tab[$p.app.tabs.sel].module[l_id].UniqParent
            ) 
        {
            dbuniq = tab[$p.app.tabs.sel].module[l_id].UniqParent;
            var l_id_parent = tab[$p.app.tabs.sel].module[l_id].DBIdParent;
        }
        //end canvas management
		var l_oldVars=this.vars;
		if (v_var=="")  {
			l_newVars=v_value;
		}
		else    
        {
			var l_oldValue=$p.string.getVar(l_oldVars,v_var);
			var l_newVars=l_oldVars.replace(v_var+"="+l_oldValue+"&","").replace("&"+v_var+"="+l_oldValue,"").replace(v_var+"="+l_oldValue,"");
			l_newVars=(l_newVars=="")   ?   (v_var+"="+v_value) :   
                                            (l_newVars+"&"+v_var+"="+v_value);
		}
		this.vars=l_newVars;
        if (tab[$p.app.tabs.sel].module[l_id] 
                && tab[$p.app.tabs.sel].module[l_id].UniqParent) 
        {
            tab[$p.app.tabs.sel].module[l_id_parent].vars = l_newVars;
        }

		//record changes in DB
		if (l_oldVars!=l_newVars && $p.app.env!="admin")
		{
			$p.ajax.call(posh["scr_savevar"],
				{
					'type':'execute',
					'variables':"prof="+tab[$p.app.tabs.sel].id
                                    +"&un="+dbuniq
                                    +"&vars="+$p.string.esc(l_newVars),
					'alarm':false
				}
			);
		}
	},
	/*
		Function: $p.app.widgets.show
                                Show a module
                                
                         parameters:

                                    classname - classname used to place widget, if not use 'module'
	*/
	show:function() {

        var currentClassName = 
            this.className ? this.className  : 
                                                'module';                                                
		$p.show(currentClassName+this.tab+"_"+this.uniq,"block");

	},
	/*
		Function: $p.app.widgets.hide
                                Hide a module
	*/
	hide:function (){
        var currentClassName = this.className ? this.className  :
                                                'module';
		$p.show(currentClassName+this.tab+"_"+this.uniq,"none");
	},
	/*
		Function: $p.app.widgets.hdrColor
                                Change module header color
                                
		Parameters:

			v_uniq - uniq ID of the module
			v_style - style to applied to this module
	*/
	hdrColor:function(v_uniq,v_style,classname)
	{
		if (!v_uniq) v_uniq=this.uniq;
        var currentClassName = classname ? classname : 'module';        
		if (!v_style) v_style=$p.string.getVar(this.vars,"bcolor");
        
		var l_obj=$(currentClassName+this.tab+"_"+v_uniq);
		//see color in main.css
        if (l_obj) l_obj.className=currentClassName+v_style;
	},
	/*
		Function: $p.app.widgets.getIcon
                                Get icon information for a module
	*/
	getIcon:function()
	{
		if ($p.string.getVar(this.vars,"icon")!="") {
			this.icon=$p.string.getVar(this.vars,"icon");
		}
	},
	/*
		Function: $p.app.widgets.showOptions
                                Show / hide modules options
                                
		Parameters:

			v_uniq - uniq ID of the module
			v_status (boolean) - display status for the options
		
                     BUG: let the function name in lowercase
	*/
	showOptions:function(v_uniq,v_status)
	{
        var currentClassName = this.className ? this.className  :  'module';
        var l_obj=$(currentClassName+tab[$p.app.tabs.sel].id+"_"+v_uniq+"_o");
		if (l_obj) l_obj.className=(v_status?"optmod":"optmodhide");
	},
	/*
		Function: $p.app.widgets.changeUniq
                                Change uniq ID of a module
                                
		Parameters:

			v_id - module sequence ID
			v_uniq - new Uniq ID for the module
	*/
	changeUniq:function(v_id,v_uniq)
	{
		if (v_uniq==indef) v_uniq=tab[$p.app.tabs.sel].maxUniq;
		$p.ajax.call(posh["scr_changeuniq"],
			{
				'type':'execute',
				'variables':"uniq="+v_uniq+"&prof="+tab[$p.app.tabs.sel].id+"&x="+tab[$p.app.tabs.sel].module[v_id].newcol+"&y="+tab[$p.app.tabs.sel].module[v_id].newpos
			}
		);
	},
	/*
		Function: $p.app.widgets.uniqToId 
                                Get module sequence ID based on its uniq ID in page
                                
		Parameters:

			v_uniq - module uniq ID
			v_tab - tab sequence ID
		
	Returns:
			 module sequence ID
	*/
	uniqToId:function(v_uniq,v_tab)
	{
		if (v_tab==indef) v_tab=$p.app.tabs.sel;
		for (var k=0;k<tab[v_tab].module.length;k++)
		{
			if ( tab[v_tab].module[k].uniq==v_uniq ) 
            { 
                return k;
            }
		}
		return 0;
	},
	/*
		Function: $p.app.widgets.idToId 
                                Get module sequence ID based on its uniq ID in page
                                
		Parameters:

			v_uniq - module uniq ID
			v_tab - tab sequence ID
		
	Returns:
			 module sequence ID
	*/
	idToId:function(v_id,v_tab)
	{
		if (v_tab==indef) v_tab=$p.app.tabs.sel;
		for (var k=0;k<tab[v_tab].module.length;k++)
		{
			if ( tab[v_tab].module[k].id==v_id ) 
            { 
                return k;
            }
		}
		return 0;
	},
	/*
		Function: $p.app.widgets.getModuleId
                                 Get a module object ID
                                 
		Parameters:

			 module div object
		
                    Returns:
			
                                 module sequence ID
	*/
	getModuleId:function(v_obj)
	{
		var ret=-1;
		var l_objid=v_obj.id;
		if (l_objid.indexOf("module")>=0)   {
			var l_idstr=l_objid.replace("module","");//substr(l_objid.indexOf("_")+1);
			var l_tab=$p.app.tabs.idToPos(l_idstr.substr(0,l_idstr.indexOf("_")));
			var l_uniq=l_idstr.substr(l_idstr.indexOf("_")+1);
			ret=$p.app.widgets.uniqToId(l_uniq,l_tab);
		}
		return ret;
	},
	/*
		Function: $p.app.widgets.saveChanges
                                Save modules changes
                                
		Parameters:

			v_vars - modules changes array
			v_supid - id of the module removed (if applicable)
			v_tab - tab sequence ID
	*/
	saveChanges: function(v_vars,v_supid,v_tab)
	{
		$p.plugin.hook.launch('app.widgets.saveChanges.start');
		var l_inc = 0,
            l_save = false;
        var l_id=indef;
		if (v_tab == indef) v_tab = $p.app.tabs.sel;

		var l_s = "prof=" + tab[$p.app.tabs.sel].id;
		var logString = "";
		
		if (tab[v_tab].moduleAlign)
		{
			//build the saving string (for each column IMPACTED)
			for (var l_var = 0;l_var < v_vars.length;l_var ++)
			{
				for (var l_nod = 0;l_nod < tab[v_tab].cols[v_vars[l_var]].childNodes.length-1;l_nod ++)
				{
					//get the information of the concerned node (only if module one)
					var l_mod = tab[v_tab].cols[v_vars[l_var]].childNodes[l_nod];
					if ($p.app.widgets.getModuleId(l_mod) != -1)
                    {
						l_id = $p.app.widgets.getModuleId(l_mod);
					}
					else
                    {
						continue;
					}
					if (tab[v_tab].showType == 0)
                    {
						//redefine the position of the modules in the modified columns
						tab[v_tab].module[l_id].newpos = l_nod + 1;
						tab[v_tab].module[l_id].newcol = v_vars[l_var];
					}
					else
                    {
						tab[v_tab].module[l_id].newposj = l_nod+1;
					}
					//if position modified, the saving string is updated
                    if (tab[v_tab].module[l_id].col != tab[v_tab].module[l_id].newcol 
                        || tab[v_tab].module[l_id].pos != tab[v_tab].module[l_id].newpos 
                        || tab[v_tab].module[l_id].posj != tab[v_tab].module[l_id].newposj)   {
						//check if new module (added) or not
						l_save = true;
						if (tab[v_tab].module[l_id].col < 1)
                        {
							l_s += "&idn=" + tab[v_tab].module[l_id].id 
                                            + "&un=" + tab[v_tab].module[l_id].uniq
                                            +"&pxn="+tab[v_tab].module[l_id].newcol
                                            +"&pyn="+tab[v_tab].module[l_id].newpos
                                            +"&xn="+tab[v_tab].module[l_id].newx
                                            +"&yn="+tab[v_tab].module[l_id].newy
                                            +"&jn="+tab[v_tab].module[l_id].newposj
                                            +"&fn="+$p.string.getVar(tab[v_tab].module[l_id].vars,'pfid')
                                            +"&vn="+$p.string.esc(tab[v_tab].module[l_id].vars);
						}
						else
                        {
							l_inc ++;
							l_s += "&id"+l_inc+"="+tab[v_tab].module[l_id].id
                                            +"&px"+l_inc+"="+tab[v_tab].module[l_id].newcol
                                            +"&py"+l_inc+"="+tab[v_tab].module[l_id].newpos
                                            +"&j"+l_inc+"="+tab[v_tab].module[l_id].newposj
                                            +"&x"+l_inc+"="+tab[v_tab].module[l_id].newx
                                            +"&y"+l_inc+"="+tab[v_tab].module[l_id].newy
                                            +"&u"+l_inc+"="+tab[v_tab].module[l_id].uniq;
						}

						// prepare string for logging
						var widgetName = get_datasource_name(tab[v_tab].module[l_id].url);
						logString += "[" + widgetName + "(" + tab[v_tab].module[l_id].col + ", " + tab[v_tab].module[l_id].pos + ")";
						logString += "->(" + tab[v_tab].module[l_id].newcol + ", " + tab[v_tab].module[l_id].newpos + ")] ";

						//change datas in widgets object 
						tab[v_tab].module[l_id].col = tab[v_tab].module[l_id].newcol;
						tab[v_tab].module[l_id].pos = tab[v_tab].module[l_id].newpos;
						tab[v_tab].module[l_id].posj = tab[v_tab].module[l_id].newposj;
                    }
				}
			}
			
			if (logString != '') // Happens when moving a widget to another tab
				$p.ajax.call('../../app/tests/LoggerResponder.php?action=13&msg=' + logString, {'type':'load'});
		}
		else
		{
			//build the saving string (for each column IMPACTED)
			for (var l_nod = 0;l_nod < tab[v_tab].root.childNodes.length;l_nod ++)
			{
				//get the information of the concerned node (only if module one)
				var l_mod = tab[v_tab].root.childNodes[l_nod];
				if ($p.app.widgets.getModuleId(l_mod) != -1)
                {
					l_id = $p.app.widgets.getModuleId(l_mod);
				}
				else
                {
					continue;
				}
				//if position modified, the saving string is updated
				if (tab[v_tab].module[l_id].x != tab[v_tab].module[l_id].newx 
                    || tab[v_tab].module[l_id].y != tab[v_tab].module[l_id].newy) {
    					//check if new module (added) or not
    					l_save = true;
    					if (tab[v_tab].module[l_id].col < 1)  {
    						l_s += "&idn="+tab[v_tab].module[l_id].id
                                    +"&un="+tab[v_tab].module[l_id].uniq
                                    +"&pxn="+tab[v_tab].module[l_id].newcol
                                    +"&pyn="+tab[v_tab].module[l_id].newpos
                                    +"&xn="+tab[v_tab].module[l_id].newx
                                    +"&yn="+tab[v_tab].module[l_id].newy
                                    +"&jn="+tab[v_tab].module[l_id].newposj
                                    +"&fn="+$p.string.getVar(tab[v_tab].module[l_id].vars,'pfid')
                                    +"&vn="+$p.string.esc(tab[v_tab].module[l_id].vars);
    					}
    					else
                        {
    						l_inc ++;
    						l_s += "&id"+l_inc+"="+tab[v_tab].module[l_id].id
                                        +"&px"+l_inc+"="+tab[v_tab].module[l_id].newcol
                                        +"&py"+l_inc+"="+tab[v_tab].module[l_id].newpos
                                        +"&j"+l_inc+"="+tab[v_tab].module[l_id].newposj
                                        +"&x"+l_inc+"="+tab[v_tab].module[l_id].newx
                                        +"&y"+l_inc+"="+tab[v_tab].module[l_id].newy
                                        +"&u"+l_inc+"="+tab[v_tab].module[l_id].uniq;
    					}
    					tab[v_tab].module[l_id].x = tab[v_tab].module[l_id].newx;
    					tab[v_tab].module[l_id].y = tab[v_tab].module[l_id].newy;
				}
			}
		}
		fri_delete_widget=false;
		if (v_supid != indef) {
			l_save = true;
			l_s += "&ids="+tab[v_tab].module[v_supid].id+"&us="+tab[v_tab].module[v_supid].uniq;
			fri_prof=tab[v_tab].id;
			fri_app_id=$p.app.user.id+':'+fri_prof+':'+tab[v_tab].module[v_supid].uniq;
			fri_delete_widget=true;
		}

		//call the XML saving function
		if (l_save && $p.app.user.id > 0)
		{
			//FRI:Delete PREFS from DB
			if (fri_delete_widget)
				fri_unregister_widget_prefs($p,fri_app_id);
		
			$p.ajax.call(posh["scr_config_place"],
				{
					'type':'execute',
					'variables':l_s,
					'alarm':false
				}
			);
		}
		// this action restart the protection counter
		if (tab[v_tab].lock != 0){$p.app.counter.reset(4);}

		$p.plugin.hook.launch('app.widgets.saveChanges.end');
	},
	/*
		Function: $p.app.widgets.refreshContent
                                Refresh module with "M" format
                                
		Parameters:

			v_id - module sequence ID
			v_tab - tab sequence ID
            
                     Returns:
                        
                                false
	*/
	refreshContent:function(v_id,v_tab,v_sharedmd5key)
	{ 
		if (v_tab==indef) v_tab=$p.app.tabs.sel;
        var l10n = tab[v_tab].module[v_id].l10n;
        
		$p.ajax.call(tab[v_tab].module[v_id].url,
			{
				'type':'load',
				'callback':
				{
					'function':$p.app.widgets.displayContent,
					'variables':
					{
						'widgetpos':v_id,
						'undef':0,
						'widgetid':tab[v_tab].module[v_id].uniq,
						'tabpos':v_tab,
						'tabid':tab[v_tab].id
					}
				},
				'source':'xml',
				'variables':"pid="+$p.app.user.id
                                +"&prof="+tab[v_tab].id
                                +"&p="+tab[v_tab].module[v_id].uniq
                                +"&format=M"
                                +"&plg="+__lang
                                +"&l10n="+l10n
                                +"&"+tab[v_tab].module[v_id].vars
                                +"&sharedmd5key="+v_sharedmd5key
                                +"&skin="+RODINSKIN
                                +"&widgetid="+tab[v_tab].module[v_id].uniq,
				'method':'POST'
			}
		);
		return false;
	},
	/*
		Function: $p.app.widgets.displayContent
                                Display module with "M" format
                                
                     Parameters:
                     
                                response - XML object 
                                vars (array) - variables (optionnal)
	*/
	displayContent:function(response,vars)
	{
		var l_tab=vars['tabpos'];
		if (response.getElementsByTagName("Module")[0])
		{
			var l_mod=response.getElementsByTagName("Module")[0];
			var l_script="";
			//refresh edit box only if current tab
			$p.app.widgets.param.fillEditBox(l_mod,vars['widgetid'],l_tab);
            //length of content
            //list contents
            var modid = vars['widgetpos'];
            var contentNumber=0;
            if ( l_mod.getElementsByTagName("Content").length > 1) {
                for (var i = 0; i< l_mod.getElementsByTagName("Content").length; i++) 
                {
                    var viewprop = $p.ajax.getProp(
                                                l_mod.getElementsByTagName("Content")[i],
                                                'view',
                                                'str',
                                                false,
                                                'home'
                                                );
                    if ( viewprop == tab[l_tab].module[modid].currentView) {
                        contentNumber=i;
                        break;
                    }
                }
            }
            
			if (l_mod.getElementsByTagName("Content")[contentNumber])
			{
				var l_contenttag=response.getElementsByTagName("Content")[contentNumber];
				var l_content=l_contenttag.firstChild.nodeValue;
				l_content=l_content.replace(/__MODULE_ID__/g,vars['widgetid']+"_"+vars['tabid']);
				if (l_content.indexOf("<script>")!=-1 
                        || l_content.indexOf('<script language="text/javascript">')!=-1 
                        || l_content.indexOf("<script language='text/javascript'>")!=-1
                    )
				{
					var l_pos1=l_content.indexOf("<script>");
					if (l_pos1==-1) l_pos1=l_content.indexOf("<script language='text/javascript'>");
					if (l_pos1==-1) l_pos1=l_content.indexOf('<script language="text/javascript">');
					l_pos1=l_content.indexOf(">",l_pos1);
					var l_pos2=l_content.indexOf("</script>",l_pos1);
					var l_script=l_content.substring((l_pos1+1),l_pos2);
				}
				$p.print("module"+tab[l_tab].id+"_"+vars['widgetid']+"_c",l_content);
				//launch script
				if (l_script!="")
				{
					try
					{
						eval(l_script);
					}
					catch(err)
					{
						$p.app.debug("widget script error :"+err.message+" / on script :"+l_script,"error");
					}
				}
			}
		}
	},
	/*
		Function: $p.app.widgets.createAll
                                Create all widgets of a personalized page
                                
		Parameters:
 
			v_tab - tab sequence ID
			v_display - set if widgets are displayed once created
	*/
	createAll:function(v_tab,v_display)
	{
		// Create all the modules
		var l_col=1;
		var l_ontop=0;
		// compute the max Uniq ID
		for (var i=0;i<tab[v_tab].module.length;i++)    {
			if (tab[v_tab].module[i].uniq>tab[v_tab].maxUniq) tab[v_tab].maxUniq=tab[v_tab].module[i].uniq;
		}

		//sort the modules depending on the show type
		if (tab[v_tab].showType==0) {
			tab[v_tab].module.sort(blocSort);
		}
		else    {
			tab[v_tab].module.sort(newspaperSort);
		}

		for (var i=0;i<tab[v_tab].module.length;i++)
		{
			while (tab[v_tab].module[i].col>l_col) l_col++;
			if (tab[v_tab].showType==0 || tab[v_tab].module[i].format!='R') 
            {
				tab[v_tab].module[i].l10n = __lang;
				tab[v_tab].module[i].create();
            }

			//display modules if not in the active tab
			if (v_tab!=$p.app.tabs.sel || v_display)    {
				tab[v_tab].module[i].show();
			}
			//if a module is added from outside of Portaneo
			if (tab[v_tab].module[i].pos==99) 
            {
                l_ontop=i;
            }

		}

		if (tab[v_tab].showType==1) {
			$p.app.widgets.rss.reader.init();
			$p.app.widgets.rss.reader.load();
			// if modules are aligned, a dummy div is added on the bottom of the column of widgets
			if (tab[v_tab].moduleAlign) tab[v_tab].cols[1].appendChild($p.app.widgets.endList());
		}
		else    {
			// if modules are aligned, a dummy div is added on the bottom of each column
			if (tab[v_tab].moduleAlign) {
				for (var i=1;i<=tab[v_tab].colnb;i++)
				{
					tab[v_tab].cols[i].appendChild($p.app.widgets.endList());
				}
			}
		}	

		//if a module is not placed, place it on top of first column (only if modules are aligned)
		if (l_ontop!=0 && tab[v_tab].moduleAlign)   {
			tab[v_tab].moduledefineWidgetContainer[l_ontop].placeonTop();
			var l_var=[];
			l_var[0]=tab[v_tab].module[l_ontop].col;
			$p.app.widgets.saveChanges(l_var,indef,v_tab);
		}
	},
 	/*
		Function: $p.app.widgets.fri_createAll4refine - for RODIN
                                Clone all widgets of a personalized page
																into a target page and place a selected
																widget (the refining widget) in the middle
		Parameters:
 
			v_tab - tab sequence ID
			v_display - set if widgets are displayed once created
	*/
	fri_createAll4refine:function(v_tab, v_tab_x, v_display)
	{
		// Create all the modules
		var l_col=1;
		var l_ontop=0;
		// compute the max Uniq ID / FRI omitted
		// compute the max Uniq ID
		for (var i=0;i<tab[v_tab].module.length;i++)    {
			if (tab[v_tab].module[i].uniq>tab[v_tab].maxUniq) tab[v_tab].maxUniq=tab[v_tab].module[i].uniq;
		}
		//sort the modules depending on the show type
		if (tab[v_tab].showType==0) {
			tab[v_tab].module.sort(blocSort);
		}
		else    {
			tab[v_tab].module.sort(newspaperSort);
		}

		for (var i=0;i<tab[v_tab].module.length;i++)
		{
			while (tab[v_tab].module[i].col>l_col) l_col++;
			if (tab[v_tab].showType==0 || tab[v_tab].module[i].format!='R') 
            {
								tab[v_tab_x].module[i].create();                
            }

			//display modules if not in the active tab
			if (v_tab_x!=$p.app.tabs.sel || v_display)    {
				tab[v_tab_x].module[i].show();
			}
			//if a module is added from outside of Portaneo
			if (tab[v_tab_x].module[i].pos==99) 
            {
                l_ontop=i;
            }

		}

		if (tab[v_tab].showType==1) {
			//$p.app.widgets.rss.reader.init();
			//$p.app.widgets.rss.reader.load();
			// if modules are aligned, a dummy div is added on the bottom of the column of widgets
			if (tab[v_tab_x].moduleAlign) tab[v_tab_x].cols[1].appendChild($p.app.widgets.endList());
		}
		else    {
			// if modules are aligned, a dummy div is added on the bottom of each column
			if (tab[v_tab_x].moduleAlign) {
				for (var i=1;i<=tab[v_tab_x].colnb;i++)
				{
					tab[v_tab_x].cols[i].appendChild($p.app.widgets.endList());
				}
			}
		}	

		//if a module is not placed, place it on top of first column (only if modules are aligned)
		if (l_ontop!=0 && tab[v_tab_x].moduleAlign)   {
			tab[v_tab_x].module[l_ontop].placeonTop();
			var l_var=[];
			l_var[0]=tab[v_tab_x].module[l_ontop].col;
			// $p.app.widgets.saveChanges(l_var,indef,v_tab); // FRI:Only temporary display
		}
	},
   /*
            function  : $p.app.widgets.maximize
            
                        maximize the widget, full-screen or full-portal if you prefer
        */				
    maximize:function (v_uniq,v_id) {
        var v_tab=$p.app.tabs.sel;
        var modSeqId = $p.app.widgets.uniqToId( v_uniq, v_tab);
        var nextModule = tab[$p.app.tabs.sel].module.length;
        tab[$p.app.tabs.sel].module[nextModule] = cloneObj(tab[$p.app.tabs.sel].module[modSeqId]);
        tab[$p.app.tabs.sel].module[nextModule].UniqParent=v_uniq;
        tab[$p.app.tabs.sel].module[nextModule].DBIdParent=modSeqId;
        tab[$p.app.tabs.sel].module[nextModule].SeqIdParent=v_id;
        tab[$p.app.tabs.sel].module[nextModule].uniq=v_uniq+10000;
        tab[$p.app.tabs.sel].module[nextModule].classname='canvas';

        tab[$p.app.tabs.sel].module[nextModule].ModulePositionId=nextModule;
        var tab_id = tab[v_tab].id;
        tab[$p.app.tabs.sel].module[nextModule].currentView="canvas";
        var modid = tab[$p.app.tabs.sel].module[nextModule].id;
        tab[$p.app.tabs.sel].module[nextModule].canvas = $('canvas'+tab_id);
        if (tab[$p.app.tabs.sel].module[modSeqId].newUrl) {
					//alert('FRI maximized 1 newurl='+tab[$p.app.tabs.sel].module[modSeqId].newUrl);

            tab[$p.app.tabs.sel].module[nextModule].url = tab[$p.app.tabs.sel].module[modSeqId].newUrl;
            delete tab[$p.app.tabs.sel].module[modSeqId].newUrl;
            delete tab[$p.app.tabs.sel].module[nextModule].newUrl;
        }
        if (tab[$p.app.tabs.sel].module[modSeqId].newFormat) {
					//alert('FRI maximized 2 format='+tab[$p.app.tabs.sel].module[modSeqId].newFormat);
            tab[$p.app.tabs.sel].module[nextModule].format = tab[$p.app.tabs.sel].module[modSeqId].newFormat;
            delete tab[$p.app.tabs.sel].module[modSeqId].newFormat;
            delete tab[$p.app.tabs.sel].module[nextModule].newFormat;
        }        
        if ($('home'+tab_id)) 
		{
            $('home'+tab_id ).style.display="none";
            $('canvas'+tab_id ).style.display="block";
            tab[$p.app.tabs.sel].module[nextModule].create();     
            tab[$p.app.tabs.sel].module[nextModule].show('canvas'); 
            tab[$p.app.tabs.sel].module[nextModule].hdrColor(indef,indef,'canvas');
            $p.show("hmod"+tab[v_tab].id+"_"+tab[$p.app.tabs.sel].module[nextModule].uniq,"block");
        }
                return false;
    },
	/* function FRI : $p.app.widgets.openWidgetInNewTab */
	/* Instead of maximize, open with show-url the same widget content */
	
	openWidgetInNewTab:function(widget_instance,widget_id,tab_id)
	{	
		var myIframeId = 'modfram'+tab_id+'_'+widget_instance;
		//alert('FRI: openWidgetInNewTab:function('+widget_instance+','+widget_id+','+tab_id+') myIframeId='+myIframeId);
		var iframe=document.getElementById(	myIframeId );
		if (iframe)
		{
			old_querystring=parseUri(iframe.src).query;
			//alert('FRI: old_querystring: '+old_querystring);
			var x=1; // dummy
			var qs = new Querystring(old_querystring);
			qs.set('show','RDW_full');
			qs.set('uncache','0'); //No caching div to be recloed
			qs.set('go','0'); //no query to be reperformed
			
				var qe=qs.get('qe','');
				var q =qs.get('q','');
				var qq;
				if (q!='') qq=q;
				else (qq=qe);
			
				//alert('FRI: qe='+qe+' q='+q+' -> qq='+qq);
			qs.set('q',qq); //no query to be reperformed but query info
			qs.set('qe',''); //no query to be reperformed but query info
			qs.set('sr',''); //No sr (json) results to process
			qs.set('_w',window.innerWidth); //how large is the browser windog
			qs.set('_h',window.innerHeight); //how large is the browser windog
			var uri= parseUri(iframe.src).protocol + '://' + parseUri(iframe.src).host + parseUri(iframe.src).path;
			var newuri=uri+'?'+qs.toString();
			olduri=uri+'?'+old_querystring;
			//alert('FRI: old : '+olduri + '\nnew: '+newuri );
	
			//iframe.src=newuri; // and reload but in new tab!
			window.open(newuri,'_blank');
		
		} 
		else alert ('FRI: System error: no iframe found to frameid='+myIframeId);
		
	
	}, 
	
    /*
                Function: $p.app.widgets.homeView
                
                        returns to normal view
                        
                  parameters:

                            v_id - id of module
        */
    homeView: function (v_id) {
        var v_tab=$p.app.tabs.sel;
        var tab_id = tab[v_tab].id;
        var ModulePosition = tab[$p.app.tabs.sel].module[v_id].ModulePositionId;
        tab[$p.app.tabs.sel].module[v_id].currentView="home";
        var l_id_parent =$p.app.widgets.uniqToId(tab[v_tab].module[v_id].UniqParent);
        $('canvas'+tab_id).innerHTML='';
        $('canvas'+tab_id).style.display="none";    
        tab[$p.app.tabs.sel].module.pop(); //remove item canvas from module array
     //  tab[$p.app.tabs.sel].module[l_id_parent].create(indef,1);
        tab[$p.app.tabs.sel].module[l_id_parent].refresh();
        tab[$p.app.tabs.sel].module[l_id_parent].hdrColor( indef,indef,'module');
        tab[$p.app.tabs.sel].module[l_id_parent].setName(indef);

        $('home'+tab_id).style.display="block";   
        return false;   
    },
	/*
		Function: $p.app.widgets.suppress
                                Remove a widget
                                
		Parameters:

			v_uniq - module uniq ID
			v_confirm (boolean) - true=ask for module removal confirmation
            
                      Returns :
                      
                                true or false
	*/
	suppress: function(v_uniq,v_confirm)
	{
		$p.plugin.hook.launch('app.widgets.suppress.start');

		var l_id = $p.app.widgets.uniqToId(v_uniq),
            l_ret = false;
		// if module to suppress is not yet included in the page
		if (showNewMod 
            && l_id == (tab[$p.app.tabs.sel].maxUniq)) {
                $p.app.widgets.close();
		}
		else
        {
			var l_response = v_confirm ? confirm(lg("msgModSup")) : 1;
			if (l_response == 1)  {
				//suppress linked RSS feeds
				if (tab[$p.app.tabs.sel].module[l_id].format == 'R') $p.app.widgets.supFeed(l_id);

				// RODIN unregister before removal
				var iframeObj = document.getElementById("modfram" + $p.app.tabs.selId + "_" + tab[$p.app.tabs.sel].module[l_id].uniq);
				fri_unregister_tab_iframe($p.app.tabs.selId, iframeObj);
				
				$p.ajax.call('../../app/tests/LoggerResponder.php?action=3&id=' + tab[$p.app.tabs.sel].module[l_id].id +
						'&name=' + get_datasource_name(tab[$p.app.tabs.sel].module[l_id].url) + '&tab=' + tab[$p.app.tabs.sel].label, {'type':'load'});
				
				//virtually change the module position
				for(var i = 0;i < tab[$p.app.tabs.sel].module.length;i++)
				{
					if (tab[$p.app.tabs.sel].module[i].newcol == tab[$p.app.tabs.sel].module[l_id].newcol 
                        && tab[$p.app.tabs.sel].module[i].newpos > tab[$p.app.tabs.sel].module[l_id].newpos){
                            tab[$p.app.tabs.sel].module[i].newpos -= 1;
                    }
					if (tab[$p.app.tabs.sel].module[i].newposj > tab[$p.app.tabs.sel].module[l_id].newposj){
                        tab[$p.app.tabs.sel].module[i].newposj -= 1;
                    }
				}

				//destruct the widget if it is visible on screen
				if (tab[$p.app.tabs.sel].showType == 0 
                    || tab[$p.app.tabs.sel].module[l_id].format != 'R') {
                        tab[$p.app.tabs.sel].module[l_id].destruct();
                }
				//register the suppression
				var l_vars = [];
				if (tab[$p.app.tabs.sel].showType == 0)   {
					l_vars[0]=tab[$p.app.tabs.sel].module[l_id].col;
                }
                else
                {
                    l_vars[0]=1;
				}
				$p.app.widgets.saveChanges(l_vars,l_id);

				tab[$p.app.tabs.sel].module.splice(l_id,1);
				
				l_ret = true;
			}
		}

		$p.plugin.hook.launch('app.widgets.suppress.end');

		return l_ret;
	},
	/*
		Function: $p.app.widgets.supFeed
                                Remove the feeds information related to a removed RSS module
		
                     Parameters:

			v_uniq - module uniq ID
			v_tab - tab sequence ID
	*/
	supFeed: function(v_uniq,v_tab)
	{
		if (v_tab == indef) v_tab = $p.app.tabs.sel;
		delete tab[v_tab].feeds[v_uniq];
        if (tab[v_tab].feeds.length > 0) {
            tab[v_tab].feeds.length -= 1
        };
        
        /*
        var i=0;
		while (tab[v_tab].feeds[i])
		{
			if (tab[v_tab].feeds[i].modUniq==v_uniq)    {
				tab[v_tab].feeds.splice(i,1);
			}
			else    {
				i++;
			}
		}
        */
	},
	/*
		Function: $p.app.widgets.endList
                                Add a hidden module (used at the bottom of each column)
                                
                     Returns:
                                
                                Element
	*/
	endList:function()
	{
		var l_obj=null;
		l_obj=new Element('div',{ 'class':'necessary'} );
		// necessary to add an empty column
		l_obj.set('html',$p.img("s.gif",180,1));
		l_obj.setStyle('width', '100%'); 
		return l_obj;
	},
	/*
		Function: $p.app.widgets.showHdr
                                Display/hide the modules headers
                                
		Parameters:

			 tab sequence ID
	*/
	showHdr:function(v_tab)
	{
        
		if (v_tab==indef) v_tab=$p.app.tabs.sel;
		if (tab[v_tab].controls=='Y')   {
			//changeStyle("hmod","display","block");
			for (var i=0;i<tab[v_tab].module.length;i++)
			{
				$p.show("hmod"+tab[v_tab].id+"_"+tab[v_tab].module[i].uniq,"block");
			}
		}
		else    {
			//if (!IE){$p.show("modules","none");} //FF bug fix
			//changeStyle("hmod","display","none");
			for (var i=0;i<tab[v_tab].module.length;i++)
			{
				$p.show("hmod"+tab[v_tab].id+"_"+tab[v_tab].module[i].uniq,"none");
			}
			//if (!IE) $p.show("modules","block"); //FF bug fix
			$p.app.pages.resize();
		}
	},
	/*
		Function: $p.app.widgets.switchHdr
                                Change the modules headers display status
	*/
	switchHdr:function()
	{
		tab[$p.app.tabs.sel].controls=(tab[$p.app.tabs.sel].controls=='Y')?'N':'Y';
		$p.app.widgets.showHdr();
	},
	/*
		Function: $p.app.widgets.changeTab
                                Move a module from one tab to another
                                
		Parameters:

			v_desttab - destination tab sequence ID
			v_uniq - module uniq ID
	*/
	changeTab:function(v_desttab,v_uniq)
	{
		$p.ajax.call(posh["scr_addmodule_othertab"],
			{
				'type':'execute',
				'variables':"src="+tab[$p.app.tabs.sel].id+"&dest="+tab[v_desttab].id+"&tabdest="+v_desttab+"&uniq="+v_uniq,
				'alarm':false,
				'forceExecution':false,
				'callback':
				{
					'function':$p.app.widgets.addInNewTab
				}
			}
		);
		//update rss unread number (only in source tab, the other is reload)
		var l_modId=$p.app.widgets.uniqToId(v_uniq);
		if (tab[$p.app.tabs.sel].module[l_modId].format=="R")
		{
			tab[$p.app.tabs.sel].module[l_modId].nbunread=0;
			$p.app.widgets.rss.pageUnread($p.app.tabs.sel);
		}
	},
	/*
		Function: $p.app.widgets.insertInTab
                                Insert a new module in a tab
                                
		Parameters:

			v_desttab - destination tab sequence ID
			v_id - module DB ID
      v_vars - variables
	*/
	insertInTab:function(v_desttab,v_id,v_vars)
	{
		//get feed id
		if (v_vars==undefined) l_fid=0; //FRI
		else
		{
			var l_fid=$p.string.getVar(v_vars,'pfid');
			if (l_fid=='') l_fid=0;
		}
		$p.ajax.call(posh["scr_addmodule_intab"],
			{
				'type':'execute',
				'variables':"dest="+tab[v_desttab].id+"&tabdest="+v_desttab+"&id="+v_id+"&fid="+l_fid+"&vars="+$p.string.esc(v_vars),
				'alarm':false,
				'forceExecution':true,
				'callback':
				{
					'function':$p.app.widgets.addInNewTab
				}
			}
		);
		$p.app.widgets.close(); // FRI
	},	
	/*
		Function: $p.app.widgets.fri_insertInTab
                                Insert a new module in a tab
                                
		Parameters:

			v_desttab - destination tab sequence ID
			v_id - module DB ID
      v_vars - variables
	*/
	fri_insertInTab:function(v_orig_tab_id,widget,v_desttab_id,v_desttab_db_id)
	{
		
		v_id=widget.id;
		v_vars=widget.vars;
		//get feed id
		if (v_vars==undefined) l_fid=0; //FRI
		else
		{
			var l_fid=$p.string.getVar(v_vars,'pfid');
			if (l_fid=='') l_fid=0;
		}
		$p.ajax.call(posh["scr_addmodule_intab"], //add in DB through PHP script
			{
				'type':'execute',
				'variables':"orig="+v_orig_tab_id+"&profile_id="+v_desttab_db_id+"&dest="+tab[v_desttab_id].id+"&tabdest="+v_desttab_id+"&id="+v_id+"&fid="+l_fid+"&vars="+$p.string.esc(v_vars),
				'alarm':false,
				'forceExecution':true,
				'callback':
				{
					'function':$p.app.widgets.addInNewTab		
				}
			}
		);
		$p.app.widgets.close(); // FRI
	},
	/*
		Function: $p.app.widgets.addInNewTab
                                Reset destination tab when adding a module in another tab
                                
		Parameters:

			 called XML file return string
	*/
	addInNewTab:function(v_ret)
	{
		if (v_ret==indef) return;
		var v_param=v_ret.split(/_/);
		//add module in new tab
		if (tab[v_param[2]].isLoaded==true) {
			tab[v_param[2]].isLoaded=indef;
			$p.print("tabextra"+v_param[2],"");
			//to be done : create twin module, place on bottom, change nbunread,...
		}
		//suppress module from old tab
		if (v_param[0]!="x") $p.app.widgets.suppress(v_param[0]);
	},
	/*
		Function: $p.app.widgets.changeName
                                Change module name
                                
		Parameters:

			v_id - module sequence ID
			v_name - new module name
			v_tab - tab sequence ID of the module
	*/
	changeName: function(v_id,v_name,v_tab)
	{
		if (v_tab == indef) v_tab = $p.app.tabs.sel;
		
		//FRI
		var mod = tab[v_tab].module[v_id];
		var picurl = mod.link; // new name
		var widgeturl= mod.url;
		v_name=$p.app.fri_getpicturedef(v_name,widgeturl,picurl); //superseed widgetname with its graphics in the link field
		
		//alert('FRI changeName now: '+ v_id+','+v_name+','+v_tab+' an '+tab[v_tab].module[v_id].url);
		
		
        
		if (v_name != indef) tab[v_tab].module[v_id].name = v_name;
        
		var l_s = (__showModuleTitle?tab[v_tab].module[v_id].name : "");

		if (tab[v_tab].module[v_id].nbunread != 0)
		{
			l_s = "<a class='rssstatus' title='"+lg('unread')+"' href='#' onmousedown='return $p.app.widgets.rss.readAll("+v_tab+","+v_id+")'>"
				+ "("+tab[v_tab].module[v_id].nbunread+")"
				+ "</a> "
				+ l_s;
		}

		$p.print("module"+tab[v_tab].id+"_"+tab[v_tab].module[v_id].uniq+"_h",l_s);
	},
	/*
		Function: $p.app.widgets.changeIcon
                                Change module Icon
                                
		Parameters:

			v_id - module sequence ID
			v_icon - module icon
			v_tab - tab sequence ID of the module
	*/
	changeIcon:function(v_id,v_icon,v_tab)
	{ 
		var l_icon = "";
      
		if (!__showicon)return false;
		if (v_tab==indef) v_tab=$p.app.tabs.sel;
		if (v_icon!=indef)  { 
			l_icon = v_icon;
		}
		else    { 
			if ( tab[v_tab].module[v_id].icon.substr(0,2)==".." ) {
				l_icon = tab[v_tab].module[v_id].icon;
			} else {
				l_icon = _dirImg+tab[v_tab].module[v_id].icon;
			}
		} 
		$("module"+tab[v_tab].id+"_"+tab[v_tab].module[v_id].uniq+"_icon").src=l_icon;
	},	
	/*
		Function: $p.app.widgets.initMove
                                Init modules move
                                
		Parameters:

			 v_tab - tab sequence ID of the modules
	*/
	initMove:function(v_tab)
	{
		if(tab[v_tab].moveIsInit)return;
		tab[v_tab].moveIsInit=true;
		for(var l_item=0;l_item<tab[v_tab].root.childNodes.length;l_item++)
		{
			var l_mod=tab[v_tab].root.childNodes[l_item];
 			$p.app.widgets.move.set(l_mod,"module",tab[v_tab].root,"multidim",true,v_tab);
		}
	},
	/*
		Function: $p.app.widgets.enableMoving
                                 Display and initialize bars for widget move and configuration
                                 
		Parameters:

			 v_tab - tab array position where widgets are
	*/
	enableMoving:function(v_tab)
	{
		if (v_tab==indef) v_tab=$p.app.tabs.sel;

		if (tab[v_tab].controls=='Y')   {
			$p.app.widgets.showHdr(v_tab);
		}
		//$p.app.pages.displayItems(v_tab); ??
		tab[v_tab].moveIsInit=false;
		useMod=true;
		$p.app.pages.displayItems(v_tab);
	},
	/*
		Function: $p.app.widgets.align
                                Manage modules alignment
                                
		Parameters:

			 v_align (boolean) - true if modules are aligned
	*/
	align:function(v_align)
	{
		var l_var=[];
		if (v_align)    {
			for (var i=0;i<tab[$p.app.tabs.sel].module.length;i++)
			{
                
				tab[$p.app.tabs.sel].module[i].newx=(20+(i*30));
				tab[$p.app.tabs.sel].module[i].newy=(120+(i*30));
			}
		}
		//save new position
		$p.app.widgets.saveChanges(l_var);

		//regenerate the page
		tab[$p.app.tabs.sel].moduleAlign=!v_align;
		//pages.open();
		$p.app.pages.clean(tab[$p.app.tabs.sel].root);
		if (v_align)    {
			for (var i=0;i<tab[$p.app.tabs.sel].module.length;i++)
			{
				tab[$p.app.tabs.sel].module[i].x=tab[$p.app.tabs.sel].module[i].newx;
				tab[$p.app.tabs.sel].module[i].y=tab[$p.app.tabs.sel].module[i].newy;
			}
		}
		$p.app.pages.init($p.app.tabs.sel);
		$p.app.pages.computeFooterPosition();
	},
	/*
		Function: $p.app.widgets.place
                                Place all the modules of the selected tab
                                
		Parameters:

			v_tab : selected tab sequence ID
	*/
	place:function(v_tab)
	{
		if (v_tab==indef) v_tab=$p.app.tabs.sel;
		if (v_tab==-1) return;
		if (!tab[v_tab].moduleAlign)    {
			for (var i=0;i<tab[v_tab].module.length;i++)
			{
				var l_obj=$("module"+tab[v_tab].id+"_"+tab[v_tab].module[i].uniq);
                if (l_obj != null)
                {
                    l_obj.setStyle("left",(parseInt(tab[v_tab].module[i].x,10)+parseInt(widgetDecalX,10))+"px");
                    l_obj.setStyle("top",(parseInt(tab[v_tab].module[i].y,10)+parseInt(widgetDecalY,10))+"px");
                }
			}
			$p.app.pages.computeFooterPosition();
		}
	},
	/**
	 * Function $p.app.widgets.isOpenInTab(id)
	 * Checks if a module/widget ID is already present in the current tab 
	 */
	isOpenInTab:function(widget_id) {
		var modulesCount = tab[$p.app.tabs.sel].module.length;
		
		var duplicatedModule = false;
		for (var i=0; i<modulesCount; i++) {
			var tempModule = tab[$p.app.tabs.sel].module[i];
			
			if (tempModule.id === widget_id) {
				duplicatedModule = get_datasource_name(tempModule.url);
				break;
			}
		}
		
		return duplicatedModule;
	},
	/*
		Function: $p.app.widgets.open
                                Load a new module
                                
		Parameters:

			v_id: module DB ID
			v_vars - module parameters
			v_type - page type (portal,...)
			v_useOverview - ???
			v_waitForDisplay - ???
                                v_position - 
                                v_target - 
            
                     Returns:
                     
                                false;
	*/
	open:function(v_id,v_vars,v_type,v_secured,v_useOverview,
                  v_waitForDisplay,v_position,v_target)
	{
		var duplicatedName = $p.app.widgets.isOpenInTab(v_id);
		
		if (duplicatedName) {
			alert(lg("lblDuplicatedWidget", duplicatedName));
		} else {		
			if (v_vars==indef) v_vars='';
			if (v_position==indef) v_position='top';
			if (v_type==indef) v_type=($p.app.tabs.sel==-1 || tab[$p.app.tabs.sel].type!=1)?'uniq':'portal';
			if (v_useOverview==indef) v_useOverview=true;
			if (v_waitForDisplay==indef) v_waitForDisplay=true;
			if (v_secured)  {
				var l_url=posh["xmlitem"]+'?id='+v_id+'&rand='+rand;
			}
			else    {
				var l_url='../cache/item_'+v_id+'.xml?rand='+rand;
			}
			$p.ajax.call(l_url,
					{
				'type':'load',
				'callback':
				{
					'function':$p.app.widgets.displayWidgetDescription, 
					'variables':
					{
						'id':v_id,
						'vars':v_vars,
						'type':v_type,
						'useOverview':v_useOverview,
						'position':v_position,
						'target':v_target
					}
				},
				'asynchron':v_waitForDisplay
					}
			);
		}
		return false;
	},
	/*
			Function: $p.app.widgets.fri_displayMod
                                            Display a new module direct in the page, or its overview
                                            
                                Parameters: 
                                
                                           response - XML object
                                           vars (array) - variables (optionnal)
	*/
	fri_displayMod: function(response,vars)
	{
		var l_s = "",
            l_result,
            l_newId;
		if (response.getElementsByTagName("item")[0])
		{
			l_result = response.getElementsByTagName("item")[0];
			var l_size      = $p.ajax.getVal(l_result,"size","int",false,100);
			var l_minwidth  = $p.ajax.getVal(l_result,"minwidth","int",false,180);
			var l_sizable   = $p.ajax.getVal(l_result,"sizable","int",false,1);
			var l_name      = $p.ajax.getVal(l_result,"name","str",false,"--");
			var l_format    = $p.ajax.getVal(l_result,"format","str",false,"I");
			var l_website   = $p.ajax.getVal(l_result,"website","str",false,"");
			var l_nbvars    = $p.ajax.getVal(l_result,"nbvars","int",false,0);
			var l_usereader = $p.ajax.getVal(l_result,"usereader","int",false,tab[$p.app.tabs.sel].usereader);
			var l_editor    = $p.ajax.getVal(l_result,"editor","str",false,"");
			var l_editorid  = $p.ajax.getVal(l_result,"editor_id","int",false,0);
            var l_views     = $p.ajax.getVal(l_result,"views","str",false,'home');
            var l_icon      = $p.ajax.getVal(l_result,"icon","str",false,indef);
            var l_l10n      = $p.ajax.getVal(l_result,"l10n","str",false,'');

			if (vars['vars'] == '')
                vars['vars'] = $p.ajax.getVal(l_result,"var","str",false,"");

			//if (showNewMod) $p.app.widgets.close(vars['type']);

			l_newId = tab[$p.app.tabs.sel].module.length;

			var l_url = $p.ajax.getVal(l_result,"url","str",false,"");
			//show overview if configured in admin panel

			if (!standaloneMode
                && (vars['type'] == "uniq" 
                    || $p.app.tabs.sel == -1 
    				|| tab[$p.app.tabs.sel].type != 1
                    || (vars['useOverview'] 
                        && __useoverview 
                        && tab[$p.app.tabs.sel].moduleAlign))) 
			{
				  var l_desc = $p.ajax.getVal(l_result,"description","str",false,"");
					tab[$p.app.tabs.sel].maxUniq++;
	/*
	    Parameters:
       
			col (int) - column of the module  
			pos (int) - position of the column in a column 
                            posj   (int) -  Position of widget in the page
			height (int) - height of the module (not application for modules with type R & M) 
			id (int) - id of the module (int)
			link (str) - link of the module provider  - data form database is filed site
			name (str) - name of the module - str
                            vars (str) - variables
			minModSize (int) - minimal size of the module 
			updModSize (boolean)  - is the module resizable? 
			size  (int)  - ? value form database is minmodsize
			url (str) - ?
			x (int) - left position (if not aligned) 
			y (int) - top position (if not aligned) 
			uniq_db (int) - uniq id of the module 
			format (str) - type of the module (R=rss, M=included module, I= framed module,U=Url)
			nbvars (int) - number of users configuration variables used
			tab  (int)  - id of the tab where the module is (int)
			blocked (boolean) - is the module blocked in the page
			minimized (boolean) - display status of the module (true=minimized, false=normal)
			usereader (boolean) - for RSS modules, is the rss reader is used to read articles
			autorefresh (boolean) - is the module refreshed every x minutes ?
			icon (str) - icon of the module
			isLoaded - loading status of the module (indef=not loaded, false=loading, true=loaded)
			header (str) - HTML header of the module
			footer (str) - HTML footer of the module
			auth (str) - for RSS authentified feeds
                        views (str) - views (home or canvas) canvas for full-screen (full-portal)
                        l10n - l10n parameters, lang comma separated
	*/					
					tab[$p.app.tabs.sel].module[l_newId]=new $p.app.widgets.object(
                                                    1, //col
                                                    0,  //pos
                                                    tab[$p.app.tabs.sel].maxUniq, //position in module array alias posj
                                                    l_size, //height
                                                    vars['id'],
                                                    l_website, //url of website
                                                    l_name,
                                                    vars['vars'],   //variables 
                                                    l_minwidth, 
                                                    l_sizable,
                                                    300,        //minmodsize
                                                    l_url,      //url of widget : xml_getwidget, p_rss ,external url,..
                                                    0,        //x
                                                    0,        //y
                                                    tab[$p.app.tabs.sel].maxUniq,
                                                    l_format,   // RM I U see docs $p.app.widgets.object
                                                    l_nbvars,   //number of variables
                                                    tab[$p.app.tabs.sel].id,    //tab_id of widget 
                                                    1,          //blocked
                                                    0,          //minimized
                                                    l_usereader, //usereader,
                                                    0,  //autorefresh
                                                    l_icon,     //icon of the moduke
                                                    false, //is loaded status of the module (indef=not loaded, false=loading, true=loaded)
                                                    indef, //header
                                                    indef, //footer
                                                    indef, //auth  for RSS authentified feeds
                                                    l_views,  //views (home or canvas) canvas for full-screen (full-portal)
                                                    l_l10n    //lang parameters for l10n widgets
                                                    );
              tab[$p.app.tabs.sel].module[l_newId].PositioninTab = l_newId;
    					
							tab[$p.app.tabs.sel].module[l_newId].fri_create(l_size);
							//tab[$p.app.tabs.sel].module[tab[$p.app.tabs.sel].module.length-1].placeinCol(1);
    					$p.app.widgets.showHdr();
							tab[$p.app.tabs.sel].module[l_newId].show();
			}
			
		}
		else    {
			$p.app.debug("Cache is not generated for this module","error");
		}
	},
	/**
	 * Show the description of a widget over a cache.
	 * Function: $p.app.widgets.displayWidgetDescription
	 * Parameters:
	 * @param response - XML object
	 * @param vars (array) - variables (optionnal)
	 */
	displayWidgetDescription: function(response,vars) {
		if (response.getElementsByTagName("item")[0]) {
			var l_result = response.getElementsByTagName("item")[0];
			
			var l_size      = $p.ajax.getVal(l_result,"size","int",false,100);
			var l_minwidth  = $p.ajax.getVal(l_result,"minwidth","int",false,180);
			var l_sizable   = $p.ajax.getVal(l_result,"sizable","int",false,1);
			var l_name      = $p.ajax.getVal(l_result,"name","str",false,"--");
			var l_format    = $p.ajax.getVal(l_result,"format","str",false,"I");
			var l_website   = $p.ajax.getVal(l_result,"website","str",false,"");
			var l_nbvars    = $p.ajax.getVal(l_result,"nbvars","int",false,0);
			var l_usereader = $p.ajax.getVal(l_result,"usereader","int",false,tab[$p.app.tabs.sel].usereader);
			var l_editor    = $p.ajax.getVal(l_result,"editor","str",false,"");
			var l_editorid  = $p.ajax.getVal(l_result,"editor_id","int",false,0);
			var l_views     = $p.ajax.getVal(l_result,"views","str",false,'home');
			var l_icon      = $p.ajax.getVal(l_result,"icon","str",false,indef);
			var l_l10n      = $p.ajax.getVal(l_result,"l10n","str",false,'');
			
			if (vars['vars'] == '')
				vars['vars'] = $p.ajax.getVal(l_result,"var","str",false,"");
			
			if (showNewMod)
				$p.app.widgets.close(vars['type']);
			
			var l_newId = tab[$p.app.tabs.sel].module.length;
			var l_url = $p.ajax.getVal(l_result,"url","str",false,"");
	
			if (vars['useOverview']) { // This happens on click
				var holderDivision = jQuery('<div id="newWidgetHolder"></div>');
				holderDivision.css("text-align", "center");
				
//				var widgetLargeIcon = jQuery('<img />', {
//					'src': l_website
//				});
//				holderDivision.append(widgetLargeIcon);
				
				var previewContainer = jQuery('<div id="previewContainer"></div>');
				previewContainer.css("width", "320px");
				previewContainer.css("height", "350px");
				previewContainer.css('margin-left', 'auto');
				previewContainer.css('margin-right', 'auto');
				previewContainer.css("position", "relative");
								
				var widgetPreview = jQuery('<div id="col0"></div>');
				widgetPreview.css("width", "100%");
				widgetPreview.css("height", "100%");
				widgetPreview.css("position", "absolute");
				widgetPreview.css("top", "0");
				widgetPreview.css("left", "0");
				previewContainer.append(widgetPreview);
				
				var invisibleCacheOverWidgetPreview = jQuery('<div id="widgetPreviewCache"></div>');
				invisibleCacheOverWidgetPreview.css("width", "100%");
				invisibleCacheOverWidgetPreview.css("height", "100%");
				invisibleCacheOverWidgetPreview.css("position", "absolute");
				invisibleCacheOverWidgetPreview.css("top", "0");
				invisibleCacheOverWidgetPreview.css("left", "0");
				previewContainer.append(invisibleCacheOverWidgetPreview);
				
				holderDivision.append(previewContainer);
				
				var widgetDescription = $p.ajax.getVal(l_result,"description","str",false,"");
				if (widgetDescription != '') {
					var widgetDescriptionParagraph = jQuery('<p />');
					widgetDescriptionParagraph.text(widgetDescription);
					holderDivision.append(widgetDescriptionParagraph);
				}
				
				var addWidgetButton = jQuery('<button />', {
					'onClick': 'tab[$p.app.tabs.sel].module[tab[$p.app.tabs.sel].module.length-1].placeinCol(1);$p.app.widgets.close();$p.app.popup.hide();' +
						"$p.ajax.call('../../app/tests/LoggerResponder.php?action=2&id=" + vars['id'] +
						"&name=" + l_name + "&tab=" + tab[$p.app.tabs.sel].label + "', {\"type\":\"load\"});"
				});
				
				addWidgetButton.css('margin-top', '30px');
				addWidgetButton.text(lg("lblBtnModAdd"));
				holderDivision.append(addWidgetButton);
				
				var main = jQuery('<div></div>');
				main.append(holderDivision);
				
				$p.app.popup.show(main.html(),$p.max(l_minwidth+20,500),indef,l_name,true,"$p.app.widgets.close('"+vars['type']+"')");
				
				tab[$p.app.tabs.sel].maxUniq++;
				
				tab[$p.app.tabs.sel].cols[0]=$("col0");
				tab[$p.app.tabs.sel].module[l_newId]=new $p.app.widgets.object(
						0, 0, 0, l_size, vars['id'], l_website, l_name,	vars['vars'], 
						l_minwidth, l_sizable, 400, l_url, 150, 150, tab[$p.app.tabs.sel].maxUniq,
						l_format, l_nbvars, tab[$p.app.tabs.sel].id, 0, 0, l_usereader,	0, l_icon, false,
						indef, indef, indef, l_views, l_l10n
				);
				tab[$p.app.tabs.sel].module[l_newId].PositioninTab = l_newId;
				tab[$p.app.tabs.sel].module[l_newId].create(l_size);
				tab[$p.app.tabs.sel].module[l_newId].show();
				$p.app.widgets.showHdr();
				
				showNewMod=true;
			} else { // This happens on drag and drop
				tab[$p.app.tabs.sel].maxUniq++;
				tab[$p.app.tabs.sel].module[l_newId]=new $p.app.widgets.object(
						-1, 0, 0, l_size, vars['id'], l_website, l_name, vars['vars'],
						l_minwidth, l_sizable, 400, l_url, 150, 150, tab[$p.app.tabs.sel].maxUniq,
						l_format, l_nbvars, tab[$p.app.tabs.sel].id, 0, 0, 0, 0, l_icon, false,
						indef, indef, indef, l_views, l_l10n
				);
				tab[$p.app.tabs.sel].module[l_newId].PositioninTab = l_newId;                                        
				tab[$p.app.tabs.sel].module[l_newId].create( l_size );
				tab[$p.app.tabs.sel].module[l_newId].show();
				
				tab[$p.app.tabs.sel].module[l_newId].placeinCol(1, indef, 'top');
			}
		}
	},
	/*
			Function: $p.app.widgets.displayMod
                                            Display a new module in the page, or its overview
                                            
                                Parameters: 
                                
                                           response - XML object
                                           vars (array) - variables (optionnal)
	*/
	displayMod: function(response,vars)
	{
		var l_s = "",
            l_result,
            l_newId;
		if (response.getElementsByTagName("item")[0])
		{
			l_result = response.getElementsByTagName("item")[0];
			var l_size      = $p.ajax.getVal(l_result,"size","int",false,100);
			var l_minwidth  = $p.ajax.getVal(l_result,"minwidth","int",false,180);
			var l_sizable   = $p.ajax.getVal(l_result,"sizable","int",false,1);
			var l_name      = $p.ajax.getVal(l_result,"name","str",false,"--");
			var l_format    = $p.ajax.getVal(l_result,"format","str",false,"I");
			var l_website   = $p.ajax.getVal(l_result,"website","str",false,"");
			var l_nbvars    = $p.ajax.getVal(l_result,"nbvars","int",false,0);
			var l_usereader = $p.ajax.getVal(l_result,"usereader","int",false,tab[$p.app.tabs.sel].usereader);
			var l_editor    = $p.ajax.getVal(l_result,"editor","str",false,"");
			var l_editorid  = $p.ajax.getVal(l_result,"editor_id","int",false,0);
            var l_views     = $p.ajax.getVal(l_result,"views","str",false,'home');
            var l_icon      = $p.ajax.getVal(l_result,"icon","str",false,indef);
            var l_l10n      = $p.ajax.getVal(l_result,"l10n","str",false,'');

			if (vars['vars'] == '')
                vars['vars'] = $p.ajax.getVal(l_result,"var","str",false,"");

			if (showNewMod) $p.app.widgets.close(vars['type']);

			l_newId = tab[$p.app.tabs.sel].module.length;

			var l_url = $p.ajax.getVal(l_result,"url","str",false,"");
			//show overview if configured in admin panel

			if (!standaloneMode
                && (vars['type'] == "uniq" 
                    || $p.app.tabs.sel == -1 
    				|| tab[$p.app.tabs.sel].type != 1
                    || (vars['useOverview'] 
                        && __useoverview 
                        && tab[$p.app.tabs.sel].moduleAlign)))   {

                    l_s += '<br />'
                        + '<center>'
                        + '<div id="col0" style="width:'+$p.max(l_minwidth,400)+'px"></div>'
                        + '</center><br />';

    				if (vars['type']=="portal") {
    					l_s += "<center>"
                            + "<input class='btn' type='button' value='"+lg("lblBtnModAdd")+"' onclick='tab[$p.app.tabs.sel].module[tab[$p.app.tabs.sel].module.length-1].placeinCol(1);$p.app.widgets.close();$p.app.popup.hide();' />"
                            + "</center>";
    				}
    				else    {
    					l_s += '<b><i>'+lg('moveToPlaceInTabs')+'</i></b>';
    				}
    				var l_desc = $p.ajax.getVal(l_result,"description","str",false,"");
    				if (l_desc != '')
                        l_s+='<br /><br /><b>'+lg('description')+'</b><br />'+l_desc;

    				if (l_editorid != 0 && vars['id'] != 86 && l_editor != "")
					{
						if (__useSharing)
						{
							l_editor = "<a href='#' onclick='$p.notebook.open("+l_editorid+")'>"+l_editor+"</a>";
						}
						l_s += '<br /><br />'
							+ '<b>'+lg("proposedBy")+'</b> : '
                            + l_editor;
					}
    				//l_s+="</td></tr></table></td></tr>";
    				//l_s+="</table>";
    				//$p.print("newmod",box(0,l_name+$p.img("star_yellow2_"+$p.ajax.getVal(l_result,"nota","int",false,0)+".gif",53,12),"$p.app.widgets.close('"+vars['type']+"')",l_s));
    				
                    $p.app.popup.show(l_s,$p.max(l_minwidth+20,500),indef,l_name+$p.img("star_yellow2_"+$p.ajax.getVal(l_result,"nota","int",false,0)+".gif",53,12),true,"$p.app.widgets.close('"+vars['type']+"')");
    				
                    //$p.show("newmod","block");
    				tab[$p.app.tabs.sel].maxUniq++;

    				if (vars['type']=="portal") {
    					tab[$p.app.tabs.sel].cols[0]=$("col0");
    					tab[$p.app.tabs.sel].module[l_newId]=new $p.app.widgets.object(
                                                    0, //col
                                                    0,  //pos
                                                    0, //position in module array alias posj
                                                    l_size, //height
                                                    vars['id'],
                                                    l_website, //url of website
                                                    l_name,
                                                    vars['vars'],   //variables 
                                                    l_minwidth, 
                                                    l_sizable,
                                                    400,        //minmodsize
                                                    l_url,      //url of widget : xml_getwidget, p_rss ,external url,..
                                                    150,        //x
                                                    150,        //y
                                                    tab[$p.app.tabs.sel].maxUniq,
                                                    l_format,   // RM I U see docs $p.app.widgets.object
                                                    l_nbvars,   //number of variables
                                                    tab[$p.app.tabs.sel].id,    //tab_id of widget 
                                                    0,          //blocked
                                                    0,          //minimized
                                                    l_usereader, //usereader,
                                                    0,  //autorefresh
                                                    l_icon,     //icon of the moduke
                                                    false, //is loaded status of the module (indef=not loaded, false=loading, true=loaded)
                                                    indef, //header
                                                    indef, //footer
                                                    indef, //auth  for RSS authentified feeds
                                                    l_views,  //views (home or canvas) canvas for full-screen (full-portal)
                                                    l_l10n    //lang parameters for l10n widgets
                                                    );
                        tab[$p.app.tabs.sel].module[l_newId].PositioninTab = l_newId;
    					tab[$p.app.tabs.sel].module[l_newId].create(l_size );
    					tab[$p.app.tabs.sel].module[l_newId].show();
    					$p.app.widgets.showHdr();
    				}
    				else if (vars['type']=="uniq")  {
                        tab[$p.app.tabs.sel].cols[0]=$("col0");
    					tab[$p.app.tabs.sel].module[l_newId]=new $p.app.widgets.object(
                                                    0,
                                                    1,
                                                    1,
                                                    l_size,
                                                    vars['id'],
                                                    l_website,
                                                    l_name,
                                                    vars['vars'],
                                                    l_minwidth,
                                                    l_sizable,
                                                    400,
                                                    l_url,
                                                    150,
                                                    150,
                                                    1,
                                                    l_format,
                                                    l_nbvars,
                                                    tab[$p.app.tabs.sel].id,
                                                    0,
                                                    0,          //minimized
                                                    0, //usereader,
                                                    0,  //autorefresh
                                                    l_icon,     //icon of the moduke
                                                    false, //is loaded status of the module (indef=not loaded, false=loading, true=loaded)
                                                    indef, //header
                                                    indef, //footer
                                                    indef, //auth  for RSS authentified feeds
                                                    l_views,  //views (home or canvas) canvas for full-screen (full-portal)             
                                                    l_l10n    //lang parameters for l10n widgets
                                                    );
                        tab[$p.app.tabs.sel].module[l_newId].PositioninTab = l_newId;                            
    					tab[$p.app.tabs.sel].module[l_newId].create( l_size );
    					tab[$p.app.tabs.sel].module[l_newId].show();
    					tab[$p.app.tabs.sel].cols[0].appendChild($p.app.widgets.endList());
    					$p.app.widgets.showHdr();
    					tab[$p.app.tabs.sel].moveIsInit=false;
    					useMod=true;
    					$p.app.pages.displayItems();
    				}
    				showNewMod=true;
			}
			else    {
				tab[$p.app.tabs.sel].maxUniq++;
				tab[$p.app.tabs.sel].module[l_newId]=new $p.app.widgets.object(
                                                        -1,
                                                        0,
                                                        0,
                                                        l_size,
                                                        vars['id'],
                                                        l_website,
                                                        l_name,
                                                        vars['vars'],
                                                        l_minwidth,
                                                        l_sizable,
                                                        400,
                                                        l_url,
                                                        150,
                                                        150,
                                                        tab[$p.app.tabs.sel].maxUniq,
                                                        l_format,
                                                        l_nbvars,
                                                        tab[$p.app.tabs.sel].id,
                                                        0,
                                                        0,          //minimized
                                                        0, //usereader,
                                                        0,  //autorefresh
                                                        l_icon,     //icon of the moduke
                                                        false, //is loaded status of the module (indef=not loaded, false=loading, true=loaded)
                                                        indef, //header
                                                        indef, //footer
                                                        indef, //auth  for RSS authentified feeds
                                                        l_views,  //views (home or canvas) canvas for full-screen (full-portal)                  
                                                        l_l10n    //lang parameters for l10n widgets
                                                        );
                tab[$p.app.tabs.sel].module[l_newId].PositioninTab = l_newId;                                        
				tab[$p.app.tabs.sel].module[l_newId].create( l_size );
				tab[$p.app.tabs.sel].module[l_newId].show();
				if(vars['target']!=indef)
				{
					tab[$p.app.tabs.sel].module[l_newId].placeinCol(vars['target'].col,indef,vars['position'],vars['target']);
				}
				else
				{
					tab[$p.app.tabs.sel].module[l_newId].placeinCol(1);
				}
			}
		}
		else    {
			$p.app.debug("Cache is not generated for this module","error");
		}
	},
	/*
			Function: $p.app.widgets.close
                                            Destruct module object
                                            
                                Parameters:
                                
                                             v_type -  uniq / portal
	*/
	close:function(v_type)
	{
		// if last module created is not placed in the portal
		if (tab[$p.app.tabs.sel].module.length==0) return;
		var l_id=tab[$p.app.tabs.sel].module.length-1;
		if (tab[$p.app.tabs.sel].module[l_id].newcol==0 || v_type=="uniq")
		{
			$p.app.widgets.supFeed(l_id);
			tab[$p.app.tabs.sel].module[l_id].destruct();
			delete(tab[$p.app.tabs.sel].module[l_id]);
			tab[$p.app.tabs.sel].module.length-=1;
			tab[$p.app.tabs.sel].maxUniq--;
		}
		//$p.print("newmod","");
		//$p.show("newmod","none");
		showNewMod=false;
	}
}



//************************************* WIDGETS PARAM FUNCTIONS ***************************************************************************************************************
/*
    Class: $p.app.widgets.param
         Widgets param functions
*/
$p.app.widgets.param={
	/*
		Function: $p.app.widgets.param.show
                                Display module configuration area
                                
		Parameters:

			 v_uniq - module Uniq ID
             
                     Returns :
                     
                                false
	*/
	show:function(v_uniq, v_tab)
	{
		$p.show("embed_"+v_tab+'_'+v_uniq,"none");
		tab[$p.app.tabs.sel].module[$p.app.widgets.uniqToId(v_uniq)].bringToFront();
		if ($p.isShown("editbar"+tab[$p.app.tabs.sel].id+"_"+v_uniq)) 
        {
            $p.app.widgets.param.hide(v_uniq);
        }
		else 
        {
            $p.show("editbar"+tab[$p.app.tabs.sel].id+"_"+v_uniq,"block");
        }
		return false;
	},
	/*
		Function: $p.app.widgets.param.hide
                                 Hide module configuration area
                                 
		Parameters:

			 v_uniq - module Uniq ID
            
                      Returns:
                      
                                false
	*/
	hide:function(v_uniq)
	{
		$p.show("editbar"+tab[$p.app.tabs.sel].id+"_"+v_uniq,"none");
		if( $("embed_"+tab[$p.app.tabs.sel].id+"_"+v_uniq) ) {
			$p.show("embed_"+tab[$p.app.tabs.sel].id+"_"+v_uniq,"none");
		}
		return false;
	},
	/*
		Function: $p.app.widgets.param.displayWidgetTitle
                                 Display the widget title
                                 
		Parameters:

			 ptitle - widget title
            
                      Returns:
                      
                                HTML code
	*/
    displayWidgetTitle:function(ptitl)
    {
		if (__widgetTitleUpdatable)
		{
			var l_s='<tr>'
				+'<td>'
				+lg("lblModuleTitle")
				+'</td>'
				+'<td>'
				+'<input type="text" name="ptitl" size="18" value="'+$p.string.doubleToSimpleCot(ptitl)+'" />'
				+'</td>'
				+'</tr>';
		}
		else
		{
			var l_s='<input type="hidden" name="ptitl" value="'+$p.string.doubleToSimpleCot(ptitl)+'" />';
		}
		return l_s;
    },
	/*
		Function: $p.app.widgets.param.displayWidgetBarColors
                                 Display the widget colors
                                 
		Parameters:

			 v_tab - tab sequence ID
			 v_uniq - module Uniq ID
            
                      Returns:
                      
                                HTML code
	*/
    displayWidgetBarColors: function(v_tab,v_uniq)
    {
        var l_s = "";
        if (__barcolnb>1) 
        {
            l_s += '<tr>'
                + '<td>'
                + lg('lblBarColor')
                + '</td>'
                + '<td>'
                + '<ul style="margin: 0px;padding: 0px;">'
                + '<li class="barstyle0" onclick="return $p.app.widgets.param.setBColor('+v_uniq+')" style="margin: 2px;display: block;width: 20px;height: 20px;list-style: none;float:left;">'+$p.img("s.gif",18,18,"","barcoli","barcol"+tab[v_tab].id+"_"+v_uniq+"-")+'</li>';

            for (var i = 1;i <= __barcolnb;i++)
            {
                l_s += '<li class="barstyle'+i+'" onclick="return $p.app.widgets.param.setBColor('+v_uniq+','+i+')" style="margin: 2px;display: block;width: 20px;height: 20px;list-style: none;float:left;">'+$p.img("s.gif",18,18,"","barcoli","barcol"+tab[v_tab].id+"_"+v_uniq+"-"+i)+'</li>';
            }
            l_s += '</ul>'
                + '<input type="hidden" name="bcolor" value="" />'
                + '</td>'
                + '</tr>';
        } 
        return l_s;
    },
	/*
		Function: $p.app.widgets.param.displayBlockWidget
                                 Display the blocking widget option (for admin)
                                 
		Parameters:

			 v_tab - tab sequence ID
			 v_uniq - module Uniq ID
            
                      Returns:
                      
                                HTML code
	*/
    displayBlockWidget:function(v_tab,l_id)
    {
        if (!$p.app.widgets.showAdminProperties) return '';
        else
        {
            return  '<tr>'
                    + '<td class="tophr">'
                    + lg("lblFixedModule")
                    + '</td>'
                    + '<td class="tophr">'
                    + '<input type="checkbox" name="admblocked"'+(tab[v_tab].module[l_id].blocked?' checked="checked"':'')+' />'
                    + '</td>'
                    + '</tr>';
        }    
    },
	/*
		Function: $p.app.widgets.param.displayButtons
                                 Display submit and close buttons
                                 
		Parameters:

			 v_uniq - module Uniq ID
            
                      Returns:
                      
                                HTML code
	*/
    displayButtons: function(v_uniq)
    {
        return  '<tr>'
                + '<td colspan="2" align="center">'
                + '<br />'
                + '<input class="submit" type="submit" value="'+lg("lblBtnValid")+'" /> '
                + '<input class="btn" type="button" onclick="return $p.app.widgets.param.hide('+v_uniq+');" value="'+lg("lblClose")+'" />'
				+ '</td>'
                + '</tr>';
    },
	/*
		Function: $p.app.widgets.param.displayWidgetOptions
                                 Display widget specific options
                                 
		Parameters:

			response - XML object
			v_tab - tab sequence ID
			v_uniq - module Uniq ID
                                l_id - module id in tab
            
                      Returns:
                      
                                HTML code
	*/
    displayWidgetOptions:function(response,v_tab,l_id,v_uniq)
    {
        var i=0;  
        var l_s="";   
        while (response && response.getElementsByTagName("UserPref")[i])
        {                           
            var parameters = new Array;  
            var key="";
            var resHTML="";
            var l_pref=response.getElementsByTagName("UserPref")[i];
            var l_type=$p.ajax.getProp(l_pref,"datatype","str",false,"string");
            var l_name=$p.ajax.getProp(l_pref,"name","str",true,"");
            var l_selValue=$p.string.getVar(tab[v_tab].module[l_id].vars,l_name);
            var l_restriction=$p.ajax.getProp(l_pref,"restricted","str",false,"AI");            
            var l_def=(l_selValue=="")      ?       lg($p.ajax.getProp(l_pref,"default_value","str",false,""))
                                            :       l_selValue;
            
            if ( (l_type!="hidden") 
                 && ((l_restriction=="A" 
                      && $p.app.user.type=="A") 
                 || (l_restriction=="AI")) )    key=lg($p.ajax.getProp(l_pref,"display_name","str",false,"")); 
                 
            parameters = {
                            'type':l_type,
                            'name':l_name,
                            'value':l_def,
                            'id':"editboxinput_"+tab[v_tab].id+"_"+v_uniq+"_"+l_name
                         };

            if ( (l_type=="enum")
                 && ( (l_restriction=="A" && $p.app.user.type=="A") 
                 || (l_restriction=="AI") ) ) {  resHTML=$p.html.form.buildEnum(parameters,l_pref);    }
                
            if ( (l_type=="bool")
                 && ( (l_restriction=="A" && $p.app.user.type=="A") 
                 || (l_restriction=="AI") ) )  {  resHTML=$p.html.form.buildCheckbox(parameters); }
            
            if ( (!(l_type=="enum" || l_type=="bool")) 
                  && ( (l_restriction=="A" && $p.app.user.type=="A") 
                  || (l_restriction=="AI") ) )  {    resHTML=$p.html.form.buildInput(parameters);   }
           
            l_s+="<tr><td>"+key+"</td><td id='inp"+v_uniq+i+"'>"+resHTML+"</td></tr>";
           
            i++;
        }

        // get widget on your website link
        if (__allowGetWidgetOnMySite)
            l_s += '<tr><td></td><td><a href="#" onclick="return $p.app.widgets.param.showCode('+v_uniq+','+tab[v_tab].id+');">'+lg("Embed")+'</a></td></tr>'

        return l_s;
    },
	/*
		Function: $p.app.widgets.param.fillEditBox
                                Fill in module configuration area
                                
		Parameters:

			response - configuration options XML response
			v_uniq - module uniq ID
			v_tab - tab sequence ID
             
                     Returns :
                     
                                false
	*/
    fillEditBox: function(response,v_uniq,v_tab)
	{
		$p.plugin.hook.launch('app.widgets.param.fillEditBox.start');
		
		//generate edit box for a module
		if (v_tab == indef) v_tab = $p.app.tabs.sel;

		var l_id = $p.app.widgets.uniqToId(v_uniq,v_tab),
            ptitl = ($p.string.getVar(tab[v_tab].module[l_id].vars,"ptitl")==""    ?    tab[v_tab].module[l_id].name
                                                                                 :    $p.string.unesc($p.string.getVar(tab[v_tab].module[l_id].vars,"ptitl")));
		if (tab[v_tab].module[l_id].format != 'R' || tab[v_tab].showType == 0)
		{
            l_s = '<form name="editform'+tab[v_tab].id+'_'+v_uniq+'" onsubmit="return $p.app.widgets.param.valid('+v_uniq+');">'
                + '<table cellpadding="2" cellspacing="0" width="90%">'
                + $p.app.widgets.param.displayWidgetTitle(ptitl)
                + $p.app.widgets.param.displayWidgetBarColors(v_tab,v_uniq)
                + $p.app.widgets.param.displayWidgetOptions(response,v_tab,l_id,v_uniq)    
                + $p.app.widgets.param.displayBlockWidget(v_tab,l_id)
                + $p.app.widgets.param.displayButtons(v_uniq)
                + '<tr>'
                + '<td colspan="2" align="center"><div id="embed_'+tab[v_tab].id+'_'+v_uniq+'" style="display: none;">'
    			+ '<textarea rows="3" cols="70"><iframe id="portaneowidget" src="'+__LOCALFOLDER+'portal/widgetforyoursite.php?id='+tab[v_tab].module[l_id].id+'" width="400" height="300" frameborder="0"></iframe>'
    			+ '</textarea></div>'
                + '</td>'
                + '</tr>'
                + '</table>'
                + '</form>';

			$p.print("editbar"+tab[v_tab].id+"_"+v_uniq,l_s);
			$p.app.widgets.param.setBColor(v_uniq,$p.string.getVar(tab[v_tab].module[l_id].vars,"bcolor"),v_tab);
			$p.app.widgets.changeName(l_id,ptitl,v_tab);
		}

		$p.plugin.hook.launch('app.widgets.param.fillEditBox.end');
	},
	/*
		Function:   $p.app.widgets.param.valid
        
                                 Save configuration changes for a module
                                 
		Parameters:

			 v_uniq - module uniq ID
             
                     Returns:
                     
                                false
	*/
	valid:function(v_uniq)
	{
		$p.plugin.hook.launch('app.widgets.param.valid.start');
        
        var className,
            l_form=document.forms["editform"+tab[$p.app.tabs.sel].id+"_"+v_uniq],
            i = 0,
            l_var,
            l_vars = [],
            l_refreshAll = false,
            l_id = $p.app.widgets.uniqToId(v_uniq);
        
        //canvas part : doesn't exists in base, only the original widget exists and must be updated
        var parent_uniq = 0,
            l_id_parent = 0;

        if ( tab[$p.app.tabs.sel].module[l_id] 
                && tab[$p.app.tabs.sel].module[l_id].UniqParent ) 
        {
            className = 'canvas';
        }
     
		while(l_form.elements[i])
		{
			l_var = "";
			switch($p.string.lc(l_form.elements[i].type))
			{
				case "text":
				case "password":
				case "select-one":
				case "hidden":
					//l_var=l_form.elements[i].name+"="+$p.string.esc(l_form.elements[i].value);break;
					//1.4.3 fix : for url, _esc is required. For titles, _esc is altering accentuated signs
					l_var = l_form.elements[i].name
                                +"="+(l_form.elements[i].name=="rssurl" ?   
                                                $p.string.esc(l_form.elements[i].value) :
                                                l_form.elements[i].value
                                      )
                                ;
					break;
				case "checkbox":
				case "radio":
					l_var = (l_form.elements[i].checked?l_form.elements[i].name+"=1":l_form.elements[i].name+"=0");break;
			}
			if ((l_form.elements[i].name).substr(0,3)=="adm")   {
				if (l_form.elements[i].name=="admblocked")  {
					//the entire module needs to be refresh -> only solution: refresh all the modules
					if (tab[$p.app.tabs.sel].module[l_id].blocked!=l_form.elements[i].checked) 
						l_refreshAll=true;
					tab[$p.app.tabs.sel].module[l_id].blocked=l_form.elements[i].checked;
				}
			}
			else
			{
				if (l_var != "")
				{
					l_var = l_var.replace(/&/g,'');
					l_vars.push(l_var);
				}
			}
			i++;
		}
		var l_qs = l_vars.join("&");
		//if title has changed or not
		if (tab[$p.app.tabs.sel].module[l_id].name != $p.string.getVar(l_qs,"ptitl")) {
			$p.app.widgets.changeName(l_id,$p.string.getVar(l_qs,"ptitl"));
		}
		//if icon is set, keep it
		if ($p.string.getVar(tab[$p.app.tabs.sel].module[l_id].vars,"icon")!="") 
        {
            l_qs += "&icon="+$p.string.getVar(tab[$p.app.tabs.sel].module[l_id].vars,"icon");
        }
        
        tab[$p.app.tabs.sel].module[l_id].changeVar("",l_qs);
		
		if (l_refreshAll)   {
			$p.app.pages.init($p.app.tabs.sel);
			$p.app.widgets.param.hide(v_uniq);
		}
		else    
        {
			tab[$p.app.tabs.sel].module[l_id].refresh();
			tab[$p.app.tabs.sel].module[l_id].hdrColor(indef,indef,className);
			$p.app.widgets.param.hide(v_uniq);
		}

		$p.plugin.hook.launch('app.widgets.param.valid.end');

		return false;		
	},
    
	/*
		Function:   $p.app.widgets.param.getModuleParam
                                 Load module configuration
                                 
		Parameters:

			v_id - module sequence ID
			v_tab - tab sequence ID
	*/
	getModuleParam:function(v_id,v_tab)
	{
		if (v_tab==indef) v_tab=$p.app.tabs.sel;    
        var UrlgetXmlCode=indef;
        //not yet in use
        var inactive=1;
        if (inactive==2 && tab[v_tab].module[v_id].url.match(/getsource/)) {
            UrlgetXmlCode = tab[v_tab].module[v_id].url;
            UrlgetXmlCode=UrlgetXmlCode.replace(/getsource/,"getxml");
        }
        var language_param='';
        if ( tab[v_tab].module[v_id] && tab[v_tab].module[v_id].l10n &&  tab[v_tab].module[v_id].l10n.match(__lang)) {
            language_param = "_"+__lang;
        }
        var l_url="../modules/module"+tab[v_tab].module[v_id].id+language_param+"_param.xml";
		switch ($p.app.env)
		{
			case "tutorial":
				l_url="../modules/tmp_module"+tab[v_tab].module[v_id].id+"_param.xml";
                if (UrlgetXmlCode) {
                    l_url=UrlgetXmlCode+"&env=tut";
                }
				break;
			case "portal_page_conn":
				l_url="../modules/module"+tab[v_tab].module[v_id].id+language_param+"_param.xml";
                l_url=l_url.replace(/\?/g,"");
                if (UrlgetXmlCode) {
                    l_url=UrlgetXmlCode;
                } 
				break;
			case "portal":
				l_url="../modules/module"+tab[v_tab].module[v_id].id+language_param+"_param.xml";
                l_url=l_url.replace(/\?/g,"");
                if (UrlgetXmlCode) {
                    l_url=UrlgetXmlCode;
                } 
				break;                
			default:
				l_url="../modules/module"+tab[v_tab].module[v_id].id+language_param+"_param.xml";
				break;
		}
		l_url=l_url.replace(/\?/g,"");
		$p.ajax.call(l_url,
			{
				'type':'load',
				'callback':
				{
					'function':$p.app.widgets.param.treatModuleParam,
					'variables':
					{
						'widgetid':tab[v_tab].module[v_id].uniq,
						'tabpos':v_tab,
                        'v_id'  : v_id
					}
				},
				'source':'xml'
			}
		);
	},
	/*
		Function:$p.app.widgets.param.treatModuleParam
                                Initialize module configuration
                               
		Parameters:

			response - XML object
			vars (array) - variables (optionnal)
	*/
	treatModuleParam:function(response,vars)
	{
        if (response.getElementsByTagName("Module")[0])
		{
			var l_mod=response.getElementsByTagName("Module")[0];
            $p.app.widgets.param.fillEditBox(l_mod,vars['widgetid'],vars['tabpos']);
		} else {

        }
	},
	/*
		Function:$p.app.widgets.param.setBColor
                                Define module header color
                                
		Parameters:

			v_uniq - module Uniq ID
			v_color - color number (of CSS file)
			v_tab - tab sequence ID
            
                      Returns:
                      
                               false
	*/     
	setBColor:function(v_uniq,v_color,v_tab)
	{
		//get the new color number in the form
		if (__barcolnb<2) return false;
		if (v_tab==indef) v_tab=$p.app.tabs.sel;
		if (!v_color) v_color="";
		if (!document.forms["editform"+tab[v_tab].id+"_"+v_uniq]) return false;
		document.forms["editform"+tab[v_tab].id+"_"+v_uniq].bcolor.value=v_color;
		//change selected image border
		($("barcol"+tab[v_tab].id+"_"+v_uniq+"-")).className="barcoli";
		for (var i=1;i<=__barcolnb;i++)
		{
			($("barcol"+tab[v_tab].id+"_"+v_uniq+"-"+i)).className="barcoli";
		}
		($("barcol"+tab[v_tab].id+"_"+v_uniq+"-"+v_color)).className="barcola";
		return false;
	},
	/*
		Function:$p.app.widgets.param.showCode
                                Show module embed code
                                
		Parameters:

			v_uniq - embed code Uniq ID

            
                      Returns:
                      
                               false
	*/  
	showCode:function(v_uniq, v_tab)
	{
		$p.show("embed_"+v_tab+'_'+v_uniq,"block");
		
		return false;
	}
}




//************************************* WIDGETS MOVES FUNCTIONS ***************************************************************************************************************
/*
    Class: $p.app.widgets.move
         Widgets moves functions
*/
$p.app.widgets.move={
	obj:null,
	status:"",
	/*
		Function: $p.app.widgets.move.init
                                 Init module move
                                 
		Parameters:

			 mObj - module object
	*/
	init:function(mObj)
	{
		mObj.onmousedown=$p.app.widgets.move.start;
		var l_obj=$p.app.widgets.move.shadow();
		if(isNaN(l_obj.style.left.toInt())) l_obj.setStyle("left","0px");
		if(isNaN(l_obj.style.top.toInt())) l_obj.setStyle("top","0px");
		mObj.onDragStart=new Function();
		mObj.onDragEnd=new Function();
		mObj.onDrag=new Function();
	},
	/*
		Function: $p.app.widgets.move.start
                                 Start object move
                                 
		Parameters:

			 e - mouse event object
             
                      Returns:
                      
                                 false
	*/
	start:function(e)
	{       
		if (this!=indef && this.id!=indef) $p.app.widgets.move.obj=this;
		var mObj=$p.app.widgets.move.obj;
		e=$p.app.widgets.move.fixE(e);
		//change the css style to draw a shadow
		var l_obj=$p.app.widgets.move.shadow();
		mObj.onDragStart(l_obj.style.left.toInt(),l_obj.style.top.toInt(),e.clientX,e.clientY);
		//recuperation des coordonnées souris
		mObj.lastMouseX=e.clientX;
		mObj.lastMouseY=e.clientY;
		document.onmousemove=$p.app.widgets.move.drag;
		document.onmouseup=$p.app.widgets.move.end;
		//change the obj status
		$p.app.widgets.move.status="dragging";
		return false;
	}, 
	/*
		Function: $p.app.widgets.move.drag
                                 Manage object move
                                 
		Parameters:

			 e - mouse event object
             
                      Returns:
                      
                                 false
	*/
	drag:function(e)
	{
		//recuperation des coordonnées
		e=$p.app.widgets.move.fixE(e);
		var mObj=$p.app.widgets.move.obj;
		var mX=e.clientX;
		var mY=e.clientY;
		var posX,posY;
		var l_obj=$p.app.widgets.move.shadow();
		posX=l_obj.style.left.toInt()+mX-mObj.lastMouseX;
		posY=l_obj.style.top.toInt()+mY-mObj.lastMouseY;		
		l_obj.setStyle("left",posX+"px");
		l_obj.setStyle("top",posY+"px");
		mObj.lastMouseX=mX;
		mObj.lastMouseY=mY;

		if ($p.navigator.IE)    {
			mObj.onDrag((mX+document.documentElement.scrollLeft),(mY+document.documentElement.scrollTop));
		}
		else {
			mObj.onDrag(e.pageX,e.pageY);
		}
		return false;
	}, 
	/*
		Function: $p.app.widgets.move.end
                                 Stop module move
	*/
	end:function()
	{
		document.onmousemove=null;
		document.onmouseup=null;
		var l_obj=$p.app.widgets.move.shadow();
		$p.app.widgets.move.obj.onDragEnd(l_obj.style.left.toInt(),l_obj.style.top.toInt());
		$p.app.widgets.move.obj=null;
		$p.app.widgets.move.status="";
	},
	/*
		Function: $p.app.widgets.move.fixE
                                 No idea => ask ERIC
                                 
		Parameters:

			 e - mouse event object
             
                      Returns:
                      
                                 mouse event object
	*/
	fixE:function(e)
	{
		if(e==indef && window.event) e=window.event;
		if(typeof e.layerX=="undefined")e.layerX=e.offsetX;
		if(typeof e.layerY=="undefined")e.layerY=e.offsetY;
        return e;
	},
	/*
		Function: $p.app.widgets.move.shadow
                                 Create module shadow on move
             
                      Returns:
                      
                                 div element
	*/
	shadow:function()
	{
		if(!mDivObj || mDivObj==null)
		{
			if (createDivDynamically)
			{
				mDivObj = new Element('div',
					{
						'styles':
						{
							'display': 'block',
							'position': 'absolute',
							'cursor': 'move',
							'backgroundColor': '#fff',
							'paddingBottom': '0px',
                            'border-color':'#f00'
						}
					}
				);
				document.body.appendChild(mDivObj);
			}
			else
			{
				mDivObj=$("moveddiv");
			}
		}
		return mDivObj;
	},
	/*
		Function: $p.app.widgets.move.hideShadow
                                 Hide module shadow after move
	*/
	hideShadow:function()
	{
		$p.app.widgets.move.shadow().setStyle("display","none");
		//IE bug : after module move, $p.app.widgets.param.valid was not working any more
		$p.app.pages.clean(mDivObj);
	},
	/*
		Function: $p.app.widgets.move.formatShadow
                                 Format the shadow based on the original object                                 
                                
                     Parameters:

			 v_content - original object HTML code
             
                      Returns:
                      
                                content with other events removed
	*/
	formatShadow:function(v_content)
	{
		v_content=$p.string.lc(v_content);
		//** suppress the iframes
		while(v_content.indexOf("<iframe")!=-1)
		{
			var l_p1=v_content.indexOf("<iframe");
			var l_p2=v_content.indexOf("</iframe>")+9;
			v_content=v_content.substr(0,l_p1)+v_content.substr(l_p2,v_content.length);
		}
		//other events during drag could interfert
		v_content=v_content.replace(/onmouseover=/g,"");
		v_content=v_content.replace(/onmouseout=/g,"");
		return v_content;
	},
	/*
		Function: $p.app.widgets.move.set
                                Initialize objects move (modules, tabs, ...)
                                
		Parameters:

			v_obj - object initialized
			v_type - object type - module / tab
			v_parent - parent object of the moved object
			v_direction - allowed move directions - horizontal / vertical / multidim
			v_usecache - is the cache div initialized on move
			v_extra - extra information
	*/
	set:function(v_obj,v_type,v_parent,v_direction,v_usecache,v_extra)
	{
        
		var l_obj=$(v_obj.id+"_h");
        
        //window.location.hash = "#";
        
		// if the node is not recognize as a moving object, go to next node
		if(!l_obj)return;
		if (v_type=="module")
        {
			if (v_extra==indef) v_extra=$p.app.tabs.sel;
			//Check that module is movable
			var l_id=$p.app.widgets.getModuleId(v_obj);
			
			if (tab[v_extra].module.length > 0 )  // FRI
			{
				if (tab[v_extra].module[l_id].blocked) 
					return;
			}
			else return; //FRI
		}
		// move cursor on bar mouseover
		l_obj.setStyle("cursor","move");
		l_obj.objMoved=v_obj;
		$p.app.widgets.move.init(l_obj);
		// manage an url in the object header
		var l_uObj=$(v_obj.id+"_u");  

        // redefine the tab link
		if(l_uObj)
        {
            var l_currentUrl = window.location.href;
            var l_calledUrl = $p.url.simpleUrl(l_uObj.href);
			//if (l_uObj.href ==window.location.href || l_uObj.href == window.location.href+"#")
            if (l_currentUrl.indexOf(l_calledUrl) == -1)
            {
				l_uObj.onmousedown=function()
                {
                    window.open(this.href);
                }
			}
			else
            {
				l_uObj.onmousedown=l_uObj.onclick;
			}
		}
		l_obj.onDragStart=function()
		{
			var l_selObj=this.objMoved;
			$p.app.widgets.move.setPos(l_selObj,v_parent);
			l_selObj.origNextSibling=l_selObj.nextSibling;
			
			// dragged object building - when the object's beeing dragged, a special style is defined.
			var mDivObj=$p.app.widgets.move.shadow();
			mDivObj.setStyle("zIndex",1000);
			
			mDivObj.style.left=$p.getPos(l_selObj,"Left")+"px";
			mDivObj.style.top=$p.getPos(l_selObj,"Top")+"px";
			
			mDivObj.setStyle("height",l_selObj.offsetHeight+"px");
			mDivObj.setStyle("width",l_selObj.offsetWidth+"px");
			mDivObj.setStyle("display","block");
			mDivObj.setOpacity("0.6");
			mDivObj.setStyle("filter","alpha(opacity=60)");
			mDivObj.set('html',(v_type=="module")?$p.app.widgets.move.formatShadow(l_selObj.innerHTML):l_selObj.innerHTML);
			mDivObj.className=l_selObj.className;

			this.isDrag=false;
			if (v_usecache) $p.app.cache.show("block");
		} ;

		l_obj.onDrag=function(v_x,v_y)
		{
			if (tab[$p.app.tabs.sel].moduleAlign)   {
				this.canBeReleased=objMove(this.objMoved,v_x,v_y,v_parent,v_direction,v_type);
			}
			else    {
				this.canBeReleased=objFreeMove(this.objMoved,v_x,v_y,v_parent,v_direction,v_type);
			}
			this.isDrag=true;
			$p.app.widgets.dragging=true;
		} ;
		
		l_obj.onDragEnd=function(v_x,v_y)
		{
			$p.app.widgets.move.hideShadow();
			if (!$p.navigator.IE) $p.app.pages.resize();

			if(this.isDrag && this.canBeReleased)
			{
				var l_uniq=this.objMoved.id.replace(v_type,"");
				if (v_type=="module")
				{
					l_uniq=l_uniq.substr(l_uniq.indexOf("_")+1);
					l_id=$p.app.widgets.uniqToId(l_uniq);
					
					// Prepare widget name for logging
					var widgetName = get_datasource_name(tab[$p.app.tabs.sel].module[l_id].url);
					
                    if (!tab[$p.app.tabs.sel].moduleAlign)  {
                        tab[$p.app.tabs.sel].module[l_id].newx=v_x-widgetDecalX;
                        tab[$p.app.tabs.sel].module[l_id].newy=v_y-widgetDecalY;
                    }
                    var l_vars=[];
                    //if inside the widget area, or over a tab !
					if (
                        $p.app.tabs.overtabid != -1
                        || tab[$p.app.tabs.sel].moduleAlign
                        || (tab[$p.app.tabs.sel].module[l_id].newx > 0 
                            && tab[$p.app.tabs.sel].module[l_id].newy > 0)
                    )
					{

						//set module position if modules are not aligned
						if (!tab[$p.app.tabs.sel].moduleAlign)  {
							this.objMoved.setStyle("left",v_x+"px");
							this.objMoved.setStyle("top",v_y+"px");
						}

                        if (tab[$p.app.tabs.sel].showType==0)   {
                            //chekc if widget has changed column
														if (tab[$p.app.tabs.sel].module.length>0) //FRI in case module has children
														{
															var l_old=tab[$p.app.tabs.sel].module[l_id].col;
															var l_new=this.objMoved.parentNode.id.replace(/col/,"");
															if (l_old==l_new){l_vars[0]=l_old;} else {l_vars[0]=l_old;l_vars[1]=l_new;}
														}
                        }
                        else    {
                            l_vars[0]=1;
                        } 
			
						//l_vars  contains the two informations like X,Y x is the source tab, Y the destination tab
						//l_id       the module id
                    	$p.app.widgets.saveChanges(l_vars); 

						if (tab[$p.app.tabs.sel].id==0 || tab[$p.app.tabs.sel].temporary)   {
							if ($p.app.tabs.overtabid!=-1)  {
								$p.app.widgets.insertInTab($p.app.tabs.overtabid,tab[$p.app.tabs.sel].module[l_id].id,tab[$p.app.tabs.sel].module[l_id].vars);
							}
						}
						else    {
							if ($p.app.tabs.overtabid!=-1)  {
								// Log widget changing tab
								var logString = "[" + widgetName + "(" + tab[$p.app.tabs.sel].label + ")->(" + tab[$p.app.tabs.overtabid].label + ")]";
								$p.ajax.call('../../app/tests/LoggerResponder.php?action=14&msg=' + logString, {'type':'load'});

								$p.app.widgets.changeTab($p.app.tabs.overtabid,l_uniq);
							}
						}
						$p.app.tabs.moduleOutAll();
						//define modules display order
						if (!tab[$p.app.tabs.sel].moduleAlign)  {
							for (var i=0;i<tab[$p.app.tabs.sel].module.length;i++)
							{
								($("module"+tab[$p.app.tabs.sel].id+"_"+tab[$p.app.tabs.sel].module[i].uniq)).setStyle("zIndex",1);
							}
							this.objMoved.setStyle("zIndex",1000);

							$p.app.pages.computeFooterPosition();
						}
					}

				}
           
				if (v_type=="tab"){$p.app.tabs.save(l_uniq);}
				if (v_type=="admpage"){$p.admin.pages.save(l_uniq);}
			}
			if (v_usecache) $p.app.cache.show("none");
			this.setStyle("zIndex",1);
			$p.app.widgets.dragging=false;
		}
	},
	/*
		Function: $p.app.widgets.move.setPos
                                Set object position during move
		
                     Parameters:

			v_obj - object moved
			v_parent - parent object
	*/
	setPos:function(v_obj,v_parent)
	{
		var l_lstNb=v_parent.length?v_parent.length:2;
		for (var j=1;j<l_lstNb;j++)
		{
			var l_decalY=0;
			var l_lst=v_parent.length?v_parent[j]:v_parent;
			for (var i=0;i<l_lst.childNodes.length;i++)
			{
				var l_node=l_lst.childNodes[i];
				//** if the node is the treated div one, the next nodes must be moved up by div height
				if (l_node==v_obj) decalY=l_node.offsetHeight;
				//** set the position of each nodes (use of decalY for modules under the one moved)
				
				l_node.pagePosLeft=$p.getPos(l_node,"Left");
				l_node.pagePosTop=$p.getPos(l_node,"Top")-l_decalY;
			}
		}
	}
}



//************************************* WIDGETS RSS FUNCTIONS ***************************************************************************************************************
/*
    Class: $p.app.widgets.rss
        
            Widgets rss functions
 */        
$p.app.widgets.rss={
    temporaryId:100000,
	/*
		Function: $p.app.widgets.rss.object  *(Contructor)*
                               RSS articles class
                               
		Parameters:
       
			id - uniq id of the article
			modUniq - uniq ID of the module where the article is
			modName - name of the module
			title - title of the article
			link - link of the article
			image - image of the article (optional)
			date - publication date of the article
			desc - description of the article
			read - read status of the article (true=read, false=not read)
			source - source of the article
            v_modid - module id container in module array
	*/
	//object:function(id,modId,modUniq,modName,title,link,image,date,desc,read,source)
	object:function(id,modUniq,modName,title,link,image,
            date,desc,isRead,source,modId)
	{ 
        this.id=id;
        
		//this.modId=modId;
		this.modUniq=modUniq;
		this.modName=modName;
		this.title=title;
		this.link=link;
		this.image=image;
		this.date=date;
		this.desc=desc;
		this.isRead=(isRead==indef  ?   0   :   isRead);
		this.source=source;
        this.modId = modId;
	},
	/*
		Function: $p.app.widgets.rss.summary 
                                Create top articles div
                               
	*/
	summary:function(){
		var l_s = "<div id='rsstoparticles'></div>";
		$p.print(v_div,l_s);

		$p.app.widgets.rss.getTopArticles();
	},
	opmlfile:"",
	/*
		Function: $p.app.widgets.rss.checkFeed 
                                Check the rss feed
                      
                     Parameters:
                       
                               v_rss - rss feed url
                               v_name - rss feed name
			v_auth - authentication string
                     
                     Returns:
                     
                                false
	*/
	checkFeed:function(v_rss,v_name,v_auth)
	{
		var l_rss = v_rss==indef  ? document.forms['rss'].vars.value    :   
                                    v_rss;
		if (!$p.url.ishttp(l_rss))  
        {
            l_rss="http://"+l_rss;
        }
		l_rss=$p.string.esc(l_rss);
		
		var l_auth=(document.forms['rss'] && document.forms['rss'].pass)?$p.Base64.encode(document.forms['rss'].login.value+":"+document.forms['rss'].pass.value):v_auth;
		var l_callbackvars={'rss':l_rss,'auth':l_auth,'name':v_name};
		var l_url,l_vars,l_method='POST';
		// define loading parameters depending on platform configuration
		if (__useproxy)
		{
			if (__proxypacfile=="")	
			{
				l_url=posh["xmlvalidrssproxy"];
				l_vars="url="+l_rss+(l_auth==indef?"":"&auth="+l_auth);
			}
			else
			{
				if (getProxy(l_rss)=="")
				{
					if (l_auth==indef)
					{
						l_url=posh["xmlvalidrss"];
						l_vars="url="+l_rss;
					}
					else
					{
						l_url=posh["xmlvalidrssauth"];
						l_vars="url="+l_rss+"&auth="+l_auth;
					}
				}
				else
				{
					l_url=posh["xmlvalidrssproxy"];
					l_vars="proxy="+getProxy(l_rss)+"&url="+l_rss+(l_auth==indef?"":"&auth="+l_auth);
				}
			}
		}
		else
		{
			if (l_auth==indef)
			{
				l_url=posh["xmlvalidrss"];
				l_vars="url="+l_rss;
			}

			else
			{	
				l_url=posh["xmlvalidrssauth"];
				l_vars="auth="+l_auth+"&url="+l_rss;
			}
		}
		$p.ajax.call(l_url,
			{
				'type':'load',
				'callback':
				{
					'function':$p.app.widgets.rss.checkFeedXml,
					'variables':l_callbackvars
				},
				'source':'html',
				'variables':l_vars,
				'method':l_method
			}
		);

		//rss checks notification
		$p.print("authrss",$p.img("ico_waiting.gif",16,16,"","imgmid")+" "+lg("checkingFeed"));
		$p.show("authrss","block");
		return false;
	},
	/*
		Function: $p.app.widgets.rss.checkFeedXml 
        
                                Analyze feed testing return
                      
                     Parameters:
                       
                              response - XML object
                              vars (array) - variables (optionnal)
                     
                     Returns:
                     
                                false
	*/
	checkFeedXml:function(response,vars)
	{
     
		if (response.indexOf("<?xml")==-1 && response.indexOf("<rss")==-1)
		{
   
			if (response=="401")
			{
				$p.app.widgets.rss.authentification();
				return false;
			}
			if (response=="407")
			{
				$p.app.alert.show(lg("proxyRejectConnection"));
				$p.show("authrss","none");
				return false;
			}
			if (response.length==3 && response.substr(0,1)=="4")
			{
				$p.app.alert.show(lg("msgIncorrectFeed")+" ("+response+")");
				$p.show("authrss","none");
				$p.app.debug("HTTP Error code : "+response,"error");
				return false;
			}

			//if HTML page instead of xml, check if HTML page propose rss link
			var l_tags=$p.string.simulateGetElementsByTagName(response,"link");
            var l_links="";
			if (l_tags.length>0)
			{
				for (var i=0;i<l_tags.length;i++)
				{
					if (l_tags[i]["type"]=="application/rss+xml")
					{
                        
                        var urlfct = $p.url.ishttp(l_tags[i]["href"])   ?   l_tags[i]["href"]   :
                                                                            document.forms['rss'].vars.value+'/'+l_tags[i]["href"];
                        var limitTitle = 35;
                        var title    = l_tags[i]["title"].length > limitTitle ?     (l_tags[i]["title"]).substr(0,limitTitle) + "...": 
                                                                                    l_tags[i]["title"];                                                 
                        var title = $p.string.trunk(l_tags[i]["title"],limitTitle,true);
                        /*

var title    = l_tags[i]["title"].length > limitTitle ?     (l_tags[i]["title"]).substr(0,limitTitle) + "...": 
                                                                                    l_tags[i]["title"];                                                 


*/
 
                        l_links+='<p class="feedslist">'
                                +'<a href="#" onclick="$p.app.widgets.rss.checkFeed(\''+ urlfct +'\')\">'
                                +title+'</a></p>';
					}
				}
				if (l_links!="") l_links="<p>"+lg("availableFeedsForThisSite")+":</p>"+l_links;
	 		}
			if (l_links=="")
			{
				$p.app.alert.show(lg("msgIncorrectFeed"));
				$p.show("authrss","none");
				$p.app.debug("RSS Feed invalid. XML returned : "+response,"error");
			}
			else
			{
				$p.print("authrss",l_links);
				$p.show("authrss","block");
			}
		}
		else
        { 
            //get list of feeds from an html page
            $p.app.widgets.rss.getFeed(vars); 
        }
		
		return false;
	},
	/*
		Function: $p.app.widgets.rss.authentification 
                                Open authentification box to access authentified feed
	*/
	authentification:function()
	{
		$p.print("authrss","<span style='color:#ff0000'>"+lg("lblProtectedFeed")+"</span><br />"
                           +"<table>"
                           +"<tr><td>"
                           +lg("lblLogin")+"</td><td><input name='login' type='text' size='10' /></td></tr><tr><td>"
                           +lg("lblPassword")+"</td><td><input name='pass' type='password' size='10' /></td></tr><tr><td></td>"
                           +"<td><input type='submit' class='btn' value='Go' />"
                           +"</td></tr>"
                           +"</table>");
                           
		$p.show("authrss","block");
	},
	/*
		Function: $p.app.widgets.rss.getFeed 
                                Register feed information in DB
                      
                     Parameters:
                       
			vars - feed information
			v_fct - function called to display module
                     
                     Returns:
                     
                                false
	*/
	getFeed:function(vars,v_fct)
	{
		if (v_fct==indef) v_fct=$p.app.widgets.rss.getFeedXml;
		if (__useproxy)
		{
			if (__proxypacfile=="") {
				getXml(posh["xmlcheckfeedproxy"],v_fct,vars,"xml","url="+vars['rss'],"POST");
			}
			else
			{
				if (getProxy(vars['rss'])=="")  {
					getXml(posh["xmlcheckfeed"],v_fct,vars,"xml","url="+vars['rss'],"POST");
				}
				else    {
					getXml(posh["xmlcheckfeedproxy"],v_fct,vars,"xml","proxy="+getProxy(vars['rss'])+"&url="+vars['rss'],"POST");
				}
			}
		}
		else    {
            if (typeof($p.tutorial)!='undefined') {
                getXml(posh["xmlcheckfeed"],v_fct,vars,"xml","url="+vars['rss']+"&id="+$p.tutorial.widgetParameters['id'] ,"POST");
            } else {
                getXml(posh["xmlcheckfeed"],v_fct,vars,"xml","url="+vars['rss'] ,"POST");
            }
		}
		return false;
	},
	/*
		Function: $p.app.widgets.rss.getFeedXml 
                                Treat checkfeed response
                      
                     Parameters:
                       
			response - XML object
			vars (array) - variables (optionnal)
	*/
	getFeedXml:function(response,vars)
	{	
		if (response.getElementsByTagName("error")[0])  {
			$p.app.alert.show(lg("msgIncorrectFeed"));
			$p.show("authrss","none");
		}
		else    {
			l_id=$p.ajax.getVal(response,"id","int",false,0);
			if (typeof($p.tutorial)!='undefined') {
				$p.tutorial.widgetParameters['icon'] = $p.ajax.getVal(response,"icon","str",false,0);
            }
			v_icon = $p.ajax.getVal(response,"icon","str",false,0);
			$p.app.widgets.rss.showMod(vars['rss'],l_id,vars['auth'],vars['name'],v_icon);
		}
	},
	/*
		Function: $p.app.widgets.rss.showmod 
                                Open RSS module
                      
                     Parameters:
                       
			v_rss - rss feed url
			v_id - rss feed DB ID
			v_auth - authentification string
			v_icon -  icon url
            
                    Returns:
                    
                                false
	*/
	showMod:function(v_rss,v_id,v_auth,v_name,v_icon)
	{
		//suppress checking feed notification
		$p.show("authrss","none");
		if ($p.app.tabs.sel=="999")
		{
            //$p.url.openLink(posh["rssaddtodirectory_step2"]+"?id="+v_id+(v_auth==indef?"":"&auth="+v_auth));
			$p.tutorial.rss.loadRssAddToDirectory_step2(v_id,v_auth);
		}
		else
		{
			if( v_icon=="" ) {
				v_icon = "rss"+v_id;
			}
			$p.app.widgets.open(86,"pfid="+v_id
                        +"&rssurl="+v_rss
                        +"&icon="+v_icon
                        +(v_auth==indef?"":"&auth="+v_auth)+(v_name==indef?"":"&ptitl="+v_name));
		}
		return false;
	},
	/*
		Function: $p.app.widgets.rss.refreshAll 
                                Refresh all feeds in all opened page
	*/
	refreshAll:function()
	{
		for (var i=0;i<tab.length;i++)
		{
			if (tab[i].isLoaded==true && !tab[i].temporary)
			{
				if (tab[i].feeds.length>0)  {
					$p.app.widgets.rss.init(i);
					tab[i].isLoaded=false;
					$p.app.tabs.refresh($p.app.tabs.sel);
				}
				if (i==$p.app.tabs.sel && tab[$p.app.tabs.sel].showType==1) {
					$p.app.widgets.rss.reader.refresh();
				}
				else
				{
					for (var j=0;j<tab[i].module.length;j++)
					{
						if (tab[i].module[j].format=='R')   {
							tab[i].module[j].refresh();
						}
						else    {
							if (tab[i].module[j].autorefresh) tab[i].module[j].refresh();
						}
					}
				}
			}
		}
	},
	/*
		Function: $p.app.widgets.rss.reloadAndRefresh 
                                Refresh rss module in the tab selected
                      
                     Parameters:
                       
			v_id - module sequence ID
			v_tab - tab sequence ID
	*/ 
	reloadAndRefresh:function(v_id,v_tab)
	{ 
		$p.app.widgets.rss.refresh(v_id,v_tab,true);
	},
	/*
		Function: $p.app.widgets.rss.refresh  
                                Refresh rss module in the tab selected
                      
                     Parameters:
                       
			v_id (int) - module sequence ID
			v_tab (int) - tab sequence ID
                               v_reloadData (boolean) - reload data or not
            
                    Returns:
                    
                                false
	*/
	refresh:function(v_id,v_tab,v_reloadData)
	{
		if (v_tab==indef) v_tab=$p.app.tabs.sel;
		$p.app.widgets.changeIcon(v_id,'../images/ico_waiting.gif',v_tab);

		//empty old feeds array
		if (v_reloadData)   {
			tab[v_tab].module[v_id].isLoaded=false;
			$p.app.widgets.supFeed(v_id,v_tab);
		}
		var l_oldNb=$p.string.getVar(tab[v_tab].module[v_id].vars,"nb",'int').toInt();
		var l_nb=(l_oldNb==0||isNaN(l_oldNb))?5:l_oldNb;
		//if no cache, add random number
		if (rssNoCache) {
			reset_rand();
			tab[v_tab].module[v_id].url+="pnocache="+rand+"&";
		}
		l_ext=$p.string.getVar(tab[v_tab].module[v_id].vars,"pwspecif");
		if (__registerfeeds)    { 
            $p.ajax.call("../portal/xmlfeeds"+l_ext+".php",
                {
                    'type':'load',
                    'variables':"rand="+rand+"&s="+tab[v_tab].module[v_id].start+"&"+tab[v_tab].module[v_id].vars,
                    'method':'POST',
                    'callback':
                    {
                        'function':$p.app.widgets.rss.display,
                        'variables':
                        {
                            'id':v_id,
                            'nb':l_nb,
                            'uniq':tab[v_tab].module[v_id].uniq,
                            'tab':v_tab
                        }
                    },
                    'caller':'getWidgetArticles'
                }
            );
		}
		else    {
            $p.ajax.call(tab[v_tab].module[v_id].url,
                {
                    'type':'load',
                    'variables':"rand="+rand
                            +"&pid="+$p.app.user.id
                            +"&prof="+tab[v_tab].id
                            +"&p="+tab[v_tab].module[v_id].uniq
                            +"&bar="+tab[v_tab].controls
                            +"&"+tab[v_tab].module[v_id].vars,
                    'method':'POST',
                    'callback':
                    {
                        'function':$p.app.widgets.rss.display,
                        'variables':
                        {
                            'id':v_id,
                            'nb':l_nb,
                            'uniq':tab[v_tab].module[v_id].uniq,
                            'tab':v_tab
                        }
                    },
                    'caller':'getWidgetArticles'
                }
            );
		}
		return false;
	},
	/*
		Function: $p.app.widgets.rss.deleteEntry 
                                Remove an article in an rss module
                      
                     Parameters:
                       
			v_mod - widget  ID
			RssArticleId - RSS article ID
	*/
	deleteEntry:function(v_mod,RssArticleId)
	{
		$p.ajax.call(posh["scr_feed_changestatus"],
			{
				'type':'execute',
				'variables':"delete=1&artId="+RssArticleId+"&v_mod="+v_mod,
				'callback':
				{
					'function':$p.app.widgets.rss.reloadAndRefresh
				}
			}
		);
	},
	/*
		Function: $p.app.widgets.rss.init
                                Init rss modules in selected tab
                      
                     Parameters:
                       
                                v_tab sequence ID
	*/
	init:function(v_tab)
	{
		// initialize feeds
		if (v_tab==indef) v_tab=tabs.sel;
		for (i=0;i<tab[v_tab].module.length;i++)
		{
			tab[v_tab].module[i].isLoaded=false;
		}
		tab[v_tab].feeds.length=0;
	},
	/*
		Function: $p.app.widgets.rss.get  
                                Init rss modules in selected tab
                      
                     Parameters:
                       
			response - rss feed xml object
			v_modId - module sequence ID
			v_nb - number of articles to display
			v_tab - tab sequence ID
            
                    Returns: 
                            
                                true or false
	*/
	get:function(response,v_modId,v_nb,v_tab)
	{ 
		tab[v_tab].feeds[v_modId]=new Object();
		if (v_tab==indef) v_tab=$p.app.tabs.sel;
        //check if the response is empty
        var l_testResponse = response.getElementsByTagName('Module');
        if (l_testResponse.length == 0)
        {
            tab[v_tab].module[v_modId].isLoaded = false;
            return false;
        }

		// get module feeds
		tab[v_tab].module[v_modId].nbunread=indef;//reset nb of unread articles

		//check if the feed is local
		var l_feedUrl = $p.app.widgets.rss.getFeedUrl(tab[v_tab].module[v_modId]);
		
		if (response.getElementsByTagName("error")[0])  {
			$p.app.debug($p.ajax.getVal(response,"error","str",false,"Unknown error"),"error");
		}
		if (response.getElementsByTagName("nbunread")[0])   {
			tab[v_tab].module[v_modId].nbunread
                =(response.getElementsByTagName("nbunread")[0].firstChild.nodeValue).toInt();
		}
		if (response.getElementsByTagName("header")[0]) {
			//if RSS, name is the URL of the RSS feed (only on current tab)
			if (tab[v_tab].module[v_modId].id==86 
                && v_tab==$p.app.tabs.sel)
			{
				if (l_feedUrl!=indef)
				{
					tab[v_tab].module[v_modId].link=l_feedUrl;
					// if new feed in page, update the module title with feed title
					if ($p.string.getVar(tab[v_tab].module[v_modId].vars,"ptitl")=="") tab[v_tab].module[v_modId].changeVar("ptitl",$p.ajax.getVal(response,"ftitle","str",false,"RSS reader"));
					$p.app.widgets.changeName(v_modId,$p.string.getVar(tab[v_tab].module[v_modId].vars,"ptitl"),v_tab);
				}
			}

			tab[v_tab].module[v_modId].header=$p.ajax.getVal(response,"header","str",false,"");
			tab[v_tab].module[v_modId].footer=$p.ajax.getVal(response,"footer","str",false,"");
			tab[v_tab].module[v_modId].isLoaded=true;
			// fill edit box (only for current tab)
			$p.app.widgets.param.fillEditBox(response,tab[v_tab].module[v_modId].uniq,v_tab);
			var i=0,l_item,l_title,l_link,l_image,l_date,l_desc,l_read,l_id,l_source;
			var l_isLocal = $p.url.isLocal(l_feedUrl);
			while (i<v_nb+1 && response.getElementsByTagName("item")[i])
			{
				l_item = response.getElementsByTagName("item")[i];
                
				l_id = $p.ajax.getVal(l_item,"id","str",false,tab[v_tab].RssArticles.length);
                tab[v_tab].RssArticles.length+=1;
				l_title = $p.ajax.getVal(l_item,"title","str",false,"...");
                if (!l_isLocal)
					l_title = $p.app.widgets.rss.secure(l_title);
				l_source = $p.ajax.getVal(l_item,"source","str",false,"");
				//parse Source
				if (l_source.indexOf('<title')!=-1)
				{
					l_sourceArr = ($p.string.simulateGetElementsByTagName(l_source,'title'));
					l_source = l_sourceArr[0]["content"];
				}
				l_source = $p.app.widgets.rss.secure(l_source);
				
				l_desc=$p.app.widgets.rss.secure($p.ajax.getVal(l_item,"content","str",false,"")==""?$p.ajax.getVal(l_item,"desc","str",false,""):$p.ajax.getVal(l_item,"content","str",false,""));
				l_link=$p.ajax.getVal(l_item,"link","str",false,"");
				l_read=$p.ajax.getVal(l_item,"read","int",false,0);
				l_date= (l_item.getElementsByTagName("pubdate")[0] 
                               && l_item.getElementsByTagName("pubdate")[0].firstChild) ?   $p.date.convertFromRss(l_item.getElementsByTagName("pubdate")[0].firstChild.nodeValue)  :   
                                                                                            new Date();
				if (l_title=="") l_title=lg("lblNoTitle");
                
                var l_audio = $p.ajax.getVal(l_item,"audio","str",false,"");
                if (l_audio!="" 
                    && l_item.getElementsByTagName("audio")[0] 
                    && l_item.getElementsByTagName("audio")[0].firstChild) {
                        l_title+=" <a href='#' onclick=\"$p.app.reader.open('"+l_audio+"','a')\">"+$p.img("ico_readmedia.gif",12,12,lg("lblListen"),"imgmid")+"</a>";
                }
                var l_video = $p.ajax.getVal(l_item,"video","str",false,"");
                if (l_video!=""
                    && l_item.getElementsByTagName("video")[0] 
                    && l_item.getElementsByTagName("video")[0].firstChild) {
                        l_title+=" <a href='#' onclick=\"$p.app.reader.open('"+l_video+"','v')\">"+$p.img("ico_readmedia.gif",12,12,lg("lblWatch"),"imgmid")+"</a>";
                }
                
                if (l_item.getElementsByTagName("image")[0] && l_item.getElementsByTagName("image")[0].firstChild)
					l_image=" src='"+$p.ajax.getVal(l_item,"image","str",false,"")+"' "+(($p.ajax.getProp(l_item.getElementsByTagName("image")[0],"width","str",false,"")=="")?"":" width="+$p.ajax.getProp(l_item.getElementsByTagName("image")[0],"width","str",false,""));
				else
					l_image="x";
                //tab[v_tab].feeds.push(new $p.app.widgets.rss.object(l_id,tab[v_tab].module[v_modId].id,tab[v_tab].module[v_modId].uniq,tab[v_tab].module[v_modId].name,l_title,l_link,l_image,l_date,l_desc,l_read,l_source));
				var nf = new $p.app.widgets.rss.object(
                                            l_id,
                                            tab[v_tab].module[v_modId].uniq,
                                            tab[v_tab].module[v_modId].name,
                                            l_title,
                                            l_link,
                                            l_image,
                                            l_date,
                                            l_desc,
                                            l_read,
                                            l_source,
                                            v_modId
                                       );   
                tab[v_tab].RssArticles[l_id]=nf;   
				tab[v_tab].feeds[v_modId][l_id]=nf;
                tab[v_tab].feeds.length+=1;
                //this.RssArticles.                                              
				i++;
			}
			//tab[v_tab].feeds[v_modId].isLastPage=false;
			tab[v_tab].module[v_modId].isLoaded=true;
			$p.app.checkLoading(false,v_tab);
			return true;
		}
		return false;
	},
	/*
		Function: $p.app.widgets.rss.getFeedUrl 
                                Get the feed URL

                     Parameters:

                                v_widget : widget array item
                                
                    Returns: 
                    
			feedURL string
                                indef : is not recognize as a feed
	*/
	getFeedUrl:function(l_widget)
	{
		var l_vars = l_widget.vars;
		var l_p1 = (l_vars.indexOf("rssurl=http://")==-1) ? (l_vars.indexOf("rssurl=https://")+7)
														  : (l_vars.indexOf("rssurl=http://")+7);
		if (l_p1 == -1)
			return indef;
		
		var l_p2 = l_vars.indexOf("/",l_p1+10);
		if (l_p2 == -1) l_p2 = l_vars.indexOf("&",l_p1);
		if (l_p2 == -1) l_p2 = l_vars.length;
		
		return l_vars.substring(l_p1,l_p2);
	},
	//ancre2
	reloadAndRefreshRating:function(response,vars)
	{
	
	},
	/*
		Function: $p.app.widgets.rss.loadRating
			load the user's rating on an article in the database 
		
		Parameters:
		
			RssArticleId - Rss article id
			l_modId - widget  ID
			v_tab - tab sequence ID
			source - origin point of the request (feedRSS, iframe, homepage)
	*/
	loadRating:function(RssArticleId,l_modId,v_tab,source)
	{ 
		if ($p.app.user.id > 0 && __useRating)
		{
			$p.ajax.call(posh["xml_display_article_user_rating"]+"?artId="+RssArticleId,
				{
					'type':'load',
					'callback' : 
					{
						'function' :$p.app.widgets.rss.readRating,
						'variables' : 
						{	
							'RssArticleId' : RssArticleId,
							'l_modId' : l_modId,
							'v_tab' : v_tab,
							'source' : source
						}
					}
				}
			);
		}
	},
	/*
		Function: $p.app.widgets.rss.readRating
			read the user's rating on an article in the database when it exists
		
		Parameters:
		
			response - XML object
			vars - variables
			
		Returns : 
			
			HTML
	*/
	readRating:function(response,vars)
	{ 
		var l_result=response.getElementsByTagName('rating');
		for (var i=0;i<l_result.length;i++)		
		{ 
			var l_rating = $p.ajax.getVal(l_result[i],'user_article_rating','',false,'0');
				
			if (l_rating > 0 )
			{
				$p.ajax.call(posh["xml_display_article_user_rating"]+"?artId="+vars['RssArticleId'],
					{
						'type':'load',
						'callback' : 
						{
							'function' :$p.app.widgets.rss.displayRating,
							'variables' : 
							{	
								'RssArticleId' : vars['RssArticleId'],
								'l_modId' : vars['l_modId'],
								'v_tab' : vars['v_tab'],
								'source' : vars['source']
							}
						}
					}
				);
			}				
			else
			{ 
				$p.ajax.call(posh["xml_display_article_user_rating"]+"?artId="+vars['RssArticleId'],
					{
						'type':'load',
						'callback' : 
						{
							'function' :$p.app.widgets.rss.displayRatingSystem,
							'variables' : 
							{	
								'RssArticleId' : vars['RssArticleId'],
								'l_modId' : vars['l_modId'],
								'v_tab' : vars['v_tab'],
								'source' : vars['source']
							}
						}
					}
				);
			} 
		}
	},
	/*
		Function: $p.app.widgets.rss.getRating
			get the user's rating on an article when he rates it
		
		Parameters:
		
			rating - rating given by the user on an article
			v_mod - widget  ID
			RssArticleId - Rss article id
			v_tab - tab sequence ID
	*/
	getRating:function(rating,v_mod,RssArticleId,v_tab)
	{ 
		if (v_tab!=indef)
		{
			$p.ajax.call(posh["scr_rate_article"],
				{ 
					'type':'execute',
					'variables' : "artId=" + RssArticleId + "&artRating=" + rating,
					'callback' : 
					{
						'function' :$p.app.widgets.rss.reloadAndRefresh(v_mod,v_tab)
					}
				}
			);
		}
		else
		{
		$p.ajax.call(posh["scr_rate_article"],
				{ 
					'type':'execute',
					'variables' : "artId=" + RssArticleId + "&artRating=" + rating
				}
			);
		}
	},
	/*
		Function: $p.app.widgets.rss.displayStars
			display the stars in the rating system
		
		Parameters:
		
			star - number of the star to display
			l_modId - widget  ID
			RssArticleId - Rss article id
			v_tab - tab sequence ID
			
		Returns : 
			
			HTML
	*/
	displayStars:function(star,l_modId,RssArticleId,v_tab)
	{	
		var l_image = $p.img('transparent.gif',14,14,lg('lblEmptyStarInfo'));	
		var l_s = '';
		l_s += "<a href='#' onclick='$p.app.widgets.rss.getRating("+star+","
				+l_modId+","
				+RssArticleId+","
				+v_tab
				+");return false;' class='star"+star+"'>"
				+ l_image
			+ "</a>" ; 
		return l_s;
	},
	/*
		Function: $p.app.widgets.rss.displayRatingSystem
			display the rating system for an article
		
		Parameters:
		
			response - XML object
			vars - variables
			
		Returns : 
			
			HTML
	*/
	displayRatingSystem:function(response,vars)
	{ 
		var l_result=response.getElementsByTagName('rating');
		var l_s = '';
		for (var i=0;i<l_result.length;i++)		
		{ 
			var l_rating = $p.ajax.getVal(l_result[i],'user_article_rating','',false,'0');

			l_s += '<div class="rating">' 
				+'<div class="my_rating">'
				+'<span class="rate">' 
				+lg("lblRate")
				+': <br/>';	
			for (var j=1;j<6;j++)
			l_s +=$p.app.widgets.rss.displayStars(j,vars['l_modId'],vars['RssArticleId'],vars['v_tab']);
			l_s += '</div>' 
				+'</span>' 
				+'</div>'; 
			$p.print('user_rating_'+vars['source']+'_'+vars['l_modId']+'_'+vars['RssArticleId'],l_s);
		}
	},
	/*
		Function: $p.app.widgets.rss.displayRating
			display the user's rating on an article
		
		Parameters:
		
			response - XML object
			vars - variables
			
		Returns : 
			
			HTML code
	*/
	displayRating:function(response,vars)
	{ 
		var l_result=response.getElementsByTagName('rating');
		var l_s = '';
		for (var i=0;i<l_result.length;i++)		
		{ 
			var l_rating = $p.ajax.getVal(l_result[i],'user_article_rating','',false,'0');
			var l_image = $p.img('rating_star_'+l_rating+'.gif',70,14,lg('lblRatingStar'+l_rating+'Info'));
				
			l_s += '<div class="rating">' 
					+'<div class="my_rating">'
					+'<span class="rate">' 
					+lg("lblRate")
					+': <br/>'
					+l_image
					+'</span>' 

			l_s += $p.app.widgets.rss.displayIconDeleteRating(vars['l_modId'],vars['RssArticleId'],vars['v_tab']); 
										
			l_s +='<br/>' 
			
				+'</div>';	
			$p.print('user_rating_'+vars['source']+'_'+vars['l_modId']+'_'+vars['RssArticleId'],l_s);
		
		}
	},
	/*
		Function: $p.app.widgets.rss.deleteRating
			Delete the user's rating on an article
		
		Parameters:
			
			v_mod - widget  ID
			RssArticleId - Rss article id
			v_tab - tab sequence ID
	*/
	deleteRating:function(v_mod,RssArticleId,v_tab)
	{
		if (v_tab!=indef)
		{
			$p.ajax.call(posh["scr_delete_rating"],
				{
					'type':'execute',
					'variables': "artId="+ RssArticleId,
					'callback':
					{
						'function':$p.app.widgets.rss.reloadAndRefresh(v_mod,v_tab)
					}
				}
			);
		}
		else
		{
			$p.ajax.call(posh["scr_delete_rating"],
				{
					'type':'execute',
					'variables': "artId="+ RssArticleId
				}
			);
		}
	},
	/*
		Function: $p.app.widgets.rss.loadAverageRating
			Load the average rating of an article
		
		Parameters:
		
			RssArticleId - Rss article id
			l_modId - widget  ID
			v_tab - tab sequence ID
			source - origin point of the request (feedRSS, iframe, homepage)
	*/
	loadAverageRating:function(RssArticleId,l_modId,v_tab,source)
	{	
		if ($p.app.user.id > 0 && __useRating)
		{ 
			$p.ajax.call(posh["scr_article_average_rating"]+"?artId="+RssArticleId,
				{
					'type':'load',
					'callback' : 
					{
						'function' :$p.app.widgets.rss.displayAverageRating,
						'variables' : 
						{
							'RssArticleId' : RssArticleId,
							'l_modId' : l_modId,
							'v_tab' : v_tab,
							'source' : source
						}
					}
				}
			);
		}
	},
	/*
		Function: $p.app.widgets.rss.displayAverageRating
			display the average rating of an article
		
		Parameters:
		
			response - XML object
			vars - variables
			
		Returns : 
			
			HTML code showing the average rating of an article
	*/
	displayAverageRating:function(response,vars)
	{ 
		var l_result=response.getElementsByTagName('rating');
		var l_s = '';
		for (var i=0;i<l_result.length;i++)		
		{ 
			var l_rating = $p.ajax.getVal(l_result[i],'article_average_rating','',false,'0');
			l_round_rating = Math.round(l_rating);
			
			if (l_round_rating!=0)
			{
				var l_counter = $p.ajax.getVal(l_result[i],'counter','',false,'0');
				var l_single_rate = $p.ajax.getVal(l_result[i],'single_rate','',false,'0');
				var l_tofixed_rating = new Number (l_rating);
				l_tofixed_rating.toFixed(1);
				l_image = $p.img('average_rating_stars_'+l_round_rating+'.gif',70,14,lg('lblRatingStar'+l_round_rating+'Info'));
				if (l_single_rate == 0)
				{ 
					var l_plural = 's';
				}
				else
				{ 
					var l_plural = '';
				}
				
				l_s +=  '<span class="average_rating">' 
						+lg("lblAverageRating")
						+': <br/>'
						+ l_image
						+ ' (' + l_tofixed_rating + ') '
						+ l_counter + ' note'+l_plural
						+'</span>' 		
				$p.print('average_rating_'+vars['source']+'_'+vars['l_modId']+'_'+vars['RssArticleId'],l_s);	
			}
		}
	},
	/*
		Function: $p.app.widgets.rss.displayIconDeleteRating
			display the icon to cancel the rate of a rated article
		
                     Parameters:

			l_modId - widget  ID
                                RssArticleId - RSS article ID
                                v_tab - tab sequence ID
                                
                    Returns: 
                    
                                HTML
	*/
	displayIconDeleteRating:function(l_modId,RssArticleId,v_tab)
	{ if ($p.app.user.id>0 &&__useRating)
		var l_addedFct="";
			{
			l_addedFct	+= "<a href='#' onclick='$p.app.widgets.rss.deleteRating("
                        + l_modId+","
                        + RssArticleId+","
						+ v_tab+");return false;'>"
						+ $p.img("ico_suppress.gif",7,7,lg("lblDeleteRatingInfo"))
                        + "</a>"
			}
		 return l_addedFct;
	},
	/*
		Function: $p.app.widgets.rss.displayIconSharing
                                Display sharing icon

                     Parameters:

                                l_tab - tab sequence ID
                                RssArticleId - RSS article ID
                                
                    Returns: 
                    
                                HTML
	*/
	displayIconSharing:function(l_tab,RssArticleId)
    {
        var l_addedFct="";
        if ($p.app.user.id>0
            &&__useSharing) {
                l_addedFct += " <a href='#' onclick='$p.friends.menu(2,"
                          + RssArticleId
                          + ");return false;'>"
                          + $p.img("ico_share_s.gif",13,10,lg("lblShareInfo"))
                          + "</a> ";
        }  
        return l_addedFct;
    },
	/*
		Function: $p.app.widgets.rss.displayIconArchive
                                Display archive icon

                     Parameters:

                                l_tab - tab sequence ID
                                RssArticleId - RSS article ID                             
                                
                    Returns: 
                    
                                HTML
	*/
	displayIconArchive:function(l_tab,RssArticleId)
    {
        var l_addedFct="";
        if ($p.app.user.id>0
            &&__useArchive) {
                l_addedFct += " <a href='#' onclick='return $p.article.save("
                          + RssArticleId+")'>"
                          + $p.img("ico_disk_s.gif",8,9,lg("lblSave"))
                          + "</a> ";
        } 
        return l_addedFct;
    },
	/*
		Function: $p.app.widgets.rss.displayIconDelete
                                
                                Display delete icon

                     Parameters:

                                l_tab - tab sequence ID
                                l_modId - 
                                RssArticleId - RSS article ID                                
                                
                    Returns: 
                    
                                HTML
	*/
	displayIconDelete:function(l_tab,l_modId,RssArticleId)
    {   
        var l_addedFct = "";
        if ($p.app.user.id > 0
            &&__useRssDelete) {
                l_addedFct += " <a href='#' onclick='$p.app.widgets.rss.deleteEntry("
                          +l_modId+","
                          +RssArticleId+");return false;'>"
                          +$p.img("ico_suppress.gif",7,7,lg("lblDeleteInfo"))
                          +"</a> ";
        }   
        return l_addedFct;
    },    
    
	/*
		Function: $p.app.widgets.rss.displayFeedImages
                                Display feed images

                     Parameters:

                                l_tab - tab sequence ID
                                RssArticleId - RSS article ID
                                j - number of articles displayed                                    
                                
                    Returns: 
                    
                                HTML
	*/
	displayFeedImages:function(v_tab,RssArticleId,j,v_widgetDisplayPref)
    {
        var l_image = '';

        if (__displayrssimages == 0 
               || (__displayrssimages == 1 && j != 0)
               || v_widgetDisplayPref == 0)
        {
            l_image = '';
        }
        else
        {
            if (tab[v_tab].RssArticles[RssArticleId].image == "x")
            {
                //check if description is containing images
                var l_desc = tab[v_tab].RssArticles[RssArticleId].desc;
                if (l_desc.indexOf('<IMG') != -1 || l_desc.indexOf('<img') != -1)
                {
                    var l_match = l_desc.match(/ src="[^"]+"/i);
                    if (l_match != null && l_match.length > 0) l_image = l_match[0];
                    else {
                        l_match = l_desc.match(/ src='[^']+'/i);
                        if (l_match != null && l_match.length > 0) l_image = l_match[0];
                    }
                }
            }
            else
            {
                l_image = tab[v_tab].RssArticles[RssArticleId].image;
            }         
        }

        if (l_image == '' || l_image.indexOf('http') == -1) return '';
        
        var l_imageScale = (v_widgetDisplayPref == 1 && j == 0) ? 2 : 1;
        
        return '<div style="width: '+(75 * l_imageScale)+'px;height: '+(50 * l_imageScale)+'px;overflow: hidden;float: left;margin-right: 6px;">'
            + '<img'
            + l_image
            + ' style="width:'+(75 * l_imageScale)+'px;" />'
            + '</div>';
    },  
    
	/*
		Function: $p.app.widgets.rss.displayFeedTitle 
                                Display feed title
                    
                    Parameters:
                        
                                l_tab - tab sequence ID
                                vars - variables
                                i - counter
                    
                    Returns: 
                    
                                HTML
	*/
	displayFeedTitle:function(l_tab,vars,RssArticleId,j,v_widgetDisplayPref)
    {
        var l_s = '<span>';
        var v_tab = l_tab;
        var addSource=(__displayrsssource 
                      && tab[v_tab].RssArticles[RssArticleId].source!="" )   ?       '<br /><span class="source">'
                                                            +       tab[v_tab].RssArticles[RssArticleId].source+'</span>'
                                                            :       '';
		

        var l_javascript = tab[v_tab].RssArticles[RssArticleId].link.indexOf('javascript:');
		if (l_javascript == -1) {
			l_s += '<a href="'+tab[v_tab].RssArticles[RssArticleId].link+'" target="npdetailf" onclick=\'return $p.app.widgets.rss.linkClicked(this,'+vars['uniq']+','+vars['id']+','+vars['nb']+','+RssArticleId+');\'';
			l_s += '<a href="'+tab[v_tab].RssArticles[RssArticleId].link+'" target="npdetailf" '
                    +'onclick=\'return $p.app.widgets.rss.linkClicked(this,'+vars['uniq']+','+vars['id']+','+vars['nb']+','+RssArticleId+');\'';
		}
		else {
			l_s += '<a href="#" target="npdetailf" '
                + 'onclick=\'$p.app.widgets.rss.linkClicked(this,'+vars['uniq']+','+vars['id']+','+vars['nb']+','+RssArticleId+');'+tab[v_tab].RssArticles[RssArticleId].link.substr(l_javascript+11)+';return false;\'';
		}
		l_s+=' onmouseover="$p.app.widgets.rss.showOverview('+RssArticleId+',event)" '
                +'onmouseout="$p.app.widgets.rss.hideOverview('+RssArticleId+')">'
                +tab[v_tab].RssArticles[RssArticleId].title+'</a>'

        l_s += addSource
            + '	</a>'
            + '</span>';
        return l_s;
    },
	/*
		Function: $p.app.widgets.rss.displayFeedDate
                                Display feed date
                    
                    Parameters:
                        
                                l_tab - tab sequence ID
                                i - counter
                    
                    Returns: 
                    
                                HTML
	*/
	displayFeedDate:function(v_tab,RssArticleId)
    {
		if (!__displayRssDate) return '';
        return '<span class="date"> - '+
                    $p.date.formatDelai($p.date.delayFromNow(tab[v_tab].RssArticles[RssArticleId].date))
                    +'</span>';
        //return '<span class="date"> - '+$p.date.formatDelai($p.date.delayFromNow(tab[v_tab].feeds[i].date))+'</span>';
    },
	/*
		Function: $p.app.widgets.rss.displayFeedTitleFormat
                                
                                Display feed title format

                     Parameters:
                       
			l_tab - tab sequence ID
			i - counter                             
                                j - number of articles displayed
                               
                    Returns: 
                    
                                HTML
	*/
	displayFeedTitleFormat:function(l_tab,RssArticleId,j)
    {	
		var v_tab = l_tab;
        var l_s = "";
        
        l_s += (__displayrssimages == 0 
              || (__displayrssimages == 1 && (tab[v_tab].RssArticles[RssArticleId].image == "x" || j != 0)) 
              || (__displayrssimages == 2 && tab[v_tab].RssArticles[RssArticleId].image == "x"))                 ?           ''
                                                                                            :           '<br />';     
        return l_s;
    }, 	
	
	/*	$p.app.widgets.rss.lastpage
		Boolean to know if the page in the feed is the last page.
		0 : not the last page
		1 : last page
	*/
	lastpage:0,	
			 
	/*
		Function: $p.app.widgets.rss.displayRSSFeeds
                                Display RSS feeds

                     Parameters:
                       
			l_tab - tab sequence ID
			l_modId -     module id (database id)                    
                                vars (array) - variables
                               
                    Returns: 
                    
                                Array containing the HTML code to display and the value of 'j'
	
        */  

    displayRSSFeeds:function(l_tab,l_modId,vars,l_auth)
    { 
        var j = 0;	
        var returnDatas = [];
        var l_s = "";
        var v_tab = l_tab;
        var l_widgetDisplayPref = getVar(tab[v_tab].module[l_modId].vars,'pdisplay');
        if (l_widgetDisplayPref == '') l_widgetDisplayPref = 0;
        /*
                        array containing list of all articles with database  unique id as key
                        tab[v_tab].RssArticles[v_id]
                        tab[v_tab].RssArticles[RssArticleId]
*/
        for (var RssArticleId in tab[v_tab].feeds[l_modId])
		{
            if ( typeof(tab[v_tab].feeds[l_modId][RssArticleId]) != 'undefined' && j<(vars['nb']+1) ) 
			{
                var l_addedFct = '&nbsp;'
                        + $p.app.widgets.rss.displayIconSharing(v_tab,RssArticleId) //display sharing icon
                        + $p.app.widgets.rss.displayIconArchive(v_tab,RssArticleId) //display archive icon
                        + $p.app.widgets.rss.displayIconDelete(v_tab,l_modId,RssArticleId), //display delete icon
                    feedId = "",
                    feedClass = "link";
                //var RssArticleId = tab[l_tab].feeds[i].id;

                feedId = 'feed'+tab[v_tab].id+'_'+vars['uniq']+'_'+RssArticleId;

                if (__registerfeeds && !l_auth)
                {
                    (tab[v_tab].RssArticles[RssArticleId].isRead == 0)      ?       feedClass += 'unread'
                                                                            :       feedClass += 'read';
                }
                else feedClass += '';
                   
				if (j < vars['nb'])
				{
                    l_s += '<div id="'+feedId+'" class="'+feedClass+'">'
                        + '<div class="articleborder">'
                        + '<div class="article">'
                        + '<div style="float: right;">'+l_addedFct+'</div>'
                        + '<div class="articleDisplayType'+l_widgetDisplayPref+'">'
                        + '<div class="articleDisplayType'+l_widgetDisplayPref+''+j+'">'
                        + $p.app.widgets.rss.displayFeedImages(v_tab,RssArticleId,j,l_widgetDisplayPref)//display feed images
                        + $p.app.widgets.rss.displayFeedTitle(v_tab,vars,RssArticleId,j,l_widgetDisplayPref) //display feed title
                        + $p.app.widgets.rss.displayFeedDate(v_tab,RssArticleId); //display article date
                
          
                //display title format
                //l_s+=$p.app.widgets.rss.displayFeedTitleFormat(l_tab,i,j);

                if (tab[v_tab].RssArticles[RssArticleId].desc != '' 
					&& ((__displayrssimages == 1 && j == 0 && tab[v_tab].RssArticles[RssArticleId].image != 'x') || __displayrssimages==2)
                    && l_widgetDisplayPref != 0
					)
				{
					l_s +='<div class="summary">'+$p.app.widgets.rss.summarize(tab[v_tab].RssArticles[RssArticleId].desc)+'</div>';
				}
                l_s += '</div>'
                    + '</div>'
                    + '<div class="float_correction" />'
                    + '</div>';
				if (__useRating)
				{
                    var source = "feed";
					$p.app.widgets.rss.loadRating(RssArticleId,l_modId,v_tab,source);
					$p.app.widgets.rss.loadAverageRating(RssArticleId,l_modId,v_tab,source);
					
					l_s += '<div id="user_rating_feed_'+l_modId+'_'+RssArticleId+'"></div>'
                        + '<div id="average_rating_feed_'+l_modId+'_'+RssArticleId+'"></div>';
					//l_s += '</div>';
				}
				l_s += '</div>'
                    //+ l_addedFct
                    + '</div>';
                }   
                j++;
				
			}
			
        }  
        returnDatas['l_s'] = l_s;
        returnDatas['j'] = j;
		if (j < (vars['nb'] + 1))
		{
            $p.app.widgets.rss.lastpage = 1
		}
        return returnDatas;
    },
	/*
		Function: $p.app.widgets.rss.displayPrevious
                                Display previous arrows
                      
                     Parameters:
                       
			l_tab - tab sequence ID
			l_modId -
                                vars - callback variables
                                j - number of articles displayed
           
                    Returns: 
                    
                                HTML code
	*/    
    displayPreviousLink:function(l_tab,l_modId,vars,j)
    {	var v_tab=l_tab;
        var l_s="";
        if (tab[v_tab].module[l_modId].start>0) {
            l_s+=" <a href='#' onclick='$p.app.widgets.rss.nextPage("+tab[v_tab].module[l_modId].uniq+","+vars['nb']+");return false;'>"
               +$p.img('ico_previous3.gif',8,11,lg('previous'),'imgmid')
               +" "
               +lg('previous')
               +"</a> &nbsp;";
        } 
        return l_s;
    },   
	/*
		Function: $p.app.widgets.rss.displayNext
                                Display next arrow
                      
                     Parameters:
                       
			l_tab - tab sequence ID
			l_modId -
                                vars (array) - variables
                                j - number of articles displayed
           
                    Returns: 
                    
                                HTML code
	*/    
    displayNextLink:function(v_tab,l_modId,vars,j)
    {   var l_s = "";
        if (j>vars['nb']) {
            l_s += " <a href='#' onclick='$p.app.widgets.rss.prevPage("+tab[v_tab].module[l_modId].uniq+","+vars['nb']+");return false;'>"
                + lg('next')
                + " "
                + $p.img('ico_next3.gif',8,11,lg('next'),"imgmid")
                + "</a> &nbsp;";
        }   
        return l_s;
    },          
 	/*
		Function: $p.app.widgets.rss.displayNoRSS 
                                Display next and previous arrows
                      
                     Parameters:
                       
			l_tab - tab sequence ID
			l_modId -
                                vars -        
           
                    Returns: 
                    
                                false
	*/    
    displayNoRSS:function(l_tab,l_modId,vars)
    {  	var v_tab = l_tab;
        var l_s="";
            l_s+='<table cellpadding="0" cellspacing="0" width="100%">'
                +'<tr>'
                +'<td>'
                +'</td>'
                +'</tr>'
                +'<tr>'
                +'<td class="rss">'
                +'<table cellpadding="1" cellspacing="3" width="100%">'
                +'<tr>'
                +'<td>'
                +lg("lblNoArticle")
                +'</td>'
                +'</tr>'
                +'</table>'
                +'</td>'
                +'</tr>'
                +'</table>';
        $p.print('module'+tab[l_tab].id+'_'+vars['uniq']+'_c',l_s);
        return false;
    },
 	/*
		Function: $p.app.widgets.rss.noAuthentification 
                                Authentified feed control
                      
                     Parameters:
                       
			l_tab - tab sequence ID
			l_modId -
                                vars - 
                                
                    Returns: 
                    
                                false / true
	*/   
    noAuthentification:function(l_tab,l_modId,vars)
    {	var v_tab = l_tab;
        var l_s=""  
        if (tab[v_tab].module[l_modId].footer=="auth")  {
            l_s+='<table cellpadding="0" cellspacing="0" width="100%">'
                +'<tr>'
                +'<td>'
                +'<a href="#" onclick="$p.app.widgets.param.show('+tab[v_tab].module[l_modId].uniq+');return false;">'
                +$p.img("lock.gif",7,9,"","imgmid")+' '+lg("authFeed")
                +'</a>'
                +'</td>'
                +'</tr>'
                +'</table>';             
            tab[v_tab].module[l_modId].footer="";  
            
            $p.print('module'+tab[v_tab].id+'_'+vars['uniq']+'_c',l_s);
            return false;
        }
       return true;
    },
	/*
		Function: $p.app.widgets.rss.displayModuleError 
                                rss module loading error
                      
                     Parameters:
                       
			response - rss feed xml object
			vars (array) - variables (optionnal)
	*/    
    displayModuleError:function(l_tab,l_modId,vars)
    {	var v_tab = l_tab;
        var l_s = '<table cellpadding="0" cellspacing="0" width="100%">'
            + '<tr>'
            + '<td>'
            + '</td>'
            + '</tr>'
            + '<tr>'
            + '<td class="rss">'
            + '<table cellpadding="1" cellspacing="3" width="100%">'
            + '<tr>'
            + '<td>'
            + lg("lblModIssue")
            + '</td>'
            + '</tr>'
            + '</table>'
            + '</td>'
            + '</tr>'
            + '</table>';

        //avoid waiting icon to stay
//        tab[l_tab].module[l_modId].isLoaded = true;
        $p.app.tabs.create(l_tab);
        $p.print('module'+tab[v_tab].id+'_'+vars['uniq']+'_c',l_s);
        return false;
    },
	/*
		Function: $p.app.widgets.rss.display 
                                Display rss feed articles
                      
                     Parameters:
                       
			response - rss feed xml object
			vars (array) - variables (optionnal)
            
                     See also:
                     
                            $p.app.widgets.rss.displayAll
                            
                            $p.app.widgets.rss.refresh
                            
                            $p.app.widgets.rss.linkClicked
                            
	*/
	display: function(response,vars)
	{
		$p.plugin.hook.launch('app.widgets.rss.display.start');

		//display rss module content
		var l_tab = (vars['tab'] == indef ? $p.app.tabs.sel : vars['tab']),
            l_s = "",
            l_item,
            l_title,
            l_modId = vars['id'],
            tabDisplayInfos = new Array(),
            j = 0,
            displayGranted = false,
            displayHeader = "";

        if (tab[l_tab].locked) return;		

		//bug correction : if a module is removed during its refresh or other module refresh
		if (!tab[l_tab].module[l_modId]) return;//if (!module[l_modId]) l_modId=$p.app.widgets.uniqToId(vars[2]);
		var l_auth = tab[l_tab].module[l_modId].auth;
		
        if (!tab[l_tab].module[l_modId].isLoaded 
            && response != indef) {
                $p.app.widgets.rss.get(
                                response,
                                l_modId,
                                (vars['nb'] == indef ? 10 : vars['nb']),
                                l_tab
                );
        }
        //Authentification control
        if ($p.app.widgets.rss.noAuthentification(l_tab,l_modId,vars))
        {
     		if (tab[l_tab].module[l_modId] && tab[l_tab].module[l_modId].isLoaded)
    		{			
    			displayHeader = tab[l_tab].module[l_modId].header;
                tabDisplayInfos = $p.app.widgets.rss.displayRSSFeeds(l_tab,l_modId,vars,l_auth); //Display all the feeds
                j = tabDisplayInfos['j']; // Number of article in the page         
                
                /*l_s += '<table cellpadding="0" cellspacing="0" width="100%" class="FeedFrame">'
                    + '<tr>'
                    + '<td  class="FeedDisplayHeader" >'
                    + displayHeader
                    + '</td>'
                    + '</tr>'
                    + '<tr>'
                    + '<td class="rss">'
                    + '<table cellpadding="1" cellspacing="3" width="100%" height="'+widgetHeight+'" id="FeedDisplayInfo" class="FeedDisplayInfo">'
                    + tabDisplayInfos['l_s'];*/
                l_s += '<div class="FeedFrame">'
                    + '<div  class="FeedDisplayHeader" >'
                    + displayHeader
                    + '</div>'
                    + '<div class="rss">'
                    + '<div id="FeedDisplayInfo" class="FeedDisplayInfo">'
                    + tabDisplayInfos['l_s'];

				if (j == 0) {
					l_s += lg("lblNoArticle");
                }
                l_s += '</div>'
                    + "<div style='text-align:right;padding-top: 5px;'>"
                    + $p.app.widgets.rss.displayPreviousLink(l_tab,l_modId,vars,j)
                    + $p.app.widgets.rss.displayNextLink(l_tab,l_modId,vars,j)
                    + '</div>'
                    + '</div>'
                    + '<div>'
                    + tab[l_tab].module[l_modId].footer
                    + '</div>';
            }
            else
            {
                $p.app.widgets.rss.displayModuleError(l_tab,l_modId,vars);
            }
		}
        
		$p.print('module'+tab[l_tab].id+'_'+vars['uniq']+'_c',l_s);
		$p.app.widgets.rss.moduleUnread(vars['uniq'],l_tab,l_modId);
		//reset icon if linked to the feed favicon
		$p.app.widgets.changeIcon(l_modId,indef,l_tab);
		//refresh newpaper mode
		if (l_tab == $p.app.tabs.sel 
            && tab[l_tab].showType == 1) {
                $p.app.widgets.rss.reader.showArticlesList(true,$p.app.widgets.rss.reader.currSrc);
        }

		$p.plugin.hook.launch('app.widgets.rss.display.end');
	},
	/*
		Function: $p.app.widgets.rss.nextPage 
                                Display next articles
                      
                     Parameters:
                       
			v_id - module Uniq ID
                                v_nb - number to start from
	*/
	nextPage:function(v_id,v_nb)
	{
		//if (tab[v_tab].module[v_id].start>=v_nb)
		//{
		var v_tab=$p.app.tabs.sel;
		var l_id=$p.app.widgets.uniqToId(v_id);
		tab[v_tab].module[l_id].start-=v_nb;
		//}
		$p.app.widgets.rss.refresh(l_id,v_tab,true);
	},
	/*
		Function: $p.app.widgets.rss.prevPage
                               Display next articles
                               
                     Parameters:
                       
			v_id - module Uniq ID
                                v_nb - number to start from
	*/
	prevPage:function(v_id,v_nb)
	{
		var v_tab = $p.app.tabs.sel;
		var l_id=$p.app.widgets.uniqToId(v_id);
		tab[v_tab].module[l_id].start+=v_nb;
		$p.app.widgets.rss.refresh(l_id,$p.app.tabs.sel,true);
	},
	/*
		Function: $p.app.widgets.rss.displayAll
        
                                Display articles of all rss modules in current page
	*/
	displayAll:function()
	{
		for (var i=0;i<tab[$p.app.tabs.sel].module.length;i++)
		{
			if (tab[$p.app.tabs.sel].module[i].format=='R')
			{
				$p.app.widgets.rss.display(indef,
                                           new Array(  i,
                                                        ( $p.string.getVar(tab[$p.app.tabs.sel].module[i].vars,"nb")  ?    $p.string.getVar(tab[$p.app.tabs.sel].module[i].vars,"nb") 
                                                          :                                                                5
                                                        ),
                                                        tab[$p.app.tabs.sel].module[i].uniq
                                                      )
                                            );
			}
		}
	},
	/*
		Function: $p.app.widgets.rss.moduleUnread
                               Compute number of unread articles
                               
                     Parameters:
                       
			v_id - module Uniq ID
			v_tab - tab sequence ID
	*/
	moduleUnread:function(v_id,v_tab,l_modId)
	{
		if (v_tab == indef) v_tab = $p.app.tabs.sel;

		var l_id = $p.app.widgets.uniqToId(v_id,v_tab),
            l_content = $("module"+tab[v_tab].id+"_"+v_id+"_c"),
            nbur = 0;

		if (__registerfeeds && !tab[v_tab].module[l_id].auth)
		{
			for (var RssArticleId in tab[v_tab].feeds[l_modId])
			{
				if (tab[v_tab].RssArticles[RssArticleId].isRead == 0) 
                {   
                    nbur++;
                }
			}
		}
		else
		{
			var l_link = l_content.getElementsByTagName("a");
			for (var RssArticleId in l_link)
			{
				var l_node = l_link[RssArticleId];
				if (l_node.target && l_node.target == 'npdetailf')
				{
					if (document.all)   {
						if (l_node.currentStyle.color!="#999999") nbur++;
					}
					else    {
						if (window.getComputedStyle(l_node, null).color!="rgb(153, 153, 153)") nbur++;
					}
				}
			}
		}
        //lastpage must be on the feeds and not on the primary object
		if (tab[v_tab].module[l_id].nbunread == indef && $p.app.widgets.rss.lastpage == 0) 
        {
         //   if (tab[v_tab].module[l_id].nbunread > 0) {
                tab[v_tab].module[l_id].nbunread = (nbur - 1);//nbur-1 because we count an additional article
                if (tab[v_tab].module[l_id].nbunread < 0) {
                    tab[v_tab].module[l_id].nbunread = 0;
                }
           // }
		}
		else if (tab[v_tab].module[l_id].nbunread == indef && $p.app.widgets.rss.lastpage == 1)  
		{
			tab[v_tab].module[l_id].nbunread = nbur;
			$p.app.widgets.rss.lastpage = 0
		}
		$p.app.widgets.changeName(l_id,indef,v_tab);
		$p.app.widgets.rss.pageUnread(v_tab);
	},
	/*
		Function: $p.app.widgets.rss.linkClicked
                               Treat mouse click on article
                               
                     Parameters:
                       
			v_linkobj - link object clicked
			v_id - module Uniq ID
			l_modId - module sequence ID
			v_nbart - number of article displayed for the module
                                RssArticleId - feed sequence ID
	*/
	linkClicked:function(v_linkobj,v_id,l_modId,v_nbart,RssArticleId)
	{
		var l_id = $p.app.widgets.uniqToId(v_id);
        var v_tab = $p.app.tabs.sel;
		$p.app.counter.activityStep = 0; // reset non activity check

		if (__registerfeeds && !tab[v_tab].module[l_id].auth)
		{
			if (tab[v_tab].RssArticles[RssArticleId].isRead==0)
			{
				$p.app.widgets.rss.saveReadStatus(RssArticleId);
                //$p.app.widgets.rss.saveReadStatus(tab[$p.app.tabs.sel].feeds[v_feedId].id);
				//change datas on the two arrays ()new one is RssArticles
                tab[v_tab].RssArticles[RssArticleId].isRead=1;
               /* if (RssArticleId) {
                    tab[v_tab].RssArticles[RssArticleId].isRead=1;
                }  *///$p.setClass("feed"+tab[$p.app.tabs.sel].id+"_"+v_id+"_"+tab[$p.app.tabs.sel].feeds[v_feedId].id,"linkread");
				$p.setClass("feed"+tab[v_tab].id+"_"+v_id+"_"+RssArticleId,"linkread");
				if (tab[v_tab].module[l_id].nbunread > 0)  {
                    tab[v_tab].module[l_id].nbunread--;
				}
                $p.app.widgets.changeName(l_id);
				$p.app.widgets.rss.pageUnread(v_tab);
			}
		}
		else
		{
			//if (tab[$p.app.tabs.sel].showType==0)
			if (v_linkobj != indef)
			{
				var l_newUnread = false;
				if (document.all){
					if (v_linkobj.currentStyle.color!="#999999" && $p.string.lc(v_linkobj.currentStyle.color)!="#c6c3c6") l_newUnread=true;
				}
				else{
					if (window.getComputedStyle(v_linkobj, null).color!="rgb(153, 153, 153)" 
                            && window.getComputedStyle(v_linkobj, null).color!="rgb(156, 0, 0)") 
                           {
                            l_newUnread = true;
                           }
				}
				if (l_newUnread){
					if (tab[v_tab].module[l_id].nbunread > 0) 
                    {    
                        tab[v_tab].module[l_id].nbunread--;
					}
                    $p.app.widgets.changeName(l_id);
					$p.app.widgets.rss.pageUnread();
				}
			}
		}
		//if already in reader mode > no action
		if (tab[v_tab].showType == 0 
                && tab[v_tab].RssArticles[RssArticleId].link.indexOf('javascript:') ==-1)
		{
			// get widget article opening preference
			var l_widgetOpenPref = getVar(tab[v_tab].module[l_id].vars,'popenin');
			if (l_widgetOpenPref == '') l_widgetOpenPref = 0;

			if (tab[v_tab].usereader == 1 
                    && tab[v_tab].module[l_id].usereader 
                    && l_widgetOpenPref == "0")
			{
				//init reader mode
				$p.app.widgets.rss.reader.init();
				//select clicked article
				//$p.app.widgets.rss.reader.showDetail(indef,tab[$p.app.tabs.sel].feeds[v_feedId].id);
                $p.app.widgets.rss.reader.showDetail(indef,RssArticleId);
				//readerarticle"+$p.app.tabs.sel+"_"+v_feedId
				$p.app.widgets.rss.reader.load(l_modId);
				// no page preloading if feeds are managed in the DB (no use for read status update)
				if (__registerfeeds && !tab[v_tab].module[l_id].auth) return false;
				//if no link, do not open destination file
                //if ((tab[$p.app.tabs.sel].feeds[v_feedId].link).indexOf("noplink")!=-1) return false;
				if ((tab[v_tab].RssArticles[RssArticleId].link).indexOf("noplink")!=-1) return false;
                //return (tab[$p.app.tabs.sel].feeds[v_feedId].link==""?false:true);
				return (tab[v_tab].RssArticles[RssArticleId].link==""?false:true);
			}
			else if (l_widgetOpenPref == "2")
			{
                //$p.app.widgets.rss.openInTab(tab[$p.app.tabs.sel].feeds[v_feedId].link,tab[$p.app.tabs.sel].module[l_id].name);
				$p.app.widgets.rss.openInTab(tab[v_tab].RssArticles[RssArticleId].link,tab[v_tab].module[l_id].name);
				return false;
			}
			else
			{
				//if ($p.navigator.IE) return true;
                //$p.url.openLink(tab[$p.app.tabs.sel].feeds[v_feedId].link,true);
				$p.url.openLink(tab[v_tab].RssArticles[RssArticleId].link,true);
				//link visited status is not instantaneous with FF
				if (!__registerfeeds ||  tab[v_tab].module[l_id].auth) 
                {
                    setTimeout("$p.app.widgets.rss.display(indef,new Array("+l_modId+","+v_nbart+","+v_id+"))",1000);
				}
                return false;
			}
		}
	},
	/*
		Function: $p.app.widgets.rss.saveReadStatus
                               Save the status of the article as read
                               
                     Parameters:
                       
			v_id - article ID
	*/
	saveReadStatus:function(v_id)
	{
		$p.ajax.call(posh["scr_feed_changestatus"],
			{
				'type':'execute',
				'variables':"artid0="+v_id+"&s0=1"
			}
		);
	},
	/*
		Function: $p.app.widgets.rss.openInTab
                               Open a rss article in a Posh tab
                               
                     Parameters:
                       
			v_link - link of the article
                                v_tabName -  Title of the page
	*/
	openInTab:function(v_link,v_tabName)
	{
		var l_name=v_tabName.substr(0,20);
		$p.app.tabs.openTempLink(l_name,v_link);
	},
	/*
		Function: $p.app.widgets.rss.readAll
        
                               Set all articles as read for a module
                               
                     Parameters:
                       
			v_tab - tab sequence ID
                                v_modId - 
                                
                     Returns:
                     
                                false
	*/
	readAll:function(v_tab,v_modId,RssArticleId)
	{
		var l_s="",inc=0;
		for (var RssArticleId in tab[v_tab].feeds[v_modId])
		{
            $p.setClass("feed"+tab[v_tab].id+"_"+tab[v_tab].module[v_modId].uniq+"_"+RssArticleId,"linkread");
            l_s+="artid"+inc+"="+RssArticleId+"&s"+inc+"=1&";
            tab[v_tab].RssArticles[RssArticleId].isRead=1;
            inc++;
		}
		$p.ajax.call(posh["scr_feed_changestatus"],
			{
				'type':'execute',
				'variables':l_s
			}
		);
		tab[v_tab].module[v_modId].nbunread=0;
		$p.app.widgets.changeName(v_modId);
		$p.app.widgets.rss.pageUnread();
		return false;
	},
	/*
		Function: $p.app.widgets.rss.pageUnread
                               Compute number of unread articles in the page
                               
                     Parameters:
                       
			v_tab - tab sequence ID
	*/
	pageUnread:function(v_tab)
	{
		if (v_tab==indef) v_tab=$p.app.tabs.sel;
		if ($p.app.user.id==-1) return;
		tab[v_tab].nbUnread=0;
		for (var i=0;i<tab[v_tab].module.length;i++)
		{
			if (tab[v_tab].module[i].nbunread>0) 
            {
                tab[v_tab].nbUnread+=tab[v_tab].module[i].nbunread;
            }
		}
		if (tab[v_tab].nbUnread>0 && !tab[v_tab].temporary)
		{
			$p.print("tabextra"+v_tab,"("+tab[v_tab].nbUnread+")");
		}
        //next condition is not very useful : see some lines
		if (v_tab==$p.app.tabs.sel) $p.navigator.changeTitle();
	},
	/*
		Function: $p.app.widgets.rss.showOverview
                                Display article overview in tooltip
                                
		Parameters:

			v_id - feed sequence ID
			event - mouse event object
	*/
	showOverview:function(v_id,event)
	{
        var v_tab = $p.app.tabs.sel
		//if (tab[$p.app.tabs.sel].feeds[v_id]) 
        if (tab[v_tab].RssArticles[v_id]) 
        {
            mouseBox(result,event);
		}
        //tab[v_tab].RssArticles[v_id].l_desc;
        /*
        var l_desc=
                    (tab[$p.app.tabs.sel].feeds[v_id] ?
                            tab[$p.app.tabs.sel].feeds[v_id].desc   :   ""
                    );
                    
         */           
        var l_desc=
                    (tab[v_tab].RssArticles[v_id] ?
                            tab[v_tab].RssArticles[v_id].desc   :   "" 
                    );	                 
/*
        var result=(tab[$p.app.tabs.sel].feeds[v_id]?
                            "<b>"+tab[$p.app.tabs.sel].feeds[v_id].title+"</b><br />"   :   ""
                    );
*/
        var result=(tab[v_tab].RssArticles[v_id]?
                            "<b>"+tab[v_tab].RssArticles[v_id].title+"</b><br />"   :   ""
                    );                    
                    
		if (__displayrssdesc==1)
		{
			result+=$p.app.widgets.rss.summarize(l_desc);
		}
		else
		{
			result+=l_desc;
		}
        if (tab[v_tab].RssArticles[v_id]) mouseBox(result,event);
		//if (tab[$p.app.tabs.sel].feeds[v_id]) mouseBox(result,event);
	},
	/*
		Function: $p.app.widgets.rss.summarize
                                Summarize an article
		
                     Parameters:
                     
			v_desc (string) - article description to summarize
            
		Returns
        
			(string)  short description
	*/
	summarize:function(v_desc)
	{
		var l_desc=$p.string.removeTags(v_desc);
		return $p.string.trunk(l_desc,100,true);
	},
	/*
		Function: $p.app.widgets.rss.hideOverview
                                Hide article overview
	*/
	hideOverview:function()
	{
		mouseBox("");
	},
	/*
		Function: $p.app.widgets.rss.secure
                                Format RSS article to avoid javascript injection
                                
		Parameters:

			 v_s - rss article string
             
                      Returns:
                      
                                string formatted  
	*/
	secure:function(v_s)
	{
		var l_s=v_s.replace(/&lt;/g,"<");
		l_s=l_s.replace(/&gt;/g,">");
		l_s=$p.string.correctEncoding(l_s);
		l_s=l_s.replace(/<script/g,"<!--");
		l_s=l_s.replace(/<\/script>/g,"-->");
		return l_s;
	},
	/*
		Function: $p.app.widgets.rss.importMenu
                                Import OPML file form
             
                      Returns:
                      
                                false
	*/
	importMenu:function()
	{
		var l_s="<table cellpadding='5' cellspacing='0' width='100%'>";
		l_s+="<tr><td class='bottomhr'><b>"+lg("lblImportOpml")+"</b><br /><br />";
		l_s+="<form enctype='multipart/form-data' method='post' action="+posh["scr_opml_import"]+" target='iopml'>";
		l_s+="<input type='file' name='opml' /> <input type='submit' class='submit' value='"+lg("lblUpload")+"' />";
		l_s+="</form>";
        l_s+="<iframe style='display:none' src='about:blank' id='iopml' name='iopml' onload='$p.app.widgets.rss.importTreat()'></iframe>";
        l_s+="<div id='opmlres'></div>";
        l_s+="</td></tr>\n";
		l_s+="<tr><td><p><b>"+lg("lblExportOpml")+"</b></p>";
		l_s+="<input type='button' value='"+lg("lblExportBtn")+"' onclick='$p.app.widgets.rss.exportOpml()' />";
		l_s+="</td></tr></table>";
		$p.print("box",box(0,lg("lblImport"),"hideBox()",l_s));
		$p.show("box","block");
		return false;
	},
	/*
		Function: $p.app.widgets.rss.importTreat
                                Treat OPML file
	*/
	importTreat:function()
	{
		var i = $("iopml");
        
		if (i.contentDocument)  {   
            var d = i.contentDocument;  
        }
		else if (i.contentWindow)   {
            var d = i.contentWindow.document;   
        }
		else    {   
            var d = window.frames["iopml"].document;    
        }
		if (d.location.href == "about:blank")    return; 
		if (d.body.innerHTML=="")   
        {   
        $p.app.alert.show(lg("msgXMLerror"));   
        }
		else    
        {
			$p.app.widgets.rss.opmlfile=d.body.innerHTML;
			getXml($p.app.widgets.rss.opmlfile,$p.app.widgets.rss.importSelectTab,"");
		}
	},
	/*
		Function: $p.app.widgets.rss.importSelectTab
                                Import selected tab from OPML file
                                
                     Parameters:
                     
                                response - XML object
                                vars - variables
	*/
	importSelectTab:function(response,vars)
	{
		if (response.getElementsByTagName("opml")[0] || response.getElementsByTagName("body")[0])
		{
			var opmlcont=response.getElementsByTagName("body")[0]   ?
                                        response.getElementsByTagName("body")[0]    :
                                        response.getElementsByTagName("opml")[0]
                                        ;
			$("opmlres").set('html','<p>'+lg("lblChooseOpmlTab")+'</p>:');
			for (var i=0;i<opmlcont.childNodes.length;i++)
			{
				var result=opmlcont.childNodes[i];
				if ($p.string.lc(result.nodeName)=="outline") 
				{
					var aLvlOpml = new Element('a', 
						{ 
							'events': {
										'click': function(){
											$p.app.widgets.rss.getImportTab(this.name); 
											}
									  },
							'href':'#'
						} 
					);
					aLvlOpml.name = i;
					aLvlOpml.set('html',$p.ajax.getProp(result,"title","str",false,"empty")+'<br />');
					aLvlOpml.inject($("opmlres"));
				}
			}
			var divOpml = new Element('div', { 'id':'opmlfeeds' } );
			divOpml.inject($("opmlres"));
		}
		if (l_s=="" || l_s==indef) 
		$("opmlres").set('html',lg("msgXMLerror"));	
	},
	/*
		Function: $p.app.widgets.rss.getImportTab
                                Load selected tab in OPML file
                                
                     Parameters:
                     
                                v_id - selected tab ID
	*/
	getImportTab:function(v_id)
	{
		getXml($p.app.widgets.rss.opmlfile,$p.app.widgets.rss.addImportTab,v_id);
	},
	/*
		Function: $p.app.widgets.rss.addImportTab
                                Add new tab based on select tab of OPML file
                                
                     Parameters:
                     
                                response - XML object
                                vars (array) - variables
	*/
	addImportTab:function(response,vars)
	{
		var l_s="<p>"+lg("selectFeedsToImport")+"</p>: ";
		if (response.getElementsByTagName("opml")[0])
		{
			var opmlcont=response.getElementsByTagName("body")[0]   ?
                                    response.getElementsByTagName("body")[0]    :
                                    response.getElementsByTagName("opml")[0]
                                    ;
			var selOutline=opmlcont.childNodes[vars];
			for (var i=0;i<selOutline.childNodes.length;i++)
			{
				var result=selOutline.childNodes[i];
				if ($p.string.lc(result.nodeName)=="outline")   {
					l_s+="<a href='#' "
                            +"onclick=\"$p.app.widgets.rss.checkFeed('"+$p.string.unesc($p.ajax.getProp(result,"xmlUrl","str",false,""))+"');return false;\">"
                            +$p.ajax.getProp(result,"title","str",false,"")+"</a>,<br /> ";	
				}
			}
		}
		$p.print("opmlfeeds",l_s);	
	},
	/*
		Function: $p.app.widgets.rss.exportOpml
                                Generate OPML file from user pages
	*/
	exportOpml:function()
	{
		$p.url.openLink(posh["xmlopmlexport"],true);
	},
	/*
		Function: $p.app.widgets.rss.getId
                                 Get feed sequence ID based on article DB ID
                                 
		Parameters:

			 v_id - article DB ID
		
                     Returns:
                     
			 article sequence ID
	*/
	getId:function(v_id)
	{
        var v_tab = $p.app.tabs.sel;
		for (var i=0;i<tab[v_tab].feeds.length;i++)
		{
			if (tab[v_tab].feeds[i].id==v_id) return i;
		}
		return v_id;
	},
	/*
		Function: $p.app.widgets.rss.getTopArticles
                                 Load top articles
	*/
	getTopArticles:function()
	{
		getXml(posh["xmltoparticles"],$p.app.widgets.rss.displayTopArticles);
	},
	/*
		Function: $p.app.widgets.rss.displayTopArticles
                                 Display top articles
                                 
                     Parameters:
                     
                                 response - XML object
	*/
	displayTopArticles:function(response)
	{
		var l_s="<ul>",l_result=response.getElementsByTagName('article');
		for (var i=0;i<l_result.length;i++)
		{
			l_s+="<li>"+$p.ajax.getVal(l_result[i],'title','str',false,'???')+"</li>";
		}
		l_s+="</ul>";

		$p.print('rsstoparticles',l_s);
	},
    /*
		Function: $p.app.widgets.rss.stopAllLoadings
                                 Stop RSS feeds loading of the page
                                 
                     Parameters:
                     
                                 v_tab : tab concerned
	*/
    stopAllLoadings: function(v_tab)
    {
        if (v_tab == indef)
            v_tab = $p.app.tabs.sel;
        //for all widgets RSS not loaded
        for (var i = 0;i < $p.ajax.requests.length;i++)
        {
            if ($p.ajax.requests[i])
            {
                if ($p.ajax.requests[i] && $p.ajax.requests[i].callerId == 'getWidgetArticles')
                {
                    $p.ajax.requests[i].kill();
                }
            }
        }
    }
}

/*
    Class: $p.app.statistics
*/
$p.app.statistics={
	contentDiv:'',
	/*
		Function: $p.app.statistics.initMenu
                                 Init the statistics menu
	*/
	initMenu:function()
	{
		$p.app.menu.options.push(
			{
				"id":"info",
				"label":lg("buzz"),
				"desc":lg("statsIconDesc"),
				"icon":"ico_comment.gif",
				"seq":50,
				"action":"$p.app.statistics.menu()",
				"type":"",
				"pages":[]
			}
		);
	},
	/*
		Function: $p.app.statistics.menu
                                 Build the statistics menu
	*/
	menu:function()
	{
		/*$p.app.menu.addTitle('statsmenu_1','',lg('mySpace'));
		var l_s=img('puce.gif')+'&nbsp;<b>'+lg('myNotebook')+'</b><br />'
			+'- <a href="#" onclick=\'$p.notebook.getRecentComments();return false;\'>'+lg('lastComments')+'</a><br />'
			+'- <a href="#" onclick=\'$p.notebook.getmostcommented();return false;\'>'+lg('MostCommented')+'</a><br />'
			+'<br />'
			+img('puce.gif')+'&nbsp;<b>'+lg('myPages')+'</b><br />'
			+'- <a href="#" onclick=\'$p.app.pages.initSummary();return false;\'>'+lg('latestArticlesOfMyPages')+'</a>';
		$p.app.menu.addArea('statsmenu_1',l_s);
*/
        var l_s = '';
        if (__useNotebook)
        {
            l_s += '<b>'+lg('mySpace')+'</b><br />'
                +'<a href="#" onclick=\'$p.notebook.getRecentComments();return false;\'>'+lg('lastComments')+'</a><br />'
                +'<a href="#" onclick=\'$p.notebook.getmostcommented();return false;\'>'+lg('MostCommented')+'</a><br />';     
        }
        
        l_s += '<br /><b>'+lg('userSpace')+'</b><br />'
            + '<a href="#" onclick="$p.app.statistics.loadMostRead(0);return false;">'+lg('mostReadArticles')+'</a><br />'
            + '<a href="#" onclick=\'$p.app.pages.initSummary();return false;\'>'+lg('latestArticlesOfMyPages')+'</a><br /><br />';

        $p.app.menu.addHTML('statsmenu_1',l_s);
    },
	/*
		$p.app.statistics.init : init statistics pages
	*/
	init:function()
	{
		$p.app.newEnv('statistics');
		$p.app.tabs.openTempTab(3,"$p.plugin.openInTab(%tabid%,function(){},'stats')",lg('statistics'),'../images/ico_stat.gif');
		$p.app.statistics.contentDiv='modules'+tab[$p.app.tabs.sel].id;
	},
	/*
		Function: $p.app.statistics.loadMostRead
                                 Load most read articles
                                 
                     Parameters:
                     
                                 v_page - number of article for a page 
	*/
	loadMostRead:function(v_page)
	{
		$p.app.statistics.init();

		$p.print($p.app.statistics.contentDiv,$p.html.buildTitle(lg("mostReadArticles"))+"<div id='plugindiv'></div>");
        
		$p.ajax.call(posh["xmlmostread"]+'?p='+v_page,
			{
				'type':'load',
				'callback':
				{
					'function':$p.app.statistics.displayMostRead,
					'variables':{'page':v_page}
				}
			}
		);
	},
	/*
		Function: $p.app.statistics.displayMostRead
                                 Display most read articles
                                 
                     Parameters:
                     
                                 response - XML object
                                 vars (array) - variables
	*/
	displayMostRead:function(response,vars)
	{
		var l_result = response.getElementsByTagName('article'),
            l_s = '';
		for (var i = 0;i < l_result.length;i++)
		{
			l_s += '<div style="float: left;width: 20px;">'
                + '<img src="'+_dirImg+'rss'+$p.ajax.getVal(l_result[i],'sourceid','int',false,0)+'" width="16" height="16" />'
                + '</div>'
                + '<div style="float: left;">'
                + '<a href="'+$p.ajax.getVal(l_result[i],'link','str',false,'#')+'" target="_blank">'
                + $p.ajax.getVal(l_result[i],'title','str',false,'-')
                + '</a> '
                + '('+$p.ajax.getVal(l_result[i],'nbread','str',false,'1')+')'
                + '<br />'
                + '<span class="source">'+$p.ajax.getVal(l_result[i],'source','str',false,'')+'</span>'
                + '</div>'
                + '<div class="float_correction"></div>';
		}

		$p.print('plugindiv',l_s);
	}
}

//************************************* WIDGETS RSS READER FUNCTIONS ***************************************************************************************************************
/*
    Class: $p.app.widgets.rss.reader
         Widgets rss reader functions
*/
$p.app.widgets.rss.reader={
	selArticle:0,
	readerWidth:0,
	readerHeight:0,
	currSrc:indef,
	paneWidth:300,
	prevEnv:'',
	/*
		Function: $p.app.widgets.rss.reader.init
                                Init rss reader
	*/
	init: function()
	{	
		//hide modules
		$p.show("modules","none");
		$p.show("newspaper","block");
		$p.show("footer","none");
		$p.app.widgets.rss.reader.prevEnv = $p.app.env;
		$p.app.newEnv('portal_reader');
		//hide menus
		hideAllBox();
		mouseBox("");	

        var l_s = '';
		var l_obj = $("newspaper");

		if ((l_obj) && (l_obj.innerHTML != ''))
            l_obj.empty();

		if (!l_obj)
		{
			var l_div = $("modules");
			l_obj = null;
			l_obj = new Element('div', 
				{
					'styles': {
						'display': 'none',
						'verticalAlign': 'top'
					},
					'id': 'newspaper'
				}
			);
			l_div.appendChild(l_obj)
		}
		
		//compute frames width
		$p.app.widgets.rss.reader.computeSize();

        l_s += '<div id="myreader">'
            + '<div class="newspaper">'
            + '<div class="sourcelist" style="float: right;">'
            + '<a href="#" onclick="$p.app.widgets.rss.reader.close();$p.app.widgets.rss.displayAll();">'
            + $p.img("ico_close.gif",12,11,lg("lblClose"),"imgmid")+" "+lg("lblClose")
            + '</a> &nbsp; '
            + '</div>'
            + '<div class="sourcelist" id="sourcelist">'
            + lg('lblLoading')
            + '</div>'
            + '<table cellpadding="0" cellspacing="0">'
            + ' <tbody>'
            + '  <tr>'
            + '   <td style="width: 300px;vertical-align: top;padding-left: 5px;">'
            + '   <div id="nparticles" style="width: 300px;height: '+$p.app.widgets.rss.readerHeight+'px;overflow: auto;vertical-align: top;">'
            + '   </div>'
            + '   </td>'
            + '   <td id="npdetail" class="frame" width="'+$p.app.widgets.rss.readerWidth+'" height="'+$p.app.widgets.rss.readerHeight+'">&nbsp;'
            + '   </td>'
            + '  </tr>'
            + ' </tbody>'
            + '</table>'
            + '</div>'
            + '</div>';

        $p.print('newspaper',l_s);

		tab[$p.app.tabs.sel].showType = 1; // rss reader active		
	},
	/*
		Function: $p.app.widgets.rss.reader.load
                                  Load feeds articles
		
                     Parameters:

			 v_scr - module sequence ID of the feed
	*/
	load: function(v_src)
	{
		var l_inc = 0,l_allLoaded = true;
		for (var i = 0;i < tab[$p.app.tabs.sel].module.length;i++)
		{
			if (tab[$p.app.tabs.sel].module[i].format == 'R' && !tab[$p.app.tabs.sel].module[i].isLoaded)
			{
				l_allLoaded = false;
				$p.app.widgets.rss.refresh(i);
			}
		}
		$p.app.widgets.rss.reader.showArticlesList(true,v_src);
	},
	/*
		Function: $p.app.widgets.rss.reader.refresh
                                Refresh feeds articles
                      
                     Returns:
                        
                                false
	*/
	refresh: function()
	{
		$p.app.widgets.rss.init($p.app.tabs.sel);
		$p.app.widgets.rss.reader.load($p.app.widgets.rss.reader.currSrc);
		return false;
	},
	/*
		Function: $p.app.widgets.rss.reader.chk
                                Display articles list
	*/
	chk:function(response,vars)
	{
		if ($p.app.widgets.rss.get(response,vars[0],vars[1]))
		{
			$p.app.widgets.rss.reader.showArticlesList(true,$p.app.widgets.rss.reader.currSrc);
		}
	},
	/*
		Function: $p.app.widgets.rss.reader.getSrcList
                                Display the reader sources list
	*/
	getSrcList: function()
	{
		//empty the element for a reload
        $p.app.widgets.rss.reader.emptySrcList();

		var srclink = [];
        var l_s = '<b>'+lg('lblSrc')+' : </b>';

		for (var i = 0;i < tab[$p.app.tabs.sel].module.length;i++)
		{
			if (tab[$p.app.tabs.sel].module[i].format == 'R'){
				if (tab[$p.app.tabs.sel].module[i].isLoaded){
					if ($p.app.widgets.rss.reader.currSrc == i){
						srclink.push(" <font class='sourcesel'>"
                                                    +tab[$p.app.tabs.sel].module[i].name+(tab[$p.app.tabs.sel].module[i].nbunread==0    ?
                                                    ""  :
                                                    " <a href='#' title='"+lg("readAll")+"' onclick='$p.app.widgets.rss.readAll("+$p.app.tabs.sel+","+i+");$p.app.widgets.rss.reader.showArticlesList(false,"+$p.app.widgets.rss.reader.currSrc+");'>("+tab[$p.app.tabs.sel].module[i].nbunread+")</a>")+"</font>");
					}
					else{
						srclink.push(" <a href='#' style='font-weight:normal' onclick='return $p.app.widgets.rss.reader.showArticlesList(false,"+i+")'>"+tab[$p.app.tabs.sel].module[i].name+"</a>"+(tab[$p.app.tabs.sel].module[i].nbunread==0?"":" <a href='#' title='"+lg("readAll")+"' onclick='$p.app.widgets.rss.readAll("+$p.app.tabs.sel+","+i+");$p.app.widgets.rss.reader.showArticlesList(false,"+$p.app.widgets.rss.reader.currSrc+");'>("+tab[$p.app.tabs.sel].module[i].nbunread+")</a>"));
					}
				}
				else
				{
					srclink.push(" "+tab[$p.app.tabs.sel].module[i].name+" "+$p.img("ico_wait.gif",9,9,lg("lblSrcLoading"),"imgmid"));
				}
			}
		}

        l_s += srclink.join(" | ");

		if ($p.app.widgets.rss.reader.currSrc != indef && srclink.length > 1) 
		{
            l_s += '&nbsp;&nbsp; | '
                + '<a href="#" onclick="return $p.app.widgets.rss.reader.showArticlesList(false);">'
                + lg("lblSrcAll")
                + '</a>'
		}

        $p.app.widgets.rss.reader.displayInSourceList(l_s);

		if (__useGSearch) 
		{
			var centerObj1 = new Element('center');
			var bObj2 = new Element ('b');
			bObj2.set('html',lg("lblSrcSrch"));
			bObj2.inject(centerObj1);
			var divObj1 = new Element('div', { 'id':'searchform', 'width':'350' } );
			divObj1.inject(centerObj1);
			centerObj1.inject($('sourcelist'));	
			var brObj2 = new Element('br');
			brObj2.inject($('sourcelist'));			
			var tableObj1 = new Element('table', { 'width':'100%' });	
			var tbodyObj1 = new Element('tbody');
			var trObj1 = new Element('tr');
			var tdObj1 = new Element('td', { 'valign':'top', 'width':'100%' } );
			var divObj2 = new Element('div', { 'id':'searchResults', 'width':'600' } );
			divObj2.set('html',lg("lblLoading"));
			divObj2.inject(tdObj1);
			tdObj1.inject(trObj1);
			trObj1.inject(tbodyObj1);
			tbodyObj1.inject(tableObj1);
			tableObj1.inject($('sourcelist'));	
		}
		$p.app.widgets.rss.reader.framesSize();
		
	},
    emptySrcList: function()
    {
        if ($('sourcelist').innerHTML != '')
            $('sourcelist').empty();
    },
    displayInSourceList: function(v_s)
    {
        $p.print('sourcelist',v_s);
    },
	/*
		Function: $p.app.widgets.rss.reader.showArticlesList
                                Display feeds of the selected source or from all sources
                                
		Parameters:

			v_sort - sorting type
			v_src - selected source
            
                     Returns:
                     
                                false
	*/
	showArticlesList:function(v_sort,v_src)
	{	
		var v_tab = $p.app.tabs.sel;
		$p.app.widgets.rss.reader.currSrc = v_src;
        var l_modId = $p.app.widgets.rss.reader.currSrc;
		//if multisources, display only once on the last source loading
		$p.app.widgets.rss.reader.getSrcList();
		//if (v_sort) tab[$p.app.tabs.sel].feeds.sort(sortDate);  //remove temporaly due to bug : when portal refresh and user is in the rss reader, once the reader closed, the feeds are not corresponding to the tooltip description !
		i=0;
        
		var l_s = '<div class="ListArticle">';
        
		//while (i<$p.app.widgets.rss.reader.nb && j<$p.app.widgets.rss.feeds.length){
        if ( v_src != "undefined" && typeof(l_modId) == 'number' && l_modId >= 0)
        {
            var res = $p.app.widgets.rss.reader.displayeachLineFeed(v_tab,l_modId);
            l_s = res[0];
            i = res[1];
        }
        else
        {   
            for (var l_modId in tab[v_tab].feeds)
            {
                if ( typeof(tab[v_tab].feeds[l_modId]) == "object")
                {
                    
                    var res = $p.app.widgets.rss.reader.displayeachLineFeed(v_tab,l_modId);
                    l_s += res[0];
                    i += res[1];
                }
            }
        }
        
		if (i == 0)
		{
			l_s += '<div style="width:100%;height:200px">'+lg("lblSrcMissing")+'</div>';
		}
		l_s += '</div>';
        
		$p.print("nparticles",l_s);
		
		if(__useGSearch) initGSearch(true);
		
		return false;	
	},
    /*
    
            function: $p.app.widgets.rss.reader.displayeachLineFeed
            
            parameters: 
            
                v_tab - id od tab
                
                l__modId - id of the module
                
           returns: html
           
           see: $p.app.widgets.rss.reader.showArticlesList
                
    */
    displayeachLineFeed: function (v_tab,l_modId)
    {
        var l_s='';
        var allArticles = new Array();
		for (var RssArticleId in tab[v_tab].feeds[l_modId])
		{
            
            if (    typeof(tab[v_tab].feeds[l_modId][RssArticleId]) != 'undefined' 
                    &&  typeof(tab[v_tab].RssArticles[RssArticleId]) != "undefined")
            {
				var l_id = $p.app.widgets.uniqToId(tab[v_tab].RssArticles[RssArticleId].modUniq);
                var l_class = (RssArticleId == $p.app.widgets.rss.reader.selArticle    ?   "highlight" :   
                                                                                          "normal" +  ((__registerfeeds && !tab[v_tab].module[l_id].auth)
                                                                                                            ?   (tab[v_tab].RssArticles[RssArticleId].isRead == 0
                                                                                                                        ?   'unread'
                                                                                                                        :   'read')
                                                                                                            :   '')
                                                                                       );
				var article  = '<div id="npart'+RssArticleId+'" class="'+l_class+'" style="padding: 5px 8px 5px 5px;">'
                    + '<img src="'+tab[v_tab].module[l_id].icon+'" width="16" height="16" align="left" valign="absmiddle" /> '
                    + '&nbsp;<a class="title" id="readerarticle'+v_tab+'_'+RssArticleId+'" href="'+tab[v_tab].RssArticles[RssArticleId].link+'" target="npdetailf" onclick="return $p.app.widgets.rss.reader.showDetail(this,'+RssArticleId+')">'+tab[v_tab].RssArticles[RssArticleId].title+'</a>'
                    + '<br />'
                    //display the article source
                    + $p.app.widgets.rss.reader.displaySource(RssArticleId)
                    //format the article date
                    + $p.date.formatDelai($p.date.delayFromNow(tab[v_tab].RssArticles[RssArticleId].date))
                    //display the sharing icon
                    + $p.app.widgets.rss.reader.displayIconSharing(RssArticleId)
                    //display the archive icon
                    + $p.app.widgets.rss.reader.displayIconArchive(RssArticleId)
					+'</div>';
                allArticles.push(article);                     
			}
		}
        allArticles.pop();
        for (var i = 0; i < allArticles.length; i++) {
            l_s += allArticles[i];
        }
        return new Array(l_s,i);
    },
     /*
		Function: $p.app.widgets.rss.reader.buildSideBar
                                 Build specific reader sidebar
                                 
		Parameters:

			 v_itemList : array containing items information
                                 
	*/
    buildSideBar: function(v_itemList)
    {
        var l_s = '<div class="ListArticle">',
            l_class;

        for (var i = 0; i < v_itemList.length; i++)
        {
            l_class = (v_itemList[i].id == $p.app.widgets.rss.reader.selArticle ? 'highlight' : 'normal');

            l_s += '<div id="npart'+v_itemList[i].id+'" class="'+l_class+'" style="padding: 5px 8px 5px 5px;">'
                + v_itemList[i].html
                + '</div>';
        }
        l_s += '</div>';
        
        $p.print('nparticles',l_s);
    },
	/*
		Function: $p.app.widgets.rss.reader.displaySource
                                 Display the article source
                                 
		Parameters:

			 j - feed ID in tab
                                 
                     Returns:
                            
                                HTML code
	*/    
	displaySource:function(RssArticleId)
	{   
        return  '<span class="source">'
                +tab[$p.app.tabs.sel].RssArticles[RssArticleId].modName
                +' '
                +((__displayrsssource 
                   && tab[$p.app.tabs.sel].RssArticles[RssArticleId].source!="" )      ?      '| '+tab[$p.app.tabs.sel].RssArticles[RssArticleId].source
                                                                      :      '')+'</span><br />';
    },    
	/*
		Function: $p.app.widgets.rss.reader.displayIconSharing
                                 Display the sharing icon
                                 
		Parameters:

			 j - feed ID in tab
                                 
                     Returns:
                            
                                HTML code
	*/    
	displayIconSharing:function(RssArticleId)
	{   
        if (!($p.app.user.id>0 && __useSharing)) return '';
        else {
              return  " <a href='#' onclick='$p.friends.menu(2,"+tab[$p.app.tabs.sel].RssArticles[RssArticleId].id+")'>"
                        + $p.img("ico_share_s.gif",13,10,lg("lblShareInfo"))
                        + "</a> ";
        }
    },
	/*
		Function: $p.app.widgets.rss.reader.displayIconSharing
                                 Display the sharing icon
                                 
		Parameters:

			 j - feed ID in tab
                                 
                     Returns:
                            
                                HTML code
	*/  
	displayIconArchive:function(RssArticleId)
	{    
        if (!($p.app.user.id>0 && __useArchive)) return '';
        else {
             return " <a href='#' onclick='return $p.article.save("+RssArticleId+")'>"
                    + $p.img("ico_disk_s.gif",8,9,lg("lblSave"))
                    + "</a> ";
        }
    },
	/*
		Function: $p.app.widgets.rss.reader.showDetail
                                 Show article detail in right panel
                                 
		Parameters:

			 v_objClicked - feed DB 
                                 v_id - ID of article in database
                                 
                     Returns:
                            
                                true or false
	*/
	showDetail:function(v_objClicked,v_id)
	{
		//after newspaper sorting, array id change => getId
        //v_id is unique id of article in database
        //l_id is id of article in web page
		//var l_id=$p.app.widgets.rss.getId(v_id);
        var RssArticleId = v_id;
        //id of page selected
        var v_tab = $p.app.tabs.sel;
        //var l_modId=$p.app.widgets.uniqToId(tab[$p.app.tabs.sel].feeds[l_id].modUniq);
        var l_modId=$p.app.widgets.uniqToId(tab[v_tab].RssArticles[RssArticleId].modUniq);
		
		if ($('npdetail').innerHTML!='')    $('npdetail').empty();
		var divObj1 = new Element('div', { 'id':'npdetailititle' } );
		var l_s='<h2>'+tab[v_tab].RssArticles[RssArticleId].title+'</h2>'
			+'<span class="source">'+tab[v_tab].RssArticles[RssArticleId].modName+'</span>'
			+' | '+$p.date.formatDelai($p.date.delayFromNow(tab[v_tab].RssArticles[RssArticleId].date))+((__displayrsssource && tab[v_tab].RssArticles[RssArticleId].source!="" )?' | '+tab[v_tab].RssArticles[RssArticleId].source:'')+' | ';
		
		if (tab[v_tab].RssArticles[RssArticleId].link!="" 
            && (tab[v_tab].RssArticles[RssArticleId].link).indexOf("noplink")==-1) {
			l_s += '<a id="npshowsumbtn" href="#" disabled="disabled" onclick="return $p.app.widgets.rss.reader.showArticlesListSummary('+RssArticleId+');">'+$p.img('ico_rss2.gif',16,16,'','imgmid')+' '+lg("lblOpenSummary")+'</a>'
				+' | <a id="npshowsitebtn" href="#" onclick=\'return $p.app.widgets.rss.reader.showArticlesListSite("'+tab[v_tab].RssArticles[RssArticleId].link+'");\'>'+$p.img('ico_adm_page.gif',16,16,'','imgmid')+' '+lg('lblOpenInReader')+'</a>'
				+' | <a id="npshowonsitebtn" href="#" onclick=\'return $p.url.openLink("'+tab[v_tab].RssArticles[RssArticleId].link+'",true)\'>'+$p.img('ico_next2.gif',12,11,'','imgmid')+' '+lg('lblOpenInNewPage')+'</a>'
				+' | <a href="#" onclick=\'return $p.app.widgets.rss.reader.maximize();\'>'+lg('maximize')+'/'+lg('minimize')+'</a>'
				if (__useRating)
				{	var source = "iframe";
					$p.app.widgets.rss.loadRating(v_id,l_modId,v_tab,source);
					$p.app.widgets.rss.loadAverageRating(v_id,l_modId,v_tab,source);
					l_s += '<div id="user_rating_iframe_'+l_modId+'_'+v_id+'"></div>'
					+ '<div id="average_rating_iframe_'+l_modId+'_'+v_id+'"></div>';
				}
			divObj1.set('html',l_s)
		}
		//ancre3
		divObj1.inject($('npdetail')); 

		var divObj2 = new Element('div', 
		{
				'styles': {
							'overflow':'auto',
							'width':'100%'
				},
				'id': 'npdetaili'
			}
		);
		var divObj3 = new Element('div', { 'id': 'npdetailidesc' } );
		divObj3.set('html',setExternalLink(tab[v_tab].RssArticles[RssArticleId].desc));
		divObj3.inject(divObj2);
		divObj2.inject($('npdetail'));
		var frameObj1 = new Element('iframe', 
			{
				'styles': {
					'width':'100%',
					'height':($p.app.widgets.rss.readerHeight-(__useSharing?20:0))+"px",
					'display':'none'
				},
				'id': 'npdetailf',
				'name':'npdetailf',
				'src':''
			}
	    );						   
		frameObj1.inject($('npdetail'));

		if (__useSharing)
		{
			var divShare = new Element('div',
				{
					'styles': {
						'overflow':'auto',
						'text-align':'center',
						'width':'100%',
						'background':'#c6c3c6',
                        'padding':'4px'
					},
					'id': 'npdetails'
				}
			);
			var aShare = new Element('a',
				{
					'href':'#',
					'styles':{
						'font-weight':'bold'
					},
					'events':{
						'click':function()
						{
							$p.friends.menu(2,RssArticleId);
						}
					}
				}
			);
			aShare.itemid=v_id;
			aShare.inject(divShare);
			var imgShare=new $p.imgObj('mynetwork.gif',16,16,"","imgmid");
			imgShare.inject(aShare);
			aShare.appendText(" "+lg("articleMayInterest"));
			divShare.inject($('npdetail'));
		}

		//($("npshowsumbtn")).disabled=true;
		//un highlight the previous article
		$p.setClass("npart"+$p.app.widgets.rss.reader.selArticle,((__registerfeeds && !tab[$p.app.tabs.sel].module[l_modId].auth)?"normalread":"read"));
		//highlight the selected article
		$p.setClass("npart"+RssArticleId,"highlight");
		$p.app.widgets.rss.reader.selArticle=RssArticleId;
		if (__registerfeeds)
		{
			$p.app.widgets.rss.linkClicked(
                                        v_objClicked,
                                        tab[v_tab].RssArticles[RssArticleId].modUniq,
                                        $p.app.widgets.uniqToId(tab[v_tab].RssArticles[RssArticleId].modUniq),
                                        indef,
                                        RssArticleId
                                        );
			$p.app.widgets.rss.reader.getSrcList();
			if (!tab[$p.app.tabs.sel].module[l_modId].auth) return false;
		}
		
		$p.app.widgets.rss.reader.getSrcList();
		$p.app.widgets.rss.reader.framesSize();
	
		return (tab[v_tab].RssArticles[RssArticleId].link==""?false:true);
	},
    /*
		Function: $p.app.widgets.rss.reader.displayContent
                                Display reader content
	*/
    displayContent: function(v_s)
    {
        $p.print('npdetail',v_s);
    },
	/*
		Function: $p.app.widgets.rss.reader.maximize
                                Maximize reader
	*/
	maximize:function()
	{
		if ($p.get('nparticles').parentNode.style.display == "none")    {
			$p.get('nparticles').parentNode.style.display = "block" ;
            //$p.show('sourcelist','block');
			$p.app.widgets.rss.reader.paneWidth=300;
		}
		else    {
			$p.get('nparticles').parentNode.style.display = "none" ;
            //$p.show('sourcelist','none');
			$p.app.widgets.rss.reader.paneWidth=0;
		}
		$p.app.widgets.rss.reader.computeSize();
		$p.app.widgets.rss.reader.framesSize();
	},
	/*
		Function: $p.app.widgets.rss.reader.showArticlesListSite
        
                                Display web site linked with the read article (in a frame)
                                
                     Parameters:
                     
                                v_scr - selected source
                               
                     Returns:
                     
                                false
	*/
	showArticlesListSite:function(v_src)
	{
        var simplifiedLocation=""+window.location;
        if (simplifiedLocation.indexOf('#')!=-1) {
            var anchorPos = simplifiedLocation.indexOf('#');
            simplifiedLocation = simplifiedLocation.substring(0, anchorPos);
        }

        if (($("npdetailf")).src=="" 
                || ($("npdetailf")).src+"#"==window.location 
                || ($("npdetailf")).src==simplifiedLocation) {
           ($("npdetailf")).src=v_src;
		}
        
        $p.show("npdetaili","none");
		$p.show("npdetailf","block");
		($("npshowsumbtn")).disabled=false;
		($("npshowsitebtn")).disabled=true;
        
		return false;
	},
	/*
		Function: $p.app.widgets.rss.reader.showArticlesListSummary
                            Display article summary loaded from RSS feed
                            
		Parameters:
        
                            v_id - article DB ID
                            
                     Returns:
                     
                            false
	*/
	showArticlesListSummary:function(v_id)
	{
		$p.show("npdetaili","block");
		$p.show("npdetailf","none");
		$p.print("npdetailidesc","<br />"+setExternalLink(tab[$p.app.tabs.sel].RssArticles[v_id].desc));
		($("npshowsumbtn")).disabled=true;
		($("npshowsitebtn")).disabled=false;
		return false;
	},
	/*
		Function: $p.app.widgets.rss.reader.close
                                Close rss reader
	*/
	close:function()
	{
		$p.app.widgets.rss.reader.hide();
		$p.show("modules","block");
		$p.show("footer","block");
		$p.app.newEnv($p.app.widgets.rss.reader.prevEnv);
	},
	/*
		Function: $p.app.widgets.rss.reader.hide
                                Hide rss reader
	*/
	hide:function()
	{
		$p.show('newspaper','none');
		$p.print('npdetail','');
		if ($p.app.tabs.sel!=-1) tab[$p.app.tabs.sel].showType=0;
	},
	/*
		Function: $p.app.widgets.rss.reader.supSrc
                                Remove a rss feed from the reader
                                
		Parameters:

			 v_id - linked module sequence ID
	*/
	supSrc:function(v_id)
	{
		if ($p.app.widgets.suppress(tab[$p.app.tabs.sel].module[v_id].uniq,true)) $p.app.widgets.rss.reader.showArticlesList(false);
	},
	/*
		Function: $p.app.widgets.rss.reader.computeSize
                                 Compute the rss reader size
	*/
	computeSize:function()
	{
		var l_left=$p.getPos($('newspaper'),"Left");
		var l_top=$p.getPos($('newspaper'),"Top");
		$p.app.widgets.rss.readerWidth=Window.getWidth()-l_left-$p.app.widgets.rss.reader.paneWidth-10;
		$p.app.widgets.rss.readerHeight=Window.getHeight()-l_top-70;
	},
	/*
		Function: $p.app.widgets.rss.reader.framesSize
                                Refresh article pane height
	*/
	framesSize:function()
	{
		$p.app.widgets.rss.reader.computeSize();
		var l_pageHeight=Window.getHeight();
		var l_readerTop=$p.getPos(($("nparticles")),"Top");
		var l_detailTop=$p.getPos(($("npdetaili")),"Top");
		var l_readerHeight=((l_pageHeight-l_readerTop)-8)+"px";
		$("nparticles").setStyle("height",l_readerHeight);
		var l_detailHeight=((l_pageHeight-l_detailTop)-26)+"px";
		if ($("npdetaili")!=null)($("npdetaili")).setStyle("height",l_detailHeight);
		if ($("npdetailf")!=null)($("npdetailf")).setStyle("height",l_detailHeight);
		if ($("npdetail")!=null)($("npdetail")).setStyle("height",l_readerHeight);
		$("npdetail").setStyle("width",$p.app.widgets.rss.readerWidth);
	}
}



//**************************************** WIDGETS FACTORY FUCNTIONS *************************************************************************************
/*
    Class: $p.app.widgets.factory
            Widget factory functions
*/
$p.app.widgets.factory={
	shown:false,
	items:[],
	waitItems:[],
    /*
    		Function: object 
                               $p.app.widgets.factory.object  *(Contructor)*
                               
                               Widget factory class
           */
	object:function(id,name,typ,secured)
	{
		this.id=id;
		this.name=name;
		this.typ=typ;
		this.secured=secured;
	},
	menu:new Array({"id":1,"title":"lblArchive2","icon":"mymodules.gif","fct":"$p.app.widgets.factory.display(0,0)","isLink":true},{"id":2,"title":"lblCreateYourMod","icon":"mymodules_create.gif","fct":"$p.app.widgets.factory.createModuleMenu()","isLink":true}),
	/*
		$p.app.widgets.factory.init : init module factory plugin
	*/
	init:function()
	{
        $p.app.menu.hide();
		$p.plugin.open();
		$p.plugin.init(lg("lblArchive2"),'factory');
		$p.app.widgets.factory.load();
		$p.app.setState("$p.app.widgets.factory.init()");
		$p.plugin.useWidget();
		return false;
	},
	/*
		$p.app.widgets.factory.load : load the list of the modules I created
	*/
	load:function()
	{
		$p.ajax.call(posh["xmlmymodules"],
			{
				'type':'load',
				'callback':
				{
					'function':$p.app.widgets.factory.get
				}
			}
		);
	},
	/*
		$p.app.widgets.factory.get : treat the list of the modules I created
	*/
	get:function(response,vars)
	{ 
		var i=0;
		$p.app.widgets.factory.items.length=0;
		$p.app.widgets.factory.waitItems.length=0;
		while (response.getElementsByTagName("module")[i])
		{
			var l_result=response.getElementsByTagName("module")[i];
			if ($p.ajax.getVal(l_result,"status","str",false,"")=="O"){
				$p.app.widgets.factory.items.push(new $p.app.widgets.factory.object($p.ajax.getVal(l_result,"id","int",false,0),$p.ajax.getVal(l_result,"name","str",false,"=="),$p.ajax.getVal(l_result,"typ","str",false,""),$p.ajax.getVal(l_result,"secured","int",false,0)));
			}
			else{
				$p.app.widgets.factory.waitItems.push(new $p.app.widgets.factory.object($p.ajax.getVal(l_result,"id","int",false,0),$p.ajax.getVal(l_result,"name","str",false,"=="),$p.ajax.getVal(l_result,"typ","str",false,""),0));
			}
			i++;
		}
		$p.app.widgets.factory.display(0,0);
	},
	/*
		$p.app.widgets.factory.display : display the list of the modules I created
		Parameters:

			v_page - page of the list
			v_pageVal - ??
	*/
	display:function(v_page,v_pageVal)
	{
		var l_s=$p.html.buildTitle(lg('lblArchive2'))
			+'<table width="100%" cellspacing="10">'
			+'<tr>'
			+'<td width="250" id="factoryaddmenu" valign="top">'
			+'</td>'
			+'<td valign="top">'
			+'<div class="subtitle">'+lg("mymodulesVal")+'</div><br />'
			+'<table>';

		var l_start=v_page*10;
		var l_end=l_start+10;
		for (var i=l_start;i<l_end;i++)
		{
			if (i<$p.app.widgets.factory.items.length)
			{
				l_s+='<tr>'
					+'<td>'
					+'<img src="'+_dirImg+'/box0_'+$p.app.widgets.factory.items[i].id+'" align="absmiddle" width="16" height="16" />'
					+'</td>'
					+'<td>'
					+'<a href="#" onclick=\'$p.app.widgets.open('+$p.app.widgets.factory.items[i].id+',"","uniq",'+($p.app.widgets.factory.items[i].secured==0?false:true)+')\'><b>'+$p.app.widgets.factory.items[i].name+'</b></a>'
					+'</td>'
					+'</tr>';
                    
				if ($p.app.widgets.factory.items[i].typ=="R") {
                    l_s+="<tr><td></td><td>[<a href='"+posh["redactor_article_modify_add"]+"'?mid="
                       +$p.app.widgets.factory.items[i].id
                       +"'>"
                       +lg("addArticle")
                       +"</a>] [<a href="+posh["redactor_feed"]+"'?mid="
                       +$p.app.widgets.factory.items[i].id
                       +"'>"
                       +lg("feedDetail")
                       +"</a>]</td></tr>";
                }
			}
		}
		l_s+='</table>'
			+($p.app.widgets.factory.items.length==0 ? "<i>"+lg("mymodulesValNone")+"</i>" : '')
			+$p.html.buildPageNavigator('previous',(v_page==0 ? '' : "$p.app.widgets.factory.display("+(v_page-1)+","+v_pageVal+")"),'','next',($p.app.widgets.factory.items.length<=i ? '' : "$p.app.widgets.factory.display("+(v_page+1)+","+v_pageVal+")"))
			+'<br /><br />'
			+'<div class="subtitle">'+lg("mymodulesWait")+'</div>'
			+'<br />'
			+'<table>';
		var l_startVal=v_pageVal*10;
		var l_endVal=l_start+10;
		for (var i=l_startVal;i<l_endVal;i++)
		{
			if (i<$p.app.widgets.factory.waitItems.length)
			{
				l_s+='<tr>'
					+'<td>'
					+'<img src="../modules/quarantine/icon'+$p.app.widgets.factory.waitItems[i].id+'" align="absmiddle" width="16" height="16" />'
					+'</td>'
					+'<td>'
					+'<b>'+$p.app.widgets.factory.waitItems[i].name+'</b>'
					+'</td>'
					+'</tr>';
			}
		}
		l_s+='</table>'
			+($p.app.widgets.factory.waitItems.length==0 ? "<i>"+lg("mymodulesWaitNone")+"</i>" : '')
			+$p.html.buildPageNavigator('previous',(v_pageVal==0 ? '' : "$p.app.widgets.factory.display("+v_page+","+(v_pageVal-1)+")"),'','next',($p.app.widgets.factory.waitItems.length<=i ? '' : "$p.app.widgets.factory.display("+v_page+","+(v_pageVal+1)+")"))
			+'</td>'
			+'</tr>'
			+'</table>';

		$p.plugin.content(l_s);
		$p.app.widgets.factory.shown=true;
        
		$p.app.widgets.factory.createModuleMenu();
	},
    displayTutorial:function()
    {
        $p.print("plugincontent",'<iframe src="'+posh["tutorial"]+'" width="100%" height="800" frameborder="no" marginwidth="0" marginheight="0" scrolling="auto"></iframe>');
    },
	/*
		$p.app.widgets.factory.createModuleMenu : display modules factory options
	*/
	createModuleMenu:function()
	{
        var l_s='';
        l_s+='<img src="../images/ico_moduleandrss.gif" align="absmiddle" />\
              <span><a href="#" onclick="$p.app.widgets.factory.displayTutorial();return false;">'+lg("createYourModules")+'</a></span>';   
        $p.print("factoryaddmenu",l_s);
  
        /*
		var divLvl1 = new Element('b');
		divLvl1.set('html',lg("addRssFeed")+ " :<br /><br />");
		divLvl1.inject($("factoryaddmenu"));
				
		var aLvl1 = new Element('a', { 'href':posh["rssaddtodirectory"], 'target':'_createmodule' } );
		var imgLvl1 = new Element('img', { 'src':'../images/ico_rsstomodule.gif', 'align':'absmiddle' } );
		var bLvl1 = new Element('span');
		bLvl1.set('html',lg("addRssFeedToDirectory")+"<br /><br />");
		imgLvl1.injectInside(aLvl1);
		bLvl1.injectInside(aLvl1);
		aLvl1.inject($("factoryaddmenu"));

		var divLvl2 = new Element('b');
		divLvl2.set('html',lg("createYourModules")+ " :<br /><br />");
		divLvl2.inject($("factoryaddmenu"));
		
		if (__allowredactor) 
		{
			var aLvl2 = new Element('a', { 'href':posh["createrss"], 'target':'_createmodule' } );
			var imgLvl2 = new Element('img', { 'src':'../images/ico_moduleandrss.gif', 'align':'absmiddle' } );
			var bLvl2 = new Element('span');
			bLvl2.set('html',lg("createRssFeed")+"<br /><br />");
			imgLvl2.injectInside(aLvl2);
			bLvl2.injectInside(aLvl2);
			aLvl2.inject($("factoryaddmenu"));			
		}		
			
		var aLvl3 = new Element('a', { 'href':posh["expert"], 'target':'_createmodule' } );
		var imgLvl3 = new Element('img', { 'src':'../images/ico_expertmodule.gif', 'align':'absmiddle' } );
		var bLvl3 = new Element('span');
		bLvl3.set('html',lg("createExpertModule")+"<br /><br />");
		imgLvl3.injectInside(aLvl3);
		bLvl3.injectInside(aLvl3);
		aLvl3.inject($("factoryaddmenu"));	
        */

		$p.app.widgets.factory.shown=true;	
	},
	/*
		$p.app.widgets.factory.hide : close modules factory
	*/
	hide:function()
	{
		$p.plugin.hide();
		$p.app.widgets.factory.shown=false;
	}
}
/*
    Class: $p.app.connection
*/
$p.app.connection={
	active:true,
    oldvalues:{},
	/*
		$p.app.connection.changePass : change user password
	*/
	changePass:function()
	{
		// Change user password
		document.forms["newpass"].pass1.value=$p.string.trim(document.forms["newpass"].pass1.value);
		document.forms["newpass"].pass2.value=$p.string.trim(document.forms["newpass"].pass2.value);
		l_pass1=document.forms["newpass"].pass1.value;
		l_pass2=document.forms["newpass"].pass2.value;
		
		if ((l_pass1.length<6) || (l_pass2.length<6)) {
			$p.app.alert.show(lg("msgSubPassToShort"));
        } 
		else if (l_pass1!=l_pass2) {
			$p.app.alert.show(lg("msgSubPassDiff"));
        }
		else {
			if (l_pass1==l_pass2) {
				$p.ajax.call(posh["scr_changepwd"],
					{
						'type':'execute',
						'variables':"oldpass="+$p.string.esc(document.forms["newpass"].oldpass.value)+"&pass1="+l_pass1,
						'alarm':true
					}
				);
			}
		}	
	},
	/*
		Function : set
                            $p.app.connection.set : connect user
		Parameters:

			v_form - form used to get user connection information
			v_function - function called when connected
			v_type - connection type
	*/
	set:function(v_form,v_function,v_type,v_md5key)
	{
		$p.plugin.hook.launch('app.connection.set.start');
        var username = v_form.username.value;
        username = $p.string.replacePlus(username);
		// connect to profile
		var l_connStr = "u="+username+"&pass="+$p.string.esc(v_form.password.value);
		if (v_form.autoconn && v_form.autoconn.checked)
            l_connStr+="&auto=1";

		if (v_type) l_connStr+="&rtype=1";
		if (username=="")
            $p.app.alert.show(lg("lblEmailChk")+".\r\n");
		else
        {
			$p.ajax.call(posh["scr_connect"],
				{
					'type':'execute',
					'variables':l_connStr,
					'alarm':true,
					'forceExecution':true,
					'callback':
					{
						'function':v_function,
                        'variables':
                        {
                            'md5key':(typeof(v_md5key)=='undefined')?'':v_md5key
                        }
					}
				}
			);
		}

		$p.plugin.hook.launch('app.connection.set.end');

		return false;
	},
	/*
		$p.app.connection.subscribe : application subscription process
		Parameters:

			v_form - subscription form object
			v_function - function called with user is registrered and connected
	*/
	subscribe:function(v_form,v_function)
	{
		$p.plugin.hook.launch('app.connection.subscribe.start');

		// subscribe to application
		var l_e="";
		var l_a="";
		var n = v_form.length;
		var i=0;
		var minicount=0;
		var tabRadio=[];
		var oldname="";
		var temp;
		
		v_form.pass.value = $p.string.trim(v_form.pass.value);
		v_form.confpass.value = $p.string.trim(v_form.confpass.value);
		
		if (v_form.username.value=="") {l_e+=lg("msgSubEmailMiss")+"<br />";}
		if (v_form.pass.value=="") {l_e+=lg("msgSubPassMiss")+"<br />";}
		else {
			if (__accountType=="mail" && !checkEmail(v_form.username.value)) {l_e+=lg("msgSubEmailValid")+"<br />";}
		}
		if ((v_form.pass.value.length<6) || (v_form.confpass.value.length<6)) {l_e+=lg("msgSubPassToShort")+"<br />";}
		if (__accountType=="mail" && v_form.username.value!=v_form.username2.value){l_e+=lg("msgSubEmaildiff")+"<br />";}
		if (v_form.pass.value!=v_form.confpass.value){l_e+=lg("msgSubPassDiff")+"<br />";}
		if(__useConditions&&!v_form.conditions.checked){l_e+=lg("msgSubCond")+"<br />";}
		var l_connStr="u="+v_form.username.value+"&p="+v_form.pass.value+((__useNewsletter&&v_form.newsletter.checked)?"&n=1":"")+"&l="+v_form.longname.value+"&nbSpecificFields="+v_form.nbSpecificFields.value;
		
		for (i=6;i<n;i++)
		{
			switch (v_form.elements[i].type)
			{
				case 'text':
					//is the field mandatory and is it filled with something
					var mandatory = v_form.elements[i].getAttribute("mandatory");
					if (mandatory==1 && v_form.elements[i].value=="")   l_a=lg("errorEmptyFieldForm");
					else    l_connStr+="&"+v_form.elements[i].name+"="+v_form.elements[i].value+"&criteria";							
				break;
				case 'select-one':
					l_connStr+="&"+v_form.elements[i].name+"="+v_form.elements[i].value;	
				break;
				case 'textarea':
					//is the field mandatory and is it filled with something
					var mandatory = v_form.elements[i].getAttribute("mandatory");
					if (mandatory==1 && v_form.elements[i].value=="") {
						l_a=lg("errorEmptyFieldForm");
					}
					else {
                        l_connStr+="&"+v_form.elements[i].name+"="+v_form.elements[i].value;	
                    }
				break;	
				case 'radio':
					var maxIndex=tabRadio.length;
					var escap=0;						
					var currentName = v_form.elements[i].name;
					var mandatory = v_form.elements[i].getAttribute("mandatory");
                    if (maxIndex>0) {
                        for (var j=0;j<maxIndex;j++)
                        {
                            if (tabRadio[j]==currentName)  { escap=1; }
                            else  {  tabRadio[maxIndex]=currentName;	}
                        }
                    }
                    else   {
                        tabRadio[0]=currentName;
                    }
                    
                    if (escap==0)   {
                        var cpt=i;
                        var correct=0;
                        //if the first element isn't checked						
                        if (v_form.elements[cpt].checked!=true) {
                                //we scan the others
                                while (v_form.elements[cpt+1].name==currentName)
                                {
                                    if (v_form.elements[cpt+1].checked==true)   {
                                            correct=1;
                                            l_connStr+="&"+v_form.elements[cpt+1].name+"="+v_form.elements[cpt+1].value;
                                    }	
                                    cpt++;
                                }
                        }												
                        else    {
                            correct=1;
                            l_connStr+="&"+v_form.elements[cpt].name+"="+v_form.elements[cpt].value;	
                        }
                        if (mandatory==1)   {	
                            if (correct!=1)   {
                                l_a=lg("errorEmptyFieldForm");	
                            }
                        }
                    }
                break;
				case 'checkbox':								
					var mycurrentName = v_form.elements[i].name;
					var passage=0;
					var c_result="";
					
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
					else  {  passage=0; }
								
					//If the field is mandatory, verify that at least one checkbox is checked
					var mandatory = v_form.elements[i].getAttribute("mandatory");
					var cpt=i;
					if (passage==1) {
						if (v_form.elements[i].checked!=true) {  minicount++; }
						else
							c_result+=v_form.elements[i].value+';';
							while (v_form.elements[cpt+1].name==mycurrentName)
							{
								temp = v_form.elements[cpt+1].value;
								if (v_form.elements[cpt+1].checked!=true) {
									minicount++;
                                }
								else {
									c_result+=temp+';';
                                }
								cpt++;
							}	
							if (mandatory==1)   {	
								if (minicount==temp)  { l_a=lg("errorEmptyFieldForm"); }
							}
							
						if (c_result!=""){	
							var taille=c_result.length;
							var variable=c_result.substr(0,taille-1); 
							c_result=variable;
						}
						l_connStr+="&"+v_form.elements[i].name+"="+c_result;
					}
                break;
            }
        }

		if (l_a!="")  {
            l_e+=l_a;
        }
		if (l_e=="") {
			var nbCriterias=v_form.nbSpecificFields.value;
			for (var j=1;j<=nbCriterias;j++)
			{
				var idName="c_id"+j;
				var idValue=v_form.elements[idName].value;
				l_connStr+="&"+idName+"="+idValue;
			}
            
			$p.ajax.call(posh["scr_subscribe"],
				{
					'type':'execute',
					'variables':l_connStr,
					'alarm':true,
					'forceExecution':true,
					'method':'post',
					'callback':
					{
						'function':v_function
					}
				}
			);
		}
		else {
			$p.app.alert.show(l_e);
		}

		$p.plugin.hook.launch('app.connection.subscribe.end');

		return false;
	},	
	/*
		$p.app.connection.test : test connection to the server
	*/
	test:function()
	{
    /* FRI: deactivated on 3.11.2013
		if ($p.app.connection.active && $p.app.user.id!=0) {
			$p.ajax.call(__LOCALFOLDER+'portal/'+posh["xmlcheckuserconnection"],
				{
					'type':'load',
					'callback':
					{
						'function':$p.app.connection.testTreatment,
						'variables':
						{
							'function':$p.app.connection.errorTest
						}
					},
					'escape':$p.app.connection.errorTest,
					'source':'xml',
					'method':'GET'
				}
			);
		}
    */
	},
	/*
		$p.app.connection.testTreatment : treat server response on server testing
	*/
	testTreatment:function(response,vars)
	{
		var l_check=$p.ajax.getVal(response,"userid","str",false,0);
		if (l_check==0)  {
            $p.app.connection.errorTest();
        }
	},
	/*
		$p.app.connection.errorTest : treat connection issue
	*/
	errorTest:function()
	{
//		if (!$p.app.connection.active) return;
        
//		$p.app.connection.active=false;
        
//		var l_response=confirm(lg("youHaveBeenDisconnected"));
//		if (l_response==1) $p.url.openLink();
        window.location.reload();
	},
	/*
		Function: saveMenu
        
                                    $p.app.connection.saveMenu 
                                        
                                    display page saving options (when user is not connected)
	
                                    use fadein to display page
    */
	saveMenu:function ()
	{
		$p.plugin.hook.launch('app.connection.saveMenu.start');
        var lblusername = __accountType=='mail'?lg('lblEmail'):lg('lblLogin');
		var l_s=
            '<div id="loginscreen">'
            +'<div style="float: left;width: 150px;">'
            +'<h2 id="lsconnectTitle">'+lg('lblAlreadyMember')+'</h2>'
            +'<p><a class="w" onclick="return $p.app.connection.buildmissingPasswordForm();" href="'+pfolder+posh["password_missing"]+'">'+lg("lblMissingPassword")+' ?</a></p>'
            +'<div id="msg_conn">&nbsp;</div>'
            +'</div>'
            +'<div id="displayPart">'
            +'<form name="form2" method="post" onsubmit="return $p.app.connection.set(this,$p.app.pages.save);">'
			+ '<strong><label for="username">'+lblusername+'</label></strong> :<br />'
			+'<input class="thinbox" name="username" id="username" type="text" maxlength="64" value="" size="30"/><br />'
			+ '<strong><labe  for="password">' +lg("lblPassword")+'</label> :<br />'
			+'<input class="thinbox" name="password" type="password" maxlength="16" size="30" />&nbsp;&nbsp;'	
			+'<br /><label><input type="checkbox" name="autoconn" />'+lg("lblAutoConnection")+'</label><br />'
			+'<br /><input type="submit" class="submit" value="'+lg("lblSavePage")+'" /><br />'
			+'</form>';

		l_s+=__conditionComment;
        l_s+= '</div>';
		l_s+='<h5 class="title">'+lg('notAMemberYet')+' ?</h5><br />'
			+$p.img('puce.gif')+'&nbsp;<a href="#" onclick="$p.app.connection.subscribeMenu();return false;">'+lg('lblAccountCreation')+'</a><br /><br />';
        l_s+= "</div>";
        
		$p.app.popup.fadein(l_s,420,indef,$p.img("ico_menu_disk.gif",14,14,"","imgmid")+" "+lg("lblSavePage"),true);
		$p.plugin.hook.launch('app.connection.saveMenu.end');
	},
	subscribeMenu:function()
	{
		var code = $p.app.captcha.generate_code();
		var captcha_call='',account_call='';
		if(__captcha) {
			captcha_call='return $p.app.captcha.verif_code(this);';
        }
		else {
			captcha_call='return $p.app.connection.subscribe(this,$p.app.connection.subscribeConfirmation);';
        }
		account_call=__accountType=="mail"?lg("lblEmail"):lg("lblLogin");

		var  type_account = "";
		if (__accountType=="mail")	{
			type_account =lg("lblEmailConfirmation")+'<br />'
					+'<input class="thinbox" name="username2" type="text" maxlength="64" style="width: 200px" />'
					+'&nbsp;<font color="red">*</font><br />';
		}
		var l_s='<h2>'+lg('lblAccountCreation')+'</h2><br />'
			+'<form name="form1" method="post" onsubmit="'+captcha_call+'">'
			+account_call+'<br />'
			+'<input class="thinbox" name="username" type="text" maxlength="64" style="width: 200px" />&nbsp;<font color="red">*</font><br />'
			+ type_account
			+lg("lblPassword")+'<br />'
			+'<input class="thinbox" type="password" name="pass" maxlength="16" style="width: 180px" />&nbsp;<font color="red">*</font><br />'
			+lg("lblPasswordConfirmation")+'<br />'
			+'<input class="thinbox" type="password" name="confpass" maxlength="16" style="width: 180px" />&nbsp;<font color="red">*</font><br />'
			+lg("lblName")+'<br />'
			+'<input class="thinbox" name="longname" type="text" maxlength="99" style="width: 200px" /><br />'
			+'<div id="specificMenu">'
			+'</div><br />';
		if (__captcha){
			l_s+=lg("captchaUse")+'<br />'
			+'		<img name="imgCaptcha" src="'+__LOCALFOLDER+'tools/securimage/'+posh["securimage_show"]+'?rand='+ code +'" id="imgCaptcha" align="absmiddle" style="cursor:pointer" onclick="$p.app.captcha.updatePage(document.forms[\'form1\'])" />'
			+'		<input id="code" type="hidden" name="code" value="'+code+'" />'
			+'      <input id="txtCaptcha" type="text" name="txtCaptcha" value="" maxlength="10" size="32" />';
		}
		//+'<label><input type="checkbox" name="autoconn" />'+lg("lblAutoConnection")+'</label><br />';
		if (__useNewsletter) l_s+='<label><input type="checkbox" name="newsletter" />'+lg('lblNewsletterRequired')+'</label> '+__APPNAME+'.<br />';
		if (__useConditions) l_s+='<input type="checkbox" name="conditions" />'+lg('lblConditionsStr','&nbsp;<a href="#" onclick=\'return $p.url.openLink("conditions.html",true,true);\'>'+lg('lblConditions')+'</a>')+'.<br />';
		l_s+='<br /><br /><center><input type="submit" class="submit" name="'+lg("lblOk")+'" value="'+lg("lblSave")+'" /></center>'
		l_s+='</form>';

		$p.app.popup.show(l_s,420,indef,$p.img("ico_menu_disk.gif",14,14,"","imgmid")+" "+lg("lblAccountCreation"),true);

		$p.app.connection.loadMenuCriterias();
	},
	/*
		$p.app.connection.subscribeConfirmation : message to confirm user subscription
	*/
	subscribeConfirmation:function()
	{
        $p.app.pages.save('user');
		$p.app.popup.show('<b>'+lg('accountToValidate')+'</b><br /><br /><a href="#" onclick="$p.app.popup.hide()">'+lg('lblClose')+'</a>',500,indef,$p.img("ico_menu_disk.gif",14,14,"","imgmid")+" "+lg("lblAccountCreation"),true);
	},
	/*
		$p.app.connection.loadMenuCriterias : call menu criterias
	*/
	loadMenuCriterias:function()
	{
        //'source':'html'
		$p.ajax.call(posh["xmldisplayhtmlcriteria"],
			{
				'type':'load',
				'callback':
				{
					'function':$p.app.connection.displayAllCriterias
				}
			}
		);		
	},
	/*
		$p.app.connection.displayMenuCriterias : display all criterias
	*/
	displayAllCriterias:function(response,vars)
	{ 
	 	var i=0;
        var l_s="";
        var info="";
        var total=1;
        var nb=$p.ajax.getVal(response,"nbcriterias","int",false,0);
        l_s+='<input type="hidden" name="nbSpecificFields" value="'+nb+'" />';
        
		while (response.getElementsByTagName("criteria")[i])
		{
			var result=response.getElementsByTagName("criteria")[i];
			var infoID=$p.ajax.getVal(result,"id","int",false,0);
			var type=$p.ajax.getVal(result,"type","str",false,"");
			var label=$p.ajax.getVal(result,"label","str",false,"");
			var options=$p.ajax.getVal(result,"options","str",false,"");
			var mandatory=$p.ajax.getVal(result,"mandatory","int",false,0);
            if (mandatory==1) {
                info='&nbsp;<font color="red">*</font>';
            }
            
            //uniq id of the criteria
            l_s+='<input type="hidden" name="c_id'+total+'" value="'+infoID+'" />'; 
      
            switch (type)
            {
                //TEXT
                case '1':
                    l_s+=label+'<br /><input type="text" class="thinbox" name="userinfo'+total+'" size="25" mandatory="'+mandatory+'" />'+info+'<br />';
                break;
                //LIST
                case '2':
                    var tabOptions=options.split(';');
                    l_s+=label+' '+info+'<br /><select name="userinfo'+total+'" mandatory="'+mandatory+'" >';  
                    for(var j=0;j<tabOptions.length;j++)
                    {
                        l_s+='<option value="'+(j+1)+'">'+tabOptions[j]+'</option>';
                    }
                    l_s+='</select><br />';
                break;
                //CHECKBOX
                case '3':
                    l_s+=label+' '+info+'<br />';
                    var tabOptions=options.split(';');
                    for(var j=0;j<tabOptions.length;j++)
                    {
                        l_s+='<INPUT type="checkbox" name="userinfo'+total+'[]" value="'+tabOptions[j]+'" mandatory="'+mandatory+'" >'+tabOptions[j]+'<br />';
                    }
                break;
                //RADIO
                case '4':
                    var tabOptions=options.split(';');
                    l_s+=label+' '+info+'<br />';
                    for(var j=0;j<tabOptions.length;j++)
                    {
                        l_s+='<INPUT type="radio" name="userinfo'+total+'" value="'+tabOptions[j]+'" mandatory="'+mandatory+'" >'+tabOptions[j]+'<br />';
                    }  
                break;
                //TEXTAREA
                case '5':
                    l_s+=label+'<br />';
                    l_s+='<textarea cols="45" rows="5" name="userinfo'+total+'" mandatory="'+mandatory+'"></textarea>'+info;
                break;
            }
            total++;
			i++;
		}
       
        $p.print("specificMenu",l_s);
	},
    link2MissingPassword: function () {
        var url = '<p><a class="w" id="ls_missingpass" onclick="return $p.app.connection.buildmissingPasswordForm();" href="'+posh["password_missing"]+'" target="_blank">'
                +lg('lblMissingPassword')
                +'</a></p>';
        return url;
    },
	/*
		Function: menu
        
                                        $p.app.connection.menu 
                                        
                                        display connection box
            
                                           and fadein rest of the page
            */
	menu:function()
	{
        var lblusername = __accountType=='mail'?lg('lblEmail'):lg('lblLogin');
        var l_s = $p.app.connection.displayLoginForm();
        $p.app.popup.fadein(l_s,500,indef,lg("lblConnectToYourPortal"));
        	
        
	},
    /*
                Function: displayLoginForm
                
                                $p.app.connection.displayLoginForm
                                
                                display html login form, whatever it can be done with it afterwards, displaying in html page or in popup or 
            */
    displayLoginForm:function () 
    {
		//alert('FRI: displayLoginForm');
        var lblusername = __accountType=="mail"?lg("email"):lg("login");
		return '\
		<div id="loginscreen">\
			<div style="float: left;width: 150px;">\
                <h2 id="lsconnectTitle">'+lg('lblConnect')+'&gt;</h2>\
                <div id="msg_conn">&nbsp;</div>\
                <p><a class="w" id="ls_missingpass" onclick="return $p.app.connection.buildmissingPasswordForm();" href="'+posh["password_missing"]+'" target="_blank">'+lg('lblMissingPassword')+'</a></p>\
			</div>\
			<div id="displayPart">\
                <form name="conBox" method="post" onsubmit="return $p.app.connection.set(this,$p.app.pages.isPageExisting)">\
                    <strong><label id="ls_lbl_username" for="username">'+lblusername+'</label><br />\
                    <input type="text" id="connectionname" class="thinbox" name="username" maxlength="64" style="width: 250px;" /><br /><br />\
                    <strong><label id="ls_lbl_password" for="password">'+lg('lblPassword')+'</label></strong><br />\
                    <input type="password" name="password" class="thinbox" maxlength="32" style="width: 250px;" /><br /><br />\
                    <input type="checkbox" id="ls_lbl_autoconnect" name="autoconn" />'+lg('lblAutoConnection')+'<br /><br />\
                    <input type="submit" class="btn" value="'+lg('lblConnect')+'" />\
                </form>\
			<br/><br/>\
            </div>\
        </div>\
            ';
    },    
    back: function (part) {
        $p.print(part,$p.app.connection.oldvalues[part]);
        delete $p.app.connection.oldvalues[part];
    },
    buildmissingPasswordForm: function () {
        
        var l_s = '\
        <div id="missingScreen">\
			<form name="help" onsubmit="return $p.app.connection.askForANewPassword(this);">\
			<h2 id="help_mis_pass"></h2>\
			<p id="lbl_set_NewPassword_Missing">gqsdgsdg</p>\
			<p><label id="help_email_conn"></label>: <input type="text" name="emaillost" size="30" maxlength="64" value="" /></p>\
            <input type="submit" class="btn"  id="btn_conn" value="OK" />\
			</form>\
			<br />\
			<script type="text/javascript">\
				document.forms["help"].emaillost.focus();\
			</script>\
		</div>\
		<div>\
        <a href="#" id="help_screen_conn" onclick="return $p.app.connection.back(\'displayPart\');">back to the login screen</a>\
		</div>\
    ';
    
        $p.app.connection.oldvalues['displayPart'] = $('displayPart').innerHTML;
        //onclick="return $p.app.connection.askForANewPassword();"
        $p.print('displayPart',l_s);
        
        $('btn_conn').value=lg('lblBtnSend');
        $p.print('msg_conn','');
        $p.print('help_mis_pass',lg('lblMissingPassword'));
        $p.print('help_screen_conn',lg('backScreenConn'));
        $p.print('lbl_set_NewPassword_Missing',lg('lblsetNewPasswordMissing'));
        $p.print('help_email_conn',lg('email'));
        
        return false;
        
    },
    response_askPasswd: function (response,vars) {
        
        if (response && response.getElementsByTagName('email').length > 0) {
       
            part = vars['part'];
            $p.app.connection.back(part);
            $p.print('msg_conn','<p class="warningok">'+lg('msgRenewPassTxt')+'</p>');
            $('displayPart').style.height='200px';
                
        } else {
            $p.print('msg_conn','<p class="warning">'+lg('lblUnknownUser')+'</p>');             
        }   
        
        
        return false;
    },
    askForANewPassword:function (v_form) {
       // $p.print('msg_conn','<p class="warning">Veuillez controler votre messagerie.<br>Vous allez recevoir un message vous permettant de recr&eacute;er un noueau mot de passe.</p>');
        //$p.app.connection.responseaskPasswd('displayPart');
        var username = $p.string.replacePlus(v_form.emaillost.value);
        $p.ajax.call(posh["scr_sendmd5"],
				{
					'type':'load',
					'variables':'username='+username,
					'alarm':false,
					'forceExecution':true,
                    //'source':'xml',
                    'method':'POST',
					'callback':
					{
						'function':$p.app.connection.response_askPasswd,
                        'variables':
                        {
                                        'part':'displayPart'
                        }            
					}
                   
				}
			);
        //$p.app.connection.backandWait('displayPart');
        return false;
        
    },
	module_ff:function()
	{
		document.location.href = "../portal/moduleff.php";
	}    
}
//********************* CAPTCHA************************************************************************************************
$p.app.captcha={
	//$p.app.captcha.updatePage
	//for change the captcha image
	updatePage:function(form)
	{
		$p.ajax.call(posh["scr_captcha_erase"],
			{
				'type':'execute',
				'variables':"code="+form.code.value,
				'forceExecution':true
			}
		);
	  //generate a new code
	  var code= $p.app.captcha.generate_code();
	  //Change the image
	  form.imgCaptcha.src = __LOCALFOLDER+'tools/securimage/'+posh["securimage_show"]+'?rand='+code;
	  //change the idden code
	  form.code.value=code;
	  
	},
	//$p.app.captcha.generate_code
	//generate the code
	generate_code:function()
	{
		var code = "";
		var charset=["A","B","C","D","E","F","G","H","K","L","M","N","P","R","S","T","U","V","W","Y","Z","2","3","4","5","6","7","8","9"];
		
		//length of charset:
		var cslen = 29;
		
		var i;
		for(i=1 ; i <= 5; i++) {
			code +=  charset[Math.floor(Math.random()*(cslen-1))] ;
		}
		return code;
	},
	//$p.app.captcha.verif_code
	verif_code:function(form)
	{
		var code_gen=form.code.value;
		var code_ent=form.txtCaptcha.value;
		$p.ajax.call(posh["xmlcaptcha"]+'?code_gen='+code_gen+'&code_ent='+code_ent,
				{
					'type':'load',
					'callback':
					{
						'function':$p.app.captcha.get_captcha_result,
						'variables':{
									'form':form
									}
					}
					
				}
			);
		return false;
	},
	//$p.app.captcha.get_captcha_result
	get_captcha_result:function(response,vars)
	{
		var test=$p.ajax.getVal(response,"resulta","int",false,-1);
		if(test==1){
			$p.app.connection.subscribe(vars['form'],$p.app.connection.subscribeConfirmation);
		}
		else{
			$p.app.alert.show(lg("BadCaptcha"));
		}
		$p.app.captcha.updatePage(vars['form']);
	}
}
//********************* BANNERS ************************************************************************************************

/*
    Class: Manage the information in the portal banner
            void
*/
$p.app.banner={};

/*
    Class: $p.app.banner.option
*/
$p.app.banner.option={

	shown:false,
	/*
		$p.app.banner.option.show : display tab options banner
	*/
	show:function()
	{
		//no access to options if page locked
		if (tab[$p.app.tabs.sel].locked) return false;
		if ($p.app.banner.option.shown) {
			$p.app.banner.option.hide();
		}
		else {
			//display the options in the banner
			var l_s='<table cellpadding="3" cellspacing="0" border="0" width="100%">'
				+'<tr>'
				+'<td class="advise" align="center">';
			if (__menuDefaultStatus != 4 && $p.app.env!="admin")
			{
				l_s+=(tab[$p.app.tabs.sel].type==1?'<a href="#" onclick=\'$p.app.menu.open("pwidget",true)\'>'+$p.img('ico_menu_add.gifffolino',14,14,'','imgmid')+' '+lg('lblAddContent')+'</a> | ':'')
					+'<a href="#" onclick=\'$p.app.menu.open("poptions",true)\'>'+lg('optionsOfThisPage')+'</a> | ';
			}

			for (var i=0;i<__option.length;i++)
			{
				if ((__option[i]["anonymous"]&&$p.app.user.id<=0)||(__option[i]["connected"]&&$p.app.user.id>0)) l_s+="<a href='#' id='"+__option[i]["id"]+"' onclick='return "+__option[i]["fct"]+"'>"+$p.img(__option[i]["img"],16,16,__option[i]["comment"],"imgmid")+" "+__option[i]["label"]+"</a> | ";
			}

			if (__tabsCanBeRenamed)
			{
				l_s+='<a href="#" onclick="return $p.app.tabs.edit('+$p.app.tabs.sel+');">'+lg('renameThisPage')+'</a> | ';
			}

			if ($p.app.user.id>=0 && tab[$p.app.tabs.sel].removable!=0 ) {
				l_s+='<a href="#" onclick="return $p.app.pages.suppress();">'+$p.img('ico_suppress.gif',14,14,'','imgmid')+' '+lg('lblSuppresspage')+'</a>';
            }
			
			l_s+='</td>'
				+'</tr>'
				+'</table>';

			$p.print("advise",l_s);
			$p.show("advise","block");
			$p.app.banner.option.shown=true;
			$p.app.banner.info.shown=false;
		}
		return false;
	},
	/*
		$p.app.banner.option.hide : hide tab options banner
	*/
	hide:function()
	{
		$p.show("advise","none");
		$p.app.banner.info.load();
		$p.app.banner.option.shown=false;
	}
}

/*
    Class: $p.app.banner.info
*/
$p.app.banner.info={
	shown:true,
	requested:'Y',
	loaded:false,
	item:[],
	itemid:0,
	init: function()
	{
		if ($('adminmessage') == null)
		{
			var l_obj = new Element('div',
				{
					'id':'adminmessage',
					'styles':{
						'position':'absolute'
					}
				}
			);
			document.body.appendChild(l_obj);

			navPrint('adminmessage',$p.html.roundBox('<div id="adminmessage_content"></div>','#FFD363','400px'));
		}
		else
		{
			navShow('adminmessage','block');
		}
	},
	/*
		$p.app.banner.info.load : load information for the information banner
	*/
	load: function()
	{
        if ($p.app.user.id <= 0)
            return;

		if (__bartype == 1 && __bartexthtml != "" && $p.app.banner.info.shown != false)
		{
			var l_s = __bartexthtml;
			//if (__barclosing) 	l_s+=" <a href='#' onclick='return $p.app.banner.info.hide()'>"+$p.img("ico_close.gif",12,11)+"</a>";
			l_s += " <a href='#' onclick='return $p.app.banner.info.hide()'>"+$p.img("ico_close.gif",12,11)+"</a>";

			$p.app.banner.info.init();
			$p.print('adminmessage_content',l_s);
			$p.app.banner.info.show();
		}
		//if ($p.app.banner.info.requested=="Y"&&__rssinfo!=""){
		if (__bartype == 2 && __rssinfo != "")
		{
            $p.app.banner.info.init();
			$p.app.banner.info.show();
			$p.ajax.call(__rssinfo,
				{
					'type':'load',
					'callback':
					{
						'function':$p.app.banner.info.displayRss
					}
 				}
			);
            
			$p.app.banner.info.loaded=true;
		}
	},
	/*
		$p.app.banner.info.displayRSS : display RSS information in banner
	*/
	displayRss: function(response,vars)
	{
		var i = 0, l_s = '';
		while (response.getElementsByTagName("item")[i])
		{
			var result = response.getElementsByTagName("item")[i];
			if ($p.ajax.getVal(result,"link","str",false,"") == "")
            {
				l_s = $p.ajax.getVal(result,"title","str",false,"...");
            }
			else
            {
				l_s = "<a target='_blank' href='"+$p.ajax.getVal(result,"link","str",false,"#")+"'>"+$p.ajax.getVal(result,"title","str",false,"...")+"</a>";
			}
            l_s += " <a href='#' onclick='return $p.app.banner.info.hide()'>"+$p.img("ico_close.gif",12,11)+"</a>";

            $p.app.banner.info.item.push(l_s);
			i++;
		}
		$p.app.banner.info.roll();
	},
	/*
		$p.app.banner.info.roll : roll over RSS articles
	*/
	roll: function()
	{
		if ($p.app.banner.info.item.length > 0 && $p.app.banner.info.shown)
		{
			var l_s = "";
			if ($p.app.banner.info.itemid==$p.app.banner.info.item.length)
                $p.app.banner.info.itemid = 0;

			l_s += $p.app.banner.info.item[$p.app.banner.info.itemid];
			$p.app.banner.info.itemid++;
            $p.print('adminmessage_content',l_s);
			//$p.print('message',l_s);
		}
	},
	/*
		$p.app.banner.info.show : display information banner
	*/
	show:function()
	{
		$p.show('adminmessage',"block");
		$p.app.banner.info.shown=true;
	},
	/*
		$p.app.banner.info.hide : hide information banner
	*/
	hide:function()
	{
		$p.show('adminmessage',"none");
		$p.app.banner.info.shown=false;
	}
}

//********************* POPUP MANAGEMENT **********************************************************************************************************************
/*
    Class: $p.app.popup
*/
$p.app.popup={
	/*
		$p.app.popup.build : build the popup object
	*/
	build:function(v_width,v_height)
	{
		if ($('popup')==null)
		{
			var l_popup=new Element('div', {'id':'popup'} );
			document.body.appendChild(l_popup);
		}
		else
			var l_popup=$('popup');

		if (v_width==indef) v_width=500;
		if (v_height==indef) v_height=200;

		l_popup.style.width = v_width+"px";
		l_popup.style.height = v_height+"px";
		l_popup.style.marginLeft = "-"+(v_width/2)+"px";
		l_popup.style.top = ($p.navigator.getScrollY()+50)+"px";

        var l_s = $p.html.roundBox(   '<div id="popuptitle" style="padding: 0 8px 0 8px;">'
                                    + '</div>'
                                    + '<div id="boxcontent" style="width: 100%">'
                                    + '<div id="popupcontent">'
                                    + '</div>'
                                    + '</div>'
                                    ,'#fff',
                                    v_width+'px'
        );
        l_popup.set('html',l_s);
           
	},/*
		$p.app.popup.buildForRodin : build the popup object
	*/
	buildForRodin:function(v_width,v_height)
	{
		if ($('popup')==null)
		{
			var l_popup=new Element('div', {'id':'popup'} );
			document.body.appendChild(l_popup);
		}
		else
			var l_popup=$('popup');

		if (v_width==indef) v_width=500;
		if (v_height==indef) v_height=200;

		l_popup.style.width = v_width+"px";
		l_popup.style.height = v_height+"px";
		l_popup.style.marginLeft = "-"+(v_width/2)+"px";
		l_popup.style.top = ($p.navigator.getScrollY()+50)+"px";

        var l_s = $p.html.rodinPopUpBox('<div id="popuptitle"></div><div id="popupcontent"></div>');
        
        l_popup.set('html',l_s);
	},
	/*
		$p.app.popup.setTitle : set the popup title
		Parameters:

			v_title - popup title
			v_closeBtn - define if close button is shown or not
			v_closeFct - function called on width close
	*/
	setTitle: function(v_title,v_closeBtn,v_closeFct)
	{
		if (v_closeBtn == indef) v_closeBtn = true;
        if (v_closeFct == indef) v_closeFct = '';
        
		if (v_title != indef)
		{
            var l_s = '<div class="popuphdr">'
                + '<div style="float: right; padding-left: 10px;">'
                + '<a href="#" onclick="'+v_closeFct+';$p.app.popup.hide();return false;">'
                + img('ico_close.gif',12,11,'','imgmid')
                + ' '+lg('lblClose')+''
                + '</a> &nbsp'
                + '</div>'
                + ' &nbsp;'+v_title
                + '</div>';
            $p.print('popuptitle',l_s);
		}
	},
	/*
		$p.app.popup.setContent: set popup content
	*/
	setContent:function(v_content)
	{
		($('popupcontent')).set('html',v_content);
	},
	/*
		$p.app.popup.show : Display popup in the middle of the page
		Parameters:

			v_content (string) - HTML content of the popup
			v_width (integer) - popup width
			v_height (integer) - popup height
			v_title (string) - title displayed in the popup header
			v_closeBtn (boolean) - define if close button is displayed or not
			v_closeFct (string) - function called when popup is closed
	*/
	show:function(v_content,v_width,v_height,v_title,v_closeBtn,v_closeFct)
	{
		//$p.app.popup.hide();
		$p.app.cache.shadow();
		$p.app.popup.build(v_width,v_height);
		$p.app.popup.setTitle(v_title,v_closeBtn,v_closeFct);
		$p.app.popup.setContent(v_content);
        //resize the page if the popup is larger than the page (prevent from blank space)
        $p.app.cache.resize();
	},
	/*
		$p.app.popup.showFRI : same as $p.app.popup.show but specialized for Rodin
	*/
	showFRI:function(v_content,v_width,v_height,v_title,v_closeBtn,v_closeFct)
	{
		/*FRI: Showing just but preserving transparency */
		$p.app.popup.buildForRodin(v_width,v_height);
		$p.app.popup.setTitle(v_title,v_closeBtn,v_closeFct);
		//$p.app.popup.setTitle(v_title,v_closeBtn,v_closeFct);
		$p.app.popup.setContent(v_content);
        $p.app.cache.resize();
	},
	/*
		$p.app.popup.fadein : same that show but with fadein effect
	*/
	fadein:function(v_content,v_width,v_height,v_title,v_closeBtn,v_openFct,v_closeFct)
	{
		$p.app.cache.init();
		var l_openFct=["$p.app.popup.show('"+$p.string.removeCot(v_content,'simple')+"',"+v_width+","+v_height+",'"+v_title+"',"+v_closeBtn+","+v_closeFct+")"];
		if (v_openFct!=indef) l_openFct.push(v_openFct);
		$p.effect.fadein($('cache'),l_openFct,0.7);
	},
	/*
		$p.app.popup.fadeinFRI : same as $.p.app.popup.fadein but for rodin
	*/
	fadeinFRI:function(v_content,v_width,v_height,v_title,v_closeBtn,v_openFct,v_closeFct)
	{
		$p.app.cache.init();
		//alert('v-title:'+v_title + "\n" + 'v-content:'+v_content);
		var l_openFct=["$p.app.popup.showFRI('"+$p.string.removeCot(v_content,'simple')+"',"+v_width+","+v_height+",'"+v_title+"',"+v_closeBtn+","+v_closeFct+")"];
		if (v_openFct!=indef) l_openFct.push(v_openFct);
		$p.effect.fadeinFRI($('cache'),l_openFct, 0.4); /*Very high transparency to see Widgets content*/
	},
	/*
		$p.app.popup.openUrl :
	*/
	openUrl:function(v_url,v_width,v_height,v_title,v_closeBtn,v_closeFct)
	{
		$p.app.popup.fadein('<iframe src="'+v_url+'" width="'+(v_width-20)+'" height="'+v_height+'" frameborder="no" marginwidth="0" marginheight="0" scrolling="auto"></iframe>',v_width,v_height,v_title,v_closeBtn,v_closeFct);
	},
	/*
		$p.app.popup.hide : close popup created with popup function 
	*/
	hide:function()
	{
		if ($('popup') != null)
		{
			document.body.removeChild($('popup'));
			$p.app.cache.hideShadow();
		}
	},
    undisplay:function ()
	{
        $('popup').style.display="none";
        $p.app.cache.hideShadow();
    },
    redisplay: function ()
	{
        $p.app.cache.shadow();
        $('popup').style.display="block";    
    },
	
	hideCacheFRI:function()
	{ /*Used for rehiding black protection while searching*/
		$p.app.popup.hide();
		return false;
	}
}
/*	
	Function: tooltip 
                generate tool tip link
	
        Parameters:

			 v_msg - message displayed in tooltip on mouse over
	
	Returns:
			 tooltip HTML code
*/

function tooltip(v_msg,nolg)
{
    if (typeof(nolg)!="undefined") 
    	return "<a href='#' onclick='return false' onmouseover=\"parent.mouseBox('"+v_msg+"',event)\" onmouseout=\"parent.mouseBox('')\">"+$p.img("ico_help_s.gif",12,12,"","imgmid")+"</a>";
    else
        return "<a href='#' onclick='return false' onmouseover=\"mouseBox(lg('"+v_msg+"'),event)\" onmouseout=\"mouseBox('')\">"+$p.img("ico_help_s.gif",12,12,"","imgmid")+"</a>";
}
/*
	Function: mousebox 
                Display text in tooltip box, next to mouse pointer
	
        Parameters:

		v_s (string) - text to display
		e(event) - event object
*/
function mouseBox(v_s,e)
{
	if (__displayrssdesc)
	{
		if (v_s=="")
		{
			$p.show("mousebox","none");
		}
		else
		{
			if ($("mousebox")==null)
			{	
				var l_div = new Element('div', { 'id': "mousebox" } );
				document.body.appendChild(l_div);	
			} else l_div=$("mousebox");
			$p.print("mousebox",v_s);

			if (e==indef && window.event) e=window.event;
			//if(typeof e.layerX=="undefined")e.layerX=e.offsetX;
			//if(typeof e.layerY=="undefined")e.layerY=e.offsetY;
			//var e=window.event;
			var posLeft=($p.navigator.IE?(e.clientX + document.documentElement.scrollLeft):e.pageX+10);
			var posTop=($p.navigator.IE?(e.clientY + document.documentElement.scrollTop):e.pageY+10);

			l_div.setStyle("left",posLeft+"px");
			l_div.setStyle("top",posTop+"px");

			$p.show("mousebox","block");
			var l_boxWidth=l_div.offsetWidth;
			var l_boxHeight=l_div.offsetHeight;
			// if a part of the box is outside the screen
			var l_outScreen=0;
			if (posLeft > (Window.getWidth()+document.documentElement.scrollLeft-l_boxWidth)) l_outScreen++;
			if (posTop > (Window.getHeight() + document.documentElement.scrollTop-l_boxHeight)) l_outScreen+=2;
			if (l_outScreen==1){l_div.setStyle("left",(Window.getWidth()+document.documentElement.scrollLeft-l_boxWidth-20)+"px");}
			if (l_outScreen==2){l_div.setStyle("top",(Window.getHeight()+document.documentElement.scrollTop-l_boxHeight-20)+"px");}
			if (l_outScreen==3)
			{
				l_div.setStyle("left",(Window.getWidth()+document.documentElement.scrollLeft-l_boxWidth-20)+"px");
				l_div.setStyle("top",(posTop-l_boxHeight-15)+"px");
			}
		}
	}
}


/*
	validKeys : Valid the search area input value
	Parameters:

			 form containing searchtxt input

function validKeys(v_form)
{
	var l_value=v_form.searchtxt.value;
	if (l_value.substr(0,3)=='ex:' || l_value=='')
	{
		$p.app.alert.show(lg("msgKWInput"));return false;
	}
	else
	{
		var i=0,l_pos=0,l_newpos;
		while (l_value.indexOf(',',l_pos)!=-1)
		{
			l_newpos=l_value.indexOf(',',l_pos);
			if (l_value.substring(l_pos,l_newpos)!='' && l_value.substring(l_pos,l_newpos)!=' '){i++;}
			l_pos=l_newpos+1;
		}
		if (l_value.substr(l_pos)!='' && l_value.substr(l_pos)!=' '){i++;}
		if (i<5){
			$p.app.alert.show(lg("msgKWError"));return false;
		}
		else
		{
			v_form.searchtxt.value=$p.string.formatForSearch(l_value);
			return true;
		}
	}
}
	Display help messages

function help(v_id)
{
	var msg;
	switch(v_id)
	{
		case 1:msg=lg("msgHelp1");break;
	}
	$p.app.alert.show(msg);
	return false;
}
// open help box - Not used anymore
function openHelp()
{
	$p.url.openLink("firstusage.html",true,true);
	return false;
}
function errorMsg(v_id)
{
	return lg("msgError"+v_id);
}
*/


/*
	Function: openPage 
                open a personalizable Portaneo page
	
        Parameters:

			 page id
*/
function openPage(v_p)
{
	if (v_p)
	{
		$p.app.pages.setCurrent(v_p);
		$p.url.openLink(posh["mypage"]+"?s="+v_p);
	}
}


//********************* ALERTS MANAGEMENT **********************************************************************************************************************
/*
    Class: $p.app.alert
        Alerts management
*/
$p.app.alert={
	shown:false,
	timer:0,
	/*
	** $p.app.alert.show : Display message
	** Parameters:

			
	** - v_msg - message to display
	** - v_type - message type 1:information 2:help 3:alert/error
	*/
	show:function(v_msg,v_type)
	{
		var l_img;
		switch (v_type)
		{
			case indef:
			case 1:
				l_img="ico_info.gif";
				break;
			case 2:
				l_img="ico_help2.gif";
				break;
			case 3:
				l_img="ico_alert.gif";
				break;
		}
		if ($("errordiv")==null)
		{
			var l_obj=null;
			l_obj = new Element('div', { "id": "errordiv", "class": "errordiv" } );	
			document.body.appendChild(l_obj);
		}
		else
		{
			l_obj=$("errordiv");
		}
		l_obj.setStyle("top",(document.all)?document.documentElement.scrollTop+"px":window.pageYOffset+"px");
		var l_msg="<img src='../images/"+l_img+"' align='absmiddle' /> "+v_msg;
		if ($p.app.alert.shown)
		{
			$p.print("errormsg",l_msg+"<br />","top");
		}
		else
		{
			$('errordiv').set('html',"<table width='100%'><tr><td align='center' id='errormsg' onclick='return $p.app.alert.hide()'>"+l_msg+"</td><td width='13' align='center'><a href='#' onclick='return $p.app.alert.hide()'>"+$p.img("ico_close.gif",12,11)+"</a></td></tr></table>");
		}
		//avoid that the message remain in the middle of the page
		window.onscroll=$p.app.alert.hide;
		$p.effect.fadein(l_obj);
		$p.app.alert.shown=true;
		//hide after x seconds
		if ($p.app.alert.timer==0)
		{
			$p.app.alert.timer=setTimeout("$p.app.alert.hide()",7000);
		}
		else
		{
			clearTimer($p.app.alert.timer);
			$p.app.alert.timer=setTimeout("$p.app.alert.hide()",7000);
		}
		return false;
	},
	hide:function()
	{
		if ($p.app.alert.shown==true)
		{
			$p.app.alert.shown=false;
			$p.effect.fadeout($("errordiv"));
			window.onscroll=null;
			$p.app.alert.timer=0;
		}
		return false;
	}
}

//********************* TIMER MANAGEMENT **********************************************************************************************************************
/*
    Class: $p.app.counter
        Timer management
*/
$p.app.counter={
	timer:0,
	step:0,
	activityStep:0,
	/*
		Function: $p.app.counter.reset
                                reset application counter
                                
		Parameters:

			 starting step (10seconds = 1 step)
	*/
	reset:function(v_step)
	{
		if ($p.app.counter.timer) $p.app.counter.stop();
		$p.app.counter.step=v_step?v_step:0;
		$p.app.counter.timer=setInterval("$p.app.counter.action()",10000);
	},
	/*
		Function: $p.app.counter.action
                                manage actions triggers on defined step
	*/
	action: function()
	{
		if ($p.app.counter.step == 1) $p.app.checkLoading(false);
		// other action are launched when user is connected
		if ($p.app.user.id > 0) {
//			if ($p.app.counter.step==1&&__useSharing) $p.msg.getNb();
			//if security applied on portal, lock after inactivity
			if ($p.app.counter.activityStep == 90
                && tab[$p.app.tabs.sel].lock != 0) { 
                    $p.app.pages.lock();
            }

			//refresh modules that need to be refreshed periodically (RSS, mails, ...)
			if (__rssrefreshdelay != 0
				&& $p.app.counter.step%__rssrefreshdelay == (__rssrefreshdelay-1)) {
				$p.app.widgets.rss.refreshAll();
			}
			//check connection to DB
			$p.app.inactivityTime++;
			if (__useChat && $p.app.user.status != 'x')
			{
				if ($p.app.connection.active)
				{
					//delay activity check if no connection
					$p.chat.checkActivity();
				}
				else
				{
					if ($p.app.counter.step%6==5) $p.chat.checkActivity();
				}
			}
			else
			{
				if ($p.app.counter.step%60==59) $p.app.connection.test();
			}
		}
		if ($p.app.counter.step%2==1 && __bartype==2) $p.app.banner.info.roll();
		$p.app.counter.step++;
		$p.app.counter.activityStep++;
	},
	/*
		Function: $p.app.counter.stop
                                stop counter
	*/
	stop:function()
	{
		clearTimer($p.app.counter.timer);
	}
}

//********************* MULTIMEDIA READER **********************************************************************************************************************
/*
    Class: $p.app.reader
        Multimedia reader
*/
$p.app.reader={
	/*
		Function: $p.app.reader.open
                                open multimedia files reader
		
                     Parameters:

			v_src - url of the multimedia file
			v_type - type of the file (a=audio, v=video)
	*/
	open:function(v_src,v_type)
	{
		$p.plugin.hook.launch('app.reader.open.start');

		// Open media inside Portaneo
		var l_s=''
			+'<table width="100%" cellpadding="0" cellspacing="0">'
			+'<tr>'
			+'<td class="advise" align="center">'
			+'<table cellpadding="3" cellspacing="0">'
			+'<tr>'
			+'<td>';
		//if ($('audio').innerHTML!='')
			//$('audio').empty();
		
		if (v_type=="a") 
		{
			l_s+="		<object classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000' width='400' height='18' id='mp3player' codebase='http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0'><param name='movie' value='"+pfolder+"../tools/mp3player.swf'><param name='flashvars' value='file="+$p.string.esc(v_src)+"&autostart=true'><param name='wmode' value='transparent' /><embed src='"+pfolder+"../tools/mp3player.swf' wmode='transparent' width='400' height='18' flashvars='file="+$p.string.esc(v_src)+"&autostart=true' name='mp3player' type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/go/getflashplayer' /></object>";
		}
		if (v_type=="v") l_s+="		<object classid='clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B' width='320' height='250' codebase='http://www.apple.com/qtactivex/qtplugin.cab'><param name='controller' value='TRUE'><param name='type' value='video/quicktime'><param name='autoplay' value='true'><param name='target' value='myself'><param name='src' value='"+v_src+"'><param name='pluginspage' value='http://www.apple.com/quicktime/download/'><embed controller='TRUE' target='myself'  width='320' height='250' src='"+v_src+"' qtsrc='"+v_src+"' type='video/quicktime' bgcolor='black' border='0' loop='false' autoplay='true' pluginspage='http://www.apple.com/quicktime/download/'></embed></object><br /><br />"+lg("lblVideoWarning");
		l_s+='</td>'
			+'<td valign="top">'
			+'<a href="#" onclick="$p.app.reader.hide();return false;">'
			+$p.img("ico_close.gif",12,11)
			+'</a>'
			+'</td>'
			+'</tr>'
			+'</table>'
			+'</td>'
			+'</tr>'
			+'</table>';
		$p.show("audio","block");
		$p.print("audio",l_s);

		$p.plugin.hook.launch('app.reader.open.end');
	},
	/*
		Function: $p.app.reader.hide
                            close multimedia files reader
	*/
	hide:function()
	{
		$p.app.pages.clean($("audio"));$p.show("audio","none");
	}
}

// initialize the hidding window (for drag action)
/*
    Class: $p.app.cache
        initialize the hidding window (for drag action)
*/
$p.app.cache={
	isInit:false,
	obj:indef,
	/*
		Function: $p.app.cache.init
                            init the page cache window (for menu move)
	*/
	init: function()
	{
		if ($p.app.cache.obj==indef) $p.app.cache.obj=$("cache");

        $p.app.cache.resize();

		$p.app.cache.obj.setStyle("backgroundColor","#000000");
	},
    resize: function()
    {
		var l_size=window.getScrollSize();

		$p.app.cache.obj.setStyle("width",l_size.x+"px");

		$p.app.cache.obj.setStyle("height",l_size.y+"px");
    },
	/*
		Function: $p.app.cache.show
                                display the cache window
                      
                      Parameters:
                      
                                v_status - none or block
	*/
	show:function(v_status)
	{
		$p.app.cache.init();

		//hide popup if opened
		$p.app.cache.hideShadow();
		navShow('popup','none');

		$p.app.cache.obj.setStyle("display",v_status);
	},
	/*
		Function: $p.app.cache.shadow
                                Display the grey cache window (used for popup display)
	*/
	shadow:function()
	{
		//hide all flash objects
		$p.navigator.hideObjects();

		$p.app.cache.init();

		$p.app.cache.obj.setStyle("display","block");
		$p.app.cache.obj.setOpacity("0.7");
		$p.app.cache.obj.setStyle("filter","alpha(opacity=70)");
	},
	/*
		Function: $p.app.cache.hideShadow
                                hide the grey cache window
	*/
	hideShadow:function()
	{
		$p.app.cache.obj.setStyle("display","none");
		//$p.app.cache.obj.setStyle("backgroundColor","#ffffff");
		$p.app.cache.obj.setOpacity("0");
		$p.app.cache.obj.setStyle("filter","alpha(opacity=0)");
		//show all flash objects
		$p.navigator.showObjects();
	}
}
/*
    Class: $p.app.help
*/
$p.app.help={
	/*  
	Function: $p.app.help.enableIntro
                    Enable the introduction message
	*/
	enableIntro: function()
	{
		$p.cookie.write('intro'+$p.app.user.id+'=1');
		$p.app.help.loadIntro();
	},
	/*  
	Function: $p.app.help.loadIntro
                     Load the introduction message
	*/
	loadIntro: function()
	{
		var l_showIntro = $p.cookie.get('intro'+$p.app.user.id);
		if (l_showIntro == '' || l_showIntro == 1)
		{
			$p.ajax.call(__LOCALFOLDER+'l10n/'+__lang+'/intromessage.html',
				{
					'type':'load',
					'source':'html',
					'callback':
					{
						'function':$p.app.help.displayIntro
					}
				}
			);
		}
	},
	/*  
	Function: $p.app.help.displayIntro
                        Display the introduction message
	
    Parameters:
		 response (string) : html response
	*/
	displayIntro:function(response)
	{
		var l_s = $p.html.roundBox('<div style="padding: 8px;color: #fff;">'
            + response
			+ '<div style="text-align:right;">'
			+ '<input type="checkbox" id="donotdisplayintro" />'+lg('doNotDisplayThisMessage')+'&nbsp;'
			+ '<input type="button" value="'+lg('lblClose')+'" onclick="$p.app.help.hideIntro()"><br />'
			+ '</div>'
            + '</div>',
            '#0E679A',
            '100%');

		$p.print('intromessage',l_s);
        $p.show($('intromessage'),"block");
	},
	/*  
	Function: $p.app.help.disableIntro : Disable the introduction message
	Parameters:
		None
	output : 
		None
	*/
	disableIntro:function()
	{
		$p.cookie.write('intro'+$p.app.user.id+'=0');
	},
	/*  
	Function: $p.app.help.hideIntro : Hide the introduction message
	Parameters:
		None
	output : 
		None
	*/
	hideIntro:function()
	{
		if ($('donotdisplayintro').checked) $p.app.help.disableIntro();
		$p.show('intromessage','none');
        ($('indicator')!=null)?$p.navigator.hideIndicator():'';
	}
}
/*
    Class: p_network
*/
$p.network={
	shown:false,
	/*
		$p.network.init : init profile plugin
	*/
	init:function()
	{
		$p.app.newEnv('network');
		$p.app.tabs.openTempTab(3,"$p.plugin.openInTab(%tabid%,function(){},'mynetwork')",lg('myNetwork'),'../images/mynetwork.gif');

		$p.network.buildPage();
	},
	/*
		Function : buildPage
		$p.network.buildPage : build network main page
	*/
	buildPage:function()
	{
		var l_s='<div class="feature">'
			+$p.html.buildFeatureHeader({
				'image':'../images/bigicon_network.gif',
				'title':(__useNetwork ? '<div style="float: right"><form onsubmit="return $p.network.add.search(this)">'+$p.img("ico_friend_add.gif",16,16,"","imgmid")+' <span style="font-size: 11px">'+lg("addFriend")+' &nbsp;</span><input type="text" name="networksearchtxt" class="thinbox" style="width: 176px;color: #aaaaaa;font-size: 9pt;" onFocus=\'$p.app.tools.inputFocus(this,"'+lg("inputEmailOrNameOrTag")+'")\' onBlur=\'$p.app.tools.inputLostFocus(this,"'+lg("inputEmailOrNameOrTag")+'");\' value="'+lg("inputEmailOrNameOrTag")+'" />&nbsp;<input type="submit" class="submit" value="'+lg("ok")+'" style="width:22px" /></form></div>'+lg('myNetwork') : lg('myAccount')),
                'menu':'<div id="network_menu"></div>'
			})
			+'<div id="network_content" class="content"></div>'
			+'</div>';

		$p.print('modules'+tab[$p.app.tabs.sel].id,l_s);
	},
	buildPageMenu:function(v_id)
	{
		var l_h=[];
		l_h.push({'id':1,'fct':'$p.network.myprofile();','icon':'ico_myaccount.gif','label':lg('myAccount')});
		if (__useNetwork)
		{
			l_h.push({'id':2,'fct':'$p.network.dashboard.myNetwork()','icon':'','label':lg('myNetwork')});
			l_h.push({'id':4,'fct':'$p.group.buildPage()','icon':'','label':lg('myGroups')});
			l_h.push({'id':5,'fct':'$p.chat.buildPage()','icon':'ico_chat.gif','label':lg('chat')});
		}

		$p.print('network_menu',$p.html.buildFeatureMenu(v_id,l_h));
	},
	/*
		$p.network.attribLangvalue : input = lang to set ( change the current language ('fr,''en'..) )
	*/
	attribLangValue:function(lgValue) 
	{ 
		var langValue=(lgValue==indef?_lang:lgValue);
		$p.ajax.call(posh["scr_changelang"],
			{
				'type':'execute',
				'variables':'lang='+langValue,
				'alarm':true,
				'forceExecution':false,
				'callback':
				{
					'function':$p.network.refreshLang
				}
			}
		);		
	},
	/*
		$p.network.refreshLang : refresh current page
	*/
	refreshLang:function() 
	{
		$p.url.openLink(posh["mypage"],false);
	},
	/*
		$p.network.myprofile : display profile options
	*/	
	myprofile:function() {
		if ($p.app.user.id==-1) {
			// If admin then do nothing
			$p.url.openLink('../admin/index.php');
		} else {
			// Code for the password change
			var passwordChangeForm = '<h1 style="margin-top: 0px;">'+lg('lblChangePassword')+'</h1>'
	            + '<form name="newpass">'
				+ '<p style="margin: 2px; text-align: right; padding-right: 60px;">' + lg('lblActualPassword') + ': <input class="thinbox" type="password" name="oldpass" maxlength="16" /></p>'
				+ '<p style="margin: 2px; text-align: right; padding-right: 60px;">' + lg('lblFuturPassword') + ': <input class="thinbox" type="password" name="pass1" maxlength="16" /></p>'
				+ '<p style="margin: 2px; text-align: right; padding-right: 60px;">' + lg('lblFuturPasswordAgain') + ': <input class="thinbox" type="password" name="pass2" maxlength="16" /></p>'
				+ '<input style="margin-top: 16px;" onClick="$p.app.connection.changePass();" type="button" value="'+lg("lblSaveChanges")+'" />'
				+ '</form>';
			
			// Code for language change
			var options = '';
			for (var i=0;i<__AVLANGS.length;i++) { 									
				var selected = __AVLANGS[i] == __lang ? ' selected="true"' : '';
				options += '<option value="' + __AVLANGS[i] + '"' + selected + '>' + __AVLANGS[i] + '</option>';
			}
			
			var changeLangueForm = '<h1 style="margin-top: 32px;">'+lg('lblChangeLanguage')+'</h1>'
				+ '<form name="changeLang">'
				+ '<p style="margin: 2px;">' + lg('lblNewLanguage') + ': <select id="langList" name="langList" size="1">' + options + '</select></p>'
				+ '<input style="margin-top: 16px;" onClick="$p.network.attribLangValue(this.parentNode.langList.value);" type="button" value="'+lg("lblSaveChanges")+'" />'
				+ '</form>';
			
			// Launch the user-configuration pop-up
			var html_message='';
			var msgtxt = '<div style="padding-left: 6px; padding-right: 28px; text-align: center; font-size: 12px;">'
				+ passwordChangeForm + changeLangueForm
				+ '</div>';
			
			$p.app.popup.fadeinFRI(msgtxt, 500, 100, html_message);
		}
	},
	/*
	$p.network.loadUserCriteria
	Load the user's criteria (to display)
	*/
	loadUserCriteria:function()
	{
		$p.ajax.call(posh["xmldisplaycriteria"],
			{
				'type':'load',
				'callback':
				{
					'function':$p.network.displayUserCriteria
				}
			}
		);
	},
	/*
	$p.network.displayUserCriteria
	display user's informations (criteria)
	*/
	displayUserCriteria: function(response,vars)
	{
		var l_s = "";
		var l_result = response.getElementsByTagName("criteria");
		var l_resultSize = l_result.length;      
		l_s += '<div>'
            + '<table>';

		for (var i = 0;i < l_resultSize;i ++)
		{            
			//gets the values
            var id = $p.ajax.getVal(l_result[i],'id','int',false,0);
			var label = $p.ajax.getVal(l_result[i],'label','str',false,'');
			var type = $p.ajax.getVal(l_result[i],"type","int",false,0);
			var options = $p.ajax.getVal(l_result[i],'options','str',false,'');
			var editable = $p.ajax.getVal(l_result[i],'editable','int',false,1);
			var parameters = $p.ajax.getVal(l_result[i],'parameters','str',false,'indef');
            if (parameters=="indef") { parameters=lg("noCriteria"); }

            $p.app.user.userCriterias[id]={
                                            'options':options,
                                            'parameters':parameters
                                          }; 
            l_s += '<tr>'
                + '<td width="150">'
                + label
                + ' :</td>'
                + '<td>';

			switch (type)
			{
				case 1 :
                case 4 :   
                case 5 :
					l_s += parameters;
					break;
				case 2 :
					var tableau = options.split(";");
					l_s += tableau[parameters-1];
					break;   
				case 3 :
					var tabParameters = parameters.split(",");
					for (var a = 0;a < tabParameters.length;a ++)
					{
                        l_s += tabParameters[a]+" - ";
					}
					break;                       
			}
            l_s += (editable == 1 ? ' <a href="#" onclick="$p.network.modifyCriteria();return false;">'+lg('modify')+'</a>' : '')
                + '</td>'
                + '</tr>';
		}		
        l_s += '</table>'
            + '</div>';

		$p.print("otherCriteria",l_s);
	},
	/*
		$p.network.modifyCriteria : profile criterias modifications
	*/
	modifyCriteria:function()
	{
        $p.ajax.call(posh["xmldisplayhtmlcriteria"],
			{
				'type':'load',
				'callback':
				{
					'function':$p.network.displayModifyForm
				}
			}
		);
	},	
    displayModifyForm:function(response,vars)
    {
        var l_s='';
        var i=0;
        var l_s="";
        var info="";
        var total=1;
        var nb=$p.ajax.getVal(response,"nbcriterias","int",false,0);
        l_s+='<br /><form name="updateCriterias">\
              <table border="0" cellspacing="0" cellpadding="4">\
              <input type="hidden" name="nbSpecificFields" value="'+nb+'" />';
		while (response.getElementsByTagName("criteria")[i])
		{
			var result=response.getElementsByTagName("criteria")[i];
			var infoID=$p.ajax.getVal(result,"id","int",false,0);
			var type=$p.ajax.getVal(result,"type","str",false,"");
			var label=$p.ajax.getVal(result,"label","str",false,"");
			var options=$p.ajax.getVal(result,"options","str",false,"");
			var mandatory=$p.ajax.getVal(result,"mandatory","int",false,0);
            info=(mandatory==1)?'&nbsp;<font color="red">*</font>':'';

            switch (type)
            {
                //TEXT
                case '1':
                    $p.app.user.userCriterias[infoID]['parameters'] = $p.app.user.userCriterias[infoID]['parameters'].replace("'", "\""); 
                    l_s+='<tr><td nowrap="nowrap">'+label+'</td>\
                          <input type="hidden" name="uniq_id'+total+'" value="'+infoID+'" />\
                          <input type="hidden" name="type'+total+'" value="'+type+'" />\
                          <td><input type="text" name="userinfo'+total+'" size="30" mandatory="'+mandatory+'" value="'+$p.app.user.userCriterias[infoID]['parameters']+'" />'+info+'</td></tr>';
                break;
                //LIST
                case '2':
                    var tabOptions=options.split(';');
                    l_s+='<tr><td nowrap="nowrap">'+label+'</td>\
                    <input type="hidden" name="uniq_id'+total+'" value="'+infoID+'" />\
                    <input type="hidden" name="type'+total+'" value="'+type+'" />\
                    <td><select name="userinfo'+total+'" mandatory="'+mandatory+'" >';
                    for(var j=0;j<tabOptions.length;j++)
                    {
                        var selected="";
                        if ((j+1)==$p.app.user.userCriterias[infoID]['parameters']) {
                            selected="selected=selected";
                        }
                        l_s+='<option value="'+(j+1)+'" '+selected+'>'+tabOptions[j]+'</option>';
                    }
                    l_s+='</select></td></tr>';
                break;
                //CHECKBOX
                case '3':
                    var tabOptions=options.split(';');
                    var tabParameters=$p.app.user.userCriterias[infoID]['parameters'].split(',');  
                    l_s+='<tr><td nowrap="nowrap">'+label+'</td>\
                          <input type="hidden" name="uniq_id'+total+'" value="'+infoID+'" ><td>\
                          <input type="hidden" name="type'+total+'" value="'+type+'" />';
                    for(var j=0;j<tabOptions.length;j++)
                    {
                        var selected="";
                        if (tabParameters.contains(tabOptions[j])) { selected="checked=checked"; }    
                        l_s+='<INPUT type="checkbox" name="userinfo'+total+'[]" id="check'+tabOptions[j]+'" value="'+(j+1)+'" mandatory="'+mandatory+'" '+selected+'>'+tabOptions[j];
                        l_s+=(j==0)?info+'<br />':'<br />';
                    }
                    l_s+='</td></tr>';
                break; 
                //RADIO
                case '4':
                    var tabOptions=options.split(';');
                    l_s+='<tr><td nowrap="nowrap">'+label+'</td>\
                    <input type="hidden" name="uniq_id'+total+'" value="'+infoID+'" /><td>\
                    <input type="hidden" name="type'+total+'" value="'+type+'" />';
                    for(var j=0;j<tabOptions.length;j++)
                    {
                        var selected="";
                        if (tabOptions[j]==$p.app.user.userCriterias[infoID]['parameters']) {
                            selected="checked=checked";
                        }
                        l_s+='<INPUT type="radio" name="userinfo'+total+'" id="radio'+tabOptions[j]+'" value="'+tabOptions[j]+'" mandatory="'+mandatory+'" '+selected+'>'+tabOptions[j];
                        l_s+=(j==0)?info+'<br />':'<br />';
                    }  
                    l_s+='</td></tr>';
                break;
                //TEXTAREA
                case '5':
                    l_s+='<tr><td nowrap="nowrap">'+label+'</td>\
                         <input type="hidden" name="uniq_id'+total+'" value="'+infoID+'" />\
                         <input type="hidden" name="type'+total+'" value="'+type+'" />\
                         <td><textarea cols="45" rows="5" name="userinfo'+total+'" mandatory="'+mandatory+'">'+$p.app.user.userCriterias[infoID]['parameters']+'</textarea>'+info+'<td></tr>';
                break;
            }
            total++;
			i++;
		}
      
        l_s+='<tr><td><input type="button" class="btn" value="'+lg("lblModify")+'" onclick="$p.network.updateMyCriterias();return false;"></td>\
              <td><input type="button" class="btn" value="'+lg("lblCancel").toUpperCase()+'" onclick="$p.network.myprofile();return false;"></td></tr>\
              </table>\
              </form>';
              
        $p.print("otherCriteria",l_s);
    },
    updateMyCriterias:function()
    {
        var formElements=document.forms['updateCriterias'].elements.length;
        if ($p.network.controlMandatoryFields(formElements,'updateCriterias')) {
            
            var nbSpecificFields=document.forms['updateCriterias'].nbSpecificFields.value;
            var criteriasList="";
            var parametersList="";
 
            //get criterias
            for (var i=1;i<=nbSpecificFields;i++)
            {
                var uniq_id=document.forms['updateCriterias'].elements['uniq_id'+i].value;
                var type=document.forms['updateCriterias'].elements['type'+i].value;
                var id='&uniq_id'+i+'='+uniq_id;
                if (type=='3') {
                    var selectList=new Array();
                    var options=$p.app.user.userCriterias[uniq_id]['options'];
                    var tabOptions=options.split(';');
                    for(var j=0;j<tabOptions.length;j++)
                    {
                        if (document.forms['updateCriterias'].elements['check'+tabOptions[j]].checked==1) {
                            selectList.push(tabOptions[j]);
                        }
                    }
                    criteriasList+="userinfo"+i+"="+selectList+id+"&";
                }
                else if (type=='4') {
                    var options=$p.app.user.userCriterias[uniq_id]['options'];
                    var tabOptions=options.split(';');
                    for(var j=0;j<tabOptions.length;j++)
                    {
                        if (document.forms['updateCriterias'].elements['radio'+tabOptions[j]].checked==1) {
                           criteriasList+="userinfo"+i+"="+tabOptions[j]+id+"&";
                        }
                    }
                }
                else if (type=='1' || type=='5') {  
                    
                    var textVal=document.forms['updateCriterias'].elements['userinfo'+i].value;
                    textVal = $p.string.formatForSearch(textVal);
                    textVal=textVal.replace(/\&/gi,"%26"); 
                    textVal=textVal.replace(/\+/gi,"%2b");
                    textVal=textVal.replace(/\</gi,"");
                    textVal=textVal.replace(/\>/gi,"");
                    criteriasList+="userinfo"+i+"="+textVal+id+"&";
                }
                else {
                    criteriasList+="userinfo"+i+"="+document.forms['updateCriterias'].elements['userinfo'+i].value+id+"&";
                }     
            }
                        
            parametersList+="nbSpecificFields="+nbSpecificFields
                          +"&"+criteriasList;
  
            //add user in the database
            $p.ajax.call(posh["scr_update_criterias"],
                {
                    'type':'execute',
                    'variables':parametersList,
                    'forceExecution':true,
                    'callback':
                    {
                        'function':$p.network.loadUserCriteria
                    }
                }
            );
        }
    },
	/*
		$p.network.hide : close profile plugin
	*/
	hide: function()
	{
		$p.plugin.hide();
		$p.network.shown = false;
	},
	/*
		$p.network.controlMandatoryFields : controls that the mandatory fields are filled.
			Parameters:

				n: number of form's elements
	*/
	controlMandatoryFields:function(n,formname,indice)
	{	
        var i=0;
        var total=0; //numbers of unfilled fields
        var minicount=0;
        var tabRadio=[];
        var oldname="";
        var temp;
        if (typeof(indice)=="undefined") {
            indice=0;
        }
        
        for (i=indice;i<n;i++)
        {
            switch (document.forms[formname].elements[i].type)
            {
                case 'text':
                {
                    //is the field mandatory and is it filled with something
                    var mandatory = document.forms[formname].elements[i].getAttribute("mandatory");
                    if (mandatory==1 && document.forms[formname].elements[i].value=="")
                        total++;
                        
                    break;
                }
            
                case 'textarea':
                {
                    //is the field mandatory and is it filled with something
                    var mandatory = document.forms[formname].elements[i].getAttribute("mandatory");
                    if (mandatory==1 && document.forms[formname].elements[i].value=="")
                        total++;
                        
                    break;
                }		
                
                case 'radio':
                {
                    var maxIndex=tabRadio.length;
                    var escap=0;						
                    var currentName = document.forms[formname].elements[i].name;
                    var mandatory = document.forms[formname].elements[i].getAttribute("mandatory");
                    if (mandatory==1)
                    {
                    
                        if (maxIndex>0)
                        {
                            for (var j=0;j<maxIndex;j++)
                            {
                                if (tabRadio[j]==currentName)
                                    escap=1;
                                else
                                    tabRadio[maxIndex]=currentName;							
                            }
                        }
                        else
                        tabRadio[0]=currentName;
                        
                        if (escap==0)
                        {
                            var cpt=i;
                            var correct=0;
                            //if the first element isn't checked						
                                if (document.forms[formname].elements[cpt].checked!=true) {
                                        //we scan the others
                                        while (document.forms[formname].elements[cpt+1].name==currentName)
                                        {
                                            if (document.forms[formname].elements[cpt+1].checked==true) {
                                                correct=1;
                                            }  
                                            cpt++;
                                        }
                                }												
                                else
                                {correct=1;}
                            
                            if (correct!=1)
                                total++;		
                        }
                    }
                    break;
                }
                
                case 'checkbox':
                {										
                    var mycurrentName = document.forms[formname].elements[i].name;
                    var passage=0;
                    
                    if (oldname=="")
                    {
                        oldname=mycurrentName;
                        passage=1;
                        minicount=0;
                        temp=0;
                    }
                    else if (oldname!=mycurrentName)
                    {
                        oldname=mycurrentName;
                        passage=1;
                        minicount=0;
                        temp=0;
                    }
                            
                    //If the field is mandatory, verify that at least one checkbox is checked
                    var mandatory = document.forms[formname].elements[i].getAttribute("mandatory");
                    if (mandatory==1)
                    {
                        var cpt=i;

                        if (passage==1)
                        {
                                if (document.forms[formname].elements[i].checked!=true)
                                    minicount++;
                                
                                    while (document.forms[formname].elements[cpt+1].name==mycurrentName)
                                    {
                                        temp = document.forms[formname].elements[cpt+1].value;
                                        if (document.forms[formname].elements[cpt+1].checked!=true)
                                            minicount++;
                                        
                                        cpt++;
                                    }	
                                    if (minicount==temp)
                                        total++;
                        }
                    }
                    break;	
                } //end case
            } //end switch
        } // end for
                        
        if (total!=0)
            {$p.app.alert.show(lg("errorEmptyFieldForm"),3);
            return false;}
        else	
        return true;
	}
}

/*
    Class: $p.network.profile
*/
$p.network.profile={
	/*
		$p.network.profile.load : load my profile
	*/
	load:function()
	{
        if (__useNetwork)   {
			$p.app.wait('myprofile');
			getXml(posh["xmlnetwork_myprofile"],$p.network.profile.display);
		}
	},
	/*
		$p.network.profile.display : display my profile
		Parameters:

			 xml response
	*/
	display:function(response,vars)
	{
        var l_picture = $p.ajax.getVal(response,"picture","str",false,"");
		var l_s = '<div class="bottomhr">'
			+ '<table cellpadding="5" cellspacing="0">'
			+ '<tr>'
			+ '<td width="150" align="center">'
			+ '<img src="'+(l_picture==""?"../images/nopicture.gif":$p.url.setParamInUrl(l_picture,"nocache",rand))+'" width="64" height="64" class="picture" />'
            + '<br /><a href="#" onclick="$p.network.profile.modify()">'+lg('modify')+'</a>'
			+ '</td>'
			+ '<td valign="top">'
			+ '<b>'+$p.ajax.getVal(response,"longname","str",false,"---")+'</b><br /><br />'
            + lg("myStatus")+' '+tooltip("myStatusHelp")+' :'
            + '<form onsubmit=\'return $p.network.profile.updateStatus(this)\'>'
			+ '<input class="thinbox" type="text" name="stat" value=\''+$p.ajax.getVal(response,"stat","str",false,"")+'\' maxlength="200" style="width: 400px;"/> '
			+ '<input type="submit" class="btn" value="'+lg("lblBtnSend")+'" />'
			+ '</form>'
            + '</td>'
			+ '</tr>'
            + '</table>'
            + '</div>'
            + '<div class="bottomhr">'
            + '<table cellpadding="5" cellspacing="0">'
            + '<tr>'
			+ '<td width="150" valign="top">'
            + lg("keywords")+' :'
            + '</td>'
			+ '<td>'
            + $p.tags.separate(response.getElementsByTagName("keyword")[0].firstChild.nodeValue)
            + ' <a href="#" onclick="$p.network.profile.modify()">'+lg('modify')+'</a>'
            + '</td>'
			+ '</tr>'
            + '<tr>'
			+ '<td valign="top">'
            +lg("description")+' :'
            + '</td>'
			+ '<td>'
            + $p.ajax.getVal(response,"desc","str",false,lg("noDescription"))            
            + '<br /><a href="#" onclick="$p.network.profile.modify()">'+lg('modify')+'</a>'
            + '</td>'
			+ '</tr>'
			+ '</table>'
            + '</div>';

		$p.print("myprofile",l_s);
	},
	/*
		$p.network.profile.passwordMenu :display the change password menu
	*/
	passwordMenu:function()
	{
		var l_s = '<div class="title">'+lg('lblModifyPassword')+'</div>'
            + '<div class="content">'
			+ '<form name="newpass">'
			+ lg('lblOldPassword')+'<br /><input class="thinbox" type="password" name="oldpass" maxlength="16" /><br /><br />'
			+ lg('lblNewPassword')+'<br /><input class="thinbox" type="password" name="pass1" maxlength="16" /><br /><br />'
			+ lg('lblRetypeNewPassword')+'<br /><input class="thinbox" type="password" name="pass2" maxlength="16" /><br /><br />'
			+ '<input onClick="$p.app.connection.changePass();" class="btn" type="button" value="'+lg("lblModify")+'" />'
			+ '</form>'
            + '</div>';

		$p.print('mypassword',l_s);
	},
	/*
		Function: $p.network.profile.changeUserMenu 
                        
                                changeUserMenu
                        
                                change user
	*/
	changeUserMenu:function()
	{
        var missingPassword = $p.app.connection.link2MissingPassword();
        var lblusername = __accountType=='mail'?lg('lblEmail'):lg('lblLogin');

		var l_s = '<div class="title">'+lg('lblChangeUser')+'</div>'
            + '<div class="content">'
			+ '<span id="msg_conn"></span>'
            + '<form method="post" name="conBox" onSubmit="return $p.app.connection.set(this,link,true);">'
			+ lblusername
            + '<br /><input class="thinbox" type="text" name="username" maxlength="64" size="30" /><br /><br />'
			+ lg("lblPassword")
            + '<br /><input class="thinbox" type="password" name="password" maxlength="16" size="30" /><br /><br />'
			+ '<input type="checkbox" name="autoconn" />'+lg('lblAutoConnection')
            + '<br /><br />'
			+ '<input type="submit" class="btn" value="'+lg('lblOk')+'" />&nbsp;'
			+ missingPassword+'</a>'
			+ '</form>'
            + '</div>';

		$p.print('changeuser',l_s);
	},
	changeLang:function()
	{
		var l_s = '<div class="title">'+lg('lblModifyLang')+'</div>'
            + '<div class="content">'
			+ '<form name="changeLang">'
			+ '<select id="langList" name="langList" size="1" onChange="$p.network.attribLangValue(this.value);">'
            + '</select>'
            + '</form>'
            + '</div>';
		$p.print('userlang',l_s);

		for (var i=0;i<__AVLANGS.length;i++)
		{ 									
			var sel=__AVLANGS[i]==__lang?true:false;
			$('langList').options[i] = new Option(__AVLANGS[i],__AVLANGS[i],sel,sel );	
		}
	},
	/*
		$p.network.profile.modify : profile modification popup
	*/
	modify:function()
	{
		var l_s='<br />'
			+'<iframe src="'+posh["frm_network_updateprofile"]+'" width="500" height="350" frameborder="no" marginwidth="0" marginheight="0" scrolling="auto"></iframe>'
			+'<br /><br /><input type="button" onclick="$p.app.popup.hide();reset_rand();$p.network.profile.load();" value="'+lg("lblClose")+'" />';

		$p.app.popup.fadein(l_s,510,indef,lg("modifyProfile"),false);
	},
	/*
		$p.network.profile.updateStatus : update my status
		Parameters:

			 form containing status information
	*/
	updateStatus:function(v_form)
	{
		executescr(posh["scr_network_updatemyprofile"],"stat="+$p.string.esc(v_form.stat.value),false,false,$p.network.profile.updateStatusSuccess);
		
		return false;
	},
	/*
		$p.network.profile.updateStatusSuccess : action when my status is updated
	*/
	updateStatusSuccess:function()
	{
		$p.app.alert.show(lg("yourStatusIsUpdated"));
	}
}

//===============================================================================================================
//					OTHER OBJECTS MANAGEMENT
//===============================================================================================================

//Old functions (compatibility
function _gel(v_id) {return $p.get(v_id);}
function navPrint(v_id,v_s,v_add){$p.print(v_id,v_s,v_add);}
function getPos(v_node,v_ref){return $p.getPos(v_node,v_ref);}
function _trim(v_s){return $p.string.trim(v_s);}
function inArray(v_arr,v_searched){return $p.array.find(v_arr,v_searched);}
function MyMooToolsDebug(){}
function _gelstn(v_t){return $$(v_t);}
function navClass(v_id,v_class){return $p.setClass(v_id,v_class);}
function navShow(v_id,v_disp){return $p.show(v_id,v_disp);}
function navIsShown(v_id){return $p.isShown(v_id);}
function navWait(v_id){return $p.app.wait(v_id);}
function _esc(v_s){return $p.string.esc(v_s);}
function _unesc(v_s){return $p.string.unesc(v_s);}
function _uc(v_s){return $p.string.uc(v_s);}
function _lc(v_s){return $p.string.lc(v_s);}
function _min(v1,v2){return $p.min(v1,v2);}
function _max(v1,v2){return $p.max(v1,v2);}
function navId(v_id,v_newId){return $p.changeId(v_id,v_newId);}
function navHeight(v_id,v_h){return $p.setHeight(v_id,v_h);}
function navWidth(v_id,v_w){return $p.setWidth(v_id,v_w);}
function img(v_file,v_w,v_h,v_alt,v_cl,v_id){return $p.img(v_file,v_w,v_h,v_alt,v_cl,v_id);}
function p_img(v_file,v_w,v_h,v_alt,v_cl,v_id){return $p.imgObj(v_file,v_w,v_h,v_alt,v_cl,v_id);}
var p_nav={IE:$p.navigator.IE,noinclusion:function(){return $p.navigator.noinclusion();},changeTitle:function(v_title){return $p.navigator.changeTitle(v_title);}}
var p_popup={show:function(v_content,v_width,v_height,v_title,v_closeBtn){return $p.app.popup.show(v_content,v_width,v_height,v_title,v_closeBtn);}}
function formatSearch(v_s){return $p.string.formatForSearch(v_s);}
function getVar(v_s,v_var){return $p.string.getVar(v_s,v_var);}
function correctCharEncoding(v_s){return $p.string.correctEncoding(v_s);}
function removeTags(v_s){return $p.string.removeTags(v_s);}
function _hesc(v_s){return $p.string.htmlToText(v_s);}
/*
	Function: _args 
                transform URL variables to an array of these variables
	
        Parameters:

			 URL
	
	Returns:
			 array of variables
*/
function _args(v_s)
{
	v_s=(v_s)?v_s:window.location.search;
	if (v_s.substr(0,1)=='&'||v_s.substr(0,1)=='?') v_s=v_s.substr(1);
	var l_arr=v_s.split('&');
	var l_ret=[];
	for (var i=0;i<l_arr.length;i++)
	{
		var pair=l_arr[i].split('=');
		l_ret[pair[0]]=pair[1];
	}
	return l_ret;
}
/*
	Function: setExternalLink 
                all links are opened in an external window
	
        Parameters:

			 HTML string with hyperlinks
	
	Returns:
			 HTML string with all hyperlinks opening in a new window
*/
function setExternalLink(v_s)
{
	return v_s.replace(/<a /g,"<a target='_blank' ");
}
/*
	Function: checkEmail 
                check email validity
	
        Parameters:

			 email
	
	Returns:
			 true / false
*/
function checkEmail(v_email)
{
	var l_ret=true;
	var l_reg = /^[a-z0-9._-]+@[a-z0-9.-]{2,}[.][a-z]{2,3}$/;
	if (l_reg.exec(v_email)==null){l_ret=false;}
	return l_ret;
}
/*
	Function:  sortDate 
                Sort by dates rule
*/
function sortDate(a,b)
{
	if (a.date>b.date) return -1;
	if (a.date<b.date) return 1;
	return 0;
}
/*
	Function:  sortId 
                sort by ID rule
*/
function sortId(a,b)
{
	if (a.id>b.id) return -1;
	if (a.id<b.id) return 1;
	return 0;
}
// Sorting function for bloc showing type
function blocSort(v_a,v_b)
{
	if (v_a.newcol<v_b.newcol) return -1;
	if (v_a.newcol>v_b.newcol) return 1;
	if (v_a.newpos<v_b.newpos) return -1;
	if (v_a.newpos>v_b.newpos) return 1;
	return 0;
}
// Sorting function for newspaper showing type
function newspaperSort(v_a,v_b)
{
	if (v_a.posj<v_b.posj) return -1;
	if (v_a.posj>v_b.posj) return 1;
	return 0;
}
function link(v_url,v_newpage,v_uselang){return $p.url.openLink(v_url,v_newpage,v_uselang);}
/*
	homebar : display application help for a new user
*/
function homebar()
{
	var l_s="<table cellpadding='0' cellspacing='0' width='100%'><tr>";
	l_s+="<td valign='top' width='33%'><b>1/ "+lg("lblAddMenu")+"</b><br /><br /><center>"+$p.img("homebar_1.gif")+"</center></td>";
	l_s+="<td class='lefthr' valign='top' width='33%'><b>2/ "+lg("lblEditMenu")+"</b><br /><br /><center>"+$p.img("homebar_2.gif")+"</center></td>";
	l_s+="<td class='lefthr' valign='top'><b>3/ "+lg("lblSave")+"</b><br /><br /><center>"+$p.img("homebar_3.gif")+"</center></td>";
	l_s+="</tr></table>";
	$p.print("box",box(0,lg("lblBeginningWith")+__APPNAME,"hideBox()",l_s));
	$p.show("box","block");
}
function formatDateShort(v_date,v_useTime){return $p.date.formatDateShort(v_date,v_useTime);}
function logout(){return $p.app.logout();}
/*
	goIndex : open Portaneo homepage
*/
function goIndex()
{
	$p.app.widgets.factory.init();
	return false;
}
/*
	shortName : Get user short name for better display in page
	Parameters:

			 user long name
	
	Returns:
			 user short name
*/
function shortName(v_n)
{
	if (v_n.indexOf("@")>0) {v_n=v_n.substr(0,(v_n.indexOf("@")));}
	return v_n;
}
function getRadioValue(v_element){return $p.app.tools.getRadioValue(v_element);}
function debug(v_msg,v_type){return $p.app.debug(v_msg,v_type);}
var p_alert={show:function(v_msg,v_type){return $p.app.alert(v_msg,v_type);}}


/*

    Class: html
    
            tools for building html 
            
            $p.html

*/
$p.html={
    /* Function: link
    
                    build new link
                    
                    $p.html.link
                
                Parameters:
                
                        text - link text
                        href - link
                       Parameters- hash {params : {}, styles:{}, events:{}}
    
            */
    link: function(text,href,parameters) {
        
        var l_s = "<a href=\""
                +  href
                + '" ';
        l_s +=  this._paramsValues(parameters['params']);       
        l_s +=  this._paramsStyles(parameters['styles']); 
        l_s +=  this._paramsValues(parameters['events']);        
        l_s += '>' + text + "</a>";
        
        return l_s;
    },
    /*
                Function: table
                
                             begin table

                             $p.html.table
                  
                   Parameters:
                   
                        params - parameters (hash)
                        styles -    styles (hash)
                        events - events (hash)
           */
    table:function (parameters) {
        var l_s =  '<table'
        l_s +=  this._paramsValues(parameters['params']);
        l_s +=  this._paramsStyles(parameters['styles']);
        l_s +=  this._paramsValues(parameters['events']);
        l_s += ">";
        
        return l_s;
    },
    /*
                Function: row
                    
                        build row
                        
                        $p.html.row
        */
    row:function (tr) {
        
    },
    /*
            Function: _paramsValues
            
                            build html tags parameters
            
             Parameters:
             
                            params - hash
        */
    _paramsValues:function (params) {
        var l_s = '';
        for (var p in params) {
            l_s += p + '="'+ params[p] +'"';
        }
        return l_s;
    },
    /*
                Function: _paramsStyles
                
                        build styles parameters for a tag style
                        
                  Parameters:

                            styles - hash
            */
    _paramsStyles:function (styles) {
         var l_s = ' style="'; 
        for ( var s in styles) {
            l_s += s + ":" + styles[s] + ";";
        }
        l_s += '" ' ; 
        return l_s;
    },
	/*
                Function: buildTitle
                
                        build title div
                        
                  Parameters:

                            v_title - title (string)
                            v_options - options displayed  on the right of the title bar (string)
            */
    buildTitle: function (v_title,v_options)
	{
        var l_s = (v_options==indef ? '' : '<div style="float:right;background: #fff;line-height: 30px;vertical-align: middle;color: #888;padding-right: 4px;">'+v_options+'</div>')
			+ '<div class="feature_title">'
			+ '<span>'+v_title+'</span>'
			+ '</div>';

		return l_s;
    },
	/*
                Function: buildPageNavigator
                
                        build page navigator
                        
                  Parameters:

			v_leftButtonLabel - Left button label
			v_leftButtonCode - left button code
			v_middleLabel - label displayed between buttons
			v_rightButtonLabel - Right button label
			v_rightButtonCode - right button code
            */
	buildPageNavigator: function(v_leftButtonLabel,v_leftButtonCode,v_middleLabel,v_rightButtonLabel,v_rightButtonCode)
	{
		var l_s='<div style="clear: both;float: none;text-align: center;background: #c6c3c6;height: 22px;margin-top: 15px;padding-top: 3px;">';
		if (v_leftButtonCode!='')
		{
			l_s+='<a href="#" onclick=\''+v_leftButtonCode+'\'>'+$p.img('ico_previous3.gif',8,11,'','imgmid')+' '+lg(v_leftButtonLabel)+'</a> &nbsp; ';
		}
		if (v_middleLabel!='')
		{
			l_s+=v_middleLabel;
		}
		if (v_rightButtonCode!='')
		{
			l_s+=' &nbsp; <a href="#" onclick=\''+v_rightButtonCode+'\'>'+lg(v_rightButtonLabel)+' '+$p.img('ico_next3.gif',8,11,'','imgmid')+'</a>';
		}
		l_s+='</div>';

		return l_s;
	},
	/*
		Function: $p.html.buildFeatureHeader
                                 Build the header of a feature page
                                 
		Parameters:

			 v_content : hash of parameters
	*/
	buildFeatureHeader: function(v_content)
	{
		var l_s = $p.html.roundBox('<table cellpadding="5" cellspacing="0" width="100%" class="header">'
			+ '<tr>'
			+ ((v_content['image']=='' || v_content['image']==indef) ? ''
                                                                     : '<td rowspan="2" width="80" valign="top">'
                                                                        + '<div class="picture_image_big">'
                                                                        + '<img src="'+v_content['image']+'" />'
                                                                        + '</div>'
                                                                        + '<div class="picture_frame_blue_big"> </div>'
                                                                        + '</td>'
            )
			+ '<td valign="top">'
			+ '<h2>'
			+ v_content['title']
			+ '</h2>'
            + (v_content['menu'] == indef ? '' : v_content['menu'])
            + '</td>'
			+ '</tr>'
			+ '</table>'
            ,'#E9EDF2'
            ,'100%');
		return l_s;
	},
	/*
		Function: $p.html.buildFeatureMenu
                                 Build the menu of a feature page
                                 
		Parameters:

			 v_selOption : ID of the selected option
			 v_options : hash of the options
	*/
	buildFeatureMenu: function(v_selOption,v_options)
	{
		var l_s='<div class="menu">'
			+'<ul>';
		for (var i=0;i<v_options.length;i++)
		{
			l_s+='<li class='+(v_selOption==v_options[i].id ? '"selected"' : '"notselected" onclick="'+v_options[i].fct+'"')+'>'
			+((v_options[i].icon==indef || v_options[i].icon=='') ? '' : $p.img(v_options[i].icon,indef,indef,'','imgmid'))
			+' '+v_options[i].label
			+'</li>';
		}
		l_s+='</ul>'
			+'</div>';

		return l_s;
	},
	/*
		Function: $p.html.roundBox
                                 Build a rounded box
                                 
		Parameters:

			 v_content : content of the box
			 v_bgColor : background color of the box
			 v_width : width of the box
	*/
	roundBox: function(v_content,v_bgColor,v_width)
	{
		if (v_bgColor == indef)
			v_bgColor = '#fff';
        if (v_width == indef)
        {
            v_width = '400px';
        }

        return '<div class="raised" style="width: '+v_width+';">'
			+ '<b class="b1" style="background: '+v_bgColor+';"></b>'
			+ '<b class="b2" style="background: '+v_bgColor+';"></b>'
			+ '<b class="b3" style="background: '+v_bgColor+';"></b>'
			+ '<b class="b4" style="background: '+v_bgColor+';"></b>'
			+ '<div class="boxcontent" style="background: '+v_bgColor+';">'
			+ v_content
			+ '</div>'
			+ '<b class="b4b" style="background: '+v_bgColor+';"></b>'
			+ '<b class="b3b" style="background: '+v_bgColor+';"></b>'
			+ '<b class="b2b" style="background: '+v_bgColor+';"></b>'
			+ '<b class="b1b" style="background: '+v_bgColor+';"></b>'
			+ '</div>';
	},
	/*
	Function: $p.html.rodinPopUpBox
                             Build a pop up box in Rodin's style
                             
	Parameters:
		 v_content : content of the box
		 v_bgColor : background color of the box
		 v_width : width of the box
	*/
	rodinPopUpBox: function(v_content,v_bgColor,v_width)
	{
		if (v_bgColor == indef)
			v_bgColor = '#fff';
	    if (v_width == indef)
	    {
	        v_width = '400px';
	    }
	
	    return '<div class="raised">'
			+ '<div class="boxcontent" style="background: '+v_bgColor+'; border-radius: 10px;">'
			+ v_content
			+ '</div>'
			+ '</div>';
	},
    /*
                Function: $p.html.breadCrumbs
                
                  parameters:
                  
                            v_items - 
        */
	breadCrumbs: function(v_items)
	{
		var l_s = '';
		for (var i = 0;i < v_items.length;i++)
		{
			if (  v_items[i].link == '' && v_items[i].fct == '') {
				l_s+= v_items[i].label;
			}
			else 
            {
				l_s+= '<a';
				if (v_items[i].link == '') {
					l_s+= ' href="#"';
				}
				else {
					l_s+= ' href="'+v_items[i].link+'"';
				}
				if (v_items[i].fct != '') {
					l_s+= ' onclick="'+v_items[i].fct+'"';
				}
				l_s+= '>'
					+ v_items[i].label
					+ '</a> ';
			}
			if (i < (v_items.length - 1)) {
				l_s+= '&raquo; ';
			}
		}

		return l_s;
	}
}


//************************************* HTML FORM FUNCTIONS ***************************************************************************************************************
/*
    Class: $p.app.html.form
         HTML form functions
*/
$p.html.form={
	/*
		Function: $p.app.html.form.buildEnum
                                 Build the enum field
                                 
		Parameters:

			 parameters (array) -  type, name, def(value), i('inp'+i = td id to inject)
                                 l_pref - XML object
	*/
    buildEnum:function(parameters,l_pref)
    {
        var inputField="";
        var j=0;
  
        inputField="<select id='"+parameters["id"]+"' name='"+parameters["name"]+"'>";
        while(l_pref.getElementsByTagName("EnumValue")[j])
        {
            var l_opt=l_pref.getElementsByTagName("EnumValue")[j];
            var l_optValue=$p.ajax.getProp(l_opt,"value","str",false,"");
           
            inputField+="<option value='"+l_optValue+"'"+((l_optValue==parameters["value"])?" selected='selected'":"")+">"+lg($p.ajax.getProp(l_opt,"display_value","str",false,l_optValue))+"</option>";
            j++;
        }
        inputField+="</select>";
        return inputField;
        
    },
	/*
		Function: $p.app.html.form.buildInput
                                 Build the input field
                                 
		Parameters:

			 parameters (array) -  type, name, value, id('inp'+i = td id to inject)
	*/   
    buildInput:function(parameters)
    {
        var sizefield = 24;
		var inputField="";
        var readOnlyCondition="";
        var boolCondition= "";
        var type="text";
        
        if (parameters["type"]=='password' ) type="password";       
        if (parameters["type"]=='hidden' ) type="hidden";       
        if (parameters["type"]=='readonly') readOnlyCondition = ($p.navigator.IE?" disabled='disabled'":"");

        //build the input field to inject
        inputField="<input type='"+type+"' id='"+parameters["id"]+"' name='"+parameters["name"]+"' value='"+parameters["value"]+"' size='"+sizefield+"' "+readOnlyCondition+" />";
        //injection

        return inputField;
    },
	/*
		Function: $p.app.html.form.buildCheckbox
                                 Build the checkbox
                                 
		Parameters:

			 parameters (array) -  type, name, value, id('inp'+i = td id to inject)
	*/   
    buildCheckbox:function(parameters)
    {        
		var inputField,readOnlyCondition,boolCondition="";
        var type="checkbox";
        var boolCondition = ((parameters["value"]==1)?" checked='checked'":"");
        //build the input field to inject
        inputField="<input type='"+type+"' id='"+parameters["id"]+"' name='"+parameters["name"]+"' "+boolCondition+" />";
        //injection
        return inputField;
        //$(id).innerHTML+=inputField;
    }       
}



/*
    Class: p_table
*/

var p_table={
	name:"default",
	title:"",
	headers:[],
	rows:[],
	footer:"",
	container:"",
	order:0,
	asc:false,
	filter:[],
	width:"100%",
	saveincookie:false,
	/*
		p_table.headerObj : create a column header
		Parameters:

			name - database field name
			label - header label (writen in the header)
			filtered - define if the column can be filtered true/false
	*/
	headerObj:function(name,label,filtered,width,sorted)
	{
		this.name=name;
		this.label=label;
		this.filtered=filtered;
		this.width=(width==indef)?"":width;
		this.sorted=(sorted==indef)?true:sorted;
	},
	/*
		p_table.row : create a new row 
	*/
	row:function()
	{
		this.columns=[];
	},
	/*
		p_table.cell : create a new cell
		Parameters:

			val - cell value (extracted from DB)
			label - label displayed in the cell
	*/
	cell:function(val,label)
	{
		this.val=val;
		this.label=(label==indef?val:label);
	},
	refresh:indef,
	/*
		p_table.reset : reset the table 
	*/
	reset:function()
	{
		p_table.headers.length=0;
		p_table.rows.length=0;
		p_table.filter.length=0;
		p_table.order=0;
		p_table.asc=false;
	},
	/*
		p_table.init : init the table 
	*/
	init:function()
	{
		if (p_table.saveincookie)
		{
			if ($p.cookie.get(p_table.name+"order")!="") p_table.order=$p.cookie.get(p_table.name+"order");
			if ($p.cookie.get(p_table.name+"asc")!="") p_table.asc=($p.cookie.get(p_table.name+"asc")==1?true:false);
			if ($p.cookie.get(p_table.name+"filter")!="") p_table.filter=($p.cookie.get(p_table.name+"filter")).split(" AND ");
		}
	},

	/*
		p_table.show : display the table 
	*/
	show:function(v_start,v_count)
	{
		if (v_start==indef) v_start=0;
		if (v_count==indef) v_count=p_table.rows.length-v_start;
		//limit to the items loaded
		var l_nbToDisplay=$p.min(p_table.rows.length-v_start,v_count);

		var l_s='<table cellpadding="3" cellspacing="0" border="1" bordercolor="#c6c3c6" width="'+p_table.width+'">';
		if (p_table.title!="") l_s+="<tr><td colspan='"+p_table.headers.length+"' bgcolor='#c6c3c6'>"+p_table.title+"</td></tr>";
		l_s+="<tr>";
		for (var i=0;i<p_table.headers.length;i++)
		{
			l_s+="<td bgcolor='#c6c3c6'"+(p_table.headers[i].width==""?"":" width='"+p_table.headers[i].width+"'")+">";
			if (p_table.headers[i].sorted) l_s+="<a href='#' onclick='p_table.sortAndRefresh("+i+","+((p_table.order==i && !p_table.asc)?"true":"false")+")'>";
			l_s+=p_table.headers[i].label+(p_table.headers[i].sorted?" "+(p_table.order==i?(p_table.asc?$p.img("ico_up_arrow.gif"):$p.img("ico_down_arrow.gif")):""):"");
			if (p_table.headers[i].sorted) l_s+="</a>";
			l_s+="</td>";
		}
		l_s+="</tr>";

		for (var i=v_start;i<v_start+l_nbToDisplay;i++)
		{
			l_s+="<tr>";
			for (var j=0;j<p_table.rows[i].columns.length;j++)
			{
				l_s+="<td>"+p_table.rows[i].columns[j].label+"</td>";
			}
			l_s+="</tr>";
		}
		l_s+="<tr><td colspan='"+p_table.headers.length+"' bgcolor='#c6c3c6'><center>";
		if (v_start>0) l_s+="<a href='#' onclick='p_table.show("+$p.max(0,(v_start-v_count))+","+v_count+");'>"+$p.img("ico_previous3.gif",indef,indef,"","imgmid")+" "+lg("previous")+"</a> &nbsp; ";
		if (v_start+v_count<p_table.rows.length) l_s+=" &nbsp; <a href='#' onclick='p_table.show("+$p.min(p_table.rows.length,(v_start+v_count))+","+v_count+");'>"+lg("next")+" "+$p.img("ico_next3.gif",indef,indef,"","imgmid")+"</a>";
		l_s+="</center></td></tr>"
		if (p_table.footer!="") l_s+="<tr><td colspan='"+p_table.headers.length+"' bgcolor='#c6c3c6'>"+p_table.footer+"</td></tr>";
		$p.print(p_table.container,l_s);
	},
	/*
		p_table.sortAndRefresh : sort the table
		Parameters:

			v_order - id of the column to sort
			v_asc - true=ascendant order
			v_start - beginning of the display
			v_count - number of items to display
	*/
	sortAndRefresh:function(v_order,v_asc,v_start,v_count)
	{
		if (v_order!=indef) p_table.order=v_order;
		if (v_asc!=indef) p_table.asc=v_asc?true:false;
		p_table.rows.sort(p_table.sortrule);
		p_table.show(v_start,v_count);
		$p.cookie.write(p_table.name+"order="+p_table.order)
		$p.cookie.write(p_table.name+"asc="+(p_table.asc?1:0));
	},
	sortrule:function(v_a,v_b)
	{
		var v_reverse=p_table.asc?-1:1;
		if ($p.string.lc($p.string.removeTags(v_a.columns[p_table.order].label))<$p.string.lc($p.string.removeTags(v_b.columns[p_table.order].label))) return -1*v_reverse;
		if ($p.string.lc($p.string.removeTags(v_a.columns[p_table.order].label))>$p.string.lc($p.string.removeTags(v_b.columns[p_table.order].label))) return 1*v_reverse;
		return 0; 
	},
	
	/*
		p_table.displayFitler : display the column data filter
		Parameters:

			 container div id
	*/
	displayFilter:function(v_div)
	{
		var l_s="";
		for (var i=0;i<p_table.filter.length;i++)
		{
			l_s+=p_table.filter[i]+" <a href='#' onclick='p_table.remFilter("+i+");return false;'>"+$p.img("ico_close.gif",12,11,lg("suppress"),"imgmid")+"</a> &nbsp;";
		}
		l_s+="<br /><form name='filter' onsubmit='p_table.addFilter(this);return false;'><select name='field' onchange='p_table.fillFilter(this.value)'>";
		var l_firstFiltered;
		for (var i=0;i<p_table.headers.length;i++)
		{
			if (p_table.headers[i].filtered)
			{
				l_s+="<option value=\""+i+"\">"+p_table.headers[i].label+"</option>";
				if (l_firstFiltered==indef) l_firstFiltered=i;
			}
		}
		l_s+="</select>";
		l_s+=" <select name='comparator'><option value='='>egal à</option><option value='!='>différent de</option><option value='<'><</option><option value='>'>></option><option value='<='><=</option><option value='>='>>=</option></select>";
		l_s+=" <select name='val'></select> <input type='submit' value='Ajouter ce filtre' /></form>";
		$p.print(v_div,l_s);
		p_table.fillFilter(l_firstFiltered);
	},
	/*
		p_table.fillFilter : fill filter input 
	*/
	fillFilter:function(v_id)
	{
		document.forms.filter.val.options.length=0;
		var l_options=[],l_optionsLabel=[];
		for (var i=0;i<p_table.rows.length;i++)
		{	
			if (!$p.array.find(l_options,p_table.rows[i].columns[v_id].val))
			{
				l_options.include(p_table.rows[i].columns[v_id].val);
				l_optionsLabel.push($p.string.removeTags(p_table.rows[i].columns[v_id].label));
			}	
		}
		for (var i=0;i<l_options.length;i++)
		{
			document.forms.filter.val.options[document.forms.filter.val.options.length] = new Option(l_optionsLabel[i],l_options[i]);
		}
	},
	/*
		p_table.addFilter : add a new filter for table information
		Parameters:

			 v_form - form containing filter value
	*/
	addFilter:function(v_form)
	{
		p_table.filter.push(p_table.headers[v_form.field.value].name+" "+v_form.comparator.value+" '"+v_form.val.value+"'");
		$p.cookie.write(p_table.name+"filter="+p_table.filter.join(" AND "));
		p_table.refresh();
	},
	/*
		p_table.remFilter : remove a filter 
	*/
	remFilter:function(v_i)
	{
		p_table.filter.splice(v_i,1);
		$p.cookie.write(p_table.name+"filter="+p_table.filter.join(" AND "));
		p_table.refresh();
	}
}





/*
	Function: correctMailEncoding 
    
                correct emails content encoding chars
	
            Parameters:

			 email content (! need to be lower case)
	
	Returns:
			 corrected content
*/
function correctMailEncoding(v_s,v_convertHTML)
{
	if (v_s.indexOf("?utf-8?") != -1)
	{
		v_s=v_s.replace(/\=\?utf\-8\?q\?/g,"");
		//v_s=v_s.replace(/\=\?UTF\-8\?Q\?/g,"");
		v_s=v_s.replace(/\?\= /g,"");
		v_s=v_s.replace(/\_/g," ");
		v_s=v_s.replace(/\?\=/g,"");
	}
	else if (v_s.indexOf("-8859-1?") != -1)
	{
		//v_s=v_s.replace(/\=\?ISO-8859-1\?Q\?/g,"");
		v_s=v_s.replace(/\=\?iso-8859-1\?q\?/g,"");
		v_s=v_s.replace(/\?\= /g,"");
		v_s=v_s.replace(/\_/g," ");
		v_s=v_s.replace(/\?\=/g,"");
	}
	v_s=v_s.replace(/\=e9/g,"é");
	v_s=v_s.replace(/\=e0/g,"à");
	v_s=v_s.replace(/\=e8/g,"è");
	v_s=v_s.replace(/\=e7/g,"ç");
	v_s=v_s.replace(/\=f9/g,"ù");
	v_s=v_s.replace(/\=ea/g,"ê");
	v_s=v_s.replace(/\=e2/g,"â");
	v_s=v_s.replace(/\=f4/g,"ô");
	v_s=v_s.replace(/\=ee/g,"î");
	v_s=v_s.replace(/\=fb/g,"û");
	v_s=v_s.replace(/\=f6/g,"ö");
	v_s=v_s.replace(/\=fc/g,"ü");
	v_s=v_s.replace(/\=ef/g,"ï");
	v_s=v_s.replace(/\=e4/g,"ä");
	v_s=v_s.replace(/\=eb/g,"ë");
	v_s=v_s.replace(/\=28/g,"(");
	v_s=v_s.replace(/\=29/g,")");
	v_s=v_s.replace(/\=20/g," ");
    v_s=v_s.replace(/\=2d/g,"-");
	v_s=v_s.replace(/\=3a/g,":");
	v_s=v_s.replace(/\=25/g,"€");
	v_s=v_s.replace(/\=ba/g,"°");
	v_s=v_s.replace(/=c3=a9/g,"é");
	v_s=v_s.replace(/=c3=a8/g,"è");
	v_s=v_s.replace(/=c3=a0/g,"à");
	v_s=v_s.replace(/=c3=a2/g,"â");
	v_s=v_s.replace(/=c3=aa/g,"ê");
	v_s=v_s.replace(/=c3=ae/g,"î");
	v_s=v_s.replace(/=c3=b4/g,"ô");
	v_s=v_s.replace(/=c3=bb/g,"û");
	v_s=v_s.replace(/=c3=b9/g,"ù");
	v_s=v_s.replace(/=c3=bc/g,"ü");
	v_s=v_s.replace(/=c3=a7/g,"ç");
	v_s=v_s.replace(/=09/g," ");
	v_s=v_s.replace(/\=\n/g,"");
	while (v_s.indexOf("  ") != -1) v_s=v_s.replace(/  /g," ");
	v_s=v_s.replace(/\n /g,"\n");
	while (v_s.indexOf("\n\n") != -1) v_s=v_s.replace(/\n\n/g,"\n");
	if (v_convertHTML) v_s=v_s.replace(/\n/g,"<br />");
	v_s=v_s.replace(/\r/g,"");
	return v_s;
}

/*
	Function: notifyByEmail 
                send email to user
	
            Parameters:

		v_address - email addresses (string or array)
		v_title - email title
		v_description - email message
		v_from (optional) - sender email
		v_decode - define if an UTF8 decode needs to be done
*/
function notifyByEmail(v_address,v_title,v_description,v_from)
{
	var l_address="";
	if (typeof(v_address)=="string" && v_address!="")
	{
		l_address="em0="+v_address+"&";
	}
	else
	{
		for (var i=0;i<v_address.length;i++)
		{
			l_address+="em"+i+"="+v_address[i]+"&";
		}
	}
	if (l_address!="")
	{
		$p.ajax.call(posh["scr_sendemail"],
			{
				'type':'execute',
				'variables':l_address+"title="+$p.string.esc(v_title)+"&desc="+$p.string.esc(v_description)+"&from="+$p.string.esc((v_from==indef?"":v_from)),
				'alarm':true
			}
		);
	}
	return false;
}
/*
	reset_rand : reset the rand variable
*/
function reset_rand()
{
	rand=$random(0,10000).toInt();
}
function getXMLprop(v_item,v_name,v_type,v_required,v_default){return $p.ajax.getProp(v_item,v_name,v_type,v_required,v_default);}
function getXMLval(v_item,v_name,v_type,v_required,v_default){return $p.ajax.getVal(v_item,v_name,v_type,v_required,v_default);}
var p_plugin={open:function(){return $p.plugin.open();},init:function(v_title,v_id){return $p.plugin.init(v_title,v_id);},menu:function(v_elemts,v_selected){return $p.plugin.menu(v_elemts,v_selected);},wait:function(){return $p.plugin.wait();},content:function(v_content){return $p.plugin.content(v_content);},hide:function(){return $p.plugin.hide();},clear:function(){return $p.plugin.clear();},useWidget:function(){return $p.plugin.useWidget();}}
var p_app={openHome:function(v_prof){return $p.app.openHome(v_prof);}}
/*
	_IG_AdjustIFrameHeight : update module height based on its content
*/
function _IG_AdjustIFrameHeight()
{
    //function desactivated for opera
    if (!Browser.Engine.presto)
    {
    	var l_height;
    	if (document.height){
    		l_height=document.height;
    	}
    	else if (document.all){
    		if (document.compatMode && document.compatMode != 'BackCompat'){
    			l_height=document.documentElement.scrollHeight + 5;
    		}
    		else{
    			l_height = document.body.scrollHeight + 5;
    		}
    	} 
    	if (navigator.appName=="Netscape"){
    		top.outerHeight=l_height;
    	}
    	else top.resizeTo(400,l_height);
    }
}
/*
	Function: _IG_SetTitle 
                change widget title
	
        Parameters:

			 widget new title
*/
function _IG_SetTitle(title)
{
}
/*
	_IG_Prefs : manage Modules preferences
	Parameters:

			 uniq ID of the module
*/
function _IG_Prefs(uniq)
{
	this.uniq=uniq;
	this.id=$p.app.widgets.uniqToId(uniq);
	this.vars=tab[$p.app.tabs.sel].module[this.id].vars;
	this.getString=getString;
	this.getInt=getInt;
	this.getBool=getBool;
	this.set=setPrefVal;
	this.open=openPref;
}
/*
	getString : get string value of a module parameter
	Parameters:

			 v_var - 
*/
function getString(v_var)
{
	return $p.string.getVar(this.vars,v_var);
}
/*
	getInt : get integer value of a module parameter
	Parameters:

			 v_var -
*/
function getInt(v_var)
{
	return ($p.string.getVar(this.vars,v_var)).toInt();
}
/*
	getBool : get boolean value of a module paramter
	Parameters:

			 v_var -
*/
function getBool(v_var)
{
	return ($p.string.getVar(this.vars,v_var)==1?true:false);
}
/*
	setPrefVal : define parameter value
	Parameters:

		v_var -   
		v_value - new value for this parameter
*/
function setPrefVal(v_var,v_value)
{
	tab[$p.app.tabs.sel].module[this.id].changeVar(v_var,v_value);
	//avoid this process in testmodule
	if ((window.location.href).indexOf(posh["testmodule"]) == -1)
        $p.app.widgets.param.getModuleParam(this.id);
}
function openPref(){}
/*
	_IG_Tabs : tabs management in modules
	Parameters:

		v_id - module ID
		v_selTab - selected tab
*/
function _IG_Tabs(v_id,v_selTab)
{
	this.moduleId=v_id;
	this.selTab=v_selTab;
	this.addTab=addTab;
	this.addDynamicTab=addDynamicTab;
	this.setSelectedTab=setSelectedTab;
	this.moveTab=moveTab;
	this.tabsContainer=null;
	this.ulObj=null;
}
/*
	addTab : add a new tab in module
	Parameters:

		v_name - name of the tab
		v_divId - ID of the div containing tab information
		v_fct - function called on tab opening
*/
function addTab(v_name,v_divId,v_fct)
{
	//define default tab if not already defined
	if (this.selTab==indef) this.selTab=v_name;
	//create tabs div if not already existing
	if (this.tabsContainer==null)
	{
		this.tabsContainer = new Element('div', { 'id': 'tabsdiv' } );	 
		document.body.appendChild(this.tabsContainer);
	}
	//create content div if not already existing
	if (v_divId==indef) v_divId="contentdiv";
	if ($(v_divId)==null)
	{
		l_obj = new Element('div', { 'id': v_divId } );	 
		document.body.appendChild(l_obj);
	}
	else
	{
		var l_obj=$(v_divId);
	}
	//create ul obj if not already existing
	if (this.ulObj==null)
	{
		this.ulObj = new Element('ul', { "class": "tablist" } );	
		this.tabsContainer.appendChild(this.ulObj);
	}
	//add the new tab
	var l_liObj = new Element('li', { "class": (this.selTab==v_name)?"tabsel":"tab", "id": v_name } );			  
	var l_aObj = new Element('a', 
		{
			'events': {
					'mouseup': function(){
					changeSelectedTab(this.title,this.main);
					this.fct(this.selDiv);}
					  },
			'href': 'javascript:void(null);',
			'main': this
		}
	);
	l_aObj.fct = v_fct;
	l_aObj.selDiv = v_divId;
	l_aObj.title = v_name;
    l_aObj.appendChild(document.createTextNode(v_name));			
	l_liObj.appendChild(l_aObj);
	this.ulObj.appendChild(l_liObj);
	if (this.selTab==v_name) v_fct(v_divId);
	
	return l_obj;
}
/*
	addDynamicTab
*/
function addDynamicTab(v_name,v_fct)
{
	addTab(v_name,indef,v_fct);
}
/*
	setSelectedTab : select a tab
	Parameters:

			 ID of the tab selected
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
function changeSelectedTab(v_name,v_obj)
{
	v_obj.selTab=v_name;
	for (var i=0;i<v_obj.ulObj.childNodes.length;i++)
	{
		var l_node=v_obj.ulObj.childNodes[i];
		if (l_node.id==v_name) v_obj.setSelectedTab(i);
	}
}
function moveTab(){}

/*
	_IG_Callback : module callback function
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
	createMyModule : link to modules tutorial tool
*/
function createMyModule()
{
	if ($p.app.user.id==0)  {
		$p.app.alert.show(lg("msgTutorialConn"));
	}
	else    {
		$p.url.openLink("../tutorial/");
	}
	return false;
}
/*
	dirOptxxxx : manage modules directory selections
	Parameters:

		v_id - directory ID
		v_level - directory depth level
*/
function dirOptOver(v_id)
{
	if ($("dir"+v_id)&&($("dir"+v_id)).className=="diropti") $p.setClass("dir"+v_id,"diropta");
}
function dirOptOut(v_id)
{
	if ($("dir"+v_id)&&($("dir"+v_id)).className=="diropta") $p.setClass("dir"+v_id,"diropti");
}
function dirOptSel(v_id,v_level)
{
	$p.setClass("dir"+v_id,"diropts");
	if (dirOptSelId[v_level]!=indef && dirOptSelId[v_level]!=v_id){$p.setClass("dir"+dirOptSelId[v_level],"diropti");}
	dirOptSelId[v_level]=v_id;
}
function catOptOver(v_id,v_secured)
{
	if ($("dir"+v_id)&&($("dir"+v_id)).className=="catopt"+(v_secured==1?"s":"")+"i") $p.setClass("dir"+v_id,"catopt"+(v_secured==1?"s":"")+"a");
}
function catOptOut(v_id,v_secured)
{
	if ($("dir"+v_id)&&($("dir"+v_id)).className=="catopt"+(v_secured==1?"s":"")+"a") $p.setClass("dir"+v_id,"catopt"+(v_secured==1?"s":"")+"i");
}
function catOptSel(v_id,v_level,v_secured)
{
	$p.setClass("dir"+v_id,"catopt"+(v_secured==1?"s":"")+"s");
	if (dirOptSelId[v_level]!=indef && dirOptSelId[v_level]!=v_id){$p.setClass("dir"+dirOptSelId[v_level],"catopt"+(dirOptSelSec[v_level]==1?"s":"")+"i");}
	dirOptSelId[v_level]=v_id;
	dirOptSelSec[v_level]=v_secured;
}
/*
	hideBox : hide horizontal menu box
*/
function hideBox()
{
	$p.print("box","");
	$p.show("box","none");
	//widgetDecalY=0;
	showBoxStatus=false;
	$p.app.widgets.place($p.app.tabs.sel);
}
/*
	hideAllBox : hide all menus
*/
function hideAllBox()
{
		
	hideBox();
	//if (__menuposition=="v"){$p.app.menu.hide();}
}
function openmod(v_id,v_vars,v_type,v_secured){return $p.app.widgets.open(v_id,v_vars,v_type,v_secured);}
function closeNewMod(v_type){return $p.app.widgets.close(v_type);}
/*
	additionalMod : check if an aditional module needs to be displayed
*/
function additionalMod()
{
	if ($p.string.getVar(window.location.search.substring(1),"open"))
	{
		var l_vars=($p.string.getVar(window.location.search.substring(1),"vars"))?$p.string.getVar(window.location.search.substring(1),"vars"):"";
		$p.app.widgets.open($p.string.getVar(window.location.search.substring(1),"open"),l_vars);
	}
}
function _IG_Analytics(){}
/*
	_IG_RegisterOnloadHandler : manage modules onload event
	Parameters:

			 function called when module is loaded
*/
function _IG_RegisterOnloadHandler(v_fct)
{
	window.onload=v_fct;
}
/*
	_IG_FetchContent : load HTML content from a file
	Parameters:

		v_url - file loaded
		v_fct - function called when file is loaded
*/
function _IG_FetchContent(v_url,v_fct)
{
	$p.ajax.call(v_url,
		{
			'type':'load',
			'callback':
			{
				'function':v_fct
			},
			'source':'html',
			'method':'GET'
		}
	);
}
function _IG_FetchXmlContent(v_url,v_fct)
{
	$p.ajax.call(v_url,
		{
			'type':'load',
			'callback':
			{
				'function':v_fct
			},
			'source':'xml',
			'method':'GET'
		}
	);
}
function _IG_FetchFeedAsJSON(v_url,v_fct,v_entries,v_summaries)
{
	if (v_entries==indef) v_entries=5;
	if (v_summaries==indef) v_summaries=false;
	$p.ajax.call(v_url,
		{
			'type':'load',
			'callback':
			{
				'function':getRssFromXml,
				'variables':
				{
					'function':v_fct,
					'entries':v_entries,
					'summary':v_summaries
				}
			},
			'source':'xml',
			'method':'GET'
		}
	);
}
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
function _IG_GetImage(v_url)
{
	return v_url;
}
function _IG_GetCachedUrl(v_url)
{
	return v_url;
}
function _IG_EmbedFlash(swf_url, swf_container, opt_params)
{
	var so = new SWFObject(swf_url, "flash", "100%", "100%", "6");
	for(opt_param in opt_params)
	{
		so.addParam(opt_param,opt_params[opt_param]);
	}
    so.write(swf_container);
}
function _IG_GetFlashMajorVersion(){}
function getRssFromXml(response,vars)
{
	var feed={};
	feed.Title=$p.ajax.getVal(response,"title","str",false,"no title");
	feed.Link=$p.ajax.getVal(response,"link","str",false,"");
	feed.Author=$p.ajax.getVal(response,"author","str",false,"");
	feed.Description=$p.ajax.getVal(response,"description","str",false,"");
	feed.Entry=[];
	var i=0;
	while (response.getElementsByTagName("item")[i] && i<vars[1])
	{
		var result=response.getElementsByTagName("item")[i];
		feed.Entry[i]={};
		feed.Entry[i].Title=$p.ajax.getVal(result,"title","str",false,"no title");
		feed.Entry[i].Link=$p.ajax.getVal(result,"link","str",false,"");
		feed.Entry[i].Summary=($p.ajax.getVal(result,"description","str",false,"")).substr(0,200);
		feed.Entry[i].Date=$p.ajax.getVal(result,"pubdate","str",false,"");
		i++;
	}
	vars[0](feed);
}
/*
	getConfig : Save portal configuration in cookie
*/
function getConfig()
{
	var l_ret="w="+tab[$p.app.tabs.sel].colnb+"&s="+tab[$p.app.tabs.sel].style+"&n="+$p.string.esc($p.app.tabs.currName)+"&t="+tab[$p.app.tabs.sel].showType+"&nb="+tab[$p.app.tabs.sel].newspapernb+"&i="+tab[$p.app.tabs.sel].icon+"&a="+(tab[$p.app.tabs.sel].moduleAlign?"Y":"N");
	for (var i=0;i!=tab[$p.app.tabs.sel].module.length;i++)
	{
		l_ret+="&id"+i+"="+tab[$p.app.tabs.sel].module[i].id+"&pos"+i+"="+tab[$p.app.tabs.sel].module[i].newpos+"&col"+i+"="+tab[$p.app.tabs.sel].module[i].newcol+"&posj"+i+"="+tab[$p.app.tabs.sel].module[i].newposj+"&x"+i+"="+tab[$p.app.tabs.sel].module[i].newx+"&y"+i+"="+tab[$p.app.tabs.sel].module[i].newy+"&var"+i+"="+$p.string.esc(tab[$p.app.tabs.sel].module[i].vars)+"&blocked"+i+"="+(tab[$p.app.tabs.sel].module[i].blocked?"1":"0")+"&minimized"+i+"="+(tab[$p.app.tabs.sel].module[i].minimized?"1":"0");
	}
	return l_ret;
}
/*
	getAvailPortals : get pages list for a user
*/
function getAvailPortals(v_sess,md5key)
{
    if (typeof(md5key['md5key'])=="undefined") {
        md5key['md5key']='';
    }
	if (v_sess)
	{
		$p.ajax.call(posh["xmltabs"],
			{
				'type':'load',
				'callback':
				{
					'function':showAvailPortals,
                    'variables':
                    {
                        'md5key':md5key['md5key']
                    }
				}
			}
		);
	}
}
/*
	showAvailPortals : display available pages for a user
*/
function showAvailPortals(response,vars)
{
	var l_s="<b>"+lg("selectThePortalDestination")+"</b> :<br />",l_result=response.getElementsByTagName("tab");
	for (var i=0;i<l_result.length;i++)
	{
		if ($p.ajax.getVal(l_result[i],"type","int",false,0)==3 || $p.ajax.getVal(l_result[i],"type","int",false,0)==1)
		{
			l_s+="<br />"+$p.img("ico_right_arrow.gif",6,9)+" <a href='#' onclick='addModToAvailPortal("+$p.ajax.getVal(l_result[i],"number","int",false,1)+",\""+vars['md5key']+"\");'>"+$p.ajax.getVal(l_result[i],"name","str",false,"- ? -")+"<a>";
		}
	}
	$p.print("connectiondiv",l_s+"<br /><br />");
}
/*
	addModToAvailPortals : add a new module for a user page (stored in cookie)
	Parameters:

			 tab DB ID where the module is added
*/
function addModToAvailPortal(v_prof,md5key)
{
    var md5Parameter="";
    if ( typeof(md5key)!="undefined" && md5key!='' ) {
        md5Parameter="&md5="+md5key;
    }
    
	//set current page as the one selected
	$p.cookie.write("currentpage="+v_prof);
	//get feed id for an rss widget
	var l_fid=(tab[$p.app.tabs.sel].module[0].format=='R')?$p.string.getVar(tab[$p.app.tabs.sel].module[0].vars,'pfid'):0;
	$p.url.openLink(posh["scr_config_updateportal"]+"?prof="+v_prof+"&modid="+tab[$p.app.tabs.sel].module[0].id+"&f="+l_fid+"&v="+$p.string.esc(tab[$p.app.tabs.sel].module[0].vars)+md5Parameter);
}
var p_connection={set:function(v_form,v_function,v_type){return $p.app.connection.set(v_form,v_function,v_type);},subscribe:function(v_form,v_function){return $p.app.connection.subscribe(v_form,v_function);},saveMenu:function(){return $p.app.connection.saveMenu();},menu:function(){return $p.app.connection.menu();}}
/*
	MODULE_ID_to_id : get the module array id from the __MODULE_ID__ variable in a 'M' module
	Parameters:

			 __MODULE_ID__
*/
function MODULE_ID_to_id(v_id,v_tab)
{
	var parts=v_id.split("_");
	return $p.app.widgets.uniqToId(parts[0],v_tab);
}
/*
	hideParent : hide parent object of an object
	Parameters:

			 object
*/
function hideParent(v_obj)
{
	v_obj.parentNode.setStyle("display","none");
	v_obj.parentNode.setStyle("display","");
}
/*
	objMove : define object position / action during move
	Parameters:

		v_obj - object moved
		v_x - horizontal position of the mouse
		v_y - vertival position of the mouse
		v_parent - parent object
		v_direction - allowed movements - vertical / horizontal / multidim
		v_type : object type (module/tab)
*/
function objMove(v_obj,v_x,v_y,v_parent,v_direction,v_type)
{
	var l_nextMod=null;
	var l_x,l_y,l_w,l_h,l_treated=false;
	var l_pond=(v_direction=="multidim")?40:v_obj.offsetHeight;
	var l_lstNb=v_parent.length?v_parent.length:2;
	//is object moving over an existing one
	for(var j=1;j<l_lstNb;j++)
	{
		var l_lst=v_parent.length?v_parent[j]:v_parent;
		for(var i=0;i<l_lst.childNodes.length;i++)
		{
			var l_node=l_lst.childNodes[i];
			l_x=l_node.pagePosLeft;
			l_y=l_node.pagePosTop;
			l_w=l_node.offsetWidth;
			l_h=l_node.offsetHeight;
			if (v_x>l_x && v_x<(l_x+l_w) && v_y>l_y && v_y<(l_y+l_h))
			{
				l_treated=true;
				if (v_y<(l_y+l_pond))
				{
					if (l_node==v_obj.nextSibling&&l_node.nextSibling)
					{
						l_nextMod=l_node.nextSibling;
						break;
					}
					else
					{
						l_nextMod=l_node;
						break;
					}
				}
			}
		}
	}
	//if module over a tab
	if (!l_treated && v_type=="module" && $p.app.user.id>0)
	{
		var l_tabs=$("tabsframe");
		if (l_tabs!=null)
		for(var i=0;i<l_tabs.childNodes.length;i++)
		{
			var l_node=l_tabs.childNodes[i];
			if (l_node.id=="") continue;
			var l_id=l_node.id.replace(/tab/,"");
			if (l_id==$p.app.tabs.sel) continue;
			//l_x=l_node.pagePosLeft;
			l_x=$p.getPos(l_node,"Left");
			l_y=$p.getPos(l_node,"Top");
			//l_x=l_node.getLeft();
			//l_y=l_node.getTop();
			
			l_w=l_node.offsetWidth;
			l_h=l_node.offsetHeight;
			if (v_x>l_x && v_x<(l_x+l_w) && v_y>l_y && v_y<(l_y+l_h))
			{
				l_treated=true;
				$p.app.tabs.moduleOver(l_id);
				//v_obj.style.display="none";
				return true;
			}
			else
			{
				$p.app.tabs.moduleOut(l_id);
			}
		}
	}
//	//is object out of selection (under first row)
//	if (!l_treated&&v_direction=="multidim"){
//		for(var j=1;j<l_lstNb;j++){
//			var l_lst=v_parent.length?v_parent[j]:v_parent;
//			var l_node=l_lst.childNodes[0];
//			l_x=l_node.pagePosLeft;
//			l_y=l_node.pagePosTop;
//			l_w=l_node.offsetWidth;
//			l_h=l_node.offsetHeight;
//			if (v_x>l_x&&v_y<=l_y) {l_nextMod=l_node;break;}
//		}
//	}
	//is object out of selection (aside the first col)
	if (!l_treated&&v_direction=="multidim")
	{
		var l_lst=v_parent.length?(v_parent[0]==indef?v_parent[1]:v_parent[0]):v_parent;
		for(var i=0;i<l_lst.childNodes.length;i++)
		{
			var l_node=l_lst.childNodes[i];
			l_x=l_node.pagePosLeft;
			l_y=l_node.pagePosTop;
			l_w=l_node.offsetWidth;
			l_h=l_node.offsetHeight;
			if (v_x<=l_x&&v_y<=l_y) {l_nextMod=l_node;break;}
		}
	}
	//is the module below the "necessary" boxes
	if (!l_treated&&v_direction=="multidim")
	{
		for(var j=1;j<l_lstNb;j++)
		{
			var l_lst=v_parent.length?v_parent[j]:v_parent;
			for(var i=0;i<l_lst.childNodes.length;i++)
			{
				var l_node=l_lst.childNodes[i];
				if (l_node.className=="necessary")
				{
					l_x=l_node.pagePosLeft;
					l_y=l_node.pagePosTop;
					l_w=l_node.offsetWidth;
					if (v_x>l_x && v_x<(l_x+l_w) && v_y>l_y)
					{
						l_treated=true;
						l_nextMod=l_node;break;
					}
				}
			}
		}
	}

	if (l_nextMod!=null&&v_obj!=l_nextMod)
	{
		if (v_type=="module")
		{
			var l_id=$p.app.widgets.getModuleId(l_nextMod);
			if (l_id!=-1 && tab[$p.app.tabs.sel].module[l_id].blocked) return true;
		}
		l_nextMod.parentNode.insertBefore(v_obj,l_nextMod);
		$p.app.widgets.move.setPos(v_obj,v_parent);
		//only for IE (Is causing a displaying issue with FF on newspaper mode : the col1 is moving under the newspaper div)
		if ($p.navigator.IE) hideParent(v_obj);
	}
	return true;
}
/*
	objFreeMove : Manage module free movements (if modules are not aligned)
	Parameters:

		v_obj - object moved
		v_x - horizontal position of the mouse
		v_y - vertival position of the mouse
		v_parent - parent object
		v_direction - allowed movements : vertical / horizontal / multidim
		v_type - object type (module/tab)
*/
function objFreeMove(v_obj,v_x,v_y,v_parent,v_direction,v_type)
{
	var l_x,l_y,l_treated=false;

	//if module over a tab
	if (v_type=="module" && $p.app.user.id>0)
	{
		var l_tabs=$("tabsframe");
		if (l_tabs!=null)
		for (var i=0;i<l_tabs.childNodes.length;i++)
		{
			var l_node=l_tabs.childNodes[i];
			if (l_node.id=="") continue;
			var l_id=l_node.id.replace(/tab/,"");
			if (l_id==$p.app.tabs.sel) continue;
			//l_x=l_node.pagePosLeft;
			l_x=$p.getPos(l_node,"Left");
			l_y=$p.getPos(l_node,"Top");
			//l_x=l_node.getLeft();
			//l_y=l_node.getTop();
			l_w=l_node.offsetWidth;
			l_h=l_node.offsetHeight;
			if (v_x>l_x && v_x<(l_x+l_w) && v_y>l_y && v_y<(l_y+l_h))
			{
				l_treated=true;
				$p.app.tabs.moduleOver(l_id);
				//v_obj.style.display="none";
				return true;
			}
			else
			{
				$p.app.tabs.moduleOut(l_id);
			}
		}
	}
	//is object out of selection (under first row)
	if (!l_treated&&v_direction=="multidim")
	{
		l_y=$p.getPos(v_parent,"Top");
		if (v_y<=l_y)
			return false;
		if (v_obj.style.left.toInt()<widgetDecalX)
			return false;
	}

	return true;
}
/*
	_bringToFront : bring widget to front
	Parameters:

			 uniq ID of the widget
*/
function _bringToFront(v_uniq)
{
	parent.tab[$p.app.tabs.sel].module[$p.app.widgets.uniqToId(v_uniq)].bringToFront();
}
function idToPos(v_id){return $p.app.tabs.idToPos(v_id);}
function uniqToId(v_uniq,v_tab){return $p.app.widgets.uniqToId(v_uniq,v_tab);}
/*
	box : Generate an horizontal menu
	Parameters:
 
		v_h - height of the menu (optional)
		v_title - title displayed on top of the menu 
		hideFct - function that close the menu (optional). If undefined, close button is not displayed
		v_content - HTML content of the menu
		v_hdr - HTML header of the menu (optional)
	
	Returns:
			 HTML code of the menu
*/
function box(v_h,v_title,hideFct,v_content,v_hdr)
{
	var l_s = (v_hdr==indef ? '' : v_hdr)
		+ '<table cellpadding="10" cellspacing="0" width="100%">'
		+ '<tr>'
		+ '<td'+(v_h > 0 ? ' height="'+v_h+'"' : '')+ '>'
		+ '<table class="pbox" cellpadding="0" cellspacing="0" border="0" width="100%">'
		+ '<tr>'
		+ '<td class="pboxheader">'
		+ '<table width="100%">'
		+ '<tr>'
		+ '<td style="color: #000;font-size: 1em;width: 100%">'
		+ '<b>'+v_title+'</b>'
		+ '</td>'
		+ (hideFct == '' ? '' : '<td style="text-align: right;white-space: nowrap;background: url(../images/ico_close.gif) no-repeat left center;"><a class="w" href="#" onclick="'+hideFct+';return false;">'+$p.img('s.gif',13,13,lg("lblClose"),"imgmid")+' '+lg("lblClose")+'</a></td>')
		+ '</tr>'
		+ '</table>'
		+ '</td>'
		+ '</tr>'
		+ '<tr>'
		+ '<td valign="top" class="pboxcontent">'
		+ v_content
		+ '</td>'
		+ '</tr>'
		+ '</table>'
		+ '</td>'
		+ '</tr>'
		+ '</table>';
	return l_s;
}
/*
	clearTimer : close a timer object
	Parameters:

			 timer name
*/
function clearTimer(v_timer)
{
	clearTimeout(v_timer);
	v_timer=0;
}
// Sort by module min width
function widthSort(a,b)
{
	if (a.minModSize>b.minModSize) return -1;
	if (a.minModSize<b.minModSize) return 1;
	return 0; 
}


//********************************* OLD FUNCTIONS (kept for compatibility reasons) ***********************************************************************

/* 
    Function: getXml
        Get XML results

        ** Parameters:
**
**      v_url (string) - url of the XML page containing data to retrieve
**      fct(function) - callback function that will treat the results. responseXML and fct_vars are the arguments sent to this function.
**      v_fctvars (string) - arguments sent to the callback function
**      v_type (string) - "xml"=return xml, "html"=return text
**      v_vars(string) - variables sent to the xml page
**      v_method (string) - "post"=send v_vars as post variables, "get" ...
**      v_escapefct (function) - function called if XML page loading does not work

** Returns : error number
*/
function getXml(v_url,fct,v_fctvars,v_type,v_vars,v_method,v_escapefct,v_async,v_priority)
{
	if (v_type==indef) v_type="xml";
	if (v_method==indef) v_method="GET";
	if (v_async==indef) v_async=true;
	if (!pfolder) {pfolder="";}
	if (v_priority==indef) v_priority=2;
	var l_action=[v_url,fct,v_fctvars,v_type,v_vars,v_method,v_escapefct,v_async];
	
	$p.ajax.requests.push(new $p.ajax.request("load",l_action,v_priority));
	$p.ajax.executeRequests();

	return false;
}
/* 
    Function: executescr
        Execute PHP script with XML file
 
** Parameters:
**
**      v_scr (string) - php script (located by default on /portal folder
**      v_vars (string) - post variables sent to the scripts
**      v_alarm(boolean) - define if the alert return by script is displayed in the user page
**      v_forced (boolean) - define if the script is executed when user is not connected
**      fct (function) - callback function, called at the end of the script processing
**      fctvars (string or array) - vars sent directly to the callback function

** Returns : error number
*/
function executescr(v_scr,v_vars,v_alarm,v_forced,fct,v_priority,v_async,fctvars)
{
	var l_noerror=true,l_msg,l_err;

	if (allowSave||v_forced)
	{
		if (v_priority==indef) v_priority=2;
		if (v_async==indef) v_async=true;
		var l_action=new Array(v_scr,v_vars,v_alarm,fct,v_async,fctvars);

		$p.ajax.requests.push(new $p.ajax.request("execute",l_action,v_priority));
		
		$p.ajax.executeRequests();

		return;
	}
	else
	{
		var l_feed=pfolder+v_scr;
	}
	//debug
	$p.app.debug("execute : "+l_feed+" (variables: "+v_vars+")");
	return l_noerror;
}
/*
    Function: cloneObj
    
                            cloneObj
                            
                            Clone an object
*/
function cloneObj(o) {
     if(typeof(o) != "object") return o;
     if(o == null) return o;
   
     var newO = new Object();
   
     for(var i in o) newO[i] = cloneObj(o[i]);
      return newO;
}
