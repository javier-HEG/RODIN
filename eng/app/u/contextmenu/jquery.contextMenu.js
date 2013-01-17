// jQuery Context Menu Plugin
//
// Version 1.01
//
// Cory S.N. LaViska
// A Beautiful Site (http://abeautifulsite.net/)
//
// More info: http://abeautifulsite.net/2008/09/jquery-context-menu-plugin/
//
// Terms of Use
//
// This plugin is dual-licensed under the GNU General Public License
//   and the MIT License and is copyright A Beautiful Site, LLC.
//
//
// Fabio Ricci (FRI) - for HEG - fabio.ricci@ggaweb.ch - 2012
// Added some extension to start actions just before constructing the menu.
// In case premenuitem_callback be set, this will be called on the item text
// of the element to call the contex menu on ( premenucallback(text) )
//
if(jQuery)( function($) {
	$.extend($.fn, {
		
		contextMenu: function(o, callback) {
			// Defaults
			if( o.menu == undefined ) return false;
			if( o.inSpeed == undefined ) o.inSpeed = 150;
			if( o.outSpeed == undefined ) o.outSpeed = 75;
			// 0 needs to be -1 for expected results (no fade)
			if( o.inSpeed == 0 ) o.inSpeed = -1;
			if( o.outSpeed == 0 ) o.outSpeed = -1;
      /* PARAMETERS: */
      var premenuitem_callback = o.premenuitem_callback; /*FRI*/
      var min_occurrences = o.min_occurrences; /*FRI*/
      var conditioned_menuitem_id = o.conditioned_menuitem_id; /*FRI*/
    	// Loop each context menu
			$(this).each( function() {
				var el = $(this);
				var offset = $(el).offset();

        var max_menu_items = 10;

        // Add contextMenu class
				$('#' + o.menu).addClass('contextMenu');
				// Simulate a true click
				$(this).mousedown( function(e) {
					var evt = e;
					evt.stopPropagation();
					$(this).mouseup( function(e) {
						e.stopPropagation();
						var srcElement = $(this);
						$(this).unbind('mouseup');
						if( evt.button == 2 ) { // 0 for left click, 2 for right click
							// Hide context menus that may be showing
							$(".contextMenu").hide();
							
							// Get this context menu
							var menu = $('#' + o.menu);
							if( $(el).hasClass('disabled') ) return false;
							
							// Detect mouse position
							var d = {}, x, y;
							if( self.innerHeight ) {
								d.pageYOffset = self.pageYOffset;
								d.pageXOffset = self.pageXOffset;
								d.innerHeight = self.innerHeight;
								d.innerWidth = self.innerWidth;
							} else if( document.documentElement &&
								document.documentElement.clientHeight ) {
								d.pageYOffset = document.documentElement.scrollTop;
								d.pageXOffset = document.documentElement.scrollLeft;
								d.innerHeight = document.documentElement.clientHeight;
								d.innerWidth = document.documentElement.clientWidth;
							} else if( document.body ) {
								d.pageYOffset = document.body.scrollTop;
								d.pageXOffset = document.body.scrollLeft;
								d.innerHeight = document.body.clientHeight;
								d.innerWidth = document.body.clientWidth;
							}
							// [Rodin] Update label in context menu
							var wort = $(el).text();
							var capitalizedLabel=wort.substr(0,1).toUpperCase()+wort.substr(1).toLowerCase();
							
							switch (o.menu) {
								case "aggViewContextMenu":
									// Context menu for widgets should be kept within iFrame border
									//alert('aggViewContextMenu');
									var aggv_offset=110;
									(e.pageX) ? x = Math.min(d.innerWidth - menu.width() - 10, e.pageX - 10) : x = e.clientX + d.scrollLeft;
									(e.pageY) ? y = Math.min(d.innerHeight - menu.height() - 10 - aggv_offset, e.pageY - 10 -aggv_offset) : y = e.clientY + d.scrollTop - aggv_offset;
									//Set label (not set in other cases)
									var menutoken=document.getElementById('widgetContextMenuLabelaggv');
									if (menutoken)
									{
										menutoken.innerHTML=capitalizedLabel;
									} else alert('notfound: widgetContextMenuLabelaggv');
								
								break;
								case "widgetContextMenu":
									//alert('widgetContextMenu');
									// Context menu for widgets should be kept within iFrame border
									(e.pageX) ? x = Math.min(d.innerWidth - menu.width() - 10, e.pageX - 10) : x = e.clientX + d.scrollLeft;
									(e.pageY) ? y = Math.min(d.innerHeight - menu.height() - 10, e.pageY - 10) : y = e.clientY + d.scrollTop;
									break;
								case "facetsContextMenu":
									//alert('facetsContextMenu');

								default:
									var areaDiv = $("#area");
									var position = areaDiv.position();
									
									(e.pageX) ? x = e.pageX - position.left -10 : x = e.clientX + d.scrollLeft;
									(e.pageY) ? y = e.pageY - position.top -10 : y = e.clientY + d.scrollTop;
									
//								(e.pageX) ? x = Math.min($(window).width() - menu.width() - position.left - 10, e.pageX - position.left - 10) : x = e.clientX + d.scrollLeft;
//								(e.pageY) ? y = Math.min($(window).height() - menu.height() - position.top - 10, e.pageY - position.top - 10) : y = e.clientY + d.scrollTop;
							}
							
							

              //FRI: In case a premenuitem_callback function were defined by the user,
              //Call that function before constructing the menu item conditioned_menuitem_id.
              var premenuitem_obj=null;
              if (typeof(premenuitem_callback) != 'undefined')
              {
                eval('premenuitem_obj = new '+premenuitem_callback+'("'+capitalizedLabel+'",'+min_occurrences+','+conditioned_menuitem_id+')'); //not cleaned inner quotes ' ?
                /*
                 * do_menu_item is a number
                 * (+/-)1 corresponds to the first menu item. (+/-)2 to the second, ...
                 * 0 means - error in execution (should not occur)
                 * A negative number n means: suppress the n-th menu item
                 */
                var occurrences = premenuitem_obj.occurrences;
                var menuitem_idx=premenuitem_obj.do_menu_item<0?(- premenuitem_obj.do_menu_item):premenuitem_obj.do_menu_item;

                if (premenuitem_obj.do_menu_item > (- max_menu_items)
                  &&premenuitem_obj.do_menu_item < (  max_menu_items)
                  &&premenuitem_obj.do_menu_item != 0)
                {
                  var cnt=-1; //First menu elem is a <h1> offbyone
                  var menuitem_selector= "#" + o.menu +" li";
                  var action=premenuitem_obj.do_menu_item<0?'hide':'show';
                
                  $(menuitem_selector).each(function()
                  {
                    cnt++;
                    if (cnt == menuitem_idx)
                    {
                      if (action=='hide')
                      {
                        //if ($(this).css('visibility')=='visible')
                        $(this).hide();
                      }
                      else  if (action=='show')
                      {
                        //if ($(this).css('visibility')!='visible')
                        //Following sets the number of occurrences in the label
                        //$(this).find("label").text(occurrences);
                        $(this).show();

                      }
                    }
                 })
               }

              }

              $("#" + o.menu + "Label").text(capitalizedLabel);

							// Show the menu
							$(document).unbind('click');


							$(menu).css({ top: y, left: x }).fadeIn(o.inSpeed);
							
							// Hover events
							$(menu).find('A').mouseover( function() {
								$(menu).find('LI.hover').removeClass('hover');
								$(this).parent().addClass('hover');
							}).mouseout( function() {
								$(menu).find('LI.hover').removeClass('hover');
							});							
							// Set the action on mouse leaving the menu
							$(menu).mouseleave(function(){
								$(".contextMenu").hide();
							});

							// Keyboard
							$(document).keypress( function(e) {
								switch( e.keyCode ) {
									case 38: // up
										if( $(menu).find('LI.hover').size() == 0 ) {
											$(menu).find('LI:last').addClass('hover');
										} else {
											$(menu).find('LI.hover').removeClass('hover').prevAll('LI:not(.disabled)').eq(0).addClass('hover');
											if( $(menu).find('LI.hover').size() == 0 ) $(menu).find('LI:last').addClass('hover');
										}
									break;
									case 40: // down
										if( $(menu).find('LI.hover').size() == 0 ) {
											$(menu).find('LI:first').addClass('hover');
										} else {
											$(menu).find('LI.hover').removeClass('hover').nextAll('LI:not(.disabled)').eq(0).addClass('hover');
											if( $(menu).find('LI.hover').size() == 0 ) $(menu).find('LI:first').addClass('hover');
										}
									break;
									case 13: // enter
										$(menu).find('LI.hover A').trigger('click');
									break;
									case 27: // esc
										$(document).trigger('click');
									break
								}
							});
							
							// When items are selected
							$('#' + o.menu).find('A').unbind('click');
							$('#' + o.menu).find('LI:not(.disabled) A').click( function() {
								$(document).unbind('click').unbind('keypress');
								$(".contextMenu").hide();
								// Callback
								if( callback ) callback( $(this).attr('href').substr(1), $(srcElement), {x: x - offset.left, y: y - offset.top, docX: x, docY: y} );
								return false;
							});
							
							// Hide bindings
							setTimeout( function() { // Delay for Mozilla
								$(document).click( function() {
									$(document).unbind('click').unbind('keypress');
									$(menu).fadeOut(o.outSpeed);
									return false;
								});
							}, 0);
						}
					});
				});
				
				// Disable text selection
				if( $.browser.mozilla ) {
					$('#' + o.menu).each( function() { $(this).css({ 'MozUserSelect' : 'none' }); });
				} else if( $.browser.msie ) {
					$('#' + o.menu).each( function() { $(this).bind('selectstart.disableTextSelect', function() { return false; }); });
				} else {
					$('#' + o.menu).each(function() { $(this).bind('mousedown.disableTextSelect', function() { return false; }); });
				}
				// Disable browser context menu (requires both selectors to work in IE/Safari + FF/Chrome)
				$(el).add($('UL.contextMenu')).bind('contextmenu', function() { return false; });
				
			});
			return $(this);
		},
		
		// Disable context menu items on the fly
		disableContextMenuItems: function(o) {
			if( o == undefined ) {
				// Disable all
				$(this).find('LI').addClass('disabled');
				return( $(this) );
			}
			$(this).each( function() {
				if( o != undefined ) {
					var d = o.split(',');
					for( var i = 0; i < d.length; i++ ) {
						$(this).find('A[href="' + d[i] + '"]').parent().addClass('disabled');
						
					}
				}
			});
			return( $(this) );
		},
		
		// Enable context menu items on the fly
		enableContextMenuItems: function(o) {
			if( o == undefined ) {
				// Enable all
				$(this).find('LI.disabled').removeClass('disabled');
				return( $(this) );
			}
			$(this).each( function() {
				if( o != undefined ) {
					var d = o.split(',');
					for( var i = 0; i < d.length; i++ ) {
						$(this).find('A[href="' + d[i] + '"]').parent().removeClass('disabled');
						
					}
				}
			});
			return( $(this) );
		},
		
		// Disable context menu(s)
		disableContextMenu: function() {
			$(this).each( function() {
				$(this).addClass('disabled');
			});
			return( $(this) );
		},
		
		// Enable context menu(s)
		enableContextMenu: function() {
			$(this).each( function() {
				$(this).removeClass('disabled');
			});
			return( $(this) );
		},
		
		// Destroy context menu(s)
		destroyContextMenu: function() {
			// Destroy specified context menus
			$(this).each( function() {
				// Disable action
				$(this).unbind('mousedown').unbind('mouseup');
			});
			return( $(this) );
		}
		
	});
})(jQuery);