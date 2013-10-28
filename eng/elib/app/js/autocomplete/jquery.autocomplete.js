/**
*  Ajax Autocomplete for jQuery, version 1.1.3
*  (c) 2010 Tomas Kirda
*
*  Ajax Autocomplete for jQuery is freely distributable under the terms of an MIT-style license.
*  For details, see the web site: http://www.devbridge.com/projects/autocomplete/jquery/
*
*  Last Review: 04/19/2010
*  Fabio Ricci (fabio.ricci@ggaweb.ch) for HEG - Mai 2013 - complete enhancement to handle SKOS and hidden regions
*/

/*jslint onevar: true, evil: true, nomen: true, eqeqeq: true, bitwise: true, regexp: true, newcap: true, immed: true */
/*global window: true, document: true, clearInterval: true, setInterval: true, jQuery: true */


/* foreach extension to Array */
Array.prototype.foreach = function( callback ) {
	  for( var k=0; k<this .length; k++ ) {
	    callback( k, this[ k ] );
	  }
}

function stringToBoolean(string)
{
	switch(string.toLowerCase()){
		case "true": case "yes": case "1": return true;
		case "false": case "no": case "0": case null: return false;
		default: return Boolean(string);
	}
}

/**
 *Toogle Autocomplete hidden item to visible 
 */
function t(srcname,p)
{
	//alert('toggleautocompletevisible('+srcname+','+p+')');
	var autocomplete= document.getElementById(AUTOCOMPLETECONTAINER_ID);
	var rodin_search_field= document.getElementById("rodinsearch_s");
	var alldivs=autocomplete.getElementsByTagName("div");
	var firsthiddenele=true;
	for (i=0; i<alldivs.length; i++) 
	{
		var div=alldivs[i];
		if (div.getAttribute('src')==srcname)
		{
			/*Hide seemore item:*/
			if (div.getAttribute('p')=='seemore' 
			&& div.innerHTML.contains(p))
			{
				div.style.visibility='hidden';
				div.style.display='none';
			}	/*show hidden items: */
			else if (div.getAttribute('p')==p && div.style.visibility=='hidden')
			{
				div.style.visibility='visible';
				div.style.display='block';
			}
		}
	}
	/*reset focus to search field to continue moving up/down with arrows*/
	rodin_search_field.focus();
	return true;	
}


