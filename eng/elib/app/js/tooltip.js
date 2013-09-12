
/* TOOLTIP */

//elib_tooltip(obj,event,txt,'puh("'+txt+')','tlw',0);
function elib_tooltip(objs,e,txt,showmode, execonopen, onmouseover, onmouseout, delayms)
{
	if (!txt) //e is txt, since no event 
		xstooltip_show(objs, txt, execonopen, onmouseover, onmouseout, e, 'elib_tooltip',showmode,delayms);
	else
		xstooltip_show(objs, e, txt, execonopen, onmouseover, onmouseout, 'elib_tooltip',showmode,delayms);
}


function elib_tooltip_up(e,txt)
{
	if (!txt) //e is txt, since no event 
		xstooltip_show(txt, e, 'elib_tooltip','up');
	else
		xstooltip_show(e, txt, 'elib_tooltip','up');
}

function elib_tooltip_upleft(e,txt)
{
	if (!txt) //e is txt, since no event 
		xstooltip_show(txt, e, 'elib_tooltip','upleft');
	else
		xstooltip_show(e, txt, 'elib_tooltip','upleft');
}




function elib_tooltip_hide()
{
	xstooltip_hide('elib_tooltip');
}

function apply_elib_tooltip(obj,tooltiptext,showmode)
{
	if (obj)
	{
		tooltiptext = "'"+tooltiptext+"'";
		var tooltiptext_coded=Base64.encode(tooltiptext);
		/*alert('Set mousover'+obj+' to '+tooltiptext+'\n\nCoded: '+tooltiptext_coded);*/
		obj.setAttribute('onmouseover','elib_tooltip_coded(event,"'+tooltiptext_coded+'","'+showmode+'")');
		obj.setAttribute('onmouseout','elib_tooltip_hide();');
	}
}





function apply_elib_tooltip_fix4(obj,tooltiptext,callback,param)
{
	if (obj)
	{
		tooltiptext = "'"+tooltiptext+"'";
		var tooltiptext_coded=Base64.encode(tooltiptext);
		/*alert('Set mousover'+obj+' to '+tooltiptext+'\n\nCoded: '+tooltiptext_coded);*/
		obj.setAttribute('onmouseover','setTimeout("'+callback+'('+param+')",100);elib_tooltip_coded(event,"'+tooltiptext_coded+'")');
		obj.setAttribute('onmouseout','');
	}
	
}





function apply_elib_tooltip_fix(obj,tooltiptext)
{
	if (obj)
	{
		tooltiptext = "'"+tooltiptext+"'";
		var tooltiptext_coded=Base64.encode(tooltiptext);
		/*alert('Set mousover'+obj+' to '+tooltiptext+'\n\nCoded: '+tooltiptext_coded);*/
		obj.setAttribute('onmouseover','elib_tooltip_coded(event,"'+tooltiptext_coded+'")');
		obj.setAttribute('onmouseout','');
	}
}


function apply_elib_tooltip2(obj,tooltiptext)
{
	if (obj)
	{
		if (tooltiptext!='')
		{
			tooltiptext = "'"+tooltiptext+"'";
			var tooltiptext_coded=Base64.encode(tooltiptext);
			/*alert('Set mousover'+obj+' to '+tooltiptext+'\n\nCoded: '+tooltiptext_coded);*/
			obj.onmouseover='elib_tooltip_coded(event,"'+tooltiptext_coded+'")';
		}
		else
		{
			obj.onmouseover='elib_tooltip_coded(event,"")';
		}
		obj.onmouseout='elib_tooltip_hide();';
	}
}




function elib_tooltip_coded(e,base64codedtxt,showmode)
{
	var txt = Base64.decode(base64codedtxt);
	/*eliminate ' */
	txt=txt.substr(1,txt.length - 2);
	
	elib_tooltip(e,txt,showmode);
}

/**
 * Show tooltip above mouse coord
 * @param objs - array od objects clicked/hovered
 * @param e - event object (from browser)
 * @param content - a wanted comment text (maybenull, maybehtml)
 * @param execonopen - sth to do after opeining
 * @param onmouseover - sth to do on mouseover
 * @param onmouseout - 
 * @param tooltipId - 
 * @param showmode - see below
 * @param delayms - if set the tooltip is shown delayed
 */
