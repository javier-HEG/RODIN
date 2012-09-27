<?php


#USE SOME USEFUL PHP ENHANCMENT HERE:
include_once("../u/RodinWidgetBase.php");
# Use values together with js.php file:
include_once("./RDW_ZBZ.inc.php");



#AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX
print<<<EOP

	var colorbase= '$POLY_COLOR_FILL_GREEN';
	var opacitybase= '$POLY_COLOR_FILL_OPACITY';
	var colormark= '$POLY_COLOR_FILL_RED';
	var opacitymark= '$POLY_COLOR_FILL_OPACITY';
	
	//Global vars:
    zbzm_polygon_toggle = [];
    zbzm_polygon_style = [];
    zbzm_polygon_title = [];
    zbzm_polygon = [];
    zbzm_marker_on=false;
	gmaps_w=100;
	gmaps_h=100; 
	center_lat=0;
	center_lng=0;
 

    function navi_mark_mouseout(n)
    {
     	if (!zbzm_marker_on)
      {
	      var tr = document.getElementById('navi_'+n);
	      if (tr)
	      {
	         tr.bgColor='#ffffff';
	      }
			}
    }


    function transfer_mouseover(n)
		{
			if (!zbzm_marker_on)
      {    	mouseover_n=-1;
     	 if (n != mouseover_n)
	      {
	        navi_mark_mouseover(n);
       	  document.getElementById('syslabel').innerHTML = zbzm_polygon_title[n];
	        var colormark= '$POLY_COLOR_FILL_BLUE';
	        var opacitymark= '.2';
	        poly_mark_mouseover(n,colormark,opacitymark,false);
	        mouseover_n=n;
	      }
      }
    }

    function transfer_mouseout(n)
		{
			if (!zbzm_marker_on)
      {
 	      navi_mark_mouseout(n);
  	    var colorbase=zbzm_polygon_style[n][0];
	      var opacitybase=zbzm_polygon_style[n][1];
	      poly_mark_mouseover(n,colorbase,opacitybase,false);
  		}
    }

	// uses $NAVI_COLOR_FILL_BLUE
    function navi_mark_mouseover(n)
    {
     	if (!zbzm_marker_on)
			{
	      var tr = document.getElementById('navi_'+n);
	      if (tr)
	      {
	         tr.bgColor='$NAVI_COLOR_FILL_BLUE';
	      }
	      else alert ('kein navi_'+n);
    	}
    }


	  function showMarkerText(marker,n)
	  {
    	//if (!zbzm_marker_on)
      {
   	   	  zbzm_marker_on=true;
	      var latlng = marker.getLatLng();
	      marker.setLatLng(latlng);
	      marker.openInfoWindowHtml(marker.info_window_content);
	      //marker.showMapBlowup();
	      if (n>=0)
	      {
	        var bounds=zbzm_polygon[n].getBounds();
	        map.setZoom(map.getBoundsZoomLevel(bounds) - 1);
	        //map.panTo(bounds.getCenter());
			map.setCenter(bounds.getCenter());
			
	      }
      }
      return false;
	  }



    function poly_mark_mouseover(n,colormark,opacitymark,recreate)
    {
      if (recreate) map.removeOverlay(zbzm_polygon[n]); // this is a method to bring poly up
      zbzm_polygon[n].setFillStyle({color:colormark,opacity:opacitymark});
      if (recreate) map.addOverlay(zbzm_polygon[n]);
    }

    function poly_mark_mouseout(n)
    {
      var colorbase=zbzm_polygon_style[n][0];
      var opacitybase=zbzm_polygon_style[n][1];
      //GLog.write('Mouseout Use colorbase='+colorbase+', opacitybase='+ opacitybase);
    	zbzm_polygon[n].setFillStyle({color:colorbase,opacity:opacitybase});
     }




    function poly_toggle(n,recreate)
    {
	
		return; // ausgeschaltet
	
	    // Toggle color of overlay n

      if (zbzm_polygon_toggle[n])
      {
        zbzm_polygon_toggle[n]=false;
        zbzm_polygon_style[n]=[colorbase,opacitybase];
        zbzm_polygon[n].setFillStyle({color:colorbase,opacity:opacitybase});
      }
      else
      {
        if (recreate)
        {
     		  //GLog.write('poly_toggle: recreate Overlay '+n);
        	map.removeOverlay(zbzm_polygon[n]); // this is a method to bring poly up
        }
        zbzm_polygon_toggle[n]=true;
        zbzm_polygon[n].setFillStyle({color:colormark,opacity:opacitymark});
        zbzm_polygon_style[n]=[colormark,opacitymark];
        if (recreate) map.addOverlay(zbzm_polygon[n]);

      }
    }


   function zoomoutmap()
   {
       document.getElementById('syslabel').innerHTML ='';
       //GLog.write("set fit zoom: "+map.getBoundsZoomLevel(bounds));
       //GLog.write("Set fit center in: "+bounds.getCenter());
	     map.setZoom(map.getBoundsZoomLevel(bounds));
	     map.setCenter(bounds.getCenter());
       return false;
   }





EOP;

#AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX - AJAX
?>
