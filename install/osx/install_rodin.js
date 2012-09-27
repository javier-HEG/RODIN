/**
 * install_rodin.js
 * Autor: Fabio Ricci (fabio.fr.ricci@hesge.ch)
 * HEG Geneve
 * Date: June 2011
 */

String.prototype.reverse=function(){return this.split("").reverse().join("");}

function update_rodin_install_inputs_step1()
{
	if (smart())
	{
		//alert('onchanged_newrodinsegment('+ seg+')');
		var seg=get_newrodinsegment();
		if (seg=='eng')
			alert('Segment "eng" is already present. Please type another segment');
		else
		{
			var defaultusernamelength = 16; //mysql limitation at the time of developping.
			var rodindbbasename=get_rodindb_basename();	
			document.getElementById('RODINDB_USERNAME').innerHTML		= limitusernamelength(get_rodindb_username(),defaultusernamelength);
			document.getElementById('RODINDB_USERNAME1').innerHTML		=limitusernamelength(get_rodindb_username(),defaultusernamelength);
			document.getElementById('RODINDB_USERNAME2').innerHTML		=limitusernamelength(get_rodindb_username(),defaultusernamelength);
			document.getElementById('RODINDB_USERNAME3').innerHTML		=limitusernamelength(get_rodindb_username(),defaultusernamelength);
			document.getElementById('RODINDB_USERNAME4').innerHTML		=limitusernamelength(get_rodindb_username(),defaultusernamelength);
			document.getElementById('RODINDB_USERNAME5').innerHTML		=limitusernamelength(get_rodindb_username(),defaultusernamelength);
			document.getElementById('RODINADMIN_USERNAME').innerHTML	=limitusernamelength(get_rodinadmindb_username()+'_'+seg,defaultusernamelength);
			document.getElementById('RODINADMIN_USERNAME0gen').innerHTML=limitusernamelength(get_rodinadmindb_username()+'_'+seg+'-0',defaultusernamelength);

			document.getElementById('RODINDB_PASSWD').innerHTML= document.f.RODINDB_PASSWD.value;
			document.getElementById('RODINDB_PASSWD2').innerHTML= document.f.RODINDB_PASSWD.value;
			document.getElementById('RODINDB_DBNAMEx').innerHTML= limitusernamelength(rodindbbasename+'_'+seg,defaultusernamelength);
			document.getElementById('RODINDB_DBNAMEd').innerHTML= limitusernamelength(rodindbbasename+'_'+seg,defaultusernamelength);
			
			document.getElementById('RODINPOSHDB_DBNAMEx').innerHTML= limitusernamelength(document.f.RODINPOSHDB_DBNAMEx.value +'_'+seg,defaultusernamelength);
			document.getElementById('RODINPOSHDB_DBNAMEd').innerHTML= limitusernamelength(document.f.RODINPOSHDB_DBNAMEx.value +'_'+seg,defaultusernamelength);
			document.getElementById('ARCDB_DBNAMEx').innerHTML= limitusernamelength(document.f.ARCDBBASENAMEx.value +'_'+seg,defaultusernamelength);
			document.getElementById('ARCDB_DBNAMEd').innerHTML= limitusernamelength(document.f.ARCDBBASENAMEx.value +'_'+seg,defaultusernamelength);
			document.getElementById('SRCDB_DBNAMEx').innerHTML= limitusernamelength(document.f.SRCDBBASENAMEx.value +'_'+seg,defaultusernamelength);
			document.getElementById('SRCDB_DBNAMEd').innerHTML= limitusernamelength(document.f.SRCDBBASENAMEx.value +'_'+seg,defaultusernamelength);
			document.getElementById('ADMINDB_DBNAMEx').innerHTML= limitusernamelength(document.f.ADMINDBBASENAMEx.value +'_'+seg,defaultusernamelength);
			document.getElementById('ADMINDB_DBNAMEd').innerHTML= limitusernamelength(document.f.ADMINDBBASENAMEx.value +'_'+seg,defaultusernamelength);
			document.getElementById('ADMINDB_DBNAMEd0').innerHTML= limitusernamelength(document.f.ADMINDBBASENAMEx.value +'_'+seg,defaultusernamelength);
			document.getElementById('ADMINDB_DBNAMEd0a').innerHTML= document.getElementById('ADMINDB_DBNAMEd0').innerHTML;
			document.getElementById('ADMINDB_DBNAMEd0gen').innerHTML= limitusernamelength(document.f.ADMINDBBASENAMEx.value +'_'+seg+'-0',defaultusernamelength);
			
			document.getElementById('NEWRODINDOCROOTx').innerHTML=document.f.OLDRODINDOCROOTx.value + '_' + seg;
			
			document.getElementById('RODINADMINDB_PASSWD').innerHTML = document.getElementById('ADMINDB_DBNAMEd0').innerHTML.reverse();
			document.f.RODINADMINDB_USERPASSx.value = document.getElementById('RODINADMINDB_PASSWD').innerHTML;
			
		}
		check_enable_install_button();
	}
}



function reverse(s){
    return s.split("").reverse().join("");
}


function limitusernamelength(uname,limitlen)
//in the middle...
{
	var len=uname.length;
	if (len > limitlen)
	{
		//alert('limitusernamelength ('+uname+' > '+limitlen);
		var mitte = limitlen/2;
		
		var primo	=uname.substr(0,mitte);
		var secondo	=uname.substr(len - mitte,mitte);
		//alert('uname= '+uname+' (mitte='+mitte+'), returning shortened name  '+primo+'+'+secondo);
		return primo+secondo;
	}
	else
	{
		return uname;
	}	
	
}






function check_enable_install_button()
{
	var button = document.f.installbutton;

	var shouldbeactive=false;
	
	var installbuttontd = document.getElementById('installbuttontd');
	shouldbeactive =   document.f.RODINDB_BASENAMEx.value.trim() != '' 
					&& document.f.RODINDB_USERNAME.value.trim() != '' 
					&& document.f.RODINDB_PASSWD.value.trim() != '' 
					&& document.f.USERWASCREATEDINMYSQLDB.checked 
					;
	if (shouldbeactive)
	{
		button.disabled = false;
		button.value='Click to create RODIN databases and to proceed to the next step';
	}
	else
	{
		button.disabled = true;
		button.value='Please fill up all fields and check the box (above) and click again to install RODIN';
	}
}



function smart()
{
	return true; //smart naming
}



function get_rodindb_basename()
{
	return document.f.RODINDB_BASENAMEx.value;
}


function get_newrodinsegment()
{
	return document.f.NEWRODINSEGMENT.value;
}

function get_rodinadmindb_username()
{
	return document.f.RODINADMINDB_USERNAMEx.value;
}

function get_rodindb_username()
{
	return document.f.RODINDB_USERNAME.value;
}