(function($) {

  var reEscape = new RegExp('(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join('|\\') + ')', 'g');

  function fnFormatResult(value, data, queryValue) {
  	/*DO NOT format inside HTML code - take always last text*/
  	var n=value.lastIndexOf(data); 
  	/*We are interested in data*/
  	/*separate value in leftside and rightside=data*/
  	var leftside;
  	var rightside;
  	if (queryValue.length>1) /*mark only from 2 chars len on... avoid html tags in queryValue <b></b>*/
  	{
  	if (n==-1) {leftside='';rightside=value;}
  	else {leftside=value.substr(0,n);rightside=value.substr(n).trim()}
    var pattern = '(' + queryValue.replace(reEscape, '\\$1') + ')';
    var retval= leftside+rightside.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>');
    if(0)  	alert('fnFormatResult(value='+value+', data='+data+', queryValue='+queryValue+'): \n\nleftside=('+leftside+') rightside=('+rightside+')\n\n'
      				+'Returns: ('+retval+')');
  	}
  	else retval = value;
    return retval;
  }

  function Autocomplete(el, options) {
    this.el = $(el);
    this.el.attr('autocomplete', 'off');
    this.suggestions = [];
    this.data = [];
    this.badQueries = [];
    this.selectedIndex = -1;
    this.currentValue = this.el.val();
    this.intervalId = 0;
    this.cachedResponse = [];
    this.onChangeInterval = null;
    this.ignoreValueChange = false;
    this.serviceUrl = options.serviceUrl;
    this.isLocal = false;
    this.options = {
      autoSubmit: false,
      minChars: 1,
      maxHeight: 300,
      deferRequestBy: 0,
      width: 0,
      highlight: true,
      params: {},
      fnFormatResult: fnFormatResult,
      delimiter: null,
      zIndex: 9999
    };
    this.initialize();
    this.setOptions(options);
  }
  
  $.fn.autocomplete = function(options) {
    return new Autocomplete(this.get(0)||$('<input />'), options);
  };


  Autocomplete.prototype = {

    killerFn: null,

    initialize: function() {

      var me, uid;
      me = this;
      uid = Math.floor(Math.random()*0x100000).toString(16);
      autocompleteElId = 'Autocomplete_' + uid;

      this.killerFn = function(e) {
        if ($(e.target).parents('.autocomplete').size() === 0) {
          me.killSuggestions();
          me.disableKillerFn();
        }
      };

      if (!this.options.width) { this.options.width = this.el.width(); }
      AUTOCOMPLETECONTAINER_ID = this.mainContainerId = 'AutocompleteContainter_' + uid;

      $('<div id="' + this.mainContainerId + '" style="position:absolute;z-index:9999;"><div class="autocomplete-w1"><div class="autocomplete" id="' + autocompleteElId + '" style="display:none;"></div></div></div>').appendTo('body');

      this.container = $('#' + autocompleteElId);
      this.fixPosition();
      if (window.opera) {
        this.el.keypress(function(e) { me.onKeyPress(e); });
      } else {
        this.el.keydown(function(e) { me.onKeyPress(e); });
      }
      this.el.keyup(function(e) { me.onKeyUp(e); });
      this.el.blur(function() { me.enableKillerFn(); });
      this.el.focus(function() { me.fixPosition(); });
    },
    
    setOptions: function(options){
      var o = this.options;
      $.extend(o, options);
      if(o.lookup){
        this.isLocal = true;
        if($.isArray(o.lookup)){ o.lookup = { suggestions:o.lookup, data:[] }; }
      }
      $('#'+this.mainContainerId).css({ zIndex:o.zIndex });
      this.container.css({ maxHeight: o.maxHeight + 'px' });
    },
    
    clearCache: function(){
      this.cachedResponse = [];
      this.badQueries = [];
    },
    
    disable: function(){
      this.disabled = true;
    },
    
    enable: function(){
      this.disabled = false;
    },

    fixPosition: function() {
      var offset = this.el.offset();
      $('#' + this.mainContainerId).css({ top: (offset.top + this.el.innerHeight()) + 'px', left: offset.left + 'px' });
    },

    enableKillerFn: function() {
      var me = this;
      $(document).bind('click', me.killerFn);
    },

    disableKillerFn: function() {
      var me = this;
      $(document).unbind('click', me.killerFn);
    },

    killSuggestions: function() {
      var me = this;
      this.stopKillSuggestions();
      this.intervalId = window.setInterval(function() { me.hide(); me.stopKillSuggestions(); }, 300);
    },

    stopKillSuggestions: function() {
      window.clearInterval(this.intervalId);
    },

    onKeyPress: function(e) {
      if (this.disabled || !this.enabled) { return; }
      // return will exit the function
      // and event will not be prevented
      switch (e.keyCode) {
        case 27: //KEY_ESC:
          this.el.val(this.currentValue);
          this.hide();
          break;
        case 9:  //KEY_TAB:
        case 13: //KEY_RETURN:
          if (this.selectedIndex === -1) {
            this.hide();
            return;
          }
          this.select(this.selectedIndex);
          if(e.keyCode === 9){ return; }
          break;
        case 38: //KEY_UP:
          this.moveUp();
          break;
        case 40: //KEY_DOWN:
          this.moveDown();
          break;
        default:
          return;
      }
      e.stopImmediatePropagation();
      e.preventDefault();
    },

    onKeyUp: function(e) {
      if(this.disabled){ return; }
      switch (e.keyCode) {
        case 38: //KEY_UP:
        case 40: //KEY_DOWN:
          return;
      }
      clearInterval(this.onChangeInterval);
      if (this.currentValue !== this.el.val()) {
        if (this.options.deferRequestBy > 0) {
          // Defer lookup in case when value changes very quickly:
          var me = this;
          this.onChangeInterval = setInterval(function() { me.onValueChange(); }, this.options.deferRequestBy);
        } else {
          this.onValueChange();
        }
      }
    },

    onValueChange: function() {
      clearInterval(this.onChangeInterval);
      this.currentValue = this.el.val();
      var q = this.getQuery(this.currentValue);
      this.selectedIndex = -1;
      if (this.ignoreValueChange) {
        this.ignoreValueChange = false;
        return;
      }
      if (q === '' || q.length < this.options.minChars) {
        this.hide();
      } else {
        this.getSuggestions(q);
      }
    },

    getQuery: function(val) {
      var d, arr;
      d = this.options.delimiter;
      if (!d) { return $.trim(val); }
      arr = val.split(d);
      return $.trim(arr[arr.length - 1]);
    },

    getSuggestionsLocal: function(q) {
      var ret, arr, len, val, i;
      arr = this.options.lookup;
      len = arr.suggestions.length;
      ret = { suggestions:[], data:[], descriptions:[], properties:[] };
      q = q.toLowerCase();
      for(i=0; i< len; i++){
        val = arr.suggestions[i];
        if(val.toLowerCase().indexOf(q) === 0){
          ret.suggestions.push(val);
          ret.data.push(arr.data[i]);
        }
      }
      return ret;
    },
    
    getSuggestions: function(q) {
      var cr, me;
      cr = this.isLocal ? this.getSuggestionsLocal(q) : this.cachedResponse[q];
      if (cr && $.isArray(cr.suggestions)) {
        this.descriptions = cr.descriptions;
        this.suggestions = cr.suggestions;
        this.properties = cr.properties;
        this.data = cr.data;
        this.suggest();
      } else if (!this.isBadQuery(q)) {
        me = this;
        me.options.params.query = q;
        me.options.params.user_id = me.options.user_id;
        me.options.params.setversion = me.options.setversion;
        $.get(this.serviceUrl, me.options.params, function(txt) { me.processResponse(txt); }, 'text');
      }
    },

    isBadQuery: function(q) {
      var i = this.badQueries.length;
      while (i--) {
        if (q.indexOf(this.badQueries[i]) === 0) { return true; }
      }
      return false;
    },

    hide: function() {
      this.enabled = false;
      this.selectedIndex = -1;
      this.container.hide();
    },

    suggest: function() {
      if (this.suggestions.length === 0
      	|| 	METASEARCH_FINISHED) {
        this.hide();
        return;
      }
     	var autocomplete= document.getElementById(AUTOCOMPLETECONTAINER_ID);
      var me, len, div, f, v, i, s, mOver, mClick;
      me = this;
      len = this.suggestions.length;
      f = this.options.fnFormatResult;
      v = this.getQuery(this.currentValue);
      mOver = function(xi) { return function() { me.activate(xi); }; };
      mClick = function(xi) { return function() { me.select(xi); }; };
      this.container.hide().empty();
      var desc = '';
      var title_content = '';
      var props=[];
      var prop='';
      var src='';
      for (i = 0; i < len; i++) {
        s = this.suggestions[i];
        var d = this.data[i];
        title_content = d; // compatibility
        if (this.descriptions)
        {
	        desc = this.descriptions[i]; 
	        if (desc===undefined || desc.contains('http')) 
	        		desc=''; 
	        else desc=(desc!=''?'\n'+desc:'');
	        title_content=d?d+desc:s;
	        props= (this.properties[i])? this.properties[i].split(';'):[];
	        prop='';
	        src='';
        }
        var p=false;
        var show=true;
		     $.each(props, function(y, v) {
		  			var assignment=v.split('=');
		  			if (assignment[0]=='src') src=assignment[1]
		  			else if (assignment[0]=='p') p=assignment[1]
		  			else if (assignment[0]=='show') show=stringToBoolean(assignment[1])
				});
       	var style=show?"visibility:visible;display:block":"visibility:hidden;display:none";
        div = $((me.selectedIndex === i ? '<div class="selected"' : '<div') + ' style="'+style+'" p="'+p+'" src="'+src+'" title="' + title_content + '">' + f(s, d, v) + '</div>');
        
        if (s.contains("seemore"))
        {
	        div.mouseover(null);
	        div.click(null);
        }
        else
        {
	        div.mouseover(mOver(i));
	        div.click(mClick(i));
        }
        this.container.append(div);
      }
      this.enabled = true;
      autocomplete.style.display='block'; //needed for reconstruction (see RODINutilities.js)
      this.container.show();
    },

    processResponse: function(text) {
      var response;
      try {
        response = eval('(' + text + ')');
      } catch (err) { return; }
      if (!$.isArray(response.data)) { response.data = []; }
      if(!this.options.noCache){
        this.cachedResponse[response.query] = response;
        if (response.suggestions.length === 0) { this.badQueries.push(response.query); }
      }
      if (response.query === this.getQuery(this.currentValue)) {
        this.suggestions = response.suggestions;
        this.data = response.data;
        this.descriptions = response.descriptions;
        this.properties = response.properties;
        this.suggest(); 
      }
    },

    activate: function(index) {
      var divs, activeItem;
      divs = this.container.children();
      // Clear previous selection:
      if (this.selectedIndex !== -1 && divs.length > this.selectedIndex) {
        $(divs.get(this.selectedIndex)).removeClass();
      }
      this.selectedIndex = index;
      if (this.selectedIndex !== -1 && divs.length > this.selectedIndex) {
        activeItem = divs.get(this.selectedIndex);
        $(activeItem).addClass('selected');
      }
      return activeItem;
    },

    deactivate: function(div, index) {
      div.className = '';
      if (this.selectedIndex === index) { this.selectedIndex = -1; }
    },

    select: function(i) {
      var selectedValue, f;
      selectedValue = this.suggestions[i];
      if (selectedValue) {
        this.el.val(selectedValue);
        if (this.options.autoSubmit) {
          f = this.el.parents('form');
          if (f.length > 0) { f.get(0).submit(); }
        }
        this.ignoreValueChange = true;
        this.hide();
        this.onSelect(i);
      }
    },

    moveUp: function() {
      if (this.selectedIndex === -1) { return; }
      else
      {
      	var nextdiv='';
      	var divs = this.container.children();

      	if (this.selectedIndex !== 0)
      			nextdiv= divs.get(this.selectedIndex-1);
      	if (nextdiv && nextdiv.style.visibility=='hidden')
      	{
      		if (nextdiv.getAttribute('p')=='seemore' )
      		{
      			this.adjustScroll(this.selectedIndex-1); /*hidden seemore -> sel one before*/
      		}
      		else /*Search upwords for 1st visible non-seemore item*/
      		{		
	      		var selected=false;
	      		for(var x=this.selectedIndex-2;x>=0;x--)
		      	{
		      		if (divs.get(x).style.visibility=='visible' && divs.get(x).getAttribute('p')=='seemore') 
		      		{
		      			this.adjustScroll(x); /*one before*/
		      			selected=true;
		      			break;
		      		}
		      	}
	      		if (!selected) {return;}
	      	}	
      	} 
	      
      	else		
	      if (this.selectedIndex === 0) {
	        this.container.children().get(0).className = '';
	        this.selectedIndex = -1;
	        this.el.val(this.currentValue);
	        return;
	      }
      this.adjustScroll(this.selectedIndex - 1);
      }
    },

    moveDown: function() {
      if (this.selectedIndex === (this.suggestions.length - 1)) { return; }
      else 
      {
      	var nextele= this.suggestions[this.selectedIndex+1];
	      if (nextele.contains('seemore'))
	      {
	      	//this.selectedIndex+=1;
	      	if (this.selectedIndex+2 === (this.suggestions.length)) { return; }
	      	else /*get the next visible element after +2 */
	      	{ var selected=false;
	      		for(var x=this.selectedIndex+2;x<this.suggestions.length;x++)
	      		{
		      		if (divs.get(x).style.visibility=='visible')
		      		{
		      			this.adjustScroll(x);
		      			selected=true;
		      			break;
		      		}
	      		}
	      		if (!selected) {return;}
	      	}
	      }
     		else 
	      {
   	      divs = this.container.children();
	
		      if (divs.get(this.selectedIndex+1).style.visibility=='visible')
		      	this.adjustScroll(this.selectedIndex + 1);
		      else {return; }
	      }
     	}
	  },

    adjustScroll: function(i) {
      var activeItem, offsetTop, upperBound, lowerBound;
      activeItem = this.activate(i);
      offsetTop = activeItem.offsetTop;
      upperBound = this.container.scrollTop();
      lowerBound = upperBound + this.options.maxHeight - 25;
      if (offsetTop < upperBound) {
        this.container.scrollTop(offsetTop);
      } else if (offsetTop > lowerBound) {
        this.container.scrollTop(offsetTop - this.options.maxHeight + 25);
      }
      this.el.val(this.getValue(this.data[i]));
    },

    onSelect: function(i) {
      var me, fn, s, d;
      me = this;
      fn = me.options.onSelect;
      s = me.suggestions[i];
      d = me.data[i];
      if (d)
	      me.el.val(d);/*FRI: Set data val as sugg.*/
      else
  	    me.el.val(me.getValue(s));
      if ($.isFunction(fn)) { fn(s, d, me.el); }
    },
    
    getValue: function(value){
        var del, currVal, arr, me;
        me = this;
        del = me.options.delimiter;
        if (!del) { return value; }
        currVal = me.currentValue;
        arr = currVal.split(del);
        if (arr.length === 1) { return value; }
        return currVal.substr(0, currVal.length - arr[arr.length - 1].length) + value;
    }

  };

}(jQuery));