function xstooltip_show(objs, e, content, execonopen, onmouseover, onmouseout, tooltipId, showmode, delayms)
{
	/*Exit if tooltip was pinned*/
	var pinned = jQuery('#'+tooltipId+'.pinned').get(0);
	if (pinned && pinned.style.opacity>0) return;
	/*--------------------*/
   
  var mouseX = 0;
  var mouseY = 0;

	if (!e) //e is txt, since no event 
	e=window.event;
	if (!e)   ; // alert('xstooltip_show() no event found to set mouse coordinates');	
	else
	{
    var obj=objs[0];
  	var maxw=0;
  	var maxh=0;
  	var mouseXdelta=0, mouseYdelta=0;
  	var correction=false;
    
  	/* corrections */
  	if (showmode=='tlw') /*top left word*/
  	{ //Show on top of element
  		//take coords from element
  		//Try to recognize multiline!!!
  		var multiline = (xstooltip_findPosY(obj) != xstooltip_findPosY(objs[objs.length - 1]));
  		if (multiline)
  		{
  			var tooltip_width = jQuery('.elibresult').width();
  			mouseX = jQuery('.elibresult').position().left - 10;
	 			tooltip_x  = jQuery('.elibresult').width();
	 			tooltip_width  = jQuery('.elibresult').width();
	 			jQuery('div#elib_tooltip').each(function(){
		 			jQuery(this).addClass('multiline').removeClass('monoline');
		 			jQuery(this).width(tooltip_width);
	 			});
	  	}
  		else {
	 			jQuery('div#elib_tooltip').each(function(){
	 				jQuery(this).addClass('monoline').removeClass('multiline');
  				jQuery(this).width('auto');
	 			});
	 			/*selection inverse? right2left*/
  			var inverse_selection=xstooltip_findPosX(objs[objs.length - 1]) < xstooltip_findPosX(obj);
  			if(inverse_selection)
	  			mouseX = xstooltip_findPosX(objs[objs.length - 1]) - 6;
  			else
  				mouseX = xstooltip_findPosX(obj) - 6;
	   	}	
	   	mouseY = xstooltip_findPosY(obj) - 29; //token at same height	as word
  	}
  	else if (showmode=='upleft')
  	{ //Show on top of element
  		mouseXdelta-=100;
  		mouseYdelta-=30;
		}
  	else if (showmode=='upleftleft')
  	{ //Show on top of element
  		mouseXdelta-=200;
  		mouseYdelta-=30;
		}
  	else if (showmode=='upupleft')
  	{ //Show on top of element
  		mouseXdelta-=100;
  		mouseYdelta-=	60;
		}
  	else if (showmode=='upupleftleft')
  	{ //Show on top of element
  		mouseXdelta-=200;
  		mouseYdelta-=	70;
		}
  	else if (showmode=='upup')
  	{ //Show on top of element
  		mouseYdelta-=	60;
		}
  	
  	/*Set mouse coord if not yet set by above sections*/
    mouseX = mouseX? mouseX: e.pageX + 5 + mouseXdelta;
    mouseY = mouseY? mouseY: e.pageY + 5 + mouseYdelta;
    if (delayms>1)
    {
    	var objidentifier='elib_tooltip_obj';
    	jQuery(obj).addClass(objidentifier);
	    var functioncall='make_tooltip_visible_delayed(\''+tooltipId+'\','+mouseX+','+mouseY+',\''+Base64.encode(content)+'\',\''+Base64.encode(onmouseover)+'\',\''+Base64.encode(onmouseout)+'\',\''+objidentifier+'\')';
		  setTimeout( functioncall, delayms );
	  }
	  else
	  	make_tooltip_visible(tooltipId,mouseX,mouseY,content,execonopen,onmouseover,onmouseout,obj);
	}
}


function make_tooltip_visible_delayed(tooltipId,x,y,content_b64,execonopen_b64,onmouseover_b64,onmouseout_b64,objidentifier)
{
	var obj = jQuery("."+objidentifier).removeClass(objidentifier).get(0);
	var execonopen=onmouseover_b64?Base64.decode(onmouseover_b64):'';
	var onmouseover=onmouseover_b64?Base64.decode(onmouseover_b64):'';
	var onmouseout=onmouseout_b64?Base64.decode(onmouseout_b64):'';
	var content=content_b64?Base64.decode(content_b64):'';
		
	make_tooltip_visible(tooltipId,x,y,content,execonopen,onmouseover,onmouseout,obj);
}



function make_tooltip_visible(tooltipId,x,y,content,execonopen,onmouseover,onmouseout,obj)
{
	var it = document.getElementById(tooltipId);
  if(it)
  {
  	/* is tooltip with (x,y) already visible on this obj?*/
  	var alreadythere=(		it.offsetLeft == x && it.offsetTop==y 
  										&& 	it.style.visibility=='visible'
  										&& 	it.style.opacity>0
  										);
  	if (!alreadythere)
  	{
	  	it.style.top = y + 'px';
		  it.style.left = x + 'px';
			it.style.zIndex = 1000;
			it.style.visibility = 'visible';
		  it.innerHTML=content;
		  it.obj=obj;
		  
		  
		  jQuery("#"+tooltipId).each(function(){
		  	jQuery(this).show();
		  	jQuery(this).fadeIn();
		  });
		  //it.style.visibility = 'visible'; 
		  jQuery("label#ttp").focus();
		  
			if (execonopen)
			{ 
				eval(execonopen); 
			}
	 		var hiding_tooltip='elib_tooltip_hide()';
	 		//var hiding_tooltip='';

			/*It helps to exec the previous onmouseout...*/
				var old_onmouseout = it.getAttribute('old_mouseleave');
				if (old_onmouseout) {
					old_onmouseout=old_onmouseout.replace(hiding_tooltip,'').trim();
					if(old_onmouseout) eval(old_onmouseout);
				}

			// (onmouseout)
			// The following code has to be performed also with onmouseout='':
			{ /*Set both hiding and unhilightning*/
				/*crome? ie? */
				it.addEventListener('mouseleave',function(){
					 var pinned_tooltip = jQuery("#"+tooltipId+'.pinned').get(0);
					 if (pinned_tooltip)
					 {
					 	 //alert('Renounce to mouseout actions menu');
					 }
					 else
							eval(hiding_tooltip+';'+onmouseout);		});
				it.setAttribute('old_mouseleave',/*hiding_tooltip+';'+*/onmouseout); 
			}

			//if (onmouseover)
			// The following code should be performed any time
			{ /*Set both hiding and unhilightning*/
				it.addEventListener('mouseenter',function(){
					eval(onmouseover);		});
				it.setAttribute('old_mouseenter',onmouseover); 
			}
		 }
  };
}













function xstooltip_findPosX(obj) 
{
  var curleft = 0;
  if (obj.offsetParent) 
  {
    while (obj.offsetParent) 
        {
            curleft += obj.offsetLeft
            obj = obj.offsetParent;
        }
    }
    else if (obj.x)
        curleft += obj.x;
    return curleft;
}


function xstooltip_findPosY(obj) 
{
   var curtop = 0;
   if (obj.offsetParent) 
   {
       while (obj.offsetParent) 
       {
           curtop += obj.offsetTop;
           obj = obj.offsetParent;
       }
   }
   else if (obj.y)
       curtop += obj.y;
   return curtop;
}

function toggle_pin_tooltip(apin)
{
	var it = document.getElementById('elib_tooltip');
	if (it)
	{
		if(apin.fix)
		{
			jQuery(it).removeClass('pinned');
			it.nomouseover=false;
			apin.fix=false;
		}
		else {
			jQuery(it).addClass('pinned');
			it.nomouseover=true;
			apin.fix=true;
		}
	}
}



function xstooltip_hide(tooltipId,force_fadeout)
{ /* in 50 ms */	
	//alert('xstooltip_hide');
	var it = document.getElementById(tooltipId);
	var fadeout = it.nomouseover?false:true;
	if (fadeout || force_fadeout)
	{
		var onmouseout = it.getAttribute('old_mouseleave');
		if (onmouseout) eval (onmouseout);
	  jQuery("#"+tooltipId).each(function(){
		  jQuery(this).fadeOut(0);
		  jQuery(this).removeClass('pinned');
		  jQuery(this).hide();
	  });
  }
}

/**
 * Quircs mode
 * @param {Object} obj
 */
function findPos(obj) 
{
	var curleft = curtop = 0;
	if (obj.offsetParent) {
		do {
			curleft += obj.offsetLeft;
			curtop += obj.offsetTop;
			} while (obj = obj.offsetParent);
	}
	return [curleft,curtop];
}